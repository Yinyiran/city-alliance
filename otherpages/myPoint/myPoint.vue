<template>
	<view>
		<nvabar title='我的积分'></nvabar>
		<image src="/static/bg10.png" class="bg2s" mode=""></image>
		<view class="h-224 part1">
			<view class="">我的积分</view>
			<view class="">{{point}}</view>
			<view class="">可用积分<text>{{point}}</text></view>
			<!-- <view class="acea-row row-middle row-center">
				开通无感积分
				<image src="/static/go.png" mode=""></image>
			</view> -->
		</view>
		<view class="container">
			<uv-subsection :list="list1" :current="current" custom-style="height: 60rpx;border-radius: 30rpx;"
				custom-item-style="border-radius: 30rpx;" activeColor='#F55606' bgColor="#FFFFFF" mode="button"
				@change="change"></uv-subsection>
			<view class="mains">
				<view class="">积分明细</view>
				<view v-for="(item,index) in list" class="items m-t-24" :key="index">
					<view class="acea-row row-middle row-between m-b-16" v-if="item.point_type==1">
						<view class="acea-row row-middle">
							<image :src="$util.img(item.headimg)" v-if="item.headimg" class="w-56 h-56 bor-28 m-r-12"
								mode=""></image>
							<image src="/static/avatar.png" v-else class="w-56 h-56 bor-28 m-r-12" mode=""></image>
							<text class="font-28">{{item.nickname}}</text>
						</view>
						<text class="font-weight-6 font-30"
							:class="{fcolor:Number(item.account_data)<0}">{{item.account_data}}</text>
					</view>
					<view class="acea-row row-middle m-b-16 row-between" style="flex-wrap: nowrap;"
						v-else-if="item.point_type==2||item.point_type==3">
						<view class="acea-row row-middle">
							<image :src="$util.img(item.sku_image)" class="w-100 h-100 bor-16 m-r-12" mode=""></image>
							<view class="text-more-2 font-28 w-340">{{item.sku_name}}</view>
						</view>
						<view class="t2">
							<view :class="{fcolor:Number(item.account_data)<0}">{{item.account_data}}</view>
							<view class="">共{{item.num}}件</view>
						</view>
					</view>
					<view class="acea-row row-middle row-between m-b-16" v-else>
						<view class="acea-row row-middle">
							<image :src="$util.img(item.headimg)" v-if="item.headimg" class="w-56 h-56 bor-28 m-r-12"
								mode=""></image>
							<image src="/static/avatar.png" v-else class="w-56 h-56 bor-28 m-r-12" mode=""></image>
							<text class="font-28">{{item.nickname}}</text>
						</view>
						<text class="font-weight-6 font-30"
							:class="{fcolor:Number(item.account_data)<0}">{{item.account_data}}</text>
					</view>
					<uv-line></uv-line>
					<view class="acea-row row-middle row-between m-t-16">
						<text class="t1" v-if="item.point_type==1">邀约奖励</text>
						<text class="t1" v-else-if="item.point_type==2">下单奖励</text>
						<text class="t1" style="background: #F0F0F0;color: #999999;"
							v-else-if="item.point_type==3">积分消耗</text>
						<text class="t1" v-else>{{item.remark}}</text>
						<text class="font-22" style="color: #222;">{{item.create_time}}</text>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		pointAccount,
		userinfo
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				list1: [
					'全部', '邀约奖励', '下单奖励', '积分消耗'
				],
				current: 0,
				page: 1,
				list: [],
				last_page: 1,
				point: 0
			};
		},
		onShow() {
			this.getinfo()
			this.userinfo()
		},
		methods: {
			change(index) {
				this.current = index;
				this.page = 1
				this.getinfo()
			},
			userinfo() {
				userinfo().then(res => {
					this.point = res.data.point
				})
			},
			getinfo() {
				let params = {
					page: this.page,
					point_type: this.current
				}
				pointAccount(params).then(res => {
					this.list = this.page > 1 ? this.list.concat(...res.data.list) : res.data.list
					this.last_page = res.data.page_count
				})
			},
		}
	}
</script>

<style lang="scss">
	::v-deep .uv-subsection--button__bar {
		background-color: #FFEDE9 !important;
	}

	.items {
		padding: 24rpx;
		background: #F8F8F8;
		border-radius: 12rpx;

		.t1 {
			font-weight: 400;
			font-size: 20rpx;
			color: #FA7F28;
			background: #FFF5ED;
			border-radius: 4rpx;
			padding: 0 6rpx;
		}

		.t2 {
			text-align: right;

			>view:first-child {
				font-weight: 600;
				font-size: 30rpx;
				color: #F51D1D;
			}

			>view:last-child {
				font-weight: 400;
				font-size: 22rpx;
				color: #999999;
			}
		}
	}

	.mains {
		margin: 20rpx 0;
		background: #FFFFFF;
		border-radius: 24rpx;
		padding: 32rpx;

		>view:first-child {
			font-weight: 600;
			font-size: 32rpx;
			color: #1D1E24;
		}
	}

	.container {
		background: #f5f5f5;
		height: 200rpx;
		border-radius: 24rpx 24rpx 0rpx 0rpx;
		padding: 32rpx;
	}

	.bg2s {
		position: absolute;
		top: 0;
		left: 0;
		width: 100vw;
		height: 550rpx;
		z-index: -1;
	}

	.part1 {
		padding-top: 20rpx;
		box-sizing: border-box;
		padding-left: 40rpx;
		position: relative;

		>view {
			font-weight: 400;
			font-size: 24rpx;
			color: #FFFFFF;
		}

		>view:nth-child(2) {
			font-weight: 600;
			font-size: 70rpx;
			color: #FFFFFF;
		}

		text {
			font-weight: 600;
			font-size: 24rpx;
			color: #FFFFFF;
		}

		// >view:last-child {
		// 	width: 184rpx;
		// 	height: 48rpx;
		// 	background: rgba(255, 255, 255, 0.4);
		// 	border-radius: 48rpx;
		// 	position: absolute;
		// 	top: 50%;
		// 	transform: translateY(-50%);
		// 	right: 40rpx;

		// 	image {
		// 		width: 16rpx;
		// 		height: 16rpx;
		// 	}
		// }
	}

	.fcolor {
		color: #F51D1D;
	}
</style>