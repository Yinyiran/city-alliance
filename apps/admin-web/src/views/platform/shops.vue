<template>
  <div class="platform-shops-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>全平台主理人商家入驻与资质审核 (Merchant Onboarding & Audit)</h3>
          <p class="sub-tip">监管全网品牌主理人签约入驻、营业执照实名资质、保证金与关联城市分公司</p>
        </div>
      </div>

      <el-tabs v-model="activeTab">
        <el-tab-pane label="全部商家 (42)" name="ALL" />
        <el-tab-pane label="待平台初审 (3)" name="PENDING" />
        <el-tab-pane label="正常经营中 (36)" name="ACTIVE" />
        <el-tab-pane label="已暂停/驳回 (3)" name="DISABLED" />
      </el-tabs>

      <el-table :data="filteredShops" stripe style="width: 100%">
        <el-table-column label="主理人/品牌" min-width="180">
          <template #default="{ row }">
            <div style="font-weight: 700">{{ row.name }}</div>
            <div style="font-size: 12px; color: #909399">编码: {{ row.code }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="cityName" label="归属城市分公司" width="140">
          <template #default="{ row }">
            <el-tag type="info">🏙️ {{ row.cityName }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="ownerName" label="主理人姓名/电话" min-width="160">
          <template #default="{ row }">
            <div>{{ row.ownerName }}</div>
            <div style="font-size: 12px; color: #909399">{{ row.phone }}</div>
          </template>
        </el-table-column>
        <el-table-column label="资质凭证" width="120">
          <template #default="{ row }">
            <el-tag :type="row.certStatus === 'VERIFIED' ? 'success' : 'warning'" size="small">
              {{ row.certStatus === 'VERIFIED' ? '营业执照已核' : '待核验' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="审核状态" width="130">
          <template #default="{ row }">
            <el-tag :type="getStatusTag(row.status)">
              {{ getStatusName(row.status) }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="createdAt" label="申请入驻时间" width="160" />
        <el-table-column label="管理动作" width="180" fixed="right">
          <template #default="{ row }">
            <el-button
              v-if="row.status === 'PENDING'"
              type="success"
              link
              size="small"
              @click="approveShop(row)"
            >
              审核通过
            </el-button>
            <el-button
              v-if="row.status === 'PENDING'"
              type="danger"
              link
              size="small"
              @click="rejectShop(row)"
            >
              驳回
            </el-button>
            <el-button
              v-if="row.status === 'ACTIVE'"
              type="danger"
              link
              size="small"
              @click="toggleShop(row)"
            >
              暂停经营
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { ElMessage } from 'element-plus';

const activeTab = ref('ALL');

const shopList = ref([
  {
    id: 's-1',
    name: '拾光烘焙研磨工坊',
    code: 'SH0001',
    cityName: '杭州市',
    ownerName: '周逸凡',
    phone: '13611223344',
    certStatus: 'VERIFIED',
    status: 'ACTIVE',
    createdAt: '2026-10-25 14:20',
  },
  {
    id: 's-2',
    name: '萌宠星球旗舰体验中心',
    code: 'SH0002',
    cityName: '杭州市',
    ownerName: '徐敏',
    phone: '13855667788',
    certStatus: 'VERIFIED',
    status: 'ACTIVE',
    createdAt: '2026-10-26 09:30',
  },
  {
    id: 's-3',
    name: '木槿造型美学沙龙',
    code: 'SH0003',
    cityName: '宁波市',
    ownerName: '孙立强',
    phone: '13977889900',
    certStatus: 'PENDING',
    status: 'PENDING',
    createdAt: '2026-10-29 18:40',
  },
]);

const filteredShops = computed(() => {
  if (activeTab.value === 'ALL') return shopList.value;
  return shopList.value.filter((s) => s.status === activeTab.value);
});

function getStatusTag(status: string) {
  const map: Record<string, string> = {
    ACTIVE: 'success',
    PENDING: 'warning',
    DISABLED: 'danger',
  };
  return map[status] || 'info';
}

function getStatusName(status: string) {
  const map: Record<string, string> = {
    ACTIVE: '正常营业',
    PENDING: '待审核',
    DISABLED: '已暂停/驳回',
  };
  return map[status] || status;
}

function approveShop(row: any) {
  row.status = 'ACTIVE';
  row.certStatus = 'VERIFIED';
  ElMessage.success(`商家【${row.name}】已审核通过并准予入驻！`);
}

function rejectShop(row: any) {
  row.status = 'DISABLED';
  ElMessage.warning(`商家【${row.name}】入驻申请已驳回`);
}

function toggleShop(row: any) {
  row.status = 'DISABLED';
  ElMessage.warning(`商家【${row.name}】已被平台暂停营业`);
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
