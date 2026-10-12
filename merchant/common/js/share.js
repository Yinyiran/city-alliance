var MemberNO = uni.getStorageSync('MemberNO')
export default {
  data() {
    return {
      // 设置默认的分享参数
      // 如果页面不设置share，就触发这个默认的分享
      share: {
        title: '快来和我一起体验吧~',  
        path: '/pages/index/index',
      },
    };
  },
  onShareAppMessage(res) {
    var that = this;
    return {
      title: that.share.title,
      path: that.share.path,
      success(res) {
        uni.showToast({
          title: '分享成功',
        });
      },
      fail(res) {
        uni.showToast({
          title: '分享失败',
          icon: 'none',
        });
      },
    };
  },
  onShareTimeline(res) { // 分享到朋友圈
    var that = this;
    return {
      title: that.share.title,
      path: that.share.path,
    };
  },
  onShareAppMessage() {
  	var that = this;
  	return {
  	  title: that.share.title,
  	  path: that.share.path,
  	};
  },
  
};
