<template>
	<view class="login">
		<view class="login-title">商家登录</view>
		<view class="login-input">
			<view class="icon"><image :src="$util.img('/upload/uniapp/shop_uniapp/login/account.png')" mode="aspectFit"/></view>
			<view class="input"><input class="uni-input" placeholder="请输入用户名" placeholder-class="input-placeholder" v-model="loginList.username" /></view>
		</view>
		<view class="login-input">
			<view class="icon"><image :src="$util.img('/upload/uniapp/shop_uniapp/login/password.png')" mode="aspectFit"/></view>
			<view class="input"><input class="uni-input" placeholder="请输入密码" v-model="loginList.password" password="true" /></view>
		</view>
		<view class="login-input">
			<view class="icon"><image :src="$util.img('/upload/uniapp/shop_uniapp/login/code.png')" mode="aspectFit"/></view>
			<view class="input"><input class="uni-input" placeholder="输入验证码" placeholder-class="input-placeholder" v-model="loginList.vcode" /></view>
			<image class="code" :src="captcha.img" mode="aspectFit" @click="getImg"></image>
		</view>
		<view class="login-btn color-base-bg" @click="Login">登录</view>
		<view class="login-text">
			还没成为我们的伙伴?
			<text class="color-base-text " @click="toRegistered">申请入驻</text>
		</view>
	</view>
</template>

<script>
import validate from '@/common/js/validate.js';
export default {
	data() {
		return {
			loginMode: 'account',
			loginList: {
				username: '',
				password: '',
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
		toRegistered() {
			this.$util.redirectTo('/pages/apply/register');
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
		//点击登录
		Login() {
			if (this.vertify()) {
				this.$api.sendRequest({
					url: '/shopapi/login/login',
					data: {
						captcha_id: this.captcha.id,
						username: this.loginList.username,
						password: this.loginList.password,
						captcha_code: this.loginList.vcode
					},
					success: res => {
						uni.showToast({
							title: res.message,
							icon: 'none'
						});
						if(res.code >= 0 ){
							uni.setStorageSync('token',res.data.token);
							uni.setStorageSync('site_id',res.data.site_id);
							if (res.data.site_id > 0) {
								this.$util.redirectTo('/pages/index/index');
							} else {
								this.getShopStatus();
							}
						}else{
							this.getImg();
						}
					}
				});
			}
		},
		//获取店铺状态
		getShopStatus(){
			var that = this
			this.$api.sendRequest({
				url: '/shopapi/apply/index',
				success: res => {
					var data = {
						shop_apply_info: JSON.stringify(res.data.shop_apply_info),
						procedure: res.data.procedure
					}
					switch (res.data.procedure) {
						case 1:
							that.$util.redirectTo('/pages/apply/mode');
							break;
						case 2:
							data.id_experience = res.data.id_experience;
							data.is_city = res.data.is_city;
							that.$util.redirectTo('/pages/apply/audit', data);
							break;
						case 6:
							data.id_experience = res.data.id_experience;
							data.is_city = res.data.is_city;
							data.receivable_config = JSON.stringify(res.data.receivable_config.value);
							that.$util.redirectTo('/pages/apply/audit', data);
							break;
						case 3:
							that.$util.redirectTo('/pages/apply/audit', data);
							break;
						case 5:
							that.$util.redirectTo('/pages/successfully/successfully');
					}
				}
			});
		},
		//表单验证
		vertify() {
			let rule = [];
			// 账号验证
			if (this.loginMode == 'account') {
				rule = [
						{ name: 'username', checkType: 'required', errorMsg: '请输入用户名' }, 
						{ name: 'password', checkType: 'required', errorMsg: '请输入密码' },
					];
				if (this.captcha.id != '') 
					rule.push({ name: 'vcode', checkType: 'required', errorMsg: '请输入验证码' });
			}
			var checkRes = validate.check(this.loginList, rule);
			if (checkRes) {
				return true;
			} else {
				this.$util.showToast({ title: validate.error });
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
	margin: 0 60rpx 0;
	padding-top:180rpx;
	.login-title {
		font-size: 60rpx;
		font-weight: 500;
		display: inline-block;
		margin-bottom: 50rpx;
	}

	.login-input {
		display: flex;
		height: 70rpx;
		margin: 50rpx auto 0;
		align-items: center;
		justify-content: center;
		border-bottom: 1px solid $color-line;
		border-radius: 10rpx;
		padding: 6rpx 10rpx;
		.icon{
			width:46rpx;
			min-width: 46rpx;
			height:46rpx;
			image{
				max-width: 100%;
				max-height: 100%;
			}
		}
		.input {
			padding: 0;
			flex: 1;
			margin-left: $margin-updown;
			.uni-input {
				padding: 0;
				text-align: left;
			}
		}
		image.code {
			width: 150rpx;
			height: 70rpx;
		}
	}
	.login-btn {
		height: 90rpx;
		line-height: 90rpx;
		border-radius: 90rpx;
		color: #fff;
		background: #ff8143;
		margin: 180rpx auto 0;
		padding: 6rpx 10rpx;
		text-align: center;
	}
	.login-text {
		text-align: center;
		margin: 50rpx auto 0;
		color:$color-tip;
	}
}
</style>
