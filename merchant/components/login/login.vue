<template>
	<view>
		<!-- 第一步：登录（手机号快捷登录 / 账号密码登录） -->
		<uv-popup ref="showpopref" :zIndex="10070" round="10" mode="bottom" closeable @close="onPopupClose">
			<view class="activity-box ">
				<view class="">
					<view class="tit">
						<image src="/static/logo.png" class="logo" mode="aspectFill"></image>
						<view class="tit_text">{{ configobj.name }}</view>
					</view>
					<view class="tab-switch acea-row row-center">
						<view class="tab-item" :class="{ on: loginTab == 'phone' }" @click="switchTab('phone')">手机号快捷登录
						</view>
						<view class="tab-item" :class="{ on: loginTab == 'account' }" @click="switchTab('account')">
							账号密码登录</view>
					</view>

					<!-- 手机号快捷登录 -->
					<block v-if="loginTab == 'phone'">
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
						</view>
					</block>

					<!-- 账号密码登录 -->
					<block v-else>
						<view class="titlabel">账号密码登录</view>
						<view class="iptbox graybg m-t-32">
							<view class="iptwid">账号</view>
							<input v-model="account" placeholder="请输入手机号或员工编号" placeholder-class="ipt-ph" />
						</view>
						<view class="iptbox graybg">
							<view class="iptwid">密码</view>
							<input v-model="password" password placeholder="请输入密码" placeholder-class="ipt-ph" />
						</view>
						<view class="opt-box">
							<button class="titlePhone public_btn" @click="submitAccountLogin">登录</button>
						</view>
					</block>

					<view class="xieyibox">
						<radio value="" style="transform: scale(0.8);" :checked="is_show" @click="is_show = !is_show" />
						<view class="font-24 fcolor">登录即表示已阅读并同意</view>
						<text class="font-24" @click="xyopen(2)">《用户协议》</text>
						<text class="font-24" @click="xyopen(1)">《隐私政策》</text>
					</view>
					<view class="agree-tip" v-if="!is_show">请先阅读并勾选同意《用户协议》《隐私政策》</view>
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
						<input type="nickname" :maxlength="8" v-model="nickname" @input="handleNicknameInput"
							placeholder="请输入昵称" />
					</view>
					<view class="opt-box">
						<button class="titlePhone public_btn" @click="submitInfo">
							完成
						</button>
					</view>
				</view>
			</view>
		</uv-popup>

		<!-- 协议查看弹窗 -->
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
	mobileAuth,
	authcodetoopenid,
	loginAuth,
	logincheckLogin,
	getAgreement,
	modifyheadimg,
	modifynickname,
	accountLogin
} from '../../common/js/api.js';

export default {
	name: "authbox",
	props: {
		autoOpen: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			is_show: false,
			xycontent: '',
			nickname: '',
			avatar: '',
			loginTab: 'phone',
			account: '',
			password: '',
			configobj: {
				name: "城盟联创",
				logo: "/static/logo.png"
			},
			weapp_openid: "",
			noPrompt: true,
			isLoggedIn: false
		};
	},
	computed: {
		showBottomBar() {
			return !this.isLoggedIn && this.noPrompt;
		}
	},
	created() {
		this.checkLoginStatus()
		uni.$on('loginChange', this.onLoginChange)
		uni.$on('loginPromptChange', this.onPromptChange)
	},
	mounted() {
		this.checkLoginStatus()
	},
	beforeDestroy() {
		uni.$off('loginChange', this.onLoginChange)
		uni.$off('loginPromptChange', this.onPromptChange)
	},
	unmounted() {
		uni.$off('loginChange', this.onLoginChange)
		uni.$off('loginPromptChange', this.onPromptChange)
	},
	methods: {
		switchTab(tab) {
			this.loginTab = tab;
		},
		// 手机号快捷登录失败：引导改用账号密码，避免用户/审核员卡在弹窗里
		onLoginFail(msg) {
			uni.showModal({
				title: '登录未成功',
				content: msg + '。您可改用「账号密码登录」，使用手机号或员工编号登录。',
				confirmText: '账号密码登录',
				showCancel: false,
				success: (res) => {
					if (res.confirm) {
						this.loginTab = 'account';
					}
				}
			});
		},
		// 统一登录成功收口：写 token、同步状态、通知页面刷新
		finishLogin(token) {
			uni.setStorageSync('token', token);
			this.isLoggedIn = true;
			this.noPrompt = false;
			uni.removeStorageSync('login_no_prompt');
			uni.$emit('loginChange', { isLogin: true });
			uni.$emit('loginPromptChange', { noPrompt: false });
		},
		// 账号密码登录
		async submitAccountLogin() {
			// 未同意《用户协议》《隐私政策》不允许登录
			// （微信审核要求：不得在未经用户明示同意的前提下收集/使用账号信息）
			if (!this.is_show) {
				this.showAgreementTip();
				return;
			}
			const account = (this.account || '').trim();
			if (!account) {
				uni.showToast({ title: '请输入账号', icon: 'none' });
				return;
			}
			if (!this.password) {
				uni.showToast({ title: '请输入密码', icon: 'none' });
				return;
			}
			// 请求层 http.js 已统一管理 showLoading/hideLoading（并发计数配对），
			// 此处不可再手动调用，否则会产生多余的 hideLoading 触发微信告警
			try {
				const res = await accountLogin({ account, password: this.password });
				if (!res || res.code != 0 || !res.data || !res.data.token) {
					uni.showToast({
						title: (res && (res.message || res.msg)) || '账号或密码错误',
						icon: 'none',
						duration: 2500
					});
					return;
				}
				this.finishLogin(res.data.token);
				this.password = '';
				this.close();
				uni.showToast({ title: '登录成功', icon: 'none' });
				this.$emit('loginsuccess');
			} catch (e) {
				uni.showToast({ title: '登录失败，请重试', icon: 'none' });
			}
		},
		checkLoginStatus() {
			this.isLoggedIn = !!uni.getStorageSync('token')
			this.noPrompt = !!uni.getStorageSync('login_no_prompt')
		},
		onPromptChange(data) {
			if (data && typeof data.noPrompt === 'boolean') {
				this.noPrompt = data.noPrompt
			} else {
				this.checkLoginStatus()
			}
		},
		onLoginChange(data) {
			if (data && data.isLogin) {
				this.isLoggedIn = true
				this.noPrompt = false
				uni.removeStorageSync('login_no_prompt')
				uni.$emit('loginPromptChange', { noPrompt: false })
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
			uni.$emit('loginPromptChange', { noPrompt: true })
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
							if (resA && resA.code == 0 && resA.data) {
								this.weapp_openid = resA.data.openid
							}
						} catch (e) {
							console.log('openid获取失败', e)
						}
						resolve()
					},
					fail: () => resolve()
				})
			})
		},
		close() {
			if (this.$refs.showpopref && typeof this.$refs.showpopref.close === 'function') {
				this.$refs.showpopref.close()
			}
		},
		// 完善资料弹窗关闭（提交成功或用户跳过），此时已登录成功
		infoclose() {
			this.avatar = ''
			this.nickname = ''
			if (this.$refs.infopopref && typeof this.$refs.infopopref.close === 'function') {
				this.$refs.infopopref.close()
			}
			this.$emit('loginsuccess')
			uni.$emit('loginsuccess')
		},
		async open(force = true) {
			this.checkLoginStatus()
			if (this.isLoggedIn) {
				return
			}
			if (this.noPrompt && !force) {
				return
			}
			await this.index()
			if (this.$refs.showpopref && typeof this.$refs.showpopref.open === 'function') {
				this.$refs.showpopref.open()
			}
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
		// 从接口返回数据中提取昵称
		getNickname(res) {
			const d = res.data || {}
			let nick = d.nickname || d.nickName || d.nick_name
			if (!nick && d.userinfo) {
				nick = d.userinfo.nickname || d.userinfo.nickName || d.userinfo.nick_name
			}
			return nick || ''
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
			// loading 由 http.js 统一管理（并发计数配对），此处手动调用会多出 hideLoading
			uni.login({
				success: async res => {
					try {
						if (!this.weapp_openid && res.code) {
							const resC = await authcodetoopenid({ code: res.code })
							if (resC && resC.code == 0 && resC.data) {
								this.weapp_openid = resC.data.openid
							}
						}
						const params = {
							code: res.code,
							iv: iv,
							encryptedData: encryptedData,
							weapp_openid: this.weapp_openid,
							nickname: '',
							headimg: ''
						}
						const resM = await mobileAuth(params)
						if (!resM || resM.code != 0) {
							this.onLoginFail((resM && (resM.message || resM.msg)) || '登录失败')
							return
						}
						uni.setStorageSync('token', resM.data.token)
						this.isLoggedIn = true
						this.noPrompt = false
						uni.removeStorageSync('login_no_prompt')
						// 登录成功，检查是否已设置昵称
						await this.checkNeedInfo()
					} catch (err) {
						uni.showToast({
							title: (err && (err.message || err.msg)) || '登录失败，请重试',
							icon: 'none'
						})
					}
				},
				fail: () => {
					uni.showToast({
						title: '微信授权失败，请重试',
						icon: 'none'
					})
				}
			})
		},
		// 检查用户资料是否完善，未设置昵称则弹出完善资料弹窗
		async checkNeedInfo() {
			this.isLoggedIn = true
			this.noPrompt = false
			uni.removeStorageSync('login_no_prompt')
			try {
				let hasNick = false
				const newCode = await this.getWxLoginCode()
				const resCk = await logincheckLogin({
					code: newCode
				}, true)
				if (resCk.code == 0 && resCk.data) {
					hasNick = !!this.getNickname(resCk)
					uni.setStorageSync('userInfo', {
						avatar: resCk.data.avatar || '',
						headimg: resCk.data.avatar || '',
						nickname: resCk.data.nickname || ''
					})
				}
				uni.$emit('loginChange', { isLogin: true })
				uni.$emit('loginsuccess')
				if (!hasNick) {
					// 关闭手机号弹窗，打开完善资料弹窗
					this.close()
					if (this.$refs.infopopref && typeof this.$refs.infopopref.open === 'function') {
						this.$refs.infopopref.open()
					}
				} else {
					this.$emit('loginsuccess')
					this.close()
					uni.showToast({
						title: '登录成功',
						icon: 'none'
					})
				}
			} catch (e) {
				uni.$emit('loginChange', { isLogin: true })
				uni.$emit('loginsuccess')
				this.$emit('loginsuccess')
				this.close()
				uni.showToast({
					title: '登录成功',
					icon: 'none'
				})
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
			// loading 由 http.js 统一管理，两个写接口各自配对，此处不再手动控制
			try {
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
				uni.setStorageSync('userInfo', {
					avatar: this.avatar,
					headimg: this.avatar,
					nickname: this.nickname
				})
				this.isLoggedIn = true
				this.noPrompt = false
				uni.removeStorageSync('login_no_prompt')
				this.avatar = ''
				this.nickname = ''
				this.$emit('loginsuccess')
				uni.$emit('loginsuccess')
				if (this.$refs.infopopref && typeof this.$refs.infopopref.close === 'function') {
					this.$refs.infopopref.close()
				}
			} catch (e) {
				try {
					if (!this.weapp_openid) {
						await this.index()
					}
					await loginAuth({
						weapp_openid: this.weapp_openid,
						nickName: this.nickname,
						avatarUrl: this.avatar
					})
					uni.setStorageSync('userInfo', {
						avatar: this.avatar,
						headimg: this.avatar,
						nickname: this.nickname
					})
				} catch (err) { }
				this.avatar = ''
				this.nickname = ''
				this.$emit('loginsuccess')
				uni.$emit('loginsuccess')
				if (this.$refs.infopopref && typeof this.$refs.infopopref.close === 'function') {
					this.$refs.infopopref.close()
				}
			}
		},
		xyopen(type) {
			let params = {
				type: type
			}
			getAgreement(params).then(res => {
				if (res.code == 0 && res.data && res.data.content) {
					this.xycontent = res.data.content
					if (this.$refs.xypopref && typeof this.$refs.xypopref.open === 'function') {
						this.$refs.xypopref.open()
					}
				} else {
					uni.navigateTo({
						url: '/otherpages/agreement/agreement?type=' + type
					})
				}
			}).catch(() => {
				uni.navigateTo({
					url: '/otherpages/agreement/agreement?type=' + type
				})
			})
		},
		xyclose() {
			if (this.$refs.xypopref && typeof this.$refs.xypopref.close === 'function') {
				this.$refs.xypopref.close()
			}
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
	padding: 40rpx 50rpx calc(40rpx + env(safe-area-inset-bottom));

	.tit {
		display: flex;
		align-items: center;
		margin-bottom: 34rpx;

		&_text {
			color: #1C274C;
			font-size: 30rpx;
			font-weight: bold;
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
		font-weight: bold;
		color: #1C274C;
		margin-bottom: 22rpx;
	}

	.tab-switch {
		margin-bottom: 32rpx;
		background: #F5F5F5;
		border-radius: 40rpx;
		padding: 6rpx;

		.tab-item {
			flex: 1;
			text-align: center;
			line-height: 68rpx;
			border-radius: 34rpx;
			font-size: 28rpx;
			color: #8D93A5;
			transition: all 0.2s;

			&.on {
				background: #FFFFFF;
				color: #F55606;
				font-weight: 600;
				box-shadow: 0 2rpx 8rpx rgba(0, 0, 0, 0.06);
			}
		}
	}

	.ipt-ph {
		color: #B2B6C2;
		font-size: 28rpx;
	}

	.agree-tip {
		margin-top: 16rpx;
		font-size: 22rpx;
		color: #F55606;
		text-align: center;
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
	}

	.xieyibox {
		display: flex;
		align-items: center;
		flex-wrap: wrap;
		justify-content: flex-start;
		margin-top: 32rpx;
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
			color: #F55606;
		}
	}

	.titlePhone {
		color: #fff;
		height: 88rpx;
		line-height: 88rpx;
		font-size: 28rpx;
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
	background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%) !important;
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
