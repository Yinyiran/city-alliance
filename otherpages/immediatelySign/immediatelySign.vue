<template>
	<view>
		<view class="setHeadBg">
			<view class="bodySty">
				<view class="font-36 font-weight f-tA color-base-border-bottom p-b-30">请认真核对相关信息，预计五分钟</view>
				<view class="font-30 font-weight p-t-20 f-tA color-base-border-bottom p-b-30">特约商户支付服务合作协议V3.3+分账结算授权委托书
				</view>
				<view class="flex p-t-30 p-b-30">
					<view v-if="show" @click="show=!show" class="iconfont icon-xuanzhong f-t font-36"></view>
					<view v-else @click="show=!show" class="iconfont icon-weixuanzhong font-36"></view>
					<view class="font-30 f-tA m-l-10">我已阅读并同意签署相关协议</view>
				</view>
				<view class="iptSty flex_b">
					<input type="tel" placeholder="请输入验证码" maxlength="11"
						style="width: 445rpx;border-right: 1rpx solid #afb0b2;" />
					<view class="font-28 f-t">发送验证码</view>
				</view>
				<view class="p-10 m-t-100">
					<button type="primary"
						@click="$util.redirectTo('/otherpages/immediatelySign/immediatelySign')">立即签署</button>
				</view>

			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				show: false
			};
		}
	}
</script>

<style lang="scss">
	page {
		background-color: #fff;
	}

	.iptSty {
		height: 114rpx;
		flex-shrink: 0;
		border-radius: 20rpx;
		background-color: #f6f7fb;
		margin: 0 auto;
		margin-bottom: 50rpx;
		padding: 0 25rpx;

		input {
			width: 100%;
		}
	}

	.setHeadBg {
		height: 405rpx;
		width: 100%;
		padding-top: 50rpx;
		box-sizing: border-box;
		background-image: linear-gradient(to top, #ff9374, #ff9374, #ff4d19);
	}

	.bodySty {
		width: 750rpx;
		opacity: 1;
		background: #ffffff;
		border-radius: 30rpx 30rpx 0 0;
		// background-color: rebeccapurple;
		margin-top: 100rpx;
		padding-top: 80rpx;
		padding-left: 30rpx;
		padding-right: 30rpx;
		padding-bottom: 30rpx;
		box-sizing: border-box;
	}
</style>