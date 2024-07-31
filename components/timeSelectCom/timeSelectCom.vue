<template>
	<view>
		<uv-popup ref="popup" round="10" bgColor="#f6f7fb">
			<view class="popupBoxCom">
				<view class="p-40 p-b-30">
					<view class="font-32 font-weight f-tA">快捷时间</view>
					<view class="timeList flex" style="flex-wrap: wrap;">
						<view v-for="(item,index) in timer" :key="index" @click="selectTimeA(item.id)"
							:class="activeTime==item.id?'activeTime':''">
							{{item.title}}
						</view>
					</view>
					<view class="flex_b m-t-50">
						<view class="font-32 font-weight f-tA">自定义时间</view>
						<view class="font-24 f-t">最长可查找时间跨度一月的交易</view>
					</view>
					<view class="flex_b m-t-30">
						<view class="timeRange font-28 grey" @click="selectTimeB(1)"
							:class="activeTimeB==1?'activeTimeB':''">
							{{activeTimeB&&(activeTimeB==1||startTime)?startTime?startTime:'选择结束时间':'选择结束时间'}}
						</view>

						<view class="font-32 f-tA font-weight">至</view>

						<view class="timeRange font-28 grey" @click="selectTimeB(2)"
							:class="activeTimeB==2?'activeTimeB':''">
							{{activeTimeB&&(activeTimeB==2||startTime)?endTime?endTime:'选择结束时间':'选择结束时间'}}
						</view>
					</view>
				</view>
				<!-- <view class="font-32 textC lh-100">选择上门时间</view> -->
				<picker-view :value="pickerValues" :indicator-style="indicatorStyle" @change="handleChange"
					class="picker-view">
					<picker-view-column>
						<view class="item" v-for="(item, index) in yearsArray" :key="index">{{item}}</view>
					</picker-view-column>
					<picker-view-column>
						<view class="item" v-for="(item, index) in monthsArray" :key="index">{{item}}</view>
					</picker-view-column>
					<picker-view-column>
						<view class="item" v-for="(item, index) in daysArray" :key="index">{{item}}</view>
					</picker-view-column>
				</picker-view>
				<view class="p-30">
					<button type="primary" @click="submitTime">确定</button>
				</view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
	export default {
		name: "timeSelectCom",
		data() {
			return {
				yearsArray: [], // 年份数组
				monthsArray: [], // 月份数组（初始化时需要根据年份动态生成）
				daysArray: [], // 日期数组（需要根据年份和月份动态生成）
				currentYear: 2000, // 当前年份
				currentMonth: 0, // 当前月份（0-11）
				// 指示器样式（可选）
				indicatorStyle: 'height: 50px;',
				activeTime: 1,
				activeTimeB: '',
				timer: [{
					title: '今天',
					id: '1'
				}, {
					title: '昨天',
					id: '2'
				}, {
					title: '近7天',
					id: '3'
				}, {
					title: '近30天',
					id: '4'
				}, ],
				startTime: '',
				endTime: ''
			};
		},
		created() {
			this.generateYears();
			this.generateMonthsAndDays(this.currentYear, this.currentMonth);
		},
		methods:{
			selectTimeB(res) {
				this.activeTimeB = res
				this.activeTime = ''
			},
			selectTimeA(res) {
				this.activeTime = res
				this.activeTimeB = ''
			},
			
			// 初始化年
			generateYears() {
				for (let year = 2000; year <= 2024; year++) {
					this.yearsArray.push(year.toString());
				}
			},
			// 初始化月 日
			generateMonthsAndDays(year, month) {
				// 重置月份和日期数组
				this.monthsArray = [];
				this.daysArray = [];
			
				// 生成月份数组
				for (let m = 0; m < 12; m++) {
					this.monthsArray.push((m + 1).toString().padStart(2, '0')); // 月份从1开始显示，并格式化为两位数
				}
			
				// 根据年份和月份生成日期数组
				let daysInMonth = new Date(year, month + 1, 0).getDate();
				this.daysArray = Array.from({
					length: daysInMonth
				}, (_, i) => (i + 1).toString().padStart(2, '0'));
			
				// 更新picker的初始选中值
				this.pickerValues = [this.yearsArray.indexOf(year.toString()), this.monthsArray.indexOf((month + 1)
					.toString().padStart(2, '0')), 0];
				console.log(this.yearsArray, 'y+++');
				console.log(this.monthsArray, 'm+++');
				console.log(this.daysArray, 'd+++');
			
				this.startTime = ` ${this.yearsArray[0]}年${this.monthsArray[0]}月${this.daysArray[0]}日`
				this.$forceUpdate()
			},
			handleChange(e) {
				const {
					value
				} = e.detail;
				const [selectedYearIndex, selectedMonthIndex, selectedDayIndex] = value;
			
				// 转换索引为实际的值
				const selectedYear = parseInt(this.yearsArray[selectedYearIndex]);
				const selectedMonth = parseInt(this.monthsArray[selectedMonthIndex]) - 1; // 月份数组是从1开始的，但Date对象是从0开始的
			
				// 如果月份改变了，重新生成日期数组
				if (this.currentMonth !== selectedMonth) {
					this.generateMonthsAndDays(selectedYear, selectedMonth);
					// 更新当前月份
					this.currentMonth = selectedMonth;
				}
			
				// 更新picker的选中日期（如果需要的话）
				if (this.pickerValues[2] !== selectedDayIndex) {
					this.pickerValues[2] = selectedDayIndex;
				}
			
				// 在这里可以添加其他逻辑，比如显示选中的日期等
				console.log(`Selected Date: ${selectedYear}-${selectedMonth + 1}-${this.daysArray[selectedDayIndex]}`);
				if (this.activeTimeB == 1) {
					this.startTime = ` ${selectedYear}年${selectedMonth + 1}月${this.daysArray[selectedDayIndex]}日`
				}
				if (this.activeTimeB == 2) {
					this.endTime = ` ${selectedYear}年${selectedMonth + 1}月${this.daysArray[selectedDayIndex]}日`
				}
			},
			
			openTime() {
				this.$refs.popup.open('bottom');
			},
			onchange(e) {
			
			},
		}
	}
</script>

<style lang="scss">
.popupBoxCom {
		width: 750rpx;
		// height: 718rpx;
		border-radius: 40rpx 40rpx 0rpx 0rpx;
		background-color: #fff;
	}

	/deep/.picker-view {
		background-color: #fff !important;
	}

	.picker-view {
		width: 750rpx;
		height: 300rpx;
		background-color: #fff !important;
		margin-bottom: 30rpx;

		/deep/ picker-view-column {
			background-color: #f6f7fb !important;
		}

		.item {
			line-height: 100rpx;
			text-align: center;
			font-weight: 600;
			font-size: 28rpx;
		}
	}
	.activeTimeB {
		border: 1rpx solid #ff4d19 !important;
		color: #ff4d19 !important;
		font-weight: 600;
	}
	
	.activeTime {
		background-color: #ff4d1a !important;
		color: #fff !important;
	}
	
	.timeList {
		view {
			width: 190rpx;
			height: 73rpx;
			border-radius: 73rpx;
			background-color: #f6f7fb;
			text-align: center;
			line-height: 73rpx;
			margin-right: 20rpx;
			margin-top: 20rpx;
		}
	
	}
	
	.timeRange {
		width: 267rpx;
		height: 87rpx;
		border-radius: 30rpx;
		background-color: #f6f7fb;
		text-align: center;
		line-height: 87rpx;
	}
</style>