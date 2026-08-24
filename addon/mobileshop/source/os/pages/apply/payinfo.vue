<template>
	<view class="pay-info">
		<view class="pay-title"><text>填写支付信息</text></view>
		<view class="pay-input">
			<view>
				<text class="pay-xin">*</text>
				<text>店铺名称:</text>
			</view>
			<input class="uni-input" disabled v-model="shop_apply_info.shop_name" />
		</view>
		<view class="pay-input">
			<view>
				<text class="pay-xin">*</text>
				<text>开店套餐:</text>
			</view>
			<input class="uni-input" disabled v-model="shop_apply_info.group_name" />
		</view>
		<view class="pay-input">
			<view>
				<text class="pay-xin">*</text>
				<text>主营行业:</text>
			</view>
			<input class="uni-input" disabled v-model="shop_apply_info.category_name" />
		</view>
		<view class="pay-input">
			<view>
				<text class="pay-xin">*</text>
				<text>入驻时长:</text>
			</view>
			<input class="uni-input" disabled v-model="shop_apply_info.apply_year" />
		</view>
		<view class="pay-input">
			<view>
				<text class="pay-xin">*</text>
				<text>保证金:</text>
			</view>
			<text class="pay-num">{{ shop_apply_info.paying_deposit }}元</text>
		</view>
		<view class="pay-input">
			<view>
				<text class="pay-xin">*</text>
				<text>服务费:</text>
			</view>
			<text class="pay-num">{{ shop_apply_info.paying_apply }}元</text>
		</view>
		<view class="pay-input">
			<view>
				<text class="pay-xin">*</text>
				<text>总计:</text>
			</view>
			<text class="pay-num">{{ shop_apply_info.paying_amount }}元</text>
		</view>
		<view class="pay-instructions"><text>说明： 店铺费用 = 服务费 * 入驻年限 + 保证金；</text></view>
		<view class="pay-input">
			<view>
				<text class="pay-xin">*</text>
				<text>付款凭证:</text>
			</view>
			<view class="pay-upimg" @click="upImg">
				<image :src="imgSrc" mode="" v-if="imgSrc !== ''"></image>
				<view v-else>
					<image src="../../static/images/upload_img.png" mode=""></image>
					<text>点击上传</text>
				</view>
			</view>
		</view>
		<view class="pay-input">
			<view>
				<text class="pay-xin">*</text>
				<text>付款凭证说明:</text>
			</view>
			<textarea value="" placeholder="" v-model="payInstructions" />
		</view>

		<!-- 收款账户表格 -->
		<view class="table">
			<view class="table-title">收款账户</view>
			<view class="table-item">
				<view class="item-name">银行开户行：</view>
				<view class="item-content">{{ receivable_config.bank_account_name }}</view>
			</view>
			<view class="table-item">
				<view class="item-name">银行账户：</view>
				<view class="item-content">{{ receivable_config.bank_account_no }}</view>
			</view>
			<view class="table-item">
				<view class="item-name">开户名称：</view>
				<view class="item-content">{{ receivable_config.bank_name }}</view>
			</view>
			<view class="table-item">
				<view class="item-name">开户所在地：</view>
				<view class="item-content">{{ receivable_config.bank_address }}</view>
			</view>
		</view>

		<!-- 按钮 -->
		<view class="pay-btn" @click="toSubmit">点击提交</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			receivable_config: null,
			shop_apply_info: '',
			imgSrc: '', //付款凭证图片路径
			payInstructions: ''
		};
	},
	onLoad(option) {
		this.receivable_config = JSON.parse(option.receivable_config);
		this.shop_apply_info = JSON.parse(option.shop_apply_info);
		this.shop_apply_info.apply_year = this.shop_apply_info.apply_year + '年';
	},
	methods: {
		//图片上传
		upImg() {
			this.$util.upload(
				1,
				{
					path: '/shopapi/upload/image'
				},
				res => {
					if (res) {
						uni.showToast({
							title: '上传成功',
							icon: 'none'
						});
					}
					this.imgSrc = res[0];
				}
			);
		},
		//点击提交
		toSubmit() {
			if (this.imgSrc !== '') {
				if (this.payInstructions !== '') {
					this.$api.sendRequest({
						url: '/shopapi/apply/editApply',
						data: {
							paying_money_certificate: this.imgSrc,
							paying_money_certificate_explain: this.payInstructions
						},
						success: res => {
							console.log(res);
							if (res.code == 0) {
								uni.showToast({
									title: res.message,
									icon: 'none'
								});
								this.$util.redirectTo('../audit/audit', { shop_apply_info: JSON.stringify(this.shop_apply_info), procedure: 3 });
							}
						}
					});
				} else {
					uni.showToast({
						title: '请填写凭证说明',
						icon: 'none'
					});
				}
			} else {
				uni.showToast({
					title: '请上传付款凭证',
					icon: 'none'
				});
			}
		}
	}
};
</script>

<style lang="scss">
page {
	background-color: #fff;
}
.pay-info {
	width: 100%;
	height: 100%;
	text-align: center;
	.pay-title {
		width: 100%;
		height: 200rpx;
		display: flex;
		align-items: center;
		justify-content: center;
		text {
			font-size: 50rpx;
			font-weight: bold;
		}
	}
	.pay-input {
		width: 500rpx;
		margin: 0 auto;
		display: flex;
		align-items: center;
		margin-bottom: 20rpx;
		view {
			flex: 3;
			text-align: right;
			font-size: 24rpx;
			.pay-xin {
				color: #ff0000;
				margin-right: 8rpx;
			}
		}
		.uni-input {
			flex: 5;
			padding: 0 10rpx;
			text-align: left;
			margin-left: 10rpx;
			background-color: #f7f7f7;
			border: 1px solid #e6e6e6;
			border-radius: 6rpx;
		}
		.pay-num {
			flex: 5;
			margin-left: 10rpx;
			padding: 0 10rpx;
			text-align: left;
		}
		.pay-upimg {
			flex: 5;
			height: 200rpx;
			margin-left: 10rpx;
			padding: 0 10rpx;
			border: 1px dotted #e6e6e6;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			view {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
			}
			image {
				width: 80rpx;
				height: 80rpx;
			}
		}
		textarea {
			flex: 5;
			height: 200rpx;
			margin-left: 10rpx;
			padding: 0 10rpx;
			border: 1px dotted #e6e6e6;
			text-align: left;
		}
	}
	.pay-instructions {
		color: #b2b2b2;
		font-size: 14rpx;
	}
	.table {
		width: 90%;
		margin: 0 auto;
		display: flex;
		flex-direction: column;
		border-left: 1px solid #e6e6e6;
		border-top: 1px solid #e6e6e6;
		margin-bottom: 20rpx;
		.table-title {
			background-color: #f7f7f7;
			padding: 10rpx 16rpx;
			border-right: 1px solid #e6e6e6;
			border-bottom: 1px solid #e6e6e6;
		}
		.table-item {
			display: flex;
			.item-name {
				flex: 1;
				border-right: 1px solid #e6e6e6;
				border-bottom: 1px solid #e6e6e6;
				padding: 6rpx 10rpx;
				text-align: right;
				font-size: 24rpx;
			}
			.item-content {
				flex: 1;
				border-right: 1px solid #e6e6e6;
				border-bottom: 1px solid #e6e6e6;
				padding: 6rpx 10rpx;
				font-size: 24rpx;
				image {
					width: 100rpx;
					height: 100rpx;
				}
			}
		}
	}
	.pay-btn {
		width: 300rpx;
		height: 60rpx;
		color: #fff;
		background-color: #ff8143;
		text-align: center;
		margin: 0 auto;
		line-height: 60rpx;
		border-radius: 6rpx;
		margin-top: 60rpx;
		margin-bottom: 60rpx;
	}
}
</style>
