<template>
	<view class="store-home-wrap">
		<!-- 自定义导航栏（Tab页模式展示） -->
		<uv-navbar v-if="isTab" bg-color="#fff" :fixed="true" :placeholder="true">
			<template v-slot:left>
				<view class="nav-store-title acea-row row-middle" @click="openStoreSelect">
					<image src="/static/stores.png" class="w-32 h-32 m-r-8" mode="aspectFit"></image>
					<text class="store-name-text text-more">{{ sinfo.store_name || '优选商家' }}</text>
					<text class="store-switch-arrow" v-if="storeList.length > 1">▾</text>
				</view>
			</template>
		</uv-navbar>

		<!-- 详情页模式下带有返回按钮的导航栏 -->
		<uv-navbar v-else bg-color="#ffffff00" :fixed="true" :placeholder="true" left-icon-color="#ffffff" @leftClick="goBack">
			<template v-slot:center>
				<text class="navbar-center-title">{{ sinfo.store_name || '门店详情' }}</text>
			</template>
		</uv-navbar>

		<!-- 1. 顶部 Banner 轮播区 -->
		<view class="banner-section">
			<swiper 
				class="store-swiper" 
				:indicator-dots="false" 
				:autoplay="bannerList.length > 1" 
				:interval="3500" 
				:duration="600" 
				circular
				@change="onBannerChange"
				v-if="bannerList.length > 0"
			>
				<swiper-item v-for="(item, index) in bannerList" :key="index" @click="onBannerClick(item)">
					<view class="swiper-image-box">
						<image :src="$util.img(item.image)" mode="aspectFill" class="banner-img"></image>
					</view>
				</swiper-item>
			</swiper>

			<!-- 缺省Banner -->
			<view class="swiper-image-box" v-else>
				<image :src="sinfo.store_image ? $util.img(sinfo.store_image) : '/static/bg1.png'" mode="aspectFill" class="banner-img"></image>
			</view>

			<!-- 自定义轮播指示点 -->
			<view class="banner-dots" v-if="bannerList.length > 1">
				<view 
					v-for="(item, index) in bannerList" 
					:key="index" 
					:class="['dot-item', { 'active': currentBannerIndex === index }]"
				></view>
			</view>
		</view>

		<!-- 2. Banner下方紧接着一行：门店粉丝量、累计销量、关注门店 -->
		<view class="store-stats-card">
			<view class="store-main-row acea-row row-middle row-between">
				<!-- 门店头像与基本信息 -->
				<view class="store-header-left acea-row row-middle">
					<image :src="sinfo.store_image ? $util.img(sinfo.store_image) : (sinfo.shop_logo ? $util.img(sinfo.shop_logo) : '/static/logo.png')" class="store-avatar" mode="aspectFill"></image>
					<view class="store-name-col">
						<view class="store-title acea-row row-middle">
							<text class="title-text text-more">{{ sinfo.store_name || '商家门店' }}</text>
						</view>
						<view class="store-address-text text-more" v-if="sinfo.full_address || sinfo.address">
							{{ sinfo.full_address || sinfo.address }}
						</view>
					</view>
				</view>

				<!-- 切换门店按钮（若有多门店且是Tab模式） -->
				<view class="switch-store-btn" v-if="isTab && storeList.length > 1" @click="openStoreSelect">
					<image src="/static/icon5.png" class="switch-icon" mode=""></image>
					<text>换店</text>
				</view>
			</view>

			<!-- 核心指标行：门店粉丝量、累计销量、关注门店 -->
			<view class="stats-action-row acea-row row-middle row-between">
				<!-- 门店粉丝量 -->
				<view class="stat-item">
					<view class="stat-num">{{ sinfo.total_num || 0 }}</view>
					<view class="stat-label">门店粉丝</view>
				</view>

				<view class="stat-divider"></view>

				<!-- 累计销量 -->
				<view class="stat-item">
					<view class="stat-num">{{ displaySales }}</view>
					<view class="stat-label">累计销量</view>
				</view>

				<view class="stat-divider"></view>

				<!-- 关注门店按钮 -->
				<view class="follow-action-col">
					<view 
						class="follow-btn acea-row row-middle row-center" 
						:class="{ 'followed': guanzhu != 0 }" 
						@click="toggleFollow"
					>
						<image src="/static/heart.png" v-if="guanzhu == 0" class="heart-icon" mode="aspectFit"></image>
						<image src="/static/heart1.png" v-else class="heart-icon" mode="aspectFit"></image>
						<text>{{ guanzhu == 0 ? '关注门店' : '已关注' }}</text>
					</view>
				</view>
			</view>
		</view>

		<!-- 3. 商品列表与顶部过滤行 -->
		<view class="goods-container">
			<!-- 顶部过滤栏：商品分类（下拉选择）、销量、距离 -->
			<view class="filter-bar acea-row row-middle row-between">
				<!-- 商品分类（下拉选择） -->
				<view class="filter-item category-select-item acea-row row-middle" @click="toggleCategoryDropdown">
					<text class="filter-text text-more" :class="{ 'filter-active': currentCategoryId !== '' }">
						{{ currentCategoryName || '商品分类' }}
					</text>
					<uv-icon 
						name="arrow-down-fill" 
						:color="currentCategoryId !== '' ? '#F55606' : '#666'" 
						size="9"
						:custom-style="{ transform: showCategoryDropdown ? 'rotate(180deg)' : 'rotate(0deg)', transition: 'transform 0.2s ease' }"
					></uv-icon>
				</view>

				<!-- 销量排序 -->
				<view class="filter-item acea-row row-middle row-center" @click="toggleSort('sale_num')">
					<text class="filter-text" :class="{ 'filter-active': currentSortType === 'sale_num' }">销量</text>
					<view class="sort-arrows">
						<uv-icon 
							name="arrow-up-fill" 
							:color="currentSortType === 'sale_num' && sortOrder === 'asc' ? '#F55606' : '#C0C4CC'" 
							size="7"
						></uv-icon>
						<uv-icon 
							name="arrow-down-fill" 
							:color="currentSortType === 'sale_num' && sortOrder === 'desc' ? '#F55606' : '#C0C4CC'" 
							size="7"
						></uv-icon>
					</view>
				</view>

				<!-- 距离排序 -->
				<view class="filter-item acea-row row-middle row-center" @click="toggleSort('distance')">
					<text class="filter-text" :class="{ 'filter-active': currentSortType === 'distance' }">距离</text>
					<view class="sort-arrows">
						<uv-icon 
							name="arrow-up-fill" 
							:color="currentSortType === 'distance' && sortOrder === 'asc' ? '#F55606' : '#C0C4CC'" 
							size="7"
						></uv-icon>
						<uv-icon 
							name="arrow-down-fill" 
							:color="currentSortType === 'distance' && sortOrder === 'desc' ? '#F55606' : '#C0C4CC'" 
							size="7"
						></uv-icon>
					</view>
				</view>
			</view>

			<!-- 分类下拉选择弹出层 -->
			<view class="category-dropdown-panel" v-if="showCategoryDropdown">
				<view class="dropdown-backdrop" @click="showCategoryDropdown = false"></view>
				<view class="dropdown-content">
					<scroll-view scroll-y class="dropdown-scroll">
						<!-- 全部分类 -->
						<view 
							class="category-option acea-row row-middle row-between" 
							:class="{ 'selected': currentCategoryId === '' }" 
							@click="selectCategory('', '全部分类')"
						>
							<text>全部分类</text>
							<uv-icon name="checkbox-mark" color="#F55606" size="14" v-if="currentCategoryId === ''"></uv-icon>
						</view>

						<!-- 具体分类项 -->
						<view 
							v-for="(cat, catIdx) in categoryList" 
							:key="catIdx" 
							class="category-option acea-row row-middle row-between"
							:class="{ 'selected': currentCategoryId == cat.category_id }"
							@click="selectCategory(cat.category_id, cat.category_name || cat.short_name)"
						>
							<text>{{ cat.category_name || cat.short_name }}</text>
							<uv-icon name="checkbox-mark" color="#F55606" size="14" v-if="currentCategoryId == cat.category_id"></uv-icon>
						</view>
					</scroll-view>
				</view>
			</view>

			<!-- 4. 商品列表：两列双列卡片显示 -->
			<view class="goods-grid" v-if="goodsList.length > 0">
				<view 
					class="goods-card" 
					v-for="(item, index) in goodsList" 
					:key="index" 
					@click="goGoodsDetail(item.sku_id || item.goods_id)"
				>
					<!-- 商品主图 -->
					<view class="goods-img-box">
						<image :src="$util.img(item.sku_image || item.goods_image)" mode="aspectFill" class="goods-img" lazy-load></image>
						<!-- 浏览量角标 -->
						<view class="click-badge acea-row row-middle" v-if="item.click_num">
							<uv-icon name="eye" size="10" color="#fff"></uv-icon>
							<text class="m-l-4">{{ item.click_num }}</text>
						</view>
						<!-- 特惠/核销标签 -->
						<view class="tag-badge">门店核销</view>
					</view>

					<!-- 商品详情说明 -->
					<view class="goods-info-box">
						<view class="goods-title text-more-2">{{ item.goods_name || item.sku_name }}</view>
						
						<!-- 距离与销量标签行 -->
						<view class="goods-meta-row acea-row row-middle">
							<view class="distance-tag" v-if="item.distance">
								<uv-icon name="map" size="10" color="#999"></uv-icon>
								<text>{{ item.distance }}km</text>
							</view>
							<text class="sales-tag">已售{{ item.sale_num || 0 }}件</text>
						</view>

						<!-- 价格与抢购按钮 -->
						<view class="price-action-row acea-row row-middle row-between">
							<view class="price-box acea-row row-bottom">
								<text class="price-symbol">￥</text>
								<text class="price-integer">{{ getPricePart(item.price, 0) }}</text>
								<text class="price-decimal" v-if="getPricePart(item.price, 1)">.{{ getPricePart(item.price, 1) }}</text>
								<text class="market-price" v-if="item.market_price && item.market_price != '0.00'">¥{{ item.market_price }}</text>
							</view>
							<view class="buy-action-btn acea-row row-middle row-center">
								抢
							</view>
						</view>
					</view>
				</view>
			</view>

			<!-- 空数据提示 -->
			<view class="empty-state" v-else-if="!loading">
				<image src="/static/empty.png" class="empty-img" mode="aspectFit"></image>
				<text class="empty-tip">暂无相关商品</text>
				<view class="reset-filter-btn" v-if="currentCategoryId !== '' || currentSortType !== ''" @click="resetFilter">
					重置筛选条件
				</view>
			</view>

			<!-- 加载状态 -->
			<view class="loading-more acea-row row-middle row-center" v-if="goodsList.length > 0">
				<text v-if="loading">加载中...</text>
				<text v-else-if="page >= totalPage" class="no-more">—— 已经到底啦 ——</text>
			</view>
		</view>

		<!-- 门店切换弹窗 -->
		<uv-popup ref="storeSelectPopup" round="24" mode="bottom">
			<view class="store-popup-container">
				<view class="popup-title acea-row row-middle row-between">
					<text class="popup-title-text">切换门店</text>
					<uv-icon name="close" size="16" color="#999" @click="closeStoreSelect"></uv-icon>
				</view>
				<scroll-view scroll-y class="store-select-scroll">
					<view 
						v-for="(store, sIdx) in storeList" 
						:key="sIdx" 
						class="store-option-item acea-row row-middle row-between"
						:class="{ 'active': currentStoreId == (store.store_id || store.id) }"
						@click="onSelectStore(store)"
					>
						<view class="store-opt-left acea-row row-middle">
							<image :src="store.store_image ? $util.img(store.store_image) : '/static/stores.png'" class="store-opt-img" mode="aspectFill"></image>
							<view class="store-opt-info">
								<view class="store-opt-name text-more">{{ store.store_name }}</view>
								<view class="store-opt-addr text-more">{{ store.full_address || store.address }}</view>
							</view>
						</view>
						<uv-icon name="checkmark-circle-fill" color="#F55606" size="20" v-if="currentStoreId == (store.store_id || store.id)"></uv-icon>
					</view>
				</scroll-view>
			</view>
		</uv-popup>

		<!-- 登录弹窗 -->
		<login ref="login"></login>
	</view>
</template>

<script>
	import {
		storeDetail,
		issubscribe,
		shopmemberAdd,
		shopmemberDel,
		goodslist,
		websiteStore,
		siteCategoryList
	} from "@/common/js/api.js";

	export default {
		name: "store-home",
		props: {
			// 外部传入的门店ID（storeDetail页面会传入）
			storeId: {
				type: [String, Number],
				default: ""
			},
			// 是否为Tab栏首页模式
			isTab: {
				type: Boolean,
				default: false
			}
		},
		data() {
			return {
				currentStoreId: "",
				sinfo: {},
				bannerList: [],
				currentBannerIndex: 0,
				guanzhu: 0,
				
				// 分类筛选
				categoryList: [],
				currentCategoryId: "",
				currentCategoryName: "商品分类",
				showCategoryDropdown: false,

				// 排序筛选：sortType: 'sale_num' | 'distance' | ''
				currentSortType: "",
				sortOrder: "desc", // 'desc' | 'asc'

				// 商品列表分页
				goodsList: [],
				page: 1,
				pageSize: 10,
				totalPage: 1,
				loading: false,

				// 门店列表（用于Tab模式换店）
				storeList: [],

				// 用户位置
				latitude: null,
				longitude: null
			};
		},
		computed: {
			displaySales() {
				if (this.sinfo.total_sales !== undefined && this.sinfo.total_sales !== null) {
					return this.sinfo.total_sales;
				}
				return this.sinfo.order_num || 0;
			}
		},
		watch: {
			storeId: {
				handler(newVal) {
					if (newVal && newVal !== this.currentStoreId) {
						this.initByStoreId(newVal);
					}
				},
				immediate: true
			}
		},
		mounted() {
			// 若非直接通过 storeId prop 触发（例如 Tab 模式），则自动读取初始化
			if (!this.storeId) {
				this.initStore();
			}
		},
		methods: {
			// 初始化门店
			async initStore() {
				// 1. 优先读取 storage 中的 store_id / site_id
				let targetId = uni.getStorageSync('store_id') || uni.getStorageSync('user_site_id') || uni.getStorageSync('site_id');
				
				// 2. 获取城市下所有门店列表备选
				await this.fetchWebsiteStores();

				if (!targetId && this.storeList.length > 0) {
					targetId = this.storeList[0].store_id || this.storeList[0].id;
				}

				if (targetId) {
					await this.initByStoreId(targetId);
				}
			},

			// 获取城市下的门店列表
			async fetchWebsiteStores() {
				try {
					let website_id = uni.getStorageSync('website_id') || '';
					let res = await websiteStore({ website_id });
					if (res && res.code >= 0 && Array.isArray(res.data)) {
						this.storeList = res.data;
					}
				} catch (e) {
					console.error('获取城市商家列表失败', e);
				}
			},

			// 根据指定的 store_id 初始化页面数据
			async initByStoreId(id) {
				this.currentStoreId = id;
				uni.setStorageSync('store_id', id);

				// 获取位置信息用于计算距离
				this.getUserLocation();

				// 1. 获取门店详情（包含商户专属Banner、累计销量、粉丝量）
				await this.fetchStoreDetail();

				// 2. 获取关注状态
				await this.fetchFollowStatus();

				// 3. 获取商户商品分类列表（供下拉选择）
				await this.fetchStoreCategories();

				// 4. 重置并获取商品列表
				this.page = 1;
				await this.fetchGoodsList();
			},

			// 获取门店详情
			async fetchStoreDetail() {
				try {
					let params = { store_id: this.currentStoreId };
					let res = await storeDetail(params);
					if (res && res.code >= 0 && res.data) {
						this.sinfo = res.data;
						// 设置 Banner 列表
						if (Array.isArray(res.data.banner_list) && res.data.banner_list.length > 0) {
							this.bannerList = res.data.banner_list;
						} else if (res.data.store_image) {
							this.bannerList = [{ id: 0, image: res.data.store_image, name: res.data.store_name }];
						} else {
							this.bannerList = [];
						}

						if (!this.isTab && res.data.store_name) {
							uni.setNavigationBarTitle({
								title: res.data.store_name
							});
						}
					}
				} catch (e) {
					console.error('获取门店详情异常', e);
				}
			},

			// 查询关注状态
			async fetchFollowStatus() {
				if (!uni.getStorageSync('token')) {
					this.guanzhu = 0;
					return;
				}
				try {
					let params = { store_id: this.currentStoreId };
					let res = await issubscribe(params);
					if (res && res.code >= 0) {
						this.guanzhu = res.data;
					}
				} catch (e) {
					this.guanzhu = 0;
				}
			},

			// 获取该商家商品对应的分类列表
			async fetchStoreCategories() {
				try {
					let site_id = this.sinfo.site_id || '';
					let res = await siteCategoryList({ site_id });
					if (res && res.code >= 0 && Array.isArray(res.data)) {
						this.categoryList = res.data;
					}
				} catch (e) {
					console.error('获取分类列表失败', e);
				}
			},

			// 获取位置
			getUserLocation() {
				uni.getLocation({
					type: 'gcj02',
					success: (res) => {
						this.latitude = res.latitude;
						this.longitude = res.longitude;
					},
					fail: () => {
						// 失败保持 null
					}
				});
			},

			// 获取商品列表
			async fetchGoodsList() {
				this.loading = true;
				try {
					let params = {
						page: this.page,
						page_size: this.pageSize,
						site_id: this.sinfo.site_id || '',
						category_id: this.currentCategoryId || 0,
						goods_type: 0 // 普通/主推商品
					};

					// 排序参数
					if (this.currentSortType === 'sale_num') {
						params.order = 'sale_num';
						params.sort = this.sortOrder;
					} else if (this.currentSortType === 'distance') {
						params.order = 'distance';
						params.sort = this.sortOrder;
						if (this.latitude && this.longitude) {
							params.latitude = this.latitude;
							params.longitude = this.longitude;
						}
					}

					let res = await goodslist(params);
					if (res && res.code >= 0 && res.data) {
						let list = res.data.list || [];
						this.goodsList = this.page > 1 ? this.goodsList.concat(list) : list;
						this.totalPage = res.data.page_count || 1;
					}
				} catch (e) {
					console.error('获取商品列表异常', e);
				} finally {
					this.loading = false;
				}
			},

			// 切换关注
			toggleFollow() {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open();
					return;
				}
				let params = { store_id: this.currentStoreId };
				if (this.guanzhu == 0) {
					shopmemberAdd(params).then(res => {
						uni.showToast({ title: '关注成功', icon: 'none' });
						this.guanzhu = 1;
						this.sinfo.total_num = (this.sinfo.total_num || 0) + 1;
					});
				} else {
					shopmemberDel(params).then(res => {
						uni.showToast({ title: '已取消关注', icon: 'none' });
						this.guanzhu = 0;
						this.sinfo.total_num = Math.max(0, (this.sinfo.total_num || 1) - 1);
					});
				}
			},

			// 切换分类下拉面板
			toggleCategoryDropdown() {
				this.showCategoryDropdown = !this.showCategoryDropdown;
			},

			// 选择具体分类
			selectCategory(id, name) {
				this.currentCategoryId = id;
				this.currentCategoryName = name || '商品分类';
				this.showCategoryDropdown = false;
				this.page = 1;
				this.fetchGoodsList();
			},

			// 切换排序
			toggleSort(type) {
				this.showCategoryDropdown = false;
				if (this.currentSortType === type) {
					// 切换升降序
					this.sortOrder = this.sortOrder === 'desc' ? 'asc' : 'desc';
				} else {
					this.currentSortType = type;
					this.sortOrder = type === 'distance' ? 'asc' : 'desc';
				}
				this.page = 1;
				this.fetchGoodsList();
			},

			// 重置筛选
			resetFilter() {
				this.currentCategoryId = "";
				this.currentCategoryName = "商品分类";
				this.currentSortType = "";
				this.sortOrder = "desc";
				this.page = 1;
				this.fetchGoodsList();
			},

			// Banner切换事件
			onBannerChange(e) {
				this.currentBannerIndex = e.detail.current;
			},

			// 点击Banner
			onBannerClick(item) {
				if (item && item.url) {
					uni.navigateTo({
						url: item.url
					});
				}
			},

			// 打开换店弹窗
			openStoreSelect() {
				if (this.storeList.length <= 1) {
					this.fetchWebsiteStores();
				}
				this.$refs.storeSelectPopup.open();
			},

			// 关闭换店弹窗
			closeStoreSelect() {
				this.$refs.storeSelectPopup.close();
			},

			// 选定新门店
			onSelectStore(store) {
				let storeId = store.store_id || store.id;
				if (storeId && storeId !== this.currentStoreId) {
					this.initByStoreId(storeId);
				}
				this.closeStoreSelect();
			},

			// 跳转商品详情
			goGoodsDetail(skuId) {
				if (!skuId) return;
				uni.navigateTo({
					url: '/otherpages/goods_detail/goods_detail?id=' + skuId
				});
			},

			// 返回上一页
			goBack() {
				uni.navigateBack({
					delta: 1
				});
			},

			// 价格切分（整数和小数）
			getPricePart(price, part) {
				if (!price && price !== 0) return '';
				let str = String(price);
				let parts = str.split('.');
				if (part === 0) return parts[0];
				return parts[1] || '';
			},

			// 供父页面调用的下拉刷新
			refresh() {
				this.page = 1;
				this.fetchStoreDetail();
				this.fetchFollowStatus();
				this.fetchGoodsList();
			},

			// 供父页面调用的触底加载
			loadMore() {
				if (this.loading || this.page >= this.totalPage) return;
				this.page++;
				this.fetchGoodsList();
			}
		}
	}
</script>

<style lang="scss" scoped>
	.store-home-wrap {
		min-height: 100vh;
		background: #F6F7FB;
		padding-bottom: 40rpx;
		box-sizing: border-box;
		position: relative;
	}

	.nav-store-title {
		max-width: 420rpx;
		.store-name-text {
			font-size: 32rpx;
			font-weight: 600;
			color: #1A1A1A;
			max-width: 340rpx;
		}
		.store-switch-arrow {
			font-size: 22rpx;
			color: #666;
			margin-left: 6rpx;
		}
	}

	.navbar-center-title {
		font-size: 32rpx;
		font-weight: 600;
		color: #1A1A1A;
	}

	/* 1. Banner轮播区 */
	.banner-section {
		width: 100%;
		height: 380rpx;
		position: relative;
		overflow: hidden;

		.store-swiper {
			width: 100%;
			height: 100%;
		}

		.swiper-image-box {
			width: 100%;
			height: 100%;
			.banner-img {
				width: 100%;
				height: 100%;
				display: block;
			}
		}

		.banner-dots {
			position: absolute;
			bottom: 40rpx;
			right: 32rpx;
			display: flex;
			align-items: center;
			gap: 10rpx;
			z-index: 10;

			.dot-item {
				width: 12rpx;
				height: 12rpx;
				border-radius: 6rpx;
				background: rgba(255, 255, 255, 0.55);
				transition: all 0.3s ease;

				&.active {
					width: 28rpx;
					background: #FFFFFF;
				}
			}
		}
	}

	/* 2. 门店数据与关注行卡片 */
	.store-stats-card {
		margin: -32rpx 24rpx 24rpx 24rpx;
		padding: 28rpx 24rpx;
		background: #FFFFFF;
		border-radius: 24rpx;
		box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.04);
		position: relative;
		z-index: 12;

		.store-main-row {
			margin-bottom: 24rpx;

			.store-header-left {
				flex: 1;
				overflow: hidden;

				.store-avatar {
					width: 88rpx;
					height: 88rpx;
					border-radius: 16rpx;
					border: 2rpx solid #F0F0F0;
					margin-right: 18rpx;
					flex-shrink: 0;
				}

				.store-name-col {
					flex: 1;
					overflow: hidden;

					.store-title {
						.title-text {
							font-size: 32rpx;
							font-weight: 600;
							color: #222222;
							line-height: 44rpx;
						}
					}

					.store-address-text {
						font-size: 22rpx;
						color: #999999;
						margin-top: 6rpx;
						line-height: 32rpx;
					}
				}
			}

			.switch-store-btn {
				display: flex;
				align-items: center;
				padding: 8rpx 16rpx;
				background: #F7F8FA;
				border-radius: 20rpx;
				font-size: 22rpx;
				color: #666666;
				margin-left: 16rpx;
				flex-shrink: 0;

				.switch-icon {
					width: 20rpx;
					height: 20rpx;
					margin-right: 6rpx;
				}
			}
		}

		/* 核心数据与关注门店按钮行 */
		.stats-action-row {
			padding-top: 20rpx;
			border-top: 1rpx solid #F2F3F7;

			.stat-item {
				flex: 1;
				text-align: center;

				.stat-num {
					font-size: 36rpx;
					font-weight: 700;
					color: #222222;
					line-height: 44rpx;
				}

				.stat-label {
					font-size: 22rpx;
					color: #888888;
					margin-top: 4rpx;
				}
			}

			.stat-divider {
				width: 1rpx;
				height: 48rpx;
				background: #EBEBEB;
			}

			.follow-action-col {
				flex: 1.2;
				display: flex;
				justify-content: flex-end;

				.follow-btn {
					width: 180rpx;
					height: 64rpx;
					background: linear-gradient(135deg, #FF6A00 0%, #F55606 100%);
					border-radius: 32rpx;
					color: #FFFFFF;
					font-size: 26rpx;
					font-weight: 500;
					box-shadow: 0 4rpx 12rpx rgba(245, 86, 6, 0.25);
					transition: all 0.2s ease;

					.heart-icon {
						width: 26rpx;
						height: 26rpx;
						margin-right: 8rpx;
					}

					&.followed {
						background: #FFF2EB;
						color: #F55606;
						box-shadow: none;
						border: 1rpx solid rgba(245, 86, 6, 0.3);
					}
				}
			}
		}
	}

	/* 3. 商品容器 */
	.goods-container {
		padding: 0 24rpx;
		position: relative;

		/* 顶部过滤栏 */
		.filter-bar {
			background: #FFFFFF;
			border-radius: 20rpx;
			padding: 20rpx 28rpx;
			margin-bottom: 20rpx;
			box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.02);

			.filter-item {
				font-size: 28rpx;
				color: #555555;
				font-weight: 400;

				.filter-text {
					margin-right: 8rpx;
					transition: color 0.2s;

					&.filter-active {
						color: #F55606;
						font-weight: 600;
					}
				}

				&.category-select-item {
					max-width: 260rpx;
				}

				.sort-arrows {
					display: flex;
					flex-direction: column;
					justify-content: center;
					line-height: 1;
					margin-left: 4rpx;
				}
			}
		}

		/* 分类下拉弹出层 */
		.category-dropdown-panel {
			position: absolute;
			top: 92rpx;
			left: 24rpx;
			right: 24rpx;
			z-index: 99;

			.dropdown-backdrop {
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				background: rgba(0, 0, 0, 0.25);
				z-index: 100;
			}

			.dropdown-content {
				position: relative;
				z-index: 101;
				background: #FFFFFF;
				border-radius: 20rpx;
				box-shadow: 0 10rpx 30rpx rgba(0, 0, 0, 0.12);
				padding: 16rpx 0;
				max-height: 520rpx;

				.dropdown-scroll {
					max-height: 480rpx;

					.category-option {
						padding: 24rpx 32rpx;
						font-size: 28rpx;
						color: #333333;
						transition: background-color 0.2s;

						&.selected {
							color: #F55606;
							font-weight: 600;
							background: #FFF9F5;
						}

						&:active {
							background: #F9F9F9;
						}
					}
				}
			}
		}

		/* 4. 双列商品卡片 */
		.goods-grid {
			display: grid;
			grid-template-columns: repeat(2, 1fr);
			gap: 20rpx;

			.goods-card {
				background: #FFFFFF;
				border-radius: 20rpx;
				overflow: hidden;
				box-shadow: 0 6rpx 20rpx rgba(0, 0, 0, 0.03);
				display: flex;
				flex-direction: column;

				.goods-img-box {
					width: 100%;
					height: 330rpx;
					position: relative;
					background: #F2F2F2;

					.goods-img {
						width: 100%;
						height: 100%;
						display: block;
					}

					.click-badge {
						position: absolute;
						bottom: 12rpx;
						left: 12rpx;
						background: rgba(0, 0, 0, 0.45);
						color: #FFFFFF;
						font-size: 20rpx;
						padding: 4rpx 12rpx;
						border-radius: 8rpx;
					}

					.tag-badge {
						position: absolute;
						top: 12rpx;
						left: 12rpx;
						background: linear-gradient(135deg, #FF6A00 0%, #F55606 100%);
						color: #FFFFFF;
						font-size: 20rpx;
						font-weight: 500;
						padding: 4rpx 12rpx;
						border-radius: 6rpx;
					}
				}

				.goods-info-box {
					padding: 18rpx 16rpx 20rpx 16rpx;
					display: flex;
					flex-direction: column;
					flex: 1;
					justify-content: space-between;

					.goods-title {
						font-size: 28rpx;
						color: #222222;
						font-weight: 500;
						line-height: 38rpx;
						height: 76rpx;
					}

					.goods-meta-row {
						margin-top: 10rpx;
						font-size: 22rpx;
						color: #999999;
						gap: 12rpx;

						.distance-tag {
							display: flex;
							align-items: center;
							gap: 2rpx;
						}
					}

					.price-action-row {
						margin-top: 16rpx;

						.price-box {
							color: #F55606;
							line-height: 1;

							.price-symbol {
								font-size: 24rpx;
								font-weight: 700;
							}

							.price-integer {
								font-size: 36rpx;
								font-weight: 700;
							}

							.price-decimal {
								font-size: 24rpx;
								font-weight: 700;
							}

							.market-price {
								font-size: 22rpx;
								color: #BBBBBB;
								text-decoration: line-through;
								margin-left: 8rpx;
								font-weight: 400;
							}
						}

						.buy-action-btn {
							width: 48rpx;
							height: 48rpx;
							background: linear-gradient(135deg, #FF7B22 0%, #F55606 100%);
							border-radius: 50%;
							color: #FFFFFF;
							font-size: 24rpx;
							font-weight: 600;
							box-shadow: 0 4rpx 10rpx rgba(245, 86, 6, 0.3);
						}
					}
				}
			}
		}

		.empty-state {
			padding: 80rpx 0;
			display: flex;
			flex-direction: column;
			align-items: center;

			.empty-img {
				width: 240rpx;
				height: 240rpx;
			}

			.empty-tip {
				font-size: 26rpx;
				color: #999999;
				margin-top: 20rpx;
			}

			.reset-filter-btn {
				margin-top: 24rpx;
				padding: 12rpx 32rpx;
				background: #FFF2EB;
				color: #F55606;
				font-size: 24rpx;
				border-radius: 30rpx;
			}
		}

		.loading-more {
			padding: 30rpx 0;
			font-size: 24rpx;
			color: #999999;
		}
	}

	/* 门店选择弹窗 */
	.store-popup-container {
		background: #FFFFFF;
		padding: 32rpx;
		max-height: 70vh;

		.popup-title {
			margin-bottom: 24rpx;

			.popup-title-text {
				font-size: 32rpx;
				font-weight: 600;
				color: #222222;
			}
		}

		.store-select-scroll {
			max-height: 55vh;

			.store-option-item {
				padding: 24rpx 0;
				border-bottom: 1rpx solid #F5F5F5;

				.store-opt-left {
					flex: 1;
					overflow: hidden;

					.store-opt-img {
						width: 80rpx;
						height: 80rpx;
						border-radius: 12rpx;
						margin-right: 16rpx;
						flex-shrink: 0;
					}

					.store-opt-info {
						flex: 1;
						overflow: hidden;

						.store-opt-name {
							font-size: 28rpx;
							font-weight: 500;
							color: #222222;
						}

						.store-opt-addr {
							font-size: 22rpx;
							color: #999999;
							margin-top: 6rpx;
						}
					}
				}

				&.active {
					.store-opt-name {
						color: #F55606;
						font-weight: 600;
					}
				}
			}
		}
	}
</style>
