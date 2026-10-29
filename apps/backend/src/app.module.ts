import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { DrizzleModule } from './infrastructure/drizzle/drizzle.module';
import { RedisModule } from './infrastructure/redis/redis.module';
import { SettlementQueueModule } from './infrastructure/queue/settlement-queue.module';
import { HardwareModule } from './domains/hardware/hardware.module';
import { AuthModule } from './domains/iam/auth.module';
import { MemberModule } from './domains/member/member.module';
import { SettleModule } from './domains/settle/settle.module';
import { StoreModule } from './domains/store/store.module';
import { CatalogModule } from './domains/catalog/catalog.module';
import { TradeModule } from './domains/trade/trade.module';
import { SigninModule } from './domains/signin/signin.module';
import { SupplyModule } from './domains/supply/supply.module';

@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: ['.env', '.env.example'],
    }),
    DrizzleModule,
    RedisModule,
    SettlementQueueModule,
    HardwareModule,
    AuthModule,
    MemberModule,
    SettleModule,
    StoreModule,
    CatalogModule,
    TradeModule,
    SigninModule,
    SupplyModule,
  ],
})
export class AppModule {}
