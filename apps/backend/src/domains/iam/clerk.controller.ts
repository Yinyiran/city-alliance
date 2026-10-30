import {
  Controller,
  Get,
  Post,
  Put,
  Delete,
  Body,
  Param,
  Query,
  UseGuards,
  UsePipes,
} from '@nestjs/common';
import { ClerkService } from './clerk.service';
import {
  CreateClerkDto,
  CreateClerkSchema,
  UpdateClerkDto,
  UpdateClerkSchema,
  QueryClerkDto,
  QueryClerkSchema,
} from '@city-alliance/contracts';
import { ZodValidationPipe } from '../../common/pipes/zod-validation.pipe';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { PermissionsGuard } from '../../common/guards/permissions.guard';
import { RequirePermissions } from '../../common/decorators/permissions.decorator';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Controller('iam/clerks')
@UseGuards(JwtAuthGuard, PermissionsGuard)
export class ClerkController {
  constructor(private readonly clerkService: ClerkService) {}

  @Post()
  @RequirePermissions('clerk:create')
  @UsePipes(new ZodValidationPipe(CreateClerkSchema))
  async createClerk(
    @CurrentUser() user: AuthenticatedUser,
    @Body() dto: CreateClerkDto,
  ) {
    const data = await this.clerkService.createClerk(user, dto);
    return {
      code: 200,
      message: '门店店员已成功建档并分配权限',
      data,
    };
  }

  @Get()
  @RequirePermissions('clerk:read')
  @UsePipes(new ZodValidationPipe(QueryClerkSchema))
  async queryClerks(
    @CurrentUser() user: AuthenticatedUser,
    @Query() query: QueryClerkDto,
  ) {
    const data = await this.clerkService.queryClerks(user, query);
    return {
      code: 200,
      data,
    };
  }

  @Put(':id')
  @RequirePermissions('clerk:update')
  @UsePipes(new ZodValidationPipe(UpdateClerkSchema))
  async updateClerk(
    @CurrentUser() user: AuthenticatedUser,
    @Param('id') id: string,
    @Body() dto: UpdateClerkDto,
  ) {
    const data = await this.clerkService.updateClerk(user, id, dto);
    return {
      code: 200,
      message: '店员档案与权限已更新',
      data,
    };
  }

  @Post(':id/toggle-status')
  @RequirePermissions('clerk:update')
  async toggleStatus(
    @CurrentUser() user: AuthenticatedUser,
    @Param('id') id: string,
  ) {
    const data = await this.clerkService.toggleClerkStatus(user, id);
    return {
      code: 200,
      message: '店员启停状态已更新',
      data,
    };
  }

  @Delete(':id')
  @RequirePermissions('clerk:delete')
  async deleteClerk(
    @CurrentUser() user: AuthenticatedUser,
    @Param('id') id: string,
  ) {
    const data = await this.clerkService.deleteClerk(user, id);
    return {
      code: 200,
      message: '店员已移除',
      data,
    };
  }
}
