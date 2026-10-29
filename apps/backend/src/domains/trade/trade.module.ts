import { Module } from '@nestjs/common';
import { TradeService } from './trade.service';
import { TradeController } from './trade.controller';
import { SettleModule } from '../settle/settle.module';
import { AuthModule } from '../iam/auth.module';

@Module({
  imports: [AuthModule, SettleModule],
  controllers: [TradeController],
  providers: [TradeService],
  exports: [TradeService],
})
export class TradeModule {}
