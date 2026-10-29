import { SetMetadata } from '@nestjs/common';
import { RoleEnum } from '@city-alliance/contracts';

export const ROLES_KEY = 'roles';
export const Roles = (...roles: RoleEnum[]) => SetMetadata(ROLES_KEY, roles);
