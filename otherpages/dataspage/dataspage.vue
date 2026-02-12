<template>
	<view>
		<view class="topbg">
			<image src="/static/topbg.png" mode=""></image>
			<view class="" v-if="shopinfo">{{shopinfo.citytitle}}</view>
			<view class="">{{ str2 + " " + str1 }}</view>
			<view class="">
				<image :src="img(shopinfo.site_log)" class="w-102 bor-4" v-if="shopinfo&&shopinfo.site_log" mode=""></image>
				{{shopinfo.site_name}}
			</view>
			<view class="">
				<image :src="shopinfo.headimg" v-if="shopinfo&&shopinfo.headimg" mode=""></image>
				{{shopinfo.username}}
			</view>
		</view>
		<image src="/static/bg11.png" class="bgs" mode="widthFix"></image>
		<view class="moneys">
			<view :class="{'on':check==0}" @click="check=0">今日成交总额</view>
			<view class="" style="width: 40rpx;"></view>
			<view :class="{'on':check==1}"  @click="check=1">累计成交总额</view>
		</view>
		<view class="part">
			<image src="/static/主标题bg.png" mode=""></image>
			<view class="">
				<text>￥</text>
				<uv-count-to :endVal="money	" separator="," color='#fff' fontSize='40' :bold='true'></uv-count-to>
			</view>
		</view>
		<view class="part1">
			<view class="">
				<image src="/static/user.png" mode=""></image>
				<view class="">用户数</view>
				<view class="">
					<uv-count-to :endVal="member_count" separator="," color='#fff' fontSize='16'
						:bold='true'></uv-count-to>
				</view>
			</view>
			<view class="">
				<image src="/static/user.png" mode=""></image>
				<view class="">订单数</view>
				<view class="">
					<uv-count-to :endVal="order_count" separator="," color='#fff' fontSize='16'
						:bold='true'></uv-count-to>
				</view>
			</view>
		</view>
		<view class="part3">
			<view class="">
				<image src="/static/组 38.png" mode=""></image>
				<view class="">
					<image src="/static/组 15.png" mode=""></image>
					产品销售排行
				</view>
				<view class="">
					<view :class="{on:check1==0}" @click="check1=0">今日销售数</view>
					<view :class="{on:check1==1}" @click="check1=1">累计销售数</view>
				</view>
			</view>
			<view class="">
				<view class="">
					<text>排行</text>
					<text>产品名称</text>
				</view>
				<view class="">
					<text>价格</text>
					<text>数额</text>
				</view>
			</view>
			<view v-for="(item,index) in list" class="items" :key="index">
				<image :src="getImagePath(index)" mode=""></image>
				<view class="">
					<image :src="$util.img(item.goods_image)" mode=""></image>
					<view class="text-more-2">{{item.goods_name}}</view>
				</view>
				<view class="">{{item.goods_price}}</view>
				<view class="">{{item.goods_money_total}}</view>
			</view>
		</view>
		<view class="" style="height: 60rpx;"></view>
	</view>
</template>

<script>
	import {miniindex} from "@/common/js/api.js"
	export default {
		data() {
			return {
				check: 0,
				check1: 0,
				goodslists:[],
				order_count:0,
				member_count:0,
				shopinfo:{},
				totalmoney:{},
				a:null,
				str2:'',
				str1:'',
				site_id:''
			}
		},
		computed:{
			list(){
				if(this.check1==0){
					return this.goodslists.today
				}else{
					return this.goodslists.all
				}
			},
			money(){
				if(this.check==0){
					return this.totalmoney.today
				}else{
					return this.totalmoney.all
				}
			}
		},
		onLoad(res) {
			this.site_id=res.site_id
			this.getinfo()
			this.a=setInterval(()=>{
				this.getTime()
			},1000)
		},
		methods: {
			getTime() {
			      // 获取当前时间
			      let date = new Date();
			      let year = date.getFullYear();
			      let month = date.getMonth() + 1;
			      let day = date.getDate();
			      let hours = date.getHours();
			      let minutes = date.getMinutes();
			      let seconds = date.getSeconds();
			      const dayNumber = date.getDay();
			      const days = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"];
			      this.str1 = `${hours.toString().padStart(2, 0)}:${minutes.toString().padStart(2, 0)}:${seconds.toString().padStart(2, 0)}`
			      this.str2 = `${year}-${month.toString().padStart(2, 0)}-${day.toString().padStart(2, 0)}`
			      this.str3 = `${days[dayNumber]}`
			    },
			img(img_path, params) {
				var path = "";
				if (img_path && img_path != undefined && img_path != "") {
					if (params && img_path != this.getDefaultImage().default_goods_img) {
						// 过滤默认图
						let arr = img_path.split(".");
						let suffix = arr[arr.length - 1];
						arr.pop();
						arr[arr.length - 1] = arr[arr.length - 1] + "_" + params.size.toUpperCase();
						arr.push(suffix);
						img_path = arr.join(".");
					}
					if (img_path.indexOf("http://") == -1 && img_path.indexOf("https://") == -1) {
						path = this.bascurl + "/" + img_path;
					} else {
						path = img_path;
					}
					// 处理商品助手的图片路径
					path = path.replace("addons/NsGoodsAssist/", "").replace("shop/goods/", "");
				} else {
					return '/static/none.png'
				}
				return path;
			},
			getImagePath(e) {
				return `/static/${e+1}.png`
			},
			getinfo(){
				console.log(this.site_id);
				let params={
					site_id:this.site_id
				}
				console.log(params);
				miniindex(params).then(res=>{
					let datas=res.data
					this.goodslists=datas.goodslists
					this.order_count=datas.order_count
					this.shopinfo=datas.shopinfo
					this.totalmoney=datas.totalmoney
					this.member_count=datas.member_count
				})
				// uni.request({
				// 	url:this.bascurl+'shopapplet/screen/miniindex?site_id='+this.site_id,
				// 	success:res=>{
				// 		console.log(res);
				// 		let datas=res.data
				// 		this.goodslists=datas.goodslists
				// 		this.order_count=datas.order_count
				// 		this.shopinfo=datas.shopinfo
				// 		this.totalmoney=datas.totalmoney
				// 		this.member_count=datas.member_count
				// 	}
				// })
			},
		}
	}
</script>

<style lang="scss">
	.text-more-2 {
	  width: 100%;
	  overflow: hidden;
	  text-overflow: ellipsis;
	  display: -webkit-box;
	  -webkit-line-clamp: 2;
	  -webkit-box-orient: vertical;
	  word-break: break-all;
	}
	.items {
		height: 84rpx;
		background: linear-gradient(90deg, rgba(18, 81, 185, 0.6) 0%, rgba(30, 100, 200, 0.8) 50%, rgba(0, 0, 0, 0) 100%);
		border-radius: 12rpx 0px 0px 12rpx;
		margin-bottom: 16rpx;
		margin-left: 40rpx;
		box-sizing: border-box;
		padding-right: 20rpx;
		position: relative;
		display: flex;
		justify-content: space-between;
		align-items: center;
		>view:nth-child(2){
			margin-left: 40rpx;
			display: flex;
			align-items: center;
			>view{
				width: 293rpx;
				font-weight: 400;
				font-size: 24rpx;
				color: #FFFFFF
			}
			image{
				width: 60rpx ;
				height: 60rpx;
				border-radius: 16rpx;
				margin-right: 8rpx;
			}
		}
		>view{
			font-weight: bold;
			font-size: 24rpx;
			color: #FFFFFF;
		}
		>image{
			width: 42rpx;
			height: 48rpx;
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
			left: -20rpx;
		}
	}

	view,
	text {
		z-index: 8;
	}

	.part3 {
		margin: 0 38rpx;
		margin-top: 32rpx;

		>view:nth-child(2) {
			padding: 20rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;

			>view:first-child {
				text:first-child {
					margin-right: 20rpx;
				}
			}

			>view:last-child {
				text:first-child {
					margin-right: 77rpx;
				}
			}

			text {
				font-weight: 400;
				font-size: 20rpx;
				color: #00D2E5;
			}
		}

		>view:first-child {
			height: 60rpx;
			position: relative;
			padding: 0 18rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;

			>image {
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
			}

			>view:nth-child(2) {
				height: 60rpx;
				font-weight: 500;
				font-size: 28rpx;
				color: #FFFFFE;
				display: flex;
				align-items: center;
				position: relative;
				z-index: 5;

				image {
					width: 35rpx;
					height: 30rpx;
					margin-right: 8rpx;
				}
			}

			>view:last-child {
				height: 60rpx;
				display: flex;
				align-items: center;
				position: relative;
				z-index: 5;

				>view {
					font-weight: 400;
					font-size: 20rpx;
					color: rgba(255, 255, 254, 0.8);
					margin-left: 20rpx;
				}

				.on {
					color: #fff;
					position: relative;
				}

				.on::after {
					content: '';
					width: 48rpx;
					height: 4rpx;
					background: #26EDFF;
					position: absolute;
					left: 50%;
					bottom: -10rpx;
					transform: translateX(-50%);
				}
			}

		}
	}

	.part1 {
		margin: 0 38rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;

		>view {
			width: calc((100% - 6rpx)/2);
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;
			height: 96rpx;
			position: relative;

			>image {
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
				height: 100%;
			}

			view {
				font-weight: 400;
				font-size: 20rpx;
				color: #FFFFFF;
				position: relative;
				z-index: 5;
			}
		}
	}

	.part {
		margin: 0 16rpx;
		position: relative;
		height: 245rpx;
		padding-top: 7%;
		box-sizing: border-box;

		>image {
			width: 100%;
			height: 100%;
			position: absolute;
			left: 0;
			top: 0;
			z-index: 1;
		}

		>view {
			display: flex;
			align-items: baseline;
			justify-content: center;
			position: relative;
			z-index: 9;

			text {
				font-weight: bold;
				font-size: 56rpx;
				color: #FEFFFF;
			}
		}
	}

	page {
		background: #000A25;
	}

	.moneys {
		display: flex;
		align-items: center;
		justify-content: center;
		margin-top: 30rpx;

		>view {
			font-weight: 400;
			font-size: 24rpx;
			color: #FFFFFF;
		}

		.on {
			font-size: 32rpx;
			font-weight: bold;
		}
	}

	.topbg {
		width: 100vw;
		height: 160rpx;
		position: relative;

		>image {
			width: 100%;
			height: 100%;
		}

		>view:nth-child(2) {
			position: absolute;
			left: 30rpx;
			top: 24rpx;
			font-weight: 600;
			font-size: 18rpx;
			color: #FFFFFF;
		}

		>view:nth-child(3) {
			position: absolute;
			right: 30rpx;
			top: 24rpx;
			font-weight: 600;
			font-size: 18rpx;
			color: #FFFFFF;
		}

		>view:nth-child(4) {
			position: absolute;
			left: 30rpx;
			bottom: 0;
			font-weight: 400;
			font-size: 22rpx;
			color: #FFFFFF;
			display: flex;
			align-items: center;

			image {
				width: 48rpx;
				height: 48rpx;
				border-radius: 50%;
				margin-right: 13rpx;

			}
		}

		>view:nth-child(5) {
			position: absolute;
			right: 30rpx;
			bottom: 0;
			font-weight: 400;
			font-size: 22rpx;
			color: #FFFFFF;
			display: flex;
			align-items: center;

			image {
				width: 48rpx;
				height: 48rpx;
				border-radius: 50%;
				margin-right: 13rpx;

			}
		}
	}

	.bgs {
		width: 100vw;
		position: fixed;
		top: 0;
		left: 0;
		z-index: 0;
	}
</style>