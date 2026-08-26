import { defineStore } from 'pinia';
import { server } from '@/common/js/http.js';

// 底部 tabBar 中购物车/福利区所在的下标（pages.json -> tabBar.list）
const CART_TABBAR_INDEX = 2;

export const useAppStore = defineStore('app', {
	state: () => ({
		token: uni.getStorageSync('token') || '',
		themeStyle: '',
		addonIsExit: {},
		showToastValue: '',
		diySeckillInterval: ''
	}),
	actions: {
		// 设置 token
		setToken(token) {
			this.token = token;
		},
		// 刷新购物车角标
		async getCartNumber() {
			try {
				const res = await server('/api/cart/goodslists', {});
				if (res.code >= 0 && Array.isArray(res.data)) {
					let total = 0;
					res.data.forEach(item => {
						total += Number(item.num) || 0;
					});
					if (total > 0) {
						uni.setTabBarBadge({
							index: CART_TABBAR_INDEX,
							text: total > 99 ? '99+' : String(total)
						});
					} else {
						uni.removeTabBarBadge({
							index: CART_TABBAR_INDEX,
							fail: () => {}
						});
					}
				}
			} catch (e) {
				// 请求失败静默处理
			}
		},
		// 刷新批发进货单角标（批发暂无独立数量接口，复用购物车角标逻辑，失败静默）
		async getWholeSaleNumber() {
			return this.getCartNumber();
		}
	}
});
