import { createApp } from 'vue';
import { createPinia } from 'pinia';
import NutUI from '@nutui/nutui-taro';
import '@nutui/nutui-taro/dist/style.css';

const App = createApp({
  onShow() {},
});

App.use(createPinia());
App.use(NutUI);

export default App;
