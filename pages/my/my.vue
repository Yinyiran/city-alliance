<template>
	<view>
		<view class="tops">
			<image src="/static/a.png" mode=""></image>
			<view class="acea-row row-middle row-center cash" @click="caches" v-if="(info.myinfo||{}).is_manager==1">提现
				<uv-icon name="arrow-right" color="#fff" size="10"></uv-icon>
			</view>
			<view class="acea-row row-middle relative">
				<image :src="$util.img(info.logo)" v-if="info.logo" class="w-102 h-52  m-r-16" mode=""></image>
				<text class="font-32 text-more" style="flex: 1;" v-if="token">{{info.site_name}}</text>
				<view class="font-32" style="flex: 1;" v-else @click="$refs.login.open()">
					<view class="login-btn">立即登录</view>
				</view>
				<view class="cutsa m-l-18" v-if="(info.myinfo||{}).is_manager==1" @click="open">
					<image src="/static/icon5.png" class="w-20 h-20 m-r-6" mode=""></image>
					切换
				</view>
				<image src="/static/qr.png" class="w-48 h-48 m-l-18 qr-btn" mode="aspectFit" @click="goShopQr" v-if="token"></image>
				<image src="/static/seeting.png" class="w-48 h-48 m-l-18 setting-btn" mode="aspectFit" @click="goSetting" v-if="token"></image>
			</view>
			<view class="acea-row row-middle m-t-12 relative">
				<text class="font-28 font-weight-6 m-r-12">{{(info.myinfo||{}).username}}</text>
				<view class="mans acea-row row-middle row-center" v-if="token">
					<image src="/static/icon4.png" class="w-22 h-22 m-r-4" mode=""></image>
					{{(info.myinfo||{}).is_manager==1?'主理人':'员工'}}
				</view>
			</view>
			<view class="checkes acea-row row-middle row-center row-between m-t-24">
				<view class="a-i">
					<view class="" style="color: #9E9285;">总订单数（单)</view>
					<view class="font-48">{{total.total_count||0}}</view>
				</view>
				<view class="lines"></view>
				<view class="a-i">
					<view class="" style="color: #9E9285;">销售总额（元)</view>
					<view class="font-48">{{total.total_sum||0}}</view>
				</view>
			</view>
			<image src="/static/bg3.png" mode=""></image>
		</view>
		<view class="members acea-row row-middle row-between">
			<view class="">
				<view class="">
					<view class="p-20 a-1">
						<view class="">自营订单 (单)</view>
						<view class="">{{ziying.totalcount}}</view>
					</view>
					<view class="line m-r-20 m-l-20"></view>
					<view class="p-20 a-1">
						<view class="">金额 (元)</view>
						<view class="">{{ziying.totalsum}}</view>
					</view>
				</view>
				<view class="acea-row row-middle row-center p-t-12 p-b-12" @click="gonext1(0)" style="color: #B2B6C2;">
					查看明细
					<image src="/static/go2.png" class="w-18 h-18 m-l-6" mode=""></image>
				</view>
			</view>
			<view class="">
				<view class="">
					<view class="p-20 a-1">
						<view class="">带单订单 (单)</view>
						<view class="">{{daidan.totalcount}}</view>
					</view>
					<view class="line m-r-20 m-l-20"></view>
					<view class="p-20 a-1">
						<view class="">金额 (元)</view>
						<view class="">{{daidan.totalsum}}</view>
					</view>
				</view>
				<view class="acea-row row-middle row-center p-t-12 p-b-12" style="color: #B2B6C2;" @click="gonext1(1)">
					查看明细
					<image src="/static/go2.png" class="w-18 h-18 m-l-6" mode=""></image>
				</view>
			</view>
			<view class="">
				<view class="">
					<view class="p-20 a-1">
						<view class="">贡献订单 (单)</view>
						<view class="">{{gongxian.totalcount}}</view>
					</view>
					<view class="line m-r-20 m-l-20"></view>
					<view class="p-20 a-1">
						<view class="">金额 (元)</view>
						<view class="">{{gongxian.totalsum}}</view>
					</view>
				</view>
				<view class="acea-row row-middle row-center p-t-12 p-b-12" style="color: #B2B6C2;" @click="gonext1(2)">
					查看明细
					<image src="/static/go2.png" class="w-18 h-18 m-l-6" mode=""></image>
				</view>
			</view>
		</view>
		<view class="btns acea-row row-middle row-between" v-if="(info.myinfo||{}).is_manager==1">
			<view class="acea-row row-middle row-center" @click="gonext('deposit')">
				<image src="/static/icon3.png" class="w-40 h-40" mode=""></image>
				<text>保证金充值</text>
				<image src="/static/go.png" class="w-16 h-16" mode=""></image>
			</view>
			<view class="acea-row row-middle row-center" @click="godatas">
				<image src="/static/icon4.png" class="w-40 h-40" mode=""></image>
				<text>数据大屏</text>
				<image src="/static/go.png" class="w-16 h-16" mode=""></image>
			</view>
		</view>
		<view class="container">
			<view class="row-between m-b-32">
				<scroll-view scroll-x="true">
					<view v-for="(item,index) in tablist" class="itens" :class="{'on':check==index}"
						@click="cuts(index)" :key="index">
						{{item}}
						<image src="/static/down1.png" class="w-24 h-8" v-if="check==index" mode=""></image>
					</view>
				</scroll-view>
			</view>
			<uv-subsection :list="list1" :current="current" activeColor='#1D1E24'
				custom-style="height: 60rpx;border-radius: 30rpx;" custom-item-style="border-radius: 30rpx;"
				@change="change"></uv-subsection>
			<view class="h-24"></view>
			<view class="meminfo acea-row row-middle" v-if="levelData&&levelData.length>0">
				<view class="memit" v-for="(item,index) in levelData">
					<view class="">{{item.level_name}}</view>
					<view class="">{{item.counts||0}}</view>
				</view>
			</view>
			<view class="m-b-24" v-if="check==0">
				<uv-tabs :list="list2" lineWidth="30" @click="onclick" :current="check1" :scrollable="false"
					lineColor="#f56c6c" :activeStyle="{
							fontWeight: 'bold',
				    	}" :inactiveStyle="{
							color:'#222',
						}" itemStyle="height: 34px;font-size:28rpx;"></uv-tabs>
			</view>

			<view class="defas acea-row row-middle row-between" v-if="myinfo&&myinfo.myinfo">
				<view class="acea-row row-middle">
					<view class="ranks">
						<image src="/static/rank.png" mode=""></image>
						<text>{{myinfo.myrank}}</text>
					</view>
					<image :src="$util.img(myinfo.myinfo.headimg)" v-if="myinfo.myinfo&&myinfo.myinfo.headimg"
						class="w-52 h-52 bor-50 m-l-24" mode=""></image>
					<image src="/static/avatar.png" v-else class="w-52 h-52 bor-50 m-l-24" mode=""></image>
					<text class="font-28 m-l-12" style="color: #222;">{{(myinfo.myinfo||{}).username}}</text>
					<text class="my">(我)</text>
				</view>
				<view class="font-28" style="color: #222;">{{(myinfo.myinfo||{}).invite_count||0}}</view>
			</view>
			<view class="acea-row row-middle row-between p-24 titles">
				<view class="acea-row row-middle">
					<text class="m-r-20">排名</text>
					<text>名称</text>
				</view>
				<text>会员数量</text>
			</view>
			<view v-for="(item,index) in list" class="m-b-20 defas acea-row row-middle row-between"
				:class="{'rankbg1':index==0,'rankbg2':index==1,'rankbg3':index==2}" :key="index">
				<view class="acea-row row-middle">
					<view class="ranks">
						<image src="/static/rank1.png" v-if="index==0" mode=""></image>
						<image src="/static/rank2.png" v-else-if="index==1" mode=""></image>
						<image src="/static/rank3.png" v-else-if="index==2" mode=""></image>
						<image src="/static/rank.png" v-else mode=""></image>
						<text v-if="index>2">{{index+1}}</text>
					</view>
					<image :src="$util.img(item.headimg)" v-if="item.headimg" class="w-52 h-52 bor-50 m-l-24" mode="">
					</image>
					<image src="/static/avatar.png" v-else class="w-52 h-52 bor-50 m-l-24" mode=""></image>
					<text class="font-28 m-l-12" style="color: #222;">{{item.username}}</text>
				</view>
				<view class="font-28" style="color: #222;">{{item.invite_count||0}}</view>
			</view>
		</view>
		<uv-popup ref="popup" :round='10' mode="bottom" :closeable="true">
			<view class="">
				<view class="font-40 font1">切换企业</view>
				<scroll-view scroll-y="true" style="max-height: 40vh;">
					<view v-for="(item,index) in slist" class="acea-row row-middle items" :key="index"
						@click="site_id=item.site_id">
						<image :src="$util.img(item.store_image)" class="w-80 h-80 bor-40 m-r-16" mode=""></image>
						<view class="">
							<view class="font-28 font-weight">{{item.store_name}}</view>
							<view class="" style="color: #999999;">{{item.team_num}}</view>
						</view>
						<radio :checked="item.site_id==site_id" />
					</view>
				</scroll-view>
				<view class="btna" @click="submit">确定</view>
			</view>
		</uv-popup>
		<login ref="login" @loginsuccess='loginsuccess'></login>
	</view>
</template>

<script>
	import {
		myIndex,
		invitemember,
		orderbyselfrank,
		conorderrank,
		getInfo,
		shopList,
		shopLogin
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				list1: ['年度排行榜', '月度排行榜'],
				tablist: ['员工会员邀约排行', '员工自签单排行', '贡献签单排行'],
				check: 0,
				check1: 0,
				list2: [{
					name: '普通会员'
				}, {
					name: '钻石会员',
				}, {
					name: '黑钻会员',
				}, {
					name: '合伙人',
				}],
				daidan: {},
				gongxian: {},
				ziying: {},
				total: {},
				levelData: [],
				list: [],
				current: 0,
				myinfo: {},
				info: {},
				slist: [],
				site_id: uni.getStorageSync('site_id'),
				token: ""
			};
		},
		onLoad() {
			if (uni.getStorageSync('token')) {
				this.shopList()
			}
		},
		onShow() {
			if (uni.getStorageSync('token')) {
				this.token = uni.getStorageSync('token')
				this.myIndex()
				this.getInfo1()
				this.qiehuan()
			} else {
				this.token = ''
				this.resetPageData()
				this.showLoginModal()
			}

		},
		methods: {
			goSetting() {
				if (!this.token) {
					this.$refs.login.open()
					return
				}
				uni.navigateTo({
					url: '/otherpages/setting/setting'
				})
			},
			goShopQr() {
				if (!this.token) {
					this.$refs.login.open()
					return
				}
				uni.navigateTo({
					url: '/otherpages/shopQr/shopQr'
				})
			},
			// 未登录时清空页面数据，避免残留上一登录账号的内容
			resetPageData() {
				this.daidan = {}
				this.gongxian = {}
				this.ziying = {}
				this.total = {}
				this.levelData = []
				this.list = []
				this.myinfo = {}
				this.info = {}
				this.slist = []
				this.site_id = ''
			},
			// 未登录弹窗提示（可取消），点"去登录"打开登录授权弹窗
			showLoginModal() {
				uni.showModal({
					title: '提示',
					content: '您还未登录，是否前往登录？',
					confirmText: '去登录',
					cancelText: '取消',
					success: (res) => {
						if (res.confirm) {
							this.$refs.login.open()
						}
					}
				})
			},
			loginsuccess() {
				this.token = uni.getStorageSync('token')
				// 并行加载，互不阻塞：getInfo1 异常/挂起时不影响其他数据刷新
				this.getInfo1()
				this.shopList()
				this.myIndex()
				this.qiehuan()
			},
			submit() {
				let params = {
					site_id: this.site_id
				}
				shopLogin(params).then(res => {
					if (res.code == 0) {
						uni.setStorageSync('site_id', res.data.site_id)
						this.close()
						this.site_id = res.data.site_id
						this.getInfo1()
						this.myIndex()
						this.qiehuan()
					}

				})
			},
			close() {
				this.site_id = uni.getStorageSync('site_id')
				this.$refs.popup.close()
			},
			open() {
				this.$refs.popup.open()
			},
			goSetting() {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				uni.navigateTo({
					url: '/otherpages/setting/setting'
				})
			},
			shopList() {
				shopList().then(res => {
					this.slist = res.data
				})
			},
			caches() {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				uni.navigateTo({
					url: '/otherpages/cashOUT/cashOUT',
					fail: e => {
						console.log(e);
					}
				})
			},
			godatas() {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				uni.navigateTo({
					url: '/otherpages/dataspage/dataspage?site_id=' + this.info.site_id
				})
			},
			async getInfo1() {
				try {
					const res = await getInfo()
					this.site_id = res.data.site_id
					uni.setStorageSync('site_id', res.data.site_id)
					this.info = res.data
				} catch (e) {
					// 站点信息获取失败不阻断页面其他数据加载
				}
			},
			change(e) {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				this.current = e
				this.qiehuan()
			},
			qiehuan() {
				if (this.check == 0) {
					this.invitemember()
				} else if (this.check == 1) {
					this.orderbyselfrank()
				} else {
					this.conorderrank()
				}
			},
			cuts(e) {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				this.check = e
				this.qiehuan()
			},
			onclick(e) {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				this.check1 = e.index
				this.qiehuan()
			},
			conorderrank() {
				let params = {
					selectby: this.current == 0 ? 'year' : 'month',
				}
				conorderrank(params).then(res => {
					this.levelData = res.data.levelData
					this.list = res.data.list
					this.myinfo = res.data.myinfo || null
				})
			},
			orderbyselfrank() {
				let params = {
					selectby: this.current == 0 ? 'year' : 'month',
					// member_level: this.check1 + 1
				}
				orderbyselfrank(params).then(res => {
					this.levelData = res.data.levelData
					this.list = res.data.list
					this.myinfo = res.data.myinfo || null
				})
			},
			invitemember() {
				let params = {
					selectby: this.current == 0 ? 'year' : 'month',
					member_level: this.check1 + 1
				}
				invitemember(params).then(res => {
					this.levelData = res.data.levelData
					this.list = res.data.list
					this.myinfo = res.data.myinfo
				})
			},
			myIndex() {
				myIndex().then(res => {
					this.daidan = res.data.daidan
					this.gongxian = res.data.gongxian
					this.ziying = res.data.ziying
					this.total = res.data.total
				})
			},
			gonext1(e) {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				uni.navigateTo({
					url: '/otherpages/order/order?current=' + e
				})
			},
			gonext(e) {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
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
	.btna {
		background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
		margin: 20rpx 40rpx;
		font-weight: 600;
		font-size: 32rpx;
		color: #FFFFFF;
		text-align: center;
		line-height: 96rpx;
		border-radius: 80rpx;
	}

	.items {
		margin: 0 40rpx;
		margin-bottom: 32rpx;
		position: relative;

		radio {
			position: absolute;
			right: 0;
			top: 50%;
			transform: translateY(-50%) scale(0.8);
		}
	}

	.font1 {
		padding: 40rpx 0;
		text-align: center;
	}

	.cutsa {
		background: rgba(0, 0, 0, 0.2);
		border-radius: 16rpx;
		font-weight: 400;
		font-size: 22rpx;
		color: #FFFFFF;
		padding: 0 12rpx;
	}

	.qr-btn,
	.setting-btn {
		flex-shrink: 0;
		transition: opacity 0.2s;

		&:active {
			opacity: 0.7;
		}
	}

	.login-btn {
		display: inline-block;
		padding: 10rpx 36rpx;
		border-radius: 40rpx;
		border: 1rpx solid #ff9e20;
		color: #ff9e20;
		font-size: 26rpx;
		line-height: 1.4;
		background: rgba(255, 255, 255, 0.15);
	}

	.container {
		margin: 32rpx;
		background: #FFFFFF;
		border-radius: 24rpx;
		padding: 24rpx;

		.meminfo {
			height: 100rpx;
			background: #F5F5F5;
			border-radius: 12rpx;
			margin-bottom: 34rpx;
			flex-wrap: nowrap;

			.memit {
				width: calc((100% - 3rpx)/4);
				text-align: center;

				>view:first-child {
					font-weight: 400;
					font-size: 20rpx;
					color: #999999;
				}

				>view:last-child {
					font-weight: 600;
					font-size: 22rpx;
					color: #222222;
				}
			}

			.line {
				width: 1rpx;
				height: 60rpx;
				background: #DDDDDD;
			}
		}

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

		scroll-view {
			white-space: nowrap;

			// padding-bottom: 8rpx;
			.itens {
				// width: calc((100% - 16rpx)/2);
				line-height: 72rpx;
				display: inline-block;
				background: #F5F5F5;
				border-radius: 16rpx;
				text-align: center;
				box-sizing: border-box;
				position: relative;
				font-weight: 400;
				font-size: 28rpx;
				color: #222222;
				padding: 0 20rpx;
				margin-right: 16rpx;
				margin-bottom: 8rpx;
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
					z-index: 1;
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

	.btns {
		margin: 32rpx;

		>view {
			height: 100rpx;
			background: #272A33;
			border-radius: 16rpx;
			border: 1rpx solid #FAD5B4;
			width: calc((100% - 16rpx)/2);
			box-sizing: border-box;

			text {
				color: #fff;
				font-weight: 400;
				font-size: 32rpx;
				color: #FFFFFF;
				margin-left: 12rpx;
				margin-right: 6rpx;
			}
		}
	}

	.members {
		margin: 0 32rpx;
		position: relative;
		z-index: 99;

		>view {
			width: calc((100% - 32rpx)/3);
			// height: 208rpx;
			background: #272A33;
			border-radius: 16rpx;
			overflow: hidden;

			>view:first-child {
				background: #3E465C;

				.line {
					border: 1rpx dashed #6F7DA3;
				}

				.a-1 {
					>view:first-child {
						font-weight: 400;
						font-size: 24rpx;
						color: #B2B6C2;
						margin-bottom: 8rpx;
					}

					>view:last-child {
						font-size: 48rpx;
						color: #FFFFFF;
						line-height: 58rpx;
					}
				}
			}
		}
	}

	page {
		background: #181A1F;
	}

	.checkes {
		background: rgba(255, 255, 255, 0.3);
		border-radius: 24rpx;
		height: 130rpx;
		text-align: center;

		.a-i {
			width: calc((100% - 64rpx - 2rpx)/2);

			>view:last-child {
				font-size: 40rpx;
				color: #523E2E;
				font-weight: bold;
			}
		}

		.lines {
			width: 2rpx;
			height: 82rpx;
			background: #F5E0CE;
		}
	}

	.mans {
		font-weight: 400;
		font-size: 20rpx;
		color: #FFFFFF;
		width: 102rpx;
		height: 32rpx;
		background: linear-gradient(90deg, #2F323D 0%, #1F1F29 100%);
		border-radius: 16rpx;
	}

	.tops {
		margin: 20rpx 32rpx;
		height: 340rpx;
		position: relative;
		padding: 32rpx;
		box-sizing: border-box;

		.relative {
			position: relative;
			z-index: 99;
		}

		.cash {
			position: absolute;
			right: 32rpx;
			top: 78rpx;
			background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
			border-radius: 32rpx;
			width: 140rpx;
			height: 64rpx;
			color: #fff;
			font-size: 26rpx;
			z-index: 9999;
		}

		>image:first-child {
			width: 100%;
			height: 100%;
			position: absolute;
			left: 0;
			top: 0;
			z-index: -1;
		}

		>image:last-child {
			position: absolute;
			top: 260rpx;
			left: 0;
			width: 100%;
			height: 453rpx;
			z-index: 1;
		}
	}
</style>