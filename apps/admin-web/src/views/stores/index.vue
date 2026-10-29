<template>
  <div class="stores-container">
    <el-card shadow="never">
      <div class="header-action">
        <h3>同城品牌与连锁门店配置</h3>
        <el-button type="primary">新增连锁分店</el-button>
      </div>

      <el-table :data="storeList" stripe style="width: 100%">
        <el-table-column label="品牌LOGO" width="100">
          <template #default="{ row }">
            <el-avatar shape="square" :size="50" :src="row.brandLogo" />
          </template>
        </el-table-column>
        <el-table-column prop="shopName" label="品牌主理人商户" min-width="160">
          <template #default="{ row }">
            <div style="font-weight: 600">{{ row.shopName }}</div>
            <div style="font-size: 12px; color: #909399">品牌下属门店数: {{ row.branchCount }} 家</div>
          </template>
        </el-table-column>
        <el-table-column prop="storeName" label="当前实体门店" min-width="180">
          <template #default="{ row }">
            <div>{{ row.storeName }}</div>
            <div style="font-size: 12px; color: #909399">{{ row.address }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="salesMultiplier" label="曝光率系数" width="130">
          <template #default="{ row }">
            <el-tag type="success">1 : {{ row.salesMultiplier }} 倍</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="企微私域群活码轮换" width="180">
          <template #default="{ row }">
            <div style="font-size: 13px">当前在用: 第 {{ row.activeQrIndex }} 张</div>
            <el-progress
              :percentage="Math.round((row.currentQrMembers / row.maxQrMembers) * 100)"
              :color="row.currentQrMembers >= row.maxQrMembers ? '#f56c6c' : '#409eff'"
            />
            <div style="font-size: 11px; color: #909399">
              {{ row.currentQrMembers }} / {{ row.maxQrMembers }} 人 (满员自动切码)
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="printerStatus" label="云小票机" width="110">
          <template #default="{ row }">
            <el-tag :type="row.printerStatus === 'ONLINE' ? 'success' : 'danger'" size="small">
              {{ row.printerStatus === 'ONLINE' ? '在线出纸' : '离线' }}
            </el-tag>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const storeList = ref([
  {
    brandLogo: 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=100',
    shopName: '梵优茗宠连锁品牌',
    branchCount: 3,
    storeName: '西湖大道中心旗舰店',
    address: '浙江省杭州市上城区西湖大道88号',
    salesMultiplier: '9.00',
    activeQrIndex: 1,
    currentQrMembers: 142,
    maxQrMembers: 200,
    printerStatus: 'ONLINE',
  },
  {
    brandLogo: 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=100',
    shopName: '梵优茗宠连锁品牌',
    branchCount: 3,
    storeName: '武林广场精品分店',
    address: '浙江省杭州市拱墅区武林广场1号',
    salesMultiplier: '9.00',
    activeQrIndex: 2,
    currentQrMembers: 36,
    maxQrMembers: 200,
    printerStatus: 'ONLINE',
  },
]);
</script>

<style scoped>
.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
</style>
