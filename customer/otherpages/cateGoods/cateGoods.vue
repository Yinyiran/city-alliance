<template>
	<view>
		<uv-sticky>
			<nvabar type='1' :title='catename' titleStyle='color:#222'></nvabar>
		</uv-sticky>
		<image src="/static/bg5.png" class="bgs" mode=""></image>
		<view class="swipers" v-if="banner.length">
			<swiper :indicator-dots="false" :autoplay="true" @change="onchange" :interval="3000" :duration="1000">
				<swiper-item v-for='(item,index) in banner' :key="index">
					<view class="swiper-item">
						<image :src="$util.img(item)" mode=""></image>
					</view>
				</swiper-item>
			</swiper>
			<view class="dots">
				<view v-for="(item,index) in banner.length" :class="{'on':current==index}" :key="index"></view>
			</view>
		</view>
		<view class="m-t-24"  style="background: #fff;min-height: 30vh;">
			<filter @sorts='sorts'></filter>
			<view class="h-24"></view>
			<view class="m-r-32 m-l-32">
				<goods :list="list"></goods>
			</view>
			<view v-if="!loading&&!list.length" class="empty-wrap">
				<image src="/otherpages/static/empty1.png" mode="" class="empty-img"></image>
				<view class="empty-txt">该分类下暂无商品</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {goodscategory,goodslist} from "@/common/js/api.js"
	export default {
		data() {
			return {
				infos:{},
				banner:[],
				cateid:"",
				catename:'',
				page:1,
				last_page:1,
				list:[],
				loading:true,
				select:-1,
				clicknum:0
			};
		},
		onReachBottom() {
			if(this.last_page>this.page){
				this.page++
				this.goodslist()
			}
		},
		onLoad(res) {
			this.cateid=res.id
			// this.catename=res.name
			this.goodscategory()
			this.goodslist()
		},
		methods:{
			sorts(e){
				console.log(e);
				this.select=e.select
				this.clicknum=e.clicknum
				this.page=1
				this.goodslist()
			},
			onchange(e){
				console.log(e);
			},
			goodslist(){
				this.loading=true
				let params={
					page:this.page,
					website_id:uni.getStorageSync('website_id'),
					goods_type:0,
					category_id:this.cateid,
					order:this.select==2?'sale_num':this.select==3?'distance':'',
					sort:this.clicknum==1?'asc':this.clicknum==2?'desc':'',
					site_id:''
				}
				goodslist(params).then(res=>{
					let rows=res&&res.data&&res.data.list?res.data.list:[]
					this.list=this.page>1?this.list.concat(rows):rows
					this.last_page=res&&res.data&&res.data.page_count?res.data.page_count:1
				}).catch(()=>{}).finally(()=>{
					this.loading=false
				})
			},
			goodscategory(){
				let params={
					category_id:this.cateid
				}
				goodscategory(params).then(res=>{
					this.catename=res.data.category_name
					this.banner=res.data.image_adv?res.data.image_adv.split(','):[]
				})
			}
		}
	}
</script>

<style lang="scss">
	page{
		background: #fff;
	}
	.swipers {
		margin: 12rpx 32rpx 0 32rpx;
		height: 290rpx;
		border-radius: 16rpx;
		overflow: hidden;
		position: relative;
	
		swiper {
			width: 100%;
			height: 100%;
	
			view,
			image {
				width: 100%;
				height: 100%;
				border-radius: 16rpx;
			}
		}
	
		.dots {
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			bottom: 20rpx;
			display: flex;
			align-items: center;
			justify-content: center;
	
			>view {
				width: 24rpx;
				height: 8rpx;
				background: #FFFFFF;
				border-radius: 6rpx;
				opacity: 0.28;
				margin-right: 8rpx;
			}
	
			.on {
				background: #FFFFFF;
				opacity: 1;
			}
		}
	}
	.bgs {
		width: 100vw;
		height: 556rpx;
		position: fixed;
		top: 0;
		left: 0;
		z-index: -1;
	}
	.empty-wrap {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding: 40rpx 0 80rpx 0;

		.empty-img {
			width: 340rpx;
			height: 340rpx;
		}

		.empty-txt {
			margin-top: 24rpx;
			font-size: 32rpx;
			color: #999;
		}
	}
</style>
