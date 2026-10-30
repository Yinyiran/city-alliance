import { Injectable, NotFoundException, BadRequestException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { goodsSpu, goodsSku, freightTemplates, shops, goodsCategories } from '../../infrastructure/drizzle/schema/index';
import { eq, inArray, and, or, like, sql, desc } from 'drizzle-orm';
import {
  TradeType,
  GoodsStatus,
  GenerateSkuMatrixDto,
  SkuItemGeneratedDto,
  CreateGoodsDto,
  UpdateGoodsDto,
  QueryGoodsDto,
  RoleEnum,
} from '@city-alliance/contracts';
import { AuthenticatedUser } from '../../common/decorators/current-user.decorator';

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
   * 分页多维度查询商品列表 (支持店铺隔离、关键词搜索、分类与履约类型筛选)
   */
  async getGoodsList(currentUser: AuthenticatedUser, query: QueryGoodsDto) {
    const page = query.page || 1;
    const pageSize = query.pageSize || 20;
    const offset = (page - 1) * pageSize;

    const conditions = [];

    // 数据权限隔离：商家只能查看自己店铺的商品
    if (currentUser.role === RoleEnum.SHOP_OWNER && currentUser.shopId) {
      conditions.push(eq(goodsSpu.shopId, currentUser.shopId));
    } else if (query.shopId) {
      conditions.push(eq(goodsSpu.shopId, query.shopId));
    }

    if (query.keyword?.trim()) {
      conditions.push(like(goodsSpu.name, `%${query.keyword.trim()}%`));
    }

    if (query.tradeType) {
      conditions.push(eq(goodsSpu.tradeType, query.tradeType));
    }

    if (query.status) {
      conditions.push(eq(goodsSpu.status, query.status));
    }

    if (query.categoryId) {
      conditions.push(eq(goodsSpu.categoryId, query.categoryId));
    }

    const whereClause = conditions.length > 0 ? and(...conditions) : undefined;

    // 查询 SPU 列表
    const spuRows = await this.db
      .select({
        id: goodsSpu.id,
        shopId: goodsSpu.shopId,
        shopName: shops.name,
        name: goodsSpu.name,
        tradeType: goodsSpu.tradeType,
        categoryId: goodsSpu.categoryId,
        categoryName: goodsCategories.name,
        coverImage: goodsSpu.coverImage,
        albumImages: goodsSpu.albumImages,
        description: goodsSpu.description,
        status: goodsSpu.status,
        basePrice: goodsSpu.basePrice,
        freightTemplateId: goodsSpu.freightTemplateId,
        createdAt: goodsSpu.createdAt,
        updatedAt: goodsSpu.updatedAt,
      })
      .from(goodsSpu)
      .leftJoin(shops, eq(goodsSpu.shopId, shops.id))
      .leftJoin(goodsCategories, eq(goodsSpu.categoryId, goodsCategories.id))
      .where(whereClause)
      .orderBy(desc(goodsSpu.createdAt))
      .limit(pageSize)
      .offset(offset);

    // 查询关联的 SKU 规格矩阵
    const spuIds = spuRows.map((s) => s.id);
    let skusBySpu: Record<string, any[]> = {};

    if (spuIds.length > 0) {
      const skuRows = await this.db
        .select()
        .from(goodsSku)
        .where(inArray(goodsSku.spuId, spuIds));

      for (const sku of skuRows) {
        if (!skusBySpu[sku.spuId]) skusBySpu[sku.spuId] = [];
        skusBySpu[sku.spuId].push(sku);
      }
    }

    // 统计总数
    const countResult = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(goodsSpu)
      .where(whereClause);
    const total = Number(countResult[0]?.count || 0);

    const items = spuRows.map((spu) => ({
      ...spu,
      skus: skusBySpu[spu.id] || [],
    }));

    return {
      items,
      total,
      page,
      pageSize,
    };
  }

  /**
   * 发布新商品 SPU 及 SKU 规格矩阵
   */
  async createGoods(currentUser: AuthenticatedUser, dto: CreateGoodsDto) {
    // 确定所属店铺
    let targetShopId = dto.shopId;
    if (currentUser.role === RoleEnum.SHOP_OWNER) {
      targetShopId = currentUser.shopId || undefined;
    }
    if (!targetShopId) {
      // 默认取系统第一个商家店铺
      const defaultShop = await this.db.select({ id: shops.id }).from(shops).limit(1);
      targetShopId = defaultShop[0]?.id;
    }
    if (!targetShopId) {
      throw new BadRequestException('未找到可挂载的商家店铺');
    }

    // 1. 插入 SPU
    const insertedSpu = await this.db
      .insert(goodsSpu)
      .values({
        shopId: targetShopId,
        name: dto.name.trim(),
        tradeType: dto.tradeType,
        categoryId: dto.categoryId || null,
        coverImage: dto.coverImage,
        albumImages: dto.albumImages || [],
        description: dto.description || '',
        basePrice: dto.basePrice,
        status: dto.status || 'ON_SALE',
        freightTemplateId: dto.freightTemplateId || null,
      })
      .returning();

    const spu = insertedSpu[0];

    // 2. 插入 SKU 规格矩阵
    const skuItemsToInsert = [];
    if (dto.skus && dto.skus.length > 0) {
      for (const s of dto.skus) {
        skuItemsToInsert.push({
          spuId: spu.id,
          skuCode: s.skuCode || `SKU_${Date.now().toString().slice(-6)}_${Math.random().toString().slice(2, 5)}`,
          specValues: s.specValues || {},
          price: s.price || dto.basePrice,
          stock: s.stock || 100,
        });
      }
    } else {
      // 默认单规格 SKU
      skuItemsToInsert.push({
        spuId: spu.id,
        skuCode: `SKU_${Date.now().toString().slice(-6)}`,
        specValues: {},
        price: dto.basePrice,
        stock: dto.baseStock || 100,
      });
    }

    await this.db.insert(goodsSku).values(skuItemsToInsert);

    return this.getSpuWithSkus(spu.id);
  }

  /**
   * 更新商品 SPU 及规格矩阵
   */
  async updateGoods(currentUser: AuthenticatedUser, id: string, dto: UpdateGoodsDto) {
    const spuList = await this.db.select().from(goodsSpu).where(eq(goodsSpu.id, id)).limit(1);
    const existing = spuList[0];
    if (!existing) {
      throw new NotFoundException('商品不存在');
    }

    if (currentUser.role === RoleEnum.SHOP_OWNER && currentUser.shopId && existing.shopId !== currentUser.shopId) {
      throw new BadRequestException('无权修改其他商家的商品');
    }

    const updates: Record<string, any> = { updatedAt: new Date() };
    if (dto.name) updates.name = dto.name.trim();
    if (dto.tradeType) updates.tradeType = dto.tradeType;
    if (dto.categoryId !== undefined) updates.categoryId = dto.categoryId;
    if (dto.coverImage) updates.coverImage = dto.coverImage;
    if (dto.albumImages) updates.albumImages = dto.albumImages;
    if (dto.description !== undefined) updates.description = dto.description;
    if (dto.basePrice) updates.basePrice = dto.basePrice;
    if (dto.status) updates.status = dto.status;
    if (dto.freightTemplateId !== undefined) updates.freightTemplateId = dto.freightTemplateId;

    await this.db.update(goodsSpu).set(updates).where(eq(goodsSpu.id, id));

    // 如果传递了新的 SKU 列表，则重建 SKU
    if (dto.skus && dto.skus.length > 0) {
      await this.db.delete(goodsSku).where(eq(goodsSku.spuId, id));
      const skuItems = dto.skus.map((s) => ({
        spuId: id,
        skuCode: s.skuCode || `SKU_${Date.now().toString().slice(-6)}_${Math.random().toString().slice(2, 5)}`,
        specValues: s.specValues || {},
        price: s.price || dto.basePrice || existing.basePrice,
        stock: s.stock || 100,
      }));
      await this.db.insert(goodsSku).values(skuItems);
    }

    return this.getSpuWithSkus(id);
  }

  /**
   * 切换商品上下架状态
   */
  async toggleGoodsStatus(currentUser: AuthenticatedUser, id: string, status: GoodsStatus) {
    await this.db.update(goodsSpu).set({ status, updatedAt: new Date() }).where(eq(goodsSpu.id, id));
    return { success: true, status };
  }

  /**
   * 删除商品
   */
  async deleteGoods(currentUser: AuthenticatedUser, id: string) {
    await this.db.delete(goodsSpu).where(eq(goodsSpu.id, id));
    return { success: true };
  }

  /**
   * 获取全部分类树/列表
   */
  async getCategories() {
    return this.db
      .select()
      .from(goodsCategories)
      .orderBy(goodsCategories.sortOrder);
  }

  async getCategoriesTree() {
    const list = await this.getCategories();
    const map = new Map<string, any>();
    const roots: any[] = [];
    for (const item of list) {
      map.set(item.id, { ...item, children: [] });
    }
    for (const item of list) {
      const node = map.get(item.id);
      if (item.parentId && map.has(item.parentId)) {
        map.get(item.parentId).children.push(node);
      } else {
        roots.push(node);
      }
    }
    return roots;
  }

  async createCategory(data: {
    parentId?: string | null;
    name: string;
    icon?: string;
    sortOrder?: number;
    isVisible?: boolean;
  }) {
    const [cat] = await this.db
      .insert(goodsCategories)
      .values({
        parentId: data.parentId || null,
        name: data.name,
        icon: data.icon || null,
        sortOrder: data.sortOrder || 0,
        isVisible: data.isVisible !== false,
      })
      .returning();
    return cat;
  }

  async updateCategory(
    id: string,
    data: {
      parentId?: string | null;
      name?: string;
      icon?: string;
      sortOrder?: number;
      isVisible?: boolean;
    },
  ) {
    const [cat] = await this.db
      .update(goodsCategories)
      .set({
        ...data,
      })
      .where(eq(goodsCategories.id, id))
      .returning();
    return cat;
  }

  async deleteCategory(id: string) {
    await this.db
      .delete(goodsCategories)
      .where(eq(goodsCategories.id, id));
    return { success: true };
  }

  /**
   * 获取店铺运费模板列表
   */
  async getFreightTemplates(shopId?: string) {
    if (shopId) {
      return this.db.select().from(freightTemplates).where(eq(freightTemplates.shopId, shopId));
    }
    return this.db.select().from(freightTemplates).limit(50);
  }

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
