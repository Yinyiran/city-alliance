<template>
	<view class="partner-page">
		<view class="hero-banner">
			<view class="hero-tag">店长/老板专属权限</view>
			<view class="hero-title">主动添加合伙人</view>
			<view class="hero-desc">店铺老板可自定义金额为核心客户或员工开通「合伙人」身份！</view>

			<!-- 免扣费名额特权卡片 -->
			<view class="quota-banner m-t-20 acea-row row-middle row-between" v-if="quotaInfo.remain_free_quota > 0">
				<view class="acea-row row-middle">
					<uv-icon name="gift-fill" size="18" color="#2A1807"></uv-icon>
					<text class="quota-txt m-l-10">享有免抽佣特权：剩余 <text class="quota-highlight">{{ quotaInfo.remain_free_quota }}</text> 个名额</text>
				</view>
				<view class="quota-badge">平台0扣费</view>
			</view>
			<view class="quota-banner-empty m-t-20 acea-row row-middle row-between" v-else>
				<text class="quota-txt-sub">免扣费名额已用完，恢复常规分佣机制</text>
				<text class="quota-num-sub">已用 {{ quotaInfo.used_quota }}/{{ quotaInfo.free_quota }}</text>
			</view>
		</view>

		<!-- 等级配额提醒 -->
		<view class="level-quota-card m-t-24 acea-row row-middle row-between">
			<view class="acea-row row-middle">
				<text class="lvl-label">当前店铺：</text>
				<text class="lvl-val font-weight-6">{{ quotaInfo.level_name || '普通店铺' }}</text>
			</view>
			<view class="lvl-count">
				合伙人上限：<text class="lvl-curr">{{ quotaInfo.partner_count }}</text> / {{ quotaInfo.max_partner_count }} 人
			</view>
		</view>

		<view class="form-card m-t-24">
			<view class="form-item">
				<view class="form-label">目标会员手机号</view>
				<view class="input-wrap acea-row row-middle">
					<input class="input-field" type="number" maxlength="11" v-model="mobile" placeholder="请输入被开通会员手机号" />
				</view>
			</view>

			<view class="form-item m-t-32">
				<view class="form-label acea-row row-between row-middle">
					<text>设定开通金额 (元)</text>
					<text class="font-22" style="color: #F5B67F;">最低门槛 ￥50 元</text>
				</view>
				<view class="input-wrap acea-row row-middle">
					<text class="symbol">￥</text>
					<input class="input-field" type="digit" v-model="customPrice" placeholder="最低门槛 50 元起" />
				</view>
			</view>

			<!-- 分润试算卡片 -->
			<view class="split-preview m-t-32" v-if="Number(customPrice) > 0">
				<view class="split-title acea-row row-between row-middle">
					<text class="font-weight-6">分佣收益试算</text>
					<text class="split-tip" v-if="quotaInfo.remain_free_quota > 0">🎉 本单享受免抽佣特权</text>
					<text class="split-tip" v-else>该金额走主理人与分公司</text>
				</view>
				<view class="split-row acea-row row-between m-t-16">
					<text class="split-label">主理人分润 {{ quotaInfo.remain_free_quota > 0 ? '(100% 免扣费特权)' : '(50%)' }}</text>
					<text class="split-val">+￥{{ shopIncome }}</text>
				</view>
				<view class="split-row acea-row row-between m-t-12" v-if="quotaInfo.remain_free_quota <= 0">
					<text class="split-label">分公司分润 (50%)</text>
					<text class="split-val">+￥{{ cityIncome }}</text>
				</view>
				<view class="split-note m-t-16" v-if="quotaInfo.remain_free_quota > 0">
					* 注：在免扣费名额配额内，平台不抽成扣费，资金100%归主理人。
				</view>
				<view class="split-note m-t-16" v-else>
					* 注：免扣费名额已使用完毕，分佣恢复，全部归属于门店与分公司公账结算。
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
			<button 
				class="submit-btn" 
				:class="{ 'btn-limit-block': quotaInfo.is_at_limit == 1 }"
				:loading="submitting" 
				@click="handleOpen"
			>
				{{ quotaInfo.is_at_limit == 1 ? ('合伙人数已达上限(' + quotaInfo.max_partner_count + '人) 请先升级店铺') : '确认立即开通合伙人' }}
			</button>
		</view>
	</view>
</template>

<script>
import { openPartnerByBoss, getPartnerQuotaInfo } from "@/common/js/api.js";

export default {
	data() {
		return {
			site_id: '',
			mobile: '',
			customPrice: '',
			submitting: false,
			quotaInfo: {
				free_quota: 0,
				used_quota: 0,
				remain_free_quota: 0,
				partner_count: 0,
				max_partner_count: 2,
				is_at_limit: 0,
				level_name: '普通店铺',
				min_price: 50.00
			}
		};
	},
	computed: {
		shopIncome() {
			let p = parseFloat(this.customPrice) || 0;
			if (this.quotaInfo && this.quotaInfo.remain_free_quota > 0) {
				return p.toFixed(2);
			}
			return (p * 0.5).toFixed(2);
		},
		cityIncome() {
			let p = parseFloat(this.customPrice) || 0;
			if (this.quotaInfo && this.quotaInfo.remain_free_quota > 0) {
				return '0.00';
			}
			return (p * 0.5).toFixed(2);
		}
	},
	onLoad() {
		this.site_id = uni.getStorageSync('site_id') || '';
		this.fetchQuotaInfo();
	},
	methods: {
		fetchQuotaInfo() {
			getPartnerQuotaInfo({ site_id: this.site_id }).then(res => {
				if (res && res.code === 0 && res.data) {
					this.quotaInfo = res.data;
				}
			}).catch(err => {
				console.error('获取合伙人配额失败', err);
			});
		},
		handleOpen() {
			if (this.quotaInfo && this.quotaInfo.is_at_limit == 1) {
				uni.showModal({
					title: '合伙人名额已满',
					content: `当前店铺属于【${this.quotaInfo.level_name}】，合伙人上限为 ${this.quotaInfo.max_partner_count} 人。请先返回工作台升级为更高等级店铺（如至尊店铺拥有50个名额）！`,
					showCancel: false
				});
				return;
			}
			if (!this.mobile || this.mobile.length !== 11) {
				uni.showToast({
					title: '请输入正确的11位手机号',
					icon: 'none'
				});
				return;
			}
			let price = parseFloat(this.customPrice);
			if (!price || isNaN(price) || price < 50) {
				uni.showToast({
					title: '自定义金额最低门槛为 50 元',
					icon: 'none'
				});
				return;
			}

			let tipMsg = (this.quotaInfo.remain_free_quota > 0) 
				? `确定以 ￥${price} 开通合伙人？(本单享免抽佣特权，收益100%归本店)` 
				: `确定以 ￥${price} 为手机号 ${this.mobile} 开通合伙人身份吗？`;

			uni.showModal({
				title: '确认开通合伙人',
				content: tipMsg,
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
				site_id: this.site_id,
				mobile: this.mobile,
				custom_price: price
			}).then(res => {
				this.submitting = false;
				if (res.code === 0) {
					uni.showToast({
						title: res.message || '开通成功！',
						icon: 'success'
					});
					this.fetchQuotaInfo();
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

	.quota-banner {
		background: #F5B67F;
		border-radius: 12rpx;
		padding: 14rpx 20rpx;
		margin-top: 20rpx;

		.quota-txt {
			font-size: 24rpx;
			color: #2A1807;
			font-weight: 500;
		}
		.quota-highlight {
			font-size: 30rpx;
			font-weight: 700;
			color: #B32400;
			margin: 0 4rpx;
		}
		.quota-badge {
			background: #2A1807;
			color: #F5B67F;
			font-size: 20rpx;
			padding: 4rpx 12rpx;
			border-radius: 6rpx;
			font-weight: 600;
		}
	}

	.quota-banner-empty {
		background: rgba(255, 255, 255, 0.06);
		border-radius: 12rpx;
		padding: 12rpx 20rpx;
		margin-top: 16rpx;

		.quota-txt-sub {
			font-size: 22rpx;
			color: #8E929E;
		}
		.quota-num-sub {
			font-size: 22rpx;
			color: #8E929E;
		}
	}
}

.level-quota-card {
	background: #1B1D24;
	border: 1rpx solid #282B36;
	border-radius: 16rpx;
	padding: 20rpx 28rpx;
	font-size: 26rpx;

	.lvl-label {
		color: #8E929E;
	}
	.lvl-val {
		color: #FAD5B4;
	}
	.lvl-count {
		color: #8E929E;
	}
	.lvl-curr {
		color: #F5B67F;
		font-weight: 600;
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
			font-size: 22rpx;
			color: #F5B67F;
			font-weight: 600;
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

	.btn-limit-block {
		background: #3A3D4D;
		color: #8E929E;
	}
}
</style>
