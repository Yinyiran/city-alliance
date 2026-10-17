<template>
	<view class="quick-pay-list-page">
		<uv-sticky>
			<nvabar type="1" title="付款明细" titleStyle="color:#222"></nvabar>
		</uv-sticky>

		<!-- 统计卡片 -->
		<view class="summary-card acea-row row-middle row-between">
			<view>
				<view class="font-24 summary-title">累计到店付款</view>
				<view class="font-44 font-weight-6 summary-money m-t-10">￥{{ totalMoney }}</view>
			</view>
			<view class="summary-count font-24">
				共 <text class="font-32 font-weight-6 m-l-4 m-r-4">{{ count }}</text> 笔
			</view>
		</view>

		<!-- 列表容器 -->
		<view class="list-container">
			<view
				class="pay-item-card"
				v-for="(item, idx) in list"
				:key="idx"
				@click="viewDetail(item)"
			>
				<view class="card-header acea-row row-middle row-between">
					<view class="shop-info acea-row row-middle">
						<image
							:src="$util.img(item.order_goods && item.order_goods[0] ? item.order_goods[0].sku_image : '')"
							class="shop-logo"
							mode="aspectFill"
						></image>
						<view class="shop-name font-30 font-weight-6 m-l-16 text-more">{{ item.site_name }}</view>
					</view>
					<view class="status-badge" :class="item.pay_status == 1 ? 'paid' : 'unpaid'">
						{{ item.pay_status == 1 ? '支付成功' : (item.order_status_name || '待支付') }}
					</view>
				</view>

				<view class="card-body acea-row row-middle row-between m-t-24">
					<view class="amount-wrap">
						<text class="font-24">实付金额 </text>
						<text class="font-40 font-weight-6 pay-amount">￥{{ Number(item.pay_money || 0).toFixed(2) }}</text>
					</view>
					<view class="action-arrow acea-row row-middle">
						<text class="font-22" style="color: #999;">详情</text>
						<uv-icon name="arrow-right" size="12" color="#bbb"></uv-icon>
					</view>
				</view>

				<view class="card-footer m-t-20 font-22">
					<view class="footer-row acea-row row-between" v-if="item.buyer_message">
						<text class="label">付款备注：</text>
						<text class="value text-more w-460">{{ item.buyer_message }}</text>
					</view>
					<view class="footer-row acea-row row-between m-t-8">
						<text class="label">订单单号：</text>
						<text class="value">{{ item.order_no }}</text>
					</view>
					<view class="footer-row acea-row row-between m-t-8">
						<text class="label">付款时间：</text>
						<text class="value">{{ formatTime(item.pay_time || item.create_time) }}</text>
					</view>
				</view>
			</view>

			<!-- 空数据展示 -->
			<view v-if="!loading && !list.length" class="empty-box p-60 text-center">
				<uv-empty mode="list" text="暂无付款记录" marginTop="100"></uv-empty>
				<button class="go-pay-btn m-t-40" @click="goToQuickPay">去付款</button>
			</view>
		</view>
	</view>
</template>

<script>
import { quickPayList } from '@/common/js/api.js';

export default {
	data() {
		return {
			list: [],
			page: 1,
			page_size: 20,
			last_page: 1,
			count: 0,
			totalMoney: '0.00',
			loading: false
		};
	},
	onShow() {
		this.page = 1;
		this.getList();
	},
	onPullDownRefresh() {
		this.page = 1;
		this.getList().finally(() => {
			uni.stopPullDownRefresh();
		});
	},
	onReachBottom() {
		if (this.page < this.last_page) {
			this.page++;
			this.getList();
		}
	},
	methods: {
		getList() {
			this.loading = true;
			const params = {
				page: this.page,
				page_size: this.page_size
			};
			return quickPayList(params)
				.then(res => {
					this.loading = false;
					if (res.code === 0 && res.data) {
						let records = res.data.list || [];
						this.list = this.page > 1 ? this.list.concat(records) : records;
						this.last_page = res.data.page_count || 1;
						this.count = res.data.count || 0;

						// 计算累计金额
						let sum = 0;
						this.list.forEach(item => {
							if (Number(item.pay_status) === 1) {
								sum += parseFloat(item.pay_money || 0);
							}
						});
						this.totalMoney = sum.toFixed(2);
					}
				})
				.catch(() => {
					this.loading = false;
				});
		},
		formatTime(val) {
			if (!val || Number(val) <= 0) return '--';
			return this.$util.timeStampTurnTime(Number(val));
		},
		viewDetail(item) {
			uni.navigateTo({
				url: `/otherpages/orderDetail/orderDetail?id=${item.order_id}`
			});
		},
		goToQuickPay() {
			uni.navigateTo({
				url: '/otherpages/quickPay/quickPay'
			});
		}
	}
};
</script>

<style lang="scss">
.quick-pay-list-page {
	min-height: 100vh;
	background: #f7f8fa;
	padding-bottom: 60rpx;
}

.summary-card {
	margin: 24rpx 32rpx;
	padding: 28rpx 32rpx;
	background: linear-gradient(135deg, #FFF0E6 0%, #FFFFFF 100%);
	border: 2rpx solid #FFE4D6;
	border-radius: 20rpx;

	.summary-title {
		color: #888;
	}

	.summary-money {
		color: #F51D1D;
	}

	.summary-count {
		color: #F55606;
	}
}

.list-container {
	padding: 0 32rpx;
}

.pay-item-card {
	background: #ffffff;
	border-radius: 20rpx;
	padding: 28rpx;
	margin-bottom: 20rpx;
	box-shadow: 0 2rpx 12rpx rgba(0, 0, 0, 0.03);

	.card-header {
		.shop-logo {
			width: 60rpx;
			height: 60rpx;
			border-radius: 12rpx;
			background: #f5f5f5;
		}

		.shop-name {
			max-width: 420rpx;
			color: #222;
		}

		.status-badge {
			font-size: 22rpx;
			padding: 4rpx 16rpx;
			border-radius: 20rpx;

			&.paid {
				background: #E8F8F0;
				color: #07c160;
				font-weight: 500;
			}

			&.unpaid {
				background: #FFF0E6;
				color: #F55606;
			}
		}
	}

	.card-body {
		.amount-wrap {
			color: #333;

			.pay-amount {
				color: #222;
			}
		}
	}

	.card-footer {
		background: #fafafa;
		border-radius: 12rpx;
		padding: 16rpx 20rpx;

		.label {
			color: #888;
		}

		.value {
			color: #555;
		}
	}
}

.go-pay-btn {
	display: inline-block;
	background: linear-gradient(135deg, #FF6A00 0%, #F51D1D 100%);
	color: #fff;
	padding: 0 60rpx;
	height: 76rpx;
	line-height: 76rpx;
	border-radius: 38rpx;
	font-size: 28rpx;
	font-weight: 600;
}
</style>
