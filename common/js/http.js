import Config from './config.js'

// #ifdef MP-WEIXIN
const app_type = 'weapp';
const app_type_name = '微信小程序';
// #endif
export function server(url, params, methods) {
	uni.showLoading({
		title: '加载中'
	})
	let promise = new Promise((resolve, reject) => {
		// 这里是获取 token 
		let token;
		if (uni.getStorageSync('token')) {
			token = uni.getStorageSync('token')
		}
		let latitude;
		let longitude;
		let website_id;
		if(uni.getStorageSync('latlng')){
			latitude=uni.getStorageSync('latlng').latitude
			longitude=uni.getStorageSync('latlng').longitude
		}
		if(uni.getStorageSync('website_id')){
			website_id=uni.getStorageSync('website_id')
		}
		uni.request({
			url: Config.baseUrl + url,
			data: {
				...params,
				app_type:app_type,
				app_type_name:app_type_name,
				token,
				longitude:longitude,
				latitude:latitude,
				website_id
			},
			method: methods || 'post',
			dataType: "json",
			header: {
				'content-type': 'application/json; charset=utf-8',
				'token': token
			},

			// 这里请求成功根据自己的 code 判断就好了
			success: function (res) { // 当请求成功时调用的函数。这个函数会得到一个参数：从服务器返回的数据。当请求成功时调用函数，即status==200。
				// console.log('数据请求响应', res)
				if (res.data.code == 0) {
					resolve(res.data)
					uni.hideLoading();
				}else if(res.data.code==-10016){
					resolve(res.data)
					uni.hideLoading();
				}else if(res.data.code==-10001){
					resolve(res.data)
					uni.hideLoading();
				}else if(res.data.code=-10010){
					uni.removeStorageSync('token')
					uni.hideLoading();
				}  else if (res.data.code == 401) {
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
				} else {
					// console.log(res.data);
					uni.showToast({
						title: res.data.message,
						icon: 'none',
						duration: 3000,
						success: res => {
							setTimeout(()=>{
								uni.hideLoading();
							},3000)
						}
					})
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