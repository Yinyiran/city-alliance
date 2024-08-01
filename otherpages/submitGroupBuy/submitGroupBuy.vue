<template>
	<view>
		<view class="modelBox">
			<view class="fromTit">套餐类型<text>*</text> </view>
			<view class="borModel flex_b" @click="openAll('选择经营类目')">
				<view class="font-28 grey">请选择套餐类型<text class="iconfont icon-yousanjiao"></text> </view>
			</view>
			<view class="fromTit m-t-30">套餐项目<text>*</text> </view>
			<view class="borModel flex_b" @click="openAll('选择经营类目')">
				<input type="text" placeholder-class="grey" placeholder="16字以内" />
			</view>
		</view>
		<view class="font-28 grey m-l-30 m-t-30">基本信息</view>
		<view class="modelBox">
			<view class="fromTit">套餐名称<text>*</text> </view>
			<view class="borModel flex_b" @click="openAll('选择经营类目')">
				<input type="text" placeholder-class="grey" placeholder="16字以内，突出卖点" />
			</view>
			<view class="fromTit m-t-30 m-b-20">使用人数</view>
			<view class="flex numList">
				<view @click="activePeopel=item.id" v-for="(item,index) in peopelList" :key="index"
					:class="activePeopel==item.id?'activePeopelSty':''" class="grey font-28 textC">
					{{item.title}}
				</view>
			</view>
			<view class="numList flex m-t-30">
				<view :class="activePeopel==''?'activePeopelSty':''" @click="activePeopel=''" class="">自定义</view>
				<input type="text" placeholder="请输入" placeholder-class="font-28 textC" />
				<text>-</text>
				<input type="text" placeholder="请输入" placeholder-class="font-28 textC" />
			</view>

			<view class="flex p-t-20 p-b-20">
				<view class="font-28 f-tA">店铺门头照</view>
				<view class="grey font-26 m-l-14">最多3张；仅支持:jpg，png</view>
			</view>
			<view class="flex" style="flex-wrap: wrap;">
				<view v-for="(item,index) in 2" :key="index" class="sysStyA">
					<image src="../../static/logo.png" mode="aspectFill"></image>
					<view class="deleteSty  flex_center">
						<view class="white iconfont icon-shanchu"></view>
						<view class="white m-l-4">删除</view>
					</view>
				</view>
				<view class="sysStyA flex_c_c">
					<view class="iconfont icon-plus-circle-fill grey font-50"></view>
					<view class="graycolor font-28">上传图片</view>
				</view>
			</view>
		</view>
		<view class="font-28 grey m-l-30 m-t-30">套餐信息</view>
		<view class="modelBox">
			<view class="flex_b">
				<view class="fromTit">套餐分组名称<text>*</text> </view>
				<view class="font-28 f-t">删除</view>
			</view>
			<view class="borModel flex_b">
				<input type="text" placeholder-class="grey" maxlength="15" placeholder="15字以内" />
			</view>
			<view class="flex m-t-28">
				<view class="font-28 font-weight">以下1选</view>
				<view class="flex numBox m-l-20">
					<view class="iconfont icon-jianshao- font-weight font-26" @click="minus" :class="numA<=1?'grey':''">
					</view>
					<view class="font-28 m-l-16 m-r-16 font-weight">{{numA}}</view>
					<view class="iconfont icon-zengjia font-weight font-26" @click="numA=numA+1"></view>
				</view>
			</view>
			<view class="underBox m-t-20">
				<view class="flex_b">
					<view class="fromTit">名称<text>*</text> </view>
					<view class="font-28 f-t">删除</view>
				</view>
				<view class="borModelB">
					<input type="text" placeholder-class="grey" maxlength="15" placeholder="15字以内" />
				</view>
				<view class="flex_b m-t-28">
					<view class="flex">
						<view class="font-28 font-weight">数量</view>
						<view class="flex numBox m-l-20">
							<view class="iconfont icon-jianshao- font-weight font-26" style="background-color: #fff;"
								@click="minus" :class="numA<=1?'grey':''">
							</view>
							<view class="font-28 m-l-16 m-r-16 font-weight">{{numA}}</view>
							<view style="background-color: #fff;" class="iconfont icon-zengjia font-weight font-26"
								@click="numA=numA+1"></view>
						</view>
					</view>
					<view class="flex">
						<view class="font-28 font-weight">单位</view>
						<view class="flex_center danJia">个<text class="iconfont icon-xiajiantou font-24"></text> </view>
					</view>
					<view class="flex">
						<view class="font-28 font-weight">单价</view>
						<view class="flex_center danJia">
							<input type="text" placeholder="" />
						</view>
					</view>
				</view>
			</view>
			<view class="lookAll m-t-20 font-28 font-weight">添加更多</view>
		</view>

		<view class="modelBox textC f-t font-weight font-28">添加套餐分组</view>

		<view class="modelBox">
			<view class="flex p-t-20 p-b-20">
				<view class="font-28 f-tA">店铺门头照</view>
				<view class="grey font-26 m-l-14">最多3张；仅支持:jpg，png</view>
			</view>
			<view class="flex" style="flex-wrap: wrap;">
				<view v-for="(item,index) in 2" :key="index" class="sysStyA">
					<image src="../../static/logo.png" mode="aspectFill"></image>
					<view class="deleteSty  flex_center">
						<view class="white iconfont icon-shanchu"></view>
						<view class="white m-l-4">删除</view>
					</view>
				</view>
				<view class="sysStyA flex_c_c">
					<view class="iconfont icon-plus-circle-fill grey font-50"></view>
					<view class="graycolor font-28">上传图片</view>
				</view>
			</view>
		</view>
		<view class="font-28 grey m-l-30 m-t-30">发布规则</view>
		<view class="modelBox">
			<view class="color-base-border-bottom p-b-30">
				<view class="fromTit m-b-20">总库存<text>*</text></view>
				<view class="m-t-20">
					<radio-group @change="radioChange">
						<view class="flex ">
							<label class="flex font-24" v-for="(item, index) in radiolistA" :key="item.name">
								<view class="font-24">
									<radio activeBackgroundColor="#fe4d1b" style="transform:scale(0.7)"
										:value="item.name" :checked="index === current" />
								</view>
								<view>{{item.name}}</view>
							</label>
						</view>
					</radio-group>
				</view>
			</view>
			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit">每日库存<text>*</text></view>
				<view class="m-t-20">
					<radio-group @change="radioChange">
						<view class="flex ">
							<label class="flex font-24" v-for="(item, index) in radiolistA" :key="item.name">
								<view class="font-24">
									<radio activeBackgroundColor="#fe4d1b" style="transform:scale(0.7)"
										:value="item.name" :checked="index === current" />
								</view>
								<view>{{item.name}}</view>
							</label>
						</view>
					</radio-group>
				</view>
			</view>
			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit m-b-20">每人最多购买<text>*</text></view>
				<uv-radio-group v-model="radiovalue">
					<uv-radio activeColor="#fe4d1b" :customStyle="{margin: '8px'}" v-for="(item, index) in radiolistA"
						:key="index" :label="item.name" :name="item.name">
					</uv-radio>
				</uv-radio-group>
			</view>
			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit m-b-20">每人每日最多购买<text>*</text></view>
				<uv-radio-group v-model="radiovalue">
					<uv-radio activeColor="#fe4d1b" :customStyle="{margin: '8px'}" v-for="(item, index) in radiolistA"
						:key="index" :label="item.name" :name="item.name">
					</uv-radio>
				</uv-radio-group>
			</view>
			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit m-b-20">开始售卖时间<text>*</text></view>
				<view class="">
					<label class="radio acea-row row-middle" @click="saletype=1">
						<radio value="" /><text class="font-28">发布后立即上线</text>
					</label>
					<label class="radio m-t-20 acea-row row-middle" @click="saletype=2">
						<radio value="" :checked="saletype==2" /><text class="font-28">选择开始时间</text>
						<view class="w-368 m-l-14" @click="start">
							<uv-input readonly placeholder="请选择时间">
								<template v-slot:suffix>
									<uv-icon name="arrow-down-fill" color="#1C274C" size="10"></uv-icon>
								</template>
							</uv-input>
						</view>
					</label>
				</view>

			</view>
			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit m-b-20">结束售卖时间<text>*</text></view>
				<view class="radio m-t-20 acea-row row-middle m-b-20">
					<text class="font-28">选择结束时间</text>
					<view class="w-368 m-l-14" @click="start">
						<uv-input readonly placeholder="请选择时间">
							<template v-slot:suffix>
								<uv-icon name="arrow-down-fill" color="#1C274C" size="10"></uv-icon>
							</template>
						</uv-input>
					</view>
				</view>
				<label class="radio">
					<radio value="" /><text class="font-28">到期后自动延续30天</text>
				</label>
			</view>
			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit m-b-20">可用时间<text>*</text></view>
				<label class="radio">
					<radio value="" /><text class="font-28">固定有效期</text>
				</label>
				<view class="">
					<label class="radio acea-row row-middle">
						<radio value="" /><text class="font-28">购买后</text>
						<view class="w-164 m-r-10 m-l-10">
							<uv-input placeholder="请输入时间" type="number"></uv-input>
						</view>
						<text class="font-28">天有效</text>
					</label>
				</view>
			</view>
			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit m-b-20">使用日期<text>*</text></view>
				<view class="">
					<label class="radio">
						<radio value="" /><text class="font-28">所有日期可用</text>
					</label>
				</view>
				<view class="">
					<label class="radio">
						<radio value="" /><text class="font-28">部分日期可用</text>
					</label>
				</view>
				<view class="acea-row usedates m-t-20">
					<view @click="activedate=index" v-for="(item,index) in datelist" :key="index"
						:class="activedate==index?'activePeopelSty':''" class="grey font-28 textC bor-10 m-t-20">
						{{item.title}}
					</view>
				</view>
			</view>
			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit m-b-20">使用时间<text>*</text></view>
				<view class="">
					<label class="radio">
						<radio value="" /><text class="font-28">营业时间可用</text>
					</label>
				</view>
				<view class="m-t-20">
					<label class="radio">
						<radio value="" /><text class="font-28">部分时间可用</text>
					</label>
				</view>
				<view class="usetimes acea-row row-between row-middle m-t-30">
					<view class="">
						<uv-input readonly placeholder="请选择时间">
							<template v-slot:suffix>
								<uv-icon name="arrow-down-fill" color="#1C274C" size="10"></uv-icon>
							</template>
						</uv-input>
					</view>
					-
					<view class="">
						<uv-input readonly placeholder="请选择时间">
							<template v-slot:suffix>
								<uv-icon name="arrow-down-fill" color="#1C274C" size="10"></uv-icon>
							</template>
						</uv-input>
					</view>
				</view>
			</view>

			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit m-b-20">是否预约<text>*</text></view>
				<label class="radio">
					<radio value="" /><text class="font-28">无需预约</text>
				</label>
				<view class="">
					<label class="radio acea-row row-middle">
						<radio value="" /><text class="font-28">需要提前预约</text>
						<view class="w-164 m-r-10 m-l-10">
							<uv-input placeholder="请输入时间" fontSize="13px" type="number"></uv-input>
						</view>
						<text class="font-28">小时</text>
					</label>
				</view>
			</view>
			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit m-b-20">券限用<text>*</text></view>
				<view class="m-b-20">
					<label class="radio">
						<radio value="" /><text class="font-28">不限</text>
					</label>
				</view>
				<label class="radio">
					<radio value="" /><text class="font-28">每人限用</text>
				</label>
				<view class="">
					<label class="radio acea-row row-middle">
						<radio value="" /><text class="font-28">每桌限用</text>
						<view class="w-164 m-r-10 m-l-10">
							<uv-input placeholder="请输入" fontSize="13px" type="number"></uv-input>
						</view>
						<text class="font-28">张</text>
					</label>
				</view>
			</view>
			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit m-b-20">堂食外卖约定</view>
				<view class="acea-row row-middle">
					<label class="radio">
						<radio value="" /><text class="font-28">堂食</text>
					</label>
					<label class="radio m-l-50">
						<radio value="" /><text class="font-28">外卖</text>
					</label>
				</view>
			</view>
			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit m-b-20">是否可用包间</view>
				<view class="acea-row row-middle row-between">
					<label class="radio">
						<radio value="" /><text class="font-28">是</text>
					</label>
					<label class="radio">
						<radio value="" /><text class="font-28">否</text>
					</label>
					<label class="radio">
						<radio value="" /><text class="font-28">详细咨询客服</text>
					</label>
					<label class="radio">
						<radio value="" /><text class="font-28">无</text>
					</label>
				</view>
			</view>
			<view class="color-base-border-bottom p-b-30 m-t-40">
				<view class="fromTit m-b-20">同时享受店内其他优惠</view>
				<view class="acea-row row-between">
					<label class="radio">
						<radio value="" /><text class="font-28">可以</text>
					</label>
					<label class="radio">
						<radio value="" /><text class="font-28">不可以</text>
					</label>
					<label class="radio">
						<radio value="" /><text class="font-28">详细咨询客服</text>
					</label>
					<label class="radio ">
						<radio value="" /><text class="font-28">无</text>
					</label>
				</view>
			</view>
			<view class="p-b-30 m-t-40">
				<view class="fromTit m-b-20">其他规则补充</view>
				<textarea placeholder="在此输入补充规则..." maxlength="-1"></textarea>
			</view>
		</view>
		<view class="modelBox">
			<view class="fromTit m-b-20">门店价 ¥0</view>
			<view class="acea-row row-middle h-58">
				<view class="fromTit">券限用<text>*</text></view>
				<view class="w-166  m-r-14 m-l-14">
					<uv-input placeholder="请输入"></uv-input>
				</view>
				<text class="fromTit">元</text>
			</view>
			<view class="acea-row row-middle m-t-40 h-58">
				<view class="fromTit">券限用<text>*</text></view>
				<view class="w-166  m-r-14 m-l-14">
					<uv-input placeholder="请输入"></uv-input>
				</view>
				<text class="fromTit">%</text>
			</view>
		</view>
		<view class="btm acea-row row-between iphone-x p-t-24 p-r-20 p-l-20">
			<view class="bor-100">保存草稿</view>
			<view class="bor-100">预览并发布</view>
		</view>
		<view class="bottom-mar"></view>
		<uv-datetime-picker ref="datetimePicker" v-model="value1" :minDate="value1" mode="datetime"
			@confirm="confirm" />
		<uv-datetime-picker ref="datetimePicker2" v-model="value" mode="date" @confirm="confirm" />
	</view>
</template>

<script>
	export default {
		data() {
			return {
				saletype: 1, //开始售卖时间
				value1: Number(new Date()),
				radiolistA: [{
					name: '不限',
					disabled: true
				}, {
					name: '限制最多',
					disabled: false
				}],
				radiovalue: '不限',
				// uv-radio-group的v-model绑定的值如果设置为某个radio的name，就会被默认选中

				numA: 1,
				activePeopel: 1,
				activedate: 0,
				datelist: [{
						title: '周一'
					}, {
						title: '周二'
					},
					{
						title: '周三'
					},
					{
						title: '周四'
					},
					{
						title: '周五'
					},
					{
						title: '周六'
					},
					{
						title: '周日'
					},
					{
						title: '节假日通用'
					},
					{
						title: '节假日不可用'
					}
				],
				peopelList: [{
					title: '单人',
					id: '1'
				}, {
					title: '双人',
					id: '2'
				}, {
					title: '3-4人',
					id: '3'
				}, {
					title: '5-6人',
					id: '4'
				}, {
					title: '不限人数',
					id: '5'
				}, {
					title: '无',
					id: '6'
				}]
			};
		},
		methods: {
			minus() {
				// 减少
				if (this.numA > 1) {
					this.numA--
				}
			},
			start() {
				this.$refs.datetimePicker.open()
			},
			confirm() {},
		}
	}
</script>

<style lang="scss">
	page{
		padding-bottom: calc(env(safe-area-inset-bottom) + 100rpx)
	}
	.lookAll {
		height: 77rpx;
		flex-shrink: 0;
		background-color: #f6f7fb;
		border-radius: 20rpx;
		text-align: center;
		line-height: 77rpx;
	}
	.btm{
		position: fixed;
		bottom: 0;
		left: 0;
		width: 100vw;
		background: #fff;
		box-sizing: border-box;
		z-index: 9;
		
		view{
			flex:0 0 calc((100% - 28rpx)/2) ;
			text-align: center;
			padding: 29rpx 0;
			font-size: 28rpx;
			color: #1C274C;
			margin-bottom: 20rpx;
		}
		view:first-child{
			border: 1rpx solid #BBBEC9;
		}
		view:last-child{
			color: #fff;
			background: #FF4D1A;
		}
	}

	textarea {
		width: 100%;
		height: 221rpx;
		padding: 27rpx 32rpx;
		border-radius: 20rpx;
		border: 1rpx solid #BBBEC9;
		box-sizing: border-box;
	}

	.usedates {
		view {
			padding: 10rpx 35rpx;
			margin-right: 28rpx;
			background: #F6F7FB;
		}

		>view:nth-child(8),
		>view:nth-child(9) {
			padding: 10rpx 20rpx;
			margin-right: 0;
		}

	}

	.usetimes {
		view {
			flex: 0 0 calc((100% - 84rpx)/2);
		}
	}

	.danJia {
		width: 92rpx;
		height: 49rpx;
		background-color: #fff;
		border-radius: 10rpx;
		margin-left: 10rpx;
	}

	.underBox {
		background-color: #f6f7fb;
		padding: 20rpx;
		border-radius: 20rpx;
	}

	.numBox {
		.iconfont {
			background-color: #f4f5f8;
			width: 40rpx;
			height: 40rpx;
			border-radius: 50%;
			text-align: center;
			line-height: 40rpx;
		}

	}

	.activePeopelSty {
		background-color: #fe4d1b !important;
		color: #fff !important;
	}

	.sysStyA {
		background-color: #f6f7fb;
		width: 198rpx;
		height: 162rpx;
		border-radius: 10rpx;
		margin-right: 10rpx;
		position: relative;

		.deleteSty {
			position: absolute;
			bottom: 0;
			width: 198rpx;
			background-image: linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0));
		}

		image {
			width: 198rpx;
			height: 162rpx;
			border-radius: 10rpx;
		}
	}

	.numList {
		flex-wrap: wrap;

		view {
			text-align: center;
			line-height: 59rpx;
			width: 127rpx;
			height: 59rpx;
			border-radius: 20rpx;
			background-color: #f6f7fb;
			margin-right: 20rpx;
			margin-bottom: 20rpx;
		}

		text {
			margin: 0 20rpx;
			margin-bottom: 20rpx;
			display: inline-block;
		}

		input {
			text-align: center;
			width: 146rpx;
			height: 59rpx;
			border-radius: 20rpx;
			border: 1rpx solid #bbbec9;
			margin-bottom: 20rpx;
		}

	}

	.inputSr {
		text-align: center;
		width: 146rpx;
		height: 59rpx;
		border-radius: 20rpx;
		border: 1rpx solid #bbbec9;
		margin-bottom: 20rpx;
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

	.borModelB {
		background-color: #fff;
		border-radius: 20rpx;
		padding: 30rpx;
		margin-top: 20rpx;

		input {
			font-size: 28rpx;
			width: 100%;
		}
	}

	.borModel {
		border: 1rpx solid #dddfe4;
		border-radius: 20rpx;
		padding: 30rpx;
		margin-top: 20rpx;

		input {
			font-size: 28rpx;
			width: 100%;
		}
	}

	.radio {
		display: flex;
		align-items: center;

		radio {
			transform: scale(0.7);
		}

	}

	/* #ifdef APP-PLUS || MP-WEIXIN */

	radio .wx-radio-input.wx-radio-input-checked {
		background-color: #FF4D1A !important;
		border-color: #FF4D1A !important;
		// box-sizing: border-box;
	}

	/**
	* 去除选中后的对号
	*/
	radio::before {
		content: '' !important;
	}

	/* #endif */
</style>