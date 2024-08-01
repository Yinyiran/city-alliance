<template>
	<view class="p-30">
		<view class="font-32 font-weight f-tA">请根据您的经营场景开通收款服务</view>
		<view class="modelBox" v-for="(item,i) in list">
			<view class="flex color-base-border-bottom p-b-20" style="width: 100%;">
				<block v-if="item.title=='微信'">
					<image src="../../static/home/wx1.png" v-if="item.type=='up'" mode="widthFix" class="payLogo">
					</image>
					<image src="../../static/home/wx2.png" v-else mode="widthFix" class="payLogo"></image>
				</block>
				<block v-else>
					<image src="../../static/home/zfb1.png" v-if="item.type=='up'" mode="widthFix" class="payLogo">
					</image>
					<image src="../../static/home/zfb2.png" v-else mode="widthFix" class="payLogo"></image>
				</block>
				<view class="font-28 font-weight f-tA m-l-16">{{item.title}}线下收款账户</view>
			</view>

			<!-- 已通过 -->
			<block v-if="item.success">
				<view class="flex m-t-30">
					<view class="font-28 grey w-160">银行卡账户</view>
					<view class="font-28 f-tA">中信银行 ****0392</view>
				</view>
				<view class="flex m-t-20">
					<view class="font-28 grey w-160">收款额度</view>
					<view class="font-28 f-tA">单笔2000；单日1万；单月30万</view>
				</view>
				<view class="flex m-t-20 color-base-border-bottom p-b-30">
					<view class="font-28 grey w-160">到账时间</view>
					<view class="font-28 f-tA">实时到账<text class="iconfont icon-bangzhu grey m-l-10 lh-28"></text> </view>
				</view>
				<view class="flex_b m-t-20">
					<view class="flex" @click="$util.redirectTo('/otherpages/transferRecord/transferRecord')">
						<view class="icon-lianxi2hebing_jilu iconfont"></view>
						<view class="font-26 f-tA m-l-4">到账记录</view>
					</view>
					<view class="flex" @click="selectTime">
						<view class="icon-shijianfang iconfont"></view>
						<view class="font-26 f-tA m-l-4">到账时间</view>
					</view>
					<view class="flex" @click="$util.redirectTo('/otherpages/editPayAccount/editPayAccount')">
						<view class="icon-qianbao iconfont"></view>
						<view class="font-26 f-tA m-l-4">修改账户</view>
					</view>
					<view class="flex">
						<view class="icon-zhuanzhang iconfont"></view>
						<view class="font-26 f-tA m-l-4">收款额度</view>
					</view>
				</view>
			</block>

			<!-- 没有资质、未通过 (待认证，审核中)-->
			<block v-else>
				<view class="font-32 font-weight textC f-tA m-t-30">资质审核中</view>
				<view class="font-24 grey textC m-b-40 m-t-20">按要求埴写店铺资料、营业执照、收款账户等资料</view>
				<view class="w-215" style="margin: 0 auto;">
					<button @click="openPop">签署入驻合同</button>
				</view>
			</block>
		</view>

		<uv-popup ref="popup" round="10" :safeAreaInsetBottom="false">
			<view class="popupBox">
				<view class="textC font-36 f-tA lh-120 color-base-border-bottom">请签署入驻合同</view>
				<view class="w-450 textC font-30 grey" style="margin: 40rpx auto;">请复制合同链接在微信或浏览器打开，并签署入驻合同。
					http://s.vv1i.cn/P7F7MR</view>
				<view class="w-255" style="margin: 0 auto;">
					<button>复制链接</button>
				</view>
			</view>
		</uv-popup>
		
		<uv-popup ref="popupB" round="10" :safeAreaInsetBottom="false">
			<view class="popupBox">
				<view class="textC font-36 f-tA lh-120 color-base-border-bottom">实时到账的额度限制</view>
				<view class="w-450 textC font-30 grey" style="margin: 40rpx auto;">
					单笔交易金额 >30000元 次日到账 商家每日总额度>50000元
					次日到账
				</view>
				<view class="w-255" style="margin: 0 auto;">
					<button>知道了</button>
				</view>
			</view>
		</uv-popup>

		<uv-picker ref="picker" title="选择到账时间" confirmColor="#ff4d1a" :columns="columns" @confirm="confirm"></uv-picker>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				columns: [
					['次日5~7点到账', '实时到账']
				],
				list: [{
					title: '微信',
					type: 'up',
					success: true
				}, {
					title: '支付宝',
					type: 'up',
					success: true
				}, {
					title: '微信',
					type: 'under',
					success: false
				}, {
					title: '支付宝',
					type: 'under',
					success: false
				}]
			};
		},
		methods: {
			confirm(e) {
				console.log(e, 'eee+++--');
			},
			openPop() {
				this.$refs.popup.open('conter');
			},
			selectTime() {
				this.$refs.picker.open();
			}
		}
	}
</script>

<style lang="scss">
	.popupBox {
		padding: 0 30rpx;
		box-sizing: border-box;
		width: 608rpx;
		height: 468rpx;
		border-radius: 10rpx;
	}

	.payLogo {
		width: 44rpx;
		height: 44rpx;
	}

	.logoR {
		position: relative;

		.otherSty {
			width: 44rpx;
			text-align: center;
			font-size: 18rpx;
			border-radius: 4rpx;
			color: #fff;
			background-color: #ff9900;
			position: absolute;
			bottom: 0;
			left: 0;

		}
	}
</style>