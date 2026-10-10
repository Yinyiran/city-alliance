<template>
	<view class="page">
		<!-- 交易状态卡片 -->
		<view class="status-card" :class="statusClass">
			<view class="acea-row row-middle">
				<text class="biz-tag" :class="'biz-' + info.biz_type">{{ bizName(info.biz_type) }}</text>
				<text class="status-text">{{ info.trade_status_name }}</text>
			</view>
			<view class="status-desc">{{ statusDesc }}</view>
			<!-- 外卖自配送进度 -->
			<view class="local-progress" v-if="Number(info.order_type) === 3">
				<view class="lp-item" v-for="s in localSteps" :key="s.step"
					:class="{ active: s.step <= localStep, done: s.step < localStep }">
					<view class="lp-dot"></view>
					<view class="lp-name">{{ s.name }}</view>
				</view>
			</view>
		</view>

		<!-- 交付信息 -->
		<view class="card">
			<view class="card-title">{{ deliverTitle }}</view>
			<!-- 到店核销 -->
			<template v-if="Number(info.order_type) === 2">
				<view class="row acea-row row-middle row-between">
					<text class="label">核销门店</text>
					<text class="value">{{ info.delivery_store_name || '门店自提' }}</text>
				</view>
				<view class="row acea-row row-middle row-between" v-if="info.delivery_code">
					<text class="label">核销码</text>
					<text class="value code">{{ info.delivery_code }}</text>
				</view>
				<view class="row acea-row row-middle row-between" v-if="info.appointment_date">
					<text class="label">预约时间</text>
					<text class="value">{{ info.appointment_date }} {{ info.appointment_slot }}</text>
				</view>
			</template>
			<!-- 快递邮寄 / 外卖 -->
			<template v-else>
				<view class="row acea-row row-middle row-between">
					<text class="label">{{ Number(info.order_type) === 3 ? '配送地址' : '收货地址' }}</text>
					<text class="value multi">{{ info.name }} {{ info.mobile }}　{{ info.full_address }}{{ info.address }}</text>
				</view>
				<view class="row acea-row row-middle row-between" v-if="info.express_no">
					<text class="label">快递单号</text>
					<text class="value code">{{ info.express_no }}</text>
				</view>
				<template v-if="Number(info.order_type) === 3 && info.package_info">
					<view class="row acea-row row-middle row-between" v-if="info.package_info.deliverer">
						<text class="label">配送员</text>
						<text class="value">{{ info.package_info.deliverer }} {{ info.package_info.deliverer_mobile }}</text>
					</view>
				</template>
			</template>
		</view>

		<!-- 商品清单 -->
		<view class="card">
			<view class="card-title">商品清单</view>
			<view class="acea-row row-middle m-t-20" v-for="g in info.goods_list" :key="g.order_goods_id">
				<image :src="$util.img(g.sku_image)" class="w-140 h-140 bor-16 m-r-20" mode=""></image>
				<view class="widths h-140 acea-row row-column row-between" style="flex-wrap: nowrap;">
					<view class="text-more-2 font-28">{{ g.sku_name }}</view>
					<view class="acea-row row-middle row-between">
						<view class="price">
							￥<text class="font-30 font-weight-6">{{ g.price }}</text>
						</view>
						<view class="nums">x{{ g.num }}</view>
					</view>
					<view class="g-tag" v-if="g.delivery_no">快递单号：{{ g.delivery_no }}</view>
				</view>
			</view>
		</view>

		<!-- 金额明细 -->
		<view class="card">
			<view class="card-title">金额明细</view>
			<view class="row acea-row row-middle row-between">
				<text class="label">商品金额</text>
				<text class="value">￥{{ info.goods_money }}</text>
			</view>
			<view class="row acea-row row-middle row-between" v-if="Number(info.delivery_money) > 0">
				<text class="label">配送费用</text>
				<text class="value">￥{{ info.delivery_money }}</text>
			</view>
			<view class="row acea-row row-middle row-between" v-if="Number(info.custom_amount) > 0">
				<text class="label">到店自定义金额</text>
				<text class="value">￥{{ info.custom_amount }}</text>
			</view>
			<view class="row acea-row row-middle row-between total">
				<text class="label">实付金额</text>
				<text class="pay">￥{{ info.pay_money }}</text>
			</view>
		</view>

		<!-- 订单信息 -->
		<view class="card">
			<view class="card-title">订单信息</view>
			<view class="row acea-row row-middle row-between">
				<text class="label">订单编号</text>
				<text class="value">{{ info.order_no }}</text>
			</view>
			<view class="row acea-row row-middle row-between">
				<text class="label">下单时间</text>
				<text class="value">{{ info.create_time_text }}</text>
			</view>
			<view class="row acea-row row-middle row-between" v-if="info.pay_time_text">
				<text class="label">支付时间</text>
				<text class="value">{{ info.pay_time_text }}</text>
			</view>
			<view class="row acea-row row-middle row-between" v-if="info.pay_type_name">
				<text class="label">支付方式</text>
				<text class="value">{{ info.pay_type_name }}</text>
			</view>
			<view class="row acea-row row-middle row-between" v-if="info.buyer_message">
				<text class="label">买家留言</text>
				<text class="value multi">{{ info.buyer_message }}</text>
			</view>
		</view>

		<view class="h-160"></view>

		<!-- 底部操作 -->
		<view class="btms iphone-x" v-if="info.actions && info.actions.length">
			<view class="acea-row row-middle row-right">
				<view v-for="act in info.actions" :key="act.key" class="btn1" @click="onAction(act)">{{ act.title }}</view>
			</view>
		</view>

		<!-- 快递发货弹层 -->
		<uv-popup v-model="deliveryPopup.show" mode="bottom" round="16" @close="resetDeliveryPopup">
			<view class="popup-body">
				<view class="popup-title">快递发货</view>
				<view class="form-item">
					<text class="form-label">物流公司<text class="required">*</text></text>
					<view class="select-box"
						@click="deliveryPopup.companyList.length && (deliveryPopup.companyPicker = true)">
						<text :class="{ placeholder: !deliveryPopup.companyName }">
							{{ deliveryPopup.companyName || '请选择物流公司' }}
						</text>
						<uv-icon name="arrow-down" color="#999" size="14"></uv-icon>
					</view>
				</view>
				<view class="form-item">
					<text class="form-label">快递单号<text class="required">*</text></text>
					<uv-input v-model="deliveryPopup.deliveryNo" placeholder="请输入快递单号" border="surround"
						:customStyle="inputStyle" clearable></uv-input>
				</view>
				<view class="form-item">
					<text class="form-label">发货商品</text>
					<view class="goods-check">
						<view class="acea-row row-middle row-between check-item" v-for="g in deliveryPopup.goodsList"
							:key="g.order_goods_id" @click="toggleGoods(g)">
							<view class="acea-row row-middle">
								<!-- 自绘勾选框：uv-checkbox 需搭配 uv-checkbox-group 使用，单独使用会导致勾选态不显示 -->
								<view class="check-box" :class="{ checked: g.checked, disabled: Number(g.delivery_status) !== 0 }">
									<uv-icon v-if="g.checked" name="checkmark" color="#fff" size="12"></uv-icon>
								</view>
								<text class="check-name m-l-12">{{ g.sku_name }}</text>
							</view>
							<text class="nums">x{{ g.num }}</text>
						</view>
					</view>
				</view>
				<view class="popup-btns acea-row row-middle">
					<view class="btn-cancel" @click="deliveryPopup.show = false">取消</view>
					<view class="btn-confirm" @click="submitExpressDelivery">确认发货</view>
				</view>
			</view>
			<uv-popup v-model="deliveryPopup.companyPicker" mode="bottom" round="16">
				<view class="popup-body">
					<view class="popup-title">选择物流公司</view>
					<scroll-view scroll-y class="company-scroll">
						<view class="company-item acea-row row-middle row-between"
							v-for="c in deliveryPopup.companyList" :key="c.company_id"
							:class="{ active: Number(deliveryPopup.companyId) === Number(c.company_id) }"
							@click="chooseCompany(c)">
							<text>{{ c.company_name }}</text>
							<uv-icon v-if="Number(deliveryPopup.companyId) === Number(c.company_id)" name="checkmark"
								color="#F55606" size="16"></uv-icon>
						</view>
						<view class="company-empty" v-if="!deliveryPopup.companyList.length">暂无可用物流公司</view>
					</scroll-view>
				</view>
			</uv-popup>
		</uv-popup>

		<!-- 外卖开始配送弹层 -->
		<uv-popup v-model="riderPopup.show" mode="bottom" round="16">
			<view class="popup-body">
				<view class="popup-title">安排配送</view>
				<view class="form-item">
					<text class="form-label">配送员姓名<text class="required">*</text></text>
					<uv-input v-model="riderPopup.deliverer" placeholder="请输入配送员姓名" border="surround"
						:customStyle="inputStyle" clearable></uv-input>
				</view>
				<view class="form-item">
					<text class="form-label">联系电话<text class="required">*</text></text>
					<uv-input v-model="riderPopup.mobile" placeholder="请输入配送员联系电话" border="surround" type="number"
						:customStyle="inputStyle" clearable></uv-input>
				</view>
				<view class="popup-btns acea-row row-middle">
					<view class="btn-cancel" @click="riderPopup.show = false">取消</view>
					<view class="btn-confirm" @click="submitLocalDelivery">开始配送</view>
				</view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
	import {
		tradeOrderDetail,
		expressCompany,
		expressDelivery,
		localTake,
		localDelivery,
		localFinish
	} from "@/common/js/api.js"

	export default {
		data() {
			return {
				orderId: 0,
				info: {},
				inputStyle: "background:#F7F8FA;border-radius:12rpx;",
				deliveryPopup: {
					show: false,
					companyPicker: false,
					companyId: 0,
					companyName: '',
					companyList: [],
					deliveryNo: '',
					goodsList: []
				},
				riderPopup: {
					show: false,
					deliverer: '',
					mobile: ''
				}
			};
		},
		computed: {
			bizName() {
				return biz => {
					const map = {
						express: '快递邮寄',
						store: '到店核销',
						local: '外卖配送'
					};
					return map[biz] || '订单';
				};
			},
			// 到店核销类（自提订单）
			isStoreTrade() {
				return Number(this.info.order_type) === 2;
			},
			statusClass() {
				const s = Number(this.info.order_status);
				if (s === 4 || s === 10) return 'status-done';
				if (s === 0 || s === -1) return 'status-init';
				return 'status-doing';
			},
			deliverTitle() {
				if (this.isStoreTrade) return '核销信息';
				return Number(this.info.order_type) === 3 ? '配送信息' : '物流信息';
			},
			statusDesc() {
				const ot = Number(this.info.order_type || 1);
				const s = Number(this.info.order_status || 0);
				if (s === 0) return '等待买家付款';
				if (s === -1) return '订单已关闭';
				if (ot === 3) {
					if (s === 1) return '请尽快接单并安排备餐';
					if (s === 2) return '备餐完成后请安排配送员配送';
					if (s === 3) return '配送中，送达后请点击「确认送达」';
					return '订单已送达';
				}
				if (ot === 2) {
					if (s === 1 || s === 2) return '买家到店后请出示核销码完成核销';
					return '订单已核销完成';
				}
				if (s === 1 || s === 2) return '请尽快为买家安排发货';
				if (s === 3) return '已发货，等待买家收货';
				return '交易已完成';
			},
			localSteps() {
				return [{
					name: '接单',
					step: 1
				}, {
					name: '备餐中',
					step: 2
				}, {
					name: '配送中',
					step: 3
				}, {
					name: '已送达',
					step: 4
				}];
			},
			localStep() {
				const s = Number(this.info.order_status || 0);
				return s >= 1 && s <= 4 ? s : 0;
			}
		},
		onLoad(res) {
			this.orderId = res.order_id || 0;
		},
		onShow() {
			this.getInfo();
		},
		methods: {
			getInfo() {
				if (!this.orderId) return;
				tradeOrderDetail({
					order_id: this.orderId
				}).then(res => {
					if (res.code == 0) {
						this.info = res.data || {};
					} else {
						uni.showToast({
							title: res.message || '加载失败',
							icon: 'none'
						});
					}
				});
			},
			onAction(act) {
				if (act.key === 'express_delivery') {
					this.openDeliveryPopup();
				} else if (act.key === 'verify') {
					this.goVerify();
				} else if (act.key === 'local_take') {
					this.doLocalTake();
				} else if (act.key === 'local_delivery') {
					this.openRiderPopup();
				} else if (act.key === 'local_finish') {
					this.doLocalFinish();
				}
			},
			// 到店核销：复用商家端已有的扫码核销页面（该页按核销码查询，不能用订单号兜底）
			goVerify() {
				const code = this.info.delivery_code;
				if (!code) {
					uni.showToast({
						title: '该订单暂无核销码，请刷新后重试',
						icon: 'none'
					});
					return;
				}
				uni.navigateTo({
					url: '/otherpages/writeOff/writeOff?code=' + code
				});
			},
			resetDeliveryPopup() {
				this.deliveryPopup.companyPicker = false;
			},
			// ---------- 快递发货 ----------
			openDeliveryPopup() {
				this.deliveryPopup.deliveryNo = '';
				this.deliveryPopup.companyId = 0;
				this.deliveryPopup.companyName = '';
				this.deliveryPopup.goodsList = (this.info.goods_list || []).map(g => Object.assign({}, g, {
					checked: Number(g.delivery_status) === 0 && Number(g.refund_status) !== 3
				}));
				this.deliveryPopup.show = true;
				if (!this.deliveryPopup.companyList.length) {
					expressCompany({}).then(res => {
						if (res.code == 0) this.deliveryPopup.companyList = res.data || [];
					});
				}
			},
			toggleGoods(g) {
				if (Number(g.delivery_status) !== 0) return;
				if (Number(g.refund_status) === 3) return;
				g.checked = !g.checked;
			},
			chooseCompany(c) {
				this.deliveryPopup.companyId = c.company_id;
				this.deliveryPopup.companyName = c.company_name;
				this.deliveryPopup.companyPicker = false;
			},
			submitExpressDelivery() {
				const ids = this.deliveryPopup.goodsList.filter(g => g.checked).map(g => g.order_goods_id).join(',');
				if (!ids) {
					uni.showToast({
						title: '请选择要发货的商品',
						icon: 'none'
					});
					return;
				}
				if (!this.deliveryPopup.companyId) {
					uni.showToast({
						title: '请选择物流公司',
						icon: 'none'
					});
					return;
				}
				if (!this.deliveryPopup.deliveryNo.trim()) {
					uni.showToast({
						title: '请填写快递单号',
						icon: 'none'
					});
					return;
				}
				uni.showLoading({
					mask: true
				});
				expressDelivery({
					order_id: this.orderId,
					order_goods_ids: ids,
					express_company_id: this.deliveryPopup.companyId,
					delivery_no: this.deliveryPopup.deliveryNo.trim()
				}).then(res => {
					uni.hideLoading();
					uni.showToast({
						title: res.code == 0 ? '发货成功' : (res.message || '发货失败'),
						icon: res.code == 0 ? 'success' : 'none'
					});
					if (res.code == 0) {
						this.deliveryPopup.show = false;
						this.getInfo();
					}
				}).catch(() => uni.hideLoading());
			},
			// ---------- 外卖 ----------
			doLocalTake() {
				uni.showModal({
					title: '商家接单',
					content: '确认接下该外卖订单？接单后请尽快安排备餐与配送',
					success: r => {
						if (!r.confirm) return;
						uni.showLoading({
							mask: true
						});
						localTake({
							order_id: this.orderId
						}).then(res => {
							uni.hideLoading();
							uni.showToast({
								title: res.code == 0 ? '接单成功' : (res.message || '接单失败'),
								icon: res.code == 0 ? 'success' : 'none'
							});
							if (res.code == 0) this.getInfo();
						}).catch(() => uni.hideLoading());
					}
				});
			},
			openRiderPopup() {
				this.riderPopup.deliverer = '';
				this.riderPopup.mobile = '';
				this.riderPopup.show = true;
			},
			submitLocalDelivery() {
				if (!this.riderPopup.deliverer.trim()) {
					uni.showToast({
						title: '请填写配送员姓名',
						icon: 'none'
					});
					return;
				}
				if (!/^1\d{10}$/.test(this.riderPopup.mobile.trim())) {
					uni.showToast({
						title: '请填写正确的配送员手机号',
						icon: 'none'
					});
					return;
				}
				uni.showLoading({
					mask: true
				});
				localDelivery({
					order_id: this.orderId,
					deliverer: this.riderPopup.deliverer.trim(),
					deliverer_mobile: this.riderPopup.mobile.trim()
				}).then(res => {
					uni.hideLoading();
					uni.showToast({
						title: res.code == 0 ? '已开始配送' : (res.message || '操作失败'),
						icon: res.code == 0 ? 'success' : 'none'
					});
					if (res.code == 0) {
						this.riderPopup.show = false;
						this.getInfo();
					}
				}).catch(() => uni.hideLoading());
			},
			doLocalFinish() {
				uni.showModal({
					title: '确认送达',
					content: '确认该订单已送达顾客？确认后订单进入已送达状态',
					success: r => {
						if (!r.confirm) return;
						uni.showLoading({
							mask: true
						});
						localFinish({
							order_id: this.orderId
						}).then(res => {
							uni.hideLoading();
							uni.showToast({
								title: res.code == 0 ? '已确认送达' : (res.message || '操作失败'),
								icon: res.code == 0 ? 'success' : 'none'
							});
							if (res.code == 0) this.getInfo();
						}).catch(() => uni.hideLoading());
					}
				});
			}
		}
	};
</script>

<style lang="scss">
	.page {
		min-height: 100vh;
		background: #F5F5F5;
		padding: 20rpx 24rpx;
	}

	.status-card {
		background: #fff;
		border-radius: 24rpx;
		padding: 32rpx;

		.biz-tag {
			font-size: 22rpx;
			padding: 4rpx 14rpx;
			border-radius: 8rpx;
			margin-right: 12rpx;
			background: #F0F5FF;
			color: #4A6B8A;

			&.biz-store {
				background: #FFF7EE;
				color: #FE780F;
			}

			&.biz-local {
				background: #FFF0E5;
				color: #F55606;
			}
		}

		.status-text {
			font-size: 36rpx;
			font-weight: 600;
		}

		.status-desc {
			margin-top: 14rpx;
			font-size: 24rpx;
			color: #999;
		}

		&.status-doing .status-text {
			color: #FE780F;
		}

		&.status-done .status-text {
			color: #25B85B;
		}
	}

	/* 外卖进度 */
	.local-progress {
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin-top: 32rpx;

		.lp-item {
			position: relative;
			flex: 1;
			display: flex;
			flex-direction: column;
			align-items: center;

			&:not(:last-child)::after {
				content: '';
				position: absolute;
				top: 12rpx;
				left: calc(50% + 14rpx);
				right: calc(-50% + 14rpx);
				height: 3rpx;
				background: #E5E5E5;
			}

			.lp-dot {
				width: 24rpx;
				height: 24rpx;
				border-radius: 50%;
				background: #E5E5E5;
			}

			.lp-name {
				margin-top: 10rpx;
				font-size: 22rpx;
				color: #999;
			}

			&.active {
				.lp-dot {
					background: #FE780F;
				}

				.lp-name {
					color: #FE780F;
					font-weight: 600;
				}

				&:not(:last-child)::after {
					background: #FFD9B3;
				}
			}

			&.done:not(:last-child)::after {
				background: #FE780F;
			}
		}
	}

	.card {
		background: #fff;
		border-radius: 24rpx;
		padding: 28rpx;
		margin-top: 20rpx;

		.card-title {
			font-size: 30rpx;
			font-weight: 600;
			color: #222;
			margin-bottom: 16rpx;
		}

		.row {
			padding: 12rpx 0;
			font-size: 26rpx;

			.label {
				flex-shrink: 0;
				width: 160rpx;
				color: #999;
			}

			.value {
				flex: 1;
				text-align: right;
				color: #333;
			}

			.multi {
				text-align: right;
				line-height: 1.5;
			}

			.code {
				color: #F55606;
				font-weight: 600;
				letter-spacing: 1rpx;
			}

			&.total {
				margin-top: 12rpx;
				padding-top: 20rpx;
				border-top: 1rpx solid #F0F0F0;

				.pay {
					font-size: 34rpx;
					font-weight: 600;
					color: #F51D1D;
				}
			}
		}

		.widths {
			width: calc(100% - 140rpx - 20rpx);
		}

		.price {
			color: #F51D1D;
		}

		.nums {
			color: #999;
			font-size: 24rpx;
		}

		.g-tag {
			margin-top: 8rpx;
			font-size: 22rpx;
			color: #F55606;
		}
	}

	.h-160 {
		height: 160rpx;
	}

	.btms {
		position: fixed;
		width: 100vw;
		background: #fff;
		bottom: 0;
		z-index: 909;
		left: 0;
		padding: 20rpx 32rpx;

		>view {
			>view {
				width: 192rpx;
				height: 96rpx;
				border-radius: 48rpx;
				text-align: center;
				line-height: 96rpx;
				box-sizing: border-box;
				font-weight: 600;
				font-size: 32rpx;
				margin-left: 16rpx;
			}

			.btn1 {
				background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
				color: #fff;
			}
		}
	}

	/* 弹层公共样式 */
	.popup-body {
		padding: 32rpx;

		.popup-title {
			font-size: 32rpx;
			font-weight: 600;
			color: #222;
			text-align: center;
			padding-bottom: 24rpx;
		}

		.form-item {
			margin-bottom: 24rpx;

			.form-label {
				display: block;
				font-size: 26rpx;
				color: #333;
				margin-bottom: 12rpx;

				.required {
					color: #F51D1D;
					margin-left: 4rpx;
				}
			}

			.select-box {
				height: 72rpx;
				background: #F7F8FA;
				border-radius: 12rpx;
				padding: 0 24rpx;
				display: flex;
				align-items: center;
				justify-content: space-between;
				font-size: 28rpx;
				color: #333;

				.placeholder {
					color: #999;
				}
			}

			.goods-check {
				max-height: 360rpx;
				overflow-y: auto;

				.check-item {
					padding: 16rpx 0;
					border-bottom: 1rpx solid #F5F5F5;

					.check-box {
						width: 36rpx;
						height: 36rpx;
						border-radius: 50%;
						border: 2rpx solid #CCCCCC;
						display: flex;
						align-items: center;
						justify-content: center;
						box-sizing: border-box;

						&.checked {
							background: #F55606;
							border-color: #F55606;
						}

						&.disabled {
							background: #E5E5E5;
							border-color: #E5E5E5;
							opacity: 0.5;
						}
					}

					.check-name {
						font-size: 26rpx;
						color: #333;
					}

					.nums {
						font-size: 24rpx;
						color: #999;
					}
				}
			}
		}

		.popup-btns {
			margin-top: 32rpx;

			>view {
				flex: 1;
				height: 80rpx;
				line-height: 80rpx;
				text-align: center;
				border-radius: 40rpx;
				font-size: 30rpx;
			}

			.btn-cancel {
				background: #F5F5F5;
				color: #666;
				margin-right: 20rpx;
			}

			.btn-confirm {
				background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
				color: #fff;
			}
		}

		.company-scroll {
			max-height: 600rpx;

			.company-item {
				padding: 26rpx 8rpx;
				border-bottom: 1rpx solid #F5F5F5;
				font-size: 28rpx;
				color: #333;

				&.active {
					color: #F55606;
					font-weight: 600;
				}
			}

			.company-empty {
				text-align: center;
				padding: 60rpx 0;
				color: #999;
				font-size: 26rpx;
			}
		}
	}
</style>
