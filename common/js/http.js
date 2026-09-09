import Config from './config.js'

// #ifdef MP-WEIXIN
const app_type = 'weappqy';
const app_type_name = '微信小程序(商家端)';
// #endif
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

			// 这里请求成功根据自己的 code 判断就好了
			success: function (res) { // 当请求成功时调用的函数。这个函数会得到一个参数：从服务器返回的数据。当请求成功时调用函数，即status==200。
				// console.log('数据请求响应', res.data.code)
				if (res.data.code == 0) {
					resolve(res.data)
					uni.hideLoading();
				} else if (res.data.code == -10016) {
					resolve(res.data)
					uni.hideLoading();
				} else if (res.data.code == -10001) {
					resolve(res.data)
					uni.hideLoading();
				} else if (res.data.code == -10010) {
					// uni.showModal({
					// 	content: "你还没登录，请先登录",
					// 	confirmColor: "#363636",
					// 	cancelColor: '#666666',
					// 	success: res => {
					// 		if (res.confirm) {
					// 			uni.navigateTo({
					// 				url: '/otherpages/login/login'
					// 			})
					// 		}
					// 	}
					// })
					uni.removeStorageSync('token')
					uni.hideLoading();
					// 业务错误也 resolve，避免调用方 await 永久挂起
					resolve(res.data)
				} else {
					console.log(res.data);
					if (silent) {
						// 静默模式：不弹错误 toast
						uni.hideLoading();
					} else {
						// token 为空时提示用户先登录
						const msg = res.data.msg == 'token不能为空' ? '请登录后，查看数据' : (res.data.msg ||
							'请求失败，请稍后重试');
						uni.hideLoading();

						// uni.showToast({
						// 	title: msg,
						// 	icon: 'none',
						// 	duration: 3000,
						// 	success: res => {
						// 		setTimeout(()=>{
						// 			uni.hideLoading();
						// 		},3000)
						// 	}
						// })
					}
					// 业务错误也 resolve，避免调用方 await 永久挂起
					resolve(res.data)
				}

			},
			fail: function (res) {
				console.warn("请求异常", res)
				// uni.showToast({
				// 	title: res.data.msg,
				// 	icon: 'none',
				// 	duration: 3000,
				// 	success: res => {
				uni.hideLoading();
				// 	}
				// })
				reject(res)
			}
		});
	});
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