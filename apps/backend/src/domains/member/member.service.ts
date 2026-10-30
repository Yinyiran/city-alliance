import { Injectable, BadRequestException, NotFoundException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { members, memberUpgradeRecords, users, shops, stores } from '../../infrastructure/drizzle/schema/index';
import { eq, or, like, and, desc, sql, inArray } from 'drizzle-orm';
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

  /**
   * 分页多维度查询会员列表 (带用户画像、归属店铺、上级合伙人)
   */
  async getMemberList(query: {
    keyword?: string;
    level?: string;
    page?: number;
    pageSize?: number;
  }) {
    const page = Number(query.page) || 1;
    const pageSize = Number(query.pageSize) || 20;
    const offset = (page - 1) * pageSize;

    const conditions = [];

    if (query.keyword?.trim()) {
      const kw = `%${query.keyword.trim()}%`;
      conditions.push(
        or(
          like(members.memberCode, kw),
          like(members.realName, kw),
          like(users.username, kw),
          like(users.nickname, kw),
          like(users.mobile, kw),
        ),
      );
    }

    if (query.level) {
      conditions.push(eq(members.level, query.level));
    }

    const whereClause = conditions.length > 0 ? and(...conditions) : undefined;

    const rows = await this.db
      .select({
        id: members.id,
        memberCode: members.memberCode,
        userId: members.userId,
        level: members.level,
        inviterId: members.inviterId,
        bindShopId: members.bindShopId,
        bindStoreId: members.bindStoreId,
        points: members.points,
        balance: members.balance,
        frozenBalance: members.frozenBalance,
        totalEarned: members.totalEarned,
        realName: members.realName,
        idCardNo: members.idCardNo,
        employmentType: members.employmentType,
        isRealNameVerified: members.isRealNameVerified,
        status: members.status,
        createdAt: members.createdAt,
        nickname: users.nickname,
        username: users.username,
        mobile: users.mobile,
        avatarUrl: users.avatarUrl,
        shopName: shops.name,
        storeName: stores.name,
      })
      .from(members)
      .leftJoin(users, eq(members.userId, users.id))
      .leftJoin(shops, eq(members.bindShopId, shops.id))
      .leftJoin(stores, eq(members.bindStoreId, stores.id))
      .where(whereClause)
      .orderBy(desc(members.createdAt))
      .limit(pageSize)
      .offset(offset);

    // 查询上级邀约人信息
    const inviterIds = Array.from(
      new Set(rows.map((r) => r.inviterId).filter((id): id is string => !!id)),
    );

    let inviterMap: Record<string, { code: string; name: string }> = {};
    if (inviterIds.length > 0) {
      const inviterRows = await this.db
        .select({
          id: members.id,
          memberCode: members.memberCode,
          realName: members.realName,
          nickname: users.nickname,
        })
        .from(members)
        .leftJoin(users, eq(members.userId, users.id))
        .where(inArray(members.id, inviterIds));

      for (const inv of inviterRows) {
        inviterMap[inv.id] = {
          code: inv.memberCode,
          name: inv.realName || inv.nickname || `合伙人(${inv.memberCode})`,
        };
      }
    }

    const countResult = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(members)
      .leftJoin(users, eq(members.userId, users.id))
      .where(whereClause);

    const total = Number(countResult[0]?.count || 0);

    const items = rows.map((r) => {
      const inv = r.inviterId ? inviterMap[r.inviterId] : null;
      return {
        ...r,
        inviterCode: inv?.code || null,
        inviterName: inv?.name || null,
      };
    });

    return {
      items,
      total,
      page,
      pageSize,
    };
  }

  /**
   * 老板自主定价开通合伙人 (主理人50% + 分公司50% 实时清算)
   */
  async customPartnerUpgrade(
    dto: { memberCode: string; price: string; remark?: string },
    operatorUserId: string,
  ) {
    const memberList = await this.db
      .select()
      .from(members)
      .where(eq(members.memberCode, dto.memberCode.trim()))
      .limit(1);

    const member = memberList[0];
    if (!member) {
      throw new NotFoundException(`未找到编号为 ${dto.memberCode} 的会员`);
    }

    const priceNum = Number(dto.price);
    if (isNaN(priceNum) || priceNum <= 0) {
      throw new BadRequestException('开通金额必须大于 0 元');
    }

    const managerReward = (priceNum * 0.5).toFixed(2);
    const branchReward = (priceNum * 0.5).toFixed(2);

    const [record] = await this.db
      .insert(memberUpgradeRecords)
      .values({
        memberId: member.id,
        fromLevel: member.level,
        toLevel: MemberLevel.PARTNER,
        payAmount: priceNum.toFixed(2),
        paymentChannel: PaymentChannel.BALANCE_PAY,
        inviterId: member.inviterId || null,
        inviterReward: '0.00',
        shopManagerReward: managerReward,
        branchReward: branchReward,
        headquarterReward: '0.00',
        status: 'PAID',
      })
      .returning();

    // 升级为创客合伙人并赠送 10000 权益积分
    await this.db
      .update(members)
      .set({
        level: MemberLevel.PARTNER,
        points: member.points + 10000,
        updatedAt: new Date(),
      })
      .where(eq(members.id, member.id));

    return {
      recordId: record.id,
      memberCode: member.memberCode,
      level: MemberLevel.PARTNER,
      price: dto.price,
      managerReward,
      branchReward,
      giftPoints: 10000,
    };
  }
}
