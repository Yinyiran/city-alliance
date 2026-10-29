<template>
  <view class="merchant-home-container">
    <view class="shop-badge-card">
      <view class="shop-title">梵优茗宠·主理人工作台</view>
      <view class="shop-sub">实体门店: 西湖大道中心旗舰店</view>
    </view>

    <!-- 收益双轨看板 (自签单收益 vs 跨店带单收益) -->
    <view class="board-grid">
      <view class="board-col">
        <view class="board-num">￥1,680.00</view>
        <view class="board-desc">今日自签单 (扣2%)</view>
      </view>
      <view class="board-col">
        <view class="board-num cross">￥420.50</view>
        <view class="board-desc">跨店带单分佣 (扣6%)</view>
      </view>
    </view>

    <!-- 常用功能九宫格 -->
    <view class="action-grid">
      <view class="action-item" @tap="navigateTo('/pages/cashier/index')">
        <view class="action-icon">📱</view>
        <view class="action-text">扫码核销</view>
      </view>
      <view class="action-item" @tap="navigateTo('/pages/signin/index')">
        <view class="action-icon">📍</view>
        <view class="action-text">到店签到码</view>
      </view>
      <view class="action-item" @tap="navigateTo('/pages/user/index')">
        <view class="action-icon">💰</view>
        <view class="action-text">主理人收益账本</view>
      </view>
      <view class="action-item">
        <view class="action-icon">🖨️</view>
        <view class="action-text">云打印机管理</view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import Taro from '@tarojs/taro';

function navigateTo(url: string) {
  Taro.switchTab({ url }).catch(() => {
    Taro.navigateTo({ url });
  });
}
</script>

<style>
.merchant-home-container {
  padding: 12px;
  background-color: #f4f6f8;
  min-height: 100vh;
}
.shop-badge-card {
  background: #1e222d;
  color: #fff;
  padding: 18px 16px;
  border-radius: 8px;
  margin-bottom: 12px;
}
.shop-title {
  font-size: 17px;
  font-weight: 700;
}
.shop-sub {
  font-size: 12px;
  color: #a0a6b5;
  margin-top: 4px;
}
.board-grid {
  display: flex;
  gap: 10px;
  margin-bottom: 16px;
}
.board-col {
  flex: 1;
  background: #fff;
  border-radius: 8px;
  padding: 16px;
}
.board-num {
  font-size: 20px;
  font-weight: 800;
  color: #1890ff;
}
.board-num.cross {
  color: #fa8c16;
}
.board-desc {
  font-size: 12px;
  color: #999;
  margin-top: 4px;
}
.action-grid {
  display: flex;
  flex-wrap: wrap;
  background: #fff;
  border-radius: 8px;
  padding: 16px 0;
}
.action-item {
  width: 25%;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.action-icon {
  font-size: 28px;
  margin-bottom: 6px;
}
.action-text {
  font-size: 12px;
  color: #333;
}
</style>
