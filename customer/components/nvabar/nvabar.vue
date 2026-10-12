<template>
	<view>
		<uv-navbar :fixed="false" :title="title" :bg-color="bgcolor" :titleStyle='titleStyle'>
			<template v-slot:left>
				<view class="acea-row row-middle" v-if="type == 0">
					<image src="/static/back.png" class="w-44 h-44 m-r-32" @click="left" mode=""></image>
					<image src="/static/hone.png" class="w-40 h-40" @click="home" mode=""></image>
				</view>
				<view class="acea-row row-middle" v-else>
					<image src="/static/back1.png" class="w-44 h-44 m-r-32" @click="left" mode=""></image>
					<image src="/static/home1.png" class="w-40 h-40" @click="home" mode=""></image>
				</view>
			</template>
		</uv-navbar>
	</view>
</template>

<script>
export default {
	name: "nvabar",
	data() {
		return {

		};
	},
	props: {
		title: {
			type: String,
			default: ''
		},
		bgcolor: {
			type: String,
			default: '#ffffff00'
		},
		titleStyle: {
			type: String,
			default: 'color:#fff'
		},
		colors: {
			type: String,
			default: '#fff'
		},
		type: {
			type: Number,
			default: 0
		}
	},
	methods: {
		left() {
			uni.navigateBack()
		},
		home() {
			uni.switchTab({
				url: '/pages/index/index'
			})
		},
	}
}
</script>

<style lang="scss"></style>