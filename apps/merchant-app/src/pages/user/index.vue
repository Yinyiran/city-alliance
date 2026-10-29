<template>
  <view class="merchant-wallet-container">
    <!-- 主理人总收益看板 -->
    <view class="wallet-header-card">
      <view class="wallet-title">可提现收益总额</view>
      <view class="wallet-amount">￥18,450.00</view>
      <view class="wallet-stats-row">
        <text>自签单收益: ￥14,200</text>
        <text style="margin-left: 12px">跨店带单收益: ￥4,250</text>
      </view>
    </view>

    <!-- 提现输入与实时试算 -->
    <view class="withdraw-card">
      <view class="card-title">申请提现至对公结算账户</view>
      <nut-input
        v-model="withdrawAmount"
        placeholder="请输入提现金额"
        type="digit"
        @update:model-value="calculateTaxPreview"
      />

      <view v-if="withdrawAmountNum > 0" class="tax-estimate-box">
        <view class="estimate-row">
          <text>申请金额:</text>
          <text>￥{{ withdrawAmountNum.toFixed(2) }}</text>
        </view>
        <view class="estimate-row">
          <text>平台提现手续费 (0.35%):</text>
          <text>- ￥{{ estimateFee.toFixed(2) }}</text>
        </view>
        <view class="estimate-row">
          <text>劳务报酬代扣个税:</text>
          <text style="color: #f5222d">- ￥{{ estimateTax.toFixed(2) }}</text>
        </view>
        <view class="estimate-row total">
          <text>预计实际到账:</text>
          <text style="color: #52c41a; font-weight: 700">￥{{ estimateNet.toFixed(2) }}</text>
        </view>
      </view>

      <nut-button
        type="primary"
        style="width: 100%; margin-top: 14px"
        @click="handleApplyWithdraw"
      >
        确认申请提现
      </nut-button>
    </view>

    <!-- 提审规范首屏可见合规规则区 (涵盖额度/次数/时间/到账/手续费/个税/条件 7条) -->
    <view class="rules-card">
      <view class="rules-header">提现与合规纳税规则说明</view>
      <view class="rule-line">1. 【提现额度】单笔最高支持 50,000 元，支持多次提现；</view>
      <view class="rule-line">2. 【提现频次】每日最多可提交 3 次提现申请；</view>
      <view class="rule-line">3. 【开放时间】工作日 09:00 - 18:00 期间正常受理工单；</view>
      <view class="rule-line">4. 【到账时效】提交审核后由对公账户打款，1-3 个工作日内到账；</view>
      <view class="rule-line">5. 【手续费率】平台统一收取 0.35% 对公资金划拨手续费；</view>
      <view class="rule-line">6. 【个税代扣】严格依照劳务报酬所得税率表代扣代缴，800元内免征，4000元以上按80%计税；</view>
      <view class="rule-line">7. 【结算条件】收益须过 7 天无理由售后安全期且满足平台正常经营规范。</view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import Taro from '@tarojs/taro';

const withdrawAmount = ref('');

const withdrawAmountNum = computed(() => Number(withdrawAmount.value) || 0);
const estimateFee = computed(() => Number((withdrawAmountNum.value * 0.0035).toFixed(2)));
const estimateTax = computed(() => {
  const gross = withdrawAmountNum.value;
  if (gross <= 800) return 0;
  if (gross <= 4000) {
    return Number(((gross - 800) * 0.2).toFixed(2));
  }
  const taxable = gross * 0.8;
  if (taxable <= 20000) return Number((taxable * 0.2).toFixed(2));
  if (taxable <= 50000) return Number((taxable * 0.3 - 2000).toFixed(2));
  return Number((taxable * 0.4 - 7000).toFixed(2));
});
const estimateNet = computed(() =>
  Math.max(0, Number((withdrawAmountNum.value - estimateFee.value - estimateTax.value).toFixed(2))),
);

function calculateTaxPreview() {}

function handleApplyWithdraw() {
  if (withdrawAmountNum.value <= 0) {
    Taro.showToast({ title: '请输入有效金额', icon: 'none' });
    return;
  }
  Taro.showToast({ title: '提现申请已提交对公审核', icon: 'success' });
  withdrawAmount.value = '';
}
</script>

<style>
.merchant-wallet-container {
  padding: 12px;
  background-color: #f4f6f8;
  min-height: 100vh;
}
.wallet-header-card {
  background: linear-gradient(135deg, #1890ff 0%, #096dd9 100%);
  color: #fff;
  border-radius: 10px;
  padding: 20px;
  margin-bottom: 12px;
}
.wallet-title {
  font-size: 13px;
  opacity: 0.9;
}
.wallet-amount {
  font-size: 32px;
  font-weight: 800;
  margin: 6px 0;
}
.wallet-stats-row {
  font-size: 11px;
  opacity: 0.85;
}
.withdraw-card {
  background: #fff;
  border-radius: 10px;
  padding: 16px;
  margin-bottom: 12px;
}
.card-title {
  font-size: 14px;
  font-weight: 700;
  color: #333;
  margin-bottom: 12px;
}
.tax-estimate-box {
  background: #fafafa;
  border-radius: 6px;
  padding: 10px 12px;
  margin-top: 10px;
}
.estimate-row {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  color: #666;
  line-height: 1.8;
}
.estimate-row.total {
  border-top: 1px dashed #e8e8e8;
  padding-top: 6px;
  margin-top: 4px;
  font-size: 13px;
}
.rules-card {
  background: #fff;
  border-radius: 10px;
  padding: 16px;
}
.rules-header {
  font-size: 13px;
  font-weight: 700;
  color: #333;
  margin-bottom: 10px;
}
.rule-line {
  font-size: 11px;
  color: #888;
  line-height: 1.7;
}
</style>
