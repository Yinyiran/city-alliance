<template>
	<view>
		<view class="p-t-40 p-l-20 font-36 font-bold">线下买单</view>
		<view class="wbg p-t-40 p-b-40 p-r-20 p-l-20 bor-20 m-20 acea-row row-middle row-between">
			<text class="font-28 font-weight-6">签约协议</text>
			<uv-icon name="play-right-fill" size="10" color="rgba(28, 39, 76, 0.15)"></uv-icon>
		</view>
		<view class="wbg p-t-40 p-b-40 p-r-20 p-l-20 bor-20 m-20 acea-row row-middle row-between"
			@click="$util.redirectTo('/otherpages/authentication/authentication')">
			<text class="font-28 font-weight-6">微信认证码</text>
			<uv-icon name="play-right-fill" size="10" color="rgba(28, 39, 76, 0.15)"></uv-icon>
		</view>
		<view class="wbg p-t-40 p-b-40 p-r-20 p-l-20 bor-20 m-20 acea-row row-middle row-between">
			<text class="font-28 font-weight-6">支付宝认证码</text>
			<uv-icon name="play-right-fill" size="10" color="rgba(28, 39, 76, 0.15)"></uv-icon>
		</view>
		<view class="p-t-40 p-l-20 font-36 font-bold">折扣买单</view>
		<view class="wbg p-t-40 p-b-40 p-r-20 p-l-20 bor-20 m-20 acea-row row-middle row-between">
			<text class="font-28 font-weight-6">签约协议</text>
			<uv-icon name="play-right-fill" size="10" color="rgba(28, 39, 76, 0.15)"></uv-icon>
		</view>
		<view class="wbg p-t-40 p-b-40 p-r-20 p-l-20 bor-20 m-20 acea-row row-middle row-between">
			<text class="font-28 font-weight-6">微信认证码</text>
			<uv-icon name="play-right-fill" size="10" color="rgba(28, 39, 76, 0.15)"></uv-icon>
		</view>
		<view class="wbg p-t-40 p-b-40 p-r-20 p-l-20 bor-20 m-20 acea-row row-middle row-between">
			<text class="font-28 font-weight-6">支付宝认证码</text>
			<uv-icon name="play-right-fill" size="10" color="rgba(28, 39, 76, 0.15)"></uv-icon>
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
	view {
		color: #1C274C;
	}

	.wbg {
		background: #fff;
	}
</style>