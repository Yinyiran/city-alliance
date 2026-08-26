<template>
	<view>


		<view class="container">
			<uv-input placeholder="请填写正确的ID" border="none" v-model='form.empid' :customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">会员ID</view>
				</template>
			</uv-input>
			<uv-input placeholder="请填写姓名" v-model="form.username" border="none" :customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">姓名</view>
				</template>
			</uv-input>
			<uv-input placeholder="请填写手机号码" type="number" v-model="form.mobile" maxlength="11" border="none"
				:customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">手机号</view>
				</template>
			</uv-input>
			<uv-input placeholder="请输入短信验证码" v-model="form.code" border="none" :customStyle="customStyle">
				<template v-slot:prefix>
					<view class="font">验证码</view>
				</template>
				<template v-slot:suffix>
					<view class="getcode" v-if="!isend" @click="getcode">获取验证码</view>
					<view class="getcode" style="color: #CCCCCC;" v-else>{{codetxt}}</view>
				</template>
			</uv-input>
		</view>
		<view class="h-300"></view>
		<view class="btms iphone-x">
			<view class="" @click="submit">确认修改</view>
		</view>
	</view>
</template>

<script>
	import {
		employeeAdd,
		mobliecode
	} from "@/common/js/api.js"
	import {
		mobile
	} from '@/uni_modules/uv-ui-tools/libs/function/test.js';
	import validate from '@/common/js/validate.js';
	export default {
		data() {
			return {
				list: [],
				check: 0,
				customStyle: "padding:28rpx",
				form: {
					empid: '',
					username: '',
					mobile: ''
				},
				isend: false,
				codetxt: "60s后重新获取",
				times: 60,

			}
		},
		onLoad() {},
		methods: {
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
					mobliecode(params).then(res => {
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
						name: 'empid',
						checkType: 'required',
						errorMsg: '请输入会员ID'
					}, {
						name: 'username',
						checkType: 'required',
						errorMsg: '请输入姓名'
					},
					{
						name: 'mobile',
						checkType: 'required',
						errorMsg: '请输入手机号'
					},
					{
						name: 'mobile',
						checkType: 'phoneno',
						errorMsg: '请输入正确的手机号'
					},
					{
						name: 'code',
						checkType: 'required',
						errorMsg: '请输入验证码'
					}
				];
				var checkRes = validate.check(this.form, rule);
				if (!checkRes) {
					this.$util.showToast({
						title: validate.error
					});
					return false;
				}
				let params = {
					...this.form
				}
				employeeAdd(params).then(res => {
					if (res.code == 0) {
						uni.showToast({
							title: '添加成功'
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
			font-size: 32rpx;
			color: #222222;
			width: 128rpx;
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