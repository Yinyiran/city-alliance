<script>
	export default {
		globalData: {
			// 保存客户发票信息
			userdata: {
				bank_name: '',
				bank_number: '',
				bank_open: '',
				bank_account: '',
			}
		},
		onLaunch: function() {
			console.log('App Launch')
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
		onLoad: function() {
		},
		onHide: function() {
			console.log('App Hide')
		}
	}
</script>

<style lang="scss">
	@import './common/css/main.scss';
	@import './common/css/common.scss';
</style>
<style lang="scss">
	page {
		margin: 0 auto;
		padding-bottom: env(safe-area-inset-bottom);
	}
</style>