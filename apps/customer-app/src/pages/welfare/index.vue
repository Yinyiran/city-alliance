<template>
  <view class="welfare-container">
    <!-- 用户当前可用积分卡片 -->
    <view class="points-card">
      <view class="points-label">我的可用积分</view>
      <view class="points-num">1,250</view>
      <view class="points-desc">看短视频、到店签到、日常消费均可赚取积分</view>
    </view>

    <!-- 纯积分兑换商品列表 -->
    <view class="section-title">积分免费换好礼</view>
    <view class="welfare-grid">
      <view v-for="item in welfareGoods" :key="item.id" class="welfare-card">
        <image class="welfare-img" :src="item.cover" mode="aspectFill" />
        <view class="welfare-name">{{ item.name }}</view>
        <view class="welfare-bottom">
          <text class="welfare-pts">{{ item.pointsNeed }} 积分</text>
          <nut-tag :type="item.deliveryType === '自提' ? 'success' : 'primary'" size="small">
            {{ item.deliveryType }}
          </nut-tag>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const welfareGoods = ref([
  {
    id: 'w1',
    name: '品牌定制帆布环保袋',
    cover: 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=150',
    pointsNeed: 300,
    deliveryType: '自提',
  },
  {
    id: 'w2',
    name: '特级冻干零食试吃装',
    cover: 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=150',
    pointsNeed: 500,
    deliveryType: '包邮',
  },
]);
</script>

<style>
.welfare-container {
  padding: 12px;
  background-color: #f8f9fa;
  min-height: 100vh;
}
.points-card {
  background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
  border-radius: 10px;
  padding: 20px;
  color: #fff;
  margin-bottom: 16px;
}
.points-label {
  font-size: 13px;
  opacity: 0.9;
}
.points-num {
  font-size: 32px;
  font-weight: 800;
  margin: 6px 0;
}
.points-desc {
  font-size: 11px;
  opacity: 0.85;
}
.section-title {
  font-size: 15px;
  font-weight: 700;
  color: #333;
  margin-bottom: 10px;
}
.welfare-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.welfare-card {
  width: calc(50% - 5px);
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  padding-bottom: 8px;
}
.welfare-img {
  width: 100%;
  height: 130px;
}
.welfare-name {
  font-size: 12px;
  font-weight: 600;
  padding: 6px 8px;
}
.welfare-bottom {
  padding: 0 8px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.welfare-pts {
  font-size: 13px;
  font-weight: 700;
  color: #ff5000;
}
</style>
