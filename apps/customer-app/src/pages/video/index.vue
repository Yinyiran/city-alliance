<template>
  <view class="video-container">
    <video
      id="videoPlayer"
      class="full-video"
      :src="currentVideo.videoUrl"
      :loop="true"
      :autoplay="true"
      :controls="false"
      :muted="isMuted"
    />

    <!-- 右侧环形防刷打点领积分浮层 -->
    <view class="progress-ring-box">
      <view class="ring-circle">
        <text class="ring-seconds">{{ countdownSeconds }}s</text>
      </view>
      <text class="ring-text">看视频领积分</text>
    </view>

    <!-- 声音开关按钮 -->
    <view class="sound-toggle-btn" @tap="isMuted = !isMuted">
      <text class="sound-icon">{{ isMuted ? '🔇' : '🔊' }}</text>
    </view>

    <!-- 底部挂载商品与下单 -->
    <view class="bottom-product-bar">
      <image class="prod-thumb" :src="currentVideo.productCover" mode="aspectFill" />
      <view class="prod-info">
        <view class="prod-name">{{ currentVideo.productName }}</view>
        <view class="prod-price">￥{{ currentVideo.productPrice }}</view>
      </view>
      <nut-button type="danger" size="small">立即下单</nut-button>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

const isMuted = ref(false);
const countdownSeconds = ref(15);
let timer: any = null;

const currentVideo = ref({
  videoUrl: 'https://www.w3schools.com/html/mov_bbb.mp4',
  productName: '精品手冲耶加雪菲 (限时赠到店伴手礼)',
  productPrice: '32.00',
  productCover: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=100',
});

onMounted(() => {
  timer = setInterval(() => {
    if (countdownSeconds.value > 1) {
      countdownSeconds.value--;
    } else {
      countdownSeconds.value = 15; // 重新计圈
    }
  }, 1000);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});
</script>

<style>
.video-container {
  width: 100vw;
  height: 100vh;
  position: relative;
  background-color: #000;
}
.full-video {
  width: 100%;
  height: 100%;
}
.progress-ring-box {
  position: absolute;
  right: 16px;
  top: 80px;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.ring-circle {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 3px solid #ff5000;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.4);
}
.ring-seconds {
  color: #fff;
  font-size: 13px;
  font-weight: 700;
}
.ring-text {
  color: #fff;
  font-size: 10px;
  margin-top: 4px;
}
.sound-toggle-btn {
  position: absolute;
  right: 16px;
  top: 160px;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
}
.sound-icon {
  font-size: 18px;
}
.bottom-product-bar {
  position: absolute;
  bottom: 30px;
  left: 12px;
  right: 12px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 10px;
  padding: 10px;
  display: flex;
  align-items: center;
}
.prod-thumb {
  width: 48px;
  height: 48px;
  border-radius: 6px;
  margin-right: 10px;
}
.prod-info {
  flex: 1;
}
.prod-name {
  font-size: 13px;
  font-weight: 700;
  color: #333;
}
.prod-price {
  font-size: 15px;
  font-weight: 700;
  color: #ff5000;
  margin-top: 2px;
}
</style>
