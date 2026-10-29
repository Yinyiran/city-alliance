<template>
  <div class="supply-goods-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>供应链供货选品池 (B2B Supply Catalog)</h3>
          <p class="sub-tip">供货商发布一手源头好货，供同城主理人商家一键选品批发采购与加价售卖</p>
        </div>
        <el-button type="primary" @click="openAddSupplyGoods">发布供货商品</el-button>
      </div>

      <el-table :data="supplyGoodsList" stripe style="width: 100%">
        <el-table-column label="供货品封面" width="90">
          <template #default="{ row }">
            <el-image :src="row.coverImage" style="width: 60px; height: 60px; border-radius: 4px" fit="cover" />
          </template>
        </el-table-column>
        <el-table-column prop="name" label="供货商品名称" min-width="180" />
        <el-table-column prop="supplierName" label="源头供货商" width="160" />
        <el-table-column prop="supplyPrice" label="供货批发底价" width="130">
          <template #default="{ row }">
            <span style="font-weight: 700; color: #409eff">￥{{ row.supplyPrice }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="suggestRetailPrice" label="建议零售价" width="130">
          <template #default="{ row }">￥{{ row.suggestRetailPrice }}</template>
        </el-table-column>
        <el-table-column prop="stock" label="供应链库存" width="110">
          <template #default="{ row }">
            <el-tag :type="row.stock < 50 ? 'danger' : 'success'" size="small">{{ row.stock }} 件</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="供货状态" width="110">
          <template #default="{ row }">
            <el-tag :type="row.status === 'ON_SALE' ? 'success' : 'info'">
              {{ row.status === 'ON_SALE' ? '供应中' : '暂停供货' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="160" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" link size="small" @click="editGoods(row)">编辑规格</el-button>
            <el-button
              :type="row.status === 'ON_SALE' ? 'danger' : 'success'"
              link
              size="small"
              @click="toggleStatus(row)"
            >
              {{ row.status === 'ON_SALE' ? '下架' : '恢复供应' }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <el-dialog v-model="dialogVisible" title="发布B2B供应链源头好货" width="550px">
      <el-form :model="formData" label-width="120px">
        <el-form-item label="商品名称" required>
          <el-input v-model="formData.name" placeholder="例如：埃塞俄比亚耶加雪菲精选生豆 5kg装" />
        </el-form-item>
        <el-form-item label="供货批发价(元)" required>
          <el-input v-model="formData.supplyPrice" placeholder="商家采购成本价" />
        </el-form-item>
        <el-form-item label="建议零售价(元)" required>
          <el-input v-model="formData.suggestRetailPrice" placeholder="终端客户建议标价" />
        </el-form-item>
        <el-form-item label="批量供货库存" required>
          <el-input-number v-model="formData.stock" :min="1" :max="99999" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="saveGoods">确认发布</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ElMessage } from 'element-plus';

const supplyGoodsList = ref([
  {
    id: 'sg-1',
    name: '埃塞俄比亚耶加雪菲精选生豆 5kg装',
    supplierName: '云南宏达咖啡供应链原产基地',
    coverImage: 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=100',
    supplyPrice: '185.00',
    suggestRetailPrice: '298.00',
    stock: 450,
    status: 'ON_SALE',
  },
  {
    id: 'sg-2',
    name: '特级烘焙专用动物稀奶油 1L*12盒/箱',
    supplierName: '新西兰乳业直供仓',
    coverImage: 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=100',
    supplyPrice: '260.00',
    suggestRetailPrice: '380.00',
    stock: 120,
    status: 'ON_SALE',
  },
  {
    id: 'sg-3',
    name: '宠物深层滋润亮毛洗护香波 4L桶装',
    supplierName: '华东宠物美护工厂直营部',
    coverImage: 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=100',
    supplyPrice: '140.00',
    suggestRetailPrice: '268.00',
    stock: 80,
    status: 'ON_SALE',
  },
]);

const dialogVisible = ref(false);
const formData = ref({
  name: '',
  supplyPrice: '',
  suggestRetailPrice: '',
  stock: 100,
});

function openAddSupplyGoods() {
  formData.value = {
    name: '',
    supplyPrice: '',
    suggestRetailPrice: '',
    stock: 100,
  };
  dialogVisible.value = true;
}

function editGoods(row: any) {
  formData.value = { ...row };
  dialogVisible.value = true;
}

function toggleStatus(row: any) {
  row.status = row.status === 'ON_SALE' ? 'OFF_SALE' : 'ON_SALE';
  ElMessage.success(`供货状态已更新为: ${row.status === 'ON_SALE' ? '供应中' : '已暂停'}`);
}

function saveGoods() {
  if (!formData.value.name || !formData.value.supplyPrice) {
    ElMessage.error('请填写完整商品名称与供货价格');
    return;
  }
  supplyGoodsList.value.unshift({
    id: 'sg-' + Date.now(),
    name: formData.value.name,
    supplierName: '平台认证供应商',
    coverImage: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=100',
    supplyPrice: formData.value.supplyPrice,
    suggestRetailPrice: formData.value.suggestRetailPrice,
    stock: formData.value.stock,
    status: 'ON_SALE',
  });
  dialogVisible.value = false;
  ElMessage.success('供货商品发布成功！');
}
</script>

<style scoped>
.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}
.sub-tip {
  color: #909399;
  font-size: 13px;
  margin-top: 4px;
}
</style>
