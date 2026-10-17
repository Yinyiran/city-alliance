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
					<view class="status-gray" v-if="item.order_status==-1 || (item.trade_type==2 && item.appointment_status==2)" style="color:#999">{{statusText(item)}}</view>
					<view class="status" v-else-if="item.order_status==0">{{statusText(item)}}</view>
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
					<!-- 快捷买单信息 -->
					<view class="appt" v-if="item.pay_scene==1">
						<text class="appt-label" style="background:#FFF0E6;color:#F55606;">到店买单</text>
						<text v-if="item.buyer_message" style="color: #888; font-size: 22rpx; margin-left: 8rpx;">备注: {{item.buyer_message}}</text>
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
					<view class="btn1" v-if="item.order_status==0" @click.stop="orderClose(item.order_id)">取消订单</view>
					<view class="btn2" v-if="item.order_status==0" @click.stop="goPay(item)">立即支付</view>
					<view class="btn1" v-if="item.trade_type==2 && item.appointment_status==1 && item.order_status==1" @click.stop="cancelAppointment(item)">取消预约</view>
					<view class="btn3" v-if="item.order_status==1 && item.trade_type!=2" @click.stop="goRefund(item)">申请退款</view>
					<view class="btn5" v-if="isVerifyOrder(item)" @click.stop="goDetail(item)">去核销</view>
					<view class="btn3" v-if="canConfirmReceipt(item)" @click.stop="confirmReceipt(item)">确认收货</view>
					<view class="btn1" v-if="item.order_status==2 && item.trade_type!=2" @click.stop="goRefund(item)">申请售后</view>
					<view class="btn4" v-if="item.order_status==10" @click.stop="againBuy(item)">再次购买</view>
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
	import {orderlist,orderClose,cancelAppointment,huifupay,orderTakeDelivery} from "@/common/js/api.js"
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
			//按订单类型（实际配送链路）输出交易状态文案
			//与后端 OrderCommon::orderTypeStatusName() 保持一致，兜底历史数据的 order_status_name 快照
			//快递邮寄(1)：待支付→待发货→已发货→已收货→已完成
			//到店核销(2)：待支付→待核销→已核销→已完成
			//外卖自配送(3)：待支付→待接单→备餐中→配送中→已送达→已完成
			orderTradeStatusName(order_type, order_status){
				order_type = parseInt(order_type, 10);
				order_status = parseInt(order_status, 10);
				let map;
				if(order_type === 3){
					map = {0:'待支付',1:'待接单',2:'备餐中',3:'配送中',4:'已送达',10:'已完成','-1':'已关闭'};
					return map[order_status] || '已送达';
				}
				if(order_type === 2){
					map = {0:'待支付',1:'待核销',2:'待核销',3:'待核销',4:'已核销',10:'已完成','-1':'已关闭'};
					return map[order_status] || '已核销';
				}
				map = {0:'待支付',1:'待发货',2:'待发货',3:'已发货',4:'已收货',10:'已完成','-1':'已关闭'};
				return map[order_status] || '已收货';
			},
			//订单列表状态文案
			statusText(item){
				const st = Number(item.order_status);
				const t = Number(item.trade_type || 0);
				const apptSt = Number(item.appointment_status);

				if (st === -1) {
					return (t === 2 && apptSt === 2) ? '已取消' : '已关闭';
				}
				if (t === 2 && apptSt === 2) {
					return '已取消';
				}

				if (Number(item.pay_scene) === 1) {
					return Number(item.pay_status) === 1 ? '买单成功' : '待付款';
				}
				//优先取后端按订单类型计算的交易状态，其次前端按矩阵兜底
				let text = item.trade_status_name || this.orderTradeStatusName(item.order_type, item.order_status);
				//预约到店：已预约但未核销时提示待到店，比「待核销」更贴合用户视角
				if(t === 2 && item.appointment_status === 1 && Number(item.order_status) !== 2){
					text = '待到店';
				}
				return text;
			},
			//是否可核销（到店类且未核销：待支付与已关闭不可核销）
			isVerifyOrder(item){
				const ot = Number(item.order_type||1);
				const st = Number(item.order_status);
				if (st === -1) return false;
				if(ot !== 2) return false;
				if(Number(item.pay_scene) === 1) return false; // 快捷买单已完成，无需核销
				const t = Number(item.trade_type || 0);
				const apptSt = Number(item.appointment_status);
				if (t === 2 && apptSt !== 1) return false;
				return (st === 1 || st === 2) && Number(item.delivery_status||0) != 1;
			},
			//是否可确认收货：快递订单已发货、外卖订单配送中（外卖由商家确认送达，顾客亦可确认）
			canConfirmReceipt(item){
				const ot = Number(item.order_type||1);
				const st = Number(item.order_status);
				if(ot === 1) return st === 3;
				if(ot === 3) return st === 3;
				return false;
			},
			//确认收货
			confirmReceipt(item){
				uni.showModal({
					title: '确认收货',
					content: '确认已收到商品？',
					success: (r) => {
						if(!r.confirm) return;
						orderTakeDelivery({ order_id: item.order_id }).then(res => {
							if(res.code == 0){
								uni.showToast({ title: '已确认收货' });
								this.getlist();
							}else{
								uni.showToast({ title: res.message || '操作失败', icon: 'none' });
							}
						});
					}
				});
			},
			goDetail(item){
				this.$util.redirectTo('/otherpages/orderDetail/orderDetail',{id:item.order_id});
			},
			goPay(item){
				if (item.out_trade_no) {
					huifupay({ out_trade_no: item.out_trade_no }).then(resA => {
						if (resA.code == 0) {
							uni.requestPayment({
								provider: 'wxpay',
								timeStamp: resA.data.timeStamp,
								nonceStr: resA.data.nonceStr,
								package: resA.data.package,
								signType: resA.data.signType,
								paySign: resA.data.paySign,
								success: () => {
									this.page = 1;
									this.getlist();
								},
								fail: () => {}
							});
						} else {
							uni.showToast({ title: resA.message || '发起支付失败', icon: 'none' });
						}
					}).catch(() => {
						this.$util.redirectTo('/otherpages/orderDetail/orderDetail', { id: item.order_id });
					});
				} else {
					this.$util.redirectTo('/otherpages/orderDetail/orderDetail', { id: item.order_id });
				}
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