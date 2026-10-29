import { Injectable, BadRequestException, ConflictException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { storeSignInRecords, videoProgressRecords, members, stores, shops } from '../../infrastructure/drizzle/schema/index';
import { eq, and } from 'drizzle-orm';
import {
  StoreSignInRequestDto,
  StoreSignInResultDto,
  VideoProgressReportDto,
  SignInStatus,
} from '@city-alliance/contracts';

@Injectable()
export class SigninService {
  constructor(@Inject(DRIZZLE_DB) private readonly db: DrizzleDatabase) {}

  /**
   * 门店到店扫码签到
   * (规则: 单门店每月仅限1次签到；扣商家2元到平台，平台给用户1元现金+50积分+可附赠自提商品)
   */
  async handleStoreSignIn(
    memberId: string,
    dto: StoreSignInRequestDto,
  ): Promise<StoreSignInResultDto> {
    const now = new Date();
    const yearMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;

    // 1. 校验当月是否已在当前门店签到过
    const existing = await this.db
      .select()
      .from(storeSignInRecords)
      .where(
        and(
          eq(storeSignInRecords.storeId, dto.storeId),
          eq(storeSignInRecords.memberId, memberId),
          eq(storeSignInRecords.yearMonth, yearMonth),
        ),
      )
      .limit(1);

    if (existing.length > 0) {
      const nextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1);
      throw new ConflictException({
        statusCode: 409,
        message: '您本月已在当前门店完成签到，下月可再次获得奖励',
        nextAvailableDate: nextMonth.toISOString().slice(0, 10),
      });
    }

    // 2. 扣商家 2 元，奖用户 1 元现金 + 50 积分
    const merchantDeduction = '2.00';
    const rewardCash = '1.00';
    const rewardPoints = 50;

    // 插入签到流水记录
    await this.db.insert(storeSignInRecords).values({
      storeId: dto.storeId,
      memberId,
      yearMonth,
      merchantDeductedAmount: merchantDeduction,
      rewardCash,
      rewardPoints,
    });

    // 为用户发放奖励现金到余额与积分
    const memberList = await this.db
      .select()
      .from(members)
      .where(eq(members.id, memberId))
      .limit(1);

    const member = memberList[0];
    if (member) {
      const newBal = (Number(member.balance) + Number(rewardCash)).toFixed(2);
      const newPoints = member.points + rewardPoints;

      await this.db
        .update(members)
        .set({
          balance: newBal,
          points: newPoints,
          updatedAt: new Date(),
        })
        .where(eq(members.id, member.id));
    }

    const nextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1);

    return {
      status: SignInStatus.SUCCESS,
      rewardCash,
      merchantDeductedAmount: merchantDeduction,
      rewardPoints,
      nextAvailableDate: nextMonth.toISOString().slice(0, 10),
      message: '签到成功！已奖励现金 1.00 元与 50 积分',
    };
  }

  /**
   * 逛一逛短视频领积分防刷打点
   */
  async reportVideoProgress(memberId: string, dto: VideoProgressReportDto) {
    if (dto.currentSeconds < 0 || dto.totalDurationSeconds <= 0) {
      throw new BadRequestException('无效的播放进度参数');
    }

    // 防重放校验: 查询 nonce 是否已被打点使用
    const existingNonce = await this.db
      .select({ id: videoProgressRecords.id })
      .from(videoProgressRecords)
      .where(eq(videoProgressRecords.nonce, dto.nonce))
      .limit(1);

    if (existingNonce.length > 0) {
      throw new ConflictException('打点凭证已消费，请勿重复刷取');
    }

    // 播放超过 15 秒且未刷单，每次给予 10 积分奖励
    let pointsAwarded = 0;
    if (dto.currentSeconds >= 15) {
      pointsAwarded = 10;
    }

    await this.db.insert(videoProgressRecords).values({
      videoId: dto.videoId,
      memberId,
      watchedSeconds: Math.floor(dto.currentSeconds),
      pointsAwarded,
      nonce: dto.nonce,
    });

    if (pointsAwarded > 0) {
      const memberList = await this.db
        .select({ points: members.points })
        .from(members)
        .where(eq(members.id, memberId))
        .limit(1);

      if (memberList.length > 0) {
        await this.db
          .update(members)
          .set({
            points: memberList[0].points + pointsAwarded,
            updatedAt: new Date(),
          })
          .where(eq(members.id, memberId));
      }
    }

    return {
      pointsAwarded,
      message: pointsAwarded > 0 ? `恭喜获得 ${pointsAwarded} 积分` : '继续观看视频解锁积分',
    };
  }
}
