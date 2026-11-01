<template>
  <div class="supply-goods-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>供应链供货选品池 (B2B Supply Catalog)</h3>
          <p class="sub-tip">供货商发布一手源头好货，供同城主理人商家一键选品批发采购与加价售卖</p>
        </div>
        <div class="action-btns">
          <el-input
            v-model="keyword"
            placeholder="搜索商品名称..."
            clearable
            style="width: 220px; margin-right: 12px"
            @keyup.enter="fetchGoods"
            @clear="fetchGoods"
          />
          <el-button type="primary" @click="openAddSupplyGoods">发布供货商品</el-button>
          <el-button @click="fetchGoods">刷新</el-button>
        </div>
      </div>

      <el-table v-loading="loading" :data="supplyGoodsList" stripe style="width: 100%">
        <el-table-column label="供货品封面" width="90">
          <template #default="{ row }">
            <el-image
              :src="row.coverImage"
              style="width: 60px; height: 60px; border-radius: 4px"
              fit="cover"
            />
          </template>
        </el-table-column>
        <el-table-column prop="name" label="供货商品名称" min-width="180">
          <template #default="{ row }">
            <div style="font-weight: 600">{{ row.name }}</div>
            <div style="font-size: 12px; color: #909399">{{ row.subTitle }}</div>
          </template>
        </el-table-column>
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
            <el-tag :type="row.stock < 50 ? 'danger' : 'success'" size="small">
              {{ row.stock }} 件
            </el-tag>
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

      <div class="pagination-container">
        <el-pagination
          v-model:current-page="page"
          v-model:page-size="pageSize"
          :total="total"
          :page-sizes="[10, 20, 50]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="fetchGoods"
          @current-change="fetchGoods"
        />
      </div>
    </el-card>

    <!-- 发布供货弹窗 -->
    <el-dialog v-model="dialogVisible" title="发布B2B供应链源头好货" width="550px">
      <el-form :model="formData" label-width="120px">
        <el-form-item label="商品名称" required>
          <el-input v-model="formData.name" placeholder="例如：埃塞俄比亚耶加雪菲精选生豆 5kg装" />
        </el-form-item>
        <el-form-item label="封面图片URL">
          <el-input v-model="formData.coverUrl" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="供货批发价(元)" required>
          <el-input v-model="formData.supplyPrice" placeholder="商家采购成本价" />
        </el-form-item>
        <el-form-item label="建议零售价(元)" required>
          <el-input v-model="formData.suggestRetailPrice" placeholder="终端客户建议标价" />
        </el-form-item>
        <el-form-item label="批量供货库存" required>
          <el-input-number v-model="formData.stock" :min="1" :max="99999" style="width: 100%" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="saving" @click="saveGoods">确认发布</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import request from '@/utils/request';

const loading = ref(false);
const supplyGoodsList = ref<any[]>([]);
const total = ref(0);
const page = ref(1);
const pageSize = ref(20);
const keyword = ref('');

const dialogVisible = ref(false);
const saving = ref(false);
const formData = ref({
  name: '',
  coverUrl: '',
  supplyPrice: '',
  suggestRetailPrice: '',
  stock: 500,
});

async function fetchGoods() {
  loading.value = true;
  try {
    const res: any = await request.get('/supply/goods', {
      params: {
        page: page.value,
        pageSize: pageSize.value,
        keyword: keyword.value || undefined,
      },
    });
    if (res && res.data) {
      supplyGoodsList.value = res.data.items || [];
      total.value = res.data.total || 0;
    }
  } catch (err: any) {
    console.error('Failed to fetch supply goods:', err);
  } finally {
    loading.value = false;
  }
}

function openAddSupplyGoods() {
  formData.value = {
    name: '',
    coverUrl: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=300',
    supplyPrice: '',
    suggestRetailPrice: '',
    stock: 500,
  };
  dialogVisible.value = true;
}

async function saveGoods() {
  if (!formData.value.name || !formData.value.supplyPrice || !formData.value.suggestRetailPrice) {
    ElMessage.error('请填写完整的商品名称、供货价与零售价');
    return;
  }
  saving.value = true;
  try {
    await request.post('/supply/goods', {
      name: formData.value.name,
      coverUrl: formData.value.coverUrl,
      supplyPrice: Number(formData.value.supplyPrice),
      suggestRetailPrice: Number(formData.value.suggestRetailPrice),
      stock: formData.value.stock,
    });
    ElMessage.success('商品已成功发布至平台供应链选品池！');
    dialogVisible.value = false;
    await fetchGoods();
  } catch (err: any) {
    ElMessage.error(err?.response?.data?.message || '发布失败');
  } finally {
    saving.value = false;
  }
}

async function toggleStatus(row: any) {
  const newStatus = row.status === 'ON_SALE' ? 'OFF_SALE' : 'ON_SALE';
  try {
    await request.put(`/supply/goods/${row.id}/status`, {
      status: newStatus,
    });
    row.status = newStatus;
    ElMessage.success(`商品状态已切换为: ${newStatus === 'ON_SALE' ? '供应中' : '暂停供货'}`);
  } catch (err: any) {
    ElMessage.error(err?.response?.data?.message || '切换状态失败');
  }
}

onMounted(() => {
  fetchGoods();
});
</script>

<style scoped>
.supply-goods-container {
  padding: 16px;
}
.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 16px;
}
.action-btns {
  display: flex;
  align-items: center;
}
.sub-tip {
  color: #909399;
  font-size: 13px;
  margin-top: 4px;
}
.pagination-container {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}
</style>
