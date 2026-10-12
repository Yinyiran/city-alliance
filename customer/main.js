import share from '@/common/js/share.js';
import { createSSRApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App';
import Util from '@/common/js/util.js';
import Http from '@/common/js/http.js';
import uvUiTools from '@/uni_modules/uv-ui-tools/index.js';

// 微信开发者工具只可靠收录「入口(app.js)直接依赖链」的模块，而 uni 插件会把被多个
// 页面/组件共享的业务模块拆成独立 chunk（common/js/api.js 等）。这些 chunk 不在
// app.js 依赖链中时，运行时 require 会报 "module 'xxx' is not defined"。
// 因此在入口显式引入全部业务模块（namespace 导入 + 显式使用，防止被 tree-shake
// 移除），强制其进入 app.js 的依赖链（与 http.js 同机制）。
import * as api from '@/common/js/api.js';
import * as config from '@/common/js/config.js';
import * as golbalConfig from '@/common/js/golbalConfig.js';
import * as refundMethod from '@/common/js/refundMethod.js';
import * as validate from '@/common/js/validate.js';
import * as store from '@/store/index.js';

export function createApp() {
  const app = createSSRApp(App);
  // 把业务模块命名空间挂载到全局属性：既作为真实"使用"防止 rollup tree-shake 掉入口
  // 对它们的引用（否则 app.js 依赖链不含这些 chunk，微信模块表未收录，运行时 require
  // 会报 "module 'xxx' is not defined"），同时也能在组件中通过 this.$business.xxx 访问。
  app.config.globalProperties.$business = {
    api,
    config,
    golbalConfig,
    refundMethod,
    validate,
    store,
  };
  app.config.globalProperties.$util = Util;
  app.config.globalProperties.$api = Http;

  // 全局错误兜底：单点渲染异常不再静默放大为整页白屏。
  // 微信审核判定「页面白屏/页面报错」通常就是某个 v-for 遍历 undefined 或
  // 深层取值为 null 导致的渲染中断，这里统一上报并提示，避免用户/审核员看到空白页。
  app.config.errorHandler = (err, instance, info) => {
    console.error('[app error]', info, err);
    uni.showToast({
      title: '页面数据加载异常，请下拉刷新重试',
      icon: 'none',
      duration: 2500,
    });
  };

  app.use(createPinia());
  app.use(uvUiTools);
  app.mixin(share);
  return {
    app,
  };
}
