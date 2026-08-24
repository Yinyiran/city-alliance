<template>
	<view class="audit">
		<view class="audit-content" v-if="procedure == 2">
			<image src="../../static/images/audit.png" mode=""></image>
			<text>信息审核中，请稍等 ~</text>
		</view>
		<view class="audit-content" v-if="procedure == 6">
			<image src="../../static/images/in_audit.png" mode=""></image>
			<view @click="toPayInfo">审核通过，请填写支付凭证</view>
		</view>
		<view class="audit-content" v-if="procedure == 3">
			<image src="../../static/images/audit.png" mode=""></image>
			<text>支付凭证审核中，请稍等 ~</text>
		</view>
		<!-- 店铺信息 -->
		<view class="table">
			<view class="table-title">店铺信息</view>
			<view class="table-item">
				<view class="item-name">店铺名称：</view>
				<view class="item-content">{{ shop_apply_info.shop_name }}</view>
			</view>
			<view class="table-item" v-if="is_city == 1">
				<view class="item-name">城市分站：</view>
				<view class="item-content">{{ shop_apply_info.site_area_name }}</view>
			</view>
			<view class="table-item">
				<view class="item-name">主营行业：</view>
				<view class="item-content">{{ shop_apply_info.category_name }}</view>
			</view>
			<view class="table-item">
				<view class="item-name">开店套餐：</view>
				<view class="item-content">{{ shop_apply_info.group_name }}</view>
			</view>
			<view class="table-item">
				<view class="item-name">入驻时长：</view>
				<view class="item-content">{{ shop_apply_info.apply_year }}年</view>
			</view>
		</view>

		<!-- 联系人信息 -->
		<view class="table">
			<view class="table-title" v-if="shop_apply_info.cert_type == 2">公司信息</view>
			<view class="table-title" v-else>联系人信息</view>
			<view class="table-item" v-if="shop_apply_info.cert_type == 2">
				<view class="item-name">公司名称：</view>
				<view class="item-content">{{ shop_apply_info.company_name }}</view>
			</view>
			<view class="table-item" v-if="shop_apply_info.cert_type == 2">
				<view class="item-name">公司所在地：</view>
				<view class="item-content">{{ shop_apply_info.company_full_address }}</view>
			</view>
			<view class="table-item">
				<view class="item-name" v-if="shop_apply_info.cert_type == 2">法人姓名：</view>
				<view class="item-name" v-else>联系人姓名：</view>
				<view class="item-content">{{ shop_apply_info.contacts_name }}</view>
			</view>
			<view class="table-item">
				<view class="item-name" v-if="shop_apply_info.cert_type == 2">法人手机号：</view>
				<view class="item-name" v-else>联系人手机号：</view>
				<view class="item-content">{{ shop_apply_info.contacts_mobile }}</view>
			</view>
			<view class="table-item">
				<view class="item-name" v-if="shop_apply_info.cert_type == 2">法人身份证号：</view>
				<view class="item-name" v-else>联系人身份证号：</view>
				<view class="item-content">{{ shop_apply_info.contacts_card_no }}</view>
			</view>
			<view class="table-item">
				<view class="item-name" v-if="shop_apply_info.cert_type == 2">法人身份证正面：</view>
				<view class="item-name" v-else>联系人身份证正面：</view>
				<view class="item-content"><image :src="shop_apply_info.contacts_card_electronic_2" mode=""></image></view>
			</view>
			<view class="table-item">
				<view class="item-name" v-if="shop_apply_info.cert_type == 2">法人身份证反面：</view>
				<view class="item-name" v-else>联系人身份证反面：</view>
				<view class="item-content"><image :src="shop_apply_info.contacts_card_electronic_3" mode=""></image></view>
			</view>
		</view>

		<!-- 银行信息 -->
		<view class="table">
			<view class="table-title">银行信息</view>
			<view class="table-item">
				<view class="item-name">银行开户名：</view>
				<view class="item-content">{{ shop_apply_info.bank_account_name }}</view>
			</view>
			<view class="table-item">
				<view class="item-name">开户银行账号：</view>
				<view class="item-content">{{ shop_apply_info.bank_account_number }}</view>
			</view>
			<view class="table-item">
				<view class="item-name">开户银行支行名称：</view>
				<view class="item-content">{{ shop_apply_info.bank_name }}</view>
			</view>
			<view class="table-item" v-if="shop_apply_info.bank_address">
				<view class="item-name">开户银行支行所在地：</view>
				<view class="item-content">{{ shop_apply_info.bank_address }}</view>
			</view>
			<view class="table-item" v-if="shop_apply_info.bank_code">
				<view class="item-name">支行联行号：</view>
				<view class="item-content">{{ shop_apply_info.bank_code }}</view>
			</view>
		</view>

		<!-- 结算账户信息 -->
		<view class="table">
			<view class="table-title">结算账户信息</view>
			<view class="table-item" v-if="shop_apply_info.bank_type == 2">
				<view class="item-name">用户真实姓名：</view>
				<view class="item-content">{{ shop_apply_info.settlement_bank_account_name }}</view>
			</view>
			<view class="table-item" v-if="shop_apply_info.bank_type == 2">
				<view class="item-name">支付宝账号：</view>
				<view class="item-content">{{ shop_apply_info.settlement_bank_account_number }}</view>
			</view>
			<view class="table-item" v-if="shop_apply_info.bank_type == 1">
				<view class="item-name">结算银行开户名：</view>
				<view class="item-content">{{ shop_apply_info.settlement_bank_account_name }}</view>
			</view>
			<view class="table-item" v-if="shop_apply_info.bank_type == 1">
				<view class="item-name">结算公司银行账号：</view>
				<view class="item-content">{{ shop_apply_info.settlement_bank_account_number }}</view>
			</view>
			<view class="table-item" v-if="shop_apply_info.bank_type == 1">
				<view class="item-name">结算开户银行支行名称：</view>
				<view class="item-content">{{ shop_apply_info.settlement_bank_name }}</view>
			</view>
			<view class="table-item" v-if="shop_apply_info.bank_type == 1">
				<view class="item-name">结算开户银行所在地：</view>
				<view class="item-content">{{ shop_apply_info.settlement_bank_address }}</view>
			</view>
			<view class="table-item" v-if="shop_apply_info.bank_type == 3">
				<view class="item-name">用户真实姓名：</view>
				<view class="item-content">{{ shop_apply_info.settlement_bank_account_name }}</view>
			</view>
			<view class="table-item" v-if="shop_apply_info.bank_type == 3">
				<view class="item-name">微信昵称：</view>
				<view class="item-content">{{ shop_apply_info.settlement_bank_address }}</view>
			</view>
			<view class="table-item" v-if="shop_apply_info.bank_type == 3">
				<view class="item-name">微信账号：</view>
				<view class="item-content">{{ shop_apply_info.settlement_bank_name }}</view>
			</view>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			shop_apply_info: '',
			id_experience: '',
			is_city: '',
			procedure: '',
			receivable_config: ''
		};
	},
	methods: {
		// 点击填写支付信息
		toPayInfo() {
			let shop_apply_info = JSON.stringify(this.shop_apply_info);
			this.$util.redirectTo('../payinfo/payinfo', { shop_apply_info: shop_apply_info, receivable_config: this.receivable_config });
		}
	},
	onLoad(option) {
		this.shop_apply_info = JSON.parse(option.shop_apply_info);
		this.id_experience = option.id_experience;
		this.is_city = option.is_city;
		this.procedure = option.procedure;
		this.receivable_config = option.receivable_config;
	}
};
</script>

<style lang="scss">
page {
	background-color: #fff;
}
.audit {
	width: 100%;
	height: 100%;
	.audit-content {
		width: 100%;
		height: 500rpx;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		image {
			width: 60rpx;
			height: 70rpx;
		}
		text {
			font-size: 28rpx;
			margin-top: 20rpx;
			color: #5d5d5d;
		}
		view {
			background-color: #ff8143;
			color: #fff;
			padding: 6rpx 20rpx;
			border-radius: 8rpx;
			margin-top: 30rpx;
		}
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
				flex: 3;
				border-right: 1px solid #e6e6e6;
				border-bottom: 1px solid #e6e6e6;
				padding: 6rpx 10rpx;
				text-align: right;
				font-size: 24rpx;
			}
			.item-content {
				flex: 4;
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
}
</style>
