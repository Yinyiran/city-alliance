<template>
	<view>
		<image src="/static/bg2.png" class="bg2s" mode=""></image>
		<view class="container">
			<view class="acea-row row-middle">
				<image :src="$util.img(info.logo)" v-if="info.logo" class="w-102 h-52 m-r-16" mode=""></image>
				<text style="color: #fff;" class="font-32">{{info.site_name}}</text>
			</view>
			<view class="checkes acea-row row-middle row-center row-between m-t-24">
				<view class="a-i">
					<view class="" style="color: #9E9285;">今日门店签到</view>
					<view class="font-48">{{today_count||0}}</view>
				</view>
				<view class="lines"></view>
				<view class="a-i">
					<view class="" style="color: #9E9285;">累计门店签到</view>
					<view class="font-48">{{total_count||0}}</view>
				</view>
			</view>
			<view class="mains">
				<uv-input placeholder="请输入关键词搜索" v-model="searchtxt" :customStyle="customStyle" border="none">
					<template v-slot:prefix>
						<uv-icon name="search"></uv-icon>
					</template>
					<template v-slot:suffix>
						<view class="search" @click="seachs">搜索</view>
					</template>
				</uv-input>
				<view class="title acea-row row-middle">签到明细</view>
				<view class="acea-row row-middle row-between p-24 a-b">
					<text>名称</text>
					<view class="acea-row row-middle row-between">
						<view class="acea-row row-middle w-116 m-r-32" @click="sets(1)">
							<text>累计签到</text>
							<image src="/static/icon1.png" v-if="sort==0" class="w-16 h-16 m-l-4" mode=""></image>
							<template v-if="sort>0">
								<image src="/static/up.png" class="w-16 h-16" v-if="sort==1" mode=""></image>
								<image src="/static/down.png" v-else class="w-16 h-16" mode=""></image>
							</template>
						</view>
						<view class="acea-row row-middle w-140 row-right" @click="sets(2)">
							<text>签到时间</text>
							<image src="/static/icon1.png" v-if="sort1==0" class="w-16 h-16 m-l-4" mode=""></image>
							<template v-if="sort1>0">
								<image src="/static/up.png" class="w-16 h-16" v-if="sort1==1" mode=""></image>
								<image src="/static/down.png" v-else class="w-16 h-16" mode=""></image>
							</template>
						</view>
					</view>
				</view>
				<view v-for="(item,index) in list" class="h-100 acea-row row-middle row-between p-r-24 p-l-24 items"
					:key="index">
					<view class="acea-row row-middle" style="flex-wrap: nowrap;">
						<image :src="$util.img(item.headimg)" v-if="item.headimg" class="w-52 h-52 bor-26 m-r-12"
							mode=""></image>
						<image src="/static/avatar.png" v-else class="w-52 h-52 bor-26 m-r-12" mode=""></image>
						<view class="text-more font-28" style="max-width: 140rpx;color: #222222;">{{item.nickname}}
						</view>
						<!-- <image src="/static/vip3.png" class="w-24 h-24" mode=""></image> -->
					</view>
					<view class="acea-row row-middle">
						<view class="">{{item.signcount}}</view>
						<view class="">{{item.sign_time}}</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		signlist,
		signIndex,
		getInfo
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				customStyle: "background: #F5F5F5;height: 64rpx;borderRadius:32rpx;paddingLeft:24rpx;",
				list: [],
				select: 0,
				sort: 0,
				cnum: 0,
				cnum1: 0,
				sort1: 0,
				searchtxt: '',
				today_count: '',
				total_count: "",
				info: {}
			};
		},
		onLoad() {
			// 签到统计、明细、商家信息属于公开数据，未登录也拉取
			this.signlist()
			this.signIndex()
			this.getInfo()
		},
		methods: {
			getInfo() {
				getInfo().then(res => {
					this.info = res.data
				})
			},
			signIndex() {
				signIndex().then(res => {
					this.today_count = res.data.today_count
					this.total_count = res.data.total_count
				})
			},
			sets(e) {
				if (e == 2) {
					if (this.cnum1 == 2) {
						this.cnum1 = 0
						this.sort1 = 0
					} else {
						this.cnum1++
						this.sort1++
					}

				} else {
					if (this.cnum == 2) {
						this.cnum = 0
						this.sort = 0
					} else {
						this.cnum++
						this.sort++
					}

				}
				this.signlist()
			},
			seachs() {
				this.signlist()
			},
			signlist() {
				let params = {
					sortcount: this.sort,
					sorttime: this.sort1,
					searchtxt: this.searchtxt
				}
				signlist(params).then(res => {
					this.list = res.data.list
				})
			},
		}
	}
</script>

<style lang="scss">
	.bg2s {
		width: 100vw;
		height: 440rpx;
		position: fixed;
		top: 0;
		left: 0;
		z-index: -1;
	}

	.items {
		background: #F5F5F5;
		margin-bottom: 20rpx;
		border-radius: 12rpx;

		>view:last-child {
			>view:first-child {
				width: 116rpx;
				margin-right: 32rpx;
				font-weight: 600;
				font-size: 28rpx;
				color: #222222;
			}

			>view:last-child {
				width: 140rpx;
				height: 68rpx;
				font-family: PingFangSC, PingFang SC;
				font-weight: 400;
				font-size: 24rpx;
				color: #999999;
			}
		}
	}

	.container {
		margin: 32rpx;

		.mains {
			padding: 24rpx;
			background: #fff;
			border-radius: 24rpx;
			margin-top: 32rpx;
		}

		.a-b {
			text {
				color: #999999;
			}
		}

		.checkes {
			background: linear-gradient(135deg, #FFFEFA 0%, #FFE9D6 100%);
			border-radius: 24rpx;
			height: 130rpx;
			text-align: center;

			.a-i {
				width: calc((100% - 64rpx - 2rpx)/2);

				>view:last-child {
					font-size: 40rpx;
					color: #2E231A;
					font-weight: bold;
				}
			}

			.lines {
				width: 2rpx;
				height: 82rpx;
				background: #F5E0CE;
			}
		}

		.search {
			font-weight: 400;
			font-size: 30rpx;
			color: #222222;
			padding: 0 28rpx;
			border-left: #DDDDDD 1rpx solid;
			height: 42rpx;
		}

		.title {
			font-weight: 600;
			font-size: 32rpx;
			color: #1D1E24;
			margin-top: 24rpx;
		}

		.title::before {
			content: '';
			width: 4rpx;
			height: 32rpx;
			background: #F55606;
			border-radius: 2rpx;
			margin-right: 12rpx;
		}
	}
</style>