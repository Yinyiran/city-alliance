<template>
	<view class="banner-page">
		<!-- 顶部添加按钮 -->
		<view class="top-action-bar">
			<button class="add-btn acea-row row-middle row-center" @click="chooseImageAndAdd">
				<uv-icon name="plus" size="16" color="#fff"></uv-icon>
				<text class="m-l-8">上传新Banner</text>
			</button>
		</view>

		<!-- Banner 列表 -->
		<view class="banner-list" v-if="list.length > 0">
			<view class="banner-card" v-for="(item, index) in list" :key="index">
				<image :src="$util.img(item.image)" class="banner-preview" mode="aspectFill"></image>
				
				<view class="banner-card-body acea-row row-middle row-between">
					<view class="banner-info">
						<view class="banner-title text-more">{{ item.name || '未命名Banner' }}</view>
						<view class="banner-meta">排序：{{ item.sort || 0 }}</view>
					</view>
					
					<view class="delete-btn acea-row row-middle row-center" @click="deleteItem(item.id)">
						<uv-icon name="trash" size="16" color="#F55606"></uv-icon>
						<text class="m-l-4">删除</text>
					</view>
				</view>
			</view>
		</view>

		<!-- 空数据状态 -->
		<view class="empty-wrap" v-else-if="!loading">
			<image src="/static/empty.png" class="empty-icon" mode="aspectFit"></image>
			<text class="empty-text">当前暂未上传门店Banner</text>
			<view class="empty-sub">上传后将在商家Tab首页及门店详情页顶部轮播展示</view>
		</view>
	</view>
</template>

<script>
	import { shopBannerList, shopBannerAdd, shopBannerDelete } from "@/common/js/api.js";

	export default {
		data() {
			return {
				list: [],
				loading: false
			};
		},
		onShow() {
			this.getList();
		},
		methods: {
			async getList() {
				this.loading = true;
				try {
					let res = await shopBannerList();
					if (res && res.code >= 0 && Array.isArray(res.data)) {
						this.list = res.data;
					}
				} catch (e) {
					console.error(e);
				} finally {
					this.loading = false;
				}
			},
			chooseImageAndAdd() {
				uni.chooseImage({
					count: 1,
					sizeType: ['compressed'],
					sourceType: ['album', 'camera'],
					success: async (res) => {
						let tempFilePath = res.tempFilePaths[0];
						uni.showLoading({ title: '上传中...' });
						try {
							let uploadRes = await this.$util.upload_file_server(tempFilePath, {}, 'banner');
							let imgUrl = typeof uploadRes === 'object' ? (uploadRes.path || uploadRes.pic_path) : uploadRes;
							if (!imgUrl) {
								uni.hideLoading();
								uni.showToast({ title: '上传失败', icon: 'none' });
								return;
							}
							
							// 自动保存 Banner
							let addRes = await shopBannerAdd({
								name: '门店Banner',
								image: imgUrl,
								sort: 0
							});
							uni.hideLoading();
							if (addRes && addRes.code >= 0) {
								uni.showToast({ title: '添加成功', icon: 'success' });
								this.getList();
							} else {
								uni.showToast({ title: (addRes && addRes.message) || '保存失败', icon: 'none' });
							}
						} catch (e) {
							uni.hideLoading();
							uni.showToast({ title: '上传发生异常', icon: 'none' });
						}
					}
				});
			},
			deleteItem(id) {
				uni.showModal({
					title: '提示',
					content: '确定要删除该Banner吗？',
					confirmColor: '#F55606',
					success: async (res) => {
						if (res.confirm) {
							let delRes = await shopBannerDelete({ id });
							if (delRes && delRes.code >= 0) {
								uni.showToast({ title: '已删除', icon: 'none' });
								this.getList();
							} else {
								uni.showToast({ title: (delRes && delRes.message) || '删除失败', icon: 'none' });
							}
						}
					}
				});
			}
		}
	}
</script>

<style lang="scss" scoped>
	.banner-page {
		min-height: 100vh;
		background: #F5F5F5;
		padding: 24rpx;
		box-sizing: border-box;
	}

	.top-action-bar {
		margin-bottom: 24rpx;

		.add-btn {
			width: 100%;
			height: 88rpx;
			background: linear-gradient(135deg, #FF6A00 0%, #F55606 100%);
			border-radius: 44rpx;
			color: #FFFFFF;
			font-size: 30rpx;
			font-weight: 500;
			box-shadow: 0 6rpx 20rpx rgba(245, 86, 6, 0.25);
		}
	}

	.banner-list {
		.banner-card {
			background: #FFFFFF;
			border-radius: 20rpx;
			overflow: hidden;
			margin-bottom: 24rpx;
			box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.03);

			.banner-preview {
				width: 100%;
				height: 320rpx;
				display: block;
				background: #F8F8F8;
			}

			.banner-card-body {
				padding: 20rpx 24rpx;

				.banner-info {
					flex: 1;
					overflow: hidden;

					.banner-title {
						font-size: 28rpx;
						color: #222222;
						font-weight: 500;
					}

					.banner-meta {
						font-size: 22rpx;
						color: #999999;
						margin-top: 6rpx;
					}
				}

				.delete-btn {
					padding: 10rpx 20rpx;
					border: 1rpx solid #F55606;
					border-radius: 30rpx;
					font-size: 24rpx;
					color: #F55606;
					margin-left: 20rpx;
				}
			}
		}
	}

	.empty-wrap {
		padding: 120rpx 0;
		display: flex;
		flex-direction: column;
		align-items: center;

		.empty-icon {
			width: 200rpx;
			height: 200rpx;
		}

		.empty-text {
			font-size: 28rpx;
			color: #666666;
			margin-top: 24rpx;
		}

		.empty-sub {
			font-size: 22rpx;
			color: #999999;
			margin-top: 10rpx;
		}
	}
</style>
