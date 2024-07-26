<template>
	<view>
		<uv-sticky>
			<navbar :title='title'></navbar>
		</uv-sticky>
		<view class="m-20 p-20 wbg acea-row row-middle row-between bor-10">
			<view class="">
				<view class="font-28">累计到账收益(元)</view>
				<view class="m-t-10 font-bold font-44">0.00</view>
			</view>
			<view class="btn bor-32" @click="$util.redirectTo('/otherpages/withdraw/withdraw')">立即提现</view>
		</view>
		<view class="m-20 p-20 wbg bor-10">
			<view class="font-weight-6 font-28 acea-row row-middle" @click="chosetime"><text
					class="m-r-10">{{year+'年'+month+'月'}}</text>
				<uv-icon name="arrow-down-fill" color="#1c274c99" size="10"></uv-icon>
			</view>
			<view class="m-t-20 p-b-14">
				<uv-line color="#1c274c1a"></uv-line>
			</view>
			<view v-for="(item,index) in 30" :key="index" class="acea-row row-between row-middle item font-28">
				<text class="font-weight-6">到账¥15</text>
				<text class="fcolor">2024-06-05 12:43:52</text>
			</view>
		</view>
		<uv-datetime-picker ref="datetimePicker" v-model="value" mode="year-month" @confirm="confirm">
		</uv-datetime-picker>
	</view>
</template>

<script>
	export default {
		data() {
			let date = new Date()
			let year = date.getFullYear()
			let month = date.getMonth() + 1
			let a = month > 9 ? month : '0' + month
			return {
				title: "累计到账",
				year: year,
				month: a,
				value:Number(new Date())
			};
		},
		methods: {
			chosetime() {
				this.$refs.datetimePicker.open();
			},
			confirm(e) {
				console.log('confirm', e);
				this.value=e.value
				let date = new Date(e.value)
				let year = date.getFullYear()
				let month = date.getMonth() + 1
				this.year=year
				this.month=month > 9 ? month : '0' + month
			}
		}
	}
</script>

<style lang="scss">
	.wbg {
		background: #fff;
	}
	.btn{
		padding: 12rpx 21rpx;
		background: #FF4D1A;
		color: #fff;
	}

	.fcolor {
		color: #898FA3;
	}


	.item {
		padding: 15rpx 0;
	}
</style>