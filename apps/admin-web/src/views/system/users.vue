<template>
  <div class="users-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>全域系统账号管理 (IAM Users Management)</h3>
          <p class="sub-tip">管理总部管理员、城市代理、商家主理人、门店员工等全域五级组织账号与认证凭证</p>
        </div>
        <div class="action-btns">
          <el-button type="primary" icon="Plus" @click="openCreateDialog">新建系统账号</el-button>
          <el-button icon="Refresh" @click="fetchUsers">刷新数据</el-button>
        </div>
      </div>

      <!-- 搜索过滤栏 -->
      <div class="filter-bar">
        <el-input
          v-model="query.keyword"
          placeholder="搜索用户名 / 姓名 / 手机号"
          clearable
          style="width: 240px; margin-right: 12px"
          @keyup.enter="handleSearch"
        />
        <el-select
          v-model="query.status"
          placeholder="账号状态"
          clearable
          style="width: 140px; margin-right: 12px"
          @change="handleSearch"
        >
          <el-option label="全部状态" value="" />
          <el-option label="正常 (ACTIVE)" value="ACTIVE" />
          <el-option label="已冻结 (DISABLED)" value="DISABLED" />
        </el-select>
        <el-button type="primary" icon="Search" @click="handleSearch">查询</el-button>
        <el-button @click="resetQuery">重置</el-button>
      </div>

      <!-- 用户数据列表 -->
      <el-table :data="userList" v-loading="loading" stripe style="width: 100%; margin-top: 16px">
        <el-table-column label="登录账号" min-width="150">
          <template #default="{ row }">
            <div style="display: flex; align-items: center; gap: 8px">
              <el-avatar :size="28" icon="UserFilled" style="background-color: #409eff" />
              <div>
                <div style="font-weight: 600; color: #303133">{{ row.username }}</div>
                <div v-if="row.username === 'admin'" style="font-size: 11px; color: #e6a23c">
                  [超级总控]
                </div>
              </div>
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="nickname" label="姓名 / 昵称" min-width="140" />

        <el-table-column prop="mobile" label="绑定手机" min-width="130">
          <template #default="{ row }">
            <span>{{ row.mobile || '--' }}</span>
          </template>
        </el-table-column>

        <el-table-column label="所属组织租户" min-width="180">
          <template #default="{ row }">
            <div v-if="row.tenantName">
              <el-tag size="small" :type="getTenantTagType(row.tenantType)">
                {{ row.tenantType }}
              </el-tag>
              <span style="margin-left: 6px; font-size: 13px">{{ row.tenantName }}</span>
            </div>
            <span v-else style="color: #909399; font-size: 13px">🏢 平台总部直属</span>
          </template>
        </el-table-column>

        <el-table-column label="分配角色" min-width="160">
          <template #default="{ row }">
            <div style="display: flex; flex-wrap: wrap; gap: 4px">
              <el-tag
                v-for="r in row.roles"
                :key="r.id"
                size="small"
                effect="plain"
                type="info"
              >
                {{ r.name || r.code }}
              </el-tag>
              <span v-if="!row.roles || row.roles.length === 0" style="color: #c0c4cc; font-size: 12px">
                未分配
              </span>
            </div>
          </template>
        </el-table-column>

        <el-table-column label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 'ACTIVE' ? 'success' : 'danger'" size="small">
              {{ row.status === 'ACTIVE' ? '正常' : '已冻结' }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column label="创建时间" min-width="160">
          <template #default="{ row }">
            <span>{{ formatDate(row.createdAt) }}</span>
          </template>
        </el-table-column>

        <el-table-column label="操作" width="220" fixed="right">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openEditDialog(row)">编辑</el-button>
            <el-button link type="warning" size="small" @click="openResetPassword(row)">重置密码</el-button>
            <el-popconfirm
              :title="row.status === 'ACTIVE' ? '确认冻结该账号登录权限吗？' : '确认恢复该账号正常使用吗？'"
              @confirm="toggleUserStatus(row)"
            >
              <template #reference>
                <el-button
                  link
                  :type="row.status === 'ACTIVE' ? 'danger' : 'success'"
                  size="small"
                  :disabled="row.username === 'admin'"
                >
                  {{ row.status === 'ACTIVE' ? '冻结' : '启用' }}
                </el-button>
              </template>
            </el-popconfirm>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页组件 -->
      <div class="pagination-bar">
        <el-pagination
          v-model:current-page="query.page"
          v-model:page-size="query.pageSize"
          :total="total"
          :page-sizes="[10, 20, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="fetchUsers"
          @current-change="fetchUsers"
        />
      </div>
    </el-card>

    <!-- 新建/编辑账号对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="isEdit ? '编辑系统账号' : '新建系统账号'"
      width="540px"
      destroy-on-close
    >
      <el-form :model="form" label-width="100px" style="padding-right: 20px">
        <el-form-item label="登录用户名" required>
          <el-input
            v-model="form.username"
            placeholder="请输入登录用户名 (3-50位英文字符/数字)"
            :disabled="isEdit"
          />
        </el-form-item>

        <el-form-item v-if="!isEdit" label="初始登录密码" required>
          <el-input
            v-model="form.password"
            type="password"
            placeholder="请输入初始密码 (至少6位)"
            show-password
          />
        </el-form-item>

        <el-form-item label="姓名 / 昵称" required>
          <el-input v-model="form.nickname" placeholder="例如：张运营 / 李老板" />
        </el-form-item>

        <el-form-item label="绑定手机号">
          <el-input v-model="form.mobile" placeholder="用于快捷登录与短信通知 (11位)" />
        </el-form-item>

        <el-form-item label="分配角色" required>
          <el-select v-model="form.roleId" placeholder="请选择系统赋予角色" style="width: 100%">
            <el-option
              v-for="r in roleOptions"
              :key="r.id"
              :label="`${r.name} (${r.code})`"
              :value="r.id"
            />
          </el-select>
        </el-form-item>
      </el-form>

      <template #footer>
        <span class="dialog-footer">
          <el-button @click="dialogVisible = false">取消</el-button>
          <el-button type="primary" :loading="submitting" @click="submitUserForm">确定保存</el-button>
        </span>
      </template>
    </el-dialog>

    <!-- 重置密码对话框 -->
    <el-dialog v-model="resetPwdVisible" title="重置账号登录密码" width="420px">
      <el-form label-position="top">
        <el-form-item label="重置目标账号">
          <el-input :model-value="currentUserRow?.username" disabled />
        </el-form-item>
        <el-form-item label="输入新密码" required>
          <el-input
            v-model="newPassword"
            type="password"
            placeholder="请输入重置后的新密码 (至少6位)"
            show-password
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="resetPwdVisible = false">取消</el-button>
          <el-button type="primary" :loading="resetting" @click="submitResetPassword">确认重置</el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import request from '../../utils/request';
import { ElMessage } from 'element-plus';
import { Plus, Refresh, Search, UserFilled } from '@element-plus/icons-vue';

const loading = ref(false);
const submitting = ref(false);
const resetting = ref(false);
const userList = ref<any[]>([]);
const total = ref(0);
const roleOptions = ref<any[]>([]);

const query = reactive({
  keyword: '',
  status: '',
  page: 1,
  pageSize: 20,
});

const dialogVisible = ref(false);
const isEdit = ref(false);
const editId = ref('');
const form = reactive({
  username: '',
  password: '',
  nickname: '',
  mobile: '',
  roleId: '',
});

const resetPwdVisible = ref(false);
const currentUserRow = ref<any>(null);
const newPassword = ref('');

onMounted(async () => {
  await fetchRoles();
  await fetchUsers();
});

async function fetchRoles() {
  try {
    const res: any = await request.get('/iam/roles');
    roleOptions.value = res.data || [];
  } catch (err) {
    console.error('Failed to fetch roles:', err);
  }
}

async function fetchUsers() {
  loading.value = true;
  try {
    const res: any = await request.get('/iam/users', { params: query });
    const data = res.data || res;
    userList.value = data.items || [];
    total.value = data.total || 0;
  } catch (err) {
    console.error('Failed to fetch users:', err);
  } finally {
    loading.value = false;
  }
}

function handleSearch() {
  query.page = 1;
  fetchUsers();
}

function resetQuery() {
  query.keyword = '';
  query.status = '';
  query.page = 1;
  fetchUsers();
}

function getTenantTagType(type: string) {
  const map: Record<string, string> = {
    PLATFORM: 'danger',
    CITY: 'warning',
    SHOP: 'primary',
    STORE: 'success',
    SUPPLY: 'info',
  };
  return map[type] || 'info';
}

function formatDate(dateStr: string) {
  if (!dateStr) return '--';
  const d = new Date(dateStr);
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')} ${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
}

function openCreateDialog() {
  isEdit.value = false;
  editId.value = '';
  form.username = '';
  form.password = '123456';
  form.nickname = '';
  form.mobile = '';
  form.roleId = roleOptions.value[0]?.id || '';
  dialogVisible.value = true;
}

function openEditDialog(row: any) {
  isEdit.value = true;
  editId.value = row.id;
  form.username = row.username;
  form.nickname = row.nickname;
  form.mobile = row.mobile || '';
  form.roleId = row.roles?.[0]?.id || '';
  dialogVisible.value = true;
}

async function submitUserForm() {
  if (!form.username.trim() || !form.nickname.trim()) {
    ElMessage.warning('用户名与昵称均为必填项');
    return;
  }
  if (!isEdit.value && (!form.password || form.password.length < 6)) {
    ElMessage.warning('密码长度至少为6位');
    return;
  }

  submitting.value = true;
  try {
    if (isEdit.value) {
      await request.put(`/iam/users/${editId.value}`, {
        nickname: form.nickname.trim(),
        mobile: form.mobile?.trim() || undefined,
        roleId: form.roleId || undefined,
      });
      ElMessage.success('账号信息已更新');
    } else {
      await request.post('/iam/users', {
        username: form.username.trim(),
        password: form.password.trim(),
        nickname: form.nickname.trim(),
        mobile: form.mobile?.trim() || undefined,
        roleId: form.roleId,
      });
      ElMessage.success('系统新账号创建成功');
    }
    dialogVisible.value = false;
    fetchUsers();
  } catch (err: any) {
    console.error('Submit user error:', err);
  } finally {
    submitting.value = false;
  }
}

function openResetPassword(row: any) {
  currentUserRow.value = row;
  newPassword.value = '';
  resetPwdVisible.value = true;
}

async function submitResetPassword() {
  if (!newPassword.value || newPassword.value.length < 6) {
    ElMessage.warning('新密码长度不能少于6位');
    return;
  }

  resetting.value = true;
  try {
    const res: any = await request.post(`/iam/users/${currentUserRow.value.id}/reset-password`, {
      newPassword: newPassword.value.trim(),
    });
    ElMessage.success(res.message || '密码重置成功');
    resetPwdVisible.value = false;
  } catch (err) {
    console.error('Reset password error:', err);
  } finally {
    resetting.value = false;
  }
}

async function toggleUserStatus(row: any) {
  const newStatus = row.status === 'ACTIVE' ? 'DISABLED' : 'ACTIVE';
  try {
    await request.put(`/iam/users/${row.id}/status`, { status: newStatus });
    ElMessage.success(`账号已成功${newStatus === 'ACTIVE' ? '启用' : '冻结'}`);
    row.status = newStatus;
  } catch (err) {
    console.error('Toggle status error:', err);
  }
}
</script>

<style scoped>
.users-container {
  padding: 0;
}

.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #ebeef5;
  padding-bottom: 16px;
  margin-bottom: 16px;
}

.header-action h3 {
  margin: 0;
  font-size: 18px;
  color: #1e222d;
}

.sub-tip {
  margin: 6px 0 0;
  font-size: 13px;
  color: #909399;
}

.filter-bar {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
}

.pagination-bar {
  margin-top: 20px;
  display: flex;
  justify-content: flex-end;
}
</style>
