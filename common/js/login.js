export default {
	data() {
		return {
			
		}
	},
	methods: {
		getMobile(e) {
			var _this = this
			if(e.detail.errMsg == 'getPhoneNumber:ok'){
				var data = {
					code: _this.code,
					encryptedData: e.detail.encryptedData,
					iv: e.detail.iv
				}
				//判断是否邀请进来
				if(uni.getStorageSync('userid')) {
					data.invite_id = uni.getStorageSync('userid')
				}
				_this.$api.sendRequest({
					url: '/api/getToken',
					data: data,
					success: res => {
						if (res.code >=0) {
							uni.setStorage({
								key: 'token',
								data: res.data.access_token,
								success: () => {
									_this.$store.commit('setToken', res.data.access_token);
								}
							});
							//移除邀请人id
							if(uni.getStorageSync('userid')) {
								uni.removeStorageSync('userid')
							}
              this.getMemberInfo()
							setTimeout(() => {
                if(_this.isPhone) {
                  uni.navigateBack({
                    delta: 2
                  })
                }else {
                  uni.navigateBack({
                    delta: 1
                  })
                }
							}, 1000);
						}
					}
				})
			}else{
				this.$util.showToast({title:'您拒绝了手机号授权'})
			}
		},
    // 获取会员基础信息
    getMemberInfo() {
      this.$api.sendRequest({
        url: '/api/user/index',
        data: {},
        success: res => {
          if (res.code >= 0 && res.data) {
            uni.setStorageSync('userInfo', res.data);
          }
        }
      })
    },
		login() {
			var that = this;
			uni.login({
				provider: 'weixin',
				success(res) {
					that.code = res.code;
				}
			})
			wx.getUserProfile({
				desc: '获取用户个人信息', 
				success: (res) => {
					if (res.errMsg == 'getUserProfile:ok') {
						var nickName = res.userInfo.nickName;
						var avatarUrl = res.userInfo.avatarUrl;
						that.authLogin(nickName,avatarUrl)
					}
				}
			})		
		},
		//授权登录
		authLogin(nickName,avatarUrl,code) {
			uni.showLoading({ title: '登录中' });
			var data = {
				code: this.code,
				avatarUrl: avatarUrl,
				nickName: nickName
			}
			//判断是否邀请进来
			if(uni.getStorageSync('userid')) {
				data.invite_id = uni.getStorageSync('userid')
			}
			var _this = this;
			this.$api.sendRequest({
				url: '/api/getToken',
				data,
				success: res => {
					if(res.code >=0) {
						uni.setStorage({
							key: 'token',
							data: res.data.access_token,
							success: () => {
								this.$store.commit('setToken', res.data.access_token);
							}
						});
						//移除邀请人id
						if(uni.getStorageSync('userid')) {
							uni.removeStorageSync('userid')
						}
						setTimeout(() => {
							uni.hideLoading();
							_this.$util.redirectTo('/pages/index/index')
						}, 1000);
					}else {
						uni.hideLoading();
						this.$util.showToast({ title: res.message });
					}
				},
				fail: () => {
					uni.hideLoading();
					this.$util.showToast({ title: '登录失败' });
				}
			});
		},
	}
}