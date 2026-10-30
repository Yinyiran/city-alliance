<template>
  <div class="category-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>商品分类目录树 (Category Hierarchy)</h3>
          <p class="sub-tip">支持多级商品层级导航，绑定主理人行业类目与首页楼层映射</p>
        </div>
        <div class="btn-group">
          <el-button type="primary" :icon="Plus" @click="openAddDialog(null)">新增一级分类</el-button>
          <el-button :icon="Refresh" @click="fetchCategories">刷新分类</el-button>
        </div>
      </div>

      <el-table
        v-loading="loading"
        :data="categoryTree"
        row-key="id"
        border
        default-expand-all
        :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
        style="width: 100%"
      >
        <el-table-column prop="name" label="类目名称" min-width="220">
          <template #default="{ row }">
            <span style="font-weight: 600; font-size: 14px">{{ row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="icon" label="类目图标" width="120" align="center">
          <template #default="{ row }">
            <span style="font-size: 20px">{{ row.icon || '📁' }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="sortOrder" label="排序权重" width="120" align="center">
          <template #default="{ row }">
            <el-tag size="small">{{ row.sortOrder }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="isVisible" label="前端可见" width="120" align="center">
          <template #default="{ row }">
            <el-switch
              v-model="row.isVisible"
              active-color="#13ce66"
              @change="(val: any) => handleToggleVisible(row, val)"
            />
          </template>
        </el-table-column>
        <el-table-column label="快捷操作" width="220" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" link size="small" @click="openAddDialog(row)">
              添加子类目
            </el-button>
            <el-button type="primary" link size="small" @click="handleEdit(row)">
              编辑
            </el-button>
            <el-button type="danger" link size="small" @click="handleDelete(row)">
              删除
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 新增/编辑分类对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="isEdit ? '编辑商品分类' : '新增商品分类'"
      width="500px"
    >
      <el-form :model="formData" label-width="100px">
        <el-form-item label="上级分类">
          <el-input :value="parentCategoryName" disabled />
        </el-form-item>
        <el-form-item label="类目名称" required>
          <el-input v-model="formData.name" placeholder="例如：同城精选烘焙 / 宠物洗护" clearable />
        </el-form-item>
        <el-form-item label="类目图标">
          <el-input v-model="formData.icon" placeholder="输入 Emoji 或 图标URL (如 ☕ / 🐕)" clearable />
        </el-form-item>
        <el-form-item label="排序权重">
          <el-input-number v-model="formData.sortOrder" :min="0" :max="999" />
        </el-form-item>
        <el-form-item label="前端显示">
          <el-switch v-model="formData.isVisible" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="saving" @click="saveCategory">保存提交</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Plus, Refresh } from '@element-plus/icons-vue';
import request from '../../utils/request';

interface CategoryNode {
  id: string;
  parentId: string | null;
  name: string;
  icon: string;
  sortOrder: number;
  isVisible: boolean;
  children?: CategoryNode[];
}

const loading = ref(false);
const rawCategories = ref<any[]>([]);
const categoryTree = ref<CategoryNode[]>([]);

const dialogVisible = ref(false);
const isEdit = ref(false);
const saving = ref(false);
const currentParentCategory = ref<any>(null);
const editingId = ref('');

const formData = reactive({
  name: '',
  icon: '📦',
  sortOrder: 0,
  isVisible: true,
});

const parentCategoryName = computed(() => {
  if (!currentParentCategory.value) return '顶级根分类 (一级)';
  return currentParentCategory.value.name;
});

function buildTree(items: any[]): CategoryNode[] {
  const map = new Map<string, CategoryNode>();
  const roots: CategoryNode[] = [];

  for (const item of items) {
    map.set(item.id, {
      id: item.id,
      parentId: item.parentId || null,
      name: item.name,
      icon: item.icon || '📁',
      sortOrder: item.sortOrder || 0,
      isVisible: item.isVisible !== false,
      children: [],
    });
  }

  for (const item of items) {
    const node = map.get(item.id)!;
    if (item.parentId && map.has(item.parentId)) {
      map.get(item.parentId)!.children!.push(node);
    } else {
      roots.push(node);
    }
  }

  return roots;
}

async function fetchCategories() {
  loading.value = true;
  try {
    const res: any = await request.get('/catalog/categories');
    if (res.code === 200 && res.data) {
      rawCategories.value = res.data;
      categoryTree.value = buildTree(res.data);
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取商品分类失败');
  } finally {
    loading.value = false;
  }
}

function openAddDialog(parent: any) {
  isEdit.value = false;
  editingId.value = '';
  currentParentCategory.value = parent;
  formData.name = '';
  formData.icon = '✨';
  formData.sortOrder = (rawCategories.value.length + 1) * 10;
  formData.isVisible = true;
  dialogVisible.value = true;
}

function handleEdit(row: any) {
  isEdit.value = true;
  editingId.value = row.id;
  currentParentCategory.value = rawCategories.value.find((c) => c.id === row.parentId) || null;
  formData.name = row.name;
  formData.icon = row.icon;
  formData.sortOrder = row.sortOrder;
  formData.isVisible = row.isVisible;
  dialogVisible.value = true;
}

async function handleToggleVisible(row: any, val: boolean) {
  try {
    await request.put(`/catalog/categories/${row.id}`, { isVisible: val });
    ElMessage.success('可见性状态已更新');
  } catch (e) {
    row.isVisible = !val;
  }
}

async function handleDelete(row: any) {
  try {
    await ElMessageBox.confirm(`确认删除分类【${row.name}】吗？若包含子分类或关联商品将受到影响。`, '删除确认', {
      type: 'warning',
    });
    const res: any = await request.delete(`/catalog/categories/${row.id}`);
    if (res.code === 200) {
      ElMessage.success('分类已删除');
      fetchCategories();
    }
  } catch (e) {
    // cancel
  }
}

async function saveCategory() {
  if (!formData.name.trim()) {
    ElMessage.error('请输入分类名称');
    return;
  }
  saving.value = true;
  try {
    if (isEdit.value) {
      const res: any = await request.put(`/catalog/categories/${editingId.value}`, {
        name: formData.name.trim(),
        icon: formData.icon.trim(),
        sortOrder: formData.sortOrder,
        isVisible: formData.isVisible,
      });
      if (res.code === 200) {
        ElMessage.success('商品分类更新成功');
        dialogVisible.value = false;
        fetchCategories();
      }
    } else {
      const res: any = await request.post('/catalog/categories', {
        parentId: currentParentCategory.value ? currentParentCategory.value.id : null,
        name: formData.name.trim(),
        icon: formData.icon.trim(),
        sortOrder: formData.sortOrder,
        isVisible: formData.isVisible,
      });
      if (res.code === 200) {
        ElMessage.success('新增商品分类成功');
        dialogVisible.value = false;
        fetchCategories();
      }
    }
  } catch (err: any) {
    // handled
  } finally {
    saving.value = false;
  }
}

onMounted(() => {
  fetchCategories();
});
</script>

<style scoped>
.category-container {
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
</style>
