<template>
  <view class="signin-admin-container">
    <view class="qr-card">
      <view class="qr-title">门店到店扫码签到码</view>
      <view class="qr-sub">顾客到店出示扫码，领到店礼与现金红包</view>

      <image
        class="qr-code-img"
        src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=240"
        mode="aspectFit"
      />

      <view class="rules-box">
        <view class="rule-title">签到业务规则：</view>
        <view class="rule-item">1. 每位顾客每个自然月在当前门店仅限签到一次；</view>
        <view class="rule-item">2. 签到成功后，商户扣除 2.00 元运营费至平台；</view>
        <view class="rule-item">3. 平台即时奖励用户 1.00 元现金与 50 会员积分。</view>
      </view>

      <view class="time-stamp">
        当前核定时间: {{ currentFormattedTime }}
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

const currentFormattedTime = ref('');

onMounted(() => {
  const d = new Date();
  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  const hours = String(d.getHours()).padStart(2, '0');
  const mins = String(d.getMinutes()).padStart(2, '0');
  const secs = String(d.getSeconds()).padStart(2, '0');
  currentFormattedTime.value = `${year}-${month}-${day} ${hours}:${mins}:${secs}`;
});
</script>

<style>
.signin-admin-container {
  padding: 16px;
  background-color: #f4f6f8;
  min-height: 100vh;
}
.qr-card {
  background: #fff;
  border-radius: 12px;
  padding: 24px 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.qr-title {
  font-size: 17px;
  font-weight: 700;
  color: #333;
}
.qr-sub {
  font-size: 12px;
  color: #999;
  margin-top: 4px;
}
.qr-code-img {
  width: 220px;
  height: 220px;
  margin: 20px 0;
}
.rules-box {
  width: 100%;
  background: #fafafa;
  border-radius: 8px;
  padding: 12px;
}
.rule-title {
  font-size: 12px;
  font-weight: 700;
  color: #666;
  margin-bottom: 6px;
}
.rule-item {
  font-size: 11px;
  color: #888;
  line-height: 1.6;
}
.time-stamp {
  font-size: 11px;
  color: #999;
  margin-top: 16px;
}
</style>
