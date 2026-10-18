<template>
	<view>
		<uv-sticky :bg-color="bgcolor">
			<nvabar type='1' title='订单详情' titleStyle='color:#222'></nvabar>
		</uv-sticky>
		<view class="acea-row row-middle row-center row-column status status1" v-if="info.order_status==-1 || (info.trade_type==2 && info.appointment_status==2)">
			<view class="acea-row row-middle" style="color: #222222;">
				<uv-icon name="close-circle" size="18" color="#222"></uv-icon>
				<text class="m-l-8">{{tradeStatusName}}</text>
			</view>
			<view class="m-t-12" style="color: #999;">{{statusDesc}}</view>
		</view>
		<view class="acea-row row-middle row-center row-column status" v-else-if="info.order_status==0">
			<view class="acea-row row-middle">
				<image src="/otherpages/static/pay.png" class="w-36 h-36 m-r-12" mode=""></image>
				{{tradeStatusName}}
			</view>
			<view class="m-t-12" style="color: #999;">{{statusDesc}}</view>
		</view>
		<view class="acea-row row-middle row-center row-column status status-pending" v-else-if="isStoreTrade && isOrderPendingVerify">
			<view class="acea-row row-middle" style="color: #FE780F;">
				<uv-icon name="scan" size="20" color="#FE780F"></uv-icon>
				<text class="m-l-8">{{tradeStatusName}}</text>
			</view>
			<view class="m-t-12" style="color: #999;">{{statusDesc}}</view>
		</view>
		<view class="acea-row row-middle row-center row-column status status1" v-else-if="[1,2,3].includes(Number(info.order_status))">
			<view class="acea-row row-middle">
				<image src="/otherpages/static/pay1.png" class="w-36 h-36 m-r-12" mode=""></image>
				{{statusTitle}}
			</view>
			<view class="m-t-12" style="color: #999;">{{statusDesc}}</view>
			<!-- 外卖自配送进度：待接单 → 备餐中 → 配送中 → 已送达 -->
			<view class="local-progress" v-if="isLocalOrder">
				<view class="lp-item" v-for="(s,idx) in localSteps" :key="s.key"
					:class="{active: s.step <= localStep, done: s.step < localStep}">
					<view class="lp-dot"></view>
					<view class="lp-name">{{s.name}}</view>
				</view>
			</view>
		</view>
		<view class="acea-row row-middle row-center row-column status status-verified" v-else-if="isOrderVerified">
			<view class="acea-row row-middle" style="color: #25B85B;">
				<uv-icon name="checkmark-circle" size="20" color="#25B85B"></uv-icon>
				<text class="m-l-8">{{tradeStatusName}}</text>
			</view>
			<view class="m-t-12" style="color: #999;">{{isStoreTrade ? '订单商品已完成核销，感谢您的光临' : '交易已完成，感谢您的支持'}}</view>
		</view>

		<!-- 预约信息卡片 -->
		<view class="verify-card appt-card" v-if="info.trade_type==2 && info.appointment_date">
			<view class="verify-header acea-row row-middle row-between">
				<view class="acea-row row-middle">
					<uv-icon name="clock" size="16" color="#C24E00"></uv-icon>
					<text class="verify-title m-l-8">预约信息</text>
				</view>
				<view class="verify-badge">{{apptStatusText}}</view>
			</view>
			<view class="appt-body">
				<view class="appt-row"><text class="k">预约时间</text><text class="v">{{info.appointment_date}} {{info.appointment_slot}}</text></view>
				<view class="appt-row" v-if="info.delivery_store_name"><text class="k">到店门店</text><text class="v">{{info.delivery_store_name}}</text></view>
				<view class="appt-row" v-if="info.appointment_user"><text class="k">联系人</text><text class="v">{{info.appointment_user}} {{info.appointment_mobile}}</text></view>
				<view class="appt-row" v-if="info.appointment_remark"><text class="k">备注</text><text class="v">{{info.appointment_remark}}</text></view>
				<view class="appt-tip">迟到可正常履约；超时未支付名额将自动释放</view>
			</view>
		</view>

		<!-- 快递物流卡片：快递邮寄(3) 及走快递发货的到店自提(0) -->
		<view class="verify-card appt-card" v-if="expressList.length">
			<view class="verify-header acea-row row-middle row-between">
				<view class="acea-row row-middle">
					<uv-icon name="car" size="16" color="#4a6b8a"></uv-icon>
					<text class="verify-title m-l-8">快递物流</text>
				</view>
				<view class="acea-row row-middle">
					<view class="verify-badge">{{ info.delivery_type_name || '快递邮寄' }}</view>
					<text class="ns-trace-entry" v-if="hasExpressNo" @click="showTrace">查看轨迹</text>
				</view>
			</view>
			<view class="appt-body">
				<view class="appt-row" v-for="(g,idx) in expressList" :key="idx">
					<text class="k">{{ g.sku_name }}</text>
					<text class="v" v-if="g.delivery_no">单号 {{ g.delivery_no }}</text>
					<text class="v" v-else style="color:#999;">待商家发货</text>
				</view>
				<view class="appt-row" v-if="!hasExpressNo">
					<text class="k">物流状态</text>
					<text class="v" style="color:#999;">商家发货后可查看快递单号</text>
				</view>
			</view>
		</view>

		<!-- 物流轨迹弹层 -->
		<view class="ns-trace-mask" v-if="traceVisible" @click="traceVisible = false"></view>
		<view class="ns-trace-panel" v-if="traceVisible">
			<view class="ns-trace-head">物流轨迹</view>
			<scroll-view scroll-y class="ns-trace-body">
				<view class="ns-trace-empty" v-if="!traceList.length">暂无物流轨迹</view>
				<view class="ns-trace-item" v-for="(t,i) in traceList" :key="i">
					<view class="ns-trace-time">{{ t.datetime }}</view>
					<view class="ns-trace-remark">{{ t.remark }}</view>
				</view>
			</scroll-view>
			<view class="ns-trace-close" @click="traceVisible = false">关闭</view>
		</view>

		<!-- 核销二维码模块 -->
		<view class="verify-card" v-if="canShowVerifyCode">
			<view class="verify-header acea-row row-middle row-between">
				<view class="acea-row row-middle">
					<image src="/otherpages/static/img2.png" class="w-36 h-36 m-r-12" mode=""></image>
					<text class="verify-title">{{verifyTitle}}</text>
				</view>
				<view class="verify-badge">{{verifyBadge}}</view>
			</view>
			<view class="verify-body acea-row row-column row-center row-middle">
				<view class="qrcode-box">
					<uv-qrcode ref="qrcode" :value="qrValue" size="340rpx" :loading="qrLoading"></uv-qrcode>
				</view>
				<view class="verify-code-num acea-row row-middle m-t-20">
					<text class="code-text">{{ formatCode(verifyCode) }}</text>
					<view class="refresh-btn acea-row row-middle m-l-16" @click="fetchVerifyCode">
						<uv-icon name="reload" size="14" color="#FF6600"></uv-icon>
						<text class="m-l-4">刷新</text>
					</view>
				</view>
				<view class="verify-tips m-t-16 acea-row row-middle">
					<uv-icon name="info-circle" size="13" color="#999999"></uv-icon>
					<text class="m-l-6">向商家出示此码核销，每次进入自动更新</text>
				</view>
			</view>
		</view>

		<!-- 已核销卡片 -->
		<view class="verify-card verified-card" v-else-if="isOrderVerified">
			<view class="verified-body acea-row row-column row-center row-middle">
				<view class="verified-stamp acea-row row-middle row-center">
					<uv-icon name="checkbox-mark" size="22" color="#25B85B"></uv-icon>
					<text class="m-l-8">已核销</text>
				</view>
				<view class="verified-desc m-t-16">该订单商品已完成线下核销提货</view>
			</view>
		</view>

		<view class="address p-32 bor-24" v-if="info.goods_type==1&&info.is_admin==1">
			<view class="acea-row row-middle row-between">
				<view class="">我的收货地址</view>
			</view>
			<view class="">
				<view class="">{{info.name}} {{info.mobile}}</view>
				<view class="">{{info.full_address.split('-').join('')}}{{info.address}}</view>
			</view>
		</view>
		<view class="">
			<view v-for="(item,index) in 1" class="p-32 items" :key="index">
				<view class="acea-row row-middle row-between">
					<view class="acea-row row-middle font-32 font-weight-6">
						<image src="/static/stores.png" class="w-32 h-32 m-r-8" mode=""></image>
						{{info.site_name}}
						<view class="go m-l-8">
							<uv-icon color="#555555" size="8" name="arrow-right"></uv-icon>
						</view>
					</view>
				</view>
				<view class="acea-row row-middle m-t-28 row-between" v-for="(item,index) in info.order_goods" :key="index">
					<image :src="$util.img(item.sku_image)" class="w-140 h-140 bor-16" mode=""></image>
					<view class="widths h-140 acea-row row-between row-column" style="flex-wrap: nowrap;">
						<view class="font-28 text-more-2">{{item.sku_name}}</view>
						<view class="acea-row row-middle row-between">
							<view class="points acea-row row-middle">
								<template v-if="info.pay_type!='POINT'">
									￥
									<text>{{item.price}}</text>
								</template>
								<template v-else>
									<text class="font-30 font-weight-6">{{item.point}}</text>
									积分
								</template>
								<view class="nums m-l-20">x{{item.num}}</view>
							</view>
							<view class="" v-if="info.order_status != -1 && !(info.trade_type == 2 && info.appointment_status == 2) && isStoreTrade && (isOrderVerified || item.delivery_status == 1)">
								<view class="order-goods-tag-verified">已核销</view>
							</view>
							<view class="" v-else-if="info.order_status != -1 && !isStoreTrade && isOrderVerified">
								<view class="order-goods-tag-verified">已完成</view>
							</view>
							<view class="" v-else-if="info.order_status != -1 && !isStoreTrade && Number(info.order_status) == 3">
								<view class="order-goods-tag-pending">{{isLocalOrder ? '配送中' : '已发货'}}</view>
							</view>
							<view class="" v-else-if="(info.order_status == -1 || (info.trade_type == 2 && info.appointment_status == 2)) && (item.refund_status == 0 || item.refund_status == -1)">
							</view>
							<view class="" v-else-if="item.refund_status == 0 || item.refund_status == -1"
								@click="goRefund(item.order_goods_id)">退款</view>
							<view @click="goRefundDetail(item.order_goods_id)" v-else>
								<view class="order-box-btn">查看退款</view>
							</view>
						</view>

					</view>
				</view>
			</view>
		</view>
		<view class="mians m-t-20">
			<!-- 	<view class="cuts acea-row row-middle row-between">
				<view :class="{'on1':check==0}" @click="check=0">
					<text>现金支付</text>
					<image src="/static/cl.png" v-if="check==0" mode=""></image>
				</view>
				<view :class="{'on1':check==1}" @click="check=1">
					<text>积分支付</text>
					<image src="/static/cr.png" v-if="check==1" mode=""></image>
				</view>
			</view> -->
			<view class="infos">
				<view class="font-32 font-weight-6">
					{{info.pay_type_name}}
				</view>
				<view class="acea-row row-middle row-between">
					<view class="font-26">商品总额</view>
					<view class="font-26 font-weight-6" v-if="info.pay_type!='POINT'">￥{{info.order_money}}</view>
					<view class="font-26 font-weight-6" v-else>积分{{info.order_point}}</view>
				</view>
				<!-- <view class="acea-row row-middle row-between">
					<view class="font-26">会员折扣
					</view>
					<view class="font-26 font-weight-6">
						钻石会员享98折
					</view>
				</view> -->
				<view class="acea-row row-middle row-right topline">
					<text class="font-26 font-weight-6">合计：</text>
					<view class="font-26 font-weight-6 price" v-if="info.pay_type!='POINT'">￥{{info.order_money}}</view>
					<view class="font-26 font-weight-6 price" v-else>积分{{info.order_point}}</view>
					<!-- <text class="linth font-26 m-l-12">￥{{info.order_money}}</text> -->
				</view>
			</view>

		</view>
		<view class="infos1">
			<view class="">订单信息</view>
			<view class="acea-row row-middle row-between">
				<view class="font-26">订单编号</view>
				<view class="font-26 font-weight-6">
					{{info.order_no}} |
					<text @click="$util.copy(info.order_no)" class="m-l-10">复制</text>
				</view>
			</view>
			<view class="acea-row row-middle row-between">
				<view class="font-26">下单时间
				</view>
				<view class="font-26 font-weight-6">
					{{info.create_time}}
				</view>
			</view>
			<view class="acea-row row-middle row-between">
				<view class="font-26">订单交易号
				</view>
				<view class="font-26 font-weight-6">
					{{info.out_trade_no}}
				</view>
			</view>
			<view class="acea-row row-middle row-between">
				<view class="font-26">配送方式
				</view>
				<view class="font-26 font-weight-6">
					{{info.delivery_type_name}}
				</view>
			</view>
		</view>
		<view class="h-300" v-if="hasAnyBottomAction"></view>
		<view class="btms iphone-x" v-if="hasAnyBottomAction">
			<view class="acea-row row-middle row-right">
				<view class="btn2" v-if="hasAction('delete_order')" @click="doDeleteOrder">删除订单</view>
				<view class="btn2" v-if="hasAction('cancel_order')" @click="orderClose">取消订单</view>
				<view class="btn1" v-if="hasAction('pay')" @click="huifupay">立即付款</view>
				<view class="btn2" v-if="hasAction('cancel_appointment')" @click="doCancelAppointment">取消预约</view>
				<view class="btn2" v-if="hasAction('apply_refund')" @click="applyOrderRefund">申请退款</view>
				<view class="btn1" v-if="hasAction('confirm_receipt')" @click="doConfirmReceipt">确认收货</view>
				<view class="btn2" v-if="hasAction('aftersale')" @click="applyOrderRefund">申请售后</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		orderDetail,
		huifupay,
		getVerifyCode,
		cancelAppointment,
		orderClose as closeOrderApi,
		orderDelete,
		orderTakeDelivery,
		orderExpressTrace
	} from '@/common/js/api.js';
	export default {
		data() {
			return {
				check: 0,
				id: '',
				info: {},
				//物流轨迹弹层
				traceVisible: false,
				traceList: [],
				tracing: false,
				verifyCode: '',
				qrLoading: false
			};
		},
		onLoad(res) {
			this.id = res.id
			this.getinfo()
		},
		onShow() {
			if (this.info && this.canShowVerifyCode) {
				this.fetchVerifyCode();
			}
		},
		computed: {
			//是否到店扫码核销类（到店自提/到店消费/预约到店）
			isStoreTrade() {
				return [0, 1, 2].includes(Number(this.info.trade_type || 0));
			},
			//是否外卖自配送订单（order_type=3，由实际配送链路决定）
			isLocalOrder() {
				return Number(this.info.order_type || 1) === 3;
			},
			//是否处于待核销中间态（到店类已支付未核销）
			isOrderPendingVerify() {
				const st = Number(this.info.order_status || 0);
				const t = Number(this.info.trade_type || 0);
				const apptSt = Number(this.info.appointment_status);
				if (st === -1) return false;
				if (t === 2 && apptSt === 2) return false;
				return [1, 2].includes(st);
			},
			//走快递物流的商品项（快递邮寄(3)，或到店自提(0)但配送方式为快递）
			expressList() {
				const t = Number(this.info.trade_type || 0);
				if (!this.info.order_goods || !this.info.order_goods.length) return [];
				const isExpressOrder = t === 3 || (t === 0 && this.info.delivery_type === 'express');
				if (!isExpressOrder) return [];
				return this.info.order_goods;
			},
			hasExpressNo() {
				return this.expressList.some(g => !!g.delivery_no);
			},
			//外卖自配送进度步骤
			localSteps() {
				return [
					{ key: 'take', name: '商家接单', step: 1 },
					{ key: 'prep', name: '备餐中', step: 2 },
					{ key: 'send', name: '配送中', step: 3 },
					{ key: 'arrive', name: '已送达', step: 4 }
				];
			},
			//外卖当前所处步骤（1待接单 2备餐中 3配送中 4已送达）
			localStep() {
				const s = Number(this.info.order_status || 0);
				if (s === 1) return 1;
				if (s === 2) return 2;
				if (s === 3) return 3;
				if (s >= 4) return 4;
				return 0;
			},
			//是否可确认收货：快递订单已发货、外卖订单配送中（到店类由核销码完成）
			canConfirmReceipt() {
				const ot = Number(this.info.order_type || 1);
				const st = Number(this.info.order_status || 0);
				if (ot === 1) return st === 3;
				if (ot === 3) return st === 3;
				return false;
			},
			//交易状态名称（按订单类型映射，兜底历史数据快照）
			tradeStatusName() {
				const ot = Number(this.info.order_type || 1);
				const st = Number(this.info.order_status || 0);
				const t = Number(this.info.trade_type || 0);
				const apptSt = Number(this.info.appointment_status);
				if (st === -1) {
					return (t === 2 && apptSt === 2) ? '已取消' : '已关闭';
				}
				if (t === 2 && apptSt === 2) {
					return '已取消';
				}
				if (t === 2 && apptSt === 1 && st !== 2 && st !== 4 && st !== 10) {
					return '待到店';
				}
				if (this.info.trade_status_name) return this.info.trade_status_name;
				let map;
				if (ot === 3) {
					map = { 0: '待支付', 1: '待接单', 2: '备餐中', 3: '配送中', 4: '已送达', 10: '已完成', '-1': '已关闭' };
					return map[st] || '已送达';
				}
				if (ot === 2) {
					map = { 0: '待支付', 1: '待核销', 2: '待核销', 3: '待核销', 4: '已核销', 10: '已完成', '-1': '已关闭' };
					return map[st] || '已核销';
				}
				map = { 0: '待支付', 1: '待发货', 2: '待发货', 3: '已发货', 4: '已收货', 10: '已完成', '-1': '已关闭' };
				return map[st] || '已收货';
			},
			//已支付后各状态标题（按订单类型 × 订单状态）
			statusTitle() {
				return this.tradeStatusName;
			},
			//已支付后各状态说明（按订单类型 × 订单状态）
			statusDesc() {
				if (this.info && this.info.status_desc) return this.info.status_desc;
				const ot = Number(this.info.order_type || 1);
				const s = Number(this.info.order_status || 0);
				const t = Number(this.info.trade_type || 0);
				if (s === -1) {
					return (t === 2 && Number(this.info.appointment_status) === 2) ? '预约已取消，名额已释放' : '订单已关闭';
				}
				if (s === 0) return '请在限定时间内完成支付，超时将会自动取消订单';
				if (t === 0) {
					if (s === 1 || s === 2) return '订单已支付，请前往指定门店出示自提码提货';
					return '订单商品已完成提货，感谢光临';
				}
				if (t === 1) {
					if (s === 1 || s === 2) return '订单已支付，请前往门店向店员出示消费码';
					return '店内消费已核销，感谢惠顾';
				}
				if (t === 2) {
					if (s === 1 || s === 2) return '已为您锁定预约时段，请按时出示服务码';
					return '预约服务已核销完成，感谢光临';
				}
				if (ot === 3 || t === 4) {
					if (s === 1) return '商家接单后即开始备餐';
					if (s === 2) return '商家正在为您备餐，请耐心等待';
					if (s === 3) return '商家已安排配送，请保持电话畅通';
					return '外卖已送达，感谢您的惠顾';
				}
				//快递邮寄
				if (s === 1 || s === 2) return '订单已支付，商家将快递邮寄给您';
				return '商品已寄出，请留意查收';
			},
			//核销码标题（到店自提/到店消费/预约到店均为到店扫码核销）
			verifyTitle() {
				const t = Number(this.info.trade_type || 0);
				if (t === 1) return '到店消费码';
				if (t === 2) return '到店服务核销码';
				return '自提核销码';
			},
			verifyBadge() {
				const t = Number(this.info.trade_type || 0);
				if (t === 1) return '到店出示';
				if (t === 2) return '到店核销';
				return '提货核销';
			},
			//预约状态文案
			apptStatusText() {
				if (Number(this.info.order_status) === -1) {
					return Number(this.info.appointment_status) === 2 ? '已取消' : '已关闭';
				}
				const map = { 0: '待付款', 1: '已预约', 2: '已取消', 3: '已过期', 4: '已完成' };
				return map[Number(this.info.appointment_status)] || '已预约';
			},
			//订单是否已完成交易（核销/收货/送达）
			//统一以 order_status 判定：4=已核销/已收货/已送达，10=已完成
			//不可用 delivery_status 判断：外卖配送中 delivery_status=1，会误判为已完成
			isOrderVerified() {
				return this.info.order_status == 4 || this.info.order_status == 10;
			},
			canShowVerifyCode() {
				if (this.info && typeof this.info.can_verify !== 'undefined') {
					return Boolean(this.info.can_verify);
				}
				//仅到店扫码核销类需要核销码；快递邮寄/外卖配送走物流或同城配送
				if (!this.isStoreTrade) return false;
				if (this.isOrderVerified) return false;
				if (this.info.order_status == -1) return false;
				if (Number(this.info.trade_type || 0) === 2 && Number(this.info.appointment_status) === 2) return false;
				if (this.info.order_status == 0 && this.info.pay_status == 0) return false;
				return this.info.order_status == 1 || this.info.order_status == 2 || this.info.pay_status == 1;
			},
			qrValue() {
				return this.verifyCode || this.info.delivery_code || this.info.order_no || '';
			},
			bgcolor() {
				if (this.info.order_status == -1 || (this.info.trade_type == 2 && this.info.appointment_status == 2)) {
					return '#F7F7F7';
				} else if (this.info.order_status == 0) {
					return '#FFF0EB'
				} else if (this.info.order_status == 1) {
					return '#EAFEF1'
				} else if (this.info.order_status == 2) {
					return '#FFF7EE'
				} else if (this.isOrderVerified) {
					return '#F0F9EB'
				}
				return '#fff'
			},
		},
		methods: {
			hasAction(action) {
				if (this.info && this.info.actions && Array.isArray(this.info.actions)) {
					return this.info.actions.includes(action);
				}
				const st = Number(this.info.order_status);
				const t = Number(this.info.trade_type || 0);
				const apptSt = Number(this.info.appointment_status);
				if (action === 'delete_order') return st === -1 || (t === 2 && apptSt === 2) || Number(this.info.refund_status) === 3;
				if (action === 'cancel_order') return st === 0;
				if (action === 'pay') return st === 0;
				if (action === 'cancel_appointment') return t === 2 && apptSt === 1 && st === 1;
				if (action === 'apply_refund') return (st === 1 || (st === 2 && t !== 2)) && t !== 2;
				if (action === 'confirm_receipt') return this.canConfirmReceipt;
				if (action === 'aftersale') return st === 4;
				return false;
			},
			hasAnyBottomAction() {
				return this.hasAction('delete_order') ||
					this.hasAction('cancel_order') ||
					this.hasAction('pay') ||
					this.hasAction('cancel_appointment') ||
					this.hasAction('apply_refund') ||
					this.hasAction('confirm_receipt') ||
					this.hasAction('aftersale');
			},
			//删除订单
			doDeleteOrder() {
				uni.showModal({
					title: '提示',
					content: '确定要删除该订单吗？删除后将不再显示',
					success: (res) => {
						if (res.confirm) {
							uni.showLoading({ mask: true });
							orderDelete({ order_id: this.id }).then(res => {
								uni.hideLoading();
								uni.showToast({
									title: res.message || (res.code == 0 ? '删除成功' : '删除失败'),
									icon: res.code == 0 ? 'success' : 'none'
								});
								if (res.code == 0) {
									setTimeout(() => {
										uni.navigateBack();
									}, 800);
								}
							}).catch(() => uni.hideLoading());
						}
					}
				});
			},
			//取消预约
			doCancelAppointment() {
				uni.showModal({
					title: '取消预约',
					content: '确定取消该预约吗？名额将释放',
					success: r => {
						if (!r.confirm) return;
						cancelAppointment({ order_id: this.id }).then(res => {
							uni.showToast({ title: res.message || '已取消', icon: 'none' });
							this.getinfo();
						});
					}
				});
			},
			//取消订单
			orderClose() {
				uni.showModal({
					title: '取消订单',
					content: '确定取消当前订单',
					success: r => {
						if (!r.confirm) return;
						closeOrderApi({ order_id: this.id }).then(() => {
							uni.showToast({ title: '已取消', icon: 'none' });
							this.getinfo();
						});
					}
				});
			},
			//申请整单退款
			applyOrderRefund() {
				this.$util.redirectTo('/otherpages/refund/refund', { id: this.id });
			},
			//确认收货（快递邮寄/外卖配送）
			doConfirmReceipt() {
				uni.showModal({
					title: '确认收货',
					content: '确认已收到商品？',
					success: r => {
						if (!r.confirm) return;
						orderTakeDelivery({ order_id: this.id }).then(res => {
							if (res.code == 0) {
								uni.showToast({ title: '已确认收货' });
								this.getinfo();
							} else {
								uni.showToast({ title: res.message || '操作失败', icon: 'none' });
							}
						});
					}
				});
			},
			//查看快递物流轨迹
			showTrace() {
				if (this.tracing) return;
				this.tracing = true;
				orderExpressTrace({ order_id: this.id }).then(res => {
					this.tracing = false;
					if (!res || res.code != 0) {
						uni.showToast({ title: (res && res.message) || '查询物流失败', icon: 'none' });
						return;
					}
					const pkgs = res.data || [];
					let list = [];
					pkgs.forEach(p => {
						const tr = p.trace || {};
						//快递100/快递鸟统一结构：{ success:true, list:[{datetime,remark}] }
						if (tr.success && tr.list && tr.list.length) {
							list = list.concat(tr.list);
						}
					});
					//按时间倒序，最新轨迹在前
					list.sort((a, b) => String(b.datetime || '').localeCompare(String(a.datetime || '')));
					this.traceList = list;
					this.traceVisible = true;
					if (!list.length) {
						uni.showToast({ title: '暂无物流轨迹', icon: 'none' });
					}
				}).catch(() => {
					this.tracing = false;
					uni.showToast({ title: '查询物流失败', icon: 'none' });
				});
			},
			formatCode(code) {
				if (!code) return '';
				return code.replace(/(.{4})/g, '$1 ').trim();
			},
			fetchVerifyCode() {
				if (!this.id) return;
				this.qrLoading = true;
				getVerifyCode({
					order_id: this.id
				}).then(res => {
					this.qrLoading = false;
					if (res.code == 0 && res.data) {
						this.verifyCode = res.data.verify_code;
						if (res.data.is_verify) {
							this.info.order_status = 4;
							this.info.delivery_status = 1;
						}
					}
				}).catch(() => {
					this.qrLoading = false;
				});
			},
			goRefundDetail(id) {
				this.$util.redirectTo('/otherpages/refund_detail/refund_detail', {
					order_goods_id: id
				});
			},
			goRefund(id) {
				this.$util.redirectTo('/otherpages/refund/refund', {
					order_goods_id: id
				});
			},
			huifupay() {
				let params1 = {
					out_trade_no: this.info.out_trade_no
				}
				huifupay(params1).then(resA => {
					if (resA.code == 0) {
						this.topay(resA)
					}
				})

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
						this.getinfo()
					},
					fail: err => {}
				})
			},
			getinfo() {
				let params = {
					order_id: this.id
				}
				orderDetail(params).then(res => {
					this.info = res.data;
					if (this.canShowVerifyCode) {
						this.fetchVerifyCode();
					}
				});
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

			>view {
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

	.status1 {
		background: linear-gradient(180deg, #FFFFFF 0%, #F5F5F5 100%);
	}

	.status2 {
		background: linear-gradient(180deg, #EAFEF1 0%, #F5F5F5 100%);

		>view:first-child {
			color: #25B85B;
		}
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

	.status-pending {
		background: linear-gradient(180deg, #FFF7EE 0%, #F5F5F5 100%);
		>view:first-child {
			color: #FE780F;
		}
	}

	/* 外卖自配送进度 */
	.local-progress {
		display: flex;
		align-items: center;
		justify-content: space-between;
		width: 100%;
		margin-top: 28rpx;
		padding: 0 20rpx;

		.lp-item {
			position: relative;
			flex: 1;
			display: flex;
			flex-direction: column;
			align-items: center;

			&:not(:last-child)::after {
				content: '';
				position: absolute;
				top: 12rpx;
				left: calc(50% + 14rpx);
				right: calc(-50% + 14rpx);
				height: 3rpx;
				background: #E5E5E5;
			}

			.lp-dot {
				width: 24rpx;
				height: 24rpx;
				border-radius: 50%;
				background: #E5E5E5;
				border: 4rpx solid #FFFFFF;
				box-sizing: border-box;
			}

			.lp-name {
				margin-top: 10rpx;
				font-size: 22rpx;
				color: #999999;
			}

			&.active {
				.lp-dot {
					background: #FE780F;
				}
				.lp-name {
					color: #FE780F;
					font-weight: 600;
				}
				&:not(:last-child)::after {
					background: #FFD9B3;
				}
			}

			&.done {
				&:not(:last-child)::after {
					background: #FE780F;
				}
			}
		}
	}

	.status-verified {
		background: linear-gradient(180deg, #F0F9EB 0%, #F5F5F5 100%);
		>view:first-child {
			color: #25B85B;
		}
	}

	/* 预约信息卡片 */
	.appt-card {
		background: #FFF9F4;
		border: 2rpx solid #FFE2CE;
	}

	.appt-body {
		.appt-row {
			display: flex;
			justify-content: space-between;
			font-size: 26rpx;
			padding: 10rpx 0;

			.k {
				color: #999;
			}

			.v {
				color: #333;
				font-weight: 600;
			}
		}

		.appt-tip {
			font-size: 22rpx;
			color: #999;
			margin-top: 10rpx;
		}

		/* 快递物流轨迹 */
		.ns-trace-entry {
			font-size: 24rpx;
			color: #4a6b8a;
			margin-left: 16rpx;
		}

		.ns-trace-mask {
			position: fixed;
			left: 0;
			top: 0;
			right: 0;
			bottom: 0;
			background: rgba(0, 0, 0, 0.5);
			z-index: 999;
		}

		.ns-trace-panel {
			position: fixed;
			left: 0;
			right: 0;
			bottom: 0;
			z-index: 1000;
			background: #fff;
			border-radius: 24rpx 24rpx 0 0;
			padding: 32rpx;
			max-height: 70vh;
			display: flex;
			flex-direction: column;
		}

		.ns-trace-head {
			font-size: 32rpx;
			font-weight: 600;
			color: #222;
			text-align: center;
			padding-bottom: 24rpx;
			border-bottom: 1rpx solid #eee;
		}

		.ns-trace-body {
			flex: 1;
			max-height: 52vh;
			padding-top: 16rpx;
		}

		.ns-trace-item {
			padding: 16rpx 0;
			border-bottom: 1rpx solid #f5f5f5;
		}

		.ns-trace-time {
			font-size: 24rpx;
			color: #999;
		}

		.ns-trace-remark {
			font-size: 26rpx;
			color: #333;
			margin-top: 6rpx;
		}

		.ns-trace-empty {
			text-align: center;
			color: #999;
			font-size: 26rpx;
			padding: 60rpx 0;
		}

		.ns-trace-close {
			margin-top: 24rpx;
			height: 80rpx;
			line-height: 80rpx;
			text-align: center;
			background: #f5f5f5;
			border-radius: 40rpx;
			font-size: 28rpx;
			color: #333;
		}
	}

	.verify-card {
		background: #ffffff;
		border-radius: 24rpx;
		margin: 20rpx 24rpx;
		padding: 32rpx;
		box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.04);

		.verify-header {
			padding-bottom: 24rpx;
			border-bottom: 1rpx dashed #EEEEEE;

			.verify-title {
				font-size: 32rpx;
				font-weight: 600;
				color: #222222;
			}

			.verify-badge {
				font-size: 22rpx;
				color: #FF6600;
				background: #FFF0E5;
				padding: 6rpx 16rpx;
				border-radius: 20rpx;
				font-weight: 500;
			}
		}

		.verify-body {
			padding-top: 32rpx;

			.qrcode-box {
				padding: 20rpx;
				background: #ffffff;
				border: 1rpx solid #F0F0F0;
				border-radius: 16rpx;
				display: flex;
				align-items: center;
				justify-content: center;
			}

			.verify-code-num {
				.code-text {
					font-size: 36rpx;
					font-weight: 700;
					color: #222222;
					letter-spacing: 2rpx;
				}

				.refresh-btn {
					font-size: 24rpx;
					color: #FF6600;
					background: #FFF4EC;
					padding: 6rpx 16rpx;
					border-radius: 24rpx;
				}
			}

			.verify-tips {
				font-size: 22rpx;
				color: #999999;
			}
		}
	}

	.verified-card {
		background: #ffffff;
		border: 1rpx solid #E1F3D8;

		.verified-body {
			padding: 40rpx 20rpx;

			.verified-stamp {
				padding: 12rpx 32rpx;
				background: #F0F9EB;
				border: 2rpx solid #25B85B;
				border-radius: 36rpx;
				color: #25B85B;
				font-size: 32rpx;
				font-weight: 600;
			}

			.verified-desc {
				font-size: 26rpx;
				color: #666666;
			}
		}
	}

	.order-goods-tag-verified {
		font-size: 22rpx;
		color: #25B85B;
		background: #F0F9EB;
		border: 1rpx solid #B8E894;
		border-radius: 8rpx;
		padding: 4rpx 14rpx;
		font-weight: 500;
	}

	.order-goods-tag-pending {
		font-size: 22rpx;
		color: #FE780F;
		background: #FFF7EE;
		border: 1rpx solid #FFD9B3;
		border-radius: 8rpx;
		padding: 4rpx 14rpx;
		font-weight: 500;
	}
</style>