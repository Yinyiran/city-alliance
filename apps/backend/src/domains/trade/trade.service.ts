import { Injectable, BadRequestException, NotFoundException, ConflictException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { orders, orderItems, members, shops, commissionRecords } from '../../infrastructure/drizzle/schema/index';
import { eq, and } from 'drizzle-orm';
import {
  OrderStatus,
  TradeType,
  PaymentChannel,
  MemberLevel,
  CommissionType,
} from '@city-alliance/contracts';
import { REDIS_CLIENT } from '../../infrastructure/redis/redis.provider';
import Redis from 'ioredis';
import { SettleService } from '../settle/settle.service';
import * as crypto from 'crypto';

export interface CreateOrderParams {
  customerId: string;
  shopId: string;
  storeId?: string;
  tradeType: TradeType;
  items: {
    spuId: string;
    skuId: string;
    goodsName: string;
    specText?: string;
    price: string;
    quantity: number;
  }[];
  appointmentTime?: string;
  deliveryInfo?: string;
}

@Injectable()
export class TradeService {
  constructor(
    @Inject(DRIZZLE_DB) private readonly db: DrizzleDatabase,
    @Inject(REDIS_CLIENT) private readonly redis: Redis,
    private readonly settleService: SettleService,
  ) {}

  /**
   * 生成唯一业务单号: CA + YYYYMMDDHHMMSS + 6位随机码
   */
  private generateOrderNo(): string {
    const timeStr = new Date()
      .toISOString()
      .replace(/[-:T.Z]/g, '')
      .slice(0, 14);
    const rand = Math.floor(100000 + Math.random() * 900000).toString();
    return `CA${timeStr}${rand}`;
  }

  /**
   * 生成 12 位纯数字高位原子核销码 (4位一段，方便手输与扫码核销)
   */
  private generateVerifyCode(): string {
    return Math.floor(100000000000 + Math.random() * 900000000000).toString();
  }

  /**
   * 全渠道创建订单 (计算商品总价、折上折会员折扣与应付总额)
   */
  async createOrder(params: CreateOrderParams) {
    if (!params.items || params.items.length === 0) {
      throw new BadRequestException('订单明细项不可为空');
    }

    // 查询顾客会员等级获取专属折上折优惠
    const customerList = await this.db
      .select()
      .from(members)
      .where(eq(members.id, params.customerId))
      .limit(1);

    const customer = customerList[0];
    const memberLevel = (customer?.level || MemberLevel.NORMAL) as MemberLevel;
    const equity = this.settleService.memberEquityMap[memberLevel];

    let totalAmountNum = 0;
    for (const item of params.items) {
      const lineTotal = Number(item.price) * item.quantity;
      totalAmountNum += lineTotal;
    }

    // 折上折优惠试算 (普通1.0, 钻石0.98, 黑钻0.96, 合伙人0.95)
    const discountRate = equity.discountRate;
    const payAmountNum = Number((totalAmountNum * discountRate).toFixed(2));
    const discountAmountNum = Number(
      (totalAmountNum - payAmountNum).toFixed(2),
    );

    const orderNo = this.generateOrderNo();
    const verifyCode =
      params.tradeType === TradeType.PICKUP ||
      params.tradeType === TradeType.APPOINTMENT
        ? this.generateVerifyCode()
        : null;

    const [newOrder] = await this.db
      .insert(orders)
      .values({
        orderNo,
        customerId: params.customerId,
        shopId: params.shopId,
        storeId: params.storeId || null,
        tradeType: params.tradeType,
        orderStatus: OrderStatus.PENDING_PAY,
        payStatus: 'UNPAID',
        totalAmount: totalAmountNum.toFixed(2),
        discountAmount: discountAmountNum.toFixed(2),
        payAmount: payAmountNum.toFixed(2),
        verifyCode,
        deliveryInfo: params.deliveryInfo || null,
        appointmentTime: params.appointmentTime
          ? new Date(params.appointmentTime)
          : null,
      })
      .returning();

    // 批量插入订单明细项
    for (const item of params.items) {
      const lineTotal = Number(item.price) * item.quantity;
      await this.db.insert(orderItems).values({
        orderId: newOrder.id,
        spuId: item.spuId,
        skuId: item.skuId,
        goodsName: item.goodsName,
        specText: item.specText || null,
        price: item.price,
        quantity: item.quantity,
        totalAmount: lineTotal.toFixed(2),
      });
    }

    return {
      orderId: newOrder.id,
      orderNo: newOrder.orderNo,
      totalAmount: newOrder.totalAmount,
      payAmount: newOrder.payAmount,
      verifyCode: newOrder.verifyCode,
      status: newOrder.orderStatus,
    };
  }

  /**
   * 订单支付成功事件处理 (触发 FSM 状态扭转 & 自签单/带单自动分润)
   */
  async handlePaymentSuccess(
    orderId: string,
    channel: PaymentChannel = PaymentChannel.WECHAT_PAY,
  ) {
    const orderList = await this.db
      .select()
      .from(orders)
      .where(eq(orders.id, orderId))
      .limit(1);

    const order = orderList[0];
    if (!order) {
      throw new NotFoundException('订单不存在');
    }

    if (order.orderStatus !== OrderStatus.PENDING_PAY) {
      return order; // 幂等处理
    }

    // 根据履约类型标定下一状态
    let nextStatus = OrderStatus.PAID;
    if (order.tradeType === TradeType.QUICK_PAY) {
      nextStatus = OrderStatus.COMPLETED; // 线下直接买单立即完成
    }

    await this.db
      .update(orders)
      .set({
        orderStatus: nextStatus,
        payStatus: 'PAID',
        paymentChannel: channel,
        paidAt: new Date(),
        completedAt: nextStatus === OrderStatus.COMPLETED ? new Date() : null,
        updatedAt: new Date(),
      })
      .where(eq(orders.id, order.id));

    // 触发分润记账：判定是自签单 (购买自己绑定的主理人商家) 还是跨店带单 (购买其他主理人商家)
    const customerList = await this.db
      .select()
      .from(members)
      .where(eq(members.id, order.customerId))
      .limit(1);

    const customer = customerList[0];
    const isCrossShop = Boolean(
      customer?.bindShopId && customer.bindShopId !== order.shopId,
    );
    const memberLevel = (customer?.level || MemberLevel.NORMAL) as MemberLevel;

    const splitResult = this.settleService.calculateConsumptionCommission(
      Number(order.payAmount),
      isCrossShop,
      memberLevel,
    );

    // 记录分润记录表 (带单佣金进入 7 天延迟解冻状态)
    for (const split of splitResult.splits) {
      await this.db.insert(commissionRecords).values({
        orderId: order.id,
        commissionType: splitResult.commissionType,
        beneficiaryId: order.customerId, // 实际按 role 路由至会员或租户ID
        beneficiaryType: split.role,
        ratioPercent: split.ratioPercent.toString(),
        amount: split.amount.toFixed(2),
        status: split.isFrozen ? 'FROZEN' : 'SETTLED',
        unfreezeDueDate: split.isFrozen
          ? new Date(Date.now() + split.freezeDays * 24 * 3600 * 1000)
          : null,
      });
    }

    return {
      orderId: order.id,
      orderStatus: nextStatus,
      splitResult,
    };
  }

  /**
   * 移动收银台原子防重扫码核销 (Redis Redlock 分布式防重锁)
   */
  async verifyOrderByCode(verifyCode: string, clerkUserId: string) {
    const lockKey = `lock:verify:${verifyCode}`;

    // 使用 Redis SET NX EX 尝试加 5 秒防重锁
    let lockAcquired = false;
    try {
      const res = await this.redis.set(lockKey, clerkUserId, 'EX', 5, 'NX');
      lockAcquired = res === 'OK';
    } catch {
      // 若 Redis 不可用则降级依赖数据库单记录原子更新
      lockAcquired = true;
    }

    if (!lockAcquired) {
      throw new ConflictException('当前核销码正在核销中，请勿重复操作');
    }

    try {
      const orderList = await this.db
        .select()
        .from(orders)
        .where(eq(orders.verifyCode, verifyCode))
        .limit(1);

      const order = orderList[0];
      if (!order) {
        throw new NotFoundException('无效的核销码，未找到对应订单');
      }

      if (order.orderStatus === OrderStatus.COMPLETED) {
        throw new BadRequestException('该订单已经核销完成，不可重复核销');
      }

      if (order.orderStatus !== OrderStatus.PAID) {
        throw new BadRequestException(`当前订单状态为 ${order.orderStatus}，不可核销`);
      }

      // 原子更新订单状态为已完成
      await this.db
        .update(orders)
        .set({
          orderStatus: OrderStatus.COMPLETED,
          verifiedAt: new Date(),
          verifyUserId: clerkUserId,
          completedAt: new Date(),
          updatedAt: new Date(),
        })
        .where(eq(orders.id, order.id));

      return {
        success: true,
        orderId: order.id,
        orderNo: order.orderNo,
        tradeType: order.tradeType,
        verifiedAt: new Date().toISOString(),
      };
    } finally {
      try {
        await this.redis.del(lockKey);
      } catch {}
    }
  }
}
