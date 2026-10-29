import { Injectable, NotFoundException, BadRequestException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { goodsSpu, goodsSku, freightTemplates } from '../../infrastructure/drizzle/schema/index';
import { eq, inArray } from 'drizzle-orm';
import {
  TradeType,
  GoodsStatus,
  GenerateSkuMatrixDto,
  SkuItemGeneratedDto,
} from '@city-alliance/contracts';

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
   * SPU 与 SKU 交叉规格矩阵引擎 (笛卡尔积算法自动生成各规格 SKU)
   */
  generateSkuMatrix(dto: GenerateSkuMatrixDto): {
    totalSkus: number;
    skus: SkuItemGeneratedDto[];
  } {
    if (!dto.specAttributes || dto.specAttributes.length === 0) {
      throw new BadRequestException('规格属性项不能为空');
    }

    // 递归求笛卡尔积
    type SpecPair = { specName: string; value: string };
    const specEntries: SpecPair[][] = dto.specAttributes.map((attr) =>
      attr.values.map((v) => ({ specName: attr.specName, value: v })),
    );

    const cartesian = (sets: SpecPair[][]): SpecPair[][] => {
      return sets.reduce<SpecPair[][]>(
        (acc, curr) => {
          const res: SpecPair[][] = [];
          for (const a of acc) {
            for (const c of curr) {
              res.push([...a, c]);
            }
          }
          return res;
        },
        [[]],
      );
    };

    const combinations = cartesian(specEntries);

    const skus: SkuItemGeneratedDto[] = combinations.map((combo, idx) => {
      const specText = combo.map((c) => c.value).join(' / ');
      const specValues: Record<string, string> = {};
      for (const c of combo) {
        specValues[c.specName] = c.value;
      }

      const paddedIndex = (idx + 1).toString().padStart(3, '0');
      const skuCode = `SKU_${Date.now().toString().slice(-6)}_${paddedIndex}`;

      return {
        specText,
        specValues,
        skuCode,
        price: dto.basePrice,
        costPrice: dto.baseCostPrice || dto.basePrice,
        stock: dto.baseStock,
      };
    });

    return {
      totalSkus: skus.length,
      skus,
    };
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

    return this.calculateFreight(
      {
        chargeType: template.chargeType,
        firstQuantity: template.firstQuantity,
        firstFee: template.firstFee,
        continueQuantity: template.continueQuantity,
        continueFee: template.continueFee,
        freeShippingThreshold: template.freeShippingThreshold,
      },
      params,
    );
  }

  /**
   * 批量更新商品上架/下架销售状态
   */
  async batchUpdateGoodsStatus(spuIds: string[], status: GoodsStatus) {
    if (!spuIds || spuIds.length === 0) {
      throw new BadRequestException('请选择至少一件商品');
    }

    await this.db
      .update(goodsSpu)
      .set({
        status,
        updatedAt: new Date(),
      })
      .where(inArray(goodsSpu.id, spuIds));

    return {
      success: true,
      updatedCount: spuIds.length,
      status,
    };
  }
}
