import { Controller, Post, Body, Get, Param, Query, UseGuards, UsePipes } from '@nestjs/common';
import { SettleService } from './settle.service';
import {
  CalculateCommissionDto,
  CalculateCommissionSchema,
  ApplyWithdrawDto,
  ApplyWithdrawSchema,
  PartnerDirectGrantDto,
  PartnerDirectGrantSchema,
  MemberLevel,
} from '@city-alliance/contracts';
import { ZodValidationPipe } from '../../common/pipes/zod-validation.pipe';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Controller('settle')
@UseGuards(JwtAuthGuard)
export class SettleController {
  constructor(private readonly settleService: SettleService) {}

  @Get('equities')
  getMemberEquities() {
    return {
      code: 200,
      data: this.settleService.memberEquityMap,
    };
  }

  @Post('calculate-commission')
  @UsePipes(new ZodValidationPipe(CalculateCommissionSchema))
  calculateCommission(@Body() dto: CalculateCommissionDto) {
    const isCross = dto.commissionType === 'DAILY_CONSUMPTION_CROSS';
    const result = this.settleService.calculateConsumptionCommission(
      Number(dto.totalAmount),
      isCross,
      dto.customerMemberLevel,
    );
    return {
      code: 200,
      data: result,
    };
  }

  @Post('tax-estimate')
  estimateTax(@Body('amount') amount: string) {
    const num = Number(amount);
    const result = this.settleService.calculateTaxAndNetAmount(num);
    return {
      code: 200,
      data: result,
    };
  }

  @Post('partner-grant')
  @UsePipes(new ZodValidationPipe(PartnerDirectGrantSchema))
  grantPartner(@Body() dto: PartnerDirectGrantDto, @CurrentUser() user: AuthenticatedUser) {
    const result = this.settleService.calculatePartnerCustomCommission(Number(dto.customPrice));
    return {
      code: 200,
      message: '合伙人开通分成试算成功',
      data: result,
    };
  }

  @Get('commissions')
  async getCommissions(
    @Query('page') page?: number,
    @Query('pageSize') pageSize?: number,
    @Query('status') status?: string,
  ) {
    const data = await this.settleService.getCommissions({ page, pageSize, status });
    return {
      code: 200,
      data,
    };
  }

  @Get('withdrawals')
  async getWithdrawals(
    @Query('page') page?: number,
    @Query('pageSize') pageSize?: number,
    @Query('status') status?: string,
  ) {
    const data = await this.settleService.getWithdrawals({ page, pageSize, status });
    return {
      code: 200,
      data,
    };
  }

  @Post('withdrawals/:id/audit')
  async auditWithdrawal(
    @Param('id') id: string,
    @Body('approved') approved: boolean,
    @Body('auditNote') auditNote: string,
    @CurrentUser() user: AuthenticatedUser,
  ) {
    const result = await this.settleService.auditWithdrawal(
      id,
      approved,
      auditNote || '',
      user.id,
    );
    return {
      code: 200,
      message: approved ? '提现申请已审核通过，已转入对公打款队列' : '提现申请已驳回',
      data: result,
    };
  }

  @Post('withdrawals/:id/pay')
  async payWithdrawal(
    @Param('id') id: string,
    @CurrentUser() user: AuthenticatedUser,
  ) {
    const result = await this.settleService.payWithdrawal(id, user.id);
    return {
      code: 200,
      message: '对公放款成功，税金合规档案已自动生成归档',
      data: result,
    };
  }
}
