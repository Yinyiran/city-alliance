<template>
	<view class="content">
		<!-- 头部 -->
		<uv-navbar bgColor="#ff4d19" :placeholder="true">
			<view class="flex" slot="left">
				<image class="navImg" src="/static/image/userNone.png" mode="aspectFill"></image>
				<view class="font-32 font-weight white m-l-20 m-r-20">白苏叶店铺</view>
				<view class="flex_center changeShopSty">
					<view class="iconfont icon-qiehuan lh-24 white"></view>
					<view class="lh-24 white">切换店铺</view>
				</view>
			</view>
			<view class="" slot="center">
			</view>
		</uv-navbar>
		<view class="shopBodyBg ">
			<view class="flex_b p-30">
				<view v-for="(item,index) in listA" @click="goRouter(item)" class="listASty" :key="index"
					:style="'backgroundImage: url('+item.image+')'">
					<view class="Atitle font-26 white">{{item.title}}</view>
				</view>
			</view>
			<view class="certifySty flex_b">
				<view class="flex">
					<image src="../../static/home/b.png" mode="aspectFill"></image>
					<view class="f-t font-30 m-r-20">收款认证</view>
					<view class="font-28 graycolor">折扣买单待认证</view>
				</view>
				<view class="iconfont icon-yousanjiao graycolor"></view>
			</view>

		</view>
		<view class="flex_b " style="padding: 30rpx 20rpx;padding-bottom: 0;">
			<view class="infoBox flex_c">
				<view class="">
					<view class="flex_b">
						<view class="flex">
							<text class="font-28 f-tA">今日交易额(元)</text>
							<text @click.stop="show=!show" v-if="show" class="iconfont icon-icon- m-l-4"></text>
							<text @click.stop="show=!show" v-else class="iconfont icon-yincang m-l-4"></text>
						</view>
						<view class="iconfont icon-yousanjiao grey"></view>
					</view>
					<view class="font-weight font-48 f-tA">{{show?0.00:'***'}} </view>
				</view>
				<view class="flex">
					<view class="m-r-50">
						<view class="font-28 grey">收款笔数</view>
						<view class="font-40 font-weight graycolor">{{show?0:'***'}}</view>
					</view>
					<view class="">
						<view class="font-28 grey">顾客数</view>
						<view class="font-40 font-weight graycolor">{{show?0:'***'}}</view>
					</view>
				</view>
			</view>
			<view class="infoBox flex_c" @click="$util.redirectTo('/otherpages/businessEchars/businessEchars')">
				<view class="flex_b m-b-10">
					<view class="flex">
						<text class="font-28 f-tA ">实时账单</text>
					</view>
					<view class="iconfont icon-yousanjiao"></view>
				</view>
				<view v-for="(item,index) in 3" :key="index" class="flex_b">
					<view class="flex">
						<view v-if="index!==1" style="color: #39af37;" class="iconfont icon-weixinzhifu2 font-40">
						</view>
						<view v-else style="color: #06b4fd;" class="iconfont icon-zhifubao font-40"></view>
						<view class="font-32 font-weight f-tA m-l-12">+10.00</view>
					</view>
					<view class="grey font-26">07-03</view>
				</view>
			</view>
		</view>

		<view class="wbg p-20 m-t-30 m-r-20 m-l-20 bor-10">
			<view class="font-30 font-weight-6 m-b-18">经营工具</view>
			<uv-grid :col="4" @click="click">
				<uv-grid-item v-for="(item,index) in listB" :key="index">
					<image :src="item.image" class="w-74 h-74" mode="aspectFill"></image>
					<view class="m-t-14 font-26">{{item.title}}</view>
				</uv-grid-item>
			</uv-grid>
		</view>
		<view class="wbg p-20 m-t-30 m-r-20 m-l-20 bor-10">
			<view class="font-30 font-weight-6 m-b-18">店铺管理</view>
			<uv-grid :col="4" @click="click">
				<uv-grid-item v-for="(item,index) in listC" :key="index">
					<image :src="item.image" class="w-74 h-74" mode="aspectFill"></image>
					<view class="m-t-14 font-26">{{item.title}}</view>
				</uv-grid-item>
			</uv-grid>
		</view>
		<view class="wbg p-20 m-t-30 m-r-20 m-l-20 bor-10">
			<view class="font-30 font-weight-6 m-b-18">营销推广</view>
			<uv-grid :col="4" @click="click">
				<uv-grid-item>
					<image src="../../static/home/yx1.png" class="w-74 h-74" mode="aspectFill"></image>
					<view class="m-t-14 font-26">支付有礼</view>
				</uv-grid-item>
			</uv-grid>
		</view>
		<view class="wbg p-20 m-t-30 m-r-20 m-l-20 bor-10">
			<view class="font-30 font-weight-6 m-b-18">其他服务</view>
			<uv-grid :col="4" @click="click">
				<uv-grid-item v-for="(item,index) in listD" :key="index" @click="goRouter(item)">
					<image :src="item.image" class="w-74 h-74" mode="aspectFill"></image>
					<view class="m-t-14 font-26">{{item.title}}</view>
				</uv-grid-item>
			</uv-grid>
		</view>

		<view class="h-400">

		</view>
		<uv-popup ref="popup" round="10">
			<view class="popupBoxHome">
				<view class="font-36 font-weight f-t textC">添加官方客服</view>
				<view class="font-36 font-weight f-t textC">申请更高的收款额度</view>
				<image src="../../static/logo.png" mode="aspectFill"></image>
				<view class="font-28 f-tA">长按扫码添加官方客服</view>
			</view>
		</uv-popup>
		<uv-picker ref="picker" :columns="columns" keyName="Name" @confirm="confirm"></uv-picker>
		<TabBar :current-page="0" />
	</view>
</template>

<script>
	export default {
		data() {
			return {
				shopList: [],
				textList: [],
				columns: [
					[]
				],
				columnsA: [
					[{
						label: '雪月夜',
						// 其他属性值
						id: 2021
						// ...
					}, {
						label: '冷夜雨',
						id: 804
					}]
				],
				addressVal: {},
				activeId: 0,
				show: false,
				listA: [{
						image: require('../../static/home/n1.png'),
						title: '收款码',
						router: '/otherpages/payCode/payCode'
					},
					{
						image: require('../../static/home/n2.png'),
						title: '团购核销'
					},
					{
						image: require('../../static/home/n3.png'),
						title: '收款账户',
						router: '/otherpages/receiptnAccount/receiptnAccount'
					},
					{
						image: require('../../static/home/n4.png'),
						title: '生意报表'
					},
				],
				listB: [{
						image: require('../../static/home/jy1.png'),
						title: '收款码'
					},
					{
						image: require('../../static/home/jy2.png'),
						title: '语音设备'
					},
					{
						image: require('../../static/home/jy3.png'),
						title: '我的团购'
					},
					{
						image: require('../../static/home/jy4.png'),
						title: '团购核销'
					},
				],
				listC: [{
						image: require('../../static/home/shop1.png'),
						title: '收款账户'
					},
					{
						image: require('../../static/home/shop2.png'),
						title: '员工管理'
					},
					{
						image: require('../../static/home/shop3.png'),
						title: '店铺资料'
					},
					{
						image: require('../../static/home/shop4.png'),
						title: '地推商务'
					},
				],
				listD: [{
						image: require('../../static/home/other1.png'),
						title: '下载中心'
					},
					{
						image: require('../../static/home/other2.png'),
						title: '在线客服'
					},
					{
						image: require('../../static/home/other3.png'),
						title: '设置'
					}
				],
				bannerList: [],
				type: 0,
				homeInfo: {},
				classList: [],
				keywords: '',
				typeList: [{
					name: '最新消息',
					id: 0
				}]
			}
		},
		onLoad(res) {
			if (res.invite_code) {
				uni.setStorage({
					key: 'invite_code',
					data: res.invite_code,
					success: () => {}
				});
			}
			if (res.scene) {
				// var sceneParams = decodeURIComponent(res.scene);
				// console.log(sceneParams, 'sss');
			}
		},
		onPullDownRefresh() {


		},
		onShow() {

		},
		methods: {
			goRouter(item) {
				if (item.title == '在线客服') {
					this.$refs.popup.open('bottom');
				} else {
					this.$util.redirectTo(item.router)
				}
			},
			addressFn() {
				this.$refs.picker.open();
			},
		}
	}
</script>

<style scoped lang="scss">
	.popupBoxHome {
		background-image: linear-gradient(to top, #fff, #fff, #fff, #ffc1ae);
		text-align: center;
		padding-top: 40rpx;

		view {
			text-align: center;
		}

		image {
			margin-top: 50rpx;
			margin-bottom: 50rpx;
			text-align: center;
			width: 339rpx;
			height: 344rpx;
		}
	}

	.infoBox {
		box-sizing: border-box;
		padding: 30rpx;
		width: 345rpx;
		height: 308rpx;
		background-color: #fff;
		border-radius: 20rpx;
	}

	.navImg {
		width: 53rpx;
		height: 53rpx;
		border-radius: 50%;
		border: 1rpx solid #fff;
	}

	.listASty {
		width: 151rpx;
		height: 167rpx;
		background-size: cover;
		position: relative;

		.Atitle {
			position: absolute;
			bottom: 0;
			width: 151rpx;
			text-align: center;
			bottom: 15rpx;
		}
	}

	.wbg {
		background: #fff;
	}

	.shopBodyBg {
		height: 350rpx;
		background-color: #ff4d19;
		position: relative;

		.certifySty {
			position: absolute;
			bottom: 0;
			width: 690rpx;
			padding: 0 20rpx;
			box-sizing: border-box;
			height: 108rpx;
			border-radius: 20rpx 20rpx 0 0;
			opacity: 1;

			left: 50%;
			margin-left: -345rpx;
			background: #ffffff;

			image {
				width: 66rpx;
				height: 66rpx;
			}
		}
	}

	.changeShopSty {
		width: 167rpx;
		height: 46rpx;
		flex-shrink: 0;
		border-radius: 47rpx;
		background: #FF8765;
	}

	.tabsList {
		padding: 18rpx 0;
		flex-wrap: wrap;

		view {
			padding: 0 10rpx;
			height: 42rpx;
			background: #F5E2DC;
			border-radius: 21rpx;
			font-weight: 400;
			font-size: 22rpx;
			color: #F43C04;
			line-height: 42rpx;
			text-align: center;
			margin-right: 20rpx;
		}
	}

	.infoFun {
		view {
			height: 36rpx;
			border: 3rpx solid #3079FF;
			padding: 0 10rpx;
			line-height: 36rpx;
			border-radius: 8rpx;
			font-weight: 400;
			font-size: 22rpx;
			color: #3079FF;
			line-height: 36rpx;
			margin-left: 20rpx;
		}

		view:nth-child(2) {
			color: #F33B04;
			border: 3rpx solid #F33B04;
		}
	}





	.infoItem {
		border-bottom: 1rpx solid #f3f3f3;
		padding-bottom: 20rpx;
	}



	.userSty {
		image {
			width: 52rpx;
			height: 52rpx;
			border-radius: 50%;
		}
	}

	.boxSty {
		padding: 25rpx;
		background-color: #fff;

		.boxStyTitle {
			display: flex;
			align-items: center;
			justify-content: space-between;

		}

		.boxItem {
			font-family: HarmonyOS Sans SC, HarmonyOS Sans SC;
			font-weight: 600;
			font-size: 28rpx;
			color: #808080;
			position: relative;
		}

		.itemActive {
			font-size: 32rpx;
			color: #000;
			z-index: 99;
		}

		.avtiveBg {
			position: absolute;
			z-index: -1;
			top: -10rpx;
			left: -10rpx;
			width: 62rpx;
			height: 62rpx;
			border-radius: 50%;
			background: linear-gradient(123deg, #FFE0C2 0%, rgba(255, 232, 156, 0) 100%);
		}
	}

	.towModel {
		width: 690rpx;
		margin: 20rpx auto;
		display: flex;
		justify-content: space-between;

		image {
			width: 330rpx;
			height: 174rpx;
		}
	}

	.dengJi {
		position: relative;
		width: 690rpx;

		image {
			width: 690rpx;
			height: 136rpx;
			border-radius: 10rpx;
		}

		margin: 20rpx auto;

		.dengJiTxt {
			position: absolute;
			left: 30rpx;
			top: 0rpx;
			height: 136rpx;
			display: flex;
			flex-direction: column;
			justify-content: space-evenly;

			:nth-child(1) {
				color: #ED783E;
			}

			:nth-child(2) {
				width: 128rpx;
				height: 40rpx;
				background: #ED783E;
				font-family: HarmonyOS Sans SC, HarmonyOS Sans SC;
				font-weight: 400;
				font-size: 24rpx;
				color: #FFFFFF;
				line-height: 40rpx;
				text-align: center;
				border-radius: 20rpx;
			}
		}
	}

	.fonList {
		display: flex;
		align-items: center;
		flex-wrap: wrap;

		.fonItem {
			width: 20%;
			text-align: center;

			image {
				width: 84rpx;
				height: 84rpx;
				margin-top: 14rpx;
			}
		}
	}


	.swiperSty {
		height: 247px;
		position: relative;

		.swiperStyTxtA {
			position: absolute;
			top: 170rpx;
			right: 30rpx;
			height: 52rpx;
			background: #EAF1FF;
			opacity: 0.9;
			padding: 0 10rpx;
			border-radius: 10rpx;
		}

		.swiperStyTxtB {
			position: absolute;
			bottom: 24rpx;
			left: 30rpx;
			// height: 52rpx;
			// background: #EAF1FF;
			// opacity: 0.9;
			// padding: 0 10rpx;
			border-radius: 20rpx;
			width: 560rpx;
			// image {
			// 	width: 36rpx;
			// 	height: 36rpx;
			// 	border-radius: 50%;
			// }
		}
	}

	.inputBox {
		width: 326rpx;
		height: 62rpx;
		opacity: 1;
		border-radius: 32rpx;
		border: 2rpx solid rgba(227, 230, 235, 1);
	}
</style>