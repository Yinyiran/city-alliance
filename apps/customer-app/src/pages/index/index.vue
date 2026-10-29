<template>
  <view class="home-container">
    <!-- 顶部主理人门店信息与关注 -->
    <view class="store-header">
      <image class="store-logo" :src="currentStore.brandLogo" mode="aspectFill" />
      <view class="store-info">
        <view class="store-name">{{ currentStore.storeName }}</view>
        <view class="store-stats">
          粉丝量 {{ currentStore.fanCount }} · 累计出单 {{ currentStore.salesCount }}
        </view>
      </view>
      <nut-button type="primary" size="small" class="follow-btn">
        + 关注门店
      </nut-button>
    </view>

    <!-- 轮播图 (商家Banner + 平台活动Banner) -->
    <nut-swiper :init-page="0" :auto-play="3000" class="banner-swiper">
      <nut-swiper-item v-for="(banner, index) in banners" :key="index">
        <image :src="banner" class="banner-img" mode="aspectFill" />
      </nut-swiper-item>
    </nut-swiper>

    <!-- 进企微私域群快捷入口 -->
    <view class="wecom-entry" @tap="showWeComDialog = true">
      <view class="wecom-left">
        <text class="wecom-title">💬 加入门店专属企微福利群</text>
        <text class="wecom-sub">领到店伴手礼，抢每日大额同城优惠券</text>
      </view>
      <nut-button type="warning" size="mini">立即进群</nut-button>
    </view>

    <!-- 门店热卖自有商品推荐 -->
    <view class="section-title">门店精选自营</view>
    <view class="goods-grid">
      <view v-for="goods in goodsList" :key="goods.id" class="goods-card">
        <image class="goods-img" :src="goods.cover" mode="aspectFill" />
        <view class="goods-title">{{ goods.name }}</view>
        <view class="goods-price-row">
          <text class="price-symbol">￥</text>
          <text class="price-val">{{ goods.price }}</text>
          <nut-tag type="danger" size="small" style="margin-left: 6px">
            {{ goods.tradeTypeName }}
          </nut-tag>
        </view>
      </view>
    </view>

    <!-- 企微私域活码轮换弹窗 -->
    <nut-dialog
      v-model:visible="showWeComDialog"
      title="欢迎加入专属微信交流群"
      :no-cancel-btn="true"
    >
      <view class="wecom-dialog-body">
        <image
          class="wecom-qr-img"
          src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=200"
          mode="aspectFit"
          :show-menu-by-longpress="true"
        />
        <text class="wecom-tip">长按上方二维码，识别加入官方微信群</text>
        <text class="wecom-notice">群满自动切换，尊享同城独家折扣</text>
      </view>
    </nut-dialog>
  </view>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const showWeComDialog = ref(false);

const currentStore = ref({
  brandLogo: 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=100',
  storeName: '梵优茗宠·西湖旗舰店',
  fanCount: 2840,
  salesCount: 8960,
});

const banners = ref([
  'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500',
  'https://images.unsplash.com/photo-1548767797-d8c844163c4c?w=500',
]);

const goodsList = ref([
  {
    id: '1',
    name: '精选手作拿铁咖啡 (到店自提)',
    cover: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=200',
    price: '38.00',
    tradeTypeName: '自提',
  },
  {
    id: '2',
    name: '宠物专业全身毛发精修 SPA',
    cover: 'https://images.unsplash.com/photo-1548767797-d8c844163c4c?w=200',
    price: '168.00',
    tradeTypeName: '预约',
  },
]);
</script>

<style>
.home-container {
  padding: 12px;
  background-color: #f8f9fa;
  min-height: 100vh;
}
.store-header {
  display: flex;
  align-items: center;
  background: #fff;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 12px;
}
.store-logo {
  width: 48px;
  height: 48px;
  border-radius: 6px;
  margin-right: 10px;
}
.store-info {
  flex: 1;
}
.store-name {
  font-size: 16px;
  font-weight: 700;
  color: #333;
}
.store-stats {
  font-size: 12px;
  color: #999;
  margin-top: 4px;
}
.banner-swiper {
  height: 160px;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 12px;
}
.banner-img {
  width: 100%;
  height: 100%;
}
.wecom-entry {
  background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
  padding: 12px 14px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}
.wecom-title {
  font-size: 14px;
  font-weight: 700;
  color: #d84315;
  display: block;
}
.wecom-sub {
  font-size: 11px;
  color: #e65100;
  margin-top: 2px;
  display: block;
}
.section-title {
  font-size: 16px;
  font-weight: 700;
  color: #333;
  margin-bottom: 10px;
}
.goods-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.goods-card {
  width: calc(50% - 5px);
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  padding-bottom: 8px;
}
.goods-img {
  width: 100%;
  height: 140px;
}
.goods-title {
  font-size: 13px;
  font-weight: 600;
  padding: 6px 8px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.goods-price-row {
  padding: 0 8px;
  display: flex;
  align-items: baseline;
}
.price-symbol {
  font-size: 12px;
  color: #ff5000;
  font-weight: 700;
}
.price-val {
  font-size: 16px;
  color: #ff5000;
  font-weight: 700;
}
.wecom-dialog-body {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 10px 0;
}
.wecom-qr-img {
  width: 180px;
  height: 180px;
  margin-bottom: 10px;
}
.wecom-tip {
  font-size: 13px;
  font-weight: 600;
  color: #333;
}
.wecom-notice {
  font-size: 11px;
  color: #999;
  margin-top: 4px;
}
</style>
