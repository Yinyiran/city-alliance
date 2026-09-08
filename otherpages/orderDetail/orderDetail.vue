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
		<view class="acea-row row-middle row-center row-column status status2" v-else-if="info.order_status==1">
			<view class="acea-row row-middle">
				<image src="/static/pay1.png" class="w-36 h-36 m-r-12" mode=""></image>
				已支付
			</view>
			<view class="m-t-12" style="color: #999;">订单已支付，请前往门店出示核销码提货</view>
		</view>
		<view class="acea-row row-middle row-center row-column status status-pending" v-else-if="info.order_status==2">
			<view class="acea-row row-middle" style="color: #FE780F;">
				<uv-icon name="scan" size="20" color="#FE780F"></uv-icon>
				<text class="m-l-8">待核销</text>
			</view>
			<view class="m-t-12" style="color: #999;">请向商家出示下方核销码进行提货核销</view>
		</view>
		<view class="acea-row row-middle row-center row-column status status-verified" v-else-if="isOrderVerified">
			<view class="acea-row row-middle" style="color: #25B85B;">
				<uv-icon name="checkmark-circle" size="20" color="#25B85B"></uv-icon>
				<text class="m-l-8">已核销</text>
			</view>
			<view class="m-t-12" style="color: #999;">订单商品已完成核销，感谢您的光临</view>
		</view>
		<view class="acea-row row-middle row-center row-column status status1" v-else-if="info.order_status==-1">
			<view class="acea-row row-middle" style="color: #222222;">
				<uv-icon name="close-circle" size="18" color="#222"></uv-icon>
				已关闭
			</view>
			<view class="m-t-12" style="color: #999;">订单已关闭</view>
		</view>

		<!-- 核销二维码模块 -->
		<view class="verify-card" v-if="canShowVerifyCode">
			<view class="verify-header acea-row row-middle row-between">
				<view class="acea-row row-middle">
					<image src="/static/img2.png" class="w-36 h-36 m-r-12" mode=""></image>
					<text class="verify-title">提货核销码</text>
				</view>
				<view class="verify-badge">到店出示</view>
			</view>
			<view class="verify-body acea-row row-column row-center row-middle">
				<view class="qrcode-box">
					<uv-qrcode ref="qrcode" :value="qrValue" size="340rpx" :loading="qrLoading"></uv-qrcode>
				</view>
				<view class="verify-code-num acea-row row-middle m-t-20">
					<text class="code-text">{{ formatCode(verifyCode) }}</text>
					<view class="refresh-btn acea-row row-middle m-l-16" @click="fetchVerifyCode">
						<uv-icon name="reload" size="14" color="#FF6600"></uv-icon>
						<text class="m-l-4">刷新</text>
					</view>
				</view>
				<view class="verify-tips m-t-16 acea-row row-middle">
					<uv-icon name="info-circle" size="13" color="#999999"></uv-icon>
					<text class="m-l-6">向商家出示此码核销，每次进入自动更新</text>
				</view>
			</view>
		</view>

		<!-- 已核销卡片 -->
		<view class="verify-card verified-card" v-else-if="isOrderVerified">
			<view class="verified-body acea-row row-column row-center row-middle">
				<view class="verified-stamp acea-row row-middle row-center">
					<uv-icon name="checkbox-mark" size="22" color="#25B85B"></uv-icon>
					<text class="m-l-8">已核销</text>
				</view>
				<view class="verified-desc m-t-16">该订单商品已完成线下核销提货</view>
			</view>
		</view>

		<view class="address p-32 bor-24" v-if="info.goods_type==1&&info.is_admin==1">
			<view class="acea-row row-middle row-between">
				<view class="">我的收货地址</view>
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
				<view class="acea-row row-middle m-t-28 row-between" v-for="(item,index) in info.order_goods" :key="index">
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
							<view class="" v-if="item.delivery_status == 1 || isOrderVerified">
								<view class="order-goods-tag-verified">已核销</view>
							</view>
							<view class="" v-else-if="info.order_status == 2">
								<view class="order-goods-tag-pending">待核销</view>
							</view>
							<view class="" v-else-if="item.refund_status == 0 || item.refund_status == -1"
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
		huifupay,
		getVerifyCode
	} from '@/common/js/api.js';
	export default {
		data() {
			return {
				check: 0,
				id: '',
				info: {},
				verifyCode: '',
				qrLoading: false
			};
		},
		onLoad(res) {
			this.id = res.id
			this.getinfo()
		},
		onShow() {
			if (this.info && this.canShowVerifyCode) {
				this.fetchVerifyCode();
			}
		},
		computed: {
			isOrderVerified() {
				return this.info.order_status == 4 || this.info.order_status == 10 || (this.info.delivery_status == 1 && this.info.order_status != 0);
			},
			canShowVerifyCode() {
				if (this.isOrderVerified) return false;
				if (this.info.order_status == -1) return false;
				if (this.info.order_status == 0 && this.info.pay_status == 0) return false;
				return this.info.order_status == 1 || this.info.order_status == 2 || this.info.pay_status == 1;
			},
			qrValue() {
				return this.verifyCode || this.info.delivery_code || this.info.order_no || '';
			},
			bgcolor() {
				if (this.info.order_status == 0) {
					return '#FFF0EB'
				} else if (this.info.order_status == 1) {
					return '#EAFEF1'
				} else if (this.info.order_status == 2) {
					return '#FFF7EE'
				} else if (this.isOrderVerified) {
					return '#F0F9EB'
				}
				return '#fff'
			},
		},
		methods: {
			formatCode(code) {
				if (!code) return '';
				return code.replace(/(.{4})/g, '$1 ').trim();
			},
			fetchVerifyCode() {
				if (!this.id) return;
				this.qrLoading = true;
				getVerifyCode({
					order_id: this.id
				}).then(res => {
					this.qrLoading = false;
					if (res.code == 0 && res.data) {
						this.verifyCode = res.data.verify_code;
						if (res.data.is_verify) {
							this.info.order_status = 4;
							this.info.delivery_status = 1;
						}
					}
				}).catch(() => {
					this.qrLoading = false;
				});
			},
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
					this.info = res.data;
					if (this.canShowVerifyCode) {
						this.fetchVerifyCode();
					}
				});
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

	.status-pending {
		background: linear-gradient(180deg, #FFF7EE 0%, #F5F5F5 100%);
		>view:first-child {
			color: #FE780F;
		}
	}

	.status-verified {
		background: linear-gradient(180deg, #F0F9EB 0%, #F5F5F5 100%);
		>view:first-child {
			color: #25B85B;
		}
	}

	.verify-card {
		background: #ffffff;
		border-radius: 24rpx;
		margin: 20rpx 24rpx;
		padding: 32rpx;
		box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.04);

		.verify-header {
			padding-bottom: 24rpx;
			border-bottom: 1rpx dashed #EEEEEE;

			.verify-title {
				font-size: 32rpx;
				font-weight: 600;
				color: #222222;
			}

			.verify-badge {
				font-size: 22rpx;
				color: #FF6600;
				background: #FFF0E5;
				padding: 6rpx 16rpx;
				border-radius: 20rpx;
				font-weight: 500;
			}
		}

		.verify-body {
			padding-top: 32rpx;

			.qrcode-box {
				padding: 20rpx;
				background: #ffffff;
				border: 1rpx solid #F0F0F0;
				border-radius: 16rpx;
				display: flex;
				align-items: center;
				justify-content: center;
			}

			.verify-code-num {
				.code-text {
					font-size: 36rpx;
					font-weight: 700;
					color: #222222;
					letter-spacing: 2rpx;
				}

				.refresh-btn {
					font-size: 24rpx;
					color: #FF6600;
					background: #FFF4EC;
					padding: 6rpx 16rpx;
					border-radius: 24rpx;
				}
			}

			.verify-tips {
				font-size: 22rpx;
				color: #999999;
			}
		}
	}

	.verified-card {
		background: #ffffff;
		border: 1rpx solid #E1F3D8;

		.verified-body {
			padding: 40rpx 20rpx;

			.verified-stamp {
				padding: 12rpx 32rpx;
				background: #F0F9EB;
				border: 2rpx solid #25B85B;
				border-radius: 36rpx;
				color: #25B85B;
				font-size: 32rpx;
				font-weight: 600;
			}

			.verified-desc {
				font-size: 26rpx;
				color: #666666;
			}
		}
	}

	.order-goods-tag-verified {
		font-size: 22rpx;
		color: #25B85B;
		background: #F0F9EB;
		border: 1rpx solid #B8E894;
		border-radius: 8rpx;
		padding: 4rpx 14rpx;
		font-weight: 500;
	}

	.order-goods-tag-pending {
		font-size: 22rpx;
		color: #FE780F;
		background: #FFF7EE;
		border: 1rpx solid #FFD9B3;
		border-radius: 8rpx;
		padding: 4rpx 14rpx;
		font-weight: 500;
	}
</style>