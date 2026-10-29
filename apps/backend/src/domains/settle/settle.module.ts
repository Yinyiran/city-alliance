import { Module } from '@nestjs/common';
import { SettleService } from './settle.service';
import { SettleController } from './settle.controller';
import { AuthModule } from '../iam/auth.module';

@Module({
  imports: [AuthModule],
  controllers: [SettleController],
  providers: [SettleService],
  exports: [SettleService],
})
export class SettleModule {}
