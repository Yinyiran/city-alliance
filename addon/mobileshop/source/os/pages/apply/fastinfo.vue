<template>
	<view class="fastinfo">
		<view class="fastinfo-title">填写开店信息</view>
		<view class="fastinfo-input">
			<view class="input-left">
				<text class="left-xin">*</text>
				<text>店铺名称</text>
			</view>
			<view class="input-right">
				<input type="text" value="" placeholder="请输入店铺名称" placeholder-class="right-input" v-model="shopName" @blur="blurShopName" />
				<text class="input-text">注意：店铺名称注册后不可修改。</text>
			</view>
		</view>
		<view class="fastinfo-input">
			<view class="input-left">
				<text class="left-xin">*</text>
				<text>城市分站</text>
			</view>
			<view class="input-right">
				<picker :range="cityList" @change="cityChange" value="index">
					<view>{{ cityName }}</view>
				</picker>
				<text class="input-text">注意：城市分站注册后不可修改。</text>
			</view>
		</view>
		<view class="fastinfo-input">
			<view class="input-left">
				<text class="left-xin">*</text>
				<text>主营行业</text>
			</view>
			<view class="input-right">
				<picker :range="industryList" @change="industryChange" value="index">
					<view>{{ industryName }}</view>
				</picker>
			</view>
		</view>
		<view class="fastinfo-btn" @click="toIn">快速入驻</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			shopName: '', //店铺名称
			industryList: [], //主营行业数组
			industryName: '请选择主营行业', //主营行业名称
			industryNumList: [], //主营行业ID数组
			industryNum: '', //主营行业ID
			cityList: [], //城市分站数组
			cityName: '请选择城市分站', //城市分站名称
			cityNumList: [], //城市分站ID数组
			cityNum: '', //城市分站ID
			isShop: false //店铺名称是否存在
		};
	},
	methods: {
		//店铺名称失去焦点
		blurShopName(e) {
			thsi.$api.sendRequest({
				url: '/shopapi/apply/shopNameExist',
				data: {
					shop_name: e.detail.value
				},
				success: res => {
					console.log(res);
					if (res.code !== 0) {
						uni.showToast({
							title: res.message,
							icon: 'none'
						});
						this.isShop = !this.isShop;
					}
				}
			});
		},
		//城市分站选择器
		cityChange(e) {
			this.cityName = this.cityList[e.detail.value];
			this.cityNum = this.cityNumList[e.detail.value];
		},
		//主营行业选择器
		industryChange(e) {
			this.industryName = this.industryList[e.detail.value];
			this.industryNum = this.industryNumList[e.detail.value];
		},
		//点击快速入驻
		toIn() {
			if (this.shopName !== '') {
				if (this.cityName !== '请选择城市分站') {
					if (this.industryName !== '请选择主营行业') {
						if (this.isShop == false) {
							this.$api.sendRequest({
								url: '/shopapi/apply/experienceApply',
								data: {
									site_name: this.shopName, //发送店铺名称
									category_id: this.industryNum, //主营行业id
									category_name: this.industryName, //主营行业名称
									website_id: this.cityNum //城市分站id
								},
								success: res => {
									uni.showToast({
										title: res.message,
										icon: 'none'
									});
									if(res.code == 0){
										util.redirectTo('/pages/apply/successfully',{},'redirectTo')
									}
								}
							});
						} else {
							uni.showToast({
								title: '店铺名称已存在',
								icon: 'none'
							});
						}
					} else {
						uni.showToast({
							title: '请选择主营业务',
							icon: 'none'
						});
					}
				} else {
					uni.showToast({
						title: '请选择城市分站',
						icon: 'none'
					});
				}
			} else {
				uni.showToast({
					title: '请填写店铺名称',
					icon: 'none'
				});
			}
		}
	},
	onReady() {
		this.$api.sendRequest({
			url: '/shopapi/apply/index',
			success: res => {
				//城市分站
				res.data.web_city.forEach(item => {
					this.cityList.push(item.site_area_name);
				});
				res.data.web_city.forEach(item => {
					this.cityNumList.push(item.site_area_id);
				});
				//所属行业
				res.data.shop_category.data.forEach(item => {
					this.industryList.push(item.category_name);
				});
				res.data.shop_category.data.forEach(item => {
					this.industryNumList.push(item.category_id);
				});
			}
		});
	}
};
</script>

<style lang="scss">
page {
	background-color: #fff;
	overflow: hidden;
}
.fastinfo {
	width: 100%;
	height: 100%;
	.fastinfo-title {
		font-size: 40rpx;
		font-weight: bold;
		text-align: center;
		margin-top: 200rpx;
		margin-bottom: 50rpx;
	}
	.fastinfo-input{
		margin: 0 $margin-both $margin-both;
		display: flex;
		.input-left{
			width:160rpx;
			padding: 6rpx 10rpx;
			text-align: right;
			.left-xin {
				color: #ff0000;
				margin-right: 6rpx;
			}
		}
		.input-right {
			flex: 4;
			margin-left: 20rpx;
			input{
				border: 1px solid $color-line;
				border-radius: 6rpx;
				padding: 6rpx 10rpx;
			}
			.right-input {
				font-size: 28rpx;
				margin-left: 10rpx;
			}
			.input-text {
				font-size: 24rpx;
				color: #ff0000;
			}
			picker {
				border: 1px solid #e6e6e6;
				border-radius: 6rpx;
				padding: 6rpx 10rpx;
				color: #757575;
			}
		}
	}
	.fastinfo-btn {
		width: 200rpx;
		height: 60rpx;
		background-color: #ff8143;
		color: #fff;
		margin: 0 auto;
		text-align: center;
		line-height: 60rpx;
		border-radius: 6rpx;
		margin-top: 60rpx;
	}
}
</style>
