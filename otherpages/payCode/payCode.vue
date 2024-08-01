<template>
	<view class="p-t-20">
		<view class="bodyModel">
			<view class="flex_center color-base-border-bottom p-b-30">
				<view class="iconfont icon-shoukuan f-tA font-42 lh-32"></view>
				<view class="font-32 font-weight f-tA lh-42 m-l-10 m-b-6">商户收款码</view>
			</view>
			<view class="boxCen m-t-60">
				<view class="font-28 f-tA m-b-50">支付宝/微信扫一扫，向我付钱</view>
				<image src="../../static/logo.png" mode="heightFix"></image>
				<view class="m-t-50 font-28 grey">编号：402389013</view>
				<view class="flex_e payBtn">
					<view class="font-28 graycolor " @click="$util.redirectTo('/otherpages/editCode/editCode')">设置收款码
					</view>
					<view class="font-28 graycolor">下载收款码</view>
				</view>
			</view>
		</view>
		<view class="payCodeBtn flex_c">
			<view v-for="(item,index) in list" :key="index" class="flex_b " @click="rouFn(item)">
				<view class="flex">
					<image :src="item.image" mode="aspectFill"></image>
					<view class="font-32 white m-l-10">{{item.title}}</view>
				</view>
				<view class="iconfont icon-yousanjiao" style="color: #ff9779;"></view>
			</view>
		</view>
		<view class="bottom-mar"></view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list: [{
					title: '交易记录',
					image: require('../../static/home/pay1.png'),
				}, {
					title: '收款语音提醒',
					image: require('../../static/home/pay2.png'),
					router: '/otherpages/sgdModel/sgdModel'
				}, {
					title: '收款账户',
					image: require('../../static/home/pay3.png'),
				}, ]
			};
		},
		methods: {
			rouFn(item) {
				this.$util.redirectTo(item.router)
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #ff4d19;
	}

	.botSty {
		border-bottom: 1rpx solid #ff8564;
		padding-bottom: 20rpx;
	}

	.payCodeBtn {
		padding: 38rpx;
		box-sizing: border-box;
		width: 710rpx;
		height: 320rpx;
		background-color: #ff6a40;
		border-radius: 16rpx;
		margin: 30rpx auto;

		image {
			width: 50rpx;
			height: 50rpx;
		}
	}

	.payBtn {
		position: absolute;
		bottom: 40rpx;
		width: 650rpx;

		view {
			width: 249rpx;
			height: 91rpx;
			border: 1rpx solid #e6e6e6;
			border-radius: 50rpx;
			text-align: center;
			line-height: 91rpx;
		}

	}

	.bodyModel {
		position: relative;
		width: 710rpx;
		height: 874rpx;
		border-radius: 20rpx;
		background-color: #fff;
		margin: 0 auto;
		padding: 30rpx;
		box-sizing: border-box;

		.boxCen {
			text-align: center;

			image {
				width: 330rpx;
				height: 330rpx;
			}
		}
	}
</style>