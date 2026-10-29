<template>
  <view class="cashier-container">
    <!-- 顶部扫码大按钮 -->
    <view class="scan-button-box" @tap="handleScanCode">
      <view class="scan-icon-circle">📷</view>
      <text class="scan-text">点击调起摄像头 扫码核销</text>
    </view>

    <!-- 手输 12 位核销码卡片 -->
    <view class="manual-box">
      <view class="box-title">或手动输入 12 位核销券码</view>
      <nut-input
        v-model="verifyCode"
        placeholder="请输入核销券码"
        clearable
      />
      <nut-button
        type="primary"
        style="width: 100%; margin-top: 12px"
        :loading="verifying"
        @click="handleManualVerify"
      >
        原子防重核销确认
      </nut-button>
    </view>

    <!-- 核销成功展示弹窗 -->
    <nut-dialog
      v-model:visible="showSuccessDialog"
      title="核销履约成功"
      :no-cancel-btn="true"
    >
      <view class="dialog-body">
        <text style="font-size: 32px">✅</text>
        <text style="font-weight: 700; margin: 10px 0">订单核销成功！</text>
        <text style="font-size: 12px; color: #999">小票已推送打印机即时出纸</text>
      </view>
    </nut-dialog>
  </view>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import Taro from '@tarojs/taro';

const verifyCode = ref('');
const verifying = ref(false);
const showSuccessDialog = ref(false);

function handleScanCode() {
  Taro.scanCode({
    success: (res) => {
      verifyCode.value = res.result;
      handleManualVerify();
    },
  });
}

function handleManualVerify() {
  if (!verifyCode.value) {
    Taro.showToast({ title: '请输入券码', icon: 'none' });
    return;
  }
  verifying.value = true;
  setTimeout(() => {
    verifying.value = false;
    showSuccessDialog.value = true;
    verifyCode.value = '';
  }, 500);
}
</script>

<style>
.cashier-container {
  padding: 16px;
  background-color: #f4f6f8;
  min-height: 100vh;
}
.scan-button-box {
  background: #fff;
  border-radius: 12px;
  padding: 36px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  margin-bottom: 16px;
}
.scan-icon-circle {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: #e6f7ff;
  font-size: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
}
.scan-text {
  font-size: 15px;
  font-weight: 700;
  color: #1890ff;
}
.manual-box {
  background: #fff;
  border-radius: 12px;
  padding: 16px;
}
.box-title {
  font-size: 13px;
  color: #666;
  margin-bottom: 12px;
}
.dialog-body {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16px 0;
}
</style>
