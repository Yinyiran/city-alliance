<template>
	<view>
		<view class="wbg m-20 p-20 bor-20">
			<view class="font-28 font-weight-6 m-t-6">
				<text class="m-r-32">商户注册手机</text>
				<text class="font-weight-5">138******32</text>
			</view>
			<view class="acea-row row-middle m-b-6 m-t-22 row-between">
				<view class="font-28 font-weight-6">
					<text class="m-r-32">店铺佣金比例</text>
					<text class="font-weight-5">5%</text>
				</view>
				<view class="fcolor font-28" @click="open1">修改</view>
			</view>
		</view>
		<view class="wbg m-20 p-20 bor-20">
			<view class="acea-row row-middle m-t-8  row-between">
				<text class="font-36 font-bold">店铺基础信息</text>
				<text class="fcolor font-28">修改</text>
			</view>
			<view class="font-28 m-t-24">
				<text class="fcolor1 m-r-46 ">店铺简称</text>
				<text>白苏叶店铺</text>
			</view>
			<view class="font-28 m-t-24">
				<text class="fcolor1 m-r-46 ">经营类目</text>
				<text>食品零售</text>
			</view>
			<view class="font-28 m-t-24">
				<text class="fcolor1 m-r-46 ">店铺地址</text>
				<text>河南省郑州市金水区金成国际广场B座</text>
			</view>
			<view class="font-28 m-t-24 m-b-8">
				<text class="fcolor1 m-r-46 ">联系电话</text>
				<text>18393823231</text>
			</view>
		</view>
		<view class="wbg m-20 p-20 bor-20">
			<view class="acea-row row-middle m-t-8  row-between">
				<text class="font-36 font-bold">店铺主体资质</text>
				<text class="fcolor font-28">修改</text>
			</view>
			<view class="font-28 m-t-24">
				<text class="fcolor1 m-r-46 ">商铺类型</text>
				<text>个人</text>
			</view>
			<view class="font-28 m-t-24">
				<text class="fcolor1 m-r-46 ">证件类型</text>
				<text>个人</text>
			</view>
			<view class="font-28 m-t-24">
				<text class="fcolor1 m-r-46 ">证件号码</text>
				<text>41232************231</text>
			</view>
			<view class="font-28 m-t-24 m-b-8">
				<text class="fcolor1 m-r-100">法人</text>
				<text>个人</text>
			</view>
		</view>
		<view class="wbg p-t-40 p-b-40 p-r-20 p-l-20 bor-20 m-20 acea-row row-middle row-between" @click="$util.redirectTo('/otherpages/signing/signing')">
			<text class="font-28 font-weight-6">签约信息</text>
			<uv-icon name="play-right-fill" size="10" color="rgba(28, 39, 76, 0.15)"></uv-icon>
		</view>
		<uv-picker ref="picker" title='选择佣金比例' :columns="columns" @confirm="confirm"></uv-picker>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				columns: [
					['中国', '美国', '日本']
				]
			};
		},
		methods:{
			open1(){
				this.$refs.picker.open()
			},
		}
	}
</script>

<style lang="scss">
	.wbg {
		background: #fff;
	}

	view,
	text {
		color: #1C274C;
	}

	.fcolor {
		color: #FF4D1A;
	}

	.fcolor1 {
		color: #898FA3;
	}
</style>