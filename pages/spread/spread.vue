<template>
	<view>
		<uv-navbar :fixed="false" bgColor="rgba(255,255,255,0)">
			<template v-slot:left>
				<view class="uv-nav-slot font-36 font-weight">
					推广管理
				</view>
			</template>
		</uv-navbar>
		<view class="part bor-10" @click="$util.redirectTo('/otherpages/amount/amount')">
			<view class="acea-row row-middle row-between">
				<view class="font-30 font-weight-6">我的钱包</view>
				<view class="acea-row row-middle">
					<text class="fcolor font-26 font-weight-6 m-r-10">去提现</text>
					<uv-icon name='play-right-fill' size="10" color="#8d93a5"></uv-icon>
				</view>
			</view>
			<view class="m-t-30 m-b-30">
				<uv-line color="#1c274c1a"></uv-line>
			</view>
			<view class="acea-row row-middle row-around">
				<view class="">
					<view class="">可提现(元)</view>
					<view class="m-t-16 font-28 font-weight-6">0.00</view>
				</view>
				<uv-line color="#1c274c1a" direction="col" length="17"></uv-line>
				<view class="">
					<view class="">待到账(元)</view>
					<view class="m-t-16 font-28 font-weight-6">450</view>
				</view>
				<uv-line color="#1c274c1a" direction="col" length="17"></uv-line>
				<view class="">
					<view class="">累计到账(元)</view>
					<view class="m-t-16 font-28 font-weight-6">0</view>
				</view>
			</view>
		</view>
		<view class="part bor-10">
			<view class="acea-row">
				<view class="font-30 font-weight-6">店铺数据</view>
			</view>
			<view class="m-t-30 m-b-30">
				<uv-line color="#1c274c1a"></uv-line>
			</view>
			<view class="acea-row row-middle row-around">
				<view class="">
					<view class="">我的店铺粉丝</view>
					<view class="m-t-16 font-28 font-weight-6">0.00</view>
				</view>
				<uv-line color="#1c274c1a" direction="col" length="17"></uv-line>
				<view class="">
					<view class="">今日收益(元)</view>
					<view class="m-t-16 font-28 font-weight-6">450</view>
				</view>
				<uv-line color="#1c274c1a" direction="col" length="17"></uv-line>
				<view class="">
					<view class="">累计收益(元)</view>
					<view class="m-t-16 font-28 font-weight-6">0</view>
				</view>
			</view>
		</view>
		<view class="part bor-10">
			<uv-grid :border="false" col='4' @click="onClick">
				<uv-grid-item v-for="(item,index) in list" :key="index">
					<image :src="item.image" class="w-76 h-76" mode=""></image>
					<view class="m-t-16 font-26">{{item.name}}</view>
				</uv-grid-item>
			</uv-grid>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list: [{
						image: require('@/static/index/icon1.png'),
						name: '推广订单'
					},
					{
						image: require('@/static/index/icon2.png'),
						name: '我的粉丝'
					}
				]
			};
		},
		methods:{
			onClick(e){
				console.log(e);
				let url=''
				if (e==0) {
					url='/otherpages/extendOredr/extendOredr'
				} else{
					url='/otherpages/fans/fans'
				}
				this.$util.redirectTo(url)
			}
		}
	}
</script>

<style lang="scss" scoped>
	page {
		background: #F6F7FB;
	}

	view {
		color: #1C274C;
		font-size: 24rpx;
	}

	.fcolor {
		color: #FF4D1A;
	}

	.part {
		padding: 30rpx 20rpx;
		margin: 30rpx 20rpx;
		background: #fff;
		text-align: center;
	}
</style>