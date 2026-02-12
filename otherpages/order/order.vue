<template>
	<view>
		<nvabar :title="title"  titleStyle='color:#222' type="2"  v-if="check==3"></nvabar>
		<nvabar :title="title"  v-else></nvabar>
		<image :src="bgs" class="bgs" mode=""></image>
		<view class="dans">
			<image src="/static/bg6.png" mode=""></image>
			<view class="acea-row row-middle row-between p-t-32">
				<view class="nums">
					<view class="">总订单数 (单)</view>
					<view class="">{{total_count}}</view>
				</view>
				<view class="line"></view>
				<view class="nums">
					<view class="">销售总额 (元)</view>
					<view class="">{{total_money}}</view>
				</view>
			</view>
		</view>
		<view class="wbg">
			<uv-tabs :list="list1" @click='onclick' :current="check" :scrollable="false" lineWidth="30"
				lineColor="linear-gradient( 90deg, #F5AB18 0%, #F55606 100%)" :activeStyle="{
					fontWeight: 'bold'
				}" :inactiveStyle="{
					color: '#222',
					fontSize:'28rpx'
				}" itemStyle="height: 44px;"></uv-tabs>
		</view>
		<view class="wbg acea-row row-middle row-between p-l-32 p-r-32 p-t-12 p-b-12">
			<view class="w-348 m-r-16">
				<uv-subsection :list="list2" :current="current" activeColor='#1D1E24'
					custom-style="height: 60rpx;border-radius: 30rpx;" custom-item-style="border-radius: 30rpx;"
					@change="change"></uv-subsection>
			</view>
			<uv-input placeholder="输入关键词搜索" border="none" @confirm='confirms' shape='circle' :customStyle="customStyle">
				<template v-slot:prefix>
					<uv-icon name="search"></uv-icon>
				</template>
			</uv-input>
		</view>
		<view class="container" v-if="check<3">
			<view v-for="(item,index) in list" :key="index">
				<view class="acea-row row-middle">
					<image src="/static/stores.png" class="w-32 h-32 m-r-8" mode=""></image>
					<text class="font-32 font-weight">{{item.site_name}}</text>
					<!-- <image src="/static/go2.png" class="w-24 h-24 m-l-8" mode=""></image> -->
				</view>
				<view class="acea-row row-middle m-t-28" v-for="(items,indexs) in item.goodslist">
					<image :src="$util.img(items.sku_image)" class="w-140 h-140 bor-16 m-r-20" mode=""></image>
					<view class="widths h-140 acea-row row-column row-between" style="flex-wrap: nowrap;">
						<view class="text-more-2 font-28">{{items.sku_name}}</view>
						<view class="acea-row row-middle row-between">
							<view class="">
								<text class="font-30 font-weight-6">{{items.point}}</text>
								积分
							</view>
							<view class="" style="color: #999999;">共{{items.num}}件</view>
						</view>
					</view>
				</view>
				<view class="acea-row row-middle row-between m-t-20 users">
					<view class="acea-row row-middle">
						<image :src="$util.img(item.headimg)" v-if="item.headimg" class="w-74 h-74 bor-40 m-r-12" mode=""></image>
						<image v-else src="/static/avatar.png" class="w-74 h-74 bor-40 m-r-12" mode=""></image>
						<view class="font-28 h-74 acea-row row-column row-between">
							<view class="">{{item.nickname}}</view>
							<view class="font-22" style="color: #999999;">{{item.create_time}}</view>
						</view>
					</view>
					<view class="mems acea-row row-middle row-center">
						<image src="/static/vip1.png" class="w-24 h-24 m-r-4" mode=""></image>
						{{item.member_level_name}}
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {orderlist} from "@/common/js/api.js"
	export default {
		data() {
			return {
				check: 0,
				list2: ['今日订单', '本月订单'],
				customStyle: "background: #F5F5F5;height: 64rpx;padding:0 28rpx;",
				current: 0,
				list1: [
					// {
					// 	name: '全部订单'
					// },
					{
						name: '自营订单'
					},
					{
						name: '带单订单'
					},
					{
						name: '贡献订单'
					}
				],
				list:[],
				page:1,
				last_page:1,
				total_count:0,
				total_money:0,
				searchtxt:''
			};
		},
		computed: {
			title() {
				return this.check == 0 ? '总订单' : this.check == 1 ? '自营订单' : this.check == 2 ? "带单订单" : '贡献订单'
			},
			bgs() {
				return this.check == 0 ? '/static/order1.png' : this.check == 1 ? '/static/order2.png' : this.check == 2 ?
					'/static/order3.png' :
					'/static/order4.png'
			},
		},
		onLoad(res) {
			if (res.current) {
				this.check = res.current
			}
			this.getlist()
		},
		methods:{
			confirms(){
				this.page=1
				this.getlist()
			},
			change(e){
				this.current=e
				this.getlist()
			},
			getlist(){
				let params={
					page:this.page,
					ordertype:Number(this.check)+1,
					selectby:this.current==0?'day':'month',
					searchtxt:this.searchtxt
				}
				orderlist(params).then(res=>{
					this.total_count=res.data.total_count
					this.total_money=res.data.total_money
					this.list=this.page>1?this.list.concat(...res.data.list):res.data.list
					this.last_page=res.data.page_count
				})
			},
			onclick(e){
				this.check=e.index
				this.page=1
				this.getlist()
			},
		}
	}
</script>

<style lang="scss">
	.container {
		margin: 20rpx 32rpx;

		>view {
			padding: 28rpx;
			background: #fff;
			border-radius: 24rpx;
			margin-bottom: 20rpx;

			.widths {
				width: calc(100% - 140rpx - 20rpx);
			}

			.users {
				background: #F5F5F5;
				border-radius: 12rpx;
				padding: 20rpx 24rpx;
			}

			.mems {
				background: linear-gradient(90deg, #ADFAFF 0%, #00A2AD 100%);
				border-radius: 16rpx;
				height: 32rpx;
				width: 124rpx;
				font-weight: 400;
				font-size: 20rpx;
				color: #FFFFFF;
			}
		}
	}

	.wbg {
		background: #fff;
	}

	.bgs {
		position: absolute;
		top: 0;
		left: 0;
		z-index: -1;
		height: 430rpx;
		width: 100vw;
	}

	.dans {
		height: 220rpx;
		margin-top: 34rpx;
		position: relative;
		padding: 0 56rpx;

		>image {
			position: absolute;
			left: 0;
			top: 0;
			width: 100%;
			height: 220rpx;
		}

		view {
			position: relative;
			z-index: 9;
			color: #fff;
		}

		.nums {
			width: calc((100% - 68rpx)/2);
			text-align: center;

			>view:first-child {
				font-weight: 400;
				font-size: 24rpx;
				color: #FFFFFF;
			}

			>view:last-child {
				font-size: 40rpx;
				color: #FFFFFF;
				font-weight: bold;
			}
		}

		.line {
			width: 2rpx;
			height: 82rpx;
			background: #555555;
		}
	}
</style>