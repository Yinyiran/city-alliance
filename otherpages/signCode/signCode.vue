<template>
	<view>
		<nvabar title='签到码'></nvabar>
		<image src="/static/sbg2.png" class="bgs" mode=""></image>
		<view class="mains">
			<image src="/static/sbg3.png" mode=""></image>
			<view class="acea-row row-middle re m-80 p-t-80">
				<image :src="$util.img(info.headimg)" v-if="info.headimg" class="w-100 h-100 bor-50" mode=""></image>
				<image src="/static/avatar.png" v-else class="w-100 h-100 bor-50" mode=""></image>
				<view class="m-l-20">
					<view class="font-32 font-weight" style="color: #222;">
						<text class="font-36" style="color: #555;">会员级别：</text>
						{{info.member_level_name}}
					</view>
					<view class="font-26" style="color: #525666;text-align: left;">会员编号: {{info.member_id}}</view>
				</view>
			</view>
			<image :src="$util.img(filepath)" class="codes re" mode=""></image>
			<view class="btms">
				<image src="/static/sbg4.png" mode=""></image>
				<view class="">注意：此二维码需前往指定商家线下扫码即可激活签到奖励</view>
			</view>
		</view>
	</view>
</template>

<script>
	import { userinfo,getqrcode,dosignin } from '@/common/js/api.js';
	export default {
		data() {
			return {
				info:"",
				filepath:''
			};
		},
		onLoad() {
			this.getqrcode()
			this.userinfo()
			// dosignin({
			// 	member_id:4,
			// 	shop_id:1
			// })
		},
		methods:{
			userinfo(){
				userinfo().then(res=>{
					this.info=res.data
				})
			},
			getqrcode(){
				getqrcode().then(res=>{
					if (res.code == 0 && res.filepath) {
						this.filepath = res.filepath;
					} else if (res.message) {
						uni.showToast({ title: res.message, icon: 'none' });
					}
				}).catch(() => {
					uni.showToast({ title: '获取签到码失败', icon: 'none' });
				});
			},
		}
	}
</script>

<style lang="scss">
	.re{
		position: relative;
		z-index: 99;
	}
	.mains{
		margin: 120rpx auto;
		position: relative;
		width: 650rpx;
		height: 844rpx;
		text-align: center;
		.codes{
			width: 320rpx;
			height: 320rpx;
		}
		>image:first-child{
			position: absolute;
			left: 0;
			top: 0;
			width: 650rpx;
			height: 100%;
		}
		>.btms{
			width: 610rpx;
			height: 233rpx;
			position: absolute;
			bottom: 0;
			left: 50%;
			transform: translateX(-50%);
			z-index: 2;
			>image:first-child{
				position: absolute;
				width: 610rpx;
				height: 100%;
				left: 0;
				bottom: 0;
				z-index: 1;
			}
			>view{
				font-size: 28rpx;
				color: #FFFFFF;
				padding: 109rpx 48rpx 0 48rpx;
				position: relative;
				z-index: 99;
			}
		}
	}
	.bgs{
		width: 100vw;
		height: 100vh;
		position: fixed;
		left: 0;
		top: 0;
		z-index: -1;
	}
</style>
