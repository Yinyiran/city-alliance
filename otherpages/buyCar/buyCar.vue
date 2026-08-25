<template>
	<view>
		<uv-sticky bg-color="#fff">
			<nvabar type='1' title='购物车' titleStyle='color:#222'></nvabar>
			<uv-tabs :list="listA" lineWidth="16" :scrollable="false" :current="current"
				lineColor="linear-gradient( 90deg, #F5AB18 0%, #F55606 100%);" :activeStyle="{
						fontWeight: 'bold',
				 }" itemStyle="height:44px;font-size: 28rpx;" @click="onclick"></uv-tabs>
		</uv-sticky>
		<view class="m-r-24 m-l-24" v-if="list.length>0">
			<view v-for="(item,index) in cartData" class="p-32 items" :key="index">
				<view class="acea-row row-middle row-between">
					<view class="acea-row row-middle font-32 font-weight-6">
						<radio value="" @click="siteAllElection(!item.checked, index)" :checked="item.checked" />
						<image src="/static/stores.png" class="w-32 h-32 m-r-8 m-l-28" mode=""></image>
						{{item.siteName}}
						<view class="go m-l-8">
							<uv-icon color="#555555" size="8" name="arrow-right"></uv-icon>
						</view>
					</view>
				</view>
				<view class="acea-row row-middle m-t-28 row-between" v-for="(items,indexs) in item.cartList"
					:key="indexs">
					<radio value="" @click="singleElection(index, indexs)" :checked="items.checked" />
					<image :src="$util.img(items.sku_image)" class="w-140 h-140 bor-16" mode=""></image>
					<view class="widths h-140 acea-row row-between row-column">
						<view class="font-28 text-more-2">{{items.sku_name}}</view>
						<view class="acea-row row-middle row-between">
							<view class="points">
								￥
								<text>{{items.price}}</text>
								<text class="line">|</text>
								<text class="font-30 font-weight-6">{{items.point}}</text>
								积分
							</view>
							<uv-number-box v-model="items.num" :name='items.cart_id' @change="onChange">
							</uv-number-box>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="acea-row row-middle row-center row-column" v-else>
			<image src="/static/empty1.png" mode="" class="w-340 h-340 m-t-220"></image>
			<view class="font-32 font-weight-6 m-t-24">您的购物车还是空的哦，快去选购吧</view>
			<view class="btns" @click="goindex">去逛逛</view>
		</view>
		<view class="btms iphone-x">
			<view class="acea-row row-between row-middle">
				<view class="acea-row row-middle" style="flex-wrap: nowrap;">
					<radio value="" :checked="checkAll" @click="allElection" /><text class="m-l-16 m-r-24"
						style="white-space: nowrap;">全选</text>
					<view class="acea-row row-middle">
						<view class="acea-row row-middle">
							<text class="font-32">合计：</text>
							<view class="points">
								￥
								<text>{{totalPrice}}</text>
								<text class="line">|</text>
								<text class="font-30 font-weight-6">{{totalPoint}}</text>
								积分
							</view>
						</view>
						<view class="" style="color: #999999;">已选：{{totalCount}}件</view>
					</view>
				</view>
				<view class="" @click="settlement">去结算</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		cartList,
		cartEdit
	} from '../../common/js/api';
	export default {
		data() {
			return {
				listA: [
					// {
					// 	name: '全部',
					// 	id: 'all'
					// },
					{
						name: '现金商品',
						id: 0
					}, {
						name: '积分商品',
						id: 1
					}
				],
				current: 0,
				list: [],
				cartData: [],
				checkAll: true,
				totalPrice: '0.00',
				totalCount: 0,
				totalPoint: "0.00",
				type:0
			};
		},
		onShow() {
			this.getlist()
		},
		methods: {
			goindex(){
				uni.switchTab({
					url:'/pages/index/index'
				})
			},
			settlement() {
				if (this.totalCount > 0) {
					let cart_ids = [];
					this.cartData.forEach(siteItem => {
						siteItem.cartList.forEach(item => {
							if (item.checked) {
								cart_ids.push(item.cart_id);
							}
						});
					});

					if (this.isSub) return;
					this.isSub = true;

					uni.setStorage({
						key: 'orderCreateData',
						data: {
							cart_ids: cart_ids.toString(),
							goodstype:this.type+1
						},
						success: () => {
							this.$util.redirectTo('/otherpages/payment/payment');
							this.isSub = false;
						}
					});
				}
			},
			/**
			 * 单选
			 * @param {Object} index
			 */
			singleElection(siteIndex, index) {
				console.log(siteIndex, index);
				this.cartData[siteIndex].cartList[index].checked = !this.cartData[siteIndex].cartList[index].checked;
				this.calculationTotalPrice();
			},
			/**
			 * 店铺全选
			 * @param {Object} checked
			 */
			siteAllElection(checked, index) {
				this.cartData[index].checked = checked;
				this.cartData[index].cartList.forEach(item => {
					item.checked = checked;
				});
				this.calculationTotalPrice();
			},
			/**
			 * 全选
			 */
			allElection(checked) {
				if (typeof checked == 'boolean') {
					this.checkAll = checked;
				} else {
					this.checkAll = !this.checkAll;
				}
				if (this.cartData.length) {
					this.cartData.forEach(siteItem => {
						siteItem.checked = this.checkAll;
						siteItem.cartList.forEach(item => {
							item.checked = this.checkAll;
						});
					});
				}
				this.calculationTotalPrice();
			},
			/**
			 * 计算购物车总价
			 */
			calculationTotalPrice() {
				if (this.cartData.length) {
					let totalPrice = 0,
						totalCount = 0,
						totalPoint = 0,
						siteAllElectionCount = 0;

					this.cartData.forEach(siteItem => {
						let siteGoodsCount = 0;
						siteItem.cartList.forEach(item => {
							if (item.checked) {
								siteGoodsCount += 1;
								totalCount += parseInt(item.num);
								if (Number(item.member_price) > 0 && Number(item.member_price) < Number(
										item.discount_price)) {
									totalPrice += item.member_price * item.num;
									totalPoint += item.point * item.num;
								} else {
									totalPrice += item.discount_price * item.num;
									totalPoint += item.point * item.num;
								}
							}
						});
						if (siteItem.cartList.length == siteGoodsCount) {
							siteItem.checked = true;
							siteAllElectionCount += 1;
						} else {
							siteItem.checked = false;
						}
					});
					this.totalPrice = totalPrice.toFixed(2);
					this.totalPoint = totalPoint.toFixed(2);
					this.totalCount = totalCount;
					this.checkAll = this.cartData.length == siteAllElectionCount;
				} else {
					this.totalPrice = '0.00';
					this.totalPoint = '0.00'
					this.totalCount = 0;
				}
				this.modifyFlag = false;
			},
			/**
			 * 处理购物车数据结构
			 */
			handleCartData(data) {
				this.invalidGoods = [];
				this.cartData = [];
				var temp = {};
				data.forEach((item, index) => {
					if (item.goods_state == 1 && item.verify_state == 1 && item.shop_status != 0) {
						// 如果最小限购超出库存则该商品失效
						if (item.min_buy > 0 && item.min_buy > item.stock) {
							this.invalidGoods.push(item);
						} else {
							item.checked = true;
							item.edit = false;
							if (temp['site_' + item.site_id] != undefined) {
								temp['site_' + item.site_id].cartList.push(item);
							} else {
								temp['site_' + item.site_id] = {
									siteId: item.site_id,
									siteName: item.site_name,
									edit: false,
									checked: true,
									cartList: [item]
								};
							}
						}
					} else {
						this.invalidGoods.push(item);
					}
				});

				this.cartData = [];
				Object.keys(temp).forEach(key => {
					this.cartData.push(temp[key]);
				});
				this.calculationTotalPrice();
				if (this.cartData.length) {
					this.cartData.forEach(cart => {
						cart.cartList.forEach(v => {
							if (v.sku_spec_format) {
								v.sku_spec_format = JSON.parse(v.sku_spec_format);
							} else {
								v.sku_spec_format = [];
							}
						});
					});
				}
				if (this.invalidGoods.length) {
					this.invalidGoods.forEach(v => {
						if (v.sku_spec_format) {
							v.sku_spec_format = JSON.parse(v.sku_spec_format);
						} else {
							v.sku_spec_format = [];
						}
					});
				}
			},
			onChange(e) {
				let params = {
					cart_id: e.name,
					num: e.value
				}
				cartEdit(params).then(res => {
					if (res.code >= 0) {
						// this.cartData[params.siteIndex].cartList[params.cartIndex].num = num;
						this.calculationTotalPrice();
					} else {
						this.$util.showToast({
							title: res.message
						});
					}
				})
			},
			onclick(e) {
				this.type = e.id
				this.getlist()
			},
			getlist() {
				let params = {
					type: String(this.type)
				}
				cartList(params).then(res => {
					this.list = res.data
					this.handleCartData(res.data)
					console.log(res);
				})
			},
		}
	}
</script>

<style lang="scss">
	.btms {
		position: fixed;
		bottom: 0;
		left: o;
		width: 100vw;
		background: #fff;
		box-shadow: 0rpx -4rpx 12rpx 0rpx rgba(0, 0, 0, 0.04);
		border-radius: 24rpx 24rpx 0rpx 0rpx;

		>view {
			margin: 24rpx 32rpx;
			flex-wrap: nowrap;

			>view:last-child {
				width: 160rpx;
				height: 80rpx;
				background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
				border-radius: 40rpx;
				font-weight: 500;
				font-size: 28rpx;
				color: #FFFFFF;
				text-align: center;
				line-height: 80rpx;
			}
		}
	}

	radio {
		transform: scale(0.8);
	}

	::v-deep .uv-number-box__input {
		width: 40rpx !important;
		height: 40rpx !important;
	}

	::v-deep .uv-number-box__plus,
	::v-deep .uv-number-box__minus {
		width: 40rpx !important;
		height: 40rpx !important;
	}

	.points {
		color: #F51D1D;

		text {
			font-weight: 600;
			font-size: 30rpx;
			color: #F51D1D;
		}

		.line {
			width: 36rpx;
			font-weight: 400;
			font-size: 28rpx;
			color: #DDDDDD;
			display: inline-block;
			text-align: center;
		}
	}

	.items {
		background: #fff;
		margin-top: 20rpx;

		.widths {
			width: calc(100% - 140rpx - 20rpx - 32rpx - 20rpx - 20rpx);
			flex-wrap: nowrap;

			.points {
				color: #F51D1D;

				text {
					font-weight: 600;
					font-size: 30rpx;
					color: #F51D1D;
				}

				.line {
					width: 36rpx;
					font-weight: 400;
					font-size: 28rpx;
					color: #DDDDDD;
					display: inline-block;
					text-align: center;
				}
			}

			.nums {
				color: #999999;
			}
		}
	}

	.btns {
		width: 240rpx;
		height: 64rpx;
		background: #F5F5F5;
		border-radius: 6rpx;
		border: 2rpx solid #DDDDDD;
		font-weight: 400;
		font-size: 26rpx;
		color: #222222;
		text-align: center;
		line-height: 64rpx;
		margin-top: 32rpx;
	}
</style>