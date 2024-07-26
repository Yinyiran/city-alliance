<template>
	<view>
		<view class="setHeadBg">
			<view class="white font-56 textC font-weight">我要开店</view>
			<view class="white font-28 textC">请按照步骤完成开店</view>
			<view class="stepSty flex_b">
				<view class="">
					<view class="font-44 font-weight textC white">1</view>
					<view class="font-28 textC white">绑定地推</view>
				</view>
				<view class="xuXian"></view>
				<view class="">
					<view class="font-44 font-weight textC white">2</view>
					<view class="font-28 textC white">资质认证</view>
				</view>
				<view class="xuXian"></view>
				<view class="">
					<view class="font-44 font-weight textC white">3</view>
					<view class="font-28 textC white">开通线下收款</view>
				</view>
				<view class="xuXian"></view>
				<view class="">
					<view class="font-44 font-weight textC white">4</view>
					<view class="font-28 textC white">开通线上收款</view>
				</view>
			</view>
			<view class="bodySty">
				<view class="iptSty flex">
					<input type="tel" placeholder="请输入地推手机号" maxlength="11" />
				</view>
				<view class="font-26 grey textC">为更好的服务您，请绑定地推</view>
				<view class="p-50">
					<button type="primary">确认绑定</button>
				</view>
			</view>
		</view>
		
		<view class="bomPosition">
			<button class="noneBtn f-t font-28 font-weight" open-type="contact" style="width: 100%;">联系客服</button>
			<view class="bottom-mar"></view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				
			};
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #fff;
	}

	.iptSty {
		width: 600rpx;
		height: 114rpx;
		flex-shrink: 0;
		border-radius: 20rpx;
		background-color: #f6f7fb;
		margin: 0 auto;
		margin-bottom: 15rpx;
		padding: 0 25rpx;

		input {
			width: 100%;
		}
	}

	.bodySty {
		width: 750rpx;
		opacity: 1;
		background: #ffffff;
		border-radius: 30rpx 30rpx 0 0;
		// background-color: rebeccapurple;
		margin-top: -60rpx;
		padding-top: 50rpx;
		padding-bottom: 30rpx;
	}

	.stepSty {
		margin-top: 50rpx;
		width: 750rpx;
		height: 240rpx;
		border-radius: 30rpx 30rpx 0 0;
		opacity: 1;
		border: 1rpx solid #ffffff;
		background: #ff9374;
		padding: 0 20rpx;
		padding-bottom: 60rpx;
		box-sizing: border-box;

		.xuXian {
			width: 30rpx;
			height: 0;
			border-top: 2rpx dashed #ffae9e;
		}
	}

	.setHeadBg {
		height: 405rpx;
		width: 100%;
		padding-top: 50rpx;
		box-sizing: border-box;
		background-image: linear-gradient(to top, #ff9374, #ff9374, #ff4d19);
	}
</style>