import {
  Controller,
  Get,
  Post,
  Put,
  Query,
  Param,
  Body,
  UsePipes,
  UseGuards,
} from '@nestjs/common';
import { StoreService } from './store.service';
import {
  SearchStoreNearbyDto,
  SearchStoreNearbySchema,
  RoleEnum,
} from '@city-alliance/contracts';
import { ZodValidationPipe } from '../../common/pipes/zod-validation.pipe';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Controller('stores')
export class StoreController {
  constructor(private readonly storeService: StoreService) {}

  @Get(['', 'list'])
  @UseGuards(JwtAuthGuard)
  async getStores(
    @CurrentUser() user: AuthenticatedUser,
    @Query()
    query: {
      keyword?: string;
      status?: string;
      shopId?: string;
      page?: number;
      pageSize?: number;
    },
  ) {
    if (user.role === RoleEnum.SHOP_OWNER && user.shopId) {
      query.shopId = user.shopId;
    }
    const data = await this.storeService.getStoreList(query);
    return {
      code: 200,
      data,
    };
  }

  @Get('clerks')
  @UseGuards(JwtAuthGuard)
  async getClerks(
    @CurrentUser() user: AuthenticatedUser,
    @Query()
    query: {
      storeId?: string;
      shopId?: string;
      keyword?: string;
      page?: number;
      pageSize?: number;
    },
  ) {
    if (
      (user.role === RoleEnum.STORE_MANAGER || user.role === RoleEnum.STORE_CLERK) &&
      user.storeId
    ) {
      query.storeId = user.storeId;
    } else if (user.role === RoleEnum.SHOP_OWNER && user.shopId) {
      query.shopId = user.shopId;
    }

    const data = await this.storeService.getClerksList(query);
    return {
      code: 200,
      data,
    };
  }

  @Post('clerks')
  @UseGuards(JwtAuthGuard)
  async createClerk(
    @CurrentUser() user: AuthenticatedUser,
    @Body()
    body: {
      storeId?: string;
      realName: string;
      phone: string;
      clerkNo?: string;
      role: string;
      permissions?: string[];
    },
  ) {
    const targetStoreId =
      (user.role === RoleEnum.STORE_MANAGER || user.role === RoleEnum.STORE_CLERK)
        ? user.storeId!
        : body.storeId!;

    const data = await this.storeService.createClerk({
      ...body,
      storeId: targetStoreId,
    });
    return {
      code: 200,
      message: '店员创建成功',
      data,
    };
  }

  @Get('shifts')
  @UseGuards(JwtAuthGuard)
  async getShifts(
    @CurrentUser() user: AuthenticatedUser,
    @Query()
    query: {
      storeId?: string;
      status?: string;
      page?: number;
      pageSize?: number;
    },
  ) {
    if (
      (user.role === RoleEnum.STORE_MANAGER || user.role === RoleEnum.STORE_CLERK) &&
      user.storeId
    ) {
      query.storeId = user.storeId;
    }

    const data = await this.storeService.getShiftsList(query);
    return {
      code: 200,
      data,
    };
  }

  @Get('shifts/current')
  @UseGuards(JwtAuthGuard)
  async getCurrentShift(
    @CurrentUser() user: AuthenticatedUser,
    @Query('storeId') storeId?: string,
  ) {
    const targetStoreId =
      (user.role === RoleEnum.STORE_MANAGER || user.role === RoleEnum.STORE_CLERK)
        ? user.storeId!
        : storeId || user.storeId;

    if (!targetStoreId) {
      return {
        code: 200,
        data: { currentShift: null, todaySummary: { verifyCount: 0, verifyAmount: '0.00' } },
      };
    }

    const data = await this.storeService.getCurrentShift(targetStoreId);
    return {
      code: 200,
      data,
    };
  }

  @Post('shifts/open')
  @UseGuards(JwtAuthGuard)
  async openShift(
    @CurrentUser() user: AuthenticatedUser,
    @Body()
    body: {
      storeId?: string;
      clerkId?: string;
      remark?: string;
    },
  ) {
    const storeId = user.storeId || body.storeId;
    const clerkId = body.clerkId || user.id;

    const data = await this.storeService.openShift({
      storeId: storeId!,
      clerkId,
      remark: body.remark,
    });
    return {
      code: 200,
      message: '收银当班签到成功',
      data,
    };
  }

  @Post('shifts/close')
  @UseGuards(JwtAuthGuard)
  async closeShift(
    @Body()
    body: {
      shiftId: string;
      cashTotal: number;
      scanTotal: number;
      diffAmount?: number;
      remark?: string;
    },
  ) {
    const data = await this.storeService.closeShift(body.shiftId, body);
    return {
      code: 200,
      message: '收银交接班轧账成功',
      data,
    };
  }

  @Get('stats')
  @UseGuards(JwtAuthGuard)
  async getStoreStats(
    @CurrentUser() user: AuthenticatedUser,
    @Query('storeId') storeId?: string,
  ) {
    const targetStoreId =
      (user.role === RoleEnum.STORE_MANAGER || user.role === RoleEnum.STORE_CLERK)
        ? user.storeId!
        : storeId || user.storeId;

    if (!targetStoreId) {
      return { code: 200, data: null };
    }

    const data = await this.storeService.getStoreStats(targetStoreId);
    return {
      code: 200,
      data,
    };
  }

  @Post()
  @UseGuards(JwtAuthGuard)
  async createStore(
    @Body()
    body: {
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
