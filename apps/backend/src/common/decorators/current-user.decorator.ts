import { createParamDecorator, ExecutionContext } from '@nestjs/common';
import { RoleEnum, TenantType, TerminalType } from '@city-alliance/contracts';

export interface AuthenticatedUser {
  id: string;
  username: string;
  nickname: string;
  avatarUrl?: string | null;
  mobile?: string | null;
  role: RoleEnum;
  tenantType: TenantType;
  tenantId?: string | null;
  shopId?: string | null;
  storeId?: string | null;
  cityId?: string | null;
  permissions: string[];
  terminal?: TerminalType;
  memberCode?: string;
}

export const CurrentUser = createParamDecorator(
  (data: keyof AuthenticatedUser | undefined, ctx: ExecutionContext) => {
    const request = ctx.switchToHttp().getRequest();
    const user = request.user as AuthenticatedUser;
    return data ? user?.[data] : user;
  },
);
