import { Controller, Post, Body, Param, UseGuards, UsePipes } from '@nestjs/common';
import { TradeService, CreateOrderParams } from './trade.service';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';
import { PaymentChannel } from '@city-alliance/contracts';

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
