<template>
  <div class="city-config-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>城市分公司站点与门户配置 (City Branch Portal Config)</h3>
          <p class="sub-tip">维护城市运营中心客服电话、同城首页轮播宣传大图及金刚区特色频道导航</p>
        </div>
        <el-button type="primary" :loading="saving" @click="saveConfig">
          保存分站配置
        </el-button>
      </div>

      <el-form label-width="140px" style="max-width: 800px; margin-top: 20px">
        <el-form-item label="分公司名称">
          <el-input :model-value="configForm.name" disabled />
        </el-form-item>
        <el-form-item label="分公司行政代码">
          <el-input :model-value="configForm.code" disabled />
        </el-form-item>
        <el-form-item label="分公司客服电话">
          <el-input v-model="configForm.servicePhone" placeholder="例如：0571-88882026 或 400 服务专线" />
        </el-form-item>
        <el-form-item label="分公司负责人">
          <el-input v-model="configForm.contactPerson" placeholder="负责人姓名" />
        </el-form-item>

        <el-divider content-position="left">同城 C 端首页轮播推荐</el-divider>
        <div v-for="(banner, idx) in configForm.banners" :key="idx" class="banner-row">
          <el-input v-model="banner.title" placeholder="轮播标题" style="width: 200px" />
          <el-input v-model="banner.url" placeholder="图片 URL" style="flex: 1" />
          <el-button type="danger" link @click="removeBanner(idx)">删除</el-button>
        </div>
        <el-button size="small" type="primary" link style="margin-bottom: 20px" @click="addBanner">
          + 添加轮播图
        </el-button>

        <el-divider content-position="left">金刚区特色导航分类</el-divider>
        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 16px">
          <el-tag
            v-for="(cat, idx) in configForm.navCategories"
            :key="idx"
            closable
            size="large"
            @close="removeCategory(idx)"
          >
            {{ cat }}
          </el-tag>
        </div>
        <div style="display: flex; gap: 10px; max-width: 320px">
          <el-input v-model="newCategoryName" placeholder="输入新分类名称" size="small" />
          <el-button size="small" type="primary" @click="addCategory">添加</el-button>
        </div>
      </el-form>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage } from 'element-plus';
import request from '../../utils/request';

const saving = ref(false);
const newCategoryName = ref('');

const configForm = reactive({
  id: '',
  name: '',
  code: '',
  servicePhone: '',
  contactPerson: '',
  banners: [] as { title: string; url: string }[],
  navCategories: [] as string[],
});

async function fetchConfig() {
  try {
    const res: any = await request.get('/city/config');
    if (res?.data) {
      Object.assign(configForm, res.data);
    }
  } catch (err: any) {
    ElMessage.error(err.message || '加载城市配置失败');
  }
}

async function saveConfig() {
  saving.value = true;
  try {
    await request.put('/city/config', configForm);
    ElMessage.success('城市分公司配置保存成功并实时生效！');
  } catch (err: any) {
    ElMessage.error(err.message || '保存失败');
  } finally {
    saving.value = false;
  }
}

function addBanner() {
  configForm.banners.push({ title: '新宣传活动', url: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=1200' });
}

function removeBanner(idx: number) {
  configForm.banners.splice(idx, 1);
}

function addCategory() {
  if (!newCategoryName.value.trim()) return;
  configForm.navCategories.push(newCategoryName.value.trim());
  newCategoryName.value = '';
}

function removeCategory(idx: number) {
  configForm.navCategories.splice(idx, 1);
}

onMounted(() => {
  fetchConfig();
});
</script>

<style scoped>
.city-config-container {
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
.banner-row {
  display: flex;
  gap: 12px;
  align-items: center;
  margin-bottom: 10px;
}
</style>
