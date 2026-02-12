(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["components/nvabar/nvabar"],{"0d97":function(n,t,e){"use strict";e.r(t);var a=e("6288"),u=e.n(a);for(var r in a)["default"].indexOf(r)<0&&function(n){e.d(t,n,(function(){return a[n]}))}(r);t["default"]=u.a},4523:function(n,t,e){"use strict";e.r(t);var a=e("bda7"),u=e("0d97");for(var r in u)["default"].indexOf(r)<0&&function(n){e.d(t,n,(function(){return u[n]}))}(r);var f=e("828b"),o=Object(f["a"])(u["default"],a["b"],a["c"],!1,null,null,null,!1,a["a"],void 0);t["default"]=o.exports},6288:function(n,t,e){"use strict";(function(n){Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var e={name:"nvabar",data:function(){return{}},props:{title:{type:String,default:""},bgcolor:{type:String,default:"#ffffff00"},titleStyle:{type:String,default:"color:#fff"},colors:{type:String,default:"#fff"},type:{type:Number,default:0}},methods:{left:function(){n.navigateBack()},home:function(){n.switchTab({url:"/pages/index/index"})}}};t.default=e}).call(this,e("df3c")["default"])},bda7:function(n,t,e){"use strict";e.d(t,"b",(function(){return u})),e.d(t,"c",(function(){return r})),e.d(t,"a",(function(){return a}));var a={uvNavbar:function(){return Promise.all([e.e("common/vendor"),e.e("uni_modules/uv-navbar/components/uv-navbar/uv-navbar")]).then(e.bind(null,"b258"))}},u=function(){var n=this.$createElement;this._self._c},r=[]}}]);
;(global["webpackJsonp"] = global["webpackJsonp"] || []).push([
    'components/nvabar/nvabar-create-component',
    {
        'components/nvabar/nvabar-create-component':(function(module, exports, __webpack_require__){
            __webpack_require__('df3c')['createComponent'](__webpack_require__("4523"))
        })
    },
    [['components/nvabar/nvabar-create-component']]
]);
