<template>
  <div class="stores-container">
    <el-card shadow="never">
      <!-- 头部操作区 -->
      <div class="header-action">
        <div class="title-group">
          <h3>同城品牌与连锁门店矩阵 (LBS & Omnichannel Stores)</h3>
          <p class="subtitle">
            支持单品牌主理人下属多实体连锁分店统一挂载、LBS地理坐标高精检索与云小票机自动出单
          </p>
        </div>
        <div class="btn-group">
          <el-button type="primary" :icon="Plus" @click="openCreateDialog">新增连锁分店</el-button>
          <el-button :icon="Refresh" @click="fetchStores">刷新列表</el-button>
        </div>
      </div>

      <!-- 搜索与筛选表单 -->
      <el-form :inline="true" :model="filters" class="filter-form">
        <el-form-item label="关键字">
          <el-input
            v-model="filters.keyword"
            placeholder="门店名称/地址/联系电话/商户名"
            clearable
            style="width: 250px"
            @keyup.enter="handleSearch"
          />
        </el-form-item>

        <el-form-item label="营业状态">
          <el-select v-model="filters.status" placeholder="全部状态" clearable style="width: 140px">
            <el-option label="全部状态" value="" />
            <el-option label="营业中" value="OPEN" />
            <el-option label="打烊中" value="CLOSED" />
          </el-select>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" :icon="Search" @click="handleSearch">查询</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <!-- 门店表格 -->
      <el-table v-loading="loading" :data="storeList" stripe border style="width: 100%">
        <el-table-column label="品牌LOGO" width="90" align="center">
          <template #default="{ row }">
            <el-avatar
              shape="square"
              :size="44"
              :src="row.brandLogo || 'https://cube.elemecdn.com/e/fd/0fc7d20532fdaf769a25683617711png.png'"
            />
          </template>
        </el-table-column>

        <el-table-column label="所属品牌 / 商户" min-width="160">
          <template #default="{ row }">
            <div style="font-weight: 600; color: #303133">{{ row.shopName || '平台自营' }}</div>
            <div v-if="row.salesMultiplier" style="font-size: 12px; color: #67c23a; margin-top: 2px">
              曝光系数: 1 : {{ row.salesMultiplier }}
            </div>
          </template>
        </el-table-column>

        <el-table-column label="连锁门店名称 & 详细地址" min-width="240">
          <template #default="{ row }">
            <div style="font-weight: 600; color: #409eff; font-size: 14px">{{ row.storeName }}</div>
            <div style="font-size: 12px; color: #606266; margin-top: 4px">
              📍 {{ row.address }}
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="phone" label="联系电话" width="140">
          <template #default="{ row }">
            <span style="font-family: monospace">{{ row.phone }}</span>
          </template>
        </el-table-column>

        <el-table-column label="空间坐标 (LBS)" width="160">
          <template #default="{ row }">
            <div style="font-size: 11px; font-family: monospace; color: #909399">
              经度: {{ row.longitude }}<br />
              纬度: {{ row.latitude }}
            </div>
          </template>
        </el-table-column>

        <el-table-column label="云小票出单机" width="140" align="center">
          <template #default="{ row }">
            <div v-if="row.cloudPrinterSn">
              <el-tag type="success" size="small">已绑定设备</el-tag>
              <div style="font-size: 11px; color: #909399; margin-top: 2px; font-family: monospace">
                {{ row.cloudPrinterType || '易联云' }}
              </div>
            </div>
            <el-tag v-else type="info" size="small">未接入打印机</el-tag>
          </template>
        </el-table-column>

        <el-table-column prop="status" label="营业状态" width="110" align="center">
          <template #default="{ row }">
            <el-switch
              v-model="row.status"
              active-value="OPEN"
              inactive-value="CLOSED"
              active-text="营业"
              inactive-text="打烊"
              inline-prompt
              @change="(val: any) => handleToggleStatus(row, val)"
            />
          </template>
        </el-table-column>

        <el-table-column label="操作" width="120" fixed="right" align="center">
          <template #default="{ row }">
            <el-button type="primary" size="small" link @click="openEditDialog(row)">编辑</el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页栏 -->
      <div class="pagination-bar">
        <el-pagination
          v-model:current-page="filters.page"
          v-model:page-size="filters.pageSize"
          :total="total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="fetchStores"
          @current-change="fetchStores"
        />
      </div>
    </el-card>

    <!-- 弹窗：新增/编辑门店 -->
    <el-dialog v-model="dialogVisible" :title="isEdit ? '编辑连锁门店' : '新增连锁分店'" width="560px">
      <el-form :model="storeForm" label-width="110px">
        <el-form-item label="门店名称" required>
          <el-input v-model="storeForm.name" placeholder="如：城盟优选（万达广场旗舰店）" clearable />
        </el-form-item>
        <el-form-item label="联系电话" required>
          <el-input v-model="storeForm.phone" placeholder="如：0755-88888888 或手机号" clearable />
        </el-form-item>
        <el-form-item label="门店详细地址" required>
          <el-input v-model="storeForm.address" placeholder="如：广东省深圳市南山区万达广场一楼 A01" clearable />
        </el-form-item>
        <el-row :gutter="12">
          <el-col :span="12">
            <el-form-item label="经度 (Lng)" required>
              <el-input v-model="storeForm.longitude" placeholder="如：113.9487390" clearable />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="纬度 (Lat)" required>
              <el-input v-model="storeForm.latitude" placeholder="如：22.5430990" clearable />
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="云小票机类型">
          <el-select v-model="storeForm.cloudPrinterType" placeholder="选择出单机品牌" style="width: 100%" clearable>
            <el-option label="易联云 (YILIANYUN)" value="YILIANYUN" />
            <el-option label="飞鹅小票机 (FEIE)" value="FEIE" />
          </el-select>
        </el-form-item>
        <el-form-item label="云打印机 SN">
          <el-input v-model="storeForm.cloudPrinterSn" placeholder="输入打印机机身 SN 识别码" clearable />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="saving" @click="submitStore">确认保存</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import { Search, Refresh, Plus } from '@element-plus/icons-vue';
import request from '../../utils/request';

const loading = ref(false);
const storeList = ref<any[]>([]);
const total = ref(0);

const filters = reactive({
  keyword: '',
  status: '',
  page: 1,
  pageSize: 20,
});

// 弹窗表单
const dialogVisible = ref(false);
const isEdit = ref(false);
const saving = ref(false);
const editingStoreId = ref('');
const storeForm = reactive({
  shopId: '',
  name: '',
  phone: '',
  address: '',
  latitude: '22.5430990',
  longitude: '113.9487390',
  cloudPrinterType: '',
  cloudPrinterSn: '',
});

async function fetchStores() {
  loading.value = true;
  try {
    const res: any = await request.get('/stores', {
      params: {
        keyword: filters.keyword || undefined,
        status: filters.status || undefined,
        page: filters.page,
        pageSize: filters.pageSize,
      },
    });
    if (res.code === 200 && res.data) {
      storeList.value = res.data.items || [];
      total.value = res.data.total || 0;
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取门店列表失败');
  } finally {
    loading.value = false;
  }
}

function handleSearch() {
  filters.page = 1;
  fetchStores();
}

function handleReset() {
  filters.keyword = '';
  filters.status = '';
  filters.page = 1;
  fetchStores();
}

async function handleToggleStatus(row: any, val: string) {
  try {
    const res: any = await request.put(`/stores/${row.id}/status`, { status: val });
    if (res.code === 200) {
      ElMessage.success(`门店状态已更新为: ${val === 'OPEN' ? '营业中' : '打烊'}`);
    }
  } catch (e) {
    row.status = val === 'OPEN' ? 'CLOSED' : 'OPEN'; // 回滚
  }
}

function openCreateDialog() {
  isEdit.value = false;
  editingStoreId.value = '';
  storeForm.shopId = storeList.value[0]?.shopId || '';
  storeForm.name = '';
  storeForm.phone = '';
  storeForm.address = '';
  storeForm.latitude = '22.5430990';
  storeForm.longitude = '113.9487390';
  storeForm.cloudPrinterType = '';
  storeForm.cloudPrinterSn = '';
  dialogVisible.value = true;
}

function openEditDialog(row: any) {
  isEdit.value = true;
  editingStoreId.value = row.id;
  storeForm.shopId = row.shopId;
  storeForm.name = row.storeName;
  storeForm.phone = row.phone;
  storeForm.address = row.address;
  storeForm.latitude = String(row.latitude);
  storeForm.longitude = String(row.longitude);
  storeForm.cloudPrinterType = row.cloudPrinterType || '';
  storeForm.cloudPrinterSn = row.cloudPrinterSn || '';
  dialogVisible.value = true;
}

async function submitStore() {
  if (!storeForm.name.trim() || !storeForm.phone.trim() || !storeForm.address.trim()) {
    ElMessage.warning('请完整填写门店名称、电话及地址');
    return;
  }
  saving.value = true;
  try {
    if (isEdit.value) {
      const res: any = await request.put(`/stores/${editingStoreId.value}`, {
        name: storeForm.name.trim(),
        phone: storeForm.phone.trim(),
        address: storeForm.address.trim(),
        latitude: storeForm.latitude.trim(),
        longitude: storeForm.longitude.trim(),
        cloudPrinterType: storeForm.cloudPrinterType || undefined,
        cloudPrinterSn: storeForm.cloudPrinterSn || undefined,
      });
      if (res.code === 200) {
        ElMessage.success('门店信息更新成功');
        dialogVisible.value = false;
        fetchStores();
      }
    } else {
      const res: any = await request.post('/stores', {
        shopId: storeForm.shopId,
        name: storeForm.name.trim(),
        phone: storeForm.phone.trim(),
        address: storeForm.address.trim(),
        latitude: storeForm.latitude.trim(),
        longitude: storeForm.longitude.trim(),
        cloudPrinterType: storeForm.cloudPrinterType || undefined,
        cloudPrinterSn: storeForm.cloudPrinterSn || undefined,
      });
      if (res.code === 200) {
        ElMessage.success('新增门店成功');
        dialogVisible.value = false;
        fetchStores();
      }
    }
  } catch (err: any) {
    // handled
  } finally {
    saving.value = false;
  }
}

onMounted(() => {
  fetchStores();
});
</script>

<style scoped>
.stores-container {
  padding: 16px;
}

.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.title-group h3 {
  margin: 0;
  font-size: 18px;
  color: #303133;
}

.title-group .subtitle {
  margin: 4px 0 0 0;
  font-size: 13px;
  color: #909399;
}

.filter-form {
  background: #f8f9fa;
  padding: 16px 16px 0 16px;
  border-radius: 6px;
  margin-bottom: 16px;
}

.pagination-bar {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}
</style>
