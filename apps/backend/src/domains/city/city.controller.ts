import {
  Controller,
  Get,
  Put,
  Query,
  Body,
  UseGuards,
} from '@nestjs/common';
import { CityService } from './city.service';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Controller('city')
@UseGuards(JwtAuthGuard)
export class CityController {
  constructor(private readonly cityService: CityService) {}

  @Get('overview')
  async getCityOverview(@CurrentUser() user: AuthenticatedUser) {
    const data = await this.cityService.getCityOverview(user);
    return {
      code: 200,
      data,
    };
  }

  @Get('shops')
  async getCityShops(
    @CurrentUser() user: AuthenticatedUser,
    @Query('keyword') keyword?: string,
    @Query('status') status?: string,
    @Query('page') page?: number,
    @Query('pageSize') pageSize?: number,
  ) {
    const data = await this.cityService.getCityShops(user, { keyword, status, page, pageSize });
    return {
      code: 200,
      data,
    };
  }

  @Get('commissions')
  async getCityCommissions(
    @CurrentUser() user: AuthenticatedUser,
    @Query('page') page?: number,
    @Query('pageSize') pageSize?: number,
    @Query('status') status?: string,
  ) {
    const data = await this.cityService.getCityCommissions(user, { page, pageSize, status });
    return {
      code: 200,
      data,
    };
  }

  @Get('config')
  async getCityConfig(@CurrentUser() user: AuthenticatedUser) {
    const data = await this.cityService.getCityConfig(user);
    return {
      code: 200,
      data,
    };
  }

  @Put('config')
  async updateCityConfig(
    @CurrentUser() user: AuthenticatedUser,
    @Body() body: any,
  ) {
    const data = await this.cityService.updateCityConfig(user, body);
    return {
      code: 200,
      message: '城市分公司配置已更新生效',
      data,
    };
  }
}
