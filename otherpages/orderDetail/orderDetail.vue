<template>
	<view>
		<uv-sticky :bg-color="bgcolor">
			<nvabar type='1' title='订单详情' titleStyle='color:#222'></nvabar>
		</uv-sticky>
		<view class="acea-row row-middle row-center row-column status" v-if="info.order_status==0">
			<view class="acea-row row-middle">
				<image src="/static/pay.png" class="w-36 h-36 m-r-12" mode=""></image>
				待支付
			</view>
			<view class="m-t-12" style="color: #999;">请在23小时59分钟内完成支付，逾期将会自动取消订单</view>
		</view>
		<view class="acea-row row-middle row-center row-column status status2" v-if="info.order_status==1">
			<view class="acea-row row-middle">
				<image src="/static/pay1.png" class="w-36 h-36 m-r-12" mode=""></image>
				已支付
			</view>
			<view class="m-t-12" style="color: #999;">订单已支付，正在配送中，请耐心等待</view>
		</view>
		<view class="acea-row row-middle row-center row-column status status1" v-if="info.order_status==-1">
			<view class="acea-row row-middle" style="color: #222222;">
				<uv-icon name="close-circle" size="18" color="#222"></uv-icon>
				<!-- <image src="/static/pay.png" class="w-36 h-36 m-r-12" mode=""></image> -->
				已关闭
			</view>
			<view class="m-t-12" style="color: #999;">订单已关闭</view>
		</view>
		<view class="address p-32 bor-24" v-if="info.goods_type==1&&info.is_admin==1">
			<view class="acea-row row-middle row-between">
				<view class="">我的收获地址</view>
				<!-- <view class="acea-row row-middle">
					<image src="/static/edit.png" class="w-24 h-24 m-r-4" mode=""></image>
					切换收货地址
				</view> -->
			</view>
			<view class="">
				<view class="">{{info.name}} {{info.mobile}}</view>
				<view class="">{{info.full_address.split('-').join('')}}{{info.address}}</view>
			</view>
		</view>
		<view class="">
			<view v-for="(item,index) in 1" class="p-32 items" :key="index">
				<view class="acea-row row-middle row-between">
					<view class="acea-row row-middle font-32 font-weight-6">
						<image src="/static/stores.png" class="w-32 h-32 m-r-8" mode=""></image>
						{{info.site_name}}
						<view class="go m-l-8">
							<uv-icon color="#555555" size="8" name="arrow-right"></uv-icon>
						</view>
					</view>
				</view>
				<view class="acea-row row-middle m-t-28 row-between" v-for="(item,index) in info.order_goods">
					<image :src="$util.img(item.sku_image)" class="w-140 h-140 bor-16" mode=""></image>
					<view class="widths h-140 acea-row row-between row-column" style="flex-wrap: nowrap;">
						<view class="font-28 text-more-2">{{item.sku_name}}</view>
						<view class="acea-row row-middle row-between">
							<view class="points acea-row row-middle">
								<template v-if="info.pay_type!='POINT'">
									￥
									<text>{{item.price}}</text>
								</template>
								<template v-else>
									<text class="font-30 font-weight-6">{{item.point}}</text>
									积分
								</template>
								<view class="nums m-l-20">x{{item.num}}</view>
							</view>
							<view class="" v-if="item.refund_status == 0 || item.refund_status == -1"
								@click="goRefund(item.order_goods_id)">退款</view>
							<view @click="goRefundDetail(item.order_goods_id)" v-else>
								<view class="order-box-btn">查看退款</view>
							</view>
						</view>

					</view>
				</view>
			</view>
		</view>
		<view class="mians m-t-20">
			<!-- 	<view class="cuts acea-row row-middle row-between">
				<view :class="{'on1':check==0}" @click="check=0">
					<text>现金支付</text>
					<image src="/static/cl.png" v-if="check==0" mode=""></image>
				</view>
				<view :class="{'on1':check==1}" @click="check=1">
					<text>积分支付</text>
					<image src="/static/cr.png" v-if="check==1" mode=""></image>
				</view>
			</view> -->
			<view class="infos">
				<view class="font-32 font-weight-6">
					{{info.pay_type_name}}
				</view>
				<view class="acea-row row-middle row-between">
					<view class="font-26">商品总额</view>
					<view class="font-26 font-weight-6" v-if="info.pay_type!='POINT'">￥{{info.order_money}}</view>
					<view class="font-26 font-weight-6" v-else>积分{{info.order_point}}</view>
				</view>
				<!-- <view class="acea-row row-middle row-between">
					<view class="font-26">会员折扣
					</view>
					<view class="font-26 font-weight-6">
						钻石会员享98折
					</view>
				</view> -->
				<view class="acea-row row-middle row-right topline">
					<text class="font-26 font-weight-6">合计：</text>
					<view class="font-26 font-weight-6 price" v-if="info.pay_type!='POINT'">￥{{info.order_money}}</view>
					<view class="font-26 font-weight-6 price" v-else>积分{{info.order_point}}</view>
					<!-- <text class="linth font-26 m-l-12">￥{{info.order_money}}</text> -->
				</view>
			</view>

		</view>
		<view class="infos1">
			<view class="">订单信息</view>
			<view class="acea-row row-middle row-between">
				<view class="font-26">订单编号</view>
				<view class="font-26 font-weight-6">
					{{info.order_no}} |
					<text @click="$util.copy(info.order_no)" class="m-l-10">复制</text>
				</view>
			</view>
			<view class="acea-row row-middle row-between">
				<view class="font-26">下单时间
				</view>
				<view class="font-26 font-weight-6">
					{{info.create_time}}
				</view>
			</view>
			<view class="acea-row row-middle row-between">
				<view class="font-26">订单交易号
				</view>
				<view class="font-26 font-weight-6">
					{{info.out_trade_no}}
				</view>
			</view>
			<view class="acea-row row-middle row-between">
				<view class="font-26">配送方式
				</view>
				<view class="font-26 font-weight-6">
					{{info.delivery_type_name}}
				</view>
			</view>
		</view>
		<view class="h-300"></view>
		<view class="btms iphone-x" v-if="info.order_status==0">
			<view class="acea-row row-middle row-right">
				<view class="btn2">取消订单</view>
				<view class="btn1" @click="huifupay">立即付款</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		orderDetail,
		huifupay
	} from '@/common/js/api.js';
	export default {
		data() {
			return {
				check: 0,
				id: '',
				info: {}
			};
		},
		onLoad(res) {
			this.id = res.id
			this.getinfo()
		},
		computed: {
			bgcolor() {
				if (this.info.order_status == 0) {
					return '#FFF0EB'
				} else if (this.info.order_status == 1) {
					return '#EAFEF1'
				}
				return '#fff'
			},
		},
		methods: {
			goRefundDetail(id) {
				this.$util.redirectTo('/otherpages/refund_detail/refund_detail', {
					order_goods_id: id
				});
			},
			goRefund(id) {
				this.$util.redirectTo('/otherpages/refund/refund', {
					order_goods_id: id
				});
			},
			huifupay() {
				let params1 = {
					out_trade_no: this.info.out_trade_no
				}
				huifupay(params1).then(resA => {
					if (resA.code == 0) {
						this.topay(resA)
					}
				})

			},
			topay(res) {
				uni.requestPayment({
					provider: 'wxpay',
					timeStamp: res.data.timeStamp,
					nonceStr: res.data.nonceStr,
					package: res.data.package,
					signType: res.data.signType,
					paySign: res.data.paySign,
					success: () => {
						this.getinfo()
					},
					fail: err => {}
				})
			},
			getinfo() {
				let params = {
					order_id: this.id
				}
				orderDetail(params).then(res => {
					this.info = res.data
				})
			},
		}
	}
</script>

<style lang="scss">
	.btms {
		position: fixed;
		width: 100vw;
		background: #fff;
		bottom: 0;
		z-index: 909;
		left: 0;

		>view {
			padding: 20rpx 32rpx;

			>view {
				width: 192rpx;
				height: 96rpx;
				border-radius: 48rpx;
				text-align: center;
				line-height: 96rpx;
				box-sizing: border-box;
				font-weight: 600;
				font-size: 32rpx;
			}

			.btn2 {
				border: 2rpx solid #DDDDDD;
				margin-right: 16rpx;
				color: #999999;
			}

			.btn1 {
				background: linear-gradient(90deg, #FFBB00 0%, #FA5114 100%);
				color: #fff;
			}
		}
	}

	.status {
		height: 176rpx;
		background: linear-gradient(180deg, #FFF0EB 0%, #F5F5F5 100%);

		>view:first-child {
			font-weight: 500;
			font-size: 36rpx;
			color: #F51D1D;
		}
	}

	.status1 {
		background: linear-gradient(180deg, #FFFFFF 0%, #F5F5F5 100%);
	}

	.status2 {
		background: linear-gradient(180deg, #EAFEF1 0%, #F5F5F5 100%);

		>view:first-child {
			color: #25B85B;
		}
	}

	.infos1 {
		background: #fff;
		padding: 32rpx;
		margin-top: 20rpx;

		>view:first-child {
			font-weight: 600;
			font-size: 32rpx;
			color: #222222;
			padding-bottom: 4rpx;
		}

		>view:not(:first-child) {
			margin-top: 20rpx;

			>view:first-child {
				font-weight: 400;
				font-size: 26rpx;
				color: #999999;
			}

			>view:last-child {
				font-weight: 400;
				font-size: 26rpx;
				color: #1A1A1A;
			}
		}
	}

	.infos {
		background: #fff;
		padding: 16rpx 32rpx;

		.topline {
			border-top: 1rpx solid #F4F4F4;
			padding-top: 24rpx;
			margin-top: 24rpx;
		}

		>view {
			margin: 16rpx 0;
		}
	}

	.price {
		font-weight: 600;
		font-size: 26rpx;
		color: #F51D1D;
	}

	.linth {
		text-decoration: line-through;
		color: #999999;
	}

	.cuts {
		border-radius: 24rpx 24rpx 0rpx 0rpx;
		overflow: hidden;
		background: linear-gradient(180deg, #F0F0F0 0%, rgba(248, 248, 248, 0.84) 100%);

		>view {
			width: 50%;
			text-align: center;
			line-height: 90rpx;

			text {
				font-weight: 400;
				font-size: 32rpx;
				color: #222222;
			}

			position: relative;

			>image {
				position: absolute;
				width: 409rpx;
				height: 90rpx;
				left: 0;
				top: 0;
				z-index: 1;
			}

			text {
				position: relative;
				z-index: 5;
			}
		}

		.on1 {
			text {
				font-weight: 600;
			}
		}

		.on1::after {
			content: '';
			width: 64rpx;
			height: 6rpx;
			background: #F55606;
			border-radius: 4rpx;
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			bottom: 0;
			z-index: 5;
		}
	}

	.items {
		background: #fff;
		margin-top: 20rpx;

		.widths {
			width: calc(100% - 140rpx - 20rpx);

			.points {
				color: #F51D1D;

				text {
					font-weight: 600;
					font-size: 30rpx;
					color: #F51D1D;
				}

				.line {
					width: 36rpx;
					font-weight: 400;
					font-size: 28rpx;
					color: #DDDDDD;
					display: inline-block;
					text-align: center;
				}
			}

			.nums {
				color: #999999;
			}
		}
	}

	.go {
		width: 24rpx;
		height: 24rpx;
		background: #F0F0F0;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
	}

	.address {
		background: #fff;

		>view:first-child {
			height: 66rpx;
			background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
			border-radius: 24rpx 24rpx 0rpx 0rpx;
			padding: 0 24rpx;

			>view {
				color: #FFFFFF;
			}
		}

		>view:last-child {
			padding: 24rpx;
			background: #F8F8F8;
			border-radius: 0rpx 0rpx 24rpx 24rpx;

			>view:first-child {
				font-weight: 400;
				font-size: 24rpx;
				color: #555555;
			}

			>view:first-child::before {
				content: '收';
				background: #222222;
				border-radius: 4rpx;
				margin-right: 8rpx;
				font-weight: 400;
				font-size: 22rpx;
				color: #FFFFFF;
				padding: 0 8rpx;
				height: 32rpx;
				text-align: center;
				line-height: 32rpx;
			}

			>view:last-child {
				font-weight: 500;
				font-size: 28rpx;
				color: #222222;
				margin-top: 12rpx;
			}
		}
	}
</style>