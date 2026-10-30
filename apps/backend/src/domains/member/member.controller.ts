import { Controller, Post, Get, Body, Param, Query, UseGuards, UsePipes } from '@nestjs/common';
import { MemberService } from './member.service';
import {
  UpgradeMemberOrderDto,
  UpgradeMemberOrderSchema,
  MemberRealNameAuthDto,
  MemberRealNameAuthSchema,
} from '@city-alliance/contracts';
import { ZodValidationPipe } from '../../common/pipes/zod-validation.pipe';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Controller('members')
@UseGuards(JwtAuthGuard)
export class MemberController {
  constructor(private readonly memberService: MemberService) {}

  @Get(['', 'list'])
  async getMembers(
    @Query() query: {
      keyword?: string;
      level?: string;
      page?: number;
      pageSize?: number;
    },
  ) {
    const data = await this.memberService.getMemberList(query);
    return {
      code: 200,
      data,
    };
  }

  @Post('custom-partner')
  async customPartner(
    @Body() body: { memberCode: string; price: string; remark?: string },
    @CurrentUser() user: AuthenticatedUser,
  ) {
    const data = await this.memberService.customPartnerUpgrade(body, user.id);
    return {
      code: 200,
      message: '创客合伙人自主开通成功，50/50 分润已实时清算',
      data,
    };
  }

  @Get('profile/:id')
  async getProfile(@Param('id') memberId: string) {
    const data = await this.memberService.getMemberProfile(memberId);
    return {
      code: 200,
      data,
    };
  }

  @Post(':id/upgrade')
  @UsePipes(new ZodValidationPipe(UpgradeMemberOrderSchema))
  async upgrade(
    @Param('id') memberId: string,
    @Body() dto: UpgradeMemberOrderDto,
  ) {
    const result = await this.memberService.upgradeMember(memberId, dto);
    return {
      code: 200,
      message: '会员开通成功，权益与分成已发放',
      data: result,
    };
  }

  @Post(':id/realname-auth')
  @UsePipes(new ZodValidationPipe(MemberRealNameAuthSchema))
  async realNameAuth(
    @Param('id') memberId: string,
    @Body() dto: MemberRealNameAuthDto,
  ) {
    const result = await this.memberService.verifyRealName(memberId, dto);
    return {
      code: 200,
      data: result,
    };
  }
}
