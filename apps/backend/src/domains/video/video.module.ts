import { Module } from '@nestjs/common';
import { VideoController } from './video.controller';
import { VideoService } from './video.service';
import { DrizzleModule } from '../../infrastructure/drizzle/drizzle.module';
import { AuthModule } from '../iam/auth.module';

@Module({
  imports: [DrizzleModule, AuthModule],
  controllers: [VideoController],
  providers: [VideoService],
  exports: [VideoService],
})
export class VideoModule {}
