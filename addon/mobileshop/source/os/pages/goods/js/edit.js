export default {
	data() {
		return {
			// 商品类型:1 实物商品 ，2 虚拟商品
			goodsClass: 1,
			
			// 商品图片高度
			goodsImgHeight: 100,

			goodsData: {
				goods_name: '',
				introduction: '',
				goods_image: [
					'http://static-11ea0c21-6b8f-47f7-b77f-cb0c7ea3f355.bspapp.com/shmily-drag-image/static/5.jpg',
					'http://static-11ea0c21-6b8f-47f7-b77f-cb0c7ea3f355.bspapp.com/shmily-drag-image/static/2.jpg'
					// 'http://static-11ea0c21-6b8f-47f7-b77f-cb0c7ea3f355.bspapp.com/shmily-drag-image/static/3.jpg'
				],

				categoryId: 0,
				spec_type: 0,
				price: 0,
				market_price: 0,
				cost_price: 0,
				weight: 0,
				volume: 0,
				sku_no: '',
				goods_stock: 0,
				goods_stock_alarm: 0,
				is_free_shipping: 1,
				shipping_template: '',
				max_buy: 0,
				min_buy: 0,
				unit: '',
				sort: 0,
				goods_state: 1,
			}
		};
	},
	onShow() {
		this.refreshGoodsImgHeight();
	},
	methods: {
		// 选择商品分类
		openGoodsCategory() {
			this.$util.redirectTo('/pages/goods/edit_item/goods_category');
		},
		// 完善注释
		openGoodsSpec() {
			this.$util.redirectTo('/pages/goods/edit_item/spec');
		},
		openGoodsSpecEdit() {
			this.$util.redirectTo('/pages/goods/edit_item/spec_edit');
		},
		openGoodsState() {
			this.$util.redirectTo('/pages/goods/edit_item/goods_state');
		},
		openExpressFreight() {
			this.$util.redirectTo('/pages/goods/edit_item/express_freight');
		},
		openAttr() {
			this.$util.redirectTo('/pages/goods/edit_item/goods_attr');
		},
		refreshGoodsImgHeight() {
			// 模拟
			setTimeout(() => {
				const query = uni.createSelectorQuery().in(this);
				query.select('.img-list').boundingClientRect(data => {
					this.goodsImgHeight = data.height + 20;
				});
				query.exec();
			}, 100 * 2);
		},
		// 验证
		verify() {

		}
	}
};
