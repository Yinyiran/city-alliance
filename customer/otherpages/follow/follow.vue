<template>
	<view>
		<uv-sticky>
			<view class="bgs">
				<nvabar title='关注列表'></nvabar>
				<image src="/otherpages/static/bg8.png" mode=""></image>
				<view class="aaa">
					<uv-input placeholder="请输入关键词搜索" v-model="keyword" :customStyle="customStyle" border="none">
						<template v-slot:prefix>
							<uv-icon name="search"></uv-icon>
						</template>
						<template v-slot:suffix>
							<view class="search" @click="search">搜索</view>
						</template>
					</uv-input>
				</view>
				<view class="whits m-l-40">
					<view class="">店铺关注数量</view>
					<view class="font-weight-6 font-70">{{list.length}}</view>
				</view>
			</view>
		</uv-sticky>
		<view class="container">
			<scroll-view scroll-y="true" @scrolltolower="scrolltolower">
				<uv-swipe-action>
					<view v-for="(item,index) in list" class="m-b-20" :key="index">
						<uv-swipe-action-item :options="options" @click='onclick' :name='item.site_id'>
							<view class="p-24">
								<view class="acea-row row-middle row-between">
									<image :src="$util.img(item.logo)" class="logos" mode=""></image>
									<view class="rights acea-row row-between row-column h-180">
										<view class="acea-row row-middle row-between">
											<view class="font-28 font-weight-6 acea-row row-middle">
												<image src="/static/stores.png" class="w-28 h-28 m-r-8" mode=""></image>
												<view class="text-more w-200">{{item.site_name}}</view>
												<image src="/otherpages/static/go3.png" class="w-26 h-26 m-l-8" mode=""></image>
											</view>
											<view class="font-22" style="color: #555;">{{item.distance}}</view>
										</view>
										<view class="font-22 m-t-8 m-b-16"></view>
										<view class="acea-row row-middle row-between">
											<view class="w1">
												<view class="acea-row row-middle m-b-12 text-more">
													<image src="/otherpages/static/clock.png" class="w-22 h-22 m-r-4" mode="">
													</image>
													营业时间 {{item.open_date}}
												</view>
												<view class="acea-row row-middle ">
													<image src="/otherpages/static/map4.png" class="w-22 h-22 m-r-4" mode="">
													</image>
													<view class="w-200 text-more">{{item.address}}</view>
												</view>
											</view>
											<view class="w2 acea-row row-middle">
												<view class="pay-btn m-r-12" @click.stop="goPay(item)">付款</view>
												<view class="m-r-12" @click="open1(item)">
													<image src="/otherpages/static/map3.png" mode=""></image>
												</view>
												<view class="" @click="calls(item)">
													<image src="/otherpages/static/phone1.png" mode=""></image>
												</view>
											</view>
										</view>
									</view>
								</view>
								<view class="acea-row row-middle info1">
									{{item.total_num}}人关注
									<view class="line"></view>
									订单数 {{item.order_num}}
									<view class="line"></view>
									关注时间 {{formatTime(item.subscribe_time)}}
								</view>
							</view>
						</uv-swipe-action-item>
					</view>

				</uv-swipe-action>
			</scroll-view>
		</view>
	</view>
</template>

<script>
	import {membershoppages,shopmemberDel} from "@/common/js/api.js"
	export default {
		data() {
			return {
				customStyle: "background: #F5F5F5;height: 64rpx;borderRadius:32rpx;paddingLeft:24rpx;",
				options: [{
					text: '取消收藏',
					style: {
						backgroundColor: '#EBEBEB',
						color:"#222222"
					}
				}],
				page:1,
				keyword:"",
				list:[],
				last_page:1,
				count:0
			};
		},
		onShow() {
			this.getlist()
		},
		methods:{
			scrolltolower(){
				if(this.last_page>this.page){
					this.page++
					this.getlist()
				}
			},
			search(){
				this.page=1
				this.getlist()
			},
			goPay(e) {
				uni.navigateTo({
					url: `/otherpages/quickPay/quickPay?site_id=${e.site_id}`
				})
			},
			calls(e) {
				uni.makePhoneCall({
					phoneNumber: e.telphone
				})
			},
			open1(e){
				uni.openLocation({
					latitude:Number(e.latitude),
					longitude:Number(e.longitude),
					name:e.address
				})
			},
			onclick(e){
				uni.showModal({
					content:'确定取消关注当前店铺?',
					success:res=>{
						if(res.confirm){
							let params={
								site_id:e
							}
							shopmemberDel(params).then(resA=>{
								if(resA.code==0){
									this.page=1
									this.getlist()
								}
							})
						}
					}
				})
			},
			getlist(){
				let params={
					page:this.page,
					keyword:this.keyword
				}
				membershoppages(params).then(res=>{
					this.list=this.page>1?this.list.concat(...res.data.list):res.data.list
					this.last_page=res.data.page_count
					this.count=res.data.count
				})
			},
			formatTime(val) {
				if (!val || Number(val) <= 0) return '--';
				return this.$util.timeStampTurnTime(val, 'ymd') || '--';
			},
		}
	}
</script>

<style lang="scss">
	.info1 {
		font-weight: 400;
		font-size: 22rpx;
		color: #555555;
		background: #F5F5F5;
		border-radius: 12rpx;
		margin-top: 16rpx;
		height: 54rpx;
		padding: 0 20rpx;

		.line {
			width: 1rpx;
			height: 22rpx;
			background: #DCDFEB;
			margin: 0 20rpx;
		}
	}

	.w1 {
		width: calc(100% - 240rpx);

		view {
			font-weight: 400;
			font-size: 22rpx;
			color: #999999;
			flex-wrap: nowrap;
		}
	}

	.w2 {
		.pay-btn {
			width: 96rpx;
			height: 52rpx;
			background: linear-gradient(135deg, #FF6A00 0%, #F51D1D 100%);
			color: #fff;
			font-size: 24rpx;
			font-weight: 600;
			border-radius: 26rpx;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		view:not(.pay-btn) {
			width: 52rpx;
			height: 52rpx;
			background: #F5F5F5;
			border-radius: 12rpx;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		image {
			width: 26rpx;
			height: 26rpx;
		}
	}

	.aaa {
		margin: 20rpx 32rpx;
	}

	.rights {
		width: calc(100% - 225rpx - 20rpx);
	}

	.logos {
		width: 225rpx;
		border-radius: 12rpx;
		height: 180rpx;
	}

	::v-deep .uv-swipe-action-item {
		border-radius: 24rpx;
	}

	.whits {
		margin-top: 52rpx;

		>view {
			color: #FFFFFF;
		}
	}

	.container {
		height: calc(100vh - 476rpx);
		margin-top: -140rpx;
		position: relative;
		z-index: 999;
		background: #F5F5F5;
		border-radius: 24rpx 24rpx 0rpx 0rpx;
		padding: 32rpx;
		box-sizing: border-box;

		scroll-view {
			width: 100%;
			height: 100%;
		}
	}

	.bgs {
		width: 100vw;
		height: 616rpx;
		position: relative;

		>image {
			width: 100%;
			height: 100%;
			position: absolute;
			left: 0;
			top: 0;
			z-index: -1;
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