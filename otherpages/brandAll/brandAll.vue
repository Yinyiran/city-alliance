<template>
	<view>
		<!-- <uv-sticky> -->
			<nvabar title='全部品牌'></nvabar>
		<!-- </uv-sticky> -->
		<image src="/static/bg1.png" class="bgs" mode=""></image>
		<view class="part1">
			<image src="/static/bg6.png" mode=""></image>
			<image src="/static/t.png" mode=""></image>
			<button open-type="contact" class="consult-btn">入驻咨询</button>
			<view class="">{{shop_num}}家</view>
		</view>
		<view class="container">
			<view class="acea-row row-middle row-center titles">
				<image src="/static/lline.png" mode=""></image>
				<view class="">商家距离推荐</view>
				<image src="/static/rline.png" mode=""></image>
			</view>
			<view v-for="(item,index) in list" class="m-b-20" @click="godetail(item.store_id)" :key="index">
				<view class="p-24">
					<view class="acea-row row-middle row-between">
						<view class="logos">
							<image :src="$util.img(item.store_image)" mode="widthFix"></image>
							<view class="">
								<image src="/static/rank1.png" v-if="index==0" mode=""></image>
								<image src="/static/rank2.png" v-else-if="index==1" mode=""></image>
								<image src="/static/rank3.png" v-else-if="index==2" mode=""></image>
								<image src="/static/rank.png" v-else mode=""></image>
								<text v-if="index>2">{{index+1}}</text>
							</view>
						</view>
						<view class="rights">
							<view class="acea-row row-middle row-between">
								<view class="font-28 font-weight-6 acea-row row-middle">
									<image src="/static/shop.png" class="w-28 h-28 m-r-8" mode=""></image>
									<view class="text-more font-28" style="width: 200rpx;">{{item.store_name}}</view>
									<image src="/static/go3.png" class="w-26 h-26 m-l-8" mode=""></image>
								</view>
								<view class="font-22" style="color: #555;">{{item.distance_value}}km</view>
							</view>
							<view class="font-22 m-t-8 m-b-16 h-36"></view>
							<view class="acea-row row-middle row-between">
								<view class="w1">
									<view class="acea-row row-middle m-b-12 text-more">
										<image src="/static/clock.png" class="w-22 h-22 m-r-4" mode="">
										</image>
										营业时间 {{item.open_date}}
									</view>
									<view class="acea-row row-middle ">
										<image src="/static/map4.png" class="w-22 h-22 m-r-4" mode="">
										</image>
										<view class="w-200 text-more">{{item.address}}</view>
									</view>
								</view>
								<view class="w2 acea-row row-middle">
									<view class="m-r-16" @click.stop="daohang(item)">
										<image src="/static/map2.png" mode=""></image>
									</view>
									<view class="" @click.stop="calls(item)">
										<image src="/static/phone.png" mode=""></image>
									</view>
								</view>
							</view>
						</view>
					</view>

				</view>
			</view>

		</view>
	</view>
</template>

<script>
	import {
		websiteInfo,
		storeList
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				shop_num: 0,
				page: 1,
				last_page: 1,
				list: []
			};
		},
		onShow() {
			this.getinfo()
			this.getlist()
		},
		onReachBottom() {
			if (this.last_page > this.page) {
				this.page++
				this.getlist()
			}
		},
		methods: {
			godetail(e){
				uni.navigateTo({
					url:'/otherpages/storeDetail/storeDetail?id='+e
				})
			},
			daohang(e) {
				uni.openLocation({
					latitude: Number(e.latitude),
					longitude: Number(e.longitude),
					name: e.address
				})
			},
			calls(e) {
				uni.makePhoneCall({
					phoneNumber: e.telphone
				})
			},
			getlist() {
				let params = {
					page: this.page,
					page_size: 30
				}
				storeList(params).then(res => {
					this.list = this.page > 1 ? this.list.concat(...res.data.list) : res.data.list
					this.last_page = res.data.page_count
				})
			},
			getinfo() {
				let params = {
					website_id: uni.getStorageSync('website_id')
				}
				websiteInfo(params).then(res => {
					this.shop_num = res.data.shop_num
				})
			}
		}
	}
</script>

<style lang="scss">
	page {
		background: #fff;
	}

	.container {
		position: relative;
		z-index: 999;
		margin-top: 32rpx;
		background: #fff;
		border-radius: 24rpx 24rpx 0rpx 0rpx;
		padding: 32rpx;
		box-sizing: border-box;

		.titles {
			view {
				font-weight: 600;
				font-size: 32rpx;
				color: #222222;
				margin: 0 24rpx;
			}

			image {
				width: 129rpx;
				height: 4rpx;
			}
		}

		.info1 {
			font-weight: 400;
			font-size: 22rpx;
			color: #555555;
			background: #F5F5F5;
			border-radius: 12rpx;
			margin-top: 16rpx;
			height: 54rpx;
			padding: 0 20rpx;

			.line {
				width: 1rpx;
				height: 22rpx;
				background: #DCDFEB;
				margin: 0 20rpx;
			}
		}

		.w1 {
			width: calc(100% - 152rpx - 16rpx);

			view {
				font-weight: 400;
				font-size: 22rpx;
				color: #999999;
				flex-wrap: nowrap;
			}
		}

		.w2 {
			view {
				width: 56rpx;
				height: 56rpx;
				background: #FFEBE0;
				border-radius: 12rpx;
				display: flex;
				align-items: center;
				justify-content: center;
			}

			>view:last-child {
				background: #E6FFEF;
			}

			image {
				width: 28rpx;
				height: 28rpx;
			}
		}

		.aaa {
			margin: 20rpx 32rpx;
		}

		.rights {
			width: calc(100% - 225rpx - 20rpx);
		}

		.logos {
			width: 225rpx;
			height: 180rpx;
			position: relative;

			>view {
				position: absolute;
				left: 0;
				top: 0;
				width: 58rpx;
				height: 64rpx;

				>image {
					width: 100%;
					height: 100%;
				}
				text{
					position: absolute;
					font-weight: 400;
					font-size: 32rpx;
					color: #222222;
					left: 50%;
					top: 50%;
					transform: translate(-50%,-50%);
					z-index: 5;
				}
			}

			>image:first-child {
				width: 100%;
				height: 100%;
				border-radius: 12rpx;
			}
		}

		::v-deep .uv-swipe-action-item {
			border-radius: 24rpx;
		}

		.whits {
			margin-top: 52rpx;

			>view {
				color: #FFFFFF;
			}
		}
	}

	.part1 {
		margin: 0 32rpx;
		margin-top: 20rpx;
		position: relative;
		height: 210rpx;

		>image:first-child {
			width: 100%;
			height: 100%;
			position: absolute;
			right: 0;
			top: 0;
			z-index: 1;
		}

		>image:nth-child(2) {
			position: absolute;
			top: 40rpx;
			left: 40rpx;
			width: 220rpx;
			height: 58rpx;
			z-index: 2;
		}

		.consult-btn {
			position: absolute;
			right: 40rpx;
			bottom: 40rpx;
			z-index: 2;
			background: linear-gradient(135deg, #FF6633, #FF3B30);
			color: #FFFFFF;
			font-size: 26rpx;
			font-weight: 500;
			padding: 0 32rpx;
			height: 60rpx;
			line-height: 60rpx;
			border-radius: 30rpx;
			box-shadow: 0 4rpx 14rpx rgba(255, 59, 48, 0.3);
			border: none;
			margin: 0;
			display: flex;
			align-items: center;
			justify-content: center;

			&::after {
				border: none;
			}

			&:active {
				opacity: 0.9;
				transform: scale(0.98);
			}
		}

		>view {
			position: absolute;
			bottom: 40rpx;
			left: 40rpx;
			font-weight: bold;
			font-size: 56rpx;
			color: #222222;
			z-index: 2;
		}
	}

	.bgs {
		position: absolute;
		width: 100vw;
		height: 440rpx;
		top: 0;
		left: 0;
		z-index: -1;
	}
</style>