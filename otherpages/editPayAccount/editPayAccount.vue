<template>
	<view>
		<view class="modelBox">
			<view class="fromTit">店铺佣金比例<text>*</text> </view>
			<view class="font-28 grey m-t-14">请选择佣金比例</view>
			<view class="borModel flex_b">
				<view class="fromTit">收款码编号</view>
				<view class="font-28 grey">请选择经营类目<text class="iconfont icon-yousanjiao"></text> </view>
			</view>
			<view class="flex p-t-20 p-b-20">
				<view class="font-28 f-tA">店铺门头照</view>
				<view class="grey font-26 m-l-14">最多3张；仅支持:jpg，png</view>
			</view>
			<view class="flex" style="flex-wrap: wrap;">
				<view v-for="(item,index) in 2" :key="index" class="sysStyA">
					<image src="/static/logo.png" mode="aspectFill"></image>
					<view class="deleteSty  flex_center">
						<view class="white iconfont icon-shanchu"></view>
						<view class="white m-l-4">删除</view>
					</view>
				</view>
				<view class="sysStyA flex_c_c m-t-20">
					<view class="iconfont icon-plus-circle-fill grey font-50"></view>
					<view class="graycolor font-28">上传图片</view>
				</view>
			</view>
			<view class="borModel flex_b">
				<view class="fromTit">银行账号</view>
				<input type="text" placeholder-class="grey" placeholder="请输入银行卡号" />
			</view>
			<view class="borModel flex_b">
				<view class="fromTit">开户人</view>
				<input type="text" placeholder-class="grey" placeholder="须与身份证主体一致" />
			</view>
			<view class="borModel flex_b">
				<view class="fromTit">开户银行支行</view>
				<view class="font-28 grey">请选择银行支行<text class="iconfont icon-yousanjiao"></text> </view>
			</view>
			
			
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				
			};
		}
	}
</script>

<style lang="scss">
	.sysStyA {
		background-color: #f6f7fb;
		width: 198rpx;
		height: 162rpx;
		border-radius: 10rpx;
		margin-right: 10rpx;
		position: relative;
	
		.deleteSty {
			position: absolute;
			bottom: 0;
			width: 198rpx;
			background-image: linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0));
		}
	
		image {
			width: 198rpx;
			height: 162rpx;
			border-radius: 10rpx;
		}
	}
	.borModel {
		border: 1rpx solid #dddfe4;
		border-radius: 20rpx;
		padding: 30rpx;
		margin-top: 30rpx;
	
		input {
			text-align: right;
			font-size: 28rpx;
		}
	}
	.fromTit {
		font-size: 28rpx;
		font-weight: 600;
		color: #1C274C;

		text {
			color: #FF1414;
			margin-left: 4rpx;
		}
	}
</style>
