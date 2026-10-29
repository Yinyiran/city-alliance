import { Injectable, OnModuleInit, OnModuleDestroy, Inject, Logger } from '@nestjs/common';
import { Queue, Worker, Job } from 'bullmq';
import { REDIS_CLIENT } from '../redis/redis.provider';
import Redis from 'ioredis';
import { DRIZZLE_DB, DrizzleDatabase } from '../drizzle/drizzle.provider';
import { commissionRecords, members, orders } from '../drizzle/schema/index';
import { eq, and, lte, gt } from 'drizzle-orm';

export const SETTLEMENT_QUEUE_NAME = 'settlement_unfreeze_queue';

@Injectable()
export class SettlementQueueService implements OnModuleInit, OnModuleDestroy {
  private readonly logger = new Logger(SettlementQueueService.name);
  private queue: Queue | null = null;
  private worker: Worker | null = null;

  constructor(
    @Inject(REDIS_CLIENT) private readonly redis: Redis,
    @Inject(DRIZZLE_DB) private readonly db: DrizzleDatabase,
  ) {}

  async onModuleInit() {
    try {
      const connection = {
        host: process.env.REDIS_HOST || '127.0.0.1',
        port: Number(process.env.REDIS_PORT) || 6379,
        password: process.env.REDIS_PASSWORD || 'city_redis_secret_2026',
      };

      this.queue = new Queue(SETTLEMENT_QUEUE_NAME, { connection });
      this.worker = new Worker(
        SETTLEMENT_QUEUE_NAME,
        async (job: Job) => {
          await this.processUnfreezeJob(job.data);
        },
        { connection },
      );

      this.logger.log(`BullMQ 结算解冻队列 Worker 初始化成功`);
    } catch (err: any) {
      this.logger.warn(`BullMQ 初始化降级: ${err.message}`);
    }
  }

  async onModuleDestroy() {
    await this.worker?.close();
    await this.queue?.close();
  }

  /**
   * 将新冻结佣金加入 7 天延迟解冻任务队列
   */
  async scheduleUnfreeze(commissionRecordId: string, delayDays: number = 7) {
    if (!this.queue) return;
    const delayMs = delayDays * 24 * 3600 * 1000;
    await this.queue.add(
      'unfreeze_commission',
      { commissionRecordId },
      { delay: delayMs, jobId: `unfreeze_${commissionRecordId}` },
    );
  }

  /**
   * 处理单条佣金解冻任务并执行条件校验
   * (前置必要条件: 1. 到期满7天; 2. 下级在平台产生真实消费)
   */
  async processUnfreezeJob(data: { commissionRecordId: string }) {
    const records = await this.db
      .select()
      .from(commissionRecords)
      .where(
        and(
          eq(commissionRecords.id, data.commissionRecordId),
          eq(commissionRecords.status, 'FROZEN'),
        ),
      )
      .limit(1);

    const record = records[0];
    if (!record) return;

    // 校验前置条件: 查询该会员或其下级是否存在已付款消费
    const hasActiveOrders = await this.checkDownlineConsumption(record.beneficiaryId);
    if (!hasActiveOrders) {
      this.logger.log(
        `佣金记录 ${record.id} 虽满7天，但下级暂未产生实际有效消费，解冻顺延等待`,
      );
      return;
    }

    // 满足条件：将状态置为 SETTLED，并将资金转入可用余额 balance
    await this.db
      .update(commissionRecords)
      .set({
        status: 'SETTLED',
        unfrozenAt: new Date(),
        updatedAt: new Date(),
      })
      .where(eq(commissionRecords.id, record.id));

    // 更新会员钱包账户
    const memberList = await this.db
      .select()
      .from(members)
      .where(eq(members.id, record.beneficiaryId))
      .limit(1);

    const member = memberList[0];
    if (member) {
      const curBalance = Number(member.balance);
      const curFrozen = Number(member.frozenBalance);
      const commissionAmount = Number(record.amount);

      const newBalance = (curBalance + commissionAmount).toFixed(2);
      const newFrozen = Math.max(0, curFrozen - commissionAmount).toFixed(2);

      await this.db
        .update(members)
        .set({
          balance: newBalance,
          frozenBalance: newFrozen,
          updatedAt: new Date(),
        })
        .where(eq(members.id, member.id));
    }
  }

  /**
   * 检查下级在平台是否产生过真实消费
   */
  async checkDownlineConsumption(inviterMemberId: string): Promise<boolean> {
    const downlineList = await this.db
      .select({ id: members.id })
      .from(members)
      .where(eq(members.inviterId, inviterMemberId));

    if (downlineList.length === 0) {
      return true; // 若无下级关系（自消费分润），则直接满足
    }

    // 检查下级是否有已支付成功的订单
    for (const dl of downlineList) {
      const paidOrders = await this.db
        .select({ id: orders.id })
        .from(orders)
        .where(
          and(
            eq(orders.customerId, dl.id),
            eq(orders.payStatus, 'PAID'),
          ),
        )
        .limit(1);

      if (paidOrders.length > 0) {
        return true;
      }
    }

    return false;
  }
}
