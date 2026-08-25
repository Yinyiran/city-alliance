<template>
	<view>
		<!-- <uv-sticky> -->
			<nvabar title='简介'></nvabar>
		<!-- </uv-sticky> -->
		<image src="/static/bg1.png" class="bgs" mode=""></image>
		<view class="part1">
			<image src="/static/bg6.png" mode=""></image>
			<image src="/static/t.png" mode=""></image>
			<image src="/static/btn1.png" @click="$util.redirectTo('/otherpages/settle/settle')" mode=""></image>
			<view class="">{{shop_num}}家</view>
		</view>
		<view class="acea-row row-middle parts">
			<view v-for="(item,index) in list" :key="index">
				<image :src="$util.img(item.store_logo)" mode=""></image>
			</view>
		</view>
		<view class="container">
			<view class="acea-row row-middle row-center m-b-32">
				<image src="/static/lline.png" mode=""></image>
				<text>城盟联创</text>
				<image src="/static/rline.png" mode=""></image>
			</view>
			<video object-fit="contain" v-if="info.video_url_info" :src="$util.img(info.video_url_info)"></video>
			<rich-text :nodes="$util.replaceImg(info.content_info)"></rich-text>
		</view>
	</view>
</template>

<script>
	import {websiteInfo,Synopsis,websiteStore} from "@/common/js/api.js"
	export default {
		data() {
			return {
				shop_num:0,
				info:{},
				list:[]
			};
		},
		onLoad() {
			this.Synopsis()
			this.getinfo()
			this.websiteStore()
		},
		methods:{
			websiteStore(){
				websiteStore().then(res=>{
					console.log(res);
					this.list=res.data
				})
			},
			Synopsis(){
				Synopsis().then(res=>{
					this.info=res.data
				})
			},
			getinfo() {
				let params = {
					website_id: uni.getStorageSync('website_id')
				}
				websiteInfo(params).then(res => {
					this.shop_num = res.data.shop_num
				})
			}
		}
	}
</script>

<style lang="scss">
	.parts{
		background: #fff;
		margin: 32rpx;
		padding: 28rpx;
		border-radius: 16rpx;
		>view{
			width: calc((100% - 36rpx)/4);
			height: 52rpx;
			border: 3rpx  #F0F0F0 solid;
			margin-right: 12rpx;
			margin-top: 12rpx;
			box-sizing: border-box;
			text-align: center;
			image{
				width: 100%;
				height: 100%;
			}
		}
		>view:nth-child(4n){
			margin-right: 0;
		}
	}
	page{
		background: #FFFFFF;
	}
	.container{
		margin: 32rpx;
		border-radius: 24rpx;
		// padding: 32rpx;
		video{
			width: 100%;
			height: 386rpx;
		}
		>view:first-child{
			text{
				font-weight: 600;
				font-size: 32rpx;
				margin: 0 24rpx;
				color: #222222;
			}
			image{
				width: 129rpx;
				height: 4rpx;
			}
		}
	}
	.bgs {
		position: absolute;
		width: 100vw;
		height: 634rpx;
		top: 0;
		left: 0;
		z-index: -1;
	}
	.part1 {
		margin: 0 32rpx;
		margin-top: 20rpx;
		position: relative;
		height: 210rpx;
	
		>image:first-child {
			width: 100%;
			height: 100%;
			position: absolute;
			right: 0;
			top: 0;
			z-index: 1;
		}
	
		>image:nth-child(2) {
			position: absolute;
			top: 40rpx;
			left: 40rpx;
			width: 220rpx;
			height: 58rpx;
			z-index: 2;
		}
	
		>image:nth-child(3) {
			width: 276rpx;
			height: 136rpx;
			position: absolute;
			right: 40rpx;
			bottom: 18rpx;
			z-index: 2;
		}
	
		>view {
			position: absolute;
			bottom: 40rpx;
			left: 40rpx;
			font-weight: bold;
			font-size: 56rpx;
			color: #222222;
			z-index: 2;
		}
	}
</style>
