import { Module } from '@nestjs/common';
import { CityController } from './city.controller';
import { CityService } from './city.service';
import { DrizzleModule } from '../../infrastructure/drizzle/drizzle.module';
import { AuthModule } from '../iam/auth.module';

@Module({
  imports: [DrizzleModule, AuthModule],
  controllers: [CityController],
  providers: [CityService],
  exports: [CityService],
})
export class CityModule {}
