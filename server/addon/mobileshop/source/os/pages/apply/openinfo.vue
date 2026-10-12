<template>
	<view class="open-info">
		<view class="info-title">填写公司认证信息</view>
		<view class="info-input">
			<view>
				<text class="info-xin">*</text>
				<text>申请类型:</text>
			</view>
			<view class="info-radio">
				<radio-group name="" @change="typeChange">
					<label>
						<radio value="2" style="transform:scale(0.7)" color="#FF8B5C" checked="true" />
						<text>公司</text>
						<radio value="1" style="transform:scale(0.7)" color="#FF8B5C" />
						<text>个人</text>
					</label>
				</radio-group>
			</view>
		</view>

		<template v-if="infoData.type == 2">
			<view class="info-input">
				<view>
					<text class="info-xin">*</text>
					<text>公司名称:</text>
				</view>
				<input class="uni-input" v-model="infoData.company_name" />
			</view>
			<view class="info-input">
				<view>
					<text class="info-xin">*</text>
					<text>联系地址:</text>
				</view>
				<view class="info-picker">
					<pick-regions :default-regions="defaultRegions" @getRegions="handleGetRegions">
						<view>{{ infoData.company_region ? infoData.company_region : '请选择省市区县' }}</view>
					</pick-regions>
				</view>
			</view>
			<view class="info-input">
				<view>
					<text class="info-xin">*</text>
					<text>公司地址:</text>
				</view>
				<input class="uni-input" v-model="infoData.company_address" />
			</view>
			<view class="info-input">
				<view>
					<text class="info-xin">*</text>
					<text>统一社会信用码:</text>
				</view>
				<input class="uni-input" v-model="infoData.business_licence_number" />
			</view>

			<view class="info-input">
				<view>
					<text class="info-xin">*</text>
					<text>营业执照电子版:</text>
				</view>
				<view class="info-upimg" @click="upLicense">
					<image :src="infoData.business_licence_number_electronic" mode="" v-if="infoData.business_licence_number_electronic !== ''"></image>
					<view v-else>
						<image src="../../static/images/upload_img.png" mode=""></image>
						<text>点击上传</text>
					</view>
				</view>
			</view>

			<view class="info-input">
				<view>
					<text class="info-xin">*</text>
					<text>法定经营范围:</text>
				</view>
				<textarea value="" placeholder="请输入法定经营范围" placeholder-class="placeholder" v-model="infoData.business_sphere" />
			</view>
		</template>
		<view class="info-input">
			<view>
				<text class="info-xin">*</text>
				<text v-if="infoData.type == 2">法人姓名:</text>
				<text v-else>联系人姓名:</text>
			</view>
			<input class="uni-input" v-model="infoData.contacts_name" />
		</view>
		<view class="info-input">
			<view>
				<text class="info-xin">*</text>
				<text v-if="infoData.type == 2">法人手机:</text>
				<text v-else>联系人手机:</text>
			</view>
			<input class="uni-input" v-model="infoData.contacts_mobile" />
		</view>
		<view class="info-input">
			<view>
				<text class="info-xin">*</text>
				<text v-if="infoData.type == 2">法人身份证:</text>
				<text v-else>联系人身份证:</text>
			</view>
			<input class="uni-input" v-model="infoData.contacts_card_no" />
		</view>

		<view class="info-input">
			<view>
				<text class="info-xin">*</text>
				<text v-if="infoData.type == 2">法人身份证正面:</text>
				<text v-else>身份证正面:</text>
			</view>
			<view class="info-upimg" @click="upIdcard1">
				<image :src="infoData.contacts_card_electronic_1" mode="" v-if="infoData.contacts_card_electronic_1 !== ''"></image>
				<view v-else>
					<image src="../../static/images/upload_img.png" mode=""></image>
					<text>点击上传</text>
				</view>
			</view>
		</view>

		<view class="info-input">
			<view>
				<text class="info-xin">*</text>
				<text v-if="infoData.type == 2">法人身份证反面:</text>
				<text v-else>身份证反面:</text>
			</view>
			<view class="info-upimg" @click="upIdcard2">
				<image :src="infoData.contacts_card_electronic_2" mode="" v-if="infoData.contacts_card_electronic_2 !== ''"></image>
				<view v-else>
					<image src="../../static/images/upload_img.png" mode=""></image>
					<text>点击上传</text>
				</view>
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
import pickRegions from '@/components/pick-regions/pick-regions.vue';
export default {
	data() {
		return {
			address: '',
			addressValue: '',
			loginMode: 'account',
			shopType: '',
			infoData: {
				//表单数据
				type: 2, //申请类型
				company_name: '', //公司名称
				company_region: '', //公司联系地址
				company_address: '', //公司地址
				business_licence_number: '', //统一社会信用代码
				business_licence_number_electronic: '', //营业执照电子版
				business_sphere: '', //法定经营范围
				contacts_name: '', //联系人姓名
				contacts_mobile: '', //联系人手机
				contacts_card_no: '', //联系人身份证
				contacts_card_electronic_1: '', //身份证正面
				contacts_card_electronic_2: '' //身份证反面
			},
			defaultRegions: [] //省市区选择器
		};
	},
	components: {
		pickRegions
	},
	methods: {
		//申请类型
		typeChange(e) {
			this.infoData.type = e.detail.value;
		},
		// 获取选择的地区
		handleGetRegions(regions) {
			this.infoData.company_region = '';
			this.infoData.company_region += regions[0] != undefined ? regions[0].label : '';
			this.infoData.company_region += regions[1] != undefined ? '-' + regions[1].label : '';
			this.infoData.company_region += regions[2] != undefined ? '-' + regions[2].label : '';
			this.addressValue = '';
			this.addressValue += regions[0] != undefined ? regions[0].value : '';
			this.addressValue += regions[1] != undefined ? '-' + regions[1].value : '';
			this.addressValue += regions[2] != undefined ? '-' + regions[2].value : '';
		},
		//上传营业执照
		upLicense() {
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
					this.infoData.business_licence_number_electronic = res[0];
				}
			);
		},
		//上传身份证正面
		upIdcard1() {
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
					this.infoData.contacts_card_electronic_1 = res[0];
				}
			);
		},
		//上传身份证反面
		upIdcard2() {
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
					this.infoData.contacts_card_electronic_2 = res[0];
				}
			);
		},

		//公司表单验证
		vertify() {
			let rule = [];
			if (this.loginMode == 'account') {
				rule = [
					{ name: 'company_name', checkType: 'required', errorMsg: '请输入公司名称' },
					{ name: 'company_region', checkType: 'required', errorMsg: '请选择联系地址' },
					{ name: 'company_address', checkType: 'required', errorMsg: '请选择公司地址' },
					{ name: 'business_licence_number', checkType: 'required', errorMsg: '请输入统一社会信用代码' },
					{ name: 'business_licence_number_electronic', checkType: 'required', errorMsg: '请上传营业执照' },
					{ name: 'business_sphere', checkType: 'required', errorMsg: '请输入法定经营范围' },
					{ name: 'contacts_name', checkType: 'required', errorMsg: '请输入姓名' },
					{ name: 'contacts_mobile', checkType: 'required', errorMsg: '请输入手机号' },
					{ name: 'contacts_card_no', checkType: 'required', errorMsg: '请输入身份证号' },
					{ name: 'contacts_card_electronic_1', checkType: 'required', errorMsg: '请上传身份证正面' },
					{ name: 'contacts_card_electronic_2', checkType: 'required', errorMsg: '请上传身份证反面' }
				];
			}
			var checkRes = validate.check(this.infoData, rule);
			if (checkRes) {
				return true;
			} else {
				this.$util.showToast({ title: validate.error });
				return false;
			}
		},
		//个人表单验证
		vertify2() {
			let rule = [];
			if (this.loginMode == 'account') {
				rule = [
					{ name: 'contacts_name', checkType: 'required', errorMsg: '请输入姓名' },
					{ name: 'contacts_mobile', checkType: 'required', errorMsg: '请输入手机号' },
					{ name: 'contacts_card_no', checkType: 'required', errorMsg: '请输入身份证号' },
					{ name: 'contacts_card_electronic_1', checkType: 'required', errorMsg: '请上传身份证正面' },
					{ name: 'contacts_card_electronic_2', checkType: 'required', errorMsg: '请上传身份证反面' }
				];
			}
			var checkRes = validate.check(this.infoData, rule);
			if (checkRes) {
				return true;
			} else {
				this.$util.showToast({ title: validate.error });
				return false;
			}
		},
		//上一步
		toPrevious() {
			uni.navigateBack({
				delta: 1
			});
		},
		//下一步
		toNext() {
			if (this.infoData.type == 2) {
				if (this.vertify()) {
					console.log(111);
				}
			} else {
				if (this.vertify2()) {
					console.log(111);
				}
			}
		}
	},
	onLoad(option) {
		this.shopType = option.shopType;
	},
	onReady() {}
};
</script>

<style lang="scss">
page {
	background-color: #fff;
}

.info-title {
	font-size: 50rpx;
	text-align: center;
	margin-top: 30rpx;
	margin-bottom: 50rpx;
}

.info-input {
	width: 500rpx;
	margin: 0 auto;
	display: flex;
	align-items: center;
	margin-bottom: 20rpx;

	view {
		flex: 4;
		text-align: right;
		font-size: 24rpx;

		.info-xin {
			color: #ff0000;
			margin-right: 8rpx;
		}
	}

	.uni-input {
		flex: 5;
		padding: 0 10rpx;
		text-align: left;
		margin-left: 10rpx;
		border: 1px solid #e6e6e6;
		border-radius: 6rpx;
	}

	.info-radio {
		flex: 5;
		padding: 0 10rpx;
		text-align: left;
		margin-left: 10rpx;
	}

	.info-picker {
		flex: 5;
		padding: 4rpx 10rpx;
		margin-left: 10rpx;
		border: 1px solid #e6e6e6;
		border-radius: 6rpx;
		text-align: left;
		view {
			text-align: left;
		}
	}

	.info-num {
		flex: 5;
		margin-left: 10rpx;
		padding: 0 10rpx;
		text-align: left;
	}

	.info-upimg {
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

	.placeholder {
		font-size: 24rpx;
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
</style>
