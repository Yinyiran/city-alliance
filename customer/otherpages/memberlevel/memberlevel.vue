<template>
	<view>
		<view class="part">
			<view class="acea-row row-middle">
				<image src="/static/avatar.png" class="w-74 h-74 bor-74" v-if="!infos.headimg" mode=""></image>
				<image :src="$util.img(infos.headimg)" class="w-74 h-74 bor-74" v-else mode=""></image>
				<view class="m-l-16">
					<view class="font-32">{{ infos.nickname }}</view>
					<view class="font-26">恭喜您已是{{ infos.member_level_name }}</view>
				</view>
			</view>
			<view class="">
				<image src="/static/ob.png" v-if="infos.member_level == 1" mode=""></image>
				<image src="/static/ob1.png" v-if="infos.member_level == 2" mode=""></image>
				<image src="/static/ob2.png" v-if="infos.member_level == 3" mode=""></image>
				<image src="/static/ob3.png" v-else-if="infos.member_level == 4" mode=""></image>
				<image src="/otherpages/static/lines.png" mode=""></image>
				<view class="relative font-40 font-weight-6" :class="{ fcolor: infos.member_level == 4 }">
					{{ infos.member_level_name }}
				</view>
				<view class="font-weight-6 m-t-12 relative " :class="{ fcolor: infos.member_level == 4 }"><text
						class="font-weight-4 m-r-6 relative" :class="{ fcolor: infos.member_level == 4 }">积分</text>{{ infos.point }}
				</view>
				<view class="w-360 m-t-8">
					<!-- <uv-line-progress :percentage="80" :showText='false' activeColor="#FFFFFF"
						inactiveColor="rgba(255, 255, 255, 0.16)" height='8rpx'></uv-line-progress> -->
				</view>
				<!-- <view class="acea-row row-middle relative m-t-12" :class="{fcolor:infos.member_level==4}">
					当前可享超多会员权益
					<uv-icon name="arrow-right" :color="true?'#FAD5B4':'#fff'" size="12"></uv-icon>
				</view> -->
			</view>
		</view>
		<view class="">
			<scroll-view scroll-x="true" class="scrools" scroll-left="30">
				<view v-for="(item, index) in levels" class="items" :class="{ on: index == check }" @click="check = index"
					:key="index">
					<view class="">
						<view class="acea-row row-middle">
							<image src="/static/vip1.png" v-if="item.level_id == 2" class="w-32 h-32" mode=""></image>
							<image src="/static/vip2.png" v-else-if="item.level_id == 3" class="w-32 h-32" mode="">
							</image>
							<image src="/static/vip3.png" v-else-if="item.level_id == 4" class="w-32 h-32" mode="">
							</image>
							{{ item.level_name }}
						</view>
						<view class="m-t-24">仅需<text>{{ item.price }}</text>元</view>
						<view class="">享{{ item.consume_discount }}折优惠</view>
					</view>
					<view class="">开通即送<text>{{ item.fan_point }}</text>积分</view>
				</view>
			</scroll-view>
		</view>
		<view class="part2">
			<view class="acea-row row-middle row-center m-b-32">
				<image src="/otherpages/static/hb5.png" class="w-40 h-40" mode=""></image>
				<text class="m-r-32 m-l-32">{{ levels[check] ? levels[check].level_name : '' }}专属特权</text>
				<image src="/otherpages/static/hb1.png" class="w-40 h-40" mode=""></image>
			</view>
			<view class="rights-grid">
				<view class="right-item">
					<image src="/otherpages/static/img3.png" class="w-80 h-80" mode=""></image>
					<view class="right-title">{{ rightsDetail.discountText }}</view>
					<view class="right-sub">消费折上折</view>
				</view>
				<view class="right-item">
					<image src="/otherpages/static/img4.png" class="w-80 h-80" mode=""></image>
					<view class="right-title">{{ rightsDetail.pointGift }}</view>
					<view class="right-sub">开通赠送</view>
				</view>
				<view class="right-item">
					<image src="/otherpages/static/img5.png" class="w-80 h-80" mode=""></image>
					<view class="right-title">{{ rightsDetail.pointRatio }}</view>
					<view class="right-sub">消费积分比例</view>
				</view>
				<view class="right-item">
					<image src="/otherpages/static/img6.png" class="w-80 h-80" mode=""></image>
					<view class="right-title">{{ rightsDetail.commissionRate }}</view>
					<view class="right-sub">消费佣金比例</view>
				</view>
			</view>
			<view class="invite-badge acea-row row-middle m-t-24">
				<text class="invite-tag">邀约特权</text>
				<text class="invite-desc">{{ rightsDetail.inviteReward }}</text>
			</view>
		</view>
		<view class="acea-row row-middle rulbtn" @click="open">
			查看会员专属规则
			<uv-icon name="arrow-right" color="#fff" size="16"></uv-icon>
		</view>
		<view class="h-68"></view>
		<view class="btn3" v-if="levels[check] && levels[check].level_id > (infos.member_level || 1)">
			<image src="/otherpages/static/btn3.png" @click="sbumit" mode=""></image>
		</view>
		<view class="already-tip" v-else>
			您当前已享有此等级权益
		</view>
		<uv-popup ref="popup" bg-color="#ffffff00">
			<view class="mains">
				<view class="acea-row row-middle row-center p-t-48 m-b-20">
					<image src="/otherpages/static/llline1.png" class="w-52 h-4" mode=""></image>
					<text>会员专属规则介绍</text>
					<image src="/otherpages/static/rline1.png" class="w-52 h-4" mode=""></image>
				</view>
				<view class="rule-content-box p-l-40 p-r-40 font-26" style="color: #666; line-height: 1.8;">
					<view class="rule-sec m-b-20">
						<view class="font-weight-6" style="color: #333;">【会员权益】</view>
						<view>1. 普通会员：享1:1消费积分，邀约办理钻石奖励10%、黑钻15%、合伙人20%。</view>
						<view>2. 钻石会员：享98折，赠1000积分，消费积分1:1.2，消费佣金0.2%，邀约办理钻石奖励15%、黑钻20%、合伙人25%。</view>
						<view>3. 黑钻会员：享96折，赠3000积分，消费积分1:1.3，消费佣金0.3%，邀约办理钻石奖励20%、黑钻25%、合伙人30%。</view>
						<view>4. 合伙人：享95折，赠10000积分，消费积分1:1.5，消费佣金0.5%，邀约办理钻石奖励25%、黑钻30%、合伙人35%。</view>
					</view>
					<view class="rule-sec m-b-20">
						<view class="font-weight-6" style="color: #333;">【奖励解锁与提现要求】</view>
						<view>• 下级办理会员后7天无理由期结束 + 在平台产生自消费后，邀约奖励方可解冻使用在平台内消费。</view>
						<view>• 提现门槛：单笔提现最低金额为 100 元。</view>
					</view>
					<view class="rule-sec m-b-20">
						<view class="font-weight-6" style="color: #333;">【提现个税与手续费】</view>
						<view>• 平台提现手续费统一为 0.35%。</view>
						<view>• 依据税法代扣代缴劳务报酬个税：单次及当月800元以内免税；超过800元按80%计税基数依法预扣预缴（20%~40%累进税率）。</view>
					</view>
				</view>
				<view class="closes" @click="closeds">
					<uv-icon name="close" size="20" color="#777"></uv-icon>
				</view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
import {
	levelList,
	userinfo,
	leveordercreate,
	huifupay,
	memberlevelpay
} from "@/common/js/api.js"
export default {
	data() {
		return {
			infos: {},
			levels: [],
			check: 0
		};
	},
	computed: {
		rightsDetail() {
			let cur = this.levels[this.check] || {};
			let id = cur.level_id || (this.check + 1);
			let map = {
				1: {
					discountText: '正价无折扣',
					pointGift: '无赠送',
					pointRatio: '1 : 1.0',
					commissionRate: '0%',
					inviteReward: '邀约钻石奖10%，黑钻15%，合伙人20%'
				},
				2: {
					discountText: '98折优惠',
					pointGift: '1000积分',
					pointRatio: '1 : 1.2',
					commissionRate: '0.2%',
					inviteReward: '邀约钻石奖15%，黑钻20%，合伙人25%'
				},
				3: {
					discountText: '96折优惠',
					pointGift: '3000积分',
					pointRatio: '1 : 1.3',
					commissionRate: '0.3%',
					inviteReward: '邀约钻石奖20%，黑钻25%，合伙人30%'
				},
				4: {
					discountText: '95折优惠',
					pointGift: '10000积分',
					pointRatio: '1 : 1.5',
					commissionRate: '0.5%',
					inviteReward: '邀约钻石奖25%，黑钻30%，合伙人35%'
				}
			};
			return map[id] || map[1];
		}
	},
	onLoad() { },
	onShow() {
		this.levelList()
		this.userinfo()
	},
	methods: {
		closeds() {
			this.$refs.popup.close()
		},
		open() {
			this.$refs.popup.open('center')
		},
		sbumit() {
			let params = {
				website_id: uni.getStorageSync('website_id'),
				level_id: this.levels[this.check].level_id
			}
			leveordercreate(params).then(res => {
				if (res.code == 0) {
					this.pays(res.data)
				} else {
					uni.showToast({
						title: res.data.message,
						icon: 'none'
					})
				}
			})
		},
		pays(e) {
			let params = {
				out_trade_no: e,
			}
			memberlevelpay(params).then(res => {
				uni.requestPayment({
					provider: 'wxpay',
					timeStamp: res.data.timeStamp,
					nonceStr: res.data.nonceStr,
					package: res.data.package,
					signType: res.data.signType,
					paySign: res.data.paySign,
					success: resA => {
						this.userinfo()
					},
					fail: err => {
						console.log(err);
					}
				})
			})
		},
		levelList() {
			levelList({
				website_id: uni.getStorageSync('website_id')
			}).then(res => {
				this.levels = res.data
			})
		},
		userinfo() {
			userinfo().then(res => {
				this.infos = res.data
			})
		},
	}
}
</script>

<style lang="scss">
.mains {
	width: 640rpx;
	height: 940rpx;
	background: linear-gradient(180deg, #FCEEDD 0%, #FFFFFF 52%, #FFFFFF 100%);
	border-radius: 40rpx;
	border: 3rpx solid #FFFFFF;
	position: relative;

	.closes {
		position: absolute;
		top: 28rpx;
		right: 28rpx;
		z-index: 100;
		padding: 12rpx;
		display: flex;
		align-items: center;
		justify-content: center;
		line-height: 1;
	}

	>view:first-child {
		image {
			width: 52rpx;
			height: 4rpx;
		}

		text {
			font-weight: 600;
			font-size: 40rpx;
			color: #472B1D;
			margin: 0 20rpx;
		}
	}
}

.rulbtn {
	margin: 0 auto;
	width: fit-content;
	padding: 0 32rpx;
	height: 64rpx;
	background: #272A33;
	border-radius: 32rpx;
	font-weight: 400;
	font-size: 26rpx;
	color: #FFFFFF;
}

.part2 {
	margin: 64rpx 46rpx;

	>view:first-child {
		text {
			font-weight: 600;
			font-size: 36rpx;
			color: #F5B67F;
		}
	}

	>view:last-child {
		text-align: center;

		>view {
			width: 25%;

			image {
				margin-bottom: 16rpx;
			}
		}

		view {
			font-weight: 400;
			font-size: 24rpx;
			color: #FFFFFF;
		}
	}
}

.scrools {
	width: 100%;
	white-space: nowrap;
	position: relative;
	z-index: 999;

	.items {
		background: #272A33;
		width: auto;
		height: 242rpx;
		margin-right: 16rpx;
		display: inline-block;
		border-radius: 16rpx;
		overflow: hidden;

		>view:first-child {
			width: auto;
			height: 192rpx;
			background: #111317;
			border-radius: 16rpx;
			position: relative;

			>view:first-child {
				width: 176rpx;
				padding-left: 16rpx;
				font-weight: 400;
				font-size: 24rpx;
				color: #FFFFFF;
				box-sizing: border-box;
				height: 50rpx;
				background: linear-gradient(90deg, #272A33 0%, #48494B 100%);
				clip-path: polygon(0% 0%, 100% 0%, 95% 100%, 0% 100%);
				border-radius: 16rpx;

				image {
					margin-right: 8rpx;
				}
			}

			>view:nth-child(2),
			>view:nth-child(3) {
				font-weight: 600;
				margin-left: 20rpx;
				margin-right: 20rpx;
				font-size: 24rpx;
				color: #FAD5B4;

				text {
					font-size: 48rpx;
					color: #FAD5B4;
					font-weight: bold;
				}
			}
		}

		>view:last-child {
			font-weight: 400;
			font-size: 18rpx;
			color: #B2B6C2;
			padding: 0 20rpx;
			line-height: 50rpx;

			text {
				font-weight: 600;
				font-size: 24rpx;
				color: #B2B6C2;
			}
		}
	}

	.items:first-child {
		margin-left: 32rpx;
	}

	.items:last-child {
		margin-right: 32rpx;
	}

	.on {
		>view:first-child {
			background: linear-gradient(180deg, #FFE7CC 0%, #FFB48E 100%) !important;

			>view:first-child {
				color: #44687A;
				background: linear-gradient(90deg, rgba(255, 255, 255, 0.48) 0%, rgba(255, 255, 255, 0.68) 100%);
			}

			>view:nth-child(2),
			>view:nth-child(3) {
				color: #222222;

				text {
					color: #222222;
				}
			}
		}
	}
}

page {
	background: #181A1F;
}

.btn3 {
	margin: 0 50rpx;
	height: 148rpx;
	position: relative;
	z-index: 999;

	>image {
		width: 100%;
		height: 100%;
	}
}

.part {
	margin: 36rpx 32rpx 0 32rpx;
	box-sizing: border-box;
	height: 415rpx;
	position: relative;

	view,
	text {
		color: #fff;
		position: relative;
		z-index: 222;
	}

	>view:last-child {
		position: absolute;
		top: 60rpx;
		height: 360rpx;
		width: 100%;
		box-sizing: border-box;
		padding-top: 92rpx;
		padding-left: 28rpx;

		>image:first-child {
			width: 100%;
			height: 100%;
			position: absolute;
			top: 0;
			left: 0;
			z-index: -1;
		}

		>image:nth-child(2) {
			position: absolute;
			width: 100%;
			height: 350rpx;
			bottom: -220rpx;
			left: 0;
			z-index: 1;
		}
	}
}

.rights-grid {
	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;

	.right-item {
		width: 23%;
		text-align: center;
		margin-bottom: 20rpx;

		image {
			margin-bottom: 12rpx;
		}

		.right-title {
			font-size: 24rpx;
			font-weight: 600;
			color: #FAD5B4;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		.right-sub {
			font-size: 20rpx;
			color: #8E929E;
			margin-top: 6rpx;
		}
	}
}

.invite-badge {
	background: rgba(250, 213, 180, 0.1);
	border: 1rpx solid rgba(250, 213, 180, 0.3);
	border-radius: 12rpx;
	padding: 16rpx 24rpx;

	.invite-tag {
		background: linear-gradient(135deg, #FAD5B4 0%, #D49D6A 100%);
		color: #331D08;
		font-size: 20rpx;
		font-weight: 600;
		padding: 4rpx 12rpx;
		border-radius: 6rpx;
		margin-right: 16rpx;
		white-space: nowrap;
	}

	.invite-desc {
		font-size: 22rpx;
		color: #FAD5B4;
		line-height: 1.4;
	}
}

.already-tip {
	text-align: center;
	color: #999;
	font-size: 28rpx;
	margin: 20rpx 0;
}
</style>