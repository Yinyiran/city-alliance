<template>
	<view>
		<nvabar type='1' title='我的钱包' titleStyle='color:#222'></nvabar>
		<image src="/otherpages/static/bg7.png" class="bgs" mode=""></image>
		<view class="mains">
			<image src="/otherpages/static/img2.png" mode=""></image>
			<view class="fcolor font-24">账户总余额 (元)</view>
			<view class="fcolor1 font-weight-6 font-56 m-t-8">{{((parseFloat(info.balance || 0) + parseFloat(info.balance_money || 0)) || 0).toFixed(2)}}</view>

			<view class="reward-section m-t-24">
				<!-- 第一行：今日现金奖励 + 收益明细 + 提现 -->
				<view class="reward-row acea-row row-middle row-between">
					<view class="row-label-val acea-row row-middle">
						<text class="fcolor font-24">今日现金奖励</text>
						<text class="fcolor1 font-weight-6 font-30 m-l-12">￥{{today_money || '0.00'}}</text>
					</view>
					<view class="row-actions acea-row row-middle">
						<view class="btn-action acea-row row-middle row-center" @click="$util.redirectTo('/otherpages/income/income')">
							<text class="btn-txt">收益明细</text>
							<uv-icon name="arrow-right" color="#ffffff" size="10" customStyle="margin-left: 8rpx;"></uv-icon>
						</view>
						<view class="btn-action btn-main acea-row row-middle row-center m-l-16" @click="$util.redirectTo('/otherpages/cashOut/cashOut')">
							<text class="btn-txt-gold">提现</text>
						</view>
					</view>
				</view>

				<view class="row-divider"></view>

				<!-- 第二行：积分奖励 + 积分明细 + 积分兑换 -->
				<view class="reward-row acea-row row-middle row-between">
					<view class="row-label-val acea-row row-middle">
						<text class="fcolor font-24">积分奖励</text>
						<text class="fcolor1 font-weight-6 font-30 m-l-12">{{info.point || 0}}</text>
					</view>
					<view class="row-actions acea-row row-middle">
						<view class="btn-action acea-row row-middle row-center" @click="$util.redirectTo('/otherpages/myPoint/myPoint')">
							<text class="btn-txt">积分明细</text>
							<uv-icon name="arrow-right" color="#ffffff" size="10" customStyle="margin-left: 8rpx;"></uv-icon>
						</view>
						<view class="btn-action btn-main acea-row row-middle row-center m-l-16" @click="gopage">
							<text class="btn-txt-gold">积分兑换</text>
						</view>
					</view>
				</view>
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
						<view class="m-l-16">
							<view class="font-28">{{item.nickname}}</view>
							<view class="type m-t-8" v-if='Number(item.account_data>0)'>{{item.type_name}}</view>
							<view class="type type1 m-t-8" v-else>{{item.type_name}}</view>
						</view>
					</view>
					<view class="" style="text-align: right;">
						<view class="font-30 font-weight-6" v-if='Number(item.account_data>0)'>{{item.account_data}}</view>
						<view class="font-30 font-weight-6" v-else style="color: #F51D1D;">{{item.account_data}}</view>
						<view class="font-22" style="color: #999999;">{{item.create_time}}</view>
					</view>
				</view>
				<uv-empty v-if="!list.length" text="暂无明细记录" mode="list" marginTop="60"></uv-empty>
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
		background: linear-gradient(135deg, #F5AB18 0%, #F55606 100%);
		border-radius: 24rpx;
		box-sizing: border-box;
		padding: 32rpx;

		>image {
			position: absolute;
			right: 48rpx;
			top: 36rpx;
			width: 120rpx;
			height: 120rpx;
			opacity: 0.9;
		}

		.reward-section {
			.reward-row {
				min-height: 68rpx;

				.row-label-val {
					flex: 1;
					overflow: hidden;
					margin-right: 12rpx;
				}

				.row-actions {
					flex-shrink: 0;

					.btn-action {
						width: 140rpx;
						height: 52rpx;
						background: rgba(255, 255, 255, 0.22);
						border: 1px solid rgba(255, 255, 255, 0.5);
						border-radius: 26rpx;
						color: #FFFFFF !important;
						box-sizing: border-box;

						.btn-txt {
							color: #FFFFFF !important;
							font-size: 22rpx;
							font-weight: 400;
						}

						&:active {
							opacity: 0.8;
						}
					}

					.btn-main {
						background: rgba(255, 255, 255, 0.8) !important;
						border: 1px solid rgba(255, 255, 255, 0.9) !important;
						box-shadow: 0 4rpx 10rpx rgba(0, 0, 0, 0.08);

						.btn-txt-gold {
							color: #F55606 !important;
							font-size: 22rpx;
							font-weight: 400;
						}
					}
				}
			}

			.row-divider {
				height: 1px;
				background: rgba(255, 255, 255, 0.18);
				margin: 10rpx 0;
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