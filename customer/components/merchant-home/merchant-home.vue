<template>
	<view class="store-home-wrap">
		<!-- 自定义导航栏（Tab页模式展示：左上角当前城市，中心固定城盟优选，背景橘色） -->
		<uv-navbar v-if="isTab" bg-color="#FF5500" :fixed="true" :placeholder="true">
			<template v-slot:left>
				<view class="nav-city-wrap acea-row row-middle">
					<image src="/static/map1.png" class="city-icon" mode="aspectFit"></image>
					<text class="city-name-text">{{ currentCity || '城市' }}</text>
				</view>
			</template>
			<template v-slot:center>
				<view class="nav-shop-switch-wrap acea-row row-middle" style="flex-wrap: nowrap;" @click="openMerchantSelect">
					<text class="nav-shop-title text-more">{{ currentMerchantTitle }}</text>
					<uv-icon name="arrow-down-fill" size="9" color="#FFFFFF" customStyle="margin-left: 6rpx; flex-shrink: 0;"></uv-icon>
					<view class="nav-switch-badge" v-if="myShops.length > 1">
						<text>{{ myShops.length }}店</text>
					</view>
				</view>
			</template>
		</uv-navbar>

		<!-- 详情页模式下带有返回按钮的导航栏 -->
		<uv-navbar v-else bg-color="#ffffff" :fixed="true" :placeholder="true" left-icon-color="#1A1A1A"
			left-icon-size="22px" @leftClick="goBack">
			<template v-slot:left>
				<view class="nav-detail-back acea-row row-middle" @click="goBack">
					<image src="/static/back1.png" class="detail-back-icon" mode="aspectFit"></image>
				</view>
			</template>
			<template v-slot:center>
				<text class="navbar-center-title">{{ sinfo.store_name || '门店详情' }}</text>
			</template>
		</uv-navbar>

		<!-- 1. 顶部 Banner 轮播区 -->
		<view class="banner-section">
			<swiper class="store-swiper" :indicator-dots="false" :autoplay="bannerList.length > 1" :interval="3500"
				:duration="600" circular @change="onBannerChange" v-if="bannerList.length > 0">
				<swiper-item v-for="(item, index) in bannerList" :key="index" @click="onBannerClick(item)">
					<view class="swiper-image-box">
						<image :src="$util.img(item.image)" mode="aspectFill" class="banner-img"></image>
					</view>
				</swiper-item>
			</swiper>

			<!-- 缺省Banner -->
			<view class="swiper-image-box" v-else>
				<image :src="sinfo.store_image ? $util.img(sinfo.store_image) : '/static/bg1.png'" mode="aspectFill"
					class="banner-img"></image>
			</view>

			<!-- 自定义轮播指示点 -->
			<view class="banner-dots" v-if="bannerList.length > 1">
				<view v-for="(item, index) in bannerList" :key="index"
					:class="['dot-item', { 'active': currentBannerIndex === index }]"></view>
			</view>
		</view>

		<!-- 2. Banner下方紧接着一行：门店粉丝量、累计销量、关注门店 -->
		<view class="store-stats-card">
			<view class="store-main-row acea-row row-middle row-between">
				<!-- 门店头像与基本信息 -->
				<view class="store-header-left acea-row row-middle">
					<image
						:src="sinfo.store_image ? $util.img(sinfo.store_image) : (sinfo.shop_logo ? $util.img(sinfo.shop_logo) : '/static/logo.png')"
						class="store-avatar" mode="aspectFill"></image>
					<view class="store-name-col">
						<view class="store-title acea-row row-middle">
							<text class="title-text text-more">{{ sinfo.store_name || '商家门店' }}</text>
						</view>
						<view class="store-address-row acea-row row-middle" v-if="sinfo.full_address || sinfo.address">
							<text class="store-dist-tag" v-if="sinfo.distance">距您{{ sinfo.distance }}</text>
							<text class="store-address-text text-more">{{ sinfo.full_address || sinfo.address }}</text>
						</view>
					</view>
				</view>

				<!-- 切换门店按钮（若有多门店且是Tab模式） -->
				<view class="switch-store-btn" v-if="isTab && storeList.length > 1" @click="openStoreSelect">
					<image src="/static/icon5.png" class="switch-icon" mode=""></image>
					<text>换店({{ storeList.length }})</text>
				</view>
			</view>

			<!-- 门店名称下方一行：营业时间、一键导航、一键电话、品牌粉丝群 -->
			<view class="store-service-row acea-row row-middle row-between">
				<!-- 营业时间 -->
				<view class="service-time-box acea-row row-middle">
					<uv-icon name="clock" size="13" color="#FF5500"></uv-icon>
					<text class="service-time-label">营业时间</text>
					<text class="service-time-val text-more">{{ sinfo.open_date || sinfo.workingtime || '09:00-22:00'
					}}</text>
				</view>

				<!-- 快捷动作组：导航、电话、粉丝群 -->
				<view class="service-actions-box acea-row row-middle">
					<view class="service-btn acea-row row-middle" @click="openNavigation">
						<uv-icon name="map-fill" size="12" color="#FF5500"></uv-icon>
						<text class="btn-txt">导航</text>
					</view>
					<view class="btn-divider"></view>
					<view class="service-btn acea-row row-middle" @click="callStorePhone">
						<uv-icon name="phone-fill" size="12" color="#FF5500"></uv-icon>
						<text class="btn-txt">电话</text>
					</view>
					<view class="btn-divider"></view>
					<wecom-group-btn :url="sinfo.wecom_group_url || sinfo.wechat_group_url || ''"
						:brand-name="sinfo.site_name || sinfo.store_name" :logo="sinfo.store_image || sinfo.shop_logo"
						:qrcode="sinfo.group_qrcode" :phone="sinfo.telphone || sinfo.telephone" btn-text="粉丝群"></wecom-group-btn>
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
					<view class="follow-btn acea-row row-middle row-center" :class="{ 'followed': guanzhu != 0 }"
						@click="toggleFollow">
						<image src="/static/heart.png" v-if="guanzhu == 0" class="heart-icon" mode="aspectFit"></image>
						<image src="/static/heart1.png" v-else class="heart-icon" mode="aspectFit"></image>
						<text class="follow-text">{{ guanzhu == 0 ? '关注门店' : '已关注' }}</text>
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
					<uv-icon name="arrow-down-fill" :color="currentCategoryId !== '' ? '#F55606' : '#666'" size="9"
						:custom-style="{ transform: showCategoryDropdown ? 'rotate(180deg)' : 'rotate(0deg)', transition: 'transform 0.2s ease' }"></uv-icon>
				</view>

				<!-- 销量排序 -->
				<view class="filter-item acea-row row-middle row-center" @click="toggleSort('sale_num')">
					<text class="filter-text" :class="{ 'filter-active': currentSortType === 'sale_num' }">销量</text>
					<view class="sort-arrows">
						<uv-icon name="arrow-up-fill"
							:color="currentSortType === 'sale_num' && sortOrder === 'asc' ? '#F55606' : '#C0C4CC'" size="7"></uv-icon>
						<uv-icon name="arrow-down-fill"
							:color="currentSortType === 'sale_num' && sortOrder === 'desc' ? '#F55606' : '#C0C4CC'"
							size="7"></uv-icon>
					</view>
				</view>

				<!-- 积分商品筛选（替换原距离排序） -->
				<view class="filter-item point-filter-item acea-row row-middle row-center"
					:class="{ 'point-active': isPointGoods }" @click="togglePointGoods">
					<uv-icon name="integral-fill" size="14" :color="isPointGoods ? '#F55606' : '#666'"></uv-icon>
					<text class="filter-text" :class="{ 'filter-active': isPointGoods }">积分商品</text>
				</view>
			</view>

			<!-- 分类下拉选择弹出层 -->
			<view class="category-dropdown-panel" v-if="showCategoryDropdown">
				<view class="dropdown-backdrop" @click="showCategoryDropdown = false"></view>
				<view class="dropdown-content">
					<scroll-view scroll-y class="dropdown-scroll">
						<!-- 全部分类 -->
						<view class="category-option acea-row row-middle row-between"
							:class="{ 'selected': currentCategoryId === '' }" @click="selectCategory('', '全部分类')">
							<text>全部分类</text>
							<uv-icon name="checkbox-mark" color="#F55606" size="14" v-if="currentCategoryId === ''"></uv-icon>
						</view>

						<!-- 具体分类项 -->
						<view v-for="(cat, catIdx) in categoryList" :key="catIdx"
							class="category-option acea-row row-middle row-between"
							:class="{ 'selected': currentCategoryId == cat.category_id }"
							@click="selectCategory(cat.category_id, cat.category_name || cat.short_name)">
							<text>{{ cat.category_name || cat.short_name }}</text>
							<uv-icon name="checkbox-mark" color="#F55606" size="14"
								v-if="currentCategoryId == cat.category_id"></uv-icon>
						</view>
					</scroll-view>
				</view>
			</view>

			<!-- 4. 商品列表：两列双列卡片显示 -->
			<view class="goods-grid" v-if="goodsList.length > 0">
				<view class="goods-card" v-for="(item, index) in goodsList" :key="index"
					@click="goGoodsDetail(item.sku_id || item.goods_id)">
					<!-- 商品主图 -->
					<view class="goods-img-box">
						<image :src="$util.img(item.sku_image || item.goods_image)" mode="aspectFill" class="goods-img" lazy-load>
						</image>
						<!-- 浏览量角标 -->
						<view class="click-badge acea-row row-middle" v-if="item.click_num">
							<uv-icon name="eye" size="10" color="#fff"></uv-icon>
							<text class="m-l-4">{{ item.click_num }}</text>
						</view>
						<!-- 特惠/核销/积分标签 -->
						<view class="tag-badge" :class="{ 'point-tag': isPointGoods || item.goods_type == 1 }">
							{{ (isPointGoods || item.goods_type == 1) ? '积分兑换' : '门店核销' }}
						</view>
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

						<!-- 价格与操作按钮 -->
						<view class="price-action-row acea-row row-middle row-between">
							<view class="price-box acea-row row-bottom">
								<template v-if="(isPointGoods || item.goods_type == 1) && (item.point > 0)">
									<text class="point-num">{{ item.point }}</text>
									<text class="point-unit">积分</text>
									<text class="point-plus" v-if="item.price && Number(item.price) > 0">+</text>
									<text class="price-symbol" v-if="item.price && Number(item.price) > 0">￥</text>
									<text class="price-integer" v-if="item.price && Number(item.price) > 0">{{
										getPricePart(item.price, 0) }}</text>
									<text class="price-decimal"
										v-if="item.price && Number(item.price) > 0 && getPricePart(item.price, 1)">.{{
											getPricePart(item.price, 1) }}</text>
								</template>
								<template v-else>
									<text class="price-symbol">￥</text>
									<text class="price-integer">{{ getPricePart(item.price, 0) }}</text>
									<text class="price-decimal" v-if="getPricePart(item.price, 1)">.{{
										getPricePart(item.price, 1)
									}}</text>
								</template>
								<text class="market-price" v-if="item.market_price && item.market_price != '0.00'">¥{{
									item.market_price }}</text>
							</view>
							<view class="buy-action-btn acea-row row-middle row-center"
								:class="{ 'point-buy-btn': isPointGoods || item.goods_type == 1 }">
								{{ (isPointGoods || item.goods_type == 1) ? '兑' : '抢' }}
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
					<view class="acea-row row-middle">
						<text class="popup-title-text">选择服务门店</text>
						<text class="popup-store-count" v-if="storeList.length > 0">（共{{ storeList.length
						}}家线下分店）</text>
					</view>
					<uv-icon name="close" size="16" color="#999" @click="closeStoreSelect"></uv-icon>
				</view>
				<scroll-view scroll-y class="store-select-scroll">
					<view v-for="(store, sIdx) in storeList" :key="sIdx" class="store-option-item acea-row row-middle row-between"
						:class="{ 'active': currentStoreId == (store.store_id || store.id) }" @click="onSelectStore(store)">
						<view class="store-opt-left acea-row row-middle">
							<image :src="store.store_image ? $util.img(store.store_image) : '/static/stores.png'"
								class="store-opt-img" mode="aspectFill"></image>
							<view class="store-opt-info">
								<view class="store-opt-name-row acea-row row-middle">
									<text class="store-opt-name text-more">{{ store.store_name }}</text>
									<text class="nearest-badge" v-if="sIdx === 0 && store.distance">离您最近</text>
								</view>
								<view class="store-opt-meta acea-row row-middle">
									<text class="store-opt-distance" v-if="store.distance">距您 {{ store.distance
									}}</text>
									<text class="store-opt-tel" v-if="store.telphone" @click.stop="makePhoneCall(store.telphone)">📞 {{
										store.telphone }}</text>
								</view>
								<view class="store-opt-addr text-more">{{ store.address_text || store.full_address ||
									store.address }}</view>
							</view>
						</view>
						<view class="store-opt-right acea-row row-middle">
							<uv-icon name="checkmark-circle-fill" color="#F55606" size="22"
								v-if="currentStoreId == (store.store_id || store.id)"></uv-icon>
							<view class="select-btn" v-else>进店</view>
						</view>
					</view>
				</scroll-view>
			</view>
		</uv-popup>



		<!-- 商家店铺切换弹窗（我的店铺[老板] + 我是店员 + 我所归属） -->
		<uv-popup ref="merchantSelectPopup" round="24" mode="bottom">
			<view class="merchant-popup-container">
				<view class="popup-title acea-row row-middle row-between">
					<view class="acea-row row-middle">
						<text class="popup-title-text">切换商家店铺</text>
						<text class="popup-store-count" v-if="myShops.length > 0">（共{{ myShops.length }}家关联店铺）</text>
					</view>
					<uv-icon name="close" size="16" color="#999" @click="closeMerchantSelect"></uv-icon>
				</view>
				<scroll-view scroll-y class="merchant-select-scroll">
					<view v-for="(shop, mIdx) in myShops" :key="mIdx"
						class="merchant-option-item acea-row row-middle row-between"
						:class="{ 'active': isCurrentShop(shop) }" @click="onSelectMerchant(shop)">
						<view class="merchant-opt-left acea-row row-middle">
							<image :src="shop.shop_image ? $util.img(shop.shop_image) : '/static/stores.png'"
								class="merchant-opt-img" mode="aspectFill"></image>
							<view class="merchant-opt-info">
								<view class="merchant-opt-name-row acea-row row-middle">
									<text class="merchant-opt-name text-more">{{ shop.site_name || shop.store_name }}</text>
									<view class="shop-role-tag" :class="'tag-' + shop.role">
										{{ shop.role_name }}
									</view>
								</view>
								<view class="merchant-opt-meta acea-row row-middle" v-if="shop.distance || shop.telephone">
									<text class="merchant-opt-distance" v-if="shop.distance">距您 {{ shop.distance }}</text>
									<text class="merchant-opt-tel" v-if="shop.telephone">📞 {{ shop.telephone }}</text>
								</view>
								<view class="merchant-opt-addr text-more" v-if="shop.address">{{ shop.address }}</view>
							</view>
						</view>
						<view class="merchant-opt-right acea-row row-middle">
							<uv-icon name="checkmark-circle-fill" color="#FF5500" size="22"
								v-if="isCurrentShop(shop)"></uv-icon>
							<view class="select-btn" v-else>进店</view>
						</view>
					</view>
					<view class="merchant-empty-box acea-row row-column row-center row-middle" v-if="myShops.length === 0">
						<text class="empty-tip">暂无可切换的关联店铺（老板/店员/归属）</text>
					</view>
				</scroll-view>
			</view>
		</uv-popup>

		<!-- 登录弹窗 -->
		<login ref="login" @loginsuccess="onLoginSuccess"></login>
	</view>
</template>

<script>
import {
	userinfo,
	storeDetail,
	issubscribe,
	shopmemberAdd,
	shopmemberDel,
	goodslist,
	websiteStore,
	siteCategoryList,
	tranAddressInfo,
	myShopList
} from "@/common/js/api.js";
import WecomGroupBtn from "@/components/wecom-group-btn/wecom-group-btn.vue";

export default {
	name: "merchant-home",
	components: {
		WecomGroupBtn
	},
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
			currentCity: "",
			isPointGoods: false,
			sinfo: {},
			bannerList: [],
			currentBannerIndex: 0,
			guanzhu: 0,

			// 记录当前生效的登录态与商家ID，用于实时比对与切店
			activeToken: null,
			activeSiteId: null,
			activeWebsiteId: null,

			// 分类筛选
			categoryList: [],
			currentCategoryId: "",
			currentCategoryName: "商品分类",
			showCategoryDropdown: false,

			// 排序筛选：sortType: 'sale_num' | ''
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

			// 用户关联商家列表（老板+店员+归属）
			myShops: [],

			// 用户位置
			latitude: null,
			longitude: null
		};
	},
	computed: {
		currentMerchantTitle() {
			if (this.sinfo && this.sinfo.site_name) {
				return this.sinfo.site_name;
			}
			if (this.sinfo && this.sinfo.store_name) {
				return this.sinfo.store_name;
			}
			return '城盟优选';
		},
		displaySales() {
			if (!this.sinfo) return 0;
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
		this.initCurrentCity();
		// 若非直接通过 storeId prop 触发（例如 Tab 模式），则自动读取初始化
		if (!this.storeId) {
			this.initStore();
		}
		// 监听全局登录态变化事件（登录成功或退出登录时触发）
		uni.$on('loginChange', this.onLoginChange);
	},
	beforeDestroy() {
		uni.$off('loginChange', this.onLoginChange);
	},
	methods: {
		onLoginSuccess() {
			if (!this.storeId) {
				this.initStore(true);
			}
		},
		onLoginChange() {
			if (!this.storeId) {
				this.initStore(true);
			}
		},

		// 检查并同步商家与门店状态（供父页面 onShow 调用）
		async checkStoreState() {
			this.initCurrentCity();
			if (this.storeId) return;

			let currentToken = uni.getStorageSync('token') || '';
			let currentWebsiteId = uni.getStorageSync('website_id') || '';
			let cachedSiteId = currentToken ? (uni.getStorageSync('user_site_id') || uni.getStorageSync('site_id') || '') : '';
			let cachedStoreId = uni.getStorageSync('store_id') || '';

			// 1. 如果此前从未初始化过，或登录态切换（有token <-> 无token），或城市切换
			if (!this.currentStoreId || this.activeToken !== currentToken || this.activeWebsiteId !== currentWebsiteId) {
				await this.initStore(true);
				return;
			}

			// 2. 如果处于登录态，校验最新的 site_id 是否和当前生效的 activeSiteId 一致
			if (currentToken) {
				if (String(cachedSiteId) !== String(this.activeSiteId || '')) {
					await this.initStore(true);
					return;
				}
			} else {
				// 未登录状态下，activeSiteId 必须为空
				if (this.activeSiteId) {
					await this.initStore(true);
					return;
				}
			}

			// 3. 如果用户在别处切换了同商家下的门店
			if (cachedStoreId && cachedStoreId !== this.currentStoreId) {
				await this.initByStoreId(cachedStoreId);
			}
		},

		// 初始化门店（LBS最近优先 + 商家自动匹配）
		async initStore(forceResetStore = false) {
			let currentToken = uni.getStorageSync('token') || '';
			let site_id = '';

			// 1. 同步当前登录者的商家 ID
			if (currentToken) {
				try {
					let res = await userinfo();
					if (res && res.code >= 0 && res.data) {
						let info = res.data;
						// 优先查看用户是否有主动切换过的店铺
						let selectedSiteId = uni.getStorageSync('selected_site_id');
						if (selectedSiteId) {
							site_id = String(selectedSiteId);
						} else {
							site_id = info.site_id ? String(info.site_id) : String(uni.getStorageSync('user_site_id') || uni.getStorageSync('site_id') || '');
						}
						if (site_id) {
							uni.setStorageSync('user_site_id', site_id);
							uni.setStorageSync('site_id', site_id);
						}
						uni.setStorageSync('userInfo', info);
					}
				} catch (e) {
					site_id = String(uni.getStorageSync('selected_site_id') || uni.getStorageSync('user_site_id') || uni.getStorageSync('site_id') || '');
				}
			} else {
				// 未登录状态，清除 user_site_id，商家 ID 视为空
				uni.removeStorageSync('user_site_id');
				site_id = '';
			}

			let currentWebsiteId = uni.getStorageSync('website_id') || '';

			// 2. 判断是否需要清空旧门店缓存
			// 如果商家ID变了、登录态变了、城市变了，旧门店必定失效
			if (this.activeSiteId !== null && (String(this.activeSiteId) !== site_id || this.activeToken !== currentToken)) {
				forceResetStore = true;
			}
			if (forceResetStore) {
				uni.removeStorageSync('store_id');
			}

			this.activeToken = currentToken;
			this.activeSiteId = site_id;
			this.activeWebsiteId = currentWebsiteId;

			// 3. 先尝试获取用户经纬度
			await this.getUserLocation();

			// 4. 根据当前商家 site_id 及定位拉取旗下所有分店
			await this.fetchWebsiteStores(site_id);

			let cachedStoreId = uni.getStorageSync('store_id');
			let targetId = '';

			if (this.storeList.length > 0) {
				// 4.1 如果非强制重置，且用户之前选中的门店依然在列表中，优先保留
				if (cachedStoreId && !forceResetStore) {
					let exists = this.storeList.find(s => String(s.store_id || s.id) === String(cachedStoreId));
					if (exists) {
						targetId = exists.store_id || exists.id;
					}
				}

				// 4.2 否则，默认选择第 1 家门店（离用户当前位置最近的门店）
				if (!targetId) {
					targetId = this.storeList[0].store_id || this.storeList[0].id;
				}
			}

			// 5. 加载选定门店的详情
			await this.initByStoreId(targetId || site_id || '');

			// 6. 若已登录，异步拉取关联商家列表（老板+店员+归属）
			if (currentToken) {
				this.fetchMyShopList();
			}
		},

		// 获取商家/城市下的门店列表
		async fetchWebsiteStores(targetSiteId = null) {
			try {
				let website_id = uni.getStorageSync('website_id') || '';
				let site_id = targetSiteId !== null ? targetSiteId : (uni.getStorageSync('user_site_id') || uni.getStorageSync('site_id') || '');
				let params = {
					website_id,
					site_id,
					latitude: this.latitude || '',
					longitude: this.longitude || ''
				};
				let res = await websiteStore(params);
				if (res && res.code >= 0 && Array.isArray(res.data)) {
					this.storeList = res.data;
				} else {
					this.storeList = [];
				}
			} catch (e) {
				console.error('获取商家分店列表失败', e);
				this.storeList = [];
			}
		},

		// 根据指定的 store_id 初始化页面数据
		async initByStoreId(id) {
			this.currentStoreId = id || '';
			if (id) {
				uni.setStorageSync('store_id', id);
			}

			// 1. 获取门店详情（包含商户专属Banner、累计销量、粉丝量、距离）
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
				let cachedSiteId = uni.getStorageSync('user_site_id') || uni.getStorageSync('site_id') || '';
				let websiteId = uni.getStorageSync('website_id') || '';
				let params = {
					store_id: this.currentStoreId || '',
					site_id: cachedSiteId,
					website_id: websiteId,
					latitude: this.latitude || '',
					longitude: this.longitude || ''
				};
				let res = await storeDetail(params);
				if (res && res.code >= 0 && res.data) {
					this.sinfo = res.data;
					if (res.data.store_id) {
						this.currentStoreId = res.data.store_id;
						uni.setStorageSync('store_id', res.data.store_id);
					}
					if (res.data.site_id) {
						uni.setStorageSync('user_site_id', res.data.site_id);
						uni.setStorageSync('site_id', res.data.site_id);
						uni.setStorageSync('selected_site_id', res.data.site_id);
						uni.setStorageSync('index_shop_info', {
							site_id: res.data.site_id,
							site_name: res.data.site_name || res.data.store_name,
							store_id: res.data.store_id || '',
							store_name: res.data.store_name || res.data.site_name,
							logo: res.data.store_logo || res.data.store_image || res.data.logo || '',
							avatar: res.data.store_logo || res.data.store_image || res.data.logo || '',
							address: res.data.address || res.data.full_address || ''
						});
					}
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
				} else {
					this.sinfo = {};
				}
			} catch (e) {
				console.error('获取门店详情异常', e);
				this.sinfo = {};
			}
		},

		// 查询关注状态
		async fetchFollowStatus() {
			if (!uni.getStorageSync('token')) {
				this.guanzhu = 0;
				return;
			}
			try {
				let siteId = this.sinfo.site_id || uni.getStorageSync('user_site_id') || uni.getStorageSync('site_id') || '';
				let params = {
					store_id: this.currentStoreId,
					site_id: siteId
				};
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

		// 获取位置（增加缓存读取与超时兜底，防止权限弹窗未确认时阻塞整个页面请求）
		getUserLocation() {
			return new Promise((resolve) => {
				if (this.latitude && this.longitude) {
					return resolve({ latitude: this.latitude, longitude: this.longitude });
				}
				let cached = uni.getStorageSync('latlng');
				if (cached && cached.latitude && cached.longitude) {
					this.latitude = cached.latitude;
					this.longitude = cached.longitude;
					return resolve(cached);
				}
				let timer = setTimeout(() => {
					resolve(null);
				}, 1200);

				try {
					uni.getLocation({
						type: 'gcj02',
						success: (res) => {
							clearTimeout(timer);
							this.latitude = res.latitude;
							this.longitude = res.longitude;
							uni.setStorageSync('latlng', {
								latitude: res.latitude,
								longitude: res.longitude
							});
							resolve(res);
						},
						fail: (err) => {
							clearTimeout(timer);
							resolve(null);
						}
					});
				} catch (e) {
					clearTimeout(timer);
					resolve(null);
				}
			});
		},

		// 获取当前城市（同城风格，默认展示定位/缓存城市）
		initCurrentCity() {
			let addressInfo = uni.getStorageSync('address_info');
			if (addressInfo && (addressInfo.city || addressInfo.name)) {
				this.currentCity = addressInfo.city || addressInfo.name;
			} else {
				let cachedCity = uni.getStorageSync('city');
				if (cachedCity) {
					this.currentCity = cachedCity;
				}
			}
			if (!this.currentCity) {
				let latlng = uni.getStorageSync('latlng');
				if (latlng && latlng.latitude && latlng.longitude) {
					tranAddressInfo({ latlng: `${latlng.latitude},${latlng.longitude}` }).then(res => {
						if (res && res.data && res.data.city) {
							this.currentCity = res.data.city;
							uni.setStorageSync('address_info', res.data);
						}
					}).catch(() => { });
				}
			}
		},

		// 一键导航到门店
		openNavigation() {
			let lat = Number(this.sinfo.latitude);
			let lng = Number(this.sinfo.longitude);
			if (!lat || !lng) {
				uni.showToast({ title: '暂未获取到门店位置', icon: 'none' });
				return;
			}
			uni.openLocation({
				latitude: lat,
				longitude: lng,
				name: this.sinfo.store_name || '门店位置',
				address: this.sinfo.full_address || this.sinfo.address || ''
			});
		},

		// 一键拨打门店电话
		callStorePhone() {
			let phone = this.sinfo.telphone || this.sinfo.telephone;
			if (!phone) {
				uni.showToast({ title: '暂未提供联系电话', icon: 'none' });
				return;
			}
			this.makePhoneCall(phone);
		},

		// 切换积分商品
		togglePointGoods() {
			this.isPointGoods = !this.isPointGoods;
			this.showCategoryDropdown = false;
			this.page = 1;
			this.fetchGoodsList();
		},

		// 拨打门店电话
		makePhoneCall(phone) {
			if (!phone) return;
			uni.makePhoneCall({
				phoneNumber: String(phone)
			});
		},

		// 获取商品列表
		async fetchGoodsList() {
			this.loading = true;
			try {
				let params = {
					page: this.page,
					page_size: this.pageSize,
					site_id: this.sinfo.site_id || uni.getStorageSync('user_site_id') || uni.getStorageSync('site_id') || '',
					category_id: this.currentCategoryId || 0,
					goods_type: this.isPointGoods ? 1 : 0
				};

				// 排序参数与位置距离
				let storeLat = this.sinfo.latitude || this.latitude;
				let storeLng = this.sinfo.longitude || this.longitude;
				if (storeLat && storeLng) {
					params.latitude = storeLat;
					params.longitude = storeLng;
				}

				if (this.currentSortType === 'sale_num') {
					params.order = 'sale_num';
					params.sort = this.sortOrder;
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
			let siteId = this.sinfo.site_id || uni.getStorageSync('user_site_id') || uni.getStorageSync('site_id') || '';
			let params = {
				store_id: this.currentStoreId,
				site_id: siteId
			};
			if (this.guanzhu == 0) {
				shopmemberAdd(params).then(res => {
					if (res && res.code >= 0) {
						uni.showToast({ title: '关注成功', icon: 'none' });
						this.guanzhu = 1;
						let newNum = (res.data && res.data.total_num !== undefined)
							? Number(res.data.total_num)
							: (parseInt(this.sinfo.total_num || 0) + 1);
						this.sinfo = {
							...this.sinfo,
							total_num: newNum
						};
					} else {
						uni.showToast({ title: (res && res.message) || '关注失败', icon: 'none' });
					}
				}).catch(() => {
					uni.showToast({ title: '关注失败', icon: 'none' });
				});
			} else {
				shopmemberDel(params).then(res => {
					if (res && res.code >= 0) {
						uni.showToast({ title: '已取消关注', icon: 'none' });
						this.guanzhu = 0;
						let newNum = (res.data && res.data.total_num !== undefined)
							? Number(res.data.total_num)
							: Math.max(0, parseInt(this.sinfo.total_num || 0) - 1);
						this.sinfo = {
							...this.sinfo,
							total_num: newNum
						};
					} else {
						uni.showToast({ title: (res && res.message) || '取消失败', icon: 'none' });
					}
				}).catch(() => {
					uni.showToast({ title: '操作失败', icon: 'none' });
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
			this.isPointGoods = false;
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

		// 拉取我的关联商家店铺列表（老板+店员+归属）
		async fetchMyShopList(openAfterFetch = false) {
			if (!uni.getStorageSync('token')) {
				this.myShops = [];
				return;
			}
			try {
				let res = await myShopList({
					latitude: this.latitude || '',
					longitude: this.longitude || ''
				});
				if (res && res.code >= 0 && Array.isArray(res.data)) {
					this.myShops = res.data;
				} else {
					this.myShops = [];
				}
				if (openAfterFetch) {
					this.$refs.merchantSelectPopup.open();
				}
			} catch (e) {
				console.error('获取关联商家列表失败', e);
				if (openAfterFetch) {
					this.$refs.merchantSelectPopup.open();
				}
			}
		},

		// 打开商家切换弹窗
		openMerchantSelect() {
			if (!uni.getStorageSync('token')) {
				this.$refs.login.open();
				return;
			}
			if (this.myShops.length === 0) {
				this.fetchMyShopList(true);
			} else {
				this.$refs.merchantSelectPopup.open();
			}
		},

		// 关闭商家切换弹窗
		closeMerchantSelect() {
			this.$refs.merchantSelectPopup.close();
		},

		// 判断店铺是否当前选中
		isCurrentShop(shop) {
			if (!shop || !shop.site_id) return false;
			return String(shop.site_id) === String(this.activeSiteId || (this.sinfo && this.sinfo.site_id) || '');
		},

		// 切换选中的商家店铺
		async onSelectMerchant(shop) {
			let siteId = shop.site_id;
			if (!siteId) return;
			this.closeMerchantSelect();
			if (String(siteId) === String(this.activeSiteId)) {
				return;
			}
			uni.showLoading({ title: '切换店铺中...' });
			uni.setStorageSync('selected_site_id', siteId);
			uni.setStorageSync('user_site_id', siteId);
			uni.setStorageSync('site_id', siteId);
			uni.removeStorageSync('store_id');
			this.activeSiteId = siteId;

			// 重新拉取新商家的线下分店
			await this.fetchWebsiteStores(siteId);
			let targetStoreId = '';
			if (this.storeList.length > 0) {
				targetStoreId = this.storeList[0].store_id || this.storeList[0].id;
			}
			await this.initByStoreId(targetStoreId || siteId);
			uni.hideLoading();
			uni.showToast({
				title: '已切换至: ' + (shop.site_name || '新店铺'),
				icon: 'none'
			});
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
				delta: 1,
				fail: () => {
					uni.switchTab({
						url: '/pages/index/index'
					});
				}
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
			this.initCurrentCity();
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

/* 顶部Tab模式导航栏：当前城市与固定城盟优选 */
.nav-city-wrap {
	display: flex;
	align-items: center;
	padding-left: 4rpx;

	.city-icon {
		width: 32rpx;
		height: 32rpx;
		margin-right: 8rpx;
		flex-shrink: 0;
	}

	.city-name-text {
		font-size: 30rpx;
		font-weight: 600;
		color: #FFFFFF;
		max-width: 220rpx;
	}
}

.nav-fixed-title {
	font-size: 34rpx;
	font-weight: 600;
	color: #FFFFFF;
}

/* 顶部导航商家切换触发器 */
.nav-shop-switch-wrap {
	max-width: 380rpx;
	cursor: pointer;
	flex-wrap: nowrap !important;
	white-space: nowrap;

	.nav-shop-title {
		font-size: 32rpx;
		font-weight: 600;
		color: #FFFFFF;
		max-width: 260rpx;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.nav-switch-badge {
		background: rgba(255, 255, 255, 0.25);
		border: 1rpx solid rgba(255, 255, 255, 0.6);
		padding: 2rpx 10rpx;
		border-radius: 16rpx;
		margin-left: 8rpx;
		line-height: 24rpx;
		flex-shrink: 0;

		text {
			font-size: 18rpx;
			color: #FFFFFF;
			font-weight: 600;
			white-space: nowrap;
		}
	}
}

.navbar-center-title {
	font-size: 32rpx;
	font-weight: 600;
	color: #1A1A1A;
}

.nav-detail-back {
	padding: 10rpx 20rpx 10rpx 0;
	display: flex;
	align-items: center;

	.detail-back-icon {
		width: 44rpx;
		height: 44rpx;
		display: block;
	}
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
	margin: -20rpx 24rpx 24rpx 24rpx;
	padding: 28rpx 24rpx;
	background: #FFFFFF;
	border-radius: 24rpx;
	box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.04);
	position: relative;
	z-index: 12;

	.store-main-row {
		margin-bottom: 16rpx;

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

				.store-address-row {
					margin-top: 6rpx;
					gap: 8rpx;
					overflow: hidden;

					.store-dist-tag {
						background: #E8F7F0;
						color: #07C160;
						font-size: 20rpx;
						font-weight: 600;
						padding: 2rpx 8rpx;
						border-radius: 6rpx;
						flex-shrink: 0;
					}

					.store-address-text {
						font-size: 22rpx;
						color: #999999;
						line-height: 32rpx;
						flex: 1;
					}
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

	/* 门店名称下方一行：营业时间、一键导航、一键电话、品牌粉丝群 */
	.store-service-row {
		margin-bottom: 22rpx;
		padding: 12rpx 18rpx;
		background: #FFF9F5;
		border-radius: 16rpx;
		border: 1rpx solid #FFE7D9;

		.service-time-box {
			flex: 1;
			overflow: hidden;
			margin-right: 12rpx;

			.service-time-label {
				font-size: 22rpx;
				color: #FF5500;
				font-weight: 500;
				margin-left: 6rpx;
				margin-right: 8rpx;
				flex-shrink: 0;
			}

			.service-time-val {
				font-size: 22rpx;
				color: #666666;
				flex: 1;
			}
		}

		.service-actions-box {
			flex-shrink: 0;
			gap: 8rpx;

			.service-btn {
				padding: 6rpx 14rpx;
				background: #FFFFFF;
				border-radius: 20rpx;
				border: 1rpx solid #FFD8C2;
				cursor: pointer;
				transition: all 0.2s ease;

				.btn-txt {
					font-size: 22rpx;
					font-weight: 500;
					color: #FF5500;
					margin-left: 4rpx;
				}

				&:active {
					opacity: 0.7;
				}

				&.group-btn {
					background: #EDFBF3;
					border-color: #B2E7C6;

					.group-txt {
						color: #07C160;
					}
				}
			}

			.btn-divider {
				width: 1rpx;
				height: 20rpx;
				background: #EED2C2;
			}
		}
	}

	/* 核心数据与关注门店按钮行 */
	.stats-action-row {
		.stat-item {
			flex: 1;
			text-align: center;

			.stat-num {
				font-size: 34rpx;
				font-weight: 700;
				color: #222222;
				line-height: 38rpx;
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
				width: 170rpx;
				height: 58rpx;
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

				.follow-text {
					color: #fff;
				}

				&.followed {
					background: #FFF2EB;
					color: #F55606;
					box-shadow: none;
					border: 1rpx solid rgba(245, 86, 6, 0.3);

					.follow-text {
						color: #F55606;
					}
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
			flex-wrap: nowrap;

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

			&.point-filter-item {
				padding: 6rpx 16rpx;
				border-radius: 24rpx;
				transition: all 0.2s ease;

				.filter-text {
					margin-left: 6rpx;
					margin-right: 0;
				}

				&.point-active {
					background: #FFF2EB;

					.filter-text {
						color: #F55606;
						font-weight: 600;
					}
				}
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

					&.point-tag {
						background: linear-gradient(135deg, #FF9500 0%, #FF5500 100%);
					}
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

					text {
						color: #6f6f6f;
					}
				}

				.price-action-row {
					margin-top: 10rpx;

					.price-box {
						line-height: 1;

						.price-symbol {
							font-size: 24rpx;
							font-weight: 700;
							color: #F55606;
						}

						.price-integer {
							font-size: 36rpx;
							font-weight: 700;
							color: #F55606;
						}

						.price-decimal {
							font-size: 24rpx;
							font-weight: 700;
							color: #F55606;
						}

						.point-num {
							font-size: 34rpx;
							font-weight: 700;
							color: #F55606;
						}

						.point-unit {
							font-size: 22rpx;
							font-weight: 600;
							color: #F55606;
							margin-left: 2rpx;
							margin-right: 4rpx;
						}

						.point-plus {
							font-size: 22rpx;
							font-weight: 600;
							color: #F55606;
							margin-right: 4rpx;
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

						&.point-buy-btn {
							background: linear-gradient(135deg, #FF9500 0%, #FF5500 100%);
						}
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
	padding: 32rpx 32rpx 48rpx 32rpx;
	max-height: 75vh;

	.popup-title {
		margin-bottom: 24rpx;

		.popup-title-text {
			font-size: 32rpx;
			font-weight: 600;
			color: #222222;
		}

		.popup-store-count {
			font-size: 24rpx;
			color: #999999;
			margin-left: 6rpx;
		}
	}

	.store-select-scroll {
		max-height: 60vh;

		.store-option-item {
			padding: 24rpx 0;
			border-bottom: 1rpx solid #F5F5F5;

			.store-opt-left {
				flex: 1;
				overflow: hidden;

				.store-opt-img {
					width: 96rpx;
					height: 96rpx;
					border-radius: 14rpx;
					margin-right: 18rpx;
					flex-shrink: 0;
					background: #F8F8F8;
				}

				.store-opt-info {
					flex: 1;
					overflow: hidden;

					.store-opt-name-row {
						gap: 10rpx;

						.store-opt-name {
							font-size: 28rpx;
							font-weight: 600;
							color: #222222;
							line-height: 38rpx;
						}

						.nearest-badge {
							background: linear-gradient(135deg, #07C160 0%, #10B981 100%);
							color: #FFFFFF;
							font-size: 18rpx;
							font-weight: 600;
							padding: 2rpx 10rpx;
							border-radius: 6rpx;
							flex-shrink: 0;
						}
					}

					.store-opt-meta {
						margin-top: 8rpx;
						gap: 16rpx;
						font-size: 22rpx;

						.store-opt-distance {
							color: #F55606;
							font-weight: 600;
						}

						.store-opt-tel {
							color: #4B7902;
							background: #F2F7EC;
							padding: 2rpx 10rpx;
							border-radius: 6rpx;
						}
					}

					.store-opt-addr {
						font-size: 22rpx;
						color: #999999;
						margin-top: 6rpx;
						line-height: 32rpx;
					}
				}
			}

			.store-opt-right {
				margin-left: 20rpx;
				flex-shrink: 0;

				.select-btn {
					padding: 8rpx 24rpx;
					border-radius: 24rpx;
					border: 2rpx solid #E0E0E0;
					font-size: 22rpx;
					color: #666666;
				}
			}

			&.active {
				.store-opt-name {
					color: #F55606;
				}
			}
		}
	}
}

/* 商家店铺切换弹窗样式 */
.merchant-popup-container {
	background: #FFFFFF;
	padding: 32rpx 28rpx 48rpx;
	box-sizing: border-box;

	.popup-title {
		padding-bottom: 24rpx;
		border-bottom: 1rpx solid #F0F0F0;

		.popup-title-text {
			font-size: 32rpx;
			font-weight: 700;
			color: #1A1A1A;
		}

		.popup-store-count {
			font-size: 24rpx;
			color: #999999;
			margin-left: 8rpx;
		}
	}

	.merchant-select-scroll {
		max-height: 60vh;
		min-height: 200rpx;
		padding-top: 16rpx;

		.merchant-option-item {
			padding: 24rpx 16rpx;
			border-radius: 16rpx;
			margin-bottom: 16rpx;
			background: #FAFAFA;
			transition: all 0.2s;

			&:last-child {
				margin-bottom: 0;
			}

			.merchant-opt-left {
				flex: 1;
				overflow: hidden;

				.merchant-opt-img {
					width: 96rpx;
					height: 96rpx;
					border-radius: 14rpx;
					margin-right: 18rpx;
					flex-shrink: 0;
					background: #ECECEC;
				}

				.merchant-opt-info {
					flex: 1;
					overflow: hidden;

					.merchant-opt-name-row {
						gap: 12rpx;

						.merchant-opt-name {
							font-size: 28rpx;
							font-weight: 600;
							color: #222222;
							line-height: 38rpx;
							max-width: 320rpx;
						}

						/* 身份徽标样式 */
						.shop-role-tag {
							font-size: 18rpx;
							font-weight: 600;
							padding: 2rpx 10rpx;
							border-radius: 6rpx;
							flex-shrink: 0;

							&.tag-owner {
								background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
								color: #FFFFFF;
							}

							&.tag-staff {
								background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
								color: #FFFFFF;
							}

							&.tag-member {
								background: linear-gradient(135deg, #10B981 0%, #059669 100%);
								color: #FFFFFF;
							}
						}
					}

					.merchant-opt-meta {
						margin-top: 8rpx;
						gap: 16rpx;
						font-size: 22rpx;

						.merchant-opt-distance {
							color: #F55606;
							font-weight: 600;
						}

						.merchant-opt-tel {
							color: #4B7902;
							background: #F2F7EC;
							padding: 2rpx 10rpx;
							border-radius: 6rpx;
						}
					}

					.merchant-opt-addr {
						font-size: 22rpx;
						color: #999999;
						margin-top: 6rpx;
						line-height: 32rpx;
					}
				}
			}

			.merchant-opt-right {
				margin-left: 20rpx;
				flex-shrink: 0;

				.select-btn {
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

				.merchant-opt-name {
					color: #FF5500;
				}
			}
		}

		.merchant-empty-box {
			padding: 60rpx 0;
			text-align: center;

			.empty-tip {
				font-size: 26rpx;
				color: #999999;
			}
		}
	}
}
</style>
