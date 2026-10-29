<template>
  <div class="members-container">
    <el-card shadow="never">
      <div class="filter-header">
        <el-form :inline="true" :model="filters">
          <el-form-item label="会员编号">
            <el-input v-model="filters.code" placeholder="输入 6 位数会员号" clearable />
          </el-form-item>
          <el-form-item label="会员等级">
            <el-select v-model="filters.level" placeholder="全部等级" clearable style="width: 140px">
              <el-option label="普通会员" value="NORMAL" />
              <el-option label="钻石会员" value="DIAMOND" />
              <el-option label="黑钻会员" value="BLACK_DIAMOND" />
              <el-option label="创客合伙人" value="PARTNER" />
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary">查询</el-button>
            <el-button @click="openCustomPartnerDialog">老板自主定价开通合伙人</el-button>
          </el-form-item>
        </el-form>
      </div>

      <el-table :data="memberList" stripe style="width: 100%">
        <el-table-column prop="memberCode" label="会员编号" width="110">
          <template #default="{ row }">
            <el-tag effect="plain" type="info">{{ row.memberCode }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="nickname" label="昵称/手机号" min-width="150">
          <template #default="{ row }">
            <div style="font-weight: 600">{{ row.nickname }}</div>
            <div style="font-size: 12px; color: #909399">{{ row.mobile }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="level" label="会员等级" width="130">
          <template #default="{ row }">
            <el-tag :type="getLevelTagType(row.level)">{{ getLevelName(row.level) }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="inviterCode" label="终身绑定上级" width="130">
          <template #default="{ row }">
            <span v-if="row.inviterCode">{{ row.inviterCode }} ({{ row.inviterName }})</span>
            <span v-else style="color: #c0c4cc">直属平台</span>
          </template>
        </el-table-column>
        <el-table-column prop="points" label="会员积分" width="100" />
        <el-table-column prop="balance" label="可用余额" width="120">
          <template #default="{ row }">￥{{ row.balance }}</template>
        </el-table-column>
        <el-table-column prop="frozenBalance" label="7天冻结佣金" width="120">
          <template #default="{ row }">
            <span style="color: #e6a23c">￥{{ row.frozenBalance }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="isRealNameVerified" label="实名认证" width="100">
          <template #default="{ row }">
            <el-tag v-if="row.isRealNameVerified" type="success" size="small">已认证</el-tag>
            <el-tag v-else type="warning" size="small">待认证</el-tag>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 老板主动自主定价开通合伙人弹窗 -->
    <el-dialog v-model="partnerDialogVisible" title="主理人自主定价开通创客合伙人" width="480px">
      <el-form label-width="110px">
        <el-form-item label="开通对象">
          <el-input placeholder="输入 6 位会员编号" />
        </el-form-item>
        <el-form-item label="开通金额 (元)">
          <el-input v-model="customPartnerPrice" placeholder="自定义金额 (走主理人50%+分公司50%)" />
        </el-form-item>
        <el-alert
          title="分润规则说明：老板自主添加合伙人，收款只走主理人50%与分公司50%实时清算分润，平台留存0元。"
          type="info"
          :closable="false"
          style="margin-bottom: 16px"
        />
      </el-form>
      <template #footer>
        <el-button @click="partnerDialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleConfirmPartner">确认开通并分润</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { ElMessage } from 'element-plus';

const filters = reactive({
  code: '',
  level: '',
});

const partnerDialogVisible = ref(false);
const customPartnerPrice = ref('1000.00');

const memberList = ref([
  {
    memberCode: '100088',
    nickname: '张三 (主理人店员)',
    mobile: '138****0001',
    level: 'PARTNER',
    inviterCode: '100001',
    inviterName: '李老板',
    points: 12500,
    balance: '3580.00',
    frozenBalance: '820.00',
    isRealNameVerified: true,
  },
  {
    memberCode: '100092',
    nickname: '同城常客-王女士',
    mobile: '139****1122',
    level: 'BLACK_DIAMOND',
    inviterCode: '100088',
    inviterName: '张三',
    points: 4200,
    balance: '640.00',
    frozenBalance: '180.00',
    isRealNameVerified: true,
  },
  {
    memberCode: '100105',
    nickname: '微信顾客_a7f9',
    mobile: '158****9988',
    level: 'NORMAL',
    inviterCode: '100092',
    inviterName: '王女士',
    points: 150,
    balance: '0.00',
    frozenBalance: '0.00',
    isRealNameVerified: false,
  },
]);

function getLevelTagType(level: string) {
  const map: Record<string, string> = {
    NORMAL: 'info',
    DIAMOND: 'primary',
    BLACK_DIAMOND: 'warning',
    PARTNER: 'danger',
  };
  return map[level] || 'info';
}

function getLevelName(level: string) {
  const map: Record<string, string> = {
    NORMAL: '普通会员',
    DIAMOND: '钻石会员 (98折)',
    BLACK_DIAMOND: '黑钻会员 (96折)',
    PARTNER: '创客合伙人 (95折)',
  };
  return map[level] || level;
}

function openCustomPartnerDialog() {
  partnerDialogVisible.value = true;
}

function handleConfirmPartner() {
  ElMessage.success('合伙人定向开通成功！主理人50%与分公司50%已记入账本');
  partnerDialogVisible.value = false;
}
</script>

<style scoped>
.filter-header {
  margin-bottom: 16px;
}
</style>
