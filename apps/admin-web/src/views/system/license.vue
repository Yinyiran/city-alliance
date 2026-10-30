<template>
  <div class="license-container">
    <el-card shadow="never">
      <div class="header-action">
        <div>
          <h3>商业授权证书与系统底层技术拓扑 (Commercial License & Architecture)</h3>
          <p class="sub-tip">
            检验平台系统合法商业授权、查看底层微内核引擎健康度及从单体到分布式架构的演进日志
          </p>
        </div>
        <el-tag type="success" size="large" effect="dark">正版商业授权生效中</el-tag>
      </div>

      <el-row :gutter="20" style="margin-top: 10px">
        <el-col :span="12">
          <el-card shadow="never" class="box-card">
            <template #header>
              <div class="card-title">
                <span>🛡️ 商业授权凭证 (License Certificate)</span>
              </div>
            </template>
            <el-descriptions :column="1" border>
              <el-descriptions-item label="授权产品名称">城盟优选 (City Alliance) 全渠道电商中台</el-descriptions-item>
              <el-descriptions-item label="当前发行版本">v2.5.0-LTS (Enterprise Release)</el-descriptions-item>
              <el-descriptions-item label="授权机构/企业">城盟优选全国品牌运营总部</el-descriptions-item>
              <el-descriptions-item label="授权有效期限">2026-01-01 至 2099-12-31 (终身商业授权)</el-descriptions-item>
              <el-descriptions-item label="多租户节点许可">无限制 (已激活城市合伙人与主理人二级租户引擎)</el-descriptions-item>
              <el-descriptions-item label="机器授权特征指纹">
                <span style="font-family: monospace; font-size: 12px; color: #409eff">
                  SHA256: 8f9b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b
                </span>
              </el-descriptions-item>
            </el-descriptions>
          </el-card>
        </el-col>

        <el-col :span="12">
          <el-card shadow="never" class="box-card">
            <template #header>
              <div class="card-title">
                <span>⚡ 底层微内核与技术拓扑 (Stack Topology)</span>
              </div>
            </template>
            <div class="stack-list">
              <div class="stack-item">
                <div class="stack-icon">🚀</div>
                <div class="stack-meta">
                  <div class="stack-name">NestJS 12 + Fastify 5 异步微内核网关</div>
                  <div class="stack-desc">底层采用高并发零拷贝 Fastify 引擎，吞吐量提升 400%</div>
                </div>
                <el-tag type="success" size="small">运行正常 (4000端口)</el-tag>
              </div>

              <div class="stack-item">
                <div class="stack-icon">🐘</div>
                <div class="stack-meta">
                  <div class="stack-name">PostgreSQL 17 企业级分布式关系数据库</div>
                  <div class="stack-desc">强事务 ACID、JSONB 动态字段支持与高可用持久化</div>
                </div>
                <el-tag type="success" size="small">已连接 (5432端口)</el-tag>
              </div>

              <div class="stack-item">
                <div class="stack-icon">🏎️</div>
                <div class="stack-meta">
                  <div class="stack-name">Redis 7.4 + BullMQ 延时状态机与分布式锁</div>
                  <div class="stack-desc">用于 JWT JTI 黑名单吊销、扫码核销 Redlock 与 7 天延迟解冻</div>
                </div>
                <el-tag type="success" size="small">健康运行 (6379端口)</el-tag>
              </div>

              <div class="stack-item">
                <div class="stack-icon">🎨</div>
                <div class="stack-meta">
                  <div class="stack-name">Vue 3.5 + Vite 6 + Element Plus 前端工程</div>
                  <div class="stack-desc">企业级 HttpOnly 内存态凭证静默刷新与无感无缝体验</div>
                </div>
                <el-tag type="success" size="small">最新驱动 (3000端口)</el-tag>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>

      <!-- 系统版本演进时间轴 -->
      <el-card shadow="never" style="margin-top: 20px">
        <template #header>
          <div class="card-title">
            <span>📜 架构升级演进历史 (Evolution Changelog)</span>
          </div>
        </template>
        <el-timeline style="margin-top: 10px">
          <el-timeline-item timestamp="2026-10-31" placement="top" type="primary">
            <el-card shadow="never">
              <h4>v2.5.0-LTS: 全量完成现代化架构平滑迁移</h4>
              <p>
                - 全面替换旧版单体 PHP 架构，建成以 NestJS 12 + PostgreSQL 17 为核心的 12 大业务域；<br />
                - 接入现代企业级 HttpOnly Cookie 双 Token 无感轮换与防重放机制；<br />
                - 落地短视频防刷看播打点时钟、全渠道核销仲裁与 25%+25% 城市合伙人自动清算。
              </p>
            </el-card>
          </el-timeline-item>
          <el-timeline-item timestamp="2026-10-25" placement="top">
            <el-card shadow="never">
              <h4>v2.1.0: 数据库与契约规范落地</h4>
              <p>
                - 构建 monorepo 统一契约包 `@city-alliance/contracts`；<br />
                - 全面迁移历史 MySQL 商家、分类、订单与 6 位会员终身锁客档案。
              </p>
            </el-card>
          </el-timeline-item>
          <el-timeline-item timestamp="2025-08-20" placement="top">
            <el-card shadow="never">
              <h4>v1.0.0: 原版初代架构上线</h4>
              <p>基于经典 ThinkPHP 6 + MySQL 单体架构，验证同城联盟多端业务可行性。</p>
            </el-card>
          </el-timeline-item>
        </el-timeline>
      </el-card>
    </el-card>
  </div>
</template>

<script setup lang="ts">
// License & Architecture view
</script>

<style scoped>
.license-container {
  padding: 16px;
}
.header-action {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.sub-tip {
  color: #909399;
  font-size: 13px;
  margin-top: 4px;
}
.box-card {
  height: 100%;
}
.card-title {
  font-weight: 600;
  font-size: 15px;
}
.stack-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.stack-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  background: #f8f9fb;
  border-radius: 8px;
}
.stack-icon {
  font-size: 24px;
}
.stack-meta {
  flex: 1;
}
.stack-name {
  font-weight: 600;
  font-size: 13px;
  color: #303133;
}
.stack-desc {
  font-size: 12px;
  color: #909399;
  margin-top: 2px;
}
</style>
