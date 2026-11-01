<template>
  <div class="city-screen-container">
    <el-card shadow="never" class="city-header-card">
      <div class="header-box">
        <div>
          <h2>🏙️ 城市分公司数据大屏 ({{ cityData.cityInfo.name }})</h2>
          <p style="color: #909399; font-size: 13px; margin-top: 4px">
            辖区模式：享有辖区全部新开通会员 25% 现金分成 + 实体门店消费带单分成
          </p>
        </div>
        <div class="city-switch">
          <el-tag size="large" type="success" effect="dark">
            当前分公司代码：{{ cityData.cityInfo.code }}
          </el-tag>
        </div>
      </div>
    </el-card>

    <el-row :gutter="16" class="stat-cards">
      <el-col :span="6">
        <el-card shadow="never" class="stat-item">
          <div class="stat-title">辖区主理人商家</div>
          <div class="stat-value">{{ cityData.stats.shopCount }} <span class="unit">家</span></div>
          <div class="stat-foot">已建立战略合作品牌</div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="never" class="stat-item">
          <div class="stat-title">辖区实体门店总数</div>
          <div class="stat-value">{{ cityData.stats.storeCount }} <span class="unit">家</span></div>
          <div class="stat-foot">LBS同城活码与台式核销</div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="never" class="stat-item highlight">
          <div class="stat-title">分公司累计总分润 (25%)</div>
          <div class="stat-value" style="color: #409eff">￥{{ cityData.stats.totalProfit }}</div>
          <div class="stat-foot">可提现余额: ￥{{ cityData.stats.withdrawableBalance }}</div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="never" class="stat-item">
          <div class="stat-title">本月辖区交易总额</div>
          <div class="stat-value" style="color: #67c23a">￥{{ cityData.stats.monthGmv }}</div>
          <div class="stat-foot">累计 GMV: ￥{{ cityData.stats.totalGmv }} ({{ cityData.stats.totalOrders }}单)</div>
        </el-card>
      </el-col>
    </el-row>

    <el-row :gutter="16" style="margin-top: 16px">
      <el-col :span="14">
        <el-card shadow="never">
          <div class="card-title">辖区主力主理人商家业绩排行</div>
          <el-table :data="cityData.topMerchants" stripe style="width: 100%; margin-top: 12px">
            <el-table-column type="index" label="排名" width="60" />
            <el-table-column prop="name" label="商家主理人" min-width="160" />
            <el-table-column prop="storeCount" label="实体门店" width="90" />
            <el-table-column prop="monthTurnover" label="本月流水" width="130">
              <template #default="{ row }">￥{{ row.monthTurnover }}</template>
            </el-table-column>
            <el-table-column label="城市 25% 收益" width="140">
              <template #default="{ row }">
                <span style="font-weight: 700; color: #409eff">￥{{ (Number(row.monthTurnover) * 0.02 * 0.25).toFixed(2) }}</span>
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </el-col>

      <el-col :span="10">
        <el-card shadow="never">
          <div class="card-title">城市分公司特权与分润细则</div>
          <div class="rule-box">
            <div class="rule-item">
              <span class="badge">1</span>
              <div>
                <strong>会员升级 25% 招商分润</strong>
                <p>辖区内顾客开通钻石/黑钻或推荐合伙人，城市分公司享有 25% 现金分成。</p>
              </div>
            </div>
            <div class="rule-item">
              <span class="badge">2</span>
              <div>
                <strong>同城消费分润反哺</strong>
                <p>辖区门店产生的每一笔自提、外卖与到店核销，享有 2%~6% 手续费中的分公司比例。</p>
              </div>
            </div>
            <div class="rule-item">
              <span class="badge">3</span>
              <div>
                <strong>7天冷静期后自动解冻</strong>
                <p>由 BullMQ 自动化结算引擎驱动，订单完成 7 天后资金自动入账分公司可提现余额。</p>
              </div>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup lang="ts">
import { reactive, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import request from '../../utils/request';

const cityData = reactive({
  cityInfo: {
    id: '',
    name: '城市分公司运营中心',
    code: 'CITY_CENTER',
  },
  stats: {
    shopCount: 0,
    storeCount: 0,
    totalGmv: '0.00',
    monthGmv: '0.00',
    totalOrders: 0,
    totalProfit: '0.00',
    settledProfit: '0.00',
    frozenProfit: '0.00',
    withdrawableBalance: '0.00',
  },
  topMerchants: [] as any[],
});

async function fetchCityOverview() {
  try {
    const res: any = await request.get('/city/overview');
    if (res?.data) {
      Object.assign(cityData, res.data);
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取城市大屏指标失败');
  }
}

onMounted(() => {
  fetchCityOverview();
});
</script>

<style scoped>
.city-header-card {
  margin-bottom: 16px;
}
.header-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.stat-cards .stat-item {
  padding: 10px;
}
.stat-title {
  font-size: 13px;
  color: #909399;
}
.stat-value {
  font-size: 26px;
  font-weight: 700;
  margin: 10px 0;
}
.stat-value .unit {
  font-size: 14px;
  font-weight: normal;
  color: #909399;
}
.stat-foot {
  font-size: 12px;
  color: #67c23a;
}
.card-title {
  font-weight: 700;
  font-size: 16px;
}
.rule-box {
  margin-top: 16px;
}
.rule-item {
  display: flex;
  gap: 12px;
  margin-bottom: 16px;
}
.rule-item .badge {
  width: 24px;
  height: 24px;
  background: #409eff;
  color: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  flex-shrink: 0;
}
.rule-item p {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}
</style>
