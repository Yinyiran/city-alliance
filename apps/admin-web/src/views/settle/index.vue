<template>
  <div class="settle-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>全平台资金总清算与提现个税中枢 (Financial Settlement & Tax Engine)</h3>
          <p class="sub-tip">
            监控全网 25% 城市分润、25% 推荐分润、自签单与跨店分润账本，审核主理人与合伙人实名提现与劳务阶梯个税代扣代缴
          </p>
        </div>
        <div>
          <el-button type="primary" :icon="Refresh" @click="handleRefresh">刷新流水</el-button>
        </div>
      </div>

      <el-tabs v-model="activeTab" @tab-change="handleTabChange">
        <!-- 佣金清算账本 -->
        <el-tab-pane label="全网消费分润账本 (自签单2% vs 带单6% vs 合伙人)" name="COMMISSIONS">
          <div class="filter-bar">
            <el-select
              v-model="commFilterStatus"
              placeholder="清算状态"
              clearable
              style="width: 160px"
              @change="fetchCommissions"
            >
              <el-option label="全部状态" value="" />
              <el-option label="7天冻结保护中 (FROZEN)" value="FROZEN" />
              <el-option label="已解冻已出账 (SETTLED)" value="SETTLED" />
              <el-option label="争议退款已冲正 (REVERSED)" value="REVERSED" />
            </el-select>
          </div>

          <el-table v-loading="loadingComm" :data="commissionList" stripe border style="width: 100%">
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
              <template #default="{ row }">
                ￥{{ row.orderAmount || '0.00' }}
              </template>
            </el-table-column>
            <el-table-column prop="ratioPercent" label="分成系数" width="100">
              <template #default="{ row }">
                {{ row.ratioPercent }}%
              </template>
            </el-table-column>
            <el-table-column prop="amount" label="分润金额" width="120">
              <template #default="{ row }">
                <span style="font-weight: 700; color: #67c23a">￥{{ row.amount }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="beneficiaryType" label="结算受益角色" width="140">
              <template #default="{ row }">
                <el-tag size="small" effect="plain">{{ formatBeneficiary(row.beneficiaryType) }}</el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="status" label="状态机流转" width="150">
              <template #default="{ row }">
                <el-tag :type="formatStatusTag(row.status)">
                  {{ formatStatusText(row.status) }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="createdAt" label="入账时间" width="180">
              <template #default="{ row }">
                {{ formatDateTime(row.createdAt) }}
              </template>
            </el-table-column>
          </el-table>

          <div class="pagination-bar">
            <el-pagination
              v-model:current-page="commPage"
              v-model:page-size="commPageSize"
              :total="commTotal"
              :page-sizes="[10, 20, 50]"
              layout="total, sizes, prev, pager, next"
              @size-change="fetchCommissions"
              @current-change="fetchCommissions"
            />
          </div>
        </el-tab-pane>

        <!-- 提现审核与个税 -->
        <el-tab-pane label="实名提现与劳务个税代扣代缴审核" name="WITHDRAWALS">
          <el-alert
            title="税合规红线：劳务报酬提现 800 元以内免征个税；800~4000 元定额扣除 800 元；4000 元以上定率扣除 20% 费用并执行 20%/30%/40% 超额累进税率；平台按 0.35% 收取通道技术服务费。"
            type="info"
            :closable="false"
            show-icon
            style="margin-bottom: 16px"
          />

          <el-table v-loading="loadingWith" :data="withdrawList" stripe border style="width: 100%">
            <el-table-column prop="realName" label="收款人姓名" width="120" />
            <el-table-column prop="idCardNo" label="证件/税号" width="180">
              <template #default="{ row }">
                <span style="font-family: monospace">{{ row.idCardNo }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="grossAmount" label="申请提现毛额" width="130">
              <template #default="{ row }">
                <span style="font-weight: 700">￥{{ row.grossAmount }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="taxAmount" label="代扣个税" width="110">
              <template #default="{ row }">
                <span style="color: #f56c6c; font-weight: 600">￥{{ row.taxAmount }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="platformFee" label="通道费(0.35%)" width="120">
              <template #default="{ row }">￥{{ row.platformFee }}</template>
            </el-table-column>
            <el-table-column prop="netAmount" label="实到账净额" width="130">
              <template #default="{ row }">
                <span style="color: #67c23a; font-weight: 700">￥{{ row.netAmount }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="channel" label="出款通道" width="110">
              <template #default="{ row }">
                <el-tag size="small">{{ row.channel === 'BANK_CARD' ? '银行卡对公' : '微信企业零钱' }}</el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="status" label="放款状态" width="130">
              <template #default="{ row }">
                <el-tag :type="formatWithdrawStatusTag(row.status)">
                  {{ formatWithdrawStatusText(row.status) }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column label="财务操作" width="220" fixed="right">
              <template #default="{ row }">
                <template v-if="row.status === 'AUDITING'">
                  <el-button type="success" size="small" @click="openAuditDialog(row, true)">
                    通过
                  </el-button>
                  <el-button type="danger" size="small" @click="openAuditDialog(row, false)">
                    驳回
                  </el-button>
                </template>
                <template v-else-if="row.status === 'PAYING'">
                  <el-button type="primary" size="small" @click="handlePay(row)">
                    确认对公出款
                  </el-button>
                </template>
                <template v-else-if="row.status === 'SUCCESS'">
                  <span style="font-size: 12px; color: #67c23a">已放款并计税</span>
                </template>
                <template v-else>
                  <span style="font-size: 12px; color: #909399">已拒绝</span>
                </template>
              </template>
            </el-table-column>
          </el-table>

          <div class="pagination-bar">
            <el-pagination
              v-model:current-page="withPage"
              v-model:page-size="withPageSize"
              :total="withTotal"
              :page-sizes="[10, 20, 50]"
              layout="total, sizes, prev, pager, next"
              @size-change="fetchWithdrawals"
              @current-change="fetchWithdrawals"
            />
          </div>
        </el-tab-pane>
      </el-tabs>
    </el-card>

    <!-- 审核弹窗 -->
    <el-dialog v-model="auditDialogVisible" :title="auditActionText + '提现申请'" width="450px">
      <el-form label-width="90px">
        <el-form-item label="申请人">
          <span style="font-weight: 600">{{ currentWithdraw?.realName }}</span>
        </el-form-item>
        <el-form-item label="实付净额">
          <span style="color: #67c23a; font-weight: 700">￥{{ currentWithdraw?.netAmount }}</span>
        </el-form-item>
        <el-form-item label="审核批注" required>
          <el-input
            v-model="auditNote"
            type="textarea"
            :rows="3"
            placeholder="请输入财务审核批复意见（如合规已核准/银行账户不一致驳回等）"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="auditDialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="submittingAudit" @click="submitAudit">
          确认{{ auditActionText }}
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Refresh } from '@element-plus/icons-vue';
import request from '../../utils/request';

const activeTab = ref('COMMISSIONS');

// 分润账本
const loadingComm = ref(false);
const commissionList = ref<any[]>([]);
const commTotal = ref(0);
const commPage = ref(1);
const commPageSize = ref(20);
const commFilterStatus = ref('');

// 提现审核
const loadingWith = ref(false);
const withdrawList = ref<any[]>([]);
const withTotal = ref(0);
const withPage = ref(1);
const withPageSize = ref(20);

// 审核弹窗
const auditDialogVisible = ref(false);
const currentWithdraw = ref<any>(null);
const isApprove = ref(true);
const auditActionText = ref('通过');
const auditNote = ref('');
const submittingAudit = ref(false);

async function fetchCommissions() {
  loadingComm.value = true;
  try {
    const res: any = await request.get('/settle/commissions', {
      params: {
        page: commPage.value,
        pageSize: commPageSize.value,
        status: commFilterStatus.value || undefined,
      },
    });
    if (res?.data) {
      commissionList.value = res.data.items || [];
      commTotal.value = res.data.total || 0;
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取分润账本失败');
  } finally {
    loadingComm.value = false;
  }
}

async function fetchWithdrawals() {
  loadingWith.value = true;
  try {
    const res: any = await request.get('/settle/withdrawals', {
      params: {
        page: withPage.value,
        pageSize: withPageSize.value,
      },
    });
    if (res?.data) {
      withdrawList.value = res.data.items || [];
      withTotal.value = res.data.total || 0;
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取提现列表失败');
  } finally {
    loadingWith.value = false;
  }
}

function handleRefresh() {
  if (activeTab.value === 'COMMISSIONS') {
    fetchCommissions();
  } else {
    fetchWithdrawals();
  }
}

function handleTabChange(tab: any) {
  if (tab === 'COMMISSIONS') {
    fetchCommissions();
  } else {
    fetchWithdrawals();
  }
}

function openAuditDialog(row: any, approved: boolean) {
  currentWithdraw.value = row;
  isApprove.value = approved;
  auditActionText.value = approved ? '通过' : '驳回';
  auditNote.value = approved ? '财务合规核对无误，准予放款' : '收款账户与实名信息不符，已驳回';
  auditDialogVisible.value = true;
}

async function submitAudit() {
  if (!auditNote.value.trim()) {
    return ElMessage.warning('请输入审核批注');
  }
  submittingAudit.value = true;
  try {
    await request.post(`/settle/withdrawals/${currentWithdraw.value.id}/audit`, {
      approved: isApprove.value,
      auditNote: auditNote.value,
    });
    ElMessage.success(`提现申请已${auditActionText.value}`);
    auditDialogVisible.value = false;
    fetchWithdrawals();
  } catch (err: any) {
    ElMessage.error(err.message || '审核失败');
  } finally {
    submittingAudit.value = false;
  }
}

async function handlePay(row: any) {
  try {
    await ElMessageBox.confirm(
      `确定对 ${row.realName} 执行放款 ￥${row.netAmount} (已扣个税 ￥${row.taxAmount}) 吗？`,
      '对公放款确认',
      { type: 'warning' },
    );
    await request.post(`/settle/withdrawals/${row.id}/pay`);
    ElMessage.success('对公放款成功，合规税务档案已归档');
    fetchWithdrawals();
  } catch (err: any) {
    if (err !== 'cancel') {
      ElMessage.error(err.message || '放款失败');
    }
  }
}

function formatCommType(val: string) {
  switch (val) {
    case 'DAILY_CONSUMPTION_SELF': return '自签单消费 (2%)';
    case 'DAILY_CONSUMPTION_CROSS': return '跨店带单消费 (6%)';
    case 'MEMBER_UPGRADE': return '会员开通升级分成';
    case 'PARTNER_FEE': return '城市合伙人招商分成';
    default: return val || '通用分润';
  }
}

function formatCommTypeTag(val: string) {
  if (val === 'DAILY_CONSUMPTION_SELF') return 'success';
  if (val === 'DAILY_CONSUMPTION_CROSS') return 'warning';
  return 'primary';
}

function formatBeneficiary(val: string) {
  switch (val) {
    case 'MEMBER': return '合伙人/推荐人';
    case 'SHOP_OWNER': return '商家主理人';
    case 'CITY_BRANCH': return '城市分公司(25%)';
    case 'HEADQUARTERS': return '平台总部运营';
    default: return val || '平台参与方';
  }
}

function formatStatusText(val: string) {
  switch (val) {
    case 'FROZEN': return '7天冷静期冻结';
    case 'UNFROZEN': return '已解冻待结算';
    case 'SETTLED': return '已结算入账';
    case 'REVERSED': return '售后退款已冲正';
    default: return val || '-';
  }
}

function formatStatusTag(val: string) {
  if (val === 'SETTLED') return 'success';
  if (val === 'FROZEN') return 'warning';
  if (val === 'REVERSED') return 'danger';
  return 'info';
}

function formatWithdrawStatusText(val: string) {
  switch (val) {
    case 'AUDITING': return '待财务审核';
    case 'PAYING': return '待对公放款';
    case 'SUCCESS': return '对公放款成功';
    case 'REJECTED': return '审核已驳回';
    default: return val || '-';
  }
}

function formatWithdrawStatusTag(val: string) {
  if (val === 'SUCCESS') return 'success';
  if (val === 'PAYING') return 'primary';
  if (val === 'AUDITING') return 'warning';
  return 'danger';
}

function formatDateTime(val: any) {
  if (!val) return '-';
  return new Date(val).toLocaleString('zh-CN', { hour12: false });
}

onMounted(() => {
  fetchCommissions();
});
</script>

<style scoped>
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
.filter-bar {
  margin-bottom: 14px;
  display: flex;
  gap: 12px;
}
.pagination-bar {
  margin-top: 16px;
  display: flex;
  justify-content: flex-end;
}
</style>
