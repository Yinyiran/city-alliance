<template>
	<view>
		<view class="modelBox">
			<view class="f-tA font-weight font-28">收款备注</view>
			<textarea name="a" id="" cols="30" rows="10" placeholder-class="font-28 grey"
				placeholder="添加收款备注，可在收款明细中透出(20字以内)"></textarea>
		</view>

		<view class="modelBox flex_b" @click="open">
			<view class="f-tA font-weight font-28">收款员工</view>
			<view class="flex">
				<text class="grey">请选择员工</text>
				<text class="iconfont icon-yousanjiao grey"></text>
			</view>
		</view>

		<uv-picker ref="picker" title="选择收款员工" confirmColor="#ff4d1a" :columns="columns" @confirm="confirm"></uv-picker>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				columns: [
					['店员1', '店员2', '店员3']
				]
			};
		},
		methods: {
			open() {
				this.$refs.picker.open();
			},
			confirm(e) {
				console.log(e, '+++---');
			}
		}
	}
</script>

<style lang="scss" scoped>
	textarea {
		height: 140rpx;
		width: 100%;
		margin-top: 20rpx;
	}
</style>