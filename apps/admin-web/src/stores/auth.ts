import { defineStore } from 'pinia';
import { RoleEnum, TenantType } from '@city-alliance/contracts';
import axios from 'axios';

export interface UserState {
  id: string;
  username: string;
  nickname: string;
  avatarUrl?: string | null;
  mobile?: string | null;
  role: RoleEnum;
  tenantType: TenantType;
  tenantId?: string | null;
  shopId?: string | null;
  storeId?: string | null;
  cityId?: string | null;
  permissions: string[];
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    // 工业级安全标准：短效 AccessToken 仅驻留在前端内存中 (Pinia State)，彻底免疫 XSS 磁盘窃取
    token: '' as string,
    // 长效 RefreshToken 托管于后端 HttpOnly SameSite=Lax Cookie，前端脚本不触碰、不落盘
    user: JSON.parse(sessionStorage.getItem('ca_user_info') || 'null') as UserState | null,
    permissions: JSON.parse(sessionStorage.getItem('ca_user_perms') || '[]') as string[],
    isInitialized: false,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    currentRole: (state) => state.user?.role || RoleEnum.SHOP_OWNER,
  },
  actions: {
    /**
     * 登录成功后设置鉴权状态 (仅内存与当前会话)
     */
    setAuth(accessToken: string, user: UserState) {
      this.token = accessToken;
      this.user = user;
      this.permissions = user.permissions || [];
      this.isInitialized = true;

      // 仅存储脱敏的用户展示数据在会话级 sessionStorage (浏览器标签页关闭自动销毁)
      sessionStorage.setItem('ca_user_info', JSON.stringify(user));
      sessionStorage.setItem('ca_user_perms', JSON.stringify(this.permissions));

      // 主动清理历史遗留的旧版 localStorage 敏感明文
      localStorage.removeItem('access_token');
      localStorage.removeItem('refresh_token');
      localStorage.removeItem('user_info');
      localStorage.removeItem('user_perms');
    },

    setAccessToken(accessToken: string) {
      this.token = accessToken;
    },

    /**
     * 应用启动 / 页面 F5 刷新时的静默会话恢复 (通过 HttpOnly Cookie 自动换发)
     */
    async initAuth(): Promise<boolean> {
      try {
        const res = await axios.post(
          '/api/v1/auth/refresh',
          {},
          {
            withCredentials: true,
            headers: { 'Content-Type': 'application/json' },
          },
        );
        const data = res.data?.data || res.data;
        if (data && data.accessToken) {
          this.setAuth(data.accessToken, data.user);
          return true;
        }
        this.clearAuth();
        return false;
      } catch {
        this.clearAuth();
        return false;
      }
    },

    switchRole(role: RoleEnum) {
      if (this.user) {
        this.user.role = role;
        sessionStorage.setItem('ca_user_info', JSON.stringify(this.user));
      }
    },

    clearAuth() {
      this.token = '';
      this.user = null;
      this.permissions = [];
      this.isInitialized = true;
      sessionStorage.removeItem('ca_user_info');
      sessionStorage.removeItem('ca_user_perms');
      localStorage.removeItem('access_token');
      localStorage.removeItem('refresh_token');
      localStorage.removeItem('user_info');
      localStorage.removeItem('user_perms');
    },

    /**
     * 退出登录：通知后端吊销 Refresh Token 并注销 Cookie，同时清空前端内存状态
     */
    async logout() {
      try {
        await axios.post(
          '/api/v1/auth/logout',
          {},
          {
            withCredentials: true,
          },
        );
      } catch {
        // 忽略服务端网络登出异常，确保前端状态彻底销毁
      } finally {
        this.clearAuth();
      }
    },
  },
});
