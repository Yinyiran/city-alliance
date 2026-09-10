<template>
	<view>
		<nvabar title='我的积分'></nvabar>
		<image src="/otherpages/static/bg10.png" class="bg2s" mode=""></image>
		<view class="part1 acea-row row-between row-middle">
			<view class="point-left">
				<view class="point-label">我的积分</view>
				<view class="point-val">{{ Math.ceil(point) }}</view>
				<view class="point-avail">可用积分<text>{{ Math.ceil(point) }}</text></view>
			</view>
			<view class="rule-card">
				<view class="rule-header acea-row row-middle row-between">
					<view class="acea-row row-middle">
						<text class="rule-light-icon">💡</text>
						<text class="rule-title">积分获取方式</text>
					</view>
				</view>
				<view class="rule-dash-line"></view>
				<view class="rule-list">
					<view class="rule-item acea-row row-middle row-between" @click="goMember">
						<view class="acea-row row-middle item-left">
							<text class="item-icon">🛍️</text>
							<text class="item-name">商品消费</text>
							<text class="item-desc">消费额×会员积分倍率</text>
						</view>
						<view class="item-badge">金额×{{ rateMultiplierText }}</view>
					</view>
					<view class="rule-item acea-row row-middle row-between" @click="goInvite">
						<view class="acea-row row-middle item-left">
							<text class="item-icon">👥</text>
							<text class="item-name">邀请注册</text>
							<text class="item-desc">邀请用户注册可得</text>
						</view>
						<view class="item-badge">+10积分</view>
					</view>
					<view class="rule-item acea-row row-middle row-between" @click="goVideo">
						<view class="acea-row row-middle item-left">
							<text class="item-icon">🎬</text>
							<text class="item-name">短视频浏览</text>
							<text class="item-desc">观看短视频送积分</text>
						</view>
						<view class="item-badge">+积分奖励</view>
					</view>
				</view>
			</view>
		</view>
		<view class="container">
			<uv-subsection :list="list1" :current="current" custom-style="height: 60rpx;border-radius: 30rpx;"
				custom-item-style="border-radius: 30rpx;" activeColor='#F55606' bgColor="#FFFFFF" mode="button"
				@change="change"></uv-subsection>
			<view class="mains">
				<view class="">积分明细</view>
				<view v-for="(item, index) in list" class="items m-t-24" :key="index">
					<view class="acea-row row-middle row-between m-b-16" v-if="item.point_type == 1">
						<view class="acea-row row-middle">
							<image :src="$util.img(item.headimg)" v-if="item.headimg" class="w-56 h-56 bor-28 m-r-12"
								mode=""></image>
							<image src="/static/avatar.png" v-else class="w-56 h-56 bor-28 m-r-12" mode=""></image>
							<text class="font-28">{{ item.nickname }}</text>
						</view>
						<text class="font-weight-6 font-30" :class="{ fcolor: Number(item.account_data) < 0 }">{{
							item.account_data }}</text>
					</view>
					<view class="acea-row row-middle m-b-16 row-between" style="flex-wrap: nowrap;"
						v-else-if="(item.point_type == 2 || item.point_type == 3) && item.sku_name">
						<view class="acea-row row-middle">
							<image :src="$util.img(item.sku_image)" class="w-100 h-100 bor-16 m-r-12" mode=""></image>
							<view class="text-more-2 font-28 w-340">{{ item.sku_name }}</view>
						</view>
						<view class="t2">
							<view :class="{ fcolor: Number(item.account_data) < 0 }">{{ item.account_data }}</view>
							<view class="" v-if="item.num">共{{ item.num }}件</view>
						</view>
					</view>
					<view class="acea-row row-middle row-between m-b-16" v-else>
						<view class="acea-row row-middle">
							<image :src="$util.img(item.headimg)" v-if="item.headimg" class="w-56 h-56 bor-28 m-r-12"
								mode=""></image>
							<image src="/static/avatar.png" v-else class="w-56 h-56 bor-28 m-r-12" mode=""></image>
							<text class="font-28">{{ item.nickname }}</text>
						</view>
						<text class="font-weight-6 font-30" :class="{ fcolor: Number(item.account_data) < 0 }">{{
							item.account_data }}</text>
					</view>
					<uv-line></uv-line>
					<view class="acea-row row-middle row-between m-t-16">
						<text class="t1" v-if="item.point_type == 1">邀约奖励</text>
						<text class="t1" v-else-if="item.point_type == 2">下单奖励</text>
						<text class="t1" style="background: #F0F0F0;color: #999999;"
							v-else-if="item.point_type == 3">积分消耗</text>
						<text class="t1" v-else>{{ item.remark }}</text>
						<text class="font-22" style="color: #222;">{{ item.create_time }}</text>
					</view>
				</view>
				<uv-empty v-if="!list.length" text="暂无积分明细" mode="data" marginTop="40"></uv-empty>
			</view>
		</view>
	</view>
</template>

<script>
import {
	pointAccount,
	userinfo
} from "@/common/js/api.js"
export default {
	data() {
		return {
			list1: [
				'全部', '邀约奖励', '下单奖励', '积分消耗'
			],
			current: 0,
			page: 1,
			list: [],
			last_page: 1,
			point: 0,
			userInfo: {}
		};
	},
	computed: {
		pointFeedbackRate() {
			const feedback = Number(this.userInfo?.member_level_info?.point_feedback);
			return !isNaN(feedback) && feedback > 0 ? feedback : 1;
		},
		rateMultiplierText() {
			const feedback = this.userInfo?.member_level_info?.point_feedback;
			if (feedback && Number(feedback) > 0) {
				return Number(feedback);
			}
			return '1';
		},
		vipBadgeText() {
			const levelName = this.userInfo?.member_level_name || this.userInfo?.member_level_info?.level_name || '普通会员';
			const feedback = this.userInfo?.member_level_info?.point_feedback;
			if (feedback && Number(feedback) > 0) {
				return `${levelName} ×${Number(feedback)}`;
			}
			return `${levelName} ×1`;
		},
		consumeExamplePoint() {
			return Math.round(100 * this.pointFeedbackRate);
		}
	},
	onShow() {
		this.page = 1
		this.getinfo()
		this.userinfo()
	},
	onReachBottom() {
		if (this.last_page > this.page) {
			this.page++
			this.getinfo()
		}
	},
	methods: {
		change(index) {
			this.current = index;
			this.page = 1
			this.getinfo()
		},
		userinfo() {
			userinfo().then(res => {
				if (res.data) {
					this.point = res.data.point || 0
					this.userInfo = res.data
				}
			})
		},
		getinfo() {
			let params = {
				page: this.page,
				point_type: this.current
			}
			pointAccount(params).then(res => {
				const newList = res.data && res.data.list ? res.data.list : []
				this.list = this.page > 1 ? this.list.concat(newList) : newList
				this.last_page = (res.data && res.data.page_count) || 1
			})
		},
		goMember() {
			uni.navigateTo({
				url: '/otherpages/memberlevel/memberlevel'
			})
		},
		goInvite() {
			uni.navigateTo({
				url: '/otherpages/inviteDatas/inviteDatas'
			})
		},
		goVideo() {
			uni.switchTab({
				url: '/pages/classify/classify'
			})
		}
	}
}
</script>

<style lang="scss">
::v-deep .uv-subsection--button__bar {
	background-color: #FFEDE9 !important;
}

.items {
	padding: 24rpx;
	background: #F8F8F8;
	border-radius: 12rpx;

	.t1 {
		font-weight: 400;
		font-size: 20rpx;
		color: #FA7F28;
		background: #FFF5ED;
		border-radius: 4rpx;
		padding: 0 6rpx;
	}

	.t2 {
		text-align: right;

		>view:first-child {
			font-weight: 600;
			font-size: 30rpx;
			color: #F51D1D;
		}

		>view:last-child {
			font-weight: 400;
			font-size: 22rpx;
			color: #999999;
		}
	}
}

.mains {
	margin: 20rpx 0;
	background: #FFFFFF;
	border-radius: 24rpx;
	padding: 32rpx;

	>view:first-child {
		font-weight: 600;
		font-size: 32rpx;
		color: #1D1E24;
	}
}

.container {
	background: #f5f5f5;
	min-height: calc(100vh - 360rpx);
	box-sizing: border-box;
	border-radius: 24rpx 24rpx 0rpx 0rpx;
	padding: 32rpx;
}

.bg2s {
	position: absolute;
	top: 0;
	left: 0;
	width: 100vw;
	height: 600rpx;
	z-index: -1;
}

.part1 {
	padding: 16rpx 24rpx 28rpx 30rpx;
	box-sizing: border-box;
	position: relative;
	align-items: center;

	.point-left {
		flex: 1;
		flex-shrink: 0;

		.point-label {
			font-weight: 400;
			font-size: 24rpx;
			color: rgba(255, 255, 255, 0.9);
		}

		.point-val {
			font-weight: 700;
			font-size: 56rpx;
			color: #FFFFFF;
			line-height: 1.2;
			margin: 10rpx 0;
			word-break: break-all;
		}

		.point-avail {
			font-weight: 400;
			font-size: 22rpx;
			color: rgba(255, 255, 255, 0.85);

			text {
				font-weight: 600;
				font-size: 22rpx;
				color: #FFFFFF;
				margin-left: 4rpx;
			}
		}
	}

	.rule-card {
		flex: 1.1;
		padding: 20rpx 22rpx 10rpx;
		box-sizing: border-box;
		border-radius: 20rpx;
		background-color: #ffffffc7;

		.rule-header {
			.rule-light-icon {
				font-size: 22rpx;
				margin-right: 6rpx;
			}

			.rule-title {
				font-weight: 700;
				font-size: 20rpx;
				color: #352617;
			}
		}

		.rule-dash-line {
			border-top: 1rpx dashed #E5CDAA;
			margin: 8rpx 0 8rpx;
		}

		.rule-list {
			.rule-item {
				height: 38rpx;
				margin-bottom: 6rpx;

				&:last-child {
					margin-bottom: 0;
				}

				.item-left {
					flex: 1;
					min-width: 0;
					overflow: hidden;

					.item-icon {
						font-size: 18rpx;
						margin-right: 6rpx;
						flex-shrink: 0;
					}

					.item-name {
						font-size: 16rpx;
						font-weight: 600;
						color: #30261D;
						margin-right: 6rpx;
						flex-shrink: 0;
						white-space: nowrap;
					}

					.item-desc {
						font-size: 16rpx;
						color: #9E9182;
						overflow: hidden;
						text-overflow: ellipsis;
						white-space: nowrap;
						flex: 1;
					}
				}

				.item-rate-text {
					font-size: 16rpx;
					color: #6D5E51;
					flex-shrink: 0;
					white-space: nowrap;
					margin-left: 6rpx;
				}

				.item-badge {
					font-size: 16rpx;
					font-weight: 500;
					color: #E86A28;
					background: #FFF0E2;
					border-radius: 6rpx;
					padding: 2rpx 8rpx;
					flex-shrink: 0;
					white-space: nowrap;
					line-height: 24rpx;
					margin-left: 6rpx;
				}
			}
		}
	}
}

.fcolor {
	color: #F51D1D;
}
</style>