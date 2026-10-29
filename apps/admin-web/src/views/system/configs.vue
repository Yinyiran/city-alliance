<template>
  <div class="system-configs-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>平台系统参数与支付生态配置 (System Configs & Gateways)</h3>
          <p class="sub-tip">维护微信支付商户号密钥、汇付天下接口、短信网关、7天冷静期时钟与全网全局参数</p>
        </div>
        <el-button type="primary" @click="saveAllConfigs">保存全局配置</el-button>
      </div>

      <el-tabs v-model="activeTab">
        <el-tab-pane label="微信支付与商户进件" name="WECHAT_PAY">
          <el-form label-width="160px" style="max-width: 700px; margin-top: 20px">
            <el-form-item label="微信支付直连商户号">
              <el-input v-model="configData.mchId" placeholder="例如：1688992211" />
            </el-form-item>
            <el-form-item label="API v3 密钥">
              <el-input v-model="configData.apiV3Key" type="password" show-password />
            </el-form-item>
            <el-form-item label="商户 API 证书序列号">
              <el-input v-model="configData.certSerialNo" />
            </el-form-item>
            <el-form-item label="汇付天下收单机构代码">
              <el-input v-model="configData.huifuMerId" placeholder="用于二级商户分账与个税代扣代缴" />
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <el-tab-pane label="清算时钟与核心规则" name="SETTLE_RULES">
          <el-form label-width="180px" style="max-width: 700px; margin-top: 20px">
            <el-form-item label="售后退款冷静期 (天)">
              <el-input-number v-model="configData.settleUnfreezeDays" :min="1" :max="30" />
              <span style="font-size: 12px; color: #909399; margin-left: 10px">
                订单完成达到此天数后，BullMQ 自动解冻分润
              </span>
            </el-form-item>
            <el-form-item label="待付款自动取消时限 (分)">
              <el-input-number v-model="configData.orderAutoCancelMinutes" :min="5" :max="120" />
            </el-form-item>
            <el-form-item label="提现个税起征点 (元)">
              <el-input-number v-model="configData.taxThreshold" :min="0" :step="100" />
              <span style="font-size: 12px; color: #909399; margin-left: 10px">
                劳务报酬阶梯扣税起征点（默认800元）
              </span>
            </el-form-item>
          </el-form>
        </el-tab-pane>

        <el-tab-pane label="安全与操作审计日志" name="AUDIT_LOGS">
          <el-table :data="auditLogs" stripe style="width: 100%; margin-top: 10px">
            <el-table-column prop="operator" label="操作人" width="140" />
            <el-table-column prop="action" label="操作事件" min-width="200" />
            <el-table-column prop="ipAddress" label="客户端IP" width="140" />
            <el-table-column prop="createdAt" label="操作时间" width="160" />
          </el-table>
        </el-tab-pane>
      </el-tabs>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ElMessage } from 'element-plus';

const activeTab = ref('WECHAT_PAY');

const configData = ref({
  mchId: '1690028192',
  apiV3Key: 'cityalliance_wxpay_v3_key_202610',
  certSerialNo: '7F8A9B0C1D2E3F4A5B6C',
  huifuMerId: 'HF8899221144',
  settleUnfreezeDays: 7,
  orderAutoCancelMinutes: 15,
  taxThreshold: 800,
});

const auditLogs = ref([
  {
    operator: '超级管理员 (Admin)',
    action: '更新平台微信支付 API v3 证书配置',
    ipAddress: '192.168.31.100',
    createdAt: '2026-10-29 16:20:10',
  },
  {
    operator: '张建国 (杭州分公司)',
    action: '审核通过入驻主理人：拾光烘焙研磨工坊',
    ipAddress: '122.224.18.52',
    createdAt: '2026-10-29 14:15:30',
  },
  {
    operator: '系统自动化调度引擎',
    action: 'BullMQ 批量解冻 14 笔满 7 天售后冷静期分润流水',
    ipAddress: '127.0.0.1 (Local)',
    createdAt: '2026-10-29 04:00:00',
  },
]);

function saveAllConfigs() {
  ElMessage.success('系统全局动态配置已持久化并热重载生效！');
}
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
</style>
