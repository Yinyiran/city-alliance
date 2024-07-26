<template>
	<view>
		<view class="" v-if="true">
			<view v-for="(item,index) in shopList" :key="index" class="modelBox">
				<view class="shopItemA flex">
					<image src="../../static/logo.png" mode="aspectFill"></image>
					<view class="m-l-20 h-100 flex_c">
						<view class="flex_b w-496">
							<view class="font-32 font-weight">白苏叶店铺</view>
							<view class="font-28 f-t font-weight" v-if="!item">认证中</view>
						</view>
						<view class="font-28 graycolor">河南省郑州市金城国际广场B座503</view>
					</view>
				</view>
				<view class="flex_b m-t-20">
					<view class="flex tabItem">
						<view v-for="(item,index) in 2" :key="index">
							食品零售
						</view>
					</view>
					<button type="mini" v-if="item">进入店铺</button>
					<button type="miniA" v-else>查看进度</button>
				</view>
			</view>
		</view>

		<view class="noneShop" v-else>
			<view class="font-56 font-weight textC white">你当前还未注册店铺</view>
			<view class="font-28 textC white">欢迎加入蛙蛙来购，成为千万商家之一</view>
			<view class="modelBox" style="margin-top: 290rpx;">
				<view class="f-tA font-weight font-36 textC m-b-50">现在享受免费入驻</view>
				<view class="flex_b flowSty">
					<image src="../../static/image/t1.png" mode=""></image>
					<image src="../../static/image/dx.png" mode=""></image>
					<image src="../../static/image/t2.png" mode=""></image>
					<image src="../../static/image/dx.png" mode=""></image>
					<image src="../../static/image/t3.png" mode=""></image>
				</view>
				<view class="flex_b m-t-20 m-b-80">
					<view class="f-tA font-28 w-113 textC font-weight">0元入驻</view>
					<view class="f-tA font-28 w-113 textC font-weight">引流拓客</view>
					<view class="f-tA font-28 w-113 textC font-weight">精准用户</view>
				</view>
				<button type="primary">立即入驻</button>
			</view>
		</view>

		<view class="bomPosition">
			<view class="flex_b bomBtn">
				<view class="flex_center">
					<view class="iconfont icon-h f-t"></view>
					<view class="f-t font-28 m-l-6">退出登录</view>
				</view>
				<view class="flex_center" @click="btnFn(2)">
					<view class="iconfont icon-shouji "></view>
					<view class=" font-28 m-l-6">手机号换绑</view>
				</view>
				<view class="flex_center" style="background-color: #ff4d1a;" @click="btnFn(3)">
					<view class="iconfont icon-tianjia white"></view>
					<view class="white font-28 m-l-6">注册新店铺</view>
				</view>
			</view>
		</view>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				shopList: [true, true, false]
			};
		},
		methods: {
			btnFn(res) {
				if (res == 2) {
					this.$util.redirectTo('/otherpages/replacePhone/replacePhone')
				}
				if (res == 3) {
					this.$util.redirectTo('/otherpages/setUpShop/setUpShop')
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	.flowSty {


		image:nth-child(2n-1) {
			width: 113rpx;
			height: 113rpx;
			border-radius: 50%;
		}

		image:nth-child(2n) {
			width: 70rpx;
			height: 4rpx;
			// background-color: red;
		}
	}

	.noneShop {
		background-image: url('http://tuanshenghuo.xing-peng.com/mobile/img/E.png');
		width: 750rpx;
		height: 850rpx;
		background-size: cover;
		padding-top: 120rpx;
	}

	.bomBtn {
		padding: 20rpx;

		.flex_center {
			width: 223rpx;
			height: 89rpx;
			border-radius: 100rpx;
			background-color: #f5f6f9;
		}
	}

	.tabItem {
		view {
			height: 48rpx;
			background-color: #f4f5f8;
			border-radius: 24rpx;
			line-height: 48rpx;
			padding: 0 10rpx;
			margin-right: 20rpx;
			color: #606882;
		}
	}

	.shopItemA {
		image {
			width: 107rpx;
			height: 107rpx;
			border-radius: 50%;
		}

	}
</style>