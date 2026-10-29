import { z } from 'zod';
import { TradeType, GoodsStatus, FreightType } from '../enums/catalog.enum.js';

export const SpecAttributeSchema = z.object({
  specName: z.string().min(1, '规格名称不可为空'),
  values: z.array(z.string().min(1)).min(1, '规格属性值至少需要1个'),
});
export type SpecAttributeDto = z.infer<typeof SpecAttributeSchema>;

export const GenerateSkuMatrixSchema = z.object({
  spuName: z.string().min(1),
  tradeType: z.nativeEnum(TradeType),
  basePrice: z.string().regex(/^\d+(\.\d{1,2})?$/, '基础价格格式需为两位小数'),
  baseStock: z.number().int().nonnegative().default(100),
  baseCostPrice: z.string().regex(/^\d+(\.\d{1,2})?$/).optional(),
  specAttributes: z.array(SpecAttributeSchema).min(1, '生成多规格矩阵至少需要1组规格项'),
});
export type GenerateSkuMatrixDto = z.infer<typeof GenerateSkuMatrixSchema>;

export const SkuItemGeneratedSchema = z.object({
  specText: z.string(), // 如 "红色 / L"
  specValues: z.record(z.string(), z.string()), // { "颜色": "红色", "尺码": "L" }
  skuCode: z.string(),
  price: z.string(),
  costPrice: z.string().optional(),
  stock: z.number().int(),
});
export type SkuItemGeneratedDto = z.infer<typeof SkuItemGeneratedSchema>;
