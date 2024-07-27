<template>
	<view>
		<view class="setHeadBg">
			<view class="white font-56 textC font-weight">我要开店</view>
			<view class="white font-28 textC">请按照步骤完成开店</view>
			<view class="stepSty flex_b">
				<block v-for="(item,i) in arrs" :key="i">
					<view class="stepItem">
						<view :class="presentId>=(i+1)?'white':'colorSty'" class="font-44 font-weight textC ">{{i+1}}
						</view>
						<view :class="presentId>=(i+1)?'white':'colorSty'" class="font-26 textC">{{item}}</view>
					</view>
					<view :class="presentId>(i+1)?'xuXianA':'xuXian'" v-if="i!=3"></view>
				</block>
			</view>
			<view class="bodySty" v-if="presentId==1">
				<view class="iptSty flex">
					<input type="tel" placeholder="请输入地推手机号" maxlength="11" />
				</view>
				<view class="font-26 grey textC">为更好的服务您，请绑定地推</view>
				<view class="p-50">
					<button type="primary" @click="submitA">确认绑定</button>
				</view>
			</view>

			<block v-if="presentId==2">
				<view class="bodySty" v-if="false">
					<view class="flex_b ApTypeSty">
						<view :class="activeB==i?'activeB':''" @click="selsectB(i)" v-for="(item,i) in arrsAp">{{item}}
						</view>
					</view>
					<view class="p-50">
						<view class="f-tA font-32 font-weight">1.认证资料</view>
						<view class="font-26 grey m-l-26">法人身份证正/反面照片</view>
						<view class="font-26 grey m-l-26">法人银行卡照片</view>
						<view class="font-26 grey m-l-26">店铺门头照/内景照/店主合影</view>
					</view>
				</view>
				<view class="UnderReview bodySty" v-else>
					<image src="/static/image/shz.png" mode="aspectFill"></image>
					<view class="font-32 font-weight f-tA m-t-40">资质审核中</view>
					<view class="grey font-26 m-t-16">请尽快完成合同签署</view>
					<view class="p-50">
						<button type="primary"
							@click="$util.redirectTo('/otherpages/immediatelySign/immediatelySign')">签署合同</button>
					</view>
				</view>
			</block>

			<block v-if="presentId==3">
				<view class="" class="bodySty">
					<view class="certifyItem flex_b">
						<view class="flex">
							<view class="iconfont icon-weixinzhifu "></view>
							<view class="">
								<view class="font-36 f-tA font-weight flex">微信待认证
								<view class="successSty">已认证</view>
								</view>
								<view class="graycolor font-28 w-300 m-t-10">完成签约认证，即可开通微信收款服务</view>
							</view>
						</view>
						<view class="iconfont icon-youjiantou"></view>
					</view>
					<view class="certifyItem flex_b">
						<view class="flex">
							<view class="iconfont icon-zhifubao"></view>
							<view class="">
								<view class="font-36 f-tA font-weight flex">支付宝待认证
								<view class="successSty">已认证</view>
								</view>
								<view class="graycolor font-28 w-300 m-t-10">完成签约认证，即可开通支付宝收款服务</view>
							</view>
						</view>
						<view class="iconfont icon-youjiantou"></view>
					</view>
				</view>
			</block>

		</view>



		<view class="bomPosition" v-if="presentId==2&&false">
			<view class="p-50">
				<button type="primary" @click="submitB">提交资料</button>
			</view>
		</view>
		<view class="bomPosition" v-else>
			<button class="noneBtn f-t font-28 font-weight" open-type="contact" style="width: 100%;">联系客服</button>
			<view class="bottom-mar"></view>
		</view>


	</view>
</template>

<script>
	export default {
		data() {
			return {
				arrs: ['绑定地推', '资质认证', '开通线下收款', '开通线上收款'],
				arrsAp: ['个人', '个体工商户', '企业'],
				presentId: 3, //代表第几步
				activeB: 0
			};
		},
		methods: {
			// 步骤1的提交
			submitA() {
				this.presentId = 2
			},
			// 步骤2的切换分类
			selsectB(i) {
				this.activeB = i
			},
			// 步骤2的提交
			submitB() {
				this.$util.redirectTo('/otherpages/shopFrom/shopFrom')
			}
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #fff;
	}
	.successSty {
		width: 108rpx;
		height: 44rpx;
		flex-shrink: 0;
		border-radius: 10rpx;
		background: #02D800;
		text-align: center;
		line-height: 44rpx;
		color: #fff;
		margin-left: 20rpx;
	}
	.certifyItem {
		width: 621rpx;
		height: 198rpx;
		border: 1rpx solid #dddfe4;
		margin: 0 auto;
		border-radius: 20rpx;
		padding: 0 30rpx;
		margin-bottom: 30rpx;

		.icon-weixinzhifu {
			color: #09bb07;
			font-size: 80rpx;
			width: 100rpx;
		}

		.icon-zhifubao {
			color: #06b4fd;
			font-size: 70rpx;
			width: 100rpx;
		}
	}

	.UnderReview {
		text-align: center;

		image {
			text-align: center;
			padding-top: 100rpx;
			width: 112.06rpx;
			height: 141.07rpx;
		}
	}

	.ApTypeSty {
		padding: 0 20rpx;

		view {
			width: 223rpx;
			height: 89rpx;
			border-radius: 100rpx;
			background-color: #f0f1f4;
			text-align: center;
			line-height: 89rpx;
			color: #1c274c;
			text-align: center;
			font-family: "Alibaba PuHuiTi 2.0";
			font-size: 28rpx;
		}

		.activeB {
			background-color: #ff4d1a !important;
			color: #fff !important;
		}
	}

	.colorSty {
		color: #ffc9ba;
	}

	.iptSty {
		width: 600rpx;
		height: 114rpx;
		flex-shrink: 0;
		border-radius: 20rpx;
		background-color: #f6f7fb;
		margin: 0 auto;
		margin-bottom: 15rpx;
		padding: 0 25rpx;

		input {
			width: 100%;
		}
	}

	.bodySty {
		width: 750rpx;
		opacity: 1;
		background: #ffffff;
		border-radius: 30rpx 30rpx 0 0;
		// background-color: rebeccapurple;
		margin-top: -60rpx;
		padding-top: 50rpx;
		padding-bottom: 30rpx;
	}

	.stepSty {
		margin-top: 50rpx;
		width: 750rpx;
		height: 240rpx;
		border-radius: 30rpx 30rpx 0 0;
		opacity: 1;
		border: 1rpx solid #ffffff;
		background: #ff9374;
		padding: 0 0rpx;
		padding-right: 20rpx;
		padding-bottom: 60rpx;
		box-sizing: border-box;

		.stepItem {
			width: 166rpx;
		}

		.xuXian {
			width: 30rpx;
			height: 0;
			border-top: 2rpx dashed #ffae9e;
		}

		.xuXianA {
			width: 30rpx;
			height: 0;
			border-top: 2rpx dashed #fff;
		}
	}

	.setHeadBg {
		height: 405rpx;
		width: 100%;
		padding-top: 50rpx;
		box-sizing: border-box;
		background-image: linear-gradient(to top, #ff9374, #ff9374, #ff4d19);
	}
</style>