<template>
	<view style="padding: 30rpx 20rpx;">
		<view class="flex_b">
			<view class="typeSty" @click="openSift">筛选<text class="iconfont icon-xiajiantou font-26"></text></view>
			<view class="typeSty" @click="openDownload">下载账单<text class="iconfont icon-xiazai font-26"></text></view>
		</view>
		<view class="modelBox " v-for="(item,i) in 3" :key="i" @click="openShow(i)">
			<view class="flex_b">
				<view class="">
					<view class="font-44 font-weight f-tA m-b-8">07-03</view>
					<view class="flex">
						<view class="font-26 f-tA"><text class="grey m-r-10">收款总金额</text>¥0.00</view>
						<view class="font-26 f-tA m-l-30"><text class="grey m-r-10">收款笔数</text>0</view>
					</view>
				</view>
				<view v-if="show&&indexId==i+1" class="iconfont icon-shangjiantou grey font-30"></view>
				<view v-else class="iconfont icon-xiajiantou1 grey font-30"></view>
			</view>
			<view class="showBoxSty color-base-border-top m-t-40 p-t-40" v-if="show&&indexId==i+1">
				<view class="flex">
					<view class="font-26 f-tA w50"><text class="grey m-r-10">应到账金额</text>¥0.00</view>
					<view class="font-26 f-tA w50"><text class="grey m-r-10">手续费</text>0</view>
				</view>
				<view class="flex m-t-20">
					<view class="font-26 f-tA w50"><text class="grey m-r-10">退款金额</text>¥0.00</view>
					<view class="font-26 f-tA w50"><text class="grey m-r-10">折扣优惠</text>0</view>
				</view>
				<view class="detaBox">
					<block>
						<view v-for="(item,index) in 3" :key="index" class="flex_b"
							:class="index!=0?'color-base-border-top p-t-20 m-t-20':''"
							@click="$util.redirectTo('/otherpages/billDetail/billDetail')">
							<view class="flex">
								<view v-if="index==1" class="iconfont icon-weixinzhifu2 font-60"
									style="color: #00c800;"></view>
								<view v-else class="iconfont icon-zhifubao font-60" style="color: #06b4fd;"></view>

								<view class="m-l-20">
									<view class="font-30 font-weight f-tA m-b-8">15:23:46 支付</view>
									<view class="flex tabsSty">
										<view class="f-tA">实时到账</view>
										<view class="f-t">线下买单</view>
									</view>
								</view>
							</view>
							<view class="flex">
								<view class="font-32 f-tA font-weight">+10</view>
								<view class="iconfont icon-yousanjiao grey lh-32"></view>
							</view>
						</view>
					</block>

				</view>
			</view>
		</view>


		<uv-popup ref="popup" round="10" bgColor="#f6f7fb">
			<view class="popupBoxCom">
				<view class="p-40 p-b-30">
					<view class="font-32 font-weight f-tA m-b-20">收款平台</view>
					<view class="selectTimeSty flex_b">
						<view class="lh-28 font-28 f-tA">2024-07-03 00:00 至 2024-09-03 00:00</view>
						<view class="lh-28 iconfont icon-yousanjiao"></view>
					</view>
				</view>

				<view class="p-40 p-b-30">
					<view class="font-32 font-weight f-tA">收款平台</view>
					<view class="timeList flex" style="flex-wrap: wrap;">
						<view v-for="(item,index) in payPlatform" :key="index" @click="selectPlatform(item.id)"
							:class="activePlatform==item.id?'activeTime':''">
							{{item.title}}
						</view>
					</view>
				</view>
				<view class="p-40 p-b-30">
					<view class="font-32 font-weight f-tA">消费类型</view>
					<view class="timeList flex" style="flex-wrap: wrap;">
						<view v-for="(item,index) in payType" :key="index" @click="selectType(item.id)"
							:class="activeType==item.id?'activeTime':''">
							{{item.title}}
						</view>
					</view>
				</view>
				<view class="p-40 p-b-30">
					<view class="font-32 font-weight f-tA">消费类型</view>
					<view class="timeList flex" style="flex-wrap: wrap;">
						<view v-for="(item,index) in identity" :key="index" @click="selectIdentity(item.id)"
							:class="activeIdentity==item.id?'activeTime':''">
							{{item.title}}
						</view>
					</view>
				</view>

				<view class="p-30">
					<button type="primary" @click="submitTime">确定</button>
				</view>
			</view>
		</uv-popup>

		<uv-popup ref="popupDownload" round="10" :safeAreaInsetBottom="false">
			<view class="popupDownloadSty">
				<view class="textC font-36 f-tA lh-120 color-base-border-bottom">已开始下载账单</view>
				<view class="w-450 textC font-30 grey" style="margin: 40rpx auto;">请前往下载中心获取表格链接</view>
				<view class="flex_a" style="margin: 0 auto;">
					<view class="btnA font-32 graycolor" @click="closeFn()">关闭</view>
					<view class="btnB font-32 white">前往下载</view>
				</view>
			</view>
		</uv-popup>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				show: false,
				indexId: 0,
				activePlatform: 1,
				activeType: 1,
				activeIdentity: 1,
				payPlatform: [{
					title: '全部',
					id: '1'
				}, {
					title: '支付宝',
					id: '2'
				}, {
					title: '微信',
					id: '3'
				}],
				identity: [{
					title: '全部',
					id: '1'
				}, {
					title: '店主',
					id: '2'
				}, ],
				payType: [{
					title: '全部',
					id: '1'
				}, {
					title: '扫码买单',
					id: '2'
				}, {
					title: '折扣买单',
					id: '3'
				}, {
					title: '团购',
					id: '4'
				}],

			};
		},
		methods: {
			closeFn() {
				this.$refs.popupDownload.close();
			},
			openSift() {
				this.$refs.popup.open('bottom');
			},
			openDownload() {
				this.$refs.popupDownload.open('conter');
			},


			openShow(i) {
				this.indexId = i + 1
				this.show = !this.show
			},
			selectPlatform(id) {
				this.activePlatform = id
			},
			selectType(id) {
				this.activeType = id
			},
			selectIdentity(id) {
				this.activeIdentity = id
			},
		}
	}
</script>

<style lang="scss">
	.btnA {
		width: 255rpx;
		height: 82rpx;
		background-color: #f3f4f7;
		border-radius: 80rpx;
		text-align: center;
		line-height: 82rpx;
	}

	.btnB {
		width: 255rpx;
		height: 82rpx;
		background-color: #ff4d1a;
		border-radius: 80rpx;
		text-align: center;
		line-height: 82rpx;
	}

	.popupDownloadSty {
		padding: 0 30rpx;
		box-sizing: border-box;
		width: 608rpx;
		height: 388rpx;
		border-radius: 10rpx;
	}

	.selectTimeSty {
		height: 98rpx;
		border-radius: 20rpx;
		background-color: #f6f7fb;
		margin: 0 auto;
		padding: 0 30rpx;
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

	.popupBoxCom {
		width: 750rpx;
		// height: 718rpx;
		border-radius: 40rpx 40rpx 0rpx 0rpx;
		background-color: #fff;
	}

	.detaBox {
		margin-top: 30rpx;
		border-radius: 20rpx;
		background-color: #f6f7fb;
		padding: 20rpx 30rpx;

		.tabsSty {
			view {
				width: 107rpx;
				height: 35rpx;
				border-radius: 5rpx;
				background-color: #fff;
				text-align: center;
				line-height: 35rpx;
				margin-right: 20rpx;
			}
		}
	}

	.w50 {
		width: 50%;
	}

	.typeSty {
		padding: 0 20rpx;
		height: 57rpx;
		text-align: center;
		background-color: #fff;
		border-radius: 10rpx;
		line-height: 57rpx;
		font-size: 26rpx;
	}
</style>