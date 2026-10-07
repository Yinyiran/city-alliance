import Config from './config.js'

// #ifdef MP-WEIXIN
const app_type = 'weappqy';
const app_type_name = '微信小程序(商家端)';
// #endif
let lastTokenToastTime = 0;
function showTokenToast() {
	let now = Date.now();
	if (now - lastTokenToastTime > 2500) {
		lastTokenToastTime = now;
		uni.showToast({
			title: '该页面数据需要登录后查看',
			icon: 'none',
			duration: 2500
		});
	}
}

export function server(url, params, methods, silent) {
	uni.showLoading({
		title: '加载中'
	})
	let promise = new Promise((resolve, reject) => {
		// 这里是获取 token 
		let token;
		if (uni.getStorageSync('token')) {
			token = uni.getStorageSync('token')
		}
		uni.request({
			url: Config.baseUrl + url,
			data: {
				site_id: uni.getStorageSync('site_id') || '',
				...params,
				app_type: app_type,
				app_type_name: app_type_name,
				token,
			},
			method: methods || 'post',
			dataType: "json",
			header: {
				'content-type': 'application/json; charset=utf-8',
				'token': token
			},

			success: function (res) {
				uni.hideLoading();
				if (!res || !res.data) {
					resolve(res || {});
					return;
				}

				let code = res.data.code;
				let msg = res.data.msg || res.data.message || '';

				// 判断是否属于 token 不存在 / token 为空 / token 过期 / 未登录
				let isTokenMissing = (code == -10010 || code == -10011);
				if (!isTokenMissing && typeof msg === 'string') {
					if (msg.indexOf('token') !== -1 || msg.indexOf('未登录') !== -1 || msg === '请先登录') {
						isTokenMissing = true;
					}
				}

				if (isTokenMissing) {
					uni.removeStorageSync('token');
					showTokenToast();
					resolve(res.data);
					return;
				}

				if (code == 0 || code == -10016 || code == -10001) {
					resolve(res.data);
					return;
				}

				// 其它错误业务提示
				if (!silent && msg) {
					uni.showToast({
						title: msg,
						icon: 'none',
						duration: 2500
					});
				}
				resolve(res.data);
			},
			fail: function (res) {
				console.warn("请求异常", res)
				uni.hideLoading();
				reject(res)
			}
		})
	})
	return promise;
}

export default {
	sendRequest({ url, data, method, success, fail }) {
		return server(url, data, method)
			.then((response) => {
				success && success(response);
				return response;
			})
			.catch((error) => {
				fail && fail(error);
				throw error;
			});
	},
};