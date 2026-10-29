<template>
  <div class="goods-container">
    <el-card shadow="never">
      <div class="header-action">
        <h3>商品目录与 SPU/SKU 交叉规格矩阵</h3>
        <el-button type="primary">发布新商品</el-button>
      </div>

      <el-table :data="goodsList" stripe style="width: 100%">
        <el-table-column label="商品封面" width="90">
          <template #default="{ row }">
            <el-image
              :src="row.coverImage"
              style="width: 60px; height: 60px; border-radius: 4px"
              fit="cover"
            />
          </template>
        </el-table-column>
        <el-table-column prop="name" label="商品名称/SPU" min-width="200" />
        <el-table-column prop="tradeType" label="履约类型 (TradeType)" width="150">
          <template #default="{ row }">
            <el-tag :type="getTradeTypeTag(row.tradeType)">{{ getTradeTypeName(row.tradeType) }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="basePrice" label="基准价格" width="110">
          <template #default="{ row }">￥{{ row.basePrice }}</template>
        </el-table-column>
        <el-table-column label="规格矩阵 (SKU 交叉)" min-width="220">
          <template #default="{ row }">
            <div v-for="sku in row.skus" :key="sku.skuCode" class="sku-tag-item">
              <span class="sku-spec">{{ formatSpecs(sku.specValues) }}</span>
              <span class="sku-price">￥{{ sku.price }}</span>
              <span class="sku-stock">(库存: {{ sku.stock }})</span>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.status === 'ON_SALE' ? 'success' : 'info'" size="small">
              {{ row.status === 'ON_SALE' ? '上架中' : '已下架' }}
            </el-tag>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const goodsList = ref([
  {
    name: '精选同城烘焙拿铁套餐',
    coverImage: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=100',
    tradeType: 'PICKUP',
    basePrice: '38.00',
    status: 'ON_SALE',
    skus: [
      { specValues: { 规格: '大杯', 温度: '热饮' }, price: '38.00', stock: 150, skuCode: 'SKU001' },
      { specValues: { 规格: '超大杯', 温度: '冰饮' }, price: '42.00', stock: 88, skuCode: 'SKU002' },
    ],
  },
  {
    name: '宠物专业全身精修 SPA (预约制)',
    coverImage: 'https://images.unsplash.com/photo-1548767797-d8c844163c4c?w=100',
    tradeType: 'APPOINTMENT',
    basePrice: '168.00',
    status: 'ON_SALE',
    skus: [
      { specValues: { 体型: '小型犬(5kg内)' }, price: '168.00', stock: 20, skuCode: 'SKU003' },
      { specValues: { 体型: '中型犬(5-15kg)' }, price: '228.00', stock: 10, skuCode: 'SKU004' },
    ],
  },
  {
    name: '全国包邮特级冻干猫粮 2.5kg',
    coverImage: 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=100',
    tradeType: 'EXPRESS',
    basePrice: '128.00',
    status: 'ON_SALE',
    skus: [
      { specValues: { 口味: '纯鸡肉生骨肉' }, price: '128.00', stock: 500, skuCode: 'SKU005' },
    ],
  },
]);

function getTradeTypeTag(type: string) {
  const map: Record<string, string> = {
    PICKUP: 'success',
    APPOINTMENT: 'warning',
    EXPRESS: 'primary',
    TAKEOUT: 'danger',
    QUICK_PAY: 'info',
  };
  return map[type] || 'info';
}

function getTradeTypeName(type: string) {
  const map: Record<string, string> = {
    PICKUP: '到店自提',
    APPOINTMENT: '到店预约',
    EXPRESS: '全国快递',
    TAKEOUT: '同城外卖',
    QUICK_PAY: '线下买单',
  };
  return map[type] || type;
}

function formatSpecs(specs: Record<string, string>) {
  return Object.entries(specs)
    .map(([k, v]) => `${k}:${v}`)
    .join(' / ');
}
</script>

<style scoped>
.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.sku-tag-item {
  font-size: 12px;
  line-height: 1.8;
}
.sku-spec {
  color: #606266;
  margin-right: 8px;
}
.sku-price {
  color: #f56c6c;
  font-weight: 600;
  margin-right: 6px;
}
.sku-stock {
  color: #909399;
}
</style>
