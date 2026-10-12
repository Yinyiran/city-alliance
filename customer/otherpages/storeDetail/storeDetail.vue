<template>
	<view class="store-detail-page">
		<merchant-home ref="merchantHome" :store-id="id" :is-tab="false"></merchant-home>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				id: ""
			};
		},
		onLoad(options) {
			this.id = options.id || options.store_id || "";
		},
		onReachBottom() {
			if (this.$refs.merchantHome) {
				this.$refs.merchantHome.loadMore();
			}
		},
		async onPullDownRefresh() {
			if (this.$refs.merchantHome) {
				await this.$refs.merchantHome.refresh();
			}
			uni.stopPullDownRefresh();
		},
		onShareAppMessage() {
			let title = (this.$refs.merchantHome && this.$refs.merchantHome.sinfo && this.$refs.merchantHome.sinfo.store_name) 
				? this.$refs.merchantHome.sinfo.store_name 
				: '门店详情';
			return {
				title: title,
				path: '/otherpages/storeDetail/storeDetail?id=' + this.id
			};
		}
	}
</script>

<style lang="scss" scoped>
	.store-detail-page {
		min-height: 100vh;
		background: #F6F7FB;
	}
</style>