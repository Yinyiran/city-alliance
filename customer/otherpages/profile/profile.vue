<template>
	<view>
		<view class="container">
			<view class="acea-row row-middle row-between p-28">
				<text class="font-32">用户头像</text>
				<view class="acea-row row-middle" @click="upomg" >
					<image :src="$util.img(infos.headimg)" class="w-68 h-68 bor-60 m-r-12" mode=""></image>
					<uv-icon name="arrow-right" color="#9BA3C2" size="16"></uv-icon>
				</view>
			</view>
			<view class="acea-row row-middle row-between p-28">
				<text class="font-32">用户昵称</text>
				<view class="acea-row row-middle" @click="open">
					<text class="font-32 m-r-12">{{infos.nickname}}</text>
					<uv-icon name="arrow-right" color="#9BA3C2" size="16"></uv-icon>
				</view>
			</view>
		</view>
		<uv-popup ref="popup" :round='10' bgColor="#ffffff00" :safeAreaInsetBottom="false">
			<view class="contens">
				<view class="">
					<view class="">用户昵称</view>
					<uv-input v-model="name" border="none" clearable="true" :customStyle="customStyle"></uv-input>
					<view class="" @click="submit">保存</view>
					
				</view>
				<view class="closes" @click="closesd">
					<uv-icon name="close-circle" color="#1A1A1A" size="32"></uv-icon>
				</view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
	import {
		userinfo,
		modifyheadimg,
		modifynickname
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				infos: {},
				customStyle:"background: #F5F5F5;height: 108rpx;border-radius: 12rpx;padding:0 32rpx",
				name:''
			};
		},
		onShow() {
			this.userinfo()
		},
		methods:{
			submit(){
				this.infos.nickname=this.name
				let params={
					nickname:this.name
				}
				modifynickname(params).then(res=>{
					this.closesd()
				})
			},
			closesd(){
				this.$refs.popup.close()
			},
			open(){
				this.name=this.infos.nickname
				this.$refs.popup.open('center')
			},
			upomg(){
				this.$util.upload(1,{},res=>{
					this.infos.headimg=res[0].path
					let params={
						headimg:res[0].path
					}
					modifyheadimg(params)
				},'')
			},
			userinfo() {
				userinfo().then(res => {
					this.infos = res.data
				})
			},
		}
	}
</script>

<style lang="scss">
	.contens{
		padding-bottom: 36rpx;
		position: relative;
		>view:first-child{
			box-sizing: border-box;
			padding: 0 50rpx;
			width: 630rpx;
			position: relative;
			padding-bottom: 104rpx;
			height: 498rpx;
			background: #FFFFFF;
			border-radius: 24rpx;
			>view:first-child{
				text-align: center;
				font-weight: 600;
				font-size: 36rpx;
				color: #1A1A1A;
				padding: 68rpx 0 40rpx 0;
			}
			>view:nth-child(3){
				height: 96rpx;
				background: linear-gradient( 90deg, #F5AB18 0%, #F55606 100%);
				border-radius: 48rpx;
				line-height: 96rpx;
				font-weight: 600;
				font-size: 32rpx;
				color: #FFFFFF;
				text-align: center;
				margin-top: 32rpx;
			}
			
		}
		.closes{
			background: #fff;
			position: absolute;
			bottom: 0;
			left: 50%;
			transform: translateX(-50%);
			padding: 8rpx;
			border-radius: 50%;
		}
	}
	.container{
		margin: 20rpx 32rpx;
		border-radius: 24rpx;
		background: #fff;
	}
</style>
