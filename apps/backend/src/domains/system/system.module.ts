import { Module } from '@nestjs/common';
import { SystemController } from './system.controller';
import { SystemService } from './system.service';
import { DrizzleModule } from '../../infrastructure/drizzle/drizzle.module';
import { AuthModule } from '../iam/auth.module';

@Module({
  imports: [DrizzleModule, AuthModule],
  controllers: [SystemController],
  providers: [SystemService],
  exports: [SystemService],
})
export class SystemModule {}
