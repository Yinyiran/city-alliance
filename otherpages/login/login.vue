<template>
	<view>
		<view class="headBg">
			<view class="headBg_tit">
				<view class="font-56">欢迎加入蛙蛙来购</view>
				<view class=""></view>
			</view>
			<view class="font-36 white">成为千万商家之一</view>
			<view class="m-t-100">
				<view class="flex white font-weight font-32 m-b-26">
					<text class="iconfont icon-jingxuan m-r-10 font-36 lh-36" style="font-weight: 300;"></text>
					免费入驻
				</view>
				<view class="flex white font-weight font-32 m-b-26">
					<text class="iconfont icon-jingxuan m-r-10 font-36 lh-36" style="font-weight: 300;"></text>
					千万级用户为您引流拓客
				</view>
				<view class="flex white font-weight font-32">
					<text class="iconfont icon-jingxuan m-r-10 font-36 lh-36" style="font-weight: 300;"></text>
					精准锁定会员
				</view>
			</view>
		</view>
		<view class="bodySty">
			<view class="iptSty flex">
				<input type="tel" placeholder="请输入手机号" v-model='mobile' maxlength="11" />
			</view>
			<view class="iptSty flex_b">
				<input type="tel" placeholder="请输入验证码" maxlength="11" style="width: 445rpx;border-right: 1rpx solid #afb0b2;"/>
				<view class="font-28 f-t center" @click="getcode">{{code}}</view>
			</view>
			<view class="p-30">
				<button type="primary">登录</button>
			</view>
			<view class="grey textC font-26 m-t-16">未注册的手机号登录后将自动注册</view>
		</view>
		<view class="flex_center">
			<view class="iconfont icon-shouji f-t"></view>
			<view class="f-t font-28 m-l-6">一键授权登录</view>
		</view>
		<view class="bottom-mar"></view>

	</view>
</template>

<script>
	import {mobile} from '@/uni_modules/uv-ui-tools/libs/function/test.js';
	export default {
		data() {
			return {
				code:"发送验证码",
				mobile:'',
				is_send:false
			};
		},
		methods:{
			getcode(){
				console.log(mobile(this.mobile));
				if(!mobile(this.mobile)){
					uni.showToast({
						title:'手机号格式错误',
						icon:'none'
					})
				}else{
						let time=10
						if(!this.is_send){
							this.$api.sendRequest({
								url:'/api/sms/send',
								data:{
									mobile:this.mobile,
									event:'login'
								},
								success:res=>{
									
								}
							})
							setInterval(()=>{
								time --;
								if (time<1) {
									this.code='获取验证码'
									this.is_send=true
									return
								}
								this.code=time+'s'
							},1000)
						}
						
						
					
				}
				// if () {
					
				// }
			},
		}
	}
</script>

<style lang="scss" >
	page {
		background-color: #fff;
	}
	.bodySty {
		width: 750rpx;
		opacity: 1;
		background: #ffffff;
		border-radius: 30rpx 30rpx 0 0;
		// background-color: rebeccapurple;
		margin-top: -100rpx;
		padding-top: 50rpx;
		padding-bottom: 30rpx;
	}
	.center{
		text-align: center;
	}
	.iptSty {
		width: 600rpx;
		height: 114rpx;
		flex-shrink: 0;
		border-radius: 20rpx;
		background-color: #f6f7fb;
		margin: 0 auto;
		margin-bottom: 50rpx;
		padding: 0 25rpx;
		input {
			width: 100%;
		}
	}

	.headBg {
		padding: 40rpx;
		box-sizing: border-box;
		background-image: url('http://tuanshenghuo.xing-peng.com/mobile/img/A.png');
		background-color: indianred;
		width: 750rpx;
		height: 672rpx;
		background-size: cover;

		.headBg_tit {
			position: relative;
			height: 78rpx;

			:nth-child(1) {
				font-family: "Alibaba PuHuiTi 2.0";
				position: absolute;
				bottom: 0;
				font-weight: 600;
				color: #fff;
				z-index: 2;
			}

			:nth-child(2) {
				position: absolute;
				bottom: 10rpx;
				background-color: #ff9900;
				height: 20rpx;
				width: 444rpx;
			}

		}
	}
</style>