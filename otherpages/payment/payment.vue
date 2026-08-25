<template>
	<view>
		<view class="address p-32 bor-24" v-if="info1.goodstype==3">
			<view class="acea-row row-middle row-between">
				<view class="">我的收获地址</view>
				<view class="acea-row row-middle" @click="$util.redirectTo('/otherpages/address/address')">
					<image src="/static/edit.png" class="w-24 h-24 m-r-4" mode=""></image>
					切换收货地址
				</view>
			</view>
			<view class="" v-if="orderinfo.member_address">
				<view class="">{{orderinfo.member_address.name}} {{orderinfo.member_address.mobile}}</view>
				<view class="">
					{{orderinfo.member_address.full_address.split('-').join('')+orderinfo.member_address.address}}
				</view>
			</view>
			<view class="acea-row row-middle row-center h-174" v-else
				@click="$util.redirectTo('/otherpages/address/address')">
				<image src="/static/map7.png" class="w-26 h-26" mode=""></image>
				<text class="font-26 m-r-10 m-l-10">请选择收货地址</text>
				<uv-icon name="arrow-right" color="#999999" size="18"></uv-icon>
			</view>
		</view>
		<view class="">
			<view v-for="(item,index) in orderinfo.shop_goods_list" class="p-32 items" :key="index">
				<view class="acea-row row-middle row-between">
					<view class="acea-row row-middle font-32 font-weight-6">
						<image src="/static/stores.png" class="w-32 h-32 m-r-8" mode=""></image>
						{{item.site_name}}
						<view class="go m-l-8">
							<uv-icon color="#555555" size="8" name="arrow-right"></uv-icon>
						</view>
					</view>
				</view>
				<view class="acea-row row-middle m-t-28 row-between" v-for="(items,indexs) in item.goods_list"
					:key="indexs">
					<image :src="$util.img(items.sku_image)" class="w-140 h-140 bor-16" mode=""></image>
					<view class="widths h-140 acea-row row-between row-column">
						<view class="font-28 text-more-2">{{items.sku_name}}</view>
						<view class="acea-row row-middle row-between">
							<view class="points" v-if="info1.goodstype==3">
								￥
								<text>{{items.price}}</text>
								<text class="line">|</text>
								<text class="font-30 font-weight-6">{{items.point}}</text>
								积分
							</view>
							<view class="points" v-if="info1.goodstype==2">
								<text class="font-30 font-weight-6">{{items.point}}</text>
								积分
							</view>
							<view class="points" v-if="info1.goodstype==1">
								￥
								<text>{{items.price}}</text>

							</view>
							<view class="nums">x{{items.num}}</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="mians m-t-20">
			<view class="cuts acea-row row-middle row-between" v-if="info1.goodstype==3">
				<view :class="{'on1':check==0}" @click="check=0">
					<text>现金支付</text>
					<image src="/static/cl.png" v-if="check==0" mode=""></image>
				</view>
				<view :class="{'on1':check==1}" @click="check=1">
					<text>积分支付</text>
					<image src="/static/cr.png" v-if="check==1" mode=""></image>
					<image src="/static/icon5.png" class="icon5" mode=""></image>
				</view>
			</view>
			<view class="infos">
				<view class="acea-row row-middle row-between">
					<view class="font-26">商品总额</view>
					<view class="font-26 font-weight-6" v-if="check==1">{{orderinfo.pay_point}}积分</view>
					<view class="font-26 font-weight-6" v-else>¥{{orderinfo.pay_money}}</view>
				</view>
				<view class="acea-row row-middle row-between" v-if="check==1">
					<view class="font-26">可用积分</view>
					<view class="font-26 font-weight-6">{{orderinfo.member_account.point}}积分</view>
				</view>
				<view class="acea-row row-middle row-between" v-if="orderinfo.member_level_name!='普通会员'">
					<view class="font-26">会员折扣
					</view>
					<view class="font-26 font-weight-6">
						{{orderinfo.member_level_name}}享{{orderinfo.member_level_consume_discount}}折
					</view>
				</view>
				<view class="acea-row row-middle row-right topline">
					<text class="font-26 font-weight-6">合计：</text>
					<template v-if='check==0'>
						<text class="price">￥{{orderinfo.pay_money}}</text>
						<text class="linth font-26 m-l-12"
							v-if="orderinfo.member_level_name!='普通会员'">￥{{orderinfo.goods_money_yuan}}</text>
					</template>
					<template v-if='check==1'>
						<text class="price">{{orderinfo.pay_point}}积分</text>
						<text class="linth font-26 m-l-12"
							v-if="orderinfo.member_level_name!='普通会员'">{{orderinfo.pay_point_yuan}}积分</text>
					</template>
				</view>
			</view>

		</view>
		<view class="infos1">
			<view class="acea-row row-middle row-between">
				<view class="font-32 font-weight-4">支付方式</view>
				<view class="font-32 font-weight-4">{{check==0?'线上支付':'积分支付'}}</view>
			</view>
		</view>

		<view class="h-300"></view>
		<view class="btms iphone-x">
			<view class="acea-row row-middle row-between">
				<view class="" v-if="check==1">
					待付积分
					<text>{{orderinfo.pay_point}}</text>
				</view>
				<view v-else class="">
					待付金额
					<text class="font-24">¥</text>
					<text>{{orderinfo.pay_money}}</text>
				</view>
				<view class="btn1" @click="submit">提交订单</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		payment,
		ordercalculate,
		pointcreate,
		storecreate,
		huifupay
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				check: 0,
				info1: {},
				orderinfo: {}
			};
		},
		async onLoad() {
			this.info1 = uni.getStorageSync('orderCreateData')
			if (this.info1.goodstype == 1) {
				this.check = 0
			}
			if (this.info1.goodstype == 2) {
				this.check = 1
			}
		},
		async onShow() {
			await this.payment()
			await this.ordercalculate()
		},
		methods: {
			pointgi() {
				let delivery = JSON.stringify(this.orderinfo.delivery);
				let params = {
					sku_id: this.info1.sku_id || '',
					num: this.info1.num || '',
					pay_type: this.check == 0 ? 1 : 2,
					delivery: delivery,
					cart_ids: this.info1.cart_ids,
					video_id:this.info1.video_id||''
				}
				pointcreate(params).then(res => {
					if (res.code == 0) {
						uni.showToast({
							title: '购买成功',
							success: () => {
								uni.navigateTo({
									url: '/otherpages/order/order'
								})
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
			monpay() {
				let delivery = JSON.stringify(this.orderinfo.delivery);
				let params = {
					sku_id: this.info1.sku_id || '',
					num: this.info1.num || '',
					delivery: delivery,
					cart_ids: this.info1.cart_ids,
					video_id:this.info1.video_id||''
				}
				storecreate(params).then(res => {
					if (res.code == 0) {
						let params1 = {
							out_trade_no: res.data
						}
						huifupay(params1).then(resA => {
							if (resA.code == 0) {
								this.topay(resA)
							}
							else{
								uni.showToast({
									title:res.message,
									icon:'none'
								})
							}
						})
					}
				})
			},
			submit() {
				if (this.info1.goodstype == 1) {
					this.monpay()
					return
				}
				if (this.info1.goodstype == 2) {
					this.pointgi()
				}
				// if (this.info1.goodstype != 1) {

				// } else {

				// }
				if (this.check == 1 && this.info1.goodstype == 3) {
					this.pointgi()
					return
				}
				if (this.check == 0 && this.info1.goodstype == 3) {
					this.monpay()
					return
				}

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
						uni.showToast({
							title: '购买成功',
							success: () => {
								uni.navigateTo({
									url: '/otherpages/order/order'
								})
							}
						})
					},
					fail: err => {
						uni.showToast({
							title: err,
							icon:'none',
							success: () => {
								uni.navigateTo({
									url: '/otherpages/order/order'
								})
							}
						})
					}
				})
			},
			// 处理结算订单数据
			handlePaymentData() {
				this.orderinfo.delivery = {};
				this.orderinfo.invoice = {};
				this.orderinfo.coupon = {};
				this.orderinfo.buyer_message = {};
				this.orderinfo.is_balance = 0;
				this.orderinfo.pay_password = '';

				var data = this.orderinfo;

				let h = new Date().getHours().toString();
				let m = new Date().getMinutes().toString();
				if (h.length == 1) {
					h = '0' + h;
				}
				if (m.length == 1) {
					m = '0' + m;
				}
				let nowTime = h + ':' + m;
				var weeks = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];

				// 获取时间，必须是字符串,跟后端一致
				let index_weeks = ['0', '1', '2', '3', '4', '5', '6'];
				let curr_week = index_weeks[new Date().getDay()];

				Object.keys(data.shop_goods_list).forEach((key, index) => {
					let siteItem = data.shop_goods_list[key];

					// 店铺配送方式
					this.orderinfo.delivery[key] = {};

					if (siteItem.express_type[0] != undefined) {
						this.orderinfo.delivery[key].delivery_type = siteItem.express_type[0].name;
						this.orderinfo.delivery[key].delivery_type_name = siteItem.express_type[0].title;
						this.orderinfo.delivery[key].store_id = 0;

						// 如果默认配送方式是门店配送模拟点击门店tab选项
						if (siteItem.express_type[0].name == 'store') {
							if (siteItem.express_type[0].store_list[0] != undefined) {
								this.orderinfo.delivery[key].store_id = siteItem.express_type[0].store_list[0]
									.store_id;
							}
						}
					}

					if (data.is_virtual == 0 && siteItem.local_config) {

						// 是否显示时间选择
						if (siteItem.local_config.info && siteItem.local_config.info.time_is_open == 1) {
							this.orderinfo.delivery[key].showTimeBar = true;
							this.orderinfo.delivery[key].buyer_ask_delivery_time = nowTime;
							// 当日是否支持配送
							if (siteItem.local_config.info.time_week.length == 0 ||
								siteItem.local_config.info.time_week.length == 7 ||
								siteItem.local_config.info.time_week.indexOf(curr_week) > -1) {
								this.orderinfo.delivery[key].canLocalDelicery = true;
							} else {
								this.orderinfo.delivery[key].canLocalDelicery = false;
							}

							if (siteItem.local_config.info.time_type == 0) {
								this.orderinfo.delivery[key].deliveryWeek = "全天";
							} else if (siteItem.local_config.info.time_week.length > 0) {
								if (siteItem.local_config.info.time_week.length == 7) {
									this.orderinfo.delivery[key].deliveryWeek = "全天";
									this.orderinfo.delivery[key].showTime = false;
								} else {
									this.orderinfo.delivery[key].showTime = true;
									// 判断配送时间是连续还是间隔
									var timeWeek = siteItem.local_config.info.time_week;
									var is_interval = false; // 是否间隔
									for (var i = 0; i < timeWeek.length; i++) {
										if ((i + 1) < timeWeek.length) {
											var difference = timeWeek[i + 1] - timeWeek[i];
											if (difference > 1) {
												is_interval = true;
												break;
											}
										}
									}

									if (is_interval) {
										var temp = [];
										for (var i = 0; i < timeWeek.length; i++) {
											temp.push(weeks[timeWeek[i]]);
										}
										this.orderinfo.delivery[key].deliveryWeek = temp.join("、");
									} else {
										this.orderinfo.delivery[key].deliveryWeek = weeks[timeWeek[0]] + "至" +
											weeks[timeWeek[timeWeek.length -
												1]];
									}
								}
							} else {
								this.orderinfo.delivery[key].deliveryWeek = "店铺未设置配送时间";
							}

							// picker组件时间起始
							let start_time = siteItem.local_config.info.start_time;
							this.orderinfo.delivery[key].start_time = this.getTimeStr(start_time);

							let end_time = siteItem.local_config.info.end_time;
							this.orderinfo.delivery[key].end_time = this.getTimeStr(end_time);

							var current_time = new Date(this.$util.timeStampTurnTime(data.timestamp));
							var hour = current_time.getHours();
							var minute = current_time.getMinutes();

							var start_hour = parseInt(this.orderinfo.delivery[key].start_time.split(":")[0]);
							var start_minute = parseInt(this.orderinfo.delivery[key].start_time.split(":")[1]);

							var end_hour = parseInt(this.orderinfo.delivery[key].end_time.split(":")[0]);
							var end_minute = parseInt(this.orderinfo.delivery[key].end_time.split(":")[1]);

							// 检测当天是否能够配送，然后判断送达时间。不在配送时间当日不能下单，例：配送时间是周一到周五，那么周末不能下单，周一到周五可以下单
							if (this.orderinfo.delivery[key].canLocalDelicery) {

								// 判断是否全天
								if (!(start_hour == end_hour && start_minute == end_minute)) {

									// 当前时间早于配送时间，送达时间：开始时间~结束时间
									if ((hour < start_hour) || (hour == start_hour && minute < start_minute)) {
										this.orderinfo.delivery[key].buyer_ask_delivery_time = (start_hour
												.toString().length == 1 ? "0" +
												start_hour :
												start_hour) + ':' +
											(start_minute.toString().length == 1 ? "0" + start_minute :
												start_minute);
									}

									// if (((hour > start_hour && hour < end_hour) || (hour == start_hour && minute > start_minute) || (hour ==
									// 		start_hour && minute >= start_minute && hour < end_hour))) {
									// }

									// 当前时间晚于配送时间，送达时间隐藏，不能下单
									if ((hour > end_hour) || (hour == end_hour && minute > end_minute)) {
										this.orderinfo.delivery[key].canLocalDelicery = false;
									}
								}

							}

						} else {
							this.orderinfo.delivery[key].showTimeBar = false;
							this.orderinfo.delivery[key].deliveryWeek = "店铺未开启配送时间";
						}

					}

					siteItem.goods_list.forEach((v) => {
						if (typeof(v.sku_spec_format) == 'string') {
							if (v.sku_spec_format) {
								v.sku_spec_format = JSON.parse(v.sku_spec_format);
							} else {
								v.sku_spec_format = [];
							}
						}
					});

					// 店铺优惠券
					this.orderinfo.coupon[key] = {};
					if (siteItem.coupon_list[0] != undefined) {
						this.orderinfo.coupon[key].coupon_id = siteItem.coupon_list[0].coupon_id;
						this.orderinfo.coupon[key].coupon_money = siteItem.coupon_list[0].money;
						this.selectCouponId = siteItem.coupon_list[0].coupon_id;
						this.selectCouponMoney = siteItem.coupon_list[0].coupon_id;
					}

					siteItem.promotionExits = siteItem.promotion && Object.keys(siteItem.promotion).length > 0;

					this.orderinfo.buyer_message[key] = '';
				});

				if (data.is_virtual) this.orderinfo.member_address = {
					mobile: ''
				};

				// 平台优惠券
				if (data.platform_coupon_list.length > 0) {
					data.platform_coupon_id = data.platform_coupon_list[0].platformcoupon_id;
					data.platform_coupon_money = data.platform_coupon_list[0].money;

					this.orderinfo.platform_coupon_id = data.platform_coupon_list[0].platformcoupon_id;
					this.orderinfo.platform_coupon_money = data.platform_coupon_list[0].money;

					this.selectPlatCouponId = data.platform_coupon_list[0].platformcoupon_id;
					this.selectPlatCouponMoney = data.platform_coupon_list[0].money;
				}

				Object.assign(data, this.orderinfo);
				this.ordercalculate();
			},
			async payment() {
				let params = {
					sku_id: this.info1.sku_id || '',
					num: this.info1.num || '',
					cart_ids: this.info1.cart_ids
				}
				await payment(params).then(res => {
					this.orderinfo = res.data
					this.handlePaymentData()
				})
			},
			async ordercalculate() {
				let params = {
					sku_id: this.info1.sku_id || '',
					num: this.info1.num || '',
					cart_ids: this.info1.cart_ids
				}
				await ordercalculate(params).then(res => {
					if (res.code >= 0) {
						this.orderinfo.member_address = res.data.member_address;
						this.orderinfo.delivery_money = res.data.delivery_money;
						this.orderinfo.coupon_money = res.data.coupon_money;
						this.orderinfo.invoice_money = res.data.invoice_money;
						this.orderinfo.invoice_delivery_money = res.data.invoice_delivery_money;
						this.orderinfo.promotion_money = res.data.promotion_money;
						this.orderinfo.order_money = res.data.order_money;
						this.orderinfo.balance_money = res.data.balance_money;
						this.orderinfo.pay_money = res.data.pay_money;
						this.orderinfo.goods_money = res.data.goods_money;
						this.orderinfo.platform_coupon_money = res.data.platform_coupon_money;
						Object.keys(res.data.shop_goods_list).forEach((key, index) => {
							let siteItem = res.data.shop_goods_list[key];
							this.orderinfo.shop_goods_list[key].order_money = siteItem
								.order_money;
							this.orderinfo.shop_goods_list[key].pay_money = siteItem.pay_money;
							this.orderinfo.shop_goods_list[key].coupon_money = siteItem
								.coupon_money;
							this.orderinfo.shop_goods_list[key].invoice_money = siteItem
								.invoice_money;
							this.orderinfo.shop_goods_list[key].invoice_delivery_money =
								siteItem.invoice_delivery_money;
						});
						this.$forceUpdate();
					} else {
						this.$util.showToast({
							title: res.message
						});
					}
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

			>view:first-child {
				font-weight: 400;
				font-size: 24rpx;
				color: #F54040;

				text {
					font-size: 48rpx;
					color: #F54040;
					font-weight: bold;
				}
			}

			>view:last-child {
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
			.icon5{
				position: relative;
				width: 32rpx;
				height: 28rpx;
				margin-left: 12rpx;
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