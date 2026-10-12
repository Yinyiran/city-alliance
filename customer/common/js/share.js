export function appendShareParams(path) {
  if (!path) path = '/pages/index/index';
  const userInfo = uni.getStorageSync('userInfo');
  const site_id = uni.getStorageSync('user_site_id') || uni.getStorageSync('share_site_id') || uni.getStorageSync('site_id') || (userInfo && userInfo.site_id) || '';
  let suid = '';
  if (userInfo && (userInfo.member_id || userInfo.id)) {
    suid = userInfo.member_id || userInfo.id;
  }
  if (!suid) {
    suid = uni.getStorageSync('invite_code') || '';
  }
  const hasQuestion = path.indexOf('?') !== -1;
  const params = [];
  if (site_id && path.indexOf('site_id=') === -1) {
    params.push(`site_id=${site_id}`);
  }
  if (suid && path.indexOf('suid=') === -1) {
    params.push(`suid=${suid}`);
  }
  if (params.length > 0) {
    return path + (hasQuestion ? '&' : '?') + params.join('&');
  }
  return path;
}

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
    const that = this;
    const rawPath = (that.share && that.share.path) ? that.share.path : '/pages/index/index';
    const finalPath = appendShareParams(rawPath);
    return {
      title: that.share ? that.share.title : '快来和我一起体验吧~',
      path: finalPath,
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
  onShareTimeline(res) {
    const that = this;
    const rawPath = (that.share && that.share.path) ? that.share.path : '/pages/index/index';
    const finalPath = appendShareParams(rawPath);
    return {
      title: that.share ? that.share.title : '快来和我一起体验吧~',
      path: finalPath,
    };
  },
};
