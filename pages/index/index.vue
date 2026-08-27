<template>
	<view>
		<uv-navbar bg-color="#ffffff00" :fixed="false">
			<template v-slot:left>
				<view class="maps acea-row row-middle" v-if="isopen" @click="switchCity">
					<image src="/static/map1.png" class="w-32 h-32 m-r-8" mode=""></image>
					{{adddressinfo.city||''}}
					<text class="city-arrow">▾</text>
				</view>
			</template>
			<template v-slot:center>
				<view class="maps">
					城盟优选
				</view>
			</template>
		</uv-navbar>
		<image src="/static/bg1.png" class="bgs" mode=""></image>
		<template v-if="isopen&&website_id">
			<view class="swipers">
				<swiper :indicator-dots="false" :autoplay="true" @change="onchange" :interval="3000" :duration="1000">
					<swiper-item v-for='(item,index) in banner' @click="gonexts(item)" :key="index">
						<view class="swiper-item">
							<image :src="$util.img(item.image)" mode=""></image>
						</view>
					</swiper-item>
				</swiper>
				<view class="dots">
					<view v-for="(item,index) in banner.length" :class="{'on':current==index}" :key="index"></view>
				</view>
			</view>
			<view class="parts1 acea-row row-middle p-l-40 p-t-20">
				<image src="/static/bg2.png" mode=""></image>
				<view class="">
					<view class="">{{usenum.shop_num||0}} <text>家</text></view>
					<view class="acea-row row-middle" @click="gonext('Synopsis')">入驻商家
						<view class="">
							<uv-icon name="arrow-right" color="#fff" size="6"></uv-icon>
						</view>
					</view>
				</view>
				<view class="leftline">
					<view class="">{{usenum.use_num||0}} <text>人</text></view>
					<view class="acea-row row-middle">使用人数
					</view>
				</view>
			</view>
			<view class="">
				<uv-grid :col="5">
					<uv-grid-item v-for="(item,index) in navlist" @click="gonext(item)" :key="index">
						<image :src="$util.img(item.image)" class="m-t-32 w-80 h-80" mode=""></image>
						<text class="m-t-16">{{item.name}}</text>
					</uv-grid-item>
					<uv-grid-item @click="gonext('all')" :key="index">
						<image src="/static/alls.png" class="m-t-32 w-80 h-80" mode=""></image>
						<text class="m-t-16">全部品牌</text>
					</uv-grid-item>
				</uv-grid>
			</view>
			<view class="part2 acea-row row-middle row-between">
				<view class="" @click="gonext('douyins')">
					<image src="/static/bg4.png" mode=""></image>
					<view class="">同城抖音直播</view>
					<view class="">好尽在美好生活</view>
				</view>
				<view class="" @click="gonext('memberlevel')">
					<image src="/static/bg3.png" mode=""></image>
					<view class="">金钻会员</view>
					<view class="" style="color: #CC8B41;">立享全场9.5折</view>
				</view>
			</view>
			<view class="container">
				<filter @sorts='sorts'></filter>
				<goods :list='list'></goods>
				<login ref="login"></login>
			</view>
			<view class="h-200"></view>
		</template>
		<template v-else>
			<view class="empts">
				<image src="/static/empty.png" mode=""></image>
				<view class="" v-if="!isopen">请允许获取您的位置</view>
				<view class="" v-if="isopen&&!show">抱歉，本城市暂未开通城创联盟~</view>
			</view>
		</template>
		<button open-type="contact" class="kefu">
			<image src="/static/kefu.png" mode=""></image>
		</button>
	</view>
</template>

<script>
	import {
		bannerList,
		tranAddressInfo,
		websiteInfo,
		navList,
		selectCity,
		goodslist
	} from "@/common/js/api.js"
	import login from "@/components/login/login.vue"
	export default {
		components: {
			login
		},
		data() {
			return {
				current: 0,
				adddressinfo: {},
				usenum: {},
				show: false,
				isopen: false,
				website_id: '',
				banner: [],
				navlist: [],
				page: 1,
				last_page: 1,
				list: [],
				select: -1,
				clicknum: 0,
				isManualCity: false
			}
		},
		async onLoad(res) {
			// 已有已选城市（手动切换或历史定位）时不重新定位，避免切换后又被拉回定位城市
			if (uni.getStorageSync('website_id')) {
				this.restoreCity()
			} else {
				await this.getareas()
			}
			if (res.scene) {
				var sceneParams = decodeURIComponent(res.scene);
				sceneParams = sceneParams.split('&');
				if (sceneParams.length) {
					sceneParams.forEach(item => {
						if (item.indexOf('suid') != -1) uni.setStorageSync('invite_code', item.split('-')[1]);
					});
				}
			}
		},
		onPullDownRefresh() {


		},
		onReachBottom() {
			if (this.last_page > this.page) {
				this.page++
				console.log(this.page);
				this.getlist()
			}
		},
		async onShow() {
			// 手动切换城市期间/之后不再重新定位，避免把已选城市切回定位城市
			if(!uni.getStorageSync('website_id') && !this.isManualCity){
				await this.getareas()
			}
			// await this.bannerList()
			// await this.websiteInfo()
			// await this.navList()
		},
		methods: {
			// 恢复已选城市：不重新定位，直接使用缓存中的城市/门店并加载数据
			restoreCity() {
				const addressInfo = uni.getStorageSync('address_info')
				const websiteId = uni.getStorageSync('website_id')
				if (addressInfo) {
					this.$set(this, 'adddressinfo', addressInfo)
				}
				this.website_id = websiteId || ''
				this.isopen = true
				this.show = true
				this.page = 1
				this.bannerList()
				this.websiteInfo()
				this.navList()
				this.getlist()
			},
			gonexts(e) {
				if (e.url) {
					if (!uni.getStorageSync('token')) {
						this.$refs.login.open()
						return
					}
					uni.navigateTo({
						url: '/otherpages/storeSign/storeSign'
					})
				}
			},
			sorts(e) {
				console.log(e);
				this.select = e.select
				this.clicknum = e.clicknum
				this.page = 1
				this.getlist()
			},
			async getlist() {
				// this.$refs.login.open()
				let params = {
					page: this.page,
					page_size: 30,
					category_id: "",
					longitude: "",
					latitude: "",
					order: "",
					sort: "",
					keyword: "",
					site_id: "",
					order: this.select == 2 ? 'sale_num' : this.select == 3 ? 'distance' : '',
					sort: this.clicknum == 1 ? 'asc' : this.clicknum == 2 ? 'desc' : '',
					website_id: this.website_id
				}
				// let res= await goodslist(params)
				// console.log(666,res);
				// return
				goodslist(params).then(res => {
					this.last_page = res.data.page_count
					this.list = this.page > 1 ? this.list.concat(...res.data.list) : res.data.list
					// this.list.push(res.data.list[0])
				})
			},
			gonext(e) {
				// this.$refs.login.open()
				// return
				if (e == 'Synopsis') {
					uni.navigateTo({
						url: '/otherpages/Synopsis/Synopsis'
					})
					return
				}
				if (e == 'douyins') {
					uni.navigateTo({
						url: '/otherpages/douyins/douyins'
					})
					return
				}
				if (e == 'memberlevel') {
					if (!uni.getStorageSync('token')) {
						this.$refs.login.open()
						return
					}
					uni.navigateTo({
						url: '/otherpages/memberlevel/memberlevel'
					})
				}
				if (e == 'all') {
					uni.navigateTo({
						url: '/otherpages/brandAll/brandAll'
					})
					return
				}
				let info = JSON.parse(e.url)
				let id = this.getUrlParams(info.wap_url).category_id
				// ley id=
				uni.navigateTo({
					url: `/otherpages/cateGoods/cateGoods?id=${id}&name=${e.name}`
				})
			},
			getUrlParams(url) {
				// 创建参数对象
				const params = {};

				// 查找问号的位置，分割出查询字符串部分
				const queryIndex = url.indexOf('?');
				if (queryIndex === -1) {
					// 没有参数，返回空对象
					return params;
				}

				// 获取问号后面的查询字符串（如 "category_id=11"）
				const queryString = url.slice(queryIndex + 1);

				// 分割多个参数（如果有多个的话，用 & 分割）
				const paramPairs = queryString.split('&');

				// 遍历每个参数对，解析键值
				paramPairs.forEach(pair => {
					// 分割键和值（用 = 分割）
					const [key, value] = pair.split('=');
					if (key) {
						// 解码URL编码的字符（如空格可能被编码为 %20）
						params[decodeURIComponent(key)] = decodeURIComponent(value || '');
					}
				});

				return params;
			},
			navList() {
				let params = {
					website_id: this.website_id
				}
				navList(params).then(res => {
					this.navlist = res.data
				})
			},
			getareas() {
				uni.getSetting({
					success: res => {
						if (res.authSetting && res.authSetting['scope.userLocation']) {
							this.isopen = true
							this.getareas1()
						} else {
							this.isopen = res.authSetting['scope.userLocation']
							uni.showModal({
								content: '为了您更好的体验，请确认获取您的位置',
								success: res => {
									if (res.confirm) {
										uni.openSetting({
											success(res) {
												console.log(res
													.authSetting)
											},
											fail: err => {
												console.log(err);
											}
										});
									}
								}
							})
						}
					}
				})
			},
			getareas1() {
				uni.getLocation({
					success: async res => {
						let params = {
							latlng: res.latitude + ',' + res.longitude
							// latlng: '34.759108,113.77855'
						}
						uni.setStorageSync('latlng', {
							latitude: res.latitude,
							longitude: res.longitude
						})
						await tranAddressInfo(params).then(async resA => {
							this.adddressinfo = resA.data
							uni.setStorageSync('address_info', resA.data)
							selectCity({
								city_id: resA.data.city_id
							}).then(async resB => {
								if (resB.code == 0) {
									uni.setStorageSync('website_id', resB.data.site_id)
									this.website_id = resB.data.site_id
									this.show = true
									await this.bannerList()
									await this.websiteInfo()
									await this.navList()
									await this.getlist()
								} else {
									this.show = false
								}
							})
							// if(resA.data.city_id){

							// }
						})
					},
				})
			},
			// 点击城市切换：打开地图选点，按所选位置重新加载对应城市数据
			switchCity() {
				// 标记为手动切换城市，拦截 onShow 自动重新定位
				this.isManualCity = true
				uni.chooseLocation({
					success: (res) => {
						uni.setStorageSync('latlng', {
							latitude: res.latitude,
							longitude: res.longitude
						})
						this.changeCity(res.latitude + ',' + res.longitude)
					},
					fail: () => {}
				})
			},
			// 按经纬度切换城市：经纬度转城市 -> 查询门店 -> 刷新首页数据
			changeCity(latlng) {
				uni.showLoading({
					title: '切换中'
				})
				// 切换城市前先清掉旧门店缓存，避免请求自动携带旧 website_id 仍返回原城市/门店数据
				uni.removeStorageSync('website_id')
				this.website_id = ''
				tranAddressInfo({
					latlng
				}).then(resA => {
					console.log('切换城市-tranAddressInfo', resA)
					if (!resA.data || !resA.data.city_id) {
						uni.hideLoading()
						this.isManualCity = false
						uni.showToast({
							title: '未识别到该城市',
							icon: 'none'
						})
						return Promise.reject('city_id missing')
					}
					this.$set(this, 'adddressinfo', resA.data)
					// 记住手动选择的城市，后续进入页面不再重新定位覆盖
					uni.setStorageSync('address_info', resA.data)
					return selectCity({
						city_id: resA.data.city_id
					})
				}).then(resB => {
					uni.hideLoading()
					if (resB.code == 0 && resB.data && resB.data.site_id) {
						uni.setStorageSync('website_id', resB.data.site_id)
						this.website_id = resB.data.site_id
						this.isopen = true
						this.show = true
						// 重置分页并清空旧列表，保证新城市数据完整替换
						this.page = 1
						this.list = []
						this.bannerList()
						this.websiteInfo()
						this.navList()
						this.getlist()
					} else {
						this.isManualCity = false
						this.show = false
						uni.showToast({
							title: '该城市暂未开通城创联盟',
							icon: 'none'
						})
					}
					}).catch(e => {
					uni.hideLoading()
					this.isManualCity = false
					if (e !== 'city_id missing') {
						uni.showToast({
							title: '切换失败，请重试',
							icon: 'none'
						})
					}
					})
			},
			bannerList() {
				let params = {
					website_id: this.website_id
				}
				bannerList(params).then(res => {
					this.banner = res.data
				})
			},
			websiteInfo() {
				let params = {
					website_id: this.website_id
				}
				websiteInfo(params).then(res => {
					this.usenum = res.data
				})
			},
			onchange(e) {
				this.current = e.detail.current
			}
		}
	}
</script>

<style scoped lang="scss">
	.empts {
		text-align: center;
		margin-top: 264rpx;

		>image {
			width: 340rpx;
			height: 340rpx;
		}

		>view {
			font-weight: 600;
			font-size: 32rpx;
			color: #222222;
			margin-top: 24rpx;
		}
	}

	.kefu {
		position: fixed;
		width: 124rpx;
		height: 124rpx;
		bottom: 15vh;
		right: 0;
		z-index: 999;
		padding: 0;
		margin: 0;
		background: #ffffff00;

		image {
			width: 100%;
			height: 100%;
		}
	}

	.container {
		background: #fff;
		margin-top: 32rpx;
		padding: 0 32rpx;
		border-radius: 24rpx 24rpx 16rpx 16rpx;
		min-height: 30vh;
		padding-bottom: 30rpx;
		// >view:first-child {
		// 	padding: 24rpx 0;
		// }
	}

	.part2 {
		margin: 32rpx;

		>view {
			width: calc((100% - 16rpx)/2);
			height: 116rpx;
			position: relative;
			box-sizing: border-box;
			padding: 20rpx 28rpx;

			>image {
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
				z-index: -1;
			}

			>view:nth-child(2) {
				font-weight: 600;
				font-size: 30rpx;
				color: #222222;
			}

			>view:nth-child(3) {
				font-weight: 400;
				font-size: 22rpx;
				color: #EC7B7B;
			}
		}
	}

	.parts1 {
		margin: 12rpx 32rpx 0 32rpx;
		height: 162rpx;
		position: relative;
		box-sizing: border-box;

		>image {
			position: absolute;
			width: 100%;
			height: 100%;
			left: 0;
			top: 0;
			z-index: -1;
		}

		.leftline {
			border-left: #5D5A66 1rpx solid;
			margin-left: 90rpx;
			padding-left: 30rpx;
		}

		>view {

			>view:first-child {
				font-size: 60rpx;
				color: #FFDE99;
				font-weight: bold;
				line-height: 1;

				text {
					font-weight: 600;
					font-size: 28rpx;
					color: #FFDE99;
				}
			}

			>view:last-child {
				font-weight: 400;
				font-size: 24rpx;
				color: #A38B5B;

				view {
					width: 20rpx;
					height: 20rpx;
					background: #A38B5B;
					border-radius: 10rpx;
					display: flex;
					align-items: center;
					justify-content: center;
					margin-left: 4rpx;
				}
			}
		}
	}

	.swipers {
		margin: 12rpx 32rpx 0 32rpx;
		height: 290rpx;
		border-radius: 16rpx;
		overflow: hidden;
		position: relative;

		swiper {
			width: 100%;
			height: 100%;

			view,
			image {
				width: 100%;
				height: 100%;
				border-radius: 16rpx;
			}
		}

		.dots {
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			bottom: 20rpx;
			display: flex;
			align-items: center;
			justify-content: center;

			>view {
				width: 24rpx;
				height: 8rpx;
				background: #FFFFFF;
				border-radius: 6rpx;
				opacity: 0.28;
				margin-right: 8rpx;
			}

			.on {
				background: #FFFFFF;
				opacity: 1;
			}
		}
	}

	.wbg {
		background: #fff;
	}

	.maps {
		font-weight: 600;
		font-size: 32rpx;
		color: #FFFFFF;
	}

	.city-arrow {
		font-size: 46rpx;
		color: #FFFFFF;
	}

	.bgs {
		position: absolute;
		width: 100vw;
		height: 440rpx;
		top: 0;
		left: 0;
		z-index: -1;
	}
</style>