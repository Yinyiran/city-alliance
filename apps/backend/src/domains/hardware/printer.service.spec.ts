import { describe, it, expect, beforeEach } from 'vitest';
import { PrinterService } from './printer.service';

describe('PrinterService - Cloud Thermal Receipt Engine', () => {
  let service: PrinterService;

  beforeEach(() => {
    service = new PrinterService();
  });

  it('should format 58mm thermal receipt with correct header, items, totals and verify code', () => {
    const receipt = service.formatReceiptContent({
      shopName: '测试品牌旗舰店',
      storeName: '杭州西湖分店',
      orderNo: 'CA202610290001',
      tradeType: 'PICKUP',
      createdAt: '2026-10-29 21:00:00',
      items: [
        { name: '美式咖啡', quantity: 2, price: '38.00' },
        { name: '精品牛角包', quantity: 1, price: '18.00' },
      ],
      totalAmount: '56.00',
      payAmount: '54.88',
      verifyCode: '889922114455',
    });

    expect(receipt).toContain('城盟优选');
    expect(receipt).toContain('测试品牌旗舰店');
    expect(receipt).toContain('杭州西湖分店');
    expect(receipt).toContain('CA202610290001');
    expect(receipt).toContain('美式咖啡');
    expect(receipt).toContain('889922114455');
  });
});
