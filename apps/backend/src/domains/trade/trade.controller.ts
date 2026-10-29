import { Controller, Post, Body, Param, UseGuards } from '@nestjs/common';
import { TradeService, CreateOrderParams } from './trade.service';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';
import {
  PaymentChannel,
  SplitCartOrderDto,
  ShipOrderDto,
  TakeawayDispatchDto,
  ApplyRefundDto,
  AuditRefundDto,
  RescheduleAppointmentDto,
  GenerateCashierQrDto,
} from '@city-alliance/contracts';

@Controller('trade')
@UseGuards(JwtAuthGuard)
export class TradeController {
  constructor(private readonly tradeService: TradeService) {}

  @Post('orders')
  async createOrder(
    @Body() body: CreateOrderParams,
    @CurrentUser() user: AuthenticatedUser,
  ) {
    const params = {
      ...body,
      customerId: body.customerId || user.id,
    };
    const order = await this.tradeService.createOrder(params);
    return {
      code: 200,
      message: '订单创建成功',
      data: order,
    };
  }

  @Post('cart/split-checkout')
  async splitCartCheckout(
    @Body() body: SplitCartOrderDto,
    @CurrentUser() user: AuthenticatedUser,
  ) {
    const dto = {
      ...body,
      customerId: body.customerId || user.id,
    };
    const result = await this.tradeService.splitCartAndCreateOrders(dto);
    return {
      code: 200,
      message: '跨店铺拆单结算成功',
      data: result,
    };
  }

  @Post('orders/:id/pay-mock')
  async payMock(
    @Param('id') orderId: string,
    @Body('channel') channel?: PaymentChannel,
  ) {
    const result = await this.tradeService.handlePaymentSuccess(
      orderId,
      channel || PaymentChannel.WECHAT_PAY,
    );
    return {
      code: 200,
      message: '支付模拟成功，分润流水已生成',
      data: result,
    };
  }

  @Post('orders/:id/cancel')
  async cancelOrder(
    @Param('id') orderId: string,
    @Body('reason') reason?: string,
  ) {
    const result = await this.tradeService.cancelOrder(orderId, reason);
    return {
      code: 200,
      message: '订单取消成功',
      data: result,
    };
  }

  @Post('orders/ship')
  async shipOrder(@Body() body: ShipOrderDto) {
    const result = await this.tradeService.shipOrder(body);
    return {
      code: 200,
      message: '快递发货成功',
      data: result,
    };
  }

  @Post('orders/dispatch-takeaway')
  async dispatchTakeaway(@Body() body: TakeawayDispatchDto) {
    const result = await this.tradeService.dispatchTakeaway(body);
    return {
      code: 200,
      message: '同城外卖骑手调度成功',
      data: result,
    };
  }

  @Post('orders/:id/confirm-receipt')
  async confirmReceipt(@Param('id') orderId: string) {
    const result = await this.tradeService.confirmReceived(orderId);
    return {
      code: 200,
      message: '订单确认收货完成',
      data: result,
    };
  }

  @Post('refund/apply')
  async applyRefund(@Body() body: ApplyRefundDto) {
    const result = await this.tradeService.applyRefund(body);
    return {
      code: 200,
      message: '退款申请已提交',
      data: result,
    };
  }

  @Post('refund/audit')
  async auditRefund(
    @Body() body: AuditRefundDto,
    @CurrentUser() user: AuthenticatedUser,
  ) {
    const result = await this.tradeService.auditRefund(body, user.id);
    return {
      code: 200,
      message: '退款审核处理完成',
      data: result,
    };
  }

  @Post('appointment/reschedule')
  async rescheduleAppointment(@Body() body: RescheduleAppointmentDto) {
    const result = await this.tradeService.rescheduleAppointment(body);
    return {
      code: 200,
      message: '预约改约成功',
      data: result,
    };
  }

  @Post('cashier/qr')
  async generateCashierQr(@Body() body: GenerateCashierQrDto) {
    const result = await this.tradeService.generateCashierQr(body);
    return {
      code: 200,
      message: '动态收款码生成成功',
      data: result,
    };
  }

  @Post('verify')
  async verifyCode(
    @Body('verifyCode') verifyCode: string,
    @CurrentUser() user: AuthenticatedUser,
  ) {
    const result = await this.tradeService.verifyOrderByCode(
      verifyCode,
      user.id,
    );
    return {
      code: 200,
      message: '扫码核销成功',
      data: result,
    };
  }
}
