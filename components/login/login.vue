<template>
	<view>
		<uv-popup ref="showpopref" :zIndex="10070" round="10" mode="bottom" @close="close" @open="open">
			<view class="activity-box ">
				<view class="">
					<view class="tit">
						<image :src="$util.img(configobj.logo)" class="logo" mode=""></image>
						<view class="tit_text">{{configobj.name}}</view>
					</view>
					<view class="titlabel">请设置头像、昵称</view>
					<view class="titlabel2">方便平台及时为您服务</view>
					<view class="acea-row row-middle row-column row-center m-b-30">
						<button open-type="chooseAvatar" class="headimg" @chooseavatar="chooseavatar">
							<image v-if="avatar" :src="$util.img(avatar)"></image>
							<image v-else src="@/static/avatar.png" mode=""></image>
						</button>
						<view class="iptwid m-t-20">上传头像 <text class="fcolor1">*</text></view>
					</view>
					<view class="iptbox graybg">
						<view class="iptwid">昵称<text class="fcolor1 font-28">*</text></view>
						<input type="nickname" v-model="nickname" @input="handleNicknameInput" placeholder="请输入昵称">
					</view>

					<view class="opt-box">

						<template v-if="is_show">
							<button class="titlePhone public_btn" v-if="canlogin" @click="showtrip">
								登录
							</button>
							<button class="titlePhone public_btn" v-else open-type="getPhoneNumber"
								@getphonenumber="getPhoneNumber">
								登录
							</button>
						</template>
						<template v-else>
							<button class="titlePhone" disabled style="color: #333;">
								登录
							</button>
						</template>
						<view class="xieyibox m-t-38 p-b-70">
							<radio value="" style="transform: scale(0.8);" :checked="is_show"
								@click="is_show=!is_show" />
							<view class="font-24 fcolor">请勾选表示您已阅读并同意</view>
							<text class="font-24" @click="xyopen(2)">《用户协议》</text>
							<text class="font-24" @click="xyopen(1)">《隐私政策》</text>
						</view>
					</view>
				</view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
	import {
		mobileAuth,
		authcodetoopenid,
		loginAuth,
		logincheckLogin
	} from '../../common/js/api';
	export default {
		name: "authbox",
		data() {
			return {
				is_show: false,
				code: '',
				nickname: '',
				avatar: '',
				configobj: {
					name: "",
					logo: ""
				},
				istrip: 1,
				beforelog: false,
				inviteCode: '',
				info: {},
				weapp_openid: ""
			};
		},
		created() {
			// this.index()
			this.inviteCode = uni.getStorageSync('inviteCode') || ''
		},
		onUnload() {
			uni.$off('agreeAgreement')
		},
		computed: {
			canlogin() {
				// if (!this.is_show) {
				// 	return true
				// }
				
				if (this.beforelog) {
					return true
				} else {
					return false
				}


			}
		},
		methods: {
			handleNicknameInput() {
			      // 根据需求调整允许的字符范围
			      // 示例：允许中英文、数字、下划线和部分常用符号
			      const allowedChars = /[^\u4e00-\u9fa5a-zA-Z0-9_·]/g
			      this.nickname = this.nickname.replace(allowedChars, '')
			      
			      // 确保不超过最大长度
			      if (this.nickname.length > 8) {
			        this.nickname = this.nickname.substring(0, 8)
			      }
			    },
			beforelogin() {
				uni.showLoading({
					title: '登录中...',
					mask: true
				})
				uni.login({
					success: async res => {
						try {
							let params1 = {
								code: res.code
							}
							const resC = await authcodetoopenid(params1)
							this.weapp_openid = resC.data.openid
							let params = {
								weapp_openid: this.weapp_openid,
								nickName: this.nickname,
								avatarUrl: this.avatar
							}
							const resA = await loginAuth(params)
							uni.setStorageSync('token', resA.data.token)
							uni.hideLoading()
							this.close()
							this.$emit('loginsuccess')
						} catch (e) {
							uni.hideLoading()
							uni.showToast({
								title: '登录失败，请稍后重试',
								icon: 'none',
								duration: 2000
							})
						}
					},
					fail: () => {
						uni.hideLoading()
						uni.showToast({
							title: '微信授权失败，请重试',
							icon: 'none',
							duration: 2000
						})
					}
				})
			},
			Synopsis() {
				Synopsis().then(res => {
					this.configobj.name = res.data.mini_name
					this.configobj.logo = res.data.mini_image
				})
			},
			index() {
				return new Promise(resolve => {
					uni.login({
						success: async res => {
							try {
								let params = {
									code: res.code
								}
								const resA = await logincheckLogin(params)
								this.beforelog = resA.data.mobile ? true : false
							} catch (e) {
								this.beforelog = false
							}
							resolve()
						},
						fail: () => {
							this.beforelog = false
							resolve()
						}
					})
				})
			},
			showtrip() {
				// if (!this.avatar) {
				// 	uni.showToast({
				// 		title: "请选择用户头像",
				// 		icon: 'none',
				// 		duration: 2000
				// 	})
				// 	return
				// }
				// if (!this.nickname) {
				// 	uni.showToast({
				// 		title: "请输入昵称",
				// 		icon: 'none',
				// 		duration: 2000
				// 	})
				// 	return
				// }
				if (!this.is_show) {
					uni.showToast({
						title: "请同意并阅读《用户协议》《隐私政策》",
						icon: 'none',
						duration: 2000
					})
					return
				}
				this.beforelogin()
			},
			okeylogin() {
				if (this.is_show) {
					this.istrip = 2
				} else {
					uni.showToast({
						title: "请同意并阅读《用户协议》《隐私政策》",
						icon: 'none',
						duration: 2000
					})
				}
			},
			xyopen(type) {
				uni.navigateTo({
					url: '/otherpages/agreement/agreement?type=' + type
				})
			},
			close() {
				this.avatar = ''
				this.nickname = ''
				this.$refs.showpopref.close()
				// this.$emit('loginsuccess')
			},
			async open() {
				uni.$off('agreeAgreement')
				uni.$once('agreeAgreement', () => {
					this.is_show = true
				})
				try {
					await this.index()
				} catch (e) {
					// 登录态检测接口异常时也允许打开授权弹窗
					this.beforelog = false
				}
				this.is_show=false
				// await this.Synopsis()
				this.$refs.showpopref.open()
			},
			// 获取用户头像
			chooseavatar(e) {
				this.$util.upload_file_server(e.detail.avatarUrl, {}, '', '').then((res) => {
					this.avatar = res.path
				})
			},
			async getPhoneNumber(e) {
				const {
					encryptedData,
					iv,
					errMsg
				} = e.detail;

				// 用户未授权（取消/拒绝）时给出友好提示
				if (errMsg != 'getPhoneNumber:ok') {
					if (errMsg.indexOf('cancel') == -1 && errMsg.indexOf('deny') == -1) {
						uni.showToast({
							title: '手机号授权失败，请重试',
							icon: 'none',
							duration: 2000
						})
					}
					return
				}

				uni.showLoading({
					title: '登录中...',
					mask: true
				})
				uni.login({
					success: async res => {
						try {
							let params1 = {
								code: res.code
							}
							const resC = await authcodetoopenid(params1)
							this.weapp_openid = resC.data.openid
							const params = {
								// code: res.code,
								nickname: this.nickname,
								headimg: this.avatar,
								iv: iv,
								encryptedData: encryptedData,
								weapp_openid: this.weapp_openid
								// invite: this.inviteCode
							}
							const res = await mobileAuth(params)
							uni.setStorageSync('token', res.data.token)
							uni.hideLoading()
							this.$emit('loginsuccess')
							this.close()
						} catch (e) {
							uni.hideLoading()
							uni.showToast({
								title: '登录失败，请稍后重试',
								icon: 'none',
								duration: 2000
							})
						}
					},
					fail: () => {
						uni.hideLoading()
						uni.showToast({
							title: '微信授权失败，请重试',
							icon: 'none',
							duration: 2000
						})
					}
				})
			},

		}

	}
</script>

<style lang="scss" scoped>
	.fcolor {
		color: #8D93A5;
	}

	.fcolor1 {
		color: #A95346;
	}


	.activity-box {
		padding: 40rpx 50rpx;

		.tit {
			display: flex;
			align-items: center;
			margin-bottom: 34rpx;

			&_text {
				color: #1C274C;
				font-size: 30rpx;
			}

			.logo {
				width: 80rpx;
				height: 80rpx;
				display: block;
				border-radius: 50%;
				margin-right: 20rpx;
			}
		}

		.loginimg {
			width: 302rpx;
			height: 158rpx;
			display: block;
			margin: 63rpx auto 30rpx;
		}

		.titlabel {
			font-size: 32rpx;
			color: #1C274C;
			margin-bottom: 22rpx;
		}

		.titlabel2 {
			color: #8D93A5;
			font-size: 28rpx;
			margin-bottom: 56rpx;
		}

		.graybg {
			background-color: #F7F7F7;
		}

		.iptbox {
			padding: 30rpx;
			border-radius: 16rpx;
			display: flex;
			align-items: center;

			margin-bottom: 20rpx;

			.iptwid {
				margin-right: 36rpx;
				font-size: 28rpx;
			}

		}

		.headimg {
			padding: 0;
			margin: 0;
			width: 118rpx;
			height: 118rpx;
			display: block;
			border: none;
			border-radius: 50%;
			background: transparent;

			image {
				width: 100%;
				height: 100%;
				border-radius: 50%;
			}
		}

		.opt-box {
			margin-top: 52rpx;

			.xieyibox {
				display: flex;
				align-items: center;
				flex-wrap: wrap;
				justify-content: flex-start;
				margin-bottom: 16rpx;

				.circle {
					width: 40rpx;
					height: 40rpx;
					border-radius: 50%;
					border: 1px solid #e0e0e0;
					box-sizing: border-box;
				}

				view {
					font-size: 24rpx;
				}

				text {
					font-size: 24rpx;
				}
			}
		}


		.titlePhone {
			color: #fff;
			height: 88rpx;
			line-height: 88rpx;
			font-size: 28rpx;
			// background-color: $uni-theme-color;
			// border-color: $uni-theme-color;
			border-radius: 98rpx;
		}

	}

	.public_btn {
		background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%) !important;
	}
</style>