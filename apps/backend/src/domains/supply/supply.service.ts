import { Injectable, NotFoundException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { suppliers, purchaseOrders, goodsSpu } from '../../infrastructure/drizzle/schema/index';
import { eq, and } from 'drizzle-orm';

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
}
