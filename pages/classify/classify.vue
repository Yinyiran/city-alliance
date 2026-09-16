<template>
	<view>
		<image src="/static/bg2.png" class="bg2s" mode=""></image>
		<view class="container">
			<view class="acea-row row-middle row-between">
				<!-- 左侧：第一行店铺名，第二行头像+姓名+员工 -->
				<view class="header-left" @click="handleHeaderClick">
					<!-- 第一行：店铺名字 + 切换箭头 -->
					<view class="acea-row row-middle" style="flex-wrap: nowrap;" v-if="siteName" @click.stop="openShopSelect">
						<image :src="$util.img(info.logo)" v-if="info.logo" class="shop-logo m-r-12" mode="heightFix">
						</image>
						<text class="font-36 font-weight-6 text-more" style="color: #fff; max-width: 340rpx;">
							{{ siteName || '门店签到' }}
						</text>
						<uv-icon name="arrow-down-fill" size="10" color="#FFFFFF" customStyle="margin-left: 8rpx; flex-shrink: 0;"></uv-icon>
					</view>
					<!-- 第二行：头像 + 姓名 + 员工/主理人 -->
					<view class="acea-row row-middle m-t-12" v-if="userName" style="flex-wrap: nowrap;">
						<text style="color: rgba(255, 255, 255, 0.95); max-width: 200rpx;"
							class="font-26 font-weight-6 text-more">
							{{ token ? userName : '' }}
						</text>
						<view class="user-role-badge m-l-10" v-if="roleName">{{ roleName }}</view>
						<text class="font-30 m-l-8 font-weight-6" style="color: #fff;" v-if="!token">点击登录</text>
					</view>
				</view>
				<!-- 右侧：扫码签到 -->
				<view class="acea-row row-middle">
					<view class="scan-header-btn acea-row row-middle" @click="scanSignIn">
						<image src="/static/img2.png" class="w-44 h-44 m-r-10" mode=""></image>
						<text>扫码签到</text>
					</view>
				</view>
			</view>

			<!-- 签到统计数据 -->
			<view class="checkes acea-row row-middle row-center row-between m-t-32">
				<view class="a-i">
					<view class="" style="color: #9E9285;">今日门店签到</view>
					<view class="font-48">{{ today_count || 0 }}</view>
				</view>
				<view class="lines"></view>
				<view class="a-i">
					<view class="" style="color: #9E9285;">累计门店签到</view>
					<view class="font-48">{{ total_count || 0 }}</view>
				</view>
			</view>

			<view class="mains">
				<uv-input placeholder="请输入关键词搜索" v-model="searchtxt" :customStyle="customStyle" border="none">
					<template v-slot:prefix>
						<uv-icon name="search"></uv-icon>
					</template>
					<template v-slot:suffix>
						<view class="search" @click="seachs">搜索</view>
					</template>
				</uv-input>
				<view class="title acea-row row-middle">签到明细</view>
				<view class="acea-row row-middle row-between p-24 a-b">
					<text>名称</text>
					<view class="acea-row row-middle row-between">
						<view class="acea-row row-middle w-116 m-r-20 row-center" @click="sets(1)">
							<text>累计签到</text>
							<image src="/static/icon1.png" v-if="sort == 0" class="w-16 h-16 m-l-4" mode=""></image>
							<template v-if="sort > 0">
								<image src="/static/up.png" class="w-16 h-16" v-if="sort == 1" mode=""></image>
								<image src="/static/down.png" v-else class="w-16 h-16" mode=""></image>
							</template>
						</view>
						<view class="acea-row row-middle w-260 row-right" @click="sets(2)">
							<text>签到时间</text>
							<image src="/static/icon1.png" v-if="sort1 == 0" class="w-16 h-16 m-l-4" mode=""></image>
							<template v-if="sort1 > 0">
								<image src="/static/up.png" class="w-16 h-16" v-if="sort1 == 1" mode=""></image>
								<image src="/static/down.png" v-else class="w-16 h-16" mode=""></image>
							</template>
						</view>
					</view>
				</view>
				<view v-for="(item, index) in list" class="h-100 acea-row row-middle row-between p-r-24 p-l-24 items"
					:key="index">
					<view class="acea-row row-middle" style="flex-wrap: nowrap;">
						<image :src="$util.img(item.headimg)" v-if="item.headimg" class="w-52 h-52 bor-26 m-r-12"
							mode=""></image>
						<image src="/static/avatar.png" v-else class="w-52 h-52 bor-26 m-r-12" mode=""></image>
						<view class="text-more font-28" style="max-width: 140rpx;color: #222222;">{{ item.nickname }}
						</view>
					</view>
					<view class="acea-row row-middle">
						<view class="sign-count-val">{{ item.signcount }}</view>
						<view class="sign-time-val">{{ formatSignTime(item.sign_time) }}</view>
					</view>
				</view>
				<uv-empty v-if="!list.length" text="暂无签到明细" mode="data" marginTop="30"></uv-empty>
			</view>
		</view>

		<!-- 签到成功弹出框 -->
		<uv-popup ref="signSuccessPopup" mode="center" :round="16">
			<view class="sign-modal-wrap acea-row row-column row-middle" v-if="signMember">
				<view class="sign-modal-icon acea-row row-middle row-center">
					<uv-icon name="checkbox-mark" color="#fff" size="28"></uv-icon>
				</view>
				<view class="sign-modal-title">签到成功</view>
				<view class="sign-member-card acea-row row-middle m-t-24">
					<image :src="$util.img(signMember.headimg)" v-if="signMember.headimg"
						class="w-80 h-80 bor-40 m-r-16" mode=""></image>
					<image src="/static/avatar.png" v-else class="w-80 h-80 bor-40 m-r-16" mode=""></image>
					<view>
						<view class="font-30 font-weight-6" style="color: #222;">{{ signMember.nickname || '会员顾客' }}
						</view>
						<view class="font-24" style="color: #999;" v-if="signMember.member_id">编号: {{
							signMember.member_id }}
						</view>
					</view>
				</view>
				<view class="sign-msg-txt m-t-20">{{ signMessage || '会员已成功完成签到，签到奖励已发放！' }}</view>
				<view class="sign-fee-tip m-t-16 acea-row row-middle row-center" v-if="signSettlement && parseFloat(signSettlement.shop_fee) > 0">
					<text class="font-24" style="color: #ff5722; font-weight: 500;">本次签到计费：¥ {{ signSettlement.shop_fee }}（周一统一划扣）</text>
				</view>
				<view class="sign-modal-btn acea-row row-middle row-center m-t-32" @click="closeSignSuccess">
					我知道了
				</view>
			</view>
		</uv-popup>

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
					<view class="manual-confirm-btn acea-row row-middle row-center" @click="confirmManualSign">确认签到
					</view>
				</view>
			</view>
		</uv-popup>

		<login ref="login" @loginsuccess="loginsuccess"></login>

		<!-- 切换企业/店铺弹窗 -->
		<shop-select-popup ref="shopPopup" @change="onShopChange"></shop-select-popup>
	</view>
</template>

<script>
import {
	signlist,
	signIndex,
	getInfo,
	dosignin
} from "@/common/js/api.js"
import ShopSelectPopup from "@/components/shop-select-popup/shop-select-popup.vue"

export default {
	components: {
		ShopSelectPopup
	},
	data() {
		return {
			customStyle: "background: #F5F5F5;height: 64rpx;borderRadius:32rpx;paddingLeft:24rpx;",
			list: [],
			select: 0,
			sort: 0,
			cnum: 0,
			cnum1: 0,
			sort1: 0,
			searchtxt: '',
			today_count: '',
			total_count: "",
			info: {},
			signMember: null,
			signMessage: '',
			signSettlement: null,
			manualCode: '',
			token: '',
			localUserInfo: {},
			currentSiteId: uni.getStorageSync('site_id') || ''
		};
	},
	computed: {
		userAvatar() {
			return (this.info && this.info.myinfo && this.info.myinfo.headimg)
				|| (this.info && this.info.headimg)
				|| (this.info && this.info.user_headimg)
				|| (this.info && this.info.logo)
				|| (this.localUserInfo && (this.localUserInfo.avatar || this.localUserInfo.headimg || this.localUserInfo.avatarUrl))
				|| '';
		},
		userName() {
			return (this.info && this.info.myinfo && this.info.myinfo.username)
				|| (this.info && this.info.myinfo && this.info.myinfo.nickname)
				|| (this.info && this.info.user_nickname)
				|| (this.localUserInfo && (this.localUserInfo.nickname || this.localUserInfo.nickName))
				|| (this.info && this.info.site_name)
				|| '商家管理';
		},
		siteName() {
			return (this.info && this.info.site_name) || '';
		},
		roleName() {
			if (!this.token) return '';
			if (this.info && this.info.myinfo && this.info.myinfo.is_manager !== undefined) {
				return this.info.myinfo.is_manager == 1 ? '主理人' : '员工';
			}
			return '';
		}
	},
	onLoad() {
		uni.$on('loginsuccess', this.loginsuccess)
		uni.$on('merchantShopChanged', this.onShopChanged)
		this.refreshAll()
	},
	onShow() {
		let cachedSiteId = uni.getStorageSync('site_id') || '';
		if (cachedSiteId && cachedSiteId !== this.currentSiteId) {
			this.currentSiteId = cachedSiteId;
			this.refreshAll();
		} else {
			this.refreshAll();
		}
	},
	beforeUnmount() {
		uni.$off('loginsuccess', this.loginsuccess)
		uni.$off('merchantShopChanged', this.onShopChanged)
	},
	onUnload() {
		uni.$off('loginsuccess', this.loginsuccess)
		uni.$off('merchantShopChanged', this.onShopChanged)
	},
	methods: {
		openShopSelect() {
			if (!this.token) {
				this.$refs.login.open();
				return;
			}
			this.$refs.shopPopup.open();
		},
		onShopChange(item) {
			this.currentSiteId = uni.getStorageSync('site_id') || '';
			this.refreshAll();
		},
		onShopChanged(data) {
			let siteId = (data && data.site_id) ? data.site_id : (uni.getStorageSync('site_id') || '');
			if (siteId && siteId !== this.currentSiteId) {
				this.currentSiteId = siteId;
				this.refreshAll();
			}
		},
		refreshAll() {
			this.token = uni.getStorageSync('token') || ''
			this.localUserInfo = uni.getStorageSync('userInfo') || {}
			this.currentSiteId = uni.getStorageSync('site_id') || ''
			if (this.token) {
				this.getInfo()
				this.signlist()
				this.signIndex()
			} else {
				this.info = {}
				this.list = []
				this.today_count = 0
				this.total_count = 0
			}
		},
		handleHeaderClick() {
			if (!this.token) {
				this.$refs.login.open()
			}
		},
		getInfo() {
			getInfo().then(res => {
				if (res && res.code == 0) {
					this.info = res.data || {}
					if (this.info.site_id && !uni.getStorageSync('site_id')) {
						uni.setStorageSync('site_id', this.info.site_id)
					}
				}
			}).catch(() => { })
		},
		signIndex() {
			if (!this.token) return
			signIndex().then(res => {
				if (res && res.code == 0 && res.data) {
					this.today_count = res.data.today_count || 0
					this.total_count = res.data.total_count || 0
				}
			}).catch(() => { })
		},
		sets(e) {
			if (e == 2) {
				if (this.cnum1 == 2) {
					this.cnum1 = 0
					this.sort1 = 0
				} else {
					this.cnum1++
					this.sort1++
				}

			} else {
				if (this.cnum == 2) {
					this.cnum = 0
					this.sort = 0
				} else {
					this.cnum++
					this.sort++
				}

			}
			this.signlist()
		},
		formatSignTime(val) {
			if (!val) return '--';
			if (typeof val === 'string' && (val.includes('-') || val.includes('/'))) {
				return val;
			}
			let num = Number(val);
			if (!isNaN(num) && num > 0) {
				if (String(num).length === 10) num *= 1000;
				const d = new Date(num);
				if (isNaN(d.getTime())) return String(val);
				const Y = d.getFullYear();
				const M = String(d.getMonth() + 1).padStart(2, '0');
				const D = String(d.getDate()).padStart(2, '0');
				const h = String(d.getHours()).padStart(2, '0');
				const m = String(d.getMinutes()).padStart(2, '0');
				const s = String(d.getSeconds()).padStart(2, '0');
				return `${Y}-${M}-${D} ${h}:${m}:${s}`;
			}
			return val;
		},
		seachs() {
			this.signlist()
		},
		signlist() {
			if (!this.token) return
			let params = {
				sortcount: this.sort,
				sorttime: this.sort1,
				searchtxt: this.searchtxt
			}
			signlist(params).then(res => {
				if (res && res.code == 0 && res.data) {
					this.list = res.data.list || []
				}
			}).catch(() => { })
		},
		scanSignIn() {
			if (!uni.getStorageSync('token')) {
				this.$refs.login.open()
				return
			}
			uni.scanCode({
				success: res => {
					let codeVal = res.result || res.path || ''
					if (codeVal) {
						this.handleSignScan(codeVal)
					} else {
						uni.showToast({
							title: '未识别到有效二维码内容',
							icon: 'none'
						})
					}
				},
				fail: err => {
					if (err && err.errMsg && err.errMsg.indexOf('cancel') === -1) {
						uni.showToast({
							title: '扫码失败，请重试',
							icon: 'none'
						})
					}
				}
			})
		},
		handleSignScan(code) {
			uni.showLoading({ title: '签到处理中...' })
			let shopId = this.info.site_id || uni.getStorageSync('site_id')
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
					// 刷新页面数据
					this.signIndex()
					this.signlist()
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
			this.$refs.signSuccessPopup.close()
			this.signSettlement = null
		},
		openManualSign() {
			if (!uni.getStorageSync('token')) {
				this.$refs.login.open()
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
			this.handleSignScan(code)
		},
		loginsuccess() {
			this.refreshAll()
		}
	}
}
</script>

<style lang="scss">
.bg2s {
	width: 100vw;
	height: 440rpx;
	position: fixed;
	top: 0;
	left: 0;
	z-index: -1;
}

.w-260 {
	width: 260rpx;
}

.items {
	background: #F5F5F5;
	margin-bottom: 20rpx;
	border-radius: 12rpx;

	>view:last-child {
		.sign-count-val {
			width: 116rpx;
			margin-right: 20rpx;
			font-weight: 600;
			font-size: 28rpx;
			color: #222222;
			text-align: center;
		}

		.sign-time-val {
			width: 260rpx;
			font-family: PingFangSC, PingFang SC;
			font-weight: 400;
			font-size: 22rpx;
			color: #999999;
			text-align: right;
			white-space: nowrap;
		}
	}
}

.container {
	margin: 32rpx;

	.header-left {
		display: flex;
		flex-direction: column;
		justify-content: center;

		.shop-logo {
			height: 38rpx;
			max-width: 80rpx;
		}

		.header-avatar {
			width: 52rpx;
			height: 52rpx;
			border-radius: 26rpx;
			border: 2rpx solid rgba(255, 255, 255, 0.85);
			background-color: #f5f5f5;
			flex-shrink: 0;
			box-shadow: 0 2rpx 8rpx rgba(0, 0, 0, 0.1);
		}

		.user-role-badge {
			padding: 2rpx 10rpx;
			background: rgba(255, 255, 255, 0.25);
			border: 1rpx solid rgba(255, 255, 255, 0.45);
			border-radius: 14rpx;
			color: #fff;
			font-size: 20rpx;
			line-height: 1.3;
			font-weight: 500;
			white-space: nowrap;
		}
	}

	.scan-header-btn {
		padding: 14rpx 24rpx;
		background: rgba(255, 255, 255, 0.28);
		border: 1rpx solid rgba(255, 255, 255, 0.5);
		border-radius: 40rpx;
		color: #fff;
		font-size: 32rpx;
		font-weight: 600;
		backdrop-filter: blur(10rpx);
		box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.08);
	}

	.manual-header-btn {
		padding: 14rpx 20rpx;
		background: rgba(255, 255, 255, 0.22);
		border: 1rpx solid rgba(255, 255, 255, 0.4);
		border-radius: 40rpx;
		color: #fff;
		font-size: 28rpx;
		font-weight: 500;
		backdrop-filter: blur(10rpx);
	}

	.mains {
		padding: 24rpx;
		background: #fff;
		border-radius: 24rpx;
		margin-top: 32rpx;
	}

	.a-b {
		text {
			color: #999999;
		}
	}

	.checkes {
		background: linear-gradient(135deg, #FFFEFA 0%, #FFE9D6 100%);
		border-radius: 24rpx;
		height: 130rpx;
		text-align: center;

		.a-i {
			width: calc((100% - 64rpx - 2rpx)/2);

			>view:last-child {
				font-size: 40rpx;
				color: #2E231A;
				font-weight: bold;
			}
		}

		.lines {
			width: 2rpx;
			height: 82rpx;
			background: #F5E0CE;
		}
	}

	.search {
		font-weight: 400;
		font-size: 30rpx;
		color: #222222;
		padding: 0 28rpx;
		border-left: #DDDDDD 1rpx solid;
		height: 42rpx;
	}

	.title {
		font-weight: 600;
		font-size: 32rpx;
		color: #1D1E24;
		margin-top: 24rpx;
	}

	.title::before {
		content: '';
		width: 4rpx;
		height: 32rpx;
		background: #F55606;
		border-radius: 2rpx;
		margin-right: 12rpx;
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
</style>