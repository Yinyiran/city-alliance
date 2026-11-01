<template>
  <div class="shifts-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>移动收银台交接班与轧帐流水 (Cashier Shift Reconciliation)</h3>
          <p class="sub-tip">记录收银员交接班时间、现金实收、聚合扫码实收总额与盘点差异</p>
        </div>
        <el-button @click="fetchShifts">
          <el-icon><Refresh /></el-icon> 刷新流水
        </el-button>
      </div>

      <el-table v-loading="loading" :data="shiftList" stripe style="width: 100%">
        <el-table-column prop="storeName" label="营业实体门店" min-width="180" />
        <el-table-column prop="clerkName" label="交班收银员" width="130">
          <template #default="{ row }">
            <div>{{ row.clerkName || '值班员' }}</div>
            <div style="font-size: 12px; color: #909399">{{ row.clerkNo || '-' }}</div>
          </template>
        </el-table-column>
        <el-table-column label="交接班时间跨度" min-width="240">
          <template #default="{ row }">
            <div>起: {{ formatDate(row.shiftStart) }}</div>
            <div style="color: #909399">止: {{ row.shiftEnd ? formatDate(row.shiftEnd) : '当前值班中...' }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="orderCount" label="核销/收银笔数" width="130">
          <template #default="{ row }">
            <span style="font-weight: 600">{{ row.orderCount }} 笔</span>
          </template>
        </el-table-column>
        <el-table-column prop="scanTotal" label="聚合扫码实收" width="140">
          <template #default="{ row }">
            <span style="color: #409eff; font-weight: 700">￥{{ Number(row.scanTotal || 0).toFixed(2) }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="cashTotal" label="现金实收" width="120">
          <template #default="{ row }">￥{{ Number(row.cashTotal || 0).toFixed(2) }}</template>
        </el-table-column>
        <el-table-column prop="diffAmount" label="长短款差异" width="120">
          <template #default="{ row }">
            <span :style="{ color: Number(row.diffAmount) === 0 ? '#67c23a' : '#f56c6c', fontWeight: 'bold' }">
              {{ Number(row.diffAmount) === 0 ? '平账 (0.00)' : '差 ￥' + Number(row.diffAmount).toFixed(2) }}
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
        <el-table-column prop="remark" label="备注" min-width="140" show-overflow-tooltip>
          <template #default="{ row }">
            <span>{{ row.remark || '-' }}</span>
          </template>
        </el-table-column>
      </el-table>

      <div class="pagination-container">
        <el-pagination
          v-model:current-page="page"
          v-model:page-size="pageSize"
          :total="total"
          :page-sizes="[10, 20, 50]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="fetchShifts"
          @current-change="fetchShifts"
        />
      </div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Refresh } from '@element-plus/icons-vue';
import request from '@/utils/request';

const loading = ref(false);
const shiftList = ref<any[]>([]);
const total = ref(0);
const page = ref(1);
const pageSize = ref(20);

function formatDate(val?: string) {
  if (!val) return '-';
  const d = new Date(val);
  return d.toLocaleString('zh-CN', { hour12: false });
}

async function fetchShifts() {
  loading.value = true;
  try {
    const res: any = await request.get('/stores/shifts', {
      params: {
        page: page.value,
        pageSize: pageSize.value,
      },
    });
    if (res && res.data) {
      shiftList.value = res.data.items || [];
      total.value = res.data.total || 0;
    }
  } catch (err: any) {
    console.error('Failed to fetch shifts:', err);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchShifts();
});
</script>

<style scoped>
.shifts-container {
  padding: 16px;
}
.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.sub-tip {
  color: #909399;
  font-size: 13px;
  margin-top: 4px;
}
.pagination-container {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}
</style>
