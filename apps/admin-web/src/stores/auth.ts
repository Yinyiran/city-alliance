import { defineStore } from 'pinia';
import { RoleEnum, TenantType } from '@city-alliance/contracts';

export interface UserState {
  id: string;
  username: string;
  nickname: string;
  role: RoleEnum;
  tenantType: TenantType;
  tenantId?: string | null;
  permissions: string[];
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('access_token') || '',
    refreshToken: localStorage.getItem('refresh_token') || '',
    user: JSON.parse(localStorage.getItem('user_info') || 'null') as UserState | null,
    permissions: JSON.parse(localStorage.getItem('user_perms') || '[]') as string[],
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    currentRole: (state) => state.user?.role || RoleEnum.SHOP_OWNER,
  },
  actions: {
    setAuth(token: string, refreshToken: string, user: UserState) {
      this.token = token;
      this.refreshToken = refreshToken;
      this.user = user;
      this.permissions = user.permissions || [];

      localStorage.setItem('access_token', token);
      localStorage.setItem('refresh_token', refreshToken);
      localStorage.setItem('user_info', JSON.stringify(user));
      localStorage.setItem('user_perms', JSON.stringify(this.permissions));
    },
    switchRole(role: RoleEnum) {
      if (this.user) {
        this.user.role = role;
        localStorage.setItem('user_info', JSON.stringify(this.user));
      }
    },
    logout() {
      this.token = '';
      this.refreshToken = '';
      this.user = null;
      this.permissions = [];

      localStorage.removeItem('access_token');
      localStorage.removeItem('refresh_token');
      localStorage.removeItem('user_info');
      localStorage.removeItem('user_perms');
    },
  },
});
