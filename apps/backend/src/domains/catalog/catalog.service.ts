import { Injectable, NotFoundException, BadRequestException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { goodsSpu, goodsSku, freightTemplates } from '../../infrastructure/drizzle/schema/index';
import { eq, and } from 'drizzle-orm';
import { TradeType } from '@city-alliance/contracts';

export interface FreightCalculateParams {
  shopId: string;
  freightTemplateId?: string | null;
  totalQuantity: number;
  totalWeightKg?: number;
  totalOrderAmount: number;
}

@Injectable()
export class CatalogService {
  constructor(@Inject(DRIZZLE_DB) private readonly db: DrizzleDatabase) {}

  /**
   * 阶梯运费引擎核心计算算法
   * (支持首件/续件、首重/续重，以及达到满额包邮门槛自动免运费)
   */
  calculateFreight(
    template: {
      chargeType: string;
      firstQuantity: number;
      firstFee: string;
      continueQuantity: number;
      continueFee: string;
      freeShippingThreshold?: string | null;
    },
    params: FreightCalculateParams,
  ): number {
    // 检查满额包邮条件
    if (template.freeShippingThreshold) {
      const threshold = Number(template.freeShippingThreshold);
      if (threshold > 0 && params.totalOrderAmount >= threshold) {
        return 0; // 满额直接包邮
      }
    }

    const firstFee = Number(template.firstFee);
    const continueFee = Number(template.continueFee);
    const firstQty = template.firstQuantity || 1;
    const continueQty = template.continueQuantity || 1;

    let unitsToCharge = params.totalQuantity;
    if (template.chargeType === 'BY_WEIGHT' && params.totalWeightKg !== undefined) {
      unitsToCharge = Math.ceil(params.totalWeightKg);
    }

    if (unitsToCharge <= firstQty) {
      return Number(firstFee.toFixed(2));
    }

    const excessUnits = unitsToCharge - firstQty;
    const continueIncrements = Math.ceil(excessUnits / continueQty);
    const totalFreight = firstFee + continueIncrements * continueFee;

    return Number(totalFreight.toFixed(2));
  }

  /**
   * 查询商品 SPU 及其全部规格 SKU 交叉矩阵详情
   */
  async getSpuWithSkus(spuId: string) {
    const spuList = await this.db
      .select()
      .from(goodsSpu)
      .where(eq(goodsSpu.id, spuId))
      .limit(1);

    const spu = spuList[0];
    if (!spu) {
      throw new NotFoundException('商品不存在或已下架');
    }

    const skus = await this.db
      .select()
      .from(goodsSku)
      .where(eq(goodsSku.spuId, spuId));

    return {
      spu,
      skus,
    };
  }

  /**
   * 订单试算运费接口
   */
  async estimateFreightForOrder(params: FreightCalculateParams): Promise<number> {
    if (!params.freightTemplateId) {
      return 0; // 无运费模板，免运费
    }

    const templates = await this.db
      .select()
      .from(freightTemplates)
      .where(eq(freightTemplates.id, params.freightTemplateId))
      .limit(1);

    const template = templates[0];
    if (!template) {
      return 0;
    }

    return this.calculateFreight(template, params);
  }
}
