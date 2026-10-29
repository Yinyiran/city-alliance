import { Provider } from '@nestjs/common';
import Redis from 'ioredis';

export const REDIS_CLIENT = 'REDIS_CLIENT';

export const redisProvider: Provider = {
  provide: REDIS_CLIENT,
  useFactory: () => {
    const host = process.env.REDIS_HOST || '127.0.0.1';
    const port = Number(process.env.REDIS_PORT) || 6379;
    const password = process.env.REDIS_PASSWORD || 'city_redis_secret_2026';
    const db = Number(process.env.REDIS_DB) || 0;

    const redis = new Redis({
      host,
      port,
      password,
      db,
      lazyConnect: true,
      maxRetriesPerRequest: 3,
      enableOfflineQueue: false,
    });

    redis.on('error', (err) => {
      // 捕获连接异常，避免无本地 Redis 实例时阻塞应用启动
      console.warn(`[Redis Notice] Redis connection warning: ${err.message}`);
    });

    return redis;
  },
};
