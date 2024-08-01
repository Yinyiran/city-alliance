<template>
	<view>
		<view class="wbg m-20 p-30 bor-30 ">
			<view class="acea-row row-between row-middle">
				<text class="font-30 font-bold">允许地推查看店铺的交易汇总数据</text>
				<uv-switch v-model="value" size='18' activeColor='#FF4D1A'></uv-switch>
			</view>
			<view class="w-414 m-t-10 font-24 fcolor">
				开启后，你的地推可查看店铺的累计营收、累计买单数等
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				value: ''
			};
		}
	}
</script>

<style lang="scss">
	.wbg {
		background: #fff;
	}
	.fcolor{
		color: #898FA3;
	}
	view,text{
		color: #1C274C;
	}
</style>