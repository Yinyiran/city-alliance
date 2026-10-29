<template>
  <div class="category-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>商品分类目录树 (Category Hierarchy)</h3>
          <p class="sub-tip">支持多级商品层级导航，绑定主理人行业类目与首页楼层映射</p>
        </div>
        <el-button type="primary" @click="openAddDialog(null)">新增一级分类</el-button>
      </div>

      <el-table
        :data="categoryTree"
        row-key="id"
        border
        default-expand-all
        :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
        style="width: 100%"
      >
        <el-table-column prop="name" label="类目名称" min-width="220" />
        <el-table-column prop="icon" label="类目图标" width="120">
          <template #default="{ row }">
            <span style="font-size: 20px">{{ row.icon || '📁' }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="sortOrder" label="排序权重" width="120">
          <template #default="{ row }">
            <el-tag size="small">{{ row.sortOrder }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="isVisible" label="前端可见" width="120">
          <template #default="{ row }">
            <el-switch v-model="row.isVisible" active-color="#13ce66" />
          </template>
        </el-table-column>
        <el-table-column label="快捷操作" width="220" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" link size="small" @click="openAddDialog(row.id)">
              添加子类目
            </el-button>
            <el-button type="primary" link size="small" @click="handleEdit(row)">
              编辑
            </el-button>
            <el-button type="danger" link size="small" @click="handleDelete(row.id)">
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
          <el-input v-model="formData.name" placeholder="例如：同城精选烘焙 / 宠物洗护" />
        </el-form-item>
        <el-form-item label="类目图标">
          <el-input v-model="formData.icon" placeholder="输入 Emoji 或 图标URL (如 ☕ / 🐕)" />
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
        <el-button type="primary" @click="saveCategory">保存提交</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';

interface CategoryNode {
  id: string;
  parentId: string | null;
  name: string;
  icon: string;
  sortOrder: number;
  isVisible: boolean;
  children?: CategoryNode[];
}

const categoryTree = ref<CategoryNode[]>([
  {
    id: 'cat-1',
    parentId: null,
    name: '餐饮美食 (到店/外卖)',
    icon: '🍔',
    sortOrder: 10,
    isVisible: true,
    children: [
      { id: 'cat-1-1', parentId: 'cat-1', name: '精品咖啡 & 烘焙', icon: '☕', sortOrder: 1, isVisible: true },
      { id: 'cat-1-2', parentId: 'cat-1', name: '同城特色小吃', icon: '🍜', sortOrder: 2, isVisible: true },
    ],
  },
  {
    id: 'cat-2',
    parentId: null,
    name: '丽人与到店预约',
    icon: '✂️',
    sortOrder: 20,
    isVisible: true,
    children: [
      { id: 'cat-2-1', parentId: 'cat-2', name: '美发沙龙', icon: '💇', sortOrder: 1, isVisible: true },
      { id: 'cat-2-2', parentId: 'cat-2', name: '美甲美睫', icon: '💅', sortOrder: 2, isVisible: true },
    ],
  },
  {
    id: 'cat-3',
    parentId: null,
    name: '宠物生活',
    icon: '🐾',
    sortOrder: 30,
    isVisible: true,
    children: [
      { id: 'cat-3-1', parentId: 'cat-3', name: '宠物洗护美容', icon: '🛁', sortOrder: 1, isVisible: true },
      { id: 'cat-3-2', parentId: 'cat-3', name: '特级粮品 (全国快递)', icon: '🥫', sortOrder: 2, isVisible: true },
    ],
  },
]);

const dialogVisible = ref(false);
const isEdit = ref(false);
const currentParentId = ref<string | null>(null);

const formData = ref({
  id: '',
  name: '',
  icon: '📦',
  sortOrder: 0,
  isVisible: true,
});

const parentCategoryName = computed(() => {
  if (!currentParentId.value) return '顶级根分类';
  const found = categoryTree.value.find((c) => c.id === currentParentId.value);
  return found ? found.name : '上级分类';
});

function openAddDialog(parentId: string | null) {
  isEdit.value = false;
  currentParentId.value = parentId;
  formData.value = {
    id: 'cat-' + Date.now(),
    name: '',
    icon: '✨',
    sortOrder: 10,
    isVisible: true,
  };
  dialogVisible.value = true;
}

function handleEdit(row: CategoryNode) {
  isEdit.value = true;
  currentParentId.value = row.parentId;
  formData.value = { ...row };
  dialogVisible.value = true;
}

function handleDelete(id: string) {
  ElMessageBox.confirm('确认删除该分类吗？其下子分类也将被联动解绑。', '警告', {
    type: 'warning',
  }).then(() => {
    ElMessage.success('类目已成功移除');
  });
}

function saveCategory() {
  if (!formData.value.name) {
    ElMessage.error('请填写分类名称');
    return;
  }
  dialogVisible.value = false;
  ElMessage.success('商品分类保存成功！');
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
