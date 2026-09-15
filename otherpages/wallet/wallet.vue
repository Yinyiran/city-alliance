<template>
	<view>
		<nvabar type='1' title='我的钱包' titleStyle='color:#222'></nvabar>
		<image src="/otherpages/static/bg7.png" class="bgs" mode=""></image>
		<view class="mains">
			<image src="/otherpages/static/img2.png" mode=""></image>
			<view class="fcolor">账户总余额 (元)</view>
			<view class="fcolor1 font-weight-6 font-56">{{((parseFloat(info.balance || 0) + parseFloat(info.balance_money || 0)) || 0).toFixed(2)}}</view>
			<view class="acea-row row-middle m-t-20">
				<view class="">
					<view class="fcolor">今日现金奖励 (元)</view>
					<view class="fcolor1 font-weight-6 font-32">{{today_money}}</view>
				</view>
				<view class="fcolor1 acea-row row-middle row-center btn1" @click="$util.redirectTo('/otherpages/income/income')">收益明细
					<uv-icon name="arrow-right" color="#fff" size="12"></uv-icon>
				</view>
				<view class="yline"></view>
				<view class="">
					<view class="fcolor">积分奖励 (个)</view>
					<view class="fcolor1 font-weight-6 font-32">{{info.point}}</view>
				</view>
				<view class="fcolor1 acea-row row-middle row-center btn1" @click="$util.redirectTo('/otherpages/myPoint/myPoint')">积分明细
					<uv-icon name="arrow-right" color="#fff" size="12"></uv-icon>
				</view>
			</view>
			<view class="btns acea-row row-middle row-between">
				<view class="" @click="$util.redirectTo('/otherpages/cashOut/cashOut')">提现</view>
				<view class="" @click="gopage">积分兑换</view>
			</view>
		</view>
		<view class="m-32">
			<uv-subsection :list="list1" :current="current" custom-style="height: 60rpx;border-radius: 30rpx;"
				custom-item-style="border-radius: 30rpx;" activeColor='#F55606' bgColor="#FFFFFF" mode="button"
				@change="change"></uv-subsection>
				<view class="container p-32 m-t-32">
					<view class="font-32 font-weight-6 m-b-32">金额明细</view>
					<view v-for="(item,index) in list" class="items acea-row row-middle row-between" :key="index">
						<view class="acea-row row-middle">
							<image src="/static/avatar.png" v-if="!item.headimg" class="w-64 h-64 bor-32" mode=""></image>
							<image :src="$util.img(item.headimg)" v-else class="w-64 h-64 bor-32" mode=""></image>
							<view class="">
								<view class="font-28">{{item.nickname}}</view>
								<view class="type" v-if='Number(item.account_data>0)'>{{item.type_name}}</view>
								<view class="type type1" v-else>{{item.type_name}}</view>
							</view>
						</view>
						<view class="" style="text-align: right;">
							<view class="font-30 font-weight-6" v-if='Number(item.account_data>0)'>{{item.account_data}}</view>
							<view class="font-30 font-weight-6" v-else style="color: #F51D1D;">{{item.account_data}}</view>
							<view class="font-22" style="color: #999999;">{{item.create_time}}</view>
						</view>
					</view>
				</view>
		</view>
	</view>
</template>

<script>
	import { userinfo,moneyAccount } from '@/common/js/api.js';
	export default {
		data() {
			return {
				list1: [
					'今日', '昨日', '本月', '上月'
				],
				current: 0,
				info:{},
				accs:{},
				today_money:"",
				list:[],
				page:1,
				last_page:1
			};
		},
		onShow() {
			this.moneyAccount()
			this.getinfo()
		},
		onReachBottom() {
			if(this.last_page>this.page){
				this.page++
				this.moneyAccount()
			}
		},
		methods: {
			moneyAccount(){
				let params={
					type:this.current+1,
					page:this.page
				}
				moneyAccount(params).then(res=>{
					this.today_money=res.data.today_money
					this.list=this.page>1?this.list.concat(...res.data.list):res.data.list
					this.last_page=res.data.page_count
				})
			},
			getinfo(){
				userinfo().then(res=>{
					this.info=res.data
				})
			},
			gopage(){
				uni.switchTab({
					url:'/pages/car/car'
				})
			},
			change(index) {
				this.current = index;
				this.page=1
				this.moneyAccount()
			}
		}
	}
</script>

<style lang="scss">
	.container{
		background: #FFFFFF;
		border-radius: 24rpx;
		.items{
			height: 120rpx;
			background: #F8F8F8;
			border-radius: 12rpx;
			margin-bottom: 20rpx;
			padding: 0 24rpx;
			.type{
				font-weight: 400;
				font-size: 20rpx;
				color: #FA7F28;
				height: 28rpx;
				background: #FFF5ED;
				border-radius: 4rpx;
				width: fit-content;
				line-height: 28rpx;
				padding: 0 6rpx;
			}
			.type1{
				background: #E6FFEF;
				color: #25B85B;
			}
		}
	}
	::v-deep .uv-subsection--button__bar {
		background-color: #FFEDE9 !important;
	}

	.bgs {
		width: 100vw;
		height: 616rpx;
		position: fixed;
		top: 0;
		left: 0;
		z-index: -1;
	}

	.mains {
		margin: 28rpx 32rpx;
		position: relative;
		z-index: 2;
		height: 412rpx;
		background: linear-gradient(135deg, #F5AB18 0%, #F55606 100%);
		border-radius: 24rpx;
		box-sizing: border-box;
		padding: 32rpx;

		>image {
			position: absolute;
			right: 62rpx;
			top: 62rpx;
			width: 120rpx;
			height: 120rpx;
		}

		.yline {
			width: 1rpx;
			height: 60rpx;
			background: #FFB6A3;
			margin: 0 28rpx;
		}

		.btn1 {
			width: 140rpx;
			height: 48rpx;
			background: rgba(255, 255, 255, 0.28);
			border-radius: 48rpx;
			margin-left: 25rpx;
		}
		.btns{
			margin-top: 36rpx;
			>view{
				width: calc((100% - 16rpx)/2);
				text-align: center;
				line-height: 88rpx;
				font-weight: 600;
				font-size: 32rpx;
				color: #F55606;
				background: rgba(255,255,255,0.92);
				border-radius: 48rpx;
			}
		}
	}

	.fcolor {
		color: #FFE7E0;
	}

	.fcolor1 {
		color: #fff;
	}
</style>