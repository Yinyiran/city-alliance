<template>
	<view class="qr-page">
		<!-- 顶部双标签切换 -->
		<view class="tab-header acea-row row-middle row-around">
			<view class="tab-item" :class="{ active: currentTab === 0 }" @click="currentTab = 0">
				<text class="tab-text">多标签二维码海报</text>
				<view class="tab-line" v-if="currentTab === 0"></view>
			</view>
			<view class="tab-item" :class="{ active: currentTab === 1 }" @click="currentTab = 1">
				<text class="tab-text">官方物料实体制作</text>
				<view class="tab-badge" v-if="materialInfo.is_first_free == 1">首次免费</view>
				<view class="tab-line" v-if="currentTab === 1"></view>
			</view>
		</view>

		<!-- ================= TAB 0: 多标签海报管理 ================= -->
		<view class="tab-content" v-if="currentTab === 0">
			<!-- 标签横向滚动栏与新增按钮 -->
			<view class="tags-scroll-wrap m-t-20">
				<scroll-view scroll-x="true" class="tags-scroll">
					<view class="tags-list acea-row row-middle">
						<view 
							class="tag-pill" 
							v-for="(item, idx) in qrList" 
							:key="idx"
							:class="{ 'tag-active': selectedQrIndex === idx }"
							@click="selectQrItem(idx)"
						>
							<text class="tag-name">{{ item.tag_name }}</text>
						</view>
						<view class="tag-add-btn acea-row row-middle" @click="openAddTagModal">
							<uv-icon name="plus" size="12" color="#F55606"></uv-icon>
							<text class="add-txt m-l-4">新建标签码</text>
						</view>
					</view>
				</scroll-view>
			</view>

			<!-- 模板风格选择 -->
			<view class="template-selector acea-row row-middle row-between m-t-24">
				<text class="tpl-title font-24">海报模板风格：</text>
				<view class="tpl-tabs acea-row row-middle">
					<view 
						class="tpl-pill" 
						v-for="(t, idx) in templates" 
						:key="idx"
						:class="{ active: currentTemplate === idx }"
						@click="currentTemplate = idx"
					>
						{{ t.name }}
					</view>
				</view>
			</view>

			<!-- 海报主体卡片 -->
			<view class="poster-card m-t-20" :class="'theme-' + currentTemplate">
				<view class="poster-inner">
					<!-- 商家品牌头 -->
					<view class="shop-header acea-row row-middle">
						<image :src="$util.img(shopInfo.logo)" v-if="shopInfo.logo" class="shop-logo" mode="aspectFill"></image>
						<view class="shop-logo-default acea-row row-middle row-center" v-else>
							<uv-icon name="home" color="#FFFFFF" size="24"></uv-icon>
						</view>
						<view class="shop-meta m-l-16">
							<view class="shop-name font-32 font-weight-6">{{ shopInfo.site_name || '我的店铺' }}</view>
							<view class="shop-sub font-22">同城品质好店 · 诚意相伴</view>
						</view>
					</view>

					<!-- 当前标签横幅 -->
					<view class="tag-banner-display m-t-24 acea-row row-middle row-center">
						<text class="tag-display-text">★ {{ currentQrItem.tag_name || '收银台' }} ★</text>
					</view>

					<!-- 二维码主体区域 -->
					<view class="qr-container m-t-24">
						<uv-qrcode 
							ref="qrcode" 
							:value="currentQrValue" 
							:size="200" 
							:options="qrOptions"
						></uv-qrcode>
					</view>

					<view class="poster-tips m-t-24 acea-row row-column row-center">
						<text class="poster-tip-main">扫码直达店铺 · 尊享会员特权</text>
						<text class="poster-tip-sub m-t-8">到店买单 / 优惠立减 / 积分返还</text>
					</view>
				</view>
			</view>

			<!-- 底部操作按钮 -->
			<view class="action-wrap m-t-32">
				<button class="btn-save" :loading="saving" @click="saveQrcode">
					<uv-icon name="download" color="#FFFFFF" size="18" class="m-r-8"></uv-icon>
					<text class="btn-text">保存海报到手机相册</text>
				</button>
				<button class="btn-material-apply m-t-20" @click="goApplyForCurrentQr">
					<uv-icon name="gift" color="#2A1807" size="18" class="m-r-8"></uv-icon>
					<text class="btn-material-text">为此码申请官方制作实体物料 ({{ materialInfo.is_first_free == 1 ? '首次免费' : '制作定制' }})</text>
				</button>
			</view>
		</view>

		<!-- ================= TAB 1: 官方实体物料制作申领 ================= -->
		<view class="tab-content" v-else>
			<!-- 首单免费特权横幅 -->
			<view class="free-privilege-banner m-t-20" v-if="materialInfo.is_first_free == 1">
				<view class="acea-row row-middle">
					<uv-icon name="gift-fill" size="20" color="#B32400"></uv-icon>
					<text class="free-title m-l-10 font-weight-6">官方赋能：首单实体物料免费赠送！</text>
				</view>
				<view class="free-desc m-t-8">每个商户首次申领亚克力立牌/台卡物料工本费与运费全免，工厂直接包邮到店！</view>
			</view>
			<view class="paid-privilege-banner m-t-20" v-else>
				<view class="acea-row row-middle">
					<uv-icon name="car-fill" size="18" color="#F55606"></uv-icon>
					<text class="paid-title m-l-10 font-weight-6">官方精工物料制作</text>
				</view>
				<view class="paid-desc m-t-8">收费标准透明可配，精工加厚高透亚克力、防水PVC覆膜，极速制作发货。</view>
			</view>

			<!-- 物料品类选择卡片 -->
			<view class="section-title m-t-32 font-28 font-weight-6">选择制作物料品类</view>
			<view class="material-grid m-t-16">
				<view 
					class="material-item" 
					v-for="(item, idx) in materialInfo.list" 
					:key="idx"
					:class="{ active: selectedMaterialIndex === idx }"
					@click="selectedMaterialIndex = idx"
				>
					<view class="material-name font-28 font-weight-6">{{ item.type_name }}</view>
					<view class="material-spec font-22 m-t-8">{{ item.material_spec }}</view>
					<view class="material-price-row acea-row row-middle row-between m-t-16">
						<view class="price-box" v-if="materialInfo.is_first_free == 1">
							<text class="free-price">￥0.00</text>
							<text class="original-price m-l-8">￥{{ item.cost_price }}</text>
						</view>
						<view class="price-box" v-else>
							<text class="real-price">￥{{ item.cost_price }}</text>
							<text class="shipping-fee m-l-8">+运费￥{{ item.shipping_fee }}</text>
						</view>
						<view class="select-radio" :class="{ checked: selectedMaterialIndex === idx }">
							<uv-icon name="checkbox-mark" size="12" color="#fff" v-if="selectedMaterialIndex === idx"></uv-icon>
						</view>
					</view>
				</view>
			</view>

			<!-- 关联印刷二维码标签 -->
			<view class="section-title m-t-32 font-28 font-weight-6">选择印刷到物料上的二维码</view>
			<view class="qr-select-box m-t-16 acea-row row-middle row-between" @click="openQrPicker">
				<text class="selected-qr-name font-28 font-weight-6">{{ currentSelectedPrintQrName }}</text>
				<view class="acea-row row-middle">
					<text class="font-24" style="color: #F55606;">切换码</text>
					<uv-icon name="arrow-right" size="12" color="#999" class="m-l-6"></uv-icon>
				</view>
			</view>

			<!-- 收货地址表单卡片 -->
			<view class="section-title m-t-32 font-28 font-weight-6">物料收件配送信息</view>
			<view class="address-card m-t-16">
				<view class="form-row acea-row row-middle">
					<text class="form-label">收货人</text>
					<input class="form-input" v-model="deliveryForm.contacts_name" placeholder="请输入收货人姓名" />
				</view>
				<view class="form-row acea-row row-middle m-t-20">
					<text class="form-label">联系手机</text>
					<input class="form-input" type="number" maxlength="11" v-model="deliveryForm.contacts_mobile" placeholder="请输入联系电话" />
				</view>
				<view class="form-row acea-row row-top m-t-20">
					<text class="form-label" style="padding-top: 10rpx;">收件地址</text>
					<textarea class="form-textarea" v-model="deliveryForm.address" placeholder="省市区及详细门牌地址（如门店地址）" auto-height></textarea>
				</view>
			</view>

			<!-- 申领提交按钮 -->
			<view class="apply-action-wrap m-t-40">
				<button class="btn-submit-apply" :loading="submittingApply" @click="submitMaterialOrder">
					{{ materialInfo.is_first_free == 1 ? '确认立即免费申领物料 (0元包邮)' : ('确认并在线支付 ￥' + currentMaterialTotalPrice + ' 制作') }}
				</button>
			</view>
		</view>

		<!-- 新建标签码弹窗 -->
		<uv-popup ref="addTagPopup" mode="center" :round="16" customStyle="width: 600rpx; padding: 36rpx; border-radius: 24rpx; background: #fff;">
			<view class="modal-inner">
				<view class="modal-title font-32 font-weight-6 text-center">新建标签二维码</view>
				<view class="modal-tip font-24 text-center m-t-8" style="color: #888;">如: 1号桌 / 收银台 / 外摆区 / 门头海报</view>
				<view class="modal-input-wrap m-t-32">
					<input class="modal-input" v-model="newTagName" placeholder="请输入标签名称 (最多10字)" maxlength="10" />
				</view>
				<view class="modal-btns acea-row row-middle row-between m-t-40">
					<button class="modal-btn-cancel" @click="$refs.addTagPopup.close()">取消</button>
					<button class="modal-btn-confirm" :loading="addingTag" @click="confirmAddTag">确认生成</button>
				</view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
import { 
	getShopQrcode,
	getShopQrcodeList,
	addShopQrcode,
	getMaterialTypeList,
	applyMaterialOrder
} from '@/common/js/api.js';

export default {
	data() {
		return {
			site_id: '',
			currentTab: 0,
			saving: false,
			submittingApply: false,
			addingTag: false,
			selectedQrIndex: 0,
			selectedMaterialIndex: 0,
			currentTemplate: 0,
			templates: [
				{ name: '极简商务白' },
				{ name: '华贵黑金' },
				{ name: '活力橙红' }
			],
			shopInfo: {
				site_id: '',
				site_name: '',
				logo: '',
				qrcode_url: ''
			},
			qrList: [],
			materialInfo: {
				is_first_free: 1,
				list: []
			},
			deliveryForm: {
				contacts_name: '',
				contacts_mobile: '',
				address: ''
			},
			newTagName: '',
			qrOptions: {
				errorCorrectLevel: 'H',
				margin: 10
			}
		};
	},
	computed: {
		currentQrItem() {
			if (this.qrList && this.qrList.length > this.selectedQrIndex) {
				return this.qrList[this.selectedQrIndex];
			}
			return { tag_name: '收银台', scene_code: '' };
		},
		currentQrValue() {
			let id = this.site_id || uni.getStorageSync('site_id') || '';
			let scene = (this.currentQrItem && this.currentQrItem.scene_code) || '';
			return `pages/index/index?site_id=${id}&scene=${scene}`;
		},
		currentSelectedPrintQrName() {
			return this.currentQrItem ? this.currentQrItem.tag_name : '默认主码';
		},
		currentMaterialTotalPrice() {
			let list = this.materialInfo.list || [];
			if (list.length > this.selectedMaterialIndex) {
				let m = list[this.selectedMaterialIndex];
				let total = parseFloat(m.cost_price || 0) + parseFloat(m.shipping_fee || 0);
				return total.toFixed(2);
			}
			return '0.00';
		}
	},
	onLoad() {
		this.site_id = uni.getStorageSync('site_id') || '';
		this.initData();
	},
	methods: {
		async initData() {
			try {
				let qrRes = await getShopQrcode({ site_id: this.site_id });
				if (qrRes && qrRes.code >= 0 && qrRes.data) {
					this.shopInfo = qrRes.data;
				}
			} catch (e) {}

			this.fetchQrList();
			this.fetchMaterialTypes();
		},
		fetchQrList() {
			getShopQrcodeList({ site_id: this.site_id }).then(res => {
				if (res && res.code === 0 && res.data) {
					this.qrList = res.data;
				}
			}).catch(err => {
				console.error('获取标签二维码列表失败', err);
			});
		},
		fetchMaterialTypes() {
			getMaterialTypeList({ site_id: this.site_id }).then(res => {
				if (res && res.code === 0 && res.data) {
					this.materialInfo = res.data;
				}
			}).catch(err => {
				console.error('获取物料列表失败', err);
			});
		},
		selectQrItem(idx) {
			this.selectedQrIndex = idx;
		},
		openAddTagModal() {
			this.newTagName = '';
			this.$refs.addTagPopup.open();
		},
		confirmAddTag() {
			if (!this.newTagName.trim()) {
				uni.showToast({ title: '请输入标签名称', icon: 'none' });
				return;
			}
			this.addingTag = true;
			addShopQrcode({
				site_id: this.site_id,
				tag_name: this.newTagName.trim()
			}).then(res => {
				this.addingTag = false;
				if (res.code === 0) {
					uni.showToast({ title: '创建成功', icon: 'success' });
					this.$refs.addTagPopup.close();
					this.fetchQrList();
					this.selectedQrIndex = 0;
				} else {
					uni.showToast({ title: res.message || '创建失败', icon: 'none' });
				}
			}).catch(() => {
				this.addingTag = false;
			});
		},
		saveQrcode() {
			if (this.saving) return;
			if (!this.$refs.qrcode) {
				uni.showToast({ title: '正在渲染海报...', icon: 'none' });
				return;
			}
			this.saving = true;
			uni.showLoading({ title: '保存海报中...' });
			this.$refs.qrcode.save({
				success: () => {
					uni.hideLoading();
					this.saving = false;
					uni.showToast({ title: '已保存至相册', icon: 'success' });
				},
				fail: () => {
					uni.hideLoading();
					this.saving = false;
					uni.showToast({ title: '保存失败，请检查相册权限', icon: 'none' });
				}
			});
		},
		goApplyForCurrentQr() {
			this.currentTab = 1;
		},
		openQrPicker() {
			let names = (this.qrList || []).map(q => q.tag_name);
			if (!names.length) return;
			uni.showActionSheet({
				itemList: names,
				success: (res) => {
					this.selectedQrIndex = res.tapIndex;
				}
			});
		},
		submitMaterialOrder() {
			let list = this.materialInfo.list || [];
			if (!list.length) {
				uni.showToast({ title: '暂无可用物料', icon: 'none' });
				return;
			}
			let selectedMat = list[this.selectedMaterialIndex];
			if (!selectedMat) return;

			if (!this.deliveryForm.contacts_name.trim() || !this.deliveryForm.contacts_mobile.trim() || !this.deliveryForm.address.trim()) {
				uni.showToast({ title: '请完整填写收货姓名、电话和地址', icon: 'none' });
				return;
			}

			let isFree = (this.materialInfo.is_first_free == 1);
			let confirmMsg = isFree
				? `确定以首单免费特权申领【${selectedMat.type_name}】吗？`
				: `确认申领【${selectedMat.type_name}】，工本及运费合计 ￥${this.currentMaterialTotalPrice}？`;

			uni.showModal({
				title: '确认提交物料制作申领',
				content: confirmMsg,
				success: (r) => {
					if (r.confirm) {
						this.doApplyOrder(selectedMat.type_id);
					}
				}
			});
		},
		doApplyOrder(typeId) {
			this.submittingApply = true;
			applyMaterialOrder({
				site_id: this.site_id,
				type_id: typeId,
				qrcode_id: this.currentQrItem ? this.currentQrItem.id : 0,
				contacts_name: this.deliveryForm.contacts_name.trim(),
				contacts_mobile: this.deliveryForm.contacts_mobile.trim(),
				address: this.deliveryForm.address.trim()
			}).then(res => {
				this.submittingApply = false;
				if (res.code === 0) {
					uni.showModal({
						title: '物料申领成功！',
						content: res.message || '我们已接收您的官方物料定制申请，将加紧制作并快递发货！',
						showCancel: false,
						success: () => {
							this.fetchMaterialTypes();
							this.currentTab = 0;
						}
					});
				} else {
					uni.showToast({ title: res.message || '申领失败', icon: 'none' });
				}
			}).catch(() => {
				this.submittingApply = false;
				uni.showToast({ title: '网络异常，请重试', icon: 'none' });
			});
		}
	}
};
</script>

<style lang="scss">
page {
	background: #F7F8FA;
	padding-bottom: 80rpx;
}

.tab-header {
	background: #ffffff;
	padding: 24rpx 0 16rpx 0;
	position: sticky;
	top: 0;
	z-index: 10;
	box-shadow: 0 2rpx 10rpx rgba(0,0,0,0.03);

	.tab-item {
		position: relative;
		padding: 10rpx 20rpx;

		.tab-text {
			font-size: 28rpx;
			color: #666;
		}

		&.active {
			.tab-text {
				font-size: 30rpx;
				font-weight: 600;
				color: #222;
			}
		}

		.tab-line {
			position: absolute;
			bottom: -8rpx;
			left: 50%;
			transform: translateX(-50%);
			width: 48rpx;
			height: 6rpx;
			background: #F55606;
			border-radius: 4rpx;
		}

		.tab-badge {
			position: absolute;
			top: -4rpx;
			right: -16rpx;
			background: #FA5151;
			color: #fff;
			font-size: 18rpx;
			padding: 2rpx 8rpx;
			border-radius: 12rpx;
			font-weight: 600;
		}
	}
}

.tab-content {
	padding: 24rpx 32rpx;
}

.tags-scroll-wrap {
	.tags-scroll {
		white-space: nowrap;
		width: 100%;
	}

	.tags-list {
		display: inline-flex;
		align-items: center;

		.tag-pill {
			display: inline-block;
			background: #ffffff;
			border: 2rpx solid #E2E4E8;
			border-radius: 32rpx;
			padding: 12rpx 24rpx;
			margin-right: 16rpx;
			font-size: 24rpx;
			color: #666;

			&.tag-active {
				background: #FFF0E6;
				border-color: #F55606;
				color: #F55606;
				font-weight: 600;
			}
		}

		.tag-add-btn {
			display: inline-flex;
			background: #fff;
			border: 2rpx dashed #F55606;
			border-radius: 32rpx;
			padding: 12rpx 24rpx;
			font-size: 24rpx;
			color: #F55606;
		}
	}
}

.template-selector {
	.tpl-title {
		color: #888;
	}

	.tpl-tabs {
		.tpl-pill {
			font-size: 22rpx;
			color: #666;
			background: #fff;
			padding: 8rpx 18rpx;
			border-radius: 20rpx;
			margin-left: 12rpx;
			border: 1rpx solid #eee;

			&.active {
				background: #222;
				color: #fff;
				font-weight: 600;
			}
		}
	}
}

.poster-card {
	background: #fff;
	border-radius: 28rpx;
	padding: 40rpx 32rpx;
	box-shadow: 0 8rpx 32rpx rgba(0,0,0,0.06);

	&.theme-0 {
		background: #ffffff;
		border: 1rpx solid #ECEEF2;
	}
	&.theme-1 {
		background: linear-gradient(135deg, #242220 0%, #151413 100%);
		color: #fff;
		.shop-name { color: #FAD5B4; }
		.shop-sub { color: #B2A292; }
		.tag-banner-display { background: rgba(245, 182, 127, 0.15); border-color: #F5B67F; .tag-display-text { color: #F5B67F; } }
		.poster-tip-main { color: #FAD5B4; }
		.poster-tip-sub { color: #8E929E; }
	}
	&.theme-2 {
		background: linear-gradient(135deg, #FFF5F0 0%, #FFFDFB 100%);
		border: 2rpx solid #FFD6C2;
		.tag-banner-display { background: #FFEEE6; border-color: #F55606; .tag-display-text { color: #F55606; } }
	}

	.shop-logo {
		width: 88rpx;
		height: 88rpx;
		border-radius: 16rpx;
	}
	.shop-logo-default {
		width: 88rpx;
		height: 88rpx;
		border-radius: 16rpx;
		background: #F55606;
	}

	.tag-banner-display {
		background: #FFF5EE;
		border: 1rpx solid #FFD8C2;
		border-radius: 12rpx;
		padding: 14rpx 0;

		.tag-display-text {
			font-size: 26rpx;
			font-weight: 700;
			color: #F55606;
			letter-spacing: 2rpx;
		}
	}

	.qr-container {
		display: flex;
		justify-content: center;
		padding: 24rpx 0;
	}

	.poster-tips {
		text-align: center;
		.poster-tip-main {
			font-size: 28rpx;
			font-weight: 600;
			color: #333;
		}
		.poster-tip-sub {
			font-size: 22rpx;
			color: #999;
		}
	}
}

.action-wrap {
	.btn-save {
		background: #F55606;
		color: #fff;
		border-radius: 44rpx;
		font-size: 30rpx;
		font-weight: 600;
		height: 88rpx;
		line-height: 88rpx;
	}
	.btn-material-apply {
		background: linear-gradient(135deg, #FAD5B4 0%, #F5B67F 100%);
		color: #2A1807;
		border-radius: 44rpx;
		font-size: 26rpx;
		font-weight: 600;
		height: 80rpx;
		line-height: 80rpx;
	}
}

.free-privilege-banner {
	background: #FFF0E6;
	border: 1rpx solid #FFD8C2;
	border-radius: 20rpx;
	padding: 24rpx;

	.free-title {
		font-size: 28rpx;
		color: #B32400;
	}
	.free-desc {
		font-size: 22rpx;
		color: #994D33;
		line-height: 1.5;
	}
}

.paid-privilege-banner {
	background: #ffffff;
	border-radius: 20rpx;
	padding: 24rpx;
	box-shadow: 0 4rpx 16rpx rgba(0,0,0,0.03);

	.paid-title {
		font-size: 28rpx;
		color: #222;
	}
	.paid-desc {
		font-size: 22rpx;
		color: #888;
	}
}

.material-grid {
	.material-item {
		background: #ffffff;
		border: 2rpx solid #EBECEF;
		border-radius: 20rpx;
		padding: 24rpx;
		margin-bottom: 20rpx;

		&.active {
			border-color: #F55606;
			background: #FFFBF9;
		}

		.material-spec {
			color: #888;
		}

		.free-price {
			color: #FA5151;
			font-size: 32rpx;
			font-weight: 700;
		}
		.original-price {
			color: #bbb;
			font-size: 22rpx;
			text-decoration: line-through;
		}
		.real-price {
			color: #F55606;
			font-size: 32rpx;
			font-weight: 700;
		}
		.shipping-fee {
			color: #888;
			font-size: 22rpx;
		}

		.select-radio {
			width: 36rpx;
			height: 36rpx;
			border-radius: 50%;
			border: 2rpx solid #ccc;
			display: flex;
			align-items: center;
			justify-content: center;

			&.checked {
				background: #F55606;
				border-color: #F55606;
			}
		}
	}
}

.qr-select-box {
	background: #ffffff;
	border-radius: 16rpx;
	padding: 24rpx;
}

.address-card {
	background: #ffffff;
	border-radius: 20rpx;
	padding: 28rpx;

	.form-row {
		.form-label {
			width: 140rpx;
			font-size: 28rpx;
			color: #333;
		}
		.form-input {
			flex: 1;
			font-size: 28rpx;
			color: #222;
		}
		.form-textarea {
			flex: 1;
			font-size: 28rpx;
			color: #222;
			min-height: 80rpx;
		}
	}
}

.apply-action-wrap {
	.btn-submit-apply {
		background: #F55606;
		color: #ffffff;
		border-radius: 44rpx;
		font-size: 30rpx;
		font-weight: 600;
		height: 88rpx;
		line-height: 88rpx;
	}
}

.modal-input-wrap {
	background: #f7f8fa;
	border-radius: 12rpx;
	padding: 16rpx 20rpx;

	.modal-input {
		font-size: 28rpx;
		color: #222;
	}
}

.modal-btns {
	.modal-btn-cancel {
		width: 46%;
		background: #f5f5f5;
		color: #666;
		font-size: 28rpx;
		border-radius: 36rpx;
		height: 72rpx;
		line-height: 72rpx;
	}
	.modal-btn-confirm {
		width: 46%;
		background: #F55606;
		color: #fff;
		font-size: 28rpx;
		border-radius: 36rpx;
		height: 72rpx;
		line-height: 72rpx;
	}
}
</style>
