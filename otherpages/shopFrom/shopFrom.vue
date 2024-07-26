<template>
	<view>
		<view class="stepHead flex_b ">
			<block v-for="(item,i) in arrs" :key="i">
				<view class="stepItem">
					<image v-if="presentId>=(i+1)" src="../../static/image/icon1.png" mode="widthFix"></image>
					<image v-else src="../../static/image/icon2.png" mode="widthFix"></image>
					<view :class="presentId>=(i+1)?'f-t':'grey'" class="font-26 textC">{{item}}</view>
				</view>
				<view :class="presentId>(i+1)?'xuXianA':'xuXian'" v-if="i!=2"></view>
			</block>
		</view>
		<block v-if="presentId==1">
			<view class="modelBox flex_b">
				<view class="fromTit">店铺佣金比例<text>*</text> </view>
				<view class="font-28 grey">请选择佣金比例<text class="iconfont icon-yousanjiao"></text> </view>
			</view>
			<view class="modelBox">
				<view class="fromTit m-b-18">绑定商家空白收款码</view>
				<view class="font-26 grey">请扫一扫空白收款码绑定当前店铺，无空白码请忽略</view>
				<view class="sysSty flex_c_c m-t-30">
					<view class="iconfont icon-saoyisao graycolor font-40"></view>
					<view class="graycolor font-28">扫一扫</view>
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">收款码编号</view>
					<view class="font-28 grey">请扫一扫空白收款码</view>
				</view>
			</view>
			<view class="modelBox">
				<view class="fromTit m-b-18">店铺信息<text>*</text> </view>
				<view class="font-26 grey">请填写准确的店铺基础信息，对消费者展示</view>
				<view class="borModel flex_b">
					<view class="fromTit">店铺名称</view>
					<input type="text" placeholder-class="grey" placeholder="请填写店铺名称" />
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">经营类目</view>
					<view class="font-28 grey">请选择经营类目<text class="iconfont icon-yousanjiao"></text> </view>
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">店铺地址</view>
					<view class="font-28 grey">请选择店铺地址<text class="iconfont icon-yousanjiao"></text> </view>
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">详细地址</view>
					<input type="text" placeholder-class="grey" placeholder="例如1单元201" />
				</view>
				<view class="borModel ">
					<view class="flex_b">
						<view class="fromTit">营业时间</view>
						<view class="font-28 grey">设置开始时间-结束时间<text class="iconfont icon-yousanjiao"></text> </view>
					</view>
					<view class="flex_b timeSty">
						<view class="timeItem" @click="selectTime(index)" v-for="(item,index) in timer"
							:class="activeTime==index?'active':''" :key="index">
							{{item}}
						</view>
						<view class="w-127"></view>
					</view>
				</view>
			</view>
			<view class="modelBox">
				<view class="fromTit m-b-18">店铺图片<text>*</text> </view>
				<view class="font-26 grey color-base-border-bottom p-b-20">对消费者展示门店门头照、内视照</view>
				<view class="flex p-t-20 p-b-20">
					<view class="font-28 f-tA">店铺门头照</view>
					<view class="grey font-26 m-l-14">最多3张；仅支持:jpg，png</view>
				</view>
				<view class="flex" style="flex-wrap: wrap;">
					<view v-for="(item,index) in 2" :key="index" class="sysStyA">
						<image src="../../static/logo.png" mode="aspectFill"></image>
					</view>
					<view class="sysStyA flex_c_c m-t-20">
						<view class="iconfont icon-plus-circle-fill grey font-50"></view>
						<view class="graycolor font-28">上传图片</view>
					</view>
				</view>
				<view class="flex p-t-20 p-b-20">
					<view class="font-28 f-tA">内景照</view>
					<view class="grey font-26 m-l-14">至少2张；仅支持:jpg，png</view>
				</view>
				<view class="flex" style="flex-wrap: wrap;">
					<view v-for="(item,index) in 2" :key="index" class="sysStyA">
						<image src="../../static/logo.png" mode="aspectFill"></image>
					</view>
					<view class="sysStyA flex_c_c m-t-20">
						<view class="iconfont icon-plus-circle-fill grey font-50"></view>
						<view class="graycolor font-28">上传图片</view>
					</view>
				</view>
				<view class="flex p-t-20 p-b-20">
					<view class="font-28 f-tA">推手门店合影</view>
					<view class="grey font-26 m-l-14">最多3张；仅支持:jpg，png</view>
				</view>
				<view class="flex" style="flex-wrap: wrap;">
					<view v-for="(item,index) in 2" :key="index" class="sysStyA">
						<image src="../../static/logo.png" mode="aspectFill"></image>
					</view>
					<view class="sysStyA flex_c_c m-t-20">
						<view class="iconfont icon-plus-circle-fill grey font-50"></view>
						<view class="graycolor font-28">上传图片</view>
					</view>
				</view>
			</view>

			<view class="modelBox">
				<view class="fromTit m-b-18">商家联系方式<text>*</text> </view>
				<view class="borModel flex_b">
					<view class="fromTit">商家姓名</view>
					<input type="text" placeholder-class="grey" placeholder="请填写商家姓名" />
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">商家电话</view>
					<input type="text" placeholder-class="grey" placeholder="请填写商家电话" />
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">商家邮箱</view>
					<input type="text" placeholder-class="grey" placeholder="请填写商家邮箱" />
				</view>
			</view>

			<view class="bottom-mar h-180"></view>

			<view class="bomPosition">
				<view class="flex_b p-30">
					<view class="btnB">保存草稿</view>
					<view class=" btnA">下一步</view>
				</view>
			</view>

		</block>


	</view>
</template>

<script>
	export default {
		data() {
			return {
				arrs: ['完善店铺资料', '商户资质认证', '开通收款账户'],
				presentId: 1,
				timer: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
				activeTime: 0
			};
		},
		methods: {
			selectTime(i) {
				this.activeTime = i
			}
		},
		computed: {
			imgNum() {
				if (this.detail && this.detail.images) {
					return 3 % this.imgFn(this.detail.images).length
				} else {
					return 0
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	.btnA {
		width: 330rpx;
		height: 96rpx;
		background-color: #ff4d1a;
		border-radius: 96rpx;
		text-align: center;
		line-height: 96rpx;
		color: #fff;
		font-size: 28rpx;
		border: 1rpx solid #ff4d1a;
	}

	.btnB {
		width: 330rpx;
		height: 96rpx;
		background-color: #fff;
		border-radius: 96rpx;
		text-align: center;
		line-height: 96rpx;
		color: #1c274c;
		font-size: 28rpx;
		border: 1rpx solid #bbbec9;
	}

	.timeSty {
		flex-wrap: wrap;

		.timeItem {
			width: 127rpx;
			height: 59rpx;
			border-radius: 20rpx;
			background-color: #f3f4f7;
			text-align: center;
			line-height: 59rpx;
			color: #898fa3;
			margin-top: 20rpx;
		}

		.active {
			background-color: #ff4d1a !important;
			color: #fff !important;
		}
	}

	.borModel {
		border: 1rpx solid #dddfe4;
		border-radius: 20rpx;
		padding: 30rpx;
		margin-top: 30rpx;

		input {
			text-align: right;
			font-size: 28rpx;
		}
	}

	.sysSty {
		background-color: #f6f7fb;
		width: 171rpx;
		height: 171rpx;
		border-radius: 10rpx;
	}

	.sysStyA {
		background-color: #f6f7fb;
		width: 198rpx;
		height: 162rpx;
		border-radius: 10rpx;
		margin-right: 10rpx;

		image {
			width: 198rpx;
			height: 162rpx;
			border-radius: 10rpx;
		}
	}

	.fromTit {
		font-size: 28rpx;
		font-weight: 600;
		color: #1C274C;

		text {
			color: #FF1414;
			margin-left: 4rpx;
		}
	}

	.stepHead {
		height: 113rpx;
		background-color: #fff;
		padding: 0 44rpx;
		box-sizing: border-box;

		.stepItem {

			width: 166rpx;
			text-align: center;

			image {
				width: 77rpx;
			}
		}

		.xuXian {
			width: 60rpx;
			height: 0;
			border-top: 2rpx dashed #afb0b2;
		}

		.xuXianA {
			width: 30rpx;
			height: 0;
			border-top: 2rpx dashed #ff4d1a;
		}
	}
</style>