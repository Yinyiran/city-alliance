<template>
	<view>
		<nvabar :title="title" bgcolor='#ffffff' :type='1' :titleStyle='titleStyle'></nvabar>

		<!-- 未查单：扫码 / 手输订单号 -->
		<view class="search-box" v-if="!info.order_id">
			<view class="scan-btn" @click="scan">扫一扫核销码/订单号</view>
			<view class="or-line"><view class="line"></view><text>或手动输入</text><view class="line"></view></view>
			<view class="input-row">
				<input class="order-input" v-model="orderNo" placeholder="请输入订单号或核销码" placeholder-class="ph" />
				<view class="query-btn" @click="searchOrder">查询</view>
			</view>
			<view class="tip">支持到店消费(自定义金额)、预约到店(改约)、到店自提收款</view>
		</view>

		<!-- 已查单 -->
		<block v-else>
			<!-- 会员识别 -->
			<view class="card">
				<view class="card-title">顾客信息</view>
				<view class="row">
					<text class="k">顾客</text>
					<text class="v">{{ info.name || '顾客' }}</text>
				</view>
				<view class="row">
					<text class="k">手机号</text>
					<text class="v">{{ info.mobile || '-' }}</text>
				</view>
				<view class="row">
					<text class="k">会员等级</text>
					<text class="v">{{ info.level_name || '普通会员' }}</text>
				</view>
			</view>

			<!-- 订单信息 -->
			<view class="card">
				<view class="card-title">订单信息</view>
				<view class="row">
					<text class="k">订单号</text>
					<text class="v">{{ info.order_no }}</text>
				</view>
				<view class="row">
					<text class="k">交易类型</text>
					<text class="v">{{ info.trade_type_name }}</text>
				</view>
				<view class="row" v-if="info.delivery_store_name">
					<text class="k">核销门店</text>
					<text class="v">{{ info.delivery_store_name }}</text>
				</view>
				<view class="row" v-if="Number(info.trade_type) === 2">
					<text class="k">预约时间</text>
					<text class="v">{{ info.appointment_date }} {{ info.appointment_slot }}</text>
				</view>
				<view class="row">
					<text class="k">订单状态</text>
					<text class="v">{{ info.order_status_name }}</text>
				</view>
				<view class="goods-wrap" v-if="info.goods_list && info.goods_list.length">
					<view class="goods-item" v-for="(g, idx) in info.goods_list" :key="idx">
						<text class="g-name">{{ g.sku_name }}</text>
						<text class="g-money">￥{{ g.price }} × {{ g.num }}</text>
					</view>
				</view>
			</view>

			<!-- 收款金额 -->
			<view class="card">
				<view class="card-title">收款金额</view>
				<view class="amount-box">
					<text class="symbol">￥</text>
					<input class="amount-input" type="digit" v-model="amount" placeholder="0.00" placeholder-class="ph" />
				</view>
				<view class="amount-tip">订单应收 ￥{{ info.pay_money }}</view>
			</view>

			<!-- 生成收款码 -->
			<view class="btn" :class="{ 'btn-disabled': submitting || paid }" v-if="!payInfo" @click="genPayCode">
				<text v-if="submitting">生成中...</text>
				<text v-else-if="paid">已收款</text>
				<text v-else>生成收款码</text>
			</view>

			<!-- 收款码展示 -->
			<view class="card pay-card" v-if="payInfo">
				<view class="card-title">请顾客扫码付款</view>
				<image class="pay-qr" :src="payInfo" mode="aspectFit"></image>
				<view class="pay-amount">￥{{ amount || info.pay_money }}</view>
				<view class="pay-tip">顾客扫码支付后，状态会自动更新</view>
				<view class="btn btn-plain" @click="resetPay">重新生成</view>
			</view>

			<!-- 预约到店：改约 -->
			<view class="btn btn-plain" v-if="Number(info.trade_type) === 2" @click="openReschedule">
				改约
			</view>
		</block>

		<!-- 改约面板 -->
		<view class="mask" v-if="showReschedule" @click="showReschedule = false"></view>
		<view class="panel" v-if="showReschedule">
			<view class="panel-title">修改预约时间</view>
			<picker mode="selector" :range="dateList" :value="dateIndex" @change="onDateChange">
				<view class="picker">日期：{{ dateList[dateIndex] || '-' }}</view>
			</picker>
			<scroll-view scroll-y class="slot-wrap">
				<view class="slot" v-for="(s, i) in slotList" :key="i" :class="{ 'slot-active': i === slotIndex }"
					@click="slotIndex = i">
					{{ s.slot_text || s.start_time }}
				</view>
				<view class="slot-empty" v-if="!slotList.length">该日期暂无可约时段</view>
			</scroll-view>
			<view class="panel-btns">
				<view class="p-btn" @click="showReschedule = false">取消</view>
				<view class="p-btn p-btn-primary" @click="doReschedule">确定改约</view>
			</view>
		</view>
	</view>
</template>

<script>
	import { getOrderInfo, cashierCode, cashierSlots, cashierReschedule } from "@/common/js/api.js"

	export default {
		data() {
			return {
				title: '收银台',
				titleStyle: "color:#222",
				orderNo: '',
				info: {},
				amount: '',
				submitting: false,
				paid: false,
				payInfo: '',
				showReschedule: false,
				dateList: [],
				dateIndex: 0,
				slotList: [],
				slotIndex: -1
			};
		},
		onLoad(res) {
			if (res && res.order_no) {
				this.orderNo = res.order_no;
				this.searchOrder();
			}
		},
		methods: {
			//扫码：扫核销码或订单二维码
			scan() {
				uni.scanCode({
					success: (r) => {
						this.orderNo = r.result || '';
						this.searchOrder();
					}
				});
			},
			searchOrder() {
				const code = String(this.orderNo || '').trim();
				if (!code) {
					uni.showToast({ title: '请输入订单号或核销码', icon: 'none' });
					return;
				}
				uni.showLoading({ title: '查询中' });
				getOrderInfo({ delivery_code: code }).then(res => {
					uni.hideLoading();
					if (res.code !== 0 || !res.data) {
						uni.showToast({ title: res.message || '未找到订单', icon: 'none' });
						return;
					}
					this.info = res.data;
					this.amount = String(res.data.pay_money || '');
					this.paid = Number(res.data.pay_status) > 0;
				}).catch(() => {
					uni.hideLoading();
					uni.showToast({ title: '查询失败', icon: 'none' });
				});
			},
			//生成收款码（顾客扫码付款）
			genPayCode() {
				if (this.submitting) return;
				const money = Number(this.amount || this.info.pay_money || 0);
				if (!(money > 0)) {
					uni.showToast({ title: '请输入正确的收款金额', icon: 'none' });
					return;
				}
				this.submitting = true;
				cashierCode({
					order_no: this.info.order_no,
					amount: money,
					store_id: this.info.delivery_store_id || 0,
					pay_type: 'WECHAT_PAY'
				}).then(res => {
					this.submitting = false;
					if (res.code !== 0) {
						uni.showToast({ title: res.message || '收款码生成失败', icon: 'none' });
						return;
					}
					const pi = res.data.pay_info || {};
					this.payInfo = pi.code_url || pi.qr_code || pi.qrCode || '';
					if (!this.payInfo) {
						uni.showToast({ title: '未获取到收款二维码', icon: 'none' });
					}
				}).catch(() => {
					this.submitting = false;
					uni.showToast({ title: '生成失败', icon: 'none' });
				});
			},
			resetPay() {
				this.payInfo = '';
			},
			//改约：拉取未来 7 天日期
			openReschedule() {
				const list = [];
				const today = new Date();
				for (let i = 0; i < 7; i++) {
					const d = new Date(today.getTime() + i * 86400000);
					const mm = d.getMonth() + 1;
					const dd = d.getDate();
					list.push(d.getFullYear() + '-' + (mm < 10 ? '0' + mm : mm) + '-' + (dd < 10 ? '0' + dd : dd));
				}
				this.dateList = list;
				this.dateIndex = 0;
				this.showReschedule = true;
				this.loadSlots();
			},
			onDateChange(e) {
				this.dateIndex = Number(e.detail.value);
				this.loadSlots();
			},
			loadSlots() {
				const goods = (this.info.goods_list || [])[0] || {};
				this.slotIndex = -1;
				if (!goods.goods_id || !this.info.delivery_store_id) {
					this.slotList = [];
					return;
				}
				cashierSlots({
					goods_id: goods.goods_id,
					store_id: this.info.delivery_store_id,
					date: this.dateList[this.dateIndex],
					num: goods.num || 1
				}).then(res => {
					this.slotList = (res.data || []).filter(s => s.can_select);
				}).catch(() => {
					this.slotList = [];
				});
			},
			doReschedule() {
				const slot = this.slotList[this.slotIndex];
				if (!slot) {
					uni.showToast({ title: '请选择可预约时段', icon: 'none' });
					return;
				}
				const goods = (this.info.goods_list || [])[0] || {};
				cashierReschedule({
					order_id: this.info.order_id,
					appointment_date: this.dateList[this.dateIndex],
					start_time: slot.start_time,
					appointment_slot: slot.slot_text || '',
					num: goods.num || 1
				}).then(res => {
					if (res.code !== 0) {
						uni.showToast({ title: res.message || '改约失败', icon: 'none' });
						return;
					}
					uni.showToast({ title: '改约成功' });
					this.showReschedule = false;
					this.info.appointment_date = this.dateList[this.dateIndex];
					this.info.appointment_slot = slot.slot_text || slot.start_time;
				}).catch(() => {
					uni.showToast({ title: '改约失败', icon: 'none' });
				});
			}
		}
	}
</script>

<style lang="scss">
	.search-box {
		padding: 60rpx 40rpx;

		.scan-btn {
			height: 180rpx;
			line-height: 180rpx;
			text-align: center;
			border-radius: 20rpx;
			background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
			color: #FFFFFF;
			font-size: 32rpx;
			font-weight: 600;
		}

		.or-line {
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 40rpx 0;

			.line {
				flex: 1;
				height: 1rpx;
				background: #EEE;
			}

			text {
				font-size: 24rpx;
				color: #999;
				padding: 0 20rpx;
			}
		}

		.input-row {
			display: flex;
			align-items: center;

			.order-input {
				flex: 1;
				height: 88rpx;
				border: 1rpx solid #E5E5E5;
				border-radius: 12rpx;
				padding: 0 20rpx;
				font-size: 28rpx;
			}

			.query-btn {
				margin-left: 20rpx;
				height: 88rpx;
				line-height: 88rpx;
				padding: 0 36rpx;
				border-radius: 12rpx;
				background: #FF6600;
				color: #FFFFFF;
				font-size: 28rpx;
			}
		}

		.tip {
			margin-top: 24rpx;
			font-size: 22rpx;
			color: #999;
			text-align: center;
		}
	}

	.ph {
		color: #BBB;
	}

	.card {
		margin: 20rpx 24rpx;
		padding: 30rpx 28rpx;
		background: #FFFFFF;
		border-radius: 20rpx;

		.card-title {
			font-size: 30rpx;
			font-weight: 600;
			color: #222;
			margin-bottom: 16rpx;
		}

		.row {
			display: flex;
			justify-content: space-between;
			padding: 10rpx 0;
			font-size: 26rpx;

			.k {
				color: #888;
			}

			.v {
				color: #222;
				max-width: 60%;
				text-align: right;
			}
		}

		.goods-wrap {
			margin-top: 16rpx;
			padding-top: 16rpx;
			border-top: 1rpx solid #F5F5F5;

			.goods-item {
				display: flex;
				justify-content: space-between;
				padding: 8rpx 0;
				font-size: 24rpx;

				.g-name {
					color: #555;
					flex: 1;
				}

				.g-money {
					color: #F51D1D;
				}
			}
		}
	}

	.amount-box {
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 20rpx 0;

		.symbol {
			font-size: 40rpx;
			color: #F51D1D;
			font-weight: bold;
		}

		.amount-input {
			width: 300rpx;
			font-size: 60rpx;
			font-weight: bold;
			color: #F51D1D;
			height: 80rpx;
		}
	}

	.amount-tip {
		text-align: center;
		font-size: 24rpx;
		color: #888;
	}

	.btn {
		margin: 30rpx 24rpx;
		height: 96rpx;
		line-height: 96rpx;
		text-align: center;
		border-radius: 48rpx;
		background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
		color: #FFFFFF;
		font-size: 32rpx;
		font-weight: 600;

		&.btn-disabled {
			background: #CCCCCC;
		}

		&.btn-plain {
			background: #FFFFFF;
			color: #FF6600;
			border: 1rpx solid #FF6600;
		}
	}

	.pay-card {
		text-align: center;

		.pay-qr {
			width: 400rpx;
			height: 400rpx;
			margin: 20rpx auto;
			display: block;
		}

		.pay-amount {
			font-size: 44rpx;
			font-weight: bold;
			color: #F51D1D;
		}

		.pay-tip {
			font-size: 24rpx;
			color: #888;
			margin-top: 12rpx;
		}
	}

	.mask {
		position: fixed;
		left: 0;
		top: 0;
		right: 0;
		bottom: 0;
		background: rgba(0, 0, 0, 0.5);
		z-index: 999;
	}

	.panel {
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		z-index: 1000;
		background: #FFFFFF;
		border-radius: 24rpx 24rpx 0 0;
		padding: 40rpx 32rpx;

		.panel-title {
			font-size: 32rpx;
			font-weight: 600;
			text-align: center;
			margin-bottom: 24rpx;
		}

		.picker {
			height: 88rpx;
			line-height: 88rpx;
			border: 1rpx solid #E5E5E5;
			border-radius: 12rpx;
			padding: 0 20rpx;
			font-size: 28rpx;
		}

		.slot-wrap {
			max-height: 460rpx;
			margin-top: 24rpx;
		}

		.slot {
			display: inline-block;
			min-width: 200rpx;
			text-align: center;
			padding: 16rpx 20rpx;
			margin: 0 16rpx 16rpx 0;
			border: 1rpx solid #E5E5E5;
			border-radius: 10rpx;
			font-size: 26rpx;
		}

		.slot-active {
			border-color: #FF6600;
			color: #FF6600;
			background: #FFF4EC;
		}

		.slot-empty {
			text-align: center;
			color: #999;
			font-size: 26rpx;
			padding: 60rpx 0;
		}

		.panel-btns {
			display: flex;
			margin-top: 24rpx;

			.p-btn {
				flex: 1;
				height: 88rpx;
				line-height: 88rpx;
				text-align: center;
				border-radius: 44rpx;
				background: #F5F5F5;
				font-size: 30rpx;
				margin: 0 10rpx;
			}

			.p-btn-primary {
				background: #FF6600;
				color: #FFFFFF;
			}
		}
	}
</style>
