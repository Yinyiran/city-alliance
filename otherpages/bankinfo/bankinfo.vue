<template>
	<view>
		<!-- 
		<view class="container">
			<view class="font-32 font-weight-6 p-t-32 p-l-32">个人信息</view>
			<uv-input placeholder="请输入真实姓名" border="none" v-model='form.realname' :customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">真实姓名</view>
				</template>
			</uv-input>
			<uv-input placeholder="请输入证件号码" border="none" v-model='form.cert_no' :customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">证件号码</view>
				</template>
			</uv-input>
			<view class="p-28 acea-row row-middle">
				<view class="font">证件有效期</view>
				<view class="acea-row row-middle">
					<label class="m-r-48 acea-row row-middle" @click="form.cert_validity_type=0">
						<radio :value="0" :checked="form.cert_validity_type==0" /><text class="font-32">非长期有效</text>
					</label>
					<label class="acea-row row-middle" @click="form.cert_validity_type=1">
						<radio :value="1" :checked="form.cert_validity_type==1" /><text class="font-32">长期有效</text>
					</label>
				</view>
			</view>
			<view class="" @click="open(1)">
				<uv-input placeholder="请选择开始时间" readonly v-model="form.cert_begin_date" border="none"
					:customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">开始时间</view>
					</template>
				</uv-input>
			</view>
			<view class="" @click="open(2)">
				<uv-input placeholder="请选择结束时间" readonly v-model="form.cert_end_date" border="none"
					:customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">结束时间</view>
					</template>
				</uv-input>
			</view>
			<uv-input placeholder="请填写手机号码" type="number" v-model="form.mobile_no" maxlength="11" border="none"
				:customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">手机号</view>
				</template>
			</uv-input>
		</view> -->
		<view class="container">
			<view class="font-32 font-weight-6 p-t-32 p-l-32">企业开户</view>
			<uv-input placeholder="请输入企业名称" border="none" v-model='form.enterprise_name' :customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">企业名称</view>
				</template>
			</uv-input>
			<!-- 			<uv-input placeholder="请输入银行卡号" border="none" v-model='form.card_no' :customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">卡号</view>
				</template>
			</uv-input> -->
			<view class="" @click="open1(1)">
				<uv-input placeholder="请选择企业注册省份" readonly v-model="provname" border="none" :customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">企业注册省份</view>
					</template>
				</uv-input>
			</view>
			<view class="" @click="open2(1)">
				<uv-input placeholder="请选择企业注册市" readonly v-model="cname" border="none" :customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">企业注册市</view>
					</template>
				</uv-input>
			</view>
			<view class="" @click="open3(1)">
				<uv-input placeholder="请选择企业注册区域" readonly v-model="aname" border="none" :customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">企业注册区域</view>
					</template>
				</uv-input>
			</view>
			<view class="">
				<uv-input placeholder="请输入企业详细地址" v-model="form.address" border="none" :customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">企业详细地址</view>
					</template>
				</uv-input>
			</view>
			<view class="">
				<uv-input placeholder="请输入统一社会信用码" v-model="form.business_licence_number" border="none"
					:customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">统一社会信用码</view>
					</template>
				</uv-input>
			</view>
			<view class="p-28 acea-row row-middle">
				<view class="font">营业执照有效期</view>
				<view class="acea-row row-middle">
					<label class="m-r-48 acea-row row-middle" @click="form.business_license_type=0">
						<radio :value="0" :checked="form.business_license_type==0" /><text class="font-32">非长期有效</text>
					</label>
					<label class="acea-row row-middle" @click="form.business_license_type=1">
						<radio :value="1" :checked="form.business_license_type==1" /><text class="font-32">长期有效</text>
					</label>
				</view>
			</view>
			<view class="" @click="open(1)">
				<uv-input placeholder="请选择开始时间" readonly v-model="form.business_license_start_time" border="none"
					:customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">开始时间</view>
					</template>
				</uv-input>
			</view>
			<view class="" @click="open(2)">
				<uv-input placeholder="请选择结束时间" readonly v-model="form.business_license_end_time" border="none"
					:customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">结束时间</view>
					</template>
				</uv-input>
			</view>
			<view class="">
				<uv-input placeholder="请输入法人姓名" v-model="form.legal_name" border="none" :customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">法人姓名</view>
					</template>
				</uv-input>
			</view>
			<view class="">
				<uv-input placeholder="请输入联系人姓名" v-model="form.contact_name" border="none" :customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">联系人姓名</view>
					</template>
				</uv-input>
			</view>
			<uv-input placeholder="请填写联系人手机号" type="number" v-model="form.contact_mobile" maxlength="11" border="none"
				:customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">联系人手机号</view>
				</template>
			</uv-input>
			<uv-input placeholder="请填写法人身份证号码" type="number" v-model="form.id_card_num" border="none"
				:customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">法人身份证号码</view>
				</template>
			</uv-input>
			<view class="p-28 acea-row row-middle">
				<view class="font">法人证件有效期</view>
				<view class="acea-row row-middle">
					<label class="m-r-48 acea-row row-middle" @click="form.id_card_time_type=0">
						<radio :value="0" :checked="form.id_card_time_type==0" /><text class="font-32">非长期有效</text>
					</label>
					<label class="acea-row row-middle" @click="form.id_card_time_type=1">
						<radio :value="1" :checked="form.id_card_time_type==1" /><text class="font-32">长期有效</text>
					</label>
				</view>
			</view>
			<view class="" @click="open(3)">
				<uv-input placeholder="请选择开始时间" readonly v-model="form.id_card_start_time" border="none"
					:customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">开始时间</view>
					</template>
				</uv-input>
			</view>
			<view class="" @click="open(4)">
				<uv-input placeholder="请选择结束时间" readonly v-model="form.id_card_end_time" border="none"
					:customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">结束时间</view>
					</template>
				</uv-input>
			</view>
		</view>
		<uv-datetime-picker ref="datetimePicker" v-model="value" mode="date" @confirm="confirm">
		</uv-datetime-picker>
		<uv-picker ref="picker" keyName='name' :columns="[plist]" @confirm="confirm1"></uv-picker>
		<uv-picker ref="picker1" keyName='name' :columns="[clist]" @confirm="confirm2"></uv-picker>
		<uv-picker ref="picker2" keyName='name' :columns="[alist]" @confirm="confirm3"></uv-picker>
		<view class="h-300"></view>
		<view class="btms iphone-x">
			<view class="" @click="submit">确定</view>
		</view>
	</view>
</template>

<script>
	import {
		arealists,
		openAccount
	} from "@/common/js/api.js"
	import {
		mobile
	} from '@/uni_modules/uv-ui-tools/libs/function/test.js';
	import {
		timeFormat
	} from '@/uni_modules/uv-ui-tools/libs/function/index.js';
	import validate from '@/common/js/validate.js';
	export default {
		data() {
			return {
				list: [],
				check: 0,
				customStyle: "padding:28rpx",
				form: {
					enterprise_name: "",
					province_id: "",
					city_id: "",
					district_id: "",
					address: "",
					business_licence_number: "",
					business_license_type: 1,
					business_license_start_time: "",
					business_license_end_time: "",
					legal_name: "",
					contact_name: "",
					id_card_num: "",
					contact_mobile: "",
					id_card_time_type: 1,
					id_card_end_time: "",
					id_card_start_time: "",
				},
				isend: false,
				codetxt: "60s后重新获取",
				times: 60,
				value: Number(new Date()),
				type: 1,
				plist: [],
				clist: [],
				alist: [],
				provname: "",
				cname: "",
				aname: ''
			}
		},
		onLoad() {
			this.arealists()
		},
		methods: {
			confirm1(e) {
				this.form.province_id = e.value[0].id
				this.provname = e.value[0].name
				this.form.city_id = ''
				this.form.district_id = ''
				this.cname = ''
				this.aname = ''
				this.$refs.picker1.setIndexs([0], true)
				this.$refs.picker2.setIndexs([0], true)
				this.getarea1()
			},
			confirm2(e) {
				this.form.city_id = e.value[0].id
				this.cname = e.value[0].name
				this.form.district_id = ''
				this.aname = ''
				this.$refs.picker2.setIndexs([0], true)
				this.getarea2()
			},
			confirm3(e) {
				this.form.district_id = e.value[0].id
				this.aname = e.value[0].name
			},
			open1(e) {
				this.$refs.picker.open()
			},
			open2() {
				if (!this.form.province_id) {
					uni.showToast({
						title: '请先选择省份',
						icon: 'none'
					})
					return
				}
				this.$refs.picker1.open()
			},
			open3() {
				if (!this.form.city_id) {
					uni.showToast({
						title: '请先选择市',
						icon: 'none'
					})
					return
				}
				this.$refs.picker2.open()
			},
			getarea1() {
				let params = {
					pid: this.form.province_id
				}
				arealists(params).then(res => {
					this.clist = res.data
				})
			},
			getarea2() {
				let params = {
					pid: this.form.city_id
				}
				arealists(params).then(res => {
					this.alist = res.data
				})
			},
			arealists() {
				arealists().then(res => {
					this.plist = res.data
				})
			},
			confirm(e) {
				if (this.type == 1) {
					this.form.business_license_start_time = timeFormat(e.value, 'yyyy-mm-dd')
				} else if (this.type == 2) {
					this.form.business_license_end_time = timeFormat(e.value, 'yyyy-mm-dd')
				} else if (this.type == 3) {
					this.form.id_card_start_time = timeFormat(e.value, 'yyyy-mm-dd')
				} else {
					this.form.id_card_end_time = timeFormat(e.value, 'yyyy-mm-dd')
				}
			},
			open(e) {
				this.type = e
				this.value = Number(new Date())
				this.$refs.datetimePicker.open()
			},
			getcode() {
				if (!mobile(this.form.mobile)) {
					uni.showToast({
						title: '手机号格式错误',
						icon: "none"
					})
					return
				}
				let params = {
					mobile: this.form.mobile
				}
				if (!this.isend) {
					mobileCode(params).then(res => {
						this.isend = true
						this.form.key = res.data.key
						let time = setInterval(() => {
							this.times--
							this.codetxt = this.times + 's后重新获取'
							if (this.times == 1) {
								clearInterval(time)
								this.times = 60
								this.isend = false
							}
						}, 1000)
					})
				}

			},
			submit() {
				var rule = [{
						name: 'enterprise_name',
						checkType: 'required',
						errorMsg: '请输入企业名称'
					}, {
						name: 'province_id',
						checkType: 'required',
						errorMsg: '请选择企业注册省份'
					},
					{
						name: 'district_id',
						checkType: 'required',
						errorMsg: '请选择企业注册市'
					},
					{
						name: 'district_id',
						checkType: 'required',
						errorMsg: '请选择企业注册区'
					},
					{
						name: 'address',
						checkType: 'required',
						errorMsg: '请输入企业详细地址'
					},
					{
						name: 'business_licence_number',
						checkType: 'required',
						errorMsg: '请输入统一社会信用码'
					},
					{
						name: 'business_license_start_time',
						checkType: 'required',
						errorMsg: '请选择营业执照开始时间'
					},
					{
						name: 'business_license_end_time',
						checkType: 'required',
						errorMsg: '请选择营业执照结束时间'
					},
					{
						name: 'legal_name',
						checkType: 'required',
						errorMsg: '请输入法人姓名'
					},
					{
						name: 'contact_name',
						checkType: 'required',
						errorMsg: '请输入联系人姓名'
					},
					{
						name: 'id_card_num',
						checkType: 'required',
						errorMsg: '请输入法人身份证号码'
					},
					{
						name: 'contact_mobile',
						checkType: 'required',
						errorMsg: '请输入联系人手机号'
					},
					{
						name: 'contact_mobile',
						checkType: 'phoneno',
						errorMsg: '请输入正确的手机号'
					},
					{
						name: 'id_card_start_time',
						checkType: 'required',
						errorMsg: '请选择证件开始时间'
					},
					{
						name: 'id_card_end_time',
						checkType: 'required',
						errorMsg: '请选择证件结束时间'
					}
				];
				var checkRes = validate.check(this.form, rule);
				if (!checkRes) {
					this.$util.showToast({
						title: validate.error
					});
					return false;
				}

				openAccount(this.form).then(res => {
					if (res.code == 0) {
						uni.showToast({
							title: '申请成功'
						})
					} else {
						uni.showToast({
							title: res.message,
							icon: 'none'
						})
					}
				})
			},
			getcate() {
				categoryList().then(res => {
					this.list = res.data
				})
			},
		}
	}
</script>

<style lang="scss">
	.btms {
		position: fixed;
		bottom: 0;
		left: 0;
		background: #fff;
		text-align: center;
		width: 100vw;
		z-index: 99;

		>view {
			margin: 20rpx 40rpx;
			line-height: 96rpx;
			background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
			border-radius: 48rpx;
			font-weight: 600;
			font-size: 32rpx;
			color: #FFFFFF;
		}
	}

	radio {
		transform: scale(0.7);
	}

	.container {
		margin: 20rpx 32rpx;
		background: #fff;
		border-radius: 24rpx;

		.font {
			font-size: 28rpx;
			color: #222222;
			width: 200rpx;
			white-space: nowrap;
		}

		.getcode {
			font-weight: 400;
			font-size: 32rpx;
			color: #F55606;
		}
	}

	.part {
		margin: 0 32rpx;
		background: #fff;
		border-radius: 24rpx;

		>view:last-child {
			>view {
				display: inline-block;
				width: calc((100% - 32rpx)/3);
				height: 64rpx;
				background: #F5F5F5;
				border-radius: 8rpx;
				margin-top: 24rpx;
				margin-right: 16rpx;
				text-align: center;
				box-sizing: border-box;
				line-height: 64rpx;
				font-weight: 400;
				font-size: 26rpx;
				color: #222200;
			}

			>view:nth-child(3n) {
				margin-right: 0;
			}

			.on {
				border: 1rpx solid #F55606;
				position: relative;
				color: #F55606;

				>image {
					position: absolute;
					right: 0;
					bottom: 0;
					width: 28rpx;
					height: 28rpx;
				}
			}
		}
	}

	.bgs {
		width: 100vw;
		height: 556rpx;
		position: fixed;
		top: 0;
		left: 0;
		z-index: -1;
	}

	.bans {
		margin: 12rpx 32rpx 20rpx 32rpx;

		image {
			width: 100%;
			height: 330rpx;
		}
	}
</style>