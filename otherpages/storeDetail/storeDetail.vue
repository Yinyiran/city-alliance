<template>
	<view class="store-detail-page">
		<store-home ref="storeHome" :store-id="id" :is-tab="false"></store-home>
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
				id: ""
			};
		},
		onLoad(options) {
			this.id = options.id || options.store_id || "";
		},
		onReachBottom() {
			if (this.$refs.storeHome) {
				this.$refs.storeHome.loadMore();
			}
		},
		async onPullDownRefresh() {
			if (this.$refs.storeHome) {
				await this.$refs.storeHome.refresh();
			}
			uni.stopPullDownRefresh();
		},
		onShareAppMessage() {
			let title = (this.$refs.storeHome && this.$refs.storeHome.sinfo && this.$refs.storeHome.sinfo.store_name) 
				? this.$refs.storeHome.sinfo.store_name 
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