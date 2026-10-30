import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/login/index.vue'),
    meta: { title: '账号登录', public: true },
  },
  {
    path: '/',
    component: () => import('../layout/index.vue'),
    redirect: '/dashboard',
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('../views/dashboard/index.vue'),
        meta: { title: '经营数据看板' },
      },
      {
        path: 'platform/cities',
        name: 'PlatformCities',
        component: () => import('../views/platform/cities.vue'),
        meta: { title: '城市分公司管理' },
      },
      {
        path: 'platform/shops',
        name: 'PlatformShops',
        component: () => import('../views/platform/shops.vue'),
        meta: { title: '主理人入驻审核' },
      },
      {
        path: 'city/screen',
        name: 'CityScreen',
        component: () => import('../views/city/screen.vue'),
        meta: { title: '城市分公司大屏' },
      },
      {
        path: 'members',
        name: 'Members',
        component: () => import('../views/members/index.vue'),
        meta: { title: '会员与分润档案' },
      },
      {
        path: 'members/ledger',
        name: 'MemberLedger',
        component: () => import('../views/members/ledger.vue'),
        meta: { title: '资金与积分台账' },
      },
      {
        path: 'stores',
        name: 'Stores',
        component: () => import('../views/stores/index.vue'),
        meta: { title: '同城门店与活码' },
      },
      {
        path: 'stores/clerks',
        name: 'StoreClerks',
        component: () => import('../views/stores/clerks.vue'),
        meta: { title: '门店店员与核销' },
      },
      {
        path: 'stores/shifts',
        name: 'StoreShifts',
        component: () => import('../views/stores/shifts.vue'),
        meta: { title: '收银交接班流水' },
      },
      {
        path: 'goods',
        name: 'Goods',
        component: () => import('../views/goods/index.vue'),
        meta: { title: '商品与规格矩阵' },
      },
      {
        path: 'goods/categories',
        name: 'GoodsCategories',
        component: () => import('../views/goods/categories.vue'),
        meta: { title: '商品多级分类' },
      },
      {
        path: 'goods/freight',
        name: 'GoodsFreight',
        component: () => import('../views/goods/freight.vue'),
        meta: { title: '运费计费模板' },
      },
      {
        path: 'trade',
        name: 'Trade',
        component: () => import('../views/trade/index.vue'),
        meta: { title: '全渠道交易履约' },
      },
      {
        path: 'marketing/coupons',
        name: 'MarketingCoupons',
        component: () => import('../views/marketing/coupons.vue'),
        meta: { title: '优惠券中心' },
      },
      {
        path: 'supply/goods',
        name: 'SupplyGoods',
        component: () => import('../views/supply/goods.vue'),
        meta: { title: '供应链供货选品' },
      },
      {
        path: 'settle',
        name: 'Settle',
        component: () => import('../views/settle/index.vue'),
        meta: { title: '财务清算与个税' },
      },
      {
        path: 'system/users',
        name: 'SystemUsers',
        component: () => import('../views/system/users.vue'),
        meta: { title: '系统账号管理' },
      },
      {
        path: 'system/roles',
        name: 'SystemRoles',
        component: () => import('../views/system/roles.vue'),
        meta: { title: '角色权限矩阵' },
      },
      {
        path: 'system/configs',
        name: 'SystemConfigs',
        component: () => import('../views/system/configs.vue'),
        meta: { title: '系统参数配置' },
      },
    ],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/dashboard',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  if (to.meta.public) {
    if (to.path === '/login' && authStore.isAuthenticated) {
      return next('/dashboard');
    }
    return next();
  }

  // 内存中无短期凭据时 (如用户按 F5 刷新页面)，尝试通过 HttpOnly Cookie 执行无感静默恢复
  if (!authStore.isAuthenticated) {
    const success = await authStore.initAuth();
    if (success) {
      return next();
    }
    return next('/login');
  }

  next();
});

export default router;
