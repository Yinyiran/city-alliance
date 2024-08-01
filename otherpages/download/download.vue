<template>
	<view>
		<view class="wbg p-20 font-24 fcolor">
			数据下载成功后，请复制链接在电脑浏览器打开，可下载Excel表格
			最多保存3个月!
		</view>
		<view class="thead acea-row row-between">
			<view class="">下载时间</view>
			<view class="">状态</view>
			<view class="">操作</view>
		</view>
		<view class="p-r-50 p-l-50">
			<view v-for="(item,index) in 300" :key="index" class="items ">
				<view class="acea-row row-between p-t-30 p-b-30">
					<view class="">07-05 11:49:15</view>
					<view class="font-24 label bor-10">下载成功</view>
					<view class="fcolor1">复制链接</view>
				</view>
				<uv-line color="#1c274c1a"></uv-line>
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
	page{
		background: #fff;
	}
	.wbg{
		background: #fff;
	}
	.fcolor{
		color: #898FA3;
	}
	.thead{
		padding: 22rpx 50rpx;
		background: #F6F7FB;
		>view{
			font-size: 30rpx;
			color: #1C274C;
		}
	}
	.items{
		view{
			font-size: 30rpx;
			color: #1C274C;
		}
		.label{
			padding: 4rpx 12rpx;
			border: 1rpx solid #BBBEC9;
		}
		.fcolor1{
			color: #FF4D1A;
		}
	}
</style>
