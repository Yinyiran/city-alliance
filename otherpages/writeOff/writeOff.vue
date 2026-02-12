<template>
	<view>
		<nvabar :title="title" bgcolor='#ffffff' :type='1' :titleStyle='titleStyle'></nvabar>
		<view class="part1">
			<view class="">待核销</view>
			<view class="">交易你已扫描核销码，请点击核销</view>
			<view class="">
				<image src="/static/head.png" mode=""></image>
				<view class="acea-row row-column row-center row-middle">
					<view class="">消费金额</view>
					<view class="acea-row row-middle row-center">{{info.level_name}}享
						<view class="">{{info.consume_discount}}</view>
						折
					</view>
					<view class="">{{info.consume_money}}</view>
					<view class="btn" @click="submit">立即核销</view>
				</view>
				<image src="/static/b.png" mode=""></image>
			</view>
		</view>
	</view>
</template>

<script>
	import {getOrderInfo,dodelivery} from "@/common/js/api.js"
	export default {
		data() {
			return {
				title: '扫码核销',
				titleStyle: "color:#222",
				code:'',
				info:{}
			};
		},
		onLoad(res) {
			if(res.code){
				this.code=res.code
			}
			this.getOrderInfo()
		},
		methods:{
			submit(){
				uni.showModal({
					content:'确定核销当前订单？',
					success:res=>{
						if(res.confirm){
							let params={
								delivery_code:this.code
							}
							dodelivery(params).then(resA=>{
								if(resA.code==0){
									uni.showToast({
										title:'核销成功',
										success:()=>{
											setTimeout(()=>{
												uni.navigateBack()
											},2000)
										}
									})
								}else{
									uni.showToast({
										title:res.message,
										icon:'none'
									})
								}
							})
						}
					}
				})
			},
			getOrderInfo(){
				let params={
					delivery_code:this.code
				}
				getOrderInfo(params).then(res=>{
					this.info=res.data
				})
			}
		}
	}
</script>

<style lang="scss">
	.part1 {
		width: 100vw;
		height: 330rpx;
		background: linear-gradient(0deg, #F5AB18 0%, #F55606 100%);
		position: relative;

		>view:first-child {
			font-weight: 600;
			font-size: 36rpx;
			color: #FFFFFF;
			padding-top: 68rpx;
			margin-left: 40rpx;
		}

		>view:nth-child(2) {
			font-weight: 400;
			font-size: 22rpx;
			color: #FFFFFF;
			margin-top: 16rpx;
			margin-left: 40rpx;
		}

		>view:last-child {
			position: absolute;
			width: 710rpx;
			left: 50%;
			transform: translateX(-50%);
			top: 232rpx;
			line-height: 1;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;

			>image:first-child {
				width: 100%;
				height: 32rpx;
			}

			>view {
				height: 420rpx;
				background: #FFFFFF;
				width: 686rpx;
				>view:first-child{
					font-weight: 400;
					font-size: 32rpx;
					color: #222222;
				}
				>view:nth-child(2){
					font-weight: 400;
					font-size: 32rpx;
					color: #B87D49;
					>view{
						height: 40rpx;
						background: #FAE5D2;
						border-radius: 8rpx;
						font-size: 32rpx;
						color: #B87D49;
						font-weight: bold;
						padding: 4rpx 6rpx;
						margin: 0 4rpx;
					}
				}
				>view:nth-child(3){
					font-size: 104rpx;
					color: #222222;
					font-weight: bold;
				}
				.btn{
					background: linear-gradient( 90deg, #F5AB18 0%, #F55606 100%);
					border-radius: 48rpx;
					line-height: 96rpx;
					margin-top: 60rpx;
					font-weight: 600;
					font-size: 32rpx;
					color: #FFFFFF;
					width:calc(100% - 32rpx);
					text-align: center;
				}
			}

			>image:last-child {
				width: 686rpx;
				// margin: 0 auto;
				height: 60rpx;
			}
		}
	}
</style>