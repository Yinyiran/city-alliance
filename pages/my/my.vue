<template>
	<view>
		<uv-navbar bg-color="#ffffff00" :fixed="false">
			<template v-slot:left>
				<view></view>
			</template>
		</uv-navbar>
		<image src="/static/myb1.png" class="bgs" mode=""></image>
		<view class="infos">
			<view class="acea-row row-middle row-between">
				<view class="acea-row row-middle">
					<view class="avatar m-r-16">
						<image src="/static/img1.png" mode=""></image>
						<image src="/static/avatar.png" v-if="!infos.headimg" mode=""></image>
						<image :src="$util.img(infos.headimg)" v-else mode=""></image>
					</view>
					<view class="" v-if="token">
						<view class="font-46 font-weight">{{ infos.nickname }}</view>
						<view class="font-26 m-t-12" style="color: #525666;">ID: {{ infos.member_id }}</view>
					</view>
					<view class="" v-else>
						<view class="font-46 font-weight" @click="gonext('next')">立即登录</view>
						<!-- <view class="font-26 m-t-12" style="color: #525666;">ID: {{infos.member_id}}</view> -->
					</view>
				</view>
				<image src="/static/seeting.png" @click="gonext('setTing')" class="w-48 h-48" mode=""></image>
			</view>
			<view class="members">
				<image src="/static/ob.png" v-if="infos.member_level == 1" mode=""></image>
				<image src="/static/ob1.png" v-else-if="infos.member_level == 2" mode=""></image>
				<image src="/static/ob2.png" v-else-if="infos.member_level == 3" mode=""></image>
				<image src="/static/ob3.png" v-else-if="infos.member_level == 4" mode=""></image>
				<image src="/static/ob.png" v-else mode=""></image>
				<view class="relative" :class="{ fcolor: infos.member_level == 4 }">{{ infos.member_level_name || '普通会员'
				}}
				</view>
				<view class="font-weight-6 m-t-12 relative " @click="gonext('myPoint')"
					:class="{ fcolor: infos.member_level == 4 }">
					<text class="font-weight-4 m-r-6 relative" :class="{ fcolor: infos.member_level == 4 }">积分</text>{{
						infos.point || 0 }}
				</view>
				<view class="w-360 m-t-8 g-8">
					<!-- <uv-line-progress :percentage="80" :showText='false' activeColor="#FFFFFF" -->
					<!-- inactiveColor="rgba(255, 255, 255, 0.16)" height='8rpx'></uv-line-progress> -->
				</view>
				<view class="acea-row row-middle relative m-t-12" @click="gonext('memberlevel')" v-if="infos.member_level > 1"
					:class="{ fcolor: infos.member_level == 4 }">
					当前可享超多会员权益
					<uv-icon name="arrow-right" :color="true ? '#FAD5B4' : '#fff'" size="12"></uv-icon>
				</view>
				<view class="acea-row row-middle relative m-t-12" @click="gonext('memberlevel')" v-else
					:class="{ fcolor: infos.member_level == 4 }">
					开通会员可享超多会员权益
					<uv-icon name="arrow-right" :color="true ? '#FAD5B4' : '#fff'" size="12"></uv-icon>
				</view>
				<view class="linea"></view>
				<view class="m-t-16 acea-row row-middle row-between">
					<view class="" @click="gonext('myPoint')">
						<view class="acea-row row-middle relative m-t-12" :class="{ fcolor: infos.member_level == 4 }">
							我的积分
							<uv-icon name="arrow-right" :color="infos.member_level == 4 ? '#FAD5B4' : '#fff'" size="12"></uv-icon>
						</view>
						<view class="relative font-32 font-weight" :class="{ fcolor: infos.member_level == 4 }">
							{{ Math.ceil(infos.point) || 0 }}</view>
					</view>
					<view class="acea-row row-middle row-center upbtn" @click="gonext('memberlevel')"
						:class="{ 'jin': infos.member_level == 4 }">升级会员
						<uv-icon name="play-right-fill" color="#222" size="12"></uv-icon>
					</view>
				</view>
			</view>
		</view>
		<view class="acea-row row-middle row-between part" style="flex-wrap: wrap;">
			<view class="balance m-b-16" @click="gonext('wallet')">
				<view class="">
					余额
					<image src="/static/go2.png" class="w-16 h-16 m-l-8" mode=""></image>
				</view>
				<view class="">{{ Number(infos.balance) + Number(infos.balance_money) || 0 }}</view>
				<image src="/static/money.png" mode=""></image>
			</view>
			<view class="m-b-16" @click="gonext('income')">
				<view class="font-26 font-weight-6">
					收益中心
				</view>
				<view class="font-20" style="color: #999;">佣金 ￥{{ infos.balance_money || 0 }}</view>
				<image src="/static/money.png" mode=""></image>
			</view>
			<view class="m-b-16" @click="gonext('teams')">
				<view class="font-26 font-weight-6">
					我的团队
				</view>
				<view class="font-20" style="color: #999;">{{ infos.team_num || 0 }}人</view>
				<image src="/static/team.png" mode=""></image>
			</view>
			<view class="m-b-16" @click="gonext('cashOut')">
				<view class="font-26 font-weight-6">
					提现
				</view>
				<view class="font-20" style="color: #999;">可提金额 {{ infos.balance_money || 0 }}</view>
				<image src="/static/tixian.png" mode=""></image>
			</view>
			<view class="" @click="gonext('follow')">
				<view class="font-26 font-weight-6">
					关注列表
				</view>
				<view class="font-20" style="color: #999;">已收藏 {{ infos.shop_member_num || 0 }}个</view>
				<image src="/static/coll.png" mode=""></image>
			</view>
		</view>
		<view class="tools">
			<uv-grid :col="4">
				<uv-grid-item v-for="(item, index) in gaidlist" @click="gonext(item.path)" :key="index">
					<view class="nums">
						<image :src="item.icon" mode=""></image>
						<view class="" v-if="item.num > 0">{{ item.num }}</view>
					</view>
					<view class="">{{ item.title }}</view>
				</uv-grid-item>
			</uv-grid>
		</view>
		<view class="container m-32">
			<view class="cuts acea-row row-middle row-between">
				<view :class="{ 'on1': check == 0 }" @click="check = 0, page = 1">
					<text>用户积分排行榜</text>
					<image src="/static/cl.png" v-if="check == 0" mode=""></image>
				</view>
				<view :class="{ 'on1': check == 1 }" @click="check = 1, page = 1">
					<text>品牌热度排行榜</text>
					<image src="/static/cr.png" v-if="check == 1" mode=""></image>
				</view>
			</view>
			<view class="aaas p-t-28">
				<view class="m-r-28 m-l-28">
					<uv-subsection :list="list1" :current="current" activeColor='#1D1E24'
						custom-style="height: 60rpx;border-radius: 30rpx;" custom-item-style="border-radius: 30rpx;"
						@change="change"></uv-subsection>
				</view>
				<view class="rank-list m-r-24 m-l-24">
					<view class="defas acea-row row-middle row-between m-t-24" style="background: #FFF1EB;"
						v-if="check == 0 && token && member_data">
						<view class="acea-row row-middle">
							<view class="ranks">
								<image src="/static/rank.png" mode=""></image>
								<text>{{ ranking }}</text>
							</view>
							<image :src="$util.img(member_data.headimg)" v-if="member_data && member_data.headimg"
								class="w-52 h-52 bor-50 m-l-24" mode=""></image>
							<image src="/static/avatar.png" v-else class="w-52 h-52 bor-50 m-l-24" mode="">
							</image>
							<text class="font-28 m-l-12" style="color: #222;">{{ member_data.nickname }}</text>
							<text class="my">(我)</text>
						</view>
						<view class="font-28" style="color: #222;">{{ member_data.real_point }}</view>
					</view>
					<view class="acea-row row-middle row-between p-24 titles" v-if='check == 1'>
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
					<view v-for="(item, index) in pointlist" class="m-b-20 defas acea-row row-middle row-between"
						v-if="check == 0" :class="{ 'rankbg1': index == 0, 'rankbg2': index == 1, 'rankbg3': index == 2 }"
						:key="index">
						<view class="acea-row row-middle">
							<view class="ranks">
								<image src="/static/rank1.png" v-if="index == 0" mode=""></image>
								<image src="/static/rank2.png" v-else-if="index == 1" mode=""></image>
								<image src="/static/rank3.png" v-else-if="index == 2" mode=""></image>
								<image src="/static/rank.png" v-else mode=""></image>
								<text v-if="index > 2">{{ index + 1 }}</text>
							</view>
							<image :src="$util.img(item.headimg)" v-if="item.headimg" class="w-52 h-52 bor-50 m-l-24" mode="">
							</image>
							<image src="/static/avatar.png" v-else class="w-52 h-52 bor-50 m-l-24" mode=""></image>
							<text class="font-28 m-l-12" style="color: #222;">{{ item.nickname }}</text>
						</view>
						<view class="font-28" style="color: #222;">{{ item.real_point }}</view>
					</view>
					<uv-empty v-if="check == 0 && !loading && !pointlist.length" text="暂无数据" mode="list"
						marginTop="80"></uv-empty>
					<template v-if="check == 1">
						<view v-for="(item, index) in shoplist" class="m-b-20 defas acea-row row-middle row-between"
							:class="{ 'rankbg1': index == 0, 'rankbg2': index == 1, 'rankbg3': index == 2 }" :key="index">
							<view class="acea-row row-middle">
								<view class="ranks">
									<image src="/static/rank1.png" v-if="index == 0" mode=""></image>
									<image src="/static/rank2.png" v-else-if="index == 1" mode=""></image>
									<image src="/static/rank3.png" v-else-if="index == 2" mode=""></image>
									<image src="/static/rank.png" v-else mode=""></image>
									<text v-if="index > 2">{{ index + 1 }}</text>
								</view>
								<image :src="$util.img(item.store_image)" class="w-72 h-52 m-l-24" mode="">
								</image>
								<text class="font-28 m-l-12 text-more w-280" style="color: #222;">{{ item.store_name
								}}</text>
							</view>
							<view class="font-28" style="color: #222;">{{ item.real_heat }}</view>
						</view>
						<uv-empty v-if="!loading && !shoplist.length" text="暂无数据" mode="list" marginTop="80"></uv-empty>
					</template>
				</view>
			</view>
		</view>
		<image src="/static/hb.png" class="hb" @click="gonext('poster')" mode=""></image>
		<!-- <view class="h-100"></view> -->
		<login ref="login" @loginsuccess='loginsuccess'></login>
	</view>
</template>

<script>
import {
	orderNum,
	pointRanking,
	shopRanking,
	userinfo
} from "@/common/js/api.js";
export default {
	watch: {
		check(news) {
			console.log(news);
			if (news == 0) {
				this.pointRanking()
			} else {
				this.shopRanking()
			}
		},
	},
	data() {
		return {
			type: 4,
			check: 0,
			list1: ['年度排行榜', '月度排行榜'],
			current: 0,
			gaidlist: [{
				icon: '/static/order1.png',
				title: '待支付',
				num: 0,
				path: "waitpay"
			},
			{
				icon: '/static/order2.png',
				title: '待核销',
				num: 0,
				path: 'waitstock'
			},
			{
				icon: '/static/order3.png',
				title: '购物车',
				path: 'buyCar',
				num: 0
			},
			{
				icon: '/static/order4.png',
				title: '退款/售后',
				path: 'after'
			},
			{
				icon: '/static/order5.png',
				title: '我的钱包',
				path: 'wallet'
			},
			{
				icon: '/static/order6.png',
				title: '收货地址',
				path: 'address'
			},
			// {
			// 	icon: '/static/order7.png',
			// 	title: '入驻城盟',
			// 	path: 'settle'
			// },
			{
				icon: '/static/order8.png',
				title: '全部订单',
				path: 'all'
			}
			],
			infos: {},
			token: '',
			member_data: {},
			ranking: 0,
			pointlist: [],
			shoplist: [],
			page: 1,
			last_page: 1,
			loading: false
		};
	},
	onLoad() {
		uni.$on('authRequired', this.onAuthRequired)
	},
	onUnload() {
		uni.$off('authRequired', this.onAuthRequired)
	},
	beforeDestroy() {
		uni.$off('authRequired', this.onAuthRequired)
	},
	unmounted() {
		uni.$off('authRequired', this.onAuthRequired)
	},
	onReachBottom() {
		if (this.last_page > this.page) {
			this.page++
			if (this.check == 0) {
				this.pointRanking()
			} else {
				this.shopRanking()
			}
		}
	},
	onShow() {
		this.token = uni.getStorageSync('token')
		if (this.token) {
			this.userinfo()
			this.orderNum()
		} else {
			this.$nextTick(() => {
				if (this.$refs.login && typeof this.$refs.login.open === 'function') {
					this.$refs.login.open()
				}
			})
		}
		this.pointRanking()
	},
	methods: {
		orderNum() {
			let params = {
				order_status: 'waitpay,waitstock,cart'
			}
			orderNum(params).then(res => {
				// 判空保护：接口异常时 res.data 为 null，三层取值会抛错并中断整页渲染
				const d = (res && res.code == 0 && res.data) || {}
				const gaid = this.gaidlist || []
				if (gaid[0]) gaid[0].num = d.waitpay || 0
				if (gaid[1]) gaid[1].num = d.waitstock || 0
				if (gaid[2]) gaid[2].num = d.cart || 0
			})
		},
		change(e) {
			this.current = e
			if (this.check == 0) {
				this.pointRanking()
			} else {
				this.shopRanking()
			}
		},
		pointRanking() {
			let params = {
				type: this.current + 1,
				page: this.page
			}
			this.loading = true
			pointRanking(params).then(res => {
				const d = (res && res.code == 0 && res.data) || {}
				const rows = Array.isArray(d.list) ? d.list : []
				this.member_data = d.member_data || {}
				this.last_page = d.page_count || 0
				this.pointlist = this.page > 1 ? this.pointlist.concat(rows) : rows
				this.ranking = d.ranking || {}
				this.loading = false
			}).catch(() => {
				this.loading = false
			})
		},
		shopRanking() {
			let params = {
				type: this.current + 1,
				page: this.page
			}
			this.loading = true
			shopRanking(params).then(res => {
				const d = (res && res.code == 0 && res.data) || {}
				const rows = Array.isArray(d.list) ? d.list : []
				this.last_page = d.page_count || 0
				this.shoplist = this.page > 1 ? this.shoplist.concat(rows) : rows
				this.loading = false
			}).catch(() => {
				this.loading = false
			})
		},
		loginsuccess() {
			this.token = uni.getStorageSync('token')
			this.userinfo()
			this.orderNum()
		},
		userinfo() {
			userinfo().then(res => {
				// 判空保护：登录态失效 / 网络异常时 res.data 为 null。
				// 原实现 `this.infos = res.data` 会把 infos 置为 null，
				// 模板里 infos.headimg / infos.member_level / Number(infos.balance) 立刻抛错，
				// 渲染中断 = 整页白屏（微信审核判定为「页面白屏」）。
				if (!res || res.code != 0 || !res.data) {
					// 登录态失效时回到未登录空态，其余情况保留骨架数据
					if (res && res._authRequired) {
						this.token = ''
						this.resetLoginState()
					}
					this.infos = this.infos || {}
					return
				}
				this.infos = res.data
				if (res.data.site_id) {
					uni.setStorageSync('user_site_id', res.data.site_id)
				}
				uni.setStorageSync('userInfo', res.data)
			})
		},
		// 未登录/登录态失效：清空个人数据并回到未登录引导，避免展示过期信息
		resetLoginState() {
			this.infos = {}
			this.gaidlist = (this.gaidlist || []).map(item => ({ ...item, num: 0 }))
		},
		onAuthRequired() {
			// http.js 检测到 token 失效时会派发该事件（跨页面同步登录态）
			this.token = ''
			this.resetLoginState()
		},
		gonext(e) {
			if (e == 'next') {
				this.$refs.login.open(true)
				return
			}
			if (e == 'waitpay') {
				uni.navigateTo({
					url: '/otherpages/order/order?current=1'
				})
				return
			}
			if (e == 'waitstock') {
				uni.navigateTo({
					url: '/otherpages/order/order?current=3'
				})
				return
			}
			if (e == 'after') {
				uni.navigateTo({
					url: '/otherpages/order/order?current=4'
				})
				return
			}
			if (e == 'all') {
				uni.navigateTo({
					url: '/otherpages/order/order?current=0'
				})
				return
			}
			uni.navigateTo({
				url: `/otherpages/${e}/${e}`
			})
		},
	}
}
</script>

<style lang="scss">
.hb {
	position: fixed;
	width: 124rpx;
	height: 124rpx;
	right: 0;
	bottom: 10vh;
	z-index: 999;
}

.container {
	border-radius: 24rpx;

	.aaas {
		background: #fff;
		padding-bottom: 24rpx;
		border-radius: 24rpx;
	}

	.rank-list {
		min-height: 400rpx;
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
				width: 375rpx;
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

.bgs {
	width: 100vw;
	height: 640rpx;
	position: absolute;
	left: 0;
	top: 0;
	z-index: -1;
}

.tools {
	background: #fff;
	border-radius: 28rpx;
	margin: 32rpx;
	padding-bottom: 32rpx;
}

.nums {
	width: 72rpx;
	height: 72rpx;
	position: relative;
	margin-top: 32rpx;

	image {
		width: 100%;
		height: 100%;
	}

	>view {
		position: absolute;
		right: 0;
		top: 0;
		width: 28rpx;
		height: 28rpx;
		background: #F54040;
		border-radius: 16rpx;
		font-weight: 500;
		font-size: 20rpx;
		color: #FFFFFF;
		text-align: center;
		line-height: 28rpx;
	}
}

.part {
	margin: 32rpx;

	>view {
		width: calc((100% - 16rpx)/2);
		height: 120rpx;
		box-sizing: border-box;
		border-radius: 24rpx;
		background: #fff;
		padding: 20rpx 24rpx;
		position: relative;
		display: flex;
		justify-content: center;
		flex-direction: column;

		>image {
			width: 72rpx;
			height: 72rpx;
			position: absolute;
			right: 16rpx;
			top: 50%;
			transform: translateY(-50%);
		}
	}

	.balance {
		background: linear-gradient(180deg, #FFF5F5 0%, #FFFFFF 100%);
		border: 4rpx solid #FFFFFF;

		>view:first-child {
			font-weight: 400;
			font-size: 24rpx;
			color: #EB6767;
		}

		>view:nth-child(2) {
			font-size: 40rpx;
			color: #F51D1D;
			font-weight: bold;
			line-height: 1;
		}
	}

}

.infos {
	margin: 0 32rpx;

	.members {
		height: 401rpx;
		margin-top: -27rpx;
		position: relative;
		box-sizing: border-box;
		padding-top: 100rpx;
		padding-left: 20rpx;

		>image:first-child {
			width: 100%;
			height: 100%;
			position: absolute;
			left: 0;
			top: 0;
		}

		.relative {
			position: relative;
			z-index: 2;
			color: #fff;
		}

		>view:nth-child(2) {
			font-weight: bold;
			font-size: 40rpx;
			color: #FFFFFF;
		}

		.linea {
			height: 2rpx;
			background: linear-gradient(270deg, rgba(255, 180, 142, 0) 0%, #FFB48E 52%, rgba(255, 180, 142, 0) 100%);
			position: relative;
			z-index: 2;
			margin-top: 28rpx;
		}

		.fcolor {
			color: #FAD5B4 !important;
		}

		.upbtn {
			line-height: 1;
			width: 156rpx;
			height: 56rpx;
			background: linear-gradient(90deg, rgba(255, 255, 255, 0.8) 0%, #FFFFFF 100%);
			border-radius: 28rpx;
			position: relative;
			z-index: 2;
			margin-right: 33rpx;
			font-weight: 600;
			font-size: 26rpx;
			color: #222222;
		}

		.jin {
			width: 156rpx;
			height: 56rpx;
			background: linear-gradient(90deg, #FFE7CC 0%, #FFB48E 100%);
			border-radius: 28rpx;
			position: relative;
			z-index: 2;
			margin-right: 33rpx;
			font-weight: 600;
			font-size: 26rpx;
			color: #222222;
		}
	}
}

.avatar {
	width: 138rpx;
	height: 138rpx;
	position: relative;

	>image:last-child {
		width: 112rpx;
		height: 112rpx;
		border-radius: 50%;
		position: absolute;
		left: 50%;
		top: 50%;
		transform: translate(-50%, -50%);
	}

	>image:first-child {
		width: 138rpx;
		height: 138rpx;
	}
}
</style>