<template>
	<view>
		<view class="address p-32 bor-24" v-if="needAddress">
			<view class="acea-row row-middle row-between">
				<view class="">我的收获地址</view>
				<view class="acea-row row-middle" @click="$util.redirectTo('/otherpages/address/address')">
					<image src="/otherpages/static/edit.png" class="w-24 h-24 m-r-4" mode=""></image>
					切换收货地址
				</view>
			</view>
			<view class="" v-if="orderinfo.member_address">
				<view class="">{{orderinfo.member_address.name}} {{orderinfo.member_address.mobile}}</view>
				<view class="">
					{{orderinfo.member_address.full_address.split('-').join('')+orderinfo.member_address.address}}
				</view>
			</view>
			<view class="acea-row row-middle row-center h-174" v-else
				@click="$util.redirectTo('/otherpages/address/address')">
				<image src="/otherpages/static/map7.png" class="w-26 h-26" mode=""></image>
				<text class="font-26 m-r-10 m-l-10">请选择收货地址</text>
				<uv-icon name="arrow-right" color="#999999" size="18"></uv-icon>
			</view>
		</view>
		<view class="">
			<view v-for="(item,index) in orderinfo.shop_goods_list" class="p-32 items" :key="index">
				<view class="acea-row row-middle row-between">
					<view class="acea-row row-middle font-32 font-weight-6">
						<image src="/static/stores.png" class="w-32 h-32 m-r-8" mode=""></image>
						{{item.site_name}}
						<view class="go m-l-8">
							<uv-icon color="#555555" size="8" name="arrow-right"></uv-icon>
						</view>
					</view>
				</view>
				<view class="acea-row row-middle m-t-28 row-between" v-for="(items,indexs) in item.goods_list"
					:key="indexs">
					<image :src="$util.img(items.sku_image)" class="w-140 h-140 bor-16" mode=""></image>
					<view class="widths h-140 acea-row row-between row-column">
						<view class="font-28 text-more-2">{{items.sku_name}}</view>
						<view class="acea-row row-middle row-between">
							<view class="points" v-if="info1.goodstype==3">
								￥
								<text>{{items.price}}</text>
								<text class="line">|</text>
								<text class="font-30 font-weight-6">{{items.point}}</text>
								积分
							</view>
							<view class="points" v-if="info1.goodstype==2">
								<text class="font-30 font-weight-6">{{items.point}}</text>
								积分
							</view>
							<view class="points" v-if="info1.goodstype==1">
								￥
								<text>{{items.price}}</text>

							</view>
							<view class="nums">x{{items.num}}</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="mians m-t-20">
			<view class="cuts acea-row row-middle row-between" v-if="info1.goodstype==3">
				<view :class="{'on1':check==0}" @click="check=0">
					<text>现金支付</text>
					<image src="/static/cl.png" v-if="check==0" mode=""></image>
				</view>
				<view :class="{'on1':check==1}" @click="check=1">
					<text>积分支付</text>
					<image src="/static/cr.png" v-if="check==1" mode=""></image>
					<image src="/static/icon5.png" class="icon5" mode=""></image>
				</view>
			</view>
			<view class="infos">
				<view class="acea-row row-middle row-between">
					<view class="font-26">商品总额</view>
					<view class="font-26 font-weight-6" v-if="check==1">{{orderinfo.pay_point}}积分</view>
					<view class="font-26 font-weight-6" v-else>¥{{orderinfo.pay_money}}</view>
				</view>
				<view class="acea-row row-middle row-between" v-if="check==0 && Number(orderinfo.delivery_money)>0">
					<view class="font-26">运费</view>
					<view class="font-26 font-weight-6">¥{{orderinfo.delivery_money}}</view>
				</view>
				<view class="acea-row row-middle row-between" v-if="check==0 && Number(orderinfo.promotion_money)>0">
					<view class="font-26">优惠金额</view>
					<view class="font-26 font-weight-6" style="color:#ff4d4f;">-¥{{orderinfo.promotion_money}}</view>
				</view>
				<view class="acea-row row-middle row-between" v-if="check==0 && Number(orderinfo.coupon_money)>0">
					<view class="font-26">优惠券</view>
					<view class="font-26 font-weight-6" style="color:#ff4d4f;">-¥{{orderinfo.coupon_money}}</view>
				</view>
				<view class="acea-row row-middle row-between" v-if="deliveryTypeName">
					<view class="font-26">配送方式</view>
					<view class="font-26 font-weight-6">{{deliveryTypeName}}</view>
				</view>
				<view class="acea-row row-middle row-between" v-if="check==1">
					<view class="font-26">可用积分</view>
					<view class="font-26 font-weight-6">{{orderinfo.member_account.point}}积分</view>
				</view>
				<view class="acea-row row-middle row-between" v-if="orderinfo.member_level_name!='普通会员'">
					<view class="font-26">会员折扣
					</view>
					<view class="font-26 font-weight-6">
						{{orderinfo.member_level_name}}享{{orderinfo.member_level_consume_discount}}折
					</view>
				</view>
				<view class="acea-row row-middle row-right topline">
					<text class="font-26 font-weight-6">合计：</text>
					<template v-if='check==0'>
						<text class="price">￥{{orderinfo.pay_money}}</text>
						<text class="linth font-26 m-l-12"
							v-if="orderinfo.member_level_name!='普通会员'">￥{{orderinfo.goods_money_yuan}}</text>
					</template>
					<template v-if='check==1'>
						<text class="price">{{orderinfo.pay_point}}积分</text>
						<text class="linth font-26 m-l-12"
							v-if="orderinfo.member_level_name!='普通会员'">{{orderinfo.pay_point_yuan}}积分</text>
					</template>
				</view>
			</view>

		</view>
		<view class="infos1">
			<view class="acea-row row-middle row-between">
				<view class="font-32 font-weight-4">支付方式</view>
				<view class="font-32 font-weight-4">{{check==0?'线上支付':'积分支付'}}</view>
			</view>
		</view>

		<!-- 到店核销类（到店自提/到店消费/预约到店）：核销门店 -->
		<view class="infos1" v-if="isStoreTrade">
			<view class="acea-row row-middle row-between m-b-16">
				<view class="font-32 font-weight-4">{{storeTitle}}</view>
				<view class="font-24" style="color:#999;">到店出示核销码核销</view>
			</view>
			<view class="store-item" v-for="(s,idx) in storeList" :key="s.store_id" :class="{active: idx==storeIndex}" @click="selectStore(idx)">
				<view class="store-name">{{s.store_name}}</view>
				<view class="store-addr">{{s.address||s.full_address||''}}</view>
				<view class="store-check" v-if="idx==storeIndex">✓</view>
			</view>
			<view class="font-24" style="color:#999;" v-if="!storeList.length">该商品暂无可用门店</view>
			<view class="font-24" style="color:#999;" v-else-if="tradeType==0">选择门店后按门店自提配送，未选择则默认快递配送</view>
		</view>

		<!-- 到店消费：自定义金额 -->
		<view class="infos1" v-if="tradeType==1">
			<view class="acea-row row-middle row-between m-b-16">
				<view class="font-32 font-weight-4">消费金额</view>
				<view class="font-24" style="color:#999;">到店支付，可扫码付款</view>
			</view>
			<view class="acea-row row-middle amount-box" v-if="allowCustom">
				<text class="font-28">￥</text>
				<input class="font-32 amount-input" type="digit" v-model="customAmount" placeholder="请输入消费金额" placeholder-class="ph" />
				<text class="font-24 m-l-8" style="color:#999;">元</text>
			</view>
			<view class="font-24" style="color:#999;" v-if="allowCustom">
				{{minAmount>0?'起付'+minAmount+'元':''}}{{maxAmount>0?(minAmount>0?'，':'（')+'最高'+maxAmount+'元':''}}{{minAmount>0&&maxAmount>0?'，不填则按商品价格':''}}
			</view>
			<view class="font-26" v-else>按商品价格结算：¥{{orderinfo.pay_money}}</view>
		</view>

		<!-- 预约到店：日期 + 时段 -->
		<view class="infos1" v-if="tradeType==2">
			<view class="font-32 font-weight-4 m-b-16">预约到店</view>
			<scroll-view scroll-x class="date-scroll" v-if="dateList.length">
				<view class="acea-row date-item" v-for="(d,idx) in dateList" :key="d.date" :class="{active: idx==dateIndex}" @click="selectDate(idx)">
					<view class="date-day">{{d.date.slice(5)}}</view>
					<view class="date-week">{{d.week_name}}</view>
				</view>
			</scroll-view>
			<view class="font-24" style="color:#999;" v-else>该商品暂无可预约日期</view>

			<view class="font-28 m-t-20 m-b-12" v-if="slotList.length">选择时段</view>
			<view class="slot-wrap" v-if="slotList.length">
				<view class="slot-item" v-for="s in slotList" :key="s.start_time" :class="{active: s.start_time==startTime, disabled: !s.can_select}" @click="selectSlot(s)">
					{{s.slot_text}}
					<text class="font-20" v-if="!s.can_select"> (已约满)</text>
					<text class="font-20" v-else> (余{{s.remain_num}})</text>
				</view>
			</view>
			<view class="font-24" style="color:#999;" v-if="!slotList.length">该门店当日暂无可预约时段</view>

			<view class="font-28 m-t-20" v-if="dateList.length">预约人信息</view>
			<view class="info-row" v-if="dateList.length">
				<input class="info-input" v-model="appointment.user_name" placeholder="请输入联系人" placeholder-class="ph" />
				<input class="info-input" v-model="appointment.mobile" type="number" placeholder="请输入联系电话" placeholder-class="ph" />
			</view>
			<view class="font-24" style="color:#999;">迟到可正常履约，超时未支付将自动释放名额</view>
		</view>

		<view class="h-300"></view>
		<view class="btms iphone-x">
			<view class="acea-row row-middle row-between">
				<view class="" v-if="check==1">
					待付积分
					<text>{{orderinfo.pay_point}}</text>
				</view>
				<view v-else class="">
					待付金额
					<text class="font-24">¥</text>
					<text>{{orderinfo.pay_money}}</text>
				</view>
				<view class="btn1" @click="submit">提交订单</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		payment,
		ordercalculate,
		pointcreate,
		storecreate,
		huifupay,
		Gdetail,
		slotList
	} from "@/common/js/api.js"
	export default {
		data() {
			return {
				check: 0,
				info1: {},
				orderinfo: {},
				//交易类型
				tradeType: 0,
				allowCustom: false,
				minAmount: 0,
				maxAmount: 0,
				customAmount: '',
				storeList: [],
				storeIndex: 0,
				dateList: [],
				dateIndex: 0,
				slotList: [],
				startTime: '',
				appointment: { user_name: '', mobile: '', remark: '' }
			};
		},
		async onLoad() {
			this.info1 = uni.getStorageSync('orderCreateData')
			if (this.info1.goodstype == 1) {
				this.check = 0
			}
			if (this.info1.goodstype == 2) {
				this.check = 1
			}
			this.tradeType = Number(this.info1.trade_type || 0);
		},
		async onShow() {
			await this.payment()
			//结算数据就绪后再按交易类型拉取门店/预约/自定义金额配置
			await this.initTradeInfo()
			await this.ordercalculate()
		},
		computed: {
			//是否到店扫码核销类（到店自提/到店消费/预约到店）
			isStoreTrade() {
				return [0, 1, 2].includes(this.tradeType);
			},
			//是否需要收货地址：快递邮寄必须填收货地址；到店类选择了核销门店则不需要
			needAddress() {
				if (this.tradeType === 3) return true;
				if (this.isStoreTrade && this.storeList.length && this.storeIndex >= 0) return false;
				return this.info1.goodstype == 3;
			},
			//核销门店区块标题
			storeTitle() {
				if (this.tradeType === 2) return '到店门店';
				if (this.tradeType === 1) return '消费门店';
				return '自提门店';
			},
			//配送方式展示文案
			deliveryTypeName() {
				if (this.tradeType === 3) return '快递邮寄';
				if (this.tradeType === 4) return '同城配送';
				if (this.tradeType === 1 || this.tradeType === 2) return '到店核销';
				if (this.tradeType === 0) {
					if (this.storeList.length) return '门店自提';
					return '快递邮寄';
				}
				return '';
			}
		},
		methods: {
			//初始化交易类型相关信息
			async initTradeInfo() {
				//0-到店自提也是有效交易类型，不能按 falsy 提前返回
				if (![0, 1, 2, 3, 4].includes(this.tradeType)) return;
				//购物车结算链路 info1 无 sku_id，回退取结算商品列表首个规格
				let goods_id = this.info1.goods_id || 0;
				let sku_id = this.info1.sku_id || 0;
				if (!sku_id) {
					const firstShop = (this.orderinfo.shop_goods_list || [])[0];
					const firstGoods = firstShop && firstShop.goods_list && firstShop.goods_list[0];
					if (firstGoods) {
						sku_id = firstGoods.sku_id || 0;
						goods_id = firstGoods.goods_id || goods_id;
					}
				}
				//自定义金额配置
				if (this.tradeType === 1) {
					await this.loadGoodsTrade(goods_id, sku_id);
				}
				//到店门店（到店自提/到店消费/预约到店均需到店扫码核销）
				if ([0, 1, 2].includes(this.tradeType)) {
					await this.loadStoreList(goods_id, sku_id);
				}
				//预约日期与时段
				if (this.tradeType === 2) {
					await this.loadDateList(goods_id, sku_id);
				}
			},
			//读取商品交易配置（允许自定义金额等）
			loadGoodsTrade(goods_id, sku_id) {
				return new Promise(resolve => {
					if (!sku_id) return resolve();
					Gdetail({ sku_id }).then(res => {
						const d = (res.data && res.data.goods_sku_detail) || {};
						const ti = d.trade_info || {};
						this.allowCustom = Number(ti.allow_custom_amount) === 1;
						this.minAmount = Number(ti.min_order_amount || 0);
						this.maxAmount = Number(ti.max_order_amount || 0);
						resolve();
					}).catch(() => resolve());
				});
			},
			//门店列表
			loadStoreList(goods_id, sku_id) {
				return new Promise(resolve => {
					Gdetail({ sku_id }).then(res => {
						const d = (res.data && res.data.goods_sku_detail) || {};
						const ti = d.trade_info || {};
						this.storeList = ti.store_list || [];
						//优先使用详情页选中的门店
						if (this.info1.store_id) {
							const idx = this.storeList.findIndex(s => Number(s.store_id) === Number(this.info1.store_id));
							if (idx > -1) this.storeIndex = idx;
						}
						resolve();
					}).catch(() => resolve());
				});
			},
			//预约日期
			loadDateList(goods_id, sku_id) {
				return new Promise(resolve => {
					slotList({ goods_id, sku_id, days: 7 }).then(res => {
						this.dateList = (res.data || []).filter(d => (d.store_list || []).length);
						if (this.dateList.length) {
							this.dateIndex = 0;
							this.loadSlotList();
						}
						resolve();
					}).catch(() => resolve());
				});
			},
			//切换日期
			selectDate(idx) {
				this.dateIndex = idx;
				this.startTime = '';
				this.loadSlotList();
			},
			//切换门店
			selectStore(idx) {
				this.storeIndex = idx;
				this.startTime = '';
				this.loadSlotList();
			},
			//选中时段
			selectSlot(s) {
				if (!s.can_select) {
					uni.showToast({ title: '该时段不可预约', icon: 'none' });
					return;
				}
				this.startTime = s.start_time;
			},
			//加载时段
			loadSlotList() {
				const date = this.dateList[this.dateIndex];
				const store = this.storeList[this.storeIndex];
				if (!date || !store) {
					this.slotList = [];
					return;
				}
				slotList({
					goods_id: this.info1.goods_id || 0,
					sku_id: this.info1.sku_id || 0,
					store_id: store.store_id,
					date: date.date,
					num: this.info1.num || 1
				}).then(res => {
					this.slotList = res.data || [];
					this.startTime = '';
				});
			},
			//提交前校验
			validateTrade() {
				if (this.tradeType === 2) {
					if (!this.dateList.length) {
						uni.showToast({ title: '该商品暂无可预约日期', icon: 'none' });
						return false;
					}
					if (!this.startTime) {
						uni.showToast({ title: '请选择预约时段', icon: 'none' });
						return false;
					}
					if (!this.appointment.user_name || !this.appointment.mobile) {
						uni.showToast({ title: '请填写联系人与电话', icon: 'none' });
						return false;
					}
				}
				if (this.tradeType === 1 && this.allowCustom && this.customAmount) {
					const v = Number(this.customAmount);
					if (isNaN(v) || v <= 0) {
						uni.showToast({ title: '请输入正确的金额', icon: 'none' });
						return false;
					}
					if (this.minAmount > 0 && v < this.minAmount) {
						uni.showToast({ title: `金额不能低于${this.minAmount}元`, icon: 'none' });
						return false;
					}
					if (this.maxAmount > 0 && v > this.maxAmount) {
						uni.showToast({ title: `金额不能高于${this.maxAmount}元`, icon: 'none' });
						return false;
					}
				}
				return true;
			},
			pointgi() {
				let delivery = JSON.stringify(this.orderinfo.delivery);
				let params = {
					sku_id: this.info1.sku_id || '',
					num: this.info1.num || '',
					pay_type: this.check == 0 ? 1 : 2,
					delivery: delivery,
					cart_ids: this.info1.cart_ids,
					video_id:this.info1.video_id||''
				}
				//交易类型参数（到店类门店核销 / 快递邮寄 / 自定义金额 / 预约）
				this.fillTradeParams(params);
				pointcreate(params).then(res => {
					if (res.code == 0) {
						uni.showToast({
							title: '购买成功',
							success: () => {
								uni.navigateTo({
									url: '/otherpages/order/order'
								})
							}
						})
					}else{
						uni.showToast({
							title:res.message,
							icon:'none'
						})
					}
				})
			},
			monpay() {
				let delivery = JSON.stringify(this.orderinfo.delivery);
				let params = {
					sku_id: this.info1.sku_id || '',
					num: this.info1.num || '',
					delivery: delivery,
					cart_ids: this.info1.cart_ids,
					video_id:this.info1.video_id||''
				}
				//交易类型参数
				this.fillTradeParams(params);
				storecreate(params).then(res => {
					if (res.code == 0) {
						//到店消费：支持顾客扫商家收款码
						if (this.tradeType === 1) {
							uni.showModal({
								title: '下单成功',
								content: '可到店出示付款码由商家扫码收款，或在小程序内完成支付',
								confirmText: '去支付',
								cancelText: '查看订单',
								success: (r) => {
									if (r.confirm) {
										//拉起收银台支付
										huifupay({ out_trade_no: res.data }).then(resA => {
											if (resA.code == 0) {
												this.topay(resA);
											} else {
												uni.showToast({ title: resA.message || '支付失败', icon: 'none' });
											}
										});
									} else {
										uni.navigateTo({ url: '/otherpages/order/order' });
									}
								}
							});
							return;
						}
						let params1 = {
							out_trade_no: res.data
						}
						huifupay(params1).then(resA => {
							if (resA.code == 0) {
								this.topay(resA)
							}
							else{
								uni.showToast({
									title:res.message,
									icon:'none'
								})
							}
						})
					} else {
						uni.showToast({
							title: res.message || '下单失败',
							icon: 'none'
						})
					}
				})
			},
			//填充交易类型相关参数
			fillTradeParams(params) {
				if (![0, 1, 2, 3, 4].includes(this.tradeType)) return;
				const store = this.storeList[this.storeIndex];
				const deliveryObj = this.orderinfo.delivery || {};

				//后端 OrderCreate::delivery() 按 $data['delivery'][$site_id] 逐店取配送参数，
				//而 delivery 的键来自后端 shop_goods_list（即店铺 site_id）。
				//购物车/多店铺链路 orderinfo 没有单一 site_id，若直接取 info1.site_id 会得到 0，
				//导致 delivery_type / store_id 被后端静默丢弃、到店类下单不带门店。
				//这里改为对已有店铺键逐个下发交易类型约束。
				let siteKeys = Object.keys(deliveryObj);
				if (!siteKeys.length) {
					const fallbackKey = this.orderinfo.site_id || this.info1.site_id;
					if (fallbackKey) {
						deliveryObj[fallbackKey] = {};
						siteKeys = [fallbackKey];
					}
				}
				if (!siteKeys.length) return;

				siteKeys.forEach(siteKey => {
					const cur = deliveryObj[siteKey] || {};
					//到店消费(1)/预约到店(2)强制门店核销配送；快递邮寄(3)强制物流；外卖配送(4)强制同城
					//到店自提(0)不强制：选了核销门店走门店自提，未选则沿用默认快递配送
					if (this.tradeType === 1 || this.tradeType === 2) {
						cur.delivery_type = 'store';
						if (store) cur.store_id = store.store_id;
					} else if (this.tradeType === 3) {
						cur.delivery_type = 'express';
						delete cur.store_id;
					} else if (this.tradeType === 4) {
						cur.delivery_type = 'local';
						if (store) cur.store_id = store.store_id;
					} else if (this.tradeType === 0 && store) {
						//到店自提：仅在用户主动选择核销门店时改为门店自提配送
						cur.delivery_type = 'store';
						cur.store_id = store.store_id;
					}
					deliveryObj[siteKey] = cur;
				});

				this.orderinfo.delivery = deliveryObj;
				params.delivery = JSON.stringify(deliveryObj);

				if (this.tradeType === 1 && this.allowCustom && this.customAmount) {
					params.custom_amount = this.customAmount;
				}
				if (this.tradeType === 2) {
					const date = this.dateList[this.dateIndex];
					params.appointment = JSON.stringify({
						appointment_date: date ? date.date : '',
						start_time: this.startTime,
						num: this.info1.num || 1,
						user_name: this.appointment.user_name,
						mobile: this.appointment.mobile,
						remark: this.appointment.remark || ''
					});
				}
				params.pay_scene = this.tradeType === 1 ? 1 : 0;
			},
			submit() {
				//交易类型校验（预约时段/自定义金额）
				if (!this.validateTrade()) return;
				if (this.info1.goodstype == 1) {
					this.monpay()
					return
				}
				if (this.info1.goodstype == 2) {
					this.pointgi()
				}
				// if (this.info1.goodstype != 1) {

				// } else {

				// }
				if (this.check == 1 && this.info1.goodstype == 3) {
					this.pointgi()
					return
				}
				if (this.check == 0 && this.info1.goodstype == 3) {
					this.monpay()
					return
				}

			},
			topay(res) {
				uni.requestPayment({
					provider: 'wxpay',
					timeStamp: res.data.timeStamp,
					nonceStr: res.data.nonceStr,
					package: res.data.package,
					signType: res.data.signType,
					paySign: res.data.paySign,
					success: () => {
						uni.showToast({
							title: '购买成功',
							success: () => {
								uni.navigateTo({
									url: '/otherpages/order/order'
								})
							}
						})
					},
					fail: err => {
						uni.showToast({
							title: err,
							icon:'none',
							success: () => {
								uni.navigateTo({
									url: '/otherpages/order/order'
								})
							}
						})
					}
				})
			},
			// 处理结算订单数据
			handlePaymentData() {
				this.orderinfo.delivery = {};
				this.orderinfo.invoice = {};
				this.orderinfo.coupon = {};
				this.orderinfo.buyer_message = {};
				this.orderinfo.is_balance = 0;
				this.orderinfo.pay_password = '';

				var data = this.orderinfo;

				let h = new Date().getHours().toString();
				let m = new Date().getMinutes().toString();
				if (h.length == 1) {
					h = '0' + h;
				}
				if (m.length == 1) {
					m = '0' + m;
				}
				let nowTime = h + ':' + m;
				var weeks = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];

				// 获取时间，必须是字符串,跟后端一致
				let index_weeks = ['0', '1', '2', '3', '4', '5', '6'];
				let curr_week = index_weeks[new Date().getDay()];

				Object.keys(data.shop_goods_list).forEach((key, index) => {
					let siteItem = data.shop_goods_list[key];

					// 店铺配送方式
					this.orderinfo.delivery[key] = {};

					if (siteItem.express_type[0] != undefined) {
						this.orderinfo.delivery[key].delivery_type = siteItem.express_type[0].name;
						this.orderinfo.delivery[key].delivery_type_name = siteItem.express_type[0].title;
						this.orderinfo.delivery[key].store_id = 0;

						// 如果默认配送方式是门店配送模拟点击门店tab选项
						if (siteItem.express_type[0].name == 'store') {
							if (siteItem.express_type[0].store_list[0] != undefined) {
								this.orderinfo.delivery[key].store_id = siteItem.express_type[0].store_list[0]
									.store_id;
							}
						}
					}

					if (data.is_virtual == 0 && siteItem.local_config) {

						// 是否显示时间选择
						if (siteItem.local_config.info && siteItem.local_config.info.time_is_open == 1) {
							this.orderinfo.delivery[key].showTimeBar = true;
							this.orderinfo.delivery[key].buyer_ask_delivery_time = nowTime;
							// 当日是否支持配送
							if (siteItem.local_config.info.time_week.length == 0 ||
								siteItem.local_config.info.time_week.length == 7 ||
								siteItem.local_config.info.time_week.indexOf(curr_week) > -1) {
								this.orderinfo.delivery[key].canLocalDelicery = true;
							} else {
								this.orderinfo.delivery[key].canLocalDelicery = false;
							}

							if (siteItem.local_config.info.time_type == 0) {
								this.orderinfo.delivery[key].deliveryWeek = "全天";
							} else if (siteItem.local_config.info.time_week.length > 0) {
								if (siteItem.local_config.info.time_week.length == 7) {
									this.orderinfo.delivery[key].deliveryWeek = "全天";
									this.orderinfo.delivery[key].showTime = false;
								} else {
									this.orderinfo.delivery[key].showTime = true;
									// 判断配送时间是连续还是间隔
									var timeWeek = siteItem.local_config.info.time_week;
									var is_interval = false; // 是否间隔
									for (var i = 0; i < timeWeek.length; i++) {
										if ((i + 1) < timeWeek.length) {
											var difference = timeWeek[i + 1] - timeWeek[i];
											if (difference > 1) {
												is_interval = true;
												break;
											}
										}
									}

									if (is_interval) {
										var temp = [];
										for (var i = 0; i < timeWeek.length; i++) {
											temp.push(weeks[timeWeek[i]]);
										}
										this.orderinfo.delivery[key].deliveryWeek = temp.join("、");
									} else {
										this.orderinfo.delivery[key].deliveryWeek = weeks[timeWeek[0]] + "至" +
											weeks[timeWeek[timeWeek.length -
												1]];
									}
								}
							} else {
								this.orderinfo.delivery[key].deliveryWeek = "店铺未设置配送时间";
							}

							// picker组件时间起始
							let start_time = siteItem.local_config.info.start_time;
							this.orderinfo.delivery[key].start_time = this.getTimeStr(start_time);

							let end_time = siteItem.local_config.info.end_time;
							this.orderinfo.delivery[key].end_time = this.getTimeStr(end_time);

							var current_time = new Date(this.$util.timeStampTurnTime(data.timestamp));
							var hour = current_time.getHours();
							var minute = current_time.getMinutes();

							var start_hour = parseInt(this.orderinfo.delivery[key].start_time.split(":")[0]);
							var start_minute = parseInt(this.orderinfo.delivery[key].start_time.split(":")[1]);

							var end_hour = parseInt(this.orderinfo.delivery[key].end_time.split(":")[0]);
							var end_minute = parseInt(this.orderinfo.delivery[key].end_time.split(":")[1]);

							// 检测当天是否能够配送，然后判断送达时间。不在配送时间当日不能下单，例：配送时间是周一到周五，那么周末不能下单，周一到周五可以下单
							if (this.orderinfo.delivery[key].canLocalDelicery) {

								// 判断是否全天
								if (!(start_hour == end_hour && start_minute == end_minute)) {

									// 当前时间早于配送时间，送达时间：开始时间~结束时间
									if ((hour < start_hour) || (hour == start_hour && minute < start_minute)) {
										this.orderinfo.delivery[key].buyer_ask_delivery_time = (start_hour
												.toString().length == 1 ? "0" +
												start_hour :
												start_hour) + ':' +
											(start_minute.toString().length == 1 ? "0" + start_minute :
												start_minute);
									}

									// if (((hour > start_hour && hour < end_hour) || (hour == start_hour && minute > start_minute) || (hour ==
									// 		start_hour && minute >= start_minute && hour < end_hour))) {
									// }

									// 当前时间晚于配送时间，送达时间隐藏，不能下单
									if ((hour > end_hour) || (hour == end_hour && minute > end_minute)) {
										this.orderinfo.delivery[key].canLocalDelicery = false;
									}
								}

							}

						} else {
							this.orderinfo.delivery[key].showTimeBar = false;
							this.orderinfo.delivery[key].deliveryWeek = "店铺未开启配送时间";
						}

					}

					siteItem.goods_list.forEach((v) => {
						if (typeof(v.sku_spec_format) == 'string') {
							if (v.sku_spec_format) {
								v.sku_spec_format = JSON.parse(v.sku_spec_format);
							} else {
								v.sku_spec_format = [];
							}
						}
					});

					// 店铺优惠券
					this.orderinfo.coupon[key] = {};
					if (siteItem.coupon_list[0] != undefined) {
						this.orderinfo.coupon[key].coupon_id = siteItem.coupon_list[0].coupon_id;
						this.orderinfo.coupon[key].coupon_money = siteItem.coupon_list[0].money;
						this.selectCouponId = siteItem.coupon_list[0].coupon_id;
						this.selectCouponMoney = siteItem.coupon_list[0].coupon_id;
					}

					siteItem.promotionExits = siteItem.promotion && Object.keys(siteItem.promotion).length > 0;

					this.orderinfo.buyer_message[key] = '';
				});

				if (data.is_virtual) this.orderinfo.member_address = {
					mobile: ''
				};

				// 平台优惠券
				if (data.platform_coupon_list.length > 0) {
					data.platform_coupon_id = data.platform_coupon_list[0].platformcoupon_id;
					data.platform_coupon_money = data.platform_coupon_list[0].money;

					this.orderinfo.platform_coupon_id = data.platform_coupon_list[0].platformcoupon_id;
					this.orderinfo.platform_coupon_money = data.platform_coupon_list[0].money;

					this.selectPlatCouponId = data.platform_coupon_list[0].platformcoupon_id;
					this.selectPlatCouponMoney = data.platform_coupon_list[0].money;
				}

				Object.assign(data, this.orderinfo);
				this.ordercalculate();
			},
			async payment() {
				let params = {
					sku_id: this.info1.sku_id || '',
					num: this.info1.num || '',
					cart_ids: this.info1.cart_ids
				}
				await payment(params).then(res => {
					this.orderinfo = res.data
					this.handlePaymentData()
				})
			},
			async ordercalculate() {
				let params = {
					sku_id: this.info1.sku_id || '',
					num: this.info1.num || '',
					cart_ids: this.info1.cart_ids
				}
				await ordercalculate(params).then(res => {
					if (res.code >= 0) {
						this.orderinfo.member_address = res.data.member_address;
						this.orderinfo.delivery_money = res.data.delivery_money;
						this.orderinfo.coupon_money = res.data.coupon_money;
						this.orderinfo.invoice_money = res.data.invoice_money;
						this.orderinfo.invoice_delivery_money = res.data.invoice_delivery_money;
						this.orderinfo.promotion_money = res.data.promotion_money;
						this.orderinfo.order_money = res.data.order_money;
						this.orderinfo.balance_money = res.data.balance_money;
						this.orderinfo.pay_money = res.data.pay_money;
						this.orderinfo.goods_money = res.data.goods_money;
						this.orderinfo.platform_coupon_money = res.data.platform_coupon_money;
						Object.keys(res.data.shop_goods_list).forEach((key, index) => {
							let siteItem = res.data.shop_goods_list[key];
							this.orderinfo.shop_goods_list[key].order_money = siteItem
								.order_money;
							this.orderinfo.shop_goods_list[key].pay_money = siteItem.pay_money;
							this.orderinfo.shop_goods_list[key].coupon_money = siteItem
								.coupon_money;
							this.orderinfo.shop_goods_list[key].invoice_money = siteItem
								.invoice_money;
							this.orderinfo.shop_goods_list[key].invoice_delivery_money =
								siteItem.invoice_delivery_money;
						});
						this.$forceUpdate();
					} else {
						this.$util.showToast({
							title: res.message
						});
					}
				})
			},
		}
	}
</script>

<style lang="scss">
	.btms {
		position: fixed;
		width: 100vw;
		background: #fff;
		bottom: 0;
		z-index: 909;
		left: 0;

		>view {
			padding: 20rpx 32rpx;

			>view:first-child {
				font-weight: 400;
				font-size: 24rpx;
				color: #F54040;

				text {
					font-size: 48rpx;
					color: #F54040;
					font-weight: bold;
				}
			}

			>view:last-child {
				width: 192rpx;
				height: 96rpx;
				border-radius: 48rpx;
				text-align: center;
				line-height: 96rpx;
				box-sizing: border-box;
				font-weight: 600;
				font-size: 32rpx;
			}

			.btn2 {
				border: 2rpx solid #DDDDDD;
				margin-right: 16rpx;
				color: #999999;
			}

			.btn1 {
				background: linear-gradient(90deg, #FFBB00 0%, #FA5114 100%);
				color: #fff;
			}
		}
	}

	.status {
		height: 176rpx;
		background: linear-gradient(180deg, #FFF0EB 0%, #F5F5F5 100%);

		>view:first-child {
			font-weight: 500;
			font-size: 36rpx;
			color: #F51D1D;
		}
	}

	/* 到店/预约交互 */
	.amount-box {
		background: #F7F8FA;
		border-radius: 10rpx;
		padding: 20rpx 24rpx;
		margin-bottom: 12rpx;
	}
	.amount-input {
		flex: 1;
		margin-left: 10rpx;
		font-weight: 600;
	}
	.ph {
		color: #BBB;
		font-size: 26rpx;
	}
	.date-scroll {
		white-space: nowrap;
	}
	.date-item {
		display: inline-flex;
		flex-direction: column;
		align-items: center;
		background: #F7F8FA;
		border-radius: 10rpx;
		padding: 16rpx 28rpx;
		margin-right: 16rpx;
	}
	.date-item.active {
		background: #C24E00;
	}
	.date-day {
		font-size: 26rpx;
		font-weight: 600;
	}
	.date-week {
		font-size: 22rpx;
		color: #999;
		margin-top: 4rpx;
	}
	.date-item.active .date-day,
	.date-item.active .date-week {
		color: #fff;
	}
	.store-item {
		position: relative;
		background: #F7F8FA;
		border: 2rpx solid transparent;
		border-radius: 12rpx;
		padding: 18rpx 20rpx;
		margin-bottom: 14rpx;
	}
	.store-item.active {
		border-color: #C24E00;
		background: #FFF9F4;
	}
	.store-name {
		font-size: 28rpx;
		font-weight: 600;
	}
	.store-addr {
		font-size: 22rpx;
		color: #999;
		margin-top: 6rpx;
	}
	.store-check {
		position: absolute;
		right: 20rpx;
		top: 18rpx;
		color: #C24E00;
		font-size: 28rpx;
	}
	.slot-wrap {
		display: flex;
		flex-wrap: wrap;
	}
	.slot-item {
		background: #F7F8FA;
		border: 2rpx solid transparent;
		border-radius: 8rpx;
		padding: 14rpx 22rpx;
		margin: 0 16rpx 16rpx 0;
		font-size: 26rpx;
	}
	.slot-item.active {
		border-color: #C24E00;
		color: #C24E00;
		background: #FFF9F4;
	}
	.slot-item.disabled {
		color: #BBB;
	}
	.info-row {
		display: flex;
		margin-bottom: 16rpx;
	}
	.info-input {
		flex: 1;
		background: #F7F8FA;
		border-radius: 10rpx;
		padding: 18rpx 20rpx;
		margin-right: 16rpx;
		font-size: 26rpx;
	}
	.info-input:last-child {
		margin-right: 0;
	}

	.infos1 {
		background: #fff;
		padding: 32rpx;
		margin-top: 20rpx;

		>view:first-child {
			font-weight: 600;
			font-size: 32rpx;
			color: #222222;
			padding-bottom: 4rpx;
		}

		>view:not(:first-child) {
			margin-top: 20rpx;

			>view:first-child {
				font-weight: 400;
				font-size: 26rpx;
				color: #999999;
			}

			>view:last-child {
				font-weight: 400;
				font-size: 26rpx;
				color: #1A1A1A;
			}
		}
	}

	.infos {
		background: #fff;
		padding: 16rpx 32rpx;

		.topline {
			border-top: 1rpx solid #F4F4F4;
			padding-top: 24rpx;
			margin-top: 24rpx;
		}

		>view {
			margin: 16rpx 0;
		}
	}

	.price {
		font-weight: 600;
		font-size: 26rpx;
		color: #F51D1D;
	}

	.linth {
		text-decoration: line-through;
		color: #999999;
	}

	.cuts {
		border-radius: 24rpx 24rpx 0rpx 0rpx;
		overflow: hidden;
		background: linear-gradient(180deg, #F0F0F0 0%, rgba(248, 248, 248, 0.84) 100%);

		>view {
			width: 50%;
			text-align: center;
			line-height: 90rpx;

			text {
				font-weight: 400;
				font-size: 32rpx;
				color: #222222;
			}

			position: relative;

			>image {
				position: absolute;
				width: 409rpx;
				height: 90rpx;
				left: 0;
				top: 0;
				z-index: 1;
			}
			.icon5{
				position: relative;
				width: 32rpx;
				height: 28rpx;
				margin-left: 12rpx;
			}

			text {
				position: relative;
				z-index: 5;
			}
		}

		.on1 {
			text {
				font-weight: 600;
			}
		}

		.on1::after {
			content: '';
			width: 64rpx;
			height: 6rpx;
			background: #F55606;
			border-radius: 4rpx;
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			bottom: 0;
			z-index: 5;
		}
	}

	.items {
		background: #fff;
		margin-top: 20rpx;

		.widths {
			width: calc(100% - 140rpx - 20rpx);

			.points {
				color: #F51D1D;

				text {
					font-weight: 600;
					font-size: 30rpx;
					color: #F51D1D;
				}

				.line {
					width: 36rpx;
					font-weight: 400;
					font-size: 28rpx;
					color: #DDDDDD;
					display: inline-block;
					text-align: center;
				}
			}

			.nums {
				color: #999999;
			}
		}
	}

	.go {
		width: 24rpx;
		height: 24rpx;
		background: #F0F0F0;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
	}

	.address {
		background: #fff;

		>view:first-child {
			height: 66rpx;
			background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
			border-radius: 24rpx 24rpx 0rpx 0rpx;
			padding: 0 24rpx;

			>view {
				color: #FFFFFF;
			}
		}

		>view:last-child {
			padding: 24rpx;
			background: #F8F8F8;
			border-radius: 0rpx 0rpx 24rpx 24rpx;

			>view:first-child {
				font-weight: 400;
				font-size: 24rpx;
				color: #555555;
			}

			>view:first-child::before {
				content: '收';
				background: #222222;
				border-radius: 4rpx;
				margin-right: 8rpx;
				font-weight: 400;
				font-size: 22rpx;
				color: #FFFFFF;
				padding: 0 8rpx;
				height: 32rpx;
				text-align: center;
				line-height: 32rpx;
			}

			>view:last-child {
				font-weight: 500;
				font-size: 28rpx;
				color: #222222;
				margin-top: 12rpx;
			}
		}
	}
</style>