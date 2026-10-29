<template>
  <div class="ledger-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>资金与积分双记账台账 (Double-Entry Financial Ledger)</h3>
          <p class="sub-tip">严格不可篡改的复式记账法：全流程追踪充值、消费抵扣、分润解冻、退款冲正与提现变动前后快照</p>
        </div>
      </div>

      <el-tabs v-model="activeTab">
        <el-tab-pane label="余额变动总台账 (Balance Ledger)" name="BALANCE">
          <el-table :data="balanceList" stripe style="width: 100%">
            <el-table-column prop="referenceNo" label="关联业务单号" min-width="180">
              <template #default="{ row }">
                <span style="font-family: monospace; font-weight: 600">{{ row.referenceNo }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="memberName" label="会员/主理人" width="130" />
            <el-table-column prop="changeType" label="变动业务类型" width="140">
              <template #default="{ row }">
                <el-tag :type="getChangeTypeTag(row.changeType)">
                  {{ getChangeTypeName(row.changeType) }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="amount" label="变动金额" width="140">
              <template #default="{ row }">
                <span :style="{ fontWeight: '700', color: Number(row.amount) > 0 ? '#67c23a' : '#f56c6c' }">
                  {{ Number(row.amount) > 0 ? '+' + row.amount : row.amount }}
                </span>
              </template>
            </el-table-column>
            <el-table-column label="变动前 ➔ 变动后余额" min-width="200">
              <template #default="{ row }">
                ￥{{ row.balanceBefore }} ➔ <strong style="color: #409eff">￥{{ row.balanceAfter }}</strong>
              </template>
            </el-table-column>
            <el-table-column prop="remark" label="审计备注" min-width="200" />
            <el-table-column prop="createdAt" label="入账时间" width="160" />
          </el-table>
        </el-tab-pane>

        <el-tab-pane label="积分明细台账 (Points Ledger)" name="POINTS">
          <el-table :data="pointsList" stripe style="width: 100%">
            <el-table-column prop="memberName" label="会员昵称" width="140" />
            <el-table-column prop="changeType" label="积分变动来源" width="150">
              <template #default="{ row }">
                <el-tag type="info">{{ row.changeType }}</el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="points" label="变动点数" width="120">
              <template #default="{ row }">
                <span :style="{ fontWeight: 'bold', color: row.points > 0 ? '#67c23a' : '#f56c6c' }">
                  {{ row.points > 0 ? '+' + row.points : row.points }}
                </span>
              </template>
            </el-table-column>
            <el-table-column label="变动前后积分" width="180">
              <template #default="{ row }">
                {{ row.pointsBefore }} ➔ <strong>{{ row.pointsAfter }}</strong>
              </template>
            </el-table-column>
            <el-table-column prop="remark" label="业务备注" min-width="200" />
            <el-table-column prop="createdAt" label="记录时间" width="160" />
          </el-table>
        </el-tab-pane>
      </el-tabs>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const activeTab = ref('BALANCE');

const balanceList = ref([
  {
    referenceNo: 'ORD2026102900018899',
    memberName: '周逸凡 (主理人)',
    changeType: 'COMMISSION_SETTLE',
    amount: '+18.62',
    balanceBefore: '1,240.00',
    balanceAfter: '1,258.62',
    remark: '订单核销完成：主理人获得自签 25% 会员消费分成',
    createdAt: '2026-10-29 15:30:12',
  },
  {
    referenceNo: 'REF2026102900091122',
    memberName: '张建国 (杭州分公司)',
    changeType: 'REFUND_REVERSE',
    amount: '-25.00',
    balanceBefore: '94,145.00',
    balanceAfter: '94,120.00',
    remark: '用户售后全额退款：冲正对冲扣除已发放的 25% 城市分润',
    createdAt: '2026-10-29 16:15:00',
  },
  {
    referenceNo: 'WTH2026102800045566',
    memberName: '李伟强 (宁波分公司)',
    changeType: 'WITHDRAW_FREEZE',
    amount: '-5,000.00',
    balanceBefore: '91,200.00',
    balanceAfter: '86,200.00',
    remark: '申请银行卡打款提现：冻结提现资金并预扣除',
    createdAt: '2026-10-28 11:20:44',
  },
]);

const pointsList = ref([
  {
    memberName: '李雷 (688219)',
    changeType: '到店打卡签到',
    points: 50,
    pointsBefore: 120,
    pointsAfter: 170,
    remark: '到店实体围栏打卡奖励 50 积分',
    createdAt: '2026-10-29 14:10:00',
  },
  {
    memberName: '韩梅梅 (991204)',
    changeType: '看播短视频奖励',
    points: 20,
    pointsBefore: 340,
    pointsAfter: 360,
    remark: '观看同城精品短视频满 30 秒进度奖励',
    createdAt: '2026-10-29 15:45:20',
  },
]);

function getChangeTypeTag(type: string) {
  const map: Record<string, string> = {
    COMMISSION_SETTLE: 'success',
    REFUND_REVERSE: 'danger',
    WITHDRAW_FREEZE: 'warning',
  };
  return map[type] || 'info';
}

function getChangeTypeName(type: string) {
  const map: Record<string, string> = {
    COMMISSION_SETTLE: '分润入账解冻',
    REFUND_REVERSE: '售后退款冲正',
    WITHDRAW_FREEZE: '提现出金冻结',
  };
  return map[type] || type;
}
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
