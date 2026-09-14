import { server } from "./http";
//获取openid
export const authcodetoopenid=params=>server('/weapp/api/weapp/authcodetoopenid',params)
//小程序登录
export const loginAuth=params=>server('/api/login/auth',params)
//手机号授权登录
export const mobileauth=params=>server('/api/tripartite/mobileauth',params)
//个人信息
export const userinfo=params=>server('/api/member/info',params)
//轮播列表
export const bannerList=params=>server('/api/api/bannerList',params)
//经纬度转省市区
export const tranAddressInfo=params=>server('/api/memberaddress/tranAddressInfo',params)
//入驻商家 使用人数
export const websiteInfo=params=>server('/api/api/websiteInfo',params)
//发送验证码
export const mobileCode=params=>server('/api/member/mobileCode',params)
//修改手机号
export const mobileEdit=params=>server('/api/member/mobileEdit',params)
//修改头像
export const modifyheadimg=params=>server('/api/member/modifyheadimg',params)
//修改昵称
export const modifynickname=params=>server('/api/member/modifynickname',params)
//收货地址列表
export const memberaddresspage=params=>server('/api/memberaddress/page',params)
//新增收货地址
export const memberaddressAdd=params=>server('/api/memberaddress/add',params)
//地址详情
export const memberaddressInfo=params=>server('/api/memberaddress/info',params)
//删除地址
export const memberaddressDelete=params=>server('/api/memberaddress/delete',params)
//设置默认地址
export const setdefault=params=>server('/api/memberaddress/setdefault',params)
//地址列表
export const arealists=params=>server('/api/address/lists',params)
//修改地址
export const memberaddressEdt=params=>server('/api/memberaddress/edit',params)
//首页导航栏
export const navList=params=>server('/api/api/navList',params)
//定位城市
export const selectCity=params=>server('/api/member/selectCity',params)
//商家入驻
export const shopJoin=params=>server('/api/member/shopJoin',params)
//行业列表
export const categoryList=params=>server('/api/shopcategory/categoryList',params)
//分类下广告图
export const goodscategory=params=>server('/api/goodscategory/info',params)
//商品列表
export const goodslist=params=>server('/api/goodssku/goodsList',params)
//商品详情
export const Gdetail=params=>server('/api/goodssku/detail',params)
//加入购物车
export const cartAdd=params=>server('/api/cart/add',params)
//购物车列表
export const cartList=params=>server('/api/cart/goodslists',params)
//编辑购物车
export const cartEdit=params=>server('/api/cart/edit',params)
//设为默认账户
export const setDefaultdefa=params=>server('/api/memberbankaccount/setDefault',params)
//提现账号列席
export const memberbankaccountPage=params=>server('/api/memberbankaccount/page',params)
//添加提现账号
export const memberbankaccountadd=params=>server('/api/memberbankaccount/add',params)
//申请提现
export const memberwithdrawapply=params=>server('/api/memberwithdraw/apply',params)
//提现设置
// 返回 data.value 需包含以下字段（用于提现页面展示提现规则）：
// max: 单日最高可提现额度(元)  min: 最低提现金额(元)  day_num: 每日提现次数
// start_time/end_time: 每日提现时间(如 09:00/21:00)  arrive_time: 到账时间说明
// fee: 提现手续费(百分比,0或空为免手续费)  introduction: 提现规则富文本说明
export const memberwithdrawconfig=params=>server('/api/memberwithdraw/config',params)
//提现账号详情
export const memberbankaccountInfo=params=>server('/api/memberbankaccount/info',params)
//编辑提现账号
export const memberbankaccountEdit=params=>server('/api/memberbankaccount/edit',params)
//删除提现账号
export const memberbankaccountDels=params=>server('/api/memberbankaccount/delete',params)
//福利区筛选
export const benefitOption=params=>server('/api/api/benefitOption',params)
//福利区订单创建
export const pointcreate=params=>server('/api/ordercreate/pointcreate',params)
//订单计算信息
export const ordercalculate=params=>server('/api/ordercreate/calculate',params)
//接口1
export const goodsskuinfo=params=>server('/api/goodssku/info',params)
//门店列表
export const storeList=params=>server('/api/store/storeList',params)
//修改商品点击量
export const modifyclicks=params=>server('/api/goods/modifyclicks',params)
//视频列表
export const videoList=params=>server('/api/video/lists',params)
//关注店铺
export const shopmemberAdd=params=>server('/api/shopmember/add',params)
//当前店铺是否关注
export const issubscribe=params=>server('/api/shopmember/issubscribe',params)
//评论列表
export const commentLists=params=>server('/api/video/commentLists',params)
//添加评论
export const commentCreate=params=>server('/api/video/commentCreate',params)
//评论点赞
export const commcollect=params=>server('/api/video/collect',params)
//短视频订单列表
export const vorderList=params=>server('/api/video/orderList',params)
//抖音
export const getConfig=params=>server('/api/api/getConfig',params)
//领取积分
export const pointAdd=params=>server('/api/video/pointAdd',params)
//视频详情
export const vDetail=params=>server('/api/video/detail',params)
//待支付订单 数据初始化
export const payment=params=>server('/api/ordercreate/payment',params)
//关注商家列表
export const membershoppages=params=>server('/api/shopmember/membershoppages',params)
//取消店铺关注
export const shopmemberDel=params=>server('/api/shopmember/delete',params)
//会员等级列表
export const levelList=params=>server('/api/memberlevel/levelList',params)
//会员升级订单创建
export const leveordercreate=params=>server('/api/memberlevel/ordercreate',params)
//会员微信支付
export const levelpay=params=>server('/api/pay/pay',params)
//用户积分排行榜
export const pointRanking=params=>server('/api/memberaccount/pointRanking',params)
//订单列表
export const orderlist=params=>server('/api/order/lists',params)
//品牌热度排行榜
export const shopRanking=params=>server('/api/memberaccount/shopRanking',params)
//我的团队
export const teamList=params=>server('/fenxiao/api/fenxiao/teamList',params)
//关闭订单
export const orderClose=params=>server('/api/order/close',params)
//商家订单创建
export const storecreate=params=>server('/api/ordercreate/create',params)
//订单数量下标
export const orderNum=params=>server('/api/order/num',params)
//简介
export const Synopsis=params=>server('/api/api/info',params)
//订单详情
export const orderDetail=params=>server('/api/order/detail',params)
//邀请好友-数据
export const invitation=params=>server('/api/member/invitation',params)
//协议管理
export const getAgreement=params=>server('/api/api/getAgreement',params)
//推广海报
export const poster=params=>server('/fenxiao/api/fenxiao/poster',params)
//余额信息
export const memberaccountinfo=params=>server('/api/memberaccount/info',params)
//用户签到页面
export const getsignininfo=params=>server('/api/sign/getsignininfo',params)
//获取用户二维码
export const getqrcode=params=>server('/api/sign/getqrcode',params)
//签到扫码
export const dosignin=params=>server('/api/sign/dosignin',params)
//签到结算与返现配置
export const getsignsettlementconfig=params=>server('/api/sign/getsettlementconfig',params)
//我的钱包
export const moneyAccount=params=>server('/api/memberaccount/moneyAccount',params)
//我的积分
export const pointAccount=params=>server('/api/memberaccount/pointAccount',params)
//支付
export const huifupay=params=>server('/api/pay/huifupay',params)
//退款信息
export const refundData=params=>server('/api/orderrefund/refundData',params)
//申请售后
export const orderrefund=params=>server('/api/orderrefund/refund',params)
//订阅接口
export const messagetmplids=params=>server('/weapp/api/weapp/messagetmplids',params)
//购买会员
export const memberlevelpay=params=>server('/api/pay/memberlevelpay',params)
//退货详情
export const orderrefundDetail=params=>server('/api/orderrefund/detail',params)
//银行卡信息认证
export const memberopenaccount=params=>server('/api/memberopenaccount/add',params)
//认证信息
export const memberopenaccountInfo=params=>server('/api/memberopenaccount/info',params)
//门店详情
export const storeDetail=params=>server('/api/store/storeDetail',params)
//商品海报
export const goodsposter=params=>server('/api/goods/poster',params)
//入驻商家列表
export const websiteStore=params=>server('/api/store/websiteStore',params)
//短视频增加转发数
export const shareAdd=params=>server('/api/video/shareAdd',params)
//审核
export const reviewStatus=params=>server('/api/api/reviewStatus',params)
//获取/刷新订单动态核销码
export const getVerifyCode=params=>server('/api/order/verifyCode',params)
//商家商品分类列表
export const siteCategoryList=params=>server('/api/goodscategory/siteCategoryList',params)