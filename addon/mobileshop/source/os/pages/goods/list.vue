<template>
	<view class="list">
		<view class="search-wrap">
			<view class="search-input-inner">
				<text class="search-input-icon iconfont iconsousuo" @click="searchGoods()"></text>
				<input class="search-input-text font-size-tag" maxlength="50" v-model="searchGoodsName" placeholder="请输入商品名称" />
			</view>
			<view class="search-btn color-base-bg" @click="linkSkip('add')"><text>+</text>添加商品</view>
		</view>
		<view class="tab-wrap">
			<block v-for="(item, index) in statusList" :key="index">
				<view class="tab-item" @click="tabChange(item.id)" :class="item.id == status ? 'active color-base-text color-base-bg-before' : ''">{{ item.name }}</view>
			</block>
		</view>
		<mescroll-uni class="list-wrap" @getData="getListData" top="210" refs="mescroll" :size="10">
			<block slot="list">
				<view class="item-inner" v-for="(item,index) in dataList" :key="index">
					<view class="item-wrap" @click="linkSkip('edit',item)">
						<image class="item-img" :src="item.goods_image == '' ? $util.img($util.getDefaultImage().default_headimg) : $util.img(item.goods_image)" mode=""></image>
						<view class="item-desc">
							<view class="item-name uni-line-hide">{{item.goods_name}}</view>
							<view class="item-num-wrap">
								<text>库存：{{item.goods_stock}}</text>
								<text>销量：{{item.sale_num}}</text>
							</view>
							<view class="item-operation">
								<text class="color-base-text item-price">￥{{item.price}}</text>
								<text @click.stop="item.is_off = 1" class="iconshenglve iconfont"></text>
							</view>
						</view>
					</view>
					<view class="operation" @click="item.is_off = 0" v-if="item.is_off">
						<view class="operation-item">
							<image :src="$util.img('/upload/uniapp/shop_uniapp/goods/goods_list_01.png')" mode=""></image>
							<text>编辑</text>
						</view>
						<view class="operation-item" v-if="item.verify_state == 1 && item.goods_state == 1" @click.stop="offGoods(item)">
							<image :src="$util.img('/upload/uniapp/shop_uniapp/goods/goods_list_02.png')" mode=""></image>
							<text>下架</text>
						</view>
						<view class="operation-item" v-if="item.verify_state == 1 && item.goods_state == 0" @click.stop="onGoods(item)">
							<image :src="$util.img('/upload/uniapp/shop_uniapp/goods/goods_list_06.png')" mode=""></image>
							<text>上架</text>
						</view>
						<view class="operation-item" @click.stop="copyGoods(item)">
							<image :src="$util.img('/upload/uniapp/shop_uniapp/goods/goods_list_03.png')" mode=""></image>
							<text>复制</text>
						</view>
						<view class="operation-item" @click.stop="deleteGoods(item)">
							<image :src="$util.img('/upload/uniapp/shop_uniapp/goods/goods_list_04.png')" mode=""></image>
							<text>删除</text>
						</view>
						<view class="operation-item" @click.stop="getVerifyStateRemark(item)" v-if="item.verify_state == 10">
							<image :src="$util.img('/upload/uniapp/shop_uniapp/goods/goods_list_07.png')" mode=""></image>
							<text>原因</text>
						</view>
						<view class="operation-item">
							<image :src="$util.img('/upload/uniapp/shop_uniapp/goods/goods_list_05.png')" mode=""></image>
							<text>出入库</text>
						</view>
					</view>
				</view>
				<ns-empty v-if="!dataList.length" :emptyBtn="{show:false}" :fixed="!1"></ns-empty>
			</block>
		</mescroll-uni>
		<loading-cover ref="loadingCover"></loading-cover>
	</view>
</template>

<script>
export default {
	data() {
		return {
			statusList: [
				{
					id: 0,
					name: '销售中',
					screenVal: 1
				},
				{
					id: 1,
					name: '仓库中',
					screenVal: 0
				},
				{
					id: 2,
					name: '待审核',
					screenVal: 0
				},
				{
					id: 3,
					name: '审核失败',
					screenVal: -2
				},
				{
					id: 4,
					name: '违规下架',
					screenVal: 10
				}
			],
			status: 0,
			dataList: [],
			searchGoodsName: ''
		};
	},
	methods: {		
		tabChange(e){
			this.status = e;
			this.mescroll.resetUpScroll();
		},
		getListData(mescroll) {
			var data = {
				page_size: mescroll.size,
				page: mescroll.num,
				search_text: this.searchGoodsName
			}
			
			if(this.status > 1){
				data.verify_state = this.statusList[this.status].screenVal;
			}else{
				data.goods_state = this.statusList[this.status].screenVal;
			}
			
			this.mescroll = mescroll;
			this.$api.sendRequest({
				url: '/shopapi/goods/lists',
				data: data,
				success: res => {
					let newArr = [];
					let msg = res.message;
					if (res.code == 0 && res.data) {
						newArr = res.data.list;
					} else {
						this.$util.showToast({ title: msg });
					}
					mescroll.endSuccess(newArr.length);
					//设置列表数据
					if (mescroll.num == 1) this.dataList = []; //如果是第一页需手动制空列表
					newArr.forEach(v => {
						v.is_off = 0;
						if (v.end_time > res.timestamp) {
							v.timeMachine = this.$util.countDown(v.end_time - res.timestamp);
						} else {
							v.timeMachine = null;
						}
					});
					this.dataList = this.dataList.concat(newArr); //追加新数据
					if (this.$refs.loadingCover) this.$refs.loadingCover.hide();
				},
				fail() {
					mescroll.endErr();
					if (this.$refs.loadingCover) this.$refs.loadingCover.hide();
				}
			});
		},
		deleteGoods(item){
			uni.showModal({
			    title: '删除',
			    content: '删除后进入回收站，确定删除吗?',
			    success: res => {
			        if (res.confirm) {
						this.$api.sendRequest({
							url: '/shopapi/goods/deleteGoods',
							data: {
								goods_ids: item.goods_id
							},
							success: res => {
								if(res.code == 0){
									this.mescroll.resetUpScroll();
									this.$util.showToast({
										title: '操作成功'
									});
								}else{
									this.$util.showToast({
										title: res.message
									});
								}
							}
						});
			        }
					item.is_off = 0;
			    }
			});
		},
		offGoods(item){
			this.$api.sendRequest({
				url: '/shopapi/goods/offGoods',
				data: {
					goods_state: 0,
					goods_ids: item.goods_id
				},
				success: res => {
					if(res.code == 0){
						this.mescroll.resetUpScroll();
						this.$util.showToast({
							title: '操作成功'
						});
					}else{
						this.$util.showToast({
							title: res.message
						});
					}
				}
			});
			
			item.is_off = 0;
		},
		onGoods(item){
			this.$api.sendRequest({
				url: '/shopapi/goods/onGoods',
				data: {
					goods_state: 1,
					goods_ids: item.goods_id
				},
				success: res => {
					if(res.code == 0){
						this.mescroll.resetUpScroll();
						this.$util.showToast({
							title: '操作成功'
						});
					}else{
						this.$util.showToast({
							title: res.message
						});
					}
				}
			});
			
			item.is_off = 0;
		},
		copyGoods(item){
			uni.showModal({
			    title: '复制',
			    content: '复制商品会存放在仓库中,确定复制吗',
			    success: res => {
			        if (res.confirm) {
						this.$api.sendRequest({
							url: '/shopapi/goods/copyGoods',
							data: {
								goods_id: item.goods_id
							},
							success: res => {
								if(res.code == 0){
									this.mescroll.resetUpScroll();
									this.$util.showToast({
										title: '操作成功'
									});
								}else{
									this.$util.showToast({
										title: res.message
									});
								}
							}
						});
					}
					item.is_off = 0;
				},
			});
		},
		getVerifyStateRemark(item){
			this.$api.sendRequest({
				url: '/shopapi/goods/getVerifyStateRemark',
				data: {
					goods_id: item.goods_id
				},
				success: res => {
					if(res.code != 0 && !res.data) return false;
					var data = res.data.verify_state_remark ? res.data.verify_state_remark : "暂无违规信息";
					uni.showModal({
					    title: '违规原因',
					    content: data,
						showCancel:false,
					    success: res => {
							item.is_off = 0;
						},
					});
					return false;
				}
			});
		},
		searchGoods(){
			this.mescroll.resetUpScroll();
		},
		linkSkip(type,item){
			if(type == 'add'){
				this.$util.redirectTo("/pages/goods/add");
			}else if(type == 'edit'){
				this.$util.redirectTo("/pages/goods/edit",{'sku_id':item.sku_id});
			}
			
		}
	}
};
</script>

<style lang="scss">
	.search-wrap{
		display: flex;
		justify-content: space-between;
		padding: 30rpx 30rpx 0;
		background-color: #fff;
		.search-input-inner{
			display: flex;
			align-items: center;
			width: 460rpx;
			height: 70rpx;
			padding: 0 30rpx;
			color: $color-tip;
			background-color: $color-bg;
			border-radius: 100rpx;
			box-sizing: border-box;
			.search-input-icon{
				margin-right: 10rpx;
			}
		}
		.search-btn{
			display: flex;
			justify-content: center;
			align-items: center;
			width: 200rpx;
			height: 70rpx;
			color: #fff;
			border-radius: 100rpx;
			text{
				margin-right: 10rpx;
			}
		}
	}
	.tab-wrap{
		position: relative;
		display: flex;
		justify-content: space-between;
		padding: 24rpx 30rpx 0;
		height: 66rpx;
		background-color: #fff;
		.tab-item{
			display: flex;
			align-items: flex-start;
		}
		.active{
			position: relative;
			&::after{
				content: "";
				position: absolute;
				bottom: 0;
				height: 4rpx;
				width: 100%;
			}
		}
	}
	.item-inner{
		position: relative;
		margin: 0 30rpx 20rpx;
		background-color: #fff;
		border-radius: $border-radius;
		.item-wrap{
			display: flex;
			padding: 30rpx;
			.item-img{
				margin-right: 20rpx;
				width: 160rpx;
				height: 160rpx;
				border-radius: $border-radius;
			}
			.item-desc{
				flex: 1;
				.item-name{
					line-height: 1.6;
					color: $color-title;
				}
				.item-num-wrap{
					margin-top: 3px;
					font-size: $font-size-tag;
					color: #909399;
					text:first-of-type{
						margin-right: 30rpx;
					}
				}
				.item-operation{
					display: flex;
					align-items: center;
					justify-content: space-between;
					line-height: 1;
					.item-price{
						font-size: 24rpx;
					}
					.iconshenglve{
						font-size: 24px;
						color: $color-disabled;
					}
				}
			}
		}
		.operation{
			overflow: hidden;
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: rgba(0,0,0,.6);
			display: flex;
			justify-content: space-around;
			align-items: center;
			border-radius: 10rpx;
			.operation-item{
				display: flex;
				flex-direction: column;
				align-items: center;
				image{
					width: 64rpx;
					height: 64rpx;
				}
				text{
					margin-top: 10rpx;
					line-height: 1;
					color: #fff;
				}
			}
		}
	}
</style>
