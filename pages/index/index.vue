<template>
	<view class="store-page-root">
		<merchant-home ref="merchantHome" :store-id="storeId" :is-tab="true"></merchant-home>
	</view>
</template>

<script>
export default {
	data() {
		return {
			storeId: ""
		};
	},
	onLoad(options) {
		if (options) {
			if (options.id || options.store_id) {
				this.storeId = options.id || options.store_id;
			}
			if (options.site_id) {
				uni.setStorageSync('share_site_id', options.site_id);
				if (!this.storeId) this.storeId = options.site_id;
			}
			if (options.suid) {
				uni.setStorageSync('invite_code', options.suid);
			}
			if (options.scene) {
				try {
					const sceneStr = decodeURIComponent(options.scene);
					const pairs = sceneStr.split('&');
					pairs.forEach(item => {
						const parts = item.includes('-') ? item.split('-') : item.split('=');
						const k = parts[0];
						const v = parts[1];
						if (k === 'site_id' || k === 's') {
							uni.setStorageSync('share_site_id', v);
							if (!this.storeId) this.storeId = v;
						}
						if (k === 'suid' || k === 'invite_code') {
							uni.setStorageSync('invite_code', v);
						}
					});
				} catch (e) {
					console.error('parse scene error', e);
				}
			}
		}
	},
	onShow() {
		// 页面显示时检查登录状态、商家归属和门店缓存，必要时自动重载
		if (this.$refs.merchantHome) {
			this.$refs.merchantHome.checkStoreState();
		}
	},
	// 触底加载下一页商品
	onReachBottom() {
		if (this.$refs.merchantHome) {
			this.$refs.merchantHome.loadMore();
		}
	},
	// 下拉刷新
	async onPullDownRefresh() {
		if (this.$refs.merchantHome) {
			await this.$refs.merchantHome.refresh();
		}
		uni.stopPullDownRefresh();
	},
	// 分享
	onShareAppMessage() {
		let title = (this.$refs.merchantHome && this.$refs.merchantHome.sinfo && this.$refs.merchantHome.sinfo.store_name)
			? this.$refs.merchantHome.sinfo.store_name
			: '优选商家';
		let storeId = this.$refs.merchantHome ? this.$refs.merchantHome.currentStoreId : '';
		return {
			title: title,
			path: '/pages/index/index?id=' + storeId
		};
	}
}
</script>

<style lang="scss" scoped>
.store-page-root {
	min-height: 100vh;
	background: #F6F7FB;
}
</style>
