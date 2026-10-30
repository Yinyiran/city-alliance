import { Module } from '@nestjs/common';
import { DashboardController } from './dashboard.controller';
import { DashboardService } from './dashboard.service';
import { DrizzleModule } from '../../infrastructure/drizzle/drizzle.module';
import { AuthModule } from '../iam/auth.module';

@Module({
  imports: [DrizzleModule, AuthModule],
  controllers: [DashboardController],
  providers: [DashboardService],
  exports: [DashboardService],
})
export class DashboardModule {}
