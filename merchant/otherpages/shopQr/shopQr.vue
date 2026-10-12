<template>
	<view class="qr-page">
		<view class="qr-card" v-if="!loading">
			<!-- 商家信息展示 -->
			<view class="shop-header">
				<image :src="$util.img(shopInfo.logo)" v-if="shopInfo.logo" class="shop-logo" mode="aspectFill"></image>
				<view class="shop-logo-default" v-else>
					<uv-icon name="home" color="#FFFFFF" size="28"></uv-icon>
				</view>
				<view class="shop-meta">
					<text class="shop-name">{{ shopInfo.site_name || '我的店铺' }}</text>
					<view class="shop-id-tag">
						<text class="tag-text">商家ID: {{ site_id }}</text>
					</view>
				</view>
			</view>

			<!-- 二维码主体区域 -->
			<view class="qr-container">
				<!-- 如果后端生成了小程序太阳码且未加载失败，展示官方码图片 -->
				<image 
					v-if="shopInfo.qrcode_url && !imgLoadError" 
					:src="$util.img(shopInfo.qrcode_url)" 
					class="qr-img" 
					mode="aspectFit" 
					@error="onImgError"
				></image>
				
				<!-- 本地实时生成的矢量二维码（带site_id参数，无须依赖微信后台发布，零故障保底） -->
				<uv-qrcode 
					v-else 
					ref="qrcode" 
					:value="qrCodeValue" 
					:size="220" 
					:options="qrOptions"
				></uv-qrcode>
			</view>

			<!-- 说明文案 -->
			<view class="tips-box">
				<view class="tip-item">
					<uv-icon name="checkmark-circle-fill" color="#F55606" size="16"></uv-icon>
					<text class="tip-text">用户扫码进入小程序注册，自动绑定为本店会员</text>
				</view>
				<view class="tip-item">
					<uv-icon name="checkmark-circle-fill" color="#F55606" size="16"></uv-icon>
					<text class="tip-text">该用户分享小程序任意卡片，将自动附带本店ID</text>
				</view>
			</view>

			<!-- 保存按钮 -->
			<view class="action-box">
				<button class="btn-save" :loading="saving" :disabled="saving" @click="saveQrcode">
					<uv-icon name="download" color="#FFFFFF" size="18" class="m-r-8"></uv-icon>
					<text class="btn-text">保存二维码到相册</text>
				</button>
			</view>
		</view>

		<!-- 加载动画 -->
		<view class="loading-state" v-else>
			<uv-loading-icon mode="semicircle" color="#F55606" size="36"></uv-loading-icon>
			<text class="loading-text">正在生成商家二维码...</text>
		</view>
	</view>
</template>

<script>
	import { getShopQrcode } from '@/common/js/api.js';

	export default {
		data() {
			return {
				site_id: '',
				loading: true,
				saving: false,
				imgLoadError: false,
				shopInfo: {
					site_id: '',
					site_name: '',
					logo: '',
					qrcode_url: '',
					qr_path: ''
				},
				qrOptions: {
					errorCorrectLevel: 'H',
					margin: 10
				}
			};
		},
		computed: {
			qrCodeValue() {
				if (this.shopInfo && this.shopInfo.qr_path) {
					return this.shopInfo.qr_path;
				}
				const id = this.site_id || uni.getStorageSync('site_id') || '';
				const suid = (this.shopInfo && this.shopInfo.suid) || '';
				return `pages/index/index?site_id=${id}${suid ? `&suid=${suid}` : ''}`;
			}
		},
		onLoad() {
			this.site_id = uni.getStorageSync('site_id') || '';
			this.fetchShopQrcode();
		},
		methods: {
			async fetchShopQrcode() {
				this.loading = true;
				try {
					const res = await getShopQrcode({ site_id: this.site_id });
					if (res && res.code >= 0 && res.data) {
						this.shopInfo = res.data;
						if (res.data.site_id) {
							this.site_id = res.data.site_id;
						}
					}
				} catch (e) {
					console.error('获取商家二维码失败', e);
				} finally {
					this.loading = false;
				}
			},
			onImgError() {
				this.imgLoadError = true;
			},
			saveQrcode() {
				if (this.saving) return;

				// 如果有可用的远程图片，优先下载并保存
				if (this.shopInfo.qrcode_url && !this.imgLoadError) {
					this.downloadAndSaveImage();
				} else {
					this.saveCanvasQrcode();
				}
			},
			downloadAndSaveImage() {
				this.saving = true;
				uni.showLoading({ title: '保存中...' });
				const imgUrl = this.$util.img(this.shopInfo.qrcode_url);
				uni.downloadFile({
					url: imgUrl,
					success: (res) => {
						if (res.statusCode === 200 && res.tempFilePath) {
							uni.saveImageToPhotosAlbum({
								filePath: res.tempFilePath,
								success: () => {
									uni.hideLoading();
									this.saving = false;
									uni.showToast({ title: '已保存至相册', icon: 'success' });
								},
								fail: (err) => {
									uni.hideLoading();
									this.saving = false;
									this.handleSaveFail(err);
								}
							});
						} else {
							// 远程下载异常时，降级使用 canvas 保存
							this.saveCanvasQrcode();
						}
					},
					fail: () => {
						this.saveCanvasQrcode();
					}
				});
			},
			saveCanvasQrcode() {
				if (!this.$refs.qrcode) {
					this.saving = false;
					uni.hideLoading();
					uni.showToast({ title: '二维码正在生成', icon: 'none' });
					return;
				}
				this.saving = true;
				uni.showLoading({ title: '保存中...' });
				this.$refs.qrcode.save({
					success: () => {
						uni.hideLoading();
						this.saving = false;
						uni.showToast({ title: '已保存至相册', icon: 'success' });
					},
					fail: (err) => {
						uni.hideLoading();
						this.saving = false;
						this.handleSaveFail(err);
					}
				});
			},
			handleSaveFail(err) {
				if (err && (err.errMsg && (err.errMsg.indexOf('auth') !== -1 || err.errMsg.indexOf('denied') !== -1))) {
					uni.showModal({
						title: '提示',
						content: '需要您授权相册权限才能将二维码保存至相册',
						confirmText: '去授权',
						success: (modalRes) => {
							if (modalRes.confirm) {
								uni.openSetting();
							}
						}
					});
				} else {
					uni.showToast({
						title: '保存失败，请稍后重试',
						icon: 'none'
					});
				}
			}
		}
	};
</script>

<style lang="scss" scoped>
	.qr-page {
		min-height: 100vh;
		background: #F6F7FB;
		padding: 36rpx 32rpx env(safe-area-inset-bottom);
		box-sizing: border-box;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: flex-start;
	}

	.qr-card {
		width: 100%;
		background: #FFFFFF;
		border-radius: 32rpx;
		padding: 48rpx 36rpx;
		box-sizing: border-box;
		box-shadow: 0 12rpx 36rpx rgba(0, 0, 0, 0.04);
		display: flex;
		flex-direction: column;
		align-items: center;
	}

	.shop-header {
		display: flex;
		align-items: center;
		width: 100%;
		padding-bottom: 32rpx;
		border-bottom: 2rpx solid #F0F0F0;

		.shop-logo {
			width: 100rpx;
			height: 100rpx;
			border-radius: 20rpx;
			border: 2rpx solid #F0F0F0;
			margin-right: 24rpx;
			flex-shrink: 0;
		}

		.shop-logo-default {
			width: 100rpx;
			height: 100rpx;
			border-radius: 20rpx;
			background: linear-gradient(135deg, #FF7033 0%, #F55606 100%);
			display: flex;
			align-items: center;
			justify-content: center;
			margin-right: 24rpx;
			flex-shrink: 0;
		}

		.shop-meta {
			display: flex;
			flex-direction: column;
			justify-content: center;
			overflow: hidden;

			.shop-name {
				font-size: 34rpx;
				font-weight: 600;
				color: #1A1A1A;
				line-height: 48rpx;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
			}

			.shop-id-tag {
				display: inline-flex;
				align-items: center;
				background: #FFF2EB;
				border-radius: 16rpx;
				padding: 4rpx 16rpx;
				margin-top: 10rpx;
				align-self: flex-start;

				.tag-text {
					font-size: 22rpx;
					font-weight: 500;
					color: #F55606;
				}
			}
		}
	}

	.qr-container {
		margin: 40rpx 0;
		padding: 28rpx;
		background: #FAFAFA;
		border: 2rpx dashed #E2E2E2;
		border-radius: 28rpx;
		display: flex;
		align-items: center;
		justify-content: center;

		.qr-img {
			width: 440rpx;
			height: 440rpx;
		}
	}

	.tips-box {
		width: 100%;
		background: #FFF9F5;
		border-radius: 20rpx;
		padding: 24rpx 28rpx;
		box-sizing: border-box;
		margin-bottom: 40rpx;

		.tip-item {
			display: flex;
			align-items: flex-start;
			margin-bottom: 12rpx;

			&:last-child {
				margin-bottom: 0;
			}

			.tip-text {
				font-size: 24rpx;
				color: #666666;
				line-height: 36rpx;
				margin-left: 12rpx;
			}
		}
	}

	.action-box {
		width: 100%;

		.btn-save {
			width: 100%;
			height: 92rpx;
			background: linear-gradient(135deg, #FF7033 0%, #F55606 100%);
			border-radius: 46rpx;
			display: flex;
			align-items: center;
			justify-content: center;
			border: none;
			box-shadow: 0 10rpx 24rpx rgba(245, 86, 6, 0.3);

			&:active {
				opacity: 0.9;
			}

			.btn-text {
				font-size: 30rpx;
				font-weight: 600;
				color: #FFFFFF;
				margin-left: 8rpx;
			}
		}
	}

	.loading-state {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding-top: 200rpx;

		.loading-text {
			margin-top: 24rpx;
			font-size: 28rpx;
			color: #999999;
		}
	}
</style>
