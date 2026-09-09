<template>
	<view class="store-page-root">
		<store-home ref="storeHome" :store-id="storeId" :is-tab="true"></store-home>
	</view>
</template>

<script>
	import storeHome from "@/components/store-home/store-home.vue";

	export default {
		components: {
			storeHome
		},
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
			// 如果当前没有显式 storeId，可触发一次检查，保证切换城市/门店后数据同步
			if (this.$refs.storeHome) {
				let cachedId = uni.getStorageSync('store_id') || '';
				if (!this.$refs.storeHome.currentStoreId) {
					this.$refs.storeHome.initStore();
				} else if (cachedId && cachedId !== this.$refs.storeHome.currentStoreId) {
					this.$refs.storeHome.initByStoreId(cachedId);
				}
			}
		},
		// 触底加载下一页商品
		onReachBottom() {
			if (this.$refs.storeHome) {
				this.$refs.storeHome.loadMore();
			}
		},
		// 下拉刷新
		async onPullDownRefresh() {
			if (this.$refs.storeHome) {
				await this.$refs.storeHome.refresh();
			}
			uni.stopPullDownRefresh();
		},
		// 分享
		onShareAppMessage() {
			let title = (this.$refs.storeHome && this.$refs.storeHome.sinfo && this.$refs.storeHome.sinfo.store_name) 
				? this.$refs.storeHome.sinfo.store_name 
				: '优选商家';
			let storeId = this.$refs.storeHome ? this.$refs.storeHome.currentStoreId : '';
			return {
				title: title,
				path: '/pages/store/index?id=' + storeId
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
