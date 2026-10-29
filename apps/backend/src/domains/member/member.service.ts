import { Injectable, BadRequestException, NotFoundException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { members, memberUpgradeRecords } from '../../infrastructure/drizzle/schema/index';
import { eq } from 'drizzle-orm';
import {
  MemberLevel,
  UpgradeMemberOrderDto,
  MemberRealNameAuthDto,
  PaymentChannel,
} from '@city-alliance/contracts';
import { SettleService } from '../settle/settle.service';

@Injectable()
export class MemberService {
  constructor(
    @Inject(DRIZZLE_DB) private readonly db: DrizzleDatabase,
    private readonly settleService: SettleService,
  ) {}

  /**
   * 生成唯一 6 位数字会员码
   */
  async generateUniqueMemberCode(): Promise<string> {
    for (let i = 0; i < 10; i++) {
      const code = Math.floor(100000 + Math.random() * 900000).toString();
      const existing = await this.db
        .select({ id: members.id })
        .from(members)
        .where(eq(members.memberCode, code))
        .limit(1);
      if (existing.length === 0) {
        return code;
      }
    }
    // 保底毫秒取后6位
    return Date.now().toString().slice(-6);
  }

  /**
   * 获取会员个人档案
   */
  async getMemberProfile(memberId: string) {
    const list = await this.db
      .select()
      .from(members)
      .where(eq(members.id, memberId))
      .limit(1);

    const member = list[0];
    if (!member) {
      throw new NotFoundException('会员档案不存在');
    }

    const equity = this.settleService.memberEquityMap[member.level as MemberLevel];
    return {
      ...member,
      equity,
    };
  }

  /**
   * 会员开通与升级 (触发四级分成演算与入账)
   */
  async upgradeMember(
    memberId: string,
    dto: UpgradeMemberOrderDto,
  ) {
    const member = await this.getMemberProfile(memberId);
    const targetEquity = this.settleService.memberEquityMap[dto.targetLevel];
    const payAmount = Number(targetEquity.price);

    // 查询上级邀约人等级
    let inviterLevel = MemberLevel.NORMAL;
    if (member.inviterId) {
      const inviterList = await this.db
        .select({ level: members.level })
        .from(members)
        .where(eq(members.id, member.inviterId))
        .limit(1);
      if (inviterList.length > 0) {
        inviterLevel = inviterList[0].level as MemberLevel;
      }
    }

    // 演算分成明细
    const splitResult = this.settleService.calculateMemberUpgradeCommission(
      payAmount,
      dto.targetLevel,
      inviterLevel,
    );

    const inviterSplit = splitResult.splits.find((s) => s.role === 'INVITER');
    const managerSplit = splitResult.splits.find((s) => s.role === 'SHOP_MANAGER');
    const branchSplit = splitResult.splits.find((s) => s.role === 'CITY_BRANCH');
    const hqSplit = splitResult.splits.find((s) => s.role === 'HEADQUARTERS');

    // 记录开通流水并更新等级
    const [record] = await this.db
      .insert(memberUpgradeRecords)
      .values({
        memberId: member.id,
        fromLevel: member.level,
        toLevel: dto.targetLevel,
        payAmount: payAmount.toFixed(2),
        paymentChannel: dto.paymentChannel,
        inviterId: member.inviterId || null,
        inviterReward: (inviterSplit?.amount || 0).toFixed(2),
        shopManagerReward: (managerSplit?.amount || 0).toFixed(2),
        branchReward: (branchSplit?.amount || 0).toFixed(2),
        headquarterReward: (hqSplit?.amount || 0).toFixed(2),
        status: 'PAID',
      })
      .returning();

    // 更新会员等级并赠送权益积分
    await this.db
      .update(members)
      .set({
        level: dto.targetLevel,
        points: member.points + targetEquity.giftPoints,
        updatedAt: new Date(),
      })
      .where(eq(members.id, member.id));

    return {
      recordId: record.id,
      fromLevel: member.level,
      toLevel: dto.targetLevel,
      giftPoints: targetEquity.giftPoints,
      splits: splitResult.splits,
    };
  }

  /**
   * 实名核身认证
   */
  async verifyRealName(memberId: string, dto: MemberRealNameAuthDto) {
    await this.db
      .update(members)
      .set({
        realName: dto.realName,
        idCardNo: dto.idCardNo,
        employmentType: dto.employmentType,
        isRealNameVerified: true,
        updatedAt: new Date(),
      })
      .where(eq(members.id, memberId));

    return {
      success: true,
      message: '实名认证成功',
    };
  }
}
