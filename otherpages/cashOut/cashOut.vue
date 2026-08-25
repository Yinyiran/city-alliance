<template>
	<view>
		<uv-sticky bg-color="#fff">
			<nvabar type='1' title='申请提现' titleStyle='color:#222'></nvabar>
		</uv-sticky>
		<view class="cash acea-row row-middle row-between" @click="gopage" v-if="!cashInfo||cashInfo.apply_status==3">
			<view class="acea-row row-middle">
				<image src="/static/warn.png" class="w-32 h-32 m-r-12" mode=""></image>
				当前未绑定提现账户，绑定账户后可提现
			</view>
			<uv-icon name="arrow-right" color="#FE780F"></uv-icon>
		</view>
		<view class="m-40">
			<view class="acea-row row-middle row-between">
				<view class="font-28">提现到账户</view>
				<!-- <view class="acea-row row-middle font-26" style="color: #F55606;" v-if="show" @click="gopage">
					<image src="/static/edit1.png" class="w-26 h-26 m-r-8" mode=""></image>
					修改
				</view> -->
			</view>
			<view class="nos m-t-20" v-if="cashInfo||cashInfo">当前未绑定提现账户，请前往绑定</view>
			<view class="nos1 m-t-20 acea-row row-middle" v-else>
				<view class="">
					<view class="">账户账号</view>
					<view class="">{{cashInfo.card_no}}</view>
				</view>
				<view class="p-l-20">
					<view class="">真实姓名</view>
					<view class="">{{cashInfo.realname}}</view>
				</view>
			</view>
			<view class="font-28 m-t-40">提现金额</view>
			<view class="acea-row inputs">
				<text>￥</text>
				<input type="number" v-model="apply_money" placeholder="请输入" />
				<text @click="alls">全部提现</text>
			</view>
			<view class="m-t-20" style="color: #999999;"> 当前可提现金额 <text>{{balance_money}}元</text>，一天最高可提现额度为{{max}}元</view>
			<view class="m-t-56" style="color: #999999;" v-html="introduction">
				
			</view>
			<view class="btms iphone-x">
				<view class="" @click="submit">提现</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {memberbankaccountPage,memberwithdrawapply,memberwithdrawconfig,memberopenaccountInfo} from "@/common/js/api.js"
	export default {
		data() {
			return {
				show:false,
				introduction:'',
				apply_money:'',
				max:"",
				cashInfo:{},
				balance_money:""
			};
		},
		onShow() {
			// this.memberbankaccountPage()
			this.memberopenaccountInfo()
			this.memberwithdrawconfig()
		},
		methods:{
			alls(){
				this.apply_money=this.balance_money
			},
			memberopenaccountInfo(){
				memberopenaccountInfo().then(res=>{
					this.cashInfo=res.data
				})
			},
			memberwithdrawconfig(){
				memberwithdrawconfig().then(res=>{
					this.introduction=res.data.value.introduction
					this.max=res.data.value.max
					this.balance_money=res.data.balance_money
				})
			},
			submit(){
				let params ={
					apply_money:this.apply_money,
					// transfer_type:this.cashInfo.withdraw_type,
					// realname:this.cashInfo.realname,
					// bank_name:this.cashInfo.branch_bank_name,
					// account_number:this.cashInfo.bank_account
				}
				memberwithdrawapply(params).then(res=>{
					if(res.code==0){
						uni.showToast({
							title:'申请成功',
							success:()=>{
								setTimeout(()=>{
									this.apply_money=''
								},2000)
							}
						})
					}
				})
			},
			gopage(){
				uni.navigateTo({
					url:'/otherpages/bankinfo/bankinfo'
				})
			},
			memberbankaccountPage(){
				memberbankaccountPage().then(res=>{
					if(res.data.count==0){
						this.show=false
					}else{
						this.cashInfo=res.data.list[0]
						this.show=true
					}
				})
			},
		}
	}
</script>

<style lang="scss">
	.btms{
		position: fixed;
		bottom: 0;
		left: 0;
		background: #fff;
		text-align: center;
		width: 100vw;
		>view{
			margin: 20rpx 40rpx;
			line-height: 96rpx;
			background: linear-gradient( 90deg, #F5AB18 0%, #F55606 100%);
			border-radius: 48rpx;
			font-weight: 600;
			font-size: 32rpx;
			color: #FFFFFF;
		}
	}
	.inputs {
		align-items: baseline;
		border-bottom: 1rpx solid #DDDDDD;

		text:first-child {
			height: 68rpx;
			width: 29rpx;
			font-size: 48rpx;
			margin-right: 20rpx;
		}

		input {
			height: 136rpx;
			background: #FFFFFF;
			font-size: 72rpx;
			color: #222222;
			flex: 1;
		}

		text:last-child {
			font-weight: 400;
			font-size: 26rpx;
			color: #F55606;
		}
	}

	.cash {
		padding: 0 32rpx;
		background: #FFF5ED;
		height: 72rpx;

		view {
			font-weight: 400;
			font-size: 28rpx;
			color: #FE780F;
		}
	}

	page {
		background: #fff;
	}
	.nos1{
		background: #F5F5F5;
		border-radius: 12rpx;
		padding: 20rpx;
		>view{
			box-sizing: border-box;
			width: 50%;
			position: relative;
			>view:first-child{
				font-size: 20rpx;
				color: #999999;
			}
			>view:last-child{
				font-weight: 600;
				font-size: 22rpx;
			}
		}
		>view:last-child::before{
			content: "";
			width: 1rpx;
			height: 60rpx;
			background: #DDDDDD;
			position: absolute;
			left: 0;
			top: 50%;
			transform: translateY(-50%);
		}
	}
	.nos {
		background: #F5F5F5;
		border-radius: 12rpx;
		font-weight: 400;
		font-size: 22rpx;
		color: #555555;
		text-align: center;
		line-height: 100rpx;
	}
</style>