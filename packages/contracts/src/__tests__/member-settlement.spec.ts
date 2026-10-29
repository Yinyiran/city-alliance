import { describe, it, expect } from 'vitest';
import {
  MemberProfileSchema,
  MemberEquitySchema,
  MemberLevel,
  CommissionType,
  CalculateCommissionSchema,
} from '../index.js';

describe('Member & Settlement Contracts Specification', () => {
  it('should validate 6-digit member code properly', () => {
    const validMember = {
      id: 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11',
      memberCode: '100088',
      userId: 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22',
      nickname: '测试会员',
      mobile: '13800138000',
      level: MemberLevel.NORMAL,
      points: 100,
      balance: '50.00',
      frozenBalance: '20.00',
      totalEarned: '70.00',
      status: 'ACTIVE' as const,
      isRealNameVerified: false,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    };

    const parsed = MemberProfileSchema.safeParse(validMember);
    expect(parsed.success).toBe(true);

    // Invalid member code (e.g. 5 digits or non-numeric)
    const invalidMember = { ...validMember, memberCode: '12345' };
    expect(MemberProfileSchema.safeParse(invalidMember).success).toBe(false);

    const nonNumericMember = { ...validMember, memberCode: 'ABC123' };
    expect(MemberProfileSchema.safeParse(nonNumericMember).success).toBe(false);
  });

  it('should validate MemberEquity configurations across 4 tiers', () => {
    const tiers = [
      {
        level: MemberLevel.NORMAL,
        levelName: '普通会员',
        price: '0.00',
        discountRate: 1.0,
        giftPoints: 0,
        pointsRatio: 1.0,
        inviteDiamondRewardPercent: 10,
        inviteBlackDiamondRewardPercent: 15,
        invitePartnerRewardPercent: 20,
        consumptionCommissionPercent: 0,
      },
      {
        level: MemberLevel.DIAMOND,
        levelName: '钻石会员',
        price: '399.00',
        discountRate: 0.98,
        giftPoints: 1000,
        pointsRatio: 1.2,
        inviteDiamondRewardPercent: 15,
        inviteBlackDiamondRewardPercent: 20,
        invitePartnerRewardPercent: 25,
        consumptionCommissionPercent: 0.2,
      },
      {
        level: MemberLevel.BLACK_DIAMOND,
        levelName: '黑钻会员',
        price: '699.00',
        discountRate: 0.96,
        giftPoints: 3000,
        pointsRatio: 1.3,
        inviteDiamondRewardPercent: 20,
        inviteBlackDiamondRewardPercent: 25,
        invitePartnerRewardPercent: 30,
        consumptionCommissionPercent: 0.3,
      },
      {
        level: MemberLevel.PARTNER,
        levelName: '创客合伙人',
        price: '1999.00',
        discountRate: 0.95,
        giftPoints: 10000,
        pointsRatio: 1.5,
        inviteDiamondRewardPercent: 25,
        inviteBlackDiamondRewardPercent: 30,
        invitePartnerRewardPercent: 35,
        consumptionCommissionPercent: 0.5,
      },
    ];

    tiers.forEach((tier) => {
      const parsed = MemberEquitySchema.safeParse(tier);
      expect(parsed.success).toBe(true);
    });
  });

  it('should validate commission calculation schema for self and cross-shop orders', () => {
    const selfOrder = {
      orderId: 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11',
      totalAmount: '200.00',
      shopId: 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22',
      customerId: 'c0eebc99-9c0b-4ef8-bb6d-6bb9bd380a33',
      commissionType: CommissionType.DAILY_CONSUMPTION_SELF,
      customerMemberLevel: MemberLevel.DIAMOND,
    };
    expect(CalculateCommissionSchema.safeParse(selfOrder).success).toBe(true);

    const crossOrder = {
      ...selfOrder,
      commissionType: CommissionType.DAILY_CONSUMPTION_CROSS,
      inviterId: 'd0eebc99-9c0b-4ef8-bb6d-6bb9bd380a44',
    };
    expect(CalculateCommissionSchema.safeParse(crossOrder).success).toBe(true);
  });
});
