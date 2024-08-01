<template>
	<view>
		<view class="head relative">
			<image :src="$util.img(img1)" mode=""></image>
			<view class="relative ">
				<view class="p-t-80 font-56 font-bold">支付有礼</view>
				<view class="font-28 m-t-20">提升客户复购粘性，提高商家锁粉收益</view>
			</view>
		</view>
		<view class="wbg p-40 m-r-20 m-l-20 bor-20 container relative">
			<view class="font-28">您的客户支付成功后，系统将下发营销活动，包括但不仅限于外卖霸王餐、1分购、红包优惠券;客户通过营销活动下单，您可享受锁粉收益。</view>
			<image :src="$util.img(img2)" class="h-900 m-t-40" mode=""></image>
			<view class="m-t-86">
				<uv-button text="立即开启" shape="circle" color="#FF4D1A"></uv-button>
			</view>
		</view>
		
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				img1:'/mobile/img/B.png',
				img2:'/mobile/img/C.png',
			};
		}
	}
</script>

<style lang="scss">
	.relative{
		position: relative;
	}
	view{
		color: #1C274C;
	}
	.head{
		width: 100vw;
		height: 970rpx;
		box-sizing: border-box;
		>view{
			z-index: 9;
			text-align: center;
			view{
				color: #fff;
			}
			>view:first-child{
				
			}
		}
		image{
			width: 100%;
			height: 100%;
			position: absolute;
			left: 0;
			top: 0;
		}
	}
	.container{
		margin-top: -424rpx;
		text-align: center;
		image{
			width: calc(100% - 40rpx);
		}
	}
	.wbg{
		background: #fff;
	}
</style>
