<template>
  <div class="platform-cities-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>城市分公司与代理商矩阵 (City Branch Management)</h3>
          <p class="sub-tip">管理各城市合伙人分公司授权、辖区绑定、25%开通分成费率与城市专享权限</p>
        </div>
        <div class="btn-group">
          <el-button type="primary" :icon="Plus" @click="openAddDialog">授权新城市分公司</el-button>
          <el-button :icon="Refresh" @click="fetchCities">刷新</el-button>
        </div>
      </div>

      <!-- 搜索筛选 -->
      <el-form :inline="true" :model="filters" class="filter-form">
        <el-form-item label="城市名称">
          <el-input v-model="filters.keyword" placeholder="搜索城市名称" clearable @keyup.enter="fetchCities" />
        </el-form-item>
        <el-form-item label="运营状态">
          <el-select v-model="filters.status" placeholder="全部状态" clearable style="width: 140px">
            <el-option label="全部状态" value="" />
            <el-option label="正常运营" value="ACTIVE" />
            <el-option label="已冻结" value="DISABLED" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :icon="Search" @click="fetchCities">查询</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <el-table v-loading="loading" :data="cityList" stripe border style="width: 100%">
        <el-table-column prop="cityName" label="授权城市" min-width="140">
          <template #default="{ row }">
            <span style="font-weight: 700; font-size: 15px">🏙️ {{ row.cityName }}</span>
            <div style="font-size: 11px; color: #909399; font-family: monospace">{{ row.code }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="companyName" label="分公司企业名称" min-width="220" />
        <el-table-column prop="agentLeader" label="负责人/联系电话" width="160">
          <template #default="{ row }">
            <div style="font-weight: 500">{{ row.agentLeader }}</div>
            <div style="font-size: 12px; color: #909399">{{ row.phone }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="merchantCount" label="辖区主理人数量" width="140" align="center">
          <template #default="{ row }">
            <el-tag type="info" effect="light">{{ row.merchantCount }} 家商家</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="totalEarned" label="分公司累计分润(25%)" width="180" align="right">
          <template #default="{ row }">
            <span style="font-weight: 700; color: #67c23a">￥{{ row.totalEarned }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="运营状态" width="110" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 'ACTIVE' ? 'success' : 'danger'">
              {{ row.status === 'ACTIVE' ? '正常运营' : '已冻结' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="160" fixed="right" align="center">
          <template #default="{ row }">
            <el-button type="primary" link size="small" @click="editCity(row)">编辑授权</el-button>
            <el-button
              :type="row.status === 'ACTIVE' ? 'danger' : 'success'"
              link
              size="small"
              @click="toggleCityStatus(row)"
            >
              {{ row.status === 'ACTIVE' ? '冻结' : '解冻' }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <el-dialog v-model="dialogVisible" :title="isEdit ? '编辑城市分公司' : '城市分公司授权建档'" width="550px">
      <el-form :model="formData" label-width="130px">
        <el-form-item label="城市名称" required>
          <el-input v-model="formData.cityName" placeholder="例如：杭州市 / 宁波市" />
        </el-form-item>
        <el-form-item label="企业全称" required>
          <el-input v-model="formData.companyName" placeholder="例如：城盟优选杭州城市运营中心" />
        </el-form-item>
        <el-form-item label="负责人姓名" required>
          <el-input v-model="formData.agentLeader" placeholder="法定代表人 / 运营负责人" />
        </el-form-item>
        <el-form-item label="联系电话" required>
          <el-input v-model="formData.phone" placeholder="手机号码" />
        </el-form-item>
        <el-form-item label="分润比例(固定)">
          <el-input value="25.00% (系统底层锁死基准)" disabled />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="saving" @click="saveCity">保存授权</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Search, Refresh, Plus } from '@element-plus/icons-vue';
import request from '../../utils/request';

const loading = ref(false);
const cityList = ref<any[]>([]);

const filters = reactive({
  keyword: '',
  status: '',
});

const dialogVisible = ref(false);
const isEdit = ref(false);
const saving = ref(false);
const currentCityId = ref('');
const formData = reactive({
  cityName: '',
  companyName: '',
  agentLeader: '',
  phone: '',
});

async function fetchCities() {
  loading.value = true;
  try {
    const res: any = await request.get('/platform/cities', {
      params: {
        keyword: filters.keyword || undefined,
        status: filters.status || undefined,
      },
    });
    if (res.code === 200 && res.data) {
      cityList.value = res.data;
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取城市列表失败');
  } finally {
    loading.value = false;
  }
}

function handleReset() {
  filters.keyword = '';
  filters.status = '';
  fetchCities();
}

function openAddDialog() {
  isEdit.value = false;
  currentCityId.value = '';
  formData.cityName = '';
  formData.companyName = '';
  formData.agentLeader = '';
  formData.phone = '';
  dialogVisible.value = true;
}

function editCity(row: any) {
  isEdit.value = true;
  currentCityId.value = row.id;
  formData.cityName = row.cityName;
  formData.companyName = row.companyName;
  formData.agentLeader = row.agentLeader;
  formData.phone = row.phone;
  dialogVisible.value = true;
}

async function toggleCityStatus(row: any) {
  const targetStatus = row.status === 'ACTIVE' ? 'DISABLED' : 'ACTIVE';
  const actionText = targetStatus === 'ACTIVE' ? '解冻恢复' : '冻结停用';
  try {
    await ElMessageBox.confirm(`确认${actionText}城市分公司【${row.cityName}】吗？`, '状态变更', {
      type: 'warning',
    });
    const res: any = await request.put(`/platform/cities/${row.id}/status`, { status: targetStatus });
    if (res.code === 200) {
      ElMessage.success(`分公司已${actionText}！`);
      fetchCities();
    }
  } catch (e) {
    // cancel
  }
}

async function saveCity() {
  if (!formData.cityName.trim() || !formData.companyName.trim()) {
    ElMessage.error('请填写完整城市与企业名称');
    return;
  }
  saving.value = true;
  try {
    if (isEdit.value) {
      const res: any = await request.put(`/platform/cities/${currentCityId.value}`, {
        cityName: formData.cityName.trim(),
        companyName: formData.companyName.trim(),
        agentLeader: formData.agentLeader.trim(),
        phone: formData.phone.trim(),
      });
      if (res.code === 200) {
        ElMessage.success('城市分公司授权已更新');
        dialogVisible.value = false;
        fetchCities();
      }
    } else {
      const res: any = await request.post('/platform/cities', {
        cityName: formData.cityName.trim(),
        companyName: formData.companyName.trim(),
        agentLeader: formData.agentLeader.trim(),
        phone: formData.phone.trim(),
      });
      if (res.code === 200) {
        ElMessage.success('城市分公司授权建档成功！');
        dialogVisible.value = false;
        fetchCities();
      }
    }
  } catch (err: any) {
    // handled
  } finally {
    saving.value = false;
  }
}

onMounted(() => {
  fetchCities();
});
</script>

<style scoped>
.platform-cities-container {
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
  background: #f8f9fa;
  padding: 16px 16px 0 16px;
  border-radius: 6px;
  margin-bottom: 16px;
}
</style>
