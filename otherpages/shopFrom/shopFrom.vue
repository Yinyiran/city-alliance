<template>
	<view>
		<view class="stepHead flex_b ">
			<block v-for="(item,i) in arrs" :key="i">
				<view class="stepItem">
					<image v-if="presentId>(i+1)" src="../../static/image/icon3.png" mode="widthFix"></image>
					<image v-else-if="presentId==(i+1)" src="../../static/image/icon1.png" mode="widthFix"></image>
					<image v-else src="../../static/image/icon2.png" mode="widthFix"></image>
					<view :class="presentId>=(i+1)?'f-t':'grey'" class="font-26 textC">{{item}}</view>
				</view>
				<view :class="presentId>(i+1)?'xuXianA':'xuXian'" v-if="i!=2"></view>
			</block>
		</view>
		<block v-if="presentId==1">
			<view class="modelBox flex_b" @click="openAll('选择佣金比例')">
				<view class="fromTit">店铺佣金比例<text>*</text> </view>
				<view class="font-28 grey">请选择佣金比例<text class="iconfont icon-yousanjiao"></text> </view>
			</view>
			<view class="modelBox">
				<view class="fromTit m-b-18">绑定商家空白收款码</view>
				<view class="font-26 grey">请扫一扫空白收款码绑定当前店铺，无空白码请忽略</view>
				<view class="sysSty flex_c_c m-t-30" @click="scanFn">
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
				<view class="borModel flex_b" @click="openAll('选择经营类目')">
					<view class="fromTit">经营类目</view>
					<view class="font-28 grey">请选择经营类目<text class="iconfont icon-yousanjiao"></text> </view>
				</view>
				<view class="borModel flex_b" @click="selectAddress">
					<view class="fromTit">店铺地址</view>
					<view class="font-28 grey">请选择店铺地址<text class="iconfont icon-yousanjiao"></text> </view>
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">详细地址</view>
					<input type="text" placeholder-class="grey" placeholder="例如1单元201" />
				</view>
				<view class="borModel ">
					<view class="flex_b" @click="openTime">
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
						<view class="deleteSty  flex_center">
							<view class="white iconfont icon-shanchu"></view>
							<view class="white m-l-4">删除</view>
						</view>
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
						<view class="deleteSty  flex_center">
							<view class="white iconfont icon-shanchu"></view>
							<view class="white m-l-4">删除</view>
						</view>
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
						<view class="deleteSty  flex_center">
							<view class="white iconfont icon-shanchu"></view>
							<view class="white m-l-4">删除</view>
						</view>
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
					<input type="tel" maxlength="11" placeholder-class="grey" placeholder="请填写商家电话" />
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
					<view class=" btnA" @click="nextStep">下一步</view>
				</view>
			</view>
		</block>
		<block v-if="presentId==2">
			<view class="modelBox flex_b" @click="openAll('选择店铺主体')">
				<view class="fromTit">店铺主体类型<text>*</text> </view>
				<view class="font-28 grey">请选择店铺主体类型<text class="iconfont icon-yousanjiao"></text> </view>
			</view>
			<view class="modelBox">
				<view class="fromTit">营业执照信息<text>*</text> </view>
				<view class="font-26 grey m-t-20">请填写营业执照信息</view>
				<view class="flex p-t-20 p-b-20">
					<view class="font-28 f-tA">推手门店合影</view>
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
					<view class="sysStyA flex_c_c m-t-20">
						<view class="iconfont icon-plus-circle-fill grey font-50"></view>
						<view class="graycolor font-28">上传图片</view>
					</view>
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">营业执照号</view>
					<view class="font-28 grey">上传执照后自动识别号码</view>
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">主体名称</view>
					<view class="font-28 grey">上传执照后自动识别</view>
				</view>

			</view>
			<view class="modelBox">
				<view class="fromTit m-b-18">法人/负责人身份信息<text>*</text> </view>
				<view class="font-26 grey color-base-border-bottom p-b-20">须与营业执照法人信息一致</view>
				<view class="flex p-t-20 p-b-20">
					<view class="font-28 f-tA">证件照</view>
					<view class="grey font-26 m-l-14">仅支持:jpg，png</view>
				</view>
				<view class="flex sfzSty">
					<image src="../../static/image/sfz1.png" mode="aspectFill"></image>
					<image src="../../static/image/sfz2.png" mode="aspectFill"></image>
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">证件姓名</view>
					<input type="text" placeholder-class="grey" placeholder="请输入姓名" />
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">证件号码</view>
					<input type="text" placeholder-class="grey" placeholder="请上传身份证" />
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">证件有效期</view>
					<input type="text" placeholder-class="grey" placeholder="开始时间 - 结束时间" />
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">法人手机号</view>
					<input type="text" placeholder-class="grey" placeholder="请输入手机号" />
				</view>
			</view>

			<view class="modelBox">
				<view class="flex_b">
					<view class="fromTit m-b-18 ">法人签名<text>*</text> </view>
					<button @click="clearCanvas" type="mini" style="margin: 0;">清空</button>
				</view>

				<view class="font-26 grey p-b-20">需与法人姓名一致，本人签名或地推代签</view>
				<canvas :hidden='!alertShow' canvas-id="canvas" class="canvasBodyA " @error="error"
					:disable-scroll="true" @touchstart="canvasStart" @touchmove="canvasMove" @touchend='canvasEnd'>
					<!-- <view class=" " style="z-index:-1;">
						<view class="iconfont icon-qianming grey font-60" style="z-index:-1;"></view>
						<view class="grey font-32" style="z-index:-1;">店铺法人签名</view>
					</view> -->
				</canvas>
			</view>

			<view class="bottom-mar h-180"></view>

			<view class="bomPosition">
				<view class="flex_b p-30">
					<view class="btnB">保存草稿</view>
					<view class="btnC" @click="lastStep">上一步</view>
					<view class=" btnA" @click="nextStepB">下一步</view>
				</view>
			</view>
		</block>

		<block v-if="presentId==3">
			<view class="modelBox">
				<view class="fromTit m-b-18">收款账户<text>*</text> </view>
				<view class="font-26 grey ">用于支付宝&微信平台的交易收款</view>
				<view class="borModel flex_b" @click="openAll('选择账户类型')">
					<view class="fromTit">账户类型</view>
					<view class="font-28 grey">选择账户类型<text class="iconfont icon-yousanjiao"></text> </view>
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
					<view class="sysStyA flex_c_c m-t-20">
						<view class="iconfont icon-plus-circle-fill grey font-50"></view>
						<view class="graycolor font-28">上传图片</view>
					</view>
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">银行账号</view>
					<input type="text" placeholder-class="grey" placeholder="请输入银行卡号" />
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">开户人</view>
					<input type="text" placeholder-class="grey" placeholder="须与身份证主体一致" />
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">开户银行支行</view>
					<view class="font-28 grey">请选择银行支行<text class="iconfont icon-yousanjiao"></text> </view>
				</view>
			</view>
			<view class="modelBox">
				<view class="fromTit m-b-18">支付宝与微信账户签约<text>*</text> </view>
				<view class="font-26 grey ">认证审核通过后，需与您的支付宝、微信账户分别签约;支付宝和微信的签约账号须与营业执照法人一致</view>
				<view class="borModel flex_b">
					<view class="fromTit">支付宝签约账号</view>
					<input type="text" placeholder-class="grey" placeholder="请输入签约账号" />
				</view>
				<view class="borModel flex_b">
					<view class="fromTit">微信签约账号</view>
					<input type="text" placeholder-class="grey" placeholder="请输入微信号" />
				</view>
			</view>
			<view class="bottom-mar h-180"></view>

			<view class="bomPosition">
				<view class="flex_b p-30">
					<view class="btnB">保存草稿</view>
					<view class="btnC" @click="lastStep">上一步</view>
					<view class=" btnA" @click="nextStepC">提交审核</view>
				</view>
			</view>
		</block>

		<!-- 选择 -->
		<uv-picker ref="picker" :title="pickTitle" confirmColor="#ff4d1a" :columns="columns"
			@confirm="confirmAll"></uv-picker>
		<!-- 选择时间段 -->
		<uv-picker ref="pickerTime" confirmColor="#ff4d1a" :columns="columnsTime" @confirm="confirmTime"></uv-picker>
		<!-- 弹出验证手机号 -->
		<uv-popup ref="popup" @change="change" round="15">
			<view>
				<view class="h-100 lh-100 textC font-32 font-weight f-tA">法人手机号验证</view>
				<view class="bodySty">
					<view class="iptSty flex">
						<input type="tel" placeholder="请输入手机号" maxlength="11" />
					</view>
					<view class="iptSty flex_b">
						<input type="tel" placeholder="请输入验证码" maxlength="11"
							style="width: 445rpx;border-right: 1rpx solid #afb0b2;" />
						<view class="font-28 f-t">发送验证码</view>
					</view>
					<view class="p-30">
						<button type="primary">登录</button>
					</view>
				</view>
			</view>
		</uv-popup>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				alertShow: true,
				pickTitle: '',
				arrs: ['完善店铺资料', '商户资质认证', '开通收款账户'],
				presentId: 3,
				timer: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
				activeTime: 0,
				columnsTime: [
					[],
					[],
					['至'],
					[],
					[]
				],
				columns: [],

				columnsYj: [
					['1%', '2%', '3%', '4%', ]
				],
				columnsZt: [
					['个体工商户', '个人', '企业']
				],
				columnsZh: [
					['对私账户', '对公账户']
				],
				columnsLm: [
					['休闲娱乐', '车主生活', '商业生活服务', ],
					['洗车加油', '洗浴保健', '家政保洁', '丽人美发', ],
				],
				selLatitude: '',
				selLongitude: '',
				mycanvas: '',
				// 判断是否开始绘画
				isStart: false,
			};
		},
		onLoad() {
			// #ifdef MP
			this.authCLcation()
			// #endif
			this.gitTime()
		},
		onReady() {
			let aa = wx.createCanvasContext("canvas", this);
			// 设置线的样式
			aa.setLineCap("round");
			aa.setLineJoin("round");
			// 初始化颜色
			aa.setStrokeStyle('#000');
			// 初始化粗细
			aa.setLineWidth(5);
			this.mycanvas = aa
		},
		onShow() {
			let aa = wx.createCanvasContext("canvas", this);
			// 设置线的样式
			aa.setLineCap("round");
			aa.setLineJoin("round");
			// 初始化颜色
			aa.setStrokeStyle('#000');
			// 初始化粗细
			aa.setLineWidth(5);
			this.mycanvas = aa
		},
		methods: {

			lastStep() {
				this.presentId--
			},
			// 清除画板
			clearCanvas() {
				// 清除区域
				this.mycanvas.clearRect(0, 0, 400, 1000);
				this.mycanvas.draw(true);
			},
			canvasStart(e) {
				this.isStart = true
				// 获取触摸点的x，y位置
				let x = e.touches[0].x;
				let y = e.touches[0].y;
				// 将画笔移动到指定坐标
				this.mycanvas.moveTo(x, y);
			},
			// 绘画进行中
			canvasMove(e) {
				// 获取移动过程中的x,y位置
				let x = e.touches[0].x;
				let y = e.touches[0].y;
				// 指定移动的位置
				this.mycanvas.lineTo(x, y);
				// 开始画线
				this.mycanvas.stroke();
				// 将绘画绘制到canvas
				this.mycanvas.draw(true);
				// 绘制完成，将起始点进行移动
				this.mycanvas.moveTo(x, y);
			},
			// 绘画结束
			canvasEnd() {
				// 判断是否开始绘画
				this.isStart = true
			},
			error(e) {
				console.log(e);
			},
			nextStep() {
				// 第一步的下一步
				this.presentId = 2
			},
			nextStepB() {
				// 第二步的下一步
				this.presentId = 3
				return
				uni.canvasToTempFilePath({
					width: 397,
					height: 556,
					destWidth: 150,
					destHeight: 200,
					canvasId: 'canvas',
					success: res => {
						uni.uploadFile({
							url: 'https://hb.hthckj.top/api/common/upload',
							filePath: res.tempFilePath,
							name: 'file',
							success: resA => {
								let a = JSON.parse(resA.data)
								this.$util.redirectTo('/otherpages/canvasSubmit/canvasSubmit', {
									tempFilePath: a.data.fullurl
								})
							},
							error: err => {}
						});
						// 在H5平台下，tempFilePath 为 base64
						// console.log(res.tempFilePath)
					}
				})
			},
			// 第三步的提交
			nextStepC() {
				this.$refs.popup.open('bottom');
			},
			// 获取位置
			selectAddress() {
				var that = this;
				uni.chooseLocation({
					latitude: this.selLatitude,
					longitude: this.selLongitude,
					success(res) {
						that.form.latitude = res.latitude;
						that.form.longitude = res.longitude;
						that.latName = res.name;
						that.form.name = res.address
						that.form.address = res.name
					},
					fail(res) {
						uni.getSetting({
							success: (res) => {
								var statu = res.authSetting;
								if (!statu['scope.userLocation']) {
									uni.showModal({
										title: '是否授权当前位置',
										content: '需要获取您的地理位置，请确认授权，否则地图功能将无法使用',
										success(tip) {
											if (tip.confirm) {
												uni.openSetting({
													success: data => {
														if (data.authSetting[
																'scope.userLocation'
															] === true) {
															this.$util.showToast({
																title: '授权成功'
															});
															//授权成功之后，再调用chooseLocation选择地方
															setTimeout(function() {
																uni.chooseLocation({
																	latitude: that
																		.selLatitude,
																	longitude: that
																		.selLongitude,
																	success: res => {
																		that.form
																			.address =
																			res
																			.address
																		that.form
																			.name =
																			res
																			.name

																		that.form
																			.latitude =
																			res
																			.latitude;
																		that.form
																			.longitude =
																			res
																			.longitude;
																		that.latName =
																			res
																			.name;
																	}
																});
															}, 1000);
														}
													}
												});
											} else {
												this.$util.showToast({
													title: '授权失败'
												});
											}
										}
									});
								}
							}
						});
					}
				});
			},
			// 开始时间结束时间
			gitTime() {
				let i = 1
				let j = 1
				for (i; i <= 12; i++) {
					this.columnsTime[0].push(i + '时')
					this.columnsTime[3].push(i + '时')
				}
				for (j; j <= 60; j++) {
					this.columnsTime[1].push(j + '分')
					this.columnsTime[4].push(j + '分')
				}
			},
			openTime() {
				this.$refs.pickerTime.open();
			},
			scanFn() {
				// 扫码
				uni.scanCode({
					onlyFromCamera: true,
					success: res => {
						if (res.result) {

						} else {
							uni.showToast({
								title: '二维码错误',
								icon: 'none'
							})
						}
					}
				});
			},
			selectTime(i) {
				this.activeTime = i
			},
			openAll(type) {
				this.pickTitle = type
				if (type == '选择佣金比例') {
					this.columns = this.columnsYj
				} else if (type == '选择经营类目') {
					this.columns = this.columnsLm
				} else if (type == '选择店铺主体') {
					this.columns = this.columnsZt
				} else if (type == '选择账户类型') {
					this.columns = this.columnsZh
				}



				this.$refs.picker.open();
			},
			confirmAll(e) {
				console.log(e, 'e+++---');

			},

			// 重新获取
			addFn() {
				let that = this
				uni.getSetting({
					success: function(res) {
						var statu = res.authSetting;
						if (!statu['scope.userLocation']) {
							uni.showModal({
								title: '是否授权当前位置',
								content: '需要获取您的地理位置，请确认授权，否则部分功能将无法正常使用',
								success(tip) {
									if (tip.confirm) {
										uni.openSetting({
											success: function(data) {
												if (data.authSetting[
														'scope.userLocation'
													] === true) {
													uni.showToast({
														title: '授权成功'
													});
													that.authCLcation()
													//授权成功之后，再调用chooseLocation选择地方
												}
											}
										});
									} else {
										this.$util.showToast({
											title: '授权失败'
										});
									}
								}
							});
						}
					}
				});
			},
			// 获取位置授权
			authCLcation() {
				uni.showLoading({
					title: '获取当前位置'
				})
				let that = this
				// 位置授权
				uni.authorize({
					scope: 'scope.userLocation',
					success: res => {
						uni.hideLoading()
						// 获取当前位置的经纬度
						uni.getLocation({
							success: res => {
								this.selLatitude = res.latitude
								this.selLongitude = res.longitude
							},
							fail: err => {
								this.$util.showToast({
									title: '获取位置失败'
								})
								this.addFn()
								uni.hideLoading()
							}
						})
					},
					fail: err => {
						console.log(err, 'err');
						uni.hideLoading()
					},
					complete: fun => {
						uni.hideLoading()
					}
				})
			},
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
	.bodySty {
		width: 750rpx;
		opacity: 1;
		background: #ffffff;
		border-radius: 30rpx 30rpx 0 0;
		// background-color: rebeccapurple;
		// padding-top: 50rpx;
		padding-bottom: 30rpx;
	}

	.iptSty {
		width: 600rpx;
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

	.canvasBodyA {
		box-sizing: border-box;
		width: 100%;
		height: 386rpx;
		background-color: #f6f7fb;
		border-radius: 10rpx;
	}

	.sfzSty {
		image {
			width: 226rpx;
			height: 177.16rpx;
			border-radius: 10rpx;
			margin-right: 20rpx;
		}
	}

	.btnA {
		// width: 330rpx;
		height: 96rpx;
		background-color: #ff4d1a;
		border-radius: 96rpx;
		text-align: center;
		line-height: 96rpx;
		color: #fff;
		font-size: 28rpx;
		border: 1rpx solid #ff4d1a;
		width: 100%;
	}

	.btnC {
		margin-right: 20rpx;
		width: 100%;
		// width: 330rpx;
		height: 96rpx;
		background-color: #fff;
		border-radius: 96rpx;
		text-align: center;
		line-height: 96rpx;
		color: #FF4D1A;
		font-size: 28rpx;
		border: 1rpx solid #FF4D1A;
	}

	.btnB {
		margin-right: 20rpx;
		width: 100%;
		// width: 330rpx;
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