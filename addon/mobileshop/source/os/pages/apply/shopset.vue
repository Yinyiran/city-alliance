<template>
	<view class="shop-set">
		<view class="set-title">选择开店套餐</view>
		<view class="set-content">
			<view class="set-item" :class="isContour == index ? 'set-item-bind' : ''" v-for="(item, index) in group_info" :key="index" @click="setChange(index)">
				<view class="item-top">
					<text class="top-text1">{{ item.group_name }}</text>
					<text class="top-text2">{{ item.remark }}</text>
				</view>
				<view class="itrm-content">
					<view class="item-list" v-for="(promotion, index) in item.promotion" :key="index">
						<text class="list-yes" v-if="promotion.is_checked == 1">√</text>
						<text class="list-no" v-else>×</text>
						<view>{{ promotion.title }}</view>
					</view>
				</view>
				<view class="set-btn">￥{{ item.fee }}/年</view>
			</view>
		</view>
		<view class="set-next">
			<view class="next-btn" @click="toPrevious">上一步</view>
			<view class="next-btn" @click="toNext">下一步</view>
		</view>
	</view>
</template>

<script>
import validate from '@/common/js/validate.js';
import { mapMutations } from 'vuex';
export default {
	data() {
		return {
			isContour: '',
			group_info: [],
			shop_set: '' //套餐类型
		};
	},
	methods: {
		setChange(index) {
			this.isContour = index;
		},
		//获取套餐信息
		getSetInfo() {
			this.$api.sendRequest({
				url: '/shopapi/apply/index',
				success: res => {
					this.group_info = res.data.group_info;
				}
			});
		},
		//点击上一步
		toPrevious() {
			uni.navigateBack({
				delta: 1
			});
		},
		//点击下一步
		toNext() {
			if (this.isContour !== '') {
				this.shop_set = this.group_info[this.isContour].group_id;
				this.$store.commit('getShopSet', this.shop_set);
				// util.redirectTo('../openinfo/openinfo')
			} else {
				this.$util.showToast({
					title: '请选择套餐',
					icon: 'none'
				});
			}
		}
	},
	onReady() {
		this.getSetInfo();
	}
};
</script>

<style lang="scss" scoped>
page {
	background-color: #fff;
}
.shop-set {
	width: 100%;
	.set-title {
		text-align: center;
		font-size: 68rpx;
		margin-top: 50rpx;
	}
	.set-content {
		width: 700rpx;
		margin: 0 auto;
		margin-top: 30rpx;
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		.set-item {
			width: 340rpx;
			padding: 30rpx 10rpx 20rpx 10rpx;
			box-sizing: border-box;
			margin-bottom: 20rpx;
			border: 1px solid #e9e9e9;
			.item-top {
				width: 60%;
				margin: 0 auto;
				text-align: center;
				margin-bottom: 20rpx;
				display: flex;
				flex-direction: column;
				border-bottom: 1px solid #e9e9e9;
				text:first-child {
					font-size: 36rpx;
				}
				text:last-child {
					font-size: 24rpx;
				}
			}
			.itrm-content {
				display: flex;
				flex-direction: column;
				.item-list {
					width: 70%;
					margin: 0 auto;
					display: flex;
					align-items: center;
					.list-yes {
						color: #ff8143;
						margin-right: 10rpx;
					}
					.list-no {
						color: #ff0000;
						margin-right: 10rpx;
					}
				}
			}
			.set-btn {
				width: 180rpx;
				height: 60rpx;
				border: 1px solid #ff8143;
				color: #ff8143;
				text-align: center;
				line-height: 60rpx;
				border-radius: 6rpx;
				margin: 0 auto;
				margin-top: 30rpx;
				font-size: 24rpx;
			}
		}
		.set-item-bind {
			border: 1px solid #ff8143;
		}
	}
	.set-next {
		width: 320rpx;
		height: 60rpx;
		margin: 0 auto;
		display: flex;
		justify-content: space-between;
		margin-top: 20rpx;
		margin-bottom: 50rpx;
		.next-btn {
			width: 150rpx;
			height: 60rpx;
			background-color: #ff8143;
			color: #fff;
			text-align: center;
			line-height: 60rpx;
			border-radius: 6rpx;
		}
	}
}
</style>
