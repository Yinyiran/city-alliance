<template>
	<view>
		<swiper :indicator-dots="false" :autoplay="false" :vertical='true' v-if="status==0"  @change="onchange" :current="current"
			@animationfinish="changeSwiper">
			<swiper-item v-for="(item,index) in list" :key="index">
				<view class="muteds" @click="muted=!muted">
					<image src="/static/muted.png" class="w-82 h-82" v-if="muted" mode=""></image>
					<image src="/static/muted1.png" class="w-82 h-82" v-else mode=""></image>
				</view>
				<view class="Circular" style="">
					<CircularProgress @getpoint='getpoint' @restart='restart' @godetail='godetail' ref="progress"
						:point='point' :target='second'></CircularProgress>
					<!-- <cover-view class="btns"></cover-view> -->
				</view>
				<view class="swiper-item" >
					<video :src="$util.img(item.video_url)" :muted='muted' :loop='true' @loadedmetadata='loadedmetadata'
						v-if="item.video_url" @play="onPlay" :play-strategy='2' @click="isplay()" :ref='videos' object-fit="cover"
						:controls="false" @timeupdate="onTimeUpdate" :autoplay='false' :show-center-play-btn="true"
						:play-btn-position='center' :id="`video${index}`">
					</video>
					<view class="goods acea-row row-middle row-between" @click="godetail(item.sku_id)">
						<image :src="$util.img(item.sku_image)" class="w-110 h-110 bor-16" mode=""></image>
						<view class="acea-row row-between row-column h-110">
							<view class="text-more">{{item.goods_name}}</view>
							<view class="acea-row row-middle prices row-between">
								<view class="acea-row row-column row-between" style="flex-wrap: nowrap;">
									<view class="acea-row row-middle">
										<view class="">限时特价</view>
										<view class="">{{goods_type(item.goods_type,item.is_admin)==3?'全国包邮':'门店核销'}}</view>
									</view>
									<view class="acea-row row-middle">
										<text>￥</text>
										{{item.price}}
										<view class="m-l-8" v-if="item.market_price!='0.00'">¥{{item.market_price}}
										</view>
									</view>
								</view>
								<image src="/static/qiang.png" class="w-106 h-64" mode="aspectFit"></image>
							</view>
						</view>
					</view>
					<view class="rights">
						<view class="a1">
							<image :src="$util.img(item.logo)" class="i1" mode=""></image>
							<view class="" v-if="showa==0" @click="guanzhu(item.site_id)">关注</view>
							<view class="" v-else>已关注</view>
						</view>
						<view class="a2" @click="open2(item.id)">
							<image src="/static/icon1.png" mode=""></image>
							<view class="">{{item.order_num}}</view>
						</view>
						<view class="a2" @click="open1(item.id)">
							<image src="/static/icon2.png" mode=""></image>
							<view class="">{{item.comment_num}}</view>
						</view>
						<button open-type="share">
							<view class="a2">
								<image src="/static/icon3.png" mode=""></image>
								<view class="">分享</view>
							</view>
						</button>
					</view>
				</view>
			</swiper-item>
		</swiper>
		<view class="m-24" v-else>
			<goods :list="list"></goods>
		</view>
		<uv-popup ref="popup1" :round='10' :closeable="true">
			<view class="orders">
				<view class="p-b-32">共{{list[current]?list[current].order_num:0}} 条订单</view>
				<scroll-view scroll-y="true">
					<view v-for="(item,index) in listB" class="orderitems acea-row m-b-32 row-between" :key="index">
						<image :src="$util.img(item.headimg)" class="w-72 h-72 bor-32 m-r-20" mode=""></image>
						<view class="">
							<view class="acea-row row-middle row-between">
								<view class="">{{item.nickname}}</view>
								<view class="">已下单</view>
							</view>
							<view class="acea-row row-middle">
								<image class="w-144 h-144 bor-16 m-r-16" :src="$util.img(item.sku_image)" mode="">
								</image>
								<view class="h-144">{{item.sku_name}}</view>
							</view>
							<view class="acea-row row-middle row-between m-t-8 m-b-24">
								<text style="color: #999999;">{{item.create_time}}</text>
								<text style='color: #555555;'>¥{{item.goods_money}}</text>
							</view>
							<uv-line></uv-line>
						</view>
					</view>
				</scroll-view>
			</view>
		</uv-popup>
		<uv-popup ref="popup" :zIndex="10010" :round='10' :closeable="true">
			<view class="part">
				<view class="">
					评论
				</view>
				<scroll-view scroll-y="true" @scrolltolower="scrolltolower">
					<view v-for="(item,index) in listA" class="pinglunitem" :key="index">
						<image :src="$util.img(item.member.headimg)" v-if="item.member.headimg" mode=""></image>
						<image :src="$util.img('/upload/default/default_img/head.png')" v-else mode=""></image>
						<view class="">
							<view class="">{{item.member.nickname}}</view>
							<view class="">{{item.content}}</view>
							<view class="">
								<view class="">{{item.create_time}}</view>
								<view :class="{'on':item.is_like==1}" @click="dianzan2(item,index)">
									<uv-icon name="heart-fill" v-if="item.collection_status==1"
										color="#FA7806"></uv-icon>
									<uv-icon name="heart" v-else color="#999"></uv-icon>
									{{item.collect_count}}
								</view>
							</view>
						</view>
					</view>
					<view class="" style="height: 100rpx;"></view>
				</scroll-view>
				<view class="acea-row row-middle row-between">
					<textarea type="text" v-model="content" :auto-height='true' placeholder="一起发评论吧" />
					<view class="" @click="sends">发送</view>
				</view>
			</view>
		</uv-popup>
		<login ref="login" @loginsuccess='loginsuccess'></login>
	</view>
</template>

<script>
	import {
    commcollect,
    commentCreate,
    commentLists,
    issubscribe,
    pointAdd,
    reviewStatus,
    shareAdd,
    shopmemberAdd,
    vDetail,
    videoList,
    vorderList
} from "@/common/js/api.js";
	export default {
		data() {
			return {
				list: [],
				page: 1,
				current: 0,
				videoContexts: [],
				showa: 0,
				page1: 1,
				last_page1: 1,
				listA: [],
				content: '',
				timesecond: 0,
				rewardTimer: null,
				target: 0,
				frequency: 0,
				isTimerRunning: false,
				token: "",
				page2: 1,
				last_page2: 1,
				listB: [],
				muted: false,
				playing: true,
				status:1
				// point: 0
			};
		},
		async onLoad(res) {
			if(res.id){
				this.shareAdd(res.id)
			}
			await this.getlist()
			await this.isguanzhu()
		},
		onShareAppMessage() {
			return {
				path:"/pages/classify/classify?id="+this.list[this.current].id,
				success:()=>{
					console.log('分享成功');
				},
				fail:e=>{
					console.log(e);
				},
				complete:()=>{
					console.log(333);
				}
			}
		},
		onShow() {
			this.token = uni.getStorageSync('token') || ''
			this.reviewStatus()
		},
		onHide() {
			this.clearRewardTimer()
			this.timesecond = 0
			this.target = 0
		},
		computed: {
			conlist() {
				this.videoContexts = []
				if (this.list.length > 0) {
					this.list.forEach((item, index) => {
						this.videoContexts.push(uni.createVideoContext("video" + index))
					})
					// this.videoContexts.map((item) => {
					// 	item.pause()
					// })
				}

			},

			second() {
				if (this.list.length <= 0) return 0
				let info = this.list[this.current]
				return info.second
			},
			point() {
				if (this.list.length <= 0) return 0
				let info = this.list[this.current]
				return info.point
			},
			point_status() {
				if (this.list.length <= 0) return 0
				let info = this.list[this.current]
				return info.point_status
			},
			percent() {
				return (this.currentTime / this.duration) * 100
			},
		},
		methods: {
			goods_type(goods_type,is_admin) {
				if (is_admin == 1) {
					return 3
				}
				if (goods_type == 0) {
					return 1
				}
				if (goods_type == 1) {
					return 2
				}
				return 1
			},
			reviewStatus(){
				reviewStatus().then(res=>{
					this.status=res.data.status
				})
			},
			shareAdd(e){
				let params={
					id:e
				}
				shareAdd(params).then(res=>{
					
				})
			},
			async scrolltolower() {
				if (this.last_page1 > this.page1) {
					this.page1++
					await this.getcommentLists()
				}
			},
			getdetail() {
				let params = {
					id: this.list[this.current].id,
				}
				vDetail(params).then(res => {
					if (res.code == 0) {
						this.list[this.current] = res.data
					}
				})
			},
			loginsuccess() {
				this.token = uni.getStorageSync('token')
			},
			restart() {
				this.isTimerRunning = false
				this.target = 0
				this.timesecond = 0
				this.targets()
			},
			async getpoint() {
				if (!this.token) return
				let params = {
					id: this.list[this.current].id,
				}
				await pointAdd(params).then(async res => {
					if (res.code == 0) {
						await this.getdetail()
						this.$refs.progress[this.current].succs()
					}
				})
			},
			isplay() {
				if (this.playing){
					this.playing=false
					this.videoContexts[this.current].pause();
				}else{
					this.playing=true
					this.videoContexts[this.current].play();
				}
				this.targets()
				// this.videoContexts[this.current].play();
				// this.videoContexts[e].pause();
			},
			onPlay() {
				return
				console.log(this.timesecond, this.second, this.isTimerRunning);
				if (this.timesecond < this.second) {
					this.targets();
				}
			},
			targets() {
				if (!this.playing) return
				if (this.isTimerRunning) return;
				if (this.point_status != 1) return
				if (this.list.length > 0) {
					this.isTimerRunning = true;
					this.$refs.progress[this.current].start()
					this.timesecond = 0
					this.rewardTimer = setInterval(async () => {
						this.timesecond++
						// this.target = Math.round((this.timesecond / this.second) * 100)
						if (this.timesecond == this.second) {
							this.clearRewardTimer();
							await this.getpoint()
							// this.isTimerRunning = false;
							this.timesecond = 0
						}
					}, 1000)
				}
			},
			clearRewardTimer() {
				if (this.rewardTimer) {
					clearInterval(this.rewardTimer);
					this.rewardTimer = null;
					console.log('定时器已清理');
				}
			},
			async open2() {
				await this.getorderlist()
				this.$refs.popup1.open('bottom')
			},
			async getorderlist() {
				let params = {
					id: this.list[this.current].id,
					page: this.page2
				}
				await vorderList(params).then(res => {
					if (res.code == 0) {
						this.listB = this.page2 > 1 ? this.listB.concat(...res.data.list) : res.data.list
						this.last_page2 = res.data.page_count
					}
				})
			},
			godetail(e) {
				uni.navigateTo({
					url: '/otherpages/goods_detail/goods_detail?id=' + this.list[this.current].sku_id+'&video_id='+this.list[this.current].id
				})
			},
			dianzan2(item, index) {
				if (!this.token) {
					this.$refs.login.open()
					return
				}
				let params = {
					category_id: item.id
				}
				commcollect(params).then(res => {
					if (res.code == 0) {
						this.listA[index].collection_status = this.listA[index].collection_status == 0 ? 1 : 0
						if (this.listA[index].collection_status == 1) {
							this.listA[index].collect_count++
						} else {
							this.listA[index].collect_count--
						}
					}
				})
			},
			async sends() {
				if (!this.token) {
					this.$refs.login.open()
					return
				}
				if (!this.content) {
					uni.showToast({
						title: '请输入内容',
						icon: "none"
					})
					return
				}
				let params = {
					category_id: this.list[this.current].id,
					content: this.content
				}
				await commentCreate(params).then(async res => {
					if (res.code == 0) {
						this.content = ''
						await this.getcommentLists()
						await this.getdetail()
					}
				})
			},
			async open1(e) {
				this.page1 = 1
				await this.getcommentLists()
				this.$refs.popup.open('bottom')
			},
			async getcommentLists() {
				let params = {
					id: this.list[this.current].id,
				}
				await commentLists(params).then(res => {
					if (res.code == 0) {
						this.listA = this.page1 > 1 ? this.listA.concat(...res.data.list) : res.data.list
						this.last_page1 = res.data.page_count
					}
				})
			},
			isguanzhu() {
				if (!this.token) return
				let params = {
					site_id: this.list[this.current].site_id,
				}
				issubscribe(params).then(res => {
					if (res.code == 0) {
						this.showa = res.data
					}
				})
			},
			guanzhu(e) {
				if (!this.token) {
					this.$refs.login.open()
					return
				}
				let params = {
					site_id: e
				}
				shopmemberAdd(params).then(res => {
					if (res.code == 0) {
						this.isguanzhu()
					}
				})
			},
			onchange(e) {
				this.clearRewardTimer()
				// this.target = 0
				this.timesecond = 0
				this.videoContexts[this.current].pause();
				// this.is_play = false
				this.current = e.detail.current;
				this.isguanzhu()
				if (this.current == this.list.length - 1) {
					this.page++
					this.getlist()
				}
				// this.hasLogged = false
				// this.duration = 0
				// if (parseInt(this.current) + 1 == this.list.length) {
				// 	if (this.list[this.current].next_id) {
				// 		this.getnextvideo()
				// 	}
				// }
			},
			changeSwiper() {
				this.timesecond = 0
				this.isTimerRunning = false
				this.playing = false
				this.$refs.progress[this.current].stop()
				this.videoContexts.forEach(item => {
					item.pause()
				})
				this.$nextTick(() => {
					this.playing = true
					this.videoContexts[this.current].play();
				})
				// this.is_play = true 
			},
			onTimeUpdate(e) {
				if (this.timesecond < this.second) {
					this.targets();
				}
			},
			loadedmetadata() {
				this.playing = true
				this.videoContexts[this.current].play();
			},
			playVideo() {
				// console.log(666);
				// this.videoContexts[this.current].play();
			},
			async getlist() {
				let params = {
					page: this.page
				}
				await videoList(params).then(res => {
					this.list = this.page > 1 ? this.list.concat(...res.data.list) : res.data.list
				})
			},
		}
	}
</script>

<style lang="scss">
	button{
		background: #ffffff00;
		margin: 0 ;
		padding: 0;
	}
	.orders {
		padding: 32rpx;

		>view:first-child {
			font-weight: 400;
			font-size: 28rpx;
			color: #222222;
		}

		scroll-view {
			height: 60vh;

			.orderitems {
				>view {
					width: calc(100% - 92rpx);

					>view:first-child {
						margin-bottom: 12rpx;

						>view:first-child {
							font-weight: 400;
							font-size: 28rpx;
							color: #999999;
						}

						>view:last-child {
							font-weight: 400;
							font-size: 22rpx;
							color: #25B85B;
							width: 82rpx;
							height: 32rpx;
							background: #E6FFEF;
							border-radius: 4rpx;
							text-align: center;
							line-height: 32rpx;
						}
					}

					>view:nth-child(2) {
						>view {
							width: calc(100% - 144rpx - 16rpx);
							justify-content: space-between;

							>view {
								height: 144rpx;
								font-weight: 400;
								font-size: 26rpx;
								color: #222222;
							}
						}
					}
				}
			}
		}
	}

	.muteds {
		position: absolute;
		right: 34rpx;
		top: 64rpx;
		z-index: 999;
	}

	.Circular {
		position: absolute;
		right: 16rpx;
		top: 192rpx;
		z-index: 999;
		width: 100rpx;
		height: 100rpx;

		.btns {
			position: absolute;
			left: 0;
			z-index: 999999;
			width: 96rpx;
			height: 20rpx;
			bottom: 0;
			background: red;
		}
	}

	.prices {
		>view:first-child {
			height: 64rpx;
			background: linear-gradient(90deg, rgba(255, 250, 240, 0) 0%, #FFFAF0 100%);
			// width: calc(100% - 140rpx);
			width: 254rpx;

			>view:first-child {
				>view{
					width: 96rpx;
					height: 28rpx;
					background: #FFEBEB;
					border-radius: 4rpx;
					text-align: center;
					line-height: 28rpx;
					font-weight: 600;
					font-size: 20rpx;
					color: #F51D1D;
					margin-right: 5rpx;
				}
			}

			>view:last-child {
				font-size: 32rpx;
				color: #F51D1D;
				font-weight: bold;

				text:first-child {
					font-size: 24rpx;
					color: #F51D1D;
					font-weight: bold;
				}

				view:last-child {
					font-weight: 400;
					font-size: 24rpx;
					color: #999999;
					text-decoration: line-through;
				}
			}
		}
	}

	.goods {
		position: absolute;
		width: 606rpx;
		height: 150rpx;
		background: #FFFFFF;
		border-radius: 24rpx;
		bottom: 32rpx;
		left: 20rpx;
		z-index: 999;
		box-sizing: border-box;
		padding: 20rpx;

		>view {
			width: calc(100% - 110rpx - 20rpx);
		}
	}

	.part {
		width: 100vw;
		height: 65vh;
		position: relative;

		>view:first-child {
			margin: 32rpx;
			color: #1f1f1f;
			font-size: 32rpx;
			font-weight: 700;
		}

		scroll-view {
			height: calc(100% - 64rpx - 36rpx - 79rpx);

			.pinglunitem {
				margin: 0 32rpx;
				margin-bottom: 50rpx;
				display: flex;
				justify-content: space-between;

				>image {
					width: 72rpx;
					height: 72rpx;
					border-radius: 50%;
				}

				>view {
					width: calc(100% - 72rpx - 16rpx);

					>view:first-child {
						color: #a1a1a1;
						font-size: 26rpx;
						font-weight: 400;
					}

					>view:nth-child(2) {
						color: #1f1f1f;
						font-size: 30rpx;
						font-weight: 400;
						margin-top: 8rpx;
					}

					>view:last-child {
						display: flex;
						align-items: center;
						justify-content: space-between;
						margin-top: 12rpx;

						>view:first-child {
							color: #a1a1a1;
							font-size: 24rpx;
							font-weight: 400;
						}

						>view:last-child {
							image {
								width: 24rpx;
								height: 24rpx;
								margin-right: 8rpx;
							}

							display: flex;
							align-items: center;
							color: #737373;
							font-size: 24rpx;
							font-weight: 400;
						}

						.on {
							color: #D3A77C !important;
						}
					}
				}
			}
		}

		>view:last-child {
			width: 100vw;
			box-sizing: border-box;
			padding: 18rpx 26rpx;
			background: #fff;
			position: absolute;
			bottom: 0;
			left: 0;

			textarea {
				width: 498rpx;
				background: #F5F5F5;
				border-radius: 36rpx;
				border: 1rpx solid #DDDDDD;
				box-sizing: border-box;
				padding: 19rpx 28rpx;
			}

			>view {
				width: calc(100% - 498rpx - 32rpx);
				text-align: center;
				height: 72rpx;
				background: linear-gradient(90deg, #FA7806 0%, #F55606 100%);
				border-radius: 36rpx;
				line-height: 72rpx;
				font-weight: 400;
				font-size: 28rpx;
				color: #FFFFFF;
			}

			// image {
			// 	width: 60rpx;
			// 	height: 60rpx;
			// 	right: 34rpx;
			// 	position: absolute;
			// 	top: 50%;
			// 	transform: translateY(-50%);
			// }
		}
	}

	swiper {
		width: 100vw;
		height: 100vh;

		.swiper-item {
			width: 100%;
			height: 100%;
			position: relative;

			.rights {
				position: absolute;
				right: 16rpx;
				z-index: 99;
				bottom: 32rpx;

				.a2 {
					margin-top: 28rpx;
					text-align: center;

					>image {
						width: 64rpx;
						height: 64rpx;
					}

					>view {
						color: #fff;
					}
				}

				>.a1 {
					position: relative;

					.i1 {
						width: 88rpx;
						height: 88rpx;
						border-radius: 44rpx;
					}

					>view {
						width: 80rpx;
						height: 32rpx;
						background: linear-gradient(90deg, #FA7806 0%, #F55606 100%);
						border-radius: 16rpx;
						text-align: center;
						line-height: 32rpx;
						font-weight: 600;
						font-size: 20rpx;
						color: #FFFFFF;
						position: absolute;
						bottom: 2rpx;
						left: 50%;
						transform: translateX(-50%);
					}
				}
			}

			video {
				width: 100%;
				height: 100%;
				position: absolute;
				z-index: 97;
				left: 0;
				top: 0;
				object-fit: contain;
			}
		}
	}
</style>