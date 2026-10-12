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
		if (options && (options.id || options.store_id)) {
			this.storeId = options.id || options.store_id;
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
