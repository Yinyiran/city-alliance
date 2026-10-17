<template>
	<view class="quick-pay-page">
		<nvabar type="1" title="向商家付款" titleStyle="color:#222"></nvabar>

		<view class="top-nav-action acea-row row-middle row-between">
			<view class="safe-tag acea-row row-middle">
				<uv-icon name="shield-check" color="#07c160" size="14"></uv-icon>
				<text class="m-l-8">微信支付官方安全保障</text>
			</view>
			<view class="record-btn acea-row row-middle" @click="goToRecords">
				<uv-icon name="order" color="#F55606" size="14"></uv-icon>
				<text class="m-l-6 font-24 font-weight-6" style="color: #F55606;">付款明细</text>
			</view>
		</view>

		<view class="main-card">
			<!-- 商家信息卡片 -->
			<view class="shop-box acea-row row-middle row-between" @click="openStoreSelect">
				<view class="shop-left acea-row row-middle" v-if="selectedShop && selectedShop.site_id">
					<image :src="$util.img(selectedShop.logo || selectedShop.avatar)" class="shop-logo" mode="aspectFill"></image>
					<view class="m-l-20">
						<view class="shop-name font-32 font-weight-6 text-more">{{ selectedShop.site_name }}</view>
						<view class="shop-sub font-22 text-more" v-if="selectedShop.address">{{ selectedShop.address }}</view>
						<view class="shop-sub font-22" v-else>到店买单 · 直接付款</view>
					</view>
				</view>
				<view class="shop-left acea-row row-middle" v-else>
					<view class="shop-logo empty-logo acea-row row-middle row-center">
						<uv-icon name="home" color="#999" size="24"></uv-icon>
					</view>
					<view class="m-l-20">
						<view class="shop-name font-32 font-weight-6" style="color: #999;">请选择收款商家</view>
						<view class="shop-sub font-22">点击选择您要付款的店铺</view>
					</view>
				</view>
				<view class="switch-btn acea-row row-middle">
					<text class="font-24">{{ selectedShop && selectedShop.site_id ? '切换' : '选择' }}</text>
					<uv-icon name="arrow-right" size="12" color="#999"></uv-icon>
				</view>
			</view>

			<view class="divider"></view>

			<!-- 金额输入区域 -->
			<view class="amount-section">
				<view class="font-26 font-weight-6 amount-title">付款金额</view>
				<view class="amount-input-box acea-row row-middle">
					<text class="currency">￥</text>
					<input
						type="digit"
						class="amount-input"
						placeholder="0.00"
						v-model="amount"
						:focus="autoFocus"
						@input="onAmountInput"
						maxlength="9"
					/>
					<view class="clear-icon" v-if="amount" @click="amount = ''">
						<uv-icon name="close-circle-fill" color="#c0c4cc" size="20"></uv-icon>
					</view>
				</view>

				<!-- 快捷金额选择 -->
				<view class="quick-amounts acea-row row-middle">
					<view
						class="quick-tag"
						:class="{ active: amount == item }"
						v-for="(item, idx) in quickList"
						:key="idx"
						@click="selectQuick(item)"
					>
						￥{{ item }}
					</view>
				</view>
			</view>

			<!-- 备注输入 -->
			<view class="remark-box m-t-32">
				<input
					type="text"
					class="remark-input"
					v-model="remark"
					placeholder="添加付款备注（选填，如桌号/包厢）"
					maxlength="50"
				/>
			</view>

			<!-- 支付按钮 -->
			<view class="pay-btn-wrap m-t-40">
				<button
					class="submit-pay-btn"
					:class="{ disabled: !canSubmit || paying }"
					:loading="paying"
					@click="handlePay"
				>
					{{ paying ? '正在调起微信支付...' : (amount ? `确认付款 ￥${Number(amount).toFixed(2)}` : '请输入金额付款') }}
				</button>
			</view>

			<view class="bottom-tips font-22 acea-row row-middle row-center">
				<uv-icon name="lock-fill" size="12" color="#aaa"></uv-icon>
				<text class="m-l-6">付款资金直接结算至商家账户</text>
			</view>
		</view>

		<!-- 商家选择抽屉弹窗 -->
		<uv-popup ref="shopPicker" mode="bottom" round="24" :closeable="true">
			<view class="picker-content">
				<view class="picker-title font-32 font-weight-6">选择收款商家</view>
				<view class="picker-search m-t-20">
					<uv-input
						placeholder="搜索店铺名称"
						v-model="searchKeyword"
						prefixIcon="search"
						prefixIconStyle="color: #999"
						shape="circle"
						@input="filterShops"
					></uv-input>
				</view>
				<scroll-view scroll-y="true" class="shop-list-scroll m-t-20">
					<view
						class="picker-item acea-row row-middle row-between"
						v-for="(s, idx) in filteredShopList"
						:key="idx"
						@click="chooseShop(s)"
					>
						<view class="acea-row row-middle shop-item-left">
							<image :src="$util.img(s.logo || s.avatar)" class="picker-logo" mode="aspectFill"></image>
							<view class="m-l-16">
								<view class="font-28 font-weight-6 text-more w-380">{{ s.site_name }}</view>
								<view class="font-22 text-more w-380" style="color: #999;" v-if="s.address">{{ s.address }}</view>
							</view>
						</view>
						<view class="check-box" v-if="selectedShop && selectedShop.site_id == s.site_id">
							<uv-icon name="checkbox-mark" color="#F55606" size="20"></uv-icon>
						</view>
					</view>
					<view v-if="!filteredShopList.length" class="p-40 text-center font-26" style="color: #999;">
						未找到相关店铺
					</view>
				</scroll-view>
			</view>
		</uv-popup>

		<!-- 支付成功弹窗 -->
		<uv-popup ref="successPopup" mode="center" round="24" :closeOnClickOverlay="false">
			<view class="success-box">
				<image src="/static/icon1.png" class="success-icon" mode=""></image>
				<view class="font-36 font-weight-6 m-t-24" style="color: #222;">付款成功</view>
				<view class="font-56 font-weight-6 m-t-16" style="color: #F51D1D;">
					￥{{ Number(lastPayResult.pay_money || 0).toFixed(2) }}
				</view>
				<view class="detail-rows m-t-32">
					<view class="row-item acea-row row-between font-26">
						<text style="color: #999;">收款商家</text>
						<text class="font-weight-6 text-more w-320">{{ lastPayResult.site_name }}</text>
					</view>
					<view class="row-item acea-row row-between font-26 m-t-16">
						<text style="color: #999;">付款时间</text>
						<text>{{ lastPayResult.pay_time || '--' }}</text>
					</view>
					<view class="row-item acea-row row-between font-26 m-t-16" v-if="lastPayResult.order_no">
						<text style="color: #999;">订单单号</text>
						<text class="font-22">{{ lastPayResult.order_no }}</text>
					</view>
					<view class="row-item acea-row row-between font-26 m-t-16" v-if="lastPayResult.remark">
						<text style="color: #999;">付款备注</text>
						<text class="text-more w-320">{{ lastPayResult.remark }}</text>
					</view>
				</view>
				<view class="success-actions m-t-40">
					<button class="view-btn" @click="goToRecords">查看付款明细</button>
					<button class="done-btn m-t-20" @click="finishPay">完成</button>
				</view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
import { membershoppages, websiteStore, quickPayCreate, huifupay } from '@/common/js/api.js';

export default {
	data() {
		return {
			site_id: 0,
			selectedShop: null,
			amount: '',
			remark: '',
			paying: false,
			autoFocus: true,
			quickList: [10, 20, 50, 100, 200, 500],
			shopList: [],
			filteredShopList: [],
			searchKeyword: '',
			lastPayResult: {
				site_name: '',
				pay_money: 0,
				order_no: '',
				pay_time: '',
				remark: ''
			}
		};
	},
	computed: {
		canSubmit() {
			return this.selectedShop && this.selectedShop.site_id && Number(this.amount) > 0;
		}
	},
	onLoad(options) {
		if (options && options.site_id) {
			this.site_id = Number(options.site_id);
		}
		this.initShops();
	},
	methods: {
		initShops() {
			// 先加载关注店铺列表
			membershoppages({ page: 1, page_size: 50 }).then(res => {
				let list = (res.data && res.data.list) || [];
				this.shopList = list;
				this.filteredShopList = list;

				if (this.site_id) {
					let found = list.find(s => Number(s.site_id) === this.site_id);
					if (found) {
						this.selectedShop = found;
						return;
					}
				}

				// 如果有关注店铺，默认选中第一个
				if (list.length > 0 && !this.selectedShop) {
					this.selectedShop = list[0];
				}

				// 如果没有关注店铺或没匹配到，补充从全部入驻商家拉取备选
				if (!this.selectedShop) {
					websiteStore({ page: 1, page_size: 30 }).then(res2 => {
						let allStores = (res2.data && res2.data.list) || [];
						if (allStores.length > 0) {
							this.shopList = this.shopList.concat(allStores);
							this.filteredShopList = this.shopList;
							if (this.site_id) {
								let found = allStores.find(s => Number(s.site_id) === this.site_id);
								if (found) {
									this.selectedShop = found;
									return;
								}
							}
							if (!this.selectedShop) {
								this.selectedShop = allStores[0];
							}
						}
					});
				}
			});
		},
		onAmountInput(e) {
			let val = e.detail.value;
			// 限制只能输入两位小数
			if (val.indexOf('.') !== -1) {
				let parts = val.split('.');
				if (parts[1].length > 2) {
					this.$nextTick(() => {
						this.amount = parts[0] + '.' + parts[1].slice(0, 2);
					});
				}
			}
		},
		selectQuick(val) {
			this.amount = String(val);
		},
		openStoreSelect() {
			this.searchKeyword = '';
			this.filteredShopList = this.shopList;
			this.$refs.shopPicker.open();
		},
		filterShops(val) {
			let k = (val || '').trim().toLowerCase();
			if (!k) {
				this.filteredShopList = this.shopList;
			} else {
				this.filteredShopList = this.shopList.filter(s => (s.site_name || '').toLowerCase().includes(k));
			}
		},
		chooseShop(shop) {
			this.selectedShop = shop;
			this.site_id = Number(shop.site_id);
			this.$refs.shopPicker.close();
		},
		goToRecords() {
			if (this.$refs.successPopup) {
				this.$refs.successPopup.close();
			}
			uni.navigateTo({
				url: '/otherpages/quickPayList/quickPayList'
			});
		},
		finishPay() {
			this.$refs.successPopup.close();
			uni.navigateBack();
		},
		handlePay() {
			if (!this.selectedShop || !this.selectedShop.site_id) {
				uni.showToast({ title: '请先选择收款商家', icon: 'none' });
				return;
			}
			const amt = parseFloat(this.amount);
			if (isNaN(amt) || amt <= 0) {
				uni.showToast({ title: '请输入有效的付款金额', icon: 'none' });
				return;
			}

			const token = uni.getStorageSync('token');
			if (!token) {
				uni.showToast({ title: '请先登录', icon: 'none' });
				return;
			}

			this.paying = true;
			uni.showLoading({ title: '订单创建中...', mask: true });

			const params = {
				site_id: this.selectedShop.site_id,
				amount: amt.toFixed(2),
				remark: this.remark || ''
			};

			quickPayCreate(params)
				.then(res => {
					if (res.code === 0 && res.data && res.data.out_trade_no) {
						const outTradeNo = res.data.out_trade_no;
						const orderNo = res.data.order_no || '';
						// 调用汇付支付获取微信支付参数
						huifupay({ out_trade_no: outTradeNo })
							.then(resA => {
								uni.hideLoading();
								if (resA.code === 0 && resA.data) {
									// 调用微信小程序原生支付
									uni.requestPayment({
										provider: 'wxpay',
										timeStamp: String(resA.data.timeStamp),
										nonceStr: resA.data.nonceStr,
										package: resA.data.package,
										signType: resA.data.signType,
										paySign: resA.data.paySign,
										success: () => {
											this.paying = false;
											this.lastPayResult = {
												site_name: this.selectedShop.site_name,
												pay_money: amt,
												order_no: orderNo,
												pay_time: this.$util.timeStampTurnTime(Math.floor(Date.now() / 1000)),
												remark: this.remark
											};
											this.$refs.successPopup.open();
										},
										fail: err => {
											this.paying = false;
											if (err && err.errMsg && err.errMsg.indexOf('cancel') !== -1) {
												uni.showToast({ title: '支付已取消', icon: 'none' });
											} else {
												uni.showToast({ title: (err && err.errMsg) || '支付未完成', icon: 'none' });
											}
										}
									});
								} else {
									this.paying = false;
									uni.showToast({ title: resA.message || '调起支付失败', icon: 'none' });
								}
							})
							.catch(() => {
								uni.hideLoading();
								this.paying = false;
								uni.showToast({ title: '调起支付失败', icon: 'none' });
							});
					} else {
						uni.hideLoading();
						this.paying = false;
						uni.showToast({ title: res.message || '创建订单失败', icon: 'none' });
					}
				})
				.catch(() => {
					uni.hideLoading();
					this.paying = false;
					uni.showToast({ title: '下单异常，请重试', icon: 'none' });
				});
		}
	}
};
</script>

<style lang="scss">
.quick-pay-page {
	min-height: 100vh;
	background: #f7f8fa;
	padding-bottom: 60rpx;
}

.top-nav-action {
	padding: 24rpx 32rpx 12rpx 32rpx;

	.safe-tag {
		font-size: 22rpx;
		color: #555;
	}

	.record-btn {
		background: #FFF0E6;
		padding: 10rpx 20rpx;
		border-radius: 24rpx;
	}
}

.main-card {
	margin: 20rpx 32rpx;
	background: #ffffff;
	border-radius: 24rpx;
	padding: 32rpx;
	box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.04);
}

.shop-box {
	padding: 8rpx 0;

	.shop-logo {
		width: 88rpx;
		height: 88rpx;
		border-radius: 16rpx;
		background: #f0f0f0;
	}

	.empty-logo {
		background: #f5f5f5;
	}

	.shop-name {
		color: #222;
		max-width: 440rpx;
	}

	.shop-sub {
		color: #888;
		margin-top: 6rpx;
		max-width: 440rpx;
	}

	.switch-btn {
		color: #F55606;
		font-weight: 500;
	}
}

.divider {
	height: 1rpx;
	background: #ebeef5;
	margin: 28rpx 0;
}

.amount-section {
	.amount-title {
		color: #333;
	}

	.amount-input-box {
		margin-top: 24rpx;
		border-bottom: 2rpx solid #e4e7ed;
		padding-bottom: 16rpx;

		.currency {
			font-size: 56rpx;
			font-weight: bold;
			color: #222;
			margin-right: 12rpx;
		}

		.amount-input {
			flex: 1;
			font-size: 72rpx;
			font-weight: bold;
			color: #222;
			height: 90rpx;
			line-height: 90rpx;
		}

		.clear-icon {
			padding: 10rpx;
		}
	}

	.quick-amounts {
		margin-top: 24rpx;
		flex-wrap: wrap;

		.quick-tag {
			padding: 12rpx 28rpx;
			background: #f5f5f5;
			border-radius: 28rpx;
			font-size: 26rpx;
			color: #555;
			margin-right: 16rpx;
			margin-bottom: 16rpx;
			transition: all 0.2s;

			&.active {
				background: #FFF0E6;
				color: #F55606;
				font-weight: 600;
				border: 1rpx solid #F55606;
			}
		}
	}
}

.remark-box {
	.remark-input {
		background: #f7f8fa;
		border-radius: 16rpx;
		padding: 20rpx 24rpx;
		font-size: 26rpx;
		color: #333;
	}
}

.pay-btn-wrap {
	.submit-pay-btn {
		background: linear-gradient(135deg, #FF6A00 0%, #F51D1D 100%);
		color: #ffffff;
		border-radius: 44rpx;
		height: 88rpx;
		line-height: 88rpx;
		font-size: 32rpx;
		font-weight: bold;
		box-shadow: 0 8rpx 24rpx rgba(245, 29, 29, 0.25);
		border: none;

		&.disabled {
			opacity: 0.5;
			box-shadow: none;
		}
	}
}

.bottom-tips {
	margin-top: 32rpx;
	color: #999;
}

.picker-content {
	background: #fff;
	padding: 32rpx;
	max-height: 70vh;

	.picker-title {
		text-align: center;
		color: #222;
	}

	.shop-list-scroll {
		max-height: 50vh;
	}

	.picker-item {
		padding: 20rpx 0;
		border-bottom: 1rpx solid #f2f2f2;

		.picker-logo {
			width: 72rpx;
			height: 72rpx;
			border-radius: 12rpx;
		}
	}
}

.success-box {
	width: 580rpx;
	background: #fff;
	border-radius: 24rpx;
	padding: 48rpx 36rpx;
	text-align: center;

	.success-icon {
		width: 110rpx;
		height: 110rpx;
		margin: 0 auto;
	}

	.detail-rows {
		background: #f9f9f9;
		border-radius: 16rpx;
		padding: 24rpx;
		text-align: left;
	}

	.view-btn {
		background: linear-gradient(135deg, #FF6A00 0%, #F51D1D 100%);
		color: #fff;
		border-radius: 40rpx;
		height: 80rpx;
		line-height: 80rpx;
		font-size: 28rpx;
		font-weight: bold;
	}

	.done-btn {
		background: #f5f5f5;
		color: #666;
		border-radius: 40rpx;
		height: 80rpx;
		line-height: 80rpx;
		font-size: 28rpx;
	}
}
</style>
