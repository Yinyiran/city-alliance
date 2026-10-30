import { Controller, Get, Post, Put, Query, Param, Body, UsePipes, UseGuards } from '@nestjs/common';
import { StoreService } from './store.service';
import {
  SearchStoreNearbyDto,
  SearchStoreNearbySchema,
} from '@city-alliance/contracts';
import { ZodValidationPipe } from '../../common/pipes/zod-validation.pipe';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';

@Controller('stores')
export class StoreController {
  constructor(private readonly storeService: StoreService) {}

  @Get()
  @UseGuards(JwtAuthGuard)
  async getStores(
    @Query() query: {
      keyword?: string;
      status?: string;
      shopId?: string;
      page?: number;
      pageSize?: number;
    },
  ) {
    const data = await this.storeService.getStoreList(query);
    return {
      code: 200,
      data,
    };
  }

  @Post()
  @UseGuards(JwtAuthGuard)
  async createStore(
    @Body() body: {
      shopId: string;
      name: string;
      phone: string;
      address: string;
      latitude: string;
      longitude: string;
      cloudPrinterSn?: string;
      cloudPrinterType?: string;
    },
  ) {
    const data = await this.storeService.createStore(body);
    return {
      code: 200,
      message: '连锁分店创建成功',
      data,
    };
  }

  @Put(':id')
  @UseGuards(JwtAuthGuard)
  async updateStore(
    @Param('id') id: string,
    @Body() body: {
      name?: string;
      phone?: string;
      address?: string;
      latitude?: string;
      longitude?: string;
      cloudPrinterSn?: string;
      cloudPrinterType?: string;
    },
  ) {
    const data = await this.storeService.updateStore(id, body);
    return {
      code: 200,
      message: '门店信息更新成功',
      data,
    };
  }

  @Put(':id/status')
  @UseGuards(JwtAuthGuard)
  async toggleStatus(
    @Param('id') id: string,
    @Body('status') status: string,
  ) {
    const data = await this.storeService.toggleStoreStatus(id, status);
    return {
      code: 200,
      message: '门店营业状态切换成功',
      data,
    };
  }

  @Get('nearby')
  @UsePipes(new ZodValidationPipe(SearchStoreNearbySchema))
  async searchNearby(@Query() query: SearchStoreNearbyDto) {
    const result = await this.storeService.searchNearbyStores(query);
    return {
      code: 200,
      data: result,
    };
  }

  @Get('brand/:shopId')
  async getBrandDetail(
    @Param('shopId') shopId: string,
    @Query('lat') lat?: string,
    @Query('lon') lon?: string,
  ) {
    const userLat = lat ? Number(lat) : undefined;
    const userLon = lon ? Number(lon) : undefined;
    const result = await this.storeService.getBrandWithBranches(shopId, userLat, userLon);
    return {
      code: 200,
      data: result,
    };
  }

  @Get('wecom-qr/:shopId')
  async getWeComQr(
    @Param('shopId') shopId: string,
    @Query('storeId') storeId?: string,
  ) {
    const qr = await this.storeService.getActiveWeComGroupQr(shopId, storeId);
    return {
      code: 200,
      data: qr,
    };
  }
}
