import {
  Controller,
  Get,
  Post,
  Put,
  Param,
  Body,
  Query,
  UseGuards,
} from '@nestjs/common';
import { SupplyService } from './supply.service';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';

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

  @Get('goods')
  async getSupplyGoods(
    @Query()
    query: {
      keyword?: string;
      status?: string;
      page?: number;
      pageSize?: number;
    },
  ) {
    const data = await this.supplyService.getSupplyGoodsList(query);
    return {
      code: 200,
      data,
    };
  }

  @Post('goods')
  async createSupplyGoods(
    @Body()
    body: {
      name: string;
      supplyPrice: number;
      suggestRetailPrice: number;
      stock: number;
      coverUrl?: string;
    },
  ) {
    const data = await this.supplyService.createSupplyGoods(body);
    return {
      code: 200,
      message: '供货商品已成功发布至平台选品池',
      data,
    };
  }

  @Put('goods/:id/status')
  async toggleGoodsStatus(
    @Param('id') id: string,
    @Body('status') status: string,
  ) {
    const data = await this.supplyService.toggleGoodsStatus(id, status);
    return {
      code: 200,
      message: '商品供应状态已更新',
      data,
    };
  }

  @Get('purchase-orders')
  async getPurchaseOrders(
    @Query()
    query: {
      supplierId?: string;
      status?: string;
      page?: number;
      pageSize?: number;
    },
  ) {
    const data = await this.supplyService.getPurchaseOrdersList(query);
    return {
      code: 200,
      data,
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

  @Post('purchase-orders/:id/ship')
  async shipOrder(
    @Param('id') id: string,
    @Body('trackingNo') trackingNo: string,
  ) {
    const order = await this.supplyService.shipPurchaseOrder(id, trackingNo);
    return {
      code: 200,
      message: '采购订单已标记发货，运单号已同步',
      data: order,
    };
  }

  @Get('settle')
  async getSettleSummary(
    @CurrentUser() user: AuthenticatedUser,
    @Query('supplierId') supplierId?: string,
  ) {
    const data = await this.supplyService.getSupplierSettleSummary(supplierId);
    return {
      code: 200,
      data,
    };
  }
}
