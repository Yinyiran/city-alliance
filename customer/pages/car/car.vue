<template>
	<view>
		<uv-navbar bg-color="#ffffff00" :fixed="false">
			<template v-slot:left>
				<view>

				</view>
			</template>
			<template v-slot:center>
				<view class="maps">
					福利区
				</view>
			</template>
		</uv-navbar>
		<image src="/static/bg5.png" class="bgs" mode=""></image>
		<view class="swipers" v-if="banner.length>0" @click="goinvite">
			<swiper :indicator-dots="false" :autoplay="true" @change="onchange" :interval="3000" :duration="1000">
				<swiper-item v-for='item in banner'>
					<view class="swiper-item">
						<image :src="$util.img(item.image)" mode=""></image>
					</view>
				</swiper-item>
			</swiper>
			<view class="dots">
				<view v-for="(item,index) in banner.length" :class="{'on':current==index}" :key="index"></view>
			</view>
		</view>
		<view class="members">
			<image src="/static/mb.png" v-if="member_level==1" mode=""></image>
			<image src="/static/mb1.png" v-else-if="member_level==2" mode=""></image>
			<image src="/static/mb3.png" v-else-if="member_level==3" mode=""></image>
			<image src="/static/mb2.png" v-else-if="member_level==4" mode=""></image>
			<image src="/static/mb.png" v-else mode=""></image>
			<view class="acea-row row-middle row-between putong">
				<view class="acea-row row-middle">
					<image src="/static/vip.png" v-if="member_level==1" class="w-36 h-36 m-r-8" mode=""></image>
					<image src="/static/vip1.png" v-else-if="member_level==2" class="w-36 h-36 m-r-8" mode=""></image>
					<image src="/static/vip2.png" v-else-if="member_level==3" class="w-36 h-36 m-r-8" mode=""></image>
					<image src="/static/vip3.png" v-else-if="member_level==4" class="w-36 h-36 m-r-8" mode=""></image>
					<image src="/static/vip.png" v-else class="w-36 h-36 m-r-8" mode=""></image>
					<view class="acea-row row-middle">
						<text v-if="member_level==1">普通会员</text>
						<text v-else-if="member_level==2">钻石会员</text>
						<text v-else-if="member_level==3">黑钻会员</text>
						<text v-else-if="member_level==4">金钻会员</text>
						<text v-else>普通会员</text>
						｜
						剩余积分 <text>{{point}}</text>
					</view>
				</view>
				<view class="acea-row row-middle" @click="gomember">
					<text>升级会员</text>
					<image src="/static/go1.png" class="w-24 h-24" mode=""></image>
				</view>
			</view>
			<view class="acea-row row-middle row-between m-t-28 cuts">
				<view v-for="(item,index) in tablist" :class="{'on':check==index}" @click="cut1(index)" :key="index">
					{{item}}
					<image src="/static/down1.png" class="w-24 h-8" v-if="check==index" mode=""></image>
				</view>
			</view>
			<view class="acea-row row-middle row-between cuts1" v-if="check==1">
				<view v-for="(item,index) in benlist" class="text-more" @click="cut2(index)"
					:class="{'on':check1==index}" :key="index">
					{{item.name}}
				</view>
			</view>
		</view>
		<view class="container" v-if="list.length > 0">
			<view v-for="(item,index) in list" :key="index" @click="godetail(item.sku_id)">
				<image :src="$util.img(item.sku_image)" class="bor-16" mode=""></image>
				<view class="font-26 m-t-16 text-more">
					{{item.goods_name}}
				</view>
				<view class="acea-row row-middle row-between m-t-8">
					<view class="acea-row row-middle font-22" style="color: #999;">
						<uv-icon name="map" color="#999" size="14"></uv-icon>
						距离{{item.distance}}km
					</view>
					<view class="font-22" style="color: #999;">已售{{item.sale_num}}件</view>
				</view>
				<view class="acea-row row-middle">
					<view class="duihaun">积分兑换</view>
					<view class="duihaun" v-if="check==0">门店核销</view>
					<view class="duihaun baoyou m-l-6" v-if="check==1">全国包邮</view>
				</view>
				<view class="acea-row row-middle" style="flex-wrap: nowrap;" v-if="check==0">
					<view class="" style="white-space: nowrap;">
						<image src="/static/coin.png" class="w-24 h-24" mode=""></image>
						<text class="nums">{{item.point}}</text>
					</view>
					<view class="lines" style="white-space: nowrap;">价值 ¥{{item.market_price}}</view>
				</view>
				<view class="" v-else>
					<view class="acea-row row-middle m-t-12">
						<view class="">
							<text class="tehui">特惠</text>
							<text class="tehui font-weight">￥</text>
							<text class="nums" style="font-size: 32rpx;">{{item.price}}</text>
						</view>
						<view class="lines" v-if="item.market_price!='0.00'">¥{{item.market_price}}</view>
					</view>
					<view class="a-1 acea-row row-middle">
						<view class="acea-row row-middle">
							<image src="/static/bg9.png" mode=""></image>
							<image src="/static/coin.png" mode=""></image>
							<text>积分价</text>
						</view>
						<view class="">{{item.point}}</view>
					</view>
				</view>
			</view>
		</view>
		<view class="acea-row row-column row-center row-middle" style="padding: 100rpx 0;" v-else>
			<image src="/otherpages/static/empty1.png" mode="aspectFit" style="width: 220rpx; height: 220rpx;"></image>
			<text class="font-26 m-t-24" style="color: #999;">暂无福利商品</text>
		</view>
		<login ref="login"></login>
	</view>
</template>

<script>
	import {
		bannerList,
		userinfo,
		benefitOption,
		goodslist
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				current: 0,
				tablist: ['门店福利兑换区', '全国包邮兑换区'],
				// tablist1: ['1-3000积分', '3000-5000积分', '5000积分以上'],
				check: 0,
				check1: 0,
				type: 3,
				website_id: '',
				site_id: '',
				banner: [],
				token: "",
				point: "",
				member_level_name: "",
				member_level: "",
				benlist: [],
				page: 1,
				last_page: 1,
				list: []
			};
		},
		onLoad(options) {
			// 支持从分享链接等渠道携带 site_id 进入，按指定商家获取门店福利数据
			if (options && options.site_id) {
				this.site_id = options.site_id
			} else {
				this.site_id = ''
			}
		},
		onShow() {
			this.website_id = uni.getStorageSync('website_id') || ''
			this.page = 1
			this.getlist()
			// 积分区间配置并行加载，不阻塞商品列表请求
			this.benefitOption()
			if (this.website_id) {
				this.bannerList()
			}
			if (uni.getStorageSync('token')) {
				this.token = uni.getStorageSync('token')
				this.userinfo()
			}
		},
		onReachBottom() {
			if(this.page<this.last_page){
				this.page++
				this.getlist()
			}
		},
		methods: {
			goinvite(){
				uni.navigateTo({
					url:'/otherpages/inviteDatas/inviteDatas'
				})
			},
			gomember(){
				if(!uni.getStorageSync('token')){
					this.$refs.login.open()
					return
				}
				uni.navigateTo({
					url:'/otherpages/memberlevel/memberlevel'
				})
			},
			godetail(e) {
				uni.navigateTo({
					url: `/otherpages/goods_detail/goods_detail?type=${this.check+2}&id=` + e
				})
			},
			cut2(e) {
				this.check1 = e
				this.page = 1
				this.list = []
				this.getlist()
			},
			cut1(e) {
				this.check = e
				this.page = 1
				this.list = []
				this.getlist()
			},
			async getlist() {
				let params = {
					page: this.page,
					website_id: this.website_id,
					goods_type: 1,
					platform: this.check,
					// 门店福利区才使用 site_id，全国包邮区属于平台商品，site_id 必须为空
					site_id: this.check == 0 ? (this.site_id || '') : ''
				}
				// 仅全国包邮兑换区(check == 1)且已选积分区间时增加积分筛选
				if (this.check == 1 && this.benlist && this.benlist[this.check1]) {
					let ben = this.benlist[this.check1]
					if (ben.min && ben.min > 0) params.min_point = ben.min
					if (ben.max && ben.max > 0) params.max_point = ben.max
				}
				try {
					await goodslist(params).then(res => {
						if (res && res.code == 0 && res.data) {
							let list = res.data.list || []
							this.list = this.page > 1 ? this.list.concat(...list) : list
							this.last_page = res.data.page_count || 1
						} else {
							if (this.page == 1) this.list = []
						}
					})
				} catch (e) {
					console.error('goodslist error:', e)
					if (this.page == 1) this.list = []
				}
			},
			async benefitOption() {
				try {
					await benefitOption().then(res => {
						this.benlist = res.code == 0 && res.data ? res.data : []
					})
				} catch (e) {
					this.benlist = []
				}
			},
			userinfo() {
				userinfo().then(res => {
					// 判空保护：未登录 / token 失效 / 网络异常时 res.data 为 null
					const d = (res && res.code == 0 && res.data) || {}
					this.point = d.point || 0
					this.member_level = d.member_level || 0
					this.member_level_name = d.member_level_name || ''
				})
			},
			bannerList() {
				let params = {
					type: 2,
					website_id: this.website_id
				}
				bannerList(params).then(res => {
					// 判空保护：模板对 banner 取 .length，置 null 会抛错导致整页白屏
					this.banner = (res && res.code == 0 && Array.isArray(res.data)) ? res.data : []
				})
			},
			onchange(e) {
				this.current = e.detail.current
			},
		}
	}
</script>

<style lang="scss">
	.a-1 {
		width: 100%;
		height: 62rpx;

		>view:first-child {
			width: 160rpx;
			height: 100%;
			position: relative;
			text{
				font-weight: 600;
				font-size: 24rpx;
				color: #FFFFFF;
				position: relative;
				z-index: 2;
			}

			>image:first-child {
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				z-index: 1;
			}

			>image:nth-child(2) {
				width: 24rpx;
				height: 24rpx;
				position: relative;
				z-index: 2;
				margin-left: 20rpx;
			}
		}

		>view:last-child {
			background: linear-gradient(270deg, rgba(255, 250, 240, 0) 0%, #FFFAF0 100%);
			width: calc(100% - 160rpx);
			font-size: 42rpx;
			color: #F51D1D;
			font-weight: bold;
		}
	}

	.container {
		margin: 0 32rpx;

		>view {
			width: calc((100% - 16rpx)/2);
			margin-right: 16rpx;
			background: #fff;
			padding: 20rpx;
			border-radius: 24rpx;
			margin-bottom: 16rpx;
			display: inline-block;
			box-sizing: border-box;

			>image {
				width: 100%;
				height: 295rpx;
			}
		}

		>view:nth-child(2n) {
			margin-right: 0;
		}

		.duihaun {
			font-weight: 600;
			font-size: 20rpx;
			color: #F51D1D;
			background: #FFEBEB;
			border-radius: 4rpx;
			padding: 0 8rpx;
			line-height: 28rpx;
			width: fit-content;
			margin-top: 16rpx;
		}

		.baoyou {
			background: #FAF3E6;
			color: #F5AB18;
		}

		.nums {
			font-size: 42rpx;
			color: #F51D1D;
			font-weight: bold;
		}

		.lines {
			text-decoration: line-through;
			font-weight: 400;
			font-size: 24rpx;
			color: #999999;
			margin-left: 8rpx;
		}

		.tehui {
			font-weight: 400;
			font-size: 22rpx;
			color: #F51D1D
		}
	}

	.members {
		min-height: 220rpx;
		margin: 32rpx;
		margin-bottom: 0;
		position: relative;
		padding: 28rpx 20rpx;
		box-sizing: border-box;

		>image {
			z-index: -1;
			width: 100%;
			height: 100%;
			position: absolute;
			left: 0;
			top: 0;
		}

		.cuts1 {
			margin-top: 32rpx;

			>view {
				width: calc((100% - 32rpx)/3);
				padding: 0 20rpx;
				height: 52rpx;
				background: #FFFFFF;
				border-radius: 26rpx;
				box-sizing: border-box;
				text-align: center;
				line-height: 52rpx;
				font-weight: 400;
				font-size: 22rpx;
				color: #555555;
				border: 2rpx solid #FFFFFF;
			}

			.on {
				background: #FAE6DC;
				border-radius: 26rpx;
				font-weight: 400;
				font-size: 22rpx;
				color: #F55606;
				position: relative;
			}

			.on::after {
				content: '';
				width: 32rpx;
				height: 2rpx;
				background: #F55606;
				border-radius: 2rpx;
				position: absolute;
				left: 50%;
				transform: translateX(-50%);
				bottom: 4rpx;
			}
		}

		.putong {
			>view:first-child {

				>view {
					font-weight: 400;
					font-size: 26rpx;
					color: #FFFFFF;

					text:first-child {
						font-size: 32rpx;
						color: #FFFFFF;
						font-weight: 600;
					}

					>text:last-child {
						font-weight: 600;
						font-size: 26rpx;
						color: #FFFFFF;
						margin-left: 4rpx;
					}
				}
			}

			>view:last-child {
				text {
					font-weight: 400;
					font-size: 24rpx;
					color: #FFFFFF;
					margin-right: 8rpx;
				}
			}
		}

		.cuts {
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
	}

	.maps {
		font-weight: 600;
		font-size: 36rpx;
		color: #1A1A1A;
	}

	.swipers {
		margin: 12rpx 32rpx 0 32rpx;
		height: 290rpx;
		border-radius: 16rpx;
		overflow: hidden;
		position: relative;

		swiper {
			width: 100%;
			height: 100%;

			view,
			image {
				width: 100%;
				height: 100%;
				border-radius: 16rpx;
			}
		}

		.dots {
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			bottom: 20rpx;
			display: flex;
			align-items: center;
			justify-content: center;

			>view {
				width: 24rpx;
				height: 8rpx;
				background: #FFFFFF;
				border-radius: 6rpx;
				opacity: 0.28;
				margin-right: 8rpx;
			}

			.on {
				background: #FFFFFF;
				opacity: 1;
			}
		}
	}

	.bgs {
		position: absolute;
		width: 100vw;
		height: 556rpx;
		top: 0;
		left: 0;
		z-index: -1;
	}
</style>