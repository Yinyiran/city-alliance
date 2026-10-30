<template>
  <div class="login-wrapper">
    <div class="login-box">
      <div class="login-header">
        <div class="brand-title">城盟优选 (City Alliance)</div>
        <div class="brand-sub">全栈现代化统一中台 (Vue 3.5 + Element Plus)</div>
      </div>

      <el-form :model="loginForm" label-position="top" class="login-form">
        <el-form-item label="登录账号">
          <el-input
            v-model="loginForm.username"
            placeholder="请输入管理员 / 主理人账号"
            size="large"
          />
        </el-form-item>

        <el-form-item label="密码">
          <el-input
            v-model="loginForm.password"
            type="password"
            placeholder="请输入密码"
            size="large"
            show-password
          />
        </el-form-item>

        <el-button
          type="primary"
          size="large"
          class="login-btn"
          :loading="loading"
          @click="handleLogin"
        >
          立即登录进入中台
        </el-button>

        <div class="quick-demo-box">
          <div style="font-size: 12px; color: #909399; margin-bottom: 8px">
            测试账号快捷填充 (生产/测试数据库真实账号):
          </div>
          <el-button size="small" @click="fillAccount('admin', '123456')">平台总部(admin)</el-button>
          <el-button size="small" @click="fillAccount('city2', '123456')">城市代理(city2)</el-button>
          <el-button size="small" @click="fillAccount('shop1', '123456')">商家主理(shop1)</el-button>
        </div>
      </el-form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import request from '../../utils/request';
import { ElMessage } from 'element-plus';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(false);

const loginForm = reactive({
  username: 'admin',
  password: '',
});

onMounted(() => {
  // 清理可能遗留的旧版开发 mock token
  if (localStorage.getItem('access_token')?.startsWith('mock_')) {
    authStore.logout();
  }
});

function fillAccount(username: string, pass: string = '123456') {
  loginForm.username = username;
  loginForm.password = pass;
}

async function handleLogin() {
  if (!loginForm.username.trim() || !loginForm.password.trim()) {
    ElMessage.warning('请输入登录账号与密码');
    return;
  }

  loading.value = true;
  try {
    const res: any = await request.post('/auth/login', {
      username: loginForm.username.trim(),
      password: loginForm.password.trim(),
      terminal: 'ADMIN_WEB',
    });

    const tokenData = res.data || res;
    if (!tokenData || !tokenData.accessToken) {
      throw new Error('未获取到鉴权令牌，请检查后端服务状态');
    }

    authStore.setAuth(tokenData.accessToken, tokenData.refreshToken, tokenData.user);
    ElMessage.success(`欢迎【${tokenData.user.nickname || tokenData.user.username}】登录城盟优选中台！`);

    // 根据角色重定向到对应控制台视角
    if (tokenData.user.role === 'CITY_AGENT') {
      router.push('/city/screen');
    } else if (tokenData.user.role === 'SUPPLIER') {
      router.push('/supply/goods');
    } else {
      router.push('/dashboard');
    }
  } catch (err: any) {
    // 错误信息在 request.ts 的响应拦截器中已弹窗提示
    console.error('Login error:', err);
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.login-wrapper {
  height: 100vh;
  width: 100vw;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #1e222d 0%, #2c3446 100%);
}

.login-box {
  width: 420px;
  background: #fff;
  border-radius: 12px;
  padding: 36px 32px;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.25);
}

.login-header {
  text-align: center;
  margin-bottom: 28px;
}

.brand-title {
  font-size: 22px;
  font-weight: 700;
  color: #1e222d;
}

.brand-sub {
  font-size: 13px;
  color: #909399;
  margin-top: 6px;
}

.login-btn {
  width: 100%;
  margin-top: 8px;
  font-weight: 600;
}

.quick-demo-box {
  margin-top: 24px;
  padding-top: 16px;
  border-top: 1px dashed #ebeef5;
  text-align: center;
}
</style>
