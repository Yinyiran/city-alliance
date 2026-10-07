<template>
	<view class="page-withdraw">
		<!-- 结算卡状态提示：未绑定 / 审核中 / 异常，三者互斥，保证只渲染一条 -->
		<view class="cash" v-if="cardTip.show" @click="gopage">
			<view class="acea-row row-middle cash-tip">
				<text>{{ cardTip.text }}</text>
			</view>
			<uv-icon name="arrow-right" color="#FE780F"></uv-icon>
		</view>

		<view class="m-40">
			<!-- 结算账户 -->
			<view class="acea-row row-middle row-between">
				<view class="font-28">提现到账户</view>
				<view class="acea-row row-middle font-26" style="color: #F55606;" v-if="hasBindCard" @click="gopage">
					<image src="/static/edit1.png" class="w-26 h-26 m-r-8" mode=""></image>
					更换
				</view>
			</view>
			<view class="nos m-t-20" v-if="!hasBindCard">当前未绑定提现开户账户，请前往绑定</view>
			<view class="nos1 m-t-20 acea-row row-middle" v-else>
				<view class="">
					<view class="">账户账号</view>
					<view class="ellipsis">{{ maskCardNo }}</view>
				</view>
				<view class="p-l-20">
					<view class="">真实姓名</view>
					<view class="ellipsis">{{ cashInfo.realname || '-' }}</view>
				</view>
			</view>

			<!-- 提现金额 -->
			<view class="font-28 m-t-40">提现金额</view>
			<view class="acea-row inputs">
				<text>￥</text>
				<input type="digit" v-model="apply_money" placeholder="请输入" placeholder-class="ph" />
				<text @click="alls">全部提现</text>
			</view>
			<view class="m-t-20 balance-tip">
				<text>当前可提现金额 {{ balance_money }} 元，</text>
				<text>单笔最高可提现 {{ maxRuleText }}</text>
			</view>

			<!-- 实时到账试算：让规则可验证，而不是只有文字 -->
			<view class="calc m-t-20" v-if="calc.money !== ''">
				<view class="calc-row acea-row row-middle row-between">
					<text class="calc-label">手续费（{{ calc.fee_rate }}%）</text>
					<text class="calc-value">- {{ calc.service_money }} 元</text>
				</view>
				<view class="calc-row acea-row row-middle row-between">
					<text class="calc-label">代扣个税</text>
					<text class="calc-value">- {{ calc.tax_money }} 元</text>
				</view>
				<view class="calc-row acea-row row-middle row-between">
					<text class="calc-label">预计实际到账</text>
					<text class="calc-value arrive">{{ calc.money }} 元</text>
				</view>
			</view>

			<!-- 提现规则：微信审核要求必须在提现页清晰展示 -->
			<view class="rules m-t-40">
				<view class="rules-title acea-row row-middle">
					<text class="rules-icon"></text>提现规则
				</view>
				<view class="rule-item" v-for="(item, index) in rules" :key="index">
					<text class="rule-label">{{ item.label }}</text>
					<text class="rule-value">{{ item.value }}</text>
				</view>
				<view class="rule-foot" v-if="!withdrawEnabled">
					当前提现功能维护中，暂不可申请，请稍后再试
				</view>
			</view>

			<view class="intro m-t-24" v-if="introduction" v-html="introduction"></view>

			<view class="rule-link acea-row row-center" @click="openRuleDetail">
				<text>查看《提现服务协议》与完整规则说明</text>
				<uv-icon name="arrow-right" size="12" color="#F55606"></uv-icon>
			</view>

			<view class="m-t-40"></view>
		</view>

		<view class="btms iphone-x">
			<view class="" @click="submit">{{ submitText }}</view>
		</view>

		<!-- 提现规则详情弹窗：把规则以可滚动长文形式完整呈现 -->
		<uv-popup ref="rulePopup" mode="bottom" round="10" :zIndex="10070">
			<view class="rule-detail">
				<view class="rule-detail-title acea-row row-center">提现规则说明</view>
				<scroll-view scroll-y="true" class="rule-detail-body">
					<view class="rule-detail-item" v-for="(item, index) in rules" :key="'d' + index">
						<text class="rd-label">{{ item.label }}</text>
						<text class="rd-value">{{ item.value }}</text>
					</view>
					<text class="rd-label rd-sub">其他说明</text>
					<view class="rd-text">
						1. 提现申请提交后由平台人工审核，审核通过后按规则扣除手续费与代扣个税，剩余金额转入您绑定的结算卡。\n
						2. 单个自然月内累计提现未超过 800 元的部分免征个人所得税；超出部分按国家规定阶梯代扣。\n
						3. 同一笔提现金额不可重复提交，请勿在审核期间对同一银行账户重复操作。\n
						4. 如结算卡信息有误导致打款失败，平台将在核实后原路退回，可提现余额将恢复。\n
						5. 提现相关疑问请联系平台客服，客服将在核实后于 1-3 个工作日内答复。
					</view>
				</scroll-view>
				<view class="rule-detail-close" @click="closeRule">我知道了</view>
			</view>
		</uv-popup>
	</view>
</template>

<script>
import {
	openAccountDetail,
	memberwithdrawapply,
	memberwithdrawconfig,
	memberwithdrawrule,
	memberwithdrawtax
} from "@/common/js/api.js"

// 与后端 Withdraw::apply() / calculateWithdrawTax() 保持一致的兜底文案。
// 接口异常时也必须展示规则，避免审核看到「无规则 / 空白」。
// 默认 min = 0（不设最低门槛），此时后端不会下发「最低提现金额」，前端也不展示。
// 若后台配置了 min > 0，规则中会自动带上该条并做提交前校验。
const FALLBACK_RULES = [
	{ label: '可提现额度', value: '不限额度，可提现金额以账户实际可用余额为准' },
	{ label: '每日提现次数', value: '每日不限提现次数，可分多次申请' },
	{ label: '提现时间', value: '全天 00:00-24:00 均可申请提现' },
	{ label: '到账时间', value: '审核通过后 1-3 个工作日到账' },
	{ label: '提现手续费', value: '按提现金额 0.35% 收取，从到账金额中内扣' },
	{ label: '个税代扣', value: '按国家规定代扣：自然月累计提现未超过 800 元的部分免征，超出部分按 20%/30%/40% 阶梯代扣' },
	{ label: '提现条件', value: '需已绑定企业对公/对私结算卡，并完成实名信息填写' }
]

export default {
	data() {
		return {
			introduction: '',
			apply_money: '',
			rules: FALLBACK_RULES.slice(),
			balance_money: '0.00',
			min: 0,
			max: 0,
			withdrawEnabled: true,
			cashInfo: null,
			calc: {
				money: '',
				service_money: '0.00',
				tax_money: '0.00',
				fee_rate: '0.35'
			},
			timer: null
		};
	},
	computed: {
		hasBindCard() {
			return !!(this.cashInfo && this.cashInfo.card_no);
		},
		// 结算卡状态：三条互斥。原先两个 v-if 会同时命中，导致同一句提示渲染两遍。
		cardTip() {
			if (!this.cashInfo) {
				return { show: true, text: '当前未绑定提现开户账户，绑定账户后可提现' };
			}
			if (Number(this.cashInfo.apply_status) === 0) {
				return { show: true, text: '结算卡资料审核中，审核通过后即可提现' };
			}
			if (Number(this.cashInfo.apply_status) === 3) {
				return { show: true, text: '结算卡资料未通过，请重新提交' };
			}
			if (this.cashInfo.status !== undefined && Number(this.cashInfo.status) !== 1) {
				return { show: true, text: '当前结算卡状态异常，请联系企业管理员' };
			}
			return { show: false, text: '' };
		},
		maskCardNo() {
			const no = String((this.cashInfo && this.cashInfo.card_no) || '');
			if (!no) return '-';
			if (no.length <= 8) return no;
			return no.slice(0, 4) + ' **** **** ' + no.slice(-4);
		},
		maxRuleText() {
			return Number(this.max) > 0 ? Number(this.max) + ' 元' : '可提现余额';
		},
		submitText() {
			if (!this.hasBindCard) return '请先绑定结算卡';
			return '提现';
		}
	},
	onShow() {
		this.fetchRule();
		this.fetchAccount();
		if (uni.getStorageSync('token')) {
			this.fetchConfig();
		}
	},
	onUnload() {
		if (this.timer) clearTimeout(this.timer);
	},
	methods: {
		// 1) 先拉免登录规则接口，保证任何状态下页面都有规则可看
		fetchRule() {
			memberwithdrawrule().then(res => {
				if (res && res.code == 0 && res.data) {
					this.applyRule(res.data);
				}
			});
		},
		// 2) 登录后再拉配置，补齐可提现余额等个性化数据
		fetchConfig() {
			memberwithdrawconfig().then(res => {
				if (!res || res.code != 0) return;
				const data = res.data || {};
				if (data.balance_money !== undefined && data.balance_money !== null && data.balance_money !== '') {
					this.balance_money = Number(data.balance_money).toFixed(2);
				}
				if (data.withdraw_rule) {
					this.applyRule(data.withdraw_rule);
				} else if (data.value) {
					this.applyRule(data.value);
				}
			});
		},
		applyRule(rule) {
			if (!rule) return;
			if (rule.rules && rule.rules.length) {
				this.rules = rule.rules;
			} else if (rule.min !== undefined || rule.max !== undefined || rule.rate !== undefined) {
				// 兜底：用后端原始配置拼装，避免规则区空白（仅接口异常时走到）
				const min = Number(rule.min) > 0 ? Number(rule.min) : 0;
				const max = Number(rule.max) > 0 ? Number(rule.max) : 0;
				const rate = (rule.rate !== undefined && rule.rate !== '') ? rule.rate : '0.35';
				const list = [];
				// min = 0 表示不设门槛，不展示「最低提现金额」
				if (min > 0) {
					list.push({ label: '最低提现金额', value: '单笔最低可提现 ' + min + ' 元' });
				}
				list.push({ label: '可提现额度', value: max > 0 ? '单笔最高可提现 ' + max + ' 元，且不超过可提现余额' : '不限额度，可提现金额以账户实际可用余额为准' });
				list.push({ label: '每日提现次数', value: '每日不限提现次数，可分多次申请' });
				list.push({ label: '提现时间', value: '全天 00:00-24:00 均可申请提现' });
				list.push({ label: '到账时间', value: '审核通过后 1-3 个工作日到账' });
				list.push({ label: '提现手续费', value: '按提现金额 ' + rate + '% 收取，从到账金额中内扣' });
				list.push({ label: '个税代扣', value: '按国家规定代扣：自然月累计提现未超过 800 元的部分免征，超出部分按 20%/30%/40% 阶梯代扣' });
				list.push({ label: '提现条件', value: '需已绑定企业对公/对私结算卡，并完成实名信息填写' });
				this.rules = list;
			}
			if (rule.is_use !== undefined) {
				this.withdrawEnabled = !!Number(rule.is_use);
			}
			if (rule.introduction !== undefined) {
				this.introduction = rule.introduction || '';
			}
			// min = 0 表示不设门槛
			this.min = Number(rule.min) > 0 ? Number(rule.min) : 0;
			if (Number(rule.max) > 0) {
				this.max = Number(rule.max);
			}
		},
		fetchAccount() {
			if (!uni.getStorageSync('token')) {
				this.cashInfo = null;
				return;
			}
			openAccountDetail().then(res => {
				this.cashInfo = (res && res.code == 0 && res.data) ? res.data : null;
			});
		},
		alls() {
			this.apply_money = Number(this.balance_money) > 0 ? this.balance_money : '';
		},
		// 输入金额时实时试算手续费与个税，让规则可被验证
		watchMoney() {
			if (this.timer) clearTimeout(this.timer);
			const money = Number(this.apply_money);
			if (!this.apply_money || isNaN(money) || money <= 0 || !uni.getStorageSync('token')) {
				this.calc = { money: '', service_money: '0.00', tax_money: '0.00', fee_rate: '0.35' };
				return;
			}
			this.timer = setTimeout(() => {
				memberwithdrawtax({ apply_money: money }).then(res => {
					if (res && res.code == 0 && res.data) {
						this.calc = {
							money: Number(res.data.money || 0).toFixed(2),
							service_money: Number(res.data.service_money || 0).toFixed(2),
							tax_money: Number(res.data.tax_money || 0).toFixed(2),
							fee_rate: Number(res.data.fee_rate || 0.35)
						};
					}
				});
			}, 400);
		},
		resetCalc() {
			this.calc = { money: '', service_money: '0.00', tax_money: '0.00', fee_rate: '0.35' };
		},
		submit() {
			if (!this.withdrawEnabled) {
				uni.showToast({ title: '提现功能维护中，请稍后再试', icon: 'none' });
				return;
			}
			if (!this.hasBindCard) {
				uni.showToast({ title: '请先绑定企业结算卡', icon: 'none' });
				return;
			}
			const money = Number(this.apply_money);
			if (!this.apply_money || isNaN(money) || money <= 0) {
				uni.showToast({ title: '请输入正确的提现金额', icon: 'none' });
				return;
			}
			if (money > Number(this.balance_money)) {
				uni.showToast({ title: '提现金额不能超过当前可提现金额', icon: 'none' });
				return;
			}
			// 最低提现额度：后台 min = 0 时不限制，也不展示
			if (Number(this.min) > 0 && money < Number(this.min)) {
				uni.showToast({ title: '单笔最低可提现' + this.min + '元', icon: 'none' });
				return;
			}
			if (Number(this.max) > 0 && money > Number(this.max)) {
				uni.showToast({ title: '单笔最高可提现金额为' + this.max + '元', icon: 'none' });
				return;
			}
			memberwithdrawapply({ apply_money: money }).then(res => {
				if (res && res.code == 0) {
					uni.showToast({
						title: '申请已提交，请等待审核',
						icon: 'none',
						success: () => {
							setTimeout(() => {
								this.apply_money = '';
								this.resetCalc();
								this.fetchConfig();
							}, 1500);
						}
					});
				} else {
					uni.showToast({ title: (res && (res.message || res.msg)) || '提交失败，请重试', icon: 'none' });
				}
			});
		},
		gopage() {
			if (this.hasBindCard) {
				uni.navigateTo({ url: '/otherpages/cashInfo/cashInfo' });
			} else {
				uni.navigateTo({ url: '/otherpages/bankinfo/bankinfo' });
			}
		},
		openRuleDetail() {
			if (this.$refs.rulePopup && typeof this.$refs.rulePopup.open === 'function') {
				this.$refs.rulePopup.open();
			}
		},
		closeRule() {
			if (this.$refs.rulePopup && typeof this.$refs.rulePopup.close === 'function') {
				this.$refs.rulePopup.close();
			}
		}
	},
	watch: {
		apply_money() {
			this.watchMoney();
		}
	}
}
</script>

<style lang="scss">
.page-withdraw {
	min-height: 100vh;
	background: #fff;
	padding-bottom: 200rpx;
}

.btms {
	position: fixed;
	bottom: 0;
	left: 0;
	background: #fff;
	text-align: center;
	width: 100vw;
	z-index: 99;

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
		font-size: 48rpx;
		margin-right: 20rpx;
	}

	input {
		height: 136rpx;
		background: #FFFFFF;
		font-size: 72rpx;
		color: #222222;
		flex: 1;
	}

	.ph {
		color: #BBBBBB;
		font-size: 56rpx;
	}

	text:last-child {
		font-weight: 400;
		font-size: 26rpx;
		color: #F55606;
		flex-shrink: 0;
	}
}

.cash {
	padding: 0 32rpx;
	background: #FFF5ED;
	height: 72rpx;

	.cash-tip {
		font-weight: 400;
		font-size: 28rpx;
		color: #FE780F;
		flex: 1;
		min-width: 0;
	}
}

.balance-tip {
	color: #999999;
	font-size: 26rpx;
}

.calc {
	background: #FFFBF6;
	border: 1rpx solid #FFE2C4;
	border-radius: 12rpx;
	padding: 20rpx 24rpx;

	.calc-row {
		font-size: 26rpx;
		line-height: 44rpx;
	}

	.calc-label {
		color: #999999;
	}

	.calc-value {
		color: #333333;

		&.arrive {
			color: #F55606;
			font-weight: 600;
		}
	}
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
		margin-bottom: 8rpx;

		.rules-icon {
			width: 8rpx;
			height: 28rpx;
			background: linear-gradient(180deg, #F5AB18 0%, #F55606 100%);
			border-radius: 4rpx;
			margin-right: 14rpx;
		}
	}

	.rule-item {
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
		padding: 14rpx 0;
	}

	.rule-label {
		font-size: 26rpx;
		color: #999999;
		flex-shrink: 0;
		margin-right: 24rpx;
	}

	.rule-value {
		font-size: 26rpx;
		color: #333333;
		text-align: right;
		flex: 1;
	}

	.rule-foot {
		margin-top: 12rpx;
		padding-top: 16rpx;
		border-top: 1rpx dashed #E5E5E5;
		font-size: 24rpx;
		color: #F55606;
	}
}

.intro {
	font-size: 24rpx;
	color: #999999;
	line-height: 40rpx;
}

.rule-link {
	margin-top: 24rpx;
	font-size: 24rpx;
	color: #F55606;
}

.ellipsis {
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.nos1 {
	background: #F5F5F5;
	border-radius: 12rpx;
	padding: 20rpx;

	>view {
		box-sizing: border-box;
		width: 50%;
		position: relative;
		min-width: 0;

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

.rule-detail {
	background: #fff;
	border-radius: 24rpx 24rpx 0 0;
	padding: 36rpx 40rpx calc(40rpx + env(safe-area-inset-bottom));

	.rule-detail-title {
		font-size: 32rpx;
		font-weight: 600;
		color: #222222;
		margin-bottom: 24rpx;
	}

	.rule-detail-body {
		max-height: 62vh;
	}

	.rule-detail-item {
		margin-bottom: 18rpx;
	}

	.rd-label {
		font-size: 26rpx;
		color: #222222;
		font-weight: 600;
	}

	.rd-value {
		display: block;
		font-size: 26rpx;
		color: #666666;
		line-height: 40rpx;
		margin-top: 6rpx;
	}

	.rd-sub {
		display: block;
		margin: 24rpx 0 12rpx;
	}

	.rd-text {
		font-size: 24rpx;
		color: #999999;
		line-height: 42rpx;
		white-space: pre-line;
	}

	.rule-detail-close {
		margin-top: 32rpx;
		line-height: 88rpx;
		text-align: center;
		border-radius: 44rpx;
		background: linear-gradient(90deg, #F5AB18 0%, #F55606 100%);
		color: #fff;
		font-size: 30rpx;
		font-weight: 600;
	}
}

page {
	background: #fff;
}
</style>
