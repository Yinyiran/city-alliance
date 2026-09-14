<template>
	<view>
		<view class="tops">
			<image :src="$util.img('upload/uniapp/invite-bg.png')" mode=""></image>
			<view class="">
				<view class="">· 每邀1单奖<text>{{yao_point}}</text>积分 邀请的越多获得的福利也越多 ·</view>
				<image src="/otherpages/static/introduce-bg.png" mode=""></image>
			</view>
			<view class="part acea-row row-middle row-around p-r-34 p-l-34">
				<view class="w-132">
					<image src="/otherpages/static/rule1.png" class="w-76 h-76" mode=""></image>
					<view class="">
						分享链接 给好友
					</view>
				</view>
				<image src="/otherpages/static/schedule.png" class="w-36 h-36" mode=""></image>
				<view class="w-132">
					<image src="/otherpages/static/rule2.png" class="w-76 h-76" mode=""></image>
					<view class="">
						好友注册 可获得 新人专属福利
					</view>
				</view>
				<image src="/otherpages/static/schedule.png" class="w-36 h-36" mode=""></image>
				<view class="w-132">
					<image src="/otherpages/static/rule3.png" class="w-76 h-76" mode=""></image>
					<view class="">
						好友完成 授权注册 得积分奖励
					</view>
				</view>
				<image src="/otherpages/static/schedule.png" class="w-36 h-36" mode=""></image>
				<view class="w-132">
					<image src="/otherpages/static/rule4.png" class="w-76 h-76" mode=""></image>
					<view class="">
						好友 入驻会员 得现金奖励
					</view>
				</view>
			</view>
		</view>
		<view class="container p-t-126 p-r-32 p-l-32 p-b-68">
			<view class="part h-240 p-32">
				<view class="as acea-row row-middle row-center">
					<image src="/otherpages/static/lline.png" mode=""></image>
					<text>我的成就</text>
					<image src="/otherpages/static/rline.png" mode=""></image>
				</view>
				<view class="acea-row row-middle m-t-20 row-around">
					<view class="">
						<view class="font-54 font-weight">{{invitation_num}}
							<text>人</text>
						</view>
						<view class="">已成功邀请</view>
					</view>
					<view class="line"></view>
					<view class="">
						<view class="font-54 font-weight">{{invitation_point}}
						<text>分</text></view>
						<view class="">积分奖励</view>
					</view>
				</view>
				
			</view>
			<view class="part h-240 p-32 m-t-24">
				<view class="as acea-row row-middle row-center">
					<image src="/otherpages/static/lline.png" mode=""></image>
					<text>邀约奖励</text>
					<image src="/otherpages/static/rline.png" mode=""></image>
				</view>
				<view class="acea-row row-middle m-t-20 row-around">
					<view class="">
						<view class="font-54 font-weight">{{member_level_num}}
							<text>人</text>
						</view>
						<view class="">已成功入驻会员</view>
					</view>
					<view class="line"></view>
					<view class="">
						<view class="font-54 font-weight">{{invitation_money}}
						<text>元</text></view>
						<view class="">现金奖励</view>
					</view>
				</view>
				
			</view>
			<view class="part p-32 m-t-24">
				<view class="as acea-row row-middle row-center">
					<image src="/otherpages/static/lline.png" mode=""></image>
					<text>邀请记录</text>
					<image src="/otherpages/static/rline.png" mode=""></image>
				</view>
				<view class="a2q">
					<view v-for="(item,index) in list" :key="index">
						<image :src="$util.img(item.headimg)" v-if="item.headimg" class="w-84 h-84 bor-42" mode=""></image>
						<image v-else src="/static/avatar.png" class="w-84 h-84 bor-42" mode=""></image>
						<view class="">+{{item.yao_point}}分</view>
					</view>
				</view>
			</view>
			<view class="btn m-t-48 m-r-32 m-l-32">
				<button open-type="share" class="share-btn" hover-class="none" @click="handleShare">
					<image src="/otherpages/static/Invite-button.png" class="h-136" style="width: 100%;" mode=""></image>
				</button>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		invitation
	} from "@/common/js/api.js"
	import {
		appendShareParams
	} from "@/common/js/share.js"
	export default {
		data() {
			return {
				share: {
					title: '邀请您加入同城联盟，专享多重好礼！',
					path: '/pages/local/index'
				},
				yao_point:"",
				invitation_num:0,//成功邀请人数
				invitation_point:0,//邀请积分奖励
				member_level_num:0,//已成功入驻会员数
				invitation_money:0,//现金奖励
				list:[]
			};
		},
		onLoad() {
			this.getinfo()
		},
		onShareAppMessage(res) {
			const finalPath = appendShareParams('/pages/local/index');
			return {
				title: '邀请您加入同城联盟，专享多重好礼！',
				path: finalPath,
				success: () => {
					uni.showToast({
						title: '分享成功'
					});
				},
				fail: () => {
					uni.showToast({
						title: '分享失败',
						icon: 'none'
					});
				}
			};
		},
		onShareTimeline() {
			const finalPath = appendShareParams('/pages/local/index');
			const query = finalPath.indexOf('?') !== -1 ? finalPath.split('?')[1] : '';
			return {
				title: '邀请您加入同城联盟，专享多重好礼！',
				query: query
			};
		},
		methods: {
			getinfo() {
				invitation().then(res => {
					this.yao_point=res.data.yao_point
					this.invitation_num=res.data.invitation_num
					this.member_level_num=res.data.member_level_num
					this.invitation_money=res.data.invitation_money
					this.list=res.data.list
				})
			},
			handleShare() {
				// #ifndef MP-WEIXIN
				uni.showToast({
					title: '请在微信小程序中点击分享',
					icon: 'none'
				});
				// #endif
			}
		}
	}
</script>

<style lang="scss">
	.a2q{
		>view{
			display: inline-block;
			width: calc((100% - 160rpx)/5);
			margin-right: 40rpx;
			margin-top: 24rpx;
			position: relative;
			>view{
				position: absolute;
				background: #FFBB00;
				border-radius: 14rpx 14rpx 14rpx 4rpx;
				right: -29rpx ;
				top: 0;
				font-weight: 400;
				font-size: 20rpx;
				color: #FFFFFF;
				padding:0 6rpx;
			}
		}
		>view:nth-child(5n){
			margin-right: 0;
		}
	}
	.container{
		background: linear-gradient( 180deg, #A7D0FE 0%, #385CEB 100%);
	}
	.part{
		background: linear-gradient(180deg , #FFFFFF 0%, #FFEDEB 100%);
		border-radius: 24rpx;
		border: 2rpx solid #FFFFFF;
		box-sizing: border-box;
		text-align: center;
		.line{
			width: 2rpx;
			height: 64rpx;
			background: #DDDDDD;
		}
		.as{
			>image{
				width: 129rpx ;
				height: 4rpx;
			}
			>text{
				font-weight: 600;
				font-size: 32rpx;
				color: #222222;
				margin: 0 24rpx;
			}
		}
	}
	.tops {
		width: 100vw;
		height: 800rpx;
		position: relative;
		>view:nth-child(3){
			width: 686rpx;
			height: 242rpx;
			box-sizing: border-box;
			position: absolute;
			top: 660rpx;
			left: 50%;
			transform: translateX(-50%);
			text-align: center;
			flex-wrap: nowrap;
		}
		>view:nth-child(2){
			width: 604rpx;
			height: 54rpx;
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			z-index: 2;
			top: 164rpx;
			>view{
				font-weight: 600;
				font-size: 24rpx;
				color: #2970FF;
				position: absolute;
				left: 50%;
				top: 50%;
				transform: translate(-50%,-50%);
				z-index: 3;
				width: 100%;
				text-align: center;
				text{
					font-weight: 600;
					font-size: 24rpx;
					color: #FF4133;
				}
			}
			>image{
				width: 100%;
				height: 100%;
				position: absolute;
				left: 0;
				z-index: 1;
				top: 0;
			}
		}
		>image {
			width: 100%;
			height: 100%;
		}
	}
	.share-btn {
		background: transparent !important;
		border: none !important;
		padding: 0 !important;
		margin: 0 !important;
		line-height: 1 !important;
		border-radius: 0 !important;
		overflow: visible !important;
		display: block !important;
		width: 100% !important;
		&::after {
			border: none !important;
			display: none !important;
		}
	}
</style>