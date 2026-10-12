<template>
	<view class="income-page">
		<!-- 顶部收益统计总览卡片 -->
		<view class="header-card">
			<view class="title-row acea-row row-between row-middle">
				<text class="label">{{ summary.is_manager == 1 ? '主理人/门店累计收益' : '员工个人累计收益' }}</text>
				<text class="tip">{{ summary.is_manager == 1 ? '分公司对公结算' : '提成收益' }}</text>
			</view>
			<view class="total-amount font-weight-6">
				<text class="symbol">￥</text>{{ totalIncome }}
			</view>
			<view class="stats-grid acea-row row-between m-t-32">
				<view class="stat-col" @click="currentTab = 0">
					<view class="stat-sub">{{ summary.is_manager == 1 ? '带单收益 (2%)' : '带单提成 (1%)' }}</view>
					<view class="stat-val">￥{{ (summary.bring_income || {}).total || '0.00' }}</view>
					<view class="stat-cnt">{{ (summary.bring_income || {}).count || 0 }} 笔订单</view>
				</view>
				<view class="stat-divider"></view>
				<view class="stat-col" @click="currentTab = 1">
					<view class="stat-sub">{{ summary.is_manager == 1 ? '会员收益 (25%)' : '合伙人推广奖励' }}</view>
					<view class="stat-val">￥{{ (summary.member_income || {}).total || '0.00' }}</view>
					<view class="stat-cnt">{{ (summary.member_income || {}).count || 0 }} 位会员</view>
				</view>
			</view>
		</view>

		<!-- 选项卡 -->
		<view class="tab-box m-t-32">
			<view class="tab-item" :class="{ active: currentTab === 0 }" @click="switchTab(0)">
				{{ summary.is_manager == 1 ? '带单收益明细 (2%)' : '带单提成明细 (1%)' }}
			</view>
			<view class="tab-item" :class="{ active: currentTab === 1 }" @click="switchTab(1)">
				{{ summary.is_manager == 1 ? '会员收益明细 (25%)' : '合伙人推广奖励' }}
			</view>
		</view>

		<!-- 带单收益列表 -->
		<view class="list-box m-t-24" v-if="currentTab === 0">
			<view class="order-card" v-for="(item, index) in bringList" :key="index">
				<view class="order-top acea-row row-between row-middle">
					<text class="order-no">订单号: {{ item.order_no || item.out_trade_no || '--' }}</text>
					<text class="tag-badge">{{ summary.is_manager == 1 ? '主理人带单' : '个人带单' }}</text>
				</view>
				<view class="order-body m-t-20 acea-row row-between row-middle">
					<view>
						<view class="font-26" style="color: #999;">出单门店: {{ item.site_name || '联盟门店' }}</view>
						<view class="font-24 m-t-8" style="color: #666;">消费总额: ￥{{ item.order_money || item.real_goods_money || '0.00' }}</view>
						<view class="font-22 m-t-8" style="color: #555;">提成比例: {{ summary.is_manager == 1 ? '2.0%' : '1.0%' }}</view>
					</view>
					<view class="text-right">
						<view class="money-add font-36 font-weight-6">+￥{{ item.commission || item.staff_commission || '0.00' }}</view>
						<view class="font-22 m-t-8" style="color: #777;">{{ formatTime(item.create_time) }}</view>
					</view>
				</view>
			</view>
			<view class="empty-tip" v-if="bringList.length === 0 && !loading">
				暂无带单收益记录
			</view>
		</view>

		<!-- 会员收益列表 -->
		<view class="list-box m-t-24" v-else>
			<view class="order-card" v-for="(item, index) in memberList" :key="index">
				<view class="order-top acea-row row-between row-middle">
					<text class="order-no">会员办理: {{ item.level_name || item.buy_level_name || '合伙人/会员' }}</text>
					<text class="tag-badge" :class="item.status == 1 ? 'tag-success' : 'tag-wait'">
						{{ item.status == 1 ? '已解冻到账' : '待7天或自消费解锁' }}
					</text>
				</view>
				<view class="order-body m-t-20 acea-row row-between row-middle">
					<view>
						<view class="font-26" style="color: #999;">办理会员: {{ item.nickname || item.mobile || '用户' }}</view>
						<view class="font-24 m-t-8" style="color: #666;">会员费: ￥{{ item.order_money || '0.00' }}</view>
						<view class="font-22 m-t-8" style="color: #555;">{{ summary.is_manager == 1 ? '主理人分成: 25.0%' : ('推广奖励: ' + (item.invite_rate || '25') + '%') }}</view>
					</view>
					<view class="text-right">
						<view class="money-add font-36 font-weight-6">+￥{{ summary.is_manager == 1 ? (item.shop_money || '0.00') : (item.invite_money || '0.00') }}</view>
						<view class="font-22 m-t-8" style="color: #777;">{{ formatTime(item.create_time) }}</view>
					</view>
				</view>
			</view>
			<view class="empty-tip" v-if="memberList.length === 0 && !loading">
				暂无会员收益记录
			</view>
		</view>
	</view>
</template>

<script>
import { getIncomeSummary, getBringOrderList, getMemberIncomeList } from "@/common/js/api.js";

export default {
	data() {
		return {
			currentTab: 0,
			summary: {
				bring_money: '0.00',
				bring_count: 0,
				member_income: '0.00',
				member_count: 0
			},
			bringList: [],
			memberList: [],
			bringPage: 1,
			memberPage: 1,
			loading: false
		};
	},
	computed: {
		totalIncome() {
			if (this.summary.total_income !== undefined) {
				return Number(this.summary.total_income).toFixed(2);
			}
			let b = parseFloat((this.summary.bring_income || {}).total || this.summary.bring_money) || 0;
			let m = parseFloat((this.summary.member_income || {}).total || this.summary.member_income) || 0;
			return (b + m).toFixed(2);
		}
	},
	onShow() {
		this.fetchSummary();
		this.fetchBringList();
		this.fetchMemberList();
	},
	onReachBottom() {
		if (this.currentTab === 0) {
			this.bringPage++;
			this.fetchBringList(true);
		} else {
			this.memberPage++;
			this.fetchMemberList(true);
		}
	},
	methods: {
		switchTab(index) {
			this.currentTab = index;
		},
		fetchSummary() {
			getIncomeSummary().then(res => {
				if (res.code === 0 && res.data) {
					this.summary = res.data;
				}
			}).catch(() => {});
		},
		fetchBringList(append = false) {
			this.loading = true;
			getBringOrderList({ page: this.bringPage, page_size: 20 }).then(res => {
				this.loading = false;
				if (res.code === 0 && res.data) {
					let list = res.data.list || [];
					this.bringList = append ? this.bringList.concat(list) : list;
				}
			}).catch(() => { this.loading = false; });
		},
		fetchMemberList(append = false) {
			this.loading = true;
			getMemberIncomeList({ page: this.memberPage, page_size: 20 }).then(res => {
				this.loading = false;
				if (res.code === 0 && res.data) {
					let list = res.data.list || [];
					this.memberList = append ? this.memberList.concat(list) : list;
				}
			}).catch(() => { this.loading = false; });
		},
		formatTime(timestamp) {
			if (!timestamp) return '--';
			let d = new Date(timestamp * 1000);
			let y = d.getFullYear();
			let m = String(d.getMonth() + 1).padStart(2, '0');
			let day = String(d.getDate()).padStart(2, '0');
			let h = String(d.getHours()).padStart(2, '0');
			let min = String(d.getMinutes()).padStart(2, '0');
			return `${y}-${m}-${day} ${h}:${min}`;
		}
	}
};
</script>

<style lang="scss">
page {
	background: #101014;
	color: #fff;
}

.income-page {
	padding: 32rpx;
}

.header-card {
	background: linear-gradient(135deg, #1F2128 0%, #17181E 100%);
	border: 2rpx solid #333642;
	border-radius: 24rpx;
	padding: 36rpx 32rpx;

	.title-row {
		.label {
			color: #9E9285;
			font-size: 26rpx;
		}
		.tip {
			color: #F5B67F;
			font-size: 22rpx;
			background: rgba(245, 182, 127, 0.15);
			padding: 4rpx 14rpx;
			border-radius: 8rpx;
		}
	}

	.total-amount {
		color: #FAD5B4;
		font-size: 60rpx;
		margin-top: 16rpx;

		.symbol {
			font-size: 36rpx;
			margin-right: 6rpx;
		}
	}

	.stats-grid {
		.stat-col {
			flex: 1;
			text-align: center;

			.stat-sub {
				color: #9E9285;
				font-size: 24rpx;
			}
			.stat-val {
				color: #fff;
				font-size: 36rpx;
				font-weight: 600;
				margin-top: 8rpx;
			}
			.stat-cnt {
				color: #666;
				font-size: 20rpx;
				margin-top: 4rpx;
			}
		}

		.stat-divider {
			width: 2rpx;
			height: 70rpx;
			background: #333642;
			margin-top: 10rpx;
		}
	}
}

.tab-box {
	display: flex;
	background: #1B1D24;
	border-radius: 16rpx;
	padding: 8rpx;

	.tab-item {
		flex: 1;
		text-align: center;
		padding: 18rpx 0;
		font-size: 26rpx;
		color: #8E929E;
		border-radius: 12rpx;
		transition: all 0.2s;

		&.active {
			background: #272A33;
			color: #FAD5B4;
			font-weight: 600;
		}
	}
}

.list-box {
	.order-card {
		background: #1B1D24;
		border-radius: 20rpx;
		padding: 28rpx;
		margin-bottom: 20rpx;
		border: 1rpx solid #282B36;

		.order-top {
			padding-bottom: 20rpx;
			border-bottom: 1rpx solid #282B36;

			.order-no {
				font-size: 24rpx;
				color: #8E929E;
			}

			.tag-badge {
				font-size: 20rpx;
				padding: 4rpx 14rpx;
				border-radius: 6rpx;
				background: rgba(245, 182, 127, 0.15);
				color: #F5B67F;

				&.tag-success {
					background: rgba(7, 193, 96, 0.15);
					color: #07c160;
				}
				&.tag-wait {
					background: rgba(254, 120, 15, 0.15);
					color: #FE780F;
				}
			}
		}

		.money-add {
			color: #F5B67F;
		}
	}

	.empty-tip {
		text-align: center;
		padding: 100rpx 0;
		color: #666;
		font-size: 26rpx;
	}
}
</style>
