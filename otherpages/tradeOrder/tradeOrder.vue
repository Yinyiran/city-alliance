<template>
	<view class="page">
		<!-- 业务分组：按实际配送链路区分 -->
		<view class="biz-tabs">
			<uv-tabs :list="bizTabs" :current="bizIndex" lineWidth="30" itemStyle="height: 44px;padding:0 18rpx;"
				lineColor="linear-gradient( 90deg, #F5AB18 0%, #F55606 100%)" :activeStyle="{fontWeight:'bold'}"
				:inactiveStyle="{color:'#666',fontSize:'28rpx'}" @click="onBizChange"></uv-tabs>
		</view>

		<!-- 处理阶段：待处理优先 -->
		<view class="stage-tabs acea-row row-middle">
			<view v-for="(s, i) in stageTabs" :key="s.key" class="stage-item"
				:class="{ active: stageIndex === i }" @click="onStageChange(i)">
				<text>{{ s.name }}</text>
				<text class="badge" v-if="stageCount[s.key] > 0">{{ stageCount[s.key] }}</text>
			</view>
		</view>

		<!-- 搜索 -->
		<view class="search-wrap">
			<uv-input v-model="searchtxt" placeholder="订单号 / 收货人 / 商品名称" border="none" shape="circle"
				:customStyle="searchStyle" @confirm="onSearch" clearable>
				<template v-slot:prefix>
					<uv-icon name="search" color="#999" size="16"></uv-icon>
				</template>
			</uv-input>
		</view>

		<!-- 订单列表 -->
		<view class="container">
			<view v-for="(item, index) in list" :key="item.order_id" class="items">
				<!-- 订单头 -->
				<view class="order-head acea-row row-middle row-between" @click="goDetail(item.order_id)">
					<view class="acea-row row-middle">
						<text class="biz-tag" :class="'biz-' + item.biz_type">{{ bizName(item.biz_type) }}</text>
						<text class="biz-tag biz-trade" v-if="item.trade_type_name">{{ item.trade_type_name }}</text>
						<text class="order-no">No.{{ item.order_no }}</text>
					</view>
					<text class="status" :class="{ 'status-done': isFinished(item) }">
						{{ item.trade_status_name }}
					</text>
				</view>

				<!-- 交付信息 -->
				<view class="deliver-info" @click="goDetail(item.order_id)">
					<!-- 到店核销：门店 + 核销码 -->
					<template v-if="Number(item.order_type) === 2">
						<view class="info-row acea-row row-middle">
							<text class="label">核销门店</text>
							<text class="value">{{ item.delivery_store_name || '门店自提' }}</text>
						</view>
						<view class="info-row acea-row row-middle" v-if="item.delivery_code">
							<text class="label">核销码</text>
							<text class="value code">{{ item.delivery_code }}</text>
						</view>
					</template>
					<!-- 快递邮寄 / 外卖：收货地址 -->
					<template v-else>
						<view class="info-row acea-row row-middle">
							<text class="label">{{ Number(item.order_type) === 3 ? '配送地址' : '收货地址' }}</text>
							<text class="value ellipsis">{{ item.name }} {{ item.mobile }}　{{ item.full_address }}{{ item.address }}</text>
						</view>
						<view class="info-row acea-row row-middle" v-if="item.express_no">
							<text class="label">快递单号</text>
							<text class="value code">{{ item.express_no }}</text>
						</view>
					</template>
					<view class="info-row acea-row row-middle">
						<text class="label">下单时间</text>
						<text class="value">{{ item.create_time_text }}</text>
					</view>
					<view class="info-row acea-row row-middle" v-if="item.buyer_message">
						<text class="label">买家留言</text>
						<text class="value ellipsis">{{ item.buyer_message }}</text>
					</view>
				</view>

				<!-- 商品 -->
				<view class="goods-list" @click="goDetail(item.order_id)">
					<view class="acea-row row-middle m-t-20" v-for="(g, gi) in item.goods_list" :key="g.order_goods_id">
						<image :src="$util.img(g.sku_image)" class="w-140 h-140 bor-16 m-r-20" mode=""></image>
						<view class="widths h-140 acea-row row-column row-between" style="flex-wrap: nowrap;">
							<view class="text-more-2 font-28">{{ g.sku_name }}</view>
							<view class="acea-row row-middle row-between">
								<view class="price">
									￥<text class="font-30 font-weight-6">{{ g.price }}</text>
								</view>
								<view class="nums">x{{ g.num }}</view>
							</view>
						</view>
					</view>
				</view>

				<!-- 金额与操作 -->
				<view class="foot acea-row row-middle row-between">
					<view class="amount">
						<text class="font-24" style="color:#999;">实付</text>
						<text class="pay">￥{{ item.pay_money }}</text>
					</view>
					<view class="btns acea-row row-middle">
						<view v-for="act in item.actions" :key="act.key" class="btn2"
							@click.stop="onAction(act, item)">{{ act.title }}</view>
					</view>
				</view>
			</view>
		</view>

		<uv-empty v-if="!loading && !list.length" mode="order" text="暂无相关订单"></uv-empty>
		<uv-load-more v-if="list.length" :status="loadStatus"></uv-load-more>

		<!-- 快递发货弹层 -->
		<uv-popup v-model="deliveryPopup.show" mode="bottom" round="16" @close="resetDeliveryPopup">
			<view class="popup-body">
				<view class="popup-title">快递发货</view>
				<view class="form-item">
					<text class="form-label">物流公司<text class="required">*</text></text>
					<view class="select-box" @click="deliveryPopup.companyList.length && (deliveryPopup.companyPicker = true)">
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
			<!-- 物流公司选择 -->
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
					<uv-input v-model="riderPopup.mobile" placeholder="请输入配送员联系电话" border="surround"
						type="number" :customStyle="inputStyle" clearable></uv-input>
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
		tradeOrderList,
		expressCompany,
		expressDelivery,
		localTake,
		localDelivery,
		localFinish
	} from "@/common/js/api.js"

	export default {
		data() {
			return {
				bizTabs: [{
					name: '全部'
				}, {
					name: '快递邮寄'
				}, {
					name: '到店核销'
				}, {
					name: '外卖配送'
				}],
				// 与后端 TradeOrder 分组常量保持一致
				bizKeys: ['all', 'express', 'store', 'local'],
				bizIndex: 0,
				stageTabs: [{
					key: 'pending',
					name: '待处理'
				}, {
					key: 'doing',
					name: '进行中'
				}, {
					key: 'finished',
					name: '已完成'
				}, {
					key: 'all',
					name: '全部'
				}],
				stageIndex: 0,
				stageCount: {},
				searchtxt: '',
				searchStyle: "background:#F5F5F5;height:64rpx;padding:0 24rpx;",
				inputStyle: "background:#F7F8FA;border-radius:12rpx;",
				list: [],
				page: 1,
				lastPage: 1,
				loading: false,
				loadStatus: 'loadmore',
				deliveryPopup: {
					show: false,
					companyPicker: false,
					orderId: 0,
					companyId: 0,
					companyName: '',
					companyList: [],
					deliveryNo: '',
					goodsList: []
				},
				riderPopup: {
					show: false,
					orderId: 0,
					deliverer: '',
					mobile: ''
				}
			};
		},
		onLoad(res) {
			// 支持从首页按分组直达，如 ?biz=local&stage=pending
			if (res.biz) {
				const idx = this.bizKeys.indexOf(res.biz);
				if (idx > -1) this.bizIndex = idx;
			}
			if (res.stage) {
				const idx = this.stageTabs.findIndex(s => s.key === res.stage);
				if (idx > -1) this.stageIndex = idx;
			}
		},
		onShow() {
			this.page = 1;
			this.getlist();
		},
		onPullDownRefresh() {
			this.page = 1;
			this.getlist(() => uni.stopPullDownRefresh());
		},
		onReachBottom() {
			if (this.lastPage > this.page) {
				this.page++;
				this.getlist();
			}
		},
		methods: {
			//订单是否已结束（用于列表置灰）。
			//order_status=10 恒为已完成；=4 时外卖配送(order_type=3)表示「已送达」仍在进行中，
			//只有快递(1)与到店核销(2)的 4 才表示已收货完成。
			//原判断写作 order_status == 4 || == 10，会把外卖已送达单误显示为已完成。
			isFinished(item) {
				const ot = Number(item.order_type || 1);
				const os = Number(item.order_status);
				if (os === 10) return true;
				if (os === 4 && ot !== 3) return true;
				return false;
			},
			bizName(biz) {
				const map = {
					express: '快递邮寄',
					store: '到店核销',
					local: '外卖配送'
				};
				return map[biz] || '订单';
			},
			onBizChange(e) {
				this.bizIndex = e.index;
				this.refresh();
			},
			onStageChange(i) {
				this.stageIndex = i;
				this.refresh();
			},
			onSearch() {
				this.refresh();
			},
			refresh() {
				this.page = 1;
				this.list = [];
				this.getlist();
			},
			getlist(cb) {
				this.loading = true;
				this.loadStatus = 'loading';
				const params = {
					page: this.page,
					biz_type: this.bizKeys[this.bizIndex],
					stage: this.stageTabs[this.stageIndex].key,
					searchtxt: this.searchtxt
				};
				tradeOrderList(params).then(res => {
					this.loading = false;
					if (res.code != 0) {
						uni.showToast({
							title: res.message || '加载失败',
							icon: 'none'
						});
						this.loadStatus = 'error';
						if (cb) cb();
						return;
					}
					const data = res.data || {};
					const rows = data.list || [];
					this.list = this.page > 1 ? this.list.concat(rows) : rows;
					this.lastPage = data.page_count || 1;
					this.stageCount = data.stage_count || {};
					this.loadStatus = this.page >= this.lastPage ? 'nomore' : 'loadmore';
					if (cb) cb();
				}).catch(() => {
					this.loading = false;
					this.loadStatus = 'error';
					if (cb) cb();
				});
			},
			goDetail(orderId) {
				this.$util.redirectTo('/otherpages/tradeOrderDetail/tradeOrderDetail', {
					order_id: orderId
				});
			},
			// 按后端下发的 actions 分发操作
			onAction(act, order) {
				if (act.key === 'express_delivery') {
					this.openDeliveryPopup(order);
				} else if (act.key === 'verify') {
					this.goVerify(order);
				} else if (act.key === 'local_take') {
					this.doLocalTake(order);
				} else if (act.key === 'local_delivery') {
					this.openRiderPopup(order);
				} else if (act.key === 'local_finish') {
					this.doLocalFinish(order);
				}
			},
			// 到店核销：复用商家端已有的扫码核销页面（含动态码校验与完整权限判断）
			// 核销页按核销码查询，不能用订单号兜底，否则必然查询失败
			goVerify(order) {
				const code = order.delivery_code;
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
			// ---------- 快递发货 ----------
			openDeliveryPopup(order) {
				this.deliveryPopup.orderId = order.order_id;
				this.deliveryPopup.deliveryNo = '';
				this.deliveryPopup.companyId = 0;
				this.deliveryPopup.companyName = '';
				// 仅未发货商品可勾选，默认全选
				this.deliveryPopup.goodsList = (order.goods_list || []).map(g => Object.assign({}, g, {
					checked: Number(g.delivery_status) === 0 && Number(g.refund_status) !== 3
				}));
				this.deliveryPopup.show = true;
				if (!this.deliveryPopup.companyList.length) {
					expressCompany({}).then(res => {
						if (res.code == 0) {
							this.deliveryPopup.companyList = res.data || [];
						}
					});
				}
			},
			resetDeliveryPopup() {
				this.deliveryPopup.companyPicker = false;
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
				const ids = this.deliveryPopup.goodsList
					.filter(g => g.checked)
					.map(g => g.order_goods_id)
					.join(',');
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
					order_id: this.deliveryPopup.orderId,
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
						this.refresh();
					}
				}).catch(() => {
					uni.hideLoading();
				});
			},
			// ---------- 外卖接单 ----------
			doLocalTake(order) {
				uni.showModal({
					title: '商家接单',
					content: '确认接下该外卖订单？接单后请尽快安排备餐与配送',
					success: r => {
						if (!r.confirm) return;
						uni.showLoading({
							mask: true
						});
						localTake({
							order_id: order.order_id
						}).then(res => {
							uni.hideLoading();
							uni.showToast({
								title: res.code == 0 ? '接单成功' : (res.message || '接单失败'),
								icon: res.code == 0 ? 'success' : 'none'
							});
							if (res.code == 0) this.refresh();
						}).catch(() => uni.hideLoading());
					}
				});
			},
			// ---------- 外卖开始配送 ----------
			openRiderPopup(order) {
				this.riderPopup.orderId = order.order_id;
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
					order_id: this.riderPopup.orderId,
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
						this.refresh();
					}
				}).catch(() => uni.hideLoading());
			},
			// ---------- 外卖确认送达 ----------
			doLocalFinish(order) {
				uni.showModal({
					title: '确认送达',
					content: '确认该订单已送达顾客？确认后订单进入已送达状态',
					success: r => {
						if (!r.confirm) return;
						uni.showLoading({
							mask: true
						});
						localFinish({
							order_id: order.order_id
						}).then(res => {
							uni.hideLoading();
							uni.showToast({
								title: res.code == 0 ? '已确认送达' : (res.message || '操作失败'),
								icon: res.code == 0 ? 'success' : 'none'
							});
							if (res.code == 0) this.refresh();
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
		padding-bottom: 40rpx;
	}

	.biz-tabs {
		background: #fff;
	}

	.stage-tabs {
		background: #fff;
		padding: 8rpx 24rpx 20rpx;

		.stage-item {
			position: relative;
			margin-right: 20rpx;
			padding: 10rpx 24rpx;
			background: #F5F5F5;
			border-radius: 28rpx;
			font-size: 26rpx;
			color: #666;

			&.active {
				background: #FFF0E5;
				color: #F55606;
				font-weight: 600;
			}

			.badge {
				margin-left: 8rpx;
				font-size: 20rpx;
				color: #fff;
				background: #F55606;
				border-radius: 16rpx;
				padding: 0 8rpx;
			}
		}
	}

	.search-wrap {
		background: #fff;
		padding: 0 24rpx 20rpx;
	}

	.container {
		padding: 20rpx 24rpx;

		.items {
			background: #fff;
			border-radius: 24rpx;
			padding: 28rpx;
			margin-bottom: 20rpx;

			.order-head {
				padding-bottom: 20rpx;
				border-bottom: 1rpx solid #F0F0F0;

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

					/* 交易类型标签：与配送链路标签区分，避免「到店核销」掩盖具体交易类型 */
					&.biz-trade {
						background: #F0F9F0;
						color: #2E8B57;
					}


					&.biz-local {
						background: #FFF0E5;
						color: #F55606;
					}
				}

				.order-no {
					font-size: 24rpx;
					color: #999;
				}

				.status {
					font-size: 26rpx;
					color: #FE780F;
					font-weight: 600;

					&.status-done {
						color: #999;
					}
				}
			}

			.deliver-info {
				padding: 16rpx 0 4rpx;

				.info-row {
					padding: 6rpx 0;
					font-size: 24rpx;

					.label {
						flex-shrink: 0;
						width: 130rpx;
						color: #999;
					}

					.value {
						flex: 1;
						color: #333;
					}

					.code {
						color: #F55606;
						font-weight: 600;
						letter-spacing: 1rpx;
					}

					.ellipsis {
						overflow: hidden;
						text-overflow: ellipsis;
						white-space: nowrap;
					}
				}
			}

			.goods-list {
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
			}

			.foot {
				margin-top: 24rpx;
				padding-top: 24rpx;
				border-top: 1rpx solid #F0F0F0;

				.amount {
					.pay {
						margin-left: 8rpx;
						font-size: 32rpx;
						font-weight: 600;
						color: #F51D1D;
					}
				}

				.btns {
					>view {
						margin-left: 16rpx;
						width: 168rpx;
						height: 64rpx;
						text-align: center;
						line-height: 64rpx;
						border-radius: 32rpx;
						font-size: 26rpx;
						box-sizing: border-box;
					}

					.btn2 {
						background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
						color: #fff;
					}
				}
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
