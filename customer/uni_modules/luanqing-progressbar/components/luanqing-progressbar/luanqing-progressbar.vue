<template>
	<view v-if="mode === 'ring'" class="circle" :style="[circle]">
		<view class="left" :style="[size]">
			<view class="left-circle"
				:style="[{'animation':stycircle1},{	'animation-delay': animationDelay },{'animation-play-state':pause_text},size,leftCircle]">
			</view>
		</view>
		<view class="right" :style="[size]">
			<view class="right-circle"
				:style="[{'animation':stycircle2},{'animation-play-state':pause_text},size,rightcircle]"></view>
		</view>
		<view class="inner" :style="[inner]">
			<slot></slot>
		</view>
	</view>
	
	<view v-else>
		<view class="line-bar" :style="'width:'+lineProgressBarWidth+'rpx;height:'+lineProgressBarHeight+'rpx;background-color:'+defaultColor+';border-radius:'+lineProgressBarRadius+'rpx'">
			<view class="line-bar-active" :style="'background-color:'+activeColor+';width:'+activeLineBarWidth+'rpx;border-radius:'+lineProgressBarRadius+'rpx'"></view>
		</view>
	</view>
</template>

<script>
	/*
	 * 圆环进度条
	 * 
	 * 对外属性：
	 * @property {String} mode 进度条类型  默认圆环ring  line长条
	 * @property {Number} deley 时间 (单位ms 毫秒)
	 * @property {Number} ringSize 圆环的尺寸大小 (即宽高，单位rpx)
	 * @property {Number} border 圆环粗细边框 (rpx)
	 * @property {activeColor} activeColor 长条/圆环已激活部分的背景色
	 * @property {String} defaultColor 长条/圆环默认的背景颜色
	 * @property {Number} lineProgressBarWidth 长条进度条的宽度
	 * @property {Number} lineProgressBarHeight 长条进度条的高度
	 * @property {Number} lineProgressHeight 长条进度条的高度
	 * @property {Number} lineProgressBarRadius 长条进度条的圆角值
	 * 
	 * 对外方法：
	 * @Method start()  开始
	 * @Method stop()  停止
	 * @Method pause()  暂停
	 * @Method resume()  复苏
	 */
	
	// 放到外层减少页面刷新
	let timerCount = undefined;
	
	export default {
		props: {
			// 模式： ring(圆环)   line(长条)
 			mode: {
				type: String,
				default: 'line'
			},
			// 圆环在多少毫秒闭环
			deley: {
				type: Number,
				default: 10
			},
			// 圆环的宽高大小 单位rpx
			ringSize: {
				type: Number,
				default: 200
			},
			// 圆环的粗细值
			border: {
				type: Number,
				default: 30
			},
			// 长条进度条的宽度 单位rpx
			lineProgressBarWidth: {
				type: Number,
				default: 750
			},
			// 长条进度条的高度 单位rpx
			lineProgressBarHeight: {
				type: Number,
				default: 20
			},
			// 长条进度条的圆角值
			lineProgressBarRadius: {
				type: Number,
				default: 12
			},
			activeColor: {
				type: String,
				default: '#E00300'
			},
			defaultColor: {
				type: String,
				default: '#CDCDCD'
			},
		},
		data() {
			return {
				activeLineBarWidth: 0,
				lineProgressBarValue: 0, // 长条进度条的进度百分比值 （0-100）
				stycircle1: {},
				stycircle2: {},
				animationDelay: 0,
				// timer: null,
				timeAll: 0, //时间
				pause_text: '', //暂停

				finish: false, //结束标识 true结束 false未开始
			}
		},
		unmounted() {
			if(timerCount){
				clearInterval(timerCount)
			}
		},
		methods: {
			calcActiveLineWidth(progress){
				progress = progress > 100 ? 100 : progress;
				progress = progress < 0 ? 0 : progress;
				
				// 计算已被激活的长条（线条）进度条的宽度
				// 背景总宽度
				let bgWidth = this.$props.lineProgressBarWidth;
				
				// 已激活的长条进度条宽度
				let activeLineBarWidth = bgWidth * (progress / 100.0);
				
				// 避免越界
				activeLineBarWidth = activeLineBarWidth < 0 ? 0 : activeLineBarWidth;
				activeLineBarWidth = activeLineBarWidth > bgWidth ? bgWidth : activeLineBarWidth;

				this.lineProgressBarValue = progress;
				this.activeLineBarWidth = activeLineBarWidth;
				console.error("宽度：",this.activeLineBarWidth);
			},
			// 开始执行
			start(progress) {
				if(this.mode === 'ring'){
					if (this.deley < 100) {
						uni.showToast({
							title: '进度时间须>=100毫秒',
							icon: 'none',
						});
					} else {
						this.stop();
						this.$nextTick(()=>{
						let time = Number((this.deley / 2).toFixed(2));
							this.stycircle2 = `progross2 ${time}ms linear forwards`;
							this.animationDelay = `${time}ms`;
							this.stycircle1 = `progross1 ${time}ms linear forwards`;
							this.timeAll = this.deley;
							this.startInterval();
						});
					}
				}else{
					this.calcActiveLineWidth(progress);
				}
			},
			// 开始计时器
			startInterval() {
				// 如果已经存在就先清除
				if(timerCount){
					clearInterval(timerCount);
				}
				
				timerCount = setInterval(() => {
					this.timeAll -= 100;
					
					if (this.timeAll < 1) {
						this.finish = true;
						clearInterval(timerCount);
						this.$emit('endTime');
					}
				}, 100)
			},
			// 暂停业务
			pause() { 
				if (this.finish) {
					return
				} 
				
				if(timerCount){
					clearInterval(timerCount);
				}
				
				this.pause_text = 'paused'
				this.$emit('pause', this.timeAll)
			},
			// 继续开始
			resume() {
				if (this.pause_text == '' || this.pause_text == 'running') {
					return
				}
				this.pause_text = 'running';
				this.startInterval();
			},
			// 停止并重置
			stop() {
				this.stycircle2 = `progross 0ms linear forwards`;
				this.stycircle1 = `progross 0ms linear forwards`;
				this.animationDelay = 0;
				this.finish = false;
				
				if(timerCount){
					clearInterval(timerCount);
				}
			},
		},
		computed: {
			circle() {
				const {
					ringSize,
					defaultColor
				} = this
				return {
					width: `${ringSize}rpx`,
					height: `${ringSize}rpx`,
					background: defaultColor
				}
			},
			size() {
				const {
					ringSize
				} = this
				return {
					width: `${ringSize /2 +1}rpx`,
					height: `${ringSize + 1}rpx`
				}
			},
			leftCircle() {
				const {
					ringSize,
					activeColor
				} = this
				return {
					borderTopLeftRadius: `${ringSize}rpx`,
					borderBottomLeftRadius: `${ringSize}rpx`,
					background: activeColor
				}
			},
			rightcircle() {
				const {
					ringSize,
					activeColor
				} = this
				return {
					borderTopRightRadius: `${ringSize}rpx`,
					borderBottomRightRadius: `${ringSize}rpx`,
					background: activeColor
				}
			},
			inner() {
				const {
					ringSize,
					border
				} = this
				return {
					width: `${ringSize - border}rpx`,
					height: `${ringSize - border}rpx`,
				}
			}
		},
		watch: {

		}
	}
</script>

<style>
	.line-bar{
		position: relative;
	}
	.line-bar-active{
		position: absolute;
		height: 100%;
		left: 0;
		top: 0;
		z-index: 999;
	}
	
	.circle {
		border-radius: 50%;
		position: relative;
		z-index: 999;
	}

	.left,
	.right {
		position: absolute;
		overflow: hidden;
		z-index: 999;
	}

	.left-circle {
		transition: all .5s;
		transform-origin: right center;
		transform: rotate(180deg);
	}

	.right-circle {
		transition: all .5s;
		transform-origin: left center;
		transform: rotate(-180deg);
	}

	@keyframes progross1 {
		to {
			transform: rotate(360deg);
		}
	}

	@keyframes progross2 {
		to {
			transform: rotate(0deg);
		}
	}

	.right {
		right: 0;
	}

	.inner {
		background: rgba(255, 255, 255, 1);
		position: absolute;
		z-index: 999;
		border-radius: 100%;
		left: 50%;
		top: 50%;
		transform: translate(-50%, -50%);
		display: flex;
		align-items: center;
		justify-content: center;
		overflow: clip;
	}
</style>
