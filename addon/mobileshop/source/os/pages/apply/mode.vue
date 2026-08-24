<template>
	<view class="index">
		<!-- 开店方式 -->
		<view class="shop-empty">
			<text class="empty-text">选择您要创建店铺的方式</text>
			<view class="empty-way">
				<view class="way-item">
					<view class="item-img"><image src="../../static/images/way1.png" mode=""></image></view>
					<view class="item-text">
						<text class="text-title">快速开店</text>
						<text class="text-content">一键开店，抢先体验</text>
					</view>
					<view class="item-btn"><view class="btn-btn" @click="fastShop(1)">立即开店</view></view>
				</view>
				<view class="way-item">
					<view class="item-img"><image src="../../static/images/way2.png" mode=""></image></view>
					<view class="item-text">
						<text class="text-title">申请开店</text>
						<text class="text-content">入驻加盟，合作共赢</text>
					</view>
					<view class="item-btn"><view class="btn-btn" @click="applyShop(2)">立即开店</view></view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {};
	},
	onLoad(option) {},
	methods: {
		//快速开店
		fastShop(e) {
			this.$util.redirectTo('/pages/apply/agreement', { type: e });
		},
		//申请开店
		applyShop(e) {
			this.$util.redirectTo('/pages/apply/agreement', { type: e });
		}
	}
};
</script>

<style lang="scss">
page {
	background-color: #fff;
}
.index {
	width: 100%;
	height: 100vh;
	display: flex;
	align-items: center;
	justify-content: center;
	.shop-empty {
		width: 90%;
		height: 60%;
		text-align: center;
		display: flex;
		flex-direction: column;
		.empty-text {
			font-size: 40rpx;
			font-weight: 600;
		}
		.empty-way {
			flex: 1;
			display: flex;
			align-items: center;
			justify-content: space-around;
			.way-item {
				width: 325rpx;
				height: 600rpx;
				background-color: #f4f4f4;
				display: flex;
				flex-direction: column;
				overflow: hidden;
				.item-img {
					width: 250rpx;
					height: 250rpx;
					margin: 0 auto;
					margin-top: 20rpx;
					box-sizing: border-box;

					background-color: #fff;
					border-radius: 50%;
					display: flex;
					align-items: center;
					justify-content: center;
					image {
						width: 80rpx;
						height: 80rpx;
					}
				}
				.item-text {
					flex: 1;
					display: flex;
					flex-direction: column;
					justify-content: center;
					.text-title {
						font-size: 40rpx;
						color: #3a3939;
					}
					.text-content {
						color: #c0a69a;
					}
				}
				.item-btn {
					flex: 1;
					display: flex;
					align-items: center;
					justify-content: center;
					.btn-btn {
						width: 200rpx;
						height: 70rpx;
						background-color: #ff8143;
						color: #fff;
						border-radius: 6rpx;
						display: flex;
						align-items: center;
						justify-content: center;
					}
				}
			}
		}
	}
}
</style>
