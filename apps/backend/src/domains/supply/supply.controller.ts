import { Controller, Get, Post, Body, UseGuards } from '@nestjs/common';
import { SupplyService } from './supply.service';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';

@Controller('supply')
@UseGuards(JwtAuthGuard)
export class SupplyController {
  constructor(private readonly supplyService: SupplyService) {}

  @Get('suppliers')
  async getSuppliers() {
    const list = await this.supplyService.listActiveSuppliers();
    return {
      code: 200,
      data: list,
    };
  }

  @Post('purchase-orders')
  async createPurchase(
    @Body()
    body: {
      buyerShopId: string;
      supplierId: string;
      totalAmount: string;
      notes?: string;
    },
  ) {
    const order = await this.supplyService.createPurchaseOrder(body);
    return {
      code: 200,
      message: '批发采购单创建成功',
      data: order,
    };
  }
}
