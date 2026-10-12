<template>
	<view class="login">
		<text class="login-title">商家账号申请</text>
		<view class="login-input">
			<text class="title">商家账号:</text>
			<view class="input"><input class="uni-input" placeholder="请输入商家账号" placeholder-class="input-placeholder" v-model="registeredList.account" /></view>
		</view>
		<view class="login-input">
			<text class="title">登录密码:</text>
			<view class="input"><input class="uni-input" placeholder="请输入密码" placeholder-class="input-placeholder" v-model="registeredList.password1" password="true" /></view>
		</view>
		<view class="login-input">
			<text class="title">确认密码:</text>
			<view class="input"><input class="uni-input" placeholder="请确认密码" placeholder-class="input-placeholder" v-model="registeredList.password2" password="true" /></view>
		</view>
		<view class="login-input">
			<view class="input"><input class="uni-input" placeholder="输入验证码" placeholder-class="input-placeholder" v-model="registeredList.vcode" /></view>
			<image :src="captcha.img" mode="" @click="getImg"></image>
		</view>
		<view class="login-btn" @click="toRegistered">点击申请</view>
		<view class="login-text">
			<text class="text-one">已有账号?</text>
			<text class="text-two" @click="toLogin">立即登录</text>
		</view>
	</view>
</template>

<script>
import validate from '@/common/js/validate.js';
export default {
	data() {
		return {
			loginMode: 'account',
			registeredList: {
				account: '',
				password1: '',
				password2: '',
				vcode: ''
			},
			captcha: {
				id: '',
				img: ''
			}
		};
	},
	onReady() {
		this.getCodeImg();
	},
	methods: {
		toLogin() {
			uni.navigateTo({
				url: '../login/login'
			});
		},
		//点击刷新验证码
		getImg() {
			this.getCodeImg();
		},
		//获取验证码
		getCodeImg() {
			this.$api.sendRequest({
				url: '/shopapi/captcha/captcha',
				data: {
					captcha_id: this.captcha.id
				},
				success: res => {
					if (res.code >= 0) {
						this.captcha = res.data;
						this.captcha.img = this.captcha.img.replace(/\r\n/g, '');
					}
				}
			});
		},
		//点击注册
		toRegistered() {
			if (!this.vertify()) {
				this.$api.sendRequest({
					url: '/shopapi/register/register',
					data: {
						captcha_id: this.captcha.id,
						username: this.registeredList.account,
						password: this.registeredList.password1,
						captcha_code: this.registeredList.vcode
					},
					success: res => {
						console.log(res);
						uni.showToast({
							title: res.message,
							icon: 'none'
						});
						uni.setStorage({
							key: 'token',
							data: res.data.token
						});
						if (res.code == 0) {
							this.$util.redirectTo('../index/index', {});
						}
					}
				});
			}
		},
		vertify() {
			let rule = [];

			// 账号验证
			if (this.loginMode == 'account') {
				rule = [
					{ name: 'account', checkType: 'required', errorMsg: '请输入商家账号' },
					{ name: 'password1', checkType: 'required', errorMsg: '请输入密码' },
					{ name: 'password2', checkType: 'required', errorMsg: '请再次输入密码' }
				];
				if (this.captcha.id != '') rule.push({ name: 'captcha_code', checkType: 'required', errorMsg: '请输入验证码' });
			}
			var checkRes = validate.check(this.registeredList, rule);
			if (checkRes) {
				return true;
			} else {
				return false;
			}
		}
	}
};
</script>

<style lang="scss">
page {
	background-color: #fff;
}

.login {
	text-align: center;

	.login-title {
		font-size: 40rpx;
		text-align: center;
		display: inline-block;
		padding-top: 200rpx;
	}

	.login-input {
		display: flex;
		width: 70%;
		height: 70rpx;
		margin: 0 auto;
		align-items: center;
		justify-content: center;
		border: 1px solid #c8c7cc;
		border-radius: 10rpx;
		padding: 6rpx 10rpx;
		margin-top: 50rpx;
		.title {
			flex: 1;
		}
		.input {
			padding: 0;
			flex: 2;
			.uni-input {
				padding: 0;
				text-align: left;
			}
		}
		image {
			flex: 1;
			height: 70rpx;
		}
	}
	.login-btn {
		width: 70%;
		height: 70rpx;
		color: #fff;
		background: #ff8143;
		margin: 0 auto;
		border-radius: 10rpx;
		line-height: 70rpx;
		padding: 6rpx 10rpx;
		margin-top: 50rpx;
	}
	.login-text {
		width: 70%;
		height: 70rpx;
		padding: 6rpx 10rpx;
		text-align: center;
		margin: 0 auto;
		margin-top: 10rpx;
		.text-one {
			font-size: 28rpx;
			color: #7f7f7f;
		}
		.text-two {
			font-size: 28rpx;
			color: #ff8143;
			padding-left: 10rpx;
		}
	}
}
.input-placeholder {
	text-align: left;
}
</style>
