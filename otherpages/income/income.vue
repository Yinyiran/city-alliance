<template>
	<view class="income-page">
		<nvabar type='1' title='我的收益中心' titleStyle='color:#222'></nvabar>
		
		<!-- 顶部核心收益资产卡片 -->
		<view class="asset-card m-32">
			<view class="asset-top acea-row row-between row-middle">
				<view>
					<view class="asset-label">累计推广总收益 (元)</view>
					<view class="asset-val font-56 font-weight-6">
						<text class="unit font-36">￥</text>{{ summary.total_income || '0.00' }}
					</view>
				</view>
				<view class="withdraw-btn acea-row row-middle row-center" @click="$util.redirectTo('/otherpages/cashOut/cashOut')">
					去提现
					<uv-icon name="arrow-right" color="#101014" size="12"></uv-icon>
				</view>
			</view>

			<view class="asset-sub-grid acea-row row-between m-t-32">
				<view class="sub-item" @click="currentTab = 0">
					<view class="sub-label">已解冻会员奖</view>
					<view class="sub-val">￥{{ summary.invite_unlocked || '0.00' }}</view>
				</view>
				<view class="v-line"></view>
				<view class="sub-item" @click="currentTab = 0">
					<view class="sub-label">待解冻会员奖</view>
					<view class="sub-val" style="color: #FF8F1F;">￥{{ summary.invite_locked || '0.00' }}</view>
					<view class="sub-tip font-20">7天+消费解锁</view>
				</view>
				<view class="v-line"></view>
				<view class="sub-item" @click="currentTab = 1">
					<view class="sub-label">带单消费佣金</view>
					<view class="sub-val" style="color: #07c160;">￥{{ summary.bring_commission || '0.00' }}</view>
				</view>
			</view>
		</view>

		<!-- 选项卡切换 -->
		<view class="tab-wrapper m-32">
			<uv-subsection :list="tabList" :current="currentTab" activeColor="#F55606" bgColor="#FFFFFF"
				custom-style="height: 68rpx;border-radius: 34rpx;" custom-item-style="border-radius: 34rpx;"
				mode="button" @change="onTabChange"></uv-subsection>
		</view>

		<!-- 列表区域 -->
		<view class="list-wrapper m-32">
			<!-- 1. 邀约办会员奖励列表 -->
			<view v-if="currentTab === 0">
				<view class="income-card m-b-24" v-for="(item, index) in inviteList" :key="index">
					<view class="card-head acea-row row-between row-middle">
						<view class="acea-row row-middle">
							<image src="/static/avatar.png" v-if="!item.headimg" class="avatar-sm" mode=""></image>
							<image :src="$util.img(item.headimg)" v-else class="avatar-sm" mode=""></image>
							<view class="m-l-16">
								<view class="font-28 font-weight-6">{{ item.nickname || '新办理会员' }}</view>
								<view class="font-22" style="color: #999;">{{ item.create_time_text }}</view>
							</view>
						</view>
						<view class="status-badge" :class="item.status == 1 ? 'badge-success' : 'badge-warn'">
							{{ item.status == 1 ? '已解冻入账' : '待7天+消费' }}
						</view>
					</view>

					<view class="card-divider m-t-20 m-b-20"></view>

					<view class="card-body acea-row row-between row-middle">
						<view>
							<view class="font-24" style="color: #666;">办理等级：<text class="font-weight-6" style="color: #222;">{{ item.level_name || '会员' }}</text></view>
							<view class="font-24 m-t-8" style="color: #666;">会员费：￥{{ item.order_money }}（奖励 {{ item.invite_rate }}%）</view>
							<view class="font-22 m-t-8" style="color: #999;" v-if="item.status == 0">
								* 距离7天无理由结束约 {{ item.remain_days }} 天，需下级在商城产生消费
							</view>
						</view>
						<view class="text-right">
							<view class="reward-money font-36 font-weight-6">+￥{{ item.invite_money }}</view>
						</view>
					</view>
				</view>

				<view class="empty-box p-40" v-if="inviteList.length === 0 && !loading">
					<image src="/otherpages/static/empty.png" class="empty-img" mode="aspectFit"></image>
					<view class="empty-txt font-26 m-t-16">暂无邀约会员奖励记录，快去邀请好友办卡吧！</view>
					<view class="invite-action-btn m-t-32 acea-row row-middle row-center" @click="$util.redirectTo('/otherpages/poster/poster')">
						邀请好友办理会员
					</view>
				</view>
			</view>

			<!-- 2. 带单消费分润佣金列表 -->
			<view v-else>
				<view class="income-card m-b-24" v-for="(item, index) in bringList" :key="index">
					<view class="card-head acea-row row-between row-middle">
						<view class="font-26 font-weight-6">订单号: {{ item.order_no || '--' }}</view>
						<view class="status-badge badge-success">带单已入账</view>
					</view>

					<view class="card-divider m-t-20 m-b-20"></view>

					<view class="card-body acea-row row-between row-middle">
						<view>
							<view class="font-24" style="color: #666;">出单门店：{{ item.site_name || '联盟门店' }}</view>
							<view class="font-24 m-t-8" style="color: #666;">消费总额：￥{{ item.real_goods_money || '0.00' }}（提成 {{ item.two_rate }}%）</view>
							<view class="font-22 m-t-8" style="color: #999;">{{ item.create_time_text }}</view>
						</view>
						<view class="text-right">
							<view class="reward-money font-36 font-weight-6" style="color: #07c160;">+￥{{ item.two_commission }}</view>
						</view>
					</view>
				</view>

				<view class="empty-box p-40" v-if="bringList.length === 0 && !loading">
					<image src="/otherpages/static/empty.png" class="empty-img" mode="aspectFit"></image>
					<view class="empty-txt font-26 m-t-16">暂无带单消费佣金记录</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
import { myIncomeSummary, myIncomeList } from '@/common/js/api.js';

export default {
	data() {
		return {
			tabList: ['邀约会员奖励', '消费带单分润'],
			currentTab: 0,
			summary: {
				total_income: '0.00',
				invite_unlocked: '0.00',
				invite_locked: '0.00',
				bring_commission: '0.00',
				balance_money: '0.00'
			},
			inviteList: [],
			bringList: [],
			invitePage: 1,
			bringPage: 1,
			inviteLastPage: 1,
			bringLastPage: 1,
			loading: false
		};
	},
	onShow() {
		this.fetchSummary();
		this.fetchList(true);
	},
	onPullDownRefresh() {
		this.fetchSummary();
		this.fetchList(true);
		setTimeout(() => {
			uni.stopPullDownRefresh();
		}, 600);
	},
	onReachBottom() {
		if (this.currentTab === 0) {
			if (this.invitePage < this.inviteLastPage) {
				this.invitePage++;
				this.fetchInviteList();
			}
		} else {
			if (this.bringPage < this.bringLastPage) {
				this.bringPage++;
				this.fetchBringList();
			}
		}
	},
	methods: {
		onTabChange(index) {
			this.currentTab = index;
			if (index === 0 && this.inviteList.length === 0) {
				this.fetchInviteList(true);
			} else if (index === 1 && this.bringList.length === 0) {
				this.fetchBringList(true);
			}
		},
		fetchSummary() {
			myIncomeSummary().then(res => {
				if (res.code == 0 && res.data) {
					this.summary = res.data;
				}
			}).catch(() => {});
		},
		fetchList(refresh = false) {
			if (this.currentTab === 0) {
				this.fetchInviteList(refresh);
			} else {
				this.fetchBringList(refresh);
			}
		},
		fetchInviteList(refresh = false) {
			if (refresh) this.invitePage = 1;
			this.loading = true;
			myIncomeList({ type: 1, page: this.invitePage, page_size: 10 }).then(res => {
				this.loading = false;
				if (res.code == 0 && res.data) {
					this.inviteList = refresh ? res.data.list : this.inviteList.concat(res.data.list);
					this.inviteLastPage = res.data.page_count;
				}
			}).catch(() => {
				this.loading = false;
			});
		},
		fetchBringList(refresh = false) {
			if (refresh) this.bringPage = 1;
			this.loading = true;
			myIncomeList({ type: 2, page: this.bringPage, page_size: 10 }).then(res => {
				this.loading = false;
				if (res.code == 0 && res.data) {
					this.bringList = refresh ? res.data.list : this.bringList.concat(res.data.list);
					this.bringLastPage = res.data.page_count;
				}
			}).catch(() => {
				this.loading = false;
			});
		}
	}
};
</script>

<style lang="scss" scoped>
.income-page {
	min-height: 100vh;
	background-color: #F8F9FA;
	padding-bottom: 60rpx;
}

.asset-card {
	background: linear-gradient(135deg, #1C1E23 0%, #2A2D35 100%);
	border-radius: 28rpx;
	padding: 40rpx 36rpx;
	color: #FFFFFF;
	box-shadow: 0 12rpx 32rpx rgba(0, 0, 0, 0.12);
}

.asset-label {
	font-size: 24rpx;
	color: #B5B8C2;
}

.asset-val {
	color: #F8D39E;
	margin-top: 12rpx;
	.unit {
		margin-right: 4rpx;
	}
}

.withdraw-btn {
	background: linear-gradient(90deg, #FAD5B4 0%, #F5B67F 100%);
	color: #101014;
	font-size: 26rpx;
	font-weight: 600;
	padding: 16rpx 32rpx;
	border-radius: 36rpx;
}

.asset-sub-grid {
	border-top: 1rpx solid rgba(255, 255, 255, 0.12);
	padding-top: 28rpx;
	.sub-item {
		flex: 1;
		text-align: center;
	}
	.sub-label {
		font-size: 22rpx;
		color: #9EA2B0;
	}
	.sub-val {
		font-size: 30rpx;
		font-weight: 600;
		color: #FFFFFF;
		margin-top: 8rpx;
	}
	.sub-tip {
		color: #9EA2B0;
		margin-top: 4rpx;
	}
	.v-line {
		width: 1rpx;
		height: 48rpx;
		background: rgba(255, 255, 255, 0.12);
		align-self: center;
	}
}

.tab-wrapper {
	box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.04);
}

.income-card {
	background: #FFFFFF;
	border-radius: 20rpx;
	padding: 28rpx 32rpx;
	box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.03);
}

.avatar-sm {
	width: 64rpx;
	height: 64rpx;
	border-radius: 32rpx;
}

.status-badge {
	font-size: 22rpx;
	padding: 6rpx 16rpx;
	border-radius: 20rpx;
	font-weight: 500;
}

.badge-success {
	background: #E8F8EE;
	color: #07c160;
}

.badge-warn {
	background: #FFF5EA;
	color: #FF8F1F;
}

.card-divider {
	height: 1rpx;
	background: #F0F2F5;
}

.reward-money {
	color: #F55606;
}

.empty-box {
	text-align: center;
	.empty-img {
		width: 240rpx;
		height: 240rpx;
		margin: 0 auto;
	}
	.empty-txt {
		color: #999;
	}
	.invite-action-btn {
		background: linear-gradient(90deg, #FF6A00 0%, #FF481F 100%);
		color: #fff;
		font-size: 28rpx;
		font-weight: 600;
		height: 80rpx;
		border-radius: 40rpx;
		margin: 32rpx auto 0;
		width: 400rpx;
	}
}
</style>
