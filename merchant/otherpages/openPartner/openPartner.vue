<template>
	<view class="partner-page">
		<view class="hero-banner">
			<view class="hero-tag">店长/老板专属权限</view>
			<view class="hero-title">主动添加合伙人</view>
			<view class="hero-desc">店铺老板可自定义金额为核心客户或员工开通「合伙人」身份，该笔费用仅由主理人与分公司分佣！</view>
		</view>

		<view class="form-card m-t-32">
			<view class="form-item">
				<view class="form-label">目标会员手机号</view>
				<view class="input-wrap acea-row row-middle">
					<input class="input-field" type="number" maxlength="11" v-model="mobile" placeholder="请输入被开通会员手机号" />
				</view>
			</view>

			<view class="form-item m-t-32">
				<view class="form-label">设定开通金额 (元)</view>
				<view class="input-wrap acea-row row-middle">
					<text class="symbol">￥</text>
					<input class="input-field" type="digit" v-model="customPrice" placeholder="请输入自定义开通价格" />
				</view>
			</view>

			<!-- 分润试算卡片 -->
			<view class="split-preview m-t-32" v-if="Number(customPrice) > 0">
				<view class="split-title acea-row row-between row-middle">
					<text class="font-weight-6">分佣收益试算</text>
					<text class="split-tip">该金额仅走主理人与分公司</text>
				</view>
				<view class="split-row acea-row row-between m-t-16">
					<text class="split-label">主理人分润 (50%)</text>
					<text class="split-val">+￥{{ shopIncome }}</text>
				</view>
				<view class="split-row acea-row row-between m-t-12">
					<text class="split-label">分公司分润 (50%)</text>
					<text class="split-val">+￥{{ cityIncome }}</text>
				</view>
				<view class="split-note m-t-16">
					* 注：该款项不会计入总部留存，全部归属于门店与分公司公账结算。
				</view>
			</view>
		</view>

		<!-- 合伙人权益介绍 -->
		<view class="privilege-card m-t-32">
			<view class="priv-title font-weight-6">开通后会员立享「合伙人」特权</view>
			<view class="priv-grid m-t-24">
				<view class="priv-item">
					<view class="priv-head">消费95折</view>
					<view class="priv-desc">全平台折上折优惠</view>
				</view>
				<view class="priv-item">
					<view class="priv-head">赠10000积分</view>
					<view class="priv-desc">开通立即发放至账户</view>
				</view>
				<view class="priv-item">
					<view class="priv-head">积分1:1.5</view>
					<view class="priv-desc">每消费1元返1.5积分</view>
				</view>
				<view class="priv-item">
					<view class="priv-head">消费佣金0.5%</view>
					<view class="priv-desc">下级消费永久带单提成</view>
				</view>
			</view>
			<view class="invite-priv m-t-20 font-24" style="color: #F5B67F;">
				★ 邀约奖励：邀约办理钻石会员奖25%，黑钻奖30%，合伙人奖35%。
			</view>
		</view>

		<view class="submit-btn-wrap">
			<button class="submit-btn" :loading="submitting" @click="handleOpen">
				确认立即开通合伙人
			</button>
		</view>
	</view>
</template>

<script>
import { openPartnerByBoss } from "@/common/js/api.js";

export default {
	data() {
		return {
			mobile: '',
			customPrice: '',
			submitting: false
		};
	},
	computed: {
		shopIncome() {
			let p = parseFloat(this.customPrice) || 0;
			return (p * 0.5).toFixed(2);
		},
		cityIncome() {
			let p = parseFloat(this.customPrice) || 0;
			return (p * 0.5).toFixed(2);
		}
	},
	methods: {
		handleOpen() {
			if (!this.mobile || this.mobile.length !== 11) {
				uni.showToast({
					title: '请输入正确的11位手机号',
					icon: 'none'
				});
				return;
			}
			let price = parseFloat(this.customPrice);
			if (!price || isNaN(price) || price <= 0) {
				uni.showToast({
					title: '请输入有效的开通金额',
					icon: 'none'
				});
				return;
			}

			uni.showModal({
				title: '确认开通合伙人',
				content: `确定以 ￥${price} 为手机号 ${this.mobile} 开通合伙人身份吗？`,
				success: (res) => {
					if (res.confirm) {
						this.submitOpen(price);
					}
				}
			});
		},
		submitOpen(price) {
			this.submitting = true;
			openPartnerByBoss({
				mobile: this.mobile,
				custom_price: price
			}).then(res => {
				this.submitting = false;
				if (res.code === 0) {
					uni.showToast({
						title: res.message || '开通成功！',
						icon: 'success'
					});
					setTimeout(() => {
						uni.navigateBack();
					}, 1800);
				} else {
					uni.showToast({
						title: res.message || '开通失败',
						icon: 'none'
					});
				}
			}).catch(err => {
				this.submitting = false;
			});
		}
	}
};
</script>

<style lang="scss">
page {
	background: #101014;
	color: #fff;
}

.partner-page {
	padding: 32rpx;
	padding-bottom: 120rpx;
}

.hero-banner {
	background: linear-gradient(135deg, #2A2118 0%, #1A1612 100%);
	border: 2rpx solid #5A4328;
	border-radius: 24rpx;
	padding: 36rpx 32rpx;

	.hero-tag {
		display: inline-block;
		font-size: 20rpx;
		background: #F5B67F;
		color: #2A1807;
		font-weight: 600;
		padding: 4rpx 14rpx;
		border-radius: 6rpx;
	}

	.hero-title {
		font-size: 40rpx;
		font-weight: 600;
		color: #FAD5B4;
		margin-top: 16rpx;
	}

	.hero-desc {
		font-size: 24rpx;
		color: #B2A292;
		margin-top: 12rpx;
		line-height: 1.6;
	}
}

.form-card {
	background: #1B1D24;
	border-radius: 20rpx;
	padding: 32rpx;
	border: 1rpx solid #282B36;

	.form-label {
		font-size: 28rpx;
		color: #CCCCCC;
		margin-bottom: 16rpx;
	}

	.input-wrap {
		background: #101014;
		border: 2rpx solid #2E3240;
		border-radius: 12rpx;
		padding: 20rpx 24rpx;

		.symbol {
			color: #F5B67F;
			font-size: 32rpx;
			margin-right: 12rpx;
			font-weight: 600;
		}

		.input-field {
			flex: 1;
			font-size: 30rpx;
			color: #fff;
		}
	}
}

.split-preview {
	background: rgba(245, 182, 127, 0.08);
	border: 1rpx dashed rgba(245, 182, 127, 0.4);
	border-radius: 16rpx;
	padding: 24rpx;

	.split-title {
		font-size: 28rpx;
		color: #FAD5B4;

		.split-tip {
			font-size: 20rpx;
			color: #8E929E;
		}
	}

	.split-row {
		font-size: 26rpx;

		.split-label {
			color: #B2A292;
		}
		.split-val {
			color: #F5B67F;
			font-weight: 600;
		}
	}

	.split-note {
		font-size: 20rpx;
		color: #8E929E;
		line-height: 1.5;
	}
}

.privilege-card {
	background: #1B1D24;
	border-radius: 20rpx;
	padding: 32rpx;
	border: 1rpx solid #282B36;

	.priv-title {
		font-size: 28rpx;
		color: #FAD5B4;
	}

	.priv-grid {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;

		.priv-item {
			width: 48%;
			background: #232630;
			border-radius: 12rpx;
			padding: 20rpx 16rpx;
			margin-bottom: 16rpx;
			box-sizing: border-box;

			.priv-head {
				font-size: 26rpx;
				font-weight: 600;
				color: #FAD5B4;
			}
			.priv-desc {
				font-size: 20rpx;
				color: #8E929E;
				margin-top: 6rpx;
			}
		}
	}
}

.submit-btn-wrap {
	margin-top: 60rpx;

	.submit-btn {
		background: linear-gradient(135deg, #FAD5B4 0%, #D49D6A 100%);
		color: #2A1807;
		font-size: 32rpx;
		font-weight: 600;
		border-radius: 44rpx;
		height: 88rpx;
		line-height: 88rpx;
		border: none;
	}
}
</style>
