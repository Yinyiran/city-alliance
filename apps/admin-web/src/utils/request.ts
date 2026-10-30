import axios, { AxiosInstance, AxiosResponse, InternalAxiosRequestConfig } from 'axios';
import { ElMessage } from 'element-plus';
import { useAuthStore } from '../stores/auth';

interface CustomAxiosRequestConfig extends InternalAxiosRequestConfig {
  _retry?: boolean;
}

const service: AxiosInstance = axios.create({
  baseURL: '/api/v1',
  timeout: 15000,
  withCredentials: true, // 核心：跨请求自动共享 HttpOnly Cookie 凭证
  headers: {
    'Content-Type': 'application/json',
  },
});

// 并发 401 无感刷新控制队列与互斥锁
let isRefreshing = false;
let failedQueue: Array<{
  resolve: (token: string) => void;
  reject: (err: any) => void;
}> = [];

function processQueue(error: any, token: string | null = null) {
  failedQueue.forEach((prom) => {
    if (error) {
      prom.reject(error);
    } else if (token) {
      prom.resolve(token);
    }
  });
  failedQueue = [];
}

// 请求拦截器
service.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore();
    // 动态注入内存中的短周期 AccessToken
    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  },
);

// 响应拦截器
service.interceptors.response.use(
  (response: AxiosResponse) => {
    const res = response.data;
    if (res && typeof res.code === 'number' && res.code !== 200 && res.code !== 201) {
      ElMessage.error(res.message || '系统处理异常');
      return Promise.reject(new Error(res.message || 'Error'));
    }
    return res;
  },
  async (error) => {
    const originalRequest = error.config as CustomAxiosRequestConfig;
    const authStore = useAuthStore();

    // 遇到 401 Unauthorized，启动无感自动续期与请求队列重放
    if (error.response?.status === 401 && originalRequest && !originalRequest._retry) {
      const requestUrl = originalRequest.url || '';

      // 登录接口本身或静默刷新接口本身失败，直接终止并退回登录，避免无限递归死循环
      if (requestUrl.includes('/auth/login') || requestUrl.includes('/auth/refresh')) {
        const message = error.response?.data?.message || '身份凭据校验失败，请重新登录';
        ElMessage.error(message);
        authStore.clearAuth();
        return Promise.reject(error);
      }

      // 如果当前已有正在进行的刷新任务，将后续并发请求放入 Promise 挂起队列
      if (isRefreshing) {
        return new Promise((resolve, reject) => {
          failedQueue.push({
            resolve: (token: string) => {
              originalRequest.headers.Authorization = `Bearer ${token}`;
              resolve(service(originalRequest));
            },
            reject: (err: any) => {
              reject(err);
            },
          });
        });
      }

      originalRequest._retry = true;
      isRefreshing = true;

      try {
        // 调用静默刷新：由浏览器底层 Cookie 自动携带 HttpOnly refresh_token 换发新令牌
        const refreshSuccess = await authStore.initAuth();
        if (refreshSuccess && authStore.token) {
          processQueue(null, authStore.token);
          originalRequest.headers.Authorization = `Bearer ${authStore.token}`;
          return service(originalRequest);
        } else {
          processQueue(new Error('登录会话已超时，请重新登录'), null);
          authStore.clearAuth();
          if (window.location.pathname !== '/login') {
            window.location.href = '/login';
          }
          return Promise.reject(error);
        }
      } catch (refreshErr) {
        processQueue(refreshErr, null);
        authStore.clearAuth();
        if (window.location.pathname !== '/login') {
          window.location.href = '/login';
        }
        return Promise.reject(refreshErr);
      } finally {
        isRefreshing = false;
      }
    }

    const message = error.response?.data?.message || error.message || '网络通信故障';
    ElMessage.error(message);
    return Promise.reject(error);
  },
);

export default service;
