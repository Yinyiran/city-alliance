<template>
	<view class="page">
		<view class="list">
			<view class="item" @click="logout">
				<text class="logout-text">退出登录</text>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {}
		},
		methods: {
			logout() {
				uni.showModal({
					title: '提示',
					content: '确定要退出登录吗？',
					confirmText: '退出',
					confirmColor: '#F55606',
					success: (res) => {
						if (res.confirm) {
							uni.removeStorageSync('token')
							uni.removeStorageSync('site_id')
							uni.removeStorageSync('userInfo')
							uni.showToast({
								title: '已退出登录',
								icon: 'none'
							})
							setTimeout(() => {
								uni.switchTab({
									url: '/pages/my/my'
								})
							}, 500)
						}
					}
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.page {
		min-height: 100vh;
		background: #F5F5F5;
		padding: 24rpx 32rpx;
		box-sizing: border-box;
	}

	.list {
		background: #FFFFFF;
		border-radius: 24rpx;
		overflow: hidden;

		.item {
			height: 110rpx;
			line-height: 110rpx;
			text-align: center;

			.logout-text {
				font-size: 30rpx;
				font-weight: 600;
				color: #F55606;
			}
		}
	}
</style>
