import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { DrizzleModule } from './infrastructure/drizzle/drizzle.module';
import { RedisModule } from './infrastructure/redis/redis.module';
import { AuthModule } from './domains/iam/auth.module';
import { MemberModule } from './domains/member/member.module';
import { SettleModule } from './domains/settle/settle.module';
import { StoreModule } from './domains/store/store.module';
import { CatalogModule } from './domains/catalog/catalog.module';
import { TradeModule } from './domains/trade/trade.module';

@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: ['.env', '.env.example'],
    }),
    DrizzleModule,
    RedisModule,
    AuthModule,
    MemberModule,
    SettleModule,
    StoreModule,
    CatalogModule,
    TradeModule,
  ],
})
export class AppModule {}
