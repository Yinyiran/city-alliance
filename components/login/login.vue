<template>
	<view>
		<!-- 第一步：手机号快捷登录 -->
		<uv-popup ref="showpopref" :zIndex="10070" round="10" mode="bottom" closeable @close="onPopupClose">
			<view class="activity-box ">
				<view class="">
					<view class="tit">
						<image :src="$util.img(configobj.logo)" class="logo" mode=""></image>
						<view class="tit_text">{{configobj.name}}</view>
					</view>
					<view class="titlabel">手机号快捷登录</view>
					<view class="titlabel2">未注册的手机号验证通过后将自动创建账号</view>
					<view class="opt-box">
						<button v-if="!is_show" class="titlePhone public_btn" @click="showAgreementTip">
							手机号快捷登录
						</button>
						<button v-else class="titlePhone public_btn" open-type="getPhoneNumber"
							@getphonenumber="getPhoneNumber">
							手机号快捷登录
						</button>
						<view class="no-prompt-action" @click="handleNoPrompt">
							<text>不再提示</text>
						</view>
						<view class="xieyibox m-t-38 p-b-70">
							<radio value="" style="transform: scale(0.8);" :checked="is_show"
								@click="is_show=!is_show" />
							<view class="font-24 fcolor">登录即表示已阅读并同意</view>
							<text class="font-24" @click="xyopen(2)">《用户协议》</text>
							<text class="font-24" @click="xyopen(1)">《隐私政策》</text>
						</view>
					</view>
				</view>
			</view>
		</uv-popup>
		<!-- 第二步：完善头像昵称（手机号登录成功后，未设置昵称时弹出） -->
		<uv-popup ref="infopopref" :zIndex="10080" round="10" mode="bottom" closeable @close="infoclose">
			<view class="activity-box ">
				<view class="">
					<view class="tit">
						<view class="tit_text">完善资料</view>
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
						<input type="nickname" :maxlength="8" v-model="nickname"  @input="handleNicknameInput" placeholder="请输入昵称" />
					</view>
					<view class="opt-box">
						<button class="titlePhone public_btn" @click="submitInfo">
							完成
						</button>
					</view>
				</view>
			</view>
		</uv-popup>
		<uv-popup ref="xypopref" closeable :zIndex="11080" round="10" mode="center" @close="xyclose">
			<view class="xieyi-box ">
				<scroll-view scroll-y="true" class="xyhig">
					<view>
						<view v-html="$util.replaceImg(xycontent)"></view>
					</view>
				</scroll-view>
			</view>
		</uv-popup>

		<!-- 底部浮动提示条：当用户点击了“不再提示”且未登录时显示 -->
		<view v-if="showBottomBar" class="login-bottom-bar acea-row row-middle row-between">
			<view class="acea-row row-middle bar-left">
				<image src="/static/avatar.png" class="bar-avatar" mode="aspectFill"></image>
				<text class="bar-tip">您当前处于未登录状态</text>
			</view>
			<view class="bar-btn acea-row row-middle row-center" @click="open(true)">
				<text>请登录</text>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		authcodetoopenid,
		mobileauth,
		getAgreement,
		userinfo,
		modifyheadimg,
		modifynickname,
		Synopsis
	} from '@/common/js/api.js';
	export default {
		name: "authbox",
		data() {
			return {
				is_show: false,
				xycontent: '',
				nickname: '',
				avatar: '',
				configobj: {
					name: "",
					logo: ""
				},
				suid: '',
				weapp_openid: "",
				noPrompt: false,
				isLoggedIn: false
			};
		},
		computed: {
			showBottomBar() {
				return !this.isLoggedIn && this.noPrompt;
			}
		},
		created() {
			this.suid = uni.getStorageSync('invite_code') || ''
			this.checkLoginStatus()
			uni.$on('loginChange', this.onLoginChange)
		},
		beforeDestroy() {
			uni.$off('loginChange', this.onLoginChange)
		},
		unmounted() {
			uni.$off('loginChange', this.onLoginChange)
		},
		methods: {
			checkLoginStatus() {
				this.isLoggedIn = !!uni.getStorageSync('token')
				this.noPrompt = !!uni.getStorageSync('login_no_prompt')
			},
			onLoginChange(data) {
				if (data && data.isLogin) {
					this.isLoggedIn = true
					this.noPrompt = false
					uni.removeStorageSync('login_no_prompt')
				} else if (data && data.isLogin === false) {
					this.isLoggedIn = false
					this.noPrompt = !!uni.getStorageSync('login_no_prompt')
				} else {
					this.checkLoginStatus()
				}
			},
			handleNoPrompt() {
				this.noPrompt = true
				uni.setStorageSync('login_no_prompt', true)
				this.close()
			},
			onPopupClose() {
				// 弹窗关闭
			},
			handleNicknameInput() {
				// 允许中英文、数字、下划线和部分常用符号
				const allowedChars = /[^\u4e00-\u9fa5a-zA-Z0-9_·]/g
				this.nickname = this.nickname.replace(allowedChars, '')

				// 确保不超过最大长度
				if (this.nickname.length > 8) {
					this.nickname = this.nickname.substring(0, 8)
				}
			},
			// 获取微信 openid
			index() {
				return new Promise((resolve) => {
					uni.login({
						success: async res => {
							let params = {
								code: res.code
							}
							try {
								const resA = await authcodetoopenid(params)
								this.weapp_openid = resA.data.openid
							} catch (e) {
								console.log('openid获取失败', e)
							}
							resolve()
						},
						fail: () => resolve()
					})
				})
			},
			Synopsis() {
				return Synopsis().then(res => {
					this.configobj.name = res.data.mini_name
					this.configobj.logo = res.data.mini_image
				})
			},
			close() {
				this.$refs.showpopref.close()
			},
			// 完善资料弹窗关闭（提交成功或用户跳过），此时已登录成功
			infoclose() {
				this.avatar = ''
				this.nickname = ''
				this.$refs.infopopref.close()
				this.$emit('loginsuccess')
			},
			async open(force = false) {
				this.checkLoginStatus()
				if (this.isLoggedIn) {
					return
				}
				if (this.noPrompt && !force) {
					return
				}
				await this.index()
				await this.Synopsis()
				this.$refs.showpopref.open()
			},
			// 获取用户头像
			chooseavatar(e) {
				this.$util.upload_file_server(e.detail.avatarUrl, {}, '', '').then((res) => {
					this.avatar = res.path
				})
			},
			// 未勾选协议时的提示（不触发微信手机号授权）
			showAgreementTip() {
				uni.showToast({
					title: "请同意并阅读《用户协议》《隐私政策》",
					icon: 'none',
					duration: 2000
				})
			},
			// 微信手机号快捷登录
			async getPhoneNumber(e) {
				if (!this.is_show) {
					uni.showToast({
						title: "请同意并阅读《用户协议》《隐私政策》",
						icon: 'none',
						duration: 2000
					})
					return
				}
				const {
					encryptedData,
					iv,
					errMsg
				} = e.detail;
				if (errMsg != 'getPhoneNumber:ok') {
					uni.showToast({
						title: "需授权手机号才能登录",
						icon: 'none',
						duration: 2000
					})
					return
				}
				// 确保 openid 已获取
				if (!this.weapp_openid) {
					await this.index()
				}
				uni.showLoading({
					title: '登录中...'
				})
				uni.login({
					success: res => {
						const shareSiteId = uni.getStorageSync('share_site_id') || ''
						const inviteCode = this.suid || uni.getStorageSync('invite_code') || ''
						const params = {
							code: res.code,
							iv: iv,
							encryptedData: encryptedData,
							weapp_openid: this.weapp_openid,
							suid: inviteCode,
							site_id: shareSiteId
						}
						mobileauth(params).then(res => {
							uni.hideLoading()
							uni.setStorageSync('token', res.data.token)
							this.isLoggedIn = true
							this.noPrompt = false
							uni.removeStorageSync('login_no_prompt')
							// 登录成功，检查是否已设置昵称
							this.checkNeedInfo()
						}).catch(() => {
							uni.hideLoading()
						})
					},
					fail: () => {
						uni.hideLoading()
					}
				})
			},
			// 检查用户资料是否完善，未设置昵称则弹出完善资料弹窗
			async checkNeedInfo() {
				this.isLoggedIn = true
				this.noPrompt = false
				uni.removeStorageSync('login_no_prompt')
				try {
					const res = await userinfo()
					const info = res.data || {}
					if (info.site_id) {
						uni.setStorageSync('user_site_id', info.site_id)
						uni.setStorageSync('site_id', info.site_id)
					} else {
						uni.removeStorageSync('user_site_id')
					}
					// 登录后身份切换，清除之前的门店缓存，以便按新商家的门店加载
					uni.removeStorageSync('store_id')
					uni.setStorageSync('userInfo', info)
					uni.$emit('loginChange', { isLogin: true, site_id: info.site_id || 0 })
					if (!info.nickname) {
						// 关闭手机号弹窗，打开完善资料弹窗
						this.close()
						this.$refs.infopopref.open()
					} else {
						this.$emit('loginsuccess')
						this.close()
					}
				} catch (e) {
					// 查询失败视为登录成功，避免卡住用户
					uni.removeStorageSync('store_id')
					uni.$emit('loginChange', { isLogin: true, site_id: 0 })
					this.$emit('loginsuccess')
					this.close()
				}
			},
			// 提交头像昵称
			async submitInfo() {
				if (!this.avatar) {
					uni.showToast({
						title: "请选择用户头像",
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
				if (this.avatar) {
					await modifyheadimg({
						headimg: this.avatar
					})
				}
				await modifynickname({
					nickname: this.nickname
				})
				uni.showToast({
					title: "设置成功",
					icon: 'none'
				})
				this.isLoggedIn = true
				this.noPrompt = false
				uni.removeStorageSync('login_no_prompt')
				// 直接通知父级登录成功（不依赖 popup 的 close 事件链）
				this.avatar = ''
				this.nickname = ''
				this.$emit('loginsuccess')
				this.$refs.infopopref.close()
			},
			xyopen(type) {
				let params = {
					type: type
				}
				getAgreement(params).then(res => {
					this.xycontent = res.data.content
					this.$refs.xypopref.open()
				})
			},
			xyclose() {
				this.$refs.xypopref.close()
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

			.no-prompt-action {
				margin-top: 28rpx;
				text-align: center;
				font-size: 32rpx;
				font-weight: 500;
				color: #8D93A5;
				padding: 16rpx 0;
				cursor: pointer;

				&:active {
					opacity: 0.7;
				}
			}

			.xieyibox {
				display: flex;
				align-items: center;
				flex-wrap: wrap;
				justify-content: flex-start;
				margin-top: 30rpx;
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

	.xieyi-box {
		padding: 30rpx;
		width: 95vw;
		box-sizing: border-box;

		.xyhig {
			max-height: 70vh;
		}

		.xieyi-tit {
			font-size: 30rpx;
			font-weight: bold;
			text-align: center;
			padding: 0 0 30rpx;
		}
	}

	.public_btn {
		background: linear-gradient( 90deg, #F5AB18 0%, #F55606 100%) !important;
	}

	.login-bottom-bar {
		position: fixed;
		left: 30rpx;
		right: 30rpx;
		bottom: calc(var(--window-bottom, 0px) + 24rpx);
		z-index: 9990;
		height: 88rpx;
		background: rgba(28, 39, 76, 0.92);
		backdrop-filter: blur(10px);
		border-radius: 44rpx;
		padding: 0 16rpx 0 24rpx;
		box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.15);
		box-sizing: border-box;
		animation: slideUp 0.3s ease-out;

		.bar-left {
			flex: 1;
			overflow: hidden;
			margin-right: 20rpx;

			.bar-avatar {
				width: 52rpx;
				height: 52rpx;
				border-radius: 50%;
				margin-right: 16rpx;
				flex-shrink: 0;
			}

			.bar-tip {
				font-size: 26rpx;
				color: #FFFFFF;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
		}

		.bar-btn {
			padding: 0 32rpx;
			height: 60rpx;
			background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
			border-radius: 30rpx;
			color: #FFFFFF;
			font-size: 26rpx;
			font-weight: 500;
			flex-shrink: 0;

			&:active {
				opacity: 0.9;
			}
		}
	}

	@keyframes slideUp {
		from {
			transform: translateY(100%);
			opacity: 0;
		}
		to {
			transform: translateY(0);
			opacity: 1;
		}
	}
</style>
