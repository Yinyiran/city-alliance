<template>
	<view>
		<view class="wbg m-t-30 m-b-20 m-l-20 bor-20 p-30">
			<view class="font-28">支付宝账号信息</view>
			<view class="m-t-36">
				<uv-input inputAlign='right' placeholder="输入支付宝实名认证的姓名" customStyle='padding:30rpx;border-radius:20rpx'>
					<template v-slot:prefix>
						<text class="font-28 font-bold">真实姓名</text>
					</template>
				</uv-input>
			</view>
			<view class="m-t-30">
				<uv-input inputAlign='right' placeholder="输入支付宝账号(手机号/邮箱)"  customStyle='padding:30rpx;border-radius:20rpx'>
					<template v-slot:prefix>
						<text class="font-28 font-bold">真实姓名</text>
					</template>
				</uv-input>
			</view>
		</view>
		<view class="wbg m-t-30 m-b-20 m-l-20 bor-20 p-30">
			<view class="font-28">身份验证</view>
			<view class="m-t-36">
				<uv-input inputAlign='right' placeholder="输入手机号" type='number' maxlength="11"  customStyle='padding:30rpx;border-radius:20rpx'>
					<template v-slot:prefix>
						<text class="font-28 font-bold">手机号</text>
					</template>
				</uv-input>
			</view>
			<view class="m-t-30">
				<uv-input inputAlign='right' placeholder="请输入验证码" customStyle='padding:30rpx;border-radius:20rpx;font-size:28rpx'>
					<template v-slot:prefix>
						<text class="font-28 font-bold">验证码</text>
					</template>
					<template v-slot:suffix>
						<text class="font-28 fcolor p-l-20">发送验证码</text>
					</template>
				</uv-input>
			</view>
		</view>
		<view class="btm p-t-20 iphone-x">
			<view class="font-28 m-r-20 m-l-20 bor-64 m-b-20">确认绑定</view>
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
	.wbg {
		background: #fff;
	}
	.fcolor{
		color: #FF4D1A;
	}
	.btm {
		position: fixed;
		width: 100vw;
		box-sizing: border-box;
		left: 0;
		bottom: 0;
		text-align: center;
		background: #fff;
		z-index: 999;
		box-shadow: 0 1rpx 0 0 #EBEBEB inset;
	
		>view {
			background: linear-gradient(90deg, #FF4F11 0%, #FF1414 100%);
			line-height: 82rpx;
			color: #fff;
		}
	}
</style>