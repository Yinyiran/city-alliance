<template>
  <el-container class="layout-container">
    <el-aside :width="isCollapse ? '64px' : '230px'" class="layout-aside">
      <div class="logo-box">
        <span class="logo-icon">🏢</span>
        <span v-if="!isCollapse" class="logo-title">城盟优选中台</span>
      </div>

      <el-menu
        :default-active="activeRoute"
        :collapse="isCollapse"
        :router="true"
        class="layout-menu"
        background-color="#1e222d"
        text-color="#c1c6d2"
        active-text-color="#409eff"
      >
        <el-menu-item index="/dashboard">
          <el-icon><DataBoard /></el-icon>
          <template #title>经营数据看板</template>
        </el-menu-item>

        <el-menu-item index="/members">
          <el-icon><User /></el-icon>
          <template #title>会员与分润档案</template>
        </el-menu-item>

        <el-menu-item index="/stores">
          <el-icon><Location /></el-icon>
          <template #title>同城门店与活码</template>
        </el-menu-item>

        <el-menu-item index="/goods">
          <el-icon><Goods /></el-icon>
          <template #title>商品与规格矩阵</template>
        </el-menu-item>

        <el-menu-item index="/trade">
          <el-icon><Tickets /></el-icon>
          <template #title>全渠道交易履约</template>
        </el-menu-item>

        <el-menu-item index="/settle">
          <el-icon><Wallet /></el-icon>
          <template #title>财务清算与个税</template>
        </el-menu-item>
      </el-menu>
    </el-aside>

    <el-container>
      <el-header class="layout-header">
        <div class="header-left">
          <el-button
            link
            class="collapse-btn"
            @click="isCollapse = !isCollapse"
          >
            <el-icon :size="20">
              <Fold v-if="!isCollapse" />
              <Expand v-else />
            </el-icon>
          </el-button>
          <el-breadcrumb separator="/">
            <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
            <el-breadcrumb-item>{{ currentRouteTitle }}</el-breadcrumb-item>
          </el-breadcrumb>
        </div>

        <div class="header-right">
          <el-tag effect="light" type="primary" class="role-badge">
            {{ currentRoleName }}
          </el-tag>
          <el-dropdown trigger="click" @command="handleCommand">
            <span class="user-dropdown">
              <el-avatar :size="32" icon="UserFilled" />
              <span class="nickname">{{ authStore.user?.nickname || '主理人' }}</span>
              <el-icon><ArrowDown /></el-icon>
            </span>
            <template #dropdown>
              <el-dropdown-menu>
                <el-dropdown-item command="profile">账号设置</el-dropdown-item>
                <el-dropdown-item divided command="logout">退出登录</el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </div>
      </el-header>

      <el-main class="layout-main">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </el-main>
    </el-container>
  </el-container>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import {
  DataBoard,
  User,
  Location,
  Goods,
  Tickets,
  Wallet,
  Fold,
  Expand,
  ArrowDown,
} from '@element-plus/icons-vue';

const isCollapse = ref(false);
const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const activeRoute = computed(() => route.path);
const currentRouteTitle = computed(() => route.meta.title || '工作台');

const currentRoleName = computed(() => {
  const map: Record<string, string> = {
    SUPER_ADMIN: '平台总部',
    CITY_AGENT: '城市分公司',
    SHOP_OWNER: '品牌主理人',
    STORE_MANAGER: '实体门店',
    SUPPLIER: 'B2B供货商',
  };
  return map[authStore.currentRole] || '商家主理人';
});

function handleCommand(command: string) {
  if (command === 'logout') {
    authStore.logout();
    router.push('/login');
  }
}
</script>

<style scoped>
.layout-container {
  height: 100vh;
  width: 100vw;
  overflow: hidden;
}

.layout-aside {
  background-color: #1e222d;
  transition: width 0.3s;
  display: flex;
  flex-direction: column;
}

.logo-box {
  height: 60px;
  display: flex;
  align-items: center;
  padding: 0 16px;
  background-color: #171a22;
  color: #fff;
}

.logo-icon {
  font-size: 24px;
  margin-right: 12px;
}

.logo-title {
  font-size: 16px;
  font-weight: 600;
  white-space: nowrap;
}

.layout-menu {
  border-right: none;
  flex: 1;
}

.layout-header {
  height: 60px;
  background-color: #fff;
  border-bottom: 1px solid #ebeef5;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.user-dropdown {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.layout-main {
  background-color: #f5f7fa;
  padding: 20px;
  overflow-y: auto;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
