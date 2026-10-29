<template>
  <div class="trade-container">
    <el-card shadow="never">
      <div class="header-action">
        <h3>全渠道订单履约中心 (Action-driven FSM)</h3>
      </div>

      <el-table :data="orderList" stripe style="width: 100%">
        <el-table-column prop="orderNo" label="订单号" min-width="180">
          <template #default="{ row }">
            <span style="font-family: monospace; font-weight: 600">{{ row.orderNo }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="tradeType" label="履约方式" width="120">
          <template #default="{ row }">
            <el-tag :type="getTradeTypeTag(row.tradeType)" size="small">
              {{ getTradeTypeName(row.tradeType) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="goodsName" label="购买商品" min-width="180" />
        <el-table-column prop="payAmount" label="实付金额" width="120">
          <template #default="{ row }">
            <span style="font-weight: 700; color: #f56c6c">￥{{ row.payAmount }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="orderStatus" label="当前状态" width="120">
          <template #default="{ row }">
            <el-tag :type="getStatusTag(row.orderStatus)">{{ getStatusName(row.orderStatus) }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="verifyCode" label="核销码/预约信息" min-width="160">
          <template #default="{ row }">
            <div v-if="row.verifyCode" style="font-weight: 600; color: #409eff">
              码: {{ row.verifyCode }}
            </div>
            <div v-if="row.appointmentTime" style="font-size: 12px; color: #909399">
              预约: {{ row.appointmentTime }}
            </div>
          </template>
        </el-table-column>
        <el-table-column label="动作矩阵" width="150" fixed="right">
          <template #default="{ row }">
            <el-button
              v-if="row.orderStatus === 'PAID' && (row.tradeType === 'PICKUP' || row.tradeType === 'APPOINTMENT')"
              type="primary"
              size="small"
              @click="handleManualVerify(row)"
            >
              券码核销
            </el-button>
            <el-button
              v-else-if="row.orderStatus === 'PAID' && row.tradeType === 'EXPRESS'"
              type="success"
              size="small"
            >
              打单发货
            </el-button>
            <span v-else style="color: #909399; font-size: 12px">流程结束</span>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ElMessage } from 'element-plus';

const orderList = ref([
  {
    orderNo: 'CA2026102900018899',
    tradeType: 'PICKUP',
    goodsName: '精选同城烘焙拿铁套餐 x2',
    payAmount: '74.48',
    orderStatus: 'PAID',
    verifyCode: '889922114455',
  },
  {
    orderNo: 'CA2026102900021177',
    tradeType: 'APPOINTMENT',
    goodsName: '宠物专业全身精修 SPA (小型犬)',
    payAmount: '161.28',
    orderStatus: 'PAID',
    verifyCode: '665511223344',
    appointmentTime: '2026-10-30 14:00',
  },
  {
    orderNo: 'CA2026102900035544',
    tradeType: 'EXPRESS',
    goodsName: '特级冻干猫粮 2.5kg',
    payAmount: '122.88',
    orderStatus: 'PAID',
  },
]);

function getTradeTypeTag(type: string) {
  const map: Record<string, string> = {
    PICKUP: 'success',
    APPOINTMENT: 'warning',
    EXPRESS: 'primary',
    TAKEOUT: 'danger',
  };
  return map[type] || 'info';
}

function getTradeTypeName(type: string) {
  const map: Record<string, string> = {
    PICKUP: '到店自提',
    APPOINTMENT: '到店预约',
    EXPRESS: '全国快递',
    TAKEOUT: '同城外卖',
  };
  return map[type] || type;
}

function getStatusTag(status: string) {
  const map: Record<string, string> = {
    PENDING_PAY: 'info',
    PAID: 'primary',
    COMPLETED: 'success',
    CANCELLED: 'info',
  };
  return map[status] || 'info';
}

function getStatusName(status: string) {
  const map: Record<string, string> = {
    PENDING_PAY: '待支付',
    PAID: '待核销/履约',
    COMPLETED: '已完成',
    CANCELLED: '已取消',
  };
  return map[status] || status;
}

function handleManualVerify(row: any) {
  row.orderStatus = 'COMPLETED';
  ElMessage.success(`订单 ${row.orderNo} 核销完成！分润状态机已启动`);
}
</script>

<style scoped>
.header-action {
  margin-bottom: 16px;
}
</style>
