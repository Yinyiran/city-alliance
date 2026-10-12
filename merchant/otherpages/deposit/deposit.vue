<template>
	<view>
		<nvabar :title="title"></nvabar>
		<image src="/static/bg4.png" class="bg4" mode=""></image>
		<view class="mains">
			<image src="/static/t1.png" mode=""></image>
			<view class="">缴纳保证金，获得更多权限</view>
			<view class="">
				<view class="">需缴纳保证金金额 (元)</view>
				<view class="">{{deposit_amount||0}}</view>
				<view class="btn" @click="submit">立即缴纳</view>
			</view>
		</view>
		<view class="warn acea-row row-middle">
			<image src="/static/warn.png" class="w-28 h-28 m-r-16" mode=""></image>
			注意：需要先缴纳保证金才可正常使用商家权限
		</view>
	</view>
</template>

<script>
	import {
		deposit,
		shopdepositpay
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				title: '保证金充值',
				deposit_amount: "",
				out_trade_no: ""
			};
		},
		onShow() {
			this.getinfo()
		},
		methods: {
			submit() {
				let params = {
					out_trade_no: this.out_trade_no
				}
				shopdepositpay(params).then(res => {
					if (res.code == 0) {
						uni.requestPayment({
							provider: 'wxpay',
							appid:res.data.appId,
							timeStamp: res.data.timeStamp,
							nonceStr: res.data.nonceStr,
							package: res.data.package,
							signType: res.data.signType,
							paySign: res.data.paySign,
							success:res=>{
								
							}
						})
					}else{
						uni.showToast({
							title:res.message,
							icon:'none'
						})
					}
				})
			},
			getinfo() {
				deposit().then(res => {
					const d = (res && res.code == 0 && res.data) || {}
					this.deposit_amount = d.deposit_amount || '0.00'
					this.out_trade_no = (res && res.out_trade_no) || d.out_trade_no || ''
				})
			},
		}
	}
</script>

<style lang="scss">
	.warn {
		background: #FFFFFF;
		border-radius: 24rpx;
		margin: 48rpx 32rpx;
		padding: 32rpx 24rpx;
		font-weight: 400;
		font-size: 24rpx;
		color: #F51D1D;
	}

	.bg4 {
		position: fixed;
		top: 0;
		z-index: -1;
		width: 100vw;
		height: 894rpx;
		top: 0;
	}

	.mains {
		>image {
			width: 280rpx;
			height: 56rpx;
			margin-top: 44rpx;
			margin-left: 52rpx;
		}

		>view:nth-child(2) {
			font-size: 32rpx;
			color: #FFFFFF;
			line-height: 44rpx;
			margin-top: 24rpx;
			margin-left: 52rpx;
			margin-bottom: 46rpx;
		}

		>view:nth-child(3) {
			padding: 64rpx 32rpx;
			margin: 0 32rpx;
			text-align: center;
			background: #FFFFFF;
			border-radius: 24rpx;

			>view:nth-child(1) {
				font-weight: 400;
				font-size: 32rpx;
				color: #222222;
			}

			>view:nth-child(2) {
				font-size: 104rpx;
				color: #222222;
				font-weight: bold;
			}

			.btn {
				background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
				border-radius: 48rpx;
				line-height: 96rpx;
				margin-top: 64rpx;
				font-weight: 600;
				font-size: 32rpx;
				color: #FFFFFF;
			}
		}
	}
</style>