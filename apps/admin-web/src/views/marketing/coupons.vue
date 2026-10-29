<template>
  <div class="coupons-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>优惠券与卡券中心 (Marketing Coupons)</h3>
          <p class="sub-tip">管理平台通用立减券、商家专属折扣券，支持满额门槛与一客一码防伪核销</p>
        </div>
        <el-button type="primary" @click="openCreateDialog">发放新优惠券</el-button>
      </div>

      <el-table :data="couponList" stripe style="width: 100%">
        <el-table-column prop="title" label="卡券名称" min-width="180" />
        <el-table-column prop="couponType" label="类型" width="120">
          <template #default="{ row }">
            <el-tag :type="row.couponType === 'CASH' ? 'danger' : 'primary'">
              {{ row.couponType === 'CASH' ? '立减现金券' : '百分比折扣券' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="券面权益" width="130">
          <template #default="{ row }">
            <span style="font-weight: 700; color: #f56c6c; font-size: 15px">
              {{ row.couponType === 'CASH' ? '￥' + row.faceValue : (Number(row.faceValue) * 10).toFixed(1) + ' 折' }}
            </span>
          </template>
        </el-table-column>
        <el-table-column prop="minOrderAmount" label="使用门槛" width="130">
          <template #default="{ row }">
            {{ Number(row.minOrderAmount) > 0 ? '满 ￥' + row.minOrderAmount : '无门槛' }}
          </template>
        </el-table-column>
        <el-table-column label="发放 / 核销情况" width="180">
          <template #default="{ row }">
            <div>已领: {{ row.issuedCount }} / {{ row.totalQuota === 0 ? '无限' : row.totalQuota }}</div>
            <el-progress
              :percentage="row.totalQuota === 0 ? 50 : Math.round((row.usedCount / (row.issuedCount || 1)) * 100)"
              :status="row.usedCount > 0 ? 'success' : undefined"
              :stroke-width="6"
            />
          </template>
        </el-table-column>
        <el-table-column prop="validity" label="有效周期" min-width="160" />
        <el-table-column prop="status" label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.status === 'ACTIVE' ? 'success' : 'info'">
              {{ row.status === 'ACTIVE' ? '生效中' : '已停发' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="150" fixed="right">
          <template #default="{ row }">
            <el-button
              type="primary"
              link
              size="small"
              @click="toggleStatus(row)"
            >
              {{ row.status === 'ACTIVE' ? '停发下架' : '重新启用' }}
            </el-button>
            <el-button type="danger" link size="small" @click="deleteCoupon(row.id)">
              作废
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <el-dialog v-model="dialogVisible" title="发放新营销优惠券" width="550px">
      <el-form :model="formData" label-width="120px">
        <el-form-item label="卡券标题" required>
          <el-input v-model="formData.title" placeholder="如：同城新会员专享 10元 满减券" />
        </el-form-item>
        <el-form-item label="卡券类型" required>
          <el-radio-group v-model="formData.couponType">
            <el-radio label="CASH">立减现金券</el-radio>
            <el-radio label="DISCOUNT">折扣券 (如 0.85 = 85折)</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item :label="formData.couponType === 'CASH' ? '立减面额(元)' : '折扣比率'" required>
          <el-input v-model="formData.faceValue" placeholder="例如：10.00 或 0.85" />
        </el-form-item>
        <el-form-item label="使用门槛(元)">
          <el-input v-model="formData.minOrderAmount" placeholder="订单实付满多少可用，0为无门槛" />
        </el-form-item>
        <el-form-item label="发放总限量">
          <el-input-number v-model="formData.totalQuota" :min="0" :max="999999" />
          <span style="font-size: 12px; color: #909399; margin-left: 10px">0 表示不限限量</span>
        </el-form-item>
        <el-form-item label="领券有效天数">
          <el-input-number v-model="formData.validDays" :min="1" :max="365" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="saveCoupon">确认发放上线</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';

const couponList = ref([
  {
    id: 'c-1',
    title: '全城通用开业大礼包立减券',
    couponType: 'CASH',
    faceValue: '15.00',
    minOrderAmount: '50.00',
    totalQuota: 500,
    issuedCount: 230,
    usedCount: 118,
    validity: '自领取后 7 天有效',
    status: 'ACTIVE',
  },
  {
    id: 'c-2',
    title: '烘焙饮品夏日畅饮 8.8 折专享券',
    couponType: 'DISCOUNT',
    faceValue: '0.88',
    minOrderAmount: '30.00',
    totalQuota: 1000,
    issuedCount: 654,
    usedCount: 420,
    validity: '自领取后 15 天有效',
    status: 'ACTIVE',
  },
  {
    id: 'c-3',
    title: '到店预约全身美容 SPA 抵用券',
    couponType: 'CASH',
    faceValue: '50.00',
    minOrderAmount: '150.00',
    totalQuota: 200,
    issuedCount: 180,
    usedCount: 96,
    validity: '自领取后 30 天有效',
    status: 'ACTIVE',
  },
]);

const dialogVisible = ref(false);
const formData = ref({
  title: '',
  couponType: 'CASH',
  faceValue: '10.00',
  minOrderAmount: '30.00',
  totalQuota: 100,
  validDays: 7,
});

function openCreateDialog() {
  formData.value = {
    title: '',
    couponType: 'CASH',
    faceValue: '10.00',
    minOrderAmount: '30.00',
    totalQuota: 100,
    validDays: 7,
  };
  dialogVisible.value = true;
}

function toggleStatus(row: any) {
  row.status = row.status === 'ACTIVE' ? 'DISABLED' : 'ACTIVE';
  ElMessage.success(`卡券状态已更新为: ${row.status === 'ACTIVE' ? '生效中' : '已下架'}`);
}

function deleteCoupon(id: string) {
  ElMessageBox.confirm('确定废弃该卡券吗？已领取的会员将不可继续使用。', '作废提示').then(() => {
    couponList.value = couponList.value.filter((c) => c.id !== id);
    ElMessage.success('卡券已成功作废');
  });
}

function saveCoupon() {
  if (!formData.value.title) {
    ElMessage.error('请填写卡券标题');
    return;
  }
  couponList.value.unshift({
    id: 'c-' + Date.now(),
    title: formData.value.title,
    couponType: formData.value.couponType,
    faceValue: formData.value.faceValue,
    minOrderAmount: formData.value.minOrderAmount,
    totalQuota: formData.value.totalQuota,
    issuedCount: 0,
    usedCount: 0,
    validity: `自领取后 ${formData.value.validDays} 天有效`,
    status: 'ACTIVE',
  });
  dialogVisible.value = false;
  ElMessage.success('新优惠券已发布上线！');
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
