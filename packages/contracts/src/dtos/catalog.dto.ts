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
  specText: z.string().optional(), // 如 "红色 / L"
  specValues: z.record(z.string(), z.string()).default({}), // { "颜色": "红色", "尺码": "L" }
  skuCode: z.string().optional(),
  price: z.string(),
  costPrice: z.string().optional(),
  stock: z.number().int().default(100),
});
export type SkuItemGeneratedDto = z.infer<typeof SkuItemGeneratedSchema>;

export const CreateGoodsSchema = z.object({
  name: z.string().min(2, '商品名称至少2个字符').max(200),
  tradeType: z.nativeEnum(TradeType).default(TradeType.EXPRESS),
  shopId: z.string().uuid().optional(),
  categoryId: z.string().uuid().optional().nullable(),
  coverImage: z.string().min(1, '请提供商品主图URL'),
  albumImages: z.array(z.string()).default([]),
  description: z.string().optional().nullable(),
  basePrice: z.string().regex(/^\d+(\.\d{1,2})?$/, '基础价格格式需为两位小数'),
  baseStock: z.number().int().nonnegative().default(100),
  status: z.nativeEnum(GoodsStatus).default(GoodsStatus.ON_SALE),
  freightTemplateId: z.string().uuid().optional().nullable(),
  skus: z.array(SkuItemGeneratedSchema).optional(),
});
export type CreateGoodsDto = z.infer<typeof CreateGoodsSchema>;

export const UpdateGoodsSchema = z.object({
  name: z.string().min(2).max(200).optional(),
  tradeType: z.nativeEnum(TradeType).optional(),
  categoryId: z.string().uuid().optional().nullable(),
  coverImage: z.string().optional(),
  albumImages: z.array(z.string()).optional(),
  description: z.string().optional().nullable(),
  basePrice: z.string().regex(/^\d+(\.\d{1,2})?$/).optional(),
  baseStock: z.number().int().nonnegative().optional(),
  status: z.nativeEnum(GoodsStatus).optional(),
  freightTemplateId: z.string().uuid().optional().nullable(),
  skus: z.array(SkuItemGeneratedSchema).optional(),
});
export type UpdateGoodsDto = z.infer<typeof UpdateGoodsSchema>;

const emptyToUndefined = (val: unknown) => (val === '' ? undefined : val);

export const QueryGoodsSchema = z.object({
  keyword: z.preprocess(emptyToUndefined, z.string().optional()),
  tradeType: z.preprocess(emptyToUndefined, z.string().optional()),
  status: z.preprocess(emptyToUndefined, z.string().optional()),
  categoryId: z.preprocess(emptyToUndefined, z.string().uuid().optional()),
  shopId: z.preprocess(emptyToUndefined, z.string().uuid().optional()),
  page: z.coerce.number().min(1).default(1),
  pageSize: z.coerce.number().min(1).max(100).default(20),
});
export type QueryGoodsDto = z.infer<typeof QueryGoodsSchema>;
