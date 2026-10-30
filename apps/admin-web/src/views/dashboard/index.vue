<template>
  <div class="dashboard-container">
    <el-row :gutter="20">
      <el-col :span="6">
        <el-card shadow="hover" class="stat-card">
          <div class="stat-label">今日总成交额 (GMV)</div>
          <div class="stat-value">￥{{ overview.gmv.today }}</div>
          <div class="stat-footer">
            累计 GMV ￥{{ overview.gmv.total }} ({{ overview.gmv.totalOrders }} 单)
          </div>
        </el-card>
      </el-col>

      <el-col :span="6">
        <el-card shadow="hover" class="stat-card">
          <div class="stat-label">全网自签单分润 (2%)</div>
          <div class="stat-value">￥{{ overview.commissions.selfSignTotal }}</div>
          <div class="stat-footer">入驻品牌商家 {{ overview.merchants.totalShops }} 家</div>
        </el-card>
      </el-col>

      <el-col :span="6">
        <el-card shadow="hover" class="stat-card">
          <div class="stat-label">跨店带单分润 (6%)</div>
          <div class="stat-value">￥{{ overview.commissions.crossChainTotal }}</div>
          <div class="stat-footer">合伙人招商收益 ￥{{ overview.commissions.partnerTotal }}</div>
        </el-card>
      </el-col>

      <el-col :span="6">
        <el-card shadow="hover" class="stat-card">
          <div class="stat-label">终身锁客会员总量</div>
          <div class="stat-value">{{ overview.members.total }} 人</div>
          <div class="stat-footer">
            合伙人 {{ overview.members.partners }} / 钻石 {{ overview.members.diamonds }}
          </div>
        </el-card>
      </el-col>
    </el-row>

    <el-row :gutter="20" style="margin-top: 20px">
      <el-col :span="16">
        <el-card header="全渠道履约模式分布与城市分公司" shadow="hover">
          <div class="fulfillment-grid">
            <div class="metric-box">
              <span class="m-icon">🏬</span>
              <span class="m-title">到店自提 (PICKUP)</span>
              <span class="m-count">{{ overview.fulfillmentDistribution.PICKUP || 0 }} 笔</span>
            </div>
            <div class="metric-box">
              <span class="m-icon">🛵</span>
              <span class="m-title">同城外卖 (TAKEOUT)</span>
              <span class="m-count">{{ overview.fulfillmentDistribution.TAKEOUT || 0 }} 笔</span>
            </div>
            <div class="metric-box">
              <span class="m-icon">📅</span>
              <span class="m-title">到店预约 (APPOINTMENT)</span>
              <span class="m-count">{{ overview.fulfillmentDistribution.APPOINTMENT || 0 }} 笔</span>
            </div>
            <div class="metric-box">
              <span class="m-icon">📦</span>
              <span class="m-title">全国快递 (EXPRESS)</span>
              <span class="m-count">{{ overview.fulfillmentDistribution.EXPRESS || 0 }} 笔</span>
            </div>
          </div>

          <div style="margin-top: 24px">
            <el-alert
              :title="'全国已开通城市分公司 ' + overview.merchants.totalCities + ' 个，25% 城市分润与 25% 直荐人分成由系统原子记账实时清算。'"
              type="success"
              :closable="false"
              show-icon
            />
          </div>
        </el-card>
      </el-col>

      <el-col :span="8">
        <el-card header="移动收银台快速扫码核销" shadow="hover">
          <div class="quick-verify-box">
            <el-input
              v-model="verifyCode"
              placeholder="请输入 12 位核销券码"
              size="large"
              clearable
              maxlength="12"
              style="font-family: monospace; font-size: 18px; letter-spacing: 2px"
              @keyup.enter="handleQuickVerify"
            >
              <template #prefix>
                <el-icon><Tickets /></el-icon>
              </template>
            </el-input>
            <el-button
              type="primary"
              size="large"
              style="width: 100%; margin-top: 16px"
              :loading="verifying"
              @click="handleQuickVerify"
            >
              原子防重确认核销
            </el-button>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { Tickets } from '@element-plus/icons-vue';
import { ElMessage } from 'element-plus';
import request from '../../utils/request';

const verifyCode = ref('');
const verifying = ref(false);

const overview = reactive({
  gmv: {
    total: '0.00',
    today: '0.00',
    totalOrders: 0,
    todayOrders: 0,
  },
  members: {
    total: 0,
    partners: 0,
    diamonds: 0,
    blackDiamonds: 0,
  },
  commissions: {
    selfSignTotal: '0.00',
    crossChainTotal: '0.00',
    partnerTotal: '0.00',
  },
  merchants: {
    totalShops: 0,
    totalCities: 0,
  },
  fulfillmentDistribution: {
    PICKUP: 0,
    APPOINTMENT: 0,
    EXPRESS: 0,
    TAKEOUT: 0,
  },
});

async function fetchOverview() {
  try {
    const res: any = await request.get('/dashboard/overview');
    if (res?.data) {
      Object.assign(overview, res.data);
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取大盘罗盘数据失败');
  }
}

async function handleQuickVerify() {
  if (!verifyCode.value.trim() || verifyCode.value.length !== 12) {
    return ElMessage.warning('请输入标准的 12 位自提/预约核销码');
  }

  verifying.value = true;
  try {
    const res: any = await request.post('/trade/verify', {
      verifyCode: verifyCode.value.trim(),
    });
    if (res.code === 200) {
      ElMessage.success(`核销成功！订单号: ${res.data?.orderNo || ''}`);
      verifyCode.value = '';
      fetchOverview();
    }
  } catch (err: any) {
    ElMessage.error(err.message || '核销失败，券码无效或已被核销');
  } finally {
    verifying.value = false;
  }
}

onMounted(() => {
  fetchOverview();
});
</script>

<style scoped>
.dashboard-container {
  padding: 16px;
}
.stat-card {
  position: relative;
  overflow: hidden;
}
.stat-label {
  font-size: 13px;
  color: #909399;
  margin-bottom: 8px;
}
.stat-value {
  font-size: 26px;
  font-weight: 700;
  color: #303133;
  margin-bottom: 8px;
}
.stat-footer {
  font-size: 12px;
  color: #606266;
}
.fulfillment-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-top: 10px;
}
.metric-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16px;
  background: #f8f9fb;
  border-radius: 8px;
  text-align: center;
}
.m-icon {
  font-size: 28px;
  margin-bottom: 8px;
}
.m-title {
  font-size: 12px;
  color: #606266;
  margin-bottom: 4px;
}
.m-count {
  font-size: 18px;
  font-weight: 700;
  color: #409eff;
}
.quick-verify-box {
  padding: 16px 8px;
}
</style>
