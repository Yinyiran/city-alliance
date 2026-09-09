<template>
	<view>
		<uv-sticky bg-color="#fff">
			<nvabar type='1' title='商品详情' titleStyle='color:#222'></nvabar>
		</uv-sticky>
		<view class="swipers" v-if="info&&info.goods_image">
			<swiper :indicator-dots="false" :autoplay="true" v-if="info.goods_image" circular="true" :current="current"
				@change="onchange" :interval="3000" :duration="1000">
				<swiper-item v-for="item in info.goods_image.split(',')">
					<view class="swiper-item">
						<image :src="$util.img(item)" mode=""></image>
					</view>
				</swiper-item>
			</swiper>
			<view class="">{{current}}/{{info.goods_image.split(',').length}}</view>
		</view>
		<view class="part1">
			<view class="acea-row row-between">
				<view class="" v-if="type==1">
					<text class="font-22">特惠</text>
					<text class="font-weight">￥</text>
					{{info.price}}
					<text>￥{{info.market_price}}</text>
				</view>
				<view class="acea-row" v-else style="flex-wrap: nowrap;align-items: baseline;">
					<template v-if="type==3">
						<text class="font-22">特惠</text>
						<text class="font-weight">￥</text>
						{{info.price}}
						<view class="w-20"></view>
					</template>
					<text class="font-22">积分</text>
					<text class="font-weight"></text>
					{{info.point}}
					<text></text>
					<view class="baoyou" v-if="goods_type==3">全国包邮</view>
					<!-- <text>￥{{info.market_price}}</text> -->
				</view>
				<view class="">已售 {{info.sale_num}}</view>
			</view>
			<view class="font-36 font-weight-6 m-t-20">
				{{info.goods_name}}
			</view>
			<!-- <view class="tags" v-if="goods_type==3">
				全国包邮
			</view> -->
			<view class="tags" v-if="goods_type!=3">
				门店核销
			</view>
			<view class="acea-row row-middle row-between m-t-22">
				<view class="acea-row row-middle font-32" style="color: #C24E00;">
					<image :src="$util.img(shop_info.logo)" v-if="shop_info.logo" class="h-48 w-180 m-r-12"
						mode="heightFix"></image>
					{{shop_info.site_name}}
				</view>
			</view>
			<view class="acea-row row-middle row-right m-t-20">
				<view class="acea-row row-middle shares" @click="poster()">
					<view class="">+{{yao_point}}分</view>
					<image src="/static/share.png" class="w-24 h-24 m-l-16 m-r-8" mode=""></image>
					分享
				</view>
			</view>
		</view>
		<view class="part2">
			<view class="font-36 font-weight-6 m-b-24">详细信息</view>
			<view class="acea-row row-middle row-between">
				<view class="acea-row row-middle font-26">
					<image src="/static/shop1.png" class="w-26 h-26 m-r-12" mode=""></image>
					门店信息
				</view>
				<view class="font-26 font-weight-6">{{shop_info.site_name}}</view>
			</view>
			<view class="acea-row row-middle row-between m-t-16">
				<view class="acea-row row-middle font-26">
					<image src="/static/clock.png" class="w-26 h-26 m-r-12" mode=""></image>
					营业时间
				</view>
				<view class="font-26 font-weight-6">{{shop_info.open_date}}</view>
			</view>
			<view class="acea-row row-middle row-between m-t-16" v-if="type==1&&shop_info.address">
				<view class="acea-row row-middle font-26">
					<image src="/static/map4.png" class="w-26 h-26 m-r-12" mode=""></image>
					店面地址
				</view>
				<view class="acea-row row-middle">
					<view class="w-378 text-more m-r-12 font-26 font-weight-6">{{shop_info.address}}</view>
					<view class="bn1" @click="daohang">
						<image src="/static/map6.png" class="w-22 h-22 m-r-4" mode=""></image>
						一键导航
					</view>
				</view>
			</view>
			<view class="acea-row row-middle row-between m-t-16" v-if="type==1&&shop_info.telphone">
				<view class="acea-row row-middle font-26">
					<image src="/static/phone4.png" class="w-26 h-26 m-r-12" mode=""></image>
					门店电话
				</view>
				<view class="acea-row row-middle">
					<view class="w-378 text-more m-r-12 font-26 font-weight-6">{{shop_info.telphone}}</view>
					<view class="bn1" @click="calls">
						<image src="/static/phone3.png" class="w-22 h-22 m-r-4" mode=""></image>
						一键拨打
					</view>
				</view>
			</view>
		</view>
		<view class="p-32 m-t-20 bor-24" style="background: #fff;">
			<view class="font-36 font-weight-6 m-b-24">商品详情</view>
			<rich-text :nodes="$util.replaceImg(info.goods_content)"></rich-text>
		</view>
		<view class="h-300"></view>
		<view class="btms iphone-x">
			<view class="acea-row row-middle row-between">
				<view class="w-120 h-86" @click="cartAdd">
					<image src="/static/car.png" class="w-40 h-40" mode=""></image>
					<view class="">加入购物车</view>
				</view>
				<view class="btn btn1" @click="buyit">立即购买
					<view class="text-more" v-if="info.fan_point>0&&goods_type!=2">积分+{{parseInt(info.fan_point)}}</view>
				</view>
				<view class="btn" @click="buyit" v-if="goods_type!=1">立即兑换</view>
			</view>
		</view>
		<login ref="login"></login>
		<ns-goods-sku ref="goodsSku" :goodstype='goods_type' :video_id='video_id' @refresh="refreshGoodsSkuDetail" :goods-detail="info"
			:max-buy="info.max_buy" :min-buy="info.min_buy"></ns-goods-sku>
		<!-- <uv-popup mode="bottom" :round='10' ref="popup">
			<view class="acea-row row-middle shares1 row-around">
				<view class="">
					<image src="/static/wx1.png" class="w-96 h-96" mode=""></image>
					<view class="">分享微信好友</view>
				</view>
				<view class="" @click="">
					<image src="/static/wx2.png" class="w-96 h-96" mode=""></image>
					<view class="">分享朋友圈</view>
				</view>
				<view class="" @click="poster">
					<image src="/static/wx3.png" class="w-96 h-96" mode=""></image>
					<view class="">下载海报</view>
				</view>
			</view>
		</uv-popup> -->
	</view>
</template>

<script>
	import nsGoodsSku from '@/components/ns-goods-sku/ns-goods-sku.vue';
	import {
		Gdetail,
		cartAdd,
		modifyclicks,
		invitation,
		goodsposter,
		userinfo
	} from "@/common/js/api.js"
	export default {
		components: {
			nsGoodsSku
		},
		computed: {
			goods_type() {
				let goods_type = this.info.goods_type
				let is_admin = this.info.is_admin
				if (is_admin == 1) {
					return 3
				}
				if (goods_type == 0) {
					return 1
				}
				if (goods_type == 1) {
					return 2
				}
				return 1
			},
		},
		data() {
			return {
				current: 1,
				yao_point: 0,
				id: "",
				info: {},
				shop_info: {},
				token: "",
				type: 1,
				member_id: "",
				video_id:""
			};
		},
		onShow() {
			this.token = uni.getStorageSync('token')
			if (this.token) {
				userinfo().then(res => {
					this.member_id = res.data.member_id
				})
			}
		},
		onLoad(res) {
			if (res.type) {
				this.type = res.type
			}
			if(res.video_id){
				this.video_id=res.video_id
			}
			if (res.member_id) {
				uni.setStorageSync('invite_code', res.member_id)
			}
			if (res.sku_id) {
				this.id = res.sku_id
			} else {
				this.id = res.id
			}
			// 小程序扫码进入
			if (res.scene) {
				var sceneParams = decodeURIComponent(res.scene);
				sceneParams = sceneParams.split('&');
				if (sceneParams.length) {
					sceneParams.forEach(item => {
						if (item.indexOf('sku_id') != -1) this.id = item.split('-')[0];
						if (item.indexOf('suid') != -1) uni.setStorageSync('invite_code', item.split('-')[1]);
					});
				}
			}
			this.getinfo()
			this.invitation()
			this.modifyclicks()
		},
		onShareAppMessage(res) {
			var path = '/otherpages/goods_detail/goods_detail?id=' + this.id;
			const site_id = uni.getStorageSync('user_site_id') || uni.getStorageSync('share_site_id') || '';
			if (site_id) path += '&site_id=' + site_id;
			if (this.member_id) path += '&suid=' + this.member_id;
			return {
				title: this.info.sku_name,
				imageUrl: this.$util.img(this.info.sku_image),
				path: path,
				success: res => {},
				fail: res => {}
			};
		},
		// #ifdef MP-WEIXIN
		// 分享到微信朋友圈
		onShareTimeline() {
			let query = 'sku_id=' + this.skuId;
			const site_id = uni.getStorageSync('user_site_id') || uni.getStorageSync('share_site_id') || '';
			if (site_id) query += '&site_id=' + site_id;
			if (this.member_id) query += '&suid=' + this.member_id;
			return {
				title: this.info.sku_name,
				query: query,
				imageUrl: this.$util.img(this.info.sku_image)
			};
		},
		// #endif
		methods: {
			poster() {
				let params = {
					qrcode_param: JSON.stringify({
						sku_id: this.id,
						suid: this.member_id
					}),
					page: '/otherpages/goods_detail/goods_detail'
				}
				goodsposter(params).then(res => {
					if (res.code == 0) {
						this.shares1(res.data.path)
					} else {
						uni.showToast({
							title: res.message,
							icon: 'none'
						})
					}
				})
			},
			shares1(e) {
				wx.downloadFile({
					url: this.$util.img(e),

					success: res => {
						wx.showShareImageMenu({
							path: res.tempFilePath,
							needShowEntrance: true,
							entrancePath: "/otherpages/goods_detail/goods_detail",
							fail: e => {
								console.log(e);
							}
						})
					}
				})
				// wx.shareFileMessage()
				// uni.share({
				// 	provider: "weixin",
				// 	scene: "WXSceneTimeline",
				// 	type: 1,
				// 	summary: "我正在使用HBuilderX开发uni-app，赶紧跟我一起来体验！",
				// 	success: function (res) {
				// 		console.log("success:" + JSON.stringify(res));
				// 	},
				// 	fail: function (err) {
				// 		console.log("fail:" + JSON.stringify(err));
				// 	}
				// });
			},
			openshare() {
				this.$refs.popup.open()
			},
			invitation() {
				invitation().then(res => {
					this.yao_point = res.data.yao_point
				})
			},
			refreshGoodsSkuDetail(goodsSkuDetail) {
				Object.assign(this.info, goodsSkuDetail);
				// this.goodsSkuDetail.unit = this.goodsSkuDetail.unit || "件";
				// if (this.$refs.goodsPromotion) this.$refs.goodsPromotion.refresh(this.goodsSkuDetail.goods_promotion);
				// console.log(goodsSkuDetail);
				// 解决轮播图数量不一致时，切换到第一个
				// if (this.swiperCurrent > this.info.sku_images.length) {
				// 	this.swiperAutoplay = true;
				// 	this.swiperCurrent = 1;
				// 	setTimeout(() => {
				// 		this.swiperAutoplay = false;
				// 	}, 40);
				// }
			},
			buyit() {
				this.$refs.goodsSku.show("buy_now", () => {
					// this.getCartCount();
				});
			},
			daohang() {
				uni.openLocation({
					latitude: Number(this.shop_info.latitude),
					longitude: Number(this.shop_info.longitude),
					name: this.shop_info.address
				})
			},
			calls() {
				uni.makePhoneCall({
					phoneNumber: this.shop_info.telphone
				})
			},
			cartAdd() {
				if (!this.token) {
					this.$refs.login.open()
					return
				}
				this.$refs.goodsSku.show("join_cart", () => {});
				// let params = {
				// 	sku_id: this.id,
				// 	num:1
				// }
				// cartAdd(params).then(res => {
				// 	if (res.code == 0) {
				// 		uni.showToast({
				// 			title: '加入成功'
				// 		})
				// 	}
				// })
			},
			modifyclicks() {
				let params = {
					sku_id: this.id
				}
				modifyclicks(params)
			},
			getinfo() {
				let params = {
					sku_id: this.id
				}
				Gdetail(params).then(res => {
					this.info = res.data.goods_sku_detail
					this.shop_info = res.data.shop_info
					if (this.info.sku_spec_format) this.info.sku_spec_format = JSON.parse(this.info
						.sku_spec_format);
					if (this.info.goods_spec_format) this.info.goods_spec_format = JSON.parse(this.info
						.goods_spec_format);
				})
			},
			onchange(e) {
				console.log(e);
			}
		}
	}
</script>

<style lang="scss">
	.tags {
		background: #FFEBEB;
		border-radius: 4rpx;
		text-align: center;
		line-height: 28rpx;
		font-weight: 600;
		color: #F51D1D;
		margin-right: 8rpx;
		width: fit-content;
		margin-top: 10rpx;
		padding: 10rpx 15rpx;
	}

	.shares1 {
		text-align: center;
		padding: 76rpx 0 56rpx 0;

		>view {


			>view {
				font-weight: 400;
				font-size: 24rpx;
				color: #999999;

			}
		}
	}

	.baoyou {
		display: inline-block;
		width: 104rpx;
		height: 32rpx;
		background: #FA7F28;
		border-radius: 4rpx;
		font-weight: 400;
		font-size: 22rpx;
		color: #FFFFFF;
		text-align: center;
		line-height: 32rpx;
		margin-left: 20rpx;
	}

	.btms {
		position: fixed;
		bottom: 0;
		left: 0;
		width: 100vw;
		background: #fff;
		z-index: 99;

		>view {
			margin: 20rpx 30rpx;
			padding-bottom: 20rpx;
			text-align: center;

			.btn {
				background: #F55606;
				border-radius: 48rpx;
				line-height: 96rpx;
				font-weight: 600;
				font-size: 32rpx;
				color: #FFFFFF;
				width: 259rpx;
			}

			.btn1 {
				position: relative;
				background: #F5AB18;

				>view {
					position: absolute;
					height: 28rpx;
					width: fit-content !important;
					background: #F54040;
					border-radius: 16rpx;
					border: 2rpx solid #FFFFFF;
					padding: 0 8rpx;
					font-weight: 500;
					font-size: 20rpx;
					color: #FFFFFF;
					left: 50%;
					transform: translateX(-50%);
					top: -15rpx;
				}
			}
		}
	}

	.bn1 {
		font-weight: 400;
		font-size: 20rpx;
		color: #F55606;
		padding: 0 8rpx;
		height: 32rpx;
		background: #FFF3ED;
		border-radius: 4rpx;
	}

	.part2 {
		background: #FFFFFF;
		border-radius: 24rpx;
		padding: 32rpx;
		margin-top: 20rpx;
	}

	.shares {
		background: #FFEBE0;
		border-radius: 24rpx;
		padding: 8rpx;
		font-weight: 400;
		font-size: 24rpx;
		color: #F55606;
		padding-right: 16rpx;

		>view {
			font-weight: 400;
			font-size: 20rpx;
			color: #FFFFFF;
			background: #FF7733;
			border-radius: 24rpx;
			line-height: 32rpx;
			padding: 0 10rpx;
		}
	}

	.part1 {
		background: #FFFFFF;
		margin-top: -30rpx;
		position: relative;
		z-index: 5;
		border-radius: 24rpx;
		padding: 32rpx;

		>view:first-child {
			align-items: baseline;

			>view:first-child {
				font-size: 48rpx;
				font-weight: bold;
				color: #F51D1D;

				text {
					color: #F51D1D;
					font-weight: 400;
				}

				>text:last-child {
					font-weight: 400;
					font-size: 24rpx;
					color: #999999;
					text-decoration: line-through;
					margin-left: 16rpx;
				}
			}

			>view:last-child {
				font-weight: 400;
				font-size: 24rpx;
				color: #999999;
			}
		}
	}

	.swipers {
		width: 100vw;
		height: 100vw;
		position: relative;

		swiper,
		.swiper-item,
		image {
			width: 100%;
			height: 100%;
		}

		>view:last-child {
			position: absolute;
			right: 20rpx;
			bottom: 50rpx;
			height: 40rpx;
			background: rgba(0, 0, 0, 0.4);
			border-radius: 20rpx;
			font-weight: 600;
			font-size: 24rpx;
			color: #FFFFFF;
			z-index: 5;
			line-height: 40rpx;
			padding: 0 18rpx;
		}
	}
</style>