import { describe, it, expect, beforeEach } from 'vitest';
import { SettleService } from './settle.service';
import { MemberLevel, CommissionType } from '@city-alliance/contracts';

describe('SettleService - Financial & Commission Engine', () => {
  let service: SettleService;

  beforeEach(() => {
    service = new SettleService();
  });

  describe('Member Upgrade Commissions', () => {
    it('should calculate Diamond upgrade commission correctly for Normal inviter', () => {
      // Normal inviter gets 10% on Diamond upgrade (399 * 10% = 39.90)
      // Manager gets 25% (99.75)
      // Branch gets 25% (99.75)
      // Headquarters gets remainder: 399 - 39.90 - 99.75 - 99.75 = 159.60 (40%)
      const result = service.calculateMemberUpgradeCommission(
        399,
        MemberLevel.DIAMOND,
        MemberLevel.NORMAL,
      );

      expect(result.commissionType).toBe(CommissionType.MEMBER_UPGRADE);
      const inviter = result.splits.find((s) => s.role === 'INVITER')!;
      const manager = result.splits.find((s) => s.role === 'SHOP_MANAGER')!;
      const branch = result.splits.find((s) => s.role === 'CITY_BRANCH')!;
      const hq = result.splits.find((s) => s.role === 'HEADQUARTERS')!;

      expect(inviter.amount).toBe(39.9);
      expect(inviter.isFrozen).toBe(true);
      expect(manager.amount).toBe(99.75);
      expect(branch.amount).toBe(99.75);
      expect(hq.amount).toBe(159.6);
      expect(inviter.amount + manager.amount + branch.amount + hq.amount).toBe(399);
    });

    it('should calculate Partner upgrade commission correctly for Black Diamond inviter', () => {
      // Black Diamond inviter gets 30% on Partner upgrade (1999 * 30% = 599.70)
      // Manager 25% (499.75)
      // Branch 25% (499.75)
      // HQ remainder (399.80)
      const result = service.calculateMemberUpgradeCommission(
        1999,
        MemberLevel.PARTNER,
        MemberLevel.BLACK_DIAMOND,
      );

      const inviter = result.splits.find((s) => s.role === 'INVITER')!;
      const manager = result.splits.find((s) => s.role === 'SHOP_MANAGER')!;
      const branch = result.splits.find((s) => s.role === 'CITY_BRANCH')!;
      const hq = result.splits.find((s) => s.role === 'HEADQUARTERS')!;

      expect(inviter.amount).toBe(599.7);
      expect(manager.amount).toBe(499.75);
      expect(branch.amount).toBe(499.75);
      expect(hq.amount).toBe(399.8);
      expect(inviter.amount + manager.amount + branch.amount + hq.amount).toBe(1999);
    });

    it('should split custom partner fee 50/50 between manager and branch', () => {
      const result = service.calculatePartnerCustomCommission(1000);
      expect(result.splits).toHaveLength(2);
      const manager = result.splits.find((s) => s.role === 'SHOP_MANAGER')!;
      const branch = result.splits.find((s) => s.role === 'CITY_BRANCH')!;

      expect(manager.amount).toBe(500);
      expect(branch.amount).toBe(500);
      expect(result.totalAmount).toBe(1000);
    });
  });

  describe('Consumption Commissions (Self vs Cross-shop)', () => {
    it('should charge 2% for self-consumption order and split between branch (0.5%) and HQ (1.5%)', () => {
      const orderAmount = 200;
      const result = service.calculateConsumptionCommission(orderAmount, false);

      expect(result.commissionType).toBe(CommissionType.DAILY_CONSUMPTION_SELF);
      expect(result.merchantPayoutAmount).toBe(196); // 200 - 4 = 196
      const branch = result.splits.find((s) => s.role === 'CITY_BRANCH')!;
      const hq = result.splits.find((s) => s.role === 'HEADQUARTERS')!;

      expect(branch.amount).toBe(1); // 200 * 0.5% = 1.00
      expect(hq.amount).toBe(3); // 200 * 1.5% = 3.00
    });

    it('should charge 6% for cross-shop order and allocate to Partner member (0.5%), manager (2%), branch (1.5%), HQ (2%)', () => {
      const orderAmount = 500;
      const result = service.calculateConsumptionCommission(
        orderAmount,
        true,
        MemberLevel.PARTNER,
      );

      expect(result.commissionType).toBe(CommissionType.DAILY_CONSUMPTION_CROSS);
      expect(result.merchantPayoutAmount).toBe(470); // 500 * (1 - 0.06) = 470
      const member = result.splits.find((s) => s.role === 'INVITER')!;
      const manager = result.splits.find((s) => s.role === 'SHOP_MANAGER')!;
      const branch = result.splits.find((s) => s.role === 'CITY_BRANCH')!;
      const hq = result.splits.find((s) => s.role === 'HEADQUARTERS')!;

      expect(member.amount).toBe(2.5); // 500 * 0.5% = 2.50
      expect(member.isFrozen).toBe(true);
      expect(manager.amount).toBe(10); // 500 * 2.0% = 10.00
      expect(branch.amount).toBe(7.5); // 500 * 1.5% = 7.50
      expect(hq.amount).toBe(10); // 500 * 2.0% = 10.00
    });
  });

  describe('Tax Calculation (Labor Remuneration Progressive Bracket)', () => {
    it('should exempt tax if gross amount is <= 800 RMB', () => {
      const result = service.calculateTaxAndNetAmount(500);
      expect(result.taxAmount).toBe(0);
      expect(result.taxableIncome).toBe(0);
      expect(result.platformFee).toBe(1.75); // 500 * 0.35% = 1.75
      expect(result.netAmount).toBe(498.25);
    });

    it('should deduct fixed 800 RMB and tax 20% for amounts between 800 and 4000 RMB', () => {
      // Gross = 2800 -> Taxable = 2000 -> Tax = 2000 * 20% = 400
      // Fee = 2800 * 0.35% = 9.80 -> Net = 2800 - 400 - 9.80 = 2390.20
      const result = service.calculateTaxAndNetAmount(2800);
      expect(result.costDeduction).toBe(800);
      expect(result.taxableIncome).toBe(2000);
      expect(result.taxRatePercent).toBe(20);
      expect(result.taxAmount).toBe(400);
      expect(result.platformFee).toBe(9.8);
      expect(result.netAmount).toBe(2390.2);
    });

    it('should apply 20% cost deduction and progressive brackets above 4000 RMB', () => {
      // Gross = 10000 -> Taxable = 8000 -> Tax = 8000 * 20% = 1600
      const res10k = service.calculateTaxAndNetAmount(10000);
      expect(res10k.costDeduction).toBe(2000);
      expect(res10k.taxableIncome).toBe(8000);
      expect(res10k.taxRatePercent).toBe(20);
      expect(res10k.taxAmount).toBe(1600);

      // Gross = 50000 -> Taxable = 40000 (bracket 2: 30%, quick deduction 2000)
      // Tax = 40000 * 30% - 2000 = 10000
      const res50k = service.calculateTaxAndNetAmount(50000);
      expect(res50k.costDeduction).toBe(10000);
      expect(res50k.taxableIncome).toBe(40000);
      expect(res50k.taxRatePercent).toBe(30);
      expect(res50k.quickDeduction).toBe(2000);
      expect(res50k.taxAmount).toBe(10000);
    });
  });
});
