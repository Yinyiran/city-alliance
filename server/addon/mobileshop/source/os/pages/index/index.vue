<template>
	<view class="shop" :style="{backgroundImage:'url('+$util.img('upload/uniapp/shop_uniapp/shop_bg.png')+')'}">
		<view class="shop_base_info">
			<view class="shop_img">
				<image :src="$util.img('upload/uniapp/shop_uniapp/shop_img.png')"></image>
			</view>
			<view class="shop_info">
				<view class="shop_title">
					官方旗舰店
					<text>旗舰店铺</text>
					<text>已认证</text>
				</view>
				<view class="shop_other_info">
					<text class="margin-right">主营行业：官方直营店</text>
					<text>店铺状态：正常</text>
				</view>
			</view>
			<view class="weixincode">
				<image :src="$util.img('upload/uniapp/shop_uniapp/erweima.png')" mode="aspectFit"></image>
			</view>
		</view>
		<!-- 交易统计 -->
		<view class="trading_statistics margin_none">
			<!-- 标题 -->
			<view class="title">
				<view class="title_left">交易统计</view>
				<view class="title_right color-base-border">
					<text >今日</text>
					<text class="active">昨日</text>
					<text>7日</text>
				</view>
			</view>
			<view class="content">
				<view>
					<view class="color-tip">订单数</view>
					<view class="num">12</view>
				</view>
				
				<view>
					<view class="color-tip">销售额</view>
					<view class="num">500</view>
				</view>
				
				<view>
					<view class="color-tip">新增会员数</view>
					<view class="num">20</view>
				</view>
			</view>
		</view>
		<!-- 公告 -->
		<view class="notice">
			<image class="img" :src="$util.img('upload/uniapp/shop_uniapp/notice.png')" mode="aspectFit"></image>
			<view class="title">
				 <swiper class="swiper" autoplay="true" vertical="true">
					<swiper-item>
						<view class="swiper-item uni-bg-red">Niushop社区团购小程序上线啦！</view>
					</swiper-item>
					<swiper-item>
						<view class="swiper-item uni-bg-green">Niushop社区团购小程序上线啦！</view>
					</swiper-item>
					<swiper-item>
						<view class="swiper-item uni-bg-blue">Niushop社区团购小程序上线啦！</view>
					</swiper-item>
				</swiper>
			</view>
			<view class="time">2020-12-31</view>
		</view>
		<!-- 待处理事项 -->
		<view class="trading_statistics ">
			<!-- 标题 -->
			<view class="title">
				<view class="title_left">待处理事项</view>
			</view>
			<view class="grid">
				<uni-grid :column="5" :showBorder="!1" > 
					<uni-grid-item>
						<image class="image" :src="$util.img('upload/uniapp/shop_uniapp/index/wating_pay.png')" mode="aspectFit" />
						<text class="text font-size-tag">待付款</text>
					</uni-grid-item>
					<uni-grid-item>
						<image class="image" :src="$util.img('upload/uniapp/shop_uniapp/index/wating_send.png')" mode="aspectFit" />
						<text class="text font-size-tag">待发货</text>
					</uni-grid-item>
					<uni-grid-item>
						<image class="image" :src="$util.img('upload/uniapp/shop_uniapp/index/return_money.png')" mode="aspectFit" />
						<text class="text font-size-tag">退款中</text>
					</uni-grid-item>
					<uni-grid-item>
						<image class="image" :src="$util.img('upload/uniapp/shop_uniapp/index/stock_warn.png')" mode="aspectFit" />
						<text class="text font-size-tag">库存预警</text>
					</uni-grid-item>
					<uni-grid-item>
						<image class="image" :src="$util.img('upload/uniapp/shop_uniapp/index/xiajia.png')" mode="aspectFit" />
						<text class="text font-size-tag">违规下架</text>
					</uni-grid-item>
				</uni-grid>
			</view>
			
		</view>
		<!-- 常用功能 -->
		<view class="trading_statistics ">
			<!-- 标题 -->
			<view class="title">
				<view class="title_left">常用功能</view>
			</view>
			<view class="grid">
				<uni-grid :column="4" :showBorder="!1" > 
					<uni-grid-item>
						<image class="image" :src="$util.img('upload/uniapp/shop_uniapp/index/fabu.png')" mode="aspectFit" />
						<text class="text font-size-tag">商品发布</text>
					</uni-grid-item>
					<uni-grid-item>
						<image class="image" :src="$util.img('upload/uniapp/shop_uniapp/index/search.png')" mode="aspectFit" />
						<text class="text font-size-tag">订单查询</text>
					</uni-grid-item>
					<uni-grid-item>
						<image class="image" :src="$util.img('upload/uniapp/shop_uniapp/index/tongji.png')" mode="aspectFit" />
						<text class="text font-size-tag">数据统计</text>
					</uni-grid-item>
					<uni-grid-item>
						<image class="image" :src="$util.img('upload/uniapp/shop_uniapp/index/xuqian.png')" mode="aspectFit" />
						<text class="text font-size-tag">店铺续签</text>
					</uni-grid-item>
				</uni-grid>
			</view>
		</view>
		<!-- 常用功能 -->
		<view class="trading_statistics ">
			<!-- 标题 -->
			<view class="title">
				<view class="title_left">销售统计</view>
				<view class="title_right color-base-border">
					<text class="active">今日</text>
					<text >昨日</text>
					<text>7日</text>
				</view>
			</view>
			<view class="ucharts">
				<block v-for="(item, index) in arr" :key="index">
				<uCharts :scroll="item.opts.enableScroll" :show="canvas" :canvasId="item.id" :chartType="item.chartType" :extraType="item.extraType" :cWidth="cWidth" :cHeight="cHeight" :opts="item.opts" :ref="item.id"/>
				</block>
			</view>
		</view>
	</view>
</template>

<script>
import uniGrid from '@/components/uni-grid/uni-grid.vue';
import uniGridItem from '@/components/uni-grid-item/uni-grid-item.vue';
import uCharts from '@/components/u-charts/u-charts.vue';
import crt from '@/components/u-charts/js/chartsUtil.js';
export default {
	data() {
		return {
			canvas: true,
			cWidth:'',
			cHeight:'',
			arr: []
		};
	},
	components: {
		uniGrid,
		uniGridItem,
		uCharts
	},
	onShow() {},
	onLoad(e) {
		this.init()
	},
	methods: {
		init(){
			this.cWidth=uni.upx2px(630);
			this.cHeight=uni.upx2px(400);
			this.getServerData();
			this.query()
		},
		query(){
			// this.findFamilyQYByList()
		},
		change(idx,type,etype){
			console.log(etype,type)
			this.$refs[this.arr[idx].id][0].changeData(this.arr[idx].id,this.arr[idx].opts,type,etype)
		},
		async getServerData() {
			var sample=crt.getSampleData()
				var Area={ enableScroll:false, unit: '元' ,yAxisdisabled:true,xAxisgridColor:'transparent',yAxisgridType:'solid',yAxisgridColor:'#eeeeee',animation:true,addLine:true}
				Area.categories = sample.Area.categories//result.data.ColumnB.categories
				Area.series = sample.Area.series//result.data.ColumnB.series
				var serverData = [{
					title:'各师商品房预（现）售供应情况',
					opts: Area,
					chartType: "area",
					extraType: "curve",
					id: "c1",
					
				}];
				this.arr = serverData;
				console.log(this.arr)
			// 	return
			// }
			// this.$api.tip("网络错误，小程序端请检查合法域名");
		},
	}
};
</script>

<style lang="scss">
	.shop{
		width:100%;
		background-repeat: no-repeat;
		background-position: top center;
		background-size: 100% 277rpx;
		.shop_base_info{
			display: flex;
			align-items: center;
			color: #fff;
			margin:0 $margin-both;
			padding:$margin-both 0;
			.shop_img{
				width:80rpx;
				height:80rpx;
				min-width: 80rpx;
				margin-right: $margin-updown;
				image{
					width:100%;
					height:100%;
					
				}
			}
			.shop_info{
				flex:1;
				.shop_title{
					color:#fff;
					font-size: $font-size-toolbar;
					font-weight: 600;
					text{
						font-size: $font-size-goods-tag;
						margin-left: $margin-updown;
						border:1px solid #fff;
						border-radius: 2rpx;
						padding:0 13rpx;
						line-height: 1;
						font-weight: 400;
						vertical-align: middle;
					}
				}
				.shop_other_info{
					color: #fff;
					margin-top: 10rpx;
					font-size: $font-size-tag;
				}
			}
			.weixincode{
				height:40rpx;
				width:40rpx;
				margin-left: $margin-updown;
				image{
					height:100%;
					width:100%;
				}
			}
		}
		.trading_statistics{
			padding:$margin-both;
			margin:$margin-both;
			background-color: #fff;
			border-radius: $border-radius;
			&.margin_none{
				margin-top: 0;
			}
			.title{
				display: flex;
				.title_left{
					flex:1;
					font-size: $font-size-toolbar;
					font-weight: bold;
				}
				.title_right{
					border:1px solid;
					height:46rpx;
					font-size: 0;
					border-radius: 46rpx;
					overflow: hidden;
					text{
						display: inline-block;
						width: 120rpx;
						height:46rpx;
						line-height: 46rpx;
						text-align: center;
						font-size: $font-size-tag;
						color:$color-tip;
						&.active{
							background: #FF6A00;
							color:#fff;
							border-radius: 46rpx;
						}
					}
				}
			}
			.content{
				display: flex;
				>view{
					flex:1;
					margin-top: 42rpx;
					.num{
						font-size: 36rpx;
						font-weight: medium;
					}
				}
			}
			.grid{
				margin-top:40rpx;
				.image {
					width: 80rpx;
					height: 80rpx;
					min-height: 80rpx;
				}
				
				.text {
					font-size: $font-size-sub;
					margin-top: 16rpx;
					color: $color-title;
				}
			}
			.ucharts{
				margin-top: 47rpx;
			}
		}
		.notice{
			display: flex;
			padding:$margin-both;
			margin: $margin-both;
			align-items: center;
			background-color: #fff;
			border-radius: $border-radius;
			.img{
				width:30rpx;
				height:24rpx;
			}
			.title{
				flex:1;
				margin-left: $margin-updown;
				
				overflow:hidden; //超出的文本隐藏
				text-overflow:ellipsis; //溢出用省略号显示
				white-space:nowrap;
				.swiper{
					width:100%;
					height:48rpx;
					view{
						overflow:hidden; //超出的文本隐藏
						text-overflow:ellipsis; //溢出用省略号显示
						white-space:nowrap;
					}
				}
			}
			.time{
				font-size: $font-size-tag;
				color: $color-tip;
				margin-left: $margin-updown;
			}
		}
		
	}
	
	
</style>
