import { Module } from '@nestjs/common';
import { JwtModule } from '@nestjs/jwt';
import { AuthService } from './auth.service';
import { AuthController } from './auth.controller';
import { ClerkService } from './clerk.service';
import { ClerkController } from './clerk.controller';
import { UserService } from './user.service';
import { UserController } from './user.controller';
import { RoleService } from './role.service';
import { RoleController } from './role.controller';
import { PlatformService } from './platform.service';
import { PlatformController } from './platform.controller';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../common/guards/roles.guard';
import { PermissionsGuard } from '../../common/guards/permissions.guard';

@Module({
  imports: [
    JwtModule.register({}),
  ],
  controllers: [AuthController, ClerkController, UserController, RoleController, PlatformController],
  providers: [AuthService, ClerkService, UserService, RoleService, PlatformService, JwtAuthGuard, RolesGuard, PermissionsGuard],
  exports: [AuthService, ClerkService, UserService, RoleService, PlatformService, JwtAuthGuard, RolesGuard, PermissionsGuard, JwtModule],
})
export class AuthModule {}
