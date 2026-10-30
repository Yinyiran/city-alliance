import { Module } from '@nestjs/common';
import { JwtModule } from '@nestjs/jwt';
import { AuthService } from './auth.service';
import { AuthController } from './auth.controller';
import { ClerkService } from './clerk.service';
import { ClerkController } from './clerk.controller';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../common/guards/roles.guard';
import { PermissionsGuard } from '../../common/guards/permissions.guard';

@Module({
  imports: [
    JwtModule.register({}),
  ],
  controllers: [AuthController, ClerkController],
  providers: [AuthService, ClerkService, JwtAuthGuard, RolesGuard, PermissionsGuard],
  exports: [AuthService, ClerkService, JwtAuthGuard, RolesGuard, PermissionsGuard, JwtModule],
})
export class AuthModule {}
