import Config from './config.js';

// #ifdef MP-WEIXIN
const app_type = 'weapp';
const app_type_name = '微信小程序';
// #endif

// 业务错误码：后端统一约定
const CODE = {
  TOKEN_MISSING: [-10010, -10011, 401], // 未登录 / token 失效
  NO_SHOP_AUTH: -10020, // 无企业端权限（商家端）
  SHOP_NOT_FOUND: -10021, // 店铺不存在
};

// 登录态失效时通知页面（页面据此弹登录引导，而不是留白）
let lastTokenToastTime = 0;
function showTokenToast() {
  const now = Date.now();
  if (now - lastTokenToastTime > 2500) {
    lastTokenToastTime = now;
    uni.showToast({
      title: '该页面数据需要登录后查看',
      icon: 'none',
      duration: 2500,
    });
  }
  uni.$emit('authRequired', { code: CODE.TOKEN_MISSING[0] });
}

// 并发请求计数：原先每个请求都 showLoading/hideLoading，
// 并发时先返回的请求会提前关闭遮罩，导致「数据没出来但遮罩已消失」，
// 视觉上表现为白屏 / 卡在加载中。
let pendingCount = 0;
function loadingStart(silent) {
  if (silent) return;
  pendingCount++;
  if (pendingCount === 1) {
    uni.showLoading({ title: '加载中', mask: true });
  }
}
function loadingEnd(silent) {
  if (silent) return;
  pendingCount = Math.max(0, pendingCount - 1);
  if (pendingCount === 0) {
    uni.hideLoading();
  }
}

/**
 * 统一请求
 * @param {string} url      接口地址
 * @param {object} params   参数
 * @param {string} methods  请求方法
 * @param {boolean} silent  是否静默（不弹 loading、不弹业务错误 toast）
 */
export function server(url, params, methods, silent) {
  loadingStart(silent);
  return new Promise((resolve) => {
    let token;
    if (uni.getStorageSync('token')) {
      token = uni.getStorageSync('token');
    }
    let latitude;
    let longitude;
    let website_id;
    if (uni.getStorageSync('latlng')) {
      latitude = uni.getStorageSync('latlng').latitude;
      longitude = uni.getStorageSync('latlng').longitude;
    }
    if (uni.getStorageSync('website_id')) {
      website_id = uni.getStorageSync('website_id');
    }

    uni.request({
      url: Config.baseUrl + url,
      data: {
        ...params,
        app_type: app_type,
        app_type_name: app_type_name,
        token,
        longitude: longitude,
        latitude: latitude,
        website_id,
      },
      method: methods || 'post',
      dataType: 'json',
      header: {
        'content-type': 'application/json; charset=utf-8',
        token: token,
      },

      success: function (res) {
        loadingEnd(silent);
        const responseData = res.data && typeof res.data === 'object' ? res.data : null;
        if (res.statusCode < 200 || res.statusCode >= 300 || !responseData) {
          // 返回非 JSON（404 HTML / 网关错误页 / PHP 报错页）
          if (!silent) {
            uni.showToast({ title: '服务异常，请稍后重试', icon: 'none', duration: 2000 });
          }
          resolve({
            code: -1,
            message: '服务异常，请稍后重试',
            data: null,
            _netError: true,
          });
          return;
        }

        const code = responseData.code;
        const msg = responseData.message || responseData.msg || '';

        // 判断是否属于 token 不存在 / token 为空 / token 过期 / 未登录
        let isTokenMissing = CODE.TOKEN_MISSING.indexOf(Number(code)) !== -1;
        if (!isTokenMissing && typeof msg === 'string') {
          if (
            msg.indexOf('token') !== -1 ||
            msg.indexOf('未登录') !== -1 ||
            msg.indexOf('请先登录') !== -1
          ) {
            isTokenMissing = true;
          }
        }

        if (isTokenMissing) {
          uni.removeStorageSync('token');
          showTokenToast();
          resolve({ ...responseData, _authRequired: 1 });
          return;
        }

        if (code == 0 || code == -10016 || code == -10001) {
          resolve(responseData);
          return;
        }

        // 其它错误业务提示
        if (!silent && msg) {
          uni.showToast({ title: msg, icon: 'none', duration: 2500 });
        }
        resolve(responseData);
      },

      fail: function (res) {
        // 网络异常：不再 reject。
        // 原实现 reject 后，全站大量未接 .catch 的接口会抛出未捕获异常，
        // 微信审核表现为「页面报错 / 页面白屏」。
        loadingEnd(silent);
        console.warn('请求异常', url, res);
        if (!silent) {
          uni.showToast({ title: '网络异常，请检查网络后重试', icon: 'none', duration: 2000 });
        }
        resolve({
          code: -1,
          message: '网络异常，请检查网络后重试',
          data: null,
          _netError: true,
        });
      },
    });
  });
}

export { CODE };

export default {
  sendRequest({ url, data, method, success, fail, silent }) {
    return server(url, data, method, silent).then((response) => {
      // 网络层失败同样回调 fail，兼容原有 $api.sendRequest 调用方
      if (response && response._netError) {
        fail && fail(response);
      } else {
        success && success(response);
      }
      return response;
    });
  },
};