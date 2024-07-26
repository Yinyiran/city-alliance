<template>
	<view>
		<uv-sticky>
			<view class="wbg  p-r-20 p-l-20 p-t-14 p-b-14 ">
				<uv-search placeholder="搜索交易编号或店铺" borderColor='#FF4D1A' searchIconColor='#8D93A5' :showAction='false'
					v-model="keyword">
					<template slot='suffix'>
						<view class="search font-24 bor-64">搜索</view>
					</template>
				</uv-search>
			</view>
			<view class="p-t-30 p-b-34 p-r-64 p-l-64 acea-row row-around row-middle wbg ">
				<view v-for="(item,index) in list" :key="index" :class="['font-30','relative',check==index?'on':'']">
					{{item}}
					<image src="@/static/index/icon3.png" v-if="check==index" mode=""></image>
				</view>
			</view>
			<view scroll-x="true"
				class="scrollx p-t-30 p-b-30 acea-row row-middle row-between p-r-20 p-l-20 acea-nowrap">
				<view v-for="(item,index) in list2" :key="index" :class="index==select?'ison':''">
					{{item}}
				</view>
			</view>
		</uv-sticky>
		<view class="">
			<view v-for="(item,index) in 10" :key="index" class="wbg bor-10 m-20 p-20 goods">
				<view class="acea-row row-between row-middle">
					<text class="font-weight-6 font-30">康洁洗衣洗鞋</text>
					<text class="fcolor font-28">已确认收货</text>
				</view>
				<view class="acea-row row-middle m-t-14">
					<image src="../../static/logo.png" class="w-100 h-100 m-r-20 bor-10" mode=""></image>
					<view class="info h-100">
						<view class="acea-row row-middle row-between">
							<view class="font-28 font-weight-6">买单消费34.00元</view>
							<view class="fcolor1 font-28">即将到账</view>
						</view>
						<view class="m-t-20">
							<text class="bor-8 font-20">京东</text>
						</view>
					</view>
				</view>
				<view class="acea-row row-middle row-around p-t-20 p-b-20 bg1 m-t-20 bor-10"
					style="text-align: center;">
					<view class="">
						<view class="fcolor2">实付</view>
						<view class="font-28 font-weight-6 m-t-10">¥7.00</view>
					</view>
					<view class="">
						<view class="fcolor2">预估收益</view>
						<view class="font-28 font-weight-6 m-t-10">¥7.00</view>
					</view>
				</view>
				<view class="m-t-20">
					<view class="acea-row row-middle row-between m-b-10">
						<view class="fcolor3 font-28">
							<text>订单编号</text>
							<text class="m-l-40">218548978971238723</text>
						</view>
						<view class="font-28">复制</view>
					</view>
					<view class="fcolor3 m-b-10 font-28">
						<text>创建时间</text>
						<text class="m-l-40">218548978971238723</text>
					</view>
					<view class="fcolor3 font-28">
						<text>预计到账</text>
						<text class="m-l-40">218548978971238723</text>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				title: '推广订单',
				list: ['已付款', '退款/售后'],
				check: 0,
				list2: ['全部', '折扣买单', '线下买单', '团购', '线上买单'],
				select: 0
			};
		}
	}
</script>

<style lang="scss">
	.relative{
		position: relative;
	}
	.wbg {
		background: #fff;
	}

	.bg1 {
		background: #F6F7FB;
	}
	.acea-nowrap{
		flex-wrap: nowrap;
	}
	.fcolor {
		color: #898FA3;
	}

	.fcolor1 {
		color: #EC9B28;
	}

	.fcolor3 {
		color: #777D94;
	}

	.on {
		font-weight: bold;
		color: #FF4D1A;

		image {
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			width: 27rpx;
			height: 9rpx;
			bottom: -10rpx;

		}
	}

	.scrollx {
		background: #F6F7FB;
		view {
			margin-right: 24rpx;
			background: #fff;
			flex: 0 0 calc((100% - 96rpx)/5);
			border-radius: 8rpx;
			text-align: center;
			padding: 6rpx 0;
		}

		.ison {
			background: #FF4D1A;
			color: #fff;
		}
	}

	.search {
		padding: 15rpx 38rpx;
		background: #FF4D1A;
		color: #fff;
		margin-right: -5rpx;
	}

	/deep/ .uv-search__content__input,
	.uv-search__content {
		background: #fff !important;
		height: 83rpx;
	}

	.info {
		width: calc(100% - 120rpx);

		text {
			padding: 3rpx 13rpx;
			background: #FFF3EF;
			color: #FF4D1A;
		}
	}

	.goods:first-child {
		margin-top: 0;
	}
</style>