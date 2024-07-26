<template>
	<view>
		<uv-sticky>
			<navbar :title='title'></navbar>
		</uv-sticky>
		<view class="m-20 p-20 wbg bor-10">
			<view class="font-28" style="font-weight: 550 ;">提现金额</view>
			<view class="acea-row row-between row-middle m-t-40 m-b-40">
				<view class="acea-row row-middle">
					<view class="font-40 font-bold m-r-20">￥</view>
					<input type="text" placeholder="输入提款金额" />
				</view>
				<view class="fcolor font-28">全部</view>
			</view>
			<uv-line color="#1C274C1a"></uv-line>
			<view class="fcolor2 m-t-20">
				可提现金额0.1元
			</view>
		</view>
		<view class="wbg bor-10 m-r-20 m-l-20 m-t-30 p-20">
			<view class="font-28">提现金额</view>
			<view class="acea-row row-middle row-between m-t-34" v-if="false">
				<view class="acea-row row-middle">
					<text class="icon-zhifubao iconfont icon"></text>
					<text class="m-l-16 font-28 font-bold">请绑定支付宝账户</text>
				</view>
				<uv-icon name="play-right-fill" color="#898FA3" size="10"></uv-icon>
			</view>
			<view class="m-t-34" v-else>
				<view class="acea-row row-middle row-between m-b-42" v-for="(item,index) in 2" :key="index" @click="check=index">
					<view class="acea-row row-middle">
						<text class="icon-zhifubao iconfont icon"></text>
						<text class="m-l-16 font-28 font-bold">白苏 130****2321</text>
						<text class="font-28 m-l-12 " style="color: red;">删除</text>
					</view>
					<radio style="transform: scale(0.8);" value="" activeBackgroundColor='#ff4d1a' :checked="check==index" />
				</view>
				<view class="center bg p-t-20 p-b-20 bor-20 font-28 m-b-10" @click="$util.redirectTo('/otherpages/bindAlipay/bindAlipay')">绑定新账户</view>
			</view>
		</view>
		<view class="fcolor center font-28 m-t-30" @click="$util.redirectTo('/otherpages/withdraw_detail/withdraw_detail')">提现明细</view>
		<view class="btm p-t-20 iphone-x">
			<view class="font-28 m-r-20 m-l-20 bor-64 m-b-20">确认提现</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				title: "立即提现",
				check:''
			};
		}
	}
</script>

<style lang="scss">
	.wbg {
		background: #fff;
	}

	.center {
		text-align: center;
	}

	.icon {
		font-size: 41rpx;
		color: #1296db;
	}

	.fcolor {
		color: #FF4D1A;
	}

	.fcolor1 {
		color: #606882;
	}

	.fcolor2 {
		color: #FF3333;
	}
	.bg{
		background: #F6F7FB;
	}
	input {
		font-size: 32rpx;
	}

	input::placeholder {
		color: #BBBEC9;
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

	/* #ifdef APP-PLUS || MP-WEIXIN */

	radio .wx-radio-input.wx-radio-input-checked {
		background-color: #FF4D1A !important;
		border-color: #FF4D1A !important;
		// box-sizing: border-box;
	}

	/**
	* 去除选中后的对号
	*/
	radio::before {
		content: '' !important;
	}

	/* #endif */
</style>