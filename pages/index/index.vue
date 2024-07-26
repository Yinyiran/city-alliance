<template>
	<view class="content">
		<!-- 头部 -->
		<uv-navbar bgColor="rgba(0,0,0,0)">
			<view class="flex font-weight white font-34" @click="addressFn" slot="left">{{addressVal.Name||'位置'}}<text
					class="iconfont icon-arrowdown"></text>
			</view>
			<view class="inputBox flex white" slot="center">
				<text class="iconfont icon-sousuo_o m-r-8 m-l-10"></text>
				<input type="text" @confirm="searchConfirm()" v-model="keywords" placeholder="搜索感兴趣的内容"
					placeholder-class="white font-26">
			</view>
		</uv-navbar>

		<view class="swiperSty">
			<uv-swiper height="247" :list="bannerList" keyName="image" indicatorStyle="right:20rpx;bottom:100rpx;"
				indicator indicatorMode="dot" circular></uv-swiper>

			<view class="swiperStyTxtB" v-if="textList.length">
				<uv-notice-bar :text="textList" direction="column"></uv-notice-bar>
			</view>
		</view>

		
		<view class="poBom">
			<view class="infoBox">
				<button class="iconfont icon-tongzhi font-36" @click="$util.redirectTo('/pages/my/infoList')"></button>
				<view class="white lh-24" v-if="homeInfo.no_read_count">{{homeInfo.no_read_count}}</view>
			</view>
			<button class="iconfont icon-kefu font-36" open-type="contact"></button>
			<button class="iconfont icon-31huidaodingbu font-36" @click="scrollTop"></button>
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
				list: [{
					name: '最新消息',
					type: 'new'
				}, {
					name: '学校食堂',
					type: 'canteen'
				}, {
					name: '校园商铺',
					type: 'shop'
				}, {
					name: '课堂',
					type: 'course'
				}],
				bannerList: [{
					image: 'https://cdn.uviewui.com/uview/swiper/swiper1.png'
				}, {
					image: 'https://cdn.uviewui.com/uview/swiper/swiper2.png'
				}, {
					image: 'https://cdn.uviewui.com/uview/swiper/swiper3.png'
				}],
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

	.poBom {
		z-index: 999;
		position: fixed;
		bottom: 300rpx;
		right: 30rpx;

		.infoBox {
			width: 76rpx;
			height: 76rpx;
			position: relative;

			view {
				padding: 4rpx;
				position: absolute;
				right: 0;
				top: 0;
				background-color: red;
				border-radius: 50%;
			}
		}

		button {
			opacity: 0.6;
			margin: 30rpx 0;
			padding: 0;
			outline: none;
			border-radius: 50%;
			line-height: 76rpx;
			width: 76rpx;
			height: 76rpx;
			background-color: #fe440c;
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