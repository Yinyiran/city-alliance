<template>
  <div class="clerks-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>门店店员与核销员管理 (Store Clerks & Cashiers)</h3>
          <p class="sub-tip">配置各实体门店核销员、收银员与导购权限，分配核销工号并防范越权核销</p>
        </div>
        <el-button type="primary" @click="openAddClerk">添加店员</el-button>
      </div>

      <el-table :data="clerkList" stripe style="width: 100%">
        <el-table-column prop="clerkNo" label="工号" width="120">
          <template #default="{ row }">
            <span style="font-family: monospace; font-weight: 600">{{ row.clerkNo }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="realName" label="店员姓名" width="130" />
        <el-table-column prop="phone" label="绑定手机" width="140" />
        <el-table-column prop="storeName" label="归属实体门店" min-width="180" />
        <el-table-column prop="role" label="岗位角色" width="130">
          <template #default="{ row }">
            <el-tag :type="getRoleTag(row.role)">{{ getRoleName(row.role) }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="verifyCount" label="累计核销订单" width="130">
          <template #default="{ row }">
            <span style="font-weight: 600; color: #409eff">{{ row.verifyCount }} 笔</span>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.status === 'ACTIVE' ? 'success' : 'danger'">
              {{ row.status === 'ACTIVE' ? '正常' : '已停职' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="160" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" link size="small" @click="editClerk(row)">编辑权限</el-button>
            <el-button
              :type="row.status === 'ACTIVE' ? 'danger' : 'success'"
              link
              size="small"
              @click="toggleStatus(row)"
            >
              {{ row.status === 'ACTIVE' ? '停用' : '启用' }}
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <el-dialog v-model="dialogVisible" title="添加门店店员/核销员" width="500px">
      <el-form :model="formData" label-width="110px">
        <el-form-item label="工号" required>
          <el-input v-model="formData.clerkNo" placeholder="例如：CLK-008" />
        </el-form-item>
        <el-form-item label="姓名" required>
          <el-input v-model="formData.realName" placeholder="店员真实姓名" />
        </el-form-item>
        <el-form-item label="手机号码" required>
          <el-input v-model="formData.phone" placeholder="用于扫码核销授权登录" />
        </el-form-item>
        <el-form-item label="归属门店" required>
          <el-select v-model="formData.storeName" placeholder="请选择门店" style="width: 100%">
            <el-option label="拾光烘焙研磨 (西湖总店)" value="拾光烘焙研磨 (西湖总店)" />
            <el-option label="拾光烘焙 (钱江新城二店)" value="拾光烘焙 (钱江新城二店)" />
            <el-option label="萌宠星球生活馆 (城西旗舰店)" value="萌宠星球生活馆 (城西旗舰店)" />
          </el-select>
        </el-form-item>
        <el-form-item label="岗位职责" required>
          <el-select v-model="formData.role" style="width: 100%">
            <el-option label="门店收银员 (支持扫码核销+收银交班)" value="CASHIER" />
            <el-option label="门店导购员 (仅支持预约接待)" value="GUIDE" />
            <el-option label="驻店店长 (全权限)" value="MANAGER" />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="saveClerk">保存店员</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ElMessage } from 'element-plus';

const clerkList = ref([
  {
    id: 'clk-1',
    clerkNo: 'CLK-001',
    realName: '王小芬',
    phone: '13511112222',
    storeName: '拾光烘焙研磨 (西湖总店)',
    role: 'CASHIER',
    verifyCount: 342,
    status: 'ACTIVE',
  },
  {
    id: 'clk-2',
    clerkNo: 'CLK-002',
    realName: '赵志军',
    phone: '13633334444',
    storeName: '拾光烘焙 (钱江新城二店)',
    role: 'MANAGER',
    verifyCount: 185,
    status: 'ACTIVE',
  },
  {
    id: 'clk-3',
    clerkNo: 'CLK-003',
    realName: '刘婷婷',
    phone: '13755556666',
    storeName: '萌宠星球生活馆 (城西旗舰店)',
    role: 'GUIDE',
    verifyCount: 68,
    status: 'ACTIVE',
  },
]);

const dialogVisible = ref(false);
const formData = ref({
  clerkNo: '',
  realName: '',
  phone: '',
  storeName: '',
  role: 'CASHIER',
});

function getRoleTag(role: string) {
  const map: Record<string, string> = {
    MANAGER: 'danger',
    CASHIER: 'primary',
    GUIDE: 'info',
  };
  return map[role] || 'info';
}

function getRoleName(role: string) {
  const map: Record<string, string> = {
    MANAGER: '驻店店长',
    CASHIER: '核销收银员',
    GUIDE: '门店导购',
  };
  return map[role] || role;
}

function openAddClerk() {
  formData.value = {
    clerkNo: 'CLK-00' + (clerkList.value.length + 1),
    realName: '',
    phone: '',
    storeName: '拾光烘焙研磨 (西湖总店)',
    role: 'CASHIER',
  };
  dialogVisible.value = true;
}

function editClerk(row: any) {
  formData.value = { ...row };
  dialogVisible.value = true;
}

function toggleStatus(row: any) {
  row.status = row.status === 'ACTIVE' ? 'DISABLED' : 'ACTIVE';
  ElMessage.success(`店员状态已更新为: ${row.status === 'ACTIVE' ? '正常' : '已停用'}`);
}

function saveClerk() {
  if (!formData.value.realName || !formData.value.phone) {
    ElMessage.error('请填写完整店员姓名与手机');
    return;
  }
  clerkList.value.unshift({
    id: 'clk-' + Date.now(),
    clerkNo: formData.value.clerkNo,
    realName: formData.value.realName,
    phone: formData.value.phone,
    storeName: formData.value.storeName,
    role: formData.value.role,
    verifyCount: 0,
    status: 'ACTIVE',
  });
  dialogVisible.value = false;
  ElMessage.success('门店店员已成功建档并开通核销权限！');
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
