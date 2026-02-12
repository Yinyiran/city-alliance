<template>
	<view>
		<uv-sticky bg-color="#fff">
			<nvabar title="团队总人数" bgcolor='#ffffff' :type='1' :titleStyle='titleStyle'></nvabar>
			<uv-tabs :list="list1" @click='onclick' :scrollable="false" lineWidth="30"
				lineColor="linear-gradient( 90deg, #F5AB18 0%, #F55606 100%)" :activeStyle="{
					fontWeight: 'bold'
		    	}" :inactiveStyle="{
					color: '#222',
					fontSize:'28rpx'
				}" itemStyle="height: 34px;"></uv-tabs>
			<view class="" style="margin: 12rpx 32rpx;">
				<uv-input placeholder="请输入关键词搜索" v-model="searchtxt" :customStyle="customStyle" border="none">
					<template v-slot:prefix>
						<uv-icon name="search"></uv-icon>
					</template>
					<template v-slot:suffix>
						<view class="search" @click="search">搜索</view>
					</template>
				</uv-input>
			</view>
		</uv-sticky>
		<view class="nums">
			<view class="">团队总人数 (人)</view>
			<view class="">{{count}}</view>
			<image src="/static/add.png" v-if="ismanager" @click="gonext" mode=""></image>
		</view>
		<view class="container" v-if="list.length>0">
			<view class="acea-row row-middle row-between p-24 titles" style="padding-top: 0;">
				<view class="acea-row row-middle">
					<text class="m-r-20">排名</text>
					<text>名称</text>
				</view>
				<text>积分</text>
			</view>
			<view v-for="(item,index) in list" class="m-b-20 defas acea-row row-middle row-between"
				:class="{'rankbg1':index==0,'rankbg2':index==1,'rankbg3':index==2}" :key="index">
				<view class="acea-row row-middle">
					<view class="ranks">
						<image src="/static/rank1.png" v-if="index==0" mode=""></image>
						<image src="/static/rank2.png" v-else-if="index==1" mode=""></image>
						<image src="/static/rank3.png" v-else-if="index==2" mode=""></image>
						<image src="/static/rank.png" v-else mode=""></image>
						<text v-if="index>2">{{index+1}}</text>
					</view>
					<image :src="$util.img(item.headimg)" class="w-52 h-52 bor-50 m-l-24" mode=""></image>
					<text class="font-28 m-l-12" style="color: #222;">{{item.nickname}}</text>
				</view>
				<view class="font-28" style="color: #222;">{{item.point}}</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {getteam} from "@/common/js/api.js"
	export default {
		data() {
			return {
				titleStyle: "color:#222",
				count:0,
				customStyle: "background: #F5F5F5;height: 64rpx;borderRadius:32rpx;paddingLeft:24rpx;",
				list1: [{
						name: '团队总人数'
					},
					{
						name: '钻石会员'
					},
					{
						name: '黑钻会员'
					},
					{
						name: '合伙人'
					}
				],
				current:0,
				page:1,
				searchtxt:'',
				list:[],
				ismanager:0
			};
		},
		onLoad(res) {
			this.current=res.current
		},
		onShow() {
			this.getlist()
		},
		methods: {
			search(){
				this.getlist()
			},
			getlist(){
				let params={
					page:this.page,
					member_level:this.current,
					searchtxt:this.searchtxt
				}
				getteam(params).then(res=>{
					this.count=res.data.count
					this.list=res.data.list
					this.ismanager=res.data.ismanager
				})
			},
			gonext(){
				uni.navigateTo({
					url:'/otherpages/employee/employee'
				})
			},
			onclick(e) {
				this.current=e.index
				this.getlist()
			}
		}
	}
</script>

<style lang="scss">
	.container {
		margin: 32rpx;
		background: #FFFFFF;
		border-radius: 24rpx;
		padding: 24rpx;
		.titles{
			text{
				font-weight: 400;
				font-size: 24rpx;
				color: #999999;
			}
		}
		.my{
			font-weight: 600;
			font-size: 28rpx;
			color: #F55606;
		}


		.defas {
			border-radius: 12rpx;
			height: 104rpx;
			padding: 0 24rpx;
			background: #F5F5F5;
		}

		.rankbg1 {
			background: linear-gradient(90deg, #FFF1D6 0%, #F6F8FC 100%);
		}

		.rankbg2 {
			background: linear-gradient(90deg, #E3EFFC 0%, #F6F8FC 100%);
		}

		.rankbg3 {
			background: linear-gradient(90deg, #FFF0E6 0%, #FFFCFA 100%);
		}

		.ranks {
			width: 58rpx;
			height: 64rpx;
			position: relative;

			>text {
				font-weight: 400;
				font-size: 32rpx;
				color: #222222;
				position: absolute;
				left: 50%;
				transform: translateX(-50%);
				bottom: 12rpx;
			}

			>image {
				width: 100%;
				height: 100%;
			}
		}
	}


	.nums {
		padding: 24rpx 32rpx;
		background: #fff;
		position: relative;
		margin: 32rpx;
		border-radius: 24rpx;

		>view:first-child {
			font-weight: 400;
			font-size: 24rpx;
			color: #999999;
		}

		>view:nth-child(2) {
			font-size: 40rpx;
			color: #222222;
			font-weight: bold;
		}

		>image {
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
			width: 204rpx;
			height: 60rpx;
			right: 32rpx;
		}
	}

	.search {
		font-weight: 400;
		font-size: 30rpx;
		color: #222222;
		padding: 0 28rpx;
		border-left: #DDDDDD 1rpx solid;
		height: 42rpx;
	}
</style>