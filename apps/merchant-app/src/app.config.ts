export default defineAppConfig({
  pages: [
    'pages/index/index',
    'pages/cashier/index',
    'pages/signin/index',
    'pages/user/index',
  ],
  window: {
    backgroundTextStyle: 'light',
    navigationBarBackgroundColor: '#1e222d',
    navigationBarTitleText: '城盟优选·商家主理人',
    navigationBarTextStyle: 'white',
  },
  tabBar: {
    color: '#909399',
    selectedColor: '#1890ff',
    backgroundColor: '#ffffff',
    borderStyle: 'black',
    list: [
      {
        pagePath: 'pages/index/index',
        text: '看板',
      },
      {
        pagePath: 'pages/cashier/index',
        text: '收银台',
      },
      {
        pagePath: 'pages/signin/index',
        text: '到店签到',
      },
      {
        pagePath: 'pages/user/index',
        text: '收益账本',
      },
    ],
  },
});
