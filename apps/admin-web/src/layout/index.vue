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
        <!-- 平台总部视角 (SUPER_ADMIN) -->
        <template v-if="authStore.currentRole === 'SUPER_ADMIN'">
          <el-menu-item index="/dashboard">
            <el-icon><DataBoard /></el-icon>
            <template #title>平台运营大盘</template>
          </el-menu-item>
          <el-menu-item index="/platform/cities">
            <el-icon><Location /></el-icon>
            <template #title>城市分公司管理</template>
          </el-menu-item>
          <el-menu-item index="/platform/shops">
            <el-icon><Goods /></el-icon>
            <template #title>主理人入驻审核</template>
          </el-menu-item>
          <el-menu-item index="/goods">
            <el-icon><Goods /></el-icon>
            <template #title>全网商品监管</template>
          </el-menu-item>
          <el-menu-item index="/trade">
            <el-icon><Tickets /></el-icon>
            <template #title>全网订单与仲裁</template>
          </el-menu-item>
          <el-menu-item index="/members">
            <el-icon><User /></el-icon>
            <template #title>全平台会员档案</template>
          </el-menu-item>
          <el-menu-item index="/settle">
            <el-icon><Wallet /></el-icon>
            <template #title>平台资金总清算</template>
          </el-menu-item>
          <el-sub-menu index="system-menu">
            <template #title>
              <el-icon><Setting /></el-icon>
              <span>系统与权限中控</span>
            </template>
            <el-menu-item index="/system/users">系统账号管理</el-menu-item>
            <el-menu-item index="/system/roles">角色权限矩阵</el-menu-item>
            <el-menu-item index="/system/configs">全局配置与审计</el-menu-item>
          </el-sub-menu>
        </template>

        <!-- 城市分公司视角 (CITY_AGENT) -->
        <template v-else-if="authStore.currentRole === 'CITY_AGENT'">
          <el-menu-item index="/city/screen">
            <el-icon><DataBoard /></el-icon>
            <template #title>城市大屏看板</template>
          </el-menu-item>
          <el-menu-item index="/platform/shops">
            <el-icon><Goods /></el-icon>
            <template #title>辖区主理人商家</template>
          </el-menu-item>
          <el-menu-item index="/stores">
            <el-icon><Location /></el-icon>
            <template #title>辖区实体门店</template>
          </el-menu-item>
          <el-menu-item index="/trade">
            <el-icon><Tickets /></el-icon>
            <template #title>辖区订单监控</template>
          </el-menu-item>
          <el-menu-item index="/settle">
            <el-icon><Wallet /></el-icon>
            <template #title>分公司25%分润账本</template>
          </el-menu-item>
        </template>

        <!-- 供货商视角 (SUPPLIER) -->
        <template v-else-if="authStore.currentRole === 'SUPPLIER'">
          <el-menu-item index="/supply/goods">
            <el-icon><Goods /></el-icon>
            <template #title>供货选品池管理</template>
          </el-menu-item>
          <el-menu-item index="/trade">
            <el-icon><Tickets /></el-icon>
            <template #title>采购订单发货</template>
          </el-menu-item>
          <el-menu-item index="/settle">
            <el-icon><Wallet /></el-icon>
            <template #title>供货款结算对账</template>
          </el-menu-item>
        </template>

        <!-- 默认/品牌主理人视角 (SHOP_OWNER) -->
        <template v-else>
          <el-menu-item index="/dashboard">
            <el-icon><DataBoard /></el-icon>
            <template #title>店铺经营看板</template>
          </el-menu-item>

          <el-sub-menu index="goods-group">
            <template #title>
              <el-icon><Goods /></el-icon>
              <span>商品与分类</span>
            </template>
            <el-menu-item index="/goods">商品与规格矩阵</el-menu-item>
            <el-menu-item index="/goods/categories">商品多级分类</el-menu-item>
            <el-menu-item index="/goods/freight">运费阶梯模板</el-menu-item>
          </el-sub-menu>

          <el-sub-menu index="trade-group">
            <template #title>
              <el-icon><Tickets /></el-icon>
              <span>订单履约</span>
            </template>
            <el-menu-item index="/trade">全渠道订单</el-menu-item>
          </el-sub-menu>

          <el-sub-menu index="stores-group">
            <template #title>
              <el-icon><Location /></el-icon>
              <span>实体门店与收银</span>
            </template>
            <el-menu-item index="/stores">实体门店与活码</el-menu-item>
            <el-menu-item index="/stores/clerks">店员与核销员</el-menu-item>
            <el-menu-item index="/stores/shifts">收银交接班流水</el-menu-item>
          </el-sub-menu>

          <el-menu-item index="/marketing/coupons">
            <el-icon><Tickets /></el-icon>
            <template #title>优惠券中心</template>
          </el-menu-item>

          <el-sub-menu index="member-group">
            <template #title>
              <el-icon><User /></el-icon>
              <span>会员资产</span>
            </template>
            <el-menu-item index="/members">锁客会员档案</el-menu-item>
            <el-menu-item index="/members/ledger">资金与积分台账</el-menu-item>
          </el-sub-menu>

          <el-menu-item index="/settle">
            <el-icon><Wallet /></el-icon>
            <template #title>主理人分润提现</template>
          </el-menu-item>
        </template>
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
          <div class="role-switcher">
            <span style="font-size: 13px; color: #606266; margin-right: 6px">切换工作台:</span>
            <el-select
              v-model="selectedRole"
              size="small"
              style="width: 150px"
              @change="onRoleChange"
            >
              <el-option label="🏢 平台总部总控" value="SUPER_ADMIN" />
              <el-option label="🏙️ 城市分公司端" value="CITY_AGENT" />
              <el-option label="🏪 商家主理人端" value="SHOP_OWNER" />
              <el-option label="🏭 供应链供货商" value="SUPPLIER" />
            </el-select>
          </div>

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
import { ref, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { RoleEnum } from '@city-alliance/contracts';
import { ElMessage } from 'element-plus';
import {
  DataBoard,
  User,
  Location,
  Goods,
  Tickets,
  Wallet,
  Setting,
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

const selectedRole = ref(authStore.currentRole);

watch(
  () => authStore.currentRole,
  (newRole) => {
    selectedRole.value = newRole;
  },
);

function onRoleChange(newRole: RoleEnum) {
  authStore.switchRole(newRole);
  ElMessage.success(`已切换至【${currentRoleName.value}】工作台`);
  if (newRole === RoleEnum.CITY_AGENT) {
    router.push('/city/screen');
  } else if (newRole === RoleEnum.SUPPLIER) {
    router.push('/supply/goods');
  } else if (newRole === RoleEnum.SUPER_ADMIN) {
    router.push('/platform/cities');
  } else {
    router.push('/dashboard');
  }
}

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
