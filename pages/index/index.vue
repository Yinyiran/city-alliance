<template>
	<view>
		<view class="" style="position: relative;">
			<!-- 顶部当前店铺与切换栏 -->
			<view class="index-shop-header acea-row row-middle row-between" @click="openShopSelect">
				<view class="shop-header-left acea-row row-middle">
					<image :src="$util.img(currentShopLogo)" class="shop-logo-img" mode="aspectFill" v-if="currentShopLogo"></image>
					<image src="/static/logo.png" class="shop-logo-img" mode="aspectFit" v-else></image>
					<text class="shop-title-text text-more">{{ currentSiteName || '商家中心' }}</text>
					<uv-icon name="arrow-down-fill" size="10" color="#F5B67F" customStyle="margin-left: 8rpx; flex-shrink: 0;"></uv-icon>
				</view>
				<view class="shop-role-pill" :class="'pill-' + (isManager ? 'manager' : 'staff')" v-if="token">
					{{ currentRoleName }}
				</view>
			</view>

			<view class="part1">
				<view class="acea-row row-middle row-between">
					<view class="acea-row row-middle row-center" @click="gonext('deliveryOrder')">
						<image src="/static/img1.png" class="w-88 h-88 m-r-20" mode=""></image>
						<view class="acea-row row-middle font-32 fcolor1">
							待核销订单
							<image src="/static/go.png" class="w-16 h-16 m-l-4" mode=""></image>
						</view>
					</view>
					<view class="acea-row row-middle row-center" @click="sancode">
						<image src="/static/img2.png" class="w-88 h-88 m-r-20" mode=""></image>
						<view class="acea-row row-middle font-32 fcolor1">
							扫码核销
							<image src="/static/go.png" class="w-16 h-16 m-l-4" mode=""></image>
						</view>
					</view>
				</view>
				<view class="line1 m-t-32"></view>
				<view class="acea-row row-middle row-between m-t-32">
					<view class="acea-row row-middle" style="color: #CCCCCC;">团队总人数 (人)：<text style="color: #F5B67F;"
							class="font-40">{{total_count}}</text></view>
					<view class="acea-row row-middle" @click="gonext('teamSize',0)" style="color: #CCCCCC;">查看明细
						<image src="/static/go1.png" class="w-16 h-16 m-l-4" mode=""></image>
					</view>
				</view>
				<view class="line1 m-t-24"></view>
				<view class="acea-row row-middle row-between m-t-24">
					<view class="acea-row row-middle" style="color: #CCCCCC;">{{ isManager ? '主理人收益 (元)：' : '员工提成收益 (元)：' }}<text style="color: #F5B67F;"
							class="font-40">{{totalIncome}}</text></view>
					<view class="acea-row row-middle" @click="gonext('incomeDetail')" style="color: #CCCCCC;">查看明细
						<image src="/static/go1.png" class="w-16 h-16 m-l-4" mode=""></image>
					</view>
				</view>
			</view>

			<image src="/static/bg1.png" class="bg1" mode=""></image>
			<view class="members acea-row row-middle row-between">
				<view class="">
					<view class="">
						<image src="/static/vb1.png" mode=""></image>
						<view class="bga acea-row row-middle">
							<image src="/static/vip1.png" class="w-32 h-32 m-r-8 m-l-16" mode=""></image>
							<text style="color: #FFFFFF;">{{(leveltotal[1]||{}).level_name}}</text>
						</view>
						<view class="" style="color: #fff;"><text style="color: #fff;"
								class="font-48">{{(leveltotal[1]||{}).counts||0}}</text>人
						</view>
					</view>
					<view class="acea-row row-middle row-center p-t-12 p-b-12" style="color: #B2B6C2;"
						@click="gonext('teamSize',1)">
						查看明细
						<image src="/static/go2.png" class="w-18 h-18 m-l-6" mode=""></image>
					</view>
				</view>
				<view class="">
					<view class="">
						<image src="/static/vb2.png" mode=""></image>
						<view class="bgb acea-row row-middle">
							<image src="/static/vip2.png" class="w-32 h-32 m-r-8 m-l-16" mode=""></image>
							<text style="color: #222;">{{(leveltotal[2]||{}).level_name}}</text>
						</view>
						<view class="" style="color: #FAD5B4;"><text style="color: #FAD5B4;"
								class="font-48">{{(leveltotal[2]||{}).counts||0}}</text>人
						</view>
					</view>
					<view class="acea-row row-middle row-center p-t-12 p-b-12" style="color: #B2B6C2;"
						@click="gonext('teamSize',2)">
						查看明细
						<image src="/static/go2.png" class="w-18 h-18 m-l-6" mode=""></image>
					</view>
				</view>
				<view class="">
					<view class="">
						<image src="/static/vb3.png" mode=""></image>
						<view class="bgc acea-row row-middle">
							<image src="/static/vip3.png" class="w-32 h-32 m-r-8 m-l-16" mode=""></image>
							<text style="color: #FFD685;">{{(leveltotal[3]||{}).level_name}}</text>
						</view>
						<view class="" style="color: #FFD685;"><text style="color: #FFD685;"
								class="font-48">{{(leveltotal[3]||{}).counts||0}}</text>人
						</view>
					</view>
					<view class="acea-row row-middle row-center p-t-12 p-b-12" style="color: #B2B6C2;"
						@click="gonext('teamSize',3)">
						查看明细
						<image src="/static/go2.png" class="w-18 h-18 m-l-6" mode=""></image>
					</view>
				</view>
			</view>
		</view>
		<view class="acea-row row-middle row-between jifenUM">
			<text>积分数量</text>
			<text>{{points||0}}</text>
		</view>
		<view class="container">
			<view class="acea-row row-middle row-between m-b-32">
				<view v-for="(item,index) in tablist" :class="{'on':check==index}" @click="cuts(index)" :key="index">
					{{item}}
					<image src="/static/down1.png" class="w-24 h-8" v-if="check==index" mode=""></image>
				</view>
			</view>
			<uv-subsection :list="list1" :current="current" activeColor='#1D1E24'
				custom-style="height: 60rpx;border-radius: 30rpx;" custom-item-style="border-radius: 30rpx;"
				@change="change"></uv-subsection>
			<view class="h-24"></view>
			<view class="defas acea-row row-middle row-between" v-if="check==0&&token&&myinfo&&myinfo.info">
				<view class="acea-row row-middle">
					<view class="ranks">
						<image src="/static/rank.png" mode=""></image>
						<text>{{myinfo.rank}}</text>
					</view>
					<image :src="$util.img(myinfo.info.site_logo)" v-if="(myinfo.info||{}).site_logo" class="w-102 h-52 m-l-24" mode=""></image>
					<text class="font-28 m-l-12" style="color: #222;">{{(myinfo.info||{}).site_name}}</text>
					<text class="my">(我)</text>
				</view>
				<view class="font-28" style="color: #222;">{{(myinfo.info||{}).account_points||0}}</view>
			</view>
			<view class="acea-row row-middle row-between p-24 titles" v-if="check==0">
				<view class="acea-row row-middle">
					<text class="m-r-20">排名</text>
					<text>品牌</text>
				</view>
				<text>热度</text>
			</view>
			<view class="acea-row row-middle row-between p-24 titles" v-else>
				<view class="acea-row row-middle">
					<text class="m-r-20">排名</text>
					<text>名称</text>
				</view>
				<text>积分</text>
			</view>
			<template v-if="check==0">
				<view v-for="(item,index) in list" class="m-b-20 defas acea-row row-middle row-between"
					:class="{'rankbg1':index==0,'rankbg2':index==1,'rankbg3':index==2}" :key="index">
					<view class="acea-row row-middle" style="flex-wrap: nowrap;">
						<view class="ranks">
							<image src="/static/rank1.png" v-if="index==0" mode=""></image>
							<image src="/static/rank2.png" v-else-if="index==1" mode=""></image>
							<image src="/static/rank3.png" v-else-if="index==2" mode=""></image>
							<image src="/static/rank.png" v-else mode=""></image>
							<text v-if="index>2">{{index+1}}</text>
						</view>
						<image :src="$util.img(item.site_logo)" v-if="item.site_logo" class="w-102 h-52 m-l-24" mode=""></image>
						<text class="font-28 m-l-12 text-more w-280" style="color: #222;">{{item.site_name}}</text>
					</view>
					<view class="font-28" style="color: #222;">{{item.account_points||0}}</view>
				</view>
			</template>
			<template v-else>
				<view v-for="(item,index) in list2" class="m-b-20 defas acea-row row-middle row-between"
					:class="{'rankbg1':index==0,'rankbg2':index==1,'rankbg3':index==2}" :key="index">
					<view class="acea-row row-middle">
						<view class="ranks">
							<image src="/static/rank1.png" v-if="index==0" mode=""></image>
							<image src="/static/rank2.png" v-else-if="index==1" mode=""></image>
							<image src="/static/rank3.png" v-else-if="index==2" mode=""></image>
							<image src="/static/rank.png" v-else mode=""></image>
							<text v-if="index>2">{{index+1}}</text>
						</view>
						<image :src="$util.img(item.memberinfo.headimg)" v-if="item.memberinfo.headimg"
							class="w-52 h-52 bor-50 m-l-24" mode=""></image>
						<image src="/static/avatar.png" v-else class="w-52 h-52 bor-50 m-l-24" mode=""></image>
						<text class="font-28 m-l-12" style="color: #222;">{{item.memberinfo.nickname}}</text>
					</view>
					<view class="font-28" style="color: #222;">{{item.account_points||0}}</view>
				</view>
			</template>
		</view>
		<login ref="login" @loginsuccess='loginsuccess'></login>

		<!-- 扫码核销订单信息弹窗 -->
		<uv-popup ref="verifyPopup" mode="center" :round="16" :closeOnClickOverlay="false" customStyle="width: 660rpx; max-height: 85vh; border-radius: 28rpx; overflow: hidden; background: #ffffff;">
			<view class="verify-popup-content" v-if="verifyOrder">
				<!-- 弹窗标题栏 -->
				<view class="verify-popup-header acea-row row-middle row-between">
					<view class="acea-row row-middle">
						<uv-icon name="scan" size="20" color="#F55606"></uv-icon>
						<text class="title-text m-l-10">订单核销确认</text>
					</view>
					<view class="close-icon" @click="closeVerifyPopup">
						<uv-icon name="close" size="18" color="#999999"></uv-icon>
					</view>
				</view>

				<!-- 弹窗滚动内容 -->
				<scroll-view scroll-y="true" class="verify-scroll-body">
					<!-- 已核销警告 -->
					<view class="verify-alert-warning acea-row row-middle" v-if="verifyOrder.is_verify == 1 || verifyOrder.order_status == 4">
						<uv-icon name="error-circle-fill" size="18" color="#E6A23C"></uv-icon>
						<text class="m-l-10">提示：该订单商品已完成核销，请勿重复核销！</text>
					</view>

					<!-- 订单基本信息卡片 -->
					<view class="verify-info-card">
						<view class="info-row acea-row row-middle row-between">
							<text class="label">订单编号</text>
							<text class="value">{{ verifyOrder.order_no }}</text>
						</view>
						<view class="info-row acea-row row-middle row-between" v-if="verifyOrder.create_time">
							<text class="label">下单时间</text>
							<text class="value">{{ verifyOrder.create_time }}</text>
						</view>
						<view class="info-row acea-row row-middle row-between">
							<text class="label">顾客姓名</text>
							<text class="value font-weight-6">{{ verifyOrder.name || '顾客' }}</text>
						</view>
						<view class="info-row acea-row row-middle row-between" v-if="verifyOrder.mobile">
							<text class="label">联系电话</text>
							<text class="value">{{ verifyOrder.mobile }}</text>
						</view>
						<view class="info-row acea-row row-middle row-between">
							<text class="label">当前状态</text>
							<view class="status-tag" :class="{'tag-verified': verifyOrder.is_verify == 1 || verifyOrder.order_status == 4, 'tag-pending': verifyOrder.is_verify != 1 && verifyOrder.order_status != 4}">
								{{ (verifyOrder.is_verify == 1 || verifyOrder.order_status == 4) ? '已核销' : (verifyOrder.order_status_name || '待核销') }}
							</view>
						</view>
					</view>

					<!-- 商品清单 -->
					<view class="verify-goods-section">
						<view class="section-title">商品清单 ({{ (verifyOrder.goods_list || []).length }}件)</view>
						<view class="goods-item acea-row row-middle" v-for="(item, index) in verifyOrder.goods_list" :key="index">
							<image :src="$util.img(item.sku_image)" class="goods-img" mode="aspectFill"></image>
							<view class="goods-info acea-row row-column row-between">
								<view class="goods-name text-more-2">{{ item.sku_name }}</view>
								<view class="goods-meta acea-row row-middle row-between">
									<view class="goods-price">
										￥<text class="price-val">{{ item.price }}</text>
										<text class="goods-num m-l-10">×{{ item.num }}</text>
									</view>
									<view class="goods-status-tag" :class="{'status-done': item.delivery_status == 1 || verifyOrder.is_verify == 1}">
										{{ (item.delivery_status == 1 || verifyOrder.is_verify == 1) ? '已核销' : '待核销' }}
									</view>
								</view>
							</view>
						</view>
					</view>

					<!-- 实付金额汇总 -->
					<view class="verify-money-card acea-row row-middle row-between">
						<text class="money-label">实付总额</text>
						<view class="money-val">
							￥<text class="big-price">{{ verifyOrder.pay_money || verifyOrder.order_money }}</text>
						</view>
					</view>
				</scroll-view>

				<!-- 底部按钮区域：取消 & 确认核销 -->
				<view class="verify-popup-footer acea-row row-middle row-between">
					<view class="cancel-btn" @click="closeVerifyPopup">取消</view>
					<view class="confirm-btn" :class="{'btn-disabled': verifyOrder.is_verify == 1 || verifyOrder.order_status == 4 || verifying}" @click="confirmVerify">
						<text v-if="verifying">核销中...</text>
						<text v-else-if="verifyOrder.is_verify == 1 || verifyOrder.order_status == 4">已完成核销</text>
						<text v-else>确认核销</text>
					</view>
				</view>
			</view>
		</uv-popup>

		<!-- 切换企业/店铺弹窗 -->
		<shop-select-popup ref="shopPopup" @change="onShopChange"></shop-select-popup>
	</view>
</template>

<script>
	import {
		getnum,
		getmemberranklist,
		getRankList,
		getInfo,
		getOrderInfo,
		dodelivery,
		getIncomeSummary
	} from "@/common/js/api.js"
	import ShopSelectPopup from "@/components/shop-select-popup/shop-select-popup.vue"

	export default {
		components: {
			ShopSelectPopup
		},
		data() {
			return {
				tablist: ['品牌积分排行榜', '员工积分排行榜'],
				check: 0,
				list1: ['年度排行榜', '月度排行榜'],
				current: 0,
				points: 0,
				total_count: 0,
				leveltotal: [],
				list: [],
				list2: [],
				myinfo: {},
				token: '',
				page: 1,
				last_page: 1,
				verifyOrder: null,
				scanCodeVal: '',
				verifying: false,
				currentSiteId: '',
				currentSiteName: '',
				currentShopLogo: '',
				isManager: 0,
				currentRoleName: '员工',
				incomeSummary: {
					is_manager: 0,
					bring_income: { total: '0.00', today: '0.00', count: 0 },
					member_income: { total: '0.00', today: '0.00', count: 0 }
				}
			}
		},
		computed: {
			totalIncome() {
				let b = parseFloat((this.incomeSummary.bring_income || {}).total) || 0;
				let m = parseFloat((this.incomeSummary.member_income || {}).total) || 0;
				return (b + m).toFixed(2);
			}
		},
		onLoad(res) {
			uni.$on('merchantShopChanged', this.onShopChanged);
			uni.$on('loginout', this.resetPageData);
			uni.$on('loginChange', this.onLoginChange);
			uni.$on('loginsuccess', this.loginsuccess);
		},
		beforeDestroy() {
			uni.$off('merchantShopChanged', this.onShopChanged);
			uni.$off('loginout', this.resetPageData);
			uni.$off('loginChange', this.onLoginChange);
			uni.$off('loginsuccess', this.loginsuccess);
		},
		onReachBottom() {
			if (this.last_page > this.page) {
				this.page++
				if (this.check == 1) {
					this.getmemberranklist()
				} else {
					this.getRankList()
				}
			}
		},
		onShow() {
			this.token = uni.getStorageSync('token') || '';
			let cachedSiteId = uni.getStorageSync('site_id') || '';

			if (!this.token) {
				this.resetPageData();
				return;
			}

			// 检查是否切换过店铺
			if (cachedSiteId && cachedSiteId !== this.currentSiteId) {
				this.currentSiteId = cachedSiteId;
				this.refreshAllData();
			} else {
				this.fetchShopInfo();
				this.getRankList();
				this.getnum();
				this.fetchIncomeSummary();
			}
		},
		onPullDownRefresh() {
			this.refreshAllData();
			setTimeout(() => {
				uni.stopPullDownRefresh();
			}, 1000);
		},
		methods: {
			resetPageData() {
				this.token = '';
				this.points = 0;
				this.total_count = 0;
				this.leveltotal = [];
				this.myinfo = {};
				this.list = [];
				this.list2 = [];
				this.currentSiteId = '';
				this.currentSiteName = '';
				this.currentShopLogo = '';
				this.isManager = 0;
				this.currentRoleName = '员工';
				this.incomeSummary = {
					is_manager: 0,
					bring_income: { total: '0.00', today: '0.00', count: 0 },
					member_income: { total: '0.00', today: '0.00', count: 0 }
				};
			},
			onLoginChange(isLogin) {
				if (!isLogin) {
					this.resetPageData();
				} else {
					this.token = uni.getStorageSync('token') || '';
					this.refreshAllData();
				}
			},
			openShopSelect() {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open();
					return;
				}
				this.$refs.shopPopup.open();
			},
			onShopChange(item) {
				this.currentSiteId = uni.getStorageSync('site_id') || '';
				if (item) {
					if (item.is_manager !== undefined || item.role) {
						let isMgr = (item.is_manager == 1 || item.role === 'owner') ? 1 : 0;
						this.isManager = isMgr;
						this.currentRoleName = isMgr ? '老板' : '员工';
						this.incomeSummary.is_manager = isMgr;
					}
					if (item.site_name) {
						this.currentSiteName = item.site_name;
						uni.setNavigationBarTitle({ title: item.site_name });
					}
					if (item.store_logo || item.store_image) {
						this.currentShopLogo = item.store_logo || item.store_image;
					}
				}
				this.refreshAllData();
			},
			onShopChanged(data) {
				let siteId = (data && data.site_id) ? data.site_id : (uni.getStorageSync('site_id') || '');
				if (data) {
					if (data.is_manager !== undefined || data.role) {
						let isMgr = (data.is_manager == 1 || data.role === 'owner') ? 1 : 0;
						this.isManager = isMgr;
						this.currentRoleName = isMgr ? '老板' : '员工';
						this.incomeSummary.is_manager = isMgr;
					}
					if (data.site_name) {
						this.currentSiteName = data.site_name;
						uni.setNavigationBarTitle({ title: data.site_name });
					}
					if (data.store_logo || data.store_image) {
						this.currentShopLogo = data.store_logo || data.store_image;
					}
				}
				if (siteId && siteId !== this.currentSiteId) {
					this.currentSiteId = siteId;
				}
				this.refreshAllData();
			},
			refreshAllData() {
				this.fetchShopInfo();
				this.page = 1;
				this.getRankList();
				if (uni.getStorageSync('token')) {
					this.getnum();
					this.fetchIncomeSummary();
					if (this.check == 1) {
						this.getmemberranklist();
					}
				}
			},
			fetchShopInfo() {
				let site_id = uni.getStorageSync('site_id') || '';
				getInfo({ site_id }).then(res => {
					if (res.code == 0 && res.data) {
						let shop = res.data;
						this.currentSiteName = shop.site_name || '商家中心';
						this.currentShopLogo = shop.logo || '';
						if (!uni.getStorageSync('site_id') && shop.site_id) {
							uni.setStorageSync('site_id', shop.site_id);
							this.currentSiteId = shop.site_id;
						}
						// 动态修改标题为当前店铺
						uni.setNavigationBarTitle({
							title: this.currentSiteName
						});
						if (shop.myinfo && shop.myinfo.is_manager !== undefined) {
							let isMgr = shop.myinfo.is_manager == 1 ? 1 : 0;
							this.isManager = isMgr;
							this.currentRoleName = isMgr ? '老板' : '员工';
							this.incomeSummary = {
								...this.incomeSummary,
								is_manager: isMgr
							};
						}
					}
				}).catch(() => {});
			},
			sancode() {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				uni.scanCode({
					scanType: ['qrCode', 'barCode'],
					success: res => {
						if (res.result) {
							this.handleScanResult(res.result)
						}
					},
					fail: (err) => {
						// 用户取消或扫码异常
						if (err && err.errMsg && err.errMsg.indexOf('cancel') === -1) {
							uni.showToast({
								title: '扫码失败，请重试',
								icon: 'none'
							})
						}
					}
				})
			},
			handleScanResult(code) {
				this.scanCodeVal = code
				uni.showLoading({ title: '查询订单信息...' })
				getOrderInfo({ delivery_code: code }).then(res => {
					uni.hideLoading()
					if (res.code == 0 && res.data) {
						this.verifyOrder = res.data
						this.$refs.verifyPopup.open()
					} else {
						uni.showModal({
							title: '核销提示',
							content: res.message || '未找到该核销码对应的订单信息',
							showCancel: false
						})
					}
				}).catch(err => {
					uni.hideLoading()
					uni.showToast({
						title: (err && err.message) ? err.message : '查询订单失败',
						icon: 'none'
					})
				})
			},
			closeVerifyPopup() {
				this.$refs.verifyPopup.close()
			},
			confirmVerify() {
				if (this.verifying) return
				if (this.verifyOrder.is_verify == 1 || this.verifyOrder.order_status == 4) {
					uni.showToast({
						title: '该订单已核销，请勿重复操作',
						icon: 'none'
					})
					return
				}
				this.verifying = true
				uni.showLoading({ title: '核销中...' })
				let params = {
					delivery_code: this.verifyOrder.delivery_code || this.scanCodeVal
				}
				dodelivery(params).then(res => {
					uni.hideLoading()
					this.verifying = false
					if (res.code == 0) {
						uni.showToast({
							title: '核销成功',
							icon: 'success'
						})
						// 本地状态更新为已核销
						this.verifyOrder.is_verify = 1
						this.verifyOrder.order_status = 4
						this.verifyOrder.order_status_name = '已核销'
						this.verifyOrder.delivery_status = 1
						this.verifyOrder.delivery_status_name = '已核销'
						if (this.verifyOrder.goods_list) {
							this.verifyOrder.goods_list.forEach(item => {
								item.delivery_status = 1
								item.delivery_status_name = '已核销'
							})
						}
						// 刷新统计数据
						this.getnum()
						setTimeout(() => {
							this.closeVerifyPopup()
						}, 1200)
					} else {
						uni.showModal({
							title: '核销失败',
							content: res.message || '核销失败，请稍后重试',
							showCancel: false
						})
					}
				}).catch(err => {
					uni.hideLoading()
					this.verifying = false
					uni.showToast({
						title: (err && err.message) ? err.message : '核销请求异常',
						icon: 'none'
					})
				})
			},
			loginsuccess() {
				this.getnum()
				getInfo().then(res => {
					uni.setStorageSync('site_id', res.data.site_id)
				})
				if (this.check == 1) {
					this.getmemberranklist()
				} else {
					this.getRankList()
				}
			},
			change(e) {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				this.current = e
				this.page = 1
				if (this.check == 1) {
					this.getmemberranklist()
				} else {
					this.getRankList()
				}
			},
			cuts(e) {
				if (!uni.getStorageSync('token')) {
					this.$refs.login.open()
					return
				}
				this.check = e
				this.page = 1
				if (e == 1) {
					this.getmemberranklist()
				} else {
					this.getRankList()
				}
			},
			getRankList() {
				let params = {
					sortby: this.current == 0 ? 'year' : 'month',
					page: this.page
				}
				getRankList(params).then(res => {
					// 空值保护：接口异常时 res.data 为 null，直接取字段会抛错并中断页面渲染
					const d = (res && res.code == 0 && res.data) || {}
					const rows = Array.isArray(d.list) ? d.list : []
					this.myinfo = d.myinfo || {}
					this.list = this.page > 1 ? this.list.concat(rows) : rows
					this.last_page = d.page_count || 0
				})
			},
			getmemberranklist() {
				let params = {
					sortby: this.current == 0 ? 'year' : 'month',
					page: this.page
				}
				getmemberranklist(params).then(res => {
					const d = (res && res.code == 0 && res.data) || {}
					const rows = Array.isArray(d.list) ? d.list : []
					this.list2 = this.page > 1 ? this.list2.concat(rows) : rows
					this.last_page = d.page_count || 0
				})
			},
			gonext(e, n) {
				let url = n !== undefined ? `/otherpages/${e}/${e}?current=${n}` : `/otherpages/${e}/${e}`;
				uni.navigateTo({
					url: url
				});
			},
			getnum() {
				getnum().then(res => {
					const d = (res && res.code == 0 && res.data) || {}
					this.points = d.points || 0
					this.total_count = (d.team_total && d.team_total.total_count) || 0
					this.leveltotal = (d.team_total && d.team_total.leveltotal) || []
				})
			},
			fetchIncomeSummary() {
				let site_id = uni.getStorageSync('site_id') || 0
				getIncomeSummary({ site_id }).then(res => {
					if (res.code == 0 && res.data) {
						let mgr = res.data.is_manager !== undefined ? (res.data.is_manager == 1 ? 1 : 0) : this.isManager;
						this.incomeSummary = {
							...res.data,
							is_manager: mgr
						};
						this.isManager = mgr;
						this.currentRoleName = mgr == 1 ? '老板' : '员工';
					}
				}).catch(() => {})
			},
		}
	}
</script>

<style lang="scss">
	.container {
		margin: 32rpx;
		background: #FFFFFF;
		border-radius: 24rpx;
		padding: 24rpx;

		.titles {
			text {
				font-weight: 400;
				font-size: 24rpx;
				color: #999999;
			}
		}

		.my {
			font-weight: 600;
			font-size: 28rpx;
			color: #F55606;
		}

		>view:first-child {
			>view {
				width: calc((100% - 16rpx)/2);
				line-height: 72rpx;
				background: #F5F5F5;
				border-radius: 16rpx;
				text-align: center;
				box-sizing: border-box;
				position: relative;
				font-weight: 400;
				font-size: 28rpx;
				color: #222222;
			}

			.on {
				border: 2rpx solid #F55606;
				font-weight: 600;
				font-size: 28rpx;
				color: #F55606;

				>image {
					position: absolute;
					left: 50%;
					transform: translateX(-50%);
					bottom: -10rpx;
				}
			}
		}

		.defas {
			border-radius: 12rpx;
			height: 104rpx;
			padding: 0 24rpx;
			background: #F5F5F5;
		}

		.rankbg1 {
			background: linear-gradient(90deg, #FFF1D6 0%, #F6F8FC 100%);
		}

		.rankbg2 {
			background: linear-gradient(90deg, #E3EFFC 0%, #F6F8FC 100%);
		}

		.rankbg3 {
			background: linear-gradient(90deg, #FFF0E6 0%, #FFFCFA 100%);
		}

		.ranks {
			width: 58rpx;
			height: 64rpx;
			position: relative;

			>text {
				font-weight: 400;
				font-size: 32rpx;
				color: #222222;
				position: absolute;
				left: 50%;
				transform: translateX(-50%);
				bottom: 12rpx;
			}

			>image {
				width: 100%;
				height: 100%;
			}
		}
	}

	.jifenUM {
		height: 80rpx;
		background: #272A33;
		margin: 32rpx;
		border-radius: 16rpx;
		position: relative;
		padding: 0 32rpx;

		&::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			border-radius: 16rpx;
			padding: 1rpx;
			background: linear-gradient(90deg, rgba(250, 213, 180, 0), rgba(250, 213, 180, 1));
			-webkit-mask:
				linear-gradient(#fff 0 0) content-box,
				linear-gradient(#fff 0 0);
			-webkit-mask-composite: xor;
			mask-composite: exclude;
			pointer-events: none;
		}

		text:first-child {
			color: #CCCCCC;
			font-size: 28rpx;
		}

		text:last-child {
			color: #F5B67F;
			font-size: 48rpx;
			font-weight: 700;
		}
	}

	.members {
		margin: 0 32rpx;
		margin-top: 85rpx;

		>view {
			width: calc((100% - 32rpx)/3);
			// height: 208rpx;
			background: #272A33;
			border-radius: 16rpx;
			overflow: hidden;

			>view:first-child {
				width: 100%;
				height: 158rpx;
				position: relative;

				>image {
					width: 100%;
					height: 100%;
				}

				>view:nth-child(2) {
					width: 176rpx;
					height: 50rpx;
					position: absolute;
					left: 0;
					top: 0;
					z-index: 3;
					clip-path: polygon(0% 0%, 100% 0%, 95% 100%, 0% 100%);
					border-radius: 0 0 20rpx 0;
				}

				.bga {
					background: linear-gradient(90deg, rgba(255, 255, 255, 0.16) 0%, rgba(255, 255, 255, 0.28) 100%);
				}

				.bgb {
					background: linear-gradient(90deg, rgba(255, 255, 255, 0.16) 0%, rgba(255, 255, 255, 0.28) 100%);
				}

				.bgc {
					background: linear-gradient(90deg, rgba(255, 255, 255, 0.16) 0%, rgba(255, 255, 255, 0.28) 100%);
				}

				>view:nth-child(3) {
					position: absolute;
					left: 20rpx;
					bottom: 32rpx;
				}
			}
		}
	}

	.bg1 {
		position: absolute;
		width: 100vw;
		height: 350rpx;
		top: 220rpx;
		left: 0;
		z-index: -1;
	}

	.line1 {
		border: 1rpx dashed #555555;
	}

	.wbg {
		background: #fff;
	}

	page {
		background: #101014;
	}

	.part1 {
		min-height: 279rpx;
		height: auto;
		background: linear-gradient(180deg, #272A33 0%, #18181F 100%);
		border-radius: 24rpx;
		margin: 0 32rpx;
		margin-top: 20rpx;
		box-sizing: border-box;
		padding: 32rpx;

		>view:first-child {
			>view {
				width: calc((100% - 46rpx)/2);
			}
		}
	}

	.fcolor1 {
		color: #fff;
	}

	/* 扫码核销弹窗样式 */
	.verify-popup-content {
		background: #ffffff;
		border-radius: 28rpx;
		overflow: hidden;

		.verify-popup-header {
			padding: 28rpx 32rpx;
			border-bottom: 1rpx solid #F0F0F0;

			.title-text {
				font-size: 32rpx;
				font-weight: 600;
				color: #222222;
			}

			.close-icon {
				padding: 8rpx;
			}
		}

		.verify-scroll-body {
			max-height: 58vh;
			padding: 24rpx 32rpx;
			box-sizing: border-box;

			.verify-alert-warning {
				background: #FDF6EC;
				border: 1rpx solid #FAECD8;
				color: #E6A23C;
				font-size: 24rpx;
				padding: 16rpx 20rpx;
				border-radius: 12rpx;
				margin-bottom: 20rpx;
			}

			.verify-info-card {
				background: #F8F9FA;
				border-radius: 16rpx;
				padding: 20rpx 24rpx;
				margin-bottom: 24rpx;

				.info-row {
					padding: 8rpx 0;
					font-size: 26rpx;

					.label {
						color: #888888;
					}

					.value {
						color: #222222;
					}

					.status-tag {
						font-size: 22rpx;
						padding: 4rpx 14rpx;
						border-radius: 6rpx;
						font-weight: 500;
					}

					.tag-verified {
						color: #25B85B;
						background: #EAFEF1;
					}

					.tag-pending {
						color: #FF6600;
						background: #FFF0E5;
					}
				}
			}

			.verify-goods-section {
				margin-bottom: 24rpx;

				.section-title {
					font-size: 28rpx;
					font-weight: 600;
					color: #222222;
					margin-bottom: 16rpx;
				}

				.goods-item {
					padding: 16rpx 0;
					border-bottom: 1rpx solid #F2F2F2;

					&:last-child {
						border-bottom: none;
					}

					.goods-img {
						width: 120rpx;
						height: 120rpx;
						border-radius: 12rpx;
						margin-right: 20rpx;
						flex-shrink: 0;
						background: #f5f5f5;
					}

					.goods-info {
						flex: 1;
						height: 120rpx;
						overflow: hidden;

						.goods-name {
							font-size: 26rpx;
							color: #222222;
							line-height: 36rpx;
						}

						.goods-meta {
							width: 100%;

							.goods-price {
								color: #F51D1D;
								font-size: 22rpx;

								.price-val {
									font-size: 30rpx;
									font-weight: 600;
								}

								.goods-num {
									color: #999999;
									font-size: 24rpx;
								}
							}

							.goods-status-tag {
								font-size: 20rpx;
								color: #FF6600;
								background: #FFF4EC;
								padding: 2rpx 12rpx;
								border-radius: 6rpx;
							}

							.status-done {
								color: #25B85B;
								background: #EAFEF1;
							}
						}
					}
				}
			}

			.verify-money-card {
				background: #FFF9F5;
				border: 1rpx solid #FFEADB;
				border-radius: 16rpx;
				padding: 20rpx 24rpx;

				.money-label {
					font-size: 28rpx;
					color: #666666;
				}

				.money-val {
					color: #F51D1D;
					font-size: 24rpx;

					.big-price {
						font-size: 36rpx;
						font-weight: 700;
					}
				}
			}
		}

		.verify-popup-footer {
			padding: 24rpx 32rpx;
			border-top: 1rpx solid #F0F0F0;
			background: #ffffff;

			.cancel-btn {
				width: 260rpx;
				height: 80rpx;
				line-height: 80rpx;
				text-align: center;
				border-radius: 40rpx;
				border: 2rpx solid #DDDDDD;
				color: #666666;
				font-size: 28rpx;
				font-weight: 500;
			}

			.confirm-btn {
				width: 300rpx;
				height: 80rpx;
				line-height: 80rpx;
				text-align: center;
				border-radius: 40rpx;
				background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
				color: #ffffff;
				font-size: 28rpx;
				font-weight: 600;
				box-shadow: 0 4rpx 12rpx rgba(245, 86, 6, 0.3);
			}

			.btn-disabled {
				background: #CCCCCC !important;
				box-shadow: none !important;
				color: #FFFFFF !important;
			}
		}
	}

	/* 顶部当前店铺展示与切换条 */
	.index-shop-header {
		margin: 20rpx 32rpx 20rpx;
		background: #1D1E24;
		border: 1rpx solid rgba(245, 182, 127, 0.25);
		border-radius: 20rpx;
		padding: 16rpx 24rpx;
		cursor: pointer;
		position: relative;
		z-index: 10;

		.shop-header-left {
			flex: 1;
			overflow: hidden;

			.shop-logo-img {
				width: 52rpx;
				height: 52rpx;
				border-radius: 12rpx;
				margin-right: 14rpx;
				flex-shrink: 0;
				background: #333333;
			}

			.shop-title-text {
				font-size: 30rpx;
				font-weight: 700;
				color: #FFFFFF;
				max-width: 440rpx;
			}
		}

		.shop-role-pill {
			font-size: 20rpx;
			font-weight: 600;
			padding: 4rpx 14rpx;
			border-radius: 12rpx;
			flex-shrink: 0;
			margin-left: 16rpx;

			&.pill-manager {
				background: linear-gradient(135deg, #FFB000 0%, #F57C00 100%);
				color: #FFFFFF;
			}

			&.pill-staff {
				background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
				color: #FFFFFF;
			}
		}
	}
</style>