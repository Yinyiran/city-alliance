import { Injectable, BadRequestException, NotFoundException, ConflictException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { orders, orderItems, members, shops, commissionRecords, orderRefunds } from '../../infrastructure/drizzle/schema/index';
import { eq, and, inArray } from 'drizzle-orm';
import {
  OrderStatus,
  TradeType,
  PaymentChannel,
  MemberLevel,
  CommissionType,
  SplitCartOrderDto,
  ShipOrderDto,
  TakeawayDispatchDto,
  ApplyRefundDto,
  AuditRefundDto,
  RescheduleAppointmentDto,
  GenerateCashierQrDto,
} from '@city-alliance/contracts';
import { REDIS_CLIENT } from '../../infrastructure/redis/redis.provider';
import Redis from 'ioredis';
import { SettleService } from '../settle/settle.service';

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
   * 全渠道创建单店订单 (计算商品总价、折上折会员折扣与应付总额)
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
      params.tradeType === TradeType.STORE ||
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
   * 跨店铺购物车跨店下单拆单引擎
   * 自动根据 shopId 拆分成多个独立子订单，支持合并支付
   */
  async splitCartAndCreateOrders(dto: SplitCartOrderDto) {
    if (!dto.shopGroups || dto.shopGroups.length === 0) {
      throw new BadRequestException('购物车店铺分组不能为空');
    }

    const subOrders: any[] = [];
    let combinedPayTotal = 0;
    const parentPayTradeNo = `PAY${Date.now()}${Math.floor(1000 + Math.random() * 9000)}`;

    for (const group of dto.shopGroups) {
      const singleOrder = await this.createOrder({
        customerId: dto.customerId,
        shopId: group.shopId,
        storeId: group.storeId,
        tradeType: group.tradeType,
        items: group.items.map((it) => ({
          spuId: it.goodsId,
          skuId: it.skuId,
          goodsName: it.goodsName,
          specText: it.skuName,
          price: it.price,
          quantity: it.quantity,
        })),
        deliveryInfo: dto.addressId ? JSON.stringify({ addressId: dto.addressId }) : undefined,
      });

      subOrders.push(singleOrder);
      combinedPayTotal += Number(singleOrder.payAmount);
    }

    return {
      parentPayTradeNo,
      totalPayAmount: combinedPayTotal.toFixed(2),
      subOrderCount: subOrders.length,
      orders: subOrders,
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
        beneficiaryId: order.customerId,
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
   * 订单 FSM：主动取消未支付订单
   */
  async cancelOrder(orderId: string, reason: string = '用户主动取消') {
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
      throw new BadRequestException(`当前订单状态为 ${order.orderStatus}，不可取消`);
    }

    await this.db
      .update(orders)
      .set({
        orderStatus: OrderStatus.CANCELLED,
        payStatus: 'CLOSED',
        updatedAt: new Date(),
      })
      .where(eq(orders.id, order.id));

    return {
      success: true,
      orderId: order.id,
      status: OrderStatus.CANCELLED,
      reason,
    };
  }

  /**
   * 快递发货处理 (仅限 EXPRESS 类型)
   */
  async shipOrder(dto: ShipOrderDto) {
    const orderList = await this.db
      .select()
      .from(orders)
      .where(eq(orders.id, dto.orderId))
      .limit(1);

    const order = orderList[0];
    if (!order) {
      throw new NotFoundException('订单不存在');
    }

    if (order.tradeType !== TradeType.EXPRESS) {
      throw new BadRequestException('非快递发货订单不支持发货流');
    }

    if (order.orderStatus !== OrderStatus.PAID) {
      throw new BadRequestException(`当前订单状态为 ${order.orderStatus}，不可发货`);
    }

    const expressPayload = JSON.stringify({
      company: dto.expressCompany,
      trackingNo: dto.expressNo,
      shippedAt: new Date().toISOString(),
    });

    await this.db
      .update(orders)
      .set({
        orderStatus: OrderStatus.DELIVERING,
        deliveryInfo: expressPayload,
        updatedAt: new Date(),
      })
      .where(eq(orders.id, order.id));

    return {
      success: true,
      orderId: order.id,
      orderStatus: OrderStatus.DELIVERING,
      expressNo: dto.expressNo,
    };
  }

  /**
   * 外卖同城配送骑手调度
   */
  async dispatchTakeaway(dto: TakeawayDispatchDto) {
    const orderList = await this.db
      .select()
      .from(orders)
      .where(eq(orders.id, dto.orderId))
      .limit(1);

    const order = orderList[0];
    if (!order) {
      throw new NotFoundException('订单不存在');
    }

    if (order.tradeType !== TradeType.TAKEOUT) {
      throw new BadRequestException('非外卖订单不可指派骑手');
    }

    if (order.orderStatus !== OrderStatus.PAID) {
      throw new BadRequestException(`当前外卖订单状态为 ${order.orderStatus}，不可派单`);
    }

    const riderPayload = JSON.stringify({
      riderName: dto.riderName,
      riderPhone: dto.riderPhone,
      dispatchedAt: new Date().toISOString(),
      estimatedMinutes: dto.estimatedDeliveryMinutes,
    });

    await this.db
      .update(orders)
      .set({
        orderStatus: OrderStatus.DELIVERING,
        deliveryInfo: riderPayload,
        updatedAt: new Date(),
      })
      .where(eq(orders.id, order.id));

    return {
      success: true,
      orderId: order.id,
      orderStatus: OrderStatus.DELIVERING,
      rider: dto.riderName,
    };
  }

  /**
   * 确认收货 (完成履约)
   */
  async confirmReceived(orderId: string) {
    const orderList = await this.db
      .select()
      .from(orders)
      .where(eq(orders.id, orderId))
      .limit(1);

    const order = orderList[0];
    if (!order) {
      throw new NotFoundException('订单不存在');
    }

    if (order.orderStatus !== OrderStatus.DELIVERING) {
      throw new BadRequestException(`只有配送中的订单才可确认收货`);
    }

    await this.db
      .update(orders)
      .set({
        orderStatus: OrderStatus.COMPLETED,
        completedAt: new Date(),
        updatedAt: new Date(),
      })
      .where(eq(orders.id, order.id));

    return {
      success: true,
      orderId: order.id,
      orderStatus: OrderStatus.COMPLETED,
    };
  }

  /**
   * 售后退款申请
   */
  async applyRefund(dto: ApplyRefundDto) {
    const orderList = await this.db
      .select()
      .from(orders)
      .where(eq(orders.id, dto.orderId))
      .limit(1);

    const order = orderList[0];
    if (!order) {
      throw new NotFoundException('订单不存在');
    }

    if (order.orderStatus !== OrderStatus.PAID && order.orderStatus !== OrderStatus.DELIVERING) {
      throw new BadRequestException(`订单状态为 ${order.orderStatus}，当前不可申请退款`);
    }

    const refundNo = `RF${Date.now()}${Math.floor(1000 + Math.random() * 9000)}`;

    const [refundRecord] = await this.db
      .insert(orderRefunds)
      .values({
        orderId: order.id,
        refundNo,
        refundAmount: dto.refundAmount,
        reason: dto.reason,
        status: 'PENDING_AUDIT',
      })
      .returning();

    await this.db
      .update(orders)
      .set({
        orderStatus: OrderStatus.REFUNDING,
        updatedAt: new Date(),
      })
      .where(eq(orders.id, order.id));

    return {
      refundId: refundRecord.id,
      refundNo: refundRecord.refundNo,
      orderStatus: OrderStatus.REFUNDING,
      amount: refundRecord.refundAmount,
    };
  }

  /**
   * 售后退款审核 (商家/平台仲裁)
   */
  async auditRefund(dto: AuditRefundDto, auditorUserId: string) {
    const refundList = await this.db
      .select()
      .from(orderRefunds)
      .where(eq(orderRefunds.id, dto.refundId))
      .limit(1);

    const refund = refundList[0];
    if (!refund) {
      throw new NotFoundException('退款申请不存在');
    }

    if (dto.approved) {
      // 同意退款
      await this.db
        .update(orderRefunds)
        .set({
          status: 'REFUNDED',
          arbitratedBy: auditorUserId,
          updatedAt: new Date(),
        })
        .where(eq(orderRefunds.id, refund.id));

      await this.db
        .update(orders)
        .set({
          orderStatus: OrderStatus.REFUNDED,
          payStatus: 'CLOSED',
          updatedAt: new Date(),
        })
        .where(eq(orders.id, refund.orderId));

      // 冲正/撤销该订单尚未解冻的分润
      await this.db
        .update(commissionRecords)
        .set({
          status: 'REVERSED',
          updatedAt: new Date(),
        })
        .where(
          and(
            eq(commissionRecords.orderId, refund.orderId),
            eq(commissionRecords.status, 'FROZEN'),
          ),
        );

      return {
        success: true,
        refundStatus: 'REFUNDED',
        orderStatus: OrderStatus.REFUNDED,
      };
    } else {
      // 驳回退款
      await this.db
        .update(orderRefunds)
        .set({
          status: 'REJECTED',
          arbitratedBy: auditorUserId,
          updatedAt: new Date(),
        })
        .where(eq(orderRefunds.id, refund.id));

      await this.db
        .update(orders)
        .set({
          orderStatus: OrderStatus.PAID,
          updatedAt: new Date(),
        })
        .where(eq(orders.id, refund.orderId));

      return {
        success: true,
        refundStatus: 'REJECTED',
        orderStatus: OrderStatus.PAID,
        rejectReason: dto.rejectReason,
      };
    }
  }

  /**
   * 预约服务改约
   */
  async rescheduleAppointment(dto: RescheduleAppointmentDto) {
    const orderList = await this.db
      .select()
      .from(orders)
      .where(eq(orders.id, dto.orderId))
      .limit(1);

    const order = orderList[0];
    if (!order) {
      throw new NotFoundException('预约订单不存在');
    }

    if (order.tradeType !== TradeType.APPOINTMENT) {
      throw new BadRequestException('该订单非预约类服务');
    }

    if (order.orderStatus !== OrderStatus.PAID) {
      throw new BadRequestException('只有已付款待履约的预约单可改约');
    }

    const newTime = new Date(dto.newAppointmentTime);
    if (newTime.getTime() <= Date.now()) {
      throw new BadRequestException('新的预约时间必须为未来时间');
    }

    await this.db
      .update(orders)
      .set({
        appointmentTime: newTime,
        updatedAt: new Date(),
      })
      .where(eq(orders.id, order.id));

    return {
      success: true,
      orderId: order.id,
      rescheduledTime: newTime.toISOString(),
    };
  }

  /**
   * 移动收银台动态收款码生成
   */
  async generateCashierQr(dto: GenerateCashierQrDto) {
    const payTradeNo = `POS${Date.now()}${Math.floor(1000 + Math.random() * 9000)}`;
    const qrPayload = JSON.stringify({
      payTradeNo,
      shopId: dto.shopId,
      amount: dto.amount,
      subject: dto.subject,
      expiresAt: new Date(Date.now() + 15 * 60 * 1000).toISOString(),
    });

    return {
      payTradeNo,
      amount: dto.amount,
      qrPayload,
      expiresInSeconds: 900,
    };
  }

  /**
   * 移动收银台原子防重扫码核销 (Redis Redlock 分布式防重锁)
   */
  async verifyOrderByCode(verifyCode: string, clerkUserId: string) {
    const lockKey = `lock:verify:${verifyCode}`;

    let lockAcquired = false;
    try {
      const res = await this.redis.set(lockKey, clerkUserId, 'EX', 5, 'NX');
      lockAcquired = res === 'OK';
    } catch {
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
