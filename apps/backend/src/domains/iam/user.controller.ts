import {
  Controller,
  Get,
  Post,
  Put,
  Body,
  Param,
  Query,
  UseGuards,
  UsePipes,
} from '@nestjs/common';
import { UserService } from './user.service';
import {
  CreateUserDto,
  CreateUserSchema,
  UpdateUserDto,
  UpdateUserSchema,
  QueryUserDto,
  QueryUserSchema,
  ResetPasswordDto,
  ResetPasswordSchema,
} from '@city-alliance/contracts';
import { ZodValidationPipe } from '../../common/pipes/zod-validation.pipe';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { PermissionsGuard } from '../../common/guards/permissions.guard';
import { RequirePermissions } from '../../common/decorators/permissions.decorator';
import { CurrentUser, AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Controller('iam/users')
@UseGuards(JwtAuthGuard, PermissionsGuard)
export class UserController {
  constructor(private readonly userService: UserService) {}

  @Get()
  @RequirePermissions('user:read')
  @UsePipes(new ZodValidationPipe(QueryUserSchema))
  async getUsers(
    @CurrentUser() user: AuthenticatedUser,
    @Query() query: QueryUserDto,
  ) {
    const data = await this.userService.getUsers(user, query);
    return {
      code: 200,
      data,
    };
  }

  @Post()
  @RequirePermissions('user:write')
  @UsePipes(new ZodValidationPipe(CreateUserSchema))
  async createUser(
    @CurrentUser() user: AuthenticatedUser,
    @Body() dto: CreateUserDto,
  ) {
    const data = await this.userService.createUser(user, dto);
    return {
      code: 200,
      message: '用户账号已成功创建',
      data,
    };
  }

  @Put(':id')
  @RequirePermissions('user:write')
  @UsePipes(new ZodValidationPipe(UpdateUserSchema))
  async updateUser(
    @CurrentUser() user: AuthenticatedUser,
    @Param('id') id: string,
    @Body() dto: UpdateUserDto,
  ) {
    const data = await this.userService.updateUser(user, id, dto);
    return {
      code: 200,
      message: '用户账号已更新',
      data,
    };
  }

  @Post(':id/reset-password')
  @RequirePermissions('user:write')
  @UsePipes(new ZodValidationPipe(ResetPasswordSchema))
  async resetPassword(
    @CurrentUser() user: AuthenticatedUser,
    @Param('id') id: string,
    @Body() dto: ResetPasswordDto,
  ) {
    const data = await this.userService.resetPassword(user, id, dto.newPassword);
    return {
      code: 200,
      message: data.message,
    };
  }

  @Put(':id/status')
  @RequirePermissions('user:write')
  async toggleStatus(
    @CurrentUser() user: AuthenticatedUser,
    @Param('id') id: string,
    @Body() body: { status: 'ACTIVE' | 'DISABLED' },
  ) {
    const data = await this.userService.toggleStatus(user, id, body.status);
    return {
      code: 200,
      message: `用户状态已更新为 ${body.status === 'ACTIVE' ? '正常' : '已冻结'}`,
      data,
    };
  }
}
