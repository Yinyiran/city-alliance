import { Module } from '@nestjs/common';
import { SettleService } from './settle.service';
import { SettleController } from './settle.controller';
import { AuthModule } from '../iam/auth.module';
import { DrizzleModule } from '../../infrastructure/drizzle/drizzle.module';

@Module({
  imports: [AuthModule, DrizzleModule],
  controllers: [SettleController],
  providers: [SettleService],
  exports: [SettleService],
})
export class SettleModule {}
