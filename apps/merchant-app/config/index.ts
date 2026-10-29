import { defineConfig } from '@tarojs/cli';

export default defineConfig({
  projectName: 'merchant-app',
  date: '2026-10-29',
  designWidth: 375,
  deviceRatio: {
    640: 2.34 / 2,
    750: 1,
    375: 2,
    828: 1.81 / 2,
  },
  sourceRoot: 'src',
  outputRoot: 'dist',
  plugins: ['@tarojs/plugin-framework-vue3'],
  defineConstants: {},
  copy: {
    patterns: [],
    options: {},
  },
  framework: 'vue3',
  compiler: 'webpack5',
  mini: {
    postcss: {
      pxtransform: {
        enable: true,
        config: {},
      },
      url: {
        enable: true,
        config: {
          limit: 1024,
        },
      },
    },
  },
});
