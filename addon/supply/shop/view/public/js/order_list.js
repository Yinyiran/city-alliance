/**
 * 按订单类型（实际配送链路）输出交易状态文案
 * 与后端 OrderCommon::orderTypeStatusName() 保持一致，用于兜底历史数据的 order_status_name 快照
 *
 * 快递邮寄(order_type=1)：待支付→待发货→已发货→已收货→已完成
 * 到店核销(order_type=2)：待支付→待核销→已核销→已完成
 * 外卖自配送(order_type=3)：待支付→待接单→备餐中→配送中→已送达→已完成
 * @param order_type
 * @param order_status
 * @returns {string}
 */
function orderTradeStatusName(order_type, order_status) {
    order_type = parseInt(order_type, 10);
    order_status = parseInt(order_status, 10);
    var map;
    if (order_type == 3) {
        map = {0:'待支付',1:'待接单',2:'备餐中',3:'配送中',4:'已送达',10:'已完成','-1':'已关闭'};
        return map[order_status] || '已送达';
    }
    if (order_type == 2) {
        map = {0:'待支付',1:'待核销',2:'待核销',3:'待核销',4:'已核销',10:'已完成','-1':'已关闭'};
        return map[order_status] || '已核销';
    }
    map = {0:'待支付',1:'待发货',2:'待发货',3:'已发货',4:'已收货',10:'已完成','-1':'已关闭'};
    return map[order_status] || '已收货';
}
/**
 * 渲染订单列表
 */
Order = function () {
};

/**
 * 设置数据集
 */
Order.prototype.setData = function (data) {
	Order.prototype.data = data;
};

/**
 * 列名数据
 */
Order.prototype.cols = [
	{
		title: '<span>商品信息</span>',
		width: "37%",
		className: "product-info",
		template: function (orderitem, order) {
            
            var h = '<div class="ns-table-title">';
                h +=    '<div class="ns-title-pic">';
                h +=    	'<img layer-src src="' + ns.img(orderitem.sku_image) + '" >';
                h +=    '</div>';
                h +=    '<div class="ns-title-content">';
				h += 		'<a href="'+ ns.url("supply://shop/goods/detail", {sku_id: orderitem.sku_id}) +'" target="_blank" title="'+ orderitem.sku_name +'" class="ns-multi-line-hiding ns-text-color">' + orderitem.sku_name + '</a>';
				h += 		'<p class="goods-class-name">' + orderitem.goods_class_name + '</p>';
                h +=    '</div>';
				h += '</div>';
				
				if (orderitem.refund_status_name != '') {
					h += '<br/><a href="'+ ns.url("supply://shop/orderrefund/detail", {order_goods_id: orderitem.order_goods_id}) +'" target="_blank" class="ns-text-color">' + orderitem.refund_status_name + '</a>';
				}
			
			return h;
		}
	},
	{
		title: "单价",
		width: "12%",
		align: "right",
		className: "order-price",
		template: function (orderitem, order) {
			var h = '<span>￥' + orderitem.price + '</span>';
			return h;
		}
	},
	{
		title: "数量",
		width: "12%",
		align: "right",
		className: "order-price",
		template: function (orderitem, order) {
			var h = '<span>' + orderitem.num + '件</span>';
			return h;
		}
	},
	{
		title: "实付款",
		width: "15%",
		align: "right",
		className: "order-money",
		merge: true,
		template: function (orderitem, order) {
			var h = '<span class="ns-line-hiding" title="￥' + order.order_money + '">￥' + order.order_money + '</span>';
			return h;
		}
	},
	{
		title: "订单状态",
		width: "12%",
		align: "center",
		className: "transaction-status",
		merge: true,
		template: function (orderitem, order) {
			var h = '<div class="ns-text-color">' + (order.trade_status_name || orderTradeStatusName(order.order_type, order.order_status)) + '</div>';
				h += '<a href="'+ ns.url("supply://shop/order/detail", {order_id: orderitem.order_id}) +'">查看详情</a>';
			return h;
		}
	},
	{
		title: "操作",
		width: "12%",
		align: "left",
		className: "operation",
		merge: true,
		template: function (orderitem, order) {
			var url = "shop/order/detail";
			var h = '';
			h += '<div class="ns-table-btn" style="display: block;">';
			for (var k = 0; k < order.action.length; k++) {
				h += '<p><a class="layui-btn" onclick="orderAction(\''+ order.action[k].action +'\', '+ order.order_id +')">' + order.action[k].title + '</a></p>';
			}

			if (order.action.length == 0 && order.is_evaluate == 1) {
				if (order.evaluate_status == 0) {
					h += '<p><a class="layui-btn" onclick="evaluateFirst('+ order.order_id +')">评价</a></p>';
				} else {
					h += '<p><a class="layui-btn" onclick="evaluateAgain('+ order.order_id +')">追评</a></p>';
				}
			}
			h += '</div>';
			return h;
			
		}
	}
];

/**
 * 渲染表头
 */
Order.prototype.header = function (hasThead) {
	var colgroup = '<colgroup>';
	var thead = '';
	if (hasThead) thead = '<thead><tr>';
	
	for (var i = 0; i < this.cols.length; i++) {
		var align = this.cols[i].align ? "text-align:" + this.cols[i].align : "";
		
		colgroup += '<col width="' + this.cols[i].width + '">';
		if (hasThead) {
			thead += '<th style="' + align + '" class="' + (this.cols[i].className || "") + '">';
			thead += '<div class="layui-table-cell">' + this.cols[i].title + '</div>';
			thead += '</th>';
		}
	}
	colgroup += '</colgroup>';
	if (hasThead) thead += '</tr></thead>';
	return colgroup + thead;
};

/**
 * 渲染内容
 */
Order.prototype.tbody = function () {
	
	var tbody = '<tbody>';
	for (var i = 0; i < this.data.list.length; i++) {
		
		var order = this.data.list[i];
		var orderitemList = order.order_goods;
		var pay_type_name = order.pay_type_name != '' ? order.pay_type_name : "";
		
		if (i > 0) {
			//分割行
			tbody += '<tr class="separation-row">';
			tbody += '<td colspan="' + this.cols.length + '"></td>';
			tbody += '</tr>';
		}
		
		//订单项头部
		tbody += '<tr class="header-row">';
		tbody += 	'<td colspan="6">';
		tbody += 		'<span class="order-item-header" style="margin-right:20px;">' + ns.time_to_date(order.create_time) + '</span>';
		tbody +=		'<span class="order-item-header" style="margin-right:20px;">订单号：' + order.order_no + '</span>';
		tbody += 		'<span class="order-item-header order-site" style="margin-right:20px;"><a href="">'+ order.buyer_shop_name +'</a></span>';
		tbody += 		'<span class="order-item-header" style="margin-right:20px;">' + order.order_type_name + '</span>';
		
		if (pay_type_name) 
		tbody += 		'<span class="order-item-header">支付方式：' + pay_type_name + '</span>';

		tbody += 	'</td>';
		tbody += '</tr>';
		
		var orderitemHtml = '';
		for (var j = 0; j < orderitemList.length; j++) {
			var orderitem = orderitemList[j];
			orderitemHtml += '<tr class="content-row">';
			for (var k = 0; k < this.cols.length; k++) {
				
				if (j == 0 && this.cols[k].merge && this.cols[k].template) {
					
					orderitemHtml += '<td class="' + (this.cols[k].className || "") + '" align="' + (this.cols[k].align || "") + '" style="' + (this.cols[k].style || "") + '" rowspan="' + orderitemList.length + '">';
					orderitemHtml += this.cols[k].template(orderitem, order);
					orderitemHtml += '</td>';
					
				} else if (this.cols[k].template && !this.cols[k].merge) {
					
					orderitemHtml += '<td class="' + (this.cols[k].className || "") + '" align="' + (this.cols[k].align || "") + '" style="' + (this.cols[k].style || "") + '">';
					orderitemHtml += this.cols[k].template(orderitem, order);
					orderitemHtml += '</td>';
					
				}
			}
			orderitemHtml += '</tr>';
		}
		
		tbody += orderitemHtml;
	}
	
	tbody += '</tbody>';
	return tbody;
};

/**
 * 渲染表格
 */
Order.prototype.fetch = function () {
	if (this.data.list.length > 0) {
		return '<table class="layui-table layui-form">' + this.header(true) + '</table><table class="layui-table order-list-table layui-form">' + this.header(false) + this.tbody() + '</table>';
	} else {
		return '<table class="layui-table order-list-table layui-form">' + this.header(true) + '</table>' + '<div class="order-no-data-block"><ul><li><i class="layui-icon layui-icon-tabs"></i> </li><li>暂无订单</li></ul></div>';
	}
};

function orderAction(orderAction, order_id) {
	switch (orderAction) {
		case 'orderPay': // 支付
			orderPay(order_id);
			break;
		case 'orderClose': //关闭
			orderClose(order_id);
			break;
		case 'memberTakeDelivery': //收货
			orderDelivery(order_id);
			break;
		case 'trace': //查看物流
			location.href = ns.url("supply://shop/order/package", {"order_id": order_id});
			break;
		// case 'memberOrderEvaluation': //评价
		// 	location.href = ns.url("supply://shop/goodsevaluate/evaluate", {"order_id": order_id})	
		// 	break;
	}
}

// 订单支付
function orderPay(order_id) {
	$.ajax({
		url: ns.url("supply://shop/order/pay"),
		data: {"order_ids": order_id},
		dataType: 'JSON',
		type: 'POST',
		success: function (res) {
			if (res.code == 0) {
				location.href = ns.url("supply://shop/pay/pay", {"out_trade_no": res.data});
			}
		}
	})
}

// 订单关闭
function orderClose(order_id) {
	$.ajax({
		url: ns.url("supply://shop/order/close"),
		data: {"order_id": order_id},
		dataType: 'JSON',
		type: 'POST',
		success: function (res) {
			if (res.code == 0) {
				location.reload();
			}
		}
	})
}

// 收货
function orderDelivery(order_id) {
	$.ajax({
		url: ns.url("supply://shop/order/takeDelivery"),
		data: {"order_id": order_id},
		dataType: 'JSON',
		type: 'POST',
		success: function (res) {
			if (res.code == 0) {
				location.reload();
			}
		}
	})
}

$(".layui-nav-child dd").click(function() {
	if (!$(this).hasClass("layui-this")) {
		$(this).addClass("layui-this");
		$(this).siblings().removeClass("layui-this");
	}
});

function evaluateFirst(order_id) {
	location.href = ns.url("supply://shop/goodsevaluate/evaluate", {"order_id": order_id})	
}

function evaluateAgain(order_id){
	location.href = ns.url("supply://shop/goodsevaluate/again", {"order_id": order_id})	
}

//一键复制商品
var id_copy = false;
function copyGoods(order_goods_id){
	if(id_copy) return;

	id_copy = false;
	$.ajax({
		url: ns.url("supply://shop/order/moveGoods"),
		data: {"order_goods_id": order_goods_id},
		dataType: 'JSON',
		type: 'POST',
		success: function (res) {
			layer.msg(res.message);
			if (res.code == 0) {
				location.reload();
			}else{
				id_copy = false;
			}
		}
	})
}