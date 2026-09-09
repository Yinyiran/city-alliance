<template>
	<view>
		<uv-sticky bg-color="#FFF">
			<nvabar type='1' title='提现账号' titleStyle='color:#222'></nvabar>
		</uv-sticky>
		<view class="cash acea-row row-middle row-between">
			<view class="acea-row row-middle">
				<image src="/otherpages/static/warn.png" class="w-32 h-32 m-r-12" mode=""></image>
				注意：请填写并选择自己想要提现的方式
			</view>
		</view>
		<view class="m-r-32  m-l-32 m-t-20">
			<uv-subsection :list="list1" :current="current" custom-style="height: 60rpx;border-radius: 30rpx;"
				custom-item-style="border-radius: 30rpx;" activeColor='#F55606' bgColor="#FFFFFF" mode="button"
				@change="change"></uv-subsection>
			<view class="m-t-20 mians">
				<template v-if="current==0">
					<view class="p-28">
						<uv-input placeholder="请填写正确的支付宝名称" v-model="realname" border="none">
							<template v-slot:prefix>
								<text class="font-32">支付宝名称</text>
							</template>
						</uv-input>
					</view>
					<view class="p-28">
						<uv-input placeholder="请填写正确的支付宝账号" v-model="bank_account" border="none">
							<template v-slot:prefix>
								<text class="font-32">支付宝账号</text>
							</template>
						</uv-input>
					</view>
				</template>
				<template v-if="current==1">
					<view class="p-28">
						<uv-input placeholder="请填写真实姓名" v-model="realname" border="none">
							<template v-slot:prefix>
								<text class="font-32">真实姓名</text>
							</template>
						</uv-input>
					</view>
					<view class="p-28">
						<uv-input placeholder="请填写正确的银行卡号" v-model="bank_account" border="none">
							<template v-slot:prefix>
								<text class="font-32">银行卡号</text>
							</template>
						</uv-input>
					</view>
					<view class="p-28">
						<uv-input placeholder="请填写正确的开户行信息" v-model="branch_bank_name" border="none">
							<template v-slot:prefix>
								<text class="font-32">开户行信息</text>
							</template>
						</uv-input>
					</view>
				</template>
				<template v-if="current==2">
					<view class="p-28">
						<uv-input placeholder="请填写正确的微信名称" v-model="realname" border="none">
							<template v-slot:prefix>
								<text class="font-32">微信名称</text>
							</template>
						</uv-input>
					</view>
					<view class="p-28">
						<uv-input placeholder="请填写正确的微信账号" v-model="bank_account" border="none">
							<template v-slot:prefix>
								<text class="font-32">微信账号</text>
							</template>
						</uv-input>
					</view>
				</template>
				<!-- <view class="acea-row row-middle row-center p-28 " v-if="current==2">
					<uv-icon name="weixin-fill" color="#07C160" size="28"></uv-icon>
					<text style="color: #07C160;" class="font-32 m-r-8 m-l-8">微信账号授权</text>
					<uv-icon name="arrow-right" color="#07C160" size="18"></uv-icon>
				</view> -->
			</view>
			<view class="mians font-32 acea-row row-middle row-between m-t-20 p-28">
				设为默认提现方式
				<uv-switch v-model="value" activeColor='#F55606'></uv-switch>
			</view>
			<view class="btn" :style="{opacity:!cansub?0.2:1}" @click="submit">确认修改</view>
		</view>
	</view>
</template>

<script>
	import {
		memberbankaccountadd,
		memberbankaccountInfo,
		memberbankaccountEdit
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				list1: ['支付宝提现', '银行卡提现', '微信提现'],
				current: 0,
				value: true,
				realname: '',
				branch_bank_name: '',
				bank_account:"",
				id:""
			};
		},
		computed:{
			cansub(){
				if(this.current==0){
					if(this.realname&&this.bank_account){
						return true
					}else{
						return false
					}
				}else if(this.current==1){
					if(this.realname&&this.bank_account&&this.branch_bank_name){
						return true
					}else{
						return false
					}
				}else{
					if(this.realname&&this.bank_account){
						return true
					}else{
						return false
					}
				}
				return false
			},
		},
		onLoad(res) {
			if(res.id){
				this.id=res.id
				this.memberbankaccountInfo()
			}
			
		},
		methods: {
			memberbankaccountInfo(){
				let params={
					id:this.id
				}
				memberbankaccountInfo(params).then(res=>{
					this.realname=res.data.realname
					this.branch_bank_name=res.data.branch_bank_name
					this.bank_account=res.data.bank_account
					this.current=res.data.withdraw_type=='alipay'?0:res.data.withdraw_type=='bank'?1:2
					this.value=res.data.is_default==1?true:false
				})
			},
			change(index) {
				this.current = index;
				this.realname = ''
				this.branch_bank_name = ''
				this.bank_account=''
			},
			submit() {
				if(!this.cansub) return
				let params = {
					withdraw_type: this.current == 0 ? 'alipay' : this.current == 1 ? 'bank' : this.current == 2 ? 'wechatpay' :
						'',
					realname:this.realname,
					branch_bank_name:this.branch_bank_name,
					bank_account:this.bank_account,
					is_default:this.value?1:0,
					id:this.id
				}
				if(this.id){
					memberbankaccountEdit(params).then(res=>{
						if(res.code==0){
							uni.showToast({
								title:'修改成功',
								success:res=>{
									setTimeout(()=>{
										uni.navigateBack()
									},2000)
								}
							})
						}
					})
					return
				}
				memberbankaccountadd(params).then(res=>{
					if(res.code==0){
						uni.showToast({
							title:'修改成功',
							success:res=>{
								setTimeout(()=>{
									this.realname = ''
									this.branch_bank_name = ''
									this.bank_account=''
								},2000)
							}
						})
					}
				})
			},
		}
	}
</script>

<style lang="scss">
	::v-deep .uv-subsection--button__bar {
		background-color: #FFEDE9 !important;
	}

	.btn {
		font-weight: 600;
		font-size: 32rpx;
		color: #FFFFFF;
		text-align: center;
		background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
		border-radius: 48rpx;
		line-height: 96rpx;
		margin-top: 64rpx;
	}

	.mians {
		background: #fff;
		border-radius: 24rpx;
		overflow: hidden;
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
</style>