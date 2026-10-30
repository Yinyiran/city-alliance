<template>
  <div class="goods-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>商品目录与 SPU/SKU 交叉规格矩阵 (Product Catalog)</h3>
          <p class="sub-tip">管理全渠道商品 SPU、分类归属、履约方式及独立价格库存规格 SKU 矩阵</p>
        </div>
        <div class="action-btns">
          <el-button type="primary" icon="Plus" @click="openCreateDialog">发布新商品</el-button>
          <el-button icon="Refresh" @click="fetchGoods">刷新数据</el-button>
        </div>
      </div>

      <!-- 搜索过滤栏 -->
      <div class="filter-bar">
        <el-input
          v-model="query.keyword"
          placeholder="搜索商品名称 / SPU"
          clearable
          style="width: 220px; margin-right: 12px"
          @keyup.enter="handleSearch"
        />

        <el-select
          v-model="query.tradeType"
          placeholder="履约方式 (全部)"
          clearable
          style="width: 150px; margin-right: 12px"
          @change="handleSearch"
        >
          <el-option label="全部履约方式" value="" />
          <el-option label="全国快递 (EXPRESS)" value="EXPRESS" />
          <el-option label="到店自提 (PICKUP)" value="PICKUP" />
          <el-option label="到店预约 (APPOINTMENT)" value="APPOINTMENT" />
          <el-option label="同城外卖 (TAKEOUT)" value="TAKEOUT" />
        </el-select>

        <el-select
          v-model="query.categoryId"
          placeholder="商品分类 (全部)"
          clearable
          style="width: 160px; margin-right: 12px"
          @change="handleSearch"
        >
          <el-option label="全部分类" value="" />
          <el-option
            v-for="cat in categoryOptions"
            :key="cat.id"
            :label="cat.name"
            :value="cat.id"
          />
        </el-select>

        <el-select
          v-model="query.status"
          placeholder="商品状态 (全部)"
          clearable
          style="width: 140px; margin-right: 12px"
          @change="handleSearch"
        >
          <el-option label="全部状态" value="" />
          <el-option label="上架中 (ON_SALE)" value="ON_SALE" />
          <el-option label="已下架 (OFF_SALE)" value="OFF_SALE" />
        </el-select>

        <el-button type="primary" icon="Search" @click="handleSearch">查询</el-button>
        <el-button @click="resetQuery">重置</el-button>
      </div>

      <!-- 商品数据列表 -->
      <el-table :data="goodsList" v-loading="loading" stripe style="width: 100%; margin-top: 16px">
        <el-table-column label="商品封面" width="85">
          <template #default="{ row }">
            <el-image
              :src="getImageUrl(row.coverImage)"
              style="width: 54px; height: 54px; border-radius: 6px; border: 1px solid #ebeef5"
              fit="cover"
            >
              <template #error>
                <div class="image-placeholder">📦</div>
              </template>
            </el-image>
          </template>
        </el-table-column>

        <el-table-column label="商品名称 / SPU" min-width="220">
          <template #default="{ row }">
            <div style="font-weight: 600; color: #303133; font-size: 14px">{{ row.name }}</div>
            <div style="display: flex; align-items: center; gap: 6px; margin-top: 4px">
              <el-tag v-if="row.categoryName" size="small" effect="plain" type="info">
                {{ row.categoryName }}
              </el-tag>
              <span v-if="row.shopName" style="font-size: 11px; color: #909399">
                🏪 {{ row.shopName }}
              </span>
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="tradeType" label="履约类型" width="130">
          <template #default="{ row }">
            <el-tag :type="getTradeTypeTag(row.tradeType)" size="small">
              {{ getTradeTypeName(row.tradeType) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column prop="basePrice" label="基准价格" width="110">
          <template #default="{ row }">
            <span style="font-weight: 700; color: #f56c6c; font-size: 14px">￥{{ row.basePrice }}</span>
          </template>
        </el-table-column>

        <el-table-column label="规格矩阵 (SKU 交叉)" min-width="240">
          <template #default="{ row }">
            <div v-if="row.skus && row.skus.length > 0" class="sku-container">
              <div v-for="sku in row.skus.slice(0, 3)" :key="sku.id" class="sku-tag-item">
                <span class="sku-spec">{{ formatSpecs(sku.specValues) }}</span>
                <span class="sku-price">￥{{ sku.price }}</span>
                <span class="sku-stock">(库存: {{ sku.stock }})</span>
              </div>
              <div v-if="row.skus.length > 3" style="font-size: 11px; color: #409eff; margin-top: 2px">
                共 {{ row.skus.length }} 组规格矩阵...
              </div>
            </div>
            <span v-else style="color: #c0c4cc; font-size: 12px">单品无多规格</span>
          </template>
        </el-table-column>

        <el-table-column label="状态" width="100" align="center">
          <template #default="{ row }">
            <el-tag :type="row.status === 'ON_SALE' ? 'success' : 'info'" size="small">
              {{ row.status === 'ON_SALE' ? '上架中' : '已下架' }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button link type="primary" size="small" @click="openEditDialog(row)">编辑</el-button>
            <el-button
              link
              :type="row.status === 'ON_SALE' ? 'warning' : 'success'"
              size="small"
              @click="toggleStatus(row)"
            >
              {{ row.status === 'ON_SALE' ? '下架' : '上架' }}
            </el-button>
            <el-popconfirm
              title="确定要彻底删除该商品吗？此操作将同步级联删除其所有SKU矩阵。"
              confirm-button-text="确认删除"
              cancel-button-text="取消"
              confirm-button-type="danger"
              @confirm="deleteProduct(row)"
            >
              <template #reference>
                <el-button link type="danger" size="small">删除</el-button>
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
          @size-change="fetchGoods"
          @current-change="fetchGoods"
        />
      </div>
    </el-card>

    <!-- 发布 / 编辑商品对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="isEdit ? '编辑商品信息与规格矩阵' : '发布全新商品 (SPU & SKU)'"
      width="680px"
      destroy-on-close
    >
      <el-form :model="form" label-width="110px" style="padding-right: 20px">
        <el-form-item label="商品名称" required>
          <el-input
            v-model="form.name"
            placeholder="例如：特级挂耳黑咖啡 20包礼盒装"
            maxlength="200"
            show-word-limit
          />
        </el-form-item>

        <el-row :gutter="16">
          <el-col :span="12">
            <el-form-item label="履约方式" required>
              <el-select v-model="form.tradeType" style="width: 100%">
                <el-option label="全国快递 (EXPRESS)" value="EXPRESS" />
                <el-option label="到店自提 (PICKUP)" value="PICKUP" />
                <el-option label="到店预约 (APPOINTMENT)" value="APPOINTMENT" />
                <el-option label="同城外卖 (TAKEOUT)" value="TAKEOUT" />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="商品分类">
              <el-select v-model="form.categoryId" placeholder="请选择商品类目" clearable style="width: 100%">
                <el-option
                  v-for="cat in categoryOptions"
                  :key="cat.id"
                  :label="cat.name"
                  :value="cat.id"
                />
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="16">
          <el-col :span="12">
            <el-form-item label="销售基准价" required>
              <el-input v-model="form.basePrice" placeholder="0.00">
                <template #prefix>￥</template>
              </el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="初始总库存" required>
              <el-input-number v-model="form.baseStock" :min="0" :max="999999" style="width: 100%" />
            </el-form-item>
          </el-col>
        </el-row>

        <el-form-item label="商品主图URL" required>
          <el-input
            v-model="form.coverImage"
            placeholder="请输入图片 URL 或使用默认图"
          />
          <div style="margin-top: 6px; display: flex; align-items: center; gap: 8px">
            <el-button size="small" @click="usePresetImage('coffee')">咖啡图</el-button>
            <el-button size="small" @click="usePresetImage('food')">餐饮图</el-button>
            <el-button size="small" @click="usePresetImage('pet')">宠物服务图</el-button>
            <el-button size="small" @click="usePresetImage('goods')">百货精品图</el-button>
          </div>
        </el-form-item>

        <el-form-item label="商品详情描述">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="3"
            placeholder="请输入商品材质、原产地、规格及售后保障说明..."
          />
        </el-form-item>

        <!-- 规格矩阵 (SKU 配置) -->
        <el-divider content-position="left">多规格交叉矩阵 (SKU Matrix)</el-divider>

        <div style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center">
          <span style="font-size: 13px; color: #606266">
            {{ form.skus.length > 0 ? `当前配置 ${form.skus.length} 组独立价格库存规格` : '当前为单品统一基准价' }}
          </span>
          <el-button size="small" icon="Plus" @click="addCustomSku">添加规格组合</el-button>
        </div>

        <div v-if="form.skus.length > 0" class="sku-edit-list">
          <div v-for="(sku, index) in form.skus" :key="index" class="sku-edit-item">
            <el-input
              v-model="sku.specName"
              placeholder="规格名 (如 分量/颜色)"
              style="width: 130px"
            />
            <el-input
              v-model="sku.specVal"
              placeholder="规格值 (如 500g/红色)"
              style="width: 130px"
            />
            <el-input
              v-model="sku.price"
              placeholder="价格"
              style="width: 110px"
            >
              <template #prefix>￥</template>
            </el-input>
            <el-input-number
              v-model="sku.stock"
              :min="0"
              placeholder="库存"
              style="width: 110px"
            />
            <el-button
              type="danger"
              icon="Delete"
              circle
              size="small"
              @click="removeSku(index)"
            />
          </div>
        </div>
      </el-form>

      <template #footer>
        <span class="dialog-footer">
          <el-button @click="dialogVisible = false">取消</el-button>
          <el-button type="primary" :loading="submitting" @click="submitGoods">
            {{ isEdit ? '确认保存修改' : '立即发布商品并入库' }}
          </el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import request from '../../utils/request';
import { ElMessage } from 'element-plus';
import { Plus, Refresh, Search, Delete } from '@element-plus/icons-vue';

const loading = ref(false);
const submitting = ref(false);
const goodsList = ref<any[]>([]);
const total = ref(0);
const categoryOptions = ref<any[]>([]);

const query = reactive({
  keyword: '',
  tradeType: '',
  status: '',
  categoryId: '',
  page: 1,
  pageSize: 20,
});

const dialogVisible = ref(false);
const isEdit = ref(false);
const editId = ref('');

const form = reactive({
  name: '',
  tradeType: 'EXPRESS',
  categoryId: '',
  basePrice: '49.90',
  baseStock: 100,
  coverImage: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=300',
  description: '',
  skus: [] as { specName: string; specVal: string; price: string; stock: number }[],
});

onMounted(async () => {
  await fetchCategories();
  await fetchGoods();
});

async function fetchCategories() {
  try {
    const res: any = await request.get('/catalog/categories');
    categoryOptions.value = res.data || [];
  } catch (err) {
    console.error('Failed to fetch categories:', err);
  }
}

async function fetchGoods() {
  loading.value = true;
  try {
    const res: any = await request.get('/catalog/goods', { params: query });
    const data = res.data || res;
    goodsList.value = data.items || [];
    total.value = data.total || 0;
  } catch (err) {
    console.error('Failed to fetch goods:', err);
  } finally {
    loading.value = false;
  }
}

function handleSearch() {
  query.page = 1;
  fetchGoods();
}

function resetQuery() {
  query.keyword = '';
  query.tradeType = '';
  query.status = '';
  query.categoryId = '';
  query.page = 1;
  fetchGoods();
}

function getImageUrl(url: string) {
  if (!url) return '';
  if (url.startsWith('http://') || url.startsWith('https://')) return url;
  if (url.startsWith('upload/')) {
    return `https://mall.cmyx.top/${url}`;
  }
  return url;
}

function getTradeTypeTag(type: string) {
  const map: Record<string, string> = {
    PICKUP: 'success',
    APPOINTMENT: 'warning',
    EXPRESS: 'primary',
    TAKEOUT: 'danger',
    QUICK_PAY: 'info',
  };
  return map[type] || 'info';
}

function getTradeTypeName(type: string) {
  const map: Record<string, string> = {
    PICKUP: '到店自提',
    APPOINTMENT: '到店预约',
    EXPRESS: '全国快递',
    TAKEOUT: '同城外卖',
    QUICK_PAY: '线下买单',
  };
  return map[type] || type;
}

function formatSpecs(specs: Record<string, string>) {
  if (!specs || Object.keys(specs).length === 0) return '默认统一规格';
  return Object.entries(specs)
    .map(([k, v]) => `${k}:${v}`)
    .join(' / ');
}

function usePresetImage(type: string) {
  const map: Record<string, string> = {
    coffee: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=300',
    food: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=300',
    pet: 'https://images.unsplash.com/photo-1548767797-d8c844163c4c?w=300',
    goods: 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=300',
  };
  form.coverImage = map[type] || map.coffee;
}

function addCustomSku() {
  form.skus.push({
    specName: '规格',
    specVal: `标准款-${form.skus.length + 1}`,
    price: form.basePrice,
    stock: 50,
  });
}

function removeSku(index: number) {
  form.skus.splice(index, 1);
}

function openCreateDialog() {
  isEdit.value = false;
  editId.value = '';
  form.name = '';
  form.tradeType = 'EXPRESS';
  form.categoryId = categoryOptions.value[0]?.id || '';
  form.basePrice = '49.90';
  form.baseStock = 100;
  form.coverImage = 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=300';
  form.description = '精选品质，产地直发，同城现做支持自提与全国配送保障。';
  form.skus = [];
  dialogVisible.value = true;
}

function openEditDialog(row: any) {
  isEdit.value = true;
  editId.value = row.id;
  form.name = row.name;
  form.tradeType = row.tradeType;
  form.categoryId = row.categoryId || '';
  form.basePrice = row.basePrice;
  form.baseStock = 100;
  form.coverImage = row.coverImage;
  form.description = row.description || '';

  form.skus = (row.skus || []).map((sku: any) => {
    const entries = Object.entries(sku.specValues || {});
    return {
      specName: entries[0]?.[0] || '规格',
      specVal: entries[0]?.[1] || '默认',
      price: sku.price,
      stock: sku.stock,
    };
  });

  dialogVisible.value = true;
}

async function submitGoods() {
  if (!form.name.trim()) {
    ElMessage.warning('商品名称不能为空');
    return;
  }
  if (!form.basePrice || isNaN(Number(form.basePrice)) || Number(form.basePrice) <= 0) {
    ElMessage.warning('请输入有效的销售价格 (大于0)');
    return;
  }

  submitting.value = true;
  try {
    const skuPayload = form.skus.map((s) => ({
      specValues: { [s.specName]: s.specVal },
      price: s.price,
      stock: s.stock,
    }));

    if (isEdit.value) {
      await request.put(`/catalog/goods/${editId.value}`, {
        name: form.name.trim(),
        tradeType: form.tradeType,
        categoryId: form.categoryId || null,
        basePrice: Number(form.basePrice).toFixed(2),
        coverImage: form.coverImage,
        description: form.description,
        skus: skuPayload.length > 0 ? skuPayload : undefined,
      });
      ElMessage.success('商品信息已更新');
    } else {
      await request.post('/catalog/goods', {
        name: form.name.trim(),
        tradeType: form.tradeType,
        categoryId: form.categoryId || null,
        basePrice: Number(form.basePrice).toFixed(2),
        baseStock: form.baseStock,
        coverImage: form.coverImage,
        description: form.description,
        skus: skuPayload.length > 0 ? skuPayload : undefined,
      });
      ElMessage.success('🎉 商品已成功发布入库！');
    }
    dialogVisible.value = false;
    await fetchGoods();
  } catch (err: any) {
    console.error('Submit goods error:', err);
  } finally {
    submitting.value = false;
  }
}

async function toggleStatus(row: any) {
  const newStatus = row.status === 'ON_SALE' ? 'OFF_SALE' : 'ON_SALE';
  try {
    await request.put(`/catalog/goods/${row.id}/status`, { status: newStatus });
    ElMessage.success(`商品已${newStatus === 'ON_SALE' ? '上架销售' : '下架停售'}`);
    row.status = newStatus;
  } catch (err) {
    console.error('Toggle status error:', err);
  }
}

async function deleteProduct(row: any) {
  try {
    await request.delete(`/catalog/goods/${row.id}`);
    ElMessage.success('商品已成功删除');
    await fetchGoods();
  } catch (err) {
    console.error('Delete goods error:', err);
  }
}
</script>

<style scoped>
.goods-container {
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

.image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f7fa;
  font-size: 20px;
}

.sku-container {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.sku-tag-item {
  font-size: 12px;
  line-height: 1.5;
}

.sku-spec {
  color: #606266;
  margin-right: 6px;
}

.sku-price {
  color: #f56c6c;
  font-weight: 600;
  margin-right: 6px;
}

.sku-stock {
  color: #909399;
}

.pagination-bar {
  margin-top: 20px;
  display: flex;
  justify-content: flex-end;
}

.sku-edit-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  background: #fafbfc;
  padding: 10px;
  border-radius: 6px;
  max-height: 200px;
  overflow-y: auto;
}

.sku-edit-item {
  display: flex;
  align-items: center;
  gap: 8px;
}
</style>
