<template>
	<view>
		<!-- <uv-sticky> -->
		<nvabar title='同城商家'></nvabar>
		<!-- </uv-sticky> -->
		<image src="/static/bg1.png" class="bgs" mode=""></image>
		<view class="part1">
			<image src="/otherpages/static/bg6.png" mode=""></image>
			<image src="/static/t.png" mode=""></image>
			<button open-type="contact" class="consult-btn">入驻咨询</button>
			<view class="">{{ shop_num }}家</view>
		</view>
		<view class="acea-row row-middle parts">
			<view class="brand-item" v-for="(brand, index) in brandGroupList" :key="index" @click="onBrandClick(brand)">
				<image :src="$util.img(brand.logo)" mode="aspectFit"></image>
				<!-- 连锁多门店角标 -->
				<view class="store-badge" v-if="brand.storeCount > 1">{{ brand.storeCount }}家</view>
			</view>
		</view>
		<view class="container">
			<view class="acea-row row-middle row-center m-b-32">
				<image src="/otherpages/static/lline.png" mode=""></image>
				<text>城盟联创</text>
				<image src="/otherpages/static/rline.png" mode=""></image>
			</view>
			<video object-fit="contain" v-if="info.video_url_info" :src="$util.img(info.video_url_info)"></video>
			<rich-text :nodes="$util.replaceImg(info.content_info)"></rich-text>
		</view>

		<!-- 多连锁门店选择弹窗 -->
		<uv-popup ref="storePopup" mode="bottom" :round="16" :closeable="true" :safeAreaInsetBottom="true">
			<view class="store-popup-content">
				<view class="popup-title">
					<text class="brand-title">{{ currentBrand.brandName }}</text>
					<text class="sub-title">连锁门店（共{{ currentBrand.storeCount }}家分店）</text>
				</view>
				<scroll-view scroll-y class="store-list-scroll">
					<view class="store-list-item acea-row row-middle row-between"
						v-for="(store, sIdx) in currentBrand.storeList" :key="sIdx"
						@click="goStoreDetail(store)">
						<view class="store-info">
							<view class="store-item-name font-30 font-weight-6">{{ store.store_name }}</view>
							<view class="store-item-address font-24" v-if="store.address">{{ store.address }}</view>
						</view>
						<view class="store-enter-btn">进店</view>
					</view>
				</scroll-view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
import { websiteInfo, Synopsis, websiteStore } from "@/common/js/api.js"
export default {
	data() {
		return {
			shop_num: 0,
			info: {},
			list: [],
			currentBrand: {
				brandName: '',
				storeCount: 0,
				storeList: []
			}
		};
	},
	computed: {
		// 聚合后的品牌列表
		brandGroupList() {
			const groups = {};
			(this.list || []).forEach(store => {
				if (!store || !store.store_logo) return;

				const brandKey = this.getBrandKey(store);
				const key = brandKey || store.store_logo;

				if (!groups[key]) {
					groups[key] = {
						brandKey: key,
						brandName: brandKey || store.store_name,
						logo: store.store_logo,
						storeCount: 0,
						storeList: []
					};
				}
				groups[key].storeCount++;
				groups[key].storeList.push(store);
			});
			return Object.values(groups);
		}
	},
	onLoad() {
		this.Synopsis()
		this.getinfo()
		this.websiteStore()
	},
	methods: {
		// 核心品牌提取清洗算法（兼容中英文括号）
		getBrandKey(store) {
			// 1. 若接口已带品牌名或企业名，优先采用
			if (store.site_name && store.site_name.trim()) return store.site_name.trim();
			if (store.brand_name && store.brand_name.trim()) return store.brand_name.trim();

			let name = (store.store_name || '').trim();
			if (!name) return store.store_logo || '';

			// 2. 清除成对的中英文括号及括号内的分店说明，如：蜜雪冰城(万达店) / 蜜雪冰城（万达店） -> 蜜雪冰城
			name = name.replace(/[\(（].*?[\)）]/g, '').trim();

			// 3. 兼容未闭合的中英文左括号或中划线左侧以外内容，如：蜜雪冰城(万达 / 蜜雪冰城-万达店
			name = name.split(/[\(（\-—]/)[0].trim();

			// 4. 清除末尾常见的分店/店缀
			name = name.replace(/(总店|分店|旗舰店|专卖店|连锁店|加盟店|店)$/, '').trim();

			return name || store.store_name || store.store_logo;
		},
		onBrandClick(brand) {
			if (brand.storeCount === 1) {
				// 仅有1家店，直接跳转门店详情
				this.goStoreDetail(brand.storeList[0]);
			} else {
				// 多家连锁店，弹出列表供用户选择
				this.currentBrand = brand;
				this.$refs.storePopup.open();
			}
		},
		goStoreDetail(storeOrId) {
			let storeId = storeOrId;
			if (typeof storeOrId === 'object' && storeOrId !== null) {
				storeId = storeOrId.store_id || storeOrId.id || storeOrId.site_id;
			}
			if (!storeId) {
				console.warn('未获取到有效门店ID', storeOrId);
				return;
			}
			if (this.$refs.storePopup) {
				this.$refs.storePopup.close();
			}
			uni.navigateTo({
				url: '/otherpages/storeDetail/storeDetail?id=' + storeId
			});
		},
		websiteStore() {
			websiteStore().then(res => {
				console.log(res);
				this.list = res.data
			})
		},
		Synopsis() {
			Synopsis().then(res => {
				this.info = res.data
			})
		},
		getinfo() {
			let params = {
				website_id: uni.getStorageSync('website_id')
			}
			websiteInfo(params).then(res => {
				this.shop_num = res.data.shop_num
			})
		}
	}
}
</script>

<style lang="scss">
.parts {
	background: #fff;
	margin: 32rpx;
	padding: 28rpx;
	border-radius: 16rpx;

	>view {
		width: calc((100% - 36rpx)/4);
		height: 52rpx;
		border: 3rpx #F0F0F0 solid;
		margin-right: 12rpx;
		margin-top: 12rpx;
		box-sizing: border-box;
		text-align: center;
		position: relative;

		image {
			width: 100%;
			height: 100%;
		}
	}

	>view:nth-child(4n) {
		margin-right: 0;
	}

	.brand-item {
		position: relative;

		.store-badge {
			position: absolute;
			top: -10rpx;
			right: -10rpx;
			background: linear-gradient(135deg, #FF6B35, #FF3B30);
			color: #fff;
			font-size: 18rpx;
			padding: 0 8rpx;
			height: 26rpx;
			line-height: 26rpx;
			border-radius: 13rpx;
			font-weight: 500;
			box-shadow: 0 2rpx 6rpx rgba(255, 59, 48, 0.35);
			z-index: 2;
			white-space: nowrap;
		}
	}
}

page {
	background: #FFFFFF;
}

.container {
	margin: 32rpx;
	border-radius: 24rpx;

	// padding: 32rpx;
	video {
		width: 100%;
		height: 386rpx;
	}

	>view:first-child {
		text {
			font-weight: 600;
			font-size: 32rpx;
			margin: 0 24rpx;
			color: #222222;
		}

		image {
			width: 129rpx;
			height: 4rpx;
		}
	}
}

.bgs {
	position: absolute;
	width: 100vw;
	height: 634rpx;
	top: 0;
	left: 0;
	z-index: -1;
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

.store-popup-content {
	background: #fff;
	border-radius: 32rpx 32rpx 0 0;
	padding: 32rpx;
	max-height: 70vh;

	.popup-title {
		margin-bottom: 28rpx;
		padding-right: 48rpx;

		.brand-title {
			font-size: 34rpx;
			font-weight: 600;
			color: #222;
			display: block;
		}

		.sub-title {
			font-size: 24rpx;
			color: #888;
			margin-top: 6rpx;
			display: block;
		}
	}

	.store-list-scroll {
		max-height: 55vh;
	}

	.store-list-item {
		padding: 24rpx 20rpx;
		background: #F8F9FA;
		border-radius: 16rpx;
		margin-bottom: 16rpx;

		&:active {
			background: #F0F2F5;
		}

		.store-info {
			flex: 1;
			margin-right: 20rpx;

			.store-item-name {
				color: #222;
				line-height: 1.4;
			}

			.store-item-address {
				color: #999;
				margin-top: 8rpx;
				line-height: 1.3;
			}
		}

		.store-enter-btn {
			background: linear-gradient(135deg, #FF9500, #FF5B00);
			color: #fff;
			font-size: 24rpx;
			padding: 10rpx 24rpx;
			border-radius: 28rpx;
			font-weight: 500;
			flex-shrink: 0;
		}
	}
}
</style>
