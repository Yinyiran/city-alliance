<template>
	<view>
		<image :src="$util.img(douyin_image)" show-menu-by-longpress mode=""></image>
		<view class="btms iphone-x">
			<view class="" @click="submit">保存图片</view>
		</view>
	</view>
</template>

<script>
	import {getConfig} from "@/common/js/api.js"
	export default {
		data() {
			return {
				douyin_image:''
			};
		},
		onShow() {
			this.getConfig()
		},
		methods:{
			submit(){
				uni.downloadFile({
				      url: this.$util.img(this.douyin_image),
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
				              resolve('图片保存成功');
				            },
				            fail: (err) => {
				              reject('保存图片失败：' + err.errMsg);
				            }
				          });
				        } else {
				          reject('图片下载失败，状态码：' + downloadRes.statusCode);
				        }
				      },
				      fail: (err) => {
				        reject('下载图片失败：' + err.errMsg);
				      }
				    });
			},
			getConfig(){
				let params={
					website_id:uni.getStorageSync('website_id')
				}
				getConfig(params).then(res=>{
					this.douyin_image=res.data.douyin_image
				})
			},
		}
	}
</script>

<style lang="scss">
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
	image{
		width: 100vw;
		height: 100vh;
	}
</style>
