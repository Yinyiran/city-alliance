<template>
  <div class="city-screen-container">
    <el-card shadow="never" class="city-header-card">
      <div class="header-box">
        <div>
          <h2>🏙️ 城市分公司数据大屏 (杭州市运营中心)</h2>
          <p style="color: #909399; font-size: 13px; margin-top: 4px">
            辖区模式：享有辖区全部新开通会员 25% 现金分成 + 实体门店 2% 流水收益
          </p>
        </div>
        <div class="city-switch">
          <el-tag size="large" type="success" effect="dark">
            当前管理分公司：杭州城盟数智商业管理有限公司
          </el-tag>
        </div>
      </div>
    </el-card>

    <el-row :gutter="16" class="stat-cards">
      <el-col :span="6">
        <el-card shadow="never" class="stat-item">
          <div class="stat-title">辖区主理人商家</div>
          <div class="stat-value">42 <span class="unit">家</span></div>
          <div class="stat-foot">较上月 +8 家</div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="never" class="stat-item">
          <div class="stat-title">辖区实体门店总数</div>
          <div class="stat-value">68 <span class="unit">家</span></div>
          <div class="stat-foot">LBS活码覆盖率 100%</div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="never" class="stat-item highlight">
          <div class="stat-title">分公司累计总分润 (25%)</div>
          <div class="stat-value" style="color: #409eff">￥128,450.00</div>
          <div class="stat-foot">可提现余额: ￥94,120.00</div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="never" class="stat-item">
          <div class="stat-title">本月自签/跨店交易总额</div>
          <div class="stat-value" style="color: #67c23a">￥642,250.00</div>
          <div class="stat-foot">月环比增长 24.5%</div>
        </el-card>
      </el-col>
    </el-row>

    <el-row :gutter="16" style="margin-top: 16px">
      <el-col :span="14">
        <el-card shadow="never">
          <div class="card-title">辖区主力主理人商家业绩排行</div>
          <el-table :data="topMerchants" stripe style="width: 100%; margin-top: 12px">
            <el-table-column type="index" label="排名" width="60" />
            <el-table-column prop="name" label="商家主理人" min-width="160" />
            <el-table-column prop="storeCount" label="实体门店" width="90" />
            <el-table-column prop="monthTurnover" label="月交易额" width="130">
              <template #default="{ row }">￥{{ row.monthTurnover }}</template>
            </el-table-column>
            <el-table-column prop="branchEarned" label="为分公司贡献25%" width="150">
              <template #default="{ row }">
                <span style="font-weight: 700; color: #409eff">￥{{ row.branchEarned }}</span>
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </el-col>

      <el-col :span="10">
        <el-card shadow="never">
          <div class="card-title">分公司收益结构与清算规则</div>
          <div class="rule-box">
            <div class="rule-item">
              <span class="badge">1</span>
              <div>
                <strong>会员开通 25% 锁定分成</strong>
                <p>辖区内顾客通过任意渠道开通钻石/黑钻会员，分公司即时入账 25% 现金分成。</p>
              </div>
            </div>
            <div class="rule-item">
              <span class="badge">2</span>
              <div>
                <strong>实体门店 2% 综合服务费</strong>
                <p>辖区门店产生的每一笔自提、外卖与到店核销，享有 2% 门店流水反哺。</p>
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
import { ref } from 'vue';

const topMerchants = ref([
  { name: '拾光烘焙研磨工坊 (连锁5店)', storeCount: 5, monthTurnover: '142,500.00', branchEarned: '35,625.00' },
  { name: '木槿造型美学沙龙', storeCount: 3, monthTurnover: '98,400.00', branchEarned: '24,600.00' },
  { name: '萌宠星球洗护生活馆', storeCount: 2, monthTurnover: '76,200.00', branchEarned: '19,050.00' },
  { name: '弄堂里地道私房菜', storeCount: 2, monthTurnover: '58,900.00', branchEarned: '14,725.00' },
]);
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
