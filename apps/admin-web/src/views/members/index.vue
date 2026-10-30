<template>
  <div class="members-container">
    <el-card shadow="never">
      <!-- 头部操作区 -->
      <div class="header-action">
        <div class="title-group">
          <h3>会员与合伙人资产中枢 (4-Tier Membership & Distribution)</h3>
          <p class="subtitle">
            全生命周期管理普通会员、钻石会员、黑钻会员与创客合伙人，支持终身绑定与自主定价
          </p>
        </div>
        <div class="btn-group">
          <el-button type="warning" :icon="Plus" @click="openCustomPartnerDialog">
            自主定价开通合伙人 (50/50分润)
          </el-button>
          <el-button :icon="Refresh" @click="fetchMembers">刷新列表</el-button>
        </div>
      </div>

      <!-- 搜索与筛选表单 -->
      <el-form :inline="true" :model="filters" class="filter-form">
        <el-form-item label="关键字">
          <el-input
            v-model="filters.keyword"
            placeholder="会员号/姓名/手机号/昵称"
            clearable
            style="width: 220px"
            @keyup.enter="handleSearch"
          />
        </el-form-item>

        <el-form-item label="会员等级">
          <el-select v-model="filters.level" placeholder="全部等级" clearable style="width: 140px">
            <el-option label="全部等级" value="" />
            <el-option label="普通会员" value="NORMAL" />
            <el-option label="钻石会员" value="DIAMOND" />
            <el-option label="黑钻会员" value="BLACK_DIAMOND" />
            <el-option label="创客合伙人" value="PARTNER" />
          </el-select>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" :icon="Search" @click="handleSearch">查询</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <!-- 会员表格 -->
      <el-table v-loading="loading" :data="memberList" stripe border style="width: 100%">
        <el-table-column prop="memberCode" label="会员编号" width="110" align="center">
          <template #default="{ row }">
            <el-tag effect="plain" type="primary" style="font-family: monospace; font-weight: 700">
              {{ row.memberCode }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column label="会员用户画像" min-width="170">
          <template #default="{ row }">
            <div style="font-weight: 600; color: #303133">
              {{ row.nickname || row.username || '微信用户' }}
              <span v-if="row.realName" style="color: #67c23a; font-size: 12px; margin-left: 4px">
                ({{ row.realName }})
              </span>
            </div>
            <div style="font-size: 12px; color: #909399; margin-top: 2px">
              {{ row.mobile ? maskMobile(row.mobile) : '未绑定手机' }}
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="level" label="会员等级" width="130" align="center">
          <template #default="{ row }">
            <el-tag :type="getLevelTagType(row.level)" effect="dark">
              {{ getLevelName(row.level) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column label="终身绑定关系" min-width="160">
          <template #default="{ row }">
            <div v-if="row.inviterCode">
              <span style="font-family: monospace; font-weight: 600; color: #409eff">
                {{ row.inviterCode }}
              </span>
              <span style="font-size: 12px; color: #606266; margin-left: 4px">
                ({{ row.inviterName }})
              </span>
            </div>
            <span v-else style="color: #c0c4cc; font-size: 13px">直属平台 (无邀约人)</span>
          </template>
        </el-table-column>

        <el-table-column label="所属店铺 / 门店" min-width="160">
          <template #default="{ row }">
            <div>{{ row.shopName || '平台公海池' }}</div>
            <div v-if="row.storeName" style="font-size: 12px; color: #909399">
              {{ row.storeName }}
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="points" label="积分" width="90" align="right">
          <template #default="{ row }">
            <span style="font-weight: 600; color: #409eff">{{ row.points }}</span>
          </template>
        </el-table-column>

        <el-table-column prop="balance" label="可用余额" width="120" align="right">
          <template #default="{ row }">
            <span style="font-weight: 600; color: #67c23a">￥{{ row.balance }}</span>
          </template>
        </el-table-column>

        <el-table-column prop="frozenBalance" label="7天在途佣金" width="130" align="right">
          <template #default="{ row }">
            <span style="font-weight: 600; color: #e6a23c">￥{{ row.frozenBalance }}</span>
          </template>
        </el-table-column>

        <el-table-column prop="isRealNameVerified" label="实名认证" width="100" align="center">
          <template #default="{ row }">
            <el-tag v-if="row.isRealNameVerified" type="success" size="small">已实名</el-tag>
            <el-tag v-else type="info" size="small">待认证</el-tag>
          </template>
        </el-table-column>

        <el-table-column label="注册时间" width="160">
          <template #default="{ row }">
            <span style="font-size: 12px; color: #909399">{{ formatDateTime(row.createdAt) }}</span>
          </template>
        </el-table-column>

        <el-table-column label="操作" width="140" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" size="small" link @click="openMemberDrawer(row)">
              权益画像
            </el-button>
            <el-button
              v-if="row.level !== 'PARTNER'"
              type="warning"
              size="small"
              link
              @click="quickOpenPartner(row)"
            >
              升级合伙人
            </el-button>
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
          @size-change="fetchMembers"
          @current-change="fetchMembers"
        />
      </div>
    </el-card>

    <!-- 老板主动自主定价开通合伙人弹窗 -->
    <el-dialog v-model="partnerDialogVisible" title="主理人自主定价开通创客合伙人" width="480px">
      <el-form label-width="110px">
        <el-form-item label="开通对象" required>
          <el-input
            v-model="customPartnerForm.memberCode"
            placeholder="输入目标 6 位会员编号 (如 888888)"
            maxlength="6"
            clearable
          />
        </el-form-item>
        <el-form-item label="开通金额 (元)" required>
          <el-input v-model="customPartnerForm.price" placeholder="自定义金额 (如 1000.00)" clearable />
        </el-form-item>
        <el-form-item label="开通备注">
          <el-input v-model="customPartnerForm.remark" placeholder="如：老员工特批、核心合伙人" />
        </el-form-item>
        <el-alert
          title="分润规则说明：主理人自主添加合伙人，金额按 50% 主理人 + 50% 城市分公司 实时结算分润，平台留存 0 元，同时自动为合伙人发放 10000 权益积分。"
          type="warning"
          :closable="false"
          style="margin-bottom: 12px"
        />
      </el-form>
      <template #footer>
        <el-button @click="partnerDialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="submittingPartner" @click="handleConfirmPartner">
          确认开通并分润
        </el-button>
      </template>
    </el-dialog>

    <!-- 会员全息档案抽屉 -->
    <el-drawer v-model="drawerVisible" title="会员全息权益画像" size="460px">
      <div v-if="activeMember" class="drawer-body">
        <div class="user-card-header">
          <el-avatar :size="56" :src="activeMember.avatarUrl || 'https://cube.elemecdn.com/3/7c/3ea6beec64369c2642b92c6726f1epng.png'" />
          <div class="user-meta">
            <h4>{{ activeMember.nickname || activeMember.username }}</h4>
            <el-tag :type="getLevelTagType(activeMember.level)" size="small">
              {{ getLevelName(activeMember.level) }}
            </el-tag>
          </div>
        </div>

        <el-descriptions title="资金与积分权益" :column="2" border style="margin-top: 20px">
          <el-descriptions-item label="会员编号">{{ activeMember.memberCode }}</el-descriptions-item>
          <el-descriptions-item label="实名状态">
            {{ activeMember.isRealNameVerified ? '已认证' : '未认证' }}
          </el-descriptions-item>
          <el-descriptions-item label="可用余额">￥{{ activeMember.balance }}</el-descriptions-item>
          <el-descriptions-item label="在途佣金">￥{{ activeMember.frozenBalance }}</el-descriptions-item>
          <el-descriptions-item label="权益积分">{{ activeMember.points }}</el-descriptions-item>
          <el-descriptions-item label="累计收益">￥{{ activeMember.totalEarned }}</el-descriptions-item>
        </el-descriptions>

        <el-descriptions title="拓扑与归属关系" :column="1" border style="margin-top: 20px">
          <el-descriptions-item label="邀约上级">
            {{ activeMember.inviterCode ? `${activeMember.inviterCode} (${activeMember.inviterName})` : '无 (直属平台)' }}
          </el-descriptions-item>
          <el-descriptions-item label="归属店铺">{{ activeMember.shopName || '平台' }}</el-descriptions-item>
          <el-descriptions-item label="归属门店">{{ activeMember.storeName || '未指定' }}</el-descriptions-item>
          <el-descriptions-item label="注册时间">{{ formatDateTime(activeMember.createdAt) }}</el-descriptions-item>
        </el-descriptions>
      </div>
    </el-drawer>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import { Search, Refresh, Plus } from '@element-plus/icons-vue';
import request from '../../utils/request';

const loading = ref(false);
const memberList = ref<any[]>([]);
const total = ref(0);

const filters = reactive({
  keyword: '',
  level: '',
  page: 1,
  pageSize: 20,
});

// 自主定价开通合伙人
const partnerDialogVisible = ref(false);
const submittingPartner = ref(false);
const customPartnerForm = reactive({
  memberCode: '',
  price: '1000.00',
  remark: '',
});

// 抽屉详情
const drawerVisible = ref(false);
const activeMember = ref<any>(null);

async function fetchMembers() {
  loading.value = true;
  try {
    const res: any = await request.get('/members', {
      params: {
        keyword: filters.keyword || undefined,
        level: filters.level || undefined,
        page: filters.page,
        pageSize: filters.pageSize,
      },
    });
    if (res.code === 200 && res.data) {
      memberList.value = res.data.items || [];
      total.value = res.data.total || 0;
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取会员列表失败');
  } finally {
    loading.value = false;
  }
}

function handleSearch() {
  filters.page = 1;
  fetchMembers();
}

function handleReset() {
  filters.keyword = '';
  filters.level = '';
  filters.page = 1;
  fetchMembers();
}

function maskMobile(m: string) {
  if (!m || m.length < 7) return m;
  return m.slice(0, 3) + '****' + m.slice(-4);
}

function formatDateTime(val: string) {
  if (!val) return '-';
  const d = new Date(val);
  return d.toLocaleString('zh-CN', { hour12: false });
}

function getLevelTagType(level: string) {
  const map: Record<string, string> = {
    NORMAL: 'info',
    DIAMOND: 'primary',
    BLACK_DIAMOND: 'warning',
    PARTNER: 'danger',
  };
  return map[level] || 'info';
}

function getLevelName(level: string) {
  const map: Record<string, string> = {
    NORMAL: '普通会员',
    DIAMOND: '钻石会员',
    BLACK_DIAMOND: '黑钻会员',
    PARTNER: '创客合伙人',
  };
  return map[level] || level;
}

function openCustomPartnerDialog() {
  customPartnerForm.memberCode = '';
  customPartnerForm.price = '1000.00';
  customPartnerForm.remark = '';
  partnerDialogVisible.value = true;
}

function quickOpenPartner(row: any) {
  customPartnerForm.memberCode = row.memberCode;
  customPartnerForm.price = '1000.00';
  customPartnerForm.remark = `为主理人客户 ${row.memberCode} 特批开通`;
  partnerDialogVisible.value = true;
}

async function handleConfirmPartner() {
  if (!customPartnerForm.memberCode.trim()) {
    ElMessage.warning('请输入目标会员的 6 位编号');
    return;
  }
  const price = parseFloat(customPartnerForm.price);
  if (isNaN(price) || price <= 0) {
    ElMessage.warning('请输入有效的开通金额');
    return;
  }

  submittingPartner.value = true;
  try {
    const res: any = await request.post('/members/custom-partner', {
      memberCode: customPartnerForm.memberCode.trim(),
      price: customPartnerForm.price.trim(),
      remark: customPartnerForm.remark.trim() || undefined,
    });
    if (res.code === 200) {
      ElMessage.success('开通合伙人成功！50/50 分润已实时清算入账');
      partnerDialogVisible.value = false;
      fetchMembers();
    }
  } catch (err: any) {
    // 错误在 request 拦截器已提示
  } finally {
    submittingPartner.value = false;
  }
}

function openMemberDrawer(row: any) {
  activeMember.value = row;
  drawerVisible.value = true;
}

onMounted(() => {
  fetchMembers();
});
</script>

<style scoped>
.members-container {
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

.user-card-header {
  display: flex;
  align-items: center;
  gap: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid #ebeef5;
}

.user-meta h4 {
  margin: 0 0 6px 0;
  font-size: 16px;
  color: #303133;
}
</style>
