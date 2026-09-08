<template>
	<view>
		<uv-popup ref="showpopref" :zIndex="10070" round="10" mode="bottom" @close="close" @open="open">
			<view class="activity-box ">
				<!-- 第一步：手机号快捷登录 -->
				<template v-if="step == 1">
					<view class="titlabel">快捷登录</view>
					<view class="opt-box">
						<button class="titlePhone public_btn" v-if="is_show" open-type="getPhoneNumber"
							@getphonenumber="getPhoneNumber">
							手机号快捷登录
						</button>
						<button class="titlePhone" v-else style="color: #ffbaba;" @click="showAgreementTip">
							手机号快捷登录
						</button>
					</view>
				</template>
				<!-- 第二步：设置头像昵称 -->
				<template v-else>
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
						<button class="titlePhone public_btn" v-if="is_show" @click="showtrip">
							完成
						</button>
						<button class="titlePhone" v-else style="color: #333;" @click="showtrip">
							完成
						</button>
					</view>
				</template>
				<view class="xieyibox m-t-38 p-b-70">
					<radio value="" style="transform: scale(0.8);" :checked="is_show" @click="is_show = !is_show" />
					<view class="font-24 fcolor">请勾选表示您已阅读并同意</view>
					<text class="font-24" @click="xyopen(2)">《用户协议》</text>
					<text class="font-24" @click="xyopen(1)">《隐私政策》</text>
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
			step: 1,
			is_show: false,
			nickname: '',
			avatar: '',
			weapp_openid: '',
			configobj: {
				name: "",
				logo: ""
			},
			inviteCode: ''
		};
	},
	created() {
		this.inviteCode = uni.getStorageSync('inviteCode') || ''
	},
	onUnload() {
		uni.$off('agreeAgreement')
	},
	methods: {
		handleNicknameInput() {
			// 示例：允许中英文、数字、下划线和部分常用符号
			const allowedChars = /[^\u4e00-\u9fa5a-zA-Z0-9_·]/g
			this.nickname = this.nickname.replace(allowedChars, '')

			// 确保不超过最大长度
			if (this.nickname.length > 8) {
				this.nickname = this.nickname.substring(0, 8)
			}
		},
		// 获取微信登录 code（Promise 封装）
		getWxLoginCode() {
			return new Promise((resolve, reject) => {
				uni.login({
					success: res => {
						if (res.code) {
							resolve(res.code)
						} else {
							reject(new Error('获取微信code失败'))
						}
					},
					fail: err => reject(err)
				})
			})
		},
		// 从接口返回数据中提取昵称，判断是否已设置
		getNickname(res) {
			const d = res.data || {}
			let nick = d.nickname || d.nickName || d.nick_name
			if (!nick && d.userinfo) {
				nick = d.userinfo.nickname || d.userinfo.nickName || d.userinfo.nick_name
			}
			return nick || ''
		},
		// 未勾选协议时的提示
		showAgreementTip() {
			uni.showToast({
				title: "请勾选同意《用户协议》《隐私政策》后再登录",
				icon: 'none',
				duration: 2000
			})
		},
		// 手机号快捷登录
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
			if (!this.is_show) {
				uni.showToast({
					title: "请勾选同意《用户协议》《隐私政策》后再登录",
					icon: 'none',
					duration: 2000
				})
				return
			}
			uni.showLoading({
				title: '登录中...',
				mask: true
			})
			uni.login({
				success: async res => {
					try {
						const params1 = {
							code: res.code
						}
						const resC = await authcodetoopenid(params1)
						if (resC.code != 0) throw new Error(resC.msg || '获取openid失败')
						this.weapp_openid = resC.data.openid
						const params = {
							nickname: '',
							headimg: '',
							iv: iv,
							encryptedData: encryptedData,
							weapp_openid: this.weapp_openid
						}
						const resM = await mobileAuth(params)
						if (resM.code != 0) throw new Error(resM.msg || '登录失败')
						uni.setStorageSync('token', resM.data.token)
						uni.hideLoading()
						// 判断是否已设置昵称：mobileAuth 仅返回 token，需调 checkLogin 查询用户信息。
						// 注意：authcodetoopenid 已消费当前 code，必须重新 uni.login 获取新 code
						let hasNick = false
						try {
							const newCode = await this.getWxLoginCode()
							const resCk = await logincheckLogin({
								code: newCode
							}, true)
							if (resCk.code == 0) {
								hasNick = !!this.getNickname(resCk)
								if (resCk.data) {
									uni.setStorageSync('userInfo', {
										avatar: resCk.data.avatar || '',
										headimg: resCk.data.avatar || '',
										nickname: resCk.data.nickname || ''
									})
								}
							}
						} catch (e) {
							// checkLogin 异常时进入设置昵称，保证新用户流程可走通
						}
						if (hasNick) {
							// 已设置昵称：直接登录成功
							uni.$emit('loginsuccess')
							this.$emit('loginsuccess')
							this.close()
							uni.showToast({
								title: '登录成功',
								icon: 'none'
							})
						} else {
							// 未设置昵称：进入设置头像、昵称
							this.step = 2
						}
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
		// 设置头像昵称后完成登录
		showtrip() {
			if (!this.is_show) {
				uni.showToast({
					title: "请勾选同意《用户协议》《隐私政策》后再登录",
					icon: 'none',
					duration: 2000
				})
				return
			}
			if (!this.nickname) {
				uni.showToast({
					title: "请输入昵称",
					icon: 'none',
					duration: 2000
				})
				return
			}
			this.beforelogin()
		},
		beforelogin() {
			uni.showLoading({
				title: '登录中...',
				mask: true
			})
			uni.login({
				success: async res => {
					try {
						const params1 = {
							code: res.code
						}
						const resC = await authcodetoopenid(params1)
						if (resC.code != 0) throw new Error(resC.msg || '获取openid失败')
						this.weapp_openid = resC.data.openid
						const params = {
							weapp_openid: this.weapp_openid,
							nickName: this.nickname,
							avatarUrl: this.avatar
						}
						const resA = await loginAuth(params)
						if (resA.code != 0) throw new Error(resA.msg || '登录失败')
						uni.setStorageSync('token', resA.data.token)
						uni.setStorageSync('userInfo', {
							avatar: this.avatar,
							headimg: this.avatar,
							nickname: this.nickname
						})
						uni.hideLoading()
						this.close()
						uni.$emit('loginsuccess')
						this.$emit('loginsuccess')
						uni.showToast({
							title: '登录成功',
							icon: 'none'
						})
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
		xyopen(type) {
			uni.navigateTo({
				url: '/otherpages/agreement/agreement?type=' + type
			})
		},
		close() {
			this.avatar = ''
			this.nickname = ''
			this.step = 1
			this.$refs.showpopref.close()
			// this.$emit('loginsuccess')
		},
		async open() {
			uni.$off('agreeAgreement')
			uni.$once('agreeAgreement', () => {
				this.is_show = true
			})
			this.step = 1
			this.is_show = false
			this.$refs.showpopref.open()
		},
		// 获取用户头像
		chooseavatar(e) {
			this.$util.upload_file_server(e.detail.avatarUrl, {}, '', '').then((res) => {
				this.avatar = res.path
			})
		}
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

	.loginimg {
		width: 302rpx;
		height: 158rpx;
		display: block;
		margin: 63rpx auto 30rpx;
	}

	.titlabel {
		font-size: 36rpx;
		font-weight: bold;
		color: #1C274C;
		margin-bottom: 22rpx;
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
