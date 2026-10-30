import { Injectable, CanActivate, ExecutionContext, ForbiddenException } from '@nestjs/common';
import { Reflector } from '@nestjs/core';
import { RoleEnum } from '@city-alliance/contracts';
import { PERMISSIONS_KEY } from '../decorators/permissions.decorator';

@Injectable()
export class PermissionsGuard implements CanActivate {
  constructor(private reflector: Reflector) {}

  canActivate(context: ExecutionContext): boolean {
    const requiredPermissions = this.reflector.getAllAndOverride<string[]>(
      PERMISSIONS_KEY,
      [context.getHandler(), context.getClass()],
    );

    if (!requiredPermissions || requiredPermissions.length === 0) {
      return true;
    }

    const { user } = context.switchToHttp().getRequest();
    if (!user) {
      throw new ForbiddenException('未登录或凭据无效');
    }

    // 平台超级管理员拥有全域通配权限
    if (user.role === RoleEnum.SUPER_ADMIN || user.permissions?.includes('*')) {
      return true;
    }

    const userPerms: string[] = user.permissions || [];
    const missingPermissions = requiredPermissions.filter((req) => {
      if (userPerms.includes(req)) return false;
      const prefixWildcard = req.split(':')[0] + ':*';
      if (userPerms.includes(prefixWildcard)) return false;
      return true;
    });

    if (missingPermissions.length > 0) {
      throw new ForbiddenException(`权限不足，缺少以下操作权限: ${missingPermissions.join(', ')}`);
    }

    return true;
  }
}
