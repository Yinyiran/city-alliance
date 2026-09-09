<template>
	<view class="page">
		<view class="menu-list">
			<view class="menu-item" @click="navToBanner">
				<view class="menu-left">
					<uv-icon name="photo" color="#333333" size="20"></uv-icon>
					<text class="menu-title">Banner管理</text>
				</view>
				<view class="menu-right">
					<text class="menu-desc">管理门店轮播图</text>
					<uv-icon name="arrow-right" color="#999999" size="14"></uv-icon>
				</view>
			</view>
			<view class="menu-item" @click="navToShopQr">
				<view class="menu-left">
					<uv-icon name="grid" color="#333333" size="20"></uv-icon>
					<text class="menu-title">商家小程序码</text>
				</view>
				<view class="menu-right">
					<text class="menu-desc">扫码绑定商家</text>
					<uv-icon name="arrow-right" color="#999999" size="14"></uv-icon>
				</view>
			</view>
		</view>

		<view class="logout-box">
			<view class="logout-item" @click="logout">
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
			navToBanner() {
				uni.navigateTo({
					url: '/otherpages/bannerManage/bannerManage'
				})
			},
			navToShopQr() {
				uni.navigateTo({
					url: '/otherpages/shopQr/shopQr'
				})
			},
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

	.menu-list {
		background: #FFFFFF;
		border-radius: 24rpx;
		overflow: hidden;
		margin-bottom: 24rpx;

		.menu-item {
			height: 112rpx;
			padding: 0 32rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;
			background: #FFFFFF;
			transition: background-color 0.2s;

			&:active {
				background-color: #F9F9F9;
			}

			.menu-left {
				display: flex;
				align-items: center;

				.menu-title {
					margin-left: 16rpx;
					font-size: 30rpx;
					font-weight: 500;
					color: #222222;
				}
			}

			.menu-right {
				display: flex;
				align-items: center;

				.menu-desc {
					font-size: 26rpx;
					color: #999999;
					margin-right: 8rpx;
				}
			}
		}
	}

	.logout-box {
		background: #FFFFFF;
		border-radius: 24rpx;
		overflow: hidden;

		.logout-item {
			height: 110rpx;
			line-height: 110rpx;
			text-align: center;

			&:active {
				background-color: #F9F9F9;
			}

			.logout-text {
				font-size: 30rpx;
				font-weight: 600;
				color: #F55606;
			}
		}
	}
</style>
