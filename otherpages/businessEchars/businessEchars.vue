<template>
	<view class="p-20">
		<view class="flex_b m-b-30">
			<view class="flex">
				<view v-for="(item,index) in typeList" :key="index" @click="typeId=index"
					:class="typeId==index?'active':''" class="itemType grey font-26">
					{{item}}
				</view>
			</view>
			<view class="font-28 f-tA" @click="openTime">06-07至07-05<text
					class="m-l-4 iconfont icon-xiajiantou font-28 grey"></text>
			</view>
		</view>
		<view class="echarsBox">
			<view class="font-28 white font-weight m-l-26 p-t-20">数据概览</view>
			<view class="echarsBoxA ">
				<view class="flex_b" style="flex-wrap: wrap;">
					<view class="item flex_c_c" @click="selectType(1)" :class="activeTypeId==1?'activeType':''">
						<view class="f-tA font-weight font-40">0</view>
						<view class="grey">交易总金额(元)</view>
					</view>
					<view class="item flex_c_c" @click="selectType(2)" :class="activeTypeId==2?'activeType':''">
						<view class="f-tA font-weight font-40">0</view>
						<view class="grey">交易笔数</view>
					</view>
					<view class="item flex_c_c" @click="selectType(3)" :class="activeTypeId==3?'activeType':''">
						<view class="f-tA font-weight font-40">0</view>
						<view class="grey">退款金额(元)</view>
					</view>
					<view class="item flex_c_c" @click="selectType(4)" :class="activeTypeId==4?'activeType':''">
						<view class="f-tA font-weight font-40">0</view>
						<view class="grey">总顾客(人)</view>
					</view>
					<view class="item flex_c_c" @click="selectType(5)" :class="activeTypeId==5?'activeType':''">
						<view class="f-tA font-weight font-40">0</view>
						<view class="grey">新客(人)</view>
					</view>
					<view class="item flex_c_c" @click="selectType(6)" :class="activeTypeId==6?'activeType':''">
						<view class="f-tA font-weight font-40">0</view>
						<view class="grey">回头客(人)</view>
					</view>
				</view>

				<view class="">
					<canvas canvas-id="columnCanvas" disable-scroll="true" class="canvas"></canvas>
				</view>

			</view>
		</view>

		<view class="modelBox">
			<view class="font-30 font-weight f-tA">结算明细</view>
			<view class="flex_b color-base-border-bottom p-b-30 m-b-30">
				<view class="">
					<view class="font-40 font-weight f-tA textC">0.00</view>
					<view class="textC grey">收款金额(元)</view>
				</view>
				<view class="font-40 font-weight f-tA textC">＝</view>
				<view class="">
					<view class="font-40 font-weight f-tA textC">0</view>
					<view class="textC grey">交易总金额(元)</view>
				</view>
				<view class="font-40 font-weight f-tA textC">-</view>
				<view class="">
					<view class="font-40 font-weight f-tA textC">0</view>
					<view class="textC grey">退款金额(元)</view>
				</view>
			</view>
			<view class="flex_b">
				<view class="">
					<view class="font-40 font-weight f-tA textC">0.00</view>
					<view class="textC grey">应结算金额(元)</view>
				</view>
				<view class="font-40 font-weight f-tA textC">＝</view>
				<view class="">
					<view class="font-40 font-weight f-tA textC">0</view>
					<view class="textC grey">收款金额(元)</view>
				</view>
				<view class="font-40 font-weight f-tA textC">-</view>
				<view class="">
					<view class="font-40 font-weight f-tA textC">0</view>
					<view class="textC grey">交易手续费(元)</view>
				</view>
			</view>
			<view class="flex_center m-t-30">
				<view class="font-40 font-weight f-tA textC m-r-34">-</view>
				<view class="">
					<view class="font-40 font-weight f-tA textC">0.00</view>
					<view class="textC grey">折扣优惠(元)</view>
				</view>
			</view>
		</view>

		<view class="modelBox">
			<view class="font-30 font-weight f-tA m-b-30">下单频次</view>
			<view class="flex m-b-40">
				<view class="w-90 grey m-r-10"style="text-align: right;">1次</view>
				<uv-line-progress :percentage="80" activeColor="#ff4d1a" inactiveColor="#fff">
					<text class="uv-percentage-slot">{{80}}人</text>
				</uv-line-progress>
			</view>
			<view class="flex m-b-40">
				<view class="w-90 grey m-r-10"style="text-align: right;">2-3次</view>
				<uv-line-progress :percentage="60" activeColor="#ff4d1a" inactiveColor="#fff">
					<text class="uv-percentage-slot">{{60}}人</text>
				</uv-line-progress>
			</view>
			<view class="flex">
				<view class="w-90 grey m-r-10"style="text-align: right;">3次以上</view>
				<uv-line-progress :percentage="30" activeColor="#ff4d1a" inactiveColor="#fff">
					<text class="uv-percentage-slot">{{30}}人</text>
				</uv-line-progress>
			</view>
		</view>

		<timeSelectCom ref="popup"></timeSelectCom>

	</view>
</template>

<script>
	import wxCharts from "@/utils/wxcharts.js"
	export default {
		data() {
			return {
				typeList: ['今日', '近7天', '近30天'],
				typeId: 0,
				activeTypeId: 1
			};
		},
		mixins: ['wxCharts'],
		onLoad() {
			this.infoFn()
		},
		methods: {
			infoFn() {
				let windowWidth = 300;
				try {
					let res = wx.getSystemInfoSync();
					windowWidth = res.windowWidth;
				} catch (e) {

				}
				new wxCharts({
					canvasId: 'columnCanvas',
					type: 'line',
					title: {
						color: 'red',
						name: '123132'
					},
					extra: {
						column: {
							width: '10'
						},
						lineStyle: 'curve'
					},
					categories: ['1月', '2月', '3月', '4月', '5月', '6月', '7月'],
					series: [{
						name: '微信收入',
						data: [1200, 3000, 4500, 3700, 700, 8044, 3700],
						color: '#ff4d1a'
					}],
					yAxis: {
						format: function(val) {
							return val
						},
						// max:400,
						// min:0
					},
					width: 355,
					height: 200,
				})
			},
			selectType(e) {
				this.activeTypeId = e
			},
			openTime() {
				this.$refs.popup.openTime('bottom');
			},
		}
	}
</script>

<style lang="scss" scoped>
	.uv-percentage-slot {
			padding: 1px 5px;
			background-color: #ffffff;
			color: #898FA3;
			// border-radius: 100px;
			font-size: 10px;
			margin-right: -5px;
		}
	.canvas {
		width: 100% !important;
		height: 405rpx;
		margin: 0 auto;
	}

	.echarsBox {
		background-color: #ff4d1a;
		width: 710rpx;
		// height: 771rpx;
		border-radius: 20rpx;
		margin: 0 auto;

		.echarsBoxA {
			margin-top: 20rpx;
			background-image: linear-gradient(to top, #fff, #fff, #fff, #fff, #fff, #ffd9cd);
			border-radius: 20rpx;
			// height: 671rpx;
			box-sizing: border-box;
			padding: 20rpx 10rpx;

			.activeType {
				background-color: #ff4d1a;

				view {
					color: #fff !important;
				}
			}

			.item {
				width: 218rpx;
				height: 120rpx;
				border-radius: 20rpx;

				view {
					text-align: center;
				}
			}
		}
	}

	.itemType {
		width: 120rpx;
		height: 53rpx;
		background-color: #fff;
		border-radius: 10rpx;
		margin-right: 20rpx;
		line-height: 53rpx;
		text-align: center;
	}

	.active {
		background-color: #ff4d19 !important;
		color: #fff !important;
	}
</style>