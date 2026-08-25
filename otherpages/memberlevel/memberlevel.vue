<template>
	<view>
		<view class="part">
			<view class="acea-row row-middle">
				<image src="/static/avatar.png" class="w-74 h-74 bor-74" v-if="!infos.headimg" mode=""></image>
				<image :src="$util.img(infos.headimg)" class="w-74 h-74 bor-74" v-else mode=""></image>
				<view class="m-l-16">
					<view class="font-32">{{infos.nickname}}</view>
					<view class="font-26">恭喜您已是{{infos.member_level_name}}</view>
				</view>
			</view>
			<view class="">
				<image src="/static/ob.png" v-if="infos.member_level==1" mode=""></image>
				<image src="/static/ob1.png" v-if="infos.member_level==2" mode=""></image>
				<image src="/static/ob2.png" v-if="infos.member_level==3" mode=""></image>
				<image src="/static/ob3.png" v-else-if="infos.member_level==4" mode=""></image>
				<image src="/static/lines.png" mode=""></image>
				<view class="relative font-40 font-weight-6" :class="{fcolor:infos.member_level==4}">
					{{infos.member_level_name}}
				</view>
				<view class="font-weight-6 m-t-12 relative " :class="{fcolor:infos.member_level==4}"><text
						class="font-weight-4 m-r-6 relative"
						:class="{fcolor:infos.member_level==4}">积分</text>{{infos.point}}</view>
				<view class="w-360 m-t-8">
					<!-- <uv-line-progress :percentage="80" :showText='false' activeColor="#FFFFFF"
						inactiveColor="rgba(255, 255, 255, 0.16)" height='8rpx'></uv-line-progress> -->
				</view>
				<!-- <view class="acea-row row-middle relative m-t-12" :class="{fcolor:infos.member_level==4}">
					当前可享超多会员权益
					<uv-icon name="arrow-right" :color="true?'#FAD5B4':'#fff'" size="12"></uv-icon>
				</view> -->
			</view>
		</view>
		<view class="">
			<scroll-view scroll-x="true" class="scrools" scroll-left="30">
				<view v-for="(item,index) in levels" class="items" :class="{on:index==check}" @click="check=index"
					:key="index">
					<view class="">
						<view class="acea-row row-middle">
							<image src="/static/vip1.png" v-if="item.level_id==2" class="w-32 h-32" mode=""></image>
							<image src="/static/vip2.png" v-else-if="item.level_id==3" class="w-32 h-32" mode="">
							</image>
							<image src="/static/vip3.png" v-else-if="item.level_id==4" class="w-32 h-32" mode="">
							</image>
							{{item.level_name}}
						</view>
						<view class="m-t-24">仅需<text>{{item.price}}</text>元</view>
						<view class="">享{{item.consume_discount}}折优惠</view>
					</view>
					<view class="">开通即送<text>{{item.fan_point}}</text>积分</view>
				</view>
			</scroll-view>
		</view>
		<view class="part2">
			<view class="acea-row row-middle row-center m-b-48">
				<image src="/static/画板 5.png" class="w-40 h-40" mode=""></image>
				<text class="m-r-32 m-l-32">{{levels[check].level_name}}权益</text>
				<image src="/static/画板 1.png" class="w-40 h-40" mode=""></image>
			</view>
			<view class="acea-row row-middle">
				<view class="">
					<image src="/static/img3.png" class="w-100 h-100" mode=""></image>
					<view class="">{{levels[check].consume_discount}}折优惠</view>
				</view>
				<view class="">
					<image src="/static/img4.png" class="w-100 h-100" mode=""></image>
					<view class="text-more">赠送{{levels[check].fan_point}}积分</view>
				</view>
				<view class="">
					<image src="/static/img5.png" class="w-100 h-100" mode=""></image>
					<view class="">专属客服</view>
				</view>
				<view class="">
					<image src="/static/img6.png" class="w-100 h-100" mode=""></image>
					<view class="">免邮费</view>
				</view>
			</view>
		</view>
		<view class="acea-row row-middle rulbtn" @click="open">
			查看会员专属规则
			<uv-icon name="arrow-right" color="#fff" size="16"></uv-icon>
		</view>
		<view class="h-68"></view>
		<view class="btn3">
			<image src="/static/btn3.png" @click="sbumit" mode=""></image>
		</view>
		<uv-popup ref="popup" bg-color="#ffffff00">
			<view class="mains">
				<view class="acea-row row-middle row-center p-t-48 m-b-20">
					<image src="/static/llline1.png" class="w-52 h-4" mode=""></image>
					<text>会员专属规则介绍</text>
					<image src="/static/rline1.png" class="w-52 h-4" mode=""></image>
				</view>
				<scroll-view scroll-y="true" style="height: 480rpx;">
					<view class="m-r-52 m-l-52">{{levels[check].remark}}</view>
				</scroll-view>
				<view class="closes" @click="closeds">
					<uv-icon name="close-circle" size="48"></uv-icon>
				</view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
	import {
		levelList,
		userinfo,
		leveordercreate,
		huifupay,
		memberlevelpay
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				infos: {},
				levels: [],
				check: 0
			};
		},
		onLoad() {},
		onShow() {
			this.levelList()
			this.userinfo()
		},
		methods: {
			closeds() {
				this.$refs.popup.close()
			},
			open() {
				this.$refs.popup.open('center')
			},
			sbumit() {
				let params = {
					website_id: uni.getStorageSync('website_id'),
					level_id: this.levels[this.check].level_id
				}
				leveordercreate(params).then(res => {
					if (res.code == 0) {
						this.pays(res.data)
					} else {
						uni.showToast({
							title: res.data.message,
							icon: 'none'
						})
					}
				})
			},
			pays(e) {
				let params = {
					out_trade_no: e,
				}
				memberlevelpay(params).then(res => {
					uni.requestPayment({
						provider: 'wxpay',
						timeStamp: res.data.timeStamp,
						nonceStr: res.data.nonceStr,
						package: res.data.package,
						signType: res.data.signType,
						paySign: res.data.paySign,
						success: resA => {
							this.userinfo()
						},
						fail: err => {
							console.log(err);
						}
					})
				})
			},
			levelList() {
				levelList({
					website_id: uni.getStorageSync('website_id')
				}).then(res => {
					this.levels = res.data
				})
			},
			userinfo() {
				userinfo().then(res => {
					this.infos = res.data
				})
			},
		}
	}
</script>

<style lang="scss">
	.mains {
		width: 640rpx;
		height: 688rpx;
		background: linear-gradient(180deg, #FCEEDD 0%, #FFFFFF 52%, #FFFFFF 100%);
		border-radius: 40rpx;
		border: 3rpx solid #FFFFFF;
		position: relative;

		.closes {
			position: absolute;
			bottom: -50rpx;
			left: 50%;
			transform: translateX(-50%);
			padding: 10rpx;
			border-radius: 50%;
			background: #fff;
		}

		>view:first-child {
			image {
				width: 52rpx;
				height: 4rpx;
			}

			text {
				font-weight: 600;
				font-size: 40rpx;
				color: #472B1D;
				margin: 0 20rpx;
			}
		}
	}

	.rulbtn {
		margin: 0 auto;
		width: fit-content;
		padding: 0 32rpx;
		height: 64rpx;
		background: #272A33;
		border-radius: 32rpx;
		font-weight: 400;
		font-size: 26rpx;
		color: #FFFFFF;
	}

	.part2 {
		margin: 64rpx 46rpx;

		>view:first-child {
			text {
				font-weight: 600;
				font-size: 36rpx;
				color: #F5B67F;
			}
		}

		>view:last-child {
			text-align: center;

			>view {
				width: 25%;

				image {
					margin-bottom: 16rpx;
				}
			}

			view {
				font-weight: 400;
				font-size: 24rpx;
				color: #FFFFFF;
			}
		}
	}

	.scrools {
		width: 100%;
		white-space: nowrap;
		position: relative;
		z-index: 999;

		.items {
			background: #272A33;
			width: auto;
			height: 242rpx;
			margin-right: 16rpx;
			display: inline-block;
			border-radius: 16rpx;
			overflow: hidden;

			>view:first-child {
				width: auto;
				height: 192rpx;
				background: #111317;
				border-radius: 16rpx;
				position: relative;

				>view:first-child {
					width: 176rpx;
					padding-left: 16rpx;
					font-weight: 400;
					font-size: 24rpx;
					color: #FFFFFF;
					box-sizing: border-box;
					height: 50rpx;
					background: linear-gradient(90deg, #272A33 0%, #48494B 100%);
					clip-path: polygon(0% 0%, 100% 0%, 95% 100%, 0% 100%);
					border-radius: 16rpx;

					image {
						margin-right: 8rpx;
					}
				}

				>view:nth-child(2),
				>view:nth-child(3) {
					font-weight: 600;
					margin-left: 20rpx;
					margin-right: 20rpx;
					font-size: 24rpx;
					color: #FAD5B4;

					text {
						font-size: 48rpx;
						color: #FAD5B4;
						font-weight: bold;
					}
				}
			}

			>view:last-child {
				font-weight: 400;
				font-size: 18rpx;
				color: #B2B6C2;
				padding: 0 20rpx;
				line-height: 50rpx;

				text {
					font-weight: 600;
					font-size: 24rpx;
					color: #B2B6C2;
				}
			}
		}

		.items:first-child {
			margin-left: 32rpx;
		}

		.items:last-child {
			margin-right: 32rpx;
		}

		.on {
			>view:first-child {
				background: linear-gradient(180deg, #FFE7CC 0%, #FFB48E 100%) !important;

				>view:first-child {
					color: #44687A;
					background: linear-gradient(90deg, rgba(255, 255, 255, 0.48) 0%, rgba(255, 255, 255, 0.68) 100%);
				}

				>view:nth-child(2),
				>view:nth-child(3) {
					color: #222222;

					text {
						color: #222222;
					}
				}
			}
		}
	}

	page {
		background: #181A1F;
	}

	.btn3 {
		margin: 0 50rpx;
		height: 148rpx;
		position: relative;
		z-index: 999;

		>image {
			width: 100%;
			height: 100%;
		}
	}

	.part {
		margin: 36rpx 32rpx 0 32rpx;
		box-sizing: border-box;
		height: 415rpx;
		position: relative;

		view,
		text {
			color: #fff;
			position: relative;
			z-index: 222;
		}

		>view:last-child {
			position: absolute;
			top: 60rpx;
			height: 360rpx;
			width: 100%;
			box-sizing: border-box;
			padding-top: 92rpx;
			padding-left: 28rpx;

			>image:first-child {
				width: 100%;
				height: 100%;
				position: absolute;
				top: 0;
				left: 0;
				z-index: -1;
			}

			>image:nth-child(2) {
				position: absolute;
				width: 100%;
				height: 350rpx;
				bottom: -220rpx;
				left: 0;
				z-index: 1;
			}
		}
	}
</style>