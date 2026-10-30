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
import { RoleService } from './role.service';
import {
  CreateRoleDto,
  CreateRoleSchema,
  UpdateRoleDto,
  UpdateRoleSchema,
} from '@city-alliance/contracts';
import { ZodValidationPipe } from '../../common/pipes/zod-validation.pipe';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { PermissionsGuard } from '../../common/guards/permissions.guard';
import { RequirePermissions } from '../../common/decorators/permissions.decorator';

@Controller('iam/roles')
@UseGuards(JwtAuthGuard, PermissionsGuard)
export class RoleController {
  constructor(private readonly roleService: RoleService) {}

  @Get()
  @RequirePermissions('role:manage')
  async getRoles(@Query('tenantType') tenantType?: string) {
    const data = await this.roleService.getRoles(tenantType);
    return {
      code: 200,
      data,
    };
  }

  @Get('permissions-tree')
  @RequirePermissions('role:manage')
  getPermissionsTree() {
    const data = this.roleService.getPermissionsTree();
    return {
      code: 200,
      data,
    };
  }

  @Post()
  @RequirePermissions('role:manage')
  @UsePipes(new ZodValidationPipe(CreateRoleSchema))
  async createRole(@Body() dto: CreateRoleDto) {
    const data = await this.roleService.createRole(dto);
    return {
      code: 200,
      message: '角色已成功创建',
      data,
    };
  }

  @Put(':id')
  @RequirePermissions('role:manage')
  @UsePipes(new ZodValidationPipe(UpdateRoleSchema))
  async updateRole(@Param('id') id: string, @Body() dto: UpdateRoleDto) {
    const data = await this.roleService.updateRole(id, dto);
    return {
      code: 200,
      message: '角色信息与权限已更新',
      data,
    };
  }

  @Delete(':id')
  @RequirePermissions('role:manage')
  async deleteRole(@Param('id') id: string) {
    const data = await this.roleService.deleteRole(id);
    return {
      code: 200,
      message: '角色已删除',
      data,
    };
  }
}
