import { Global, Module } from '@nestjs/common';
import { SettlementQueueService } from './settlement-queue.service';

@Global()
@Module({
  providers: [SettlementQueueService],
  exports: [SettlementQueueService],
})
export class SettlementQueueModule {}
