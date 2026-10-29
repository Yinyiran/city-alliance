<template>
  <div class="platform-cities-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>城市分公司与代理商矩阵 (City Branch Management)</h3>
          <p class="sub-tip">管理各城市合伙人分公司授权、辖区绑定、25%开通分成费率与城市专享权限</p>
        </div>
        <el-button type="primary" @click="openAddDialog">授权新城市分公司</el-button>
      </div>

      <el-table :data="cityList" stripe style="width: 100%">
        <el-table-column prop="cityName" label="授权城市" min-width="140">
          <template #default="{ row }">
            <span style="font-weight: 700; font-size: 15px">🏙️ {{ row.cityName }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="companyName" label="分公司企业名称" min-width="200" />
        <el-table-column prop="agentLeader" label="负责人/法人" width="130">
          <template #default="{ row }">
            <div>{{ row.agentLeader }}</div>
            <div style="font-size: 12px; color: #909399">{{ row.phone }}</div>
          </template>
        </el-table-column>
        <el-table-column prop="merchantCount" label="辖区主理人数量" width="140">
          <template #default="{ row }">
            <el-tag type="info">{{ row.merchantCount }} 家商家</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="totalEarned" label="分公司累计分润(25%)" width="180">
          <template #default="{ row }">
            <span style="font-weight: 700; color: #67c23a">￥{{ row.totalEarned }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="运营状态" width="110">
          <template #default="{ row }">
            <el-tag :type="row.status === 'ACTIVE' ? 'success' : 'danger'">
              {{ row.status === 'ACTIVE' ? '正常运营' : '已冻结' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="180" fixed="right">
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

    <el-dialog v-model="dialogVisible" title="城市分公司授权建档" width="550px">
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
        <el-button type="primary" @click="saveCity">保存授权</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ElMessage } from 'element-plus';

const cityList = ref([
  {
    id: 'city-1',
    cityName: '杭州市',
    companyName: '杭州城盟数智商业管理有限公司',
    agentLeader: '张建国',
    phone: '13800571001',
    merchantCount: 42,
    totalEarned: '128450.00',
    status: 'ACTIVE',
  },
  {
    id: 'city-2',
    cityName: '宁波市',
    companyName: '宁波城盟优选供应链运营有限公司',
    agentLeader: '李伟强',
    phone: '13900574002',
    merchantCount: 28,
    totalEarned: '86200.00',
    status: 'ACTIVE',
  },
  {
    id: 'city-3',
    cityName: '温州市',
    companyName: '温州城盟同城网络科技有限公司',
    agentLeader: '陈志祥',
    phone: '13700577003',
    merchantCount: 19,
    totalEarned: '54180.00',
    status: 'ACTIVE',
  },
]);

const dialogVisible = ref(false);
const formData = ref({
  cityName: '',
  companyName: '',
  agentLeader: '',
  phone: '',
});

function openAddDialog() {
  formData.value = {
    cityName: '',
    companyName: '',
    agentLeader: '',
    phone: '',
  };
  dialogVisible.value = true;
}

function editCity(row: any) {
  formData.value = { ...row };
  dialogVisible.value = true;
}

function toggleCityStatus(row: any) {
  row.status = row.status === 'ACTIVE' ? 'DISABLED' : 'ACTIVE';
  ElMessage.success(`分公司状态已更新为: ${row.status === 'ACTIVE' ? '正常运营' : '已冻结'}`);
}

function saveCity() {
  if (!formData.value.cityName || !formData.value.companyName) {
    ElMessage.error('请填写完整城市与公司信息');
    return;
  }
  cityList.value.unshift({
    id: 'city-' + Date.now(),
    cityName: formData.value.cityName,
    companyName: formData.value.companyName,
    agentLeader: formData.value.agentLeader,
    phone: formData.value.phone,
    merchantCount: 0,
    totalEarned: '0.00',
    status: 'ACTIVE',
  });
  dialogVisible.value = false;
  ElMessage.success('城市分公司授权已生效！');
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
