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
            开发快捷进入:
          </div>
          <el-button size="small" @click="quickEnter('SHOP_OWNER')">商家主理人</el-button>
          <el-button size="small" @click="quickEnter('CITY_AGENT')">城市分公司</el-button>
          <el-button size="small" @click="quickEnter('SUPER_ADMIN')">平台总部</el-button>
        </div>
      </el-form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { RoleEnum, TenantType } from '@city-alliance/contracts';
import { ElMessage } from 'element-plus';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(false);

const loginForm = reactive({
  username: 'admin',
  password: 'password123',
});

function quickEnter(roleStr: string) {
  const role = roleStr as RoleEnum;
  authStore.setAuth('mock_access_token_2026', 'mock_refresh_token_2026', {
    id: 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11',
    username: 'manager_demo',
    nickname: roleStr === 'SHOP_OWNER' ? '李老板 (主理人)' : '城市代理人',
    role,
    tenantType: TenantType.SHOP,
    permissions: ['*'],
  });
  ElMessage.success('登录成功，欢迎进入城盟优选中台！');
  router.push('/dashboard');
}

function handleLogin() {
  loading.value = true;
  setTimeout(() => {
    loading.value = false;
    quickEnter('SHOP_OWNER');
  }, 500);
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
