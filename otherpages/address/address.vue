<template>
	<view>
		<uv-sticky bg-color="#FFF">
			<nvabar type='1' title='收货地址' titleStyle='color:#222'></nvabar>
		</uv-sticky>
		<view class="m-r-20 m-l-20">
			<view v-for="(item,index) in list" class="items" :key="index">
				<view class="font-30 font-weight-6">
					{{item.name}}
					<text class="p-l-20"></text>
					{{item.mobile}}
				</view>
				<view class="m-t-8" style="color: #555;">
					{{item.full_address.split('-').join('')}}{{item.address}}
				</view>
				<view class="acea-row row-middle row-between m-t-20">
					<view class="acea-row row-middle" @click="defas(item)">
						<radio value="" :checked="item.is_default==1" />
						<text class="defa" v-if="item.is_default==1">已默认</text>
						<text class="defa fcolor" v-else>设为默认</text>
					</view>
					<view class="acea-row row-middle">
						<view class="edit m-r-16" @click="delets(item)">删除</view>
						<view class="edit" @click="edits(item.id)">编辑</view>
					</view>
				</view>
			</view>
		</view>
		<view class="btms iphone-x">
			<view class="acea-row row-middle row-center" @click="adds">
				<uv-icon name="plus-circle" color="#fff"></uv-icon>
				<view class="w-16"></view>
				新建收货地址</view>
		</view>
	</view>
</template>

<script>
	import {memberaddresspage,setdefault,memberaddressDelete} from "@/common/js/api.js"
	export default {
		data() {
			return {
				list:[]
			};
		},
		onLoad() {
		},
		onShow() {
			this.getlist()
		},
		methods:{
			delets(e){
				uni.showModal({
					content:'确定要删除当前地址？',
					success:res=>{
						if(res.confirm){
							let params={
								id:e.id
							}
							memberaddressDelete(params).then(res=>{
								this.getlist()
							})
						}
					}
				})
			},
			defas(e){
				if(e.is_default==1) return
				let params={
					id:e.id
				}
				setdefault(params).then(res=>{
					this.getlist()
				})
			},
			getlist(){
				let params={
					page:1,
					page_size:30
				}
				memberaddresspage(params).then(res=>{
					this.list=res.data.list
				})
			},
			edits(e){
				uni.navigateTo({
					url:'/otherpages/addAddress/addAddress?id='+e
				})
			},
			adds(){
				uni.navigateTo({
					url:'/otherpages/addAddress/addAddress'
				})
			}
		}
	}
</script>

<style lang="scss">
	.items{
		padding: 32rpx;
		background: #fff;
		margin-top: 20rpx;
		border-radius: 32rpx;
	}
	.defa {
		font-weight: 400;
		font-size: 24rpx;
		color: #F55606;
	}
	
	.fcolor {
		color: #999999;
	}
	radio {
		transform: scale(0.7);
	}
	.edit{
		width: 100rpx;
		height: 48rpx;
		background: #FFFFFF;
		border-radius: 24rpx;
		border: 1rpx solid #DDDDDD;
		text-align: center;
		line-height: 48rpx;
	}
	.btms {
		position: fixed;
		bottom: 0;
		left: 0;
		background: #fff;
		text-align: center;
		width: 100vw;

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