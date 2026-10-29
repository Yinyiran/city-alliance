import { Controller, Get, Post, Param, Body } from '@nestjs/common';
import { CatalogService, FreightCalculateParams } from './catalog.service';

@Controller('catalog')
export class CatalogController {
  constructor(private readonly catalogService: CatalogService) {}

  @Get('goods/:id')
  async getGoodsDetail(@Param('id') spuId: string) {
    const data = await this.catalogService.getSpuWithSkus(spuId);
    return {
      code: 200,
      data,
    };
  }

  @Post('estimate-freight')
  async estimateFreight(@Body() params: FreightCalculateParams) {
    const freight = await this.catalogService.estimateFreightForOrder(params);
    return {
      code: 200,
      data: {
        freightAmount: freight,
      },
    };
  }
}
