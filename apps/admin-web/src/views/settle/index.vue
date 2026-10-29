<template>
  <div class="settle-container">
    <el-tabs type="border-card">
      <el-tab-pane label="日常消费与分润账本 (自签单2% vs 带单6%)">
        <el-table :data="commissionList" stripe style="width: 100%">
          <el-table-column prop="orderNo" label="关联订单号" min-width="160" />
          <el-table-column prop="type" label="分成模式" width="160">
            <template #default="{ row }">
              <el-tag :type="row.type === '自签单' ? 'success' : 'warning'">{{ row.type }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="orderAmount" label="订单金额" width="110">
            <template #default="{ row }">￥{{ row.orderAmount }}</template>
          </el-table-column>
          <el-table-column prop="roleAllocations" label="四级分润明细" min-width="260">
            <template #default="{ row }">
              <div v-for="item in row.allocations" :key="item.role" class="alloc-item">
                <span class="alloc-role">{{ item.role }}:</span>
                <span class="alloc-amount">￥{{ item.amount }} ({{ item.ratio }}%)</span>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="status" label="解冻状态机" width="120">
            <template #default="{ row }">
              <el-tag :type="row.status === 'SETTLED' ? 'success' : 'warning'">
                {{ row.status === 'SETTLED' ? '已解冻入账' : '7天冻结保护中' }}
              </el-tag>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>

      <el-tab-pane label="实名提现与阶梯个税代扣代缴审核">
        <el-alert
          title="合规税务提示：劳务报酬提现800元以内免征；800~4000扣除800；4000以上按80%计税并执行20%/30%/40%超额累进税率，平台手续费0.35%。"
          type="info"
          :closable="false"
          style="margin-bottom: 16px"
        />

        <el-table :data="withdrawList" stripe style="width: 100%">
          <el-table-column prop="realName" label="收款人姓名" width="110" />
          <el-table-column prop="idCardNo" label="身份证号" width="170" />
          <el-table-column prop="grossAmount" label="申请提现毛额" width="120">
            <template #default="{ row }">￥{{ row.grossAmount }}</template>
          </el-table-column>
          <el-table-column prop="taxAmount" label="代扣个税" width="110">
            <template #default="{ row }">
              <span style="color: #f56c6c">￥{{ row.taxAmount }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="platformFee" label="手续费 (0.35%)" width="120">
            <template #default="{ row }">￥{{ row.platformFee }}</template>
          </el-table-column>
          <el-table-column prop="netAmount" label="实际到账净额" width="120">
            <template #default="{ row }">
              <span style="font-weight: 700; color: #67c23a">￥{{ row.netAmount }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="status" label="放款状态" width="110">
            <template #default="{ row }">
              <el-tag :type="row.status === 'SUCCESS' ? 'success' : 'warning'" size="small">
                {{ row.status === 'SUCCESS' ? '对公已放款' : '待对公审核' }}
              </el-tag>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const commissionList = ref([
  {
    orderNo: 'CA2026102900018899',
    type: '自签单 (扣2%)',
    orderAmount: '200.00',
    allocations: [
      { role: '城市分公司 (0.5%)', amount: '1.00', ratio: '0.5' },
      { role: '平台总部留存 (1.5%)', amount: '3.00', ratio: '1.5' },
      { role: '商家实际结算到手', amount: '196.00', ratio: '98' },
    ],
    status: 'SETTLED',
  },
  {
    orderNo: 'CA2026102900021177',
    type: '跨店带单 (扣6%)',
    orderAmount: '500.00',
    allocations: [
      { role: '带单合伙人 (0.5%)', amount: '2.50', ratio: '0.5' },
      { role: '商家主理人 (2.0%)', amount: '10.00', ratio: '2.0' },
      { role: '城市分公司 (1.5%)', amount: '7.50', ratio: '1.5' },
      { role: '平台总部留存 (2.0%)', amount: '10.00', ratio: '2.0' },
      { role: '商家实际结算到手', amount: '470.00', ratio: '94' },
    ],
    status: 'FROZEN',
  },
]);

const withdrawList = ref([
  {
    realName: '张三 (店员)',
    idCardNo: '3301021990********',
    grossAmount: '2800.00',
    taxAmount: '400.00', // (2800 - 800) * 20%
    platformFee: '9.80', // 2800 * 0.35%
    netAmount: '2390.20',
    status: 'SUCCESS',
  },
  {
    realName: '李老板 (主理人)',
    idCardNo: '3301051985********',
    grossAmount: '10000.00',
    taxAmount: '1600.00', // (10000 * 80%) * 20%
    platformFee: '35.00',
    netAmount: '8365.00',
    status: 'SUCCESS',
  },
]);
</script>

<style scoped>
.alloc-item {
  font-size: 12px;
  line-height: 1.6;
}
.alloc-role {
  color: #606266;
  margin-right: 6px;
}
.alloc-amount {
  color: #409eff;
  font-weight: 600;
}
</style>
