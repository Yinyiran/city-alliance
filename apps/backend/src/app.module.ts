import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { DrizzleModule } from './infrastructure/drizzle/drizzle.module';
import { AuthModule } from './domains/iam/auth.module';
import { MemberModule } from './domains/member/member.module';
import { SettleModule } from './domains/settle/settle.module';

@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: ['.env', '.env.example'],
    }),
    DrizzleModule,
    AuthModule,
    MemberModule,
    SettleModule,
  ],
})
export class AppModule {}
