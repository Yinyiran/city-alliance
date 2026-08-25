<template>
	<view class="container">
		<!-- 将按钮移到最外层 -->
		<view class="b1" @click="show">{{!showitem?'立即领取':`${point}积分`}}</view>
		<view class="aws" v-if="showitem" >
			<!-- <image src="/static/bag1.png" v-if='!showitem' class="w-52 h-52" mode=""></image>
			<image src="/static/bag2.png" v-else class="w-52 h-52" mode=""></image> -->
			<view class="a2" :class="{shows:showitem}" >
				<view class="">获得奖励</view>
				<view class="">{{point}}积分</view>
			</view>
		</view>
		<view class="circle-container">
			<!-- <l-circle :strokeWidth='3' :trailWidth='3' :duration='1000' :size='50' strokeColor='#FED83D' :canvas='true'
				:current.sync="modelVale" :percent="target">
				<view class="a1s">
					<image src="/static/bag1.png" v-if='!showitem' class="w-52 h-52" mode=""></image>
					<image src="/static/bag2.png" v-else class="w-52 h-52" mode=""></image>
					<view class="a2" :class="{shows:showitem}" v-if="showitem">
						<view class="">获得奖励</view>
						<view class="">{{point}}积分</view>
					</view>
				</view>
			</l-circle> -->
			<luanqing-progressbar  ref="ringProgressBar" active-color="#FED83D"
				default-color="rgba(0, 0, 0, 0.32)" mode="ring" :border="8" :ringSize="100" :deley="target*1000">
				<view class="a1s">
					<image src="/static/bag1.png" v-if='!showitem' class="w-52 h-52" mode=""></image>
					<image src="/static/bag2.png" v-else class="w-52 h-52" mode=""></image>
					<!-- <view class="a2" :class="{shows:showitem}" v-if="!showitem" >
						<view class="">获得奖励</view>
						<view class="">{{point}}积分</view>
					</view> -->
				</view>
			</luanqing-progressbar>
		</view>
	</view>
</template>
<script>
	export default {
		data() {
			return {
				modelVale: 0,
				showitem: false
			}
		},
		props: {
			target: {
				type: Number,
				default: 0
			},
			point: {
				type: String,
				default: 0
			}
		},
		methods: {
			start(){
				this.$refs.ringProgressBar.start();
			},
			stop(){
				this.$refs.ringProgressBar.stop();
			},
			pause(){
				this.$refs.ringProgressBar.pause();
			},
			resume(){
				this.$refs.ringProgressBar.resume();
			},
			show() {
				uni.navigateTo({
					url:'/otherpages/inviteDatas/inviteDatas'
				})
				// this.$emit('godetail')
				// this.$refs.ringProgressBar.start();
				// return
				// if (this.target < 100) {
				// 	uni.showToast({
				// 		title: '还未到领取时间',
				// 		icon: "none"
				// 	})
				// 	return
				// } else {
				// 	this.$emit('getpoint')
				// }
			},
			succs() {
				this.showitem = true
				setTimeout(() => {
					this.showitem = false
					this.$emit('restart')
				}, 2000)
			},
		}
	}
</script>
<style lang="scss">
	.container {
		width: 100rpx;
		height: 100rpx;
		position: relative;

		.circle-container {
			position: relative;
			z-index: 1;
		}

		.b1 {
			position: absolute;
			bottom: -40rpx;
			/* 向下延伸一点 */
			left: 50%;
			transform: translateX(-50%);
			z-index: 99999;
			/* 降低但确保在circle-container之上 */
			width: 120rpx;
			/* 加宽 */
			height: 40rpx;
			/* 加高 */
			background: linear-gradient(90deg, #FF4753 0%, #F51D1D 100%);
			border-radius: 20rpx;
			/* 圆角加大 */
			font-weight: 600;
			font-size: 22rpx;
			/* 字体稍大 */
			color: #FFFFFF;
			text-align: center;
			line-height: 40rpx;
			/* 垂直居中 */
			box-shadow: 0 2rpx 4rpx rgba(0, 0, 0, 0.2);
			/* 添加阴影增强层次感 */
		}
		.aws{
			display: flex;
			flex-direction: column;
			justify-content: center;
			width: 250rpx;
			height: 100rpx;
			background: rgba(0, 0, 0, 0.32);
			box-sizing: border-box;
			padding-left: 32rpx;
			border-radius: 50rpx;
			position: absolute;
			right: 0;
			top: 0;
			// transform: translateX(250rpx);
			text-align: left;
			// background: red;
			z-index: -1;
			>.a2 {
			
				>view:first-child {
					font-weight: 400;
					font-size: 20rpx;
					color: #FFFFFF;
				}
			
				>view:last-child {
					font-weight: 600;
					font-size: 24rpx;
					color: #FED83D;
				}
			}
		}
		.a1s {
			width: 100rpx;
			height: 100rpx;
			background: rgba(0, 0, 0, 0.32);
			border-radius: 50%;
			text-align: center;
			line-height: 100rpx;
			position: relative;
			// top: 0;
			// right: 0;
			z-index: 10;

			image {
				width: 52rpx;
				height: 52rpx;
				position: absolute;
				left: 50%;
				top: 50%;
				transform: translate(-50%, -50%);
			}

			>.a2 {
				display: flex;
				flex-direction: column;
				justify-content: center;
				width: 250rpx;
				height: 100rpx;
				background: rgba(0, 0, 0, 0.32);
				box-sizing: border-box;
				padding-left: 32rpx;
				border-radius: 50rpx;
				position: absolute;
				right: 0;
				top: 0;
				// transform: translateX(250rpx);
				text-align: left;
				// background: red;
				z-index: -1;

				>view:first-child {
					font-weight: 400;
					font-size: 20rpx;
					color: #FFFFFF;
				}

				>view:last-child {
					font-weight: 600;
					font-size: 24rpx;
					color: #FED83D;
				}
			}
		}
	}

	.shows {
		// transform: translateX(0) !important;
	}
</style>