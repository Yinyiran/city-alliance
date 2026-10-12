<template>
	<view>
		<nvabar title='签到码'></nvabar>
		<image src="/otherpages/static/sbg2.png" class="bgs" mode=""></image>
		<view class="mains">
			<image src="/otherpages/static/sbg3.png" mode=""></image>
			<view class="acea-row row-middle re m-80 p-t-80">
				<image :src="$util.img(info.headimg)" v-if="info.headimg" class="w-100 h-100 bor-50" mode=""></image>
				<image src="/static/avatar.png" v-else class="w-100 h-100 bor-50" mode=""></image>
				<view class="m-l-20">
					<view class="font-32 font-weight" style="color: #222;">
						<text class="font-36" style="color: #555;">会员级别：</text>
						{{info.member_level_name}}
					</view>
					<view class="font-26" style="color: #525666;text-align: left;">会员编号: {{info.member_id}}</view>
				</view>
			</view>
			<view class="qrcode-container re">
				<!-- 普通二维码（与订单提货核销码一致），100% 包含真实 member_id，所有扫码设备即扫即得 -->
				<view class="codes acea-row row-middle row-center" v-if="info && info.member_id" style="margin: 0 auto; background: #fff; border-radius: 16rpx; padding: 16rpx; box-sizing: border-box; display: inline-flex;">
					<uv-qrcode ref="qrcode" :value="'member_id=' + info.member_id" size="288rpx" :loading="qrLoading"></uv-qrcode>
				</view>
				<!-- 加载中占位 -->
				<view class="codes acea-row row-middle row-center" v-else style="margin: 0 auto; background: #f8f8f8; border-radius: 16rpx; display: inline-flex; color: #999; font-size: 26rpx;">
					二维码加载中...
				</view>
				<view class="acea-row row-middle row-center m-t-16" v-if="info && info.member_id">
					<text class="font-24" style="color: #666;">签到编号: {{info.member_id}}</text>
					<view class="refresh-btn acea-row row-middle m-l-16" @click="refreshQrcode">
						<uv-icon name="reload" size="13" color="#FF6600"></uv-icon>
						<text class="m-l-4 font-22" style="color: #FF6600;">刷新</text>
					</view>
				</view>
			</view>
			<view class="btms">
				<image src="/otherpages/static/sbg4.png" mode=""></image>
				<view class="" v-if="userReward > 0">出示此码由门店扫描，每次签到立享 {{ userReward }} 元现金奖励（每周一统一结算至钱包余额）</view>
				<view class="" v-else>注意：此二维码需前往指定商家线下扫码即可激活签到奖励</view>
			</view>
		</view>
	</view>
</template>

<script>
	import { userinfo, getsignsettlementconfig } from '@/common/js/api.js';
	export default {
		data() {
			return {
				info: "",
				qrLoading: false,
				userReward: 0
			};
		},
		onLoad() {
			this.userinfo();
			this.getSettlementConfig();
		},
		methods: {
			getSettlementConfig() {
				getsignsettlementconfig().then(res => {
					if (res && res.code == 0 && res.data) {
						let reward = parseFloat(res.data.user_reward);
						if (res.data.is_open && !isNaN(reward) && reward > 0) {
							this.userReward = reward.toFixed(2);
						} else {
							this.userReward = 0;
						}
					}
				}).catch(() => {
					this.userReward = 0;
				});
			},
			userinfo() {
				this.qrLoading = true;
				return userinfo().then(res => {
					if (res && res.code == 0) {
						this.info = res.data;
					}
				}).catch(() => {
					uni.showToast({ title: '获取用户信息失败', icon: 'none' });
				}).finally(() => {
					this.qrLoading = false;
				});
			},
			refreshQrcode() {
				this.userinfo().then(() => {
					uni.showToast({ title: '已刷新', icon: 'success' });
				});
			}
		}
	}
</script>

<style lang="scss">
	.re{
		position: relative;
		z-index: 99;
	}
	.mains{
		margin: 120rpx auto;
		position: relative;
		width: 650rpx;
		height: 844rpx;
		text-align: center;
		.codes{
			width: 320rpx;
			height: 320rpx;
		}
		>image:first-child{
			position: absolute;
			left: 0;
			top: 0;
			width: 650rpx;
			height: 100%;
		}
		>.btms{
			width: 610rpx;
			height: 233rpx;
			position: absolute;
			bottom: 0;
			left: 50%;
			transform: translateX(-50%);
			z-index: 2;
			>image:first-child{
				position: absolute;
				width: 610rpx;
				height: 100%;
				left: 0;
				bottom: 0;
				z-index: 1;
			}
			>view{
				font-size: 28rpx;
				color: #FFFFFF;
				padding: 109rpx 48rpx 0 48rpx;
				position: relative;
				z-index: 99;
			}
		}
	}
	.bgs{
		width: 100vw;
		height: 100vh;
		position: fixed;
		left: 0;
		top: 0;
		z-index: -1;
	}
</style>
