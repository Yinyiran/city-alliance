<template>
	<view class="successfully">
		<text class="successfully-title">开店成功</text>
		<image :src="$util.img('upload/uniapp/shop_uniapp/login/apply_succeed.png')" mode="" class="successfully-img"></image>
		<text class="successfully-text">恭喜您开店成功，快点击进入店铺，进行体验吧！</text>
		<view class="successfully-btn" @click="goIndex">进入店铺</view>
	</view>
</template>

<script>
export default {
	data() {
		return {};
	},
	methods: {
		goIndex() {
			util.redirectTo('/pages/index/index')
		}
	}
};
</script>

<style lang="scss" scoped>
page {
	background-color: #fff;
	overflow: hidden;
}
.successfully {
	width: 100%;
	display: flex;
	flex-direction: column;
	align-items: center;
	margin-top: 100rpx;
	.successfully-title {
		font-size: 50rpx;
		font-weight: bold;
	}
	.successfully-img {
		width: 200rpx;
		height: 200rpx;
		margin-top: 200rpx;
	}
	.successfully-text {
		font-size: 28rpx;
		margin-top: 60rpx;
	}
	.successfully-btn {
		width: 200rpx;
		height: 60rpx;
		color: #fff;
		text-align: center;
		line-height: 60rpx;
		border-radius: 6rpx;
		background-color: #ff8143;
		margin-top: 30rpx;
	}
}
</style>
