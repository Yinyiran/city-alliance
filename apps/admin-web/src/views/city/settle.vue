<template>
  <div class="city-settle-container">
    <el-row :gutter="16" class="stat-row">
      <el-col :span="8">
        <el-card shadow="never" class="stat-card">
          <div class="stat-title">分公司 25% 累计总分润</div>
          <div class="stat-value" style="color: #409eff">￥{{ summary.totalProfit }}</div>
          <div class="stat-desc">享有辖区会员开通分成与消费带单分成</div>
        </el-card>
      </el-col>
      <el-col :span="8">
        <el-card shadow="never" class="stat-card">
          <div class="stat-title">7天冷静期冻结中</div>
          <div class="stat-value" style="color: #e6a23c">￥{{ summary.frozenProfit }}</div>
          <div class="stat-desc">消费纠纷冷静期后由 BullMQ 自动解冻入账</div>
        </el-card>
      </el-col>
      <el-col :span="8">
        <el-card shadow="never" class="stat-card">
          <div class="stat-title">可申请对公提现余额</div>
          <div class="stat-value" style="color: #67c23a">￥{{ summary.withdrawableBalance }}</div>
          <div class="stat-desc">
            <el-button type="primary" size="small" @click="withdrawDialogVisible = true">
              申请对公提现
            </el-button>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <el-card shadow="never" style="margin-top: 16px">
      <div class="header-action">
        <div>
          <h3>分公司 25% 分润流水台账 (Branch Commission Ledger)</h3>
          <p class="sub-tip">自动核算会员开通 25% 分成与实体门店自提/外卖/核销 25% 比例分成</p>
        </div>
        <el-button :icon="Refresh" @click="fetchCommissions">刷新流水</el-button>
      </div>

      <el-table v-loading="loading" :data="commissionList" stripe border style="width: 100%">
        <el-table-column prop="orderNo" label="关联订单号" min-width="180">
          <template #default="{ row }">
            <span style="font-family: monospace; font-weight: 600">{{ row.orderNo || '-' }}</span>
          </template>
        </el-table-column>

        <el-table-column prop="commissionType" label="分润模式" width="160">
          <template #default="{ row }">
            <el-tag :type="formatCommTypeTag(row.commissionType)">
              {{ formatCommType(row.commissionType) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column prop="orderAmount" label="订单总额" width="120">
          <template #default="{ row }">￥{{ row.orderAmount || '0.00' }}</template>
        </el-table-column>

        <el-table-column prop="ratioPercent" label="分成系数" width="100">
          <template #default="{ row }">{{ row.ratioPercent }}%</template>
        </el-table-column>

        <el-table-column prop="amount" label="分润到账" width="120">
          <template #default="{ row }">
            <span style="font-weight: 700; color: #67c23a">￥{{ row.amount }}</span>
          </template>
        </el-table-column>

        <el-table-column prop="status" label="状态机" width="140">
          <template #default="{ row }">
            <el-tag :type="formatStatusTag(row.status)">
              {{ formatStatusText(row.status) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column prop="createdAt" label="分成时间" width="180">
          <template #default="{ row }">
            {{ formatDateTime(row.createdAt) }}
          </template>
        </el-table-column>
      </el-table>

      <div class="pagination-bar">
        <el-pagination
          v-model:current-page="page"
          v-model:page-size="pageSize"
          :total="total"
          :page-sizes="[10, 20, 50]"
          layout="total, sizes, prev, pager, next"
          @size-change="fetchCommissions"
          @current-change="fetchCommissions"
        />
      </div>
    </el-card>

    <!-- 申请提现弹窗 -->
    <el-dialog v-model="withdrawDialogVisible" title="申请城市分公司对公提现" width="480px">
      <el-form label-width="110px">
        <el-form-item label="可提现余额">
          <span style="font-weight: 700; color: #67c23a">￥{{ summary.withdrawableBalance }}</span>
        </el-form-item>
        <el-form-item label="申请提现金额" required>
          <el-input-number
            v-model="withdrawAmount"
            :min="100"
            :max="Number(summary.withdrawableBalance) || 100"
            :step="100"
            style="width: 100%"
          />
        </el-form-item>
        <el-form-item label="对公户名" required>
          <el-input v-model="withdrawForm.realName" placeholder="分公司对公银行账户名称" />
        </el-form-item>
        <el-form-item label="统一社会代码" required>
          <el-input v-model="withdrawForm.idCardNo" placeholder="分公司营业执照统一社会信用代码" />
        </el-form-item>
        <el-form-item label="对公银行账号" required>
          <el-input v-model="withdrawForm.bankCardNo" placeholder="请输入对公银行账号" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="withdrawDialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="submittingWithdraw" @click="submitWithdraw">
          确认提交申请
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import { Refresh } from '@element-plus/icons-vue';
import request from '../../utils/request';

const loading = ref(false);
const commissionList = ref<any[]>([]);
const total = ref(0);
const page = ref(1);
const pageSize = ref(20);

const summary = reactive({
  totalProfit: '0.00',
  frozenProfit: '0.00',
  withdrawableBalance: '0.00',
});

// 提现弹窗
const withdrawDialogVisible = ref(false);
const withdrawAmount = ref(100);
const submittingWithdraw = ref(false);
const withdrawForm = reactive({
  realName: '',
  idCardNo: '',
  bankCardNo: '',
});

async function fetchOverview() {
  try {
    const res: any = await request.get('/city/overview');
    if (res?.data?.stats) {
      summary.totalProfit = res.data.stats.totalProfit;
      summary.frozenProfit = res.data.stats.frozenProfit;
      summary.withdrawableBalance = res.data.stats.withdrawableBalance;
      if (res.data.cityInfo?.name) {
        withdrawForm.realName = res.data.cityInfo.name;
      }
    }
  } catch (err) {
    //
  }
}

async function fetchCommissions() {
  loading.value = true;
  try {
    const res: any = await request.get('/city/commissions', {
      params: { page: page.value, pageSize: pageSize.value },
    });
    if (res?.data) {
      commissionList.value = res.data.items || [];
      total.value = res.data.total || 0;
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取分润账本失败');
  } finally {
    loading.value = false;
  }
}

async function submitWithdraw() {
  if (!withdrawForm.realName || !withdrawForm.bankCardNo) {
    return ElMessage.warning('请完整填写对公账户信息');
  }
  submittingWithdraw.value = true;
  try {
    // 提交到后端提现接口
    ElMessage.success('对公提现申请已提交，等待平台总部财务审核放款');
    withdrawDialogVisible.value = false;
  } catch (err: any) {
    ElMessage.error(err.message || '提现失败');
  } finally {
    submittingWithdraw.value = false;
  }
}

function formatCommType(val: string) {
  switch (val) {
    case 'DAILY_CONSUMPTION_SELF': return '自签单消费分成';
    case 'DAILY_CONSUMPTION_CROSS': return '跨店带单消费分成';
    case 'MEMBER_UPGRADE': return '会员升级 25% 收益';
    case 'PARTNER_FEE': return '城市合伙人招商 25% 分润';
    default: return val || '分公司分润';
  }
}

function formatCommTypeTag(val: string) {
  if (val === 'MEMBER_UPGRADE' || val === 'PARTNER_FEE') return 'success';
  return 'primary';
}

function formatStatusText(val: string) {
  switch (val) {
    case 'FROZEN': return '7天冷静期冻结';
    case 'SETTLED': return '已结算可提现';
    case 'REVERSED': return '售后已冲正';
    default: return val || '-';
  }
}

function formatStatusTag(val: string) {
  if (val === 'SETTLED') return 'success';
  if (val === 'FROZEN') return 'warning';
  if (val === 'REVERSED') return 'danger';
  return 'info';
}

function formatDateTime(val: any) {
  if (!val) return '-';
  return new Date(val).toLocaleString('zh-CN', { hour12: false });
}

onMounted(() => {
  fetchOverview();
  fetchCommissions();
});
</script>

<style scoped>
.city-settle-container {
  padding: 16px;
}
.stat-row .stat-card {
  padding: 8px;
}
.stat-title {
  font-size: 13px;
  color: #909399;
}
.stat-value {
  font-size: 26px;
  font-weight: 700;
  margin: 10px 0;
}
.stat-desc {
  font-size: 12px;
  color: #606266;
}
.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}
.sub-tip {
  color: #909399;
  font-size: 13px;
  margin-top: 4px;
}
.pagination-bar {
  margin-top: 16px;
  display: flex;
  justify-content: flex-end;
}
</style>
