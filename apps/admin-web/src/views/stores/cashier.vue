<template>
  <div class="cashier-container">
    <!-- 顶部状态栏 -->
    <el-card shadow="never" class="shift-bar">
      <div class="shift-header">
        <div class="store-meta">
          <el-tag type="success" effect="dark" size="large">
            <el-icon><Shop /></el-icon>
            {{ currentShift?.storeName || storeName || '门店收银工作台' }}
          </el-tag>
          <span class="clerk-info">
            当班收银员：<strong>{{ currentShift?.clerkName || currentClerkName || '值班员' }}</strong>
            <span v-if="currentShift?.clerkNo" class="clerk-no">({{ currentShift?.clerkNo }})</span>
          </span>
          <span v-if="currentShift?.shiftStart" class="shift-time">
            开班时间：{{ formatDate(currentShift.shiftStart) }}
          </span>
        </div>
        <div class="shift-actions">
          <el-button v-if="!currentShift" type="primary" @click="handleOpenShift">
            <el-icon><Timer /></el-icon> 开班打卡签到
          </el-button>
          <el-button v-else type="danger" plain @click="openCloseShiftDialog">
            <el-icon><DocumentChecked /></el-icon> 交接班轧账
          </el-button>
          <el-button @click="refreshAll">
            <el-icon><Refresh /></el-icon> 刷新
          </el-button>
        </div>
      </div>
    </el-card>

    <el-row :gutter="20" class="main-workspace">
      <!-- 左侧：核销工作区 -->
      <el-col :xs="24" :lg="14">
        <el-card shadow="never" class="verify-card">
          <div class="card-title">
            <el-icon><CreditCard /></el-icon>
            <span>提货凭证 / 12位核销码即时核销</span>
          </div>
          <p class="card-tip">支持外接USB/无线扫码枪即扫即核，或手动输入顾客出示的12位数字自提码</p>

          <div class="input-panel">
            <el-input
              v-model="verifyCode"
              placeholder="请输入或扫码录入 12 位核销码..."
              size="large"
              clearable
              maxlength="20"
              class="verify-input"
              @keyup.enter="handleVerify"
            >
              <template #prepend>核销码</template>
              <template #append>
                <el-button type="primary" :loading="verifying" @click="handleVerify">
                  立即核销
                </el-button>
              </template>
            </el-input>

            <!-- 软键盘快捷按键 -->
            <div class="keypad">
              <el-button v-for="num in [1,2,3,4,5,6,7,8,9,0]" :key="num" class="key-btn" @click="appendNumber(num)">
                {{ num }}
              </el-button>
              <el-button class="key-btn danger" @click="verifyCode = ''">清空</el-button>
              <el-button class="key-btn action" @click="verifyCode = verifyCode.slice(0, -1)">退格</el-button>
            </div>
          </div>

          <!-- 最近一次核销成功卡片 -->
          <transition name="el-fade-in-linear">
            <div v-if="lastVerifiedResult" class="verified-alert">
              <el-result
                icon="success"
                title="核销成功"
                :sub-title="`订单编号: ${lastVerifiedResult.orderNo || lastVerifiedResult.orderId}`"
              >
                <template #extra>
                  <div class="alert-details">
                    <div><strong>核销时间：</strong>{{ formatDate(lastVerifiedResult.verifiedAt) }}</div>
                    <div><strong>履约形态：</strong>{{ lastVerifiedResult.tradeType || 'STORE_PICKUP (自提)' }}</div>
                  </div>
                  <el-button type="success" size="small" @click="lastVerifiedResult = null">
                    完成并继续下一单
                  </el-button>
                </template>
              </el-result>
            </div>
          </transition>
        </el-card>
      </el-col>

      <!-- 右侧：当班数据罗盘 & 实时流水 -->
      <el-col :xs="24" :lg="10">
        <!-- 概况指标 -->
        <el-row :gutter="12" class="stats-row">
          <el-col :span="12">
            <el-card shadow="never" class="stat-box">
              <div class="stat-label">今日已核销订单</div>
              <div class="stat-value primary">{{ todaySummary.verifyCount }} 笔</div>
            </el-card>
          </el-col>
          <el-col :span="12">
            <el-card shadow="never" class="stat-box">
              <div class="stat-label">今日核销总额</div>
              <div class="stat-value success">￥{{ todaySummary.verifyAmount }}</div>
            </el-card>
          </el-col>
        </el-row>

        <!-- 当班流水卡片 -->
        <el-card shadow="never" class="recent-list-card">
          <template #header>
            <div class="panel-header">
              <span>当班最新核销流水</span>
              <el-tag size="small" type="info">实时对账</el-tag>
            </div>
          </template>

          <el-table :data="recentOrders" height="420" stripe style="width: 100%">
            <el-table-column prop="orderNo" label="订单号" min-width="140">
              <template #default="{ row }">
                <span class="order-no">{{ row.orderNo }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="payAmount" label="金额" width="90">
              <template #default="{ row }">
                <span class="order-price">￥{{ row.payAmount }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="status" label="状态" width="80">
              <template #default="{ row }">
                <el-tag size="small" type="success">已核销</el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="updatedAt" label="核销时间" width="130">
              <template #default="{ row }">
                {{ formatTimeOnly(row.updatedAt) }}
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </el-col>
    </el-row>

    <!-- 交班轧账弹窗 -->
    <el-dialog
      v-model="closeShiftDialogVisible"
      title="收银交接班结班轧账"
      width="500px"
    >
      <el-form :model="closeShiftForm" label-width="120px">
        <el-form-item label="当班开班时间">
          <span>{{ formatDate(currentShift?.shiftStart) }}</span>
        </el-form-item>
        <el-form-item label="当班已核销单数">
          <strong>{{ todaySummary.verifyCount }} 笔</strong>
        </el-form-item>
        <el-form-item label="扫码实收 (￥)" required>
          <el-input-number v-model="closeShiftForm.scanTotal" :precision="2" :step="10" :min="0" style="width: 100%" />
        </el-form-item>
        <el-form-item label="现金实收 (￥)" required>
          <el-input-number v-model="closeShiftForm.cashTotal" :precision="2" :step="10" :min="0" style="width: 100%" />
        </el-form-item>
        <el-form-item label="长短款轧差差异 (￥)">
          <el-input-number v-model="closeShiftForm.diffAmount" :precision="2" style="width: 100%" />
          <div class="form-tip">0.00 代表平账；负数为短款，正数为长款</div>
        </el-form-item>
        <el-form-item label="交班备注">
          <el-input v-model="closeShiftForm.remark" type="textarea" :rows="2" placeholder="填写备用金结余或交接特殊事项" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="closeShiftDialogVisible = false">取消</el-button>
        <el-button type="danger" :loading="closingShift" @click="confirmCloseShift">
          确认结班打单
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import {
  Shop,
  Timer,
  DocumentChecked,
  Refresh,
  CreditCard,
} from '@element-plus/icons-vue';
import request from '@/utils/request';

const storeName = ref('');
const currentClerkName = ref('');
const verifyCode = ref('');
const verifying = ref(false);
const lastVerifiedResult = ref<any>(null);

const currentShift = ref<any>(null);
const todaySummary = ref({
  verifyCount: 0,
  verifyAmount: '0.00',
});

const recentOrders = ref<any[]>([]);

// 交班轧账
const closeShiftDialogVisible = ref(false);
const closingShift = ref(false);
const closeShiftForm = ref({
  cashTotal: 0,
  scanTotal: 0,
  diffAmount: 0,
  remark: '',
});

function appendNumber(num: number) {
  if (verifyCode.value.length < 20) {
    verifyCode.value += String(num);
  }
}

function formatDate(val?: string) {
  if (!val) return '-';
  const d = new Date(val);
  return d.toLocaleString('zh-CN', { hour12: false });
}

function formatTimeOnly(val?: string) {
  if (!val) return '-';
  const d = new Date(val);
  return d.toLocaleTimeString('zh-CN', { hour12: false });
}

async function fetchShiftData() {
  try {
    const res: any = await request.get('/stores/shifts/current');
    if (res && res.data) {
      currentShift.value = res.data.currentShift;
      if (res.data.todaySummary) {
        todaySummary.value = res.data.todaySummary;
      }
    }
  } catch (err: any) {
    console.error('Failed to fetch shift:', err);
  }
}

async function fetchRecentVerifications() {
  try {
    const res: any = await request.get('/trade/orders', {
      params: {
        orderStatus: 'COMPLETED',
        tradeType: 'STORE_PICKUP',
        page: 1,
        pageSize: 15,
      },
    });
    if (res && res.data && res.data.items) {
      recentOrders.value = res.data.items;
    }
  } catch (err: any) {
    console.error('Failed to fetch orders:', err);
  }
}

async function refreshAll() {
  await Promise.all([fetchShiftData(), fetchRecentVerifications()]);
}

async function handleVerify() {
  const code = verifyCode.value.trim().replace(/\s+/g, '');
  if (!code) {
    ElMessage.warning('请输入提货码或扫码');
    return;
  }

  verifying.value = true;
  try {
    const res: any = await request.post('/trade/verify', {
      verifyCode: code,
    });
    if (res && (res.code === 200 || res.data)) {
      ElMessage.success('订单核销成功！');
      lastVerifiedResult.value = res.data || { orderNo: code, verifiedAt: new Date().toISOString() };
      verifyCode.value = '';
      await refreshAll();
    } else {
      ElMessage.error(res?.message || '核销失败');
    }
  } catch (err: any) {
    ElMessage.error(err?.response?.data?.message || err.message || '核销异常，请核对核销码');
  } finally {
    verifying.value = false;
  }
}

async function handleOpenShift() {
  try {
    await request.post('/stores/shifts/open', {
      remark: '早班签到开班',
    });
    ElMessage.success('当班签到成功！');
    await fetchShiftData();
  } catch (err: any) {
    ElMessage.error(err?.response?.data?.message || '开班失败');
  }
}

function openCloseShiftDialog() {
  closeShiftForm.value = {
    scanTotal: Number(todaySummary.value.verifyAmount) || 0,
    cashTotal: 0,
    diffAmount: 0,
    remark: '',
  };
  closeShiftDialogVisible.value = true;
}

async function confirmCloseShift() {
  if (!currentShift.value?.id) {
    ElMessage.warning('当前无活跃班次');
    return;
  }
  closingShift.value = true;
  try {
    await request.post('/stores/shifts/close', {
      shiftId: currentShift.value.id,
      cashTotal: closeShiftForm.value.cashTotal,
      scanTotal: closeShiftForm.value.scanTotal,
      diffAmount: closeShiftForm.value.diffAmount,
      remark: closeShiftForm.value.remark,
    });
    ElMessage.success('交班轧账完成！已结班');
    closeShiftDialogVisible.value = false;
    await fetchShiftData();
  } catch (err: any) {
    ElMessage.error(err?.response?.data?.message || '轧账结班失败');
  } finally {
    closingShift.value = false;
  }
}

onMounted(() => {
  refreshAll();
});
</script>

<style scoped>
.cashier-container {
  padding: 16px;
  background-color: #f0f2f5;
  min-height: calc(100vh - 84px);
}

.shift-bar {
  margin-bottom: 16px;
  border-radius: 8px;
}

.shift-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.store-meta {
  display: flex;
  align-items: center;
  gap: 16px;
}

.clerk-info {
  font-size: 15px;
  color: #303133;
}

.clerk-no {
  color: #909399;
  font-size: 13px;
  margin-left: 4px;
}

.shift-time {
  font-size: 13px;
  color: #909399;
}

.main-workspace {
  margin-top: 8px;
}

.verify-card {
  border-radius: 8px;
  min-height: 520px;
}

.card-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 18px;
  font-weight: 700;
  color: #303133;
}

.card-tip {
  color: #909399;
  font-size: 13px;
  margin: 6px 0 20px 0;
}

.input-panel {
  max-width: 620px;
  margin: 0 auto;
}

.verify-input {
  font-size: 18px;
  font-family: monospace;
}

.keypad {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
  margin-top: 20px;
}

.key-btn {
  height: 52px;
  font-size: 20px;
  font-weight: 600;
  border-radius: 8px;
}

.key-btn.danger {
  color: #f56c6c;
}

.key-btn.action {
  color: #e6a23c;
}

.verified-alert {
  margin-top: 24px;
  border-radius: 8px;
  background: #f0f9eb;
  padding: 16px;
}

.alert-details {
  font-size: 14px;
  color: #606266;
  text-align: left;
  margin-bottom: 12px;
}

.stats-row {
  margin-bottom: 16px;
}

.stat-box {
  border-radius: 8px;
  text-align: center;
}

.stat-label {
  font-size: 13px;
  color: #909399;
  margin-bottom: 6px;
}

.stat-value {
  font-size: 26px;
  font-weight: 700;
}

.stat-value.primary {
  color: #409eff;
}

.stat-value.success {
  color: #67c23a;
}

.recent-list-card {
  border-radius: 8px;
}

.panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 600;
}

.order-no {
  font-family: monospace;
  font-weight: 600;
  color: #303133;
}

.order-price {
  font-weight: 700;
  color: #f56c6c;
}

.form-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}
</style>
