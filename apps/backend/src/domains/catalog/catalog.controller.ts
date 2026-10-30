import {
  Controller,
  Get,
  Post,
  Put,
  Delete,
  Param,
  Body,
  Query,
  UseGuards,
  UsePipes,
} from '@nestjs/common';
import { CatalogService, FreightCalculateParams } from './catalog.service';
import {
  CreateGoodsDto,
  CreateGoodsSchema,
  UpdateGoodsDto,
  UpdateGoodsSchema,
  QueryGoodsDto,
  QueryGoodsSchema,
  GoodsStatus,
} from '@city-alliance/contracts';
import { ZodValidationPipe } from '../../common/pipes/zod-validation.pipe';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Controller('catalog')
export class CatalogController {
  constructor(private readonly catalogService: CatalogService) {}

  @Get('goods')
  @UseGuards(JwtAuthGuard)
  @UsePipes(new ZodValidationPipe(QueryGoodsSchema))
  async getGoods(
    @CurrentUser() user: AuthenticatedUser,
    @Query() query: QueryGoodsDto,
  ) {
    const data = await this.catalogService.getGoodsList(user, query);
    return {
      code: 200,
      data,
    };
  }

  @Get('goods/:id')
  async getGoodsDetail(@Param('id') spuId: string) {
    const data = await this.catalogService.getSpuWithSkus(spuId);
    return {
      code: 200,
      data,
    };
  }

  @Post('goods')
  @UseGuards(JwtAuthGuard)
  @UsePipes(new ZodValidationPipe(CreateGoodsSchema))
  async createGoods(
    @CurrentUser() user: AuthenticatedUser,
    @Body() dto: CreateGoodsDto,
  ) {
    const data = await this.catalogService.createGoods(user, dto);
    return {
      code: 200,
      message: '商品发布成功，规格矩阵已自动生成',
      data,
    };
  }

  @Put('goods/:id')
  @UseGuards(JwtAuthGuard)
  @UsePipes(new ZodValidationPipe(UpdateGoodsSchema))
  async updateGoods(
    @CurrentUser() user: AuthenticatedUser,
    @Param('id') id: string,
    @Body() dto: UpdateGoodsDto,
  ) {
    const data = await this.catalogService.updateGoods(user, id, dto);
    return {
      code: 200,
      message: '商品信息已更新',
      data,
    };
  }

  @Put('goods/:id/status')
  @UseGuards(JwtAuthGuard)
  async toggleGoodsStatus(
    @CurrentUser() user: AuthenticatedUser,
    @Param('id') id: string,
    @Body('status') status: GoodsStatus,
  ) {
    const data = await this.catalogService.toggleGoodsStatus(user, id, status);
    return {
      code: 200,
      message: `商品状态已调整为 ${status === 'ON_SALE' ? '上架销售' : '已下架'}`,
      data,
    };
  }

  @Delete('goods/:id')
  @UseGuards(JwtAuthGuard)
  async deleteGoods(
    @CurrentUser() user: AuthenticatedUser,
    @Param('id') id: string,
  ) {
    const data = await this.catalogService.deleteGoods(user, id);
    return {
      code: 200,
      message: '商品已删除',
      data,
    };
  }

  @Get('categories')
  async getCategories() {
    const data = await this.catalogService.getCategories();
    return {
      code: 200,
      data,
    };
  }

  @Get('freight-templates')
  @UseGuards(JwtAuthGuard)
  async getFreightTemplates(@Query('shopId') shopId?: string) {
    const data = await this.catalogService.getFreightTemplates(shopId);
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
