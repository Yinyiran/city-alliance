<template>
	<view>
		<view v-for="(item,index) in list" class="acea-row row-middle row-between items m-t-32" :key="index" @click="gonext(item.sku_id)">
			<view class="">
				<image :src="$util.img(item.sku_image)" mode="" class="bor-16"></image>
				<view class="acea-row row-middle acea-row row-middle">
					<uv-icon name="eye" size="12" color="#fff"></uv-icon>
					<view class="w-4"></view>
					{{item.click_num}}
				</view>
			</view>
			<view class="acea-row row-column row-between" style="flex-wrap: nowrap;">
				<view class="acea-row row-middle" style="flex-wrap: nowrap;">
					<image :src="$util.img(item.logo)" mode="heightFix" v-if='item.logo' class="h-40 w-150"></image>
					<text class="font-28 font-weight-6 m-l-12 text-more" style="color: #C24E00;">{{item.site_name}}</text>
				</view>
				<view class="text-more-2 font-26">{{item.goods_name}}</view>
				<view class="acea-row row-middle font-22" style="color: #999;">
					<uv-icon name="map" size="12" color="#999"></uv-icon>
					距离{{item.distance||0}}km
					｜
					已售{{item.sale_num||0}}件
				</view>
				<view class="acea-row row-middle prices">
					<view class="acea-row row-column row-between" style="flex-wrap: nowrap;">
						<view class="acea-row row-middle">
							<view class="">{{tradeText(item)}}</view>
							<view class="">{{secondText(item)}}</view>
						</view>
						<view class="acea-row row-middle">
							<text>￥</text>
							{{item.price}}
							<view class="m-l-8" v-if="item.market_price!='0.00'">¥{{item.market_price}}</view>
						</view>
					</view>
					<image src="/static/qiang.png" class="w-140 h-74" mode=""></image>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name:"goods",
		data() {
			return {
				
			};
		},
		props:{
			list:{
				type:Array,
				default:()=>[]
			}
		},
		methods:{
			gonext(e){
				uni.navigateTo({
					url:'/otherpages/goods_detail/goods_detail?id='+e
				})
			},
			//交易类型主标签
			tradeText(item){
				const map = {0:'限时特价',1:'到店消费',2:'预约到店',3:'到店自提',4:'外卖配送'};
				return map[Number(item.trade_type||0)] || '限时特价';
			},
			//交易类型副标签
			secondText(item){
				const t = Number(item.trade_type||0);
				if(t === 2) return item.service_duration ? `服务${item.service_duration}分钟` : '需预约';
				if(t === 1) return Number(item.allow_custom_amount)===1 ? '金额自定' : '门店核销';
				if(t === 4) return '同城配送';
				return '门店核销';
			}
		}
	}
</script>

<style lang="scss">
	.items{
		>view:first-child{
			position: relative;
			width: 240rpx;
			height: 240rpx;
			border-radius: 20rpx;
			>image{
				width: 100%;
				height: 100%;
			}
			>view{
				padding: 0 8rpx;
				position: absolute;
				height: 28rpx;
				background: rgba(0, 0, 0, 0.36);
				border-radius: 8rpx;
				left: 12rpx;
				bottom: 12rpx;
				font-weight: 400;
				font-size: 20rpx;
				color: #FFFFFF;
			}
		}
		>view:last-child{
			width: calc(100% - 240rpx - 20rpx);
			height: 240rpx;
		}
		.prices{
			>view:first-child{
				height: 74rpx;
				background: linear-gradient( 90deg, rgba(255,250,240,0) 0%, #FFFAF0 100%);
				width: calc(100% - 140rpx);
				>view:first-child{
					>view{
						width: 96rpx;
						height: 28rpx;
						background: #FFEBEB;
						border-radius: 4rpx;
						text-align: center;
						line-height: 28rpx;
						font-weight: 600;
						font-size: 20rpx;
						color: #F51D1D;
						margin-right: 8rpx;
					}
				}
				>view:last-child{
					font-size: 42rpx;
					color: #F51D1D;
					font-weight: bold;
					text:first-child{
						font-size: 24rpx;
						color: #F51D1D;
						font-weight: bold;
					}
					view:last-child{
						font-weight: 400;
						font-size: 24rpx;
						color: #999999;
						text-decoration: line-through;
					}
				}
			}
		}
	}
</style>