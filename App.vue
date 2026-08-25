<script>
	export default {
		onLaunch: function() {
			let _this = this
			uni.getSystemInfo({
				success: function(e) {
					// #ifndef MP
					_this.globalData.StatusBar = e.statusBarHeight;
					if (e.platform == 'android') {
						_this.globalData.CustomBar = e.statusBarHeight + 50;
					} else {
						_this.globalData.CustomBar = e.statusBarHeight + 45;
					};
					// #endif

					// #ifdef MP-WEIXIN
					_this.globalData.StatusBar = e.statusBarHeight;
					let custom = uni.getMenuButtonBoundingClientRect();
					_this.globalData.Custom = custom;
					_this.globalData.CustomBar = custom.bottom + custom.top - e.statusBarHeight;
					// #endif		

					// #ifdef MP-ALIPAY
					_this.globalData.StatusBar = e.statusBarHeight;
					_this.globalData.CustomBar = e.statusBarHeight + e.titleBarHeight;
					// #endif
				}
			})
			// 小程序版本更新
			// #ifdef MP
			const updateManager = uni.getUpdateManager();
			updateManager.onCheckForUpdate(function(res) {
				// 请求完新版本信息的回调
			});
			updateManager.onUpdateReady(function(res) {
				uni.showModal({
					title: '更新提示',
					content: '新版本已经准备好，是否重启应用？',
					success(res) {
						if (res.confirm) {
							// 新的版本已经下载好，调用 applyUpdate 应用新版本并重启	
							updateManager.applyUpdate();
						}
					}
				});
			});

			updateManager.onUpdateFailed(function(res) {
				// 新的版本下载失败
			});
			// #endif
		},
		onLoad: function() {},
		onHide: function() {
			console.log('App Hide')
		}
	}
</script>

<style lang="scss">
	@import './common/css/main.scss';
	@import './common/css/common.scss';

	radio .wx-radio-input.wx-radio-input-checked {
		background-color: #F55606 !important;
		border-color: #F55606 !important;
		// box-sizing: border-box;
	}

	checkbox.red[checked] .wx-checkbox-input,
	checkbox.red.checked .uni-checkbox-input {
		background-color: #0C997D !important;
		border-color: #0C997D !important;
		color: #0C997D !important;
	}

	/**
	* 去除选中后的对号
	*/
	radio::before {
		content: '' !important;
	}
</style>
<style lang="scss">
	// page {
	// 	padding-bottom: env(safe-area-inset-bottom);
	// }
</style>