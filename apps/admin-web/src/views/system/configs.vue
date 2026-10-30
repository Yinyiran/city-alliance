<template>
  <div class="system-configs-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>平台系统参数与渠道生态配置 (System Configs & Ecosystem)</h3>
          <p class="sub-tip">维护微信支付商户号密钥、汇付天下接口、7天冷静期时钟、微信小程序/服务号生态参数与操作审计</p>
        </div>
        <el-button type="primary" :loading="saving" @click="saveAllConfigs">
          保存全局配置
        </el-button>
      </div>

      <el-tabs v-model="activeTab" @tab-change="handleTabChange">
        <!-- 微信支付与收单通道 -->
        <el-tab-pane label="微信支付与收单通道" name="WECHAT_PAY">
          <el-form label-width="180px" style="max-width: 750px; margin-top: 20px">
            <el-form-item label="微信支付直连商户号">
              <el-input v-model="configData.mchId" placeholder="例如：1688992211" />
            </el-form-item>
            <el-form-item label="API v3 密钥 (32字节)">
              <el-input v-model="configData.apiV3Key" type="password" show-password placeholder="请输入商户 API v3 密钥" />
            </el-form-item>
            <el-form-item label="商户 API 证书序列号">
              <el-input v-model="configData.certSerialNo" placeholder="请输入商户证书序列号" />
            </el-form-item>
            <el-form-item label="汇付天下收单机构代码">
              <el-input v-model="configData.huifuMerId" placeholder="用于二级商户自动清算分账与劳务税代扣代缴" />
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <!-- 清算时钟与核心规则 -->
        <el-tab-pane label="清算时钟与核心规则" name="SETTLE_RULES">
          <el-form label-width="200px" style="max-width: 750px; margin-top: 20px">
            <el-form-item label="售后退款冷静期 (天)">
              <el-input-number v-model="configData.settleUnfreezeDays" :min="1" :max="30" />
              <span class="field-hint">
                订单完成达到此天数且未发生退款时，BullMQ 自动解冻分润
              </span>
            </el-form-item>
            <el-form-item label="待付款自动取消时限 (分)">
              <el-input-number v-model="configData.orderAutoCancelMinutes" :min="5" :max="120" />
              <span class="field-hint">未支付订单超过该时长自动关闭并归还库存</span>
            </el-form-item>
            <el-form-item label="提现个税起征点 (元)">
              <el-input-number v-model="configData.taxThreshold" :min="0" :step="100" />
              <span class="field-hint">
                劳务报酬预扣预缴阶梯税起征点（国家基准默认800元）
              </span>
            </el-form-item>
            <el-form-item label="合伙人招商分成比例 (%)">
              <el-input-number v-model="configData.partnerInviteRewardRatio" :min="0" :max="100" />
              <span class="field-hint">推荐直荐人一次性分成奖励系数（默认 25%）</span>
            </el-form-item>
            <el-form-item label="城市分公司分润比例 (%)">
              <el-input-number v-model="configData.cityBranchProfitRatio" :min="0" :max="100" />
              <span class="field-hint">城市合伙人辖区开通分成比例（默认 25%）</span>
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <!-- 渠道生态 (小程序与公众号) -->
        <el-tab-pane label="渠道生态 (小程序与服务号)" name="CHANNELS">
          <el-form label-width="200px" style="max-width: 780px; margin-top: 20px">
            <el-divider content-position="left">顾客端微信小程序</el-divider>
            <el-form-item label="顾客小程序 AppID">
              <el-input v-model="configData.customerWeappAppId" placeholder="例如：wx1a2b3c4d5e6f7g8h" />
            </el-form-item>
            <el-form-item label="顾客小程序 AppSecret">
              <el-input v-model="configData.customerWeappSecret" type="password" show-password />
            </el-form-item>

            <el-divider content-position="left">商户端微信小程序 (主理人助手)</el-divider>
            <el-form-item label="商户小程序 AppID">
              <el-input v-model="configData.merchantWeappAppId" placeholder="例如：wx8h7g6f5e4d3c2b1a" />
            </el-form-item>
            <el-form-item label="商户小程序 AppSecret">
              <el-input v-model="configData.merchantWeappSecret" type="password" show-password />
            </el-form-item>

            <el-divider content-position="left">微信官方服务号与模板消息</el-divider>
            <el-form-item label="服务号 AppID">
              <el-input v-model="configData.officialAppId" />
            </el-form-item>
            <el-form-item label="服务号 AppSecret">
              <el-input v-model="configData.officialSecret" type="password" show-password />
            </el-form-item>
            <el-form-item label="服务器配置 Token">
              <el-input v-model="configData.officialToken" />
            </el-form-item>
            <el-form-item label="EncodingAESKey">
              <el-input v-model="configData.officialAesKey" />
            </el-form-item>
            <el-form-item label="支付成功提醒模板ID">
              <el-input v-model="configData.tmplPaySuccess" />
            </el-form-item>
            <el-form-item label="核销完成提醒模板ID">
              <el-input v-model="configData.tmplVerifyNotify" />
            </el-form-item>
            <el-form-item label="提现到账提醒模板ID">
              <el-input v-model="configData.tmplWithdrawSuccess" />
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <!-- 安全与操作审计日志 -->
        <el-tab-pane label="安全与操作审计日志" name="AUDIT_LOGS">
          <div style="margin-bottom: 12px; display: flex; justify-content: flex-end">
            <el-button size="small" @click="fetchAuditLogs">刷新日志</el-button>
          </div>
          <el-table v-loading="loadingLogs" :data="auditLogs" stripe border style="width: 100%">
            <el-table-column prop="operator" label="操作人" width="160" />
            <el-table-column prop="action" label="操作事件" min-width="180">
              <template #default="{ row }">
                <el-tag size="small" type="primary">{{ row.action }}</el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="targetEntity" label="目标实体" width="140" />
            <el-table-column prop="targetId" label="目标标识" width="120" />
            <el-table-column prop="ipAddress" label="客户端IP" width="140" />
            <el-table-column prop="createdAt" label="操作时间" width="180">
              <template #default="{ row }">
                {{ formatDateTime(row.createdAt) }}
              </template>
            </el-table-column>
          </el-table>

          <div style="margin-top: 16px; display: flex; justify-content: flex-end">
            <el-pagination
              v-model:current-page="auditPagination.page"
              v-model:page-size="auditPagination.pageSize"
              :total="auditPagination.total"
              layout="total, prev, pager, next"
              @current-change="fetchAuditLogs"
            />
          </div>
        </el-tab-pane>
      </el-tabs>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import request from '../../utils/request';

const activeTab = ref('WECHAT_PAY');
const saving = ref(false);
const loadingLogs = ref(false);

const configData = reactive<Record<string, any>>({
  mchId: '',
  apiV3Key: '',
  certSerialNo: '',
  huifuMerId: '',
  settleUnfreezeDays: 7,
  orderAutoCancelMinutes: 30,
  taxThreshold: 800,
  partnerInviteRewardRatio: 25,
  cityBranchProfitRatio: 25,
  customerWeappAppId: '',
  customerWeappSecret: '',
  merchantWeappAppId: '',
  merchantWeappSecret: '',
  officialAppId: '',
  officialSecret: '',
  officialToken: '',
  officialAesKey: '',
  tmplPaySuccess: '',
  tmplVerifyNotify: '',
  tmplWithdrawSuccess: '',
});

const auditLogs = ref<any[]>([]);
const auditPagination = reactive({
  page: 1,
  pageSize: 15,
  total: 0,
});

async function fetchConfigs() {
  try {
    const res: any = await request.get('/system/configs');
    if (res?.data) {
      Object.assign(configData, res.data);
    }
  } catch (err: any) {
    ElMessage.error(err.message || '加载系统参数失败');
  }
}

async function fetchAuditLogs() {
  loadingLogs.value = true;
  try {
    const res: any = await request.get('/system/audit-logs', {
      params: {
        page: auditPagination.page,
        pageSize: auditPagination.pageSize,
      },
    });
    if (res?.data) {
      auditLogs.value = res.data.items || [];
      auditPagination.total = res.data.total || 0;
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取审计日志失败');
  } finally {
    loadingLogs.value = false;
  }
}

async function saveAllConfigs() {
  saving.value = true;
  try {
    await request.post('/system/configs', configData);
    ElMessage.success('系统全局动态配置已持久化至数据库并热重载生效！');
    if (activeTab.value === 'AUDIT_LOGS') {
      fetchAuditLogs();
    }
  } catch (err: any) {
    ElMessage.error(err.message || '保存配置失败');
  } finally {
    saving.value = false;
  }
}

function handleTabChange(tabName: any) {
  if (tabName === 'AUDIT_LOGS') {
    fetchAuditLogs();
  }
}

function formatDateTime(val: any) {
  if (!val) return '-';
  const d = new Date(val);
  return d.toLocaleString('zh-CN', { hour12: false });
}

onMounted(() => {
  fetchConfigs();
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
.field-hint {
  font-size: 12px;
  color: #909399;
  margin-left: 10px;
}
</style>
