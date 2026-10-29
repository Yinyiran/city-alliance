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
        path: 'members',
        name: 'Members',
        component: () => import('../views/members/index.vue'),
        meta: { title: '会员与分润档案' },
      },
      {
        path: 'stores',
        name: 'Stores',
        component: () => import('../views/stores/index.vue'),
        meta: { title: '同城门店与活码' },
      },
      {
        path: 'goods',
        name: 'Goods',
        component: () => import('../views/goods/index.vue'),
        meta: { title: '商品与规格矩阵' },
      },
      {
        path: 'trade',
        name: 'Trade',
        component: () => import('../views/trade/index.vue'),
        meta: { title: '全渠道交易履约' },
      },
      {
        path: 'settle',
        name: 'Settle',
        component: () => import('../views/settle/index.vue'),
        meta: { title: '财务清算与个税' },
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

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  if (to.meta.public) {
    return next();
  }
  if (!authStore.isAuthenticated) {
    return next('/login');
  }
  next();
});

export default router;
