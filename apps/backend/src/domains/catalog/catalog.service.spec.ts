import { describe, it, expect, beforeEach } from 'vitest';
import { CatalogService } from './catalog.service';

describe('CatalogService - Freight & SKU Engine', () => {
  let service: CatalogService;

  beforeEach(() => {
    service = new CatalogService({} as any);
  });

  describe('Freight Step Billing Calculation', () => {
    const byCountTemplate = {
      chargeType: 'BY_COUNT',
      firstQuantity: 1,
      firstFee: '10.00',
      continueQuantity: 2,
      continueFee: '5.00',
      freeShippingThreshold: '100.00', // 满100包邮
    };

    it('should charge base fee for single item within firstQuantity', () => {
      const fee = service.calculateFreight(byCountTemplate, {
        shopId: 'shop-1',
        totalQuantity: 1,
        totalOrderAmount: 30,
      });
      expect(fee).toBe(10);
    });

    it('should charge stepped fee for multiple items exceeding firstQuantity', () => {
      // 4 items: 1st item = 10, remaining 3 items = ceil(3/2) * 5 = 10 -> Total = 20
      const fee = service.calculateFreight(byCountTemplate, {
        shopId: 'shop-1',
        totalQuantity: 4,
        totalOrderAmount: 80,
      });
      expect(fee).toBe(20);
    });

    it('should waive shipping fee if order amount meets free shipping threshold', () => {
      const fee = service.calculateFreight(byCountTemplate, {
        shopId: 'shop-1',
        totalQuantity: 5,
        totalOrderAmount: 120, // >= 100
      });
      expect(fee).toBe(0);
    });
  });
});
