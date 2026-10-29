<template>
  <view class="user-container">
    <!-- 用户信息卡片与 6 位数会员 ID -->
    <view class="user-card">
      <image class="avatar" :src="member.avatar" mode="aspectFill" />
      <view class="user-main">
        <view class="user-name-row">
          <text class="nickname">{{ member.nickname }}</text>
          <nut-tag :type="member.levelTagType" size="small">{{ member.levelName }}</nut-tag>
        </view>
        <view class="code-row">
          <text class="member-id">会员号: {{ member.memberCode }}</text>
          <text class="inviter-text">邀请人: {{ member.inviterName }}</text>
        </view>
      </view>
    </view>

    <!-- 会员权益折上折说明卡 -->
    <view class="equity-banner">
      <view class="equity-title">👑 专属尊享会员权益</view>
      <view class="equity-sub">当前享有: 全场商品折上 {{ member.discountText }}，消费送 {{ member.pointsRatio }} 倍积分</view>
    </view>

    <!-- 资产与钱包数据看板 (可用余额 + 7天延迟解冻收益) -->
    <view class="wallet-card">
      <view class="wallet-col">
        <view class="wallet-val">￥{{ member.balance }}</view>
        <view class="wallet-lbl">可用余额</view>
      </view>
      <view class="wallet-col">
        <view class="wallet-val frozen">￥{{ member.frozenBalance }}</view>
        <view class="wallet-lbl">7天解冻中</view>
      </view>
      <view class="wallet-col">
        <view class="wallet-val">{{ member.points }}</view>
        <view class="wallet-lbl">会员积分</view>
      </view>
    </view>

    <!-- 常用功能列表 -->
    <view class="menu-list">
      <nut-cell title="我的全部订单" is-link />
      <nut-cell title="申请实名提现 (阶梯个税代扣)" is-link sub-title="满100元起提" />
      <nut-cell title="到店扫码签到记录" is-link />
      <nut-cell title="联系在线客服" is-link />
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const member = ref({
  memberCode: '100088',
  nickname: '张三 (创客合伙人)',
  avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100',
  levelName: '创客合伙人',
  levelTagType: 'danger',
  discountText: '95 折',
  pointsRatio: '1.5',
  inviterName: '李老板 (100001)',
  balance: '3580.00',
  frozenBalance: '820.00',
  points: 12500,
});
</script>

<style>
.user-container {
  padding: 12px;
  background-color: #f8f9fa;
  min-height: 100vh;
}
.user-card {
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 10px;
  padding: 16px;
  margin-bottom: 12px;
}
.avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  margin-right: 12px;
}
.user-main {
  flex: 1;
}
.user-name-row {
  display: flex;
  align-items: center;
  gap: 8px;
}
.nickname {
  font-size: 16px;
  font-weight: 700;
  color: #333;
}
.code-row {
  margin-top: 6px;
  display: flex;
  gap: 12px;
}
.member-id {
  font-size: 12px;
  color: #409eff;
  font-weight: 600;
}
.inviter-text {
  font-size: 12px;
  color: #999;
}
.equity-banner {
  background: linear-gradient(135deg, #2b2e4a 0%, #1e222d 100%);
  color: #f7d794;
  padding: 14px 16px;
  border-radius: 8px;
  margin-bottom: 12px;
}
.equity-title {
  font-size: 14px;
  font-weight: 700;
}
.equity-sub {
  font-size: 11px;
  margin-top: 4px;
  opacity: 0.9;
}
.wallet-card {
  background: #fff;
  border-radius: 10px;
  padding: 16px 0;
  display: flex;
  margin-bottom: 16px;
}
.wallet-col {
  flex: 1;
  text-align: center;
}
.wallet-val {
  font-size: 18px;
  font-weight: 700;
  color: #333;
}
.wallet-val.frozen {
  color: #e6a23c;
}
.wallet-lbl {
  font-size: 11px;
  color: #999;
  margin-top: 4px;
}
.menu-list {
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
}
</style>
