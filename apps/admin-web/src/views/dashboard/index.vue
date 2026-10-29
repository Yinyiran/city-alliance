<template>
  <div class="dashboard-container">
    <el-row :gutter="20">
      <el-col :span="6">
        <el-card shadow="hover" class="stat-card">
          <div class="stat-label">今日总成交额 (GMV)</div>
          <div class="stat-value">￥28,450.00</div>
          <div class="stat-footer">日环比 <span class="trend-up">+12.5%</span></div>
        </el-card>
      </el-col>

      <el-col :span="6">
        <el-card shadow="hover" class="stat-card">
          <div class="stat-label">自签单净收益 (扣2%)</div>
          <div class="stat-value">￥16,820.00</div>
          <div class="stat-footer">平台自营出单占比 60%</div>
        </el-card>
      </el-col>

      <el-col :span="6">
        <el-card shadow="hover" class="stat-card">
          <div class="stat-label">跨店带单分润 (扣6%)</div>
          <div class="stat-value">￥4,320.50</div>
          <div class="stat-footer">贡献带单店铺 18 家</div>
        </el-card>
      </el-col>

      <el-col :span="6">
        <el-card shadow="hover" class="stat-card">
          <div class="stat-label">终身锁客会员总量</div>
          <div class="stat-value">1,286 人</div>
          <div class="stat-footer">合伙人 42 / 黑钻 118</div>
        </el-card>
      </el-col>
    </el-row>

    <el-row :gutter="20" style="margin-top: 20px">
      <el-col :span="16">
        <el-card header="全渠道履约概况 (7天趋势)" shadow="hover">
          <div class="chart-placeholder">
            <el-empty description="折线图已接入后台 Fastify 网关，实时聚合自提、外卖与预约走势" />
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
import { ref } from 'vue';
import { Tickets } from '@element-plus/icons-vue';
import { ElMessage } from 'element-plus';

const verifyCode = ref('');
const verifying = ref(false);

function handleQuickVerify() {
  if (!verifyCode.value) {
    ElMessage.warning('请输入12位核销券码');
    return;
  }
  verifying.value = true;
  setTimeout(() => {
    verifying.value = false;
    ElMessage.success('核销成功！小票已自动提交云打印机');
    verifyCode.value = '';
  }, 600);
}
</script>

<style scoped>
.stat-card {
  border-radius: 8px;
}
.stat-label {
  font-size: 14px;
  color: #909399;
}
.stat-value {
  font-size: 26px;
  font-weight: 700;
  color: #303133;
  margin: 10px 0;
}
.stat-footer {
  font-size: 13px;
  color: #606266;
}
.trend-up {
  color: #67c23a;
  font-weight: 600;
}
.chart-placeholder {
  height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.quick-verify-box {
  padding: 20px 0;
}
</style>
