import path from 'path';
import uni from '@dcloudio/vite-plugin-uni';
import { defineConfig } from 'vite';

// uni-app 内部按绝对路径解析源码目录。若 UNI_INPUT_DIR 为相对路径（如 "./"），
// 构建时 static 目录会被误判忽略、无法复制到产物（app.json 中 tabBar 图标即因此报"未找到"）。
// 因此在加载插件前统一转为绝对路径。
process.env.UNI_INPUT_DIR = path.resolve(process.env.UNI_INPUT_DIR || './');

export default defineConfig({
  css: {
    preprocessorOptions: {
      scss: {
        silenceDeprecations: ['import', 'global-builtin', 'legacy-js-api'],
      },
    },
  },
  plugins: [uni()],
});
