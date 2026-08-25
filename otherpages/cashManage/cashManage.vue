<template>
	<view>
		<uv-sticky bg-color="#fff">
			<nvabar type='1' title='提现账户管理' titleStyle='color:#222'></nvabar>
		</uv-sticky>
		<view class="m-20">
			<view v-for="(item,index) in list" class="items" :key="index">
				<template v-if="item.withdraw_type=='alipay'">
					<view class="acea-row row-middle row-between m-b-16">
						<view class="fcolor font-30">支付宝名称</view>
						<view class="font-30">{{item.realname}}</view>
					</view>
					<view class="acea-row row-middle row-between m-b-16">
						<view class="fcolor font-30">支付宝账号</view>
						<view class="font-30">{{item.bank_account}}</view>
					</view>
				</template>
				<template v-if="item.withdraw_type=='wechatpay'">
					<view class="acea-row row-middle row-between m-b-16">
						<view class="fcolor font-30">微信名称</view>
						<view class="font-30">{{item.realname}}</view>
					</view>
					<view class="acea-row row-middle row-between m-b-16">
						<view class="fcolor font-30">微信账号</view>
						<view class="font-30">{{item.bank_account}}</view>
					</view>
				</template>
				<template v-if="item.withdraw_type=='bank'">
					<view class="acea-row row-middle row-between m-b-16">
						<view class="fcolor font-30">真实姓名</view>
						<view class="font-30">{{item.realname}}</view>
					</view>
					<view class="acea-row row-middle row-between m-b-16">
						<view class="fcolor font-30">银行卡号</view>
						<view class="font-30">{{item.bank_account}}</view>
					</view>
					<view class="acea-row row-middle row-between">
						<view class="fcolor font-30">开户行信息</view>
						<view class="font-30">{{item.branch_bank_name}}</view>
					</view>
				</template>
				<view class="acea-row row-middle row-between m-t-32">
					<view class="acea-row row-middle" @click="sets(item.id)">
						<radio value="" :checked="item.is_default==1" />
						<text class="defa" v-if="item.is_default==1">已默认</text>
						<text class="defa fcolor" v-else>设为默认</text>
					</view>
					<view class="acea-row row-middle">
						<view class="edit m-r-20" v-if="item.is_default==0" @click="dels(item.id)">删除</view>
						<view class="edit" @click="edits(item.id)">编辑</view>
					</view>
				</view>
			</view>
		</view>
		<view class="h-300"></view>
		<view class="btms iphone-x">
			<view class="" @click="adds">新增</view>
		</view>
	</view>
</template>

<script>
	import {
		memberbankaccountPage,
		setDefaultdefa,
		memberbankaccountDels
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				page: 1,
				list: []
			};
		},
		onShow() {
			this.memberbankaccountPage()
		},
		methods: {
			dels(e){
				uni.showModal({
					content:'确认删除当前提现账号？',
					success:as=>{
						if(as.confirm){
							let params={
								id:e
							}
							memberbankaccountDels(params).then(res=>{
								if(res.code==0){
									this.memberbankaccountPage()
								}
							})
						}
					}
				})
			},
			sets(e){
				let params={
					id:e
				}
				setDefaultdefa(params).then(res=>{
					this.memberbankaccountPage()
				})
			},
			edits(e){
				uni.navigateTo({
					url: '/otherpages/account/account?id='+e
				})
			},
			adds() {
				uni.navigateTo({
					url: '/otherpages/account/account'
				})
			},
			memberbankaccountPage() {
				let params = {
					page: this.page,
				}
				memberbankaccountPage().then(res => {
					this.list = res.data.list
				})
			},
		}
	}
</script>

<style lang="scss">
	.btms {
		position: fixed;
		bottom: 0;
		left: 0;
		background: #fff;
		text-align: center;
		width: 100vw;

		>view {
			margin: 20rpx 40rpx;
			line-height: 96rpx;
			background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
			border-radius: 48rpx;
			font-weight: 600;
			font-size: 32rpx;
			color: #FFFFFF;
		}
	}

	radio {
		transform: scale(0.7);
	}

	.edit {
		width: 100rpx;
		height: 48rpx;
		background: #FFFFFF;
		border-radius: 24rpx;
		border: 1rpx solid #DDDDDD;
		text-align: center;
		line-height: 48rpx;
	}

	.defa {
		font-weight: 400;
		font-size: 24rpx;
		color: #F55606;
	}

	.fcolor {
		color: #999999;
	}

	.items {
		margin-bottom: 20rpx;
		background: #fff;
		padding: 32rpx;
		border-radius: 24rpx;
	}
</style>