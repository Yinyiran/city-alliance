import { Injectable, NotFoundException, BadRequestException, Inject } from '@nestjs/common';
import { DRIZZLE_DB, DrizzleDatabase } from '../../infrastructure/drizzle/drizzle.provider';
import { videos, shops, goodsSpu, pointsRecords } from '../../infrastructure/drizzle/schema/index';
import { eq, and, or, like, desc, asc, sql } from 'drizzle-orm';
import { AuthenticatedUser } from '../../common/decorators/current-user.decorator';

export interface CreateVideoDto {
  title: string;
  coverUrl?: string;
  videoUrl: string;
  shopId?: string;
  goodsId?: string;
  goodsName?: string;
  goodsPrice?: number;
  rewardPoints?: number;
  rewardSeconds?: number;
  sortOrder?: number;
}

@Injectable()
export class VideoService {
  constructor(
    @Inject(DRIZZLE_DB)
    private readonly db: DrizzleDatabase,
  ) {}

  /**
   * 分页查询短视频流列表
   */
  async getVideos(query: {
    page?: number;
    pageSize?: number;
    keyword?: string;
    status?: string;
  }) {
    const page = Math.max(1, Number(query.page) || 1);
    const pageSize = Math.min(100, Math.max(1, Number(query.pageSize) || 20));
    const offset = (page - 1) * pageSize;

    const conditions = [];
    if (query.status) {
      conditions.push(eq(videos.status, query.status));
    }
    if (query.keyword?.trim()) {
      conditions.push(like(videos.title, `%${query.keyword.trim()}%`));
    }

    const whereClause = conditions.length > 0 ? and(...conditions) : undefined;

    const rows = await this.db
      .select({
        id: videos.id,
        legacyId: videos.legacyId,
        title: videos.title,
        coverUrl: videos.coverUrl,
        videoUrl: videos.videoUrl,
        shopId: videos.shopId,
        goodsId: videos.goodsId,
        goodsName: videos.goodsName,
        goodsPrice: videos.goodsPrice,
        rewardPoints: videos.rewardPoints,
        rewardSeconds: videos.rewardSeconds,
        viewCount: videos.viewCount,
        likeCount: videos.likeCount,
        shareCount: videos.shareCount,
        status: videos.status,
        sortOrder: videos.sortOrder,
        createdAt: videos.createdAt,
        updatedAt: videos.updatedAt,
      })
      .from(videos)
      .where(whereClause)
      .orderBy(asc(videos.sortOrder), desc(videos.createdAt))
      .limit(pageSize)
      .offset(offset);

    const countRes = await this.db
      .select({ count: sql<number>`count(*)` })
      .from(videos)
      .where(whereClause);

    return {
      items: rows,
      total: Number(countRes[0]?.count || 0),
      page,
      pageSize,
    };
  }

  /**
   * 发布/上传短视频
   */
  async createVideo(dto: CreateVideoDto, user?: AuthenticatedUser) {
    if (!dto.title || !dto.videoUrl) {
      throw new BadRequestException('视频标题和视频播放地址不能为空');
    }

    const inserted = await this.db
      .insert(videos)
      .values({
        title: dto.title,
        coverUrl: dto.coverUrl || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80',
        videoUrl: dto.videoUrl,
        shopId: dto.shopId || user?.shopId || null,
        goodsId: dto.goodsId || null,
        goodsName: dto.goodsName || null,
        goodsPrice: dto.goodsPrice ? String(dto.goodsPrice) : null,
        rewardPoints: String(dto.rewardPoints || 5),
        rewardSeconds: dto.rewardSeconds || 30,
        sortOrder: dto.sortOrder || 0,
        status: 'APPROVED',
      })
      .returning();

    return inserted[0];
  }

  /**
   * 审核短视频
   */
  async auditVideo(id: string, approved: boolean) {
    const list = await this.db
      .select()
      .from(videos)
      .where(eq(videos.id, id))
      .limit(1);

    if (!list[0]) {
      throw new NotFoundException('短视频不存在');
    }

    const nextStatus = approved ? 'APPROVED' : 'REJECTED';
    await this.db
      .update(videos)
      .set({
        status: nextStatus,
        updatedAt: new Date(),
      })
      .where(eq(videos.id, id));

    return {
      id,
      status: nextStatus,
    };
  }

  /**
   * 上架/下架短视频
   */
  async updateStatus(id: string, status: string) {
    const list = await this.db
      .select()
      .from(videos)
      .where(eq(videos.id, id))
      .limit(1);

    if (!list[0]) {
      throw new NotFoundException('短视频不存在');
    }

    await this.db
      .update(videos)
      .set({
        status,
        updatedAt: new Date(),
      })
      .where(eq(videos.id, id));

    return {
      id,
      status,
    };
  }

  /**
   * 30 秒看播防刷打点奖励打卡时钟
   */
  async reportWatchProgress(
    videoId: string,
    watchedSeconds: number,
    memberId?: string,
  ) {
    const list = await this.db
      .select()
      .from(videos)
      .where(eq(videos.id, videoId))
      .limit(1);

    const video = list[0];
    if (!video) {
      throw new NotFoundException('视频不存在');
    }

    // 播放量累加
    await this.db
      .update(videos)
      .set({
        viewCount: sql`${videos.viewCount} + 1`,
      })
      .where(eq(videos.id, videoId));

    const threshold = video.rewardSeconds || 30;
    const isRewarded = watchedSeconds >= threshold;

    let pointsAwarded = 0;
    if (isRewarded && memberId) {
      pointsAwarded = Number(video.rewardPoints);
      try {
        await this.db.insert(pointsRecords).values({
          memberId,
          points: Math.round(pointsAwarded),
          pointsBefore: 0,
          pointsAfter: Math.round(pointsAwarded),
          changeType: 'VIDEO_WATCH_REWARD',
          remark: `观看短视频《${video.title}》满 ${threshold} 秒防刷奖励`,
        });
      } catch (err) {
        // ignore duplicate
      }
    }

    return {
      videoId,
      watchedSeconds,
      threshold,
      rewarded: isRewarded,
      pointsAwarded,
    };
  }
}
