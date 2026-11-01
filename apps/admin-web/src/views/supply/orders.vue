<template>
  <div class="supply-orders-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>B2B 商家批发采购订单与发货履约 (Wholesale Fulfillment)</h3>
          <p class="sub-tip">管理各同城品牌主理人发起的集中采购与批发补货订单，打单发货并追踪物流单号</p>
        </div>
        <div class="header-filters">
          <el-select v-model="filterStatus" placeholder="订单状态" clearable style="width: 140px; margin-right: 8px" @change="fetchOrders">
            <el-option label="全部状态" value="" />
            <el-option label="待支付" value="PENDING_PAY" />
            <el-option label="待发货" value="PAID" />
            <el-option label="已发货" value="SHIPPED" />
            <el-option label="已完成" value="COMPLETED" />
          </el-select>
          <el-button @click="fetchOrders">刷新</el-button>
        </div>
      </div>

      <el-table v-loading="loading" :data="orderList" stripe style="width: 100%">
        <el-table-column prop="orderNo" label="采购单号" min-width="170">
          <template #default="{ row }">
            <span style="font-family: monospace; font-weight: 600">{{ row.orderNo }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="buyerShopName" label="采购商家 (主理人)" min-width="160">
          <template #default="{ row }">
            <span>{{ row.buyerShopName || '商户门店' }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="totalAmount" label="批发采购总额" width="130">
          <template #default="{ row }">
            <span style="font-weight: 700; color: #f56c6c">￥{{ row.totalAmount }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="shippingTrackingNo" label="物流单号" min-width="160">
          <template #default="{ row }">
            <span v-if="row.shippingTrackingNo" style="font-family: monospace">{{ row.shippingTrackingNo }}</span>
            <span v-else style="color: #909399">尚未发货</span>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="110">
          <template #default="{ row }">
            <el-tag :type="getStatusTag(row.status)">{{ getStatusName(row.status) }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="createdAt" label="下单时间" min-width="160">
          <template #default="{ row }">
            {{ formatDate(row.createdAt) }}
          </template>
        </el-table-column>
        <el-table-column label="操作" width="140" fixed="right">
          <template #default="{ row }">
            <el-button
              v-if="row.status === 'PAID' || row.status === 'PENDING_PAY'"
              type="primary"
              link
              size="small"
              @click="openShipDialog(row)"
            >
              打单发货
            </el-button>
            <el-button
              v-else-if="row.status === 'SHIPPED'"
              type="info"
              link
              size="small"
              @click="openShipDialog(row)"
            >
              修改运单
            </el-button>
            <span v-else style="color: #909399; font-size: 13px">已办结</span>
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
          @size-change="fetchOrders"
          @current-change="fetchOrders"
        />
      </div>
    </el-card>

    <!-- 发货弹窗 -->
    <el-dialog v-model="shipDialogVisible" title="采购订单物流发货打单" width="480px">
      <el-form label-width="110px">
        <el-form-item label="采购单号">
          <span style="font-family: monospace; font-weight: 600">{{ currentOrder?.orderNo }}</span>
        </el-form-item>
        <el-form-item label="采购商家">
          <span>{{ currentOrder?.buyerShopName }}</span>
        </el-form-item>
        <el-form-item label="物流运单号" required>
          <el-input v-model="trackingNo" placeholder="请输入中通/顺丰/德邦运单号..." />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="shipDialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="shipping" @click="confirmShip">
          确认发货出库
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
const orderList = ref<any[]>([]);
const total = ref(0);
const page = ref(1);
const pageSize = ref(20);
const filterStatus = ref('');

const shipDialogVisible = ref(false);
const shipping = ref(false);
const currentOrder = ref<any>(null);
const trackingNo = ref('');

function formatDate(val?: string) {
  if (!val) return '-';
  const d = new Date(val);
  return d.toLocaleString('zh-CN', { hour12: false });
}

function getStatusTag(st: string) {
  const map: Record<string, string> = {
    PENDING_PAY: 'warning',
    PAID: 'primary',
    SHIPPED: 'success',
    COMPLETED: 'info',
  };
  return map[st] || 'info';
}

function getStatusName(st: string) {
  const map: Record<string, string> = {
    PENDING_PAY: '待商家付款',
    PAID: '待供货商发货',
    SHIPPED: '已发货在途',
    COMPLETED: '已签收完成',
  };
  return map[st] || st;
}

async function fetchOrders() {
  loading.value = true;
  try {
    const res: any = await request.get('/supply/purchase-orders', {
      params: {
        page: page.value,
        pageSize: pageSize.value,
        status: filterStatus.value || undefined,
      },
    });
    if (res && res.data) {
      orderList.value = res.data.items || [];
      total.value = res.data.total || 0;
    }
  } catch (err: any) {
    console.error('Failed to fetch purchase orders:', err);
  } finally {
    loading.value = false;
  }
}

function openShipDialog(row: any) {
  currentOrder.value = row;
  trackingNo.value = row.shippingTrackingNo || '';
  shipDialogVisible.value = true;
}

async function confirmShip() {
  if (!trackingNo.value.trim()) {
    ElMessage.warning('请输入运单号');
    return;
  }
  shipping.value = true;
  try {
    await request.post(`/supply/purchase-orders/${currentOrder.value.id}/ship`, {
      trackingNo: trackingNo.value.trim(),
    });
    ElMessage.success('发货成功！运单号已绑定');
    shipDialogVisible.value = false;
    await fetchOrders();
  } catch (err: any) {
    ElMessage.error(err?.response?.data?.message || '发货失败');
  } finally {
    shipping.value = false;
  }
}

onMounted(() => {
  fetchOrders();
});
</script>

<style scoped>
.supply-orders-container {
  padding: 16px;
}
.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 16px;
}
.header-filters {
  display: flex;
  align-items: center;
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
