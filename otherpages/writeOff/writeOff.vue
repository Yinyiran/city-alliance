<template>
	<view>
		<nvabar :title="title" bgcolor='#ffffff' :type='1' :titleStyle='titleStyle'></nvabar>
		<view class="part1">
			<view class="">{{ isVerified ? '已核销' : '待核销' }}</view>
			<view class="">{{ isVerified ? '该订单商品已完成核销' : '你已扫描核销码，请核对订单及商品后点击核销' }}</view>
			<view class="">
				<image src="/static/head.png" mode=""></image>
				<view class="content-box acea-row row-column row-center row-middle">
					<view class="order-no-text" v-if="info.order_no">订单号：{{ info.order_no }}</view>
					<view class="consumer-text" v-if="info.name">顾客：{{ info.name }} {{ info.mobile || '' }}</view>
					<view class="money-label m-t-16">消费金额</view>
					<view class="consume-amount">￥{{ info.pay_money || info.consume_money || info.order_money || 0 }}</view>
					
					<!-- 商品清单 -->
					<view class="goods-list-wrap" v-if="info.goods_list && info.goods_list.length">
						<view class="goods-subitem acea-row row-middle" v-for="(item, idx) in info.goods_list" :key="idx">
							<image :src="$util.img(item.sku_image)" class="goods-thumb" mode="aspectFill"></image>
							<view class="goods-detail acea-row row-column row-between">
								<text class="goods-title text-more">{{ item.sku_name }}</text>
								<view class="acea-row row-middle row-between">
									<text class="goods-price-text">￥{{ item.price }} × {{ item.num }}</text>
									<text class="goods-badge" :class="{'badge-done': item.delivery_status == 1 || isVerified}">
										{{ (item.delivery_status == 1 || isVerified) ? '已核销' : '待核销' }}
									</text>
								</view>
							</view>
						</view>
					</view>

					<view class="btn" :class="{'btn-disabled': isVerified || submitting}" @click="submit">
						<text v-if="submitting">核销中...</text>
						<text v-else-if="isVerified">已核销</text>
						<text v-else>立即核销</text>
					</view>
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
				info:{},
				submitting: false,
				//核销时段不符时，商家确认后强制核销（仅本次有效）
				force: false
			};
		},
		computed: {
			isVerified() {
				return this.info.is_verify == 1 || this.info.order_status == 4;
			}
		},
		onLoad(res) {
			if(res.code){
				this.code=res.code
			}
			this.getOrderInfo()
		},
		methods:{
			submit(){
				if (this.isVerified || this.submitting) return
				uni.showModal({
					content:'确定核销当前订单？',
					success:res=>{
						if(res.confirm){
							this.submitting = true
							let params={
								delivery_code:this.code,
								force: this.force ? 1 : 0
							}
							dodelivery(params).then(resA=>{
								this.submitting = false
								if(resA.code==0){
									this.force = false;
									this.info.is_verify = 1
									this.info.order_status = 4
									this.info.order_status_name = '已核销'
									if (this.info.goods_list) {
										this.info.goods_list.forEach(item => {
											item.delivery_status = 1
											item.delivery_status_name = '已核销'
										})
									}
									uni.showToast({
										title:'核销成功',
										success:()=>{
											setTimeout(()=>{
												uni.navigateBack()
											},1500)
										}
									})
								}else if(resA.code == -2){
									//核销时段不符：商家确认后可强制核销，建议先引导顾客改约
									uni.showModal({
										title: '超出预约时段',
										content: (resA.message || '顾客当前不在预约时段内。') + '\n\n仍要为该顾客核销吗？建议先引导改约。',
										confirmText: '仍要核销',
										cancelText: '暂不核销',
										success: r => {
											if (r.confirm) {
												this.force = true;
												this.submit();
											}
										}
									})
								}else{
									uni.showToast({
										title:resA.message || '核销失败',
										icon:'none'
									})
								}
							}).catch(() => {
								this.submitting = false
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
					if (res.code == 0 && res.data) {
						this.info=res.data
					} else {
						uni.showToast({
							title: res.message || '获取订单失败',
							icon: 'none'
						})
					}
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

			.content-box {
				min-height: 420rpx;
				background: #FFFFFF;
				width: 686rpx;
				padding: 30rpx 24rpx;
				box-sizing: border-box;

				.order-no-text {
					font-size: 26rpx;
					color: #666666;
					margin-bottom: 8rpx;
				}

				.consumer-text {
					font-size: 26rpx;
					color: #222222;
					font-weight: 500;
				}

				.money-label {
					font-weight: 400;
					font-size: 28rpx;
					color: #888888;
				}

				.consume-amount {
					font-size: 64rpx;
					color: #F51D1D;
					font-weight: bold;
					margin-top: 12rpx;
				}

				.goods-list-wrap {
					width: 100%;
					margin-top: 24rpx;
					border-top: 1rpx solid #F0F0F0;
					padding-top: 16rpx;

					.goods-subitem {
						padding: 12rpx 0;
						border-bottom: 1rpx dashed #F5F5F5;

						&:last-child {
							border-bottom: none;
						}

						.goods-thumb {
							width: 96rpx;
							height: 96rpx;
							border-radius: 12rpx;
							margin-right: 16rpx;
							flex-shrink: 0;
							background: #f5f5f5;
						}

						.goods-detail {
							flex: 1;
							height: 96rpx;
							overflow: hidden;

							.goods-title {
								font-size: 26rpx;
								color: #222222;
							}

							.goods-price-text {
								font-size: 24rpx;
								color: #666666;
							}

							.goods-badge {
								font-size: 20rpx;
								color: #FF6600;
								background: #FFF4EC;
								padding: 2rpx 10rpx;
								border-radius: 6rpx;
							}

							.badge-done {
								color: #25B85B;
								background: #EAFEF1;
							}
						}
					}
				}

				.btn {
					background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
					border-radius: 48rpx;
					line-height: 96rpx;
					margin-top: 36rpx;
					font-weight: 600;
					font-size: 32rpx;
					color: #FFFFFF;
					width: 100%;
					text-align: center;
				}

				.btn-disabled {
					background: #CCCCCC !important;
					color: #FFFFFF !important;
				}
			}

			>image:last-child {
				width: 686rpx;
				height: 60rpx;
			}
		}
	}
</style>