import Config from './config.js'
import Util from './util.js'

// #ifdef MP-WEIXIN
const app_type = 'weapp';
const app_type_name = '微信小程序';
// #endif
export default {
	sendRequest(params) {
		uni.showLoading({
			title: '加载中'
		})
		var method = params.data != undefined ? 'POST' : 'GET', // 请求方式

			url = Config.baseUrl + params.url, // 请求路径
			data = {
				// app_type,
				// app_type_name
			},
			token = '';
		// token
		if (uni.getStorageSync('token')) token = uni.getStorageSync('token');

		// 参数
		if (params.data != undefined) Object.assign(data, params.data);
		if (params.method) {
			method = params.method
		}
		uni.request({
			url: url,
			method: method,
			data: data,

			header: params.header || {
				'content-type': 'application/json',
				"token": token ? token : ''
			},
			dataType: params.dataType || 'json',
			responseType: params.responseType || 'text',
			success: (res) => {
				console.log(res, '+++++++++++');
				uni.hideLoading()
				if (res.data.msg == "请登录后操作" || res.data.code == "401" || res.msg == "请登录后操作" || res.msg ==
					"401") {
					uni.removeStorageSync('token');
					uni.removeStorageSync('identity_key');
					//登录失效页面返回
					uni.showModal({
						title: '登录过期',
						content: '请前往登录',
						success: res => {
							if (res.confirm) {
								uni.navigateTo({
									url: '/pages/Home/Home'
								})
							} else if (res.cancel) {}
						}
					});

				} else {
					typeof params.success == 'function' && params.success(res.data);
				}
			},
			fail: (res) => {
				uni.hideLoading()
				typeof params.fail == 'function' && params.fail(res);
			},
			complete: (res) => {
				uni.hideLoading()
				typeof params.complete == 'function' && params.complete(res);
			}
		});
	}
}
