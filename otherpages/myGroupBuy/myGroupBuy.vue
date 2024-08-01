<template>
	<view>
		<view class="wbg  p-r-20 p-l-20 p-t-14 p-b-14 ">
			<uv-search placeholder="搜索交易编号或店铺" borderColor='#FF4D1A' searchIconColor='#8D93A5' :showAction='false'
				v-model="keyword">
				<template slot='suffix'>
					<view class="search font-24 bor-64">搜索</view>
				</template>
			</uv-search>
		</view>
		<view class="flex_b p-30">
			<view v-for="(item,index) in list" :key="index" @click="selectFn(index)"
				:class="activeType==index?'active':''" class="font-28 graycolor typeItem">
				{{item}}
			</view>
		</view>
		<view class="flex p-l-30 p-r-30">
			<view class="flex_e sortItem">
				<view class="font-26 grey">创建时间</view>
				<view class="">
					<view class="icon-shangjiantou font-24 iconfont lh-16 grey" style="transform: scale(0.8);"></view>
					<view class="icon-xiajiantou1 iconfont font-24 lh-16 f-tA" style="transform: scale(0.8);"></view>
				</view>
			</view>
			<view class="flex_e sortItem">
				<view class="font-26 grey">已卖出</view>
				<view class="">
					<view class="icon-shangjiantou font-24 iconfont lh-16 grey" style="transform: scale(0.8);"></view>
					<view class="icon-xiajiantou1 iconfont font-24 lh-16 f-tA" style="transform: scale(0.8);"></view>
				</view>
			</view>
			<view class="flex_e sortItem">
				<view class="font-26 grey">已核销</view>
				<view class="">
					<view class="icon-shangjiantou font-24 iconfont lh-16 grey" style="transform: scale(0.8);"></view>
					<view class="icon-xiajiantou1 iconfont font-24 lh-16 f-tA" style="transform: scale(0.8);"></view>
				</view>
			</view>
		</view>

		<view class="">
			<view v-for="(item,index) in 3" :key="index" class="modelBox">
				<view class="flex_b">
					<view class="font-32 font-weight">专业洗鞋子</view>
					<view class="graycolor font-28">{{index==1?'已上架':'待发布'}}</view>
				</view>
				<view class="font-28 grey m-t-10">精品鞋子洗</view>

				<view class="flex_a color-base-border-top m-t-20 p-t-30 color-base-border-bottom p-b-30">
					<view class="textC">
						<view class="font-40 font-weight">23</view>
						<view class="grey">已售卖</view>
					</view>
					<view class="textC">
						<view class="font-40 font-weight">23</view>
						<view class="grey">已核销</view>
					</view>
					<view class="textC">
						<view class="font-40 font-weight">23</view>
						<view class="grey">售卖金额</view>
					</view>
				</view>
				<view class="flex_b">
					<view class=""></view>
					<view class="flex btnModel">
						<view class="">更多</view>
						<view class="">查看</view>
						<view class="">编辑</view>
					</view>
				</view>
			</view>
		</view>

		<view class="bottom-mar h-200"></view>

		<view class="bomPosition ">
			<view class="p-30">
				<button type="primary"
					@click="$util.redirectTo('/otherpages/submitGroupBuy/submitGroupBuy')">发布套餐</button>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list: ['全部', '待发布', '已上架', '已下架', '已失效', ],
				activeType: 0
			};
		},
		methods: {
			selectFn(i) {
				this.activeType = i
			}
		}

	}
</script>

<style lang="scss">
	.btnModel {
		margin-top: 30rpx;

		view {
			width: 154rpx;
			height: 63rpx;
			border: 1rpx solid #bbbec9;
			border-radius: 36rpx;
			text-align: center;
			line-height: 63rpx;
			font-size: 28rpx;
			margin-left: 20rpx;
		}
	}

	.sortItem {
		width: 173rpx;
		height: 53rpx;
		background-color: #fff;
		border-radius: 10rpx;
		padding: 0 4rpx;
		box-sizing: border-box;
		margin-right: 20rpx;
	}

	.active {
		background-color: #FF4D1A;
		width: 113rpx;
		height: 56rpx;
		border-radius: 56rpx;
		text-align: center;
		line-height: 56rpx;
		color: #fff !important;
	}

	.typeItem {
		width: 113rpx;
		text-align: center;
	}

	.wbg {
		// background: #fff;
	}

	.search {
		padding: 15rpx 38rpx;
		background: #FF4D1A;
		color: #fff;
		margin-right: -5rpx;
	}

	/deep/ .uv-search__content__input,

	.uv-search__content {
		// background: #fff !important;
		height: 83rpx;
	}
</style>