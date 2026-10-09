<template>
	<view>
		<uv-sticky bg-color="#fff">
			<nvabar type='1' title='订单列表' titleStyle='color:#222'></nvabar>
			<view class="">
				<uv-tabs :list="listA" lineWidth="16" :current="current" lineColor="#f56c6c" :scrollable="false" @change="onchange" :activeStyle="{
							fontWeight: 'bold',color:'#f56c6c'
				    	}" :inactiveStyle="{
							color: '#222222',
						}" itemStyle="height: 44px;font-size:28rpx;padding:0 8rpx"></uv-tabs>
			</view>
		</uv-sticky>
		<view class="container">
			<view v-for="(item,index) in list" class="p-28 items" :key="index" @click="$util.redirectTo('/otherpages/orderDetail/orderDetail',{id:item.order_id})">
				<view class="acea-row row-middle row-between">
					<view class="acea-row row-middle font-32 font-weight-6">
						<image src="/static/stores.png" class="w-32 h-32 m-r-8" mode=""></image>
						{{item.site_name}}
						<view class="go m-l-8">
							<uv-icon color="#555555" size="8" name="arrow-right"></uv-icon>
						</view>
					</view>
					<view class="status" v-if="item.order_status==0">{{statusText(item)}}</view>
						<view class="status" v-else-if="item.order_status==1">{{statusText(item)}}</view>
						<view class="status" v-else-if="item.order_status==2" style="color: #FE780F;">{{statusText(item)}}</view>
						<view class="" v-else>{{statusText(item)}}</view>
					</view>
					<!-- 预约信息 -->
					<view class="appt" v-if="item.trade_type==2 && item.appointment_date">
						<text class="appt-label">预约</text>
						<text>{{item.appointment_date}} {{item.appointment_slot}}</text>
						<text class="appt-store" v-if="item.delivery_store_name">· {{item.delivery_store_name}}</text>
					</view>
				<view class="acea-row row-middle m-t-28 row-between" v-for="(items,indexs) in item.order_goods">
					<image :src="$util.img(items.sku_image)" class="w-140 h-140 bor-16" mode=""></image>
					<view class="widths h-140 acea-row row-between row-column">
						<view class="font-28 text-more-2">{{items.sku_name}}</view>
						<view class="acea-row row-middle row-between">
							<view class="points" v-if="item.pay_type=='POINT'">
								<text class="font-30 font-weight-6">{{items.point}}</text>
								积分
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
					<view class="btn1" v-if="item.order_status==0" @click="orderClose(item.order_id)">取消订单</view>
					<view class="btn2" v-if="item.order_status==0" @click="goPay(item)">立即支付</view>
					<view class="btn1" v-if="item.trade_type==2 && item.appointment_status==1" @click="cancelAppointment(item)">取消预约</view>
					<view class="btn3" v-if="item.order_status==1" @click="goRefund(item)">申请退款</view>
					<view class="btn5" v-if="isVerifyOrder(item)" @click="goDetail(item)">去核销</view>
					<view class="btn1" v-if="item.order_status==2" @click="goRefund(item)">申请售后</view>
					<view class="btn4" v-if="item.order_status==10" @click="againBuy(item)">再次购买</view>
				</view>
			</view>
			<view v-if="!loading&&!list.length" class="empty-wrap">
				<image src="/otherpages/static/empty1.png" mode="" class="empty-img"></image>
				<view class="empty-txt">暂无相关订单</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {orderlist,orderClose,cancelAppointment,huifupay} from "@/common/js/api.js"
	export default {
		data() {
			return {
				listA: [{
						name: "全部",
						id:'all'
					},
					{
						name: "待支付",
						id:"waitpay"
					},
					{
						name: "待到店",
						id:'waitappointment'
					},
					{
						name: "待收货",
						id:'waitconfirm'
					},
					{
						name: "待核销",
						id:'waitstock'
					},
					{
						name: "售后",
						id:'after'
					},
				],
				page:1,
				order_status:'all',
				list:[],
				loading:true,
				last_page:1,
				current:0
			};
		},
		onLoad(res) {
			// current 为字符串，需转为数字并做越界兜底
			const current = Number(res.current || 0)
			if (!isNaN(current) && current >= 0 && current < this.listA.length) {
				this.current = current
				this.order_status = this.listA[current].id
			}
		},
		onShow() {
			this.getlist()
		},
		methods:{
			//按交易类型显示状态文案
			statusText(item){
				const t = Number(item.trade_type||0);
				if(item.order_status === 2) return '待核销';
				if(t === 2 && item.appointment_status === 1 && item.order_status !== 2) return '待到店';
				if(t === 1 && item.order_status === 1) return '待到店消费';
				if(t === 3 && item.order_status === 1) return '待自提';
				if(t === 4 && item.order_status === 1) return '待发货';
				return item.order_status_name;
			},
			//是否可核销（到店类且未核销）
			isVerifyOrder(item){
				const t = Number(item.trade_type||0);
				return [1,2,3].includes(t) && item.delivery_status != 1 && item.order_status != 0 && item.order_status != -1;
			},
			goDetail(item){
				this.$util.redirectTo('/otherpages/orderDetail/orderDetail',{id:item.order_id});
			},
			goPay(item){
				this.$util.redirectTo('/otherpages/orderDetail/orderDetail',{id:item.order_id});
			},
			goRefund(item){
				this.$util.redirectTo('/otherpages/orderDetail/orderDetail',{id:item.order_id});
			},
			againBuy(item){
				if(item.order_goods && item.order_goods.length){
					const sku = item.order_goods[0];
					this.$util.redirectTo('/otherpages/goods_detail/goods_detail',{id:sku.sku_id});
				}
			},
			//取消预约
			cancelAppointment(item){
				uni.showModal({
					title:'取消预约',
					content:'确定取消该预约吗？名额将释放',
					success:r=>{
						if(r.confirm){
							cancelAppointment({ order_id: item.order_id }).then(res=>{
								uni.showToast({ title: res.message || '已取消', icon:'none' });
								this.getlist();
							})
						}
					}
				})
			},
			orderClose(e){
				uni.showModal({
					content:'确定取消当前订单',
					success:res=>{
						if(res.confirm){
							let params={
								order_id:e
							}
							orderClose(params).then(resA=>{
								this.getlist()
							})
						}
					}
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
				this.loading=true
				let params={
					page:this.page,
					order_status:this.order_status
				}
				orderlist(params).then(res=>{
					let rows=res&&res.data&&res.data.list?res.data.list:[]
					this.list=this.page>1?this.list.concat(rows):rows
					this.last_page=res&&res.data&&res.data.page_count?res.data.page_count:1
				}).catch(()=>{}).finally(()=>{
					this.loading=false
				})
			},
		}
	}
</script>

<style lang="scss">
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

			.appt {
				background: #FFF6F0;
				border-radius: 8rpx;
				padding: 12rpx 16rpx;
				margin-top: 16rpx;
				font-size: 24rpx;
				color: #C24E00;

				.appt-label {
					background: #C24E00;
					color: #fff;
					border-radius: 4rpx;
					padding: 2rpx 10rpx;
					margin-right: 10rpx;
					font-size: 20rpx;
				}

				.appt-store {
					color: #999;
					margin-left: 8rpx;
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

	.empty-wrap {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding: 120rpx 0 160rpx 0;

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