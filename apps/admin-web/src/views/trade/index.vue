<template>
  <div class="trade-container">
    <el-card shadow="never">
      <!-- 头部操作区 -->
      <div class="header-action">
        <div class="title-group">
          <h3>全渠道订单履约与分润核销中心 (Omnichannel Fulfillment)</h3>
          <p class="subtitle">统一支持自提核销、预约到店、快递打单发货、同城外卖调度及逆向售后退款</p>
        </div>
        <div class="btn-group">
          <el-button type="primary" :icon="Check" @click="openQuickVerifyDialog">
            券码快速核销 (12位)
          </el-button>
          <el-button :icon="Refresh" @click="fetchOrders">刷新列表</el-button>
        </div>
      </div>

      <!-- 搜索与筛选区 -->
      <el-form :inline="true" :model="queryForm" class="filter-form">
        <el-form-item label="单号/核销码">
          <el-input
            v-model="queryForm.keyword"
            placeholder="输入订单号或12位核销码"
            clearable
            style="width: 220px"
            @keyup.enter="handleSearch"
          />
        </el-form-item>

        <el-form-item label="履约方式">
          <el-select v-model="queryForm.tradeType" placeholder="全部方式" clearable style="width: 140px">
            <el-option label="全部方式" value="" />
            <el-option label="到店自提" value="PICKUP" />
            <el-option label="到店预约" value="APPOINTMENT" />
            <el-option label="全国快递" value="EXPRESS" />
            <el-option label="同城外卖" value="TAKEOUT" />
          </el-select>
        </el-form-item>

        <el-form-item label="订单状态">
          <el-select v-model="queryForm.orderStatus" placeholder="全部状态" clearable style="width: 140px">
            <el-option label="全部状态" value="" />
            <el-option label="待支付" value="PENDING_PAY" />
            <el-option label="待履约/已支付" value="PAID" />
            <el-option label="配送/发货中" value="DELIVERING" />
            <el-option label="已完成" value="COMPLETED" />
            <el-option label="退款中" value="REFUNDING" />
            <el-option label="已退款" value="REFUNDED" />
            <el-option label="已取消" value="CANCELLED" />
          </el-select>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" :icon="Search" @click="handleSearch">查询</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <!-- 订单表格 -->
      <el-table v-loading="loading" :data="orderList" stripe border style="width: 100%">
        <el-table-column prop="orderNo" label="订单号 / 下单时间" min-width="210">
          <template #default="{ row }">
            <div style="font-family: monospace; font-weight: 600; color: #303133">{{ row.orderNo }}</div>
            <div style="font-size: 12px; color: #909399; margin-top: 4px">
              {{ formatDateTime(row.createdAt) }}
            </div>
          </template>
        </el-table-column>

        <el-table-column label="所属店铺 / 门店" min-width="160">
          <template #default="{ row }">
            <div style="font-weight: 500">{{ row.shopName || '平台直营' }}</div>
            <div v-if="row.storeName" style="font-size: 12px; color: #606266; margin-top: 2px">
              {{ row.storeName }}
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="tradeType" label="履约方式" width="110" align="center">
          <template #default="{ row }">
            <el-tag :type="getTradeTypeTag(row.tradeType)" effect="light">
              {{ getTradeTypeName(row.tradeType) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column label="购买商品明细" min-width="220">
          <template #default="{ row }">
            <div v-if="row.items && row.items.length > 0">
              <div v-for="item in row.items" :key="item.id" class="item-row">
                <span class="item-name">{{ item.goodsName }}</span>
                <span v-if="item.specText" class="item-spec">({{ item.specText }})</span>
                <span class="item-qty">x{{ item.quantity }}</span>
              </div>
            </div>
            <div v-else>
              <span>{{ row.goodsName }}</span>
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="payAmount" label="实付金额" width="130" align="right">
          <template #default="{ row }">
            <div style="font-weight: 700; color: #f56c6c; font-size: 15px">￥{{ row.payAmount }}</div>
            <el-tag size="small" :type="row.payStatus === 'PAID' ? 'success' : 'info'" style="margin-top: 4px">
              {{ row.payStatus === 'PAID' ? '已支付' : '待支付' }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column prop="orderStatus" label="履约状态" width="110" align="center">
          <template #default="{ row }">
            <el-tag :type="getStatusTag(row.orderStatus)">{{ getStatusName(row.orderStatus) }}</el-tag>
          </template>
        </el-table-column>

        <el-table-column label="核销凭证 / 履约信息" min-width="170">
          <template #default="{ row }">
            <div v-if="row.verifyCode" class="verify-code-pill">
              码: <span>{{ row.verifyCode }}</span>
            </div>
            <div v-if="row.appointmentTime" style="font-size: 12px; color: #e6a23c; margin-top: 4px">
              预约: {{ formatDateTime(row.appointmentTime) }}
            </div>
            <div v-if="row.deliveryInfo?.expressNo" style="font-size: 12px; color: #409eff; margin-top: 4px">
              {{ row.deliveryInfo.expressCompany }}: {{ row.deliveryInfo.expressNo }}
            </div>
            <div v-if="row.deliveryInfo?.riderName" style="font-size: 12px; color: #67c23a; margin-top: 4px">
              骑手: {{ row.deliveryInfo.riderName }} ({{ row.deliveryInfo.riderPhone }})
            </div>
          </template>
        </el-table-column>

        <el-table-column label="操作" width="220" fixed="right">
          <template #default="{ row }">
            <!-- 到店自提/到店预约 扫码核销 -->
            <el-button
              v-if="row.orderStatus === 'PAID' && (row.tradeType === 'PICKUP' || row.tradeType === 'APPOINTMENT')"
              type="primary"
              size="small"
              @click="handleVerify(row)"
            >
              券码核销
            </el-button>

            <!-- 全国快递 打单发货 -->
            <el-button
              v-if="row.orderStatus === 'PAID' && row.tradeType === 'EXPRESS'"
              type="success"
              size="small"
              @click="openShipDialog(row)"
            >
              打单发货
            </el-button>

            <!-- 同城外卖 调度骑手 -->
            <el-button
              v-if="row.orderStatus === 'PAID' && row.tradeType === 'TAKEOUT'"
              type="warning"
              size="small"
              @click="openTakeawayDialog(row)"
            >
              调度骑手
            </el-button>

            <!-- 配送中 确认收货 -->
            <el-button
              v-if="row.orderStatus === 'DELIVERING'"
              type="info"
              size="small"
              @click="handleConfirmReceipt(row)"
            >
              确认收货
            </el-button>

            <!-- 待支付 模拟支付（方便测试） -->
            <el-button
              v-if="row.orderStatus === 'PENDING_PAY'"
              type="success"
              size="small"
              link
              @click="handlePayMock(row)"
            >
              模拟买单
            </el-button>

            <!-- 详情抽屉 -->
            <el-button type="primary" size="small" link @click="openDrawer(row)">
              详情
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页栏 -->
      <div class="pagination-bar">
        <el-pagination
          v-model:current-page="queryForm.page"
          v-model:page-size="queryForm.pageSize"
          :total="total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="fetchOrders"
          @current-change="fetchOrders"
        />
      </div>
    </el-card>

    <!-- 弹窗：快捷核销 -->
    <el-dialog v-model="quickVerifyVisible" title="门店券码核销兑换" width="460px">
      <el-form label-position="top">
        <el-form-item label="12位核销兑换码" required>
          <el-input
            v-model="quickVerifyCode"
            placeholder="请输入顾客出示的12位核销码"
            maxlength="12"
            clearable
            style="font-family: monospace; font-size: 18px; letter-spacing: 2px"
            @keyup.enter="submitQuickVerify"
          />
        </el-form-item>
        <p style="font-size: 12px; color: #909399">
          💡 核销成功后将原子级触发订单 COMPLETED 状态变更，并自动启动商户资金结算与合伙人佣金入账流程。
        </p>
      </el-form>
      <template #footer>
        <el-button @click="quickVerifyVisible = false">取消</el-button>
        <el-button type="primary" :loading="verifying" @click="submitQuickVerify">确认核销</el-button>
      </template>
    </el-dialog>

    <!-- 弹窗：打单发货 -->
    <el-dialog v-model="shipDialogVisible" title="快递打单发货" width="480px">
      <el-form :model="shipForm" label-width="100px">
        <el-form-item label="订单号">
          <span style="font-family: monospace; font-weight: 600">{{ activeOrder?.orderNo }}</span>
        </el-form-item>
        <el-form-item label="快递物流商" required>
          <el-select v-model="shipForm.expressCompany" placeholder="请选择快递公司" style="width: 100%">
            <el-option label="顺丰速运" value="顺丰速运" />
            <el-option label="中通快递" value="中通快递" />
            <el-option label="圆通速递" value="圆通速递" />
            <el-option label="韵达快递" value="韵达快递" />
            <el-option label="京东快递" value="京东快递" />
            <el-option label="极兔速递" value="极兔速递" />
          </el-select>
        </el-form-item>
        <el-form-item label="物流运单号" required>
          <el-input v-model="shipForm.expressNo" placeholder="请输入物流快递单号" clearable />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="shipDialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="shipping" @click="submitShip">确认发货</el-button>
      </template>
    </el-dialog>

    <!-- 弹窗：同城外卖调度 -->
    <el-dialog v-model="takeawayDialogVisible" title="同城外卖骑手调度" width="480px">
      <el-form :model="takeawayForm" label-width="110px">
        <el-form-item label="订单号">
          <span style="font-family: monospace; font-weight: 600">{{ activeOrder?.orderNo }}</span>
        </el-form-item>
        <el-form-item label="骑手姓名" required>
          <el-input v-model="takeawayForm.riderName" placeholder="如：张师傅" clearable />
        </el-form-item>
        <el-form-item label="骑手手机号" required>
          <el-input v-model="takeawayForm.riderPhone" placeholder="请输入11位手机号码" maxlength="11" clearable />
        </el-form-item>
        <el-form-item label="预计送达(分)">
          <el-input-number v-model="takeawayForm.estimatedDeliveryMinutes" :min="10" :max="120" style="width: 100%" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="takeawayDialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="dispatching" @click="submitTakeaway">派遣配送</el-button>
      </template>
    </el-dialog>

    <!-- 抽屉：订单履约详情 -->
    <el-drawer v-model="drawerVisible" title="订单全息详情" size="500px">
      <div v-if="activeOrder" class="drawer-content">
        <el-descriptions title="基本信息" :column="1" border>
          <el-descriptions-item label="订单编号">{{ activeOrder.orderNo }}</el-descriptions-item>
          <el-descriptions-item label="下单时间">{{ formatDateTime(activeOrder.createdAt) }}</el-descriptions-item>
          <el-descriptions-item label="会员标识">{{ activeOrder.customerCode || activeOrder.customerId }}</el-descriptions-item>
          <el-descriptions-item label="所属店铺">{{ activeOrder.shopName || '平台直营' }}</el-descriptions-item>
          <el-descriptions-item label="履约门店">{{ activeOrder.storeName || '未指定' }}</el-descriptions-item>
          <el-descriptions-item label="支付通道">{{ activeOrder.paymentChannel || '微信支付' }}</el-descriptions-item>
          <el-descriptions-item label="总金额">￥{{ activeOrder.totalAmount }}</el-descriptions-item>
          <el-descriptions-item label="实付金额">￥{{ activeOrder.payAmount }}</el-descriptions-item>
        </el-descriptions>

        <h4 style="margin: 20px 0 10px">商品列表</h4>
        <el-table :data="activeOrder.items || []" border size="small">
          <el-table-column prop="goodsName" label="商品名称" />
          <el-table-column prop="specText" label="规格" width="100" />
          <el-table-column prop="price" label="单价" width="80" />
          <el-table-column prop="quantity" label="数量" width="60" align="center" />
        </el-table>

        <div v-if="activeOrder.verifyCode" style="margin-top: 20px">
          <h4>核销状态</h4>
          <el-alert
            :title="'12位核销码: ' + activeOrder.verifyCode"
            :type="activeOrder.orderStatus === 'COMPLETED' ? 'success' : 'warning'"
            :description="activeOrder.orderStatus === 'COMPLETED' ? '已核销完成并出账' : '待顾客出示核销'"
            show-icon
            :closable="false"
          />
        </div>
      </div>
    </el-drawer>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Search, Refresh, Check } from '@element-plus/icons-vue';
import request from '../../utils/request';

const loading = ref(false);
const orderList = ref<any[]>([]);
const total = ref(0);

const queryForm = reactive({
  keyword: '',
  tradeType: '',
  orderStatus: '',
  page: 1,
  pageSize: 20,
});

// 快捷核销弹窗
const quickVerifyVisible = ref(false);
const quickVerifyCode = ref('');
const verifying = ref(false);

// 打单发货弹窗
const shipDialogVisible = ref(false);
const shipping = ref(false);
const shipForm = reactive({
  expressCompany: '顺丰速运',
  expressNo: '',
});

// 外卖调度弹窗
const takeawayDialogVisible = ref(false);
const dispatching = ref(false);
const takeawayForm = reactive({
  riderName: '',
  riderPhone: '',
  estimatedDeliveryMinutes: 30,
});

// 抽屉与当前选中订单
const drawerVisible = ref(false);
const activeOrder = ref<any>(null);

async function fetchOrders() {
  loading.value = true;
  try {
    const res: any = await request.get('/trade/orders', {
      params: {
        keyword: queryForm.keyword || undefined,
        tradeType: queryForm.tradeType || undefined,
        orderStatus: queryForm.orderStatus || undefined,
        page: queryForm.page,
        pageSize: queryForm.pageSize,
      },
    });
    if (res.code === 200 && res.data) {
      orderList.value = res.data.items || [];
      total.value = res.data.total || 0;
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取订单列表失败');
  } finally {
    loading.value = false;
  }
}

function handleSearch() {
  queryForm.page = 1;
  fetchOrders();
}

function handleReset() {
  queryForm.keyword = '';
  queryForm.tradeType = '';
  queryForm.orderStatus = '';
  queryForm.page = 1;
  fetchOrders();
}

// 格式化时间
function formatDateTime(val: string) {
  if (!val) return '-';
  const d = new Date(val);
  return d.toLocaleString('zh-CN', { hour12: false });
}

// 字典与标签
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
    PAID: 'warning',
    DELIVERING: 'primary',
    COMPLETED: 'success',
    REFUNDING: 'danger',
    REFUNDED: 'info',
    CANCELLED: 'info',
  };
  return map[status] || 'info';
}

function getStatusName(status: string) {
  const map: Record<string, string> = {
    PENDING_PAY: '待支付',
    PAID: '待履约',
    DELIVERING: '配送/发货中',
    COMPLETED: '已完成',
    REFUNDING: '退款审核中',
    REFUNDED: '已退款',
    CANCELLED: '已取消',
  };
  return map[status] || status;
}

// 快捷核销
function openQuickVerifyDialog() {
  quickVerifyCode.value = '';
  quickVerifyVisible.value = true;
}

async function submitQuickVerify() {
  if (!quickVerifyCode.value.trim() || quickVerifyCode.value.trim().length !== 12) {
    ElMessage.warning('请输入正确的12位数字核销码');
    return;
  }
  verifying.value = true;
  try {
    const res: any = await request.post('/trade/verify', {
      verifyCode: quickVerifyCode.value.trim(),
    });
    if (res.code === 200) {
      ElMessage.success('扫码核销成功！订单已完结');
      quickVerifyVisible.value = false;
      fetchOrders();
    }
  } catch (err: any) {
    // 错误在 request 拦截器已提示
  } finally {
    verifying.value = false;
  }
}

// 行内直接核销
async function handleVerify(row: any) {
  try {
    await ElMessageBox.confirm(
      `确认核销订单 ${row.orderNo} 吗？\n核销码: ${row.verifyCode}`,
      '核销确认',
      { type: 'warning' }
    );
    const res: any = await request.post('/trade/verify', {
      verifyCode: row.verifyCode,
    });
    if (res.code === 200) {
      ElMessage.success('核销成功，已自动结算');
      fetchOrders();
    }
  } catch (e) {
    // 取消
  }
}

// 打单发货
function openShipDialog(row: any) {
  activeOrder.value = row;
  shipForm.expressCompany = '顺丰速运';
  shipForm.expressNo = '';
  shipDialogVisible.value = true;
}

async function submitShip() {
  if (!shipForm.expressNo.trim()) {
    ElMessage.warning('请输入物流单号');
    return;
  }
  shipping.value = true;
  try {
    const res: any = await request.post('/trade/orders/ship', {
      orderId: activeOrder.value.id,
      expressCompany: shipForm.expressCompany,
      expressNo: shipForm.expressNo.trim(),
    });
    if (res.code === 200) {
      ElMessage.success('发货成功！订单进入配送中状态');
      shipDialogVisible.value = false;
      fetchOrders();
    }
  } catch (e) {
    // error handled
  } finally {
    shipping.value = false;
  }
}

// 同城外卖调度
function openTakeawayDialog(row: any) {
  activeOrder.value = row;
  takeawayForm.riderName = '';
  takeawayForm.riderPhone = '';
  takeawayForm.estimatedDeliveryMinutes = 30;
  takeawayDialogVisible.value = true;
}

async function submitTakeaway() {
  if (!takeawayForm.riderName.trim() || !takeawayForm.riderPhone.trim()) {
    ElMessage.warning('请完整填写骑手姓名及11位电话');
    return;
  }
  dispatching.value = true;
  try {
    const res: any = await request.post('/trade/orders/dispatch-takeaway', {
      orderId: activeOrder.value.id,
      riderName: takeawayForm.riderName.trim(),
      riderPhone: takeawayForm.riderPhone.trim(),
      estimatedDeliveryMinutes: takeawayForm.estimatedDeliveryMinutes,
    });
    if (res.code === 200) {
      ElMessage.success('骑手派遣成功！');
      takeawayDialogVisible.value = false;
      fetchOrders();
    }
  } catch (e) {
    // error handled
  } finally {
    dispatching.value = false;
  }
}

// 确认收货
async function handleConfirmReceipt(row: any) {
  try {
    await ElMessageBox.confirm(`确认该订单 ${row.orderNo} 已送达并完成收货吗？`, '收货确认', {
      type: 'info',
    });
    const res: any = await request.post(`/trade/orders/${row.id}/confirm-receipt`);
    if (res.code === 200) {
      ElMessage.success('订单已确认收货完成');
      fetchOrders();
    }
  } catch (e) {
    // 取消
  }
}

// 模拟买单
async function handlePayMock(row: any) {
  try {
    const res: any = await request.post(`/trade/orders/${row.id}/pay-mock`);
    if (res.code === 200) {
      ElMessage.success('模拟支付成功，分润流水已生成！');
      fetchOrders();
    }
  } catch (e) {
    // error handled
  }
}

// 抽屉详情
function openDrawer(row: any) {
  activeOrder.value = row;
  drawerVisible.value = true;
}

onMounted(() => {
  fetchOrders();
});
</script>

<style scoped>
.trade-container {
  padding: 16px;
}

.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.title-group h3 {
  margin: 0;
  font-size: 18px;
  color: #303133;
}

.title-group .subtitle {
  margin: 4px 0 0 0;
  font-size: 13px;
  color: #909399;
}

.filter-form {
  background: #f8f9fa;
  padding: 16px 16px 0 16px;
  border-radius: 6px;
  margin-bottom: 16px;
}

.pagination-bar {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}

.verify-code-pill {
  font-family: monospace;
  font-weight: 700;
  color: #409eff;
  background: #ecf5ff;
  padding: 2px 8px;
  border-radius: 4px;
  display: inline-block;
}

.item-row {
  line-height: 1.5;
  margin-bottom: 4px;
}

.item-name {
  font-weight: 500;
  color: #303133;
}

.item-spec {
  color: #909399;
  font-size: 12px;
  margin-left: 4px;
}

.item-qty {
  color: #409eff;
  font-weight: 600;
  margin-left: 6px;
}
</style>
