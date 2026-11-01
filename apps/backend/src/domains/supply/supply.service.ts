import { Injectable, NotFoundException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import {
  suppliers,
  purchaseOrders,
  goodsSpu,
  goodsSku,
  shops,
} from '../../infrastructure/drizzle/schema/index';
import { eq, and, or, like, desc, sql } from 'drizzle-orm';

@Injectable()
export class SupplyService {
  constructor(@Inject(DRIZZLE_DB) private readonly db: DrizzleDatabase) {}

  /**
   * 查询所有已入驻上线的供货商主体列表
   */
  async listActiveSuppliers() {
    return this.db
      .select()
      .from(suppliers)
      .where(eq(suppliers.status, 'ACTIVE'));
  }

  /**
   * 商家向供货商发起批发采购下单
   */
  async createPurchaseOrder(params: {
    buyerShopId: string;
    supplierId: string;
    totalAmount: string;
    notes?: string;
  }) {
    const orderNo = `PO${Date.now()}${Math.floor(1000 + Math.random() * 9000)}`;

    const [order] = await this.db
      .insert(purchaseOrders)
      .values({
        orderNo,
        supplierId: params.supplierId,
        buyerShopId: params.buyerShopId,
        totalAmount: params.totalAmount,
        status: 'PENDING_PAY',
        notes: params.notes || null,
      })
      .returning();

    return order;
  }

  /**
   * 供货选品池商品列表检索 (B2B 选品)
   */
  async getSupplyGoodsList(query: {
    keyword?: string;
    status?: string;
    page?: number;
    pageSize?: number;
  }) {
    const page = Number(query.page) || 1;
    const pageSize = Number(query.pageSize) || 20;
    const offset = (page - 1) * pageSize;

    const conditions = [];
    if (query.status) {
      conditions.push(eq(goodsSpu.status, query.status));
    }
    if (query.keyword?.trim()) {
      conditions.push(like(goodsSpu.name, `%${query.keyword.trim()}%`));
    }

    const whereClause = conditions.length > 0 ? and(...conditions) : undefined;

    const rows = await this.db
      .select({
        id: goodsSpu.id,
        name: goodsSpu.name,
        description: goodsSpu.description,
        coverImage: goodsSpu.coverImage,
        tradeType: goodsSpu.tradeType,
        status: goodsSpu.status,
        basePrice: goodsSpu.basePrice,
        skuId: goodsSku.id,
        price: goodsSku.price,
        stock: goodsSku.stock,
        createdAt: goodsSpu.createdAt,
      })
      .from(goodsSpu)
      .leftJoin(goodsSku, eq(goodsSpu.id, goodsSku.spuId))
      .where(whereClause)
      .orderBy(desc(goodsSpu.createdAt))
      .limit(pageSize)
      .offset(offset);

    const countResult = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(goodsSpu)
      .where(whereClause);

    return {
      items: rows.map((r) => ({
        id: r.id,
        name: r.name,
        subTitle: r.description || '全国源头直采供应链精选',
        coverImage: r.coverImage || 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=200',
        supplyPrice: (Number(r.price || r.basePrice || 0) * 0.7).toFixed(2),
        suggestRetailPrice: r.price || r.basePrice || '0.00',
        stock: Number(r.stock || 500),
        status: r.status,
        supplierName: '城盟全国供应链原产中心',
        createdAt: r.createdAt,
      })),
      total: Number(countResult[0]?.count || 0),
      page,
      pageSize,
    };
  }

  /**
   * 发布供应链货源选品
   */
  async createSupplyGoods(data: {
    name: string;
    supplyPrice: string | number;
    suggestRetailPrice: string | number;
    stock: number;
    coverUrl?: string;
  }) {
    // 1. 获取一个默认 shopId (如城盟精选旗舰店)
    const [shop] = await this.db.select().from(shops).limit(1);

    const [spu] = await this.db
      .insert(goodsSpu)
      .values({
        shopId: shop.id,
        name: data.name,
        description: '全国源头直采供应链精选',
        coverImage:
          data.coverUrl ||
          'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=300',
        albumImages: [],
        tradeType: 'EXPRESS',
        status: 'ON_SALE',
        basePrice: String(data.suggestRetailPrice),
      })
      .returning();

    // 2. 插入对应的批发采购 SKU
    const [sku] = await this.db
      .insert(goodsSku)
      .values({
        spuId: spu.id,
        specValues: { 规格: '标准批发大包装' },
        price: String(data.suggestRetailPrice),
        stock: Number(data.stock),
        skuCode: `SKU-SUPPLY-${Date.now().toString().slice(-6)}`,
      })
      .returning();

    return {
      spu,
      sku,
    };
  }

  /**
   * 变更商品上下架状态
   */
  async toggleGoodsStatus(spuId: string, status: string) {
    const [updated] = await this.db
      .update(goodsSpu)
      .set({
        status,
        updatedAt: new Date(),
      })
      .where(eq(goodsSpu.id, spuId))
      .returning();

    return updated;
  }

  /**
   * 分页查询 B2B 批发采购订单列表
   */
  async getPurchaseOrdersList(query: {
    supplierId?: string;
    status?: string;
    page?: number;
    pageSize?: number;
  }) {
    const page = Number(query.page) || 1;
    const pageSize = Number(query.pageSize) || 20;
    const offset = (page - 1) * pageSize;

    const conditions = [];
    if (query.supplierId) {
      conditions.push(eq(purchaseOrders.supplierId, query.supplierId));
    }
    if (query.status) {
      conditions.push(eq(purchaseOrders.status, query.status));
    }

    const whereClause = conditions.length > 0 ? and(...conditions) : undefined;

    const rows = await this.db
      .select({
        id: purchaseOrders.id,
        orderNo: purchaseOrders.orderNo,
        supplierId: purchaseOrders.supplierId,
        supplierName: suppliers.companyName,
        buyerShopId: purchaseOrders.buyerShopId,
        buyerShopName: shops.name,
        totalAmount: purchaseOrders.totalAmount,
        status: purchaseOrders.status,
        shippingTrackingNo: purchaseOrders.shippingTrackingNo,
        notes: purchaseOrders.notes,
        createdAt: purchaseOrders.createdAt,
      })
      .from(purchaseOrders)
      .leftJoin(suppliers, eq(purchaseOrders.supplierId, suppliers.id))
      .leftJoin(shops, eq(purchaseOrders.buyerShopId, shops.id))
      .where(whereClause)
      .orderBy(desc(purchaseOrders.createdAt))
      .limit(pageSize)
      .offset(offset);

    const countResult = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(purchaseOrders)
      .where(whereClause);

    return {
      items: rows,
      total: Number(countResult[0]?.count || 0),
      page,
      pageSize,
    };
  }

  /**
   * 供货商发货，登记物流快递单号
   */
  async shipPurchaseOrder(orderId: string, trackingNo: string) {
    const [order] = await this.db
      .update(purchaseOrders)
      .set({
        status: 'SHIPPED',
        shippingTrackingNo: trackingNo,
        updatedAt: new Date(),
      })
      .where(eq(purchaseOrders.id, orderId))
      .returning();

    if (!order) {
      throw new NotFoundException('采购单不存在');
    }

    return order;
  }

  /**
   * 供货商结算大盘与对账台账
   */
  async getSupplierSettleSummary(supplierId?: string) {
    const conditions = [];
    if (supplierId) {
      conditions.push(eq(purchaseOrders.supplierId, supplierId));
    }
    const whereClause = conditions.length > 0 ? and(...conditions) : undefined;

    const stats = await this.db
      .select({
        totalAmount: sql<string>`coalesce(sum(cast(${purchaseOrders.totalAmount} as numeric)), 0)`,
        orderCount: sql<number>`count(*)`,
        shippedCount: sql<number>`count(case when ${purchaseOrders.status} in ('SHIPPED', 'COMPLETED') then 1 end)`,
        settledAmount: sql<string>`coalesce(sum(case when ${purchaseOrders.status} = 'COMPLETED' then cast(${purchaseOrders.totalAmount} as numeric) else 0 end), 0)`,
        pendingAmount: sql<string>`coalesce(sum(case when ${purchaseOrders.status} in ('PAID', 'SHIPPED') then cast(${purchaseOrders.totalAmount} as numeric) else 0 end), 0)`,
      })
      .from(purchaseOrders)
      .where(whereClause);

    const s = stats[0];

    return {
      totalRevenue: Number(s?.totalAmount || 0).toFixed(2),
      orderCount: Number(s?.orderCount || 0),
      shippedCount: Number(s?.shippedCount || 0),
      settledAmount: Number(s?.settledAmount || 0).toFixed(2),
      pendingAmount: Number(s?.pendingAmount || 0).toFixed(2),
      withdrawableAmount: Number(s?.settledAmount || 0).toFixed(2),
    };
  }
}
