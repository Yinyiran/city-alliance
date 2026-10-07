import Config from './config.js'

// #ifdef MP-WEIXIN
const app_type = 'weappqy';
const app_type_name = '微信小程序(商家端)';
// #endif

// 业务错误码：后端 shopapplet 统一约定
const CODE = {
	TOKEN_MISSING: [-10010, -10011],   // 未登录 / token 失效
	NO_SHOP_AUTH: -10020,              // 当前账号未开通企业端权限
	SHOP_NOT_FOUND: -10021             // 店铺不存在或已停用
}

// 登录态失效时通知页面（页面据此弹登录引导，而不是留白）
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
	uni.$emit('authRequired', { code: CODE.TOKEN_MISSING[0] })
}

// 并发请求计数：原先每个请求都 showLoading/hideLoading，
// 并发时先返回的请求会提前关闭遮罩，导致页面"数据没出来但遮罩已消失"，
// 视觉上表现为白屏 / 卡在加载中。
let pendingCount = 0;
function loadingStart(silent) {
	if (silent) return;
	pendingCount++;
	if (pendingCount === 1) {
		uni.showLoading({ title: '加载中', mask: true })
	}
}
function loadingEnd(silent) {
	if (silent) return;
	pendingCount = Math.max(0, pendingCount - 1);
	if (pendingCount === 0) {
		uni.hideLoading()
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
	loadingStart(silent)
	return new Promise((resolve) => {
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
				loadingEnd(silent)
				if (!res || !res.data || typeof res.data !== 'object') {
					// 返回非 JSON（404 HTML / 网关错误页 / PHP 报错页）
					resolve({
						code: -1,
						message: '服务异常，请稍后重试',
						data: null,
						_netError: true
					})
					return;
				}

				let code = res.data.code
				let msg = res.data.msg || res.data.message || ''

				// 判断是否属于 token 不存在 / token 为空 / token 过期 / 未登录
				let isTokenMissing = CODE.TOKEN_MISSING.indexOf(Number(code)) !== -1
				if (!isTokenMissing && typeof msg === 'string') {
					if (msg.indexOf('token') !== -1 || msg.indexOf('未登录') !== -1 || msg === '请先登录') {
						isTokenMissing = true
					}
				}

				if (isTokenMissing) {
					uni.removeStorageSync('token')
					showTokenToast()
					resolve(Object.assign({}, res.data, { _authRequired: 1 }))
					return
				}

				if (code == 0 || code == -10016 || code == -10001) {
					resolve(res.data)
					return
				}

				// 其它错误业务提示
				if (!silent && msg) {
					uni.showToast({
						title: msg,
						icon: 'none',
						duration: 2500
					})
				}
				resolve(res.data)
			},
			fail: function (res) {
				// 网络异常：不再 reject。
				// 原实现 reject 后，全站大量未接 .catch 的接口会抛出未捕获异常，
				// 微信审核表现为「页面报错 / 页面白屏」。
				loadingEnd(silent)
				console.warn("请求异常", url, res)
				if (!silent) {
					uni.showToast({
						title: '网络异常，请检查网络后重试',
						icon: 'none',
						duration: 2000
					})
				}
				resolve({
					code: -1,
					message: '网络异常，请检查网络后重试',
					data: null,
					_netError: true
				})
			}
		})
	})
}

export { CODE }

export default {
	sendRequest({ url, data, method, success, fail, silent }) {
		return server(url, data, method, silent)
			.then((response) => {
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
