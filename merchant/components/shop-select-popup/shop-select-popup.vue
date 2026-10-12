<template>
	<uv-popup ref="popup" :round="16" mode="bottom" :closeable="true">
		<view class="shop-popup-wrap">
			<view class="popup-header acea-row row-middle">
				<text class="title-text">切换企业/店铺</text>
				<text class="shop-count" v-if="slist.length > 0">（共{{ slist.length }}家店铺）</text>
			</view>

			<scroll-view scroll-y="true" class="shop-list-scroll">
				<view v-for="(item, index) in slist" :key="index"
					class="shop-item acea-row row-middle row-between"
					:class="{ 'active': item.site_id == currentSiteId }"
					@click="selectShop(item)">
					<view class="shop-left acea-row row-middle">
						<image :src="$util.img(item.store_image || item.store_logo)"
							class="shop-img" mode="aspectFill"></image>
						<view class="shop-info">
							<view class="shop-name-row acea-row row-middle">
								<text class="shop-name text-more">{{ item.site_name || item.store_name }}</text>
								<view class="role-badge" :class="'badge-' + (item.role || (item.is_manager == 1 ? 'owner' : 'staff'))">
									{{ item.role_name || (item.is_manager == 1 ? '老板' : '店员') }}
								</view>
							</view>
							<view class="shop-meta acea-row row-middle">
								<text class="team-text" v-if="item.team_num !== undefined">团队成员: {{ item.team_num }}人</text>
							</view>
						</view>
					</view>
					<view class="shop-right acea-row row-middle">
						<uv-icon name="checkmark-circle-fill" color="#F55606" size="22"
							v-if="item.site_id == currentSiteId"></uv-icon>
						<view class="enter-btn" v-else>进店</view>
					</view>
				</view>

				<view class="empty-tip acea-row row-column row-center row-middle" v-if="slist.length === 0">
					<text>暂无关联的企业与店铺</text>
				</view>
			</scroll-view>
		</view>
	</uv-popup>
</template>

<script>
	import { shopList, shopLogin } from "@/common/js/api.js"

	export default {
		name: "shop-select-popup",
		data() {
			return {
				slist: [],
				currentSiteId: uni.getStorageSync('site_id') || '',
				switching: false
			}
		},
		methods: {
			open() {
				this.currentSiteId = uni.getStorageSync('site_id') || '';
				this.fetchShopList();
				this.$refs.popup.open();
			},
			close() {
				this.$refs.popup.close();
			},
			fetchShopList() {
				shopList().then(res => {
					if (res.code == 0 && Array.isArray(res.data)) {
						this.slist = res.data;
					} else {
						this.slist = [];
					}
				}).catch(() => {
					this.slist = [];
				});
			},
			selectShop(item) {
				if (this.switching) return;
				let siteId = item.site_id;
				if (!siteId) return;

				if (String(siteId) === String(this.currentSiteId)) {
					this.close();
					return;
				}

				this.switching = true;
				uni.showLoading({ title: '切换店铺中...' });
				shopLogin({ site_id: siteId }).then(res => {
					uni.hideLoading();
					this.switching = false;
					if (res.code == 0) {
						let newSiteId = res.data.site_id || siteId;
						uni.setStorageSync('site_id', newSiteId);
						this.currentSiteId = newSiteId;
						this.close();

						// 触发全局切店广播
						uni.$emit('merchantShopChanged', {
							site_id: newSiteId,
							site_name: item.site_name || item.store_name,
							is_manager: res.data.is_manager
						});

						this.$emit('change', item);

						uni.showToast({
							title: '已切换至: ' + (item.site_name || item.store_name || '新店铺'),
							icon: 'none'
						});
					} else {
						uni.showToast({
							title: res.message || '切换失败',
							icon: 'none'
						});
					}
				}).catch(err => {
					uni.hideLoading();
					this.switching = false;
					uni.showToast({
						title: (err && err.message) ? err.message : '网络异常，请重试',
						icon: 'none'
					});
				});
			}
		}
	}
</script>

<style lang="scss" scoped>
.shop-popup-wrap {
	background: #FFFFFF;
	padding: 32rpx 28rpx 48rpx;
	box-sizing: border-box;

	.popup-header {
		padding-bottom: 24rpx;
		border-bottom: 1rpx solid #F0F0F0;

		.title-text {
			font-size: 34rpx;
			font-weight: 700;
			color: #1A1A1A;
		}

		.shop-count {
			font-size: 24rpx;
			color: #999999;
			margin-left: 8rpx;
		}
	}

	.shop-list-scroll {
		max-height: 60vh;
		min-height: 240rpx;
		padding-top: 20rpx;

		.shop-item {
			padding: 24rpx 16rpx;
			border-radius: 16rpx;
			margin-bottom: 16rpx;
			background: #FAFAFA;
			transition: all 0.2s;

			&:last-child {
				margin-bottom: 0;
			}

			.shop-left {
				flex: 1;
				overflow: hidden;

				.shop-img {
					width: 88rpx;
					height: 88rpx;
					border-radius: 16rpx;
					margin-right: 18rpx;
					flex-shrink: 0;
					background: #ECECEC;
				}

				.shop-info {
					flex: 1;
					overflow: hidden;

					.shop-name-row {
						gap: 12rpx;

						.shop-name {
							font-size: 30rpx;
							font-weight: 600;
							color: #222222;
							line-height: 40rpx;
							max-width: 320rpx;
						}

						.role-badge {
							font-size: 18rpx;
							font-weight: 600;
							padding: 2rpx 10rpx;
							border-radius: 6rpx;
							flex-shrink: 0;

							&.badge-owner {
								background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
								color: #FFFFFF;
							}

							&.badge-staff {
								background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
								color: #FFFFFF;
							}

							&.badge-member {
								background: linear-gradient(135deg, #10B981 0%, #059669 100%);
								color: #FFFFFF;
							}
						}
					}

					.shop-meta {
						margin-top: 8rpx;
						font-size: 22rpx;
						color: #999999;
					}
				}
			}

			.shop-right {
				margin-left: 20rpx;
				flex-shrink: 0;

				.enter-btn {
					padding: 8rpx 24rpx;
					border-radius: 24rpx;
					border: 2rpx solid #E0E0E0;
					font-size: 22rpx;
					color: #666666;
				}
			}

			&.active {
				background: #FFF5F0;
				border: 1rpx solid #FFD0BD;

				.shop-name {
					color: #F55606;
				}
			}
		}

		.empty-tip {
			padding: 80rpx 0;
			font-size: 26rpx;
			color: #999999;
		}
	}
}
</style>
