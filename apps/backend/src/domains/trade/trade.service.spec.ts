import { describe, it, expect, beforeEach, vi } from 'vitest';
import { TradeService } from './trade.service';
import {
  TradeType,
  MemberLevel,
  OrderStatus,
  PaymentChannel,
  SplitCartOrderDto,
} from '@city-alliance/contracts';
import { SettleService } from '../settle/settle.service';

describe('TradeService - Phase 2 Omnichannel FSM & POS Cashier Engine', () => {
  let service: TradeService;
  let settleService: SettleService;
  let mockDb: any;
  let mockRedis: any;

  beforeEach(() => {
    settleService = new SettleService();
    mockDb = {
      select: vi.fn(),
      insert: vi.fn(),
      update: vi.fn(),
    };
    mockRedis = {
      set: vi.fn().mockResolvedValue('OK'),
      del: vi.fn().mockResolvedValue(1),
    };
    service = new TradeService(mockDb, mockRedis, settleService);
  });

  describe('Member Discounts Calculation in Trade', () => {
    it('钻石会员享受折上折 98 折', () => {
      const equity = settleService.memberEquityMap[MemberLevel.DIAMOND];
      const totalAmount = 100;
      const payAmount = Number((totalAmount * equity.discountRate).toFixed(2));
      expect(payAmount).toBe(98);
    });

    it('合伙人享受折上折 95 折', () => {
      const equity = settleService.memberEquityMap[MemberLevel.PARTNER];
      const totalAmount = 200;
      const payAmount = Number((totalAmount * equity.discountRate).toFixed(2));
      expect(payAmount).toBe(190);
    });
  });

  describe('Cart Splitting Engine (跨店铺购物车拆单)', () => {
    it('应将包含多个店铺的购物车商品自动拆分为各自独立的子订单', async () => {
      // 模拟创建单个订单返回结果
      const mockCreatedOrder1 = {
        id: 'ord-1',
        orderNo: 'CA202610290001',
        totalAmount: '100.00',
        discountAmount: '0.00',
        payAmount: '100.00',
        orderStatus: OrderStatus.PENDING_PAY,
      };
      const mockCreatedOrder2 = {
        id: 'ord-2',
        orderNo: 'CA202610290002',
        totalAmount: '200.00',
        discountAmount: '0.00',
        payAmount: '200.00',
        orderStatus: OrderStatus.PENDING_PAY,
      };

      vi.spyOn(service, 'createOrder')
        .mockResolvedValueOnce({
          orderId: 'ord-1',
          orderNo: 'CA202610290001',
          totalAmount: '100.00',
          payAmount: '100.00',
          verifyCode: null,
          status: OrderStatus.PENDING_PAY,
        })
        .mockResolvedValueOnce({
          orderId: 'ord-2',
          orderNo: 'CA202610290002',
          totalAmount: '200.00',
          payAmount: '200.00',
          verifyCode: null,
          status: OrderStatus.PENDING_PAY,
        });

      const splitDto: SplitCartOrderDto = {
        customerId: 'a0000000-0000-0000-0000-000000000001',
        paymentChannel: PaymentChannel.WECHAT_PAY,
        shopGroups: [
          {
            shopId: 'b0000000-0000-0000-0000-000000000001',
            tradeType: TradeType.EXPRESS,
            items: [
              {
                goodsId: 'c0000000-0000-0000-0000-000000000001',
                skuId: 'd0000000-0000-0000-0000-000000000001',
                goodsName: '店铺A商品',
                skuName: '规格1',
                price: '100.00',
                quantity: 1,
              },
            ],
          },
          {
            shopId: 'b0000000-0000-0000-0000-000000000002',
            tradeType: TradeType.TAKEOUT,
            items: [
              {
                goodsId: 'c0000000-0000-0000-0000-000000000002',
                skuId: 'd0000000-0000-0000-0000-000000000002',
                goodsName: '店铺B外卖',
                skuName: '单人餐',
                price: '200.00',
                quantity: 1,
              },
            ],
          },
        ],
      };

      const result = await service.splitCartAndCreateOrders(splitDto);

      expect(result.subOrderCount).toBe(2);
      expect(result.totalPayAmount).toBe('300.00');
      expect(result.parentPayTradeNo).toMatch(/^PAY\d+/);
      expect(result.orders.length).toBe(2);
    });
  });

  describe('FSM Order Lifecycle State Transitions', () => {
    it('快递发货订单状态扭转 PAID -> DELIVERING', async () => {
      const mockOrder = {
        id: 'ord-express-1',
        tradeType: TradeType.EXPRESS,
        orderStatus: OrderStatus.PAID,
      };

      mockDb.select.mockReturnValue({
        from: () => ({
          where: () => ({
            limit: () => [mockOrder],
          }),
        }),
      });

      mockDb.update.mockReturnValue({
        set: () => ({
          where: vi.fn().mockResolvedValue(true),
        }),
      });

      const res = await service.shipOrder({
        orderId: 'ord-express-1',
        expressCompany: '顺丰速运',
        expressNo: 'SF1234567890',
      });

      expect(res.success).toBe(true);
      expect(res.orderStatus).toBe(OrderStatus.DELIVERING);
      expect(res.expressNo).toBe('SF1234567890');
    });

    it('同城外卖调度骑手状态扭转 PAID -> DELIVERING', async () => {
      const mockOrder = {
        id: 'ord-takeout-1',
        tradeType: TradeType.TAKEOUT,
        orderStatus: OrderStatus.PAID,
      };

      mockDb.select.mockReturnValue({
        from: () => ({
          where: () => ({
            limit: () => [mockOrder],
          }),
        }),
      });

      mockDb.update.mockReturnValue({
        set: () => ({
          where: vi.fn().mockResolvedValue(true),
        }),
      });

      const res = await service.dispatchTakeaway({
        orderId: 'ord-takeout-1',
        riderName: '李骑手',
        riderPhone: '13800138000',
        estimatedDeliveryMinutes: 25,
      });

      expect(res.success).toBe(true);
      expect(res.orderStatus).toBe(OrderStatus.DELIVERING);
      expect(res.rider).toBe('李骑手');
    });

    it('配送中确认收货 DELIVERING -> COMPLETED', async () => {
      const mockOrder = {
        id: 'ord-delivering-1',
        orderStatus: OrderStatus.DELIVERING,
      };

      mockDb.select.mockReturnValue({
        from: () => ({
          where: () => ({
            limit: () => [mockOrder],
          }),
        }),
      });

      mockDb.update.mockReturnValue({
        set: () => ({
          where: vi.fn().mockResolvedValue(true),
        }),
      });

      const res = await service.confirmReceived('ord-delivering-1');
      expect(res.success).toBe(true);
      expect(res.orderStatus).toBe(OrderStatus.COMPLETED);
    });

    it('未付款订单可主动取消 PENDING_PAY -> CANCELLED', async () => {
      const mockOrder = {
        id: 'ord-pending-1',
        orderStatus: OrderStatus.PENDING_PAY,
      };

      mockDb.select.mockReturnValue({
        from: () => ({
          where: () => ({
            limit: () => [mockOrder],
          }),
        }),
      });

      mockDb.update.mockReturnValue({
        set: () => ({
          where: vi.fn().mockResolvedValue(true),
        }),
      });

      const res = await service.cancelOrder('ord-pending-1', '不想买了');
      expect(res.success).toBe(true);
      expect(res.status).toBe(OrderStatus.CANCELLED);
    });
  });

  describe('Refund and Arbitration FSM', () => {
    it('退款审核同意后应将订单置为 REFUNDED 并冲正冻结的分润流水', async () => {
      const mockRefund = {
        id: 'ref-1',
        orderId: 'ord-refund-1',
        refundAmount: '99.00',
        status: 'PENDING_AUDIT',
      };

      mockDb.select.mockReturnValue({
        from: () => ({
          where: () => ({
            limit: () => [mockRefund],
          }),
        }),
      });

      mockDb.update.mockReturnValue({
        set: () => ({
          where: vi.fn().mockResolvedValue(true),
        }),
      });

      const res = await service.auditRefund(
        { refundId: 'ref-1', approved: true, isPlatformArbitration: false },
        'auditor-1',
      );

      expect(res.success).toBe(true);
      expect(res.refundStatus).toBe('REFUNDED');
      expect(res.orderStatus).toBe(OrderStatus.REFUNDED);
    });
  });

  describe('Appointment Reschedule', () => {
    it('未来时间合法预约改约成功', async () => {
      const mockOrder = {
        id: 'ord-appoint-1',
        tradeType: TradeType.APPOINTMENT,
        orderStatus: OrderStatus.PAID,
      };

      mockDb.select.mockReturnValue({
        from: () => ({
          where: () => ({
            limit: () => [mockOrder],
          }),
        }),
      });

      mockDb.update.mockReturnValue({
        set: () => ({
          where: vi.fn().mockResolvedValue(true),
        }),
      });

      const futureDate = new Date(Date.now() + 86400000 * 2).toISOString();
      const res = await service.rescheduleAppointment({
        orderId: 'ord-appoint-1',
        newAppointmentTime: futureDate,
      });

      expect(res.success).toBe(true);
      expect(res.rescheduledTime).toBe(new Date(futureDate).toISOString());
    });
  });

  describe('Cashier Dynamic QR Generation', () => {
    it('生成符合标准的 15 分钟有效动态收款二维码', async () => {
      const res = await service.generateCashierQr({
        shopId: 'shop-pos-1',
        amount: '128.50',
        subject: '到店快餐买单',
      });

      expect(res.payTradeNo).toMatch(/^POS\d+/);
      expect(res.amount).toBe('128.50');
      expect(res.expiresInSeconds).toBe(900);
      const parsed = JSON.parse(res.qrPayload);
      expect(parsed.amount).toBe('128.50');
      expect(parsed.shopId).toBe('shop-pos-1');
    });
  });
});
