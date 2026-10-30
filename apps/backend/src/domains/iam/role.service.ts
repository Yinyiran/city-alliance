import {
  Injectable,
  Inject,
  BadRequestException,
  NotFoundException,
  ForbiddenException,
} from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { roles, userRoles } from '../../infrastructure/drizzle/schema/iam.schema';
import { CreateRoleDto, UpdateRoleDto } from '@city-alliance/contracts';
import { eq, sql } from 'drizzle-orm';

@Injectable()
export class RoleService {
  constructor(@Inject(DRIZZLE_DB) private readonly db: DrizzleDatabase) {}

  /**
   * 全域细粒度权限点树状字典
   */
  getPermissionsTree() {
    return [
      {
        id: 'platform_scope',
        label: '🏢 平台总部与多租户管理',
        children: [
          { id: 'city:read', label: '查看城市分公司' },
          { id: 'city:write', label: '开通/编辑城市分公司' },
          { id: 'shop:audit', label: '商家主理人入驻审核' },
          { id: 'tenant:manage', label: '多租户组织架构配置' },
        ],
      },
      {
        id: 'shop_scope',
        label: '🏪 品牌主理人与店铺管理',
        children: [
          { id: 'shop:read', label: '查看店铺档案' },
          { id: 'shop:write', label: '修改店铺资料与企微设置' },
        ],
      },
      {
        id: 'store_scope',
        label: '📍 同城实体门店与店员收银',
        children: [
          { id: 'store:read', label: '查看线下门店' },
          { id: 'store:write', label: '新增/编辑实体门店与活码' },
          { id: 'clerk:read', label: '查看门店店员' },
          { id: 'clerk:write', label: '店员建档与赋权' },
          { id: 'trade:verify', label: '自提卡券扫码核销' },
          { id: 'shift:manage', label: '收银交接班与对账' },
        ],
      },
      {
        id: 'goods_scope',
        label: '📦 全渠道商品与矩阵中心',
        children: [
          { id: 'goods:read', label: '查看商品列表' },
          { id: 'goods:write', label: '发布与编辑商品SPU/SKU' },
          { id: 'goods:category', label: '多级类目配置' },
          { id: 'goods:freight', label: '运费阶梯计费模板' },
        ],
      },
      {
        id: 'trade_scope',
        label: '📑 订单履约与售后仲裁',
        children: [
          { id: 'trade:read', label: '查看全渠道订单' },
          { id: 'trade:write', label: '订单发货与履约处理' },
          { id: 'trade:dispatch', label: '同城外卖三方配送调度' },
          { id: 'trade:refund', label: '售后退款审核与仲裁' },
        ],
      },
      {
        id: 'member_scope',
        label: '👥 锁客会员与营销中心',
        children: [
          { id: 'member:read', label: '查看会员档案与锁客拓扑' },
          { id: 'member:write', label: '会员等级与积分调整' },
          { id: 'marketing:coupon', label: '优惠券发放与营销' },
          { id: 'marketing:signin', label: '签到连签奖池配置' },
        ],
      },
      {
        id: 'settle_scope',
        label: '💰 资金清算与分润提现',
        children: [
          { id: 'settle:read', label: '查看分润账本与台账' },
          { id: 'settle:write', label: '发起主理人/分公司提现' },
          { id: 'settle:audit', label: '财务打款审核与个税代扣' },
        ],
      },
      {
        id: 'system_scope',
        label: '⚙️ 组织与系统安全中控',
        children: [
          { id: 'user:read', label: '查看系统账号' },
          { id: 'user:write', label: '账号创建/编辑/重置密码' },
          { id: 'role:manage', label: '角色权限矩阵配置' },
          { id: 'system:config', label: '全局系统参数设定' },
          { id: 'system:log', label: '操作审计日志查看' },
        ],
      },
    ];
  }

  async getRoles(tenantType?: string) {
    const conditions = [];
    if (tenantType) {
      conditions.push(eq(roles.tenantType, tenantType));
    }

    const roleList = await this.db
      .select({
        id: roles.id,
        code: roles.code,
        name: roles.name,
        tenantType: roles.tenantType,
        permissions: roles.permissions,
        createdAt: roles.createdAt,
      })
      .from(roles)
      .where(conditions.length > 0 ? conditions[0] : undefined)
      .orderBy(roles.createdAt);

    // 统计各角色关联的用户数量
    const userCountRows = await this.db
      .select({
        roleId: userRoles.roleId,
        count: sql<number>`count(*)`,
      })
      .from(userRoles)
      .groupBy(userRoles.roleId);

    const countMap: Record<string, number> = {};
    for (const r of userCountRows) {
      countMap[r.roleId] = Number(r.count || 0);
    }

    return roleList.map((r) => ({
      ...r,
      userCount: countMap[r.id] || 0,
      isSystem: ['SUPER_ADMIN', 'CITY_AGENT', 'SHOP_OWNER', 'STORE_MANAGER', 'STORE_CLERK', 'SUPPLIER'].includes(
        r.code,
      ),
    }));
  }

  async createRole(dto: CreateRoleDto) {
    const existing = await this.db
      .select({ id: roles.id })
      .from(roles)
      .where(eq(roles.code, dto.code.trim().toUpperCase()))
      .limit(1);

    if (existing.length > 0) {
      throw new BadRequestException('该角色代码标识已存在');
    }

    const inserted = await this.db
      .insert(roles)
      .values({
        code: dto.code.trim().toUpperCase(),
        name: dto.name.trim(),
        tenantType: dto.tenantType,
        permissions: dto.permissions || [],
      })
      .returning();

    return inserted[0];
  }

  async updateRole(id: string, dto: UpdateRoleDto) {
    const existing = await this.db
      .select()
      .from(roles)
      .where(eq(roles.id, id))
      .limit(1);

    if (existing.length === 0) {
      throw new NotFoundException('角色不存在');
    }

    const updates: Record<string, any> = {};
    if (dto.name) updates.name = dto.name.trim();
    if (dto.permissions) updates.permissions = dto.permissions;

    await this.db.update(roles).set(updates).where(eq(roles.id, id));
    return { success: true };
  }

  async deleteRole(id: string) {
    const existing = await this.db
      .select()
      .from(roles)
      .where(eq(roles.id, id))
      .limit(1);

    if (existing.length === 0) {
      throw new NotFoundException('角色不存在');
    }

    const systemRoleCodes = [
      'SUPER_ADMIN',
      'CITY_AGENT',
      'SHOP_OWNER',
      'STORE_MANAGER',
      'STORE_CLERK',
      'SUPPLIER',
    ];
    if (systemRoleCodes.includes(existing[0].code)) {
      throw new ForbiddenException('系统内置基础角色禁止删除');
    }

    await this.db.delete(roles).where(eq(roles.id, id));
    return { success: true };
  }
}
