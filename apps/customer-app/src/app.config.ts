export default defineAppConfig({
  pages: [
    'pages/index/index',
    'pages/nearby/index',
    'pages/video/index',
    'pages/welfare/index',
    'pages/user/index',
  ],
  window: {
    backgroundTextStyle: 'light',
    navigationBarBackgroundColor: '#ffffff',
    navigationBarTitleText: '城盟优选',
    navigationBarTextStyle: 'black',
  },
  tabBar: {
    color: '#909399',
    selectedColor: '#ff5000',
    backgroundColor: '#ffffff',
    borderStyle: 'black',
    list: [
      {
        pagePath: 'pages/index/index',
        text: '首页',
      },
      {
        pagePath: 'pages/nearby/index',
        text: '同城',
      },
      {
        pagePath: 'pages/video/index',
        text: '逛一逛',
      },
      {
        pagePath: 'pages/welfare/index',
        text: '福利区',
      },
      {
        pagePath: 'pages/user/index',
        text: '我的',
      },
    ],
  },
});
