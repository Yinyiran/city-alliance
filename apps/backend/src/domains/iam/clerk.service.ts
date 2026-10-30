import {
  Injectable,
  NotFoundException,
  BadRequestException,
  Inject,
} from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import {
  storeClerks,
  stores,
  shops,
  users,
  roles,
  userRoles,
} from '../../infrastructure/drizzle/schema/index';
import { eq, and, or, like, sql, inArray } from 'drizzle-orm';
import {
  CreateClerkDto,
  UpdateClerkDto,
  QueryClerkDto,
  RoleEnum,
  ClerkRoleEnum,
  TenantType,
  UserStatus,
} from '@city-alliance/contracts';
import { AuthenticatedUser } from '../../common/decorators/current-user.decorator';
import { DataScopeHelper } from '../../common/helpers/data-scope.helper';
import * as crypto from 'crypto';

@Injectable()
export class ClerkService {
  constructor(@Inject(DRIZZLE_DB) private readonly db: DrizzleDatabase) {}

  private hashPassword(password: string): string {
    return crypto.createHash('sha256').update(password + '_city_alliance_salt').digest('hex');
  }

  /**
   * 主理人添加门店店员并分配角色与细粒度权限
   */
  async createClerk(user: AuthenticatedUser, dto: CreateClerkDto) {
    // 1. 查询目标实体门店
    const storeList = await this.db
      .select({
        id: stores.id,
        shopId: stores.shopId,
        name: stores.name,
      })
      .from(stores)
      .where(eq(stores.id, dto.storeId))
      .limit(1);

    const store = storeList[0];
    if (!store) {
      throw new NotFoundException('实体门店不存在');
    }

    // 2. 严格多租户数据范围防越权校验
    DataScopeHelper.validateStoreAccess(user, store.id, store.shopId);

    // 3. 校验该门店是否已存在同工号店员
    const existingClerkNo = await this.db
      .select({ id: storeClerks.id })
      .from(storeClerks)
      .where(and(eq(storeClerks.storeId, dto.storeId), eq(storeClerks.clerkNo, dto.clerkNo)))
      .limit(1);

    if (existingClerkNo.length > 0) {
      throw new BadRequestException(`该门店下已存在工号为【${dto.clerkNo}】的店员`);
    }

    // 4. 查询或创建用户登录账号
    let userId: string;
    const existingUsers = await this.db
      .select({ id: users.id, mobile: users.mobile })
      .from(users)
      .where(or(eq(users.mobile, dto.phone), eq(users.username, `clerk_${dto.phone}`)))
      .limit(1);

    if (existingUsers.length > 0) {
      userId = existingUsers[0].id;
    } else {
      const defaultPassword = dto.initialPassword || dto.phone.slice(-6);
      const [newUser] = await this.db
        .insert(users)
        .values({
          username: `clerk_${dto.phone}`,
          passwordHash: this.hashPassword(defaultPassword),
          nickname: dto.realName,
          mobile: dto.phone,
          status: 'ACTIVE',
        })
        .returning({ id: users.id });
      userId = newUser.id;
    }

    // 5. 赋予对应岗位角色的默认权限与自定义细粒度权限
    const rolePermissionsMap: Record<ClerkRoleEnum, string[]> = {
      [ClerkRoleEnum.CASHIER]: ['order:read', 'trade:verify', 'pos:cashier', 'pos:shift'],
      [ClerkRoleEnum.GUIDE]: ['order:read', 'trade:appointment:reschedule'],
      [ClerkRoleEnum.MANAGER]: ['order:*', 'trade:*', 'pos:*', 'clerk:read'],
    };

    const basePermissions = rolePermissionsMap[dto.role] || ['order:read', 'trade:verify'];
    const mergedPermissions = Array.from(new Set([...basePermissions, ...(dto.permissions || [])]));

    // 6. 分配系统角色关联
    const roleCode = dto.role === ClerkRoleEnum.MANAGER ? RoleEnum.STORE_MANAGER : RoleEnum.STORE_CLERK;
    const roleRecords = await this.db
      .select({ id: roles.id })
      .from(roles)
      .where(eq(roles.code, roleCode))
      .limit(1);

    if (roleRecords.length > 0) {
      await this.db
        .insert(userRoles)
        .values({
          userId,
          roleId: roleRecords[0].id,
        })
        .onConflictDoNothing();
    }

    // 7. 插入店员档案
    const [clerk] = await this.db
      .insert(storeClerks)
      .values({
        storeId: dto.storeId,
        userId,
        clerkNo: dto.clerkNo,
        realName: dto.realName,
        phone: dto.phone,
        role: dto.role,
        permissions: mergedPermissions,
        verifyCount: 0,
        status: 'ACTIVE',
      })
      .returning();

    return {
      ...clerk,
      storeName: store.name,
    };
  }

  /**
   * 分页多维查询门店店员列表 (支持数据范围隔离)
   */
  async queryClerks(user: AuthenticatedUser, query: QueryClerkDto) {
    const dataScope = DataScopeHelper.resolveDataScope(user);

    // 构建允许查询的 storeIds
    let storeConditions: any[] = [];
    if (!dataScope.isAll) {
      if (dataScope.shopId) {
        // 查找属于该商家的所有门店
        const myStores = await this.db
          .select({ id: stores.id })
          .from(stores)
          .where(eq(stores.shopId, dataScope.shopId));
        const myStoreIds = myStores.map((s) => s.id);
        if (myStoreIds.length === 0) {
          return { total: 0, page: query.page, pageSize: query.pageSize, items: [] };
        }
        storeConditions.push(inArray(storeClerks.storeId, myStoreIds));
      } else if (dataScope.storeId) {
        storeConditions.push(eq(storeClerks.storeId, dataScope.storeId));
      }
    }

    if (query.storeId) {
      storeConditions.push(eq(storeClerks.storeId, query.storeId));
    }
    if (query.role) {
      storeConditions.push(eq(storeClerks.role, query.role));
    }
    if (query.status) {
      storeConditions.push(eq(storeClerks.status, query.status));
    }
    if (query.keyword) {
      storeConditions.push(
        or(
          like(storeClerks.realName, `%${query.keyword}%`),
          like(storeClerks.clerkNo, `%${query.keyword}%`),
          like(storeClerks.phone, `%${query.keyword}%`),
        ),
      );
    }

    const whereClause = storeConditions.length > 0 ? and(...storeConditions) : undefined;

    // 统计总数
    const countResult = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(storeClerks)
      .where(whereClause);
    const total = Number(countResult[0]?.count || 0);

    const offset = (query.page - 1) * query.pageSize;
    const clerkRows = await this.db
      .select({
        id: storeClerks.id,
        userId: storeClerks.userId,
        storeId: storeClerks.storeId,
        clerkNo: storeClerks.clerkNo,
        realName: storeClerks.realName,
        phone: storeClerks.phone,
        role: storeClerks.role,
        permissions: storeClerks.permissions,
        verifyCount: storeClerks.verifyCount,
        status: storeClerks.status,
        createdAt: storeClerks.createdAt,
        storeName: stores.name,
      })
      .from(storeClerks)
      .leftJoin(stores, eq(storeClerks.storeId, stores.id))
      .where(whereClause)
      .limit(query.pageSize)
      .offset(offset);

    return {
      total,
      page: query.page,
      pageSize: query.pageSize,
      items: clerkRows,
    };
  }

  /**
   * 编辑店员与权限变更
   */
  async updateClerk(user: AuthenticatedUser, clerkId: string, dto: UpdateClerkDto) {
    const clerkList = await this.db
      .select({
        id: storeClerks.id,
        storeId: storeClerks.storeId,
        userId: storeClerks.userId,
        shopId: stores.shopId,
      })
      .from(storeClerks)
      .innerJoin(stores, eq(storeClerks.storeId, stores.id))
      .where(eq(storeClerks.id, clerkId))
      .limit(1);

    const clerk = clerkList[0];
    if (!clerk) {
      throw new NotFoundException('店员不存在');
    }

    DataScopeHelper.validateStoreAccess(user, clerk.storeId, clerk.shopId);

    const updateFields: any = {
      updatedAt: new Date(),
    };
    if (dto.realName) updateFields.realName = dto.realName;
    if (dto.phone) updateFields.phone = dto.phone;
    if (dto.role) updateFields.role = dto.role;
    if (dto.permissions) updateFields.permissions = dto.permissions;
    if (dto.status) updateFields.status = dto.status;

    const [updated] = await this.db
      .update(storeClerks)
      .set(updateFields)
      .where(eq(storeClerks.id, clerkId))
      .returning();

    // 如果修改了手机号或姓名，联动同步 users 档案
    if (dto.phone || dto.realName) {
      await this.db
        .update(users)
        .set({
          mobile: dto.phone,
          nickname: dto.realName,
        })
        .where(eq(users.id, clerk.userId));
    }

    return updated;
  }

  /**
   * 启停用店员 (一键禁用核销/登录)
   */
  async toggleClerkStatus(user: AuthenticatedUser, clerkId: string) {
    const clerkList = await this.db
      .select({
        id: storeClerks.id,
        storeId: storeClerks.storeId,
        userId: storeClerks.userId,
        status: storeClerks.status,
        shopId: stores.shopId,
      })
      .from(storeClerks)
      .innerJoin(stores, eq(storeClerks.storeId, stores.id))
      .where(eq(storeClerks.id, clerkId))
      .limit(1);

    const clerk = clerkList[0];
    if (!clerk) {
      throw new NotFoundException('店员不存在');
    }

    DataScopeHelper.validateStoreAccess(user, clerk.storeId, clerk.shopId);

    const nextStatus = clerk.status === UserStatus.ACTIVE ? UserStatus.DISABLED : UserStatus.ACTIVE;

    const [updated] = await this.db
      .update(storeClerks)
      .set({
        status: nextStatus,
        updatedAt: new Date(),
      })
      .where(eq(storeClerks.id, clerkId))
      .returning();

    // 同步禁用 users 账号
    await this.db
      .update(users)
      .set({ status: nextStatus })
      .where(eq(users.id, clerk.userId));

    return updated;
  }

  /**
   * 删除店员
   */
  async deleteClerk(user: AuthenticatedUser, clerkId: string) {
    const clerkList = await this.db
      .select({
        id: storeClerks.id,
        storeId: storeClerks.storeId,
        shopId: stores.shopId,
      })
      .from(storeClerks)
      .innerJoin(stores, eq(storeClerks.storeId, stores.id))
      .where(eq(storeClerks.id, clerkId))
      .limit(1);

    const clerk = clerkList[0];
    if (!clerk) {
      throw new NotFoundException('店员不存在');
    }

    DataScopeHelper.validateStoreAccess(user, clerk.storeId, clerk.shopId);

    await this.db.delete(storeClerks).where(eq(storeClerks.id, clerkId));

    return { success: true, message: '店员已移除' };
  }
}
