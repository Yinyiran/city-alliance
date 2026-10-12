<template>
	<view>
		<view class="acea-row row-middle row-around p-t-24 p-l-24">
			<view class="acea-row row-middle" @click="checks(1)">
				<text class="m-r-8 font-28">推荐</text>
				<view class="">
					<uv-icon name="arrow-up-fill" :color="select==1&&clicknum=='1'?'#F55606':''" size="10"></uv-icon>
					<uv-icon name="arrow-down-fill" :color="select==1&&clicknum=='2'?'#F55606':''" size="10"></uv-icon>
				</view>
			</view>
			<view class="acea-row row-middle"  @click="checks(2)">
				<text class="m-r-8 font-28">销量</text>
				<view class="">
					<uv-icon name="arrow-up-fill" :color="select==2&&clicknum=='1'?'#F55606':''" size="10"></uv-icon>
					<uv-icon name="arrow-down-fill" :color="select==2&&clicknum=='2'?'#F55606':''" size="10"></uv-icon>
				</view>
			</view>
			<view class="acea-row row-middle"  @click="checks(3)">
				<text class="m-r-8 font-28">距离</text>
				<view class="">
					<uv-icon name="arrow-up-fill" :color="select==3&&clicknum=='1'?'#F55606':''" size="10"></uv-icon>
					<uv-icon name="arrow-down-fill" :color="select==3&&clicknum=='2'?'#F55606':''" size="10"></uv-icon>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: "filter",
		data() {
			return {
				select: -1,
				sort: '',
				clicknum: 0
			};
		},
		methods: {
			checks(e) {
				if (this.select == -1) {
					this.select = e
				}
				if(this.select!=e){
					this.select=e
					this.clicknum=0
				}
				if (e == this.select) {
					if (this.clicknum == 2) {
						this.clicknum = 0
						this.select=-1
					} else {
						this.clicknum++
					}
				}
				this.$emit('sorts',{
					select:this.select,
					clicknum:this.clicknum
				})
			}
		}
	}
</script>

<style lang="scss">

</style>