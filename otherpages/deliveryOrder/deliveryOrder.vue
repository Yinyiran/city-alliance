<template>
	<view>
		<!-- 页面已由「订单管理-到店核销」分组取代，保留入口引导，避免两套核销列表口径不一致 -->
		<view class="tips-bar acea-row row-middle row-between" @click="goTradeOrder">
			<text class="tips-text">订单管理已升级，可在新页面按交易类型查看与核销订单</text>
			<view class="tips-go acea-row row-middle row-center">
				<text>前往</text>
				<uv-icon name="arrow-right" color="#F55606" size="12"></uv-icon>
			</view>
		</view>
		<view class="container">
			<view v-for="(item,index) in list" class="p-28 items" :key="index" @click="$util.redirectTo('/otherpages/orderDetail/orderDetail',{id:item.order_id})">
				<view class="acea-row row-middle row-between">
					<view class="acea-row row-middle font-32 font-weight-6">
						<image src="/static/stores.png" class="w-32 h-32 m-r-8" mode=""></image>
						{{item.site_name}}
						<!-- <view class="go m-l-8">
							<uv-icon color="#555555" size="8" name="arrow-right"></uv-icon>
						</view> -->
					</view>
					<!-- <view class="status" v-if="item.order_status==0">{{item.order_status_name}}</view> -->
					<!-- <view class="status" v-else-if="item.order_status==1">{{item.order_status_name}}</view> -->
					<view class="status" style="color: #FE780F;">待核销</view>
					<!-- <view class="" v-else>{{item.order_status_name}}</view> -->
				</view>
				<view class="acea-row row-middle m-t-28 row-between" v-for="(items,indexs) in item.goods_list">
					<image :src="$util.img(items.sku_image)" class="w-140 h-140 bor-16" mode=""></image>
					<view class="widths h-140 acea-row row-between row-column">
						<view class="font-28 text-more-2">{{items.sku_name}}</view>
						<view class="acea-row row-middle row-between">
							<view class="points" v-if="item.pay_type=='POINT'">
								<text class="font-30 font-weight-6">{{items.point}}</text>
								积分
							</view>
							<view class="price" v-else>
								￥
								<text class="font-30 font-weight-6">{{items.price}}</text>
							</view>
							<view class="nums">x{{items.num}}</view>
						</view>
					</view>
				</view>
				<!-- <view class="wuliu m-t-20">
					<view class="acea-row row-middle font-weight-6" style="color: #222;">
						<view class="dots m-r-8"></view>
						已下单</view>
						<view class="">您提交了订单，请等待第三方卖家系统确认</view>
						<view class="">2025/03/03 10:12:31</view>
						<view class="acea-row row-middle row-center">
							<uv-icon name="arrow-right" color="#555" size="12"></uv-icon>
						</view>
				</view> -->
				<view class="btns acea-row row-middle row-right">
					<view class="btn2" @click="gonext(item.delivery_code)">核销</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {deliveryOrder} from "@/common/js/api.js"
	export default {
		data() {
			return {
				page:1,
				order_status:'all',
				list:[],
				last_page:1,
				current:0
			};
		},
		onLoad(res) {
			if(res.current){
				this.current=res.current
				this.order_status=this.listA[this.current].id
			}
		},
		onShow() {
			this.getlist()
		},
		onReachBottom() {
			if(this.last_page>this.page){
				this.page++
				this.getlist()
			}
		},
		methods:{
			gonext(e){
				uni.navigateTo({
					url:'/otherpages/writeOff/writeOff?code='+e
				})
			},
			// 旧「待核销」页已由订单管理页的「到店核销」分组取代，引导至新入口
			goTradeOrder(){
				uni.redirectTo({
					url:'/otherpages/tradeOrder/tradeOrder?biz=store&stage=pending'
				})
			},
			onchange(e){
				// console.log(e);
				this.current=e.index
				// if(e.index==4){
					
				// }else{
					this.order_status=e.id
					this.getlist()
				// }
			},
			getlist(){
				let params={
					page:this.page,
				}
				deliveryOrder(params).then(res=>{
					this.list=this.page>1?this.list.concat(...res.data.list):res.data.list
					this.last_page=res.data.page_count
				})
			},
		}
	}
</script>

<style lang="scss">
	.tips-bar {
		margin: 20rpx 24rpx 0;
		padding: 20rpx 24rpx;
		background: #FFF7EE;
		border-radius: 16rpx;

		.tips-text {
			flex: 1;
			font-size: 24rpx;
			color: #FE780F;
		}

		.tips-go {
			flex-shrink: 0;
			font-size: 24rpx;
			color: #F55606;
			font-weight: 600;
		}
	}

	.container {
		margin: 20rpx 24rpx;

		.items {
			background: #fff;
			border-radius: 24rpx;
			margin-bottom: 20rpx;

			.status {
				font-weight: 400;
				font-size: 24rpx;
				color: #F51D1D;
			}

			.widths {
				width: calc(100% - 140rpx - 20rpx);


				.nums {
					color: #999999;
				}
			}
			.wuliu{
				background: #F5F5F5;
				border-radius: 12rpx;
				padding: 12rpx 20rpx;
				position: relative;
				>view{
					font-size: 22rpx;
					color: #999999;
				}
				.dots{
					width: 12rpx;
					height: 12rpx;
					background: #25B85B;
					border-radius: 50%;
				}
				>view:last-child{
					border-radius: 50%;
					width: 32rpx;
					height: 32rpx;
					background: #FFFFFF;
					position: absolute;
					right: 20rpx;
					top: 50%;
					transform: translateY(-50%);
				}
			}

			.btns {
				margin-top: 20rpx;

				>view {
					margin-left: 16rpx;
					width: 136rpx;
					height: 64rpx;
					text-align: center;
					line-height: 64rpx;
					box-sizing: border-box;
					border-radius: 32rpx;
					overflow: hidden;
				}

				.btn1 {
					border: 1rpx solid #DDDDDD;
					color: #999999;
				}

				.btn2 {
					background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
					color: #fff;
				}

				.btn3 {
					border: 1rpx solid #DDDDDD;
				}

				.btn4 {
					color: #F55606;
					position: relative;
					border-radius: 32rpx !important;
					z-index: 1;
				}

				.btn4::before {
					content: '';
					position: absolute;
					top: 0;
					left: 0;
					right: 0;
					bottom: 0;
					padding: 1rpx;
					/* 边框宽度 */
					border-radius: 32rpx;
					/* 与元素圆角一致 */
					background: linear-gradient(90deg, rgba(245, 171, 24, 1), rgba(245, 86, 6, 1));
					-webkit-mask:
						linear-gradient(#fff 0 0) content-box,
						linear-gradient(#fff 0 0);
					-webkit-mask-composite: xor;
					mask-composite: exclude;
					z-index: -1;
				}
				.btn5{
					background: #F5AB18;
					color: #fff;
				}
			}
		}

		.go {
			width: 24rpx;
			height: 24rpx;
			background: #F0F0F0;
			display: flex;
			align-items: center;
			justify-content: center;
			border-radius: 50%;
		}
	}
</style>