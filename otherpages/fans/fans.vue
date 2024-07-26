<template>
	<view>
		<view class="">
			<view v-for="(item,index) in 10" :key="index" class="acea-row row-middle wbg m-20 p-20 bor-20">
				<image src="../../static/logo.png" class="w-102 h-102 m-r-20" mode=""></image>
				<view class="h-102">
					<view class="p-t-12 font-32 font-weight-6">用户昵称</view>
					<view class="p-t-6 fcolor">2024.07.30 邀请</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				title:'我的粉丝'
			};
		}
	}
</script>

<style lang="scss">

	.wbg{
		background: #fff;
	}
	.fcolor{
		color: #5D6581;
	}
	.fcolor1{
		color: #fff;
	}
	.fcolor2{
		color: #898FA3;
	}
	.fcolor3{
		color: #606882;
	}
	image{
		border-radius: 50%;
	}
</style>
