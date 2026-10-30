<template>
  <div class="roles-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>角色与权限域配置 (RBAC & Permission Matrix)</h3>
          <p class="sub-tip">配置平台总部、城市分公司、品牌主理人、门店及自定义业务岗位的细粒度功能与按钮权限</p>
        </div>
        <div class="action-btns">
          <el-button type="primary" icon="Plus" @click="openCreateRoleDialog">新建自定义角色</el-button>
          <el-button icon="Refresh" @click="fetchRoles">刷新角色</el-button>
        </div>
      </div>

      <el-row :gutter="20" style="margin-top: 16px">
        <!-- 左侧角色卡片列表 -->
        <el-col :span="8">
          <div class="role-list-box">
            <div
              v-for="role in roleList"
              :key="role.id"
              class="role-card"
              :class="{ active: selectedRole?.id === role.id }"
              @click="selectRole(role)"
            >
              <div class="role-card-header">
                <span class="role-title">{{ role.name }}</span>
                <el-tag size="small" :type="role.isSystem ? 'danger' : 'primary'">
                  {{ role.isSystem ? '系统内置' : '自定义' }}
                </el-tag>
              </div>
              <div class="role-code-text">标识: {{ role.code }}</div>
              <div class="role-meta">
                <el-tag size="small" effect="plain" type="info">{{ role.tenantType }}</el-tag>
                <span class="user-count-tip">共 <b>{{ role.userCount }}</b> 名账号成员</span>
              </div>
            </div>
          </div>
        </el-col>

        <!-- 右侧细粒度权限树勾选配置 -->
        <el-col :span="16">
          <div v-if="selectedRole" class="permission-config-panel">
            <div class="panel-header">
              <div>
                <span style="font-size: 16px; font-weight: 600; color: #303133">
                  【{{ selectedRole.name }}】功能与操作权限树
                </span>
                <span v-if="selectedRole.code === 'SUPER_ADMIN'" style="margin-left: 10px; color: #e6a23c; font-size: 12px">
                  ⚡ 超级管理员默认享有系统全域通配权限 (*)
                </span>
              </div>
              <el-button
                type="primary"
                :loading="saving"
                :disabled="selectedRole.code === 'SUPER_ADMIN'"
                @click="saveRolePermissions"
              >
                保存权限矩阵
              </el-button>
            </div>

            <div class="tree-box">
              <el-tree
                ref="treeRef"
                :data="permissionsTree"
                show-checkbox
                node-key="id"
                default-expand-all
                :props="{ label: 'label', children: 'children' }"
                :disabled="selectedRole.code === 'SUPER_ADMIN'"
              />
            </div>
          </div>

          <div v-else class="empty-tip">
            <el-empty description="请在左侧选择一个角色以查看并配置细粒度权限" />
          </div>
        </el-col>
      </el-row>
    </el-card>

    <!-- 新建自定义角色对话框 -->
    <el-dialog v-model="createDialogVisible" title="新建自定义业务角色" width="500px">
      <el-form :model="createForm" label-width="110px">
        <el-form-item label="角色标识代码" required>
          <el-input
            v-model="createForm.code"
            placeholder="例如：FINANCE_OFFICER (大写字母)"
          />
        </el-form-item>
        <el-form-item label="角色显示名称" required>
          <el-input v-model="createForm.name" placeholder="例如：总部财务审核专员" />
        </el-form-item>
        <el-form-item label="归属租户级别" required>
          <el-select v-model="createForm.tenantType" placeholder="请选择适用组织层级" style="width: 100%">
            <el-option label="🏢 平台总部 (PLATFORM)" value="PLATFORM" />
            <el-option label="🏙️ 城市分公司 (CITY)" value="CITY" />
            <el-option label="🏪 品牌商家 (SHOP)" value="SHOP" />
            <el-option label="📍 线下门店 (STORE)" value="STORE" />
            <el-option label="🏭 B2B 供货商 (SUPPLY)" value="SUPPLY" />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="createDialogVisible = false">取消</el-button>
          <el-button type="primary" :loading="creating" @click="submitCreateRole">立即创建</el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, nextTick } from 'vue';
import request from '../../utils/request';
import { ElMessage, ElTree } from 'element-plus';
import { Plus, Refresh } from '@element-plus/icons-vue';

const roleList = ref<any[]>([]);
const selectedRole = ref<any>(null);
const permissionsTree = ref<any[]>([]);
const treeRef = ref<InstanceType<typeof ElTree>>();
const saving = ref(false);

const createDialogVisible = ref(false);
const creating = ref(false);
const createForm = reactive({
  code: '',
  name: '',
  tenantType: 'PLATFORM',
});

onMounted(async () => {
  await fetchPermissionsTree();
  await fetchRoles();
});

async function fetchPermissionsTree() {
  try {
    const res: any = await request.get('/iam/roles/permissions-tree');
    permissionsTree.value = res.data || [];
  } catch (err) {
    console.error('Failed to fetch permissions tree:', err);
  }
}

async function fetchRoles() {
  try {
    const res: any = await request.get('/iam/roles');
    roleList.value = res.data || [];
    if (roleList.value.length > 0 && !selectedRole.value) {
      selectRole(roleList.value[0]);
    }
  } catch (err) {
    console.error('Failed to fetch roles:', err);
  }
}

function selectRole(role: any) {
  selectedRole.value = role;
  nextTick(() => {
    if (treeRef.value) {
      const perms = role.permissions || [];
      if (role.code === 'SUPER_ADMIN' || perms.includes('*')) {
        // 勾选全部
        const allLeafKeys: string[] = [];
        permissionsTree.value.forEach((cat) => {
          cat.children?.forEach((node: any) => allLeafKeys.push(node.id));
        });
        treeRef.value.setCheckedKeys(allLeafKeys);
      } else {
        treeRef.value.setCheckedKeys(perms);
      }
    }
  });
}

async function saveRolePermissions() {
  if (!selectedRole.value || selectedRole.value.code === 'SUPER_ADMIN') return;

  saving.value = true;
  try {
    const checkedKeys = treeRef.value?.getCheckedKeys(true) as string[];
    await request.put(`/iam/roles/${selectedRole.value.id}`, {
      permissions: checkedKeys,
    });
    selectedRole.value.permissions = checkedKeys;
    ElMessage.success(`【${selectedRole.value.name}】权限配置已生效！`);
  } catch (err) {
    console.error('Save role permissions error:', err);
  } finally {
    saving.value = false;
  }
}

function openCreateRoleDialog() {
  createForm.code = '';
  createForm.name = '';
  createForm.tenantType = 'PLATFORM';
  createDialogVisible.value = true;
}

async function submitCreateRole() {
  if (!createForm.code.trim() || !createForm.name.trim()) {
    ElMessage.warning('角色标识与角色名称为必填项');
    return;
  }

  creating.value = true;
  try {
    await request.post('/iam/roles', {
      code: createForm.code.trim().toUpperCase(),
      name: createForm.name.trim(),
      tenantType: createForm.tenantType,
      permissions: [],
    });
    ElMessage.success('自定义角色已成功创建');
    createDialogVisible.value = false;
    await fetchRoles();
  } catch (err) {
    console.error('Create role error:', err);
  } finally {
    creating.value = false;
  }
}
</script>

<style scoped>
.roles-container {
  padding: 0;
}

.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #ebeef5;
  padding-bottom: 16px;
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

.role-list-box {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.role-card {
  padding: 14px 16px;
  border: 1px solid #e4e7ed;
  border-radius: 8px;
  cursor: pointer;
  background: #fafafa;
  transition: all 0.2s ease;
}

.role-card:hover {
  border-color: #409eff;
  background: #f0f7ff;
}

.role-card.active {
  border-color: #409eff;
  background: #ecf5ff;
  box-shadow: 0 2px 12px 0 rgba(64, 158, 255, 0.15);
}

.role-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.role-title {
  font-size: 15px;
  font-weight: 600;
  color: #303133;
}

.role-code-text {
  font-size: 12px;
  color: #909399;
  margin-top: 6px;
  font-family: monospace;
}

.role-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
}

.user-count-tip {
  font-size: 12px;
  color: #606266;
}

.permission-config-panel {
  background: #fff;
  border: 1px solid #e4e7ed;
  border-radius: 8px;
  padding: 16px 20px;
}

.panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #ebeef5;
  padding-bottom: 14px;
  margin-bottom: 16px;
}

.tree-box {
  max-height: 560px;
  overflow-y: auto;
  padding: 10px;
  background: #fafbfc;
  border-radius: 6px;
}

.empty-tip {
  padding: 40px 0;
}
</style>
