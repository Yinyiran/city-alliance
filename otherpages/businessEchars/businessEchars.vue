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
			<view class="echarsBoxA">
				<view class="">
					<view class="">0</view>
					<view class="">交易总金额(元)</view>
				</view>
			</view>
		</view>


		<timeSelectCom ref="popup"></timeSelectCom>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				typeList: ['今日', '近7天', '近30天'],
				typeId: 0,
			};
		},
		methods: {
			openTime() {
				console.log(this.$refs.popup);
				this.$refs.popup.openTime('bottom');
			},
		}
	}
</script>

<style lang="scss" scoped>
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
			height: 671rpx;
			box-sizing: border-box;
			padding: 20rpx 10rpx;
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