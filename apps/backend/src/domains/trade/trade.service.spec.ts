import { describe, it, expect, beforeEach } from 'vitest';
import { TradeService } from './trade.service';
import { TradeType, MemberLevel } from '@city-alliance/contracts';
import { SettleService } from '../settle/settle.service';

describe('TradeService - FSM & Verify Code Logic', () => {
  let service: TradeService;
  let settleService: SettleService;

  beforeEach(() => {
    settleService = new SettleService();
    service = new TradeService({} as any, {} as any, settleService);
  });

  describe('Member Discounts Calculation in Trade', () => {
    it('should apply 98 discount rate for Diamond member', () => {
      const equity = settleService.memberEquityMap[MemberLevel.DIAMOND];
      const totalAmount = 100;
      const payAmount = Number((totalAmount * equity.discountRate).toFixed(2));
      expect(payAmount).toBe(98);
    });

    it('should apply 95 discount rate for Partner member', () => {
      const equity = settleService.memberEquityMap[MemberLevel.PARTNER];
      const totalAmount = 200;
      const payAmount = Number((totalAmount * equity.discountRate).toFixed(2));
      expect(payAmount).toBe(190);
    });
  });
});
