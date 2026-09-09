<template>
	<view>
		<view class="main acea-row row-middle">
			<view class="m-r-20">
				<image :src="$util.img(infos.headimg)" mode=""></image>
				<image src="/otherpages/static/vv.png" mode=""></image>
			</view>
			<view class="">
				<view class="">
					<text class="font-36" style="color: #555;">会员级别:</text>
					<text class="font-36 font-weight-6">{{infos.member_level_name}}</text>
				</view>
				<view class="font-26 m-t-8" style="color: #525666;">会员编号: {{infos.member_id}}</view>
			</view>
		</view>
		<view class="container">
			<view class="acea-row row-middle row-between" @click="gonext('profile')">
				<view class="acea-row row-middle font-32">
					<image src="/otherpages/static/grxx.png" class="w-40 h-40 m-r-20" mode=""></image>
					个人信息
				</view>
				<uv-icon name="arrow-right" color="#CCCCCC" size="24"></uv-icon>
			</view>
			<view class="acea-row row-middle row-between" @click="gonext('phones')">
				<view class="acea-row row-middle font-32">
					<image src="/otherpages/static/phone2.png" class="w-40 h-40 m-r-20" mode=""></image>
					手机号
				</view>
				<view class="acea-row row-middle">
					<text class="m-r-12 font-32">{{infos.mobile}}</text>
					<uv-icon name="arrow-right" color="#CCCCCC" size="24"></uv-icon>
				</view>
			</view>
			<view class="acea-row row-middle row-between" @click="gonext('address')">
				<view class="acea-row row-middle font-32">
					<image src="/otherpages/static/map5.png" class="w-40 h-40 m-r-20" mode=""></image>
					收货地址
				</view>
				<uv-icon name="arrow-right" color="#CCCCCC" size="24"></uv-icon>
			</view>
		</view>
		<view class="container">
			<view class="acea-row row-middle row-between" @click="exit">
				<view class="acea-row row-middle font-32">
					<image src="/otherpages/static/exit.png" class="w-40 h-40 m-r-20" mode=""></image>
					退出登录
				</view>
				<uv-icon name="arrow-right" color="#CCCCCC" size="24"></uv-icon>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		userinfo
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				infos: {}
			};
		},
		onShow() {
			this.userinfo()
		},
		methods: {
			exit(){
				uni.showModal({
					content:'确定退出登录？',
					success:res=>{
						if (res.confirm) {
							uni.removeStorageSync('token');
							uni.removeStorageSync('userInfo');
							uni.removeStorageSync('user_site_id');
							uni.removeStorageSync('site_id');
							uni.removeStorageSync('store_id');
							uni.$emit('loginChange', { isLogin: false, site_id: 0 });
							uni.switchTab({
								url:'/pages/store/index'
							});
						}
					}
				})
			},
			userinfo() {
				userinfo().then(res => {
					this.infos = res.data
				})
			},
			gonext(e){
				uni.navigateTo({
					url:`/otherpages/${e}/${e}`
				})
			}
		}
	}
</script>

<style lang="scss">
	.container {
		background: #fff;
		border-radius: 24rpx;
		margin: 32rpx;

		>view {
			padding: 30rpx 28rpx;
		}
	}

	.main {
		margin: 20rpx 32rpx 32rpx 32rpx;

		>view:first-child {
			width: 100rpx;
			height: 100rpx;
			position: relative;

			>image:first-child {
				width: 100%;
				height: 100%;
				border-radius: 50%;
			}

			>image:last-child {
				width: 32rpx;
				height: 32rpx;
				position: absolute;
				right: 0;
				bottom: 0;
			}
		}
	}
</style>