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
				<view v-for="(item,index) in listA" class="listASty" :key="index"
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
		<view class="flex_b " style="padding: 30rpx 20rpx;">
			<view class="infoBox">
				
			</view>
			<view class="infoBox">
				
			</view>
		</view>




		<view class="h-400"></view>
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
				listA: [{
						image: require('../../static/home/n1.png'),
						title: '收款码'
					},
					{
						image: require('../../static/home/n2.png'),
						title: '团购核销'
					},
					{
						image: require('../../static/home/n3.png'),
						title: '收款账户'
					},
					{
						image: require('../../static/home/n4.png'),
						title: '生意报表'
					},
				],
				funList: [{
						image: require('../../static/logo.png'),
						title: '食堂招商',
						router: '/otherpages/squareList/squareList'
					},
					{
						image: require('../../static/logo.png'),
						title: '求货供货',
						router: '/pages/square/square'
					},
					{
						image: require('../../static/logo.png'),
						title: '团餐优商',
						router: '/enterprisepages/enterpriseHome/enterpriseHome'
					},
					{
						image: require('../../static/logo.png'),
						title: '二手市场',
						router: '/pages/square/square?type=erShou'
					},
					{
						image: require('../../static/logo.png'),
						title: '邀请有奖',
						router: '/otherpages/invite/invite'
					},
					{
						image: require('../../static/logo.png'),
						title: '团膳招标',
						router: '/otherpages/tsSeek/tsSeek'
					},
					{
						image: require('../../static/logo.png'),
						title: '食堂整包',
						router: '/otherpages/canteen/canteen'
					},
					{
						image: require('../../static/logo.png'),
						title: '求职/招聘',
						router: '/otherpages/recruit/recruit'
					},
					{
						image: require('../../static/logo.png'),
						title: '档口达人',
						router: '/otherpages/seekStalls/seekStalls'
					},
					{
						image: require('../../static/logo.png'),
						title: '团餐云课堂',
						router: '/otherpages/classList/classList'
					},
					// /otherpages/spread/spread 推广
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
			addressFn() {
				this.$refs.picker.open();
			},
		}
	}
</script>

<style scoped lang="scss">
	.infoBox {
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