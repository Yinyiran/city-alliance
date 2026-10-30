<template>
  <div class="videos-container">
    <el-card shadow="never">
      <!-- 头部操作区 -->
      <div class="header-action">
        <div>
          <h3>全平台短视频带货流与内容审核中枢 (Short Video & Shoppable Feeds)</h3>
          <p class="sub-tip">
            监管全网实体主理人探店与商品视频流，审核挂载带货卡片，设置 30 秒看播防刷打点积分奖励
          </p>
        </div>
        <div class="btn-group">
          <el-button type="primary" :icon="Plus" @click="openCreateDialog">
            上传/发布新视频
          </el-button>
          <el-button :icon="Refresh" @click="fetchVideos">刷新</el-button>
        </div>
      </div>

      <!-- 筛选栏 -->
      <el-form :inline="true" :model="queryForm" class="filter-form">
        <el-form-item label="视频标题">
          <el-input
            v-model="queryForm.keyword"
            placeholder="搜索探店/商品视频标题"
            clearable
            style="width: 220px"
            @keyup.enter="handleSearch"
          />
        </el-form-item>
        <el-form-item label="审核状态">
          <el-select v-model="queryForm.status" placeholder="全部状态" clearable style="width: 140px">
            <el-option label="全部状态" value="" />
            <el-option label="已上架/通过 (APPROVED)" value="APPROVED" />
            <el-option label="待审核 (PENDING)" value="PENDING" />
            <el-option label="已驳回 (REJECTED)" value="REJECTED" />
            <el-option label="已下架 (OFFLINE)" value="OFFLINE" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :icon="Search" @click="handleSearch">查询</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <!-- 视频列表表格 -->
      <el-table v-loading="loading" :data="videoList" stripe border style="width: 100%">
        <el-table-column label="视频内容" min-width="260">
          <template #default="{ row }">
            <div class="video-info-cell">
              <div class="cover-wrapper" @click="previewVideo(row)">
                <img :src="row.coverUrl" class="video-cover" alt="封面" />
                <div class="play-overlay">
                  <el-icon><VideoPlay /></el-icon>
                </div>
              </div>
              <div class="video-text">
                <span class="video-title">{{ row.title }}</span>
                <span class="video-url-hint">{{ row.videoUrl }}</span>
              </div>
            </div>
          </template>
        </el-table-column>

        <el-table-column label="挂载带货商品" width="180">
          <template #default="{ row }">
            <div v-if="row.goodsName" class="goods-badge">
              <el-tag size="small" type="success">🛒 带货</el-tag>
              <div style="font-size: 12px; font-weight: 600; margin-top: 4px">{{ row.goodsName }}</div>
              <div v-if="row.goodsPrice" style="font-size: 12px; color: #f56c6c">￥{{ row.goodsPrice }}</div>
            </div>
            <span v-else style="font-size: 12px; color: #909399">纯内容探店 (未挂载)</span>
          </template>
        </el-table-column>

        <el-table-column label="30秒看播时钟奖励" width="170">
          <template #default="{ row }">
            <el-tag size="small" type="warning" effect="light">
              时钟: {{ row.rewardSeconds }} 秒
            </el-tag>
            <div style="font-size: 12px; color: #e6a23c; margin-top: 4px">
              奖励: +{{ row.rewardPoints }} 积分
            </div>
          </template>
        </el-table-column>

        <el-table-column label="互动大盘" width="160">
          <template #default="{ row }">
            <div style="font-size: 12px; line-height: 1.8">
              <span>👀 播放: {{ row.viewCount }}</span><br />
              <span>❤️ 点赞: {{ row.likeCount }}</span><br />
              <span>↗️ 分享: {{ row.shareCount }}</span>
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="status" label="状态" width="110">
          <template #default="{ row }">
            <el-tag :type="formatStatusTag(row.status)">
              {{ formatStatusText(row.status) }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column label="运营管理操作" width="220" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" size="small" link @click="previewVideo(row)">
              预览播放
            </el-button>

            <template v-if="row.status === 'PENDING'">
              <el-button type="success" size="small" link @click="handleAudit(row, true)">
                审核通过
              </el-button>
              <el-button type="danger" size="small" link @click="handleAudit(row, false)">
                驳回
              </el-button>
            </template>

            <template v-else-if="row.status === 'APPROVED'">
              <el-button type="warning" size="small" link @click="toggleStatus(row, 'OFFLINE')">
                违规下架
              </el-button>
            </template>

            <template v-else-if="row.status === 'OFFLINE'">
              <el-button type="success" size="small" link @click="toggleStatus(row, 'APPROVED')">
                重新上架
              </el-button>
            </template>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页栏 -->
      <div class="pagination-bar">
        <el-pagination
          v-model:current-page="queryForm.page"
          v-model:page-size="queryForm.pageSize"
          :total="total"
          :page-sizes="[10, 20, 50]"
          layout="total, sizes, prev, pager, next"
          @size-change="fetchVideos"
          @current-change="fetchVideos"
        />
      </div>
    </el-card>

    <!-- 弹窗：视频播放预览 -->
    <el-dialog v-model="previewVisible" :title="activeVideo?.title || '视频播放预览'" width="420px">
      <div class="player-container">
        <video
          v-if="previewVisible"
          :src="activeVideo?.videoUrl"
          controls
          autoplay
          style="width: 100%; border-radius: 8px; max-height: 520px; background: #000"
        />
        <div v-if="activeVideo?.goodsName" class="floating-goods-card">
          <span class="card-tag">挂载推荐</span>
          <span class="card-name">{{ activeVideo.goodsName }}</span>
          <span v-if="activeVideo.goodsPrice" class="card-price">￥{{ activeVideo.goodsPrice }}</span>
        </div>
      </div>
    </el-dialog>

    <!-- 弹窗：上传/发布短视频 -->
    <el-dialog v-model="createDialogVisible" title="发布探店与带货短视频" width="560px">
      <el-form :model="createForm" label-width="120px">
        <el-form-item label="视频标题" required>
          <el-input v-model="createForm.title" placeholder="如：鲜活现烤招牌体验" />
        </el-form-item>
        <el-form-item label="视频资源URL" required>
          <el-input v-model="createForm.videoUrl" placeholder="MP4 视频地址" />
        </el-form-item>
        <el-form-item label="封面图片URL">
          <el-input v-model="createForm.coverUrl" placeholder="视频封面图片地址" />
        </el-form-item>
        <el-form-item label="挂载带货商品">
          <el-input v-model="createForm.goodsName" placeholder="商品名称（选填）" />
        </el-form-item>
        <el-form-item label="带货单价 (元)">
          <el-input-number v-model="createForm.goodsPrice" :min="0" :precision="2" style="width: 100%" />
        </el-form-item>
        <el-form-item label="看播奖励时限 (秒)">
          <el-input-number v-model="createForm.rewardSeconds" :min="5" :max="120" style="width: 100%" />
        </el-form-item>
        <el-form-item label="奖励积分数">
          <el-input-number v-model="createForm.rewardPoints" :min="1" :max="100" style="width: 100%" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="createDialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="submitting" @click="submitCreate">立即发布</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Search, Refresh, Plus, VideoPlay } from '@element-plus/icons-vue';
import request from '../../utils/request';

const loading = ref(false);
const videoList = ref<any[]>([]);
const total = ref(0);

const queryForm = reactive({
  keyword: '',
  status: '',
  page: 1,
  pageSize: 20,
});

// 预览弹窗
const previewVisible = ref(false);
const activeVideo = ref<any>(null);

// 发布弹窗
const createDialogVisible = ref(false);
const submitting = ref(false);
const createForm = reactive({
  title: '',
  videoUrl: '',
  coverUrl: '',
  goodsName: '',
  goodsPrice: 0,
  rewardSeconds: 30,
  rewardPoints: 5,
});

async function fetchVideos() {
  loading.value = true;
  try {
    const res: any = await request.get('/videos', {
      params: {
        page: queryForm.page,
        pageSize: queryForm.pageSize,
        keyword: queryForm.keyword || undefined,
        status: queryForm.status || undefined,
      },
    });
    if (res?.data) {
      videoList.value = res.data.items || [];
      total.value = res.data.total || 0;
    }
  } catch (err: any) {
    ElMessage.error(err.message || '获取视频列表失败');
  } finally {
    loading.value = false;
  }
}

function handleSearch() {
  queryForm.page = 1;
  fetchVideos();
}

function handleReset() {
  queryForm.keyword = '';
  queryForm.status = '';
  queryForm.page = 1;
  fetchVideos();
}

function previewVideo(row: any) {
  activeVideo.value = row;
  previewVisible.value = true;
}

function openCreateDialog() {
  createForm.title = '';
  createForm.videoUrl = '';
  createForm.coverUrl = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80';
  createForm.goodsName = '';
  createForm.goodsPrice = 0;
  createForm.rewardSeconds = 30;
  createForm.rewardPoints = 5;
  createDialogVisible.value = true;
}

async function submitCreate() {
  if (!createForm.title.trim()) {
    return ElMessage.warning('请输入视频标题');
  }
  if (!createForm.videoUrl.trim()) {
    return ElMessage.warning('请输入视频播放URL');
  }
  submitting.value = true;
  try {
    await request.post('/videos', createForm);
    ElMessage.success('短视频发布成功并自动上线！');
    createDialogVisible.value = false;
    fetchVideos();
  } catch (err: any) {
    ElMessage.error(err.message || '发布失败');
  } finally {
    submitting.value = false;
  }
}

async function handleAudit(row: any, approved: boolean) {
  try {
    await request.post(`/videos/${row.id}/audit`, { approved });
    ElMessage.success(approved ? '视频已审核通过' : '视频已驳回');
    fetchVideos();
  } catch (err: any) {
    ElMessage.error(err.message || '审核失败');
  }
}

async function toggleStatus(row: any, nextStatus: string) {
  const tip = nextStatus === 'OFFLINE' ? '确定将该视频违规下架吗？' : '确定重新上架该视频吗？';
  try {
    await ElMessageBox.confirm(tip, '状态变更确认', { type: 'warning' });
    await request.put(`/videos/${row.id}/status`, { status: nextStatus });
    ElMessage.success(nextStatus === 'OFFLINE' ? '视频已下架' : '视频已重新上线');
    fetchVideos();
  } catch (err) {
    // cancel
  }
}

function formatStatusText(status: string) {
  switch (status) {
    case 'APPROVED': return '正常上架';
    case 'PENDING': return '待审核';
    case 'REJECTED': return '审核驳回';
    case 'OFFLINE': return '已违规下架';
    default: return status || '-';
  }
}

function formatStatusTag(status: string) {
  switch (status) {
    case 'APPROVED': return 'success';
    case 'PENDING': return 'warning';
    case 'REJECTED': return 'danger';
    case 'OFFLINE': return 'info';
    default: return 'info';
  }
}

onMounted(() => {
  fetchVideos();
});
</script>

<style scoped>
.videos-container {
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
.filter-form {
  margin-bottom: 12px;
}
.video-info-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}
.cover-wrapper {
  position: relative;
  width: 72px;
  height: 96px;
  border-radius: 6px;
  overflow: hidden;
  cursor: pointer;
  flex-shrink: 0;
  background: #1e222d;
}
.video-cover {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.play-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.35);
  color: #fff;
  font-size: 24px;
  opacity: 0.85;
}
.cover-wrapper:hover .play-overlay {
  opacity: 1;
  background: rgba(0, 0, 0, 0.5);
}
.video-text {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.video-title {
  font-weight: 600;
  color: #303133;
}
.video-url-hint {
  font-size: 11px;
  color: #909399;
  font-family: monospace;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 280px;
}
.pagination-bar {
  margin-top: 16px;
  display: flex;
  justify-content: flex-end;
}
.player-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.floating-goods-card {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background: #f0f9eb;
  border: 1px solid #e1f3d8;
  border-radius: 6px;
}
.card-tag {
  font-size: 11px;
  color: #67c23a;
  font-weight: 700;
}
.card-name {
  font-size: 13px;
  font-weight: 600;
  color: #303133;
  flex: 1;
}
.card-price {
  font-size: 13px;
  font-weight: 700;
  color: #f56c6c;
}
</style>
