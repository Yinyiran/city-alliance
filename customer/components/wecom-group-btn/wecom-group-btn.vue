<template>
	<view class="wecom-group-btn-root" :style="customStyle">
		<!-- 按钮主体容器 -->
		<view class="wecom-trigger-box acea-row row-middle" :class="btnClass" @click="handleClick">
			<slot>
				<uv-icon name="weixin-fill" size="13" color="#07C160"></uv-icon>
				<text class="btn-txt">{{ btnText }}</text>
			</slot>

			<!-- 微信小程序端透明遮罩层：覆盖在按钮上面，捕获点击直接唤起企微插件 -->
			<!-- #ifdef MP-WEIXIN -->
			<view class="wecom-plugin-mask" v-if="url">
				<cell :url="url" @bindstartmessage="onGroupClickStart" @bindcompletemessage="onGroupComplete"></cell>
			</view>
			<!-- #endif -->
		</view>

		<!-- 企微群二维码与信息弹窗（未配置企微URL、插件异常或非微信端时的兜底弹窗） -->
		<uv-popup ref="wecomModal" round="24" mode="center" :closeable="true">
			<view class="wecom-popup-wrap">
				<view class="popup-brand-head acea-row row-middle">
					<image :src="computedLogo" class="brand-popup-logo" mode="aspectFill"></image>
					<view class="brand-popup-info">
						<view class="brand-popup-name text-more">{{ brandName || '品牌粉丝群' }}</view>
						<view class="brand-popup-sub">官方品牌粉丝专属福利群</view>
					</view>
				</view>

				<view class="group-benefits-tag acea-row row-middle row-center">
					<text class="benefit-item">✨ 新品抢先</text>
					<text class="benefit-dot">·</text>
					<text class="benefit-item">🎁 专享福利</text>
					<text class="benefit-dot">·</text>
					<text class="benefit-item">💬 专属客服</text>
				</view>

				<!-- 群二维码展示区 -->
				<view class="group-qrcode-box" @click="previewGroupQr">
					<image :src="computedQr" class="group-qrcode-img" mode="aspectFit"></image>
					<view class="qrcode-tip">点击放大二维码，长按识别加入</view>
				</view>

				<!-- 操作按钮组 -->
				<view class="group-modal-actions">
					<button class="join-group-btn" @click="saveGroupQrcode">
						保存二维码进群
					</button>
					<view class="group-contact-phone" v-if="phone" @click="callPhone">
						门店电话：{{ phone }} (点击拨打)
					</view>
				</view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
export default {
	name: "wecom-group-btn",
	// #ifdef MP-WEIXIN
	usingComponents: {
		"cell": "plugin://materialPlugin/cell"
	},
	// #endif
	props: {
		// 企微群/微信客服链接 URL
		url: {
			type: String,
			default: ""
		},
		// 品牌/门店名称
		brandName: {
			type: String,
			default: ""
		},
		// 品牌/门店 Logo
		logo: {
			type: String,
			default: ""
		},
		// 企微群二维码图片路径
		qrcode: {
			type: String,
			default: ""
		},
		// 门店联系电话
		phone: {
			type: [String, Number],
			default: ""
		},
		// 按钮文字
		btnText: {
			type: String,
			default: "粉丝群"
		},
		// 自定义按钮样式类
		btnClass: {
			type: String,
			default: ""
		},
		// 自定义根样式
		customStyle: {
			type: [Object, String],
			default: () => ({})
		}
	},
	computed: {
		computedLogo() {
			if (this.logo) {
				return this.$util.img(this.logo);
			}
			return "/static/logo.png";
		},
		computedQr() {
			if (this.qrcode) {
				return this.$util.img(this.qrcode);
			}
			if (this.logo) {
				return this.$util.img(this.logo);
			}
			return "/static/logo.png";
		}
	},
	methods: {
		// 企微插件点击事件埋点
		onGroupClickStart(e) {
			console.log("企微插件调起开始", e);
			this.$emit("start", e);
		},
		// 企微插件回调
		onGroupComplete(e) {
			console.log("企微插件完成回调", e);
			this.$emit("complete", e);
			// 如果由于未配置或环境原因失败，则兜底打开弹窗
			if (e && e.detail && e.detail.errcode && e.detail.errcode !== 0) {
				this.openModal();
			}
		},
		// 按钮点击（当无 url 或在非小程序环境下执行）
		handleClick() {
			// #ifdef MP-WEIXIN
			if (this.url) {
				// 有 url 时，微信端上的 cell 遮罩层已捕获点击，无需多余操作
				return;
			}
			// #endif

			if (this.qrcode) {
				this.openModal();
			} else {
				uni.showToast({
					title: "商家正在筹备粉丝群，敬请期待",
					icon: "none"
				});
			}
		},
		// 打开二维码弹窗
		openModal() {
			this.$refs.wecomModal.open();
		},
		// 关闭二维码弹窗
		closeModal() {
			this.$refs.wecomModal.close();
		},
		// 点击预览二维码
		previewGroupQr() {
			if (this.computedQr) {
				uni.previewImage({
					urls: [this.computedQr],
					current: this.computedQr
				});
			}
		},
		// 保存群二维码
		saveGroupQrcode() {
			if (!this.computedQr) {
				uni.showToast({ title: "暂无可保存的二维码", icon: "none" });
				return;
			}
			// #ifdef H5
			uni.previewImage({
				urls: [this.computedQr],
				current: this.computedQr
			});
			uni.showToast({ title: "请长按图片保存到相册", icon: "none" });
			// #endif
			// #ifndef H5
			uni.downloadFile({
				url: this.computedQr,
				success: (res) => {
					if (res.statusCode === 200) {
						uni.saveImageToPhotosAlbum({
							filePath: res.tempFilePath,
							success: () => {
								uni.showToast({ title: "二维码已保存到相册", icon: "success" });
							},
							fail: () => {
								uni.showToast({ title: "保存失败或未授予权限", icon: "none" });
							}
						});
					} else {
						uni.showToast({ title: "下载二维码失败", icon: "none" });
					}
				},
				fail: () => {
					uni.showToast({ title: "下载二维码失败", icon: "none" });
				}
			});
			// #endif
		},
		// 拨打电话
		callPhone() {
			if (!this.phone) return;
			uni.makePhoneCall({
				phoneNumber: String(this.phone)
			});
		}
	}
};
</script>

<style lang="scss" scoped>
.wecom-group-btn-root {
	position: relative;
	display: inline-flex;
	align-items: center;
}

.wecom-trigger-box {
	position: relative;
	overflow: hidden;
	padding: 6rpx 14rpx;
	background: #EDFBF3;
	border-radius: 20rpx;
	border: 1rpx solid #B2E7C6;
	cursor: pointer;
	transition: all 0.2s ease;

	.btn-txt {
		font-size: 22rpx;
		font-weight: 500;
		color: #07C160;
		margin-left: 4rpx;
	}

	&:active {
		opacity: 0.7;
	}
}

/* 透明遮罩层：铺满整个自定义按钮，捕获点击并直接调起企业微信插件 */
.wecom-plugin-mask {
	position: absolute;
	left: 0;
	top: 0;
	right: 0;
	bottom: 0;
	width: 100%;
	height: 100%;
	opacity: 0;
	z-index: 10;
	overflow: hidden;

	cell {
		width: 100% !important;
		height: 100% !important;
		display: block !important;
	}
}

/* 品牌粉丝群兜底弹窗 */
.wecom-popup-wrap {
	width: 620rpx;
	background: #FFFFFF;
	border-radius: 28rpx;
	padding: 40rpx 36rpx;
	box-sizing: border-box;

	.popup-brand-head {
		margin-bottom: 24rpx;

		.brand-popup-logo {
			width: 96rpx;
			height: 96rpx;
			border-radius: 20rpx;
			border: 2rpx solid #F2F2F2;
			margin-right: 20rpx;
			flex-shrink: 0;
		}

		.brand-popup-info {
			flex: 1;
			overflow: hidden;

			.brand-popup-name {
				font-size: 32rpx;
				font-weight: 700;
				color: #222222;
				line-height: 44rpx;
			}

			.brand-popup-sub {
				font-size: 24rpx;
				color: #888888;
				margin-top: 6rpx;
			}
		}
	}

	.group-benefits-tag {
		background: #F8F9FD;
		border-radius: 16rpx;
		padding: 14rpx 20rpx;
		margin-bottom: 30rpx;
		gap: 12rpx;

		.benefit-item {
			font-size: 22rpx;
			color: #555555;
		}

		.benefit-dot {
			color: #CCCCCC;
			font-size: 20rpx;
		}
	}

	.group-qrcode-box {
		width: 380rpx;
		margin: 0 auto 30rpx auto;
		display: flex;
		flex-direction: column;
		align-items: center;

		.group-qrcode-img {
			width: 360rpx;
			height: 360rpx;
			border-radius: 16rpx;
			border: 2rpx solid #ECECEC;
			background: #FAFAFA;
		}

		.qrcode-tip {
			font-size: 22rpx;
			color: #999999;
			margin-top: 14rpx;
			text-align: center;
		}
	}

	.group-modal-actions {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 16rpx;

		.join-group-btn {
			width: 100%;
			height: 80rpx;
			line-height: 80rpx;
			background: linear-gradient(135deg, #07C160 0%, #05A050 100%);
			color: #FFFFFF;
			font-size: 30rpx;
			font-weight: 600;
			border-radius: 40rpx;
			box-shadow: 0 6rpx 16rpx rgba(7, 193, 96, 0.25);
			border: none;
			padding: 0;
			text-align: center;

			&:after {
				border: none;
			}
		}

		.group-contact-phone {
			font-size: 24rpx;
			color: #FF5500;
			text-decoration: underline;
			padding: 6rpx 0;
		}
	}
}
</style>
