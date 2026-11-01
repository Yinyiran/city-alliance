<template>
  <div class="city-shops-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>辖区主理人商家与入驻管理 (District Merchants)</h3>
          <p class="sub-tip">监管城市分公司辖区内所有合作品牌、实体商家资质与各店流水贡献</p>
        </div>
        <el-button :icon="Refresh" @click="fetchShops">刷新</el-button>
      </div>

      <el-form :inline="true" :model="queryForm" class="filter-form">
        <el-form-item label="商家名称">
          <el-input
            v-model="queryForm.keyword"
            placeholder="输入品牌或商家名称"
            clearable
            style="width: 220px"
            @keyup.enter="handleSearch"
          />
        </el-form-item>
        <el-form-item label="营业状态">
          <el-select v-model="queryForm.status" placeholder="全部状态" clearable style="width: 140px">
            <el-option label="全部状态" value="" />
            <el-option label="正常营业 (ACTIVE)" value="ACTIVE" />
            <el-option label="暂停合作 (INACTIVE)" value="INACTIVE" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :icon="Search" @click="handleSearch">查询</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <el-table v-loading="loading" :data="shopList" stripe border style="width: 100%">
        <el-table-column label="品牌商家" min-width="200">
          <template #default="{ row }">
            <div style="display: flex; align-items: center; gap: 10px">
              <el-avatar shape="square" :size="40" :src="row.logoUrl" />
              <div>
                <span style="font-weight: 600">{{ row.name }}</span>
                <div style="font-size: 11px; color: #909399">账号: {{ row.ownerUsername }}</div>
              </div>
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="ownerMobile" label="联系电话" width="140">
          <template #default="{ row }">
            <span>{{ row.ownerMobile || '-' }}</span>
          </template>
        </el-table-column>

        <el-table-column prop="storeCount" label="实体门店数" width="110" align="center">
          <template #default="{ row }">
            <el-tag size="small" type="primary">{{ row.storeCount }} 家</el-tag>
          </template>
        </el-table-column>

        <el-table-column prop="totalSales" label="累计成交流水" width="140">
          <template #default="{ row }">
            <span style="font-weight: 700; color: #67c23a">￥{{ row.totalSales }}</span>
          </template>
        </el-table-column>

        <el-table-column prop="status" label="合作状态" width="110" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 'ACTIVE' ? 'success' : 'danger'" size="small">
              {{ row.status === 'ACTIVE' ? '正常营业' : '暂停合作' }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column prop="createdAt" label="签约入驻时间" width="180">
          <template #default="{ row }">
            {{ formatDateTime(row.createdAt) }}
          </template>
        </el-table-column>
      </el-table>

      <div class="pagination-bar">
        <el-pagination
          v-model:current-page="queryForm.page"
          v-model:page-size="queryForm.pageSize"
          :total="total"
          :page-sizes="[10, 20, 50]"
          layout="total, sizes, prev, pager, next"
          @size-change="fetchShops"
          @current-change="fetchShops"
        />
      </div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import { Search, Refresh } from '@element-plus/icons-vue';
import request from '../../utils/request';

const loading = ref(false);
const shopList = ref<any[]>([]);
const total = ref(0);

const queryForm = reactive({
  keyword: '',
  status: '',
  page: 1,
  pageSize: 20,
});

async function fetchShops() {
  loading.value = true;
  try {
    const res: any = await request.get('/city/shops', {
      params: {
        keyword: queryForm.keyword || undefined,
        status: queryForm.status || undefined,
        page: queryForm.page,
        pageSize: queryForm.pageSize,
      },
    });
    if (res?.data) {
      shopList.value = res.data.items || [];
      total.value = res.data.total || 0;
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取辖区商家失败');
  } finally {
    loading.value = false;
  }
}

function handleSearch() {
  queryForm.page = 1;
  fetchShops();
}

function handleReset() {
  queryForm.keyword = '';
  queryForm.status = '';
  queryForm.page = 1;
  fetchShops();
}

function formatDateTime(val: any) {
  if (!val) return '-';
  return new Date(val).toLocaleString('zh-CN', { hour12: false });
}

onMounted(() => {
  fetchShops();
});
</script>

<style scoped>
.city-shops-container {
  padding: 16px;
}
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
.filter-form {
  margin-bottom: 12px;
}
.pagination-bar {
  margin-top: 16px;
  display: flex;
  justify-content: flex-end;
}
</style>
