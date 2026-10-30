import { describe, it, expect, beforeEach, vi } from 'vitest';
import { AuthService } from './auth.service';
import { ClerkService } from './clerk.service';
import { PermissionsGuard } from '../../common/guards/permissions.guard';
import { DataScopeHelper } from '../../common/helpers/data-scope.helper';
import {
  RoleEnum,
  TenantType,
  TerminalType,
  ClerkRoleEnum,
  UserStatus,
  DataScopeEnum,
} from '@city-alliance/contracts';
import { UnauthorizedException, ForbiddenException, BadRequestException } from '@nestjs/common';
import { Reflector } from '@nestjs/core';

describe('IAM & Multi-Tenancy Domain (组织体系与多租户权限域)', () => {
  let authService: AuthService;
  let clerkService: ClerkService;
  let permissionsGuard: PermissionsGuard;
  let reflector: Reflector;

  let mockDb: any;
  let mockRedis: any;
  let mockJwtService: any;
  let mockConfigService: any;

  beforeEach(() => {
    vi.clearAllMocks();

    mockRedis = {
      setex: vi.fn().mockResolvedValue('OK'),
      get: vi.fn().mockResolvedValue('ACTIVE'),
      del: vi.fn().mockResolvedValue(1),
    };

    mockJwtService = {
      signAsync: vi.fn().mockImplementation((payload) => Promise.resolve(`jwt_token_${payload.id || payload.sub}`)),
      verifyAsync: vi.fn().mockImplementation((token) => Promise.resolve({ sub: 'user-123', jti: 'jti-123', type: 'refresh' })),
    };

    mockConfigService = {
      get: vi.fn().mockReturnValue('mock_jwt_secret'),
    };

    mockDb = {
      select: vi.fn(),
      insert: vi.fn(),
      update: vi.fn(),
      delete: vi.fn(),
    };

    reflector = new Reflector();
    permissionsGuard = new PermissionsGuard(reflector);

    authService = new AuthService(
      mockDb,
      mockRedis,
      mockJwtService,
      mockConfigService,
    );

    clerkService = new ClerkService(mockDb);
  });

  describe('1.1 五端统一登录与 JWT 双 Token 轮换 (Dual Token Rotation)', () => {
    it('应成功验证密码、解析组织角色并签发 AccessToken(2h) 与 RefreshToken(7d)', async () => {
      // 模拟正确账号与哈希
      const passwordHash = (authService as any).hashPassword('password123');
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          where: vi.fn().mockReturnValue({
            limit: vi.fn().mockResolvedValue([
              {
                id: 'user-001',
                username: 'shop_boss',
                passwordHash,
                nickname: '烘焙老板',
                status: 'ACTIVE',
                tenantId: 'tenant-shop-001',
              },
            ]),
          }),
        }),
      });

      // 模拟角色查询
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          innerJoin: vi.fn().mockReturnValue({
            where: vi.fn().mockResolvedValue([
              {
                code: RoleEnum.SHOP_OWNER,
                tenantType: TenantType.SHOP,
                permissions: ['goods:read', 'trade:read'],
              },
            ]),
          }),
        }),
      });

      // 模拟 shops 查询
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          where: vi.fn().mockReturnValue({
            limit: vi.fn().mockResolvedValue([{ id: 'shop-001' }]),
          }),
        }),
      });

      const result = await authService.loginWithPassword({
        username: 'shop_boss',
        password: 'password123',
        terminal: TerminalType.ADMIN_WEB,
      });

      expect(result.accessToken).toBeDefined();
      expect(result.refreshToken).toBeDefined();
      expect(result.expiresIn).toBe(7200);
      expect(result.user.role).toBe(RoleEnum.SHOP_OWNER);
      expect(result.user.shopId).toBe('shop-001');
      expect(mockRedis.setex).toHaveBeenCalledTimes(1);
    });

    it('当非商家角色试图登录商家端或POS端时，应触发终端准入拦截', async () => {
      const passwordHash = (authService as any).hashPassword('pass123');
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          where: vi.fn().mockReturnValue({
            limit: vi.fn().mockResolvedValue([
              {
                id: 'user-customer',
                username: 'normal_customer',
                passwordHash,
                status: 'ACTIVE',
              },
            ]),
          }),
        }),
      });

      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          innerJoin: vi.fn().mockReturnValue({
            where: vi.fn().mockResolvedValue([
              {
                code: RoleEnum.MEMBER,
                tenantType: TenantType.PLATFORM,
                permissions: [],
              },
            ]),
          }),
        }),
      });

      await expect(
        authService.loginWithPassword({
          username: 'normal_customer',
          password: 'pass123',
          terminal: TerminalType.MERCHANT_APP,
        }),
      ).rejects.toThrow(ForbiddenException);
    });

    it('双 Token 轮换刷新时，旧 RefreshToken 应在 Redis 中即刻撤销并签发新 Token 对', async () => {
      mockRedis.get.mockResolvedValueOnce('ACTIVE');
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          where: vi.fn().mockReturnValue({
            limit: vi.fn().mockResolvedValue([
              { id: 'user-123', username: 'shop_boss', status: 'ACTIVE', nickname: 'Boss' },
            ]),
          }),
        }),
      });
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          innerJoin: vi.fn().mockReturnValue({
            where: vi.fn().mockResolvedValue([
              { code: RoleEnum.SHOP_OWNER, tenantType: TenantType.SHOP, permissions: ['order:read'] },
            ]),
          }),
        }),
      });

      const rotated = await authService.refreshToken({ refreshToken: 'valid_refresh_token' });

      // 验证撤销旧 token
      expect(mockRedis.del).toHaveBeenCalledWith('auth:refresh:user-123:jti-123');
      // 验证注册新 token
      expect(mockRedis.setex).toHaveBeenCalledTimes(1);
      expect(rotated.accessToken).toBeDefined();
      expect(rotated.refreshToken).toBeDefined();
    });
  });

  describe('1.2 微信小程序双端静默登录与手机号快捷授权', () => {
    it('顾客端小程序静默登录首次访问，应自动开户并生成 6 位数会员编码', async () => {
      // 首次用户不存在
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          where: vi.fn().mockReturnValue({
            limit: vi.fn().mockResolvedValue([]),
          }),
        }),
      });

      // 模拟创建用户
      mockDb.insert.mockReturnValueOnce({
        values: vi.fn().mockReturnValue({
          returning: vi.fn().mockResolvedValue([{ id: 'new-user-001', username: 'wx_123', nickname: '顾客' }]),
        }),
      });

      // 模拟创建会员
      mockDb.insert.mockReturnValueOnce({
        values: vi.fn().mockResolvedValue([{ id: 'member-001' }]),
      });

      const result = await authService.loginWithWechat({
        code: 'mock_wx_code_001',
        terminal: TerminalType.CUSTOMER_APP,
      });

      expect(result.user.role).toBe(RoleEnum.MEMBER);
      expect(result.user.memberCode).toHaveLength(6);
    });

    it('商家端小程序提供手机号快捷授权时，应自动关联门店店员档案与核销权限', async () => {
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          where: vi.fn().mockReturnValue({
            limit: vi.fn().mockResolvedValue([]),
          }),
        }),
      });

      // 模拟按手机号命中店员
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          innerJoin: vi.fn().mockReturnValue({
            leftJoin: vi.fn().mockReturnValue({
              where: vi.fn().mockReturnValue({
                limit: vi.fn().mockResolvedValue([
                  {
                    id: 'clerk-001',
                    userId: 'user-clerk-001',
                    storeId: 'store-001',
                    shopId: 'shop-001',
                    role: 'CASHIER',
                    realName: '小王收银员',
                    permissions: ['trade:verify', 'pos:shift'],
                    userUsername: 'clerk_13812345678',
                  },
                ]),
              }),
            }),
          }),
        }),
      });

      const result = await authService.loginWithWechat({
        code: 'wx_clerk_code',
        phoneCode: 'wx_phone_code_123',
        terminal: TerminalType.MERCHANT_APP,
      });

      expect(result.user.role).toBe(RoleEnum.STORE_CLERK);
      expect(result.user.storeId).toBe('store-001');
      expect(result.user.permissions).toContain('trade:verify');
    });
  });

  describe('1.3 五级组织拓扑与数据范围隔离 (Data Scope Isolation)', () => {
    it('平台超级管理员应享有 ALL 级全域通配权限', () => {
      const scope = DataScopeHelper.resolveDataScope({
        id: 'admin-1',
        username: 'admin',
        nickname: 'Admin',
        role: RoleEnum.SUPER_ADMIN,
        tenantType: TenantType.PLATFORM,
        permissions: ['*'],
      });
      expect(scope.isAll).toBe(true);
      expect(scope.scope).toBe(DataScopeEnum.ALL);
    });

    it('城市代理商应限制在 CITY 辖区数据范围', () => {
      const scope = DataScopeHelper.resolveDataScope({
        id: 'city-1',
        username: 'city_agent',
        nickname: '杭州分公司',
        role: RoleEnum.CITY_AGENT,
        tenantType: TenantType.CITY,
        tenantId: 'city-tenant-hangzhou',
        cityId: 'city-hangzhou',
        permissions: ['city:*'],
      });
      expect(scope.isAll).toBe(false);
      expect(scope.scope).toBe(DataScopeEnum.CITY);
      expect(scope.cityId).toBe('city-hangzhou');
    });

    it('商家主理人跨店铺越权访问时，应被严格拦截抛出 ForbiddenException', () => {
      const shopOwnerUser = {
        id: 'owner-1',
        username: 'owner1',
        nickname: '烘焙老板',
        role: RoleEnum.SHOP_OWNER,
        tenantType: TenantType.SHOP,
        shopId: 'shop-001',
        permissions: ['*'],
      };

      // 允许访问自己的店铺
      expect(() => DataScopeHelper.validateShopAccess(shopOwnerUser, 'shop-001')).not.toThrow();

      // 越权访问其他店铺时被拦截
      expect(() => DataScopeHelper.validateShopAccess(shopOwnerUser, 'shop-999')).toThrow(
        ForbiddenException,
      );
    });

    it('店员跨门店核销或操作时，应被严格拦截抛出 ForbiddenException', () => {
      const clerkUser = {
        id: 'clerk-1',
        username: 'clerk1',
        nickname: '店员小张',
        role: RoleEnum.STORE_CLERK,
        tenantType: TenantType.STORE,
        storeId: 'store-west-lake',
        shopId: 'shop-001',
        permissions: ['trade:verify'],
      };

      // 本店操作通过
      expect(() => DataScopeHelper.validateStoreAccess(clerkUser, 'store-west-lake')).not.toThrow();

      // 跨店操作拦截
      expect(() => DataScopeHelper.validateStoreAccess(clerkUser, 'store-other')).toThrow(
        ForbiddenException,
      );
    });
  });

  describe('1.4 动态菜单树与细粒度按钮鉴权 (PermissionsGuard & Dynamic Menu)', () => {
    it('PermissionsGuard 对超级管理员或带通配符的用户应一律放行', () => {
      vi.spyOn(reflector, 'getAllAndOverride').mockReturnValue(['clerk:delete', 'order:refund']);

      const mockContext = {
        getHandler: vi.fn(),
        getClass: vi.fn(),
        switchToHttp: () => ({
          getRequest: () => ({
            user: { role: RoleEnum.SUPER_ADMIN, permissions: ['*'] },
          }),
        }),
      } as any;

      expect(permissionsGuard.canActivate(mockContext)).toBe(true);
    });

    it('PermissionsGuard 当用户缺少对应细粒度权限时，应抛出 ForbiddenException', () => {
      vi.spyOn(reflector, 'getAllAndOverride').mockReturnValue(['clerk:delete']);

      const mockContext = {
        getHandler: vi.fn(),
        getClass: vi.fn(),
        switchToHttp: () => ({
          getRequest: () => ({
            user: { role: RoleEnum.STORE_CLERK, permissions: ['order:read', 'trade:verify'] },
          }),
        }),
      } as any;

      expect(() => permissionsGuard.canActivate(mockContext)).toThrow(ForbiddenException);
    });

    it('不同角色请求 getMenuTree 应返回符合其组织权限的动态路由树', async () => {
      const superAdminMenus = await authService.getMenuTree({
        id: 'admin',
        username: 'admin',
        nickname: 'Admin',
        role: RoleEnum.SUPER_ADMIN,
        tenantType: TenantType.PLATFORM,
        permissions: ['*'],
      });
      expect(superAdminMenus.some((m) => m.path === '/platform/cities')).toBe(true);
      expect(superAdminMenus.some((m) => m.path === '/system/configs')).toBe(true);

      const cityAgentMenus = await authService.getMenuTree({
        id: 'city',
        username: 'city',
        nickname: '杭州分公司',
        role: RoleEnum.CITY_AGENT,
        tenantType: TenantType.CITY,
        permissions: ['city:*'],
      });
      expect(cityAgentMenus.some((m) => m.path === '/city/screen')).toBe(true);
      expect(cityAgentMenus.every((m) => m.path !== '/system/configs')).toBe(true);
    });
  });

  describe('1.5 门店员工与角色配置 (Clerk Management Service)', () => {
    it('主理人成功添加店员，自动绑定账号、分配对应岗位与细粒度权限', async () => {
      const shopOwnerUser = {
        id: 'owner-1',
        username: 'boss',
        nickname: '主理人',
        role: RoleEnum.SHOP_OWNER,
        tenantType: TenantType.SHOP,
        shopId: 'shop-001',
        permissions: ['*'],
      };

      // 模拟门店存在且属于该商家
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          where: vi.fn().mockReturnValue({
            limit: vi.fn().mockResolvedValue([{ id: 'store-001', shopId: 'shop-001', name: '西湖总店' }]),
          }),
        }),
      });

      // 模拟工号查重 (未重复)
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          where: vi.fn().mockReturnValue({
            limit: vi.fn().mockResolvedValue([]),
          }),
        }),
      });

      // 模拟手机号查重 (新用户)
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          where: vi.fn().mockReturnValue({
            limit: vi.fn().mockResolvedValue([]),
          }),
        }),
      });

      // 模拟创建 users 账号
      mockDb.insert.mockReturnValueOnce({
        values: vi.fn().mockReturnValue({
          returning: vi.fn().mockResolvedValue([{ id: 'user-clerk-new' }]),
        }),
      });

      // 模拟查询角色
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          where: vi.fn().mockReturnValue({
            limit: vi.fn().mockResolvedValue([{ id: 'role-cashier-id' }]),
          }),
        }),
      });

      // 模拟分配 userRoles
      mockDb.insert.mockReturnValueOnce({
        values: vi.fn().mockReturnValue({
          onConflictDoNothing: vi.fn().mockResolvedValue({}),
        }),
      });

      // 模拟插入 storeClerks
      mockDb.insert.mockReturnValueOnce({
        values: vi.fn().mockReturnValue({
          returning: vi.fn().mockResolvedValue([
            {
              id: 'clerk-001',
              storeId: 'store-001',
              userId: 'user-clerk-new',
              clerkNo: 'CLK-008',
              realName: '李收银',
              phone: '13811223344',
              role: ClerkRoleEnum.CASHIER,
              permissions: ['order:read', 'trade:verify', 'pos:cashier', 'pos:shift'],
              status: 'ACTIVE',
            },
          ]),
        }),
      });

      const clerk = await clerkService.createClerk(shopOwnerUser, {
        storeId: 'store-001',
        clerkNo: 'CLK-008',
        realName: '李收银',
        phone: '13811223344',
        role: ClerkRoleEnum.CASHIER,
        permissions: ['pos:cashier'],
      });

      expect(clerk.clerkNo).toBe('CLK-008');
      expect(clerk.storeName).toBe('西湖总店');
      expect(clerk.permissions).toContain('trade:verify');
    });

    it('一键启停用店员时，应联动更新店员状态与底层用户账号状态', async () => {
      const shopOwnerUser = {
        id: 'owner-1',
        username: 'boss',
        nickname: '主理人',
        role: RoleEnum.SHOP_OWNER,
        tenantType: TenantType.SHOP,
        shopId: 'shop-001',
        permissions: ['*'],
      };

      // 模拟查询店员存在
      mockDb.select.mockReturnValueOnce({
        from: vi.fn().mockReturnValue({
          innerJoin: vi.fn().mockReturnValue({
            where: vi.fn().mockReturnValue({
              limit: vi.fn().mockResolvedValue([
                {
                  id: 'clerk-001',
                  storeId: 'store-001',
                  userId: 'user-clerk-1',
                  status: UserStatus.ACTIVE,
                  shopId: 'shop-001',
                },
              ]),
            }),
          }),
        }),
      });

      // 模拟更新 storeClerks
      mockDb.update.mockReturnValueOnce({
        set: vi.fn().mockReturnValue({
          where: vi.fn().mockReturnValue({
            returning: vi.fn().mockResolvedValue([{ id: 'clerk-001', status: UserStatus.DISABLED }]),
          }),
        }),
      });

      // 模拟更新 users
      mockDb.update.mockReturnValueOnce({
        set: vi.fn().mockReturnValue({
          where: vi.fn().mockResolvedValue({}),
        }),
      });

      const updated = await clerkService.toggleClerkStatus(shopOwnerUser, 'clerk-001');
      expect(updated.status).toBe(UserStatus.DISABLED);
    });
  });
});
