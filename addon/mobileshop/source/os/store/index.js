import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)

import Http from '../common/js/http.js'

const store = new Vuex.Store({
	state: {
		shop_set: "", //开店套餐
		Development: 1,
		addonIsExit: {
			bundling: 0,
			coupon: 0,
			discount: 0,
			fenxiao: 0,
			gift: 0,
			groupbuy: 0,
			manjian: 0,
			memberconsume: 0,
			memberrecharge: 0,
			memberregister: 0,
			membersignin: 0,
			memberwithdraw: 0,
			pintuan: 0,
			pointexchange: 0,
			seckill: 0,
			store: 0,
			topic: 0,
			bargain: 0,
			membercancel: 0,
			servicer: 0
		},
		bottomNavHeight: 0,
		sourceMember: 0, // 来源会员
		authInfo: {}, // 授权信息
		paySource: '',
		token: null
	},
	mutations: {
		getShopSet(state, shop_set) {
			state.shop_set = shop_set
		},
		setAddonIsexit(state, addonIsExit) {
			state.addonIsExit = Object.assign(state.addonIsExit, addonIsExit);
		},
		setToken(state, value) {
			state.token = value;
		},
		setAuthinfo(state, value) {
			state.authInfo = value;
		},
		setSourceMember(state, value) {
			state.sourceMember = value;
		},
		setPaySource(state, value) {
			state.paySource = value;
		},
		setBottomNavHeight(state, value) {
			state.bottomNavHeight = value;
		}

	},
	actions: {
		// 获取插件是否安装
		async getAddonIsexit() {
			if (uni.getStorageSync('memberAddonIsExit')) {
				this.commit('setAddonIsexit', uni.getStorageSync('memberAddonIsExit'))
			}
			var res = await Http.sendRequest({
				url: '/api/addon/addonisexit',
				async:false
			})
			let data = res;
			if (res.code == 0) {
				uni.setStorageSync('memberAddonIsExit', res.data);
				this.commit('setAddonIsexit', res.data)
			}
			
		},
	}
})

export default store
