<template>
	<view>
		<view class="teamtp">
			<nvabar title='我的团队'></nvabar>
			<image src="/otherpages/static/teambg.png" mode=""></image>
			<view class="aaa acea-row row-middle m-t-32 m-l-32 m-r-32">
				<uv-input placeholder="请输入关键词搜索" v-model="keyword" :customStyle="customStyle" border="none">
					<template v-slot:prefix>
						<uv-icon name="search"></uv-icon>
					</template>
					<template v-slot:suffix>
						<view class="search" @click="search">搜索</view>
					</template>
				</uv-input>
				<image src="/otherpages/static/btn2.png" @click='poster' class="w-192 h-64 m-l-24" mode=""></image>
			</view>
			<view class="">
				<view class="">团队成员数量</view>
				<view class=""><text>{{today_num}}</text>人</view>
			</view>
			<view class="acea-row row-middle row-around">
				<image src="/otherpages/static/teambg1.png" mode=""></image>
				<view class="m-t-22">
					<view class="">今日新增</view>
					<view class="font-weight-6">{{today_num}}</view>
				</view>
				<view class="m-t-22">
					<view class="">本月新增</view>
					<view class="font-weight-6">{{month_num}}</view>
				</view>
			</view>
			<view class="h-32"></view>
		</view>
		<view class="m-r-32 m-l-32">
			<uv-subsection :list="list1" :current="current" custom-style="height: 60rpx;border-radius: 30rpx;"
				custom-item-style="border-radius: 30rpx;" activeColor='#F55606' bgColor="#FFFFFF" mode="button"
				@change="change"></uv-subsection>
				<view class="">
					<view v-for="(item,index) in list" class="items" :key="index">
						<view class="acea-row row-middle">
							<image :src="$util.img(item.headimg)" class="w-74 h-74 bor-40 m-r-12" mode=""></image>
							<view class="">
								<view class="font-30">{{item.nickname}}</view>
								<view class="font-22" style="color: #555;">手机号: {{item.mobile}}</view>
							</view>
						</view>
						<view class="acea-row row-middle row-between">
							<view class="as">
								<view class="">消费额 (元)</view>
								<view class="">{{item.order_money}}</view>
							</view>
							<view class="as">
								<view class="">带单金额 (元)</view>
								<view class="">{{item.dai_money}}</view>
							</view>
							<view class="as">
								<view class="">团队数量 (人)</view>
								<view class="">{{item.team_num}}</view>
							</view>
						</view>
						<view class="font-22 m-t-12 m-b-4" style="color: #999999;">注册时间: {{item.reg_time}}</view>
						<view class="font-22" v-if="item.member_level_time" style="color: #999999;">成为会员: {{item.member_level_time}}</view>
					</view>
				</view>
		</view>
	</view>
</template>

<script>
	import { teamList } from "@/common/js/api.js"
	export default {
		data() {
			return {
				customStyle: "background: #F5F5F5;height: 64rpx;borderRadius:32rpx;paddingLeft:24rpx;",
				keyword:"",
				list1: [
					'全部', '钻石会员', '黑钻会员', '合伙人'
				],
				current:0,
				today_num:0,
				month_num:0,
				today_num:0,
				last_page:1,
				page:1,
				list:[]
			};
		},
		onLoad() {
			this.getlist()
		},
		onReachBottom() {
			if(this.last_page>this.page){
				this.page++
				this.getlist()
			}
		},
		methods:{
			poster(){
				uni.navigateTo({
					url:'/otherpages/poster/poster'
				})
			},
			change(index) {
				this.current = index;
				this.getlist()
			},
			search(){
				this.page=1
				this.getlist()
			},
			getlist(){
				let params={
					search:this.keyword,
					type:this.current,
					page:this.page
				}
				teamList(params).then(res=>{
					this.list=this.page>1?this.list.concat(res.data.list):res.data.list
					this.last_page=res.data.page_count
					this.today_num=res.data.today_num
					this.month_num=res.data.month_num
					this.today_num=res.data.today_num
				})
			},
		}
	}
</script>

<style lang="scss">
	.items{
		margin-top: 20rpx;
		background: #FFFFFF;
		border-radius: 24rpx;
		padding: 28rpx;
		>view:nth-child(2){
			padding:0 20rpx;
			margin-top: 20rpx;
			height: 100rpx;
			background: #F5F5F5;
			border-radius: 12rpx;
			box-sizing: border-box;
			.as{
				width: calc(100% / 3);
				padding-left: 20rpx;
				box-sizing: border-box;
				position: relative;
				>view:first-child{
					font-weight: 400;
					font-size: 20rpx;
					color: #999999;
				}
				>view:nth-child(2){
					font-weight: 600;
					font-size: 22rpx;
					color: #222222;
				}
			}
			.as:not(:first-child)::before{
				content: '';
				position: absolute;
				left: 0;
				top: 50%;
				transform: translateY(-50%);
				width: 1rpx;
				height: 60rpx;
				background: #ddd;
			}
		}
	}
	page{
		background: #F5F5F5;
	}
	.search {
		font-weight: 400;
		font-size: 30rpx;
		color: #222222;
		padding: 0 28rpx;
		border-left: #DDDDDD 1rpx solid;
		height: 42rpx;
	}
	.teamtp {
		height: 620rpx;
		position: relative;

		>image:nth-child(2) {
			position: absolute;
			left: 0;
			top: 0;
			z-index: -1;
			width: 100%;
			height: 100%;
		}
		>view:nth-child(4){
			margin-left: 40rpx;
			margin-top: 62rpx;
			view,text{
				color: #FFFFFF;
			}
			>view:last-child{
				text{
					font-weight: 600;
					font-size: 70rpx;
				}
			}
		}
		>view:nth-child(5){
			margin: 0 32rpx;
			margin-top: 26rpx;
			position: relative;
			image{
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
				height: 110rpx;
			}
			view{
				text-align: center;
				color: #fff;
				z-index: 99;
				position: relative;
			}
		}
		>view:last-child{
			background: #F5F5F5;
			border-radius: 24rpx 24rpx 0rpx 0rpx;
			position: relative;
			z-index: 99;
			position: absolute;
			bottom: 0;
			left: 0;
			width: 100%;
		}
	}
</style>