<template>
	<view>
		<uv-sticky>
			<navbar :title='title'></navbar>
		</uv-sticky>
		<uv-collapse accordion :border='false' @open="open">
			<uv-collapse-item title="2024年06月"   :border='false'>
				<view v-for="(item,index) in 30" :key="index" class="acea-row row-between row-middle item font-28">
					<text class="font-weight-6">到账¥15</text>
					<text class="fcolor">2024-06-05 12:43:52</text>
				</view>
			</uv-collapse-item>
		</uv-collapse>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				title: '提现明细'
			};
		},
		methods: {
			change() {},
			close() {},
			open(e) {},
		}
	}
</script>

<style lang="scss">
	/deep/ .uv-collapse-item{
		background: #fff;
		margin: 20rpx;
		border-radius: 10rpx;
	}
	.fcolor {
		color: #898FA3;
	}
	
	.item {
		padding: 15rpx 0;
	}
	/deep/ .uv-cell__body{
		padding: 20rpx !important;
	}
	/deep/ .uv-cell__title-text{
		font-size: 28rpx;
		font-weight: 650;
		color: #1C274C;
	}
	/deep/ .uv-collapse-item__content__text{
		padding: 12rpx 0;
	}
</style>