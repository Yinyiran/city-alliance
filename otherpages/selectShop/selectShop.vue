<template>
	<view>
		<view class="">
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
		<view class="bomPosition">
			<view class="flex_b bomBtn">
				<view class="flex_center">
					<view class="iconfont icon-h f-t"></view>
					<view class="f-t font-28 m-l-6">退出登录</view>
				</view>
				<view class="flex_center" @click="btnFn">
					<view class="iconfont icon-shouji "></view>
					<view class=" font-28 m-l-6">手机号换绑</view>
				</view>
				<view class="flex_center" style="background-color: #ff4d1a;">
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
		methods:{
			btnFn() {
				this.$util.redirectTo('/otherpages/replacePhone/replacePhone')
			}
		}
	}
</script>

<style lang="scss" scoped>
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