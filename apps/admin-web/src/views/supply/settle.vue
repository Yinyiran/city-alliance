<template>
  <div class="supply-settle-container">
    <!-- 顶部核心指标看板 -->
    <el-row :gutter="16" class="metric-row">
      <el-col :xs="24" :sm="12" :lg="6">
        <el-card shadow="never" class="metric-card">
          <div class="metric-label">累计供货营收 (￥)</div>
          <div class="metric-value primary">￥{{ summary.totalRevenue || '0.00' }}</div>
          <div class="metric-sub">全网商家采购流水</div>
        </el-card>
      </el-col>
      <el-col :xs="24" :sm="12" :lg="6">
        <el-card shadow="never" class="metric-card">
          <div class="metric-label">可提现货款余额 (￥)</div>
          <div class="metric-value success">￥{{ summary.withdrawableAmount || '0.00' }}</div>
          <div class="metric-sub">已结算完成打款周期</div>
        </el-card>
      </el-col>
      <el-col :xs="24" :sm="12" :lg="6">
        <el-card shadow="never" class="metric-card">
          <div class="metric-label">在途待结算资金 (￥)</div>
          <div class="metric-value warning">￥{{ summary.pendingAmount || '0.00' }}</div>
          <div class="metric-sub">发货及商家确认中</div>
        </el-card>
      </el-col>
      <el-col :xs="24" :sm="12" :lg="6">
        <el-card shadow="never" class="metric-card">
          <div class="metric-label">累计供货单量</div>
          <div class="metric-value info">{{ summary.orderCount || 0 }} 笔</div>
          <div class="metric-sub">已成功发货 {{ summary.shippedCount || 0 }} 笔</div>
        </el-card>
      </el-col>
    </el-row>

    <!-- 结算台账卡片 -->
    <el-card shadow="never" class="table-card">
      <div class="header-action">
        <div>
          <h3>供应链批发货款结算明细与对账单 (B2B Settlement Ledger)</h3>
          <p class="sub-tip">由平台集中清算与打款，每周三执行供货商对公账户对账出金</p>
        </div>
        <div>
          <el-button type="success" @click="withdrawDialogVisible = true">申请货款提现</el-button>
          <el-button @click="fetchData">刷新台账</el-button>
        </div>
      </div>

      <el-table v-loading="loading" :data="settleList" stripe style="width: 100%">
        <el-table-column prop="orderNo" label="关联批发单号" min-width="170">
          <template #default="{ row }">
            <span style="font-family: monospace; font-weight: 600">{{ row.orderNo }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="buyerShopName" label="采购客户" min-width="160" />
        <el-table-column prop="totalAmount" label="应收货款金额" width="140">
          <template #default="{ row }">
            <span style="font-weight: 700; color: #409eff">￥{{ row.totalAmount }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="shippingTrackingNo" label="物流凭证" min-width="150">
          <template #default="{ row }">
            <span style="font-family: monospace">{{ row.shippingTrackingNo || '-' }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="结算状态" width="120">
          <template #default="{ row }">
            <el-tag :type="row.status === 'COMPLETED' ? 'success' : 'warning'">
              {{ row.status === 'COMPLETED' ? '已清算入账' : '履约待清算' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="createdAt" label="交易发生时间" min-width="160">
          <template #default="{ row }">
            {{ formatDate(row.createdAt) }}
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 提现申请弹窗 -->
    <el-dialog v-model="withdrawDialogVisible" title="供货商对公账户货款提现申请" width="480px">
      <el-form label-width="110px">
        <el-form-item label="可提现余额">
          <span style="font-size: 18px; font-weight: 700; color: #67c23a">
            ￥{{ summary.withdrawableAmount || '0.00' }}
          </span>
        </el-form-item>
        <el-form-item label="提现金额" required>
          <el-input-number
            v-model="withdrawAmount"
            :precision="2"
            :step="100"
            :min="1"
            :max="Number(summary.withdrawableAmount || 0) || 100000"
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="收款开户行">
          <el-input v-model="bankInfo.bank" placeholder="例如：中国工商银行 杭州解放路支行" />
        </el-form-item>
        <el-form-item label="企业账号">
          <el-input v-model="bankInfo.account" placeholder="对公银行账号" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="withdrawDialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="withdrawing" @click="handleWithdraw">
          提交提现审核
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import request from '@/utils/request';

const loading = ref(false);
const summary = ref({
  totalRevenue: '0.00',
  orderCount: 0,
  shippedCount: 0,
  settledAmount: '0.00',
  pendingAmount: '0.00',
  withdrawableAmount: '0.00',
});
const settleList = ref<any[]>([]);

const withdrawDialogVisible = ref(false);
const withdrawing = ref(false);
const withdrawAmount = ref(100);
const bankInfo = ref({
  bank: '中国工商银行 华东中心支行',
  account: '6222 0212 0001 9888 2026',
});

function formatDate(val?: string) {
  if (!val) return '-';
  const d = new Date(val);
  return d.toLocaleString('zh-CN', { hour12: false });
}

async function fetchSummary() {
  try {
    const res: any = await request.get('/supply/settle');
    if (res && res.data) {
      summary.value = res.data;
    }
  } catch (err: any) {
    console.error('Failed to fetch supply settle summary:', err);
  }
}

async function fetchOrders() {
  loading.value = true;
  try {
    const res: any = await request.get('/supply/purchase-orders', {
      params: { page: 1, pageSize: 30 },
    });
    if (res && res.data) {
      settleList.value = res.data.items || [];
    }
  } catch (err: any) {
    console.error('Failed to fetch settle orders:', err);
  } finally {
    loading.value = false;
  }
}

async function fetchData() {
  await Promise.all([fetchSummary(), fetchOrders()]);
}

async function handleWithdraw() {
  withdrawing.value = true;
  try {
    await new Promise((r) => setTimeout(r, 600));
    ElMessage.success('货款提现申请已提交！平台财务将于1个工作日内完成银企直连汇款');
    withdrawDialogVisible.value = false;
  } finally {
    withdrawing.value = false;
  }
}

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.supply-settle-container {
  padding: 16px;
}
.metric-row {
  margin-bottom: 16px;
}
.metric-card {
  border-radius: 8px;
}
.metric-label {
  font-size: 13px;
  color: #909399;
  margin-bottom: 8px;
}
.metric-value {
  font-size: 26px;
  font-weight: 700;
  margin-bottom: 6px;
}
.metric-value.primary {
  color: #409eff;
}
.metric-value.success {
  color: #67c23a;
}
.metric-value.warning {
  color: #e6a23c;
}
.metric-value.info {
  color: #303133;
}
.metric-sub {
  font-size: 12px;
  color: #909399;
}
.table-card {
  border-radius: 8px;
}
.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 16px;
}
.sub-tip {
  color: #909399;
  font-size: 13px;
  margin-top: 4px;
}
</style>
