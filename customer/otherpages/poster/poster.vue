<template>
	<view>
		<image :src="$util.img(path)" mode="widthFix" show-menu-by-longpress></image>
		<view class="btms iphone-x">
			<view class="" @click="submit">保存图片</view>
		</view>
	</view>
</template>

<script>
	import {
		poster,
		userinfo
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				path: "",
				member_id: ""
			};
		},
		async onShow() {
			await this.getuser()
			await this.getinfo()
		},
		methods: {
			submit() {
				uni.downloadFile({
					url: this.$util.img(this.path),
					success: (downloadRes) => {
						if (downloadRes.statusCode === 200) {
							// 下载成功，保存图片
							uni.saveImageToPhotosAlbum({
								filePath: downloadRes.tempFilePath,
								success: () => {
									uni.showToast({
										title: '图片保存成功',
										icon: 'success',
										duration: 2000
									});
								},
								fail: (err) => {
									uni.showToast({
										title: '保存图片失败：' + (err.errMsg || ''),
										icon: 'none'
									});
								}
							});
						} else {
							uni.showToast({
								title: '图片下载失败',
								icon: 'none'
							});
						}
					},
					fail: (err) => {
						uni.showToast({
							title: '下载图片失败：' + (err.errMsg || ''),
							icon: 'none'
						});
					}
				});
			},
			async getuser() {
				try {
					const res = await userinfo()
					if (res && res.data) {
						this.member_id = res.data.member_id
						this.site_id = res.data.site_id || uni.getStorageSync('user_site_id') || uni.getStorageSync('share_site_id') || ''
						if (this.site_id) {
							uni.setStorageSync('user_site_id', this.site_id)
						}
					}
				} catch (e) {
					console.error('获取用户信息失败', e)
				}
			},
			async getinfo() {
				const site_id = this.site_id || uni.getStorageSync('user_site_id') || uni.getStorageSync('share_site_id') || '';
				let params = {
					page: '/pages/index/index',
					qrcode_param: JSON.stringify({
						suid: this.member_id,
						site_id: site_id
					})
				}
				await poster(params).then(res => {
					this.path = res.data.path
				})
			}
		}
	}
</script>

<style lang="scss">
	image {
		width: 100vw;
		height: 100vh;
	}

	.btms {
		position: fixed;
		bottom: 0;
		left: 0;
		background: #fff;
		text-align: center;
		width: 100vw;
		z-index: 99;

		>view {
			margin: 20rpx 40rpx;
			line-height: 96rpx;
			background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
			border-radius: 48rpx;
			font-weight: 600;
			font-size: 32rpx;
			color: #FFFFFF;
		}
	}
</style>