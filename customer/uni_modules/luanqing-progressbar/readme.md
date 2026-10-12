# luanqing-progressbar

## 圆环进度条、长条进度条、时间计时器、条形进度条、圆形进度条

#### 方法说明

名称|说明|默认值
--|--|--

start()|line模式下可传进度值(0-100)|
stop()|停止并重置状态（ring模式独有）|
pause()|暂停（ring模式独有）|
resume()|恢复（ring模式独有）|


#### 参数说明

名称|说明|默认值
--|--|--

mode|字符串类型，line(长条进度)、ring(圆环进度)| 'ring'
deley|圆环进度条从开始到结束的时间，单位毫秒，最低100ms(ring模式)|
ringSize|圆环的宽高值，单位rpx，(ring模式)| 200
border|圆环边框粗细(ring模式)|30
activeColor|激活状态的颜色，所有模式共享|'E00300'
defaultColor|默认状态的颜色，所有模式共享|'CDCDCD'
lineProgressBarWidth|长条进度条的宽度，单位rpx，(line模式)|750
lineProgressBarHeight|长条进度条的高度，单位rpx，(line模式)|20
lineProgressBarRadius|长条进度条的圆角值，单位rpx，(line模式)|12

#### 示例代码
```
<template>
	<view class="uni_flex_col_align_center" style="height: 750rpx;">
		
		<!-- 圆环进度条 -->
		<luanqing-progressbar 
			v-if="showMode === 'ring'"
			ref="ringProgressBar" 
			active-color="#E00300" 
			default-color="#ACACAC"		
			mode="ring"	
			:border="30" 
			:ringSize="220" 
			:deley="2500">
		</luanqing-progressbar>

		<view v-if="showMode === 'ring'" class="uni_flex_row_between_center mt30">
			<view @click="start" class="button red">开始</view>
			<view @click="stop" class="button red">停止</view>
		</view>
		
		<view v-if="showMode === 'ring'" class="uni_flex_row_between_center mt5" style="margin-bottom: 128rpx;">
			<view @click="pause" class="button blue">暂停</view>
			<view @click="resume" class="button blue">恢复</view>
		</view>
		
		<!-- 长条进度条 -->
		<luanqing-progressbar
			v-if="showMode === 'line'"
			ref="lineProgressBar" 
			active-color="#E00300" 
			default-color="#ACACAC"		
			mode="line"
			:lineProgressBarWidth="600"
			:lineProgressBarHeight="30"	
			:lineProgressBarRadius="20"
			>
		</luanqing-progressbar>
		
		<view v-if="showMode === 'line'" class="uni_flex_row_between_center mt20">
			<view @click="start(25)" class="button red">25%</view>
			<view @click="start(50)" class="button red">50%</view>
			<view @click="start(75)" class="button red">75%</view>
			<view @click="start(100)" class="button red">100%</view>
		</view>
	
	</view>
</template>

<script>
	export default {
		data() {
			return {
				// 显示哪个类型的进度条   ring圆环  line长条
				showMode: 'line' , 
			}
		},
		onReady() {
			if(this.showMode === 'ring'){
				uni.setNavigationBarTitle({
					title:'圆环进度条 定时器'
				});
			}else{
				uni.setNavigationBarTitle({
					title:'线条、长条进度条'
				});
			}
		},
		methods: {
			// 开始
			start(progress){
				if(this.showMode === 'ring'){
					this.$refs.ringProgressBar.start();
				}else{
					this.$refs.lineProgressBar.start(progress);
				}
			},
			// 停止
			stop(){
				if(this.showMode === 'ring'){
					this.$refs.ringProgressBar.stop();
				}else{
					this.$refs.lineProgressBar.stop();
				}
			},
			// 暂停
			pause(){
				if(this.showMode === 'ring'){
					this.$refs.ringProgressBar.pause();
				}else{
					this.$refs.lineProgressBar.pause();
				}
			},
			// 恢复
			resume(){
				if(this.showMode === 'ring'){
					this.$refs.ringProgressBar.resume();
				}else{
					this.$refs.lineProgressBar.resume();
				}
			}
		}
	}
</script>

<style>
.button{
	margin: 20rpx;
	width: 100rpx;
	color: #ffffff;
	font-size: 30rpx;
	padding: 8rpx 20rpx;
	border-radius: 12rpx;
	display: flex;
	align-items: center;
	justify-content: center;
	letter-spacing: 4rpx;
}
.red{
	background-color: #E00300;
}
.blue{
	background-color: cornflowerblue;
}
.button:active{
	background-color: #CDCDCD;
}
</style>

```
