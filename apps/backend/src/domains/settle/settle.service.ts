import { Injectable, BadRequestException } from '@nestjs/common';
import {
  MemberLevel,
  CommissionType,
  MemberEquityDto,
} from '@city-alliance/contracts';

export interface CommissionSplitResult {
  totalAmount: number;
  commissionType: CommissionType;
  splits: {
    role: 'INVITER' | 'SHOP_MANAGER' | 'CITY_BRANCH' | 'HEADQUARTERS';
    ratioPercent: number;
    amount: number;
    isFrozen: boolean;
    freezeDays: number;
  }[];
  merchantPayoutAmount: number; // 商家实际到手金额
}

export interface TaxCalculationResult {
  grossAmount: number;
  costDeduction: number; // 20% 成本扣除额
  taxableIncome: number; // 应纳税所得额
  taxRatePercent: number; // 预扣税率: 20% / 30% / 40%
  quickDeduction: number; // 速算扣除数: 0 / 2000 / 7000
  taxAmount: number; // 实扣个人所得税
  platformFee: number; // 平台手续费 (0.35%)
  netAmount: number; // 实际到账金额
}

@Injectable()
export class SettleService {
  /**
   * 四级会员权益配置字典 (SSOT 基准)
   */
  readonly memberEquityMap: Record<MemberLevel, MemberEquityDto> = {
    [MemberLevel.NORMAL]: {
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
      minWithdrawAmount: 100,
      freezeDays: 7,
    },
    [MemberLevel.DIAMOND]: {
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
      minWithdrawAmount: 100,
      freezeDays: 7,
    },
    [MemberLevel.BLACK_DIAMOND]: {
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
      minWithdrawAmount: 100,
      freezeDays: 7,
    },
    [MemberLevel.PARTNER]: {
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
      minWithdrawAmount: 100,
      freezeDays: 7,
    },
  };

  /**
   * 1. 会员购买与升级多级分成计算
   * (邀约人比例奖励 + 主理人25% + 城市分公司25% + 平台总部留存)
   */
  calculateMemberUpgradeCommission(
    payAmount: number,
    targetLevel: MemberLevel,
    inviterLevel: MemberLevel = MemberLevel.NORMAL,
  ): CommissionSplitResult {
    if (payAmount <= 0) {
      throw new BadRequestException('升级金额必须大于0');
    }

    const equity = this.memberEquityMap[inviterLevel];
    let inviterRatio = 0;
    if (targetLevel === MemberLevel.DIAMOND) {
      inviterRatio = equity.inviteDiamondRewardPercent;
    } else if (targetLevel === MemberLevel.BLACK_DIAMOND) {
      inviterRatio = equity.inviteBlackDiamondRewardPercent;
    } else if (targetLevel === MemberLevel.PARTNER) {
      inviterRatio = equity.invitePartnerRewardPercent;
    }

    const managerRatio = 25; // 主理人 25%
    const branchRatio = 25; // 分公司 25%
    const headquarterRatio = Number(
      (100 - inviterRatio - managerRatio - branchRatio).toFixed(2),
    );

    const inviterAmount = Number(((payAmount * inviterRatio) / 100).toFixed(2));
    const managerAmount = Number(((payAmount * managerRatio) / 100).toFixed(2));
    const branchAmount = Number(((payAmount * branchRatio) / 100).toFixed(2));
    const headquarterAmount = Number(
      (payAmount - inviterAmount - managerAmount - branchAmount).toFixed(2),
    );

    return {
      totalAmount: payAmount,
      commissionType: CommissionType.MEMBER_UPGRADE,
      splits: [
        {
          role: 'INVITER',
          ratioPercent: inviterRatio,
          amount: inviterAmount,
          isFrozen: true,
          freezeDays: 7,
        },
        {
          role: 'SHOP_MANAGER',
          ratioPercent: managerRatio,
          amount: managerAmount,
          isFrozen: false,
          freezeDays: 0,
        },
        {
          role: 'CITY_BRANCH',
          ratioPercent: branchRatio,
          amount: branchAmount,
          isFrozen: false,
          freezeDays: 0,
        },
        {
          role: 'HEADQUARTERS',
          ratioPercent: headquarterRatio,
          amount: headquarterAmount,
          isFrozen: false,
          freezeDays: 0,
        },
      ],
      merchantPayoutAmount: 0,
    };
  }

  /**
   * 2. 店铺老板自定价主动开通合伙人分成计算
   * (仅走主理人50% 与 城市分公司50%)
   */
  calculatePartnerCustomCommission(customPrice: number): CommissionSplitResult {
    if (customPrice <= 0) {
      throw new BadRequestException('开通金额必须大于0');
    }

    const managerRatio = 50;
    const branchRatio = 50;
    const managerAmount = Number(((customPrice * managerRatio) / 100).toFixed(2));
    const branchAmount = Number((customPrice - managerAmount).toFixed(2));

    return {
      totalAmount: customPrice,
      commissionType: CommissionType.PARTNER_FEE,
      splits: [
        {
          role: 'SHOP_MANAGER',
          ratioPercent: managerRatio,
          amount: managerAmount,
          isFrozen: false,
          freezeDays: 0,
        },
        {
          role: 'CITY_BRANCH',
          ratioPercent: branchRatio,
          amount: branchAmount,
          isFrozen: false,
          freezeDays: 0,
        },
      ],
      merchantPayoutAmount: 0,
    };
  }

  /**
   * 3. 商家日常消费佣金分润计算 (自签单 2% vs 跨店带单 6%)
   */
  calculateConsumptionCommission(
    orderAmount: number,
    isCrossShop: boolean,
    customerMemberLevel: MemberLevel = MemberLevel.NORMAL,
  ): CommissionSplitResult {
    if (orderAmount <= 0) {
      throw new BadRequestException('订单金额必须大于0');
    }

    if (!isCrossShop) {
      // 自签单 (平台扣 2%: 分公司 0.5% + 总部 1.5%)
      const branchRatio = 0.5;
      const headquarterRatio = 1.5;
      const branchAmount = Number(((orderAmount * branchRatio) / 100).toFixed(2));
      const headquarterAmount = Number(
        ((orderAmount * headquarterRatio) / 100).toFixed(2),
      );
      const feeAmount = Number((branchAmount + headquarterAmount).toFixed(2));
      const merchantPayout = Number((orderAmount - feeAmount).toFixed(2));

      return {
        totalAmount: orderAmount,
        commissionType: CommissionType.DAILY_CONSUMPTION_SELF,
        splits: [
          {
            role: 'CITY_BRANCH',
            ratioPercent: branchRatio,
            amount: branchAmount,
            isFrozen: false,
            freezeDays: 0,
          },
          {
            role: 'HEADQUARTERS',
            ratioPercent: headquarterRatio,
            amount: headquarterAmount,
            isFrozen: false,
            freezeDays: 0,
          },
        ],
        merchantPayoutAmount: merchantPayout,
      };
    } else {
      // 跨店带单 (总佣金 6%: 带单会员 0.2%~0.5% + 主理人 2% + 分公司 1.5% + 总部剩余)
      const equity = this.memberEquityMap[customerMemberLevel];
      const memberRatio = equity.consumptionCommissionPercent; // 0% / 0.2% / 0.3% / 0.5%
      const managerRatio = 2.0;
      const branchRatio = 1.5;
      const totalCommissionRatio = 6.0;
      const headquarterRatio = Number(
        (totalCommissionRatio - memberRatio - managerRatio - branchRatio).toFixed(3),
      );

      const memberAmount = Number(((orderAmount * memberRatio) / 100).toFixed(2));
      const managerAmount = Number(((orderAmount * managerRatio) / 100).toFixed(2));
      const branchAmount = Number(((orderAmount * branchRatio) / 100).toFixed(2));
      const headquarterAmount = Number(
        ((orderAmount * headquarterRatio) / 100).toFixed(2),
      );

      const totalDeducted = Number(
        (memberAmount + managerAmount + branchAmount + headquarterAmount).toFixed(2),
      );
      const merchantPayout = Number((orderAmount - totalDeducted).toFixed(2));

      return {
        totalAmount: orderAmount,
        commissionType: CommissionType.DAILY_CONSUMPTION_CROSS,
        splits: [
          {
            role: 'INVITER',
            ratioPercent: memberRatio,
            amount: memberAmount,
            isFrozen: true, // 带单佣金受7天消费状态机保护
            freezeDays: 7,
          },
          {
            role: 'SHOP_MANAGER',
            ratioPercent: managerRatio,
            amount: managerAmount,
            isFrozen: false,
            freezeDays: 0,
          },
          {
            role: 'CITY_BRANCH',
            ratioPercent: branchRatio,
            amount: branchAmount,
            isFrozen: false,
            freezeDays: 0,
          },
          {
            role: 'HEADQUARTERS',
            ratioPercent: headquarterRatio,
            amount: headquarterAmount,
            isFrozen: false,
            freezeDays: 0,
          },
        ],
        merchantPayoutAmount: merchantPayout,
      };
    }
  }

  /**
   * 4. 劳务报酬阶梯个税与提现净额演算
   * (800免征；800~4000减800；4000以上按80%计税；20%/30%/40%超额累进；0.35%平台手续费)
   */
  calculateTaxAndNetAmount(
    grossAmount: number,
    platformFeeRate: number = 0.0035, // 0.35% 手续费
  ): TaxCalculationResult {
    if (grossAmount <= 0) {
      throw new BadRequestException('提现金额必须大于0');
    }

    // 平台手续费
    const platformFee = Number((grossAmount * platformFeeRate).toFixed(2));

    let costDeduction = 0;
    let taxableIncome = 0;
    let taxRatePercent = 0;
    let quickDeduction = 0;
    let taxAmount = 0;

    if (grossAmount <= 800) {
      // 800元以内免征个人所得税
      taxableIncome = 0;
      taxRatePercent = 0;
      quickDeduction = 0;
      taxAmount = 0;
    } else if (grossAmount <= 4000) {
      // 800 ~ 4000: 定额减除800元费用
      costDeduction = 800;
      taxableIncome = Number((grossAmount - 800).toFixed(2));
      taxRatePercent = 20;
      quickDeduction = 0;
      taxAmount = Number(((taxableIncome * 20) / 100).toFixed(2));
    } else {
      // 4000元以上: 定率减除20%费用 (以80%为应纳税所得额)
      costDeduction = Number((grossAmount * 0.2).toFixed(2));
      taxableIncome = Number((grossAmount * 0.8).toFixed(2));

      if (taxableIncome <= 20000) {
        taxRatePercent = 20;
        quickDeduction = 0;
      } else if (taxableIncome <= 50000) {
        taxRatePercent = 30;
        quickDeduction = 2000;
      } else {
        taxRatePercent = 40;
        quickDeduction = 7000;
      }

      taxAmount = Number(
        (((taxableIncome * taxRatePercent) / 100) - quickDeduction).toFixed(2),
      );
      if (taxAmount < 0) taxAmount = 0;
    }

    const netAmount = Number(
      (grossAmount - platformFee - taxAmount).toFixed(2),
    );

    return {
      grossAmount,
      costDeduction,
      taxableIncome,
      taxRatePercent,
      quickDeduction,
      taxAmount,
      platformFee,
      netAmount,
    };
  }
}
