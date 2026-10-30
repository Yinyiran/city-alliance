import { Injectable, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { systemConfigs, systemAuditLogs, users } from '../../infrastructure/drizzle/schema/index';
import { eq, desc, sql } from 'drizzle-orm';
import { AuthenticatedUser } from '../../common/decorators/current-user.decorator';

export interface AuditLogDto {
  userId?: string;
  tenantId?: string;
  action: string;
  targetEntity: string;
  targetId?: string;
  ipAddress?: string;
  userAgent?: string;
  changes?: any;
}

@Injectable()
export class SystemService {
  constructor(
    @Inject(DRIZZLE_DB)
    private readonly db: DrizzleDatabase,
  ) {}

  /**
   * 默认平台系统配置字典 (兜底保障)
   */
  private readonly defaultConfigs: Record<string, any> = {
    // 微信支付与结算通道
    mchId: '1688992211',
    apiV3Key: 'city_alliance_wechat_pay_v3_key_32bytes',
    certSerialNo: '7AB9C8E0123456789ABCDEF0123456789ABCDEF0',
    huifuMerId: 'HF883900192',

    // 清算时钟与核心规则
    settleUnfreezeDays: 7,
    orderAutoCancelMinutes: 30,
    taxThreshold: 800,
    partnerInviteRewardRatio: 25,
    cityBranchProfitRatio: 25,

    // 微信小程序双端配置
    customerWeappAppId: 'wx1a2b3c4d5e6f7g8h',
    customerWeappSecret: '9a8b7c6d5e4f3a2b1c0d9e8f7a6b5c4d',
    merchantWeappAppId: 'wx8h7g6f5e4d3c2b1a',
    merchantWeappSecret: '1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c6d',

    // 微信公众号与推送
    officialAppId: 'wx9988776655443322',
    officialSecret: 'abcdef0123456789abcdef0123456789',
    officialToken: 'city_alliance_token_2026',
    officialAesKey: 'cityallianceaeskey0123456789abcdefghijklmnopq',
    tmplPaySuccess: 'TMPL_PAY_SUCCESS_001',
    tmplVerifyNotify: 'TMPL_VERIFY_NOTIFY_002',
    tmplWithdrawSuccess: 'TMPL_WITHDRAW_SUCCESS_003',
  };

  /**
   * 获取系统全局配置 (融合数据库持久化与默认配置)
   */
  async getConfigs(group?: string): Promise<Record<string, any>> {
    const rows = await this.db
      .select()
      .from(systemConfigs);

    const result = { ...this.defaultConfigs };

    for (const row of rows) {
      if (group && row.group !== group) continue;
      result[row.configKey] = row.configValue;
    }

    return result;
  }

  /**
   * 批量保存系统配置并记录审计日志
   */
  async saveConfigs(
    configs: Record<string, any>,
    user?: AuthenticatedUser,
    ipAddress?: string,
    userAgent?: string,
  ): Promise<{ success: boolean; updatedCount: number }> {
    const keys = Object.keys(configs);
    let count = 0;

    for (const key of keys) {
      const val = configs[key];
      // 确定分组
      let group = 'SYSTEM';
      if (['mchId', 'apiV3Key', 'certSerialNo', 'huifuMerId'].includes(key)) {
        group = 'PAYMENT';
      } else if (['settleUnfreezeDays', 'orderAutoCancelMinutes', 'taxThreshold', 'partnerInviteRewardRatio', 'cityBranchProfitRatio'].includes(key)) {
        group = 'SETTLE_RULES';
      } else if (key.startsWith('customerWeapp') || key.startsWith('merchantWeapp')) {
        group = 'WEAPP';
      } else if (key.startsWith('official') || key.startsWith('tmpl')) {
        group = 'WECHAT';
      }

      await this.db
        .insert(systemConfigs)
        .values({
          configKey: key,
          configValue: val,
          group,
          description: `配置项 [${key}]`,
          updatedAt: new Date(),
        })
        .onConflictDoUpdate({
          target: systemConfigs.configKey,
          set: {
            configValue: val,
            group,
            updatedAt: new Date(),
          },
        });

      count++;
    }

    // 记录操作审计
    await this.recordAuditLog({
      userId: user?.id,
      tenantId: user?.tenantId || undefined,
      action: 'UPDATE_SYSTEM_CONFIGS',
      targetEntity: 'system_configs',
      targetId: 'GLOBAL',
      ipAddress: ipAddress || '127.0.0.1',
      userAgent,
      changes: configs,
    });

    return {
      success: true,
      updatedCount: count,
    };
  }

  /**
   * 记录审计日志
   */
  async recordAuditLog(dto: AuditLogDto): Promise<void> {
    try {
      await this.db.insert(systemAuditLogs).values({
        userId: dto.userId || null,
        tenantId: dto.tenantId || null,
        action: dto.action,
        targetEntity: dto.targetEntity,
        targetId: dto.targetId || null,
        ipAddress: dto.ipAddress || null,
        userAgent: dto.userAgent || null,
        changes: dto.changes || null,
      });
    } catch (err) {
      console.error('Failed to record audit log:', err);
    }
  }

  /**
   * 分页查询操作与合规审计日志
   */
  async getAuditLogs(query: { page?: number; pageSize?: number }) {
    const page = Math.max(1, Number(query.page) || 1);
    const pageSize = Math.min(100, Math.max(1, Number(query.pageSize) || 20));
    const offset = (page - 1) * pageSize;

    const rows = await this.db
      .select({
        id: systemAuditLogs.id,
        action: systemAuditLogs.action,
        targetEntity: systemAuditLogs.targetEntity,
        targetId: systemAuditLogs.targetId,
        ipAddress: systemAuditLogs.ipAddress,
        userAgent: systemAuditLogs.userAgent,
        changes: systemAuditLogs.changes,
        createdAt: systemAuditLogs.createdAt,
        operator: sql<string>`coalesce(${users.nickname}, ${users.username}, ${users.mobile}, '系统管理员')`,
      })
      .from(systemAuditLogs)
      .leftJoin(users, eq(systemAuditLogs.userId, users.id))
      .orderBy(desc(systemAuditLogs.createdAt))
      .limit(pageSize)
      .offset(offset);

    const countRes = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(systemAuditLogs);

    const total = Number(countRes[0]?.count || 0);

    return {
      items: rows,
      total,
      page,
      pageSize,
    };
  }
}
