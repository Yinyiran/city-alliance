import { Controller, Post, Body, Get, Param, UseGuards, UsePipes } from '@nestjs/common';
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
}
