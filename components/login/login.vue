<template>
	<view>
		<!-- 第一步：手机号快捷登录 -->
		<uv-popup ref="showpopref" :zIndex="10070" round="10" mode="bottom">
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
				weapp_openid: ""
			};
		},
		created() {
			this.suid = uni.getStorageSync('invite_code') || ''
		},
		methods: {
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
			async open() {
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
						const params = {
							code: res.code,
							iv: iv,
							encryptedData: encryptedData,
							weapp_openid: this.weapp_openid,
							suid: this.suid,
							site_id: shareSiteId
						}
						mobileauth(params).then(res => {
							uni.hideLoading()
							uni.setStorageSync('token', res.data.token)
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
				try {
					const res = await userinfo()
					const info = res.data || {}
					if (info.site_id) {
						uni.setStorageSync('user_site_id', info.site_id)
					}
					uni.setStorageSync('userInfo', info)
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
</style>
