import {
  Injectable,
  Inject,
  BadRequestException,
  NotFoundException,
  ForbiddenException,
} from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { users, userRoles, roles, tenants } from '../../infrastructure/drizzle/schema/iam.schema';
import {
  CreateUserDto,
  UpdateUserDto,
  QueryUserDto,
  RoleEnum,
  TenantType,
} from '@city-alliance/contracts';
import { eq, and, or, like, sql, desc, inArray } from 'drizzle-orm';
import * as crypto from 'crypto';
import { AuthenticatedUser } from '../../common/decorators/current-user.decorator';

@Injectable()
export class UserService {
  constructor(@Inject(DRIZZLE_DB) private readonly db: DrizzleDatabase) {}

  private hashPassword(password: string): string {
    return crypto.createHash('sha256').update(password + '_city_alliance_salt').digest('hex');
  }

  async getUsers(currentUser: AuthenticatedUser, query: QueryUserDto) {
    const page = query.page || 1;
    const pageSize = query.pageSize || 20;
    const offset = (page - 1) * pageSize;

    const conditions = [];

    // 数据权限隔离
    if (currentUser.role !== RoleEnum.SUPER_ADMIN) {
      if (currentUser.tenantType === TenantType.CITY && currentUser.tenantId) {
        // 城市分公司只能查看本城市及下属商家的账号
        const childTenants = await this.db
          .select({ id: tenants.id })
          .from(tenants)
          .where(eq(tenants.parentId, currentUser.tenantId));
        const allowedTenantIds = [currentUser.tenantId, ...childTenants.map((t) => t.id)];
        conditions.push(inArray(users.tenantId, allowedTenantIds));
      } else if (currentUser.tenantId) {
        // 商家只能查看属于自己店铺的账号
        conditions.push(eq(users.tenantId, currentUser.tenantId));
      }
    }

    if (query.keyword?.trim()) {
      const kw = `%${query.keyword.trim()}%`;
      conditions.push(or(like(users.username, kw), like(users.nickname, kw), like(users.mobile, kw)));
    }

    if (query.status) {
      conditions.push(eq(users.status, query.status));
    }

    const whereClause = conditions.length > 0 ? and(...conditions) : undefined;

    // 查询用户列表
    const userList = await this.db
      .select({
        id: users.id,
        username: users.username,
        nickname: users.nickname,
        mobile: users.mobile,
        avatarUrl: users.avatarUrl,
        tenantId: users.tenantId,
        tenantName: tenants.name,
        tenantType: tenants.type,
        status: users.status,
        createdAt: users.createdAt,
        updatedAt: users.updatedAt,
      })
      .from(users)
      .leftJoin(tenants, eq(users.tenantId, tenants.id))
      .where(whereClause)
      .orderBy(desc(users.createdAt))
      .limit(pageSize)
      .offset(offset);

    // 查询用户角色关联
    const userIds = userList.map((u) => u.id);
    let rolesByUser: Record<string, { id: string; code: string; name: string }[]> = {};

    if (userIds.length > 0) {
      const userRoleRows = await this.db
        .select({
          userId: userRoles.userId,
          roleId: roles.id,
          roleCode: roles.code,
          roleName: roles.name,
        })
        .from(userRoles)
        .innerJoin(roles, eq(userRoles.roleId, roles.id))
        .where(inArray(userRoles.userId, userIds));

      for (const ur of userRoleRows) {
        if (!rolesByUser[ur.userId]) rolesByUser[ur.userId] = [];
        rolesByUser[ur.userId].push({
          id: ur.roleId,
          code: ur.roleCode,
          name: ur.roleName,
        });
      }
    }

    // 统计总数
    const countResult = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(users)
      .where(whereClause);
    const total = Number(countResult[0]?.count || 0);

    const items = userList.map((u) => ({
      ...u,
      roles: rolesByUser[u.id] || [],
    }));

    return {
      items,
      total,
      page,
      pageSize,
    };
  }

  async createUser(currentUser: AuthenticatedUser, dto: CreateUserDto) {
    // 检查用户名唯一性
    const existing = await this.db
      .select({ id: users.id })
      .from(users)
      .where(eq(users.username, dto.username.trim()))
      .limit(1);

    if (existing.length > 0) {
      throw new BadRequestException('该登录账号已存在，请更换用户名');
    }

    // 检查角色是否存在
    const roleRecord = await this.db
      .select()
      .from(roles)
      .where(eq(roles.id, dto.roleId))
      .limit(1);

    if (roleRecord.length === 0) {
      throw new NotFoundException('指定的分配角色不存在');
    }

    let targetTenantId = dto.tenantId || null;
    if (currentUser.role !== RoleEnum.SUPER_ADMIN) {
      // 非超管限制为当前租户
      targetTenantId = currentUser.tenantId || null;
    }

    const passwordHash = this.hashPassword(dto.password);

    const inserted = await this.db
      .insert(users)
      .values({
        username: dto.username.trim(),
        passwordHash,
        nickname: dto.nickname.trim(),
        mobile: dto.mobile?.trim() || null,
        tenantId: targetTenantId,
        status: 'ACTIVE',
      })
      .returning();

    const newUser = inserted[0];

    // 关联角色
    await this.db.insert(userRoles).values({
      userId: newUser.id,
      roleId: dto.roleId,
    });

    return {
      id: newUser.id,
      username: newUser.username,
      nickname: newUser.nickname,
      mobile: newUser.mobile,
      tenantId: newUser.tenantId,
      status: newUser.status,
    };
  }

  async updateUser(currentUser: AuthenticatedUser, id: string, dto: UpdateUserDto) {
    const existing = await this.db
      .select()
      .from(users)
      .where(eq(users.id, id))
      .limit(1);

    if (existing.length === 0) {
      throw new NotFoundException('用户不存在');
    }

    const updates: Record<string, any> = { updatedAt: new Date() };
    if (dto.nickname !== undefined) updates.nickname = dto.nickname.trim();
    if (dto.mobile !== undefined) updates.mobile = dto.mobile?.trim() || null;
    if (dto.status !== undefined) updates.status = dto.status;
    if (currentUser.role === RoleEnum.SUPER_ADMIN && dto.tenantId !== undefined) {
      updates.tenantId = dto.tenantId;
    }

    await this.db.update(users).set(updates).where(eq(users.id, id));

    // 如果指定了角色更新
    if (dto.roleId) {
      await this.db.delete(userRoles).where(eq(userRoles.userId, id));
      await this.db.insert(userRoles).values({
        userId: id,
        roleId: dto.roleId,
      });
    }

    return { success: true };
  }

  async resetPassword(currentUser: AuthenticatedUser, id: string, newPassword: string) {
    const existing = await this.db
      .select({ id: users.id, username: users.username })
      .from(users)
      .where(eq(users.id, id))
      .limit(1);

    if (existing.length === 0) {
      throw new NotFoundException('用户不存在');
    }

    const passwordHash = this.hashPassword(newPassword);
    await this.db.update(users).set({ passwordHash, updatedAt: new Date() }).where(eq(users.id, id));

    return { success: true, message: `账号【${existing[0].username}】的密码已成功重置` };
  }

  async toggleStatus(currentUser: AuthenticatedUser, id: string, status: 'ACTIVE' | 'DISABLED') {
    const existing = await this.db
      .select({ id: users.id, username: users.username })
      .from(users)
      .where(eq(users.id, id))
      .limit(1);

    if (existing.length === 0) {
      throw new NotFoundException('用户不存在');
    }

    if (existing[0].username === 'admin' && status === 'DISABLED') {
      throw new ForbiddenException('系统超级管理员 admin 账号禁止停用');
    }

    await this.db.update(users).set({ status, updatedAt: new Date() }).where(eq(users.id, id));
    return { success: true, status };
  }
}
