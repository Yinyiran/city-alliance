<template>
	<view>
		<uv-sticky bg-color="#fff">
			<nvabar type='1' title='申请提现' titleStyle='color:#222'></nvabar>
		</uv-sticky>
		<view class="cash acea-row row-middle row-between" @click="gopage" v-if="!cashInfo || cashInfo.apply_status == 3">
			<view class="acea-row row-middle">
				<image src="/otherpages/static/warn.png" class="w-32 h-32 m-r-12" mode=""></image>
				当前未绑定提现账户，绑定账户后可提现
			</view>
			<uv-icon name="arrow-right" color="#FE780F"></uv-icon>
		</view>
		<view class="m-40">
			<view class="acea-row row-middle row-between">
				<view class="font-28">提现到账户</view>
				<!-- <view class="acea-row row-middle font-26" style="color: #F55606;" v-if="show" @click="gopage">
					<image src="/otherpages/static/edit1.png" class="w-26 h-26 m-r-8" mode=""></image>
					修改
				</view> -->
			</view>
			<view class="nos m-t-20" v-if="!cashInfo || cashInfo.apply_status == 3">当前未绑定提现账户，请前往绑定</view>
			<view class="nos1 m-t-20 acea-row row-middle" v-else>
				<view class="">
					<view class="">账户账号</view>
					<view class="">{{ cashInfo.card_no }}</view>
				</view>
				<view class="p-l-20">
					<view class="">真实姓名</view>
					<view class="">{{ cashInfo.realname }}</view>
				</view>
			</view>
			<view class="font-28 m-t-40">提现金额</view>
			<view class="acea-row inputs">
				<text>￥</text>
				<input type="number" v-model="apply_money" placeholder="请输入" />
				<text @click="alls">全部提现</text>
			</view>
			<view class="m-t-20" style="color: #999999;"> 当前可提现金额 <text>{{ balance_money }}元</text>，一天最高可提现额度为{{ max }}元
			</view>
			<view class="calc-card m-t-32" v-if="taxDetail && Number(apply_money) > 0">
				<view class="calc-header acea-row row-middle row-between">
					<view class="calc-title font-28 font-weight-6">预估到账明细</view>
					<view class="calc-badge">依法代扣代缴</view>
				</view>
				<view class="calc-row acea-row row-between m-t-16 font-26">
					<text style="color: #666;">申请提现</text>
					<text class="font-weight-6">￥{{ taxDetail.apply_money }}</text>
				</view>
				<view class="calc-row acea-row row-between m-t-12 font-26">
					<text style="color: #666;">平台手续费 ({{ taxDetail.charge_rate }}%)</text>
					<text style="color: #F55606;">-￥{{ taxDetail.charge_money }}</text>
				</view>
				<view class="calc-row acea-row row-between m-t-12 font-26">
					<text style="color: #666;">劳务报酬个税代扣 ({{ taxDetail.tax_rate }}%)</text>
					<text style="color: #F55606;">-￥{{ taxDetail.tax_money }}</text>
				</view>
				<view class="calc-divider m-t-16 m-b-16"></view>
				<view class="calc-row acea-row row-between font-28">
					<text class="font-weight-6">预计实际到账</text>
					<text class="font-weight-6 font-32" style="color: #07c160;">￥{{ taxDetail.real_money }}</text>
				</view>
				<view class="calc-tip m-t-12 font-22" style="color: #999;">
					* {{ min > 0 ? '单笔最低提现' + min + '元；' : '不设最低提现额度，可按需分多次提现；' }}单次及月800元以内免缴个税，超出800元按国家劳务报酬税率代扣代缴。
				</view>
			</view>
			<view class="m-t-40 introduction-box" v-if="introduction" v-html="introduction">
			</view>
			<view class="btms iphone-x">
				<view class="" @click="submit">提现</view>
			</view>
		</view>
	</view>
</template>

<script>
import { memberbankaccountPage, memberwithdrawapply, memberwithdrawconfig, memberopenaccountInfo, memberwithdrawCalculateTax } from "@/common/js/api.js"
export default {
	data() {
		return {
			show: false,
			introduction: '',
			apply_money: '',
			// 最低提现额度：后台可配置，0 表示不设门槛（不展示、不校验）
			min: 0,
			max: "",
			rules: [],
			cashInfo: {},
			balance_money: "",
			taxDetail: null
		};
	},
	watch: {
		apply_money(val) {
			this.calcTax(val);
		}
	},
	onShow() {
		// this.memberbankaccountPage()
		this.memberopenaccountInfo()
		this.memberwithdrawconfig()
	},
	methods: {
		calcTax(val) {
			let money = parseFloat(val);
			if (!money || isNaN(money) || money <= 0) {
				this.taxDetail = null;
				return;
			}
			clearTimeout(this._calcTimer);
			this._calcTimer = setTimeout(() => {
				memberwithdrawCalculateTax({ apply_money: money }).then(res => {
					if (res.code == 0 && res.data) {
						this.taxDetail = res.data;
					}
				}).catch(() => { });
			}, 300);
		},
		alls() {
			this.apply_money = this.balance_money
		},
		memberopenaccountInfo() {
			memberopenaccountInfo().then(res => {
				this.cashInfo = res.data
			})
		},
		memberwithdrawconfig() {
			memberwithdrawconfig().then(res => {
				let data = res.data || {}
				this.setRules(data.value || {}, data.balance_money)
			}).catch(() => {
				// 接口异常时展示默认规则，保证提现页面始终有规则说明
				this.setRules({}, '')
			})
		},
		setRules(cfg, balanceMoney) {
			this.balance_money = balanceMoney
			// 最低提现额度：后台可配置，支持 0 与两位小数；0 表示不设门槛（不展示、不校验）
			let min = Number(cfg.min) > 0 ? Number(cfg.min) : 0
			this.min = min
			// 最高提现额度：后端 max 为 0 / 空 表示不限额度。
			// 原实现兜底为 700 元属于虚构上限，会与实际逻辑不符并被审核判定为虚假规则。
			let max = Number(cfg.max) > 0 ? Number(cfg.max) : 0
			this.max = max
			let maxText = max > 0 ? (max + '元') : '不限'
			// 最低额度文案片段：min = 0 时改为"不设门槛"
			let minQuotaHtml = min > 0
				? '<div class="rule-sec-p">1. 单笔最低提现：' + min + ' 元</div>'
				: '<div class="rule-sec-p">1. 平台不设最低提现金额门槛，用户可按需分多次提现。</div>'
			// 提现手续费率
			let feeRate = (cfg.fee !== undefined && cfg.fee !== null && cfg.fee !== '') ? cfg.fee : '0.35'
			let feePercent = feeRate + '%'
			// 到账时效
			let arriveDesc = cfg.arrive_desc || cfg.arrive_time || '平台默认 T+1 工作日到账，周末、法定节假日顺延。'

			// 如果后端有返回自定义 introduction 富文本，优先使用后端的富文本；否则使用结构化排版的佣金提现规则说明
			if (cfg.introduction) {
				this.introduction = cfg.introduction
			} else {
				this.introduction = `
						<div class="rule-doc">
							<div class="rule-doc-title">佣金提现规则说明</div>

							<div class="rule-sec">
								<div class="rule-sec-title">一、提现前置条件</div>
								<div class="rule-sec-p">1. 用户在平台获取的推广分销佣金，属于劳务报酬所得，依据国家税务法律法规，所有佣金提现必须完成真实实名认证。</div>
								<div class="rule-sec-p">2. 实名认证需提交：真实姓名、身份证号码、手机号码，并完成平台实名校验，未实名用户无法发起任何提现。</div>
								<div class="rule-sec-p">3. 用户实名信息仅用于身份核验、资金对账、税务申报，平台严格保密用户隐私信息，绝不对外泄露或用作其他用途。</div>
								<div class="rule-sec-p">4. 平台支持两种提现渠道：提现至银行卡、提现至微信零钱，用户可自主选择。</div>
							</div>

							<div class="rule-sec">
								<div class="rule-sec-title">二、个人所得税代扣代缴规则（平台依法预扣预缴）</div>
								<div class="rule-sec-p">1. 平台严格按照国家税务标准，按自然月合并统计用户当月全部佣金收入统一计税，不以单次提现拆分计税。</div>
								<div class="rule-sec-p">2. 计税规则：</div>
								<div class="rule-sec-sub">• 当月累计佣金 ≤4000 元：应纳税所得额 = 当月佣金 − 800 元免税费用</div>
								<div class="rule-sec-sub">• 当月累计佣金 ＞4000 元：应纳税所得额 = 当月佣金 × 80%（国家法定 20% 经营成本免征）</div>
								<div class="rule-sec-p">3. 劳务报酬预扣预缴税率：</div>
								<div class="rule-sec-sub">• 计税基数 0–20000 元：预扣税率 20%</div>
								<div class="rule-sec-sub">• 计税基数 20000–50000 元：预扣税率 30%（速算扣除 2000）</div>
								<div class="rule-sec-sub">• 计税基数 50000 元以上：预扣税率 40%（速算扣除 7000）</div>
								<div class="rule-tip-box">
									<div class="rule-tip-title">重要税务说明</div>
									<div class="rule-sec-p">本次扣除为平台预扣预缴税费，不等于用户最终年度实际税费。</div>
									<div class="rule-sec-p">次年 3 月–6 月，用户可自行在「个人所得税 APP」进行年度汇算清缴，系统统一核算全年综合所得，实行多退少补。</div>
									<div class="rule-sec-p">即使用户当月税费为 0 元，平台依然会依法完成全员全额税务申报。</div>
								</div>
							</div>

							<div class="rule-sec">
								<div class="rule-sec-title">三、提现手续费标准</div>
								<div class="rule-sec-p">1. 平台所有提现通道统一手续费：${feePercent}</div>
								<div class="rule-sec-p">2. 提现至银行卡：手续费 ${feePercent}（内扣）</div>
								<div class="rule-sec-p">3. 提现至微信零钱：手续费 ${feePercent}（内扣）</div>
								<div class="rule-tip-box">
									<div class="rule-tip-title">补充说明</div>
									<div class="rule-sec-p">若用户选择提现至微信零钱，资金到账微信零钱后，用户个人自行从零钱提现到银行卡产生的 0.1% 手续费，由微信官方收取，与城盟优选平台无关。</div>
								</div>
							</div>

							<div class="rule-sec">
														<div class="rule-sec-title">四、提现额度说明</div>
														${minQuotaHtml}
														<div class="rule-sec-p">2. 单笔最高提现：${maxText}</div>
														<div class="rule-sec-sub">（单笔提现金额不可超过账户实际可提现余额）</div>
													</div>

							<div class="rule-sec">
								<div class="rule-sec-title">五、资金计算方式</div>
								<div class="rule-sec-p rule-formula">实际到账金额 = 税前佣金金额 − 预扣预缴个税 − ${feePercent} 通道手续费</div>
								<div class="rule-sec-p">最终以该金额提交官方支付机构（汇付天下）进行打款。</div>
							</div>

							<div class="rule-sec">
								<div class="rule-sec-title">六、到账时效与规则</div>
								<div class="rule-sec-p">1. ${arriveDesc}</div>
								<div class="rule-sec-p">2. 若遇银行系统维护、微信支付通道升级，到账时间将自动延迟，属于正常通道波动。</div>
								<div class="rule-sec-p">3. 所有提现记录、扣税记录、手续费明细将永久保留在用户个人中心，可随时查询核对。</div>
							</div>

							<div class="rule-sec">
								<div class="rule-sec-title">七、温馨提示</div>
								<div class="rule-sec-p">1. 用户提现账户（银行卡 / 微信实名）必须与平台实名认证姓名、证件信息完全一致，信息不一致将直接打款失败、资金原路退回。</div>
								<div class="rule-sec-p">2. 所有佣金收入、税务代扣、资金结算均严格遵守国家税务法及支付监管规则，合规透明、可查可溯。</div>
							</div>
						</div>
					`
			}

			// 顶部核心规则卡片
			let feeText = (cfg.fee !== undefined && cfg.fee !== null && Number(cfg.fee) > 0) ? ('按提现金额收取' + cfg.fee + '%手续费') : (cfg.fee === '0' || cfg.fee === 0 ? '免手续费' : feePercent + '（内扣）')
			this.rules = [
						{ label: '最低提现金额', value: '单笔最低' + min + '元' },
						{ label: '单笔提现额度', value: '最高' + maxText },
						{ label: '通道手续费', value: feeText },
						{ label: '到账时效', value: 'T+1工作日到账' }
					]
					if (min <= 0) {
						this.rules[0] = { label: '提现门槛', value: '不设最低额度' }
					}
				},
				submit() {
					let money = Number(this.apply_money)
					if (!this.apply_money || isNaN(money) || money <= 0) {
						uni.showToast({
							title: '请输入正确的提现金额',
							icon: 'none'
						})
						return
					}
					// 最低提现额度：后台 min = 0 时不限制（依据微信《小程序平台运营规范》3.3，建议保持 0）
					if (Number(this.min) > 0 && money < Number(this.min)) {
						uni.showToast({
							title: '单笔最低提现' + this.min + '元',
							icon: 'none'
						})
						return
					}
					if (money > Number(this.balance_money)) {
				uni.showToast({
					title: '提现金额不能超过当前可提现金额',
					icon: 'none'
				})
				return
			}
			if (Number(this.max) > 0 && money > Number(this.max)) {
				uni.showToast({
					title: '单日最高可提现金额为' + this.max + '元',
					icon: 'none'
				})
				return
			}
			if (!this.cashInfo || !this.cashInfo.card_no) {
				uni.showToast({
					title: '请先完成实名认证并绑定提现银行卡',
					icon: 'none'
				})
				return
			}
			let params = {
				apply_money: this.apply_money,
				realname: this.cashInfo.realname || this.cashInfo.card_name || '',
				bank_name: this.cashInfo.branch_bank_name || this.cashInfo.bank_name || '银行卡',
				account_number: this.cashInfo.card_no || this.cashInfo.bank_account || '',
				mobile: this.cashInfo.mobile || this.cashInfo.mobile_no || '',
				id_card: this.cashInfo.cert_no || this.cashInfo.id_card || '',
				job_type: '其它',
				face_auth_status: 1,
				face_auth_sn: 'FACE_' + new Date().getTime()
			}
			memberwithdrawapply(params).then(res => {
				if (res.code == 0) {
					uni.showToast({
						title: '申请成功',
						success: () => {
							setTimeout(() => {
								this.apply_money = ''
								this.taxDetail = null
								this.memberwithdrawconfig()
							}, 2000)
						}
					})
				} else {
					uni.showToast({
						title: res.message,
						icon: 'none'
					})
				}
			})
		},
		gopage() {
			uni.navigateTo({
				url: '/otherpages/bankinfo/bankinfo'
			})
		},
		memberbankaccountPage() {
			memberbankaccountPage().then(res => {
				if (res.data.count == 0) {
					this.show = false
				} else {
					this.cashInfo = res.data.list[0]
					this.show = true
				}
			})
		},
	}
}
</script>

<style lang="scss">
.btms {
	position: fixed;
	bottom: 0;
	left: 0;
	background: #fff;
	text-align: center;
	width: 100vw;

	>view {
		margin: 20rpx 40rpx;
		line-height: 96rpx;
		background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
		border-radius: 48rpx;
		font-weight: 600;
		font-size: 32rpx;
		color: #FFFFFF;
	}
}

.inputs {
	align-items: baseline;
	border-bottom: 1rpx solid #DDDDDD;

	text:first-child {
		height: 68rpx;
		width: 29rpx;
		font-size: 40rpx;
		margin-right: 20rpx;
	}

	input {
		height: 106rpx;
		background: #FFFFFF;
		font-size: 38rpx;
		color: #222222;
		flex: 1;
	}

	text:last-child {
		font-weight: 400;
		font-size: 26rpx;
		color: #F55606;
	}
}

.cash {
	padding: 0 32rpx;
	background: #FFF5ED;
	height: 72rpx;

	view {
		font-weight: 400;
		font-size: 28rpx;
		color: #FE780F;
	}
}

page {
	background: #fff;
}

.nos1 {
	background: #F5F5F5;
	border-radius: 12rpx;
	padding: 20rpx;

	>view {
		box-sizing: border-box;
		width: 50%;
		position: relative;

		>view:first-child {
			font-size: 20rpx;
			color: #999999;
		}

		>view:last-child {
			font-weight: 600;
			font-size: 22rpx;
		}
	}

	>view:last-child::before {
		content: "";
		width: 1rpx;
		height: 60rpx;
		background: #DDDDDD;
		position: absolute;
		left: 0;
		top: 50%;
		transform: translateY(-50%);
	}
}

.nos {
	background: #F5F5F5;
	border-radius: 12rpx;
	font-weight: 400;
	font-size: 22rpx;
	color: #555555;
	text-align: center;
	line-height: 100rpx;
}

.rules {
	background: #FBFBFB;
	border: 1rpx solid #F0F0F0;
	border-radius: 16rpx;
	padding: 24rpx 28rpx;

	.rules-title {
		font-weight: 600;
		font-size: 30rpx;
		color: #222222;
		margin-bottom: 16rpx;

		.rules-icon {
			width: 8rpx;
			height: 28rpx;
			background: linear-gradient(180deg, #F5AB18 0%, #F55606 100%);
			border-radius: 4rpx;
			margin-right: 14rpx;
		}
	}

	.rule-item {
		padding: 14rpx 0;

		.rule-label {
			font-size: 26rpx;
			color: #999999;
			flex-shrink: 0;
		}

		.rule-value {
			font-size: 26rpx;
			color: #333333;
			margin-left: 32rpx;
			text-align: right;
		}
	}
}

.introduction-box {
	padding-bottom: 210rpx;
	color: #666666;
	font-size: 26rpx;
	line-height: 1.6;

	.rule-doc {
		background: #FAFAFA;
		border: 1rpx solid #EEEEEE;
		border-radius: 16rpx;
		padding: 30rpx 24rpx;

		.rule-doc-title {
			font-size: 30rpx;
			font-weight: bold;
			color: #222222;
			text-align: center;
			margin-bottom: 24rpx;
			padding-bottom: 16rpx;
			border-bottom: 1rpx dashed #E0E0E0;
		}

		.rule-sec {
			margin-bottom: 24rpx;

			&:last-child {
				margin-bottom: 0;
			}

			.rule-sec-title {
				font-size: 28rpx;
				font-weight: 600;
				color: #333333;
				margin-bottom: 12rpx;
			}

			.rule-sec-p {
				font-size: 24rpx;
				color: #666666;
				line-height: 1.7;
				margin-bottom: 8rpx;
			}

			.rule-sec-sub {
				font-size: 24rpx;
				color: #777777;
				padding-left: 20rpx;
				line-height: 1.7;
				margin-bottom: 6rpx;
			}

			.rule-formula {
				color: #F55606;
				font-weight: 500;
				background: #FFF5ED;
				padding: 12rpx 16rpx;
				border-radius: 8rpx;
				margin: 8rpx 0;
			}

			.rule-tip-box {
				background: #FFF9F2;
				border-left: 6rpx solid #FE780F;
				padding: 16rpx 20rpx;
				border-radius: 4rpx;
				margin-top: 12rpx;

				.rule-tip-title {
					font-size: 24rpx;
					font-weight: bold;
					color: #FE780F;
					margin-bottom: 6rpx;
				}

				.rule-sec-p {
					font-size: 22rpx;
					color: #8A6D3B;
					margin-bottom: 4rpx;
					line-height: 1.6;
				}
			}
		}
	}
}

.calc-card {
	background: #FAFAFA;
	border: 1rpx solid #EFEFEF;
	border-radius: 16rpx;
	padding: 24rpx;

	.calc-badge {
		font-size: 20rpx;
		background: #E8F4D9;
		color: #52C41A;
		padding: 4rpx 12rpx;
		border-radius: 6rpx;
	}

	.calc-divider {
		height: 1rpx;
		background: #E8E8E8;
	}

	.text-red {
		color: #F55606;
	}
}
</style>