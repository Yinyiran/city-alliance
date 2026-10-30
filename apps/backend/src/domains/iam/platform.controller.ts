import { Controller, Get, Post, Put, Query, Param, Body, UseGuards } from '@nestjs/common';
import { PlatformService } from './platform.service';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';

@Controller('platform')
@UseGuards(JwtAuthGuard)
export class PlatformController {
  constructor(private readonly platformService: PlatformService) {}

  @Get('cities')
  async getCities(@Query() query: { keyword?: string; status?: string }) {
    const data = await this.platformService.getCityList(query);
    return {
      code: 200,
      data,
    };
  }

  @Post('cities')
  async createCity(
    @Body() body: {
      cityName: string;
      companyName: string;
      agentLeader: string;
      phone: string;
    },
  ) {
    const data = await this.platformService.createCity(body);
    return {
      code: 200,
      message: '城市分公司授权成功',
      data,
    };
  }

  @Put('cities/:id')
  async updateCity(
    @Param('id') id: string,
    @Body() body: {
      cityName?: string;
      companyName?: string;
      agentLeader?: string;
      phone?: string;
    },
  ) {
    const data = await this.platformService.updateCity(id, body);
    return {
      code: 200,
      message: '城市分公司授权更新成功',
      data,
    };
  }

  @Put('cities/:id/status')
  async toggleCityStatus(
    @Param('id') id: string,
    @Body('status') status: string,
  ) {
    const data = await this.platformService.toggleCityStatus(id, status);
    return {
      code: 200,
      message: '城市分公司状态更新成功',
      data,
    };
  }

  @Get('shops')
  async getShops(
    @Query() query: {
      status?: string;
      keyword?: string;
      cityId?: string;
      page?: number;
      pageSize?: number;
    },
  ) {
    const data = await this.platformService.getShopList(query);
    return {
      code: 200,
      data,
    };
  }

  @Put('shops/:id/status')
  async updateShopStatus(
    @Param('id') shopId: string,
    @Body('status') status: string,
  ) {
    const data = await this.platformService.updateShopStatus(shopId, status);
    return {
      code: 200,
      message: '商家审核状态变更成功',
      data,
    };
  }

  @Put('shops/:id/multiplier')
  async updateShopMultiplier(
    @Param('id') shopId: string,
    @Body('multiplier') multiplier: string,
  ) {
    const data = await this.platformService.updateShopMultiplier(shopId, multiplier);
    return {
      code: 200,
      message: '商家曝光率倍数调整成功',
      data,
    };
  }
}
