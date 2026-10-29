<template>
  <view class="nearby-container">
    <!-- 顶部类目滑动导航 (第一外卖、第二自提、第三预约，最后一项为全部) -->
    <scroll-view scroll-x class="category-scroll">
      <view class="category-row">
        <view
          v-for="cat in categories"
          :key="cat.id"
          class="cat-chip"
          :class="{ active: currentCat === cat.id }"
          @tap="currentCat = cat.id"
        >
          <text>{{ cat.name }}</text>
        </view>
      </view>
    </scroll-view>

    <!-- 空间门店列表 (单品牌多门店，统一LOGO聚合展示) -->
    <view class="brand-list">
      <view v-for="shop in shopList" :key="shop.id" class="brand-card">
        <view class="brand-header">
          <image class="brand-logo" :src="shop.brandLogo" mode="aspectFill" />
          <view class="brand-info">
            <view class="brand-name-row">
              <text class="brand-title">{{ shop.name }}</text>
              <nut-tag type="primary" size="small">{{ shop.branchCount }}家连锁分店</nut-tag>
            </view>
            <view class="brand-dist">
              最近分店距离您 {{ shop.nearestDistance }} · {{ shop.address }}
            </view>
          </view>
        </view>

        <!-- 门店下方展示 3-4 个主推热卖商品 -->
        <scroll-view scroll-x class="showcase-scroll">
          <view class="showcase-row">
            <view
              v-for="goods in shop.showcaseGoods"
              :key="goods.id"
              class="showcase-item"
            >
              <image class="showcase-img" :src="goods.cover" mode="aspectFill" />
              <view class="showcase-name">{{ goods.name }}</view>
              <view class="showcase-price">￥{{ goods.price }}</view>
            </view>
          </view>
        </scroll-view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const currentCat = ref('all');

const categories = ref([
  { id: 'takeout', name: '同城外卖' },
  { id: 'pickup', name: '商品自提' },
  { id: 'appointment', name: '门店预约' },
  { id: 'pet', name: '萌宠生活' },
  { id: 'food', name: '餐饮美食' },
  { id: 'all', name: '全部类目' },
]);

const shopList = ref([
  {
    id: 's1',
    name: '梵优茗宠品牌',
    brandLogo: 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=100',
    branchCount: 3,
    nearestDistance: '850m',
    address: '西湖大道中心旗舰店',
    showcaseGoods: [
      { id: 'g1', name: '手作美式咖啡', price: '28.00', cover: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=150' },
      { id: 'g2', name: '宠物专业精修SPA', price: '168.00', cover: 'https://images.unsplash.com/photo-1548767797-d8c844163c4c?w=150' },
      { id: 'g3', name: '特级纯鸡肉冻干', price: '128.00', cover: 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=150' },
    ],
  },
]);
</script>

<style>
.nearby-container {
  padding: 10px;
  background-color: #f8f9fa;
  min-height: 100vh;
}
.category-scroll {
  white-space: nowrap;
  margin-bottom: 12px;
}
.category-row {
  display: flex;
  gap: 8px;
}
.cat-chip {
  padding: 6px 14px;
  border-radius: 20px;
  background: #fff;
  font-size: 13px;
  color: #666;
}
.cat-chip.active {
  background: #ff5000;
  color: #fff;
  font-weight: 600;
}
.brand-card {
  background: #fff;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 12px;
}
.brand-header {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}
.brand-logo {
  width: 50px;
  height: 50px;
  border-radius: 6px;
  margin-right: 10px;
}
.brand-info {
  flex: 1;
}
.brand-name-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.brand-title {
  font-size: 15px;
  font-weight: 700;
  color: #333;
}
.brand-dist {
  font-size: 11px;
  color: #999;
  margin-top: 4px;
}
.showcase-scroll {
  white-space: nowrap;
}
.showcase-row {
  display: flex;
  gap: 8px;
}
.showcase-item {
  width: 90px;
  flex-shrink: 0;
}
.showcase-img {
  width: 90px;
  height: 90px;
  border-radius: 6px;
}
.showcase-name {
  font-size: 11px;
  color: #333;
  margin-top: 4px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.showcase-price {
  font-size: 12px;
  font-weight: 700;
  color: #ff5000;
}
</style>
