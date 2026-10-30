import { ForbiddenException } from '@nestjs/common';
import { RoleEnum, TenantType, DataScopeEnum } from '@city-alliance/contracts';
import { AuthenticatedUser } from '../decorators/current-user.decorator';

export interface DataScopeFilter {
  scope: DataScopeEnum;
  isAll: boolean;
  cityId?: string | null;
  shopId?: string | null;
  storeId?: string | null;
  tenantId?: string | null;
}

export class DataScopeHelper {
  /**
   * 解析用户当前的数据范围
   */
  static resolveDataScope(user: AuthenticatedUser): DataScopeFilter {
    if (user.role === RoleEnum.SUPER_ADMIN || user.tenantType === TenantType.PLATFORM) {
      return {
        scope: DataScopeEnum.ALL,
        isAll: true,
      };
    }

    if (user.role === RoleEnum.CITY_AGENT || user.tenantType === TenantType.CITY) {
      return {
        scope: DataScopeEnum.CITY,
        isAll: false,
        cityId: user.cityId || user.tenantId,
        tenantId: user.tenantId,
      };
    }

    if (user.role === RoleEnum.SHOP_OWNER || user.tenantType === TenantType.SHOP) {
      return {
        scope: DataScopeEnum.SHOP,
        isAll: false,
        shopId: user.shopId || user.tenantId,
        tenantId: user.tenantId,
      };
    }

    if (
      user.role === RoleEnum.STORE_MANAGER ||
      user.role === RoleEnum.STORE_CLERK ||
      user.tenantType === TenantType.STORE
    ) {
      return {
        scope: DataScopeEnum.STORE,
        isAll: false,
        shopId: user.shopId,
        storeId: user.storeId,
        tenantId: user.tenantId,
      };
    }

    if (user.role === RoleEnum.SUPPLIER || user.tenantType === TenantType.SUPPLY) {
      return {
        scope: DataScopeEnum.SUPPLY,
        isAll: false,
        tenantId: user.tenantId,
      };
    }

    return {
      scope: DataScopeEnum.SELF,
      isAll: false,
      tenantId: user.tenantId,
    };
  }

  /**
   * 严格校验对商家数据的操作权限 (防跨租户越权攻击)
   */
  static validateShopAccess(user: AuthenticatedUser, targetShopId: string): void {
    if (user.role === RoleEnum.SUPER_ADMIN || user.tenantType === TenantType.PLATFORM) {
      return;
    }

    const userShopId = user.shopId || user.tenantId;
    if (userShopId && userShopId === targetShopId) {
      return;
    }

    throw new ForbiddenException('数据越权拦截：无权访问或操作非名下主理人商家的数据');
  }

  /**
   * 严格校验对门店数据的操作权限 (防跨门店越权攻击)
   */
  static validateStoreAccess(
    user: AuthenticatedUser,
    targetStoreId: string,
    targetStoreShopId?: string,
  ): void {
    if (user.role === RoleEnum.SUPER_ADMIN || user.tenantType === TenantType.PLATFORM) {
      return;
    }

    // 商家主理人可以管理自己名下的所有实体门店
    if (
      (user.role === RoleEnum.SHOP_OWNER || user.tenantType === TenantType.SHOP) &&
      targetStoreShopId
    ) {
      const userShopId = user.shopId || user.tenantId;
      if (userShopId === targetStoreShopId) {
        return;
      }
    }

    // 驻店店长或店员只能操作本店
    if (user.storeId && user.storeId === targetStoreId) {
      return;
    }

    throw new ForbiddenException('数据越权拦截：无权访问或操作非归属实体门店的数据');
  }
}
