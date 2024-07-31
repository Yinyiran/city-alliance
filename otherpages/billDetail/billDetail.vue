<template>
	<view>
		<view class="modelBox">
			<view class="font-28 f-tA textC">收款金额(元)</view>
			<view class="font-64 f-tA textC font-weight m-b-60">+20.18</view>
			<view class="font-28 f-t flex m-b-30"><text class="w-190 blockSty">应到账金额</text>9.96</view>
			<view class="font-28 flex f-tA m-b-30"><text class="w-190 grey blockSty">手续费</text>9.96</view>
			<view class="font-28 flex f-tA m-b-30 color-base-border-bottom p-b-30"><text
					class="w-190 grey blockSty">折扣优惠</text>9.96</view>
			<view class="font-28 flex f-tA m-b-30"><text class="w-190 grey blockSty">收款时间</text>2024-07-03 15:27:09
			</view>
			<view class="font-28 flex f-tA m-b-30"><text class="w-190 grey blockSty">交易状态</text>交易成功</view>
			<view class="font-28 flex f-tA m-b-30"><text class="w-190 grey blockSty">收款平台</text>微信</view>
			<view class="font-28 flex f-tA m-b-30"><text class="w-190 grey blockSty">消费类型</text>线下买单</view>
			<view class="font-28 flex f-tA m-b-30"><text class="w-190 grey blockSty">交易编号</text>23671867845163</view>
			<view class="font-28 flex f-tA m-b-30"><text class="w-190 grey blockSty">收款员工</text>暂无</view>
			<view class="font-28 flex f-tA m-b-30"><text class="w-190 grey blockSty">付款备注</text>暂无</view>
			<view class="font-28 flex f-tA m-b-30 color-base-border-bottom p-b-30"><text
					class="w-190 grey blockSty ">收款备注</text>暂无</view>
			<view class="font-28 flex f-tA m-b-30"><text class="w-190 grey blockSty">收款账户</text>中信银行 ****2323(以实际到账为准)
			</view>
			<view class="font-28 flex f-tA"><text class="w-190 grey blockSty">到账时间</text>实时到账<view
					class="font-28 f-t flex m-l-30">资金未到账？</view>
			</view>
			<view class="btnA font-28 grey" @click="refundFn">退款</view>
		</view>
		<view class="bottom-mar"></view>
	</view>
</template>

<script>
	export default {
		data() {
			return {

			};
		},
		methods: {
			refundFn() {
				uni.showModal({
					title: '温馨提示',
					content: '是否确认退款',
					confirmColor:'#ff4d1a',
					success:res=> {
						if (res.confirm) {
							console.log('用户点击确定');
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					}
				});
			}
		}
	}
</script>

<style lang="scss">
	.blockSty {
		display: inline-block;
	}

	.btnA {
		width: 255rpx;
		height: 82rpx;
		background-color: #fff;
		border: 1rpx solid #BBBEC9;
		border-radius: 80rpx;
		text-align: center;
		line-height: 82rpx;
		margin: 0 auto;
		margin-top: 50rpx;
	}
</style>