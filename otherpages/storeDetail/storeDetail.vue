<template>
	<view>
		<view class="tops">
			<view class="acea-row row-middle">
				<image :src="$util.img(sinfo.store_image)" mode=""></image>
				<view class="acea-row row-column row-between">
					<view class="acea-row row-middle">
						<image src="/static/stores.png" class="w-28 h-28 m-r-8" mode=""></image>
						<view class="text-more font-28" style="width: 200rpx;">{{sinfo.store_name}}</view>
					</view>
					<view class="acea-row row-middle info1">
						{{sinfo.total_num}}人关注
						<view class="line"></view>
						订单数 {{sinfo.order_num}}
					</view>
				</view>
				<view class="acea-row row-middle row-center" :style="{color:guanzhu!=0?'#F55606':''}" @click="btns()">
					<image src="/static/heart.png" v-if="guanzhu==0" mode=""></image>
					<image src="/static/heart1.png" v-else mode=""></image>
					{{guanzhu==0?'关注':'已关注'}}
				</view>
			</view>
		</view>
		<view class="container">
			<view class="acea-row row-middle">
				<view class="w-452 m-r-16">
					<uv-subsection :list="list1" activeColor="#222" custom-style="height: 60rpx;border-radius: 30rpx;"
						custom-item-style="border-radius: 30rpx;" :current="current" @change="onchange"></uv-subsection>
				</view>
				<view class="acea-row row-middle row-center" @click="cutsa">销量
					<image src="/static/down.png" class="w-16 h-16" :class="{roat:sort!=0}" mode=""></image>
				</view>
			</view>
			<goods :list="list"></goods>
			<login ref="login"></login>
		</view>
	</view>
</template>

<script>
	import {
		storeDetail,
		issubscribe,
		shopmemberAdd,
		shopmemberDel,
		goodslist
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				title: "111",
				sinfo: {},
				guanzhu: 0,
				list1: ['推荐', '积分兑换'],
				current: 0,
				sort: 0,
				list:[],
				last_page:1
			};
		},
		async onLoad(res) {
			this.id = res.id
			await this.storeDetail()
			await this.issubscribe()
			await this.getlist()
		},
		methods: {
			cutsa(){
				this.sort=this.sort==0?1:0
				this.page=1
				this.getlist()
			},
			onchange(e) {
				this.current=e
				this.page=1
				this.getlist()
			},
			async getlist() {
				let params = {
					page: this.page,
					order: 'sale_num',
					sort: this.sort == 0 ? 'desc' : "asc",
					goods_type: this.current,
					site_id: this.sinfo.site_id
				}
				await goodslist(params).then(res => {
					this.list=this.page>1?this.list.concat(...res.data.list):res.data.list
					this.last_page=res.data.page_count
				})
			},
			btns() {
				if(!uni.getStorageSync('token')){
					this.$refs.login.open()
					return
				}
				if (this.guanzhu == 0) {
					this.shopmemberAdd()
				} else {
					this.shopmemberDel()
				}
			},
			shopmemberAdd() {
				let params = {
					store_id: this.id
				}
				shopmemberAdd(params).then(res => {
					this.issubscribe()
				})
			},
			shopmemberDel() {
				let params = {
					store_id: this.id
				}
				shopmemberDel(params).then(res => {
					this.issubscribe()
				})
			},
			issubscribe() {
				let params = {
					store_id: this.id
				}
				issubscribe(params).then(res => {
					this.guanzhu = res.data
				})
			},
			async storeDetail() {
				let params = {
					store_id: this.id
				}
				await storeDetail(params).then(res => {
					uni.setNavigationBarTitle({
						title: res.data.store_name
					})
					this.sinfo = res.data
				})
			},
		}
	}
</script>

<style lang="scss">
	.roat{
		transform: rotate(180deg);
	}
	.container {
		min-height: calc(100vh - 240rpx);
		border-radius: 24rpx;
		background: #fff;
		position: relative;
		z-index: 99;
		padding: 36rpx;

		>view:first-child {
			flex-wrap: nowrap;

			>view:last-child {
				width: 218rpx;
				height: 64rpx;
				background: #F5F5F5;
				border-radius: 32rpx;
			}
		}
	}

	.tops {
		.info1 {
			font-weight: 400;
			font-size: 22rpx;
			color: #555555;
			border-radius: 12rpx;
			margin-top: 16rpx;
			height: 54rpx;
			padding: 0 20rpx;

			.line {
				width: 1rpx;
				height: 22rpx;
				background: #DCDFEB;
				margin: 0 20rpx;
			}
		}

		width: 100vw;
		height: 240rpx;
		background: linear-gradient(180deg, #F55606 0%, #F5F5F5 100%);
		padding: 0 32rpx;
		box-sizing: border-box;
		padding-top: 20rpx;

		>view {
			padding: 24rpx 20rpx;
			background: #fff;
			border-radius: 16rpx;

			>image {
				width: 188rpx;
				height: 143rpx;
				border-radius: 16rpx;
				margin-right: 20rpx;
			}

			>view:nth-child(2) {
				width: calc(100% - 188rpx - 20rpx - 32rpx - 112rpx);
				height: 143rpx;
			}

			>view:last-child {
				width: 112rpx;
				height: 48rpx;
				background: #FFFFFF;
				border-radius: 28rpx;
				border: 1rpx solid #F55606;

				>image {
					width: 24rpx;
					margin-right: 8rpx;
					height: 24rpx;
				}
			}
		}
	}
</style>