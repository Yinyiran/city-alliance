import { server } from "./http";
//获取openid
export const authcodetoopenid=params=>server('/weappqy/api/weapp/authcodetoopenid',params)
//小程序登录
export const loginAuth=params=>server('/shopapplet/login/auth',params)
// 手机号授权登录
export const mobileAuth=params=>server('/shopapplet/login/mobileAuth',params)
//首页头部数据
export const getnum=params=>server('/shopapplet/index/getnum',params)
//团队列表
export const getteam=params=>server('/shopapplet/index/getteam',params)
//员工积分排行榜
export const getmemberranklist=params=>server('/shopapplet/index/getmemberranklist ',params)
//品牌积分排行榜
export const getRankList=params=>server('/shopapplet/index/getshopranklist',params)
//添加员工
export const employeeAdd=params=>server('/shopapplet/employee/adds',params)
//发送验证码
export const mobliecode=params=>server('/shopapplet/employee/mobliecode',params)
//签到列表
export const signlist=params=>server('/shopapplet/sign/getlist',params)
//累计签到统计
export const signIndex=params=>server('/shopapplet/sign/index',params)
//订单列表
export const orderlist=params=>server('/shopapplet/order/orderlist',params)
//待核销订单列表
export const deliveryOrder=params=>server('/shopapplet/order/deliveryOrder',params)
//我的页面头部数据
export const myIndex=params=>server('/shopapplet/my/index',params)
//员工会员邀约排行
export const invitemember=params=>server('/shopapplet/my/invitemember',params)
//自签单排行
export const orderbyselfrank=params=>server('/shopapplet/my/orderbyselfrank',params)
//贡献单排行
export const conorderrank=params=>server('/shopapplet/my/conorderrank',params)
//订单核销页面
export const getOrderInfo=params=>server('/shopapplet/order/getOrderInfo',params)
//立即核销
export const dodelivery=params=>server('/shopapplet/order/dodelivery',params)
//我的积分
export const pointAccount=params=>server('/shopapplet/points/getList',params)
//保证金缴纳
export const deposit=params=>server('/shopapplet/shop/deposit',params)
//保证金缴纳1
export const shopdepositpay=params=>server('/api/pay/shopdepositpay',params)
//检查登录（silent=true 时不弹错误 toast，用于登录流程中的辅助判断）
export const logincheckLogin=(params,silent)=>server('/shopapplet/login/checkLogin',params,'post',silent)
//商家信息
export const getInfo=params=>server('/shopapplet/shop/getInfo',params)
//协议管理
export const getAgreement=params=>server('/api/api/getAgreement',params)
//数据大屏
export const miniindex=params=>server('/shopapplet/screen/miniindex',params)
//企业用户基本信息开户详情
export const openAccountDetail=params=>server('/shopapplet/merchant/openAccountDetail',params)
//企业用户基本信息开户
export const openAccount=params=>server('/shopapplet/merchant/openAccount',params)
//地址列表
export const arealists=params=>server('/api/address/lists',params)
//提交开卡信息
export const openCard=params=>server('/shopapplet/merchant/openCard',params)
//总行银行号列表
export const bankCode=params=>server('/shopapplet/merchant/bankCode',params)
//支行列表
export const branchCode=params=>server('/shopapplet/merchant/branchCode',params)
//关联企业列表
export const shopList=params=>server('/shopapplet/merchant/shopList',params)
//切换商家
export const shopLogin=params=>server('/shopapplet/merchant/shopLogin',params)
//申请提现
export const memberwithdrawapply=params=>server('/api/memberwithdraw/apply',params)
//提现设置
export const memberwithdrawconfig=params=>server('/api/memberwithdraw/config',params)