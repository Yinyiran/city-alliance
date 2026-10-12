<template>
	<view>
		<view class="container">
			<uv-input placeholder="请填写需要修改的手机号码" type="number" v-model="mobile" maxlength="11" border="none" :customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">新手机号</view>
				</template>
			</uv-input>
			<uv-input placeholder="请输入短信验证码" v-model="code" border="none" :customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">验证码</view>
				</template>
				<template v-slot:suffix>
					<view class="getcode" v-if="!isend" @click="getcode">获取验证码</view>
					<view class="getcode" style="color: #CCCCCC;" v-else>{{codetxt}}</view>
				</template>
			</uv-input>	
		</view>
		<view class="btn" :style="{opacity:cansub?1:0.32}" @click="submit">确认修改</view>
	</view>
</template>

<script>
	import {mobileCode,mobileEdit} from "@/common/js/api.js"
	import { mobile } from '@/uni_modules/uv-ui-tools/libs/function/test.js';
	export default {
		data() {
			return {
				customStyle:"padding:28rpx",
				isend:false,
				mobile:"",
				codetxt:"60s后重新获取",
				times:60,
				key:"",
				code:''
			};
		},
		computed:{
			cansub(){
				if(this.code&&mobile(this.mobile)){
					return true
				}
				return false
			}
		},
		methods:{
			submit(){
				if(!this.cansub) return;
				let params={
					mobile:this.mobile,
					key:this.key,
					code:this.code
				}
				mobileEdit(params).then(res=>{
					if(res.code==0){
						uni.showToast({
							title:'修改成功'
						})
					}
				})
			},
			getcode(){
				if(!mobile(this.mobile)){
					uni.showToast({
						title:'手机号格式错误',
						icon:"none"
					})
					return
				}
				let params={
					mobile:this.mobile
				}
				if(!this.isend){
					mobileCode(params).then(res=>{
						this.isend=true
						this.key=res.data.key
						let time=setInterval(()=>{
							this.times--
							this.codetxt=this.times+'s后重新获取'
							if(this.times==1){
								clearInterval(time)
								this.times=10
								this.isend=false
							}
						},1000)
					})
				}
				
			},
		}
	}
</script>

<style lang="scss">
	.btn{
		height: 96rpx;
		background: linear-gradient( 90deg, #F5AB18 0%, #F55606 100%);
		border-radius: 48rpx;
		opacity: 0.32;
		text-align: center;
		line-height: 96rpx;
		margin: 64rpx 32rpx;
		font-weight: 600;
		font-size: 32rpx;
		color: #FFFFFF;
	}
	.container{
		margin: 20rpx 32rpx;
		background: #fff;
		border-radius: 24rpx;
		.font{
			font-size: 32rpx;
			color: #222222;
			width: 128rpx;
		}
		.getcode{
			font-weight: 400;
			font-size: 32rpx;
			color: #F55606;
		}
	}
</style>
