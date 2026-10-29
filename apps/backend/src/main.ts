import { NestFactory } from '@nestjs/core';
import {
  FastifyAdapter,
  NestFastifyApplication,
} from '@nestjs/platform-fastify';
import { AppModule } from './app.module';
import { Logger } from '@nestjs/common';
import helmet from '@fastify/helmet';
import cors from '@fastify/cors';
import cookie from '@fastify/cookie';
import { HttpExceptionFilter } from './common/filters/http-exception.filter';

async function bootstrap() {
  const logger = new Logger('Bootstrap');

  const app = await NestFactory.create<NestFastifyApplication>(
    AppModule,
    new FastifyAdapter({
      logger: true,
      trustProxy: true,
    }),
  );

  // 安全与跨域插件
  await app.register(helmet, {
    contentSecurityPolicy: false,
  });

  await app.register(cors, {
    origin: true,
    credentials: true,
  });

  await app.register(cookie, {
    secret: process.env.COOKIE_SECRET || 'city_alliance_cookie_secret_2026',
  });

  // 全局 API 前缀
  app.setGlobalPrefix(process.env.API_PREFIX || '/api/v1');

  // 全局异常过滤器
  app.useGlobalFilters(new HttpExceptionFilter());

  const port = Number(process.env.PORT) || 4000;
  await app.listen(port, '0.0.0.0');

  logger.log(`=======================================================`);
  logger.log(`🚀 City Alliance NestJS 12 后端中枢已就绪`);
  logger.log(`📡 服务监听: http://localhost:${port}${process.env.API_PREFIX || '/api/v1'}`);
  logger.log(`⚡ 架构核心: Fastify 5 + Drizzle ORM + PostgreSQL 17`);
  logger.log(`=======================================================`);
}

bootstrap();
