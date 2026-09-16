<template>
	<view class="page">
		<view class="menu-list">
			<view class="menu-item" @click="openManualSign">
				<view class="menu-left">
					<uv-icon name="edit-pen" color="#333333" size="20"></uv-icon>
					<text class="menu-title">手动签到</text>
				</view>
				<view class="menu-right">
					<text class="menu-desc">输入编号或手机号</text>
					<uv-icon name="arrow-right" color="#999999" size="14"></uv-icon>
				</view>
			</view>
			<view class="menu-item" @click="navToBanner">
				<view class="menu-left">
					<uv-icon name="photo" color="#333333" size="20"></uv-icon>
					<text class="menu-title">Banner管理</text>
				</view>
				<view class="menu-right">
					<text class="menu-desc">管理门店轮播图</text>
					<uv-icon name="arrow-right" color="#999999" size="14"></uv-icon>
				</view>
			</view>
			<view class="menu-item" @click="navToShopQr">
				<view class="menu-left">
					<uv-icon name="grid" color="#333333" size="20"></uv-icon>
					<text class="menu-title">商家小程序码</text>
				</view>
				<view class="menu-right">
					<text class="menu-desc">扫码绑定商家</text>
					<uv-icon name="arrow-right" color="#999999" size="14"></uv-icon>
				</view>
			</view>
		</view>

		<view class="logout-box">
			<view class="logout-item" @click="logout">
				<text class="logout-text">退出登录</text>
			</view>
		</view>

		<!-- 手动输入会员号签到弹窗 -->
		<uv-popup ref="manualSignPopup" mode="center" :round="16">
			<view class="manual-modal-wrap acea-row row-column row-middle">
				<view class="manual-modal-title">会员签到录入</view>
				<view class="manual-modal-tip">可直接输入会员编号（如 4）或会员手机号</view>
				<view class="manual-input-box m-t-24">
					<input class="manual-input" type="text" v-model="manualCode" placeholder="请输入会员编号或手机号" />
				</view>
				<view class="manual-btn-row acea-row row-middle row-between m-t-32">
					<view class="manual-cancel-btn acea-row row-middle row-center" @click="closeManualSign">取消</view>
					<view class="manual-confirm-btn acea-row row-middle row-center" @click="confirmManualSign">确认签到</view>
				</view>
			</view>
		</uv-popup>

		<!-- 签到成功弹出框 -->
		<uv-popup ref="signSuccessPopup" mode="center" :round="16">
			<view class="sign-modal-wrap acea-row row-column row-middle" v-if="signMember">
				<view class="sign-modal-icon acea-row row-middle row-center">
					<uv-icon name="checkbox-mark" color="#fff" size="28"></uv-icon>
				</view>
				<view class="sign-modal-title">签到成功</view>
				<view class="sign-member-card acea-row row-middle m-t-24">
					<image :src="$util.img(signMember.headimg)" v-if="signMember.headimg"
						class="w-80 h-80 bor-40 m-r-16" mode="aspectFill"></image>
					<image src="/static/avatar.png" v-else class="w-80 h-80 bor-40 m-r-16" mode=""></image>
					<view>
						<view class="font-30 font-weight-6" style="color: #222;">{{ signMember.nickname || '会员顾客' }}</view>
						<view class="font-24" style="color: #999;" v-if="signMember.member_id">编号: {{ signMember.member_id }}</view>
					</view>
				</view>
				<view class="sign-msg-txt m-t-20">{{ signMessage || '会员已成功完成签到，签到奖励已发放！' }}</view>
				<view class="sign-fee-tip m-t-16 acea-row row-middle row-center" v-if="signSettlement && parseFloat(signSettlement.shop_fee) > 0">
					<text class="font-24" style="color: #ff5722; font-weight: 500;">本次签到计费：¥ {{ signSettlement.shop_fee }}（周一统一划扣）</text>
				</view>
				<view class="sign-modal-btn acea-row row-middle row-center m-t-32" @click="closeSignSuccess">我知道了</view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
	import { dosignin } from "@/common/js/api.js"

	export default {
		data() {
			return {
				manualCode: '',
				signMember: null,
				signMessage: '',
				signSettlement: null
			}
		},
		methods: {
			openManualSign() {
				if (!uni.getStorageSync('token')) {
					uni.showToast({
						title: '请先登录',
						icon: 'none'
					})
					return
				}
				this.manualCode = ''
				if (this.$refs.manualSignPopup) {
					this.$refs.manualSignPopup.open()
				}
			},
			closeManualSign() {
				if (this.$refs.manualSignPopup) {
					this.$refs.manualSignPopup.close()
				}
			},
			confirmManualSign() {
				let code = (this.manualCode || '').trim()
				if (!code) {
					uni.showToast({
						title: '请输入会员编号或手机号',
						icon: 'none'
					})
					return
				}
				this.closeManualSign()
				this.handleSign(code)
			},
			handleSign(code) {
				uni.showLoading({ title: '签到处理中...' })
				let shopId = uni.getStorageSync('site_id') || 0
				dosignin({
					code: code,
					shop_id: shopId
				}).then(res => {
					uni.hideLoading()
					if (res.code == 0) {
						this.signMember = (res.data && res.data.member) || {}
						this.signMessage = res.message || '签到成功'
						this.signSettlement = (res.data && res.data.settlement) || null
						if (this.$refs.signSuccessPopup) {
							this.$refs.signSuccessPopup.open()
						} else {
							uni.showToast({ title: res.message || '签到成功', icon: 'success' })
						}
					} else {
						let memberName = (res.data && res.data.member && (res.data.member.nickname || res.data.member.username)) ? `【${res.data.member.nickname || res.data.member.username}】` : ''
						uni.showModal({
							title: '签到提示',
							content: memberName ? `${memberName}${res.message || '签到失败'}` : (res.message || '签到失败，请稍后重试'),
							showCancel: false,
							confirmText: '我知道了'
						})
					}
				}).catch(err => {
					uni.hideLoading()
					uni.showToast({
						title: (err && err.message) ? err.message : '签到请求异常',
						icon: 'none'
					})
				})
			},
			closeSignSuccess() {
				if (this.$refs.signSuccessPopup) {
					this.$refs.signSuccessPopup.close()
				}
				this.signSettlement = null
			},
			navToBanner() {
				uni.navigateTo({
					url: '/otherpages/bannerManage/bannerManage'
				})
			},
			navToShopQr() {
				uni.navigateTo({
					url: '/otherpages/shopQr/shopQr'
				})
			},
			logout() {
				uni.showModal({
					title: '提示',
					content: '确定要退出登录吗？',
					confirmText: '退出',
					confirmColor: '#F55606',
					success: (res) => {
						if (res.confirm) {
							uni.removeStorageSync('token')
							uni.removeStorageSync('site_id')
							uni.removeStorageSync('userInfo')
							uni.showToast({
								title: '已退出登录',
								icon: 'none'
							})
							setTimeout(() => {
								uni.switchTab({
									url: '/pages/my/my'
								})
							}, 500)
						}
					}
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.page {
		min-height: 100vh;
		background: #F5F5F5;
		padding: 24rpx 32rpx;
		box-sizing: border-box;
	}

	.menu-list {
		background: #FFFFFF;
		border-radius: 24rpx;
		overflow: hidden;
		margin-bottom: 24rpx;

		.menu-item {
			height: 112rpx;
			padding: 0 32rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;
			background: #FFFFFF;
			transition: background-color 0.2s;

			&:active {
				background-color: #F9F9F9;
			}

			.menu-left {
				display: flex;
				align-items: center;

				.menu-title {
					margin-left: 16rpx;
					font-size: 30rpx;
					font-weight: 500;
					color: #222222;
				}
			}

			.menu-right {
				display: flex;
				align-items: center;

				.menu-desc {
					font-size: 26rpx;
					color: #999999;
					margin-right: 8rpx;
				}
			}
		}
	}

	.logout-box {
		background: #FFFFFF;
		border-radius: 24rpx;
		overflow: hidden;

		.logout-item {
			height: 110rpx;
			line-height: 110rpx;
			text-align: center;

			&:active {
				background-color: #F9F9F9;
			}

			.logout-text {
				font-size: 30rpx;
				font-weight: 600;
				color: #F55606;
			}
		}
	}

	.manual-modal-wrap {
		width: 580rpx;
		background: #fff;
		padding: 40rpx 36rpx;
		border-radius: 28rpx;
		box-sizing: border-box;

		.manual-modal-title {
			font-size: 34rpx;
			font-weight: bold;
			color: #222;
		}

		.manual-modal-tip {
			font-size: 24rpx;
			color: #999;
			margin-top: 12rpx;
			text-align: center;
		}

		.manual-input-box {
			width: 100%;
			background: #F5F6F8;
			border-radius: 16rpx;
			padding: 20rpx 24rpx;
			box-sizing: border-box;

			.manual-input {
				width: 100%;
				font-size: 28rpx;
				color: #222;
			}
		}

		.manual-btn-row {
			width: 100%;

			.manual-cancel-btn {
				width: 230rpx;
				height: 76rpx;
				background: #F5F5F5;
				color: #666;
				font-size: 28rpx;
				border-radius: 38rpx;
			}

			.manual-confirm-btn {
				width: 250rpx;
				height: 76rpx;
				background: linear-gradient(135deg, #F55606 0%, #FF7438 100%);
				color: #fff;
				font-size: 28rpx;
				font-weight: 600;
				border-radius: 38rpx;
			}
		}
	}

	.sign-modal-wrap {
		width: 580rpx;
		background: #fff;
		padding: 48rpx 36rpx 40rpx;
		border-radius: 28rpx;
		box-sizing: border-box;

		.sign-modal-icon {
			width: 100rpx;
			height: 100rpx;
			background: linear-gradient(135deg, #19be6b 0%, #46d68b 100%);
			border-radius: 50%;
			box-shadow: 0 8rpx 24rpx rgba(25, 190, 107, 0.3);
		}

		.sign-modal-title {
			font-size: 36rpx;
			font-weight: bold;
			color: #222;
			margin-top: 20rpx;
		}

		.sign-member-card {
			width: 100%;
			background: #F8F9FB;
			padding: 20rpx 24rpx;
			border-radius: 16rpx;
			box-sizing: border-box;
		}

		.sign-msg-txt {
			font-size: 26rpx;
			color: #666;
			text-align: center;
			line-height: 1.5;
		}

		.sign-modal-btn {
			width: 100%;
			height: 80rpx;
			background: linear-gradient(135deg, #F55606 0%, #FF7438 100%);
			color: #fff;
			font-size: 30rpx;
			font-weight: 600;
			border-radius: 40rpx;
		}
	}
</style>
