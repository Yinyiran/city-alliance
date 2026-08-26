<template>
	<view>
		<view class="" style="position: relative;">
			<view class="part1">
				<view class="acea-row row-middle row-between">
					<view class="acea-row row-middle row-center" @click="gonext('deliveryOrder')">
						<image src="/static/img1.png" class="w-88 h-88 m-r-20" mode=""></image>
						<view class="acea-row row-middle font-32 fcolor1">
							待核销订单
							<image src="/static/go.png" class="w-16 h-16 m-l-4" mode=""></image>
						</view>
					</view>
					<view class="acea-row row-middle row-center" @click="sancode">
						<image src="/static/img2.png" class="w-88 h-88 m-r-20" mode=""></image>
						<view class="acea-row row-middle font-32 fcolor1">
							扫码核销
							<image src="/static/go.png" class="w-16 h-16 m-l-4" mode=""></image>
						</view>
					</view>
				</view>
				<view class="line1 m-t-32"></view>
				<view class="acea-row row-middle row-between m-t-32">
					<view class="acea-row row-middle" style="color: #CCCCCC;">团队总人数 (人)：<text style="color: #F5B67F;"
							class="font-40">{{total_count}}</text></view>
					<view class="acea-row row-middle" @click="gonext('teamSize',0)" style="color: #CCCCCC;">查看明细
						<image src="/static/go1.png" class="w-16 h-16 m-l-4" mode=""></image>
					</view>
				</view>
			</view>
			<image src="/static/bg1.png" class="bg1" mode=""></image>
			<view class="members acea-row row-middle row-between">
				<view class="">
					<view class="">
						<image src="/static/vb1.png" mode=""></image>
						<view class="bga acea-row row-middle">
							<image src="/static/vip1.png" class="w-32 h-32 m-r-8 m-l-16" mode=""></image>
							<text style="color: #FFFFFF;">{{(leveltotal[1]||{}).level_name}}</text>
						</view>
						<view class="" style="color: #fff;"><text style="color: #fff;"
								class="font-48">{{(leveltotal[1]||{}).counts||0}}</text>人
						</view>
					</view>
					<view class="acea-row row-middle row-center p-t-12 p-b-12" style="color: #B2B6C2;"
						@click="gonext('teamSize',1)">
						查看明细
						<image src="/static/go2.png" class="w-18 h-18 m-l-6" mode=""></image>
					</view>
				</view>
				<view class="">
					<view class="">
						<image src="/static/vb2.png" mode=""></image>
						<view class="bgb acea-row row-middle">
							<image src="/static/vip2.png" class="w-32 h-32 m-r-8 m-l-16" mode=""></image>
							<text style="color: #222;">{{(leveltotal[2]||{}).level_name}}</text>
						</view>
						<view class="" style="color: #FAD5B4;"><text style="color: #FAD5B4;"
								class="font-48">{{(leveltotal[2]||{}).counts||0}}</text>人
						</view>
					</view>
					<view class="acea-row row-middle row-center p-t-12 p-b-12" style="color: #B2B6C2;"
						@click="gonext('teamSize',2)">
						查看明细
						<image src="/static/go2.png" class="w-18 h-18 m-l-6" mode=""></image>
					</view>
				</view>
				<view class="">
					<view class="">
						<image src="/static/vb3.png" mode=""></image>
						<view class="bgc acea-row row-middle">
							<image src="/static/vip3.png" class="w-32 h-32 m-r-8 m-l-16" mode=""></image>
							<text style="color: #FFD685;">{{(leveltotal[3]||{}).level_name}}</text>
						</view>
						<view class="" style="color: #FFD685;"><text style="color: #FFD685;"
								class="font-48">{{(leveltotal[3]||{}).counts||0}}</text>人
						</view>
					</view>
					<view class="acea-row row-middle row-center p-t-12 p-b-12" style="color: #B2B6C2;"
						@click="gonext('teamSize',3)">
						查看明细
						<image src="/static/go2.png" class="w-18 h-18 m-l-6" mode=""></image>
					</view>
				</view>
			</view>
		</view>
		<view class="acea-row row-middle row-between jifenUM">
			<text>积分数量</text>
			<text>{{points||0}}</text>
		</view>
		<view class="container">
			<view class="acea-row row-middle row-between m-b-32">
				<view v-for="(item,index) in tablist" :class="{'on':check==index}" @click="cuts(index)" :key="index">
					{{item}}
					<image src="/static/down1.png" class="w-24 h-8" v-if="check==index" mode=""></image>
				</view>
			</view>
			<uv-subsection :list="list1" :current="current" activeColor='#1D1E24'
				custom-style="height: 60rpx;border-radius: 30rpx;" custom-item-style="border-radius: 30rpx;"
				@change="change"></uv-subsection>
			<view class="h-24"></view>
			<view class="defas acea-row row-middle row-between" v-if="check==0&&token&&myinfo">
				<view class="acea-row row-middle">
					<view class="ranks">
						<image src="/static/rank.png" mode=""></image>
						<text>{{myinfo.rank}}</text>
					</view>
					<image :src="$util.img(myinfo.info.site_logo)" v-if="myinfo.info.site_logo" class="w-102 h-52 m-l-24" mode=""></image>
					<text class="font-28 m-l-12" style="color: #222;">{{myinfo.info.site_name}}</text>
					<text class="my">(我)</text>
				</view>
				<view class="font-28" style="color: #222;">{{myinfo.info.account_points||0}}</view>
			</view>
			<view class="acea-row row-middle row-between p-24 titles" v-if="check==0">
				<view class="acea-row row-middle">
					<text class="m-r-20">排名</text>
					<text>品牌</text>
				</view>
				<text>热度</text>
			</view>
			<view class="acea-row row-middle row-between p-24 titles" v-else>
				<view class="acea-row row-middle">
					<text class="m-r-20">排名</text>
					<text>名称</text>
				</view>
				<text>积分</text>
			</view>
			<template v-if="check==0">
				<view v-for="(item,index) in list" class="m-b-20 defas acea-row row-middle row-between"
					:class="{'rankbg1':index==0,'rankbg2':index==1,'rankbg3':index==2}" :key="index">
					<view class="acea-row row-middle" style="flex-wrap: nowrap;">
						<view class="ranks">
							<image src="/static/rank1.png" v-if="index==0" mode=""></image>
							<image src="/static/rank2.png" v-else-if="index==1" mode=""></image>
							<image src="/static/rank3.png" v-else-if="index==2" mode=""></image>
							<image src="/static/rank.png" v-else mode=""></image>
							<text v-if="index>2">{{index+1}}</text>
						</view>
						<image :src="$util.img(item.site_logo)" v-if="item.site_logo" class="w-102 h-52 m-l-24" mode=""></image>
						<text class="font-28 m-l-12 text-more w-280" style="color: #222;">{{item.site_name}}</text>
					</view>
					<view class="font-28" style="color: #222;">{{item.account_points||0}}</view>
				</view>
			</template>
			<template v-else>
				<view v-for="(item,index) in list2" class="m-b-20 defas acea-row row-middle row-between"
					:class="{'rankbg1':index==0,'rankbg2':index==1,'rankbg3':index==2}" :key="index">
					<view class="acea-row row-middle">
						<view class="ranks">
							<image src="/static/rank1.png" v-if="index==0" mode=""></image>
							<image src="/static/rank2.png" v-else-if="index==1" mode=""></image>
							<image src="/static/rank3.png" v-else-if="index==2" mode=""></image>
							<image src="/static/rank.png" v-else mode=""></image>
							<text v-if="index>2">{{index+1}}</text>
						</view>
						<image :src="$util.img(item.memberinfo.headimg)" v-if="item.memberinfo.headimg"
							class="w-52 h-52 bor-50 m-l-24" mode=""></image>
						<image src="/static/avatar.png" v-else class="w-52 h-52 bor-50 m-l-24" mode=""></image>
						<text class="font-28 m-l-12" style="color: #222;">{{item.memberinfo.nickname}}</text>
					</view>
					<view class="font-28" style="color: #222;">{{item.account_points||0}}</view>
				</view>
			</template>
		</view>
		<login ref="login" @loginsuccess='loginsuccess'></login>
	</view>
</template>

<script>
	import {
		getnum,
		getmemberranklist,
		getRankList,
		getInfo
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				tablist: ['品牌积分排行榜', '员工积分排行榜'],
				check: 0,
				list1: ['年度排行榜', '月度排行榜'],
				current: 0,
				points: 0,
				total_count: 0,
				leveltotal: [],
				list: [],
				list2: [],
				myinfo: {},
				token: '',
				page: 1,
				last_page: 1
			}
		},
		onLoad(res) {

		},
		onReachBottom() {
			if (this.last_page > this.page) {
				this.page++
				if (this.check == 1) {
					this.getmemberranklist()
				} else {
					this.getRankList()
				}
			}
		},
		onShow() {
			this.token = uni.getStorageSync('token') || ''
			// 品牌积分排行榜属于公开数据，未登录也拉取
			this.getRankList()
			if (uni.getStorageSync('token')) {
				this.getnum()
				// this.getmemberranklist()
			}
		},
		onPullDownRefresh() {


		},
		methods: {
			sancode() {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				uni.scanCode({
					success: res => {
						if (res.result) {
							uni.navigateTo({
								url: '/otherpages/writeOff/writeOff?code=' + res.result
							})
						}
					},
					fail: () => {
						uni.showToast({
							title: '扫码失败',
							icon: 'none'
						})
					}
				})
			},
			loginsuccess() {
				this.getnum()
				getInfo().then(res => {
					uni.setStorageSync('site_id', res.data.site_id)
				})
				if (this.check == 1) {
					this.getmemberranklist()
				} else {
					this.getRankList()
				}
			},
			change(e) {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				this.current = e
				this.page = 1
				if (this.check == 1) {
					this.getmemberranklist()
				} else {
					this.getRankList()
				}
			},
			cuts(e) {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				this.check = e
				this.page = 1
				if (e == 1) {
					this.getmemberranklist()
				} else {
					this.getRankList()
				}
			},
			getRankList() {
				let params = {
					sortby: this.current == 0 ? 'year' : 'month',
					page: this.page
				}
				getRankList(params).then(res => {
					this.myinfo = res.data.myinfo
					this.list = this.page > 1 ? this.list.concat(...res.data.list) : res.data.list
					this.last_page = res.data.page_count
				})
			},
			getmemberranklist() {
				let params = {
					sortby: this.current == 0 ? 'year' : 'month',
					page: this.page
				}
				getmemberranklist(params).then(res => {
					this.list2 = this.page > 1 ? this.list2.concat(...res.data.list) : res.data.list
					this.last_page = res.data.page_count
				})
			},
			gonext(e, n) {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				uni.navigateTo({
					url: `/otherpages/${e}/${e}?current=${n}`
				})
			},
			getnum() {
				getnum().then(res => {
					this.points = res.data.points
					this.total_count = (res.data.team_total && res.data.team_total.total_count) || 0
					this.leveltotal = (res.data.team_total && res.data.team_total.leveltotal) || []
				})
			},
		}
	}
</script>

<style lang="scss">
	.container {
		margin: 32rpx;
		background: #FFFFFF;
		border-radius: 24rpx;
		padding: 24rpx;

		.titles {
			text {
				font-weight: 400;
				font-size: 24rpx;
				color: #999999;
			}
		}

		.my {
			font-weight: 600;
			font-size: 28rpx;
			color: #F55606;
		}

		>view:first-child {
			>view {
				width: calc((100% - 16rpx)/2);
				line-height: 72rpx;
				background: #F5F5F5;
				border-radius: 16rpx;
				text-align: center;
				box-sizing: border-box;
				position: relative;
				font-weight: 400;
				font-size: 28rpx;
				color: #222222;
			}

			.on {
				border: 2rpx solid #F55606;
				font-weight: 600;
				font-size: 28rpx;
				color: #F55606;

				>image {
					position: absolute;
					left: 50%;
					transform: translateX(-50%);
					bottom: -10rpx;
				}
			}
		}

		.defas {
			border-radius: 12rpx;
			height: 104rpx;
			padding: 0 24rpx;
			background: #F5F5F5;
		}

		.rankbg1 {
			background: linear-gradient(90deg, #FFF1D6 0%, #F6F8FC 100%);
		}

		.rankbg2 {
			background: linear-gradient(90deg, #E3EFFC 0%, #F6F8FC 100%);
		}

		.rankbg3 {
			background: linear-gradient(90deg, #FFF0E6 0%, #FFFCFA 100%);
		}

		.ranks {
			width: 58rpx;
			height: 64rpx;
			position: relative;

			>text {
				font-weight: 400;
				font-size: 32rpx;
				color: #222222;
				position: absolute;
				left: 50%;
				transform: translateX(-50%);
				bottom: 12rpx;
			}

			>image {
				width: 100%;
				height: 100%;
			}
		}
	}

	.jifenUM {
		height: 80rpx;
		background: #272A33;
		margin: 32rpx;
		border-radius: 16rpx;
		position: relative;
		padding: 0 32rpx;

		&::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			border-radius: 16rpx;
			padding: 1rpx;
			background: linear-gradient(90deg, rgba(250, 213, 180, 0), rgba(250, 213, 180, 1));
			-webkit-mask:
				linear-gradient(#fff 0 0) content-box,
				linear-gradient(#fff 0 0);
			-webkit-mask-composite: xor;
			mask-composite: exclude;
			pointer-events: none;
		}

		text:first-child {
			color: #CCCCCC;
			font-size: 28rpx;
		}

		text:last-child {
			color: #F5B67F;
			font-size: 48rpx;
			font-weight: 700;
		}
	}

	.members {
		margin: 0 32rpx;
		margin-top: 85rpx;

		>view {
			width: calc((100% - 32rpx)/3);
			// height: 208rpx;
			background: #272A33;
			border-radius: 16rpx;
			overflow: hidden;

			>view:first-child {
				width: 100%;
				height: 158rpx;
				position: relative;

				>image {
					width: 100%;
					height: 100%;
				}

				>view:nth-child(2) {
					width: 176rpx;
					height: 50rpx;
					position: absolute;
					left: 0;
					top: 0;
					z-index: 3;
					clip-path: polygon(0% 0%, 100% 0%, 95% 100%, 0% 100%);
					border-radius: 0 0 20rpx 0;
				}

				.bga {
					background: linear-gradient(90deg, rgba(255, 255, 255, 0.16) 0%, rgba(255, 255, 255, 0.28) 100%);
				}

				.bgb {
					background: linear-gradient(90deg, rgba(255, 255, 255, 0.16) 0%, rgba(255, 255, 255, 0.28) 100%);
				}

				.bgc {
					background: linear-gradient(90deg, rgba(255, 255, 255, 0.16) 0%, rgba(255, 255, 255, 0.28) 100%);
				}

				>view:nth-child(3) {
					position: absolute;
					left: 20rpx;
					bottom: 32rpx;
				}
			}
		}
	}

	.bg1 {
		position: absolute;
		width: 100vw;
		height: 350rpx;
		top: 220rpx;
		left: 0;
		z-index: -1;
	}

	.line1 {
		border: 1rpx dashed #555555;
	}

	.wbg {
		background: #fff;
	}

	page {
		background: #101014;
	}

	.part1 {
		height: 279rpx;
		background: linear-gradient(180deg, #272A33 0%, #18181F 100%);
		border-radius: 24rpx;
		margin: 0 32rpx;
		margin-top: 20rpx;
		box-sizing: border-box;
		padding: 32rpx;

		>view:first-child {
			>view {
				width: calc((100% - 46rpx)/2);
			}
		}
	}

	.fcolor1 {
		color: #fff;
	}
</style>