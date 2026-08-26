import share from '@/common/js/share.js';
import { createSSRApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App';
import Util from './common/js/util.js';
import Http from './common/js/http.js';
import uvUiTools from '@/uni_modules/uv-ui-tools/index.js';

export function createApp() {
  const app = createSSRApp(App);
  app.config.globalProperties.$util = Util;
  app.config.globalProperties.$api = Http;
  app.use(createPinia());
  app.use(uvUiTools);
  app.mixin(share);
  return {
    app,
  };
}
