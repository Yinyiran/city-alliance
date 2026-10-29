import { describe, it, expect, beforeEach } from 'vitest';
import { CatalogService } from './catalog.service';
import { TradeType } from '@city-alliance/contracts';

describe('CatalogService - Freight & Spec Matrix Engine', () => {
  let catalogService: CatalogService;

  beforeEach(() => {
    // 构造无外部依赖的纯算法单元测试环境
    catalogService = new CatalogService({} as any);
  });

  it('按件计费运费阶梯计算 - 未超件仅收首费', () => {
    const template = {
      chargeType: 'BY_PIECE',
      firstQuantity: 2,
      firstFee: '10.00',
      continueQuantity: 1,
      continueFee: '5.00',
    };

    const fee = catalogService.calculateFreight(template, {
      shopId: 'shop-1',
      totalQuantity: 2,
      totalOrderAmount: 80,
    });

    expect(fee).toBe(10);
  });

  it('按件计费运费阶梯计算 - 超件加收续件费用', () => {
    const template = {
      chargeType: 'BY_PIECE',
      firstQuantity: 1,
      firstFee: '8.00',
      continueQuantity: 2,
      continueFee: '4.00',
    };

    // 购买 4 件: 首件 1 件收 8 元，剩余 3 件每 2 件一组需 2 组续费 = 8 + 4 * 2 = 16 元
    const fee = catalogService.calculateFreight(template, {
      shopId: 'shop-1',
      totalQuantity: 4,
      totalOrderAmount: 120,
    });

    expect(fee).toBe(16);
  });

  it('满额包邮门槛自动减免运费', () => {
    const template = {
      chargeType: 'BY_PIECE',
      firstQuantity: 1,
      firstFee: '15.00',
      continueQuantity: 1,
      continueFee: '5.00',
      freeShippingThreshold: '99.00',
    };

    const fee = catalogService.calculateFreight(template, {
      shopId: 'shop-1',
      totalQuantity: 5,
      totalOrderAmount: 108.0,
    });

    expect(fee).toBe(0);
  });

  it('SPU-SKU 规格交叉矩阵笛卡尔积自动生成引擎 (颜色 x 尺码)', () => {
    const result = catalogService.generateSkuMatrix({
      spuName: '城盟特选高定卫衣',
      tradeType: TradeType.EXPRESS,
      basePrice: '199.00',
      baseStock: 50,
      specAttributes: [
        { specName: '颜色', values: ['经典黑', '米杏白'] },
        { specName: '尺码', values: ['M', 'L', 'XL'] },
      ],
    });

    // 2 种颜色 x 3 种尺码 = 6 组 SKU
    expect(result.totalSkus).toBe(6);
    expect(result.skus.length).toBe(6);
    expect(result.skus[0].specText).toBe('经典黑 / M');
    expect(result.skus[0].price).toBe('199.00');
    expect(result.skus[0].stock).toBe(50);
    expect(result.skus[5].specText).toBe('米杏白 / XL');
  });
});
