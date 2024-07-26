<template>
	<view class="p-t-90">
		<view class="textC font-40 font-weight f-tA">输入新的手机号</view>
		<view class="font-28 graycolor textC m-b-70">换绑新手机号后，可以用新手机号验证登录</view>
		<view class="iptSty flex ">
			<input type="tel" placeholder="请输入手机号" maxlength="11" />
		</view>
		<view class="iptSty flex_b">
			<input type="tel" placeholder="请输入验证码" maxlength="11"
				style="width: 445rpx;border-right: 1rpx solid #afb0b2;" />
			<view class="font-28 f-t">发送验证码</view>
		</view>
		<view class="grey textC font-26 ">请确保新手机号未被其他账号绑定</view>
		<view class="">
			<button type="primary">确定更换手机号</button>
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
		margin-bottom: 50rpx;
		padding: 0 25rpx;

		input {
			width: 100%;
		}
	}
</style>