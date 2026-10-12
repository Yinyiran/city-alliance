<template>
	<view>
		<!-- <uv-sticky> -->
		<nvabar title='全部品牌'></nvabar>
		<!-- </uv-sticky> -->
		<image src="/static/bg1.png" class="bgs" mode=""></image>
		<view class="part1">
			<image src="/otherpages/static/bg6.png" mode=""></image>
			<image src="/static/t.png" mode=""></image>
			<button open-type="contact" class="consult-btn">入驻咨询</button>
			<view class="">{{ shop_num }}家</view>
		</view>
		<view class="container">
			<view class="acea-row row-middle row-center titles">
				<image src="/otherpages/static/lline.png" mode=""></image>
				<view class="">商家推荐</view>
				<image src="/otherpages/static/rline.png" mode=""></image>
			</view>

			<!-- 胶囊样式过滤筛选栏 -->
			<view class="filter-capsule-wrapper acea-row row-middle">
				<view class="filter-capsule-bar">
					<view class="capsule-item" :class="{ active: sortType === 'distance' }"
						@click="changeSort('distance')">
						<text>距离</text>
					</view>
					<view class="capsule-item" :class="{ active: sortType === 'follow' }" @click="changeSort('follow')">
						<text>关注量</text>
					</view>
				</view>
				<view class="filter-single-capsule" :class="{ active: sortType === 'sales' }"
					@click="changeSort('sales')">
					<text>销量</text>
					<text class="arrow-down">{{ sortType === 'sales' && sortOrder === 'asc' ? '↑' : '↓' }}</text>
				</view>
			</view>

			<view v-for="(item, index) in list" class="m-b-20 store-card-item" :key="index">
				<view class="p-24">
					<!-- 第一行：商家信息（flex布局，严格nowrap不换行不变形） -->
					<view class="store-main-row" @click="godetail(item.store_id)">
						<view class="logos">
							<image :src="$util.img(item.store_image)" mode="aspectFill"></image>
							<view class="rank-badge">
								<image src="/static/rank1.png" v-if="index == 0" mode=""></image>
								<image src="/static/rank2.png" v-else-if="index == 1" mode=""></image>
								<image src="/static/rank3.png" v-else-if="index == 2" mode=""></image>
								<image src="/static/rank.png" v-else mode=""></image>
								<text v-if="index > 2">{{ index + 1 }}</text>
							</view>
						</view>
						<view class="rights">
							<view class="store-name-row">
								<view class="store-title-wrap">
									<image src="/static/shop.png" class="shop-icon" mode=""></image>
									<text class="store-title-text">{{ item.store_name }}</text>
									<image src="/otherpages/static/go3.png" class="go-icon" mode=""></image>
								</view>
								<text class="distance-text">{{ item.distance_value }}km</text>
							</view>
							<view class="rights-bottom">
								<view class="w1">
									<view class="info-line m-b-6">
										<image src="/otherpages/static/clock.png" class="info-icon" mode=""></image>
										<text class="info-text">营业时间 {{ item.open_date }}</text>
									</view>
									<view class="info-line">
										<image src="/otherpages/static/map4.png" class="info-icon" mode=""></image>
										<text class="info-text">{{ item.address }}</text>
									</view>
								</view>
								<view class="w2">
									<view class="btn-icon btn-nav" @click.stop="daohang(item)">
										<image src="/otherpages/static/map2.png" mode=""></image>
									</view>
									<view class="btn-icon btn-call" @click.stop="calls(item)">
										<image src="/otherpages/static/phone.png" mode=""></image>
									</view>
								</view>
							</view>
						</view>
					</view>

					<!-- 第二行：本店商品（横向滑动，显示4个，单独一行名称，另再一行销量+价格突出） -->
					<view class="store-goods-scroll-wrap m-t-20" v-if="item.goods_list && item.goods_list.length > 0">
						<scroll-view scroll-x="true" class="store-goods-scroll" :show-scrollbar="false">
							<view class="store-goods-row">
								<view v-for="(goods, gIdx) in item.goods_list" :key="gIdx" class="goods-card-mini"
									@click.stop="goGoodsDetail(goods)">
									<image :src="$util.img(goods.goods_image)" mode="aspectFill" class="goods-mini-img">
									</image>
									<view class="goods-mini-title">{{ goods.goods_name }}</view>
									<view class="goods-mini-bottom">
										<view class="goods-mini-price">
											<text class="price-symbol">￥</text>{{ goods.price }}
										</view>
										<text class="goods-mini-sales">销{{ goods.sale_num || 0 }}</text>
									</view>
								</view>
							</view>
						</scroll-view>
					</view>
				</view>
			</view>

			<view v-if="list.length === 0" class="empty-wrap acea-row row-middle row-center p-40 font-26"
				style="color: #999;">
				暂无相关商家
			</view>

		</view>
	</view>
</template>

<script>
import {
	websiteInfo,
	storeList
} from "@/common/js/api.js"
export default {
	data() {
		return {
			shop_num: 0,
			page: 1,
			last_page: 1,
			list: [],
			sortType: 'distance', // 'distance' | 'sales' | 'follow'
			sortOrder: 'desc',    // 'asc' | 'desc'
			latitude: '',
			longitude: ''
		};
	},
	onLoad() {
		this.initLocation();
	},
	onShow() {
		this.getinfo();
		this.getlist();
	},
	onReachBottom() {
		if (this.last_page > this.page) {
			this.page++;
			this.getlist();
		}
	},
	methods: {
		initLocation() {
			let userLoc = uni.getStorageSync('userLocation');
			if (userLoc && userLoc.info) {
				this.latitude = userLoc.info.latitude;
				this.longitude = userLoc.info.longitude;
			} else {
				let loc = uni.getStorageSync('location');
				if (loc && loc.latitude) {
					this.latitude = loc.latitude;
					this.longitude = loc.longitude;
				}
			}
			if (!this.latitude) {
				uni.getLocation({
					type: 'gcj02',
					success: res => {
						this.latitude = res.latitude;
						this.longitude = res.longitude;
						if (this.sortType === 'distance' && this.page === 1) {
							this.getlist();
						}
					}
				});
			}
		},
		changeSort(type) {
			if (this.sortType === type) {
				if (type === 'sales') {
					this.sortOrder = this.sortOrder === 'desc' ? 'asc' : 'desc';
					this.page = 1;
					this.getlist();
				}
				return;
			}
			this.sortType = type;
			this.sortOrder = 'desc';
			this.page = 1;
			this.getlist();
		},
		godetail(e) {
			uni.navigateTo({
				url: '/otherpages/storeDetail/storeDetail?id=' + e
			});
		},
		goGoodsDetail(goods) {
			let id = goods.sku_id || goods.goods_id;
			uni.navigateTo({
				url: '/otherpages/goods_detail/goods_detail?id=' + id
			});
		},
		daohang(e) {
			uni.openLocation({
				latitude: Number(e.latitude),
				longitude: Number(e.longitude),
				name: e.address
			});
		},
		calls(e) {
			uni.makePhoneCall({
				phoneNumber: e.telphone
			});
		},
		getlist() {
			let params = {
				page: this.page,
				page_size: 30,
				sort_type: this.sortType,
				order_type: this.sortType,
				sort: this.sortType === 'distance' ? 'asc' : this.sortOrder,
				latitude: this.latitude || '',
				longitude: this.longitude || '',
				website_id: uni.getStorageSync('website_id') || 0
			};
			storeList(params).then(res => {
				if (res && res.data) {
					let newList = res.data.list || [];
					this.list = this.page > 1 ? this.list.concat(...newList) : newList;
					this.last_page = res.data.page_count || 1;
				}
			});
		},
		getinfo() {
			let params = {
				website_id: uni.getStorageSync('website_id')
			};
			websiteInfo(params).then(res => {
				if (res && res.data) {
					this.shop_num = res.data.shop_num || 0;
				}
			});
		}
	}
}
</script>

<style lang="scss">
page {
	background: #fff;
}

.container {
	position: relative;
	z-index: 999;
	margin-top: 32rpx;
	background: #fff;
	border-radius: 24rpx 24rpx 0rpx 0rpx;
	padding: 32rpx;
	box-sizing: border-box;

	.titles {
		view {
			font-weight: 600;
			font-size: 32rpx;
			color: #222222;
			margin: 0 24rpx;
		}

		image {
			width: 129rpx;
			height: 4rpx;
		}
	}

	.filter-capsule-wrapper {
		margin: 28rpx 0 24rpx 0;
		display: flex;
		align-items: center;

		.filter-capsule-bar {
			background: #F0F2F5;
			border-radius: 999rpx;
			padding: 6rpx;
			display: inline-flex;
			align-items: center;
			margin-right: 18rpx;

			.capsule-item {
				height: 56rpx;
				line-height: 56rpx;
				padding: 0 34rpx;
				font-size: 26rpx;
				color: #555555;
				border-radius: 999rpx;
				transition: all 0.2s ease;
				text-align: center;
				box-sizing: border-box;

				&.active {
					background: #FFFFFF;
					color: #222222;
					font-weight: 600;
					box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.08);
				}
			}
		}

		.filter-single-capsule {
			background: #F0F2F5;
			border-radius: 999rpx;
			padding: 0 30rpx;
			height: 68rpx;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			font-size: 26rpx;
			color: #555555;
			transition: all 0.2s ease;
			box-sizing: border-box;

			.arrow-down {
				font-size: 22rpx;
				margin-left: 4rpx;
				font-weight: bold;
			}

			&.active {
				background: #FFFFFF;
				color: #222222;
				font-weight: 600;
				box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.08);
			}
		}
	}

	.store-card-item {
		background: #FFFFFF;
		border-radius: 16rpx;
		box-shadow: 0 2rpx 12rpx rgba(0, 0, 0, 0.03);
		border: 1rpx solid #F5F5F5;
	}

	.store-main-row {
		display: flex;
		align-items: center;
		flex-wrap: nowrap !important;
		width: 100%;
		height: 120rpx;
	}

	.logos {
		width: 150rpx;
		height: 120rpx;
		position: relative;
		flex-shrink: 0;
		border-radius: 12rpx;
		overflow: hidden;
		background: #f7f7f7;

		.rank-badge,
		>view {
			position: absolute;
			left: 0;
			top: 0;
			width: 44rpx;
			height: 48rpx;
			z-index: 5;

			>image {
				width: 100%;
				height: 100%;
				display: block;
			}

			text {
				position: absolute;
				font-weight: 500;
				font-size: 24rpx;
				color: #222222;
				left: 50%;
				top: 50%;
				transform: translate(-50%, -50%);
				z-index: 6;
			}
		}

		>image:first-child {
			width: 100%;
			height: 100%;
			border-radius: 12rpx;
			display: block;
		}
	}

	.rights {
		flex: 1;
		min-width: 0;
		margin-left: 18rpx;
		height: 120rpx;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		flex-wrap: nowrap !important;

		.store-name-row {
			display: flex;
			align-items: center;
			justify-content: space-between;
			flex-wrap: nowrap !important;
			width: 100%;

			.store-title-wrap {
				display: flex;
				align-items: center;
				flex: 1;
				min-width: 0;
				flex-wrap: nowrap !important;

				.shop-icon {
					width: 28rpx;
					height: 28rpx;
					margin-right: 8rpx;
					flex-shrink: 0;
				}

				.store-title-text {
					font-size: 28rpx;
					font-weight: 600;
					color: #222222;
					overflow: hidden;
					text-overflow: ellipsis;
					white-space: nowrap;
					flex: 1;
					min-width: 0;
				}

				.go-icon {
					width: 24rpx;
					height: 24rpx;
					margin-left: 6rpx;
					flex-shrink: 0;
				}
			}

			.distance-text {
				font-size: 22rpx;
				color: #666666;
				margin-left: 12rpx;
				flex-shrink: 0;
				white-space: nowrap;
			}
		}

		.rights-bottom {
			display: flex;
			align-items: center;
			justify-content: space-between;
			flex-wrap: nowrap !important;
			width: 100%;

			.w1 {
				flex: 1;
				min-width: 0;
				margin-right: 12rpx;
				display: flex;
				flex-direction: column;
				justify-content: center;

				.info-line {
					display: flex;
					align-items: center;
					flex-wrap: nowrap !important;
					font-size: 22rpx;
					color: #999999;
					line-height: 1.3;

					&.m-b-6 {
						margin-bottom: 6rpx;
					}

					.info-icon {
						width: 22rpx;
						height: 22rpx;
						margin-right: 6rpx;
						flex-shrink: 0;
					}

					.info-text {
						flex: 1;
						min-width: 0;
						overflow: hidden;
						text-overflow: ellipsis;
						white-space: nowrap;
					}
				}
			}

			.w2 {
				display: flex;
				align-items: center;
				flex-shrink: 0;
				flex-wrap: nowrap !important;

				.btn-icon {
					width: 50rpx;
					height: 50rpx;
					background: #FFEBE0;
					border-radius: 10rpx;
					display: flex;
					align-items: center;
					justify-content: center;
					flex-shrink: 0;

					&.btn-call {
						background: #E6FFEF;
						margin-left: 12rpx;
					}

					image {
						width: 26rpx;
						height: 26rpx;
						display: block;
					}
				}
			}
		}
	}

	.store-goods-scroll-wrap {
		width: 100%;
		overflow: hidden;

		.store-goods-scroll {
			width: 100%;
			white-space: nowrap;
		}

		.store-goods-row {
			display: flex;
			flex-direction: row;
			flex-wrap: nowrap;
			gap: 14rpx;
		}

		.goods-card-mini {
			width: 149rpx;
			flex-shrink: 0;
			display: flex;
			flex-direction: column;

			.goods-mini-img {
				width: 149rpx;
				height: 149rpx;
				border-radius: 12rpx;
				display: block;
				background: #F8F9FA;
			}

			.goods-mini-title {
				font-size: 22rpx;
				color: #333333;
				line-height: 30rpx;
				height: 30rpx;
				margin-top: 10rpx;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
				width: 100%;
			}

			.goods-mini-bottom {
				display: flex;
				align-items: baseline;
				justify-content: space-between;
				margin-top: 6rpx;
				flex-wrap: nowrap;
				width: 100%;

				.goods-mini-price {
					font-size: 24rpx;
					font-weight: 600;
					color: #FF3B30;
					line-height: 1;
					white-space: nowrap;

					.price-symbol {
						font-size: 20rpx;
						font-weight: 500;
						margin-right: 2rpx;
					}
				}

				.goods-mini-sales {
					font-size: 20rpx;
					color: #999999;
					line-height: 1;
					white-space: nowrap;
					margin-left: 4rpx;
					flex-shrink: 0;
				}
			}
		}
	}

	.info1 {
		font-weight: 400;
		font-size: 22rpx;
		color: #555555;
		background: #F5F5F5;
		border-radius: 12rpx;
		margin-top: 16rpx;
		height: 54rpx;
		padding: 0 20rpx;

		.line {
			width: 1rpx;
			height: 22rpx;
			background: #DCDFEB;
			margin: 0 20rpx;
		}
	}

	.aaa {
		margin: 20rpx 32rpx;
	}

	::v-deep .uv-swipe-action-item {
		border-radius: 24rpx;
	}

	.whits {
		margin-top: 52rpx;

		>view {
			color: #FFFFFF;
		}
	}
}

.part1 {
	margin: 0 32rpx;
	margin-top: 20rpx;
	position: relative;
	height: 210rpx;

	>image:first-child {
		width: 100%;
		height: 100%;
		position: absolute;
		right: 0;
		top: 0;
		z-index: 1;
	}

	>image:nth-child(2) {
		position: absolute;
		top: 40rpx;
		left: 40rpx;
		width: 220rpx;
		height: 58rpx;
		z-index: 2;
	}

	.consult-btn {
		position: absolute;
		right: 40rpx;
		bottom: 40rpx;
		z-index: 2;
		background: linear-gradient(135deg, #FF6633, #FF3B30);
		color: #FFFFFF;
		font-size: 26rpx;
		font-weight: 500;
		padding: 0 32rpx;
		height: 60rpx;
		line-height: 60rpx;
		border-radius: 30rpx;
		box-shadow: 0 4rpx 14rpx rgba(255, 59, 48, 0.3);
		border: none;
		margin: 0;
		display: flex;
		align-items: center;
		justify-content: center;

		&::after {
			border: none;
		}

		&:active {
			opacity: 0.9;
			transform: scale(0.98);
		}
	}

	>view {
		position: absolute;
		bottom: 40rpx;
		left: 40rpx;
		font-weight: bold;
		font-size: 56rpx;
		color: #222222;
		z-index: 2;
	}
}

.bgs {
	position: absolute;
	width: 100vw;
	height: 440rpx;
	top: 0;
	left: 0;
	z-index: -1;
}
</style>