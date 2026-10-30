<template>
  <div class="platform-shops-container">
    <el-card shadow="never">
      <div class="header-action">
        <div class="title-group">
          <h3>全平台主理人商家入驻与资质审核 (Merchant Onboarding & Audit)</h3>
          <p class="sub-tip">监管全网品牌主理人签约入驻、营业执照实名资质、曝光系数调控与关联城市分公司</p>
        </div>
        <div class="btn-group">
          <el-button :icon="Refresh" @click="fetchShops">刷新数据</el-button>
        </div>
      </div>

      <!-- 状态选项卡 -->
      <el-tabs v-model="activeTab" @tab-change="handleTabChange">
        <el-tab-pane :label="`全部商家 (${tabCounts.ALL})`" name="ALL" />
        <el-tab-pane :label="`待平台初审 (${tabCounts.PENDING})`" name="PENDING" />
        <el-tab-pane :label="`正常经营中 (${tabCounts.ACTIVE})`" name="ACTIVE" />
        <el-tab-pane :label="`已暂停/驳回 (${tabCounts.DISABLED})`" name="DISABLED" />
      </el-tabs>

      <!-- 搜索表单 -->
      <el-form :inline="true" :model="queryForm" class="filter-form">
        <el-form-item label="关键字">
          <el-input
            v-model="queryForm.keyword"
            placeholder="搜索商家品牌名/主理人账号/手机"
            clearable
            style="width: 240px"
            @keyup.enter="handleSearch"
          />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :icon="Search" @click="handleSearch">查询</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <!-- 商家表格 -->
      <el-table v-loading="loading" :data="shopList" stripe border style="width: 100%">
        <el-table-column label="主理人/品牌" min-width="190">
          <template #default="{ row }">
            <div style="font-weight: 700; color: #303133; font-size: 14px">{{ row.name }}</div>
            <div style="font-size: 11px; color: #909399; font-family: monospace; margin-top: 2px">
              {{ row.code }}
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="cityName" label="归属城市分公司" width="140">
          <template #default="{ row }">
            <el-tag type="info" effect="light">🏙️ {{ row.cityName }}</el-tag>
          </template>
        </el-table-column>

        <el-table-column label="主理人负责人/电话" min-width="160">
          <template #default="{ row }">
            <div style="font-weight: 500">{{ row.ownerName || row.ownerUsername }}</div>
            <div style="font-size: 12px; color: #909399">{{ row.phone }}</div>
          </template>
        </el-table-column>

        <el-table-column prop="salesMultiplier" label="曝光率系数" width="130" align="center">
          <template #default="{ row }">
            <el-tag type="success">1 : {{ row.salesMultiplier }}</el-tag>
          </template>
        </el-table-column>

        <el-table-column label="资质凭证" width="120" align="center">
          <template #default="{ row }">
            <el-tag :type="row.certStatus === 'VERIFIED' ? 'success' : 'warning'" size="small">
              {{ row.certStatus === 'VERIFIED' ? '执照已核验' : '待核验' }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column prop="status" label="审核状态" width="120" align="center">
          <template #default="{ row }">
            <el-tag :type="getStatusTag(row.status)">
              {{ getStatusName(row.status) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column label="申请时间" width="160">
          <template #default="{ row }">
            <span style="font-size: 12px; color: #909399">{{ formatDateTime(row.createdAt) }}</span>
          </template>
        </el-table-column>

        <el-table-column label="管理动作" width="220" fixed="right">
          <template #default="{ row }">
            <!-- 待审核操作 -->
            <el-button
              v-if="row.status === 'PENDING'"
              type="success"
              link
              size="small"
              @click="handleStatusChange(row, 'ACTIVE', '审核通过')"
            >
              审核通过
            </el-button>
            <el-button
              v-if="row.status === 'PENDING'"
              type="danger"
              link
              size="small"
              @click="handleStatusChange(row, 'DISABLED', '驳回入驻')"
            >
              驳回
            </el-button>

            <!-- 正常经营中操作 -->
            <el-button
              v-if="row.status === 'ACTIVE'"
              type="danger"
              link
              size="small"
              @click="handleStatusChange(row, 'DISABLED', '暂停经营')"
            >
              暂停经营
            </el-button>

            <!-- 停用中操作 -->
            <el-button
              v-if="row.status === 'DISABLED'"
              type="success"
              link
              size="small"
              @click="handleStatusChange(row, 'ACTIVE', '恢复经营')"
            >
              恢复经营
            </el-button>

            <!-- 调整曝光率 -->
            <el-button type="primary" link size="small" @click="openMultiplierDialog(row)">
              曝光系数
            </el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页栏 -->
      <div class="pagination-bar">
        <el-pagination
          v-model:current-page="queryForm.page"
          v-model:page-size="queryForm.pageSize"
          :total="total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="fetchShops"
          @current-change="fetchShops"
        />
      </div>
    </el-card>

    <!-- 弹窗：曝光率倍数调节 -->
    <el-dialog v-model="multiplierDialogVisible" title="主理人同城流量曝光系数调控" width="460px">
      <el-form label-width="120px">
        <el-form-item label="商户名称">
          <span style="font-weight: 600">{{ activeShop?.name }}</span>
        </el-form-item>
        <el-form-item label="曝光率倍数">
          <el-select v-model="selectedMultiplier" style="width: 100%">
            <el-option label="1 : 1.00 (标准基础权重)" value="1.00" />
            <el-option label="1 : 1.50 (推荐优质商家)" value="1.50" />
            <el-option label="1 : 3.00 (重点核心主理人)" value="3.00" />
            <el-option label="1 : 5.00 (战略旗舰品牌)" value="5.00" />
            <el-option label="1 : 9.00 (全城霸屏顶流)" value="9.00" />
          </el-select>
        </el-form-item>
        <p style="font-size: 12px; color: #909399; margin: 0">
          💡 曝光系数将实时参与同城两公里 LBS 距离排序计算公式：`展示热度 = 基础热度 * 曝光系数`。
        </p>
      </el-form>
      <template #footer>
        <el-button @click="multiplierDialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="savingMultiplier" @click="submitMultiplier">保存设置</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Search, Refresh } from '@element-plus/icons-vue';
import request from '../../utils/request';

const loading = ref(false);
const activeTab = ref('ALL');
const shopList = ref<any[]>([]);
const total = ref(0);

const tabCounts = reactive({
  ALL: 0,
  PENDING: 0,
  ACTIVE: 0,
  DISABLED: 0,
});

const queryForm = reactive({
  keyword: '',
  page: 1,
  pageSize: 20,
});

// 曝光系数弹窗
const multiplierDialogVisible = ref(false);
const activeShop = ref<any>(null);
const selectedMultiplier = ref('1.00');
const savingMultiplier = ref(false);

async function fetchShops() {
  loading.value = true;
  try {
    const res: any = await request.get('/platform/shops', {
      params: {
        status: activeTab.value === 'ALL' ? undefined : activeTab.value,
        keyword: queryForm.keyword || undefined,
        page: queryForm.page,
        pageSize: queryForm.pageSize,
      },
    });
    if (res.code === 200 && res.data) {
      shopList.value = res.data.items || [];
      total.value = res.data.total || 0;
      if (activeTab.value === 'ALL') {
        tabCounts.ALL = total.value;
      }
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取主理人商家列表失败');
  } finally {
    loading.value = false;
  }
}

function handleTabChange() {
  queryForm.page = 1;
  fetchShops();
}

function handleSearch() {
  queryForm.page = 1;
  fetchShops();
}

function handleReset() {
  queryForm.keyword = '';
  queryForm.page = 1;
  fetchShops();
}

function formatDateTime(val: string) {
  if (!val) return '-';
  const d = new Date(val);
  return d.toLocaleString('zh-CN', { hour12: false });
}

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
    ACTIVE: '正常经营',
    PENDING: '待审核',
    DISABLED: '已暂停/驳回',
  };
  return map[status] || status;
}

async function handleStatusChange(row: any, targetStatus: string, actionName: string) {
  try {
    await ElMessageBox.confirm(`确认对商家【${row.name}】执行${actionName}操作吗？`, '状态变更确认', {
      type: targetStatus === 'ACTIVE' ? 'success' : 'warning',
    });
    const res: any = await request.put(`/platform/shops/${row.id}/status`, { status: targetStatus });
    if (res.code === 200) {
      ElMessage.success(`${actionName}操作成功！`);
      fetchShops();
    }
  } catch (e) {
    // cancel
  }
}

function openMultiplierDialog(row: any) {
  activeShop.value = row;
  selectedMultiplier.value = String(row.salesMultiplier || '1.00');
  multiplierDialogVisible.value = true;
}

async function submitMultiplier() {
  savingMultiplier.value = true;
  try {
    const res: any = await request.put(`/platform/shops/${activeShop.value.id}/multiplier`, {
      multiplier: selectedMultiplier.value,
    });
    if (res.code === 200) {
      ElMessage.success('曝光率倍数调整成功');
      multiplierDialogVisible.value = false;
      fetchShops();
    }
  } catch (e) {
    // handled
  } finally {
    savingMultiplier.value = false;
  }
}

onMounted(() => {
  fetchShops();
});
</script>

<style scoped>
.platform-shops-container {
  padding: 16px;
}
.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
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
.pagination-bar {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}
</style>
