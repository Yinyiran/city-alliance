import { Controller, Get, Post, Query, Param, Body, UsePipes } from '@nestjs/common';
import { StoreService } from './store.service';
import {
  SearchStoreNearbyDto,
  SearchStoreNearbySchema,
} from '@city-alliance/contracts';
import { ZodValidationPipe } from '../../common/pipes/zod-validation.pipe';

@Controller('stores')
export class StoreController {
  constructor(private readonly storeService: StoreService) {}

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
