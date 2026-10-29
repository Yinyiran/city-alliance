<template>
  <div class="shifts-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>移动收银台交接班与轧帐流水 (Cashier Shift Reconciliation)</h3>
          <p class="sub-tip">记录收银员交接班时间、现金实收、聚合扫码实收总额与盘点差异</p>
        </div>
      </div>

      <el-table :data="shiftList" stripe style="width: 100%">
        <el-table-column prop="storeName" label="营业实体门店" min-width="180" />
        <el-table-column prop="clerkName" label="交班收银员" width="130">
          <template #default="{ row }">
            <div>{{ row.clerkName }}</div>
            <div style="font-size: 12px; color: #909399">{{ row.clerkNo }}</div>
          </template>
        </el-table-column>
        <el-table-column label="交接班时间跨度" min-width="240">
          <template #default="{ row }">
            <div>起: {{ row.shiftStart }}</div>
            <div style="color: #909399">止: {{ row.shiftEnd || '当前值班中...' }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="orderCount" label="核销/收银笔数" width="130">
          <template #default="{ row }">
            <span style="font-weight: 600">{{ row.orderCount }} 笔</span>
          </template>
        </el-table-column>
        <el-table-column prop="scanTotal" label="聚合扫码实收" width="140">
          <template #default="{ row }">
            <span style="color: #409eff; font-weight: 700">￥{{ row.scanTotal }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="cashTotal" label="现金实收" width="120">
          <template #default="{ row }">￥{{ row.cashTotal }}</template>
        </el-table-column>
        <el-table-column prop="diffAmount" label="长短款差异" width="120">
          <template #default="{ row }">
            <span :style="{ color: Number(row.diffAmount) === 0 ? '#67c23a' : '#f56c6c', fontWeight: 'bold' }">
              {{ Number(row.diffAmount) === 0 ? '平账 (0.00)' : '差 ￥' + row.diffAmount }}
            </span>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="班次状态" width="110">
          <template #default="{ row }">
            <el-tag :type="row.status === 'CLOSED' ? 'success' : 'warning'">
              {{ row.status === 'CLOSED' ? '已结班轧账' : '值班中' }}
            </el-tag>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const shiftList = ref([
  {
    id: 'sh-1',
    storeName: '拾光烘焙研磨 (西湖总店)',
    clerkName: '王小芬',
    clerkNo: 'CLK-001',
    shiftStart: '2026-10-29 08:30:00',
    shiftEnd: '2026-10-29 16:30:00',
    orderCount: 84,
    scanTotal: '3,842.50',
    cashTotal: '150.00',
    diffAmount: '0.00',
    status: 'CLOSED',
  },
  {
    id: 'sh-2',
    storeName: '拾光烘焙研磨 (西湖总店)',
    clerkName: '张敏',
    clerkNo: 'CLK-004',
    shiftStart: '2026-10-29 16:30:00',
    shiftEnd: '2026-10-29 22:30:00',
    orderCount: 62,
    scanTotal: '2,910.00',
    cashTotal: '80.00',
    diffAmount: '0.00',
    status: 'CLOSED',
  },
  {
    id: 'sh-3',
    storeName: '萌宠星球生活馆 (城西旗舰店)',
    clerkName: '刘婷婷',
    clerkNo: 'CLK-003',
    shiftStart: '2026-10-30 09:00:00',
    shiftEnd: null,
    orderCount: 18,
    scanTotal: '890.00',
    cashTotal: '0.00',
    diffAmount: '0.00',
    status: 'OPEN',
  },
]);
</script>

<style scoped>
.header-action {
  margin-bottom: 16px;
}
.sub-tip {
  color: #909399;
  font-size: 13px;
  margin-top: 4px;
}
</style>
