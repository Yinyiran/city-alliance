<template>
	<view>
		<view class="p-t-30 p-b-38 p-r-64 p-l-64 acea-row row-around row-middle wbg ">
			<view v-for="(item,index) in list" :key="index" :class="['font-30','relative',check==index?'on':'']"
				@click="check=index">
				{{item}}
				<image src="@/static/index/icon3.png" v-if="check==index" mode=""></image>
			</view>
		</view>
		<view class="m-t-88 center font-40 font-bold">长按二维码识别或保存</view>
		<view class="fcolor center m-t-20 font-28">打开微信扫一扫</view>
		<view class="center m-t-30">
			<image src="../../static/logo.png" show-menu-by-longpress class="w-340 h-340" mode=""></image>
		</view>
		<view class="wbg p-t-40 p-b-40 p-r-20 p-l-20 bor-20 m-r-20 m-l-20 m-t-66 acea-row row-middle row-between font-28 font-weight-6">
			<text>您的商户号：2329831987</text>
			<text class="fcolor1">复制</text>
		</view>
		<view class="m-r-106 m-l-106 m-t-38 center font-28">
			请用法人(白苏叶)的手机微信扫描上方二维码，完成开户意愿确认。
		</view>
		<view class="m-t-214 center font-28">
			已完成认证，<text class="fcolor1">刷新状态</text>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				check:0,
				list:['专属认证码','通用认证码']
			};
		}
	}
</script>

<style lang="scss">
	view{
		color: #1C274C;
	}
	.wbg{
		background: #fff;
	}
	.relative{
		position: relative;
	}
	.center{
		text-align: center;
	}
	.fcolor{
		color: #606882;
	}
	.fcolor1{color: #FF4D1A;}
	.on {
		font-weight: bold;
		color: #FF4D1A;
	
		image {
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			width: 27rpx;
			height: 9rpx;
			bottom: -20rpx;
	
		}
	}
</style>
