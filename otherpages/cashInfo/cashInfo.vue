<template>
	<view>
		<view class="container">
			<view class="font-32 font-weight-6 p-t-32 p-l-32">结算信息</view>
			<view class="p-28 acea-row row-middle">
				<view class="font">结算卡类型</view>
				<view class="acea-row row-middle">
					<label class="acea-row row-middle" @click="form.card_type=0">
						<radio :value="0" :checked="form.card_type==0" /><text class="font-32">对公</text>
					</label>
					<label class="acea-row row-middle m-r-20 m-l-20" @click="form.card_type=1">
						<radio :value="1" :checked="form.card_type==1" /><text class="font-32">对私</text>
					</label>
					<label class="acea-row row-middle" @click="form.card_type=2">
						<radio :value="1" :checked="form.card_type==2" /><text class="font-32">对私非法人</text>
					</label>
				</view>
			</view>
			<!-- <uv-input placeholder="请输入真实姓名" border="none" v-model='form.card_name' :customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">卡户名</view>
				</template>
			</uv-input> -->
			<uv-input placeholder="请输入结算卡卡号" border="none" v-model='form.card_no' :customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">结算卡卡号</view>
				</template>
			</uv-input>
			<uv-input placeholder="请输入持卡人姓名" border="none" v-model='form.card_name' :customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">持卡人姓名</view>
				</template>
			</uv-input>
			<template v-if="form.card_type==0">
				<view class="" @click="open3()">
					<uv-input placeholder="请选择银行" readonly v-model="bankname" border="none" :customStyle="customStyle">
						<template v-slot:prefix>
							<view class="font">银行</view>
						</template>
					</uv-input>
				</view>
				<view class="" @click="open4()">
					<uv-input placeholder="请选择支行" readonly v-model="bankname1" border="none" :customStyle="customStyle">
						<template v-slot:prefix>
							<view class="font">支行</view>
						</template>
					</uv-input>
				</view>
			</template>
			<view class="" @click="open1(1)">
				<uv-input placeholder="请选择省份" readonly v-model="provname" border="none" :customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">银行所在省</view>
					</template>
				</uv-input>
			</view>
			<view class="" @click="open2(1)">
				<uv-input placeholder="请选择所在市" readonly v-model="cname" border="none" :customStyle="customStyle">
					<template v-slot:prefix>
						<view class="font">银行所在市</view>
					</template>
				</uv-input>
			</view>
			<template v-if="form.card_type!=0">
				<uv-input placeholder="请输入持卡人身份证号码" border="none" v-model='form.cert_no' :customStyle="customStyle">
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
			</template>
		</view>
		<uv-datetime-picker ref="datetimePicker" v-model="value" mode="date" @confirm="confirm">
		</uv-datetime-picker>
		<uv-picker ref="picker" keyName='name' :columns="[plist]" @confirm="confirm1"></uv-picker>
		<uv-picker ref="picker1" keyName='name' :columns="[clist]" @confirm="confirm2"></uv-picker>
		<uv-picker ref="bank" keyName='name' :columns="[blist]" @confirm="confirm3"></uv-picker>
		<uv-popup ref="popup" :round='10' :closeable="true">
			<view class="zhihangs p-24">
				<view class="acea-row row-middle m-b-20">
					<text class="font-28">支行名称：</text>
					<input type="text" class='input1' placeholder="请输入支行名称" v-model="search" />
					<view class="btn1" @click="branchCode">筛选</view>
				</view>
				<scroll-view scroll-y="true" class="a1" @scrolltolower='scrolltolower'>
					<zb-table :data="zlist" @edit="edit" :columns="column"></zb-table>
				</scroll-view>
			</view>
		</uv-popup>
		<view class="h-300"></view>
		<view class="btms iphone-x">
			<view class="" @click="submit">确定</view>
		</view>
	</view>
</template>

<script>
	import {
		arealists,
		openCard,
		bankCode,
		branchCode
	} from "@/common/js/api.js"
	import {
		mobile
	} from '@/uni_modules/uv-ui-tools/libs/function/test.js';
	import {
		timeFormat
	} from '@/uni_modules/uv-ui-tools/libs/function/index.js';
	import validate from 'common/js/validate.js';
	export default {
		data() {
			return {
				list: [],
				blist: [],
				zlist: [],
				column: [{
						name: 'serial_name',
						label: '支行名称',
						width: 160,
						fixed:true
					},
					{
						name: 'serial_number',
						label: '联行号',
						width: 80
					},
					{
						name: 'bank_name',
						label: '银行名称',
						width: 70
					},
					{
						name: 'operation',
						type: 'operation',
						label: '操作',
						width: 60,
						renders: [{
							name: '确认',
							func: 'edit',
							class: "btn" // func 代表子元素点击的事件 父元素接收的事件 父元素 @edit
						}, ]
					}
				],
				check: 0,
				customStyle: "padding:28rpx",
				form: {
					card_type: 0,
					prov_id: "",
					area_id: "",
					bank_code: "",
					card_no: '',
					branch_code: "",
					cert_no: "",
					cert_validity_type: 0,
					cert_begin_date: "",
					cert_end_date: "",
					card_name: ""
				},
				isend: false,
				codetxt: "60s后重新获取",
				times: 60,
				value: Number(new Date()),
				type: 1,
				plist: [],
				clist: [],
				provname: "",
				cname: "",
				bankname: '',
				bankname1: '',
				page: 1,
				search: '',
				last_page:1
			}
		},
		onLoad() {
			this.arealists()
			this.bankCode()
		},
		methods: {
			scrolltolower(){
				this.page++
				this.branchCode()
			},
			edit(e) {
				console.log(e);
				this.form.branch_code=e.serial_number
				this.bankname1=e.serial_name
				this.$refs.popup.close()
			},
			open4() {
				this.$refs.popup.open('bottom')
			},
			branchCode() {
				let params = {
					bank_num: this.form.bank_code,
					page: this.page,
					page_size: 30,
					serial_name: this.search
				}
				branchCode(params).then(res => {
					this.zlist = this.page>1?this.zlist.concat(...res.data.list):res.data.list
					this.last_page=res.data.page_count
				})
			},
			confirm3(e) {
				this.form.bank_code = e.value[0].code
				this.bankname = e.value[0].name
				this.branchCode()
			},
			bankCode() {
				bankCode().then(res => {
					this.blist = res.data
				})
			},
			open3() {
				this.$refs.bank.open()
			},
			confirm1(e) {
				this.form.prov_id = e.value[0].id
				this.provname = e.value[0].name
				this.form.area_id = ''
				this.cname = ''
				this.$refs.picker1.setIndexs([0], true)
				this.getarea1()
			},
			confirm2(e) {
				this.form.area_id = e.value[0].id
				this.cname = e.value[0].name
			},
			open1(e) {
				this.$refs.picker.open()
			},
			open2() {
				if (!this.form.prov_id) {
					uni.showToast({
						title: '请先选择省份',
						icon: 'none'
					})
					return
				}
				this.$refs.picker1.open()
			},
			getarea1() {
				let params = {
					pid: this.form.prov_id
				}
				arealists(params).then(res => {
					this.clist = res.data
				})
			},
			arealists() {
				arealists().then(res => {
					this.plist = res.data
				})
			},
			confirm(e) {
				if (this.type == 1) {
					this.form.cert_begin_date = timeFormat(e.value, 'yyyy-mm-dd')
				} else {
					this.form.cert_end_date = timeFormat(e.value, 'yyyy-mm-dd')
				}
			},
			open(e) {
				this.type = e
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
						name: 'card_no',
						checkType: 'required',
						errorMsg: '请输入结算卡卡号'
					}, {
						name: 'cert_no',
						checkType: 'required',
						errorMsg: '请输入个人证件号码'
					},
					{
						name: 'mobile_no',
						checkType: 'required',
						errorMsg: '请输入手机号'
					},
					{
						name: 'mobile_no',
						checkType: 'phoneno',
						errorMsg: '请输入正确的手机号'
					},
					{
						name: 'cert_begin_date',
						checkType: 'required',
						errorMsg: '请选择开始时间'
					},
					{
						name: 'cert_end_date',
						checkType: 'required',
						errorMsg: '请选择结束时间'
					},
					{
						name: 'card_name',
						checkType: 'required',
						errorMsg: '请输入持卡人姓名'
					},
					{
						name: 'card_no',
						checkType: 'required',
						errorMsg: '请输入银行卡号'
					},
					{
						name: 'prov_id',
						checkType: 'required',
						errorMsg: '请选择省份'
					},
					{
						name: 'area_id',
						checkType: 'required',
						errorMsg: '请选择所在市'
					}
				];
				// var checkRes = validate.check(this.form, rule);
				// if (!checkRes) {
				// 	this.$util.showToast({
				// 		title: validate.error
				// 	});
				// 	return false;
				// }

				openCard(this.form).then(res => {
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
	.btn1{
		background: #FF6A00;
		border-radius: 80rpx;
		padding: 20rpx 30rpx;
		color: #fff;
		font-size: 28rpx;
		margin-left: 20rpx;
	}
	.input1{
		margin: 0 20rpx;
		border: 1rpx solid #999;
		padding: 10rpx 20rpx;
	}
	.btn {
		background: #ffffff00 !important;
		color: #FF6A00 !important;
		padding: 0 !important;
	}

	.zhihangs {
		.a1 {
			max-height: 80vh;
		}
	}

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
			width: 140rpx;
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