<template>
  <div class="freight-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>运费计费规则模板 (Freight Engine Matrix)</h3>
          <p class="sub-tip">支持按件/按重阶梯续件计算，以及满额包邮自动免运费机制</p>
        </div>
        <el-button type="primary" @click="openAddTemplate">新建运费模板</el-button>
      </div>

      <el-table :data="templateList" stripe style="width: 100%">
        <el-table-column prop="name" label="模板名称" min-width="160" />
        <el-table-column prop="chargeType" label="计费模式" width="130">
          <template #default="{ row }">
            <el-tag :type="row.chargeType === 'BY_COUNT' ? 'primary' : 'warning'">
              {{ row.chargeType === 'BY_COUNT' ? '按件数 (Count)' : '按重量 (kg)' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="首件/首重" width="140">
          <template #default="{ row }">
            {{ row.firstQuantity }} 件/kg ➔ ￥{{ row.firstFee }}
          </template>
        </el-table-column>
        <el-table-column label="续件/续重" width="140">
          <template #default="{ row }">
            每 +{{ row.continueQuantity }} ➔ ￥{{ row.continueFee }}
          </template>
        </el-table-column>
        <el-table-column label="满额包邮门槛" width="140">
          <template #default="{ row }">
            <el-tag v-if="row.freeShippingThreshold" type="success" size="small">
              满 ￥{{ row.freeShippingThreshold }} 包邮
            </el-tag>
            <span v-else style="color: #909399">无包邮门槛</span>
          </template>
        </el-table-column>
        <el-table-column prop="createdAt" label="创建时间" width="160" />
        <el-table-column label="操作" width="140" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" link size="small" @click="editTemplate(row)">编辑</el-button>
            <el-button type="danger" link size="small" @click="deleteTemplate(row.id)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <el-dialog v-model="dialogVisible" title="运费计费模板配置" width="550px">
      <el-form :model="formData" label-width="120px">
        <el-form-item label="模板名称" required>
          <el-input v-model="formData.name" placeholder="如：全国江浙沪默认件数模板" />
        </el-form-item>
        <el-form-item label="计费模式" required>
          <el-radio-group v-model="formData.chargeType">
            <el-radio label="BY_COUNT">按件数计费</el-radio>
            <el-radio label="BY_WEIGHT">按重量计费 (kg)</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-row :gutter="10">
          <el-col :span="12">
            <el-form-item label="首件/首重">
              <el-input-number v-model="formData.firstQuantity" :min="1" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="首费金额(元)">
              <el-input v-model="formData.firstFee" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="12">
            <el-form-item label="续件/续重">
              <el-input-number v-model="formData.continueQuantity" :min="1" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="续费金额(元)">
              <el-input v-model="formData.continueFee" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="满额包邮">
          <el-input v-model="formData.freeShippingThreshold" placeholder="留空或0表示不包邮" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="saveTemplate">保存模板</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';

const templateList = ref([
  {
    id: 'tpl-1',
    name: '全国普通快递基础模板',
    chargeType: 'BY_COUNT',
    firstQuantity: 1,
    firstFee: '8.00',
    continueQuantity: 1,
    continueFee: '3.00',
    freeShippingThreshold: '99.00',
    createdAt: '2026-10-28 10:00',
  },
  {
    id: 'tpl-2',
    name: '生鲜特大冷链 (按重量计费)',
    chargeType: 'BY_WEIGHT',
    firstQuantity: 1,
    firstFee: '15.00',
    continueQuantity: 1,
    continueFee: '6.00',
    freeShippingThreshold: '199.00',
    createdAt: '2026-10-28 14:30',
  },
  {
    id: 'tpl-3',
    name: '同城全场任意满额免邮',
    chargeType: 'BY_COUNT',
    firstQuantity: 1,
    firstFee: '6.00',
    continueQuantity: 1,
    continueFee: '2.00',
    freeShippingThreshold: '49.00',
    createdAt: '2026-10-29 09:15',
  },
]);

const dialogVisible = ref(false);
const formData = ref({
  id: '',
  name: '',
  chargeType: 'BY_COUNT',
  firstQuantity: 1,
  firstFee: '8.00',
  continueQuantity: 1,
  continueFee: '3.00',
  freeShippingThreshold: '',
});

function openAddTemplate() {
  formData.value = {
    id: 'tpl-' + Date.now(),
    name: '',
    chargeType: 'BY_COUNT',
    firstQuantity: 1,
    firstFee: '10.00',
    continueQuantity: 1,
    continueFee: '5.00',
    freeShippingThreshold: '',
  };
  dialogVisible.value = true;
}

function editTemplate(row: any) {
  formData.value = { ...row };
  dialogVisible.value = true;
}

function deleteTemplate(id: string) {
  ElMessageBox.confirm('确认删除该运费模板吗？', '提示').then(() => {
    templateList.value = templateList.value.filter((t) => t.id !== id);
    ElMessage.success('运费模板已删除');
  });
}

function saveTemplate() {
  if (!formData.value.name) {
    ElMessage.error('请填写模板名称');
    return;
  }
  dialogVisible.value = false;
  ElMessage.success('运费模板已保存成功');
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
