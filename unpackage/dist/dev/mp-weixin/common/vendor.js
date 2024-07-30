(global["webpackJsonp"] = global["webpackJsonp"] || []).push([["common/vendor"],{

/***/ 1:
/*!*********************************************************!*\
  !*** ./node_modules/@dcloudio/uni-mp-weixin/dist/wx.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var objectKeys = ['qy', 'env', 'error', 'version', 'lanDebug', 'cloud', 'serviceMarket', 'router', 'worklet', '__webpack_require_UNI_MP_PLUGIN__'];
var singlePageDisableKey = ['lanDebug', 'router', 'worklet'];
var target = typeof globalThis !== 'undefined' ? globalThis : function () {
  return this;
}();
var key = ['w', 'x'].join('');
var oldWx = target[key];
var launchOption = oldWx.getLaunchOptionsSync ? oldWx.getLaunchOptionsSync() : null;
function isWxKey(key) {
  if (launchOption && launchOption.scene === 1154 && singlePageDisableKey.includes(key)) {
    return false;
  }
  return objectKeys.indexOf(key) > -1 || typeof oldWx[key] === 'function';
}
function initWx() {
  var newWx = {};
  for (var _key in oldWx) {
    if (isWxKey(_key)) {
      // TODO wrapper function
      newWx[_key] = oldWx[_key];
    }
  }
  return newWx;
}
target[key] = initWx();
var _default = target[key];
exports.default = _default;

/***/ }),

/***/ 10:
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/nonIterableRest.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableRest, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 11:
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ 12);
function _defineProperty(obj, key, value) {
  key = toPropertyKey(key);
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }
  return obj;
}
module.exports = _defineProperty, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 12:
/*!**************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/toPropertyKey.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var _typeof = __webpack_require__(/*! ./typeof.js */ 13)["default"];
var toPrimitive = __webpack_require__(/*! ./toPrimitive.js */ 14);
function toPropertyKey(t) {
  var i = toPrimitive(t, "string");
  return "symbol" == _typeof(i) ? i : i + "";
}
module.exports = toPropertyKey, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 13:
/*!*******************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/typeof.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _typeof(o) {
  "@babel/helpers - typeof";

  return (module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports), _typeof(o);
}
module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 14:
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/toPrimitive.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var _typeof = __webpack_require__(/*! ./typeof.js */ 13)["default"];
function toPrimitive(t, r) {
  if ("object" != _typeof(t) || !t) return t;
  var e = t[Symbol.toPrimitive];
  if (void 0 !== e) {
    var i = e.call(t, r || "default");
    if ("object" != _typeof(i)) return i;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return ("string" === r ? String : Number)(t);
}
module.exports = toPrimitive, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 15:
/*!**********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/construct.js ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ 16);
var isNativeReflectConstruct = __webpack_require__(/*! ./isNativeReflectConstruct.js */ 17);
function _construct(t, e, r) {
  if (isNativeReflectConstruct()) return Reflect.construct.apply(null, arguments);
  var o = [null];
  o.push.apply(o, e);
  var p = new (t.bind.apply(t, o))();
  return r && setPrototypeOf(p, r.prototype), p;
}
module.exports = _construct, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 16:
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _setPrototypeOf(o, p) {
  module.exports = _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports;
  return _setPrototypeOf(o, p);
}
module.exports = _setPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 17:
/*!*************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/isNativeReflectConstruct.js ***!
  \*************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _isNativeReflectConstruct() {
  try {
    var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
  } catch (t) {}
  return (module.exports = _isNativeReflectConstruct = function _isNativeReflectConstruct() {
    return !!t;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports)();
}
module.exports = _isNativeReflectConstruct, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 174:
/*!**************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-ui-tools/libs/mixin/mpMixin.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _default = {
  // 将自定义节点设置成虚拟的（去掉自定义组件包裹层），更加接近Vue组件的表现，能更好的使用flex属性
  options: {
    virtualHost: true
  }
};
exports.default = _default;

/***/ }),

/***/ 175:
/*!************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-ui-tools/libs/mixin/mixin.js ***!
  \************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ 13);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var index = _interopRequireWildcard(__webpack_require__(/*! ../function/index.js */ 176));
var test = _interopRequireWildcard(__webpack_require__(/*! ../function/test.js */ 177));
var _route = _interopRequireDefault(__webpack_require__(/*! ../util/route.js */ 180));
var _debounce = _interopRequireDefault(__webpack_require__(/*! ../function/debounce.js */ 181));
var _throttle = _interopRequireDefault(__webpack_require__(/*! ../function/throttle.js */ 182));
function _getRequireWildcardCache(nodeInterop) { if (typeof WeakMap !== "function") return null; var cacheBabelInterop = new WeakMap(); var cacheNodeInterop = new WeakMap(); return (_getRequireWildcardCache = function _getRequireWildcardCache(nodeInterop) { return nodeInterop ? cacheNodeInterop : cacheBabelInterop; })(nodeInterop); }
function _interopRequireWildcard(obj, nodeInterop) { if (!nodeInterop && obj && obj.__esModule) { return obj; } if (obj === null || _typeof(obj) !== "object" && typeof obj !== "function") { return { default: obj }; } var cache = _getRequireWildcardCache(nodeInterop); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (key !== "default" && Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj.default = obj; if (cache) { cache.set(obj, newObj); } return newObj; }
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default2 = {
  // 定义每个组件都可能需要用到的外部样式以及类名
  props: {
    // 每个组件都有的父组件传递的样式，可以为字符串或者对象形式
    customStyle: {
      type: [Object, String],
      default: function _default() {
        return {};
      }
    },
    customClass: {
      type: String,
      default: ''
    },
    // 跳转的页面路径
    url: {
      type: String,
      default: ''
    },
    // 页面跳转的类型
    linkType: {
      type: String,
      default: 'navigateTo'
    }
  },
  data: function data() {
    return {};
  },
  onLoad: function onLoad() {
    // getRect挂载到$uv上，因为这方法需要使用in(this)，所以无法把它独立成一个单独的文件导出
    this.$uv.getRect = this.$uvGetRect;
  },
  created: function created() {
    // 组件当中，只有created声明周期，为了能在组件使用，故也在created中将方法挂载到$uv
    this.$uv.getRect = this.$uvGetRect;
  },
  computed: {
    $uv: function $uv() {
      var _uni, _uni$$uv, _uni$$uv$config;
      return _objectSpread(_objectSpread({}, index), {}, {
        test: test,
        route: _route.default,
        debounce: _debounce.default,
        throttle: _throttle.default,
        unit: (_uni = uni) === null || _uni === void 0 ? void 0 : (_uni$$uv = _uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$config = _uni$$uv.config) === null || _uni$$uv$config === void 0 ? void 0 : _uni$$uv$config.unit
      });
    },
    /**
     * 生成bem规则类名
     * 由于微信小程序，H5，nvue之间绑定class的差异，无法通过:class="[bem()]"的形式进行同用
     * 故采用如下折中做法，最后返回的是数组（一般平台）或字符串（支付宝和字节跳动平台），类似['a', 'b', 'c']或'a b c'的形式
     * @param {String} name 组件名称
     * @param {Array} fixed 一直会存在的类名
     * @param {Array} change 会根据变量值为true或者false而出现或者隐藏的类名
     * @returns {Array|string}
     */
    bem: function bem() {
      return function (name, fixed, change) {
        var _this = this;
        // 类名前缀
        var prefix = "uv-".concat(name, "--");
        var classes = {};
        if (fixed) {
          fixed.map(function (item) {
            // 这里的类名，会一直存在
            classes[prefix + _this[item]] = true;
          });
        }
        if (change) {
          change.map(function (item) {
            // 这里的类名，会根据this[item]的值为true或者false，而进行添加或者移除某一个类
            _this[item] ? classes[prefix + item] = _this[item] : delete classes[prefix + item];
          });
        }
        return Object.keys(classes);
        // 支付宝，头条小程序无法动态绑定一个数组类名，否则解析出来的结果会带有","，而导致失效
      };
    }
  },

  methods: {
    // 跳转某一个页面
    openPage: function openPage() {
      var urlKey = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'url';
      var url = this[urlKey];
      if (url) {
        // 执行类似uni.navigateTo的方法
        uni[this.linkType]({
          url: url
        });
      }
    },
    // 查询节点信息
    // 目前此方法在支付宝小程序中无法获取组件跟接点的尺寸，为支付宝的bug(2020-07-21)
    // 解决办法为在组件根部再套一个没有任何作用的view元素
    $uvGetRect: function $uvGetRect(selector, all) {
      var _this2 = this;
      return new Promise(function (resolve) {
        uni.createSelectorQuery().in(_this2)[all ? 'selectAll' : 'select'](selector).boundingClientRect(function (rect) {
          if (all && Array.isArray(rect) && rect.length) {
            resolve(rect);
          }
          if (!all && rect) {
            resolve(rect);
          }
        }).exec();
      });
    },
    getParentData: function getParentData() {
      var _this3 = this;
      var parentName = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      // 避免在created中去定义parent变量
      if (!this.parent) this.parent = {};
      // 这里的本质原理是，通过获取父组件实例(也即类似uv-radio的父组件uv-radio-group的this)
      // 将父组件this中对应的参数，赋值给本组件(uv-radio的this)的parentData对象中对应的属性
      // 之所以需要这么做，是因为所有端中，头条小程序不支持通过this.parent.xxx去监听父组件参数的变化
      // 此处并不会自动更新子组件的数据，而是依赖父组件uv-radio-group去监听data的变化，手动调用更新子组件的方法去重新获取
      this.parent = this.$uv.$parent.call(this, parentName);
      if (this.parent.children) {
        // 如果父组件的children不存在本组件的实例，才将本实例添加到父组件的children中
        this.parent.children.indexOf(this) === -1 && this.parent.children.push(this);
      }
      if (this.parent && this.parentData) {
        // 历遍parentData中的属性，将parent中的同名属性赋值给parentData
        Object.keys(this.parentData).map(function (key) {
          _this3.parentData[key] = _this3.parent[key];
        });
      }
    },
    // 阻止事件冒泡
    preventEvent: function preventEvent(e) {
      e && typeof e.stopPropagation === 'function' && e.stopPropagation();
    },
    // 空操作
    noop: function noop(e) {
      this.preventEvent(e);
    }
  },
  onReachBottom: function onReachBottom() {
    uni.$emit('uvOnReachBottom');
  },
  beforeDestroy: function beforeDestroy() {
    var _this4 = this;
    // 判断当前页面是否存在parent和chldren，一般在checkbox和checkbox-group父子联动的场景会有此情况
    // 组件销毁时，移除子组件在父组件children数组中的实例，释放资源，避免数据混乱
    if (this.parent && test.array(this.parent.children)) {
      // 组件销毁时，移除父组件中的children数组中对应的实例
      var childrenList = this.parent.children;
      childrenList.map(function (child, index) {
        // 如果相等，则移除
        if (child === _this4) {
          childrenList.splice(index, 1);
        }
      });
    }
  },
  // 兼容vue3
  unmounted: function unmounted() {
    var _this5 = this;
    if (this.parent && test.array(this.parent.children)) {
      // 组件销毁时，移除父组件中的children数组中对应的实例
      var childrenList = this.parent.children;
      childrenList.map(function (child, index) {
        // 如果相等，则移除
        if (child === _this5) {
          childrenList.splice(index, 1);
        }
      });
    }
  }
};
exports.default = _default2;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 176:
/*!***************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-ui-tools/libs/function/index.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.$parent = $parent;
exports.addStyle = addStyle;
exports.addUnit = addUnit;
exports.deepClone = deepClone;
exports.deepMerge = deepMerge;
exports.error = error;
exports.formValidate = formValidate;
exports.getDuration = getDuration;
exports.getHistoryPage = getHistoryPage;
exports.getProperty = getProperty;
exports.getPx = getPx;
exports.guid = guid;
exports.os = os;
exports.padZero = padZero;
exports.page = page;
exports.pages = pages;
exports.priceFormat = priceFormat;
exports.queryParams = queryParams;
exports.random = random;
exports.randomArray = randomArray;
exports.range = range;
exports.setConfig = setConfig;
exports.setProperty = setProperty;
exports.sleep = sleep;
exports.sys = sys;
exports.timeFormat = timeFormat;
exports.timeFrom = timeFrom;
exports.toast = toast;
exports.trim = trim;
exports.type2icon = type2icon;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ 5));
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ 13));
var _test = __webpack_require__(/*! ./test.js */ 177);
var _digit = __webpack_require__(/*! ./digit.js */ 178);
/**
 * @description 如果value小于min，取min；如果value大于max，取max
 * @param {number} min
 * @param {number} max
 * @param {number} value
 */
function range() {
  var min = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
  var max = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
  var value = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0;
  return Math.max(min, Math.min(max, Number(value)));
}

/**
 * @description 用于获取用户传递值的px值  如果用户传递了"xxpx"或者"xxrpx"，取出其数值部分，如果是"xxxrpx"还需要用过uni.upx2px进行转换
 * @param {number|string} value 用户传递值的px值
 * @param {boolean} unit
 * @returns {number|string}
 */
function getPx(value) {
  var unit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  if ((0, _test.number)(value)) {
    return unit ? "".concat(value, "px") : Number(value);
  }
  // 如果带有rpx，先取出其数值部分，再转为px值
  if (/(rpx|upx)$/.test(value)) {
    return unit ? "".concat(uni.upx2px(parseInt(value)), "px") : Number(uni.upx2px(parseInt(value)));
  }
  return unit ? "".concat(parseInt(value), "px") : parseInt(value);
}

/**
 * @description 进行延时，以达到可以简写代码的目的 比如: await uni.$uv.sleep(20)将会阻塞20ms
 * @param {number} value 堵塞时间 单位ms 毫秒
 * @returns {Promise} 返回promise
 */
function sleep() {
  var value = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 30;
  return new Promise(function (resolve) {
    setTimeout(function () {
      resolve();
    }, value);
  });
}
/**
 * @description 运行期判断平台
 * @returns {string} 返回所在平台(小写)
 * @link 运行期判断平台 https://uniapp.dcloud.io/frame?id=判断平台
 */
function os() {
  return uni.getSystemInfoSync().platform.toLowerCase();
}
/**
 * @description 获取系统信息同步接口
 * @link 获取系统信息同步接口 https://uniapp.dcloud.io/api/system/info?id=getsysteminfosync
 */
function sys() {
  return uni.getSystemInfoSync();
}

/**
 * @description 取一个区间数
 * @param {Number} min 最小值
 * @param {Number} max 最大值
 */
function random(min, max) {
  if (min >= 0 && max > 0 && max >= min) {
    var gab = max - min + 1;
    return Math.floor(Math.random() * gab + min);
  }
  return 0;
}

/**
 * @param {Number} len uuid的长度
 * @param {Boolean} firstU 将返回的首字母置为"u"
 * @param {Nubmer} radix 生成uuid的基数(意味着返回的字符串都是这个基数),2-二进制,8-八进制,10-十进制,16-十六进制
 */
function guid() {
  var len = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 32;
  var firstU = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
  var radix = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
  var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');
  var uuid = [];
  radix = radix || chars.length;
  if (len) {
    // 如果指定uuid长度,只是取随机的字符,0|x为位运算,能去掉x的小数位,返回整数位
    for (var i = 0; i < len; i++) {
      uuid[i] = chars[0 | Math.random() * radix];
    }
  } else {
    var r;
    // rfc4122标准要求返回的uuid中,某些位为固定的字符
    uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
    uuid[14] = '4';
    for (var _i = 0; _i < 36; _i++) {
      if (!uuid[_i]) {
        r = 0 | Math.random() * 16;
        uuid[_i] = chars[_i == 19 ? r & 0x3 | 0x8 : r];
      }
    }
  }
  // 移除第一个字符,并用u替代,因为第一个字符为数值时,该guuid不能用作id或者class
  if (firstU) {
    uuid.shift();
    return "u".concat(uuid.join(''));
  }
  return uuid.join('');
}

/**
* @description 获取父组件的参数，因为支付宝小程序不支持provide/inject的写法
   this.$parent在非H5中，可以准确获取到父组件，但是在H5中，需要多次this.$parent.$parent.xxx
   这里默认值等于undefined有它的含义，因为最顶层元素(组件)的$parent就是undefined，意味着不传name
   值(默认为undefined)，就是查找最顶层的$parent
*  @param {string|undefined} name 父组件的参数名
*/
function $parent() {
  var name = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : undefined;
  var parent = this.$parent;
  // 通过while历遍，这里主要是为了H5需要多层解析的问题
  while (parent) {
    // 父组件
    if (parent.$options && parent.$options.name !== name) {
      // 如果组件的name不相等，继续上一级寻找
      parent = parent.$parent;
    } else {
      return parent;
    }
  }
  return false;
}

/**
 * @description 样式转换
 * 对象转字符串，或者字符串转对象
 * @param {object | string} customStyle 需要转换的目标
 * @param {String} target 转换的目的，object-转为对象，string-转为字符串
 * @returns {object|string}
 */
function addStyle(customStyle) {
  var target = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'object';
  // 字符串转字符串，对象转对象情形，直接返回
  if ((0, _test.empty)(customStyle) || (0, _typeof2.default)(customStyle) === 'object' && target === 'object' || target === 'string' && typeof customStyle === 'string') {
    return customStyle;
  }
  // 字符串转对象
  if (target === 'object') {
    // 去除字符串样式中的两端空格(中间的空格不能去掉，比如padding: 20px 0如果去掉了就错了)，空格是无用的
    customStyle = trim(customStyle);
    // 根据";"将字符串转为数组形式
    var styleArray = customStyle.split(';');
    var style = {};
    // 历遍数组，拼接成对象
    for (var i = 0; i < styleArray.length; i++) {
      // 'font-size:20px;color:red;'，如此最后字符串有";"的话，会导致styleArray最后一个元素为空字符串，这里需要过滤
      if (styleArray[i]) {
        var item = styleArray[i].split(':');
        style[trim(item[0])] = trim(item[1]);
      }
    }
    return style;
  }
  // 这里为对象转字符串形式
  var string = '';
  for (var _i2 in customStyle) {
    // 驼峰转为中划线的形式，否则css内联样式，无法识别驼峰样式属性名
    var key = _i2.replace(/([A-Z])/g, '-$1').toLowerCase();
    string += "".concat(key, ":").concat(customStyle[_i2], ";");
  }
  // 去除两端空格
  return trim(string);
}

/**
 * @description 添加单位，如果有rpx，upx，%，px等单位结尾或者值为auto，直接返回，否则加上px单位结尾
 * @param {string|number} value 需要添加单位的值
 * @param {string} unit 添加的单位名 比如px
 */
function addUnit() {
  var _uni, _uni$$uv, _uni$$uv$config, _uni2, _uni2$$uv, _uni2$$uv$config;
  var value = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'auto';
  var unit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : (_uni = uni) !== null && _uni !== void 0 && (_uni$$uv = _uni.$uv) !== null && _uni$$uv !== void 0 && (_uni$$uv$config = _uni$$uv.config) !== null && _uni$$uv$config !== void 0 && _uni$$uv$config.unit ? (_uni2 = uni) === null || _uni2 === void 0 ? void 0 : (_uni2$$uv = _uni2.$uv) === null || _uni2$$uv === void 0 ? void 0 : (_uni2$$uv$config = _uni2$$uv.config) === null || _uni2$$uv$config === void 0 ? void 0 : _uni2$$uv$config.unit : 'px';
  value = String(value);
  // 用uvui内置验证规则中的number判断是否为数值
  return (0, _test.number)(value) ? "".concat(value).concat(unit) : value;
}

/**
 * @description 深度克隆
 * @param {object} obj 需要深度克隆的对象
 * @param cache 缓存
 * @returns {*} 克隆后的对象或者原值（不是对象）
 */
function deepClone(obj) {
  var cache = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : new WeakMap();
  if (obj === null || (0, _typeof2.default)(obj) !== 'object') return obj;
  if (cache.has(obj)) return cache.get(obj);
  var clone;
  if (obj instanceof Date) {
    clone = new Date(obj.getTime());
  } else if (obj instanceof RegExp) {
    clone = new RegExp(obj);
  } else if (obj instanceof Map) {
    clone = new Map(Array.from(obj, function (_ref) {
      var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
        key = _ref2[0],
        value = _ref2[1];
      return [key, deepClone(value, cache)];
    }));
  } else if (obj instanceof Set) {
    clone = new Set(Array.from(obj, function (value) {
      return deepClone(value, cache);
    }));
  } else if (Array.isArray(obj)) {
    clone = obj.map(function (value) {
      return deepClone(value, cache);
    });
  } else if (Object.prototype.toString.call(obj) === '[object Object]') {
    clone = Object.create(Object.getPrototypeOf(obj));
    cache.set(obj, clone);
    for (var _i3 = 0, _Object$entries = Object.entries(obj); _i3 < _Object$entries.length; _i3++) {
      var _Object$entries$_i = (0, _slicedToArray2.default)(_Object$entries[_i3], 2),
        key = _Object$entries$_i[0],
        value = _Object$entries$_i[1];
      clone[key] = deepClone(value, cache);
    }
  } else {
    clone = Object.assign({}, obj);
  }
  cache.set(obj, clone);
  return clone;
}

/**
 * @description JS对象深度合并
 * @param {object} target 需要拷贝的对象
 * @param {object} source 拷贝的来源对象
 * @returns {object|boolean} 深度合并后的对象或者false（入参有不是对象）
 */
function deepMerge() {
  var target = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var source = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  target = deepClone(target);
  if ((0, _typeof2.default)(target) !== 'object' || target === null || (0, _typeof2.default)(source) !== 'object' || source === null) return target;
  var merged = Array.isArray(target) ? target.slice() : Object.assign({}, target);
  for (var prop in source) {
    if (!source.hasOwnProperty(prop)) continue;
    var sourceValue = source[prop];
    var targetValue = merged[prop];
    if (sourceValue instanceof Date) {
      merged[prop] = new Date(sourceValue);
    } else if (sourceValue instanceof RegExp) {
      merged[prop] = new RegExp(sourceValue);
    } else if (sourceValue instanceof Map) {
      merged[prop] = new Map(sourceValue);
    } else if (sourceValue instanceof Set) {
      merged[prop] = new Set(sourceValue);
    } else if ((0, _typeof2.default)(sourceValue) === 'object' && sourceValue !== null) {
      merged[prop] = deepMerge(targetValue, sourceValue);
    } else {
      merged[prop] = sourceValue;
    }
  }
  return merged;
}

/**
 * @description error提示
 * @param {*} err 错误内容
 */
function error(err) {
  // 开发环境才提示，生产环境不会提示
  if (true) {
    console.error("uvui\u63D0\u793A\uFF1A".concat(err));
  }
}

/**
 * @description 打乱数组
 * @param {array} array 需要打乱的数组
 * @returns {array} 打乱后的数组
 */
function randomArray() {
  var array = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  // 原理是sort排序,Math.random()产生0<= x < 1之间的数,会导致x-0.05大于或者小于0
  return array.sort(function () {
    return Math.random() - 0.5;
  });
}

// padStart 的 polyfill，因为某些机型或情况，还无法支持es7的padStart，比如电脑版的微信小程序
// 所以这里做一个兼容polyfill的兼容处理
if (!String.prototype.padStart) {
  // 为了方便表示这里 fillString 用了ES6 的默认参数，不影响理解
  String.prototype.padStart = function (maxLength) {
    var fillString = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : ' ';
    if (Object.prototype.toString.call(fillString) !== '[object String]') {
      throw new TypeError('fillString must be String');
    }
    var str = this;
    // 返回 String(str) 这里是为了使返回的值是字符串字面量，在控制台中更符合直觉
    if (str.length >= maxLength) return String(str);
    var fillLength = maxLength - str.length;
    var times = Math.ceil(fillLength / fillString.length);
    while (times >>= 1) {
      fillString += fillString;
      if (times === 1) {
        fillString += fillString;
      }
    }
    return fillString.slice(0, fillLength) + str;
  };
}

/**
 * @description 格式化时间
 * @param {String|Number} dateTime 需要格式化的时间戳
 * @param {String} fmt 格式化规则 yyyy:mm:dd|yyyy:mm|yyyy年mm月dd日|yyyy年mm月dd日 hh时MM分等,可自定义组合 默认yyyy-mm-dd
 * @returns {string} 返回格式化后的字符串
 */
function timeFormat() {
  var dateTime = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var formatStr = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'yyyy-mm-dd';
  var date;
  // 若传入时间为假值，则取当前时间
  if (!dateTime) {
    date = new Date();
  }
  // 若为unix秒时间戳，则转为毫秒时间戳（逻辑有点奇怪，但不敢改，以保证历史兼容）
  else if (/^\d{10}$/.test(dateTime === null || dateTime === void 0 ? void 0 : dateTime.toString().trim())) {
    date = new Date(dateTime * 1000);
  }
  // 若用户传入字符串格式时间戳，new Date无法解析，需做兼容
  else if (typeof dateTime === 'string' && /^\d+$/.test(dateTime.trim())) {
    date = new Date(Number(dateTime));
  }
  // 处理平台性差异，在Safari/Webkit中，new Date仅支持/作为分割符的字符串时间
  // 处理 '2022-07-10 01:02:03'，跳过 '2022-07-10T01:02:03'
  else if (typeof dateTime === 'string' && dateTime.includes('-') && !dateTime.includes('T')) {
    date = new Date(dateTime.replace(/-/g, '/'));
  }
  // 其他都认为符合 RFC 2822 规范
  else {
    date = new Date(dateTime);
  }
  var timeSource = {
    'y': date.getFullYear().toString(),
    // 年
    'm': (date.getMonth() + 1).toString().padStart(2, '0'),
    // 月
    'd': date.getDate().toString().padStart(2, '0'),
    // 日
    'h': date.getHours().toString().padStart(2, '0'),
    // 时
    'M': date.getMinutes().toString().padStart(2, '0'),
    // 分
    's': date.getSeconds().toString().padStart(2, '0') // 秒
    // 有其他格式化字符需求可以继续添加，必须转化成字符串
  };

  for (var key in timeSource) {
    var _ref3 = new RegExp("".concat(key, "+")).exec(formatStr) || [],
      _ref4 = (0, _slicedToArray2.default)(_ref3, 1),
      ret = _ref4[0];
    if (ret) {
      // 年可能只需展示两位
      var beginIndex = key === 'y' && ret.length === 2 ? 2 : 0;
      formatStr = formatStr.replace(ret, timeSource[key].slice(beginIndex));
    }
  }
  return formatStr;
}

/**
 * @description 时间戳转为多久之前
 * @param {String|Number} timestamp 时间戳
 * @param {String|Boolean} format
 * 格式化规则如果为时间格式字符串，超出一定时间范围，返回固定的时间格式；
 * 如果为布尔值false，无论什么时间，都返回多久以前的格式
 * @returns {string} 转化后的内容
 */
function timeFrom() {
  var timestamp = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var format = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'yyyy-mm-dd';
  if (timestamp == null) timestamp = Number(new Date());
  timestamp = parseInt(timestamp);
  // 判断用户输入的时间戳是秒还是毫秒,一般前端js获取的时间戳是毫秒(13位),后端传过来的为秒(10位)
  if (timestamp.toString().length == 10) timestamp *= 1000;
  var timer = new Date().getTime() - timestamp;
  timer = parseInt(timer / 1000);
  // 如果小于5分钟,则返回"刚刚",其他以此类推
  var tips = '';
  switch (true) {
    case timer < 300:
      tips = '刚刚';
      break;
    case timer >= 300 && timer < 3600:
      tips = "".concat(parseInt(timer / 60), "\u5206\u949F\u524D");
      break;
    case timer >= 3600 && timer < 86400:
      tips = "".concat(parseInt(timer / 3600), "\u5C0F\u65F6\u524D");
      break;
    case timer >= 86400 && timer < 2592000:
      tips = "".concat(parseInt(timer / 86400), "\u5929\u524D");
      break;
    default:
      // 如果format为false，则无论什么时间戳，都显示xx之前
      if (format === false) {
        if (timer >= 2592000 && timer < 365 * 86400) {
          tips = "".concat(parseInt(timer / (86400 * 30)), "\u4E2A\u6708\u524D");
        } else {
          tips = "".concat(parseInt(timer / (86400 * 365)), "\u5E74\u524D");
        }
      } else {
        tips = timeFormat(timestamp, format);
      }
  }
  return tips;
}

/**
 * @description 去除空格
 * @param String str 需要去除空格的字符串
 * @param String pos both(左右)|left|right|all 默认both
 */
function trim(str) {
  var pos = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'both';
  str = String(str);
  if (pos == 'both') {
    return str.replace(/^\s+|\s+$/g, '');
  }
  if (pos == 'left') {
    return str.replace(/^\s*/, '');
  }
  if (pos == 'right') {
    return str.replace(/(\s*$)/g, '');
  }
  if (pos == 'all') {
    return str.replace(/\s+/g, '');
  }
  return str;
}

/**
 * @description 对象转url参数
 * @param {object} data,对象
 * @param {Boolean} isPrefix,是否自动加上"?"
 * @param {string} arrayFormat 规则 indices|brackets|repeat|comma
 */
function queryParams() {
  var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var isPrefix = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
  var arrayFormat = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'brackets';
  var prefix = isPrefix ? '?' : '';
  var _result = [];
  if (['indices', 'brackets', 'repeat', 'comma'].indexOf(arrayFormat) == -1) arrayFormat = 'brackets';
  var _loop = function _loop(key) {
    var value = data[key];
    // 去掉为空的参数
    if (['', undefined, null].indexOf(value) >= 0) {
      return "continue";
    }
    // 如果值为数组，另行处理
    if (value.constructor === Array) {
      // e.g. {ids: [1, 2, 3]}
      switch (arrayFormat) {
        case 'indices':
          // 结果: ids[0]=1&ids[1]=2&ids[2]=3
          for (var i = 0; i < value.length; i++) {
            _result.push("".concat(key, "[").concat(i, "]=").concat(value[i]));
          }
          break;
        case 'brackets':
          // 结果: ids[]=1&ids[]=2&ids[]=3
          value.forEach(function (_value) {
            _result.push("".concat(key, "[]=").concat(_value));
          });
          break;
        case 'repeat':
          // 结果: ids=1&ids=2&ids=3
          value.forEach(function (_value) {
            _result.push("".concat(key, "=").concat(_value));
          });
          break;
        case 'comma':
          // 结果: ids=1,2,3
          var commaStr = '';
          value.forEach(function (_value) {
            commaStr += (commaStr ? ',' : '') + _value;
          });
          _result.push("".concat(key, "=").concat(commaStr));
          break;
        default:
          value.forEach(function (_value) {
            _result.push("".concat(key, "[]=").concat(_value));
          });
      }
    } else {
      _result.push("".concat(key, "=").concat(value));
    }
  };
  for (var key in data) {
    var _ret = _loop(key);
    if (_ret === "continue") continue;
  }
  return _result.length ? prefix + _result.join('&') : '';
}

/**
 * 显示消息提示框
 * @param {String} title 提示的内容，长度与 icon 取值有关。
 * @param {Number} duration 提示的延迟时间，单位毫秒，默认：2000
 */
function toast(title) {
  var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 2000;
  uni.showToast({
    title: String(title),
    icon: 'none',
    duration: duration
  });
}

/**
 * @description 根据主题type值,获取对应的图标
 * @param {String} type 主题名称,primary|info|error|warning|success
 * @param {boolean} fill 是否使用fill填充实体的图标
 */
function type2icon() {
  var type = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'success';
  var fill = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  // 如果非预置值,默认为success
  if (['primary', 'info', 'error', 'warning', 'success'].indexOf(type) == -1) type = 'success';
  var iconName = '';
  // 目前(2019-12-12),info和primary使用同一个图标
  switch (type) {
    case 'primary':
      iconName = 'info-circle';
      break;
    case 'info':
      iconName = 'info-circle';
      break;
    case 'error':
      iconName = 'close-circle';
      break;
    case 'warning':
      iconName = 'error-circle';
      break;
    case 'success':
      iconName = 'checkmark-circle';
      break;
    default:
      iconName = 'checkmark-circle';
  }
  // 是否是实体类型,加上-fill,在icon组件库中,实体的类名是后面加-fill的
  if (fill) iconName += '-fill';
  return iconName;
}

/**
 * @description 数字格式化
 * @param {number|string} number 要格式化的数字
 * @param {number} decimals 保留几位小数
 * @param {string} decimalPoint 小数点符号
 * @param {string} thousandsSeparator 千分位符号
 * @returns {string} 格式化后的数字
 */
function priceFormat(number) {
  var decimals = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
  var decimalPoint = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '.';
  var thousandsSeparator = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : ',';
  number = "".concat(number).replace(/[^0-9+-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number;
  var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
  var sep = typeof thousandsSeparator === 'undefined' ? ',' : thousandsSeparator;
  var dec = typeof decimalPoint === 'undefined' ? '.' : decimalPoint;
  var s = '';
  s = (prec ? (0, _digit.round)(n, prec) + '' : "".concat(Math.round(n))).split('.');
  var re = /(-?\d+)(\d{3})/;
  while (re.test(s[0])) {
    s[0] = s[0].replace(re, "$1".concat(sep, "$2"));
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

/**
 * @description 获取duration值
 * 如果带有ms或者s直接返回，如果大于一定值，认为是ms单位，小于一定值，认为是s单位
 * 比如以30位阈值，那么300大于30，可以理解为用户想要的是300ms，而不是想花300s去执行一个动画
 * @param {String|number} value 比如: "1s"|"100ms"|1|100
 * @param {boolean} unit  提示: 如果是false 默认返回number
 * @return {string|number}
 */
function getDuration(value) {
  var unit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
  var valueNum = parseInt(value);
  if (unit) {
    if (/s$/.test(value)) return value;
    return value > 30 ? "".concat(value, "ms") : "".concat(value, "s");
  }
  if (/ms$/.test(value)) return valueNum;
  if (/s$/.test(value)) return valueNum > 30 ? valueNum : valueNum * 1000;
  return valueNum;
}

/**
 * @description 日期的月或日补零操作
 * @param {String} value 需要补零的值
 */
function padZero(value) {
  return "00".concat(value).slice(-2);
}

/**
 * @description 在uv-form的子组件内容发生变化，或者失去焦点时，尝试通知uv-form执行校验方法
 * @param {*} instance
 * @param {*} event
 */
function formValidate(instance, event) {
  var formItem = $parent.call(instance, 'uv-form-item');
  var form = $parent.call(instance, 'uv-form');
  // 如果发生变化的input或者textarea等，其父组件中有uv-form-item或者uv-form等，就执行form的validate方法
  // 同时将form-item的pros传递给form，让其进行精确对象验证
  if (formItem && form) {
    form.validateField(formItem.prop, function () {}, event);
  }
}

/**
 * @description 获取某个对象下的属性，用于通过类似'a.b.c'的形式去获取一个对象的的属性的形式
 * @param {object} obj 对象
 * @param {string} key 需要获取的属性字段
 * @returns {*}
 */
function getProperty(obj, key) {
  if (!obj) {
    return;
  }
  if (typeof key !== 'string' || key === '') {
    return '';
  }
  if (key.indexOf('.') !== -1) {
    var keys = key.split('.');
    var firstObj = obj[keys[0]] || {};
    for (var i = 1; i < keys.length; i++) {
      if (firstObj) {
        firstObj = firstObj[keys[i]];
      }
    }
    return firstObj;
  }
  return obj[key];
}

/**
 * @description 设置对象的属性值，如果'a.b.c'的形式进行设置
 * @param {object} obj 对象
 * @param {string} key 需要设置的属性
 * @param {string} value 设置的值
 */
function setProperty(obj, key, value) {
  if (!obj) {
    return;
  }
  // 递归赋值
  var inFn = function inFn(_obj, keys, v) {
    // 最后一个属性key
    if (keys.length === 1) {
      _obj[keys[0]] = v;
      return;
    }
    // 0~length-1个key
    while (keys.length > 1) {
      var k = keys[0];
      if (!_obj[k] || (0, _typeof2.default)(_obj[k]) !== 'object') {
        _obj[k] = {};
      }
      var _key = keys.shift();
      // 自调用判断是否存在属性，不存在则自动创建对象
      inFn(_obj[k], keys, v);
    }
  };
  if (typeof key !== 'string' || key === '') {} else if (key.indexOf('.') !== -1) {
    // 支持多层级赋值操作
    var keys = key.split('.');
    inFn(obj, keys, value);
  } else {
    obj[key] = value;
  }
}

/**
 * @description 获取当前页面路径
 */
function page() {
  var _pages;
  var pages = getCurrentPages();
  var route = (_pages = pages[pages.length - 1]) === null || _pages === void 0 ? void 0 : _pages.route;
  // 某些特殊情况下(比如页面进行redirectTo时的一些时机)，pages可能为空数组
  return "/".concat(route ? route : '');
}

/**
 * @description 获取当前路由栈实例数组
 */
function pages() {
  var pages = getCurrentPages();
  return pages;
}

/**
 * 获取页面历史栈指定层实例
 * @param back {number} [0] - 0或者负数，表示获取历史栈的哪一层，0表示获取当前页面实例，-1 表示获取上一个页面实例。默认0。
 */
function getHistoryPage() {
  var back = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
  var pages = getCurrentPages();
  var len = pages.length;
  return pages[len - 1 + back];
}

/**
 * @description 修改uvui内置属性值
 * @param {object} props 修改内置props属性
 * @param {object} config 修改内置config属性
 * @param {object} color 修改内置color属性
 * @param {object} zIndex 修改内置zIndex属性
 */
function setConfig(_ref5) {
  var _ref5$props = _ref5.props,
    props = _ref5$props === void 0 ? {} : _ref5$props,
    _ref5$config = _ref5.config,
    config = _ref5$config === void 0 ? {} : _ref5$config,
    _ref5$color = _ref5.color,
    color = _ref5$color === void 0 ? {} : _ref5$color,
    _ref5$zIndex = _ref5.zIndex,
    zIndex = _ref5$zIndex === void 0 ? {} : _ref5$zIndex;
  var deepMerge = uni.$uv.deepMerge;
  uni.$uv.config = deepMerge(uni.$uv.config, config);
  uni.$uv.props = deepMerge(uni.$uv.props, props);
  uni.$uv.color = deepMerge(uni.$uv.color, color);
  uni.$uv.zIndex = deepMerge(uni.$uv.zIndex, zIndex);
}
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 177:
/*!**************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-ui-tools/libs/function/test.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.amount = amount;
exports.array = array;
exports.carNo = carNo;
exports.chinese = chinese;
exports.code = code;
exports.contains = contains;
exports.date = date;
exports.dateISO = dateISO;
exports.digits = digits;
exports.email = email;
exports.empty = empty;
exports.enOrNum = enOrNum;
exports.func = func;
exports.idCard = idCard;
exports.image = image;
exports.jsonString = jsonString;
exports.landline = landline;
exports.letter = letter;
exports.mobile = mobile;
exports.number = number;
exports.object = object;
exports.promise = promise;
exports.range = range;
exports.rangeLength = rangeLength;
exports.regExp = regExp;
exports.string = string;
exports.url = url;
exports.video = video;
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ 13));
/**
 * 验证电子邮箱格式
 */
function email(value) {
  return /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test(value);
}

/**
 * 验证手机格式
 */
function mobile(value) {
  return /^1([3589]\d|4[5-9]|6[1-2,4-7]|7[0-8])\d{8}$/.test(value);
}

/**
 * 验证URL格式
 */
function url(value) {
  return /^((https|http|ftp|rtsp|mms):\/\/)(([0-9a-zA-Z_!~*'().&=+$%-]+: )?[0-9a-zA-Z_!~*'().&=+$%-]+@)?(([0-9]{1,3}.){3}[0-9]{1,3}|([0-9a-zA-Z_!~*'()-]+.)*([0-9a-zA-Z][0-9a-zA-Z-]{0,61})?[0-9a-zA-Z].[a-zA-Z]{2,6})(:[0-9]{1,4})?((\/?)|(\/[0-9a-zA-Z_!~*'().;?:@&=+$,%#-]+)+\/?)$/.test(value);
}

/**
 * 验证日期格式
 */
function date(value) {
  if (!value) return false;
  // 判断是否数值或者字符串数值(意味着为时间戳)，转为数值，否则new Date无法识别字符串时间戳
  if (number(value)) value = +value;
  return !/Invalid|NaN/.test(new Date(value).toString());
}

/**
 * 验证ISO类型的日期格式
 */
function dateISO(value) {
  return /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(value);
}

/**
 * 验证十进制数字
 */
function number(value) {
  return /^[\+-]?(\d+\.?\d*|\.\d+|\d\.\d+e\+\d+)$/.test(value);
}

/**
 * 验证字符串
 */
function string(value) {
  return typeof value === 'string';
}

/**
 * 验证整数
 */
function digits(value) {
  return /^\d+$/.test(value);
}

/**
 * 验证身份证号码
 */
function idCard(value) {
  return /^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/.test(value);
}

/**
 * 是否车牌号
 */
function carNo(value) {
  // 新能源车牌
  var xreg = /^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}(([0-9]{5}[DF]$)|([DF][A-HJ-NP-Z0-9][0-9]{4}$))/;
  // 旧车牌
  var creg = /^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-HJ-NP-Z0-9]{4}[A-HJ-NP-Z0-9挂学警港澳]{1}$/;
  if (value.length === 7) {
    return creg.test(value);
  }
  if (value.length === 8) {
    return xreg.test(value);
  }
  return false;
}

/**
 * 金额,只允许2位小数
 */
function amount(value) {
  // 金额，只允许保留两位小数
  return /^[1-9]\d*(,\d{3})*(\.\d{1,2})?$|^0\.\d{1,2}$/.test(value);
}

/**
 * 中文
 */
function chinese(value) {
  var reg = /^[\u4e00-\u9fa5]+$/gi;
  return reg.test(value);
}

/**
 * 只能输入字母
 */
function letter(value) {
  return /^[a-zA-Z]*$/.test(value);
}

/**
 * 只能是字母或者数字
 */
function enOrNum(value) {
  // 英文或者数字
  var reg = /^[0-9a-zA-Z]*$/g;
  return reg.test(value);
}

/**
 * 验证是否包含某个值
 */
function contains(value, param) {
  return value.indexOf(param) >= 0;
}

/**
 * 验证一个值范围[min, max]
 */
function range(value, param) {
  return value >= param[0] && value <= param[1];
}

/**
 * 验证一个长度范围[min, max]
 */
function rangeLength(value, param) {
  return value.length >= param[0] && value.length <= param[1];
}

/**
 * 是否固定电话
 */
function landline(value) {
  var reg = /^\d{3,4}-\d{7,8}(-\d{3,4})?$/;
  return reg.test(value);
}

/**
 * 判断是否为空
 */
function empty(value) {
  switch ((0, _typeof2.default)(value)) {
    case 'undefined':
      return true;
    case 'string':
      if (value.replace(/(^[ \t\n\r]*)|([ \t\n\r]*$)/g, '').length == 0) return true;
      break;
    case 'boolean':
      if (!value) return true;
      break;
    case 'number':
      if (value === 0 || isNaN(value)) return true;
      break;
    case 'object':
      if (value === null || value.length === 0) return true;
      for (var i in value) {
        return false;
      }
      return true;
  }
  return false;
}

/**
 * 是否json字符串
 */
function jsonString(value) {
  if (typeof value === 'string') {
    try {
      var obj = JSON.parse(value);
      if ((0, _typeof2.default)(obj) === 'object' && obj) {
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }
  return false;
}

/**
 * 是否数组
 */
function array(value) {
  if (typeof Array.isArray === 'function') {
    return Array.isArray(value);
  }
  return Object.prototype.toString.call(value) === '[object Array]';
}

/**
 * 是否对象
 */
function object(value) {
  return Object.prototype.toString.call(value) === '[object Object]';
}

/**
 * 是否短信验证码
 */
function code(value) {
  var len = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 6;
  return new RegExp("^\\d{".concat(len, "}$")).test(value);
}

/**
 * 是否函数方法
 * @param {Object} value
 */
function func(value) {
  return typeof value === 'function';
}

/**
 * 是否promise对象
 * @param {Object} value
 */
function promise(value) {
  return object(value) && func(value.then) && func(value.catch);
}

/** 是否图片格式
 * @param {Object} value
 */
function image(value) {
  var newValue = value.split('?')[0];
  var IMAGE_REGEXP = /\.(jpeg|jpg|gif|png|svg|webp|jfif|bmp|dpg)/i;
  return IMAGE_REGEXP.test(newValue);
}

/**
 * 是否视频格式
 * @param {Object} value
 */
function video(value) {
  var VIDEO_REGEXP = /\.(mp4|mpg|mpeg|dat|asf|avi|rm|rmvb|mov|wmv|flv|mkv|m3u8)/i;
  return VIDEO_REGEXP.test(value);
}

/**
 * 是否为正则对象
 * @param {Object}
 * @return {Boolean}
 */
function regExp(o) {
  return o && Object.prototype.toString.call(o) === '[object RegExp]';
}

/***/ }),

/***/ 178:
/*!***************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-ui-tools/libs/function/digit.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
exports.divide = divide;
exports.enableBoundaryChecking = enableBoundaryChecking;
exports.minus = minus;
exports.plus = plus;
exports.round = round;
exports.times = times;
var _toArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toArray */ 179));
var _boundaryCheckingState = true; // 是否进行越界检查的全局开关

/**
 * 把错误的数据转正
 * @private
 * @example strip(0.09999999999999998)=0.1
 */
function strip(num) {
  var precision = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 15;
  return +parseFloat(Number(num).toPrecision(precision));
}

/**
 * Return digits length of a number
 * @private
 * @param {*number} num Input number
 */
function digitLength(num) {
  // Get digit length of e
  var eSplit = num.toString().split(/[eE]/);
  var len = (eSplit[0].split('.')[1] || '').length - +(eSplit[1] || 0);
  return len > 0 ? len : 0;
}

/**
 * 把小数转成整数,如果是小数则放大成整数
 * @private
 * @param {*number} num 输入数
 */
function float2Fixed(num) {
  if (num.toString().indexOf('e') === -1) {
    return Number(num.toString().replace('.', ''));
  }
  var dLen = digitLength(num);
  return dLen > 0 ? strip(Number(num) * Math.pow(10, dLen)) : Number(num);
}

/**
 * 检测数字是否越界，如果越界给出提示
 * @private
 * @param {*number} num 输入数
 */
function checkBoundary(num) {
  if (_boundaryCheckingState) {
    if (num > Number.MAX_SAFE_INTEGER || num < Number.MIN_SAFE_INTEGER) {
      console.warn("".concat(num, " \u8D85\u51FA\u4E86\u7CBE\u5EA6\u9650\u5236\uFF0C\u7ED3\u679C\u53EF\u80FD\u4E0D\u6B63\u786E"));
    }
  }
}

/**
 * 把递归操作扁平迭代化
 * @param {number[]} arr 要操作的数字数组
 * @param {function} operation 迭代操作
 * @private
 */
function iteratorOperation(arr, operation) {
  var _arr = (0, _toArray2.default)(arr),
    num1 = _arr[0],
    num2 = _arr[1],
    others = _arr.slice(2);
  var res = operation(num1, num2);
  others.forEach(function (num) {
    res = operation(res, num);
  });
  return res;
}

/**
 * 高精度乘法
 * @export
 */
function times() {
  for (var _len = arguments.length, nums = new Array(_len), _key = 0; _key < _len; _key++) {
    nums[_key] = arguments[_key];
  }
  if (nums.length > 2) {
    return iteratorOperation(nums, times);
  }
  var num1 = nums[0],
    num2 = nums[1];
  var num1Changed = float2Fixed(num1);
  var num2Changed = float2Fixed(num2);
  var baseNum = digitLength(num1) + digitLength(num2);
  var leftValue = num1Changed * num2Changed;
  checkBoundary(leftValue);
  return leftValue / Math.pow(10, baseNum);
}

/**
 * 高精度加法
 * @export
 */
function plus() {
  for (var _len2 = arguments.length, nums = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
    nums[_key2] = arguments[_key2];
  }
  if (nums.length > 2) {
    return iteratorOperation(nums, plus);
  }
  var num1 = nums[0],
    num2 = nums[1];
  // 取最大的小数位
  var baseNum = Math.pow(10, Math.max(digitLength(num1), digitLength(num2)));
  // 把小数都转为整数然后再计算
  return (times(num1, baseNum) + times(num2, baseNum)) / baseNum;
}

/**
 * 高精度减法
 * @export
 */
function minus() {
  for (var _len3 = arguments.length, nums = new Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
    nums[_key3] = arguments[_key3];
  }
  if (nums.length > 2) {
    return iteratorOperation(nums, minus);
  }
  var num1 = nums[0],
    num2 = nums[1];
  var baseNum = Math.pow(10, Math.max(digitLength(num1), digitLength(num2)));
  return (times(num1, baseNum) - times(num2, baseNum)) / baseNum;
}

/**
 * 高精度除法
 * @export
 */
function divide() {
  for (var _len4 = arguments.length, nums = new Array(_len4), _key4 = 0; _key4 < _len4; _key4++) {
    nums[_key4] = arguments[_key4];
  }
  if (nums.length > 2) {
    return iteratorOperation(nums, divide);
  }
  var num1 = nums[0],
    num2 = nums[1];
  var num1Changed = float2Fixed(num1);
  var num2Changed = float2Fixed(num2);
  checkBoundary(num1Changed);
  checkBoundary(num2Changed);
  // 重要，这里必须用strip进行修正
  return times(num1Changed / num2Changed, strip(Math.pow(10, digitLength(num2) - digitLength(num1))));
}

/**
 * 四舍五入
 * @export
 */
function round(num, ratio) {
  var base = Math.pow(10, ratio);
  var result = divide(Math.round(Math.abs(times(num, base))), base);
  if (num < 0 && result !== 0) {
    result = times(result, -1);
  }
  // 位数不足则补0
  return result;
}

/**
 * 是否进行边界检查，默认开启
 * @param flag 标记开关，true 为开启，false 为关闭，默认为 true
 * @export
 */
function enableBoundaryChecking() {
  var flag = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
  _boundaryCheckingState = flag;
}
var _default = {
  times: times,
  plus: plus,
  minus: minus,
  divide: divide,
  round: round,
  enableBoundaryChecking: enableBoundaryChecking
};
exports.default = _default;

/***/ }),

/***/ 179:
/*!********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/toArray.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles.js */ 6);
var iterableToArray = __webpack_require__(/*! ./iterableToArray.js */ 20);
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ 8);
var nonIterableRest = __webpack_require__(/*! ./nonIterableRest.js */ 10);
function _toArray(arr) {
  return arrayWithHoles(arr) || iterableToArray(arr) || unsupportedIterableToArray(arr) || nonIterableRest();
}
module.exports = _toArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 18:
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/toConsumableArray.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayWithoutHoles = __webpack_require__(/*! ./arrayWithoutHoles.js */ 19);
var iterableToArray = __webpack_require__(/*! ./iterableToArray.js */ 20);
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ 8);
var nonIterableSpread = __webpack_require__(/*! ./nonIterableSpread.js */ 21);
function _toConsumableArray(arr) {
  return arrayWithoutHoles(arr) || iterableToArray(arr) || unsupportedIterableToArray(arr) || nonIterableSpread();
}
module.exports = _toConsumableArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 180:
/*!***********************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-ui-tools/libs/util/route.js ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ 36));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ 38));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ 23));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ 24));
var _index = __webpack_require__(/*! @/uni_modules/uv-ui-tools/libs/function/index.js */ 176);
/**
 * 路由跳转方法，该方法相对于直接使用uni.xxx的好处是使用更加简单快捷
 * 并且带有路由拦截功能
 */
var Router = /*#__PURE__*/function () {
  function Router() {
    (0, _classCallCheck2.default)(this, Router);
    // 原始属性定义
    this.config = {
      type: 'navigateTo',
      url: '',
      delta: 1,
      // navigateBack页面后退时,回退的层数
      params: {},
      // 传递的参数
      animationType: 'pop-in',
      // 窗口动画,只在APP有效
      animationDuration: 300,
      // 窗口动画持续时间,单位毫秒,只在APP有效
      intercept: false,
      // 是否需要拦截
      events: {} // 页面间通信接口，用于监听被打开页面发送到当前页面的数据。hbuilderx 2.8.9+ 开始支持。
    };
    // 因为route方法是需要对外赋值给另外的对象使用，同时route内部有使用this，会导致route失去上下文
    // 这里在构造函数中进行this绑定
    this.route = this.route.bind(this);
  }

  // 判断url前面是否有"/"，如果没有则加上，否则无法跳转
  (0, _createClass2.default)(Router, [{
    key: "addRootPath",
    value: function addRootPath(url) {
      return url[0] === '/' ? url : "/".concat(url);
    }

    // 整合路由参数
  }, {
    key: "mixinParam",
    value: function mixinParam(url, params) {
      url = url && this.addRootPath(url);

      // 使用正则匹配，主要依据是判断是否有"/","?","="等，如“/page/index/index?name=mary"
      // 如果有url中有get参数，转换后无需带上"?"
      var query = '';
      if (/.*\/.*\?.*=.*/.test(url)) {
        // object对象转为get类型的参数
        query = (0, _index.queryParams)(params, false);
        // 因为已有get参数,所以后面拼接的参数需要带上"&"隔开
        return url += "&".concat(query);
      }
      // 直接拼接参数，因为此处url中没有后面的query参数，也就没有"?/&"之类的符号
      query = (0, _index.queryParams)(params);
      return url += query;
    }

    // 对外的方法名称
  }, {
    key: "route",
    value: function () {
      var _route = (0, _asyncToGenerator2.default)( /*#__PURE__*/_regenerator.default.mark(function _callee() {
        var options,
          params,
          mergeConfig,
          isNext,
          _args = arguments;
        return _regenerator.default.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                options = _args.length > 0 && _args[0] !== undefined ? _args[0] : {};
                params = _args.length > 1 && _args[1] !== undefined ? _args[1] : {};
                // 合并用户的配置和内部的默认配置
                mergeConfig = {};
                if (typeof options === 'string') {
                  // 如果options为字符串，则为route(url, params)的形式
                  mergeConfig.url = this.mixinParam(options, params);
                  mergeConfig.type = 'navigateTo';
                } else {
                  mergeConfig = (0, _index.deepMerge)(this.config, options);
                  // 否则正常使用mergeConfig中的url和params进行拼接
                  mergeConfig.url = this.mixinParam(options.url, options.params);
                }
                // 如果本次跳转的路径和本页面路径一致，不执行跳转，防止用户快速点击跳转按钮，造成多次跳转同一个页面的问题
                if (!(mergeConfig.url === (0, _index.page)())) {
                  _context.next = 6;
                  break;
                }
                return _context.abrupt("return");
              case 6:
                if (params.intercept) {
                  mergeConfig.intercept = params.intercept;
                }
                // params参数也带给拦截器
                mergeConfig.params = params;
                // 合并内外部参数
                mergeConfig = (0, _index.deepMerge)(this.config, mergeConfig);
                // 判断用户是否定义了拦截器
                if (!(typeof mergeConfig.intercept === 'function')) {
                  _context.next = 16;
                  break;
                }
                _context.next = 12;
                return new Promise(function (resolve, reject) {
                  mergeConfig.intercept(mergeConfig, resolve);
                });
              case 12:
                isNext = _context.sent;
                // 如果isNext为true，则执行路由跳转
                isNext && this.openPage(mergeConfig);
                _context.next = 17;
                break;
              case 16:
                this.openPage(mergeConfig);
              case 17:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));
      function route() {
        return _route.apply(this, arguments);
      }
      return route;
    }() // 执行路由跳转
  }, {
    key: "openPage",
    value: function openPage(config) {
      // 解构参数
      var url = config.url,
        type = config.type,
        delta = config.delta,
        animationType = config.animationType,
        animationDuration = config.animationDuration,
        events = config.events;
      if (config.type == 'navigateTo' || config.type == 'to') {
        uni.navigateTo({
          url: url,
          animationType: animationType,
          animationDuration: animationDuration,
          events: events
        });
      }
      if (config.type == 'redirectTo' || config.type == 'redirect') {
        uni.redirectTo({
          url: url
        });
      }
      if (config.type == 'switchTab' || config.type == 'tab') {
        uni.switchTab({
          url: url
        });
      }
      if (config.type == 'reLaunch' || config.type == 'launch') {
        uni.reLaunch({
          url: url
        });
      }
      if (config.type == 'navigateBack' || config.type == 'back') {
        uni.navigateBack({
          delta: delta
        });
      }
    }
  }]);
  return Router;
}();
var _default = new Router().route;
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 181:
/*!******************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-ui-tools/libs/function/debounce.js ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var timeout = null;

/**
 * 防抖原理：一定时间内，只有最后一次操作，再过wait毫秒后才执行函数
 *
 * @param {Function} func 要执行的回调函数
 * @param {Number} wait 延时的时间
 * @param {Boolean} immediate 是否立即执行
 * @return null
 */
function debounce(func) {
  var wait = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 500;
  var immediate = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  // 清除定时器
  if (timeout !== null) clearTimeout(timeout);
  // 立即执行，此类情况一般用不到
  if (immediate) {
    var callNow = !timeout;
    timeout = setTimeout(function () {
      timeout = null;
    }, wait);
    if (callNow) typeof func === 'function' && func();
  } else {
    // 设置定时器，当最后一次操作后，timeout不会再被清除，所以在延时wait毫秒后执行func回调方法
    timeout = setTimeout(function () {
      typeof func === 'function' && func();
    }, wait);
  }
}
var _default = debounce;
exports.default = _default;

/***/ }),

/***/ 182:
/*!******************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-ui-tools/libs/function/throttle.js ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var timer;
var flag;
/**
 * 节流原理：在一定时间内，只能触发一次
 *
 * @param {Function} func 要执行的回调函数
 * @param {Number} wait 延时的时间
 * @param {Boolean} immediate 是否立即执行
 * @return null
 */
function throttle(func) {
  var wait = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 500;
  var immediate = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
  if (immediate) {
    if (!flag) {
      flag = true;
      // 如果是立即执行，则在wait毫秒内开始时执行
      typeof func === 'function' && func();
      timer = setTimeout(function () {
        flag = false;
      }, wait);
    }
  } else if (!flag) {
    flag = true;
    // 如果是非立即执行，则在wait毫秒内的结束处执行
    timer = setTimeout(function () {
      flag = false;
      typeof func === 'function' && func();
    }, wait);
  }
}
var _default = throttle;
exports.default = _default;

/***/ }),

/***/ 183:
/*!********************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-navbar/components/uv-navbar/props.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default = {
  props: _objectSpread({
    // 是否开启顶部安全区适配
    safeAreaInsetTop: {
      type: Boolean,
      default: true
    },
    // 固定在顶部时，是否生成一个等高元素，以防止塌陷
    placeholder: {
      type: Boolean,
      default: false
    },
    // 是否固定在顶部
    fixed: {
      type: Boolean,
      default: true
    },
    // 是否显示下边框
    border: {
      type: Boolean,
      default: false
    },
    // 左边的图标
    leftIcon: {
      type: String,
      default: 'arrow-left'
    },
    // 左边的提示文字
    leftText: {
      type: String,
      default: ''
    },
    // 左右的提示文字
    rightText: {
      type: String,
      default: ''
    },
    // 右边的图标
    rightIcon: {
      type: String,
      default: ''
    },
    // 标题
    title: {
      type: [String, Number],
      default: ''
    },
    // 背景颜色
    bgColor: {
      type: String,
      default: '#ffffff'
    },
    imgMode: {
      type: String,
      default: 'aspectFill'
    },
    // 标题的宽度
    titleWidth: {
      type: [String, Number],
      default: '400rpx'
    },
    // 导航栏高度
    height: {
      type: [String, Number],
      default: '44px'
    },
    // 左侧返回图标的大小
    leftIconSize: {
      type: [String, Number],
      default: 20
    },
    // 左侧返回图标的颜色
    leftIconColor: {
      type: String,
      default: '#303133'
    },
    // 点击左侧区域(返回图标)，是否自动返回上一页
    autoBack: {
      type: Boolean,
      default: false
    },
    // 标题的样式，对象或字符串
    titleStyle: {
      type: [String, Object],
      default: ''
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.navbar)
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 19:
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ 9);
function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return arrayLikeToArray(arr);
}
module.exports = _arrayWithoutHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 191:
/*!********************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-picker/components/uv-picker/props.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default2 = {
  props: _objectSpread({
    // 是否展示顶部的操作栏
    showToolbar: {
      type: Boolean,
      default: true
    },
    // 顶部标题
    title: {
      type: String,
      default: ''
    },
    // 弹窗圆角
    round: {
      type: [String, Number],
      default: 0
    },
    // 对象数组，设置每一列的数据
    columns: {
      type: Array,
      default: function _default() {
        return [];
      }
    },
    // 是否显示加载中状态
    loading: {
      type: Boolean,
      default: false
    },
    // 各列中，单个选项的高度
    itemHeight: {
      type: [String, Number],
      default: 44
    },
    // 取消按钮的文字
    cancelText: {
      type: String,
      default: '取消'
    },
    // 确认按钮的文字
    confirmText: {
      type: String,
      default: '确定'
    },
    // 取消按钮的颜色
    cancelColor: {
      type: String,
      default: '#909193'
    },
    // 确认按钮的颜色
    confirmColor: {
      type: String,
      default: '#3c9cff'
    },
    // 文字颜色
    color: {
      type: String,
      default: ''
    },
    // 选中文字的颜色
    activeColor: {
      type: String,
      default: ''
    },
    // 每列中可见选项的数量
    visibleItemCount: {
      type: [String, Number],
      default: 5
    },
    // 选项对象中，需要展示的属性键名
    keyName: {
      type: String,
      default: 'text'
    },
    // 是否允许点击遮罩关闭选择器
    closeOnClickOverlay: {
      type: Boolean,
      default: true
    },
    // 是否允许点击确认关闭选择器
    closeOnClickConfirm: {
      type: Boolean,
      default: true
    },
    // 各列的默认索引
    defaultIndex: {
      type: Array,
      default: function _default() {
        return [];
      }
    },
    // 是否在手指松开时立即触发 change 事件。若不开启则会在滚动动画结束后触发 change 事件，只在微信2.21.1及以上有效
    immediateChange: {
      type: Boolean,
      default: true
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.picker)
};
exports.default = _default2;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 199:
/*!****************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-icon/components/uv-icon/icons.js ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _default = {
  'uvicon-level': 'e68f',
  'uvicon-checkbox-mark': 'e659',
  'uvicon-folder': 'e694',
  'uvicon-movie': 'e67c',
  'uvicon-star-fill': 'e61e',
  'uvicon-star': 'e618',
  'uvicon-phone-fill': 'e6ac',
  'uvicon-phone': 'e6ba',
  'uvicon-apple-fill': 'e635',
  'uvicon-backspace': 'e64d',
  'uvicon-attach': 'e640',
  'uvicon-empty-data': 'e671',
  'uvicon-empty-address': 'e68a',
  'uvicon-empty-favor': 'e662',
  'uvicon-empty-car': 'e657',
  'uvicon-empty-order': 'e66b',
  'uvicon-empty-list': 'e672',
  'uvicon-empty-search': 'e677',
  'uvicon-empty-permission': 'e67d',
  'uvicon-empty-news': 'e67e',
  'uvicon-empty-history': 'e685',
  'uvicon-empty-coupon': 'e69b',
  'uvicon-empty-page': 'e60e',
  'uvicon-empty-wifi-off': 'e6cc',
  'uvicon-reload': 'e627',
  'uvicon-order': 'e695',
  'uvicon-server-man': 'e601',
  'uvicon-search': 'e632',
  'uvicon-more-dot-fill': 'e66f',
  'uvicon-scan': 'e631',
  'uvicon-map': 'e665',
  'uvicon-map-fill': 'e6a8',
  'uvicon-tags': 'e621',
  'uvicon-tags-fill': 'e613',
  'uvicon-eye': 'e664',
  'uvicon-eye-fill': 'e697',
  'uvicon-eye-off': 'e69c',
  'uvicon-eye-off-outline': 'e688',
  'uvicon-mic': 'e66d',
  'uvicon-mic-off': 'e691',
  'uvicon-calendar': 'e65c',
  'uvicon-trash': 'e623',
  'uvicon-trash-fill': 'e6ce',
  'uvicon-play-left': 'e6bf',
  'uvicon-play-right': 'e6b3',
  'uvicon-minus': 'e614',
  'uvicon-plus': 'e625',
  'uvicon-info-circle': 'e69f',
  'uvicon-info-circle-fill': 'e6a7',
  'uvicon-question-circle': 'e622',
  'uvicon-question-circle-fill': 'e6bc',
  'uvicon-close': 'e65a',
  'uvicon-checkmark': 'e64a',
  'uvicon-checkmark-circle': 'e643',
  'uvicon-checkmark-circle-fill': 'e668',
  'uvicon-setting': 'e602',
  'uvicon-setting-fill': 'e6d0',
  'uvicon-heart': 'e6a2',
  'uvicon-heart-fill': 'e68b',
  'uvicon-camera': 'e642',
  'uvicon-camera-fill': 'e650',
  'uvicon-more-circle': 'e69e',
  'uvicon-more-circle-fill': 'e684',
  'uvicon-chat': 'e656',
  'uvicon-chat-fill': 'e63f',
  'uvicon-bag': 'e647',
  'uvicon-error-circle': 'e66e',
  'uvicon-error-circle-fill': 'e655',
  'uvicon-close-circle': 'e64e',
  'uvicon-close-circle-fill': 'e666',
  'uvicon-share': 'e629',
  'uvicon-share-fill': 'e6bb',
  'uvicon-share-square': 'e6c4',
  'uvicon-shopping-cart': 'e6cb',
  'uvicon-shopping-cart-fill': 'e630',
  'uvicon-bell': 'e651',
  'uvicon-bell-fill': 'e604',
  'uvicon-list': 'e690',
  'uvicon-list-dot': 'e6a9',
  'uvicon-zhifubao-circle-fill': 'e617',
  'uvicon-weixin-circle-fill': 'e6cd',
  'uvicon-weixin-fill': 'e620',
  'uvicon-qq-fill': 'e608',
  'uvicon-qq-circle-fill': 'e6b9',
  'uvicon-moments-circel-fill': 'e6c2',
  'uvicon-moments': 'e6a0',
  'uvicon-car': 'e64f',
  'uvicon-car-fill': 'e648',
  'uvicon-warning-fill': 'e6c7',
  'uvicon-warning': 'e6c1',
  'uvicon-clock-fill': 'e64b',
  'uvicon-clock': 'e66c',
  'uvicon-edit-pen': 'e65d',
  'uvicon-edit-pen-fill': 'e679',
  'uvicon-email': 'e673',
  'uvicon-email-fill': 'e683',
  'uvicon-minus-circle': 'e6a5',
  'uvicon-plus-circle': 'e603',
  'uvicon-plus-circle-fill': 'e611',
  'uvicon-file-text': 'e687',
  'uvicon-file-text-fill': 'e67f',
  'uvicon-pushpin': 'e6d1',
  'uvicon-pushpin-fill': 'e6b6',
  'uvicon-grid': 'e68c',
  'uvicon-grid-fill': 'e698',
  'uvicon-play-circle': 'e6af',
  'uvicon-play-circle-fill': 'e62a',
  'uvicon-pause-circle-fill': 'e60c',
  'uvicon-pause': 'e61c',
  'uvicon-pause-circle': 'e696',
  'uvicon-gift-fill': 'e6b0',
  'uvicon-gift': 'e680',
  'uvicon-kefu-ermai': 'e660',
  'uvicon-server-fill': 'e610',
  'uvicon-coupon-fill': 'e64c',
  'uvicon-coupon': 'e65f',
  'uvicon-integral': 'e693',
  'uvicon-integral-fill': 'e6b1',
  'uvicon-home-fill': 'e68e',
  'uvicon-home': 'e67b',
  'uvicon-account': 'e63a',
  'uvicon-account-fill': 'e653',
  'uvicon-thumb-down-fill': 'e628',
  'uvicon-thumb-down': 'e60a',
  'uvicon-thumb-up': 'e612',
  'uvicon-thumb-up-fill': 'e62c',
  'uvicon-lock-fill': 'e6a6',
  'uvicon-lock-open': 'e68d',
  'uvicon-lock-opened-fill': 'e6a1',
  'uvicon-lock': 'e69d',
  'uvicon-red-packet': 'e6c3',
  'uvicon-photo-fill': 'e6b4',
  'uvicon-photo': 'e60d',
  'uvicon-volume-off-fill': 'e6c8',
  'uvicon-volume-off': 'e6bd',
  'uvicon-volume-fill': 'e624',
  'uvicon-volume': 'e605',
  'uvicon-download': 'e670',
  'uvicon-arrow-up-fill': 'e636',
  'uvicon-arrow-down-fill': 'e638',
  'uvicon-play-left-fill': 'e6ae',
  'uvicon-play-right-fill': 'e6ad',
  'uvicon-arrow-downward': 'e634',
  'uvicon-arrow-leftward': 'e63b',
  'uvicon-arrow-rightward': 'e644',
  'uvicon-arrow-upward': 'e641',
  'uvicon-arrow-down': 'e63e',
  'uvicon-arrow-right': 'e63c',
  'uvicon-arrow-left': 'e646',
  'uvicon-arrow-up': 'e633',
  'uvicon-skip-back-left': 'e6c5',
  'uvicon-skip-forward-right': 'e61f',
  'uvicon-arrow-left-double': 'e637',
  'uvicon-man': 'e675',
  'uvicon-woman': 'e626',
  'uvicon-en': 'e6b8',
  'uvicon-twitte': 'e607',
  'uvicon-twitter-circle-fill': 'e6cf'
};
exports.default = _default;

/***/ }),

/***/ 2:
/*!************************************************************!*\
  !*** ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(wx, global) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.createApp = createApp;
exports.createComponent = createComponent;
exports.createPage = createPage;
exports.createPlugin = createPlugin;
exports.createSubpackageApp = createSubpackageApp;
exports.default = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ 5));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _construct2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/construct */ 15));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ 18));
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ 13));
var _uniI18n = __webpack_require__(/*! @dcloudio/uni-i18n */ 22);
var _vue = _interopRequireDefault(__webpack_require__(/*! vue */ 25));
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var realAtob;
var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
var b64re = /^(?:[A-Za-z\d+/]{4})*?(?:[A-Za-z\d+/]{2}(?:==)?|[A-Za-z\d+/]{3}=?)?$/;
if (typeof atob !== 'function') {
  realAtob = function realAtob(str) {
    str = String(str).replace(/[\t\n\f\r ]+/g, '');
    if (!b64re.test(str)) {
      throw new Error("Failed to execute 'atob' on 'Window': The string to be decoded is not correctly encoded.");
    }

    // Adding the padding if missing, for semplicity
    str += '=='.slice(2 - (str.length & 3));
    var bitmap;
    var result = '';
    var r1;
    var r2;
    var i = 0;
    for (; i < str.length;) {
      bitmap = b64.indexOf(str.charAt(i++)) << 18 | b64.indexOf(str.charAt(i++)) << 12 | (r1 = b64.indexOf(str.charAt(i++))) << 6 | (r2 = b64.indexOf(str.charAt(i++)));
      result += r1 === 64 ? String.fromCharCode(bitmap >> 16 & 255) : r2 === 64 ? String.fromCharCode(bitmap >> 16 & 255, bitmap >> 8 & 255) : String.fromCharCode(bitmap >> 16 & 255, bitmap >> 8 & 255, bitmap & 255);
    }
    return result;
  };
} else {
  // 注意atob只能在全局对象上调用，例如：`const Base64 = {atob};Base64.atob('xxxx')`是错误的用法
  realAtob = atob;
}
function b64DecodeUnicode(str) {
  return decodeURIComponent(realAtob(str).split('').map(function (c) {
    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
  }).join(''));
}
function getCurrentUserInfo() {
  var token = wx.getStorageSync('uni_id_token') || '';
  var tokenArr = token.split('.');
  if (!token || tokenArr.length !== 3) {
    return {
      uid: null,
      role: [],
      permission: [],
      tokenExpired: 0
    };
  }
  var userInfo;
  try {
    userInfo = JSON.parse(b64DecodeUnicode(tokenArr[1]));
  } catch (error) {
    throw new Error('获取当前用户信息出错，详细错误信息为：' + error.message);
  }
  userInfo.tokenExpired = userInfo.exp * 1000;
  delete userInfo.exp;
  delete userInfo.iat;
  return userInfo;
}
function uniIdMixin(Vue) {
  Vue.prototype.uniIDHasRole = function (roleId) {
    var _getCurrentUserInfo = getCurrentUserInfo(),
      role = _getCurrentUserInfo.role;
    return role.indexOf(roleId) > -1;
  };
  Vue.prototype.uniIDHasPermission = function (permissionId) {
    var _getCurrentUserInfo2 = getCurrentUserInfo(),
      permission = _getCurrentUserInfo2.permission;
    return this.uniIDHasRole('admin') || permission.indexOf(permissionId) > -1;
  };
  Vue.prototype.uniIDTokenValid = function () {
    var _getCurrentUserInfo3 = getCurrentUserInfo(),
      tokenExpired = _getCurrentUserInfo3.tokenExpired;
    return tokenExpired > Date.now();
  };
}
var _toString = Object.prototype.toString;
var hasOwnProperty = Object.prototype.hasOwnProperty;
function isFn(fn) {
  return typeof fn === 'function';
}
function isStr(str) {
  return typeof str === 'string';
}
function isObject(obj) {
  return obj !== null && (0, _typeof2.default)(obj) === 'object';
}
function isPlainObject(obj) {
  return _toString.call(obj) === '[object Object]';
}
function hasOwn(obj, key) {
  return hasOwnProperty.call(obj, key);
}
function noop() {}

/**
 * Create a cached version of a pure function.
 */
function cached(fn) {
  var cache = Object.create(null);
  return function cachedFn(str) {
    var hit = cache[str];
    return hit || (cache[str] = fn(str));
  };
}

/**
 * Camelize a hyphen-delimited string.
 */
var camelizeRE = /-(\w)/g;
var camelize = cached(function (str) {
  return str.replace(camelizeRE, function (_, c) {
    return c ? c.toUpperCase() : '';
  });
});
function sortObject(obj) {
  var sortObj = {};
  if (isPlainObject(obj)) {
    Object.keys(obj).sort().forEach(function (key) {
      sortObj[key] = obj[key];
    });
  }
  return !Object.keys(sortObj) ? obj : sortObj;
}
var HOOKS = ['invoke', 'success', 'fail', 'complete', 'returnValue'];
var globalInterceptors = {};
var scopedInterceptors = {};
function mergeHook(parentVal, childVal) {
  var res = childVal ? parentVal ? parentVal.concat(childVal) : Array.isArray(childVal) ? childVal : [childVal] : parentVal;
  return res ? dedupeHooks(res) : res;
}
function dedupeHooks(hooks) {
  var res = [];
  for (var i = 0; i < hooks.length; i++) {
    if (res.indexOf(hooks[i]) === -1) {
      res.push(hooks[i]);
    }
  }
  return res;
}
function removeHook(hooks, hook) {
  var index = hooks.indexOf(hook);
  if (index !== -1) {
    hooks.splice(index, 1);
  }
}
function mergeInterceptorHook(interceptor, option) {
  Object.keys(option).forEach(function (hook) {
    if (HOOKS.indexOf(hook) !== -1 && isFn(option[hook])) {
      interceptor[hook] = mergeHook(interceptor[hook], option[hook]);
    }
  });
}
function removeInterceptorHook(interceptor, option) {
  if (!interceptor || !option) {
    return;
  }
  Object.keys(option).forEach(function (hook) {
    if (HOOKS.indexOf(hook) !== -1 && isFn(option[hook])) {
      removeHook(interceptor[hook], option[hook]);
    }
  });
}
function addInterceptor(method, option) {
  if (typeof method === 'string' && isPlainObject(option)) {
    mergeInterceptorHook(scopedInterceptors[method] || (scopedInterceptors[method] = {}), option);
  } else if (isPlainObject(method)) {
    mergeInterceptorHook(globalInterceptors, method);
  }
}
function removeInterceptor(method, option) {
  if (typeof method === 'string') {
    if (isPlainObject(option)) {
      removeInterceptorHook(scopedInterceptors[method], option);
    } else {
      delete scopedInterceptors[method];
    }
  } else if (isPlainObject(method)) {
    removeInterceptorHook(globalInterceptors, method);
  }
}
function wrapperHook(hook, params) {
  return function (data) {
    return hook(data, params) || data;
  };
}
function isPromise(obj) {
  return !!obj && ((0, _typeof2.default)(obj) === 'object' || typeof obj === 'function') && typeof obj.then === 'function';
}
function queue(hooks, data, params) {
  var promise = false;
  for (var i = 0; i < hooks.length; i++) {
    var hook = hooks[i];
    if (promise) {
      promise = Promise.resolve(wrapperHook(hook, params));
    } else {
      var res = hook(data, params);
      if (isPromise(res)) {
        promise = Promise.resolve(res);
      }
      if (res === false) {
        return {
          then: function then() {}
        };
      }
    }
  }
  return promise || {
    then: function then(callback) {
      return callback(data);
    }
  };
}
function wrapperOptions(interceptor) {
  var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  ['success', 'fail', 'complete'].forEach(function (name) {
    if (Array.isArray(interceptor[name])) {
      var oldCallback = options[name];
      options[name] = function callbackInterceptor(res) {
        queue(interceptor[name], res, options).then(function (res) {
          /* eslint-disable no-mixed-operators */
          return isFn(oldCallback) && oldCallback(res) || res;
        });
      };
    }
  });
  return options;
}
function wrapperReturnValue(method, returnValue) {
  var returnValueHooks = [];
  if (Array.isArray(globalInterceptors.returnValue)) {
    returnValueHooks.push.apply(returnValueHooks, (0, _toConsumableArray2.default)(globalInterceptors.returnValue));
  }
  var interceptor = scopedInterceptors[method];
  if (interceptor && Array.isArray(interceptor.returnValue)) {
    returnValueHooks.push.apply(returnValueHooks, (0, _toConsumableArray2.default)(interceptor.returnValue));
  }
  returnValueHooks.forEach(function (hook) {
    returnValue = hook(returnValue) || returnValue;
  });
  return returnValue;
}
function getApiInterceptorHooks(method) {
  var interceptor = Object.create(null);
  Object.keys(globalInterceptors).forEach(function (hook) {
    if (hook !== 'returnValue') {
      interceptor[hook] = globalInterceptors[hook].slice();
    }
  });
  var scopedInterceptor = scopedInterceptors[method];
  if (scopedInterceptor) {
    Object.keys(scopedInterceptor).forEach(function (hook) {
      if (hook !== 'returnValue') {
        interceptor[hook] = (interceptor[hook] || []).concat(scopedInterceptor[hook]);
      }
    });
  }
  return interceptor;
}
function invokeApi(method, api, options) {
  for (var _len = arguments.length, params = new Array(_len > 3 ? _len - 3 : 0), _key = 3; _key < _len; _key++) {
    params[_key - 3] = arguments[_key];
  }
  var interceptor = getApiInterceptorHooks(method);
  if (interceptor && Object.keys(interceptor).length) {
    if (Array.isArray(interceptor.invoke)) {
      var res = queue(interceptor.invoke, options);
      return res.then(function (options) {
        // 重新访问 getApiInterceptorHooks, 允许 invoke 中再次调用 addInterceptor,removeInterceptor
        return api.apply(void 0, [wrapperOptions(getApiInterceptorHooks(method), options)].concat(params));
      });
    } else {
      return api.apply(void 0, [wrapperOptions(interceptor, options)].concat(params));
    }
  }
  return api.apply(void 0, [options].concat(params));
}
var promiseInterceptor = {
  returnValue: function returnValue(res) {
    if (!isPromise(res)) {
      return res;
    }
    return new Promise(function (resolve, reject) {
      res.then(function (res) {
        if (res[0]) {
          reject(res[0]);
        } else {
          resolve(res[1]);
        }
      });
    });
  }
};
var SYNC_API_RE = /^\$|Window$|WindowStyle$|sendHostEvent|sendNativeEvent|restoreGlobal|requireGlobal|getCurrentSubNVue|getMenuButtonBoundingClientRect|^report|interceptors|Interceptor$|getSubNVueById|requireNativePlugin|upx2px|hideKeyboard|canIUse|^create|Sync$|Manager$|base64ToArrayBuffer|arrayBufferToBase64|getLocale|setLocale|invokePushCallback|getWindowInfo|getDeviceInfo|getAppBaseInfo|getSystemSetting|getAppAuthorizeSetting|initUTS|requireUTS|registerUTS/;
var CONTEXT_API_RE = /^create|Manager$/;

// Context例外情况
var CONTEXT_API_RE_EXC = ['createBLEConnection'];

// 同步例外情况
var ASYNC_API = ['createBLEConnection', 'createPushMessage'];
var CALLBACK_API_RE = /^on|^off/;
function isContextApi(name) {
  return CONTEXT_API_RE.test(name) && CONTEXT_API_RE_EXC.indexOf(name) === -1;
}
function isSyncApi(name) {
  return SYNC_API_RE.test(name) && ASYNC_API.indexOf(name) === -1;
}
function isCallbackApi(name) {
  return CALLBACK_API_RE.test(name) && name !== 'onPush';
}
function handlePromise(promise) {
  return promise.then(function (data) {
    return [null, data];
  }).catch(function (err) {
    return [err];
  });
}
function shouldPromise(name) {
  if (isContextApi(name) || isSyncApi(name) || isCallbackApi(name)) {
    return false;
  }
  return true;
}

/* eslint-disable no-extend-native */
if (!Promise.prototype.finally) {
  Promise.prototype.finally = function (callback) {
    var promise = this.constructor;
    return this.then(function (value) {
      return promise.resolve(callback()).then(function () {
        return value;
      });
    }, function (reason) {
      return promise.resolve(callback()).then(function () {
        throw reason;
      });
    });
  };
}
function promisify(name, api) {
  if (!shouldPromise(name) || !isFn(api)) {
    return api;
  }
  return function promiseApi() {
    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    for (var _len2 = arguments.length, params = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
      params[_key2 - 1] = arguments[_key2];
    }
    if (isFn(options.success) || isFn(options.fail) || isFn(options.complete)) {
      return wrapperReturnValue(name, invokeApi.apply(void 0, [name, api, options].concat(params)));
    }
    return wrapperReturnValue(name, handlePromise(new Promise(function (resolve, reject) {
      invokeApi.apply(void 0, [name, api, Object.assign({}, options, {
        success: resolve,
        fail: reject
      })].concat(params));
    })));
  };
}
var EPS = 1e-4;
var BASE_DEVICE_WIDTH = 750;
var isIOS = false;
var deviceWidth = 0;
var deviceDPR = 0;
function checkDeviceWidth() {
  var _wx$getSystemInfoSync = wx.getSystemInfoSync(),
    platform = _wx$getSystemInfoSync.platform,
    pixelRatio = _wx$getSystemInfoSync.pixelRatio,
    windowWidth = _wx$getSystemInfoSync.windowWidth; // uni=>wx runtime 编译目标是 uni 对象，内部不允许直接使用 uni

  deviceWidth = windowWidth;
  deviceDPR = pixelRatio;
  isIOS = platform === 'ios';
}
function upx2px(number, newDeviceWidth) {
  if (deviceWidth === 0) {
    checkDeviceWidth();
  }
  number = Number(number);
  if (number === 0) {
    return 0;
  }
  var result = number / BASE_DEVICE_WIDTH * (newDeviceWidth || deviceWidth);
  if (result < 0) {
    result = -result;
  }
  result = Math.floor(result + EPS);
  if (result === 0) {
    if (deviceDPR === 1 || !isIOS) {
      result = 1;
    } else {
      result = 0.5;
    }
  }
  return number < 0 ? -result : result;
}
var LOCALE_ZH_HANS = 'zh-Hans';
var LOCALE_ZH_HANT = 'zh-Hant';
var LOCALE_EN = 'en';
var LOCALE_FR = 'fr';
var LOCALE_ES = 'es';
var messages = {};
var locale;
{
  locale = normalizeLocale(wx.getSystemInfoSync().language) || LOCALE_EN;
}
function initI18nMessages() {
  if (!isEnableLocale()) {
    return;
  }
  var localeKeys = Object.keys(__uniConfig.locales);
  if (localeKeys.length) {
    localeKeys.forEach(function (locale) {
      var curMessages = messages[locale];
      var userMessages = __uniConfig.locales[locale];
      if (curMessages) {
        Object.assign(curMessages, userMessages);
      } else {
        messages[locale] = userMessages;
      }
    });
  }
}
initI18nMessages();
var i18n = (0, _uniI18n.initVueI18n)(locale, {});
var t = i18n.t;
var i18nMixin = i18n.mixin = {
  beforeCreate: function beforeCreate() {
    var _this = this;
    var unwatch = i18n.i18n.watchLocale(function () {
      _this.$forceUpdate();
    });
    this.$once('hook:beforeDestroy', function () {
      unwatch();
    });
  },
  methods: {
    $$t: function $$t(key, values) {
      return t(key, values);
    }
  }
};
var setLocale = i18n.setLocale;
var getLocale = i18n.getLocale;
function initAppLocale(Vue, appVm, locale) {
  var state = Vue.observable({
    locale: locale || i18n.getLocale()
  });
  var localeWatchers = [];
  appVm.$watchLocale = function (fn) {
    localeWatchers.push(fn);
  };
  Object.defineProperty(appVm, '$locale', {
    get: function get() {
      return state.locale;
    },
    set: function set(v) {
      state.locale = v;
      localeWatchers.forEach(function (watch) {
        return watch(v);
      });
    }
  });
}
function isEnableLocale() {
  return typeof __uniConfig !== 'undefined' && __uniConfig.locales && !!Object.keys(__uniConfig.locales).length;
}
function include(str, parts) {
  return !!parts.find(function (part) {
    return str.indexOf(part) !== -1;
  });
}
function startsWith(str, parts) {
  return parts.find(function (part) {
    return str.indexOf(part) === 0;
  });
}
function normalizeLocale(locale, messages) {
  if (!locale) {
    return;
  }
  locale = locale.trim().replace(/_/g, '-');
  if (messages && messages[locale]) {
    return locale;
  }
  locale = locale.toLowerCase();
  if (locale === 'chinese') {
    // 支付宝
    return LOCALE_ZH_HANS;
  }
  if (locale.indexOf('zh') === 0) {
    if (locale.indexOf('-hans') > -1) {
      return LOCALE_ZH_HANS;
    }
    if (locale.indexOf('-hant') > -1) {
      return LOCALE_ZH_HANT;
    }
    if (include(locale, ['-tw', '-hk', '-mo', '-cht'])) {
      return LOCALE_ZH_HANT;
    }
    return LOCALE_ZH_HANS;
  }
  var lang = startsWith(locale, [LOCALE_EN, LOCALE_FR, LOCALE_ES]);
  if (lang) {
    return lang;
  }
}
// export function initI18n() {
//   const localeKeys = Object.keys(__uniConfig.locales || {})
//   if (localeKeys.length) {
//     localeKeys.forEach((locale) =>
//       i18n.add(locale, __uniConfig.locales[locale])
//     )
//   }
// }

function getLocale$1() {
  // 优先使用 $locale
  if (isFn(getApp)) {
    var app = getApp({
      allowDefault: true
    });
    if (app && app.$vm) {
      return app.$vm.$locale;
    }
  }
  return normalizeLocale(wx.getSystemInfoSync().language) || LOCALE_EN;
}
function setLocale$1(locale) {
  var app = isFn(getApp) ? getApp() : false;
  if (!app) {
    return false;
  }
  var oldLocale = app.$vm.$locale;
  if (oldLocale !== locale) {
    app.$vm.$locale = locale;
    onLocaleChangeCallbacks.forEach(function (fn) {
      return fn({
        locale: locale
      });
    });
    return true;
  }
  return false;
}
var onLocaleChangeCallbacks = [];
function onLocaleChange(fn) {
  if (onLocaleChangeCallbacks.indexOf(fn) === -1) {
    onLocaleChangeCallbacks.push(fn);
  }
}
if (typeof global !== 'undefined') {
  global.getLocale = getLocale$1;
}
var interceptors = {
  promiseInterceptor: promiseInterceptor
};
var baseApi = /*#__PURE__*/Object.freeze({
  __proto__: null,
  upx2px: upx2px,
  getLocale: getLocale$1,
  setLocale: setLocale$1,
  onLocaleChange: onLocaleChange,
  addInterceptor: addInterceptor,
  removeInterceptor: removeInterceptor,
  interceptors: interceptors
});
function findExistsPageIndex(url) {
  var pages = getCurrentPages();
  var len = pages.length;
  while (len--) {
    var page = pages[len];
    if (page.$page && page.$page.fullPath === url) {
      return len;
    }
  }
  return -1;
}
var redirectTo = {
  name: function name(fromArgs) {
    if (fromArgs.exists === 'back' && fromArgs.delta) {
      return 'navigateBack';
    }
    return 'redirectTo';
  },
  args: function args(fromArgs) {
    if (fromArgs.exists === 'back' && fromArgs.url) {
      var existsPageIndex = findExistsPageIndex(fromArgs.url);
      if (existsPageIndex !== -1) {
        var delta = getCurrentPages().length - 1 - existsPageIndex;
        if (delta > 0) {
          fromArgs.delta = delta;
        }
      }
    }
  }
};
var previewImage = {
  args: function args(fromArgs) {
    var currentIndex = parseInt(fromArgs.current);
    if (isNaN(currentIndex)) {
      return;
    }
    var urls = fromArgs.urls;
    if (!Array.isArray(urls)) {
      return;
    }
    var len = urls.length;
    if (!len) {
      return;
    }
    if (currentIndex < 0) {
      currentIndex = 0;
    } else if (currentIndex >= len) {
      currentIndex = len - 1;
    }
    if (currentIndex > 0) {
      fromArgs.current = urls[currentIndex];
      fromArgs.urls = urls.filter(function (item, index) {
        return index < currentIndex ? item !== urls[currentIndex] : true;
      });
    } else {
      fromArgs.current = urls[0];
    }
    return {
      indicator: false,
      loop: false
    };
  }
};
var UUID_KEY = '__DC_STAT_UUID';
var deviceId;
function useDeviceId(result) {
  deviceId = deviceId || wx.getStorageSync(UUID_KEY);
  if (!deviceId) {
    deviceId = Date.now() + '' + Math.floor(Math.random() * 1e7);
    wx.setStorage({
      key: UUID_KEY,
      data: deviceId
    });
  }
  result.deviceId = deviceId;
}
function addSafeAreaInsets(result) {
  if (result.safeArea) {
    var safeArea = result.safeArea;
    result.safeAreaInsets = {
      top: safeArea.top,
      left: safeArea.left,
      right: result.windowWidth - safeArea.right,
      bottom: result.screenHeight - safeArea.bottom
    };
  }
}
function populateParameters(result) {
  var _result$brand = result.brand,
    brand = _result$brand === void 0 ? '' : _result$brand,
    _result$model = result.model,
    model = _result$model === void 0 ? '' : _result$model,
    _result$system = result.system,
    system = _result$system === void 0 ? '' : _result$system,
    _result$language = result.language,
    language = _result$language === void 0 ? '' : _result$language,
    theme = result.theme,
    version = result.version,
    platform = result.platform,
    fontSizeSetting = result.fontSizeSetting,
    SDKVersion = result.SDKVersion,
    pixelRatio = result.pixelRatio,
    deviceOrientation = result.deviceOrientation;
  // const isQuickApp = "mp-weixin".indexOf('quickapp-webview') !== -1

  var extraParam = {};

  // osName osVersion
  var osName = '';
  var osVersion = '';
  {
    osName = system.split(' ')[0] || '';
    osVersion = system.split(' ')[1] || '';
  }
  var hostVersion = version;

  // deviceType
  var deviceType = getGetDeviceType(result, model);

  // deviceModel
  var deviceBrand = getDeviceBrand(brand);

  // hostName
  var _hostName = getHostName(result);

  // deviceOrientation
  var _deviceOrientation = deviceOrientation; // 仅 微信 百度 支持

  // devicePixelRatio
  var _devicePixelRatio = pixelRatio;

  // SDKVersion
  var _SDKVersion = SDKVersion;

  // hostLanguage
  var hostLanguage = language.replace(/_/g, '-');

  // wx.getAccountInfoSync

  var parameters = {
    appId: "__UNI__D4A2604",
    appName: "shopBIZ",
    appVersion: "1.0.0",
    appVersionCode: "100",
    appLanguage: getAppLanguage(hostLanguage),
    uniCompileVersion: "4.15",
    uniRuntimeVersion: "4.15",
    uniPlatform: undefined || "mp-weixin",
    deviceBrand: deviceBrand,
    deviceModel: model,
    deviceType: deviceType,
    devicePixelRatio: _devicePixelRatio,
    deviceOrientation: _deviceOrientation,
    osName: osName.toLocaleLowerCase(),
    osVersion: osVersion,
    hostTheme: theme,
    hostVersion: hostVersion,
    hostLanguage: hostLanguage,
    hostName: _hostName,
    hostSDKVersion: _SDKVersion,
    hostFontSizeSetting: fontSizeSetting,
    windowTop: 0,
    windowBottom: 0,
    // TODO
    osLanguage: undefined,
    osTheme: undefined,
    ua: undefined,
    hostPackageName: undefined,
    browserName: undefined,
    browserVersion: undefined
  };
  Object.assign(result, parameters, extraParam);
}
function getGetDeviceType(result, model) {
  var deviceType = result.deviceType || 'phone';
  {
    var deviceTypeMaps = {
      ipad: 'pad',
      windows: 'pc',
      mac: 'pc'
    };
    var deviceTypeMapsKeys = Object.keys(deviceTypeMaps);
    var _model = model.toLocaleLowerCase();
    for (var index = 0; index < deviceTypeMapsKeys.length; index++) {
      var _m = deviceTypeMapsKeys[index];
      if (_model.indexOf(_m) !== -1) {
        deviceType = deviceTypeMaps[_m];
        break;
      }
    }
  }
  return deviceType;
}
function getDeviceBrand(brand) {
  var deviceBrand = brand;
  if (deviceBrand) {
    deviceBrand = brand.toLocaleLowerCase();
  }
  return deviceBrand;
}
function getAppLanguage(defaultLanguage) {
  return getLocale$1 ? getLocale$1() : defaultLanguage;
}
function getHostName(result) {
  var _platform = 'WeChat';
  var _hostName = result.hostName || _platform; // mp-jd
  {
    if (result.environment) {
      _hostName = result.environment;
    } else if (result.host && result.host.env) {
      _hostName = result.host.env;
    }
  }
  return _hostName;
}
var getSystemInfo = {
  returnValue: function returnValue(result) {
    useDeviceId(result);
    addSafeAreaInsets(result);
    populateParameters(result);
  }
};
var showActionSheet = {
  args: function args(fromArgs) {
    if ((0, _typeof2.default)(fromArgs) === 'object') {
      fromArgs.alertText = fromArgs.title;
    }
  }
};
var getAppBaseInfo = {
  returnValue: function returnValue(result) {
    var _result = result,
      version = _result.version,
      language = _result.language,
      SDKVersion = _result.SDKVersion,
      theme = _result.theme;
    var _hostName = getHostName(result);
    var hostLanguage = language.replace('_', '-');
    result = sortObject(Object.assign(result, {
      appId: "__UNI__D4A2604",
      appName: "shopBIZ",
      appVersion: "1.0.0",
      appVersionCode: "100",
      appLanguage: getAppLanguage(hostLanguage),
      hostVersion: version,
      hostLanguage: hostLanguage,
      hostName: _hostName,
      hostSDKVersion: SDKVersion,
      hostTheme: theme
    }));
  }
};
var getDeviceInfo = {
  returnValue: function returnValue(result) {
    var _result2 = result,
      brand = _result2.brand,
      model = _result2.model;
    var deviceType = getGetDeviceType(result, model);
    var deviceBrand = getDeviceBrand(brand);
    useDeviceId(result);
    result = sortObject(Object.assign(result, {
      deviceType: deviceType,
      deviceBrand: deviceBrand,
      deviceModel: model
    }));
  }
};
var getWindowInfo = {
  returnValue: function returnValue(result) {
    addSafeAreaInsets(result);
    result = sortObject(Object.assign(result, {
      windowTop: 0,
      windowBottom: 0
    }));
  }
};
var getAppAuthorizeSetting = {
  returnValue: function returnValue(result) {
    var locationReducedAccuracy = result.locationReducedAccuracy;
    result.locationAccuracy = 'unsupported';
    if (locationReducedAccuracy === true) {
      result.locationAccuracy = 'reduced';
    } else if (locationReducedAccuracy === false) {
      result.locationAccuracy = 'full';
    }
  }
};

// import navigateTo from 'uni-helpers/navigate-to'

var compressImage = {
  args: function args(fromArgs) {
    // https://developers.weixin.qq.com/community/develop/doc/000c08940c865011298e0a43256800?highLine=compressHeight
    if (fromArgs.compressedHeight && !fromArgs.compressHeight) {
      fromArgs.compressHeight = fromArgs.compressedHeight;
    }
    if (fromArgs.compressedWidth && !fromArgs.compressWidth) {
      fromArgs.compressWidth = fromArgs.compressedWidth;
    }
  }
};
var protocols = {
  redirectTo: redirectTo,
  // navigateTo,  // 由于在微信开发者工具的页面参数，会显示__id__参数，因此暂时关闭mp-weixin对于navigateTo的AOP
  previewImage: previewImage,
  getSystemInfo: getSystemInfo,
  getSystemInfoSync: getSystemInfo,
  showActionSheet: showActionSheet,
  getAppBaseInfo: getAppBaseInfo,
  getDeviceInfo: getDeviceInfo,
  getWindowInfo: getWindowInfo,
  getAppAuthorizeSetting: getAppAuthorizeSetting,
  compressImage: compressImage
};
var todos = ['vibrate', 'preloadPage', 'unPreloadPage', 'loadSubPackage'];
var canIUses = [];
var CALLBACKS = ['success', 'fail', 'cancel', 'complete'];
function processCallback(methodName, method, returnValue) {
  return function (res) {
    return method(processReturnValue(methodName, res, returnValue));
  };
}
function processArgs(methodName, fromArgs) {
  var argsOption = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  var returnValue = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
  var keepFromArgs = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : false;
  if (isPlainObject(fromArgs)) {
    // 一般 api 的参数解析
    var toArgs = keepFromArgs === true ? fromArgs : {}; // returnValue 为 false 时，说明是格式化返回值，直接在返回值对象上修改赋值
    if (isFn(argsOption)) {
      argsOption = argsOption(fromArgs, toArgs) || {};
    }
    for (var key in fromArgs) {
      if (hasOwn(argsOption, key)) {
        var keyOption = argsOption[key];
        if (isFn(keyOption)) {
          keyOption = keyOption(fromArgs[key], fromArgs, toArgs);
        }
        if (!keyOption) {
          // 不支持的参数
          console.warn("The '".concat(methodName, "' method of platform '\u5FAE\u4FE1\u5C0F\u7A0B\u5E8F' does not support option '").concat(key, "'"));
        } else if (isStr(keyOption)) {
          // 重写参数 key
          toArgs[keyOption] = fromArgs[key];
        } else if (isPlainObject(keyOption)) {
          // {name:newName,value:value}可重新指定参数 key:value
          toArgs[keyOption.name ? keyOption.name : key] = keyOption.value;
        }
      } else if (CALLBACKS.indexOf(key) !== -1) {
        if (isFn(fromArgs[key])) {
          toArgs[key] = processCallback(methodName, fromArgs[key], returnValue);
        }
      } else {
        if (!keepFromArgs) {
          toArgs[key] = fromArgs[key];
        }
      }
    }
    return toArgs;
  } else if (isFn(fromArgs)) {
    fromArgs = processCallback(methodName, fromArgs, returnValue);
  }
  return fromArgs;
}
function processReturnValue(methodName, res, returnValue) {
  var keepReturnValue = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
  if (isFn(protocols.returnValue)) {
    // 处理通用 returnValue
    res = protocols.returnValue(methodName, res);
  }
  return processArgs(methodName, res, returnValue, {}, keepReturnValue);
}
function wrapper(methodName, method) {
  if (hasOwn(protocols, methodName)) {
    var protocol = protocols[methodName];
    if (!protocol) {
      // 暂不支持的 api
      return function () {
        console.error("Platform '\u5FAE\u4FE1\u5C0F\u7A0B\u5E8F' does not support '".concat(methodName, "'."));
      };
    }
    return function (arg1, arg2) {
      // 目前 api 最多两个参数
      var options = protocol;
      if (isFn(protocol)) {
        options = protocol(arg1);
      }
      arg1 = processArgs(methodName, arg1, options.args, options.returnValue);
      var args = [arg1];
      if (typeof arg2 !== 'undefined') {
        args.push(arg2);
      }
      if (isFn(options.name)) {
        methodName = options.name(arg1);
      } else if (isStr(options.name)) {
        methodName = options.name;
      }
      var returnValue = wx[methodName].apply(wx, args);
      if (isSyncApi(methodName)) {
        // 同步 api
        return processReturnValue(methodName, returnValue, options.returnValue, isContextApi(methodName));
      }
      return returnValue;
    };
  }
  return method;
}
var todoApis = Object.create(null);
var TODOS = ['onTabBarMidButtonTap', 'subscribePush', 'unsubscribePush', 'onPush', 'offPush', 'share'];
function createTodoApi(name) {
  return function todoApi(_ref) {
    var fail = _ref.fail,
      complete = _ref.complete;
    var res = {
      errMsg: "".concat(name, ":fail method '").concat(name, "' not supported")
    };
    isFn(fail) && fail(res);
    isFn(complete) && complete(res);
  };
}
TODOS.forEach(function (name) {
  todoApis[name] = createTodoApi(name);
});
var providers = {
  oauth: ['weixin'],
  share: ['weixin'],
  payment: ['wxpay'],
  push: ['weixin']
};
function getProvider(_ref2) {
  var service = _ref2.service,
    success = _ref2.success,
    fail = _ref2.fail,
    complete = _ref2.complete;
  var res = false;
  if (providers[service]) {
    res = {
      errMsg: 'getProvider:ok',
      service: service,
      provider: providers[service]
    };
    isFn(success) && success(res);
  } else {
    res = {
      errMsg: 'getProvider:fail service not found'
    };
    isFn(fail) && fail(res);
  }
  isFn(complete) && complete(res);
}
var extraApi = /*#__PURE__*/Object.freeze({
  __proto__: null,
  getProvider: getProvider
});
var getEmitter = function () {
  var Emitter;
  return function getUniEmitter() {
    if (!Emitter) {
      Emitter = new _vue.default();
    }
    return Emitter;
  };
}();
function apply(ctx, method, args) {
  return ctx[method].apply(ctx, args);
}
function $on() {
  return apply(getEmitter(), '$on', Array.prototype.slice.call(arguments));
}
function $off() {
  return apply(getEmitter(), '$off', Array.prototype.slice.call(arguments));
}
function $once() {
  return apply(getEmitter(), '$once', Array.prototype.slice.call(arguments));
}
function $emit() {
  return apply(getEmitter(), '$emit', Array.prototype.slice.call(arguments));
}
var eventApi = /*#__PURE__*/Object.freeze({
  __proto__: null,
  $on: $on,
  $off: $off,
  $once: $once,
  $emit: $emit
});

/**
 * 框架内 try-catch
 */
/**
 * 开发者 try-catch
 */
function tryCatch(fn) {
  return function () {
    try {
      return fn.apply(fn, arguments);
    } catch (e) {
      // TODO
      console.error(e);
    }
  };
}
function getApiCallbacks(params) {
  var apiCallbacks = {};
  for (var name in params) {
    var param = params[name];
    if (isFn(param)) {
      apiCallbacks[name] = tryCatch(param);
      delete params[name];
    }
  }
  return apiCallbacks;
}
var cid;
var cidErrMsg;
var enabled;
function normalizePushMessage(message) {
  try {
    return JSON.parse(message);
  } catch (e) {}
  return message;
}
function invokePushCallback(args) {
  if (args.type === 'enabled') {
    enabled = true;
  } else if (args.type === 'clientId') {
    cid = args.cid;
    cidErrMsg = args.errMsg;
    invokeGetPushCidCallbacks(cid, args.errMsg);
  } else if (args.type === 'pushMsg') {
    var message = {
      type: 'receive',
      data: normalizePushMessage(args.message)
    };
    for (var i = 0; i < onPushMessageCallbacks.length; i++) {
      var callback = onPushMessageCallbacks[i];
      callback(message);
      // 该消息已被阻止
      if (message.stopped) {
        break;
      }
    }
  } else if (args.type === 'click') {
    onPushMessageCallbacks.forEach(function (callback) {
      callback({
        type: 'click',
        data: normalizePushMessage(args.message)
      });
    });
  }
}
var getPushCidCallbacks = [];
function invokeGetPushCidCallbacks(cid, errMsg) {
  getPushCidCallbacks.forEach(function (callback) {
    callback(cid, errMsg);
  });
  getPushCidCallbacks.length = 0;
}
function getPushClientId(args) {
  if (!isPlainObject(args)) {
    args = {};
  }
  var _getApiCallbacks = getApiCallbacks(args),
    success = _getApiCallbacks.success,
    fail = _getApiCallbacks.fail,
    complete = _getApiCallbacks.complete;
  var hasSuccess = isFn(success);
  var hasFail = isFn(fail);
  var hasComplete = isFn(complete);
  Promise.resolve().then(function () {
    if (typeof enabled === 'undefined') {
      enabled = false;
      cid = '';
      cidErrMsg = 'uniPush is not enabled';
    }
    getPushCidCallbacks.push(function (cid, errMsg) {
      var res;
      if (cid) {
        res = {
          errMsg: 'getPushClientId:ok',
          cid: cid
        };
        hasSuccess && success(res);
      } else {
        res = {
          errMsg: 'getPushClientId:fail' + (errMsg ? ' ' + errMsg : '')
        };
        hasFail && fail(res);
      }
      hasComplete && complete(res);
    });
    if (typeof cid !== 'undefined') {
      invokeGetPushCidCallbacks(cid, cidErrMsg);
    }
  });
}
var onPushMessageCallbacks = [];
// 不使用 defineOnApi 实现，是因为 defineOnApi 依赖 UniServiceJSBridge ，该对象目前在小程序上未提供，故简单实现
var onPushMessage = function onPushMessage(fn) {
  if (onPushMessageCallbacks.indexOf(fn) === -1) {
    onPushMessageCallbacks.push(fn);
  }
};
var offPushMessage = function offPushMessage(fn) {
  if (!fn) {
    onPushMessageCallbacks.length = 0;
  } else {
    var index = onPushMessageCallbacks.indexOf(fn);
    if (index > -1) {
      onPushMessageCallbacks.splice(index, 1);
    }
  }
};
var baseInfo = wx.getAppBaseInfo && wx.getAppBaseInfo();
if (!baseInfo) {
  baseInfo = wx.getSystemInfoSync();
}
var host = baseInfo ? baseInfo.host : null;
var shareVideoMessage = host && host.env === 'SAAASDK' ? wx.miniapp.shareVideoMessage : wx.shareVideoMessage;
var api = /*#__PURE__*/Object.freeze({
  __proto__: null,
  shareVideoMessage: shareVideoMessage,
  getPushClientId: getPushClientId,
  onPushMessage: onPushMessage,
  offPushMessage: offPushMessage,
  invokePushCallback: invokePushCallback
});
var mocks = ['__route__', '__wxExparserNodeId__', '__wxWebviewId__'];
function findVmByVueId(vm, vuePid) {
  var $children = vm.$children;
  // 优先查找直属(反向查找:https://github.com/dcloudio/uni-app/issues/1200)
  for (var i = $children.length - 1; i >= 0; i--) {
    var childVm = $children[i];
    if (childVm.$scope._$vueId === vuePid) {
      return childVm;
    }
  }
  // 反向递归查找
  var parentVm;
  for (var _i = $children.length - 1; _i >= 0; _i--) {
    parentVm = findVmByVueId($children[_i], vuePid);
    if (parentVm) {
      return parentVm;
    }
  }
}
function initBehavior(options) {
  return Behavior(options);
}
function isPage() {
  return !!this.route;
}
function initRelation(detail) {
  this.triggerEvent('__l', detail);
}
function selectAllComponents(mpInstance, selector, $refs) {
  var components = mpInstance.selectAllComponents(selector) || [];
  components.forEach(function (component) {
    var ref = component.dataset.ref;
    $refs[ref] = component.$vm || toSkip(component);
    {
      if (component.dataset.vueGeneric === 'scoped') {
        component.selectAllComponents('.scoped-ref').forEach(function (scopedComponent) {
          selectAllComponents(scopedComponent, selector, $refs);
        });
      }
    }
  });
}
function syncRefs(refs, newRefs) {
  var oldKeys = (0, _construct2.default)(Set, (0, _toConsumableArray2.default)(Object.keys(refs)));
  var newKeys = Object.keys(newRefs);
  newKeys.forEach(function (key) {
    var oldValue = refs[key];
    var newValue = newRefs[key];
    if (Array.isArray(oldValue) && Array.isArray(newValue) && oldValue.length === newValue.length && newValue.every(function (value) {
      return oldValue.includes(value);
    })) {
      return;
    }
    refs[key] = newValue;
    oldKeys.delete(key);
  });
  oldKeys.forEach(function (key) {
    delete refs[key];
  });
  return refs;
}
function initRefs(vm) {
  var mpInstance = vm.$scope;
  var refs = {};
  Object.defineProperty(vm, '$refs', {
    get: function get() {
      var $refs = {};
      selectAllComponents(mpInstance, '.vue-ref', $refs);
      // TODO 暂不考虑 for 中的 scoped
      var forComponents = mpInstance.selectAllComponents('.vue-ref-in-for') || [];
      forComponents.forEach(function (component) {
        var ref = component.dataset.ref;
        if (!$refs[ref]) {
          $refs[ref] = [];
        }
        $refs[ref].push(component.$vm || toSkip(component));
      });
      return syncRefs(refs, $refs);
    }
  });
}
function handleLink(event) {
  var _ref3 = event.detail || event.value,
    vuePid = _ref3.vuePid,
    vueOptions = _ref3.vueOptions; // detail 是微信,value 是百度(dipatch)

  var parentVm;
  if (vuePid) {
    parentVm = findVmByVueId(this.$vm, vuePid);
  }
  if (!parentVm) {
    parentVm = this.$vm;
  }
  vueOptions.parent = parentVm;
}
function markMPComponent(component) {
  // 在 Vue 中标记为小程序组件
  var IS_MP = '__v_isMPComponent';
  Object.defineProperty(component, IS_MP, {
    configurable: true,
    enumerable: false,
    value: true
  });
  return component;
}
function toSkip(obj) {
  var OB = '__ob__';
  var SKIP = '__v_skip';
  if (isObject(obj) && Object.isExtensible(obj)) {
    // 避免被 @vue/composition-api 观测
    Object.defineProperty(obj, OB, {
      configurable: true,
      enumerable: false,
      value: (0, _defineProperty2.default)({}, SKIP, true)
    });
  }
  return obj;
}
var WORKLET_RE = /_(.*)_worklet_factory_/;
function initWorkletMethods(mpMethods, vueMethods) {
  if (vueMethods) {
    Object.keys(vueMethods).forEach(function (name) {
      var matches = name.match(WORKLET_RE);
      if (matches) {
        var workletName = matches[1];
        mpMethods[name] = vueMethods[name];
        mpMethods[workletName] = vueMethods[workletName];
      }
    });
  }
}
var MPPage = Page;
var MPComponent = Component;
var customizeRE = /:/g;
var customize = cached(function (str) {
  return camelize(str.replace(customizeRE, '-'));
});
function initTriggerEvent(mpInstance) {
  var oldTriggerEvent = mpInstance.triggerEvent;
  var newTriggerEvent = function newTriggerEvent(event) {
    for (var _len3 = arguments.length, args = new Array(_len3 > 1 ? _len3 - 1 : 0), _key3 = 1; _key3 < _len3; _key3++) {
      args[_key3 - 1] = arguments[_key3];
    }
    // 事件名统一转驼峰格式，仅处理：当前组件为 vue 组件、当前组件为 vue 组件子组件
    if (this.$vm || this.dataset && this.dataset.comType) {
      event = customize(event);
    } else {
      // 针对微信/QQ小程序单独补充驼峰格式事件，以兼容历史项目
      var newEvent = customize(event);
      if (newEvent !== event) {
        oldTriggerEvent.apply(this, [newEvent].concat(args));
      }
    }
    return oldTriggerEvent.apply(this, [event].concat(args));
  };
  try {
    // 京东小程序 triggerEvent 为只读
    mpInstance.triggerEvent = newTriggerEvent;
  } catch (error) {
    mpInstance._triggerEvent = newTriggerEvent;
  }
}
function initHook(name, options, isComponent) {
  var oldHook = options[name];
  options[name] = function () {
    markMPComponent(this);
    initTriggerEvent(this);
    if (oldHook) {
      for (var _len4 = arguments.length, args = new Array(_len4), _key4 = 0; _key4 < _len4; _key4++) {
        args[_key4] = arguments[_key4];
      }
      return oldHook.apply(this, args);
    }
  };
}
if (!MPPage.__$wrappered) {
  MPPage.__$wrappered = true;
  Page = function Page() {
    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    initHook('onLoad', options);
    return MPPage(options);
  };
  Page.after = MPPage.after;
  Component = function Component() {
    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    initHook('created', options);
    return MPComponent(options);
  };
}
var PAGE_EVENT_HOOKS = ['onPullDownRefresh', 'onReachBottom', 'onAddToFavorites', 'onShareTimeline', 'onShareAppMessage', 'onPageScroll', 'onResize', 'onTabItemTap'];
function initMocks(vm, mocks) {
  var mpInstance = vm.$mp[vm.mpType];
  mocks.forEach(function (mock) {
    if (hasOwn(mpInstance, mock)) {
      vm[mock] = mpInstance[mock];
    }
  });
}
function hasHook(hook, vueOptions) {
  if (!vueOptions) {
    return true;
  }
  if (_vue.default.options && Array.isArray(_vue.default.options[hook])) {
    return true;
  }
  vueOptions = vueOptions.default || vueOptions;
  if (isFn(vueOptions)) {
    if (isFn(vueOptions.extendOptions[hook])) {
      return true;
    }
    if (vueOptions.super && vueOptions.super.options && Array.isArray(vueOptions.super.options[hook])) {
      return true;
    }
    return false;
  }
  if (isFn(vueOptions[hook]) || Array.isArray(vueOptions[hook])) {
    return true;
  }
  var mixins = vueOptions.mixins;
  if (Array.isArray(mixins)) {
    return !!mixins.find(function (mixin) {
      return hasHook(hook, mixin);
    });
  }
}
function initHooks(mpOptions, hooks, vueOptions) {
  hooks.forEach(function (hook) {
    if (hasHook(hook, vueOptions)) {
      mpOptions[hook] = function (args) {
        return this.$vm && this.$vm.__call_hook(hook, args);
      };
    }
  });
}
function initUnknownHooks(mpOptions, vueOptions) {
  var excludes = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
  findHooks(vueOptions).forEach(function (hook) {
    return initHook$1(mpOptions, hook, excludes);
  });
}
function findHooks(vueOptions) {
  var hooks = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
  if (vueOptions) {
    Object.keys(vueOptions).forEach(function (name) {
      if (name.indexOf('on') === 0 && isFn(vueOptions[name])) {
        hooks.push(name);
      }
    });
  }
  return hooks;
}
function initHook$1(mpOptions, hook, excludes) {
  if (excludes.indexOf(hook) === -1 && !hasOwn(mpOptions, hook)) {
    mpOptions[hook] = function (args) {
      return this.$vm && this.$vm.__call_hook(hook, args);
    };
  }
}
function initVueComponent(Vue, vueOptions) {
  vueOptions = vueOptions.default || vueOptions;
  var VueComponent;
  if (isFn(vueOptions)) {
    VueComponent = vueOptions;
  } else {
    VueComponent = Vue.extend(vueOptions);
  }
  vueOptions = VueComponent.options;
  return [VueComponent, vueOptions];
}
function initSlots(vm, vueSlots) {
  if (Array.isArray(vueSlots) && vueSlots.length) {
    var $slots = Object.create(null);
    vueSlots.forEach(function (slotName) {
      $slots[slotName] = true;
    });
    vm.$scopedSlots = vm.$slots = $slots;
  }
}
function initVueIds(vueIds, mpInstance) {
  vueIds = (vueIds || '').split(',');
  var len = vueIds.length;
  if (len === 1) {
    mpInstance._$vueId = vueIds[0];
  } else if (len === 2) {
    mpInstance._$vueId = vueIds[0];
    mpInstance._$vuePid = vueIds[1];
  }
}
function initData(vueOptions, context) {
  var data = vueOptions.data || {};
  var methods = vueOptions.methods || {};
  if (typeof data === 'function') {
    try {
      data = data.call(context); // 支持 Vue.prototype 上挂的数据
    } catch (e) {
      if (Object({"VUE_APP_DARK_MODE":"false","VUE_APP_NAME":"shopBIZ","VUE_APP_PLATFORM":"mp-weixin","NODE_ENV":"development","BASE_URL":"/"}).VUE_APP_DEBUG) {
        console.warn('根据 Vue 的 data 函数初始化小程序 data 失败，请尽量确保 data 函数中不访问 vm 对象，否则可能影响首次数据渲染速度。', data);
      }
    }
  } else {
    try {
      // 对 data 格式化
      data = JSON.parse(JSON.stringify(data));
    } catch (e) {}
  }
  if (!isPlainObject(data)) {
    data = {};
  }
  Object.keys(methods).forEach(function (methodName) {
    if (context.__lifecycle_hooks__.indexOf(methodName) === -1 && !hasOwn(data, methodName)) {
      data[methodName] = methods[methodName];
    }
  });
  return data;
}
var PROP_TYPES = [String, Number, Boolean, Object, Array, null];
function createObserver(name) {
  return function observer(newVal, oldVal) {
    if (this.$vm) {
      this.$vm[name] = newVal; // 为了触发其他非 render watcher
    }
  };
}

function initBehaviors(vueOptions, initBehavior) {
  var vueBehaviors = vueOptions.behaviors;
  var vueExtends = vueOptions.extends;
  var vueMixins = vueOptions.mixins;
  var vueProps = vueOptions.props;
  if (!vueProps) {
    vueOptions.props = vueProps = [];
  }
  var behaviors = [];
  if (Array.isArray(vueBehaviors)) {
    vueBehaviors.forEach(function (behavior) {
      behaviors.push(behavior.replace('uni://', "wx".concat("://")));
      if (behavior === 'uni://form-field') {
        if (Array.isArray(vueProps)) {
          vueProps.push('name');
          vueProps.push('value');
        } else {
          vueProps.name = {
            type: String,
            default: ''
          };
          vueProps.value = {
            type: [String, Number, Boolean, Array, Object, Date],
            default: ''
          };
        }
      }
    });
  }
  if (isPlainObject(vueExtends) && vueExtends.props) {
    behaviors.push(initBehavior({
      properties: initProperties(vueExtends.props, true)
    }));
  }
  if (Array.isArray(vueMixins)) {
    vueMixins.forEach(function (vueMixin) {
      if (isPlainObject(vueMixin) && vueMixin.props) {
        behaviors.push(initBehavior({
          properties: initProperties(vueMixin.props, true)
        }));
      }
    });
  }
  return behaviors;
}
function parsePropType(key, type, defaultValue, file) {
  // [String]=>String
  if (Array.isArray(type) && type.length === 1) {
    return type[0];
  }
  return type;
}
function initProperties(props) {
  var isBehavior = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var file = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
  var options = arguments.length > 3 ? arguments[3] : undefined;
  var properties = {};
  if (!isBehavior) {
    properties.vueId = {
      type: String,
      value: ''
    };
    {
      if (options.virtualHost) {
        properties.virtualHostStyle = {
          type: null,
          value: ''
        };
        properties.virtualHostClass = {
          type: null,
          value: ''
        };
      }
    }
    // scopedSlotsCompiler auto
    properties.scopedSlotsCompiler = {
      type: String,
      value: ''
    };
    properties.vueSlots = {
      // 小程序不能直接定义 $slots 的 props，所以通过 vueSlots 转换到 $slots
      type: null,
      value: [],
      observer: function observer(newVal, oldVal) {
        var $slots = Object.create(null);
        newVal.forEach(function (slotName) {
          $slots[slotName] = true;
        });
        this.setData({
          $slots: $slots
        });
      }
    };
  }
  if (Array.isArray(props)) {
    // ['title']
    props.forEach(function (key) {
      properties[key] = {
        type: null,
        observer: createObserver(key)
      };
    });
  } else if (isPlainObject(props)) {
    // {title:{type:String,default:''},content:String}
    Object.keys(props).forEach(function (key) {
      var opts = props[key];
      if (isPlainObject(opts)) {
        // title:{type:String,default:''}
        var value = opts.default;
        if (isFn(value)) {
          value = value();
        }
        opts.type = parsePropType(key, opts.type);
        properties[key] = {
          type: PROP_TYPES.indexOf(opts.type) !== -1 ? opts.type : null,
          value: value,
          observer: createObserver(key)
        };
      } else {
        // content:String
        var type = parsePropType(key, opts);
        properties[key] = {
          type: PROP_TYPES.indexOf(type) !== -1 ? type : null,
          observer: createObserver(key)
        };
      }
    });
  }
  return properties;
}
function wrapper$1(event) {
  // TODO 又得兼容 mpvue 的 mp 对象
  try {
    event.mp = JSON.parse(JSON.stringify(event));
  } catch (e) {}
  event.stopPropagation = noop;
  event.preventDefault = noop;
  event.target = event.target || {};
  if (!hasOwn(event, 'detail')) {
    event.detail = {};
  }
  if (hasOwn(event, 'markerId')) {
    event.detail = (0, _typeof2.default)(event.detail) === 'object' ? event.detail : {};
    event.detail.markerId = event.markerId;
  }
  if (isPlainObject(event.detail)) {
    event.target = Object.assign({}, event.target, event.detail);
  }
  return event;
}
function getExtraValue(vm, dataPathsArray) {
  var context = vm;
  dataPathsArray.forEach(function (dataPathArray) {
    var dataPath = dataPathArray[0];
    var value = dataPathArray[2];
    if (dataPath || typeof value !== 'undefined') {
      // ['','',index,'disable']
      var propPath = dataPathArray[1];
      var valuePath = dataPathArray[3];
      var vFor;
      if (Number.isInteger(dataPath)) {
        vFor = dataPath;
      } else if (!dataPath) {
        vFor = context;
      } else if (typeof dataPath === 'string' && dataPath) {
        if (dataPath.indexOf('#s#') === 0) {
          vFor = dataPath.substr(3);
        } else {
          vFor = vm.__get_value(dataPath, context);
        }
      }
      if (Number.isInteger(vFor)) {
        context = value;
      } else if (!propPath) {
        context = vFor[value];
      } else {
        if (Array.isArray(vFor)) {
          context = vFor.find(function (vForItem) {
            return vm.__get_value(propPath, vForItem) === value;
          });
        } else if (isPlainObject(vFor)) {
          context = Object.keys(vFor).find(function (vForKey) {
            return vm.__get_value(propPath, vFor[vForKey]) === value;
          });
        } else {
          console.error('v-for 暂不支持循环数据：', vFor);
        }
      }
      if (valuePath) {
        context = vm.__get_value(valuePath, context);
      }
    }
  });
  return context;
}
function processEventExtra(vm, extra, event, __args__) {
  var extraObj = {};
  if (Array.isArray(extra) && extra.length) {
    /**
     *[
     *    ['data.items', 'data.id', item.data.id],
     *    ['metas', 'id', meta.id]
     *],
     *[
     *    ['data.items', 'data.id', item.data.id],
     *    ['metas', 'id', meta.id]
     *],
     *'test'
     */
    extra.forEach(function (dataPath, index) {
      if (typeof dataPath === 'string') {
        if (!dataPath) {
          // model,prop.sync
          extraObj['$' + index] = vm;
        } else {
          if (dataPath === '$event') {
            // $event
            extraObj['$' + index] = event;
          } else if (dataPath === 'arguments') {
            extraObj['$' + index] = event.detail ? event.detail.__args__ || __args__ : __args__;
          } else if (dataPath.indexOf('$event.') === 0) {
            // $event.target.value
            extraObj['$' + index] = vm.__get_value(dataPath.replace('$event.', ''), event);
          } else {
            extraObj['$' + index] = vm.__get_value(dataPath);
          }
        }
      } else {
        extraObj['$' + index] = getExtraValue(vm, dataPath);
      }
    });
  }
  return extraObj;
}
function getObjByArray(arr) {
  var obj = {};
  for (var i = 1; i < arr.length; i++) {
    var element = arr[i];
    obj[element[0]] = element[1];
  }
  return obj;
}
function processEventArgs(vm, event) {
  var args = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
  var extra = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : [];
  var isCustom = arguments.length > 4 ? arguments[4] : undefined;
  var methodName = arguments.length > 5 ? arguments[5] : undefined;
  var isCustomMPEvent = false; // wxcomponent 组件，传递原始 event 对象

  // fixed 用户直接触发 mpInstance.triggerEvent
  var __args__ = isPlainObject(event.detail) ? event.detail.__args__ || [event.detail] : [event.detail];
  if (isCustom) {
    // 自定义事件
    isCustomMPEvent = event.currentTarget && event.currentTarget.dataset && event.currentTarget.dataset.comType === 'wx';
    if (!args.length) {
      // 无参数，直接传入 event 或 detail 数组
      if (isCustomMPEvent) {
        return [event];
      }
      return __args__;
    }
  }
  var extraObj = processEventExtra(vm, extra, event, __args__);
  var ret = [];
  args.forEach(function (arg) {
    if (arg === '$event') {
      if (methodName === '__set_model' && !isCustom) {
        // input v-model value
        ret.push(event.target.value);
      } else {
        if (isCustom && !isCustomMPEvent) {
          ret.push(__args__[0]);
        } else {
          // wxcomponent 组件或内置组件
          ret.push(event);
        }
      }
    } else {
      if (Array.isArray(arg) && arg[0] === 'o') {
        ret.push(getObjByArray(arg));
      } else if (typeof arg === 'string' && hasOwn(extraObj, arg)) {
        ret.push(extraObj[arg]);
      } else {
        ret.push(arg);
      }
    }
  });
  return ret;
}
var ONCE = '~';
var CUSTOM = '^';
function isMatchEventType(eventType, optType) {
  return eventType === optType || optType === 'regionchange' && (eventType === 'begin' || eventType === 'end');
}
function getContextVm(vm) {
  var $parent = vm.$parent;
  // 父组件是 scoped slots 或者其他自定义组件时继续查找
  while ($parent && $parent.$parent && ($parent.$options.generic || $parent.$parent.$options.generic || $parent.$scope._$vuePid)) {
    $parent = $parent.$parent;
  }
  return $parent && $parent.$parent;
}
function handleEvent(event) {
  var _this2 = this;
  event = wrapper$1(event);

  // [['tap',[['handle',[1,2,a]],['handle1',[1,2,a]]]]]
  var dataset = (event.currentTarget || event.target).dataset;
  if (!dataset) {
    return console.warn('事件信息不存在');
  }
  var eventOpts = dataset.eventOpts || dataset['event-opts']; // 支付宝 web-view 组件 dataset 非驼峰
  if (!eventOpts) {
    return console.warn('事件信息不存在');
  }

  // [['handle',[1,2,a]],['handle1',[1,2,a]]]
  var eventType = event.type;
  var ret = [];
  eventOpts.forEach(function (eventOpt) {
    var type = eventOpt[0];
    var eventsArray = eventOpt[1];
    var isCustom = type.charAt(0) === CUSTOM;
    type = isCustom ? type.slice(1) : type;
    var isOnce = type.charAt(0) === ONCE;
    type = isOnce ? type.slice(1) : type;
    if (eventsArray && isMatchEventType(eventType, type)) {
      eventsArray.forEach(function (eventArray) {
        var methodName = eventArray[0];
        if (methodName) {
          var handlerCtx = _this2.$vm;
          if (handlerCtx.$options.generic) {
            // mp-weixin,mp-toutiao 抽象节点模拟 scoped slots
            handlerCtx = getContextVm(handlerCtx) || handlerCtx;
          }
          if (methodName === '$emit') {
            handlerCtx.$emit.apply(handlerCtx, processEventArgs(_this2.$vm, event, eventArray[1], eventArray[2], isCustom, methodName));
            return;
          }
          var handler = handlerCtx[methodName];
          if (!isFn(handler)) {
            var _type = _this2.$vm.mpType === 'page' ? 'Page' : 'Component';
            var path = _this2.route || _this2.is;
            throw new Error("".concat(_type, " \"").concat(path, "\" does not have a method \"").concat(methodName, "\""));
          }
          if (isOnce) {
            if (handler.once) {
              return;
            }
            handler.once = true;
          }
          var params = processEventArgs(_this2.$vm, event, eventArray[1], eventArray[2], isCustom, methodName);
          params = Array.isArray(params) ? params : [];
          // 参数尾部增加原始事件对象用于复杂表达式内获取额外数据
          if (/=\s*\S+\.eventParams\s*\|\|\s*\S+\[['"]event-params['"]\]/.test(handler.toString())) {
            // eslint-disable-next-line no-sparse-arrays
            params = params.concat([,,,,,,,,,, event]);
          }
          ret.push(handler.apply(handlerCtx, params));
        }
      });
    }
  });
  if (eventType === 'input' && ret.length === 1 && typeof ret[0] !== 'undefined') {
    return ret[0];
  }
}
var eventChannels = {};
function getEventChannel(id) {
  var eventChannel = eventChannels[id];
  delete eventChannels[id];
  return eventChannel;
}
var hooks = ['onShow', 'onHide', 'onError', 'onPageNotFound', 'onThemeChange', 'onUnhandledRejection'];
function initEventChannel() {
  _vue.default.prototype.getOpenerEventChannel = function () {
    // 微信小程序使用自身getOpenerEventChannel
    {
      return this.$scope.getOpenerEventChannel();
    }
  };
  var callHook = _vue.default.prototype.__call_hook;
  _vue.default.prototype.__call_hook = function (hook, args) {
    if (hook === 'onLoad' && args && args.__id__) {
      this.__eventChannel__ = getEventChannel(args.__id__);
      delete args.__id__;
    }
    return callHook.call(this, hook, args);
  };
}
function initScopedSlotsParams() {
  var center = {};
  var parents = {};
  function currentId(fn) {
    var vueIds = this.$options.propsData.vueId;
    if (vueIds) {
      var vueId = vueIds.split(',')[0];
      fn(vueId);
    }
  }
  _vue.default.prototype.$hasSSP = function (vueId) {
    var slot = center[vueId];
    if (!slot) {
      parents[vueId] = this;
      this.$on('hook:destroyed', function () {
        delete parents[vueId];
      });
    }
    return slot;
  };
  _vue.default.prototype.$getSSP = function (vueId, name, needAll) {
    var slot = center[vueId];
    if (slot) {
      var params = slot[name] || [];
      if (needAll) {
        return params;
      }
      return params[0];
    }
  };
  _vue.default.prototype.$setSSP = function (name, value) {
    var index = 0;
    currentId.call(this, function (vueId) {
      var slot = center[vueId];
      var params = slot[name] = slot[name] || [];
      params.push(value);
      index = params.length - 1;
    });
    return index;
  };
  _vue.default.prototype.$initSSP = function () {
    currentId.call(this, function (vueId) {
      center[vueId] = {};
    });
  };
  _vue.default.prototype.$callSSP = function () {
    currentId.call(this, function (vueId) {
      if (parents[vueId]) {
        parents[vueId].$forceUpdate();
      }
    });
  };
  _vue.default.mixin({
    destroyed: function destroyed() {
      var propsData = this.$options.propsData;
      var vueId = propsData && propsData.vueId;
      if (vueId) {
        delete center[vueId];
        delete parents[vueId];
      }
    }
  });
}
function parseBaseApp(vm, _ref4) {
  var mocks = _ref4.mocks,
    initRefs = _ref4.initRefs;
  initEventChannel();
  {
    initScopedSlotsParams();
  }
  if (vm.$options.store) {
    _vue.default.prototype.$store = vm.$options.store;
  }
  uniIdMixin(_vue.default);
  _vue.default.prototype.mpHost = "mp-weixin";
  _vue.default.mixin({
    beforeCreate: function beforeCreate() {
      if (!this.$options.mpType) {
        return;
      }
      this.mpType = this.$options.mpType;
      this.$mp = (0, _defineProperty2.default)({
        data: {}
      }, this.mpType, this.$options.mpInstance);
      this.$scope = this.$options.mpInstance;
      delete this.$options.mpType;
      delete this.$options.mpInstance;
      if (this.mpType === 'page' && typeof getApp === 'function') {
        // hack vue-i18n
        var app = getApp();
        if (app.$vm && app.$vm.$i18n) {
          this._i18n = app.$vm.$i18n;
        }
      }
      if (this.mpType !== 'app') {
        initRefs(this);
        initMocks(this, mocks);
      }
    }
  });
  var appOptions = {
    onLaunch: function onLaunch(args) {
      if (this.$vm) {
        // 已经初始化过了，主要是为了百度，百度 onShow 在 onLaunch 之前
        return;
      }
      {
        if (wx.canIUse && !wx.canIUse('nextTick')) {
          // 事实 上2.2.3 即可，简单使用 2.3.0 的 nextTick 判断
          console.error('当前微信基础库版本过低，请将 微信开发者工具-详情-项目设置-调试基础库版本 更换为`2.3.0`以上');
        }
      }
      this.$vm = vm;
      this.$vm.$mp = {
        app: this
      };
      this.$vm.$scope = this;
      // vm 上也挂载 globalData
      this.$vm.globalData = this.globalData;
      this.$vm._isMounted = true;
      this.$vm.__call_hook('mounted', args);
      this.$vm.__call_hook('onLaunch', args);
    }
  };

  // 兼容旧版本 globalData
  appOptions.globalData = vm.$options.globalData || {};
  // 将 methods 中的方法挂在 getApp() 中
  var methods = vm.$options.methods;
  if (methods) {
    Object.keys(methods).forEach(function (name) {
      appOptions[name] = methods[name];
    });
  }
  initAppLocale(_vue.default, vm, normalizeLocale(wx.getSystemInfoSync().language) || LOCALE_EN);
  initHooks(appOptions, hooks);
  initUnknownHooks(appOptions, vm.$options);
  return appOptions;
}
function parseApp(vm) {
  return parseBaseApp(vm, {
    mocks: mocks,
    initRefs: initRefs
  });
}
function createApp(vm) {
  App(parseApp(vm));
  return vm;
}
var encodeReserveRE = /[!'()*]/g;
var encodeReserveReplacer = function encodeReserveReplacer(c) {
  return '%' + c.charCodeAt(0).toString(16);
};
var commaRE = /%2C/g;

// fixed encodeURIComponent which is more conformant to RFC3986:
// - escapes [!'()*]
// - preserve commas
var encode = function encode(str) {
  return encodeURIComponent(str).replace(encodeReserveRE, encodeReserveReplacer).replace(commaRE, ',');
};
function stringifyQuery(obj) {
  var encodeStr = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : encode;
  var res = obj ? Object.keys(obj).map(function (key) {
    var val = obj[key];
    if (val === undefined) {
      return '';
    }
    if (val === null) {
      return encodeStr(key);
    }
    if (Array.isArray(val)) {
      var result = [];
      val.forEach(function (val2) {
        if (val2 === undefined) {
          return;
        }
        if (val2 === null) {
          result.push(encodeStr(key));
        } else {
          result.push(encodeStr(key) + '=' + encodeStr(val2));
        }
      });
      return result.join('&');
    }
    return encodeStr(key) + '=' + encodeStr(val);
  }).filter(function (x) {
    return x.length > 0;
  }).join('&') : null;
  return res ? "?".concat(res) : '';
}
function parseBaseComponent(vueComponentOptions) {
  var _ref5 = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {},
    isPage = _ref5.isPage,
    initRelation = _ref5.initRelation;
  var needVueOptions = arguments.length > 2 ? arguments[2] : undefined;
  var _initVueComponent = initVueComponent(_vue.default, vueComponentOptions),
    _initVueComponent2 = (0, _slicedToArray2.default)(_initVueComponent, 2),
    VueComponent = _initVueComponent2[0],
    vueOptions = _initVueComponent2[1];
  var options = _objectSpread({
    multipleSlots: true,
    // styleIsolation: 'apply-shared',
    addGlobalClass: true
  }, vueOptions.options || {});
  {
    // 微信 multipleSlots 部分情况有 bug，导致内容顺序错乱 如 u-list，提供覆盖选项
    if (vueOptions['mp-weixin'] && vueOptions['mp-weixin'].options) {
      Object.assign(options, vueOptions['mp-weixin'].options);
    }
  }
  var componentOptions = {
    options: options,
    data: initData(vueOptions, _vue.default.prototype),
    behaviors: initBehaviors(vueOptions, initBehavior),
    properties: initProperties(vueOptions.props, false, vueOptions.__file, options),
    lifetimes: {
      attached: function attached() {
        var properties = this.properties;
        var options = {
          mpType: isPage.call(this) ? 'page' : 'component',
          mpInstance: this,
          propsData: properties
        };
        initVueIds(properties.vueId, this);

        // 处理父子关系
        initRelation.call(this, {
          vuePid: this._$vuePid,
          vueOptions: options
        });

        // 初始化 vue 实例
        this.$vm = new VueComponent(options);

        // 处理$slots,$scopedSlots（暂不支持动态变化$slots）
        initSlots(this.$vm, properties.vueSlots);

        // 触发首次 setData
        this.$vm.$mount();
      },
      ready: function ready() {
        // 当组件 props 默认值为 true，初始化时传入 false 会导致 created,ready 触发, 但 attached 不触发
        // https://developers.weixin.qq.com/community/develop/doc/00066ae2844cc0f8eb883e2a557800
        if (this.$vm) {
          this.$vm._isMounted = true;
          this.$vm.__call_hook('mounted');
          this.$vm.__call_hook('onReady');
        }
      },
      detached: function detached() {
        this.$vm && this.$vm.$destroy();
      }
    },
    pageLifetimes: {
      show: function show(args) {
        this.$vm && this.$vm.__call_hook('onPageShow', args);
      },
      hide: function hide() {
        this.$vm && this.$vm.__call_hook('onPageHide');
      },
      resize: function resize(size) {
        this.$vm && this.$vm.__call_hook('onPageResize', size);
      }
    },
    methods: {
      __l: handleLink,
      __e: handleEvent
    }
  };
  // externalClasses
  if (vueOptions.externalClasses) {
    componentOptions.externalClasses = vueOptions.externalClasses;
  }
  if (Array.isArray(vueOptions.wxsCallMethods)) {
    vueOptions.wxsCallMethods.forEach(function (callMethod) {
      componentOptions.methods[callMethod] = function (args) {
        return this.$vm[callMethod](args);
      };
    });
  }
  if (needVueOptions) {
    return [componentOptions, vueOptions, VueComponent];
  }
  if (isPage) {
    return componentOptions;
  }
  return [componentOptions, VueComponent];
}
function parseComponent(vueComponentOptions, needVueOptions) {
  return parseBaseComponent(vueComponentOptions, {
    isPage: isPage,
    initRelation: initRelation
  }, needVueOptions);
}
var hooks$1 = ['onShow', 'onHide', 'onUnload'];
hooks$1.push.apply(hooks$1, PAGE_EVENT_HOOKS);
function parseBasePage(vuePageOptions) {
  var _parseComponent = parseComponent(vuePageOptions, true),
    _parseComponent2 = (0, _slicedToArray2.default)(_parseComponent, 2),
    pageOptions = _parseComponent2[0],
    vueOptions = _parseComponent2[1];
  initHooks(pageOptions.methods, hooks$1, vueOptions);
  pageOptions.methods.onLoad = function (query) {
    this.options = query;
    var copyQuery = Object.assign({}, query);
    delete copyQuery.__id__;
    this.$page = {
      fullPath: '/' + (this.route || this.is) + stringifyQuery(copyQuery)
    };
    this.$vm.$mp.query = query; // 兼容 mpvue
    this.$vm.__call_hook('onLoad', query);
  };
  {
    initUnknownHooks(pageOptions.methods, vuePageOptions, ['onReady']);
  }
  {
    initWorkletMethods(pageOptions.methods, vueOptions.methods);
  }
  return pageOptions;
}
function parsePage(vuePageOptions) {
  return parseBasePage(vuePageOptions);
}
function createPage(vuePageOptions) {
  {
    return Component(parsePage(vuePageOptions));
  }
}
function createComponent(vueOptions) {
  {
    return Component(parseComponent(vueOptions));
  }
}
function createSubpackageApp(vm) {
  var appOptions = parseApp(vm);
  var app = getApp({
    allowDefault: true
  });
  vm.$scope = app;
  var globalData = app.globalData;
  if (globalData) {
    Object.keys(appOptions.globalData).forEach(function (name) {
      if (!hasOwn(globalData, name)) {
        globalData[name] = appOptions.globalData[name];
      }
    });
  }
  Object.keys(appOptions).forEach(function (name) {
    if (!hasOwn(app, name)) {
      app[name] = appOptions[name];
    }
  });
  if (isFn(appOptions.onShow) && wx.onAppShow) {
    wx.onAppShow(function () {
      for (var _len5 = arguments.length, args = new Array(_len5), _key5 = 0; _key5 < _len5; _key5++) {
        args[_key5] = arguments[_key5];
      }
      vm.__call_hook('onShow', args);
    });
  }
  if (isFn(appOptions.onHide) && wx.onAppHide) {
    wx.onAppHide(function () {
      for (var _len6 = arguments.length, args = new Array(_len6), _key6 = 0; _key6 < _len6; _key6++) {
        args[_key6] = arguments[_key6];
      }
      vm.__call_hook('onHide', args);
    });
  }
  if (isFn(appOptions.onLaunch)) {
    var args = wx.getLaunchOptionsSync && wx.getLaunchOptionsSync();
    vm.__call_hook('onLaunch', args);
  }
  return vm;
}
function createPlugin(vm) {
  var appOptions = parseApp(vm);
  if (isFn(appOptions.onShow) && wx.onAppShow) {
    wx.onAppShow(function () {
      for (var _len7 = arguments.length, args = new Array(_len7), _key7 = 0; _key7 < _len7; _key7++) {
        args[_key7] = arguments[_key7];
      }
      vm.__call_hook('onShow', args);
    });
  }
  if (isFn(appOptions.onHide) && wx.onAppHide) {
    wx.onAppHide(function () {
      for (var _len8 = arguments.length, args = new Array(_len8), _key8 = 0; _key8 < _len8; _key8++) {
        args[_key8] = arguments[_key8];
      }
      vm.__call_hook('onHide', args);
    });
  }
  if (isFn(appOptions.onLaunch)) {
    var args = wx.getLaunchOptionsSync && wx.getLaunchOptionsSync();
    vm.__call_hook('onLaunch', args);
  }
  return vm;
}
todos.forEach(function (todoApi) {
  protocols[todoApi] = false;
});
canIUses.forEach(function (canIUseApi) {
  var apiName = protocols[canIUseApi] && protocols[canIUseApi].name ? protocols[canIUseApi].name : canIUseApi;
  if (!wx.canIUse(apiName)) {
    protocols[canIUseApi] = false;
  }
});
var uni = {};
if (typeof Proxy !== 'undefined' && "mp-weixin" !== 'app-plus') {
  uni = new Proxy({}, {
    get: function get(target, name) {
      if (hasOwn(target, name)) {
        return target[name];
      }
      if (baseApi[name]) {
        return baseApi[name];
      }
      if (api[name]) {
        return promisify(name, api[name]);
      }
      {
        if (extraApi[name]) {
          return promisify(name, extraApi[name]);
        }
        if (todoApis[name]) {
          return promisify(name, todoApis[name]);
        }
      }
      if (eventApi[name]) {
        return eventApi[name];
      }
      return promisify(name, wrapper(name, wx[name]));
    },
    set: function set(target, name, value) {
      target[name] = value;
      return true;
    }
  });
} else {
  Object.keys(baseApi).forEach(function (name) {
    uni[name] = baseApi[name];
  });
  {
    Object.keys(todoApis).forEach(function (name) {
      uni[name] = promisify(name, todoApis[name]);
    });
    Object.keys(extraApi).forEach(function (name) {
      uni[name] = promisify(name, extraApi[name]);
    });
  }
  Object.keys(eventApi).forEach(function (name) {
    uni[name] = eventApi[name];
  });
  Object.keys(api).forEach(function (name) {
    uni[name] = promisify(name, api[name]);
  });
  Object.keys(wx).forEach(function (name) {
    if (hasOwn(wx, name) || hasOwn(protocols, name)) {
      uni[name] = promisify(name, wrapper(name, wx[name]));
    }
  });
}
wx.createApp = createApp;
wx.createPage = createPage;
wx.createComponent = createComponent;
wx.createSubpackageApp = createSubpackageApp;
wx.createPlugin = createPlugin;
var uni$1 = uni;
var _default = uni$1;
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/wx.js */ 1)["default"], __webpack_require__(/*! ./../../../webpack/buildin/global.js */ 3)))

/***/ }),

/***/ 20:
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/iterableToArray.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
}
module.exports = _iterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 200:
/*!****************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-icon/components/uv-icon/props.js ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default = {
  props: _objectSpread({
    // 图标类名
    name: {
      type: String,
      default: ''
    },
    // 图标颜色，可接受主题色
    color: {
      type: String,
      default: '#606266'
    },
    // 字体大小，单位px
    size: {
      type: [String, Number],
      default: '16px'
    },
    // 是否显示粗体
    bold: {
      type: Boolean,
      default: false
    },
    // 点击图标的时候传递事件出去的index（用于区分点击了哪一个）
    index: {
      type: [String, Number],
      default: null
    },
    // 触摸图标时的类名
    hoverClass: {
      type: String,
      default: ''
    },
    // 自定义扩展前缀，方便用户扩展自己的图标库
    customPrefix: {
      type: String,
      default: 'uvicon'
    },
    // 图标右边或者下面的文字
    label: {
      type: [String, Number],
      default: ''
    },
    // label的位置，只能右边或者下边
    labelPos: {
      type: String,
      default: 'right'
    },
    // label的大小
    labelSize: {
      type: [String, Number],
      default: '15px'
    },
    // label的颜色
    labelColor: {
      type: String,
      default: '#606266'
    },
    // label与图标的距离
    space: {
      type: [String, Number],
      default: '3px'
    },
    // 图片的mode
    imgMode: {
      type: String,
      default: 'aspectFit'
    },
    // 用于显示图片小图标时，图片的宽度
    width: {
      type: [String, Number],
      default: ''
    },
    // 用于显示图片小图标时，图片的高度
    height: {
      type: [String, Number],
      default: ''
    },
    // 用于解决某些情况下，让图标垂直居中的用途
    top: {
      type: [String, Number],
      default: 0
    },
    // 是否阻止事件传播
    stop: {
      type: Boolean,
      default: false
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.icon)
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 208:
/*!****************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-line/components/uv-line/props.js ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default = {
  props: _objectSpread({
    color: {
      type: String,
      default: '#d6d7d9'
    },
    // 长度，竖向时表现为高度，横向时表现为长度，可以为百分比，带px单位的值等
    length: {
      type: [String, Number],
      default: '100%'
    },
    // 线条方向，col-竖向，row-横向
    direction: {
      type: String,
      default: 'row'
    },
    // 是否显示细边框
    hairline: {
      type: Boolean,
      default: true
    },
    // 线条与上下左右元素的间距，字符串形式，如"30px"、"20px 30px"
    margin: {
      type: [String, Number],
      default: 0
    },
    // 是否虚线，true-虚线，false-实线
    dashed: {
      type: Boolean,
      default: false
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.line)
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 21:
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/nonIterableSpread.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableSpread, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 216:
/*!****************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-grid/components/uv-grid/props.js ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default = {
  props: _objectSpread({
    // 分成几列
    col: {
      type: [String, Number],
      default: 3
    },
    // 是否显示边框
    border: {
      type: Boolean,
      default: false
    },
    // 宫格对齐方式，表现为数量少的时候，靠左，居中，还是靠右
    align: {
      type: String,
      default: 'left'
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.grid)
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 22:
/*!*************************************************************!*\
  !*** ./node_modules/@dcloudio/uni-i18n/dist/uni-i18n.es.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni, global) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.LOCALE_ZH_HANT = exports.LOCALE_ZH_HANS = exports.LOCALE_FR = exports.LOCALE_ES = exports.LOCALE_EN = exports.I18n = exports.Formatter = void 0;
exports.compileI18nJsonStr = compileI18nJsonStr;
exports.hasI18nJson = hasI18nJson;
exports.initVueI18n = initVueI18n;
exports.isI18nStr = isI18nStr;
exports.isString = void 0;
exports.normalizeLocale = normalizeLocale;
exports.parseI18nJson = parseI18nJson;
exports.resolveLocale = resolveLocale;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ 5));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ 23));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ 24));
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ 13));
var isObject = function isObject(val) {
  return val !== null && (0, _typeof2.default)(val) === 'object';
};
var defaultDelimiters = ['{', '}'];
var BaseFormatter = /*#__PURE__*/function () {
  function BaseFormatter() {
    (0, _classCallCheck2.default)(this, BaseFormatter);
    this._caches = Object.create(null);
  }
  (0, _createClass2.default)(BaseFormatter, [{
    key: "interpolate",
    value: function interpolate(message, values) {
      var delimiters = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : defaultDelimiters;
      if (!values) {
        return [message];
      }
      var tokens = this._caches[message];
      if (!tokens) {
        tokens = parse(message, delimiters);
        this._caches[message] = tokens;
      }
      return compile(tokens, values);
    }
  }]);
  return BaseFormatter;
}();
exports.Formatter = BaseFormatter;
var RE_TOKEN_LIST_VALUE = /^(?:\d)+/;
var RE_TOKEN_NAMED_VALUE = /^(?:\w)+/;
function parse(format, _ref) {
  var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
    startDelimiter = _ref2[0],
    endDelimiter = _ref2[1];
  var tokens = [];
  var position = 0;
  var text = '';
  while (position < format.length) {
    var char = format[position++];
    if (char === startDelimiter) {
      if (text) {
        tokens.push({
          type: 'text',
          value: text
        });
      }
      text = '';
      var sub = '';
      char = format[position++];
      while (char !== undefined && char !== endDelimiter) {
        sub += char;
        char = format[position++];
      }
      var isClosed = char === endDelimiter;
      var type = RE_TOKEN_LIST_VALUE.test(sub) ? 'list' : isClosed && RE_TOKEN_NAMED_VALUE.test(sub) ? 'named' : 'unknown';
      tokens.push({
        value: sub,
        type: type
      });
    }
    //  else if (char === '%') {
    //   // when found rails i18n syntax, skip text capture
    //   if (format[position] !== '{') {
    //     text += char
    //   }
    // }
    else {
      text += char;
    }
  }
  text && tokens.push({
    type: 'text',
    value: text
  });
  return tokens;
}
function compile(tokens, values) {
  var compiled = [];
  var index = 0;
  var mode = Array.isArray(values) ? 'list' : isObject(values) ? 'named' : 'unknown';
  if (mode === 'unknown') {
    return compiled;
  }
  while (index < tokens.length) {
    var token = tokens[index];
    switch (token.type) {
      case 'text':
        compiled.push(token.value);
        break;
      case 'list':
        compiled.push(values[parseInt(token.value, 10)]);
        break;
      case 'named':
        if (mode === 'named') {
          compiled.push(values[token.value]);
        } else {
          if (true) {
            console.warn("Type of token '".concat(token.type, "' and format of value '").concat(mode, "' don't match!"));
          }
        }
        break;
      case 'unknown':
        if (true) {
          console.warn("Detect 'unknown' type of token!");
        }
        break;
    }
    index++;
  }
  return compiled;
}
var LOCALE_ZH_HANS = 'zh-Hans';
exports.LOCALE_ZH_HANS = LOCALE_ZH_HANS;
var LOCALE_ZH_HANT = 'zh-Hant';
exports.LOCALE_ZH_HANT = LOCALE_ZH_HANT;
var LOCALE_EN = 'en';
exports.LOCALE_EN = LOCALE_EN;
var LOCALE_FR = 'fr';
exports.LOCALE_FR = LOCALE_FR;
var LOCALE_ES = 'es';
exports.LOCALE_ES = LOCALE_ES;
var hasOwnProperty = Object.prototype.hasOwnProperty;
var hasOwn = function hasOwn(val, key) {
  return hasOwnProperty.call(val, key);
};
var defaultFormatter = new BaseFormatter();
function include(str, parts) {
  return !!parts.find(function (part) {
    return str.indexOf(part) !== -1;
  });
}
function startsWith(str, parts) {
  return parts.find(function (part) {
    return str.indexOf(part) === 0;
  });
}
function normalizeLocale(locale, messages) {
  if (!locale) {
    return;
  }
  locale = locale.trim().replace(/_/g, '-');
  if (messages && messages[locale]) {
    return locale;
  }
  locale = locale.toLowerCase();
  if (locale === 'chinese') {
    // 支付宝
    return LOCALE_ZH_HANS;
  }
  if (locale.indexOf('zh') === 0) {
    if (locale.indexOf('-hans') > -1) {
      return LOCALE_ZH_HANS;
    }
    if (locale.indexOf('-hant') > -1) {
      return LOCALE_ZH_HANT;
    }
    if (include(locale, ['-tw', '-hk', '-mo', '-cht'])) {
      return LOCALE_ZH_HANT;
    }
    return LOCALE_ZH_HANS;
  }
  var locales = [LOCALE_EN, LOCALE_FR, LOCALE_ES];
  if (messages && Object.keys(messages).length > 0) {
    locales = Object.keys(messages);
  }
  var lang = startsWith(locale, locales);
  if (lang) {
    return lang;
  }
}
var I18n = /*#__PURE__*/function () {
  function I18n(_ref3) {
    var locale = _ref3.locale,
      fallbackLocale = _ref3.fallbackLocale,
      messages = _ref3.messages,
      watcher = _ref3.watcher,
      formater = _ref3.formater;
    (0, _classCallCheck2.default)(this, I18n);
    this.locale = LOCALE_EN;
    this.fallbackLocale = LOCALE_EN;
    this.message = {};
    this.messages = {};
    this.watchers = [];
    if (fallbackLocale) {
      this.fallbackLocale = fallbackLocale;
    }
    this.formater = formater || defaultFormatter;
    this.messages = messages || {};
    this.setLocale(locale || LOCALE_EN);
    if (watcher) {
      this.watchLocale(watcher);
    }
  }
  (0, _createClass2.default)(I18n, [{
    key: "setLocale",
    value: function setLocale(locale) {
      var _this = this;
      var oldLocale = this.locale;
      this.locale = normalizeLocale(locale, this.messages) || this.fallbackLocale;
      if (!this.messages[this.locale]) {
        // 可能初始化时不存在
        this.messages[this.locale] = {};
      }
      this.message = this.messages[this.locale];
      // 仅发生变化时，通知
      if (oldLocale !== this.locale) {
        this.watchers.forEach(function (watcher) {
          watcher(_this.locale, oldLocale);
        });
      }
    }
  }, {
    key: "getLocale",
    value: function getLocale() {
      return this.locale;
    }
  }, {
    key: "watchLocale",
    value: function watchLocale(fn) {
      var _this2 = this;
      var index = this.watchers.push(fn) - 1;
      return function () {
        _this2.watchers.splice(index, 1);
      };
    }
  }, {
    key: "add",
    value: function add(locale, message) {
      var override = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
      var curMessages = this.messages[locale];
      if (curMessages) {
        if (override) {
          Object.assign(curMessages, message);
        } else {
          Object.keys(message).forEach(function (key) {
            if (!hasOwn(curMessages, key)) {
              curMessages[key] = message[key];
            }
          });
        }
      } else {
        this.messages[locale] = message;
      }
    }
  }, {
    key: "f",
    value: function f(message, values, delimiters) {
      return this.formater.interpolate(message, values, delimiters).join('');
    }
  }, {
    key: "t",
    value: function t(key, locale, values) {
      var message = this.message;
      if (typeof locale === 'string') {
        locale = normalizeLocale(locale, this.messages);
        locale && (message = this.messages[locale]);
      } else {
        values = locale;
      }
      if (!hasOwn(message, key)) {
        console.warn("Cannot translate the value of keypath ".concat(key, ". Use the value of keypath as default."));
        return key;
      }
      return this.formater.interpolate(message[key], values).join('');
    }
  }]);
  return I18n;
}();
exports.I18n = I18n;
function watchAppLocale(appVm, i18n) {
  // 需要保证 watch 的触发在组件渲染之前
  if (appVm.$watchLocale) {
    // vue2
    appVm.$watchLocale(function (newLocale) {
      i18n.setLocale(newLocale);
    });
  } else {
    appVm.$watch(function () {
      return appVm.$locale;
    }, function (newLocale) {
      i18n.setLocale(newLocale);
    });
  }
}
function getDefaultLocale() {
  if (typeof uni !== 'undefined' && uni.getLocale) {
    return uni.getLocale();
  }
  // 小程序平台，uni 和 uni-i18n 互相引用，导致访问不到 uni，故在 global 上挂了 getLocale
  if (typeof global !== 'undefined' && global.getLocale) {
    return global.getLocale();
  }
  return LOCALE_EN;
}
function initVueI18n(locale) {
  var messages = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var fallbackLocale = arguments.length > 2 ? arguments[2] : undefined;
  var watcher = arguments.length > 3 ? arguments[3] : undefined;
  // 兼容旧版本入参
  if (typeof locale !== 'string') {
    var _ref4 = [messages, locale];
    locale = _ref4[0];
    messages = _ref4[1];
  }
  if (typeof locale !== 'string') {
    // 因为小程序平台，uni-i18n 和 uni 互相引用，导致此时访问 uni 时，为 undefined
    locale = getDefaultLocale();
  }
  if (typeof fallbackLocale !== 'string') {
    fallbackLocale = typeof __uniConfig !== 'undefined' && __uniConfig.fallbackLocale || LOCALE_EN;
  }
  var i18n = new I18n({
    locale: locale,
    fallbackLocale: fallbackLocale,
    messages: messages,
    watcher: watcher
  });
  var _t = function t(key, values) {
    if (typeof getApp !== 'function') {
      // app view
      /* eslint-disable no-func-assign */
      _t = function t(key, values) {
        return i18n.t(key, values);
      };
    } else {
      var isWatchedAppLocale = false;
      _t = function t(key, values) {
        var appVm = getApp().$vm;
        // 可能$vm还不存在，比如在支付宝小程序中，组件定义较早，在props的default里使用了t()函数（如uni-goods-nav），此时app还未初始化
        // options: {
        // 	type: Array,
        // 	default () {
        // 		return [{
        // 			icon: 'shop',
        // 			text: t("uni-goods-nav.options.shop"),
        // 		}, {
        // 			icon: 'cart',
        // 			text: t("uni-goods-nav.options.cart")
        // 		}]
        // 	}
        // },
        if (appVm) {
          // 触发响应式
          appVm.$locale;
          if (!isWatchedAppLocale) {
            isWatchedAppLocale = true;
            watchAppLocale(appVm, i18n);
          }
        }
        return i18n.t(key, values);
      };
    }
    return _t(key, values);
  };
  return {
    i18n: i18n,
    f: function f(message, values, delimiters) {
      return i18n.f(message, values, delimiters);
    },
    t: function t(key, values) {
      return _t(key, values);
    },
    add: function add(locale, message) {
      var override = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
      return i18n.add(locale, message, override);
    },
    watch: function watch(fn) {
      return i18n.watchLocale(fn);
    },
    getLocale: function getLocale() {
      return i18n.getLocale();
    },
    setLocale: function setLocale(newLocale) {
      return i18n.setLocale(newLocale);
    }
  };
}
var isString = function isString(val) {
  return typeof val === 'string';
};
exports.isString = isString;
var formater;
function hasI18nJson(jsonObj, delimiters) {
  if (!formater) {
    formater = new BaseFormatter();
  }
  return walkJsonObj(jsonObj, function (jsonObj, key) {
    var value = jsonObj[key];
    if (isString(value)) {
      if (isI18nStr(value, delimiters)) {
        return true;
      }
    } else {
      return hasI18nJson(value, delimiters);
    }
  });
}
function parseI18nJson(jsonObj, values, delimiters) {
  if (!formater) {
    formater = new BaseFormatter();
  }
  walkJsonObj(jsonObj, function (jsonObj, key) {
    var value = jsonObj[key];
    if (isString(value)) {
      if (isI18nStr(value, delimiters)) {
        jsonObj[key] = compileStr(value, values, delimiters);
      }
    } else {
      parseI18nJson(value, values, delimiters);
    }
  });
  return jsonObj;
}
function compileI18nJsonStr(jsonStr, _ref5) {
  var locale = _ref5.locale,
    locales = _ref5.locales,
    delimiters = _ref5.delimiters;
  if (!isI18nStr(jsonStr, delimiters)) {
    return jsonStr;
  }
  if (!formater) {
    formater = new BaseFormatter();
  }
  var localeValues = [];
  Object.keys(locales).forEach(function (name) {
    if (name !== locale) {
      localeValues.push({
        locale: name,
        values: locales[name]
      });
    }
  });
  localeValues.unshift({
    locale: locale,
    values: locales[locale]
  });
  try {
    return JSON.stringify(compileJsonObj(JSON.parse(jsonStr), localeValues, delimiters), null, 2);
  } catch (e) {}
  return jsonStr;
}
function isI18nStr(value, delimiters) {
  return value.indexOf(delimiters[0]) > -1;
}
function compileStr(value, values, delimiters) {
  return formater.interpolate(value, values, delimiters).join('');
}
function compileValue(jsonObj, key, localeValues, delimiters) {
  var value = jsonObj[key];
  if (isString(value)) {
    // 存在国际化
    if (isI18nStr(value, delimiters)) {
      jsonObj[key] = compileStr(value, localeValues[0].values, delimiters);
      if (localeValues.length > 1) {
        // 格式化国际化语言
        var valueLocales = jsonObj[key + 'Locales'] = {};
        localeValues.forEach(function (localValue) {
          valueLocales[localValue.locale] = compileStr(value, localValue.values, delimiters);
        });
      }
    }
  } else {
    compileJsonObj(value, localeValues, delimiters);
  }
}
function compileJsonObj(jsonObj, localeValues, delimiters) {
  walkJsonObj(jsonObj, function (jsonObj, key) {
    compileValue(jsonObj, key, localeValues, delimiters);
  });
  return jsonObj;
}
function walkJsonObj(jsonObj, walk) {
  if (Array.isArray(jsonObj)) {
    for (var i = 0; i < jsonObj.length; i++) {
      if (walk(jsonObj, i)) {
        return true;
      }
    }
  } else if (isObject(jsonObj)) {
    for (var key in jsonObj) {
      if (walk(jsonObj, key)) {
        return true;
      }
    }
  }
  return false;
}
function resolveLocale(locales) {
  return function (locale) {
    if (!locale) {
      return locale;
    }
    locale = normalizeLocale(locale) || locale;
    return resolveLocaleChain(locale).find(function (locale) {
      return locales.indexOf(locale) > -1;
    });
  };
}
function resolveLocaleChain(locale) {
  var chain = [];
  var tokens = locale.split('-');
  while (tokens.length) {
    chain.push(tokens.join('-'));
    tokens.pop();
  }
  return chain;
}
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"], __webpack_require__(/*! ./../../../webpack/buildin/global.js */ 3)))

/***/ }),

/***/ 224:
/*!*********************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-grid/components/uv-grid-item/props.js ***!
  \*********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default = {
  props: _objectSpread({
    // 宫格的name
    name: {
      type: [String, Number, null],
      default: null
    },
    // 背景颜色
    bgColor: {
      type: String,
      default: 'transparent'
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.gridItem)
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 23:
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}
module.exports = _classCallCheck, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 239:
/*!********************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-sticky/components/uv-sticky/props.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default = {
  props: _objectSpread({
    // 吸顶容器到顶部某个距离的时候，进行吸顶，在H5平台，NavigationBar为44px
    offsetTop: {
      type: [String, Number],
      default: 0
    },
    // 自定义导航栏的高度
    customNavHeight: {
      type: [String, Number],
      default: 0
    },
    // 是否禁用吸顶功能
    disabled: {
      type: Boolean,
      default: false
    },
    // 吸顶区域的背景颜色
    bgColor: {
      type: String,
      default: 'transparent'
    },
    // z-index值
    zIndex: {
      type: [String, Number],
      default: ''
    },
    // 列表中的索引值
    index: {
      type: [String, Number],
      default: ''
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.sticky)
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 24:
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/createClass.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ 12);
function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, toPropertyKey(descriptor.key), descriptor);
  }
}
function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  Object.defineProperty(Constructor, "prototype", {
    writable: false
  });
  return Constructor;
}
module.exports = _createClass, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 247:
/*!********************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-search/components/uv-search/props.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default2 = {
  props: _objectSpread({
    value: {
      type: [String, Number],
      default: ''
    },
    modelValue: {
      type: [String, Number],
      default: ''
    },
    // 搜索框形状，round-圆形，square-方形
    shape: {
      type: String,
      default: 'round'
    },
    // 搜索框背景色，默认值#f2f2f2
    bgColor: {
      type: String,
      default: '#f2f2f2'
    },
    // 占位提示文字
    placeholder: {
      type: String,
      default: '请输入关键字'
    },
    // 是否启用清除控件
    clearabled: {
      type: Boolean,
      default: true
    },
    // 是否自动聚焦
    focus: {
      type: Boolean,
      default: false
    },
    // 是否在搜索框右侧显示取消按钮
    showAction: {
      type: Boolean,
      default: true
    },
    // 右边控件的样式
    actionStyle: {
      type: Object,
      default: function _default() {
        return {};
      }
    },
    // 取消按钮文字
    actionText: {
      type: String,
      default: '搜索'
    },
    // 输入框内容对齐方式，可选值为 left|center|right
    inputAlign: {
      type: String,
      default: 'left'
    },
    // input输入框的样式，可以定义文字颜色，大小等，对象形式
    inputStyle: {
      type: Object,
      default: function _default() {
        return {};
      }
    },
    // 是否禁用输入框
    disabled: {
      type: Boolean,
      default: false
    },
    // 边框颜色
    borderColor: {
      type: String,
      default: 'transparent'
    },
    // 搜索图标的颜色，默认同输入框字体颜色
    searchIconColor: {
      type: String,
      default: '#909399'
    },
    // 输入框字体颜色
    color: {
      type: String,
      default: '#606266'
    },
    // placeholder的颜色
    placeholderColor: {
      type: String,
      default: '#909399'
    },
    // 左边输入框的图标，可以为uvui图标名称或图片路径
    searchIcon: {
      type: String,
      default: 'search'
    },
    searchIconSize: {
      type: [Number, String],
      default: 22
    },
    // 组件与其他上下左右元素之间的距离，带单位的字符串形式，如"30px"、"30px 20px"等写法
    margin: {
      type: String,
      default: '0'
    },
    // 开启showAction时，是否在input获取焦点时才显示
    animation: {
      type: Boolean,
      default: false
    },
    // 输入框最大能输入的长度，-1为不限制长度(来自uniapp文档)
    maxlength: {
      type: [String, Number],
      default: -1
    },
    // 搜索框高度，单位px
    height: {
      type: [String, Number],
      default: 32
    },
    // 搜索框左侧文本
    label: {
      type: [String, Number, null],
      default: null
    },
    // 搜索框扩展样式
    boxStyle: {
      type: [String, Object],
      default: function _default() {
        return {};
      }
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.search)
};
exports.default = _default2;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 25:
/*!******************************************************************************************!*\
  !*** ./node_modules/@dcloudio/vue-cli-plugin-uni/packages/mp-vue/dist/mp.runtime.esm.js ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/*!
 * Vue.js v2.6.11
 * (c) 2014-2023 Evan You
 * Released under the MIT License.
 */
/*  */

var emptyObject = Object.freeze({});

// These helpers produce better VM code in JS engines due to their
// explicitness and function inlining.
function isUndef (v) {
  return v === undefined || v === null
}

function isDef (v) {
  return v !== undefined && v !== null
}

function isTrue (v) {
  return v === true
}

function isFalse (v) {
  return v === false
}

/**
 * Check if value is primitive.
 */
function isPrimitive (value) {
  return (
    typeof value === 'string' ||
    typeof value === 'number' ||
    // $flow-disable-line
    typeof value === 'symbol' ||
    typeof value === 'boolean'
  )
}

/**
 * Quick object check - this is primarily used to tell
 * Objects from primitive values when we know the value
 * is a JSON-compliant type.
 */
function isObject (obj) {
  return obj !== null && typeof obj === 'object'
}

/**
 * Get the raw type string of a value, e.g., [object Object].
 */
var _toString = Object.prototype.toString;

function toRawType (value) {
  return _toString.call(value).slice(8, -1)
}

/**
 * Strict object type check. Only returns true
 * for plain JavaScript objects.
 */
function isPlainObject (obj) {
  return _toString.call(obj) === '[object Object]'
}

function isRegExp (v) {
  return _toString.call(v) === '[object RegExp]'
}

/**
 * Check if val is a valid array index.
 */
function isValidArrayIndex (val) {
  var n = parseFloat(String(val));
  return n >= 0 && Math.floor(n) === n && isFinite(val)
}

function isPromise (val) {
  return (
    isDef(val) &&
    typeof val.then === 'function' &&
    typeof val.catch === 'function'
  )
}

/**
 * Convert a value to a string that is actually rendered.
 */
function toString (val) {
  return val == null
    ? ''
    : Array.isArray(val) || (isPlainObject(val) && val.toString === _toString)
      ? JSON.stringify(val, null, 2)
      : String(val)
}

/**
 * Convert an input value to a number for persistence.
 * If the conversion fails, return original string.
 */
function toNumber (val) {
  var n = parseFloat(val);
  return isNaN(n) ? val : n
}

/**
 * Make a map and return a function for checking if a key
 * is in that map.
 */
function makeMap (
  str,
  expectsLowerCase
) {
  var map = Object.create(null);
  var list = str.split(',');
  for (var i = 0; i < list.length; i++) {
    map[list[i]] = true;
  }
  return expectsLowerCase
    ? function (val) { return map[val.toLowerCase()]; }
    : function (val) { return map[val]; }
}

/**
 * Check if a tag is a built-in tag.
 */
var isBuiltInTag = makeMap('slot,component', true);

/**
 * Check if an attribute is a reserved attribute.
 */
var isReservedAttribute = makeMap('key,ref,slot,slot-scope,is');

/**
 * Remove an item from an array.
 */
function remove (arr, item) {
  if (arr.length) {
    var index = arr.indexOf(item);
    if (index > -1) {
      return arr.splice(index, 1)
    }
  }
}

/**
 * Check whether an object has the property.
 */
var hasOwnProperty = Object.prototype.hasOwnProperty;
function hasOwn (obj, key) {
  return hasOwnProperty.call(obj, key)
}

/**
 * Create a cached version of a pure function.
 */
function cached (fn) {
  var cache = Object.create(null);
  return (function cachedFn (str) {
    var hit = cache[str];
    return hit || (cache[str] = fn(str))
  })
}

/**
 * Camelize a hyphen-delimited string.
 */
var camelizeRE = /-(\w)/g;
var camelize = cached(function (str) {
  return str.replace(camelizeRE, function (_, c) { return c ? c.toUpperCase() : ''; })
});

/**
 * Capitalize a string.
 */
var capitalize = cached(function (str) {
  return str.charAt(0).toUpperCase() + str.slice(1)
});

/**
 * Hyphenate a camelCase string.
 */
var hyphenateRE = /\B([A-Z])/g;
var hyphenate = cached(function (str) {
  return str.replace(hyphenateRE, '-$1').toLowerCase()
});

/**
 * Simple bind polyfill for environments that do not support it,
 * e.g., PhantomJS 1.x. Technically, we don't need this anymore
 * since native bind is now performant enough in most browsers.
 * But removing it would mean breaking code that was able to run in
 * PhantomJS 1.x, so this must be kept for backward compatibility.
 */

/* istanbul ignore next */
function polyfillBind (fn, ctx) {
  function boundFn (a) {
    var l = arguments.length;
    return l
      ? l > 1
        ? fn.apply(ctx, arguments)
        : fn.call(ctx, a)
      : fn.call(ctx)
  }

  boundFn._length = fn.length;
  return boundFn
}

function nativeBind (fn, ctx) {
  return fn.bind(ctx)
}

var bind = Function.prototype.bind
  ? nativeBind
  : polyfillBind;

/**
 * Convert an Array-like object to a real Array.
 */
function toArray (list, start) {
  start = start || 0;
  var i = list.length - start;
  var ret = new Array(i);
  while (i--) {
    ret[i] = list[i + start];
  }
  return ret
}

/**
 * Mix properties into target object.
 */
function extend (to, _from) {
  for (var key in _from) {
    to[key] = _from[key];
  }
  return to
}

/**
 * Merge an Array of Objects into a single Object.
 */
function toObject (arr) {
  var res = {};
  for (var i = 0; i < arr.length; i++) {
    if (arr[i]) {
      extend(res, arr[i]);
    }
  }
  return res
}

/* eslint-disable no-unused-vars */

/**
 * Perform no operation.
 * Stubbing args to make Flow happy without leaving useless transpiled code
 * with ...rest (https://flow.org/blog/2017/05/07/Strict-Function-Call-Arity/).
 */
function noop (a, b, c) {}

/**
 * Always return false.
 */
var no = function (a, b, c) { return false; };

/* eslint-enable no-unused-vars */

/**
 * Return the same value.
 */
var identity = function (_) { return _; };

/**
 * Check if two values are loosely equal - that is,
 * if they are plain objects, do they have the same shape?
 */
function looseEqual (a, b) {
  if (a === b) { return true }
  var isObjectA = isObject(a);
  var isObjectB = isObject(b);
  if (isObjectA && isObjectB) {
    try {
      var isArrayA = Array.isArray(a);
      var isArrayB = Array.isArray(b);
      if (isArrayA && isArrayB) {
        return a.length === b.length && a.every(function (e, i) {
          return looseEqual(e, b[i])
        })
      } else if (a instanceof Date && b instanceof Date) {
        return a.getTime() === b.getTime()
      } else if (!isArrayA && !isArrayB) {
        var keysA = Object.keys(a);
        var keysB = Object.keys(b);
        return keysA.length === keysB.length && keysA.every(function (key) {
          return looseEqual(a[key], b[key])
        })
      } else {
        /* istanbul ignore next */
        return false
      }
    } catch (e) {
      /* istanbul ignore next */
      return false
    }
  } else if (!isObjectA && !isObjectB) {
    return String(a) === String(b)
  } else {
    return false
  }
}

/**
 * Return the first index at which a loosely equal value can be
 * found in the array (if value is a plain object, the array must
 * contain an object of the same shape), or -1 if it is not present.
 */
function looseIndexOf (arr, val) {
  for (var i = 0; i < arr.length; i++) {
    if (looseEqual(arr[i], val)) { return i }
  }
  return -1
}

/**
 * Ensure a function is called only once.
 */
function once (fn) {
  var called = false;
  return function () {
    if (!called) {
      called = true;
      fn.apply(this, arguments);
    }
  }
}

var ASSET_TYPES = [
  'component',
  'directive',
  'filter'
];

var LIFECYCLE_HOOKS = [
  'beforeCreate',
  'created',
  'beforeMount',
  'mounted',
  'beforeUpdate',
  'updated',
  'beforeDestroy',
  'destroyed',
  'activated',
  'deactivated',
  'errorCaptured',
  'serverPrefetch'
];

/*  */



var config = ({
  /**
   * Option merge strategies (used in core/util/options)
   */
  // $flow-disable-line
  optionMergeStrategies: Object.create(null),

  /**
   * Whether to suppress warnings.
   */
  silent: false,

  /**
   * Show production mode tip message on boot?
   */
  productionTip: "development" !== 'production',

  /**
   * Whether to enable devtools
   */
  devtools: "development" !== 'production',

  /**
   * Whether to record perf
   */
  performance: false,

  /**
   * Error handler for watcher errors
   */
  errorHandler: null,

  /**
   * Warn handler for watcher warns
   */
  warnHandler: null,

  /**
   * Ignore certain custom elements
   */
  ignoredElements: [],

  /**
   * Custom user key aliases for v-on
   */
  // $flow-disable-line
  keyCodes: Object.create(null),

  /**
   * Check if a tag is reserved so that it cannot be registered as a
   * component. This is platform-dependent and may be overwritten.
   */
  isReservedTag: no,

  /**
   * Check if an attribute is reserved so that it cannot be used as a component
   * prop. This is platform-dependent and may be overwritten.
   */
  isReservedAttr: no,

  /**
   * Check if a tag is an unknown element.
   * Platform-dependent.
   */
  isUnknownElement: no,

  /**
   * Get the namespace of an element
   */
  getTagNamespace: noop,

  /**
   * Parse the real tag name for the specific platform.
   */
  parsePlatformTagName: identity,

  /**
   * Check if an attribute must be bound using property, e.g. value
   * Platform-dependent.
   */
  mustUseProp: no,

  /**
   * Perform updates asynchronously. Intended to be used by Vue Test Utils
   * This will significantly reduce performance if set to false.
   */
  async: true,

  /**
   * Exposed for legacy reasons
   */
  _lifecycleHooks: LIFECYCLE_HOOKS
});

/*  */

/**
 * unicode letters used for parsing html tags, component names and property paths.
 * using https://www.w3.org/TR/html53/semantics-scripting.html#potentialcustomelementname
 * skipping \u10000-\uEFFFF due to it freezing up PhantomJS
 */
var unicodeRegExp = /a-zA-Z\u00B7\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u037D\u037F-\u1FFF\u200C-\u200D\u203F-\u2040\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD/;

/**
 * Check if a string starts with $ or _
 */
function isReserved (str) {
  var c = (str + '').charCodeAt(0);
  return c === 0x24 || c === 0x5F
}

/**
 * Define a property.
 */
function def (obj, key, val, enumerable) {
  Object.defineProperty(obj, key, {
    value: val,
    enumerable: !!enumerable,
    writable: true,
    configurable: true
  });
}

/**
 * Parse simple path.
 */
var bailRE = new RegExp(("[^" + (unicodeRegExp.source) + ".$_\\d]"));
function parsePath (path) {
  if (bailRE.test(path)) {
    return
  }
  var segments = path.split('.');
  return function (obj) {
    for (var i = 0; i < segments.length; i++) {
      if (!obj) { return }
      obj = obj[segments[i]];
    }
    return obj
  }
}

/*  */

// can we use __proto__?
var hasProto = '__proto__' in {};

// Browser environment sniffing
var inBrowser = typeof window !== 'undefined';
var inWeex = typeof WXEnvironment !== 'undefined' && !!WXEnvironment.platform;
var weexPlatform = inWeex && WXEnvironment.platform.toLowerCase();
var UA = inBrowser && window.navigator.userAgent.toLowerCase();
var isIE = UA && /msie|trident/.test(UA);
var isIE9 = UA && UA.indexOf('msie 9.0') > 0;
var isEdge = UA && UA.indexOf('edge/') > 0;
var isAndroid = (UA && UA.indexOf('android') > 0) || (weexPlatform === 'android');
var isIOS = (UA && /iphone|ipad|ipod|ios/.test(UA)) || (weexPlatform === 'ios');
var isChrome = UA && /chrome\/\d+/.test(UA) && !isEdge;
var isPhantomJS = UA && /phantomjs/.test(UA);
var isFF = UA && UA.match(/firefox\/(\d+)/);

// Firefox has a "watch" function on Object.prototype...
var nativeWatch = ({}).watch;
if (inBrowser) {
  try {
    var opts = {};
    Object.defineProperty(opts, 'passive', ({
      get: function get () {
      }
    })); // https://github.com/facebook/flow/issues/285
    window.addEventListener('test-passive', null, opts);
  } catch (e) {}
}

// this needs to be lazy-evaled because vue may be required before
// vue-server-renderer can set VUE_ENV
var _isServer;
var isServerRendering = function () {
  if (_isServer === undefined) {
    /* istanbul ignore if */
    if (!inBrowser && !inWeex && typeof global !== 'undefined') {
      // detect presence of vue-server-renderer and avoid
      // Webpack shimming the process
      _isServer = global['process'] && global['process'].env.VUE_ENV === 'server';
    } else {
      _isServer = false;
    }
  }
  return _isServer
};

// detect devtools
var devtools = inBrowser && window.__VUE_DEVTOOLS_GLOBAL_HOOK__;

/* istanbul ignore next */
function isNative (Ctor) {
  return typeof Ctor === 'function' && /native code/.test(Ctor.toString())
}

var hasSymbol =
  typeof Symbol !== 'undefined' && isNative(Symbol) &&
  typeof Reflect !== 'undefined' && isNative(Reflect.ownKeys);

var _Set;
/* istanbul ignore if */ // $flow-disable-line
if (typeof Set !== 'undefined' && isNative(Set)) {
  // use native Set when available.
  _Set = Set;
} else {
  // a non-standard Set polyfill that only works with primitive keys.
  _Set = /*@__PURE__*/(function () {
    function Set () {
      this.set = Object.create(null);
    }
    Set.prototype.has = function has (key) {
      return this.set[key] === true
    };
    Set.prototype.add = function add (key) {
      this.set[key] = true;
    };
    Set.prototype.clear = function clear () {
      this.set = Object.create(null);
    };

    return Set;
  }());
}

/*  */

var warn = noop;
var tip = noop;
var generateComponentTrace = (noop); // work around flow check
var formatComponentName = (noop);

if (true) {
  var hasConsole = typeof console !== 'undefined';
  var classifyRE = /(?:^|[-_])(\w)/g;
  var classify = function (str) { return str
    .replace(classifyRE, function (c) { return c.toUpperCase(); })
    .replace(/[-_]/g, ''); };

  warn = function (msg, vm) {
    var trace = vm ? generateComponentTrace(vm) : '';

    if (config.warnHandler) {
      config.warnHandler.call(null, msg, vm, trace);
    } else if (hasConsole && (!config.silent)) {
      console.error(("[Vue warn]: " + msg + trace));
    }
  };

  tip = function (msg, vm) {
    if (hasConsole && (!config.silent)) {
      console.warn("[Vue tip]: " + msg + (
        vm ? generateComponentTrace(vm) : ''
      ));
    }
  };

  formatComponentName = function (vm, includeFile) {
    if (vm.$root === vm) {
      if (vm.$options && vm.$options.__file) { // fixed by xxxxxx
        return ('') + vm.$options.__file
      }
      return '<Root>'
    }
    var options = typeof vm === 'function' && vm.cid != null
      ? vm.options
      : vm._isVue
        ? vm.$options || vm.constructor.options
        : vm;
    var name = options.name || options._componentTag;
    var file = options.__file;
    if (!name && file) {
      var match = file.match(/([^/\\]+)\.vue$/);
      name = match && match[1];
    }

    return (
      (name ? ("<" + (classify(name)) + ">") : "<Anonymous>") +
      (file && includeFile !== false ? (" at " + file) : '')
    )
  };

  var repeat = function (str, n) {
    var res = '';
    while (n) {
      if (n % 2 === 1) { res += str; }
      if (n > 1) { str += str; }
      n >>= 1;
    }
    return res
  };

  generateComponentTrace = function (vm) {
    if (vm._isVue && vm.$parent) {
      var tree = [];
      var currentRecursiveSequence = 0;
      while (vm && vm.$options.name !== 'PageBody') {
        if (tree.length > 0) {
          var last = tree[tree.length - 1];
          if (last.constructor === vm.constructor) {
            currentRecursiveSequence++;
            vm = vm.$parent;
            continue
          } else if (currentRecursiveSequence > 0) {
            tree[tree.length - 1] = [last, currentRecursiveSequence];
            currentRecursiveSequence = 0;
          }
        }
        !vm.$options.isReserved && tree.push(vm);
        vm = vm.$parent;
      }
      return '\n\nfound in\n\n' + tree
        .map(function (vm, i) { return ("" + (i === 0 ? '---> ' : repeat(' ', 5 + i * 2)) + (Array.isArray(vm)
            ? ((formatComponentName(vm[0])) + "... (" + (vm[1]) + " recursive calls)")
            : formatComponentName(vm))); })
        .join('\n')
    } else {
      return ("\n\n(found in " + (formatComponentName(vm)) + ")")
    }
  };
}

/*  */

var uid = 0;

/**
 * A dep is an observable that can have multiple
 * directives subscribing to it.
 */
var Dep = function Dep () {
  this.id = uid++;
  this.subs = [];
};

Dep.prototype.addSub = function addSub (sub) {
  this.subs.push(sub);
};

Dep.prototype.removeSub = function removeSub (sub) {
  remove(this.subs, sub);
};

Dep.prototype.depend = function depend () {
  if (Dep.SharedObject.target) {
    Dep.SharedObject.target.addDep(this);
  }
};

Dep.prototype.notify = function notify () {
  // stabilize the subscriber list first
  var subs = this.subs.slice();
  if ( true && !config.async) {
    // subs aren't sorted in scheduler if not running async
    // we need to sort them now to make sure they fire in correct
    // order
    subs.sort(function (a, b) { return a.id - b.id; });
  }
  for (var i = 0, l = subs.length; i < l; i++) {
    subs[i].update();
  }
};

// The current target watcher being evaluated.
// This is globally unique because only one watcher
// can be evaluated at a time.
// fixed by xxxxxx (nvue shared vuex)
/* eslint-disable no-undef */
Dep.SharedObject = {};
Dep.SharedObject.target = null;
Dep.SharedObject.targetStack = [];

function pushTarget (target) {
  Dep.SharedObject.targetStack.push(target);
  Dep.SharedObject.target = target;
  Dep.target = target;
}

function popTarget () {
  Dep.SharedObject.targetStack.pop();
  Dep.SharedObject.target = Dep.SharedObject.targetStack[Dep.SharedObject.targetStack.length - 1];
  Dep.target = Dep.SharedObject.target;
}

/*  */

var VNode = function VNode (
  tag,
  data,
  children,
  text,
  elm,
  context,
  componentOptions,
  asyncFactory
) {
  this.tag = tag;
  this.data = data;
  this.children = children;
  this.text = text;
  this.elm = elm;
  this.ns = undefined;
  this.context = context;
  this.fnContext = undefined;
  this.fnOptions = undefined;
  this.fnScopeId = undefined;
  this.key = data && data.key;
  this.componentOptions = componentOptions;
  this.componentInstance = undefined;
  this.parent = undefined;
  this.raw = false;
  this.isStatic = false;
  this.isRootInsert = true;
  this.isComment = false;
  this.isCloned = false;
  this.isOnce = false;
  this.asyncFactory = asyncFactory;
  this.asyncMeta = undefined;
  this.isAsyncPlaceholder = false;
};

var prototypeAccessors = { child: { configurable: true } };

// DEPRECATED: alias for componentInstance for backwards compat.
/* istanbul ignore next */
prototypeAccessors.child.get = function () {
  return this.componentInstance
};

Object.defineProperties( VNode.prototype, prototypeAccessors );

var createEmptyVNode = function (text) {
  if ( text === void 0 ) text = '';

  var node = new VNode();
  node.text = text;
  node.isComment = true;
  return node
};

function createTextVNode (val) {
  return new VNode(undefined, undefined, undefined, String(val))
}

// optimized shallow clone
// used for static nodes and slot nodes because they may be reused across
// multiple renders, cloning them avoids errors when DOM manipulations rely
// on their elm reference.
function cloneVNode (vnode) {
  var cloned = new VNode(
    vnode.tag,
    vnode.data,
    // #7975
    // clone children array to avoid mutating original in case of cloning
    // a child.
    vnode.children && vnode.children.slice(),
    vnode.text,
    vnode.elm,
    vnode.context,
    vnode.componentOptions,
    vnode.asyncFactory
  );
  cloned.ns = vnode.ns;
  cloned.isStatic = vnode.isStatic;
  cloned.key = vnode.key;
  cloned.isComment = vnode.isComment;
  cloned.fnContext = vnode.fnContext;
  cloned.fnOptions = vnode.fnOptions;
  cloned.fnScopeId = vnode.fnScopeId;
  cloned.asyncMeta = vnode.asyncMeta;
  cloned.isCloned = true;
  return cloned
}

/*
 * not type checking this file because flow doesn't play well with
 * dynamically accessing methods on Array prototype
 */

var arrayProto = Array.prototype;
var arrayMethods = Object.create(arrayProto);

var methodsToPatch = [
  'push',
  'pop',
  'shift',
  'unshift',
  'splice',
  'sort',
  'reverse'
];

/**
 * Intercept mutating methods and emit events
 */
methodsToPatch.forEach(function (method) {
  // cache original method
  var original = arrayProto[method];
  def(arrayMethods, method, function mutator () {
    var args = [], len = arguments.length;
    while ( len-- ) args[ len ] = arguments[ len ];

    var result = original.apply(this, args);
    var ob = this.__ob__;
    var inserted;
    switch (method) {
      case 'push':
      case 'unshift':
        inserted = args;
        break
      case 'splice':
        inserted = args.slice(2);
        break
    }
    if (inserted) { ob.observeArray(inserted); }
    // notify change
    ob.dep.notify();
    return result
  });
});

/*  */

var arrayKeys = Object.getOwnPropertyNames(arrayMethods);

/**
 * In some cases we may want to disable observation inside a component's
 * update computation.
 */
var shouldObserve = true;

function toggleObserving (value) {
  shouldObserve = value;
}

/**
 * Observer class that is attached to each observed
 * object. Once attached, the observer converts the target
 * object's property keys into getter/setters that
 * collect dependencies and dispatch updates.
 */
var Observer = function Observer (value) {
  this.value = value;
  this.dep = new Dep();
  this.vmCount = 0;
  def(value, '__ob__', this);
  if (Array.isArray(value)) {
    if (hasProto) {
      {// fixed by xxxxxx 微信小程序使用 plugins 之后，数组方法被直接挂载到了数组对象上，需要执行 copyAugment 逻辑
        if(value.push !== value.__proto__.push){
          copyAugment(value, arrayMethods, arrayKeys);
        } else {
          protoAugment(value, arrayMethods);
        }
      }
    } else {
      copyAugment(value, arrayMethods, arrayKeys);
    }
    this.observeArray(value);
  } else {
    this.walk(value);
  }
};

/**
 * Walk through all properties and convert them into
 * getter/setters. This method should only be called when
 * value type is Object.
 */
Observer.prototype.walk = function walk (obj) {
  var keys = Object.keys(obj);
  for (var i = 0; i < keys.length; i++) {
    defineReactive$$1(obj, keys[i]);
  }
};

/**
 * Observe a list of Array items.
 */
Observer.prototype.observeArray = function observeArray (items) {
  for (var i = 0, l = items.length; i < l; i++) {
    observe(items[i]);
  }
};

// helpers

/**
 * Augment a target Object or Array by intercepting
 * the prototype chain using __proto__
 */
function protoAugment (target, src) {
  /* eslint-disable no-proto */
  target.__proto__ = src;
  /* eslint-enable no-proto */
}

/**
 * Augment a target Object or Array by defining
 * hidden properties.
 */
/* istanbul ignore next */
function copyAugment (target, src, keys) {
  for (var i = 0, l = keys.length; i < l; i++) {
    var key = keys[i];
    def(target, key, src[key]);
  }
}

/**
 * Attempt to create an observer instance for a value,
 * returns the new observer if successfully observed,
 * or the existing observer if the value already has one.
 */
function observe (value, asRootData) {
  if (!isObject(value) || value instanceof VNode) {
    return
  }
  var ob;
  if (hasOwn(value, '__ob__') && value.__ob__ instanceof Observer) {
    ob = value.__ob__;
  } else if (
    shouldObserve &&
    !isServerRendering() &&
    (Array.isArray(value) || isPlainObject(value)) &&
    Object.isExtensible(value) &&
    !value._isVue &&
    !value.__v_isMPComponent
  ) {
    ob = new Observer(value);
  }
  if (asRootData && ob) {
    ob.vmCount++;
  }
  return ob
}

/**
 * Define a reactive property on an Object.
 */
function defineReactive$$1 (
  obj,
  key,
  val,
  customSetter,
  shallow
) {
  var dep = new Dep();

  var property = Object.getOwnPropertyDescriptor(obj, key);
  if (property && property.configurable === false) {
    return
  }

  // cater for pre-defined getter/setters
  var getter = property && property.get;
  var setter = property && property.set;
  if ((!getter || setter) && arguments.length === 2) {
    val = obj[key];
  }

  var childOb = !shallow && observe(val);
  Object.defineProperty(obj, key, {
    enumerable: true,
    configurable: true,
    get: function reactiveGetter () {
      var value = getter ? getter.call(obj) : val;
      if (Dep.SharedObject.target) { // fixed by xxxxxx
        dep.depend();
        if (childOb) {
          childOb.dep.depend();
          if (Array.isArray(value)) {
            dependArray(value);
          }
        }
      }
      return value
    },
    set: function reactiveSetter (newVal) {
      var value = getter ? getter.call(obj) : val;
      /* eslint-disable no-self-compare */
      if (newVal === value || (newVal !== newVal && value !== value)) {
        return
      }
      /* eslint-enable no-self-compare */
      if ( true && customSetter) {
        customSetter();
      }
      // #7981: for accessor properties without setter
      if (getter && !setter) { return }
      if (setter) {
        setter.call(obj, newVal);
      } else {
        val = newVal;
      }
      childOb = !shallow && observe(newVal);
      dep.notify();
    }
  });
}

/**
 * Set a property on an object. Adds the new property and
 * triggers change notification if the property doesn't
 * already exist.
 */
function set (target, key, val) {
  if ( true &&
    (isUndef(target) || isPrimitive(target))
  ) {
    warn(("Cannot set reactive property on undefined, null, or primitive value: " + ((target))));
  }
  if (Array.isArray(target) && isValidArrayIndex(key)) {
    target.length = Math.max(target.length, key);
    target.splice(key, 1, val);
    return val
  }
  if (key in target && !(key in Object.prototype)) {
    target[key] = val;
    return val
  }
  var ob = (target).__ob__;
  if (target._isVue || (ob && ob.vmCount)) {
     true && warn(
      'Avoid adding reactive properties to a Vue instance or its root $data ' +
      'at runtime - declare it upfront in the data option.'
    );
    return val
  }
  if (!ob) {
    target[key] = val;
    return val
  }
  defineReactive$$1(ob.value, key, val);
  ob.dep.notify();
  return val
}

/**
 * Delete a property and trigger change if necessary.
 */
function del (target, key) {
  if ( true &&
    (isUndef(target) || isPrimitive(target))
  ) {
    warn(("Cannot delete reactive property on undefined, null, or primitive value: " + ((target))));
  }
  if (Array.isArray(target) && isValidArrayIndex(key)) {
    target.splice(key, 1);
    return
  }
  var ob = (target).__ob__;
  if (target._isVue || (ob && ob.vmCount)) {
     true && warn(
      'Avoid deleting properties on a Vue instance or its root $data ' +
      '- just set it to null.'
    );
    return
  }
  if (!hasOwn(target, key)) {
    return
  }
  delete target[key];
  if (!ob) {
    return
  }
  ob.dep.notify();
}

/**
 * Collect dependencies on array elements when the array is touched, since
 * we cannot intercept array element access like property getters.
 */
function dependArray (value) {
  for (var e = (void 0), i = 0, l = value.length; i < l; i++) {
    e = value[i];
    e && e.__ob__ && e.__ob__.dep.depend();
    if (Array.isArray(e)) {
      dependArray(e);
    }
  }
}

/*  */

/**
 * Option overwriting strategies are functions that handle
 * how to merge a parent option value and a child option
 * value into the final value.
 */
var strats = config.optionMergeStrategies;

/**
 * Options with restrictions
 */
if (true) {
  strats.el = strats.propsData = function (parent, child, vm, key) {
    if (!vm) {
      warn(
        "option \"" + key + "\" can only be used during instance " +
        'creation with the `new` keyword.'
      );
    }
    return defaultStrat(parent, child)
  };
}

/**
 * Helper that recursively merges two data objects together.
 */
function mergeData (to, from) {
  if (!from) { return to }
  var key, toVal, fromVal;

  var keys = hasSymbol
    ? Reflect.ownKeys(from)
    : Object.keys(from);

  for (var i = 0; i < keys.length; i++) {
    key = keys[i];
    // in case the object is already observed...
    if (key === '__ob__') { continue }
    toVal = to[key];
    fromVal = from[key];
    if (!hasOwn(to, key)) {
      set(to, key, fromVal);
    } else if (
      toVal !== fromVal &&
      isPlainObject(toVal) &&
      isPlainObject(fromVal)
    ) {
      mergeData(toVal, fromVal);
    }
  }
  return to
}

/**
 * Data
 */
function mergeDataOrFn (
  parentVal,
  childVal,
  vm
) {
  if (!vm) {
    // in a Vue.extend merge, both should be functions
    if (!childVal) {
      return parentVal
    }
    if (!parentVal) {
      return childVal
    }
    // when parentVal & childVal are both present,
    // we need to return a function that returns the
    // merged result of both functions... no need to
    // check if parentVal is a function here because
    // it has to be a function to pass previous merges.
    return function mergedDataFn () {
      return mergeData(
        typeof childVal === 'function' ? childVal.call(this, this) : childVal,
        typeof parentVal === 'function' ? parentVal.call(this, this) : parentVal
      )
    }
  } else {
    return function mergedInstanceDataFn () {
      // instance merge
      var instanceData = typeof childVal === 'function'
        ? childVal.call(vm, vm)
        : childVal;
      var defaultData = typeof parentVal === 'function'
        ? parentVal.call(vm, vm)
        : parentVal;
      if (instanceData) {
        return mergeData(instanceData, defaultData)
      } else {
        return defaultData
      }
    }
  }
}

strats.data = function (
  parentVal,
  childVal,
  vm
) {
  if (!vm) {
    if (childVal && typeof childVal !== 'function') {
       true && warn(
        'The "data" option should be a function ' +
        'that returns a per-instance value in component ' +
        'definitions.',
        vm
      );

      return parentVal
    }
    return mergeDataOrFn(parentVal, childVal)
  }

  return mergeDataOrFn(parentVal, childVal, vm)
};

/**
 * Hooks and props are merged as arrays.
 */
function mergeHook (
  parentVal,
  childVal
) {
  var res = childVal
    ? parentVal
      ? parentVal.concat(childVal)
      : Array.isArray(childVal)
        ? childVal
        : [childVal]
    : parentVal;
  return res
    ? dedupeHooks(res)
    : res
}

function dedupeHooks (hooks) {
  var res = [];
  for (var i = 0; i < hooks.length; i++) {
    if (res.indexOf(hooks[i]) === -1) {
      res.push(hooks[i]);
    }
  }
  return res
}

LIFECYCLE_HOOKS.forEach(function (hook) {
  strats[hook] = mergeHook;
});

/**
 * Assets
 *
 * When a vm is present (instance creation), we need to do
 * a three-way merge between constructor options, instance
 * options and parent options.
 */
function mergeAssets (
  parentVal,
  childVal,
  vm,
  key
) {
  var res = Object.create(parentVal || null);
  if (childVal) {
     true && assertObjectType(key, childVal, vm);
    return extend(res, childVal)
  } else {
    return res
  }
}

ASSET_TYPES.forEach(function (type) {
  strats[type + 's'] = mergeAssets;
});

/**
 * Watchers.
 *
 * Watchers hashes should not overwrite one
 * another, so we merge them as arrays.
 */
strats.watch = function (
  parentVal,
  childVal,
  vm,
  key
) {
  // work around Firefox's Object.prototype.watch...
  if (parentVal === nativeWatch) { parentVal = undefined; }
  if (childVal === nativeWatch) { childVal = undefined; }
  /* istanbul ignore if */
  if (!childVal) { return Object.create(parentVal || null) }
  if (true) {
    assertObjectType(key, childVal, vm);
  }
  if (!parentVal) { return childVal }
  var ret = {};
  extend(ret, parentVal);
  for (var key$1 in childVal) {
    var parent = ret[key$1];
    var child = childVal[key$1];
    if (parent && !Array.isArray(parent)) {
      parent = [parent];
    }
    ret[key$1] = parent
      ? parent.concat(child)
      : Array.isArray(child) ? child : [child];
  }
  return ret
};

/**
 * Other object hashes.
 */
strats.props =
strats.methods =
strats.inject =
strats.computed = function (
  parentVal,
  childVal,
  vm,
  key
) {
  if (childVal && "development" !== 'production') {
    assertObjectType(key, childVal, vm);
  }
  if (!parentVal) { return childVal }
  var ret = Object.create(null);
  extend(ret, parentVal);
  if (childVal) { extend(ret, childVal); }
  return ret
};
strats.provide = mergeDataOrFn;

/**
 * Default strategy.
 */
var defaultStrat = function (parentVal, childVal) {
  return childVal === undefined
    ? parentVal
    : childVal
};

/**
 * Validate component names
 */
function checkComponents (options) {
  for (var key in options.components) {
    validateComponentName(key);
  }
}

function validateComponentName (name) {
  if (!new RegExp(("^[a-zA-Z][\\-\\.0-9_" + (unicodeRegExp.source) + "]*$")).test(name)) {
    warn(
      'Invalid component name: "' + name + '". Component names ' +
      'should conform to valid custom element name in html5 specification.'
    );
  }
  if (isBuiltInTag(name) || config.isReservedTag(name)) {
    warn(
      'Do not use built-in or reserved HTML elements as component ' +
      'id: ' + name
    );
  }
}

/**
 * Ensure all props option syntax are normalized into the
 * Object-based format.
 */
function normalizeProps (options, vm) {
  var props = options.props;
  if (!props) { return }
  var res = {};
  var i, val, name;
  if (Array.isArray(props)) {
    i = props.length;
    while (i--) {
      val = props[i];
      if (typeof val === 'string') {
        name = camelize(val);
        res[name] = { type: null };
      } else if (true) {
        warn('props must be strings when using array syntax.');
      }
    }
  } else if (isPlainObject(props)) {
    for (var key in props) {
      val = props[key];
      name = camelize(key);
      res[name] = isPlainObject(val)
        ? val
        : { type: val };
    }
  } else if (true) {
    warn(
      "Invalid value for option \"props\": expected an Array or an Object, " +
      "but got " + (toRawType(props)) + ".",
      vm
    );
  }
  options.props = res;
}

/**
 * Normalize all injections into Object-based format
 */
function normalizeInject (options, vm) {
  var inject = options.inject;
  if (!inject) { return }
  var normalized = options.inject = {};
  if (Array.isArray(inject)) {
    for (var i = 0; i < inject.length; i++) {
      normalized[inject[i]] = { from: inject[i] };
    }
  } else if (isPlainObject(inject)) {
    for (var key in inject) {
      var val = inject[key];
      normalized[key] = isPlainObject(val)
        ? extend({ from: key }, val)
        : { from: val };
    }
  } else if (true) {
    warn(
      "Invalid value for option \"inject\": expected an Array or an Object, " +
      "but got " + (toRawType(inject)) + ".",
      vm
    );
  }
}

/**
 * Normalize raw function directives into object format.
 */
function normalizeDirectives (options) {
  var dirs = options.directives;
  if (dirs) {
    for (var key in dirs) {
      var def$$1 = dirs[key];
      if (typeof def$$1 === 'function') {
        dirs[key] = { bind: def$$1, update: def$$1 };
      }
    }
  }
}

function assertObjectType (name, value, vm) {
  if (!isPlainObject(value)) {
    warn(
      "Invalid value for option \"" + name + "\": expected an Object, " +
      "but got " + (toRawType(value)) + ".",
      vm
    );
  }
}

/**
 * Merge two option objects into a new one.
 * Core utility used in both instantiation and inheritance.
 */
function mergeOptions (
  parent,
  child,
  vm
) {
  if (true) {
    checkComponents(child);
  }

  if (typeof child === 'function') {
    child = child.options;
  }

  normalizeProps(child, vm);
  normalizeInject(child, vm);
  normalizeDirectives(child);

  // Apply extends and mixins on the child options,
  // but only if it is a raw options object that isn't
  // the result of another mergeOptions call.
  // Only merged options has the _base property.
  if (!child._base) {
    if (child.extends) {
      parent = mergeOptions(parent, child.extends, vm);
    }
    if (child.mixins) {
      for (var i = 0, l = child.mixins.length; i < l; i++) {
        parent = mergeOptions(parent, child.mixins[i], vm);
      }
    }
  }

  var options = {};
  var key;
  for (key in parent) {
    mergeField(key);
  }
  for (key in child) {
    if (!hasOwn(parent, key)) {
      mergeField(key);
    }
  }
  function mergeField (key) {
    var strat = strats[key] || defaultStrat;
    options[key] = strat(parent[key], child[key], vm, key);
  }
  return options
}

/**
 * Resolve an asset.
 * This function is used because child instances need access
 * to assets defined in its ancestor chain.
 */
function resolveAsset (
  options,
  type,
  id,
  warnMissing
) {
  /* istanbul ignore if */
  if (typeof id !== 'string') {
    return
  }
  var assets = options[type];
  // check local registration variations first
  if (hasOwn(assets, id)) { return assets[id] }
  var camelizedId = camelize(id);
  if (hasOwn(assets, camelizedId)) { return assets[camelizedId] }
  var PascalCaseId = capitalize(camelizedId);
  if (hasOwn(assets, PascalCaseId)) { return assets[PascalCaseId] }
  // fallback to prototype chain
  var res = assets[id] || assets[camelizedId] || assets[PascalCaseId];
  if ( true && warnMissing && !res) {
    warn(
      'Failed to resolve ' + type.slice(0, -1) + ': ' + id,
      options
    );
  }
  return res
}

/*  */



function validateProp (
  key,
  propOptions,
  propsData,
  vm
) {
  var prop = propOptions[key];
  var absent = !hasOwn(propsData, key);
  var value = propsData[key];
  // boolean casting
  var booleanIndex = getTypeIndex(Boolean, prop.type);
  if (booleanIndex > -1) {
    if (absent && !hasOwn(prop, 'default')) {
      value = false;
    } else if (value === '' || value === hyphenate(key)) {
      // only cast empty string / same name to boolean if
      // boolean has higher priority
      var stringIndex = getTypeIndex(String, prop.type);
      if (stringIndex < 0 || booleanIndex < stringIndex) {
        value = true;
      }
    }
  }
  // check default value
  if (value === undefined) {
    value = getPropDefaultValue(vm, prop, key);
    // since the default value is a fresh copy,
    // make sure to observe it.
    var prevShouldObserve = shouldObserve;
    toggleObserving(true);
    observe(value);
    toggleObserving(prevShouldObserve);
  }
  if (
    true
  ) {
    assertProp(prop, key, value, vm, absent);
  }
  return value
}

/**
 * Get the default value of a prop.
 */
function getPropDefaultValue (vm, prop, key) {
  // no default, return undefined
  if (!hasOwn(prop, 'default')) {
    return undefined
  }
  var def = prop.default;
  // warn against non-factory defaults for Object & Array
  if ( true && isObject(def)) {
    warn(
      'Invalid default value for prop "' + key + '": ' +
      'Props with type Object/Array must use a factory function ' +
      'to return the default value.',
      vm
    );
  }
  // the raw prop value was also undefined from previous render,
  // return previous default value to avoid unnecessary watcher trigger
  if (vm && vm.$options.propsData &&
    vm.$options.propsData[key] === undefined &&
    vm._props[key] !== undefined
  ) {
    return vm._props[key]
  }
  // call factory function for non-Function types
  // a value is Function if its prototype is function even across different execution context
  return typeof def === 'function' && getType(prop.type) !== 'Function'
    ? def.call(vm)
    : def
}

/**
 * Assert whether a prop is valid.
 */
function assertProp (
  prop,
  name,
  value,
  vm,
  absent
) {
  if (prop.required && absent) {
    warn(
      'Missing required prop: "' + name + '"',
      vm
    );
    return
  }
  if (value == null && !prop.required) {
    return
  }
  var type = prop.type;
  var valid = !type || type === true;
  var expectedTypes = [];
  if (type) {
    if (!Array.isArray(type)) {
      type = [type];
    }
    for (var i = 0; i < type.length && !valid; i++) {
      var assertedType = assertType(value, type[i]);
      expectedTypes.push(assertedType.expectedType || '');
      valid = assertedType.valid;
    }
  }

  if (!valid) {
    warn(
      getInvalidTypeMessage(name, value, expectedTypes),
      vm
    );
    return
  }
  var validator = prop.validator;
  if (validator) {
    if (!validator(value)) {
      warn(
        'Invalid prop: custom validator check failed for prop "' + name + '".',
        vm
      );
    }
  }
}

var simpleCheckRE = /^(String|Number|Boolean|Function|Symbol)$/;

function assertType (value, type) {
  var valid;
  var expectedType = getType(type);
  if (simpleCheckRE.test(expectedType)) {
    var t = typeof value;
    valid = t === expectedType.toLowerCase();
    // for primitive wrapper objects
    if (!valid && t === 'object') {
      valid = value instanceof type;
    }
  } else if (expectedType === 'Object') {
    valid = isPlainObject(value);
  } else if (expectedType === 'Array') {
    valid = Array.isArray(value);
  } else {
    valid = value instanceof type;
  }
  return {
    valid: valid,
    expectedType: expectedType
  }
}

/**
 * Use function string name to check built-in types,
 * because a simple equality check will fail when running
 * across different vms / iframes.
 */
function getType (fn) {
  var match = fn && fn.toString().match(/^\s*function (\w+)/);
  return match ? match[1] : ''
}

function isSameType (a, b) {
  return getType(a) === getType(b)
}

function getTypeIndex (type, expectedTypes) {
  if (!Array.isArray(expectedTypes)) {
    return isSameType(expectedTypes, type) ? 0 : -1
  }
  for (var i = 0, len = expectedTypes.length; i < len; i++) {
    if (isSameType(expectedTypes[i], type)) {
      return i
    }
  }
  return -1
}

function getInvalidTypeMessage (name, value, expectedTypes) {
  var message = "Invalid prop: type check failed for prop \"" + name + "\"." +
    " Expected " + (expectedTypes.map(capitalize).join(', '));
  var expectedType = expectedTypes[0];
  var receivedType = toRawType(value);
  var expectedValue = styleValue(value, expectedType);
  var receivedValue = styleValue(value, receivedType);
  // check if we need to specify expected value
  if (expectedTypes.length === 1 &&
      isExplicable(expectedType) &&
      !isBoolean(expectedType, receivedType)) {
    message += " with value " + expectedValue;
  }
  message += ", got " + receivedType + " ";
  // check if we need to specify received value
  if (isExplicable(receivedType)) {
    message += "with value " + receivedValue + ".";
  }
  return message
}

function styleValue (value, type) {
  if (type === 'String') {
    return ("\"" + value + "\"")
  } else if (type === 'Number') {
    return ("" + (Number(value)))
  } else {
    return ("" + value)
  }
}

function isExplicable (value) {
  var explicitTypes = ['string', 'number', 'boolean'];
  return explicitTypes.some(function (elem) { return value.toLowerCase() === elem; })
}

function isBoolean () {
  var args = [], len = arguments.length;
  while ( len-- ) args[ len ] = arguments[ len ];

  return args.some(function (elem) { return elem.toLowerCase() === 'boolean'; })
}

/*  */

function handleError (err, vm, info) {
  // Deactivate deps tracking while processing error handler to avoid possible infinite rendering.
  // See: https://github.com/vuejs/vuex/issues/1505
  pushTarget();
  try {
    if (vm) {
      var cur = vm;
      while ((cur = cur.$parent)) {
        var hooks = cur.$options.errorCaptured;
        if (hooks) {
          for (var i = 0; i < hooks.length; i++) {
            try {
              var capture = hooks[i].call(cur, err, vm, info) === false;
              if (capture) { return }
            } catch (e) {
              globalHandleError(e, cur, 'errorCaptured hook');
            }
          }
        }
      }
    }
    globalHandleError(err, vm, info);
  } finally {
    popTarget();
  }
}

function invokeWithErrorHandling (
  handler,
  context,
  args,
  vm,
  info
) {
  var res;
  try {
    res = args ? handler.apply(context, args) : handler.call(context);
    if (res && !res._isVue && isPromise(res) && !res._handled) {
      res.catch(function (e) { return handleError(e, vm, info + " (Promise/async)"); });
      // issue #9511
      // avoid catch triggering multiple times when nested calls
      res._handled = true;
    }
  } catch (e) {
    handleError(e, vm, info);
  }
  return res
}

function globalHandleError (err, vm, info) {
  if (config.errorHandler) {
    try {
      return config.errorHandler.call(null, err, vm, info)
    } catch (e) {
      // if the user intentionally throws the original error in the handler,
      // do not log it twice
      if (e !== err) {
        logError(e, null, 'config.errorHandler');
      }
    }
  }
  logError(err, vm, info);
}

function logError (err, vm, info) {
  if (true) {
    warn(("Error in " + info + ": \"" + (err.toString()) + "\""), vm);
  }
  /* istanbul ignore else */
  if ((inBrowser || inWeex) && typeof console !== 'undefined') {
    console.error(err);
  } else {
    throw err
  }
}

/*  */

var callbacks = [];
var pending = false;

function flushCallbacks () {
  pending = false;
  var copies = callbacks.slice(0);
  callbacks.length = 0;
  for (var i = 0; i < copies.length; i++) {
    copies[i]();
  }
}

// Here we have async deferring wrappers using microtasks.
// In 2.5 we used (macro) tasks (in combination with microtasks).
// However, it has subtle problems when state is changed right before repaint
// (e.g. #6813, out-in transitions).
// Also, using (macro) tasks in event handler would cause some weird behaviors
// that cannot be circumvented (e.g. #7109, #7153, #7546, #7834, #8109).
// So we now use microtasks everywhere, again.
// A major drawback of this tradeoff is that there are some scenarios
// where microtasks have too high a priority and fire in between supposedly
// sequential events (e.g. #4521, #6690, which have workarounds)
// or even between bubbling of the same event (#6566).
var timerFunc;

// The nextTick behavior leverages the microtask queue, which can be accessed
// via either native Promise.then or MutationObserver.
// MutationObserver has wider support, however it is seriously bugged in
// UIWebView in iOS >= 9.3.3 when triggered in touch event handlers. It
// completely stops working after triggering a few times... so, if native
// Promise is available, we will use it:
/* istanbul ignore next, $flow-disable-line */
if (typeof Promise !== 'undefined' && isNative(Promise)) {
  var p = Promise.resolve();
  timerFunc = function () {
    p.then(flushCallbacks);
    // In problematic UIWebViews, Promise.then doesn't completely break, but
    // it can get stuck in a weird state where callbacks are pushed into the
    // microtask queue but the queue isn't being flushed, until the browser
    // needs to do some other work, e.g. handle a timer. Therefore we can
    // "force" the microtask queue to be flushed by adding an empty timer.
    if (isIOS) { setTimeout(noop); }
  };
} else if (!isIE && typeof MutationObserver !== 'undefined' && (
  isNative(MutationObserver) ||
  // PhantomJS and iOS 7.x
  MutationObserver.toString() === '[object MutationObserverConstructor]'
)) {
  // Use MutationObserver where native Promise is not available,
  // e.g. PhantomJS, iOS7, Android 4.4
  // (#6466 MutationObserver is unreliable in IE11)
  var counter = 1;
  var observer = new MutationObserver(flushCallbacks);
  var textNode = document.createTextNode(String(counter));
  observer.observe(textNode, {
    characterData: true
  });
  timerFunc = function () {
    counter = (counter + 1) % 2;
    textNode.data = String(counter);
  };
} else if (typeof setImmediate !== 'undefined' && isNative(setImmediate)) {
  // Fallback to setImmediate.
  // Technically it leverages the (macro) task queue,
  // but it is still a better choice than setTimeout.
  timerFunc = function () {
    setImmediate(flushCallbacks);
  };
} else {
  // Fallback to setTimeout.
  timerFunc = function () {
    setTimeout(flushCallbacks, 0);
  };
}

function nextTick (cb, ctx) {
  var _resolve;
  callbacks.push(function () {
    if (cb) {
      try {
        cb.call(ctx);
      } catch (e) {
        handleError(e, ctx, 'nextTick');
      }
    } else if (_resolve) {
      _resolve(ctx);
    }
  });
  if (!pending) {
    pending = true;
    timerFunc();
  }
  // $flow-disable-line
  if (!cb && typeof Promise !== 'undefined') {
    return new Promise(function (resolve) {
      _resolve = resolve;
    })
  }
}

/*  */

/* not type checking this file because flow doesn't play well with Proxy */

var initProxy;

if (true) {
  var allowedGlobals = makeMap(
    'Infinity,undefined,NaN,isFinite,isNaN,' +
    'parseFloat,parseInt,decodeURI,decodeURIComponent,encodeURI,encodeURIComponent,' +
    'Math,Number,Date,Array,Object,Boolean,String,RegExp,Map,Set,JSON,Intl,' +
    'require' // for Webpack/Browserify
  );

  var warnNonPresent = function (target, key) {
    warn(
      "Property or method \"" + key + "\" is not defined on the instance but " +
      'referenced during render. Make sure that this property is reactive, ' +
      'either in the data option, or for class-based components, by ' +
      'initializing the property. ' +
      'See: https://vuejs.org/v2/guide/reactivity.html#Declaring-Reactive-Properties.',
      target
    );
  };

  var warnReservedPrefix = function (target, key) {
    warn(
      "Property \"" + key + "\" must be accessed with \"$data." + key + "\" because " +
      'properties starting with "$" or "_" are not proxied in the Vue instance to ' +
      'prevent conflicts with Vue internals. ' +
      'See: https://vuejs.org/v2/api/#data',
      target
    );
  };

  var hasProxy =
    typeof Proxy !== 'undefined' && isNative(Proxy);

  if (hasProxy) {
    var isBuiltInModifier = makeMap('stop,prevent,self,ctrl,shift,alt,meta,exact');
    config.keyCodes = new Proxy(config.keyCodes, {
      set: function set (target, key, value) {
        if (isBuiltInModifier(key)) {
          warn(("Avoid overwriting built-in modifier in config.keyCodes: ." + key));
          return false
        } else {
          target[key] = value;
          return true
        }
      }
    });
  }

  var hasHandler = {
    has: function has (target, key) {
      var has = key in target;
      var isAllowed = allowedGlobals(key) ||
        (typeof key === 'string' && key.charAt(0) === '_' && !(key in target.$data));
      if (!has && !isAllowed) {
        if (key in target.$data) { warnReservedPrefix(target, key); }
        else { warnNonPresent(target, key); }
      }
      return has || !isAllowed
    }
  };

  var getHandler = {
    get: function get (target, key) {
      if (typeof key === 'string' && !(key in target)) {
        if (key in target.$data) { warnReservedPrefix(target, key); }
        else { warnNonPresent(target, key); }
      }
      return target[key]
    }
  };

  initProxy = function initProxy (vm) {
    if (hasProxy) {
      // determine which proxy handler to use
      var options = vm.$options;
      var handlers = options.render && options.render._withStripped
        ? getHandler
        : hasHandler;
      vm._renderProxy = new Proxy(vm, handlers);
    } else {
      vm._renderProxy = vm;
    }
  };
}

/*  */

var seenObjects = new _Set();

/**
 * Recursively traverse an object to evoke all converted
 * getters, so that every nested property inside the object
 * is collected as a "deep" dependency.
 */
function traverse (val) {
  _traverse(val, seenObjects);
  seenObjects.clear();
}

function _traverse (val, seen) {
  var i, keys;
  var isA = Array.isArray(val);
  if ((!isA && !isObject(val)) || Object.isFrozen(val) || val instanceof VNode) {
    return
  }
  if (val.__ob__) {
    var depId = val.__ob__.dep.id;
    if (seen.has(depId)) {
      return
    }
    seen.add(depId);
  }
  if (isA) {
    i = val.length;
    while (i--) { _traverse(val[i], seen); }
  } else {
    keys = Object.keys(val);
    i = keys.length;
    while (i--) { _traverse(val[keys[i]], seen); }
  }
}

var mark;
var measure;

if (true) {
  var perf = inBrowser && window.performance;
  /* istanbul ignore if */
  if (
    perf &&
    perf.mark &&
    perf.measure &&
    perf.clearMarks &&
    perf.clearMeasures
  ) {
    mark = function (tag) { return perf.mark(tag); };
    measure = function (name, startTag, endTag) {
      perf.measure(name, startTag, endTag);
      perf.clearMarks(startTag);
      perf.clearMarks(endTag);
      // perf.clearMeasures(name)
    };
  }
}

/*  */

var normalizeEvent = cached(function (name) {
  var passive = name.charAt(0) === '&';
  name = passive ? name.slice(1) : name;
  var once$$1 = name.charAt(0) === '~'; // Prefixed last, checked first
  name = once$$1 ? name.slice(1) : name;
  var capture = name.charAt(0) === '!';
  name = capture ? name.slice(1) : name;
  return {
    name: name,
    once: once$$1,
    capture: capture,
    passive: passive
  }
});

function createFnInvoker (fns, vm) {
  function invoker () {
    var arguments$1 = arguments;

    var fns = invoker.fns;
    if (Array.isArray(fns)) {
      var cloned = fns.slice();
      for (var i = 0; i < cloned.length; i++) {
        invokeWithErrorHandling(cloned[i], null, arguments$1, vm, "v-on handler");
      }
    } else {
      // return handler return value for single handlers
      return invokeWithErrorHandling(fns, null, arguments, vm, "v-on handler")
    }
  }
  invoker.fns = fns;
  return invoker
}

function updateListeners (
  on,
  oldOn,
  add,
  remove$$1,
  createOnceHandler,
  vm
) {
  var name, def$$1, cur, old, event;
  for (name in on) {
    def$$1 = cur = on[name];
    old = oldOn[name];
    event = normalizeEvent(name);
    if (isUndef(cur)) {
       true && warn(
        "Invalid handler for event \"" + (event.name) + "\": got " + String(cur),
        vm
      );
    } else if (isUndef(old)) {
      if (isUndef(cur.fns)) {
        cur = on[name] = createFnInvoker(cur, vm);
      }
      if (isTrue(event.once)) {
        cur = on[name] = createOnceHandler(event.name, cur, event.capture);
      }
      add(event.name, cur, event.capture, event.passive, event.params);
    } else if (cur !== old) {
      old.fns = cur;
      on[name] = old;
    }
  }
  for (name in oldOn) {
    if (isUndef(on[name])) {
      event = normalizeEvent(name);
      remove$$1(event.name, oldOn[name], event.capture);
    }
  }
}

/*  */

/*  */

// fixed by xxxxxx (mp properties)
function extractPropertiesFromVNodeData(data, Ctor, res, context) {
  var propOptions = Ctor.options.mpOptions && Ctor.options.mpOptions.properties;
  if (isUndef(propOptions)) {
    return res
  }
  var externalClasses = Ctor.options.mpOptions.externalClasses || [];
  var attrs = data.attrs;
  var props = data.props;
  if (isDef(attrs) || isDef(props)) {
    for (var key in propOptions) {
      var altKey = hyphenate(key);
      var result = checkProp(res, props, key, altKey, true) ||
          checkProp(res, attrs, key, altKey, false);
      // externalClass
      if (
        result &&
        res[key] &&
        externalClasses.indexOf(altKey) !== -1 &&
        context[camelize(res[key])]
      ) {
        // 赋值 externalClass 真正的值(模板里 externalClass 的值可能是字符串)
        res[key] = context[camelize(res[key])];
      }
    }
  }
  return res
}

function extractPropsFromVNodeData (
  data,
  Ctor,
  tag,
  context// fixed by xxxxxx
) {
  // we are only extracting raw values here.
  // validation and default values are handled in the child
  // component itself.
  var propOptions = Ctor.options.props;
  if (isUndef(propOptions)) {
    // fixed by xxxxxx
    return extractPropertiesFromVNodeData(data, Ctor, {}, context)
  }
  var res = {};
  var attrs = data.attrs;
  var props = data.props;
  if (isDef(attrs) || isDef(props)) {
    for (var key in propOptions) {
      var altKey = hyphenate(key);
      if (true) {
        var keyInLowerCase = key.toLowerCase();
        if (
          key !== keyInLowerCase &&
          attrs && hasOwn(attrs, keyInLowerCase)
        ) {
          tip(
            "Prop \"" + keyInLowerCase + "\" is passed to component " +
            (formatComponentName(tag || Ctor)) + ", but the declared prop name is" +
            " \"" + key + "\". " +
            "Note that HTML attributes are case-insensitive and camelCased " +
            "props need to use their kebab-case equivalents when using in-DOM " +
            "templates. You should probably use \"" + altKey + "\" instead of \"" + key + "\"."
          );
        }
      }
      checkProp(res, props, key, altKey, true) ||
      checkProp(res, attrs, key, altKey, false);
    }
  }
  // fixed by xxxxxx
  return extractPropertiesFromVNodeData(data, Ctor, res, context)
}

function checkProp (
  res,
  hash,
  key,
  altKey,
  preserve
) {
  if (isDef(hash)) {
    if (hasOwn(hash, key)) {
      res[key] = hash[key];
      if (!preserve) {
        delete hash[key];
      }
      return true
    } else if (hasOwn(hash, altKey)) {
      res[key] = hash[altKey];
      if (!preserve) {
        delete hash[altKey];
      }
      return true
    }
  }
  return false
}

/*  */

// The template compiler attempts to minimize the need for normalization by
// statically analyzing the template at compile time.
//
// For plain HTML markup, normalization can be completely skipped because the
// generated render function is guaranteed to return Array<VNode>. There are
// two cases where extra normalization is needed:

// 1. When the children contains components - because a functional component
// may return an Array instead of a single root. In this case, just a simple
// normalization is needed - if any child is an Array, we flatten the whole
// thing with Array.prototype.concat. It is guaranteed to be only 1-level deep
// because functional components already normalize their own children.
function simpleNormalizeChildren (children) {
  for (var i = 0; i < children.length; i++) {
    if (Array.isArray(children[i])) {
      return Array.prototype.concat.apply([], children)
    }
  }
  return children
}

// 2. When the children contains constructs that always generated nested Arrays,
// e.g. <template>, <slot>, v-for, or when the children is provided by user
// with hand-written render functions / JSX. In such cases a full normalization
// is needed to cater to all possible types of children values.
function normalizeChildren (children) {
  return isPrimitive(children)
    ? [createTextVNode(children)]
    : Array.isArray(children)
      ? normalizeArrayChildren(children)
      : undefined
}

function isTextNode (node) {
  return isDef(node) && isDef(node.text) && isFalse(node.isComment)
}

function normalizeArrayChildren (children, nestedIndex) {
  var res = [];
  var i, c, lastIndex, last;
  for (i = 0; i < children.length; i++) {
    c = children[i];
    if (isUndef(c) || typeof c === 'boolean') { continue }
    lastIndex = res.length - 1;
    last = res[lastIndex];
    //  nested
    if (Array.isArray(c)) {
      if (c.length > 0) {
        c = normalizeArrayChildren(c, ((nestedIndex || '') + "_" + i));
        // merge adjacent text nodes
        if (isTextNode(c[0]) && isTextNode(last)) {
          res[lastIndex] = createTextVNode(last.text + (c[0]).text);
          c.shift();
        }
        res.push.apply(res, c);
      }
    } else if (isPrimitive(c)) {
      if (isTextNode(last)) {
        // merge adjacent text nodes
        // this is necessary for SSR hydration because text nodes are
        // essentially merged when rendered to HTML strings
        res[lastIndex] = createTextVNode(last.text + c);
      } else if (c !== '') {
        // convert primitive to vnode
        res.push(createTextVNode(c));
      }
    } else {
      if (isTextNode(c) && isTextNode(last)) {
        // merge adjacent text nodes
        res[lastIndex] = createTextVNode(last.text + c.text);
      } else {
        // default key for nested array children (likely generated by v-for)
        if (isTrue(children._isVList) &&
          isDef(c.tag) &&
          isUndef(c.key) &&
          isDef(nestedIndex)) {
          c.key = "__vlist" + nestedIndex + "_" + i + "__";
        }
        res.push(c);
      }
    }
  }
  return res
}

/*  */

function initProvide (vm) {
  var provide = vm.$options.provide;
  if (provide) {
    vm._provided = typeof provide === 'function'
      ? provide.call(vm)
      : provide;
  }
}

function initInjections (vm) {
  var result = resolveInject(vm.$options.inject, vm);
  if (result) {
    toggleObserving(false);
    Object.keys(result).forEach(function (key) {
      /* istanbul ignore else */
      if (true) {
        defineReactive$$1(vm, key, result[key], function () {
          warn(
            "Avoid mutating an injected value directly since the changes will be " +
            "overwritten whenever the provided component re-renders. " +
            "injection being mutated: \"" + key + "\"",
            vm
          );
        });
      } else {}
    });
    toggleObserving(true);
  }
}

function resolveInject (inject, vm) {
  if (inject) {
    // inject is :any because flow is not smart enough to figure out cached
    var result = Object.create(null);
    var keys = hasSymbol
      ? Reflect.ownKeys(inject)
      : Object.keys(inject);

    for (var i = 0; i < keys.length; i++) {
      var key = keys[i];
      // #6574 in case the inject object is observed...
      if (key === '__ob__') { continue }
      var provideKey = inject[key].from;
      var source = vm;
      while (source) {
        if (source._provided && hasOwn(source._provided, provideKey)) {
          result[key] = source._provided[provideKey];
          break
        }
        source = source.$parent;
      }
      if (!source) {
        if ('default' in inject[key]) {
          var provideDefault = inject[key].default;
          result[key] = typeof provideDefault === 'function'
            ? provideDefault.call(vm)
            : provideDefault;
        } else if (true) {
          warn(("Injection \"" + key + "\" not found"), vm);
        }
      }
    }
    return result
  }
}

/*  */



/**
 * Runtime helper for resolving raw children VNodes into a slot object.
 */
function resolveSlots (
  children,
  context
) {
  if (!children || !children.length) {
    return {}
  }
  var slots = {};
  for (var i = 0, l = children.length; i < l; i++) {
    var child = children[i];
    var data = child.data;
    // remove slot attribute if the node is resolved as a Vue slot node
    if (data && data.attrs && data.attrs.slot) {
      delete data.attrs.slot;
    }
    // named slots should only be respected if the vnode was rendered in the
    // same context.
    if ((child.context === context || child.fnContext === context) &&
      data && data.slot != null
    ) {
      var name = data.slot;
      var slot = (slots[name] || (slots[name] = []));
      if (child.tag === 'template') {
        slot.push.apply(slot, child.children || []);
      } else {
        slot.push(child);
      }
    } else {
      // fixed by xxxxxx 临时 hack 掉 uni-app 中的异步 name slot page
      if(child.asyncMeta && child.asyncMeta.data && child.asyncMeta.data.slot === 'page'){
        (slots['page'] || (slots['page'] = [])).push(child);
      }else{
        (slots.default || (slots.default = [])).push(child);
      }
    }
  }
  // ignore slots that contains only whitespace
  for (var name$1 in slots) {
    if (slots[name$1].every(isWhitespace)) {
      delete slots[name$1];
    }
  }
  return slots
}

function isWhitespace (node) {
  return (node.isComment && !node.asyncFactory) || node.text === ' '
}

/*  */

function normalizeScopedSlots (
  slots,
  normalSlots,
  prevSlots
) {
  var res;
  var hasNormalSlots = Object.keys(normalSlots).length > 0;
  var isStable = slots ? !!slots.$stable : !hasNormalSlots;
  var key = slots && slots.$key;
  if (!slots) {
    res = {};
  } else if (slots._normalized) {
    // fast path 1: child component re-render only, parent did not change
    return slots._normalized
  } else if (
    isStable &&
    prevSlots &&
    prevSlots !== emptyObject &&
    key === prevSlots.$key &&
    !hasNormalSlots &&
    !prevSlots.$hasNormal
  ) {
    // fast path 2: stable scoped slots w/ no normal slots to proxy,
    // only need to normalize once
    return prevSlots
  } else {
    res = {};
    for (var key$1 in slots) {
      if (slots[key$1] && key$1[0] !== '$') {
        res[key$1] = normalizeScopedSlot(normalSlots, key$1, slots[key$1]);
      }
    }
  }
  // expose normal slots on scopedSlots
  for (var key$2 in normalSlots) {
    if (!(key$2 in res)) {
      res[key$2] = proxyNormalSlot(normalSlots, key$2);
    }
  }
  // avoriaz seems to mock a non-extensible $scopedSlots object
  // and when that is passed down this would cause an error
  if (slots && Object.isExtensible(slots)) {
    (slots)._normalized = res;
  }
  def(res, '$stable', isStable);
  def(res, '$key', key);
  def(res, '$hasNormal', hasNormalSlots);
  return res
}

function normalizeScopedSlot(normalSlots, key, fn) {
  var normalized = function () {
    var res = arguments.length ? fn.apply(null, arguments) : fn({});
    res = res && typeof res === 'object' && !Array.isArray(res)
      ? [res] // single vnode
      : normalizeChildren(res);
    return res && (
      res.length === 0 ||
      (res.length === 1 && res[0].isComment) // #9658
    ) ? undefined
      : res
  };
  // this is a slot using the new v-slot syntax without scope. although it is
  // compiled as a scoped slot, render fn users would expect it to be present
  // on this.$slots because the usage is semantically a normal slot.
  if (fn.proxy) {
    Object.defineProperty(normalSlots, key, {
      get: normalized,
      enumerable: true,
      configurable: true
    });
  }
  return normalized
}

function proxyNormalSlot(slots, key) {
  return function () { return slots[key]; }
}

/*  */

/**
 * Runtime helper for rendering v-for lists.
 */
function renderList (
  val,
  render
) {
  var ret, i, l, keys, key;
  if (Array.isArray(val) || typeof val === 'string') {
    ret = new Array(val.length);
    for (i = 0, l = val.length; i < l; i++) {
      ret[i] = render(val[i], i, i, i); // fixed by xxxxxx
    }
  } else if (typeof val === 'number') {
    ret = new Array(val);
    for (i = 0; i < val; i++) {
      ret[i] = render(i + 1, i, i, i); // fixed by xxxxxx
    }
  } else if (isObject(val)) {
    if (hasSymbol && val[Symbol.iterator]) {
      ret = [];
      var iterator = val[Symbol.iterator]();
      var result = iterator.next();
      while (!result.done) {
        ret.push(render(result.value, ret.length, i, i++)); // fixed by xxxxxx
        result = iterator.next();
      }
    } else {
      keys = Object.keys(val);
      ret = new Array(keys.length);
      for (i = 0, l = keys.length; i < l; i++) {
        key = keys[i];
        ret[i] = render(val[key], key, i, i); // fixed by xxxxxx
      }
    }
  }
  if (!isDef(ret)) {
    ret = [];
  }
  (ret)._isVList = true;
  return ret
}

/*  */

/**
 * Runtime helper for rendering <slot>
 */
function renderSlot (
  name,
  fallback,
  props,
  bindObject
) {
  var scopedSlotFn = this.$scopedSlots[name];
  var nodes;
  if (scopedSlotFn) { // scoped slot
    props = props || {};
    if (bindObject) {
      if ( true && !isObject(bindObject)) {
        warn(
          'slot v-bind without argument expects an Object',
          this
        );
      }
      props = extend(extend({}, bindObject), props);
    }
    // fixed by xxxxxx app-plus scopedSlot
    nodes = scopedSlotFn(props, this, props._i) || fallback;
  } else {
    nodes = this.$slots[name] || fallback;
  }

  var target = props && props.slot;
  if (target) {
    return this.$createElement('template', { slot: target }, nodes)
  } else {
    return nodes
  }
}

/*  */

/**
 * Runtime helper for resolving filters
 */
function resolveFilter (id) {
  return resolveAsset(this.$options, 'filters', id, true) || identity
}

/*  */

function isKeyNotMatch (expect, actual) {
  if (Array.isArray(expect)) {
    return expect.indexOf(actual) === -1
  } else {
    return expect !== actual
  }
}

/**
 * Runtime helper for checking keyCodes from config.
 * exposed as Vue.prototype._k
 * passing in eventKeyName as last argument separately for backwards compat
 */
function checkKeyCodes (
  eventKeyCode,
  key,
  builtInKeyCode,
  eventKeyName,
  builtInKeyName
) {
  var mappedKeyCode = config.keyCodes[key] || builtInKeyCode;
  if (builtInKeyName && eventKeyName && !config.keyCodes[key]) {
    return isKeyNotMatch(builtInKeyName, eventKeyName)
  } else if (mappedKeyCode) {
    return isKeyNotMatch(mappedKeyCode, eventKeyCode)
  } else if (eventKeyName) {
    return hyphenate(eventKeyName) !== key
  }
}

/*  */

/**
 * Runtime helper for merging v-bind="object" into a VNode's data.
 */
function bindObjectProps (
  data,
  tag,
  value,
  asProp,
  isSync
) {
  if (value) {
    if (!isObject(value)) {
       true && warn(
        'v-bind without argument expects an Object or Array value',
        this
      );
    } else {
      if (Array.isArray(value)) {
        value = toObject(value);
      }
      var hash;
      var loop = function ( key ) {
        if (
          key === 'class' ||
          key === 'style' ||
          isReservedAttribute(key)
        ) {
          hash = data;
        } else {
          var type = data.attrs && data.attrs.type;
          hash = asProp || config.mustUseProp(tag, type, key)
            ? data.domProps || (data.domProps = {})
            : data.attrs || (data.attrs = {});
        }
        var camelizedKey = camelize(key);
        var hyphenatedKey = hyphenate(key);
        if (!(camelizedKey in hash) && !(hyphenatedKey in hash)) {
          hash[key] = value[key];

          if (isSync) {
            var on = data.on || (data.on = {});
            on[("update:" + key)] = function ($event) {
              value[key] = $event;
            };
          }
        }
      };

      for (var key in value) loop( key );
    }
  }
  return data
}

/*  */

/**
 * Runtime helper for rendering static trees.
 */
function renderStatic (
  index,
  isInFor
) {
  var cached = this._staticTrees || (this._staticTrees = []);
  var tree = cached[index];
  // if has already-rendered static tree and not inside v-for,
  // we can reuse the same tree.
  if (tree && !isInFor) {
    return tree
  }
  // otherwise, render a fresh tree.
  tree = cached[index] = this.$options.staticRenderFns[index].call(
    this._renderProxy,
    null,
    this // for render fns generated for functional component templates
  );
  markStatic(tree, ("__static__" + index), false);
  return tree
}

/**
 * Runtime helper for v-once.
 * Effectively it means marking the node as static with a unique key.
 */
function markOnce (
  tree,
  index,
  key
) {
  markStatic(tree, ("__once__" + index + (key ? ("_" + key) : "")), true);
  return tree
}

function markStatic (
  tree,
  key,
  isOnce
) {
  if (Array.isArray(tree)) {
    for (var i = 0; i < tree.length; i++) {
      if (tree[i] && typeof tree[i] !== 'string') {
        markStaticNode(tree[i], (key + "_" + i), isOnce);
      }
    }
  } else {
    markStaticNode(tree, key, isOnce);
  }
}

function markStaticNode (node, key, isOnce) {
  node.isStatic = true;
  node.key = key;
  node.isOnce = isOnce;
}

/*  */

function bindObjectListeners (data, value) {
  if (value) {
    if (!isPlainObject(value)) {
       true && warn(
        'v-on without argument expects an Object value',
        this
      );
    } else {
      var on = data.on = data.on ? extend({}, data.on) : {};
      for (var key in value) {
        var existing = on[key];
        var ours = value[key];
        on[key] = existing ? [].concat(existing, ours) : ours;
      }
    }
  }
  return data
}

/*  */

function resolveScopedSlots (
  fns, // see flow/vnode
  res,
  // the following are added in 2.6
  hasDynamicKeys,
  contentHashKey
) {
  res = res || { $stable: !hasDynamicKeys };
  for (var i = 0; i < fns.length; i++) {
    var slot = fns[i];
    if (Array.isArray(slot)) {
      resolveScopedSlots(slot, res, hasDynamicKeys);
    } else if (slot) {
      // marker for reverse proxying v-slot without scope on this.$slots
      if (slot.proxy) {
        slot.fn.proxy = true;
      }
      res[slot.key] = slot.fn;
    }
  }
  if (contentHashKey) {
    (res).$key = contentHashKey;
  }
  return res
}

/*  */

function bindDynamicKeys (baseObj, values) {
  for (var i = 0; i < values.length; i += 2) {
    var key = values[i];
    if (typeof key === 'string' && key) {
      baseObj[values[i]] = values[i + 1];
    } else if ( true && key !== '' && key !== null) {
      // null is a special value for explicitly removing a binding
      warn(
        ("Invalid value for dynamic directive argument (expected string or null): " + key),
        this
      );
    }
  }
  return baseObj
}

// helper to dynamically append modifier runtime markers to event names.
// ensure only append when value is already string, otherwise it will be cast
// to string and cause the type check to miss.
function prependModifier (value, symbol) {
  return typeof value === 'string' ? symbol + value : value
}

/*  */

function installRenderHelpers (target) {
  target._o = markOnce;
  target._n = toNumber;
  target._s = toString;
  target._l = renderList;
  target._t = renderSlot;
  target._q = looseEqual;
  target._i = looseIndexOf;
  target._m = renderStatic;
  target._f = resolveFilter;
  target._k = checkKeyCodes;
  target._b = bindObjectProps;
  target._v = createTextVNode;
  target._e = createEmptyVNode;
  target._u = resolveScopedSlots;
  target._g = bindObjectListeners;
  target._d = bindDynamicKeys;
  target._p = prependModifier;
}

/*  */

function FunctionalRenderContext (
  data,
  props,
  children,
  parent,
  Ctor
) {
  var this$1 = this;

  var options = Ctor.options;
  // ensure the createElement function in functional components
  // gets a unique context - this is necessary for correct named slot check
  var contextVm;
  if (hasOwn(parent, '_uid')) {
    contextVm = Object.create(parent);
    // $flow-disable-line
    contextVm._original = parent;
  } else {
    // the context vm passed in is a functional context as well.
    // in this case we want to make sure we are able to get a hold to the
    // real context instance.
    contextVm = parent;
    // $flow-disable-line
    parent = parent._original;
  }
  var isCompiled = isTrue(options._compiled);
  var needNormalization = !isCompiled;

  this.data = data;
  this.props = props;
  this.children = children;
  this.parent = parent;
  this.listeners = data.on || emptyObject;
  this.injections = resolveInject(options.inject, parent);
  this.slots = function () {
    if (!this$1.$slots) {
      normalizeScopedSlots(
        data.scopedSlots,
        this$1.$slots = resolveSlots(children, parent)
      );
    }
    return this$1.$slots
  };

  Object.defineProperty(this, 'scopedSlots', ({
    enumerable: true,
    get: function get () {
      return normalizeScopedSlots(data.scopedSlots, this.slots())
    }
  }));

  // support for compiled functional template
  if (isCompiled) {
    // exposing $options for renderStatic()
    this.$options = options;
    // pre-resolve slots for renderSlot()
    this.$slots = this.slots();
    this.$scopedSlots = normalizeScopedSlots(data.scopedSlots, this.$slots);
  }

  if (options._scopeId) {
    this._c = function (a, b, c, d) {
      var vnode = createElement(contextVm, a, b, c, d, needNormalization);
      if (vnode && !Array.isArray(vnode)) {
        vnode.fnScopeId = options._scopeId;
        vnode.fnContext = parent;
      }
      return vnode
    };
  } else {
    this._c = function (a, b, c, d) { return createElement(contextVm, a, b, c, d, needNormalization); };
  }
}

installRenderHelpers(FunctionalRenderContext.prototype);

function createFunctionalComponent (
  Ctor,
  propsData,
  data,
  contextVm,
  children
) {
  var options = Ctor.options;
  var props = {};
  var propOptions = options.props;
  if (isDef(propOptions)) {
    for (var key in propOptions) {
      props[key] = validateProp(key, propOptions, propsData || emptyObject);
    }
  } else {
    if (isDef(data.attrs)) { mergeProps(props, data.attrs); }
    if (isDef(data.props)) { mergeProps(props, data.props); }
  }

  var renderContext = new FunctionalRenderContext(
    data,
    props,
    children,
    contextVm,
    Ctor
  );

  var vnode = options.render.call(null, renderContext._c, renderContext);

  if (vnode instanceof VNode) {
    return cloneAndMarkFunctionalResult(vnode, data, renderContext.parent, options, renderContext)
  } else if (Array.isArray(vnode)) {
    var vnodes = normalizeChildren(vnode) || [];
    var res = new Array(vnodes.length);
    for (var i = 0; i < vnodes.length; i++) {
      res[i] = cloneAndMarkFunctionalResult(vnodes[i], data, renderContext.parent, options, renderContext);
    }
    return res
  }
}

function cloneAndMarkFunctionalResult (vnode, data, contextVm, options, renderContext) {
  // #7817 clone node before setting fnContext, otherwise if the node is reused
  // (e.g. it was from a cached normal slot) the fnContext causes named slots
  // that should not be matched to match.
  var clone = cloneVNode(vnode);
  clone.fnContext = contextVm;
  clone.fnOptions = options;
  if (true) {
    (clone.devtoolsMeta = clone.devtoolsMeta || {}).renderContext = renderContext;
  }
  if (data.slot) {
    (clone.data || (clone.data = {})).slot = data.slot;
  }
  return clone
}

function mergeProps (to, from) {
  for (var key in from) {
    to[camelize(key)] = from[key];
  }
}

/*  */

/*  */

/*  */

/*  */

// inline hooks to be invoked on component VNodes during patch
var componentVNodeHooks = {
  init: function init (vnode, hydrating) {
    if (
      vnode.componentInstance &&
      !vnode.componentInstance._isDestroyed &&
      vnode.data.keepAlive
    ) {
      // kept-alive components, treat as a patch
      var mountedNode = vnode; // work around flow
      componentVNodeHooks.prepatch(mountedNode, mountedNode);
    } else {
      var child = vnode.componentInstance = createComponentInstanceForVnode(
        vnode,
        activeInstance
      );
      child.$mount(hydrating ? vnode.elm : undefined, hydrating);
    }
  },

  prepatch: function prepatch (oldVnode, vnode) {
    var options = vnode.componentOptions;
    var child = vnode.componentInstance = oldVnode.componentInstance;
    updateChildComponent(
      child,
      options.propsData, // updated props
      options.listeners, // updated listeners
      vnode, // new parent vnode
      options.children // new children
    );
  },

  insert: function insert (vnode) {
    var context = vnode.context;
    var componentInstance = vnode.componentInstance;
    if (!componentInstance._isMounted) {
      callHook(componentInstance, 'onServiceCreated');
      callHook(componentInstance, 'onServiceAttached');
      componentInstance._isMounted = true;
      callHook(componentInstance, 'mounted');
    }
    if (vnode.data.keepAlive) {
      if (context._isMounted) {
        // vue-router#1212
        // During updates, a kept-alive component's child components may
        // change, so directly walking the tree here may call activated hooks
        // on incorrect children. Instead we push them into a queue which will
        // be processed after the whole patch process ended.
        queueActivatedComponent(componentInstance);
      } else {
        activateChildComponent(componentInstance, true /* direct */);
      }
    }
  },

  destroy: function destroy (vnode) {
    var componentInstance = vnode.componentInstance;
    if (!componentInstance._isDestroyed) {
      if (!vnode.data.keepAlive) {
        componentInstance.$destroy();
      } else {
        deactivateChildComponent(componentInstance, true /* direct */);
      }
    }
  }
};

var hooksToMerge = Object.keys(componentVNodeHooks);

function createComponent (
  Ctor,
  data,
  context,
  children,
  tag
) {
  if (isUndef(Ctor)) {
    return
  }

  var baseCtor = context.$options._base;

  // plain options object: turn it into a constructor
  if (isObject(Ctor)) {
    Ctor = baseCtor.extend(Ctor);
  }

  // if at this stage it's not a constructor or an async component factory,
  // reject.
  if (typeof Ctor !== 'function') {
    if (true) {
      warn(("Invalid Component definition: " + (String(Ctor))), context);
    }
    return
  }

  // async component
  var asyncFactory;
  if (isUndef(Ctor.cid)) {
    asyncFactory = Ctor;
    Ctor = resolveAsyncComponent(asyncFactory, baseCtor);
    if (Ctor === undefined) {
      // return a placeholder node for async component, which is rendered
      // as a comment node but preserves all the raw information for the node.
      // the information will be used for async server-rendering and hydration.
      return createAsyncPlaceholder(
        asyncFactory,
        data,
        context,
        children,
        tag
      )
    }
  }

  data = data || {};

  // resolve constructor options in case global mixins are applied after
  // component constructor creation
  resolveConstructorOptions(Ctor);

  // transform component v-model data into props & events
  if (isDef(data.model)) {
    transformModel(Ctor.options, data);
  }

  // extract props
  var propsData = extractPropsFromVNodeData(data, Ctor, tag, context); // fixed by xxxxxx

  // functional component
  if (isTrue(Ctor.options.functional)) {
    return createFunctionalComponent(Ctor, propsData, data, context, children)
  }

  // extract listeners, since these needs to be treated as
  // child component listeners instead of DOM listeners
  var listeners = data.on;
  // replace with listeners with .native modifier
  // so it gets processed during parent component patch.
  data.on = data.nativeOn;

  if (isTrue(Ctor.options.abstract)) {
    // abstract components do not keep anything
    // other than props & listeners & slot

    // work around flow
    var slot = data.slot;
    data = {};
    if (slot) {
      data.slot = slot;
    }
  }

  // install component management hooks onto the placeholder node
  installComponentHooks(data);

  // return a placeholder vnode
  var name = Ctor.options.name || tag;
  var vnode = new VNode(
    ("vue-component-" + (Ctor.cid) + (name ? ("-" + name) : '')),
    data, undefined, undefined, undefined, context,
    { Ctor: Ctor, propsData: propsData, listeners: listeners, tag: tag, children: children },
    asyncFactory
  );

  return vnode
}

function createComponentInstanceForVnode (
  vnode, // we know it's MountedComponentVNode but flow doesn't
  parent // activeInstance in lifecycle state
) {
  var options = {
    _isComponent: true,
    _parentVnode: vnode,
    parent: parent
  };
  // check inline-template render functions
  var inlineTemplate = vnode.data.inlineTemplate;
  if (isDef(inlineTemplate)) {
    options.render = inlineTemplate.render;
    options.staticRenderFns = inlineTemplate.staticRenderFns;
  }
  return new vnode.componentOptions.Ctor(options)
}

function installComponentHooks (data) {
  var hooks = data.hook || (data.hook = {});
  for (var i = 0; i < hooksToMerge.length; i++) {
    var key = hooksToMerge[i];
    var existing = hooks[key];
    var toMerge = componentVNodeHooks[key];
    if (existing !== toMerge && !(existing && existing._merged)) {
      hooks[key] = existing ? mergeHook$1(toMerge, existing) : toMerge;
    }
  }
}

function mergeHook$1 (f1, f2) {
  var merged = function (a, b) {
    // flow complains about extra args which is why we use any
    f1(a, b);
    f2(a, b);
  };
  merged._merged = true;
  return merged
}

// transform component v-model info (value and callback) into
// prop and event handler respectively.
function transformModel (options, data) {
  var prop = (options.model && options.model.prop) || 'value';
  var event = (options.model && options.model.event) || 'input'
  ;(data.attrs || (data.attrs = {}))[prop] = data.model.value;
  var on = data.on || (data.on = {});
  var existing = on[event];
  var callback = data.model.callback;
  if (isDef(existing)) {
    if (
      Array.isArray(existing)
        ? existing.indexOf(callback) === -1
        : existing !== callback
    ) {
      on[event] = [callback].concat(existing);
    }
  } else {
    on[event] = callback;
  }
}

/*  */

var SIMPLE_NORMALIZE = 1;
var ALWAYS_NORMALIZE = 2;

// wrapper function for providing a more flexible interface
// without getting yelled at by flow
function createElement (
  context,
  tag,
  data,
  children,
  normalizationType,
  alwaysNormalize
) {
  if (Array.isArray(data) || isPrimitive(data)) {
    normalizationType = children;
    children = data;
    data = undefined;
  }
  if (isTrue(alwaysNormalize)) {
    normalizationType = ALWAYS_NORMALIZE;
  }
  return _createElement(context, tag, data, children, normalizationType)
}

function _createElement (
  context,
  tag,
  data,
  children,
  normalizationType
) {
  if (isDef(data) && isDef((data).__ob__)) {
     true && warn(
      "Avoid using observed data object as vnode data: " + (JSON.stringify(data)) + "\n" +
      'Always create fresh vnode data objects in each render!',
      context
    );
    return createEmptyVNode()
  }
  // object syntax in v-bind
  if (isDef(data) && isDef(data.is)) {
    tag = data.is;
  }
  if (!tag) {
    // in case of component :is set to falsy value
    return createEmptyVNode()
  }
  // warn against non-primitive key
  if ( true &&
    isDef(data) && isDef(data.key) && !isPrimitive(data.key)
  ) {
    {
      warn(
        'Avoid using non-primitive value as key, ' +
        'use string/number value instead.',
        context
      );
    }
  }
  // support single function children as default scoped slot
  if (Array.isArray(children) &&
    typeof children[0] === 'function'
  ) {
    data = data || {};
    data.scopedSlots = { default: children[0] };
    children.length = 0;
  }
  if (normalizationType === ALWAYS_NORMALIZE) {
    children = normalizeChildren(children);
  } else if (normalizationType === SIMPLE_NORMALIZE) {
    children = simpleNormalizeChildren(children);
  }
  var vnode, ns;
  if (typeof tag === 'string') {
    var Ctor;
    ns = (context.$vnode && context.$vnode.ns) || config.getTagNamespace(tag);
    if (config.isReservedTag(tag)) {
      // platform built-in elements
      if ( true && isDef(data) && isDef(data.nativeOn)) {
        warn(
          ("The .native modifier for v-on is only valid on components but it was used on <" + tag + ">."),
          context
        );
      }
      vnode = new VNode(
        config.parsePlatformTagName(tag), data, children,
        undefined, undefined, context
      );
    } else if ((!data || !data.pre) && isDef(Ctor = resolveAsset(context.$options, 'components', tag))) {
      // component
      vnode = createComponent(Ctor, data, context, children, tag);
    } else {
      // unknown or unlisted namespaced elements
      // check at runtime because it may get assigned a namespace when its
      // parent normalizes children
      vnode = new VNode(
        tag, data, children,
        undefined, undefined, context
      );
    }
  } else {
    // direct component options / constructor
    vnode = createComponent(tag, data, context, children);
  }
  if (Array.isArray(vnode)) {
    return vnode
  } else if (isDef(vnode)) {
    if (isDef(ns)) { applyNS(vnode, ns); }
    if (isDef(data)) { registerDeepBindings(data); }
    return vnode
  } else {
    return createEmptyVNode()
  }
}

function applyNS (vnode, ns, force) {
  vnode.ns = ns;
  if (vnode.tag === 'foreignObject') {
    // use default namespace inside foreignObject
    ns = undefined;
    force = true;
  }
  if (isDef(vnode.children)) {
    for (var i = 0, l = vnode.children.length; i < l; i++) {
      var child = vnode.children[i];
      if (isDef(child.tag) && (
        isUndef(child.ns) || (isTrue(force) && child.tag !== 'svg'))) {
        applyNS(child, ns, force);
      }
    }
  }
}

// ref #5318
// necessary to ensure parent re-render when deep bindings like :style and
// :class are used on slot nodes
function registerDeepBindings (data) {
  if (isObject(data.style)) {
    traverse(data.style);
  }
  if (isObject(data.class)) {
    traverse(data.class);
  }
}

/*  */

function initRender (vm) {
  vm._vnode = null; // the root of the child tree
  vm._staticTrees = null; // v-once cached trees
  var options = vm.$options;
  var parentVnode = vm.$vnode = options._parentVnode; // the placeholder node in parent tree
  var renderContext = parentVnode && parentVnode.context;
  vm.$slots = resolveSlots(options._renderChildren, renderContext);
  vm.$scopedSlots = emptyObject;
  // bind the createElement fn to this instance
  // so that we get proper render context inside it.
  // args order: tag, data, children, normalizationType, alwaysNormalize
  // internal version is used by render functions compiled from templates
  vm._c = function (a, b, c, d) { return createElement(vm, a, b, c, d, false); };
  // normalization is always applied for the public version, used in
  // user-written render functions.
  vm.$createElement = function (a, b, c, d) { return createElement(vm, a, b, c, d, true); };

  // $attrs & $listeners are exposed for easier HOC creation.
  // they need to be reactive so that HOCs using them are always updated
  var parentData = parentVnode && parentVnode.data;

  /* istanbul ignore else */
  if (true) {
    defineReactive$$1(vm, '$attrs', parentData && parentData.attrs || emptyObject, function () {
      !isUpdatingChildComponent && warn("$attrs is readonly.", vm);
    }, true);
    defineReactive$$1(vm, '$listeners', options._parentListeners || emptyObject, function () {
      !isUpdatingChildComponent && warn("$listeners is readonly.", vm);
    }, true);
  } else {}
}

var currentRenderingInstance = null;

function renderMixin (Vue) {
  // install runtime convenience helpers
  installRenderHelpers(Vue.prototype);

  Vue.prototype.$nextTick = function (fn) {
    return nextTick(fn, this)
  };

  Vue.prototype._render = function () {
    var vm = this;
    var ref = vm.$options;
    var render = ref.render;
    var _parentVnode = ref._parentVnode;

    if (_parentVnode) {
      vm.$scopedSlots = normalizeScopedSlots(
        _parentVnode.data.scopedSlots,
        vm.$slots,
        vm.$scopedSlots
      );
    }

    // set parent vnode. this allows render functions to have access
    // to the data on the placeholder node.
    vm.$vnode = _parentVnode;
    // render self
    var vnode;
    try {
      // There's no need to maintain a stack because all render fns are called
      // separately from one another. Nested component's render fns are called
      // when parent component is patched.
      currentRenderingInstance = vm;
      vnode = render.call(vm._renderProxy, vm.$createElement);
    } catch (e) {
      handleError(e, vm, "render");
      // return error render result,
      // or previous vnode to prevent render error causing blank component
      /* istanbul ignore else */
      if ( true && vm.$options.renderError) {
        try {
          vnode = vm.$options.renderError.call(vm._renderProxy, vm.$createElement, e);
        } catch (e) {
          handleError(e, vm, "renderError");
          vnode = vm._vnode;
        }
      } else {
        vnode = vm._vnode;
      }
    } finally {
      currentRenderingInstance = null;
    }
    // if the returned array contains only a single node, allow it
    if (Array.isArray(vnode) && vnode.length === 1) {
      vnode = vnode[0];
    }
    // return empty vnode in case the render function errored out
    if (!(vnode instanceof VNode)) {
      if ( true && Array.isArray(vnode)) {
        warn(
          'Multiple root nodes returned from render function. Render function ' +
          'should return a single root node.',
          vm
        );
      }
      vnode = createEmptyVNode();
    }
    // set parent
    vnode.parent = _parentVnode;
    return vnode
  };
}

/*  */

function ensureCtor (comp, base) {
  if (
    comp.__esModule ||
    (hasSymbol && comp[Symbol.toStringTag] === 'Module')
  ) {
    comp = comp.default;
  }
  return isObject(comp)
    ? base.extend(comp)
    : comp
}

function createAsyncPlaceholder (
  factory,
  data,
  context,
  children,
  tag
) {
  var node = createEmptyVNode();
  node.asyncFactory = factory;
  node.asyncMeta = { data: data, context: context, children: children, tag: tag };
  return node
}

function resolveAsyncComponent (
  factory,
  baseCtor
) {
  if (isTrue(factory.error) && isDef(factory.errorComp)) {
    return factory.errorComp
  }

  if (isDef(factory.resolved)) {
    return factory.resolved
  }

  var owner = currentRenderingInstance;
  if (owner && isDef(factory.owners) && factory.owners.indexOf(owner) === -1) {
    // already pending
    factory.owners.push(owner);
  }

  if (isTrue(factory.loading) && isDef(factory.loadingComp)) {
    return factory.loadingComp
  }

  if (owner && !isDef(factory.owners)) {
    var owners = factory.owners = [owner];
    var sync = true;
    var timerLoading = null;
    var timerTimeout = null

    ;(owner).$on('hook:destroyed', function () { return remove(owners, owner); });

    var forceRender = function (renderCompleted) {
      for (var i = 0, l = owners.length; i < l; i++) {
        (owners[i]).$forceUpdate();
      }

      if (renderCompleted) {
        owners.length = 0;
        if (timerLoading !== null) {
          clearTimeout(timerLoading);
          timerLoading = null;
        }
        if (timerTimeout !== null) {
          clearTimeout(timerTimeout);
          timerTimeout = null;
        }
      }
    };

    var resolve = once(function (res) {
      // cache resolved
      factory.resolved = ensureCtor(res, baseCtor);
      // invoke callbacks only if this is not a synchronous resolve
      // (async resolves are shimmed as synchronous during SSR)
      if (!sync) {
        forceRender(true);
      } else {
        owners.length = 0;
      }
    });

    var reject = once(function (reason) {
       true && warn(
        "Failed to resolve async component: " + (String(factory)) +
        (reason ? ("\nReason: " + reason) : '')
      );
      if (isDef(factory.errorComp)) {
        factory.error = true;
        forceRender(true);
      }
    });

    var res = factory(resolve, reject);

    if (isObject(res)) {
      if (isPromise(res)) {
        // () => Promise
        if (isUndef(factory.resolved)) {
          res.then(resolve, reject);
        }
      } else if (isPromise(res.component)) {
        res.component.then(resolve, reject);

        if (isDef(res.error)) {
          factory.errorComp = ensureCtor(res.error, baseCtor);
        }

        if (isDef(res.loading)) {
          factory.loadingComp = ensureCtor(res.loading, baseCtor);
          if (res.delay === 0) {
            factory.loading = true;
          } else {
            timerLoading = setTimeout(function () {
              timerLoading = null;
              if (isUndef(factory.resolved) && isUndef(factory.error)) {
                factory.loading = true;
                forceRender(false);
              }
            }, res.delay || 200);
          }
        }

        if (isDef(res.timeout)) {
          timerTimeout = setTimeout(function () {
            timerTimeout = null;
            if (isUndef(factory.resolved)) {
              reject(
                 true
                  ? ("timeout (" + (res.timeout) + "ms)")
                  : undefined
              );
            }
          }, res.timeout);
        }
      }
    }

    sync = false;
    // return in case resolved synchronously
    return factory.loading
      ? factory.loadingComp
      : factory.resolved
  }
}

/*  */

function isAsyncPlaceholder (node) {
  return node.isComment && node.asyncFactory
}

/*  */

function getFirstComponentChild (children) {
  if (Array.isArray(children)) {
    for (var i = 0; i < children.length; i++) {
      var c = children[i];
      if (isDef(c) && (isDef(c.componentOptions) || isAsyncPlaceholder(c))) {
        return c
      }
    }
  }
}

/*  */

/*  */

function initEvents (vm) {
  vm._events = Object.create(null);
  vm._hasHookEvent = false;
  // init parent attached events
  var listeners = vm.$options._parentListeners;
  if (listeners) {
    updateComponentListeners(vm, listeners);
  }
}

var target;

function add (event, fn) {
  target.$on(event, fn);
}

function remove$1 (event, fn) {
  target.$off(event, fn);
}

function createOnceHandler (event, fn) {
  var _target = target;
  return function onceHandler () {
    var res = fn.apply(null, arguments);
    if (res !== null) {
      _target.$off(event, onceHandler);
    }
  }
}

function updateComponentListeners (
  vm,
  listeners,
  oldListeners
) {
  target = vm;
  updateListeners(listeners, oldListeners || {}, add, remove$1, createOnceHandler, vm);
  target = undefined;
}

function eventsMixin (Vue) {
  var hookRE = /^hook:/;
  Vue.prototype.$on = function (event, fn) {
    var vm = this;
    if (Array.isArray(event)) {
      for (var i = 0, l = event.length; i < l; i++) {
        vm.$on(event[i], fn);
      }
    } else {
      (vm._events[event] || (vm._events[event] = [])).push(fn);
      // optimize hook:event cost by using a boolean flag marked at registration
      // instead of a hash lookup
      if (hookRE.test(event)) {
        vm._hasHookEvent = true;
      }
    }
    return vm
  };

  Vue.prototype.$once = function (event, fn) {
    var vm = this;
    function on () {
      vm.$off(event, on);
      fn.apply(vm, arguments);
    }
    on.fn = fn;
    vm.$on(event, on);
    return vm
  };

  Vue.prototype.$off = function (event, fn) {
    var vm = this;
    // all
    if (!arguments.length) {
      vm._events = Object.create(null);
      return vm
    }
    // array of events
    if (Array.isArray(event)) {
      for (var i$1 = 0, l = event.length; i$1 < l; i$1++) {
        vm.$off(event[i$1], fn);
      }
      return vm
    }
    // specific event
    var cbs = vm._events[event];
    if (!cbs) {
      return vm
    }
    if (!fn) {
      vm._events[event] = null;
      return vm
    }
    // specific handler
    var cb;
    var i = cbs.length;
    while (i--) {
      cb = cbs[i];
      if (cb === fn || cb.fn === fn) {
        cbs.splice(i, 1);
        break
      }
    }
    return vm
  };

  Vue.prototype.$emit = function (event) {
    var vm = this;
    if (true) {
      var lowerCaseEvent = event.toLowerCase();
      if (lowerCaseEvent !== event && vm._events[lowerCaseEvent]) {
        tip(
          "Event \"" + lowerCaseEvent + "\" is emitted in component " +
          (formatComponentName(vm)) + " but the handler is registered for \"" + event + "\". " +
          "Note that HTML attributes are case-insensitive and you cannot use " +
          "v-on to listen to camelCase events when using in-DOM templates. " +
          "You should probably use \"" + (hyphenate(event)) + "\" instead of \"" + event + "\"."
        );
      }
    }
    var cbs = vm._events[event];
    if (cbs) {
      cbs = cbs.length > 1 ? toArray(cbs) : cbs;
      var args = toArray(arguments, 1);
      var info = "event handler for \"" + event + "\"";
      for (var i = 0, l = cbs.length; i < l; i++) {
        invokeWithErrorHandling(cbs[i], vm, args, vm, info);
      }
    }
    return vm
  };
}

/*  */

var activeInstance = null;
var isUpdatingChildComponent = false;

function setActiveInstance(vm) {
  var prevActiveInstance = activeInstance;
  activeInstance = vm;
  return function () {
    activeInstance = prevActiveInstance;
  }
}

function initLifecycle (vm) {
  var options = vm.$options;

  // locate first non-abstract parent
  var parent = options.parent;
  if (parent && !options.abstract) {
    while (parent.$options.abstract && parent.$parent) {
      parent = parent.$parent;
    }
    parent.$children.push(vm);
  }

  vm.$parent = parent;
  vm.$root = parent ? parent.$root : vm;

  vm.$children = [];
  vm.$refs = {};

  vm._watcher = null;
  vm._inactive = null;
  vm._directInactive = false;
  vm._isMounted = false;
  vm._isDestroyed = false;
  vm._isBeingDestroyed = false;
}

function lifecycleMixin (Vue) {
  Vue.prototype._update = function (vnode, hydrating) {
    var vm = this;
    var prevEl = vm.$el;
    var prevVnode = vm._vnode;
    var restoreActiveInstance = setActiveInstance(vm);
    vm._vnode = vnode;
    // Vue.prototype.__patch__ is injected in entry points
    // based on the rendering backend used.
    if (!prevVnode) {
      // initial render
      vm.$el = vm.__patch__(vm.$el, vnode, hydrating, false /* removeOnly */);
    } else {
      // updates
      vm.$el = vm.__patch__(prevVnode, vnode);
    }
    restoreActiveInstance();
    // update __vue__ reference
    if (prevEl) {
      prevEl.__vue__ = null;
    }
    if (vm.$el) {
      vm.$el.__vue__ = vm;
    }
    // if parent is an HOC, update its $el as well
    if (vm.$vnode && vm.$parent && vm.$vnode === vm.$parent._vnode) {
      vm.$parent.$el = vm.$el;
    }
    // updated hook is called by the scheduler to ensure that children are
    // updated in a parent's updated hook.
  };

  Vue.prototype.$forceUpdate = function () {
    var vm = this;
    if (vm._watcher) {
      vm._watcher.update();
    }
  };

  Vue.prototype.$destroy = function () {
    var vm = this;
    if (vm._isBeingDestroyed) {
      return
    }
    callHook(vm, 'beforeDestroy');
    vm._isBeingDestroyed = true;
    // remove self from parent
    var parent = vm.$parent;
    if (parent && !parent._isBeingDestroyed && !vm.$options.abstract) {
      remove(parent.$children, vm);
    }
    // teardown watchers
    if (vm._watcher) {
      vm._watcher.teardown();
    }
    var i = vm._watchers.length;
    while (i--) {
      vm._watchers[i].teardown();
    }
    // remove reference from data ob
    // frozen object may not have observer.
    if (vm._data.__ob__) {
      vm._data.__ob__.vmCount--;
    }
    // call the last hook...
    vm._isDestroyed = true;
    // invoke destroy hooks on current rendered tree
    vm.__patch__(vm._vnode, null);
    // fire destroyed hook
    callHook(vm, 'destroyed');
    // turn off all instance listeners.
    vm.$off();
    // remove __vue__ reference
    if (vm.$el) {
      vm.$el.__vue__ = null;
    }
    // release circular reference (#6759)
    if (vm.$vnode) {
      vm.$vnode.parent = null;
    }
  };
}

function updateChildComponent (
  vm,
  propsData,
  listeners,
  parentVnode,
  renderChildren
) {
  if (true) {
    isUpdatingChildComponent = true;
  }

  // determine whether component has slot children
  // we need to do this before overwriting $options._renderChildren.

  // check if there are dynamic scopedSlots (hand-written or compiled but with
  // dynamic slot names). Static scoped slots compiled from template has the
  // "$stable" marker.
  var newScopedSlots = parentVnode.data.scopedSlots;
  var oldScopedSlots = vm.$scopedSlots;
  var hasDynamicScopedSlot = !!(
    (newScopedSlots && !newScopedSlots.$stable) ||
    (oldScopedSlots !== emptyObject && !oldScopedSlots.$stable) ||
    (newScopedSlots && vm.$scopedSlots.$key !== newScopedSlots.$key)
  );

  // Any static slot children from the parent may have changed during parent's
  // update. Dynamic scoped slots may also have changed. In such cases, a forced
  // update is necessary to ensure correctness.
  var needsForceUpdate = !!(
    renderChildren ||               // has new static slots
    vm.$options._renderChildren ||  // has old static slots
    hasDynamicScopedSlot
  );

  vm.$options._parentVnode = parentVnode;
  vm.$vnode = parentVnode; // update vm's placeholder node without re-render

  if (vm._vnode) { // update child tree's parent
    vm._vnode.parent = parentVnode;
  }
  vm.$options._renderChildren = renderChildren;

  // update $attrs and $listeners hash
  // these are also reactive so they may trigger child update if the child
  // used them during render
  vm.$attrs = parentVnode.data.attrs || emptyObject;
  vm.$listeners = listeners || emptyObject;

  // update props
  if (propsData && vm.$options.props) {
    toggleObserving(false);
    var props = vm._props;
    var propKeys = vm.$options._propKeys || [];
    for (var i = 0; i < propKeys.length; i++) {
      var key = propKeys[i];
      var propOptions = vm.$options.props; // wtf flow?
      props[key] = validateProp(key, propOptions, propsData, vm);
    }
    toggleObserving(true);
    // keep a copy of raw propsData
    vm.$options.propsData = propsData;
  }
  
  // fixed by xxxxxx update properties(mp runtime)
  vm._$updateProperties && vm._$updateProperties(vm);
  
  // update listeners
  listeners = listeners || emptyObject;
  var oldListeners = vm.$options._parentListeners;
  vm.$options._parentListeners = listeners;
  updateComponentListeners(vm, listeners, oldListeners);

  // resolve slots + force update if has children
  if (needsForceUpdate) {
    vm.$slots = resolveSlots(renderChildren, parentVnode.context);
    vm.$forceUpdate();
  }

  if (true) {
    isUpdatingChildComponent = false;
  }
}

function isInInactiveTree (vm) {
  while (vm && (vm = vm.$parent)) {
    if (vm._inactive) { return true }
  }
  return false
}

function activateChildComponent (vm, direct) {
  if (direct) {
    vm._directInactive = false;
    if (isInInactiveTree(vm)) {
      return
    }
  } else if (vm._directInactive) {
    return
  }
  if (vm._inactive || vm._inactive === null) {
    vm._inactive = false;
    for (var i = 0; i < vm.$children.length; i++) {
      activateChildComponent(vm.$children[i]);
    }
    callHook(vm, 'activated');
  }
}

function deactivateChildComponent (vm, direct) {
  if (direct) {
    vm._directInactive = true;
    if (isInInactiveTree(vm)) {
      return
    }
  }
  if (!vm._inactive) {
    vm._inactive = true;
    for (var i = 0; i < vm.$children.length; i++) {
      deactivateChildComponent(vm.$children[i]);
    }
    callHook(vm, 'deactivated');
  }
}

function callHook (vm, hook) {
  // #7573 disable dep collection when invoking lifecycle hooks
  pushTarget();
  var handlers = vm.$options[hook];
  var info = hook + " hook";
  if (handlers) {
    for (var i = 0, j = handlers.length; i < j; i++) {
      invokeWithErrorHandling(handlers[i], vm, null, vm, info);
    }
  }
  if (vm._hasHookEvent) {
    vm.$emit('hook:' + hook);
  }
  popTarget();
}

/*  */

var MAX_UPDATE_COUNT = 100;

var queue = [];
var activatedChildren = [];
var has = {};
var circular = {};
var waiting = false;
var flushing = false;
var index = 0;

/**
 * Reset the scheduler's state.
 */
function resetSchedulerState () {
  index = queue.length = activatedChildren.length = 0;
  has = {};
  if (true) {
    circular = {};
  }
  waiting = flushing = false;
}

// Async edge case #6566 requires saving the timestamp when event listeners are
// attached. However, calling performance.now() has a perf overhead especially
// if the page has thousands of event listeners. Instead, we take a timestamp
// every time the scheduler flushes and use that for all event listeners
// attached during that flush.
var currentFlushTimestamp = 0;

// Async edge case fix requires storing an event listener's attach timestamp.
var getNow = Date.now;

// Determine what event timestamp the browser is using. Annoyingly, the
// timestamp can either be hi-res (relative to page load) or low-res
// (relative to UNIX epoch), so in order to compare time we have to use the
// same timestamp type when saving the flush timestamp.
// All IE versions use low-res event timestamps, and have problematic clock
// implementations (#9632)
if (inBrowser && !isIE) {
  var performance = window.performance;
  if (
    performance &&
    typeof performance.now === 'function' &&
    getNow() > document.createEvent('Event').timeStamp
  ) {
    // if the event timestamp, although evaluated AFTER the Date.now(), is
    // smaller than it, it means the event is using a hi-res timestamp,
    // and we need to use the hi-res version for event listener timestamps as
    // well.
    getNow = function () { return performance.now(); };
  }
}

/**
 * Flush both queues and run the watchers.
 */
function flushSchedulerQueue () {
  currentFlushTimestamp = getNow();
  flushing = true;
  var watcher, id;

  // Sort queue before flush.
  // This ensures that:
  // 1. Components are updated from parent to child. (because parent is always
  //    created before the child)
  // 2. A component's user watchers are run before its render watcher (because
  //    user watchers are created before the render watcher)
  // 3. If a component is destroyed during a parent component's watcher run,
  //    its watchers can be skipped.
  queue.sort(function (a, b) { return a.id - b.id; });

  // do not cache length because more watchers might be pushed
  // as we run existing watchers
  for (index = 0; index < queue.length; index++) {
    watcher = queue[index];
    if (watcher.before) {
      watcher.before();
    }
    id = watcher.id;
    has[id] = null;
    watcher.run();
    // in dev build, check and stop circular updates.
    if ( true && has[id] != null) {
      circular[id] = (circular[id] || 0) + 1;
      if (circular[id] > MAX_UPDATE_COUNT) {
        warn(
          'You may have an infinite update loop ' + (
            watcher.user
              ? ("in watcher with expression \"" + (watcher.expression) + "\"")
              : "in a component render function."
          ),
          watcher.vm
        );
        break
      }
    }
  }

  // keep copies of post queues before resetting state
  var activatedQueue = activatedChildren.slice();
  var updatedQueue = queue.slice();

  resetSchedulerState();

  // call component updated and activated hooks
  callActivatedHooks(activatedQueue);
  callUpdatedHooks(updatedQueue);

  // devtool hook
  /* istanbul ignore if */
  if (devtools && config.devtools) {
    devtools.emit('flush');
  }
}

function callUpdatedHooks (queue) {
  var i = queue.length;
  while (i--) {
    var watcher = queue[i];
    var vm = watcher.vm;
    if (vm._watcher === watcher && vm._isMounted && !vm._isDestroyed) {
      callHook(vm, 'updated');
    }
  }
}

/**
 * Queue a kept-alive component that was activated during patch.
 * The queue will be processed after the entire tree has been patched.
 */
function queueActivatedComponent (vm) {
  // setting _inactive to false here so that a render function can
  // rely on checking whether it's in an inactive tree (e.g. router-view)
  vm._inactive = false;
  activatedChildren.push(vm);
}

function callActivatedHooks (queue) {
  for (var i = 0; i < queue.length; i++) {
    queue[i]._inactive = true;
    activateChildComponent(queue[i], true /* true */);
  }
}

/**
 * Push a watcher into the watcher queue.
 * Jobs with duplicate IDs will be skipped unless it's
 * pushed when the queue is being flushed.
 */
function queueWatcher (watcher) {
  var id = watcher.id;
  if (has[id] == null) {
    has[id] = true;
    if (!flushing) {
      queue.push(watcher);
    } else {
      // if already flushing, splice the watcher based on its id
      // if already past its id, it will be run next immediately.
      var i = queue.length - 1;
      while (i > index && queue[i].id > watcher.id) {
        i--;
      }
      queue.splice(i + 1, 0, watcher);
    }
    // queue the flush
    if (!waiting) {
      waiting = true;

      if ( true && !config.async) {
        flushSchedulerQueue();
        return
      }
      nextTick(flushSchedulerQueue);
    }
  }
}

/*  */



var uid$2 = 0;

/**
 * A watcher parses an expression, collects dependencies,
 * and fires callback when the expression value changes.
 * This is used for both the $watch() api and directives.
 */
var Watcher = function Watcher (
  vm,
  expOrFn,
  cb,
  options,
  isRenderWatcher
) {
  this.vm = vm;
  if (isRenderWatcher) {
    vm._watcher = this;
  }
  vm._watchers.push(this);
  // options
  if (options) {
    this.deep = !!options.deep;
    this.user = !!options.user;
    this.lazy = !!options.lazy;
    this.sync = !!options.sync;
    this.before = options.before;
  } else {
    this.deep = this.user = this.lazy = this.sync = false;
  }
  this.cb = cb;
  this.id = ++uid$2; // uid for batching
  this.active = true;
  this.dirty = this.lazy; // for lazy watchers
  this.deps = [];
  this.newDeps = [];
  this.depIds = new _Set();
  this.newDepIds = new _Set();
  this.expression =  true
    ? expOrFn.toString()
    : undefined;
  // parse expression for getter
  if (typeof expOrFn === 'function') {
    this.getter = expOrFn;
  } else {
    this.getter = parsePath(expOrFn);
    if (!this.getter) {
      this.getter = noop;
       true && warn(
        "Failed watching path: \"" + expOrFn + "\" " +
        'Watcher only accepts simple dot-delimited paths. ' +
        'For full control, use a function instead.',
        vm
      );
    }
  }
  this.value = this.lazy
    ? undefined
    : this.get();
};

/**
 * Evaluate the getter, and re-collect dependencies.
 */
Watcher.prototype.get = function get () {
  pushTarget(this);
  var value;
  var vm = this.vm;
  try {
    value = this.getter.call(vm, vm);
  } catch (e) {
    if (this.user) {
      handleError(e, vm, ("getter for watcher \"" + (this.expression) + "\""));
    } else {
      throw e
    }
  } finally {
    // "touch" every property so they are all tracked as
    // dependencies for deep watching
    if (this.deep) {
      traverse(value);
    }
    popTarget();
    this.cleanupDeps();
  }
  return value
};

/**
 * Add a dependency to this directive.
 */
Watcher.prototype.addDep = function addDep (dep) {
  var id = dep.id;
  if (!this.newDepIds.has(id)) {
    this.newDepIds.add(id);
    this.newDeps.push(dep);
    if (!this.depIds.has(id)) {
      dep.addSub(this);
    }
  }
};

/**
 * Clean up for dependency collection.
 */
Watcher.prototype.cleanupDeps = function cleanupDeps () {
  var i = this.deps.length;
  while (i--) {
    var dep = this.deps[i];
    if (!this.newDepIds.has(dep.id)) {
      dep.removeSub(this);
    }
  }
  var tmp = this.depIds;
  this.depIds = this.newDepIds;
  this.newDepIds = tmp;
  this.newDepIds.clear();
  tmp = this.deps;
  this.deps = this.newDeps;
  this.newDeps = tmp;
  this.newDeps.length = 0;
};

/**
 * Subscriber interface.
 * Will be called when a dependency changes.
 */
Watcher.prototype.update = function update () {
  /* istanbul ignore else */
  if (this.lazy) {
    this.dirty = true;
  } else if (this.sync) {
    this.run();
  } else {
    queueWatcher(this);
  }
};

/**
 * Scheduler job interface.
 * Will be called by the scheduler.
 */
Watcher.prototype.run = function run () {
  if (this.active) {
    var value = this.get();
    if (
      value !== this.value ||
      // Deep watchers and watchers on Object/Arrays should fire even
      // when the value is the same, because the value may
      // have mutated.
      isObject(value) ||
      this.deep
    ) {
      // set new value
      var oldValue = this.value;
      this.value = value;
      if (this.user) {
        try {
          this.cb.call(this.vm, value, oldValue);
        } catch (e) {
          handleError(e, this.vm, ("callback for watcher \"" + (this.expression) + "\""));
        }
      } else {
        this.cb.call(this.vm, value, oldValue);
      }
    }
  }
};

/**
 * Evaluate the value of the watcher.
 * This only gets called for lazy watchers.
 */
Watcher.prototype.evaluate = function evaluate () {
  this.value = this.get();
  this.dirty = false;
};

/**
 * Depend on all deps collected by this watcher.
 */
Watcher.prototype.depend = function depend () {
  var i = this.deps.length;
  while (i--) {
    this.deps[i].depend();
  }
};

/**
 * Remove self from all dependencies' subscriber list.
 */
Watcher.prototype.teardown = function teardown () {
  if (this.active) {
    // remove self from vm's watcher list
    // this is a somewhat expensive operation so we skip it
    // if the vm is being destroyed.
    if (!this.vm._isBeingDestroyed) {
      remove(this.vm._watchers, this);
    }
    var i = this.deps.length;
    while (i--) {
      this.deps[i].removeSub(this);
    }
    this.active = false;
  }
};

/*  */

var sharedPropertyDefinition = {
  enumerable: true,
  configurable: true,
  get: noop,
  set: noop
};

function proxy (target, sourceKey, key) {
  sharedPropertyDefinition.get = function proxyGetter () {
    return this[sourceKey][key]
  };
  sharedPropertyDefinition.set = function proxySetter (val) {
    this[sourceKey][key] = val;
  };
  Object.defineProperty(target, key, sharedPropertyDefinition);
}

function initState (vm) {
  vm._watchers = [];
  var opts = vm.$options;
  if (opts.props) { initProps(vm, opts.props); }
  if (opts.methods) { initMethods(vm, opts.methods); }
  if (opts.data) {
    initData(vm);
  } else {
    observe(vm._data = {}, true /* asRootData */);
  }
  if (opts.computed) { initComputed(vm, opts.computed); }
  if (opts.watch && opts.watch !== nativeWatch) {
    initWatch(vm, opts.watch);
  }
}

function initProps (vm, propsOptions) {
  var propsData = vm.$options.propsData || {};
  var props = vm._props = {};
  // cache prop keys so that future props updates can iterate using Array
  // instead of dynamic object key enumeration.
  var keys = vm.$options._propKeys = [];
  var isRoot = !vm.$parent;
  // root instance props should be converted
  if (!isRoot) {
    toggleObserving(false);
  }
  var loop = function ( key ) {
    keys.push(key);
    var value = validateProp(key, propsOptions, propsData, vm);
    /* istanbul ignore else */
    if (true) {
      var hyphenatedKey = hyphenate(key);
      if (isReservedAttribute(hyphenatedKey) ||
          config.isReservedAttr(hyphenatedKey)) {
        warn(
          ("\"" + hyphenatedKey + "\" is a reserved attribute and cannot be used as component prop."),
          vm
        );
      }
      defineReactive$$1(props, key, value, function () {
        if (!isRoot && !isUpdatingChildComponent) {
          {
            if(vm.mpHost === 'mp-baidu' || vm.mpHost === 'mp-kuaishou' || vm.mpHost === 'mp-xhs'){//百度、快手、小红书 observer 在 setData callback 之后触发，直接忽略该 warn
                return
            }
            //fixed by xxxxxx __next_tick_pending,uni://form-field 时不告警
            if(
                key === 'value' && 
                Array.isArray(vm.$options.behaviors) &&
                vm.$options.behaviors.indexOf('uni://form-field') !== -1
              ){
              return
            }
            if(vm._getFormData){
              return
            }
            var $parent = vm.$parent;
            while($parent){
              if($parent.__next_tick_pending){
                return  
              }
              $parent = $parent.$parent;
            }
          }
          warn(
            "Avoid mutating a prop directly since the value will be " +
            "overwritten whenever the parent component re-renders. " +
            "Instead, use a data or computed property based on the prop's " +
            "value. Prop being mutated: \"" + key + "\"",
            vm
          );
        }
      });
    } else {}
    // static props are already proxied on the component's prototype
    // during Vue.extend(). We only need to proxy props defined at
    // instantiation here.
    if (!(key in vm)) {
      proxy(vm, "_props", key);
    }
  };

  for (var key in propsOptions) loop( key );
  toggleObserving(true);
}

function initData (vm) {
  var data = vm.$options.data;
  data = vm._data = typeof data === 'function'
    ? getData(data, vm)
    : data || {};
  if (!isPlainObject(data)) {
    data = {};
     true && warn(
      'data functions should return an object:\n' +
      'https://vuejs.org/v2/guide/components.html#data-Must-Be-a-Function',
      vm
    );
  }
  // proxy data on instance
  var keys = Object.keys(data);
  var props = vm.$options.props;
  var methods = vm.$options.methods;
  var i = keys.length;
  while (i--) {
    var key = keys[i];
    if (true) {
      if (methods && hasOwn(methods, key)) {
        warn(
          ("Method \"" + key + "\" has already been defined as a data property."),
          vm
        );
      }
    }
    if (props && hasOwn(props, key)) {
       true && warn(
        "The data property \"" + key + "\" is already declared as a prop. " +
        "Use prop default value instead.",
        vm
      );
    } else if (!isReserved(key)) {
      proxy(vm, "_data", key);
    }
  }
  // observe data
  observe(data, true /* asRootData */);
}

function getData (data, vm) {
  // #7573 disable dep collection when invoking data getters
  pushTarget();
  try {
    return data.call(vm, vm)
  } catch (e) {
    handleError(e, vm, "data()");
    return {}
  } finally {
    popTarget();
  }
}

var computedWatcherOptions = { lazy: true };

function initComputed (vm, computed) {
  // $flow-disable-line
  var watchers = vm._computedWatchers = Object.create(null);
  // computed properties are just getters during SSR
  var isSSR = isServerRendering();

  for (var key in computed) {
    var userDef = computed[key];
    var getter = typeof userDef === 'function' ? userDef : userDef.get;
    if ( true && getter == null) {
      warn(
        ("Getter is missing for computed property \"" + key + "\"."),
        vm
      );
    }

    if (!isSSR) {
      // create internal watcher for the computed property.
      watchers[key] = new Watcher(
        vm,
        getter || noop,
        noop,
        computedWatcherOptions
      );
    }

    // component-defined computed properties are already defined on the
    // component prototype. We only need to define computed properties defined
    // at instantiation here.
    if (!(key in vm)) {
      defineComputed(vm, key, userDef);
    } else if (true) {
      if (key in vm.$data) {
        warn(("The computed property \"" + key + "\" is already defined in data."), vm);
      } else if (vm.$options.props && key in vm.$options.props) {
        warn(("The computed property \"" + key + "\" is already defined as a prop."), vm);
      }
    }
  }
}

function defineComputed (
  target,
  key,
  userDef
) {
  var shouldCache = !isServerRendering();
  if (typeof userDef === 'function') {
    sharedPropertyDefinition.get = shouldCache
      ? createComputedGetter(key)
      : createGetterInvoker(userDef);
    sharedPropertyDefinition.set = noop;
  } else {
    sharedPropertyDefinition.get = userDef.get
      ? shouldCache && userDef.cache !== false
        ? createComputedGetter(key)
        : createGetterInvoker(userDef.get)
      : noop;
    sharedPropertyDefinition.set = userDef.set || noop;
  }
  if ( true &&
      sharedPropertyDefinition.set === noop) {
    sharedPropertyDefinition.set = function () {
      warn(
        ("Computed property \"" + key + "\" was assigned to but it has no setter."),
        this
      );
    };
  }
  Object.defineProperty(target, key, sharedPropertyDefinition);
}

function createComputedGetter (key) {
  return function computedGetter () {
    var watcher = this._computedWatchers && this._computedWatchers[key];
    if (watcher) {
      if (watcher.dirty) {
        watcher.evaluate();
      }
      if (Dep.SharedObject.target) {// fixed by xxxxxx
        watcher.depend();
      }
      return watcher.value
    }
  }
}

function createGetterInvoker(fn) {
  return function computedGetter () {
    return fn.call(this, this)
  }
}

function initMethods (vm, methods) {
  var props = vm.$options.props;
  for (var key in methods) {
    if (true) {
      if (typeof methods[key] !== 'function') {
        warn(
          "Method \"" + key + "\" has type \"" + (typeof methods[key]) + "\" in the component definition. " +
          "Did you reference the function correctly?",
          vm
        );
      }
      if (props && hasOwn(props, key)) {
        warn(
          ("Method \"" + key + "\" has already been defined as a prop."),
          vm
        );
      }
      if ((key in vm) && isReserved(key)) {
        warn(
          "Method \"" + key + "\" conflicts with an existing Vue instance method. " +
          "Avoid defining component methods that start with _ or $."
        );
      }
    }
    vm[key] = typeof methods[key] !== 'function' ? noop : bind(methods[key], vm);
  }
}

function initWatch (vm, watch) {
  for (var key in watch) {
    var handler = watch[key];
    if (Array.isArray(handler)) {
      for (var i = 0; i < handler.length; i++) {
        createWatcher(vm, key, handler[i]);
      }
    } else {
      createWatcher(vm, key, handler);
    }
  }
}

function createWatcher (
  vm,
  expOrFn,
  handler,
  options
) {
  if (isPlainObject(handler)) {
    options = handler;
    handler = handler.handler;
  }
  if (typeof handler === 'string') {
    handler = vm[handler];
  }
  return vm.$watch(expOrFn, handler, options)
}

function stateMixin (Vue) {
  // flow somehow has problems with directly declared definition object
  // when using Object.defineProperty, so we have to procedurally build up
  // the object here.
  var dataDef = {};
  dataDef.get = function () { return this._data };
  var propsDef = {};
  propsDef.get = function () { return this._props };
  if (true) {
    dataDef.set = function () {
      warn(
        'Avoid replacing instance root $data. ' +
        'Use nested data properties instead.',
        this
      );
    };
    propsDef.set = function () {
      warn("$props is readonly.", this);
    };
  }
  Object.defineProperty(Vue.prototype, '$data', dataDef);
  Object.defineProperty(Vue.prototype, '$props', propsDef);

  Vue.prototype.$set = set;
  Vue.prototype.$delete = del;

  Vue.prototype.$watch = function (
    expOrFn,
    cb,
    options
  ) {
    var vm = this;
    if (isPlainObject(cb)) {
      return createWatcher(vm, expOrFn, cb, options)
    }
    options = options || {};
    options.user = true;
    var watcher = new Watcher(vm, expOrFn, cb, options);
    if (options.immediate) {
      try {
        cb.call(vm, watcher.value);
      } catch (error) {
        handleError(error, vm, ("callback for immediate watcher \"" + (watcher.expression) + "\""));
      }
    }
    return function unwatchFn () {
      watcher.teardown();
    }
  };
}

/*  */

var uid$3 = 0;

function initMixin (Vue) {
  Vue.prototype._init = function (options) {
    var vm = this;
    // a uid
    vm._uid = uid$3++;

    var startTag, endTag;
    /* istanbul ignore if */
    if ( true && config.performance && mark) {
      startTag = "vue-perf-start:" + (vm._uid);
      endTag = "vue-perf-end:" + (vm._uid);
      mark(startTag);
    }

    // a flag to avoid this being observed
    vm._isVue = true;
    // merge options
    if (options && options._isComponent) {
      // optimize internal component instantiation
      // since dynamic options merging is pretty slow, and none of the
      // internal component options needs special treatment.
      initInternalComponent(vm, options);
    } else {
      vm.$options = mergeOptions(
        resolveConstructorOptions(vm.constructor),
        options || {},
        vm
      );
    }
    /* istanbul ignore else */
    if (true) {
      initProxy(vm);
    } else {}
    // expose real self
    vm._self = vm;
    initLifecycle(vm);
    initEvents(vm);
    initRender(vm);
    callHook(vm, 'beforeCreate');
    !vm._$fallback && initInjections(vm); // resolve injections before data/props  
    initState(vm);
    !vm._$fallback && initProvide(vm); // resolve provide after data/props
    !vm._$fallback && callHook(vm, 'created');      

    /* istanbul ignore if */
    if ( true && config.performance && mark) {
      vm._name = formatComponentName(vm, false);
      mark(endTag);
      measure(("vue " + (vm._name) + " init"), startTag, endTag);
    }

    if (vm.$options.el) {
      vm.$mount(vm.$options.el);
    }
  };
}

function initInternalComponent (vm, options) {
  var opts = vm.$options = Object.create(vm.constructor.options);
  // doing this because it's faster than dynamic enumeration.
  var parentVnode = options._parentVnode;
  opts.parent = options.parent;
  opts._parentVnode = parentVnode;

  var vnodeComponentOptions = parentVnode.componentOptions;
  opts.propsData = vnodeComponentOptions.propsData;
  opts._parentListeners = vnodeComponentOptions.listeners;
  opts._renderChildren = vnodeComponentOptions.children;
  opts._componentTag = vnodeComponentOptions.tag;

  if (options.render) {
    opts.render = options.render;
    opts.staticRenderFns = options.staticRenderFns;
  }
}

function resolveConstructorOptions (Ctor) {
  var options = Ctor.options;
  if (Ctor.super) {
    var superOptions = resolveConstructorOptions(Ctor.super);
    var cachedSuperOptions = Ctor.superOptions;
    if (superOptions !== cachedSuperOptions) {
      // super option changed,
      // need to resolve new options.
      Ctor.superOptions = superOptions;
      // check if there are any late-modified/attached options (#4976)
      var modifiedOptions = resolveModifiedOptions(Ctor);
      // update base extend options
      if (modifiedOptions) {
        extend(Ctor.extendOptions, modifiedOptions);
      }
      options = Ctor.options = mergeOptions(superOptions, Ctor.extendOptions);
      if (options.name) {
        options.components[options.name] = Ctor;
      }
    }
  }
  return options
}

function resolveModifiedOptions (Ctor) {
  var modified;
  var latest = Ctor.options;
  var sealed = Ctor.sealedOptions;
  for (var key in latest) {
    if (latest[key] !== sealed[key]) {
      if (!modified) { modified = {}; }
      modified[key] = latest[key];
    }
  }
  return modified
}

function Vue (options) {
  if ( true &&
    !(this instanceof Vue)
  ) {
    warn('Vue is a constructor and should be called with the `new` keyword');
  }
  this._init(options);
}

initMixin(Vue);
stateMixin(Vue);
eventsMixin(Vue);
lifecycleMixin(Vue);
renderMixin(Vue);

/*  */

function initUse (Vue) {
  Vue.use = function (plugin) {
    var installedPlugins = (this._installedPlugins || (this._installedPlugins = []));
    if (installedPlugins.indexOf(plugin) > -1) {
      return this
    }

    // additional parameters
    var args = toArray(arguments, 1);
    args.unshift(this);
    if (typeof plugin.install === 'function') {
      plugin.install.apply(plugin, args);
    } else if (typeof plugin === 'function') {
      plugin.apply(null, args);
    }
    installedPlugins.push(plugin);
    return this
  };
}

/*  */

function initMixin$1 (Vue) {
  Vue.mixin = function (mixin) {
    this.options = mergeOptions(this.options, mixin);
    return this
  };
}

/*  */

function initExtend (Vue) {
  /**
   * Each instance constructor, including Vue, has a unique
   * cid. This enables us to create wrapped "child
   * constructors" for prototypal inheritance and cache them.
   */
  Vue.cid = 0;
  var cid = 1;

  /**
   * Class inheritance
   */
  Vue.extend = function (extendOptions) {
    extendOptions = extendOptions || {};
    var Super = this;
    var SuperId = Super.cid;
    var cachedCtors = extendOptions._Ctor || (extendOptions._Ctor = {});
    if (cachedCtors[SuperId]) {
      return cachedCtors[SuperId]
    }

    var name = extendOptions.name || Super.options.name;
    if ( true && name) {
      validateComponentName(name);
    }

    var Sub = function VueComponent (options) {
      this._init(options);
    };
    Sub.prototype = Object.create(Super.prototype);
    Sub.prototype.constructor = Sub;
    Sub.cid = cid++;
    Sub.options = mergeOptions(
      Super.options,
      extendOptions
    );
    Sub['super'] = Super;

    // For props and computed properties, we define the proxy getters on
    // the Vue instances at extension time, on the extended prototype. This
    // avoids Object.defineProperty calls for each instance created.
    if (Sub.options.props) {
      initProps$1(Sub);
    }
    if (Sub.options.computed) {
      initComputed$1(Sub);
    }

    // allow further extension/mixin/plugin usage
    Sub.extend = Super.extend;
    Sub.mixin = Super.mixin;
    Sub.use = Super.use;

    // create asset registers, so extended classes
    // can have their private assets too.
    ASSET_TYPES.forEach(function (type) {
      Sub[type] = Super[type];
    });
    // enable recursive self-lookup
    if (name) {
      Sub.options.components[name] = Sub;
    }

    // keep a reference to the super options at extension time.
    // later at instantiation we can check if Super's options have
    // been updated.
    Sub.superOptions = Super.options;
    Sub.extendOptions = extendOptions;
    Sub.sealedOptions = extend({}, Sub.options);

    // cache constructor
    cachedCtors[SuperId] = Sub;
    return Sub
  };
}

function initProps$1 (Comp) {
  var props = Comp.options.props;
  for (var key in props) {
    proxy(Comp.prototype, "_props", key);
  }
}

function initComputed$1 (Comp) {
  var computed = Comp.options.computed;
  for (var key in computed) {
    defineComputed(Comp.prototype, key, computed[key]);
  }
}

/*  */

function initAssetRegisters (Vue) {
  /**
   * Create asset registration methods.
   */
  ASSET_TYPES.forEach(function (type) {
    Vue[type] = function (
      id,
      definition
    ) {
      if (!definition) {
        return this.options[type + 's'][id]
      } else {
        /* istanbul ignore if */
        if ( true && type === 'component') {
          validateComponentName(id);
        }
        if (type === 'component' && isPlainObject(definition)) {
          definition.name = definition.name || id;
          definition = this.options._base.extend(definition);
        }
        if (type === 'directive' && typeof definition === 'function') {
          definition = { bind: definition, update: definition };
        }
        this.options[type + 's'][id] = definition;
        return definition
      }
    };
  });
}

/*  */



function getComponentName (opts) {
  return opts && (opts.Ctor.options.name || opts.tag)
}

function matches (pattern, name) {
  if (Array.isArray(pattern)) {
    return pattern.indexOf(name) > -1
  } else if (typeof pattern === 'string') {
    return pattern.split(',').indexOf(name) > -1
  } else if (isRegExp(pattern)) {
    return pattern.test(name)
  }
  /* istanbul ignore next */
  return false
}

function pruneCache (keepAliveInstance, filter) {
  var cache = keepAliveInstance.cache;
  var keys = keepAliveInstance.keys;
  var _vnode = keepAliveInstance._vnode;
  for (var key in cache) {
    var cachedNode = cache[key];
    if (cachedNode) {
      var name = getComponentName(cachedNode.componentOptions);
      if (name && !filter(name)) {
        pruneCacheEntry(cache, key, keys, _vnode);
      }
    }
  }
}

function pruneCacheEntry (
  cache,
  key,
  keys,
  current
) {
  var cached$$1 = cache[key];
  if (cached$$1 && (!current || cached$$1.tag !== current.tag)) {
    cached$$1.componentInstance.$destroy();
  }
  cache[key] = null;
  remove(keys, key);
}

var patternTypes = [String, RegExp, Array];

var KeepAlive = {
  name: 'keep-alive',
  abstract: true,

  props: {
    include: patternTypes,
    exclude: patternTypes,
    max: [String, Number]
  },

  created: function created () {
    this.cache = Object.create(null);
    this.keys = [];
  },

  destroyed: function destroyed () {
    for (var key in this.cache) {
      pruneCacheEntry(this.cache, key, this.keys);
    }
  },

  mounted: function mounted () {
    var this$1 = this;

    this.$watch('include', function (val) {
      pruneCache(this$1, function (name) { return matches(val, name); });
    });
    this.$watch('exclude', function (val) {
      pruneCache(this$1, function (name) { return !matches(val, name); });
    });
  },

  render: function render () {
    var slot = this.$slots.default;
    var vnode = getFirstComponentChild(slot);
    var componentOptions = vnode && vnode.componentOptions;
    if (componentOptions) {
      // check pattern
      var name = getComponentName(componentOptions);
      var ref = this;
      var include = ref.include;
      var exclude = ref.exclude;
      if (
        // not included
        (include && (!name || !matches(include, name))) ||
        // excluded
        (exclude && name && matches(exclude, name))
      ) {
        return vnode
      }

      var ref$1 = this;
      var cache = ref$1.cache;
      var keys = ref$1.keys;
      var key = vnode.key == null
        // same constructor may get registered as different local components
        // so cid alone is not enough (#3269)
        ? componentOptions.Ctor.cid + (componentOptions.tag ? ("::" + (componentOptions.tag)) : '')
        : vnode.key;
      if (cache[key]) {
        vnode.componentInstance = cache[key].componentInstance;
        // make current key freshest
        remove(keys, key);
        keys.push(key);
      } else {
        cache[key] = vnode;
        keys.push(key);
        // prune oldest entry
        if (this.max && keys.length > parseInt(this.max)) {
          pruneCacheEntry(cache, keys[0], keys, this._vnode);
        }
      }

      vnode.data.keepAlive = true;
    }
    return vnode || (slot && slot[0])
  }
};

var builtInComponents = {
  KeepAlive: KeepAlive
};

/*  */

function initGlobalAPI (Vue) {
  // config
  var configDef = {};
  configDef.get = function () { return config; };
  if (true) {
    configDef.set = function () {
      warn(
        'Do not replace the Vue.config object, set individual fields instead.'
      );
    };
  }
  Object.defineProperty(Vue, 'config', configDef);

  // exposed util methods.
  // NOTE: these are not considered part of the public API - avoid relying on
  // them unless you are aware of the risk.
  Vue.util = {
    warn: warn,
    extend: extend,
    mergeOptions: mergeOptions,
    defineReactive: defineReactive$$1
  };

  Vue.set = set;
  Vue.delete = del;
  Vue.nextTick = nextTick;

  // 2.6 explicit observable API
  Vue.observable = function (obj) {
    observe(obj);
    return obj
  };

  Vue.options = Object.create(null);
  ASSET_TYPES.forEach(function (type) {
    Vue.options[type + 's'] = Object.create(null);
  });

  // this is used to identify the "base" constructor to extend all plain-object
  // components with in Weex's multi-instance scenarios.
  Vue.options._base = Vue;

  extend(Vue.options.components, builtInComponents);

  initUse(Vue);
  initMixin$1(Vue);
  initExtend(Vue);
  initAssetRegisters(Vue);
}

initGlobalAPI(Vue);

Object.defineProperty(Vue.prototype, '$isServer', {
  get: isServerRendering
});

Object.defineProperty(Vue.prototype, '$ssrContext', {
  get: function get () {
    /* istanbul ignore next */
    return this.$vnode && this.$vnode.ssrContext
  }
});

// expose FunctionalRenderContext for ssr runtime helper installation
Object.defineProperty(Vue, 'FunctionalRenderContext', {
  value: FunctionalRenderContext
});

Vue.version = '2.6.11';

/**
 * https://raw.githubusercontent.com/Tencent/westore/master/packages/westore/utils/diff.js
 */
var ARRAYTYPE = '[object Array]';
var OBJECTTYPE = '[object Object]';
var NULLTYPE = '[object Null]';
var UNDEFINEDTYPE = '[object Undefined]';
// const FUNCTIONTYPE = '[object Function]'

function diff(current, pre) {
    var result = {};
    syncKeys(current, pre);
    _diff(current, pre, '', result);
    return result
}

function syncKeys(current, pre) {
    if (current === pre) { return }
    var rootCurrentType = type(current);
    var rootPreType = type(pre);
    if (rootCurrentType == OBJECTTYPE && rootPreType == OBJECTTYPE) {
        if(Object.keys(current).length >= Object.keys(pre).length){
            for (var key in pre) {
                var currentValue = current[key];
                if (currentValue === undefined) {
                    current[key] = null;
                } else {
                    syncKeys(currentValue, pre[key]);
                }
            }
        }
    } else if (rootCurrentType == ARRAYTYPE && rootPreType == ARRAYTYPE) {
        if (current.length >= pre.length) {
            pre.forEach(function (item, index) {
                syncKeys(current[index], item);
            });
        }
    }
}

function nullOrUndefined(currentType, preType) {
    if(
        (currentType === NULLTYPE || currentType === UNDEFINEDTYPE) && 
        (preType === NULLTYPE || preType === UNDEFINEDTYPE)
    ) {
        return false
    }
    return true
}

function _diff(current, pre, path, result) {
    if (current === pre) { return }
    var rootCurrentType = type(current);
    var rootPreType = type(pre);
    if (rootCurrentType == OBJECTTYPE) {
        if (rootPreType != OBJECTTYPE || Object.keys(current).length < Object.keys(pre).length) {
            setResult(result, path, current);
        } else {
            var loop = function ( key ) {
                var currentValue = current[key];
                var preValue = pre[key];
                var currentType = type(currentValue);
                var preType = type(preValue);
                if (currentType != ARRAYTYPE && currentType != OBJECTTYPE) {
                    if (currentValue !== pre[key] && nullOrUndefined(currentType, preType)) {
                        setResult(result, (path == '' ? '' : path + ".") + key, currentValue);
                    }
                } else if (currentType == ARRAYTYPE) {
                    if (preType != ARRAYTYPE) {
                        setResult(result, (path == '' ? '' : path + ".") + key, currentValue);
                    } else {
                        if (currentValue.length < preValue.length) {
                            setResult(result, (path == '' ? '' : path + ".") + key, currentValue);
                        } else {
                            currentValue.forEach(function (item, index) {
                                _diff(item, preValue[index], (path == '' ? '' : path + ".") + key + '[' + index + ']', result);
                            });
                        }
                    }
                } else if (currentType == OBJECTTYPE) {
                    if (preType != OBJECTTYPE || Object.keys(currentValue).length < Object.keys(preValue).length) {
                        setResult(result, (path == '' ? '' : path + ".") + key, currentValue);
                    } else {
                        for (var subKey in currentValue) {
                            _diff(currentValue[subKey], preValue[subKey], (path == '' ? '' : path + ".") + key + '.' + subKey, result);
                        }
                    }
                }
            };

            for (var key in current) loop( key );
        }
    } else if (rootCurrentType == ARRAYTYPE) {
        if (rootPreType != ARRAYTYPE) {
            setResult(result, path, current);
        } else {
            if (current.length < pre.length) {
                setResult(result, path, current);
            } else {
                current.forEach(function (item, index) {
                    _diff(item, pre[index], path + '[' + index + ']', result);
                });
            }
        }
    } else {
        setResult(result, path, current);
    }
}

function setResult(result, k, v) {
    // if (type(v) != FUNCTIONTYPE) {
        result[k] = v;
    // }
}

function type(obj) {
    return Object.prototype.toString.call(obj)
}

/*  */

function flushCallbacks$1(vm) {
    if (vm.__next_tick_callbacks && vm.__next_tick_callbacks.length) {
        if (Object({"VUE_APP_DARK_MODE":"false","VUE_APP_NAME":"shopBIZ","VUE_APP_PLATFORM":"mp-weixin","NODE_ENV":"development","BASE_URL":"/"}).VUE_APP_DEBUG) {
            var mpInstance = vm.$scope;
            console.log('[' + (+new Date) + '][' + (mpInstance.is || mpInstance.route) + '][' + vm._uid +
                ']:flushCallbacks[' + vm.__next_tick_callbacks.length + ']');
        }
        var copies = vm.__next_tick_callbacks.slice(0);
        vm.__next_tick_callbacks.length = 0;
        for (var i = 0; i < copies.length; i++) {
            copies[i]();
        }
    }
}

function hasRenderWatcher(vm) {
    return queue.find(function (watcher) { return vm._watcher === watcher; })
}

function nextTick$1(vm, cb) {
    //1.nextTick 之前 已 setData 且 setData 还未回调完成
    //2.nextTick 之前存在 render watcher
    if (!vm.__next_tick_pending && !hasRenderWatcher(vm)) {
        if(Object({"VUE_APP_DARK_MODE":"false","VUE_APP_NAME":"shopBIZ","VUE_APP_PLATFORM":"mp-weixin","NODE_ENV":"development","BASE_URL":"/"}).VUE_APP_DEBUG){
            var mpInstance = vm.$scope;
            console.log('[' + (+new Date) + '][' + (mpInstance.is || mpInstance.route) + '][' + vm._uid +
                ']:nextVueTick');
        }
        return nextTick(cb, vm)
    }else{
        if(Object({"VUE_APP_DARK_MODE":"false","VUE_APP_NAME":"shopBIZ","VUE_APP_PLATFORM":"mp-weixin","NODE_ENV":"development","BASE_URL":"/"}).VUE_APP_DEBUG){
            var mpInstance$1 = vm.$scope;
            console.log('[' + (+new Date) + '][' + (mpInstance$1.is || mpInstance$1.route) + '][' + vm._uid +
                ']:nextMPTick');
        }
    }
    var _resolve;
    if (!vm.__next_tick_callbacks) {
        vm.__next_tick_callbacks = [];
    }
    vm.__next_tick_callbacks.push(function () {
        if (cb) {
            try {
                cb.call(vm);
            } catch (e) {
                handleError(e, vm, 'nextTick');
            }
        } else if (_resolve) {
            _resolve(vm);
        }
    });
    // $flow-disable-line
    if (!cb && typeof Promise !== 'undefined') {
        return new Promise(function (resolve) {
            _resolve = resolve;
        })
    }
}

/*  */

function clearInstance(key, value) {
  // 简易去除 Vue 和小程序组件实例
  if (value) {
    if (value._isVue || value.__v_isMPComponent) {
      return {}
    }
  }
  return value
}

function cloneWithData(vm) {
  // 确保当前 vm 所有数据被同步
  var ret = Object.create(null);
  var dataKeys = [].concat(
    Object.keys(vm._data || {}),
    Object.keys(vm._computedWatchers || {}));

  dataKeys.reduce(function(ret, key) {
    ret[key] = vm[key];
    return ret
  }, ret);

  // vue-composition-api
  var compositionApiState = vm.__composition_api_state__ || vm.__secret_vfa_state__;
  var rawBindings = compositionApiState && compositionApiState.rawBindings;
  if (rawBindings) {
    Object.keys(rawBindings).forEach(function (key) {
      ret[key] = vm[key];
    });
  }

  //TODO 需要把无用数据处理掉，比如 list=>l0 则 list 需要移除，否则多传输一份数据
  Object.assign(ret, vm.$mp.data || {});
  if (
    Array.isArray(vm.$options.behaviors) &&
    vm.$options.behaviors.indexOf('uni://form-field') !== -1
  ) { //form-field
    ret['name'] = vm.name;
    ret['value'] = vm.value;
  }

  return JSON.parse(JSON.stringify(ret, clearInstance))
}

var patch = function(oldVnode, vnode) {
  var this$1 = this;

  if (vnode === null) { //destroy
    return
  }
  if (this.mpType === 'page' || this.mpType === 'component') {
    var mpInstance = this.$scope;
    var data = Object.create(null);
    try {
      data = cloneWithData(this);
    } catch (err) {
      console.error(err);
    }
    data.__webviewId__ = mpInstance.data.__webviewId__;
    var mpData = Object.create(null);
    Object.keys(data).forEach(function (key) { //仅同步 data 中有的数据
      mpData[key] = mpInstance.data[key];
    });
    var diffData = this.$shouldDiffData === false ? data : diff(data, mpData);
    if (Object.keys(diffData).length) {
      if (Object({"VUE_APP_DARK_MODE":"false","VUE_APP_NAME":"shopBIZ","VUE_APP_PLATFORM":"mp-weixin","NODE_ENV":"development","BASE_URL":"/"}).VUE_APP_DEBUG) {
        console.log('[' + (+new Date) + '][' + (mpInstance.is || mpInstance.route) + '][' + this._uid +
          ']差量更新',
          JSON.stringify(diffData));
      }
      this.__next_tick_pending = true;
      mpInstance.setData(diffData, function () {
        this$1.__next_tick_pending = false;
        flushCallbacks$1(this$1);
      });
    } else {
      flushCallbacks$1(this);
    }
  }
};

/*  */

function createEmptyRender() {

}

function mountComponent$1(
  vm,
  el,
  hydrating
) {
  if (!vm.mpType) {//main.js 中的 new Vue
    return vm
  }
  if (vm.mpType === 'app') {
    vm.$options.render = createEmptyRender;
  }
  if (!vm.$options.render) {
    vm.$options.render = createEmptyRender;
    if (true) {
      /* istanbul ignore if */
      if ((vm.$options.template && vm.$options.template.charAt(0) !== '#') ||
        vm.$options.el || el) {
        warn(
          'You are using the runtime-only build of Vue where the template ' +
          'compiler is not available. Either pre-compile the templates into ' +
          'render functions, or use the compiler-included build.',
          vm
        );
      } else {
        warn(
          'Failed to mount component: template or render function not defined.',
          vm
        );
      }
    }
  }
  
  !vm._$fallback && callHook(vm, 'beforeMount');

  var updateComponent = function () {
    vm._update(vm._render(), hydrating);
  };

  // we set this to vm._watcher inside the watcher's constructor
  // since the watcher's initial patch may call $forceUpdate (e.g. inside child
  // component's mounted hook), which relies on vm._watcher being already defined
  new Watcher(vm, updateComponent, noop, {
    before: function before() {
      if (vm._isMounted && !vm._isDestroyed) {
        callHook(vm, 'beforeUpdate');
      }
    }
  }, true /* isRenderWatcher */);
  hydrating = false;
  return vm
}

/*  */

function renderClass (
  staticClass,
  dynamicClass
) {
  if (isDef(staticClass) || isDef(dynamicClass)) {
    return concat(staticClass, stringifyClass(dynamicClass))
  }
  /* istanbul ignore next */
  return ''
}

function concat (a, b) {
  return a ? b ? (a + ' ' + b) : a : (b || '')
}

function stringifyClass (value) {
  if (Array.isArray(value)) {
    return stringifyArray(value)
  }
  if (isObject(value)) {
    return stringifyObject(value)
  }
  if (typeof value === 'string') {
    return value
  }
  /* istanbul ignore next */
  return ''
}

function stringifyArray (value) {
  var res = '';
  var stringified;
  for (var i = 0, l = value.length; i < l; i++) {
    if (isDef(stringified = stringifyClass(value[i])) && stringified !== '') {
      if (res) { res += ' '; }
      res += stringified;
    }
  }
  return res
}

function stringifyObject (value) {
  var res = '';
  for (var key in value) {
    if (value[key]) {
      if (res) { res += ' '; }
      res += key;
    }
  }
  return res
}

/*  */

var parseStyleText = cached(function (cssText) {
  var res = {};
  var listDelimiter = /;(?![^(]*\))/g;
  var propertyDelimiter = /:(.+)/;
  cssText.split(listDelimiter).forEach(function (item) {
    if (item) {
      var tmp = item.split(propertyDelimiter);
      tmp.length > 1 && (res[tmp[0].trim()] = tmp[1].trim());
    }
  });
  return res
});

// normalize possible array / string values into Object
function normalizeStyleBinding (bindingStyle) {
  if (Array.isArray(bindingStyle)) {
    return toObject(bindingStyle)
  }
  if (typeof bindingStyle === 'string') {
    return parseStyleText(bindingStyle)
  }
  return bindingStyle
}

/*  */

var MP_METHODS = ['createSelectorQuery', 'createIntersectionObserver', 'selectAllComponents', 'selectComponent'];

function getTarget(obj, path) {
  var parts = path.split('.');
  var key = parts[0];
  if (key.indexOf('__$n') === 0) { //number index
    key = parseInt(key.replace('__$n', ''));
  }
  if (parts.length === 1) {
    return obj[key]
  }
  return getTarget(obj[key], parts.slice(1).join('.'))
}

function internalMixin(Vue) {

  Vue.config.errorHandler = function(err, vm, info) {
    Vue.util.warn(("Error in " + info + ": \"" + (err.toString()) + "\""), vm);
    console.error(err);
    /* eslint-disable no-undef */
    var app = typeof getApp === 'function' && getApp();
    if (app && app.onError) {
      app.onError(err);
    }
  };

  var oldEmit = Vue.prototype.$emit;

  Vue.prototype.$emit = function(event) {
    if (this.$scope && event) {
      var triggerEvent = this.$scope['_triggerEvent'] || this.$scope['triggerEvent'];
      if (triggerEvent) {
        try {
          triggerEvent.call(this.$scope, event, {
            __args__: toArray(arguments, 1)
          });
        } catch (error) {

        }
      }
    }
    return oldEmit.apply(this, arguments)
  };

  Vue.prototype.$nextTick = function(fn) {
    return nextTick$1(this, fn)
  };

  MP_METHODS.forEach(function (method) {
    Vue.prototype[method] = function(args) {
      if (this.$scope && this.$scope[method]) {
        return this.$scope[method](args)
      }
      // mp-alipay
      if (typeof my === 'undefined') {
        return
      }
      if (method === 'createSelectorQuery') {
        /* eslint-disable no-undef */
        return my.createSelectorQuery(args)
      } else if (method === 'createIntersectionObserver') {
        /* eslint-disable no-undef */
        return my.createIntersectionObserver(args)
      }
      // TODO mp-alipay 暂不支持 selectAllComponents,selectComponent
    };
  });

  Vue.prototype.__init_provide = initProvide;

  Vue.prototype.__init_injections = initInjections;

  Vue.prototype.__call_hook = function(hook, args) {
    var vm = this;
    // #7573 disable dep collection when invoking lifecycle hooks
    pushTarget();
    var handlers = vm.$options[hook];
    var info = hook + " hook";
    var ret;
    if (handlers) {
      for (var i = 0, j = handlers.length; i < j; i++) {
        ret = invokeWithErrorHandling(handlers[i], vm, args ? [args] : null, vm, info);
      }
    }
    if (vm._hasHookEvent) {
      vm.$emit('hook:' + hook, args);
    }
    popTarget();
    return ret
  };

  Vue.prototype.__set_model = function(target, key, value, modifiers) {
    if (Array.isArray(modifiers)) {
      if (modifiers.indexOf('trim') !== -1) {
        value = value.trim();
      }
      if (modifiers.indexOf('number') !== -1) {
        value = this._n(value);
      }
    }
    if (!target) {
      target = this;
    }
    // 解决动态属性添加
    Vue.set(target, key, value);
  };

  Vue.prototype.__set_sync = function(target, key, value) {
    if (!target) {
      target = this;
    }
    // 解决动态属性添加
    Vue.set(target, key, value);
  };

  Vue.prototype.__get_orig = function(item) {
    if (isPlainObject(item)) {
      return item['$orig'] || item
    }
    return item
  };

  Vue.prototype.__get_value = function(dataPath, target) {
    return getTarget(target || this, dataPath)
  };


  Vue.prototype.__get_class = function(dynamicClass, staticClass) {
    return renderClass(staticClass, dynamicClass)
  };

  Vue.prototype.__get_style = function(dynamicStyle, staticStyle) {
    if (!dynamicStyle && !staticStyle) {
      return ''
    }
    var dynamicStyleObj = normalizeStyleBinding(dynamicStyle);
    var styleObj = staticStyle ? extend(staticStyle, dynamicStyleObj) : dynamicStyleObj;
    return Object.keys(styleObj).map(function (name) { return ((hyphenate(name)) + ":" + (styleObj[name])); }).join(';')
  };

  Vue.prototype.__map = function(val, iteratee) {
    //TODO 暂不考虑 string
    var ret, i, l, keys, key;
    if (Array.isArray(val)) {
      ret = new Array(val.length);
      for (i = 0, l = val.length; i < l; i++) {
        ret[i] = iteratee(val[i], i);
      }
      return ret
    } else if (isObject(val)) {
      keys = Object.keys(val);
      ret = Object.create(null);
      for (i = 0, l = keys.length; i < l; i++) {
        key = keys[i];
        ret[key] = iteratee(val[key], key, i);
      }
      return ret
    } else if (typeof val === 'number') {
      ret = new Array(val);
      for (i = 0, l = val; i < l; i++) {
        // 第一个参数暂时仍和小程序一致
        ret[i] = iteratee(i, i);
      }
      return ret
    }
    return []
  };

}

/*  */

var LIFECYCLE_HOOKS$1 = [
    //App
    'onLaunch',
    'onShow',
    'onHide',
    'onUniNViewMessage',
    'onPageNotFound',
    'onThemeChange',
    'onError',
    'onUnhandledRejection',
    //Page
    'onInit',
    'onLoad',
    // 'onShow',
    'onReady',
    // 'onHide',
    'onUnload',
    'onPullDownRefresh',
    'onReachBottom',
    'onTabItemTap',
    'onAddToFavorites',
    'onShareTimeline',
    'onShareAppMessage',
    'onResize',
    'onPageScroll',
    'onNavigationBarButtonTap',
    'onBackPress',
    'onNavigationBarSearchInputChanged',
    'onNavigationBarSearchInputConfirmed',
    'onNavigationBarSearchInputClicked',
    'onUploadDouyinVideo',
    'onNFCReadMessage',
    //Component
    // 'onReady', // 兼容旧版本，应该移除该事件
    'onPageShow',
    'onPageHide',
    'onPageResize'
];
function lifecycleMixin$1(Vue) {

    //fixed vue-class-component
    var oldExtend = Vue.extend;
    Vue.extend = function(extendOptions) {
        extendOptions = extendOptions || {};

        var methods = extendOptions.methods;
        if (methods) {
            Object.keys(methods).forEach(function (methodName) {
                if (LIFECYCLE_HOOKS$1.indexOf(methodName)!==-1) {
                    extendOptions[methodName] = methods[methodName];
                    delete methods[methodName];
                }
            });
        }

        return oldExtend.call(this, extendOptions)
    };

    var strategies = Vue.config.optionMergeStrategies;
    var mergeHook = strategies.created;
    LIFECYCLE_HOOKS$1.forEach(function (hook) {
        strategies[hook] = mergeHook;
    });

    Vue.prototype.__lifecycle_hooks__ = LIFECYCLE_HOOKS$1;
}

/*  */

// install platform patch function
Vue.prototype.__patch__ = patch;

// public mount method
Vue.prototype.$mount = function(
    el ,
    hydrating 
) {
    return mountComponent$1(this, el, hydrating)
};

lifecycleMixin$1(Vue);
internalMixin(Vue);

/*  */

/* harmony default export */ __webpack_exports__["default"] = (Vue);

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../../webpack/buildin/global.js */ 3)))

/***/ }),

/***/ 255:
/*!**************************************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-datetime-picker/components/uv-datetime-picker/props.js ***!
  \**************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default = {
  props: _objectSpread({
    value: {
      type: [String, Number],
      default: ''
    },
    modelValue: {
      type: [String, Number],
      default: ''
    },
    // 是否打开组件
    show: {
      type: Boolean,
      default: false
    },
    // 是否展示顶部的操作栏
    showToolbar: {
      type: Boolean,
      default: true
    },
    // 顶部标题
    title: {
      type: String,
      default: ''
    },
    // 展示格式，mode=date为日期选择，mode=time为时间选择，mode=year-month为年月选择，mode=datetime为日期时间选择
    mode: {
      type: String,
      default: 'datetime'
    },
    // 可选的最大时间
    maxDate: {
      type: Number,
      // 最大默认值为后10年
      default: new Date(new Date().getFullYear() + 10, 0, 1).getTime()
    },
    // 可选的最小时间
    minDate: {
      type: Number,
      // 最小默认值为前10年
      default: new Date(new Date().getFullYear() - 10, 0, 1).getTime()
    },
    // 可选的最小小时，仅mode=time有效
    minHour: {
      type: Number,
      default: 0
    },
    // 可选的最大小时，仅mode=time有效
    maxHour: {
      type: Number,
      default: 23
    },
    // 可选的最小分钟，仅mode=time有效
    minMinute: {
      type: Number,
      default: 0
    },
    // 可选的最大分钟，仅mode=time有效
    maxMinute: {
      type: Number,
      default: 59
    },
    // 选项过滤函数
    filter: {
      type: [Function, null],
      default: null
    },
    // 选项格式化函数
    formatter: {
      type: [Function, null],
      default: null
    },
    // 是否显示加载中状态
    loading: {
      type: Boolean,
      default: false
    },
    // 各列中，单个选项的高度
    itemHeight: {
      type: [String, Number],
      default: 44
    },
    // 取消按钮的文字
    cancelText: {
      type: String,
      default: '取消'
    },
    // 确认按钮的文字
    confirmText: {
      type: String,
      default: '确认'
    },
    // 取消按钮的颜色
    cancelColor: {
      type: String,
      default: '#909193'
    },
    // 确认按钮的颜色
    confirmColor: {
      type: String,
      default: '#3c9cff'
    },
    // 每列中可见选项的数量
    visibleItemCount: {
      type: [String, Number],
      default: 5
    },
    // 是否允许点击遮罩关闭选择器
    closeOnClickOverlay: {
      type: Boolean,
      default: true
    },
    // 是否允许点击确认关闭选择器
    closeOnClickConfirm: {
      type: Boolean,
      default: true
    },
    // 是否清空上次选择内容
    clearDate: {
      type: Boolean,
      default: false
    },
    // 圆角
    round: {
      type: [String, Number],
      default: 0
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.datetimePicker)
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 256:
/*!***********************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-ui-tools/libs/util/dayjs.js ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ 13));
var __getOwnPropNames = Object.getOwnPropertyNames;
var __commonJS = function __commonJS(cb, mod) {
  return function __require() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = {
      exports: {}
    }).exports, mod), mod.exports;
  };
};
var require_dayjs_min = __commonJS({
  "uvuidayjs": function uvuidayjs(exports, module) {
    !function (t, e) {
      "object" == (0, _typeof2.default)(exports) && "undefined" != typeof module ? module.exports = e() :  true ? !(__WEBPACK_AMD_DEFINE_FACTORY__ = (e),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) :
				__WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : undefined;
    }(exports, function () {
      "use strict";

      var t = 1e3,
        e = 6e4,
        n = 36e5,
        r = "millisecond",
        i = "second",
        s = "minute",
        u = "hour",
        a = "day",
        o = "week",
        f = "month",
        h = "quarter",
        c = "year",
        d = "date",
        l = "Invalid Date",
        $ = /^(\d{4})[-/]?(\d{1,2})?[-/]?(\d{0,2})[Tt\s]*(\d{1,2})?:?(\d{1,2})?:?(\d{1,2})?[.:]?(\d+)?$/,
        y = /\[([^\]]+)]|Y{1,4}|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/g,
        M = {
          name: "en",
          weekdays: "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),
          months: "January_February_March_April_May_June_July_August_September_October_November_December".split("_"),
          ordinal: function ordinal(t2) {
            var e2 = ["th", "st", "nd", "rd"],
              n2 = t2 % 100;
            return "[" + t2 + (e2[(n2 - 20) % 10] || e2[n2] || e2[0]) + "]";
          }
        },
        m = function m(t2, e2, n2) {
          var r2 = String(t2);
          return !r2 || r2.length >= e2 ? t2 : "" + Array(e2 + 1 - r2.length).join(n2) + t2;
        },
        v = {
          s: m,
          z: function z(t2) {
            var e2 = -t2.utcOffset(),
              n2 = Math.abs(e2),
              r2 = Math.floor(n2 / 60),
              i2 = n2 % 60;
            return (e2 <= 0 ? "+" : "-") + m(r2, 2, "0") + ":" + m(i2, 2, "0");
          },
          m: function t2(e2, n2) {
            if (e2.date() < n2.date()) return -t2(n2, e2);
            var r2 = 12 * (n2.year() - e2.year()) + (n2.month() - e2.month()),
              i2 = e2.clone().add(r2, f),
              s2 = n2 - i2 < 0,
              u2 = e2.clone().add(r2 + (s2 ? -1 : 1), f);
            return +(-(r2 + (n2 - i2) / (s2 ? i2 - u2 : u2 - i2)) || 0);
          },
          a: function a(t2) {
            return t2 < 0 ? Math.ceil(t2) || 0 : Math.floor(t2);
          },
          p: function p(t2) {
            return {
              M: f,
              y: c,
              w: o,
              d: a,
              D: d,
              h: u,
              m: s,
              s: i,
              ms: r,
              Q: h
            }[t2] || String(t2 || "").toLowerCase().replace(/s$/, "");
          },
          u: function u(t2) {
            return void 0 === t2;
          }
        },
        g = "en",
        D = {};
      D[g] = M;
      var p = function p(t2) {
          return t2 instanceof _;
        },
        S = function t2(e2, n2, r2) {
          var i2;
          if (!e2) return g;
          if ("string" == typeof e2) {
            var s2 = e2.toLowerCase();
            D[s2] && (i2 = s2), n2 && (D[s2] = n2, i2 = s2);
            var u2 = e2.split("-");
            if (!i2 && u2.length > 1) return t2(u2[0]);
          } else {
            var a2 = e2.name;
            D[a2] = e2, i2 = a2;
          }
          return !r2 && i2 && (g = i2), i2 || !r2 && g;
        },
        w = function w(t2, e2) {
          if (p(t2)) return t2.clone();
          var n2 = "object" == (0, _typeof2.default)(e2) ? e2 : {};
          return n2.date = t2, n2.args = arguments, new _(n2);
        },
        O = v;
      O.l = S, O.i = p, O.w = function (t2, e2) {
        return w(t2, {
          locale: e2.$L,
          utc: e2.$u,
          x: e2.$x,
          $offset: e2.$offset
        });
      };
      var _ = function () {
          function M2(t2) {
            this.$L = S(t2.locale, null, true), this.parse(t2);
          }
          var m2 = M2.prototype;
          return m2.parse = function (t2) {
            this.$d = function (t3) {
              var e2 = t3.date,
                n2 = t3.utc;
              if (null === e2) return new Date(NaN);
              if (O.u(e2)) return new Date();
              if (e2 instanceof Date) return new Date(e2);
              if ("string" == typeof e2 && !/Z$/i.test(e2)) {
                var r2 = e2.match($);
                if (r2) {
                  var i2 = r2[2] - 1 || 0,
                    s2 = (r2[7] || "0").substring(0, 3);
                  return n2 ? new Date(Date.UTC(r2[1], i2, r2[3] || 1, r2[4] || 0, r2[5] || 0, r2[6] || 0, s2)) : new Date(r2[1], i2, r2[3] || 1, r2[4] || 0, r2[5] || 0, r2[6] || 0, s2);
                }
              }
              return new Date(e2);
            }(t2), this.$x = t2.x || {}, this.init();
          }, m2.init = function () {
            var t2 = this.$d;
            this.$y = t2.getFullYear(), this.$M = t2.getMonth(), this.$D = t2.getDate(), this.$W = t2.getDay(), this.$H = t2.getHours(), this.$m = t2.getMinutes(), this.$s = t2.getSeconds(), this.$ms = t2.getMilliseconds();
          }, m2.$utils = function () {
            return O;
          }, m2.isValid = function () {
            return !(this.$d.toString() === l);
          }, m2.isSame = function (t2, e2) {
            var n2 = w(t2);
            return this.startOf(e2) <= n2 && n2 <= this.endOf(e2);
          }, m2.isAfter = function (t2, e2) {
            return w(t2) < this.startOf(e2);
          }, m2.isBefore = function (t2, e2) {
            return this.endOf(e2) < w(t2);
          }, m2.$g = function (t2, e2, n2) {
            return O.u(t2) ? this[e2] : this.set(n2, t2);
          }, m2.unix = function () {
            return Math.floor(this.valueOf() / 1e3);
          }, m2.valueOf = function () {
            return this.$d.getTime();
          }, m2.startOf = function (t2, e2) {
            var n2 = this,
              r2 = !!O.u(e2) || e2,
              h2 = O.p(t2),
              l2 = function l2(t3, e3) {
                var i2 = O.w(n2.$u ? Date.UTC(n2.$y, e3, t3) : new Date(n2.$y, e3, t3), n2);
                return r2 ? i2 : i2.endOf(a);
              },
              $2 = function $2(t3, e3) {
                return O.w(n2.toDate()[t3].apply(n2.toDate("s"), (r2 ? [0, 0, 0, 0] : [23, 59, 59, 999]).slice(e3)), n2);
              },
              y2 = this.$W,
              M3 = this.$M,
              m3 = this.$D,
              v2 = "set" + (this.$u ? "UTC" : "");
            switch (h2) {
              case c:
                return r2 ? l2(1, 0) : l2(31, 11);
              case f:
                return r2 ? l2(1, M3) : l2(0, M3 + 1);
              case o:
                var g2 = this.$locale().weekStart || 0,
                  D2 = (y2 < g2 ? y2 + 7 : y2) - g2;
                return l2(r2 ? m3 - D2 : m3 + (6 - D2), M3);
              case a:
              case d:
                return $2(v2 + "Hours", 0);
              case u:
                return $2(v2 + "Minutes", 1);
              case s:
                return $2(v2 + "Seconds", 2);
              case i:
                return $2(v2 + "Milliseconds", 3);
              default:
                return this.clone();
            }
          }, m2.endOf = function (t2) {
            return this.startOf(t2, false);
          }, m2.$set = function (t2, e2) {
            var n2,
              o2 = O.p(t2),
              h2 = "set" + (this.$u ? "UTC" : ""),
              l2 = (n2 = {}, n2[a] = h2 + "Date", n2[d] = h2 + "Date", n2[f] = h2 + "Month", n2[c] = h2 + "FullYear", n2[u] = h2 + "Hours", n2[s] = h2 + "Minutes", n2[i] = h2 + "Seconds", n2[r] = h2 + "Milliseconds", n2)[o2],
              $2 = o2 === a ? this.$D + (e2 - this.$W) : e2;
            if (o2 === f || o2 === c) {
              var y2 = this.clone().set(d, 1);
              y2.$d[l2]($2), y2.init(), this.$d = y2.set(d, Math.min(this.$D, y2.daysInMonth())).$d;
            } else l2 && this.$d[l2]($2);
            return this.init(), this;
          }, m2.set = function (t2, e2) {
            return this.clone().$set(t2, e2);
          }, m2.get = function (t2) {
            return this[O.p(t2)]();
          }, m2.add = function (r2, h2) {
            var d2,
              l2 = this;
            r2 = Number(r2);
            var $2 = O.p(h2),
              y2 = function y2(t2) {
                var e2 = w(l2);
                return O.w(e2.date(e2.date() + Math.round(t2 * r2)), l2);
              };
            if ($2 === f) return this.set(f, this.$M + r2);
            if ($2 === c) return this.set(c, this.$y + r2);
            if ($2 === a) return y2(1);
            if ($2 === o) return y2(7);
            var M3 = (d2 = {}, d2[s] = e, d2[u] = n, d2[i] = t, d2)[$2] || 1,
              m3 = this.$d.getTime() + r2 * M3;
            return O.w(m3, this);
          }, m2.subtract = function (t2, e2) {
            return this.add(-1 * t2, e2);
          }, m2.format = function (t2) {
            var e2 = this,
              n2 = this.$locale();
            if (!this.isValid()) return n2.invalidDate || l;
            var r2 = t2 || "YYYY-MM-DDTHH:mm:ssZ",
              i2 = O.z(this),
              s2 = this.$H,
              u2 = this.$m,
              a2 = this.$M,
              o2 = n2.weekdays,
              f2 = n2.months,
              h2 = function h2(t3, n3, i3, s3) {
                return t3 && (t3[n3] || t3(e2, r2)) || i3[n3].slice(0, s3);
              },
              c2 = function c2(t3) {
                return O.s(s2 % 12 || 12, t3, "0");
              },
              d2 = n2.meridiem || function (t3, e3, n3) {
                var r3 = t3 < 12 ? "AM" : "PM";
                return n3 ? r3.toLowerCase() : r3;
              },
              $2 = {
                YY: String(this.$y).slice(-2),
                YYYY: this.$y,
                M: a2 + 1,
                MM: O.s(a2 + 1, 2, "0"),
                MMM: h2(n2.monthsShort, a2, f2, 3),
                MMMM: h2(f2, a2),
                D: this.$D,
                DD: O.s(this.$D, 2, "0"),
                d: String(this.$W),
                dd: h2(n2.weekdaysMin, this.$W, o2, 2),
                ddd: h2(n2.weekdaysShort, this.$W, o2, 3),
                dddd: o2[this.$W],
                H: String(s2),
                HH: O.s(s2, 2, "0"),
                h: c2(1),
                hh: c2(2),
                a: d2(s2, u2, true),
                A: d2(s2, u2, false),
                m: String(u2),
                mm: O.s(u2, 2, "0"),
                s: String(this.$s),
                ss: O.s(this.$s, 2, "0"),
                SSS: O.s(this.$ms, 3, "0"),
                Z: i2
              };
            return r2.replace(y, function (t3, e3) {
              return e3 || $2[t3] || i2.replace(":", "");
            });
          }, m2.utcOffset = function () {
            return 15 * -Math.round(this.$d.getTimezoneOffset() / 15);
          }, m2.diff = function (r2, d2, l2) {
            var $2,
              y2 = O.p(d2),
              M3 = w(r2),
              m3 = (M3.utcOffset() - this.utcOffset()) * e,
              v2 = this - M3,
              g2 = O.m(this, M3);
            return g2 = ($2 = {}, $2[c] = g2 / 12, $2[f] = g2, $2[h] = g2 / 3, $2[o] = (v2 - m3) / 6048e5, $2[a] = (v2 - m3) / 864e5, $2[u] = v2 / n, $2[s] = v2 / e, $2[i] = v2 / t, $2)[y2] || v2, l2 ? g2 : O.a(g2);
          }, m2.daysInMonth = function () {
            return this.endOf(f).$D;
          }, m2.$locale = function () {
            return D[this.$L];
          }, m2.locale = function (t2, e2) {
            if (!t2) return this.$L;
            var n2 = this.clone(),
              r2 = S(t2, e2, true);
            return r2 && (n2.$L = r2), n2;
          }, m2.clone = function () {
            return O.w(this.$d, this);
          }, m2.toDate = function () {
            return new Date(this.valueOf());
          }, m2.toJSON = function () {
            return this.isValid() ? this.toISOString() : null;
          }, m2.toISOString = function () {
            return this.$d.toISOString();
          }, m2.toString = function () {
            return this.$d.toUTCString();
          }, M2;
        }(),
        T = _.prototype;
      return w.prototype = T, [["$ms", r], ["$s", i], ["$m", s], ["$H", u], ["$W", a], ["$M", f], ["$y", c], ["$D", d]].forEach(function (t2) {
        T[t2[1]] = function (e2) {
          return this.$g(e2, t2[0], t2[1]);
        };
      }), w.extend = function (t2, e2) {
        return t2.$i || (t2(e2, _, w), t2.$i = true), w;
      }, w.locale = S, w.isDayjs = p, w.unix = function (t2) {
        return w(1e3 * t2);
      }, w.en = D[g], w.Ls = D, w.p = {}, w;
    });
  }
});
var _default = require_dayjs_min();
exports.default = _default;

/***/ }),

/***/ 26:
/*!***************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/pages.json ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {



/***/ }),

/***/ 262:
/*!******************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-input/components/uv-input/props.js ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default = {
  props: _objectSpread({
    value: {
      type: [String, Number],
      default: ''
    },
    modelValue: {
      type: [String, Number],
      default: ''
    },
    // 输入框类型
    // number-数字输入键盘，app-vue下可以输入浮点数，app-nvue和小程序平台下只能输入整数
    // idcard-身份证输入键盘，微信、支付宝、百度、QQ小程序
    // digit-带小数点的数字键盘，App的nvue页面、微信、支付宝、百度、头条、QQ小程序
    // text-文本输入键盘
    type: {
      type: String,
      default: 'text'
    },
    // 是否禁用输入框
    disabled: {
      type: Boolean,
      default: false
    },
    // 禁用状态时的背景色
    disabledColor: {
      type: String,
      default: '#f5f7fa'
    },
    // 是否显示清除控件
    clearable: {
      type: Boolean,
      default: false
    },
    // 是否密码类型
    password: {
      type: Boolean,
      default: false
    },
    // 最大输入长度，设置为 -1 的时候不限制最大长度
    maxlength: {
      type: [String, Number],
      default: -1
    },
    // 	输入框为空时的占位符
    placeholder: {
      type: String,
      default: null
    },
    // 指定placeholder的样式类，注意页面或组件的style中写了scoped时，需要在类名前写/deep/
    placeholderClass: {
      type: String,
      default: 'input-placeholder'
    },
    // 指定placeholder的样式
    placeholderStyle: {
      type: [String, Object],
      default: 'color: #c0c4cc'
    },
    // 设置右下角按钮的文字，有效值：send|search|next|go|done，兼容性详见uni-app文档
    // https://uniapp.dcloud.io/component/input
    // https://uniapp.dcloud.io/component/textarea
    confirmType: {
      type: String,
      default: 'done'
    },
    // 点击键盘右下角按钮时是否保持键盘不收起，H5无效
    confirmHold: {
      type: Boolean,
      default: false
    },
    // focus时，点击页面的时候不收起键盘，微信小程序有效
    holdKeyboard: {
      type: Boolean,
      default: false
    },
    // 自动获取焦点
    // 在 H5 平台能否聚焦以及软键盘是否跟随弹出，取决于当前浏览器本身的实现。nvue 页面不支持，需使用组件的 focus()、blur() 方法控制焦点
    focus: {
      type: Boolean,
      default: false
    },
    // 键盘收起时，是否自动失去焦点，目前仅App3.0.0+有效
    autoBlur: {
      type: Boolean,
      default: false
    },
    // 指定focus时光标的位置
    cursor: {
      type: [String, Number],
      default: -1
    },
    // 输入框聚焦时底部与键盘的距离
    cursorSpacing: {
      type: [String, Number],
      default: 30
    },
    // 光标起始位置，自动聚集时有效，需与selection-end搭配使用
    selectionStart: {
      type: [String, Number],
      default: -1
    },
    // 光标结束位置，自动聚集时有效，需与selection-start搭配使用
    selectionEnd: {
      type: [String, Number],
      default: -1
    },
    // 键盘弹起时，是否自动上推页面
    adjustPosition: {
      type: Boolean,
      default: true
    },
    // 输入框内容对齐方式，可选值为：left|center|right
    inputAlign: {
      type: String,
      default: 'left'
    },
    // 输入框字体的大小
    fontSize: {
      type: [String, Number],
      default: '14px'
    },
    // 输入框字体颜色
    color: {
      type: String,
      default: '#303133'
    },
    // 输入框前置图标
    prefixIcon: {
      type: String,
      default: ''
    },
    // 前置图标样式，对象或字符串
    prefixIconStyle: {
      type: [String, Object],
      default: ''
    },
    // 输入框后置图标
    suffixIcon: {
      type: String,
      default: ''
    },
    // 后置图标样式，对象或字符串
    suffixIconStyle: {
      type: [String, Object],
      default: ''
    },
    // 边框类型，surround-四周边框，bottom-底部边框，none-无边框
    border: {
      type: String,
      default: 'surround'
    },
    // 是否只读，与disabled不同之处在于disabled会置灰组件，而readonly则不会
    readonly: {
      type: Boolean,
      default: false
    },
    // 输入框形状，circle-圆形，square-方形
    shape: {
      type: String,
      default: 'square'
    },
    // 用于处理或者过滤输入框内容的方法
    formatter: {
      type: [Function, null],
      default: null
    },
    // 是否忽略组件内对文本合成系统事件的处理
    ignoreCompositionEvent: {
      type: Boolean,
      default: true
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.input)
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 270:
/*!************************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-collapse/components/uv-collapse/props.js ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default = {
  props: _objectSpread({
    // 当前展开面板的name，非手风琴模式：[<string | number>]，手风琴模式：string | number
    value: {
      type: [String, Number, Array, null],
      default: null
    },
    // 是否手风琴模式
    accordion: {
      type: Boolean,
      default: false
    },
    // 是否显示外边框
    border: {
      type: Boolean,
      default: true
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.collapse)
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 276:
/*!*****************************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-collapse/components/uv-collapse-item/props.js ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default = {
  props: _objectSpread({
    // 标题
    title: {
      type: String,
      default: ''
    },
    // 标题右侧内容
    value: {
      type: String,
      default: ''
    },
    // 标题下方的描述信息
    label: {
      type: String,
      default: ''
    },
    // 是否禁用折叠面板
    disabled: {
      type: Boolean,
      default: false
    },
    // 是否展示右侧箭头并开启点击反馈
    isLink: {
      type: Boolean,
      default: true
    },
    // 是否开启点击反馈
    clickable: {
      type: Boolean,
      default: true
    },
    // 是否显示内边框
    border: {
      type: Boolean,
      default: true
    },
    // 标题的对齐方式
    align: {
      type: String,
      default: 'left'
    },
    // 唯一标识符
    name: {
      type: [String, Number],
      default: ''
    },
    // 标题左侧图片，可为绝对路径的图片或内置图标
    icon: {
      type: String,
      default: ''
    },
    // 面板展开收起的过渡时间，单位ms
    duration: {
      type: Number,
      default: 300
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.collapseItem)
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 284:
/*!****************************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-status-bar/components/uv-status-bar/props.js ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _default = {
  props: {
    bgColor: {
      type: String,
      default: 'transparent'
    }
  }
};
exports.default = _default;

/***/ }),

/***/ 292:
/*!**********************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-toolbar/components/uv-toolbar/props.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default = {
  props: _objectSpread({
    // 是否展示工具条
    show: {
      type: Boolean,
      default: true
    },
    // 是否显示下边框
    showBorder: {
      type: Boolean,
      default: false
    },
    // 取消按钮的文字
    cancelText: {
      type: String,
      default: '取消'
    },
    // 确认按钮的文字
    confirmText: {
      type: String,
      default: '确认'
    },
    // 取消按钮的颜色
    cancelColor: {
      type: String,
      default: '#909193'
    },
    // 确认按钮的颜色
    confirmColor: {
      type: String,
      default: '#3c9cff'
    },
    // 标题文字
    title: {
      type: String,
      default: ''
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.toolbar)
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 3:
/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ 300:
/*!***********************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-ui-tools/libs/function/colorGradient.js ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.colorGradient = colorGradient;
exports.colorToRgba = colorToRgba;
exports.hexToRgb = hexToRgb;
exports.rgbToHex = rgbToHex;
/**
 * 求两个颜色之间的渐变值
 * @param {string} startColor 开始的颜色
 * @param {string} endColor 结束的颜色
 * @param {number} step 颜色等分的份额
 * */
function colorGradient() {
  var startColor = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'rgb(0, 0, 0)';
  var endColor = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'rgb(255, 255, 255)';
  var step = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 10;
  var startRGB = hexToRgb(startColor, false); // 转换为rgb数组模式
  var startR = startRGB[0];
  var startG = startRGB[1];
  var startB = startRGB[2];
  var endRGB = hexToRgb(endColor, false);
  var endR = endRGB[0];
  var endG = endRGB[1];
  var endB = endRGB[2];
  var sR = (endR - startR) / step; // 总差值
  var sG = (endG - startG) / step;
  var sB = (endB - startB) / step;
  var colorArr = [];
  for (var i = 0; i < step; i++) {
    // 计算每一步的hex值
    var hex = rgbToHex("rgb(".concat(Math.round(sR * i + startR), ",").concat(Math.round(sG * i + startG), ",").concat(Math.round(sB * i + startB), ")"));
    // 确保第一个颜色值为startColor的值
    if (i === 0) hex = rgbToHex(startColor);
    // 确保最后一个颜色值为endColor的值
    if (i === step - 1) hex = rgbToHex(endColor);
    colorArr.push(hex);
  }
  return colorArr;
}

// 将hex表示方式转换为rgb表示方式(这里返回rgb数组模式)
function hexToRgb(sColor) {
  var str = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
  var reg = /^#([0-9a-fA-f]{3}|[0-9a-fA-f]{6})$/;
  sColor = String(sColor).toLowerCase();
  if (sColor && reg.test(sColor)) {
    if (sColor.length === 4) {
      var sColorNew = '#';
      for (var i = 1; i < 4; i += 1) {
        sColorNew += sColor.slice(i, i + 1).concat(sColor.slice(i, i + 1));
      }
      sColor = sColorNew;
    }
    // 处理六位的颜色值
    var sColorChange = [];
    for (var _i = 1; _i < 7; _i += 2) {
      sColorChange.push(parseInt("0x".concat(sColor.slice(_i, _i + 2))));
    }
    if (!str) {
      return sColorChange;
    }
    return "rgb(".concat(sColorChange[0], ",").concat(sColorChange[1], ",").concat(sColorChange[2], ")");
  }
  if (/^(rgb|RGB)/.test(sColor)) {
    var arr = sColor.replace(/(?:\(|\)|rgb|RGB)*/g, '').split(',');
    return arr.map(function (val) {
      return Number(val);
    });
  }
  return sColor;
}

// 将rgb表示方式转换为hex表示方式
function rgbToHex(rgb) {
  var _this = rgb;
  var reg = /^#([0-9a-fA-f]{3}|[0-9a-fA-f]{6})$/;
  if (/^(rgb|RGB)/.test(_this)) {
    var aColor = _this.replace(/(?:\(|\)|rgb|RGB)*/g, '').split(',');
    var strHex = '#';
    for (var i = 0; i < aColor.length; i++) {
      var hex = Number(aColor[i]).toString(16);
      hex = String(hex).length == 1 ? "".concat(0, hex) : hex; // 保证每个rgb的值为2位
      if (hex === '0') {
        hex += hex;
      }
      strHex += hex;
    }
    if (strHex.length !== 7) {
      strHex = _this;
    }
    return strHex;
  }
  if (reg.test(_this)) {
    var aNum = _this.replace(/#/, '').split('');
    if (aNum.length === 6) {
      return _this;
    }
    if (aNum.length === 3) {
      var numHex = '#';
      for (var _i2 = 0; _i2 < aNum.length; _i2 += 1) {
        numHex += aNum[_i2] + aNum[_i2];
      }
      return numHex;
    }
  } else {
    return _this;
  }
}

/**
* JS颜色十六进制转换为rgb或rgba,返回的格式为 rgba（255，255，255，0.5）字符串
* sHex为传入的十六进制的色值
* alpha为rgba的透明度
*/
function colorToRgba(color, alpha) {
  color = rgbToHex(color);
  // 十六进制颜色值的正则表达式
  var reg = /^#([0-9a-fA-f]{3}|[0-9a-fA-f]{6})$/;
  /* 16进制颜色转为RGB格式 */
  var sColor = String(color).toLowerCase();
  if (sColor && reg.test(sColor)) {
    if (sColor.length === 4) {
      var sColorNew = '#';
      for (var i = 1; i < 4; i += 1) {
        sColorNew += sColor.slice(i, i + 1).concat(sColor.slice(i, i + 1));
      }
      sColor = sColorNew;
    }
    // 处理六位的颜色值
    var sColorChange = [];
    for (var _i3 = 1; _i3 < 7; _i3 += 2) {
      sColorChange.push(parseInt("0x".concat(sColor.slice(_i3, _i3 + 2))));
    }
    // return sColorChange.join(',')
    return "rgba(".concat(sColorChange.join(','), ",").concat(alpha, ")");
  }
  return sColor;
}

/***/ }),

/***/ 301:
/*!********************************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-loading-icon/components/uv-loading-icon/props.js ***!
  \********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default2 = {
  props: _objectSpread({
    // 是否显示组件
    show: {
      type: Boolean,
      default: true
    },
    // 颜色
    color: {
      type: String,
      default: '#909193'
    },
    // 提示文字颜色
    textColor: {
      type: String,
      default: '#909193'
    },
    // 文字和图标是否垂直排列
    vertical: {
      type: Boolean,
      default: false
    },
    // 模式选择，circle-圆形，spinner-花朵形，semicircle-半圆形
    mode: {
      type: String,
      default: 'spinner'
    },
    // 图标大小，单位默认px
    size: {
      type: [String, Number],
      default: 24
    },
    // 文字大小
    textSize: {
      type: [String, Number],
      default: 15
    },
    // 文字样式
    textStyle: {
      type: Object,
      default: function _default() {
        return {};
      }
    },
    // 文字内容
    text: {
      type: [String, Number],
      default: ''
    },
    // 动画模式 https://www.runoob.com/cssref/css3-pr-animation-timing-function.html
    timingFunction: {
      type: String,
      default: 'linear'
    },
    // 动画执行周期时间
    duration: {
      type: [String, Number],
      default: 1200
    },
    // mode=circle时的暗边颜色
    inactiveColor: {
      type: String,
      default: ''
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.loadingIcon)
};
exports.default = _default2;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 309:
/*!**********************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-overlay/components/uv-overlay/props.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default = {
  props: _objectSpread({
    // 是否显示遮罩
    show: {
      type: Boolean,
      default: false
    },
    // 层级z-index
    zIndex: {
      type: [String, Number],
      default: 10070
    },
    // 遮罩的过渡时间，单位为ms
    duration: {
      type: [String, Number],
      default: 300
    },
    // 不透明度值，当做rgba的第四个参数
    opacity: {
      type: [String, Number],
      default: 0.5
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.overlay)
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 317:
/*!**************************************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-transition/components/uv-transition/createAnimation.js ***!
  \**************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.createAnimation = createAnimation;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ 23));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ 24));
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
// const defaultOption = {
// 	duration: 300,
// 	timingFunction: 'linear',
// 	delay: 0,
// 	transformOrigin: '50% 50% 0'
// }
var MPAnimation = /*#__PURE__*/function () {
  function MPAnimation(options, _this) {
    (0, _classCallCheck2.default)(this, MPAnimation);
    this.options = options;
    // 在iOS10+QQ小程序平台下，传给原生的对象一定是个普通对象而不是Proxy对象，否则会报parameter should be Object instead of ProxyObject的错误
    this.animation = uni.createAnimation(_objectSpread({}, options));
    this.currentStepAnimates = {};
    this.next = 0;
    this.$ = _this;
  }
  (0, _createClass2.default)(MPAnimation, [{
    key: "_nvuePushAnimates",
    value: function _nvuePushAnimates(type, args) {
      var aniObj = this.currentStepAnimates[this.next];
      var styles = {};
      if (!aniObj) {
        styles = {
          styles: {},
          config: {}
        };
      } else {
        styles = aniObj;
      }
      if (animateTypes1.includes(type)) {
        if (!styles.styles.transform) {
          styles.styles.transform = '';
        }
        var unit = '';
        if (type === 'rotate') {
          unit = 'deg';
        }
        styles.styles.transform += "".concat(type, "(").concat(args + unit, ") ");
      } else {
        styles.styles[type] = "".concat(args);
      }
      this.currentStepAnimates[this.next] = styles;
    }
  }, {
    key: "_animateRun",
    value: function _animateRun() {
      var styles = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var config = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var ref = this.$.$refs['ani'].ref;
      if (!ref) return;
      return new Promise(function (resolve, reject) {
        nvueAnimation.transition(ref, _objectSpread({
          styles: styles
        }, config), function (res) {
          resolve();
        });
      });
    }
  }, {
    key: "_nvueNextAnimate",
    value: function _nvueNextAnimate(animates) {
      var _this2 = this;
      var step = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
      var fn = arguments.length > 2 ? arguments[2] : undefined;
      var obj = animates[step];
      if (obj) {
        var styles = obj.styles,
          config = obj.config;
        this._animateRun(styles, config).then(function () {
          step += 1;
          _this2._nvueNextAnimate(animates, step, fn);
        });
      } else {
        this.currentStepAnimates = {};
        typeof fn === 'function' && fn();
        this.isEnd = true;
      }
    }
  }, {
    key: "step",
    value: function step() {
      var config = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      this.animation.step(config);
      return this;
    }
  }, {
    key: "run",
    value: function run(fn) {
      this.$.animationData = this.animation.export();
      this.$.timer = setTimeout(function () {
        typeof fn === 'function' && fn();
      }, this.$.durationTime);
    }
  }]);
  return MPAnimation;
}();
var animateTypes1 = ['matrix', 'matrix3d', 'rotate', 'rotate3d', 'rotateX', 'rotateY', 'rotateZ', 'scale', 'scale3d', 'scaleX', 'scaleY', 'scaleZ', 'skew', 'skewX', 'skewY', 'translate', 'translate3d', 'translateX', 'translateY', 'translateZ'];
var animateTypes2 = ['opacity', 'backgroundColor'];
var animateTypes3 = ['width', 'height', 'left', 'right', 'top', 'bottom'];
animateTypes1.concat(animateTypes2, animateTypes3).forEach(function (type) {
  MPAnimation.prototype[type] = function () {
    var _this$animation;
    (_this$animation = this.animation)[type].apply(_this$animation, arguments);
    return this;
  };
});
function createAnimation(option, _this) {
  if (!_this) return;
  clearTimeout(_this.timer);
  return new MPAnimation(option, _this);
}
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 330:
/*!****************************************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/uni_modules/uv-cell/components/uv-cell/props.js ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var _uni$$uv, _uni$$uv$props;
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0, _defineProperty2.default)(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
var _default2 = {
  props: _objectSpread({
    // 标题
    title: {
      type: [String, Number],
      default: ''
    },
    // 标题下方的描述信息
    label: {
      type: [String, Number],
      default: ''
    },
    // 右侧的内容
    value: {
      type: [String, Number],
      default: ''
    },
    // 左侧图标名称，或者图片链接(本地文件建议使用绝对地址)
    icon: {
      type: String,
      default: ''
    },
    // 是否禁用cell
    disabled: {
      type: Boolean,
      default: false
    },
    // 是否显示下边框
    border: {
      type: Boolean,
      default: true
    },
    // 内容是否垂直居中(主要是针对右侧的value部分)
    center: {
      type: Boolean,
      default: true
    },
    // 点击后跳转的URL地址
    url: {
      type: String,
      default: ''
    },
    // 链接跳转的方式，内部使用的是uvui封装的route方法，可能会进行拦截操作
    linkType: {
      type: String,
      default: 'navigateTo'
    },
    // 是否开启点击反馈(表现为点击时加上灰色背景)
    clickable: {
      type: Boolean,
      default: false
    },
    // 是否展示右侧箭头并开启点击反馈
    isLink: {
      type: Boolean,
      default: false
    },
    // 是否显示表单状态下的必填星号(此组件可能会内嵌入input组件)
    required: {
      type: Boolean,
      default: false
    },
    // 右侧的图标箭头
    rightIcon: {
      type: String,
      default: 'arrow-right'
    },
    // 右侧箭头的方向，可选值为：left，up，down
    arrowDirection: {
      type: String,
      default: ''
    },
    // 左侧图标样式
    iconStyle: {
      type: [Object, String],
      default: function _default() {
        return {};
      }
    },
    // 右侧箭头图标的样式
    rightIconStyle: {
      type: [Object, String],
      default: function _default() {
        return {};
      }
    },
    // 标题的样式
    titleStyle: {
      type: [Object, String],
      default: function _default() {
        return {};
      }
    },
    // 单位元的大小，可选值为large
    size: {
      type: String,
      default: ''
    },
    // 点击cell是否阻止事件传播
    stop: {
      type: Boolean,
      default: true
    },
    // 标识符，cell被点击时返回
    name: {
      type: [Number, String],
      default: ''
    },
    // 单元格自定义样式
    cellStyle: {
      type: [Object, String],
      default: function _default() {}
    }
  }, (_uni$$uv = uni.$uv) === null || _uni$$uv === void 0 ? void 0 : (_uni$$uv$props = _uni$$uv.props) === null || _uni$$uv$props === void 0 ? void 0 : _uni$$uv$props.cell)
};
exports.default = _default2;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 34:
/*!**********************************************************************************************************!*\
  !*** ./node_modules/@dcloudio/vue-cli-plugin-uni/packages/vue-loader/lib/runtime/componentNormalizer.js ***!
  \**********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return normalizeComponent; });
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent (
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier, /* server only */
  shadowMode, /* vue-cli only */
  components, // fixed by xxxxxx auto components
  renderjs // fixed by xxxxxx renderjs
) {
  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // fixed by xxxxxx auto components
  if (components) {
    if (!options.components) {
      options.components = {}
    }
    var hasOwn = Object.prototype.hasOwnProperty
    for (var name in components) {
      if (hasOwn.call(components, name) && !hasOwn.call(options.components, name)) {
        options.components[name] = components[name]
      }
    }
  }
  // fixed by xxxxxx renderjs
  if (renderjs) {
    if(typeof renderjs.beforeCreate === 'function'){
			renderjs.beforeCreate = [renderjs.beforeCreate]
		}
    (renderjs.beforeCreate || (renderjs.beforeCreate = [])).unshift(function() {
      this[renderjs.__module] = this
    });
    (options.mixins || (options.mixins = [])).push(renderjs)
  }

  // render functions
  if (render) {
    options.render = render
    options.staticRenderFns = staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = 'data-v-' + scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = shadowMode
      ? function () { injectStyles.call(this, this.$root.$options.shadowRoot) }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}


/***/ }),

/***/ 35:
/*!**********************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/common/js/util.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ 36));
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ 13));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ 38));
var _config = _interopRequireDefault(__webpack_require__(/*! ./config.js */ 39));
var _http = _interopRequireDefault(__webpack_require__(/*! ./http.js */ 40));
var _default = {
  /**
   * 查看图片
   * @param {string} i 第几个
   * @param {string} imgs 图片数组 或者图片字符串
   */
  lookImg: function lookImg(i, imgs) {
    var that = this;
    var images = [];
    if (typeof imgs == 'string') {
      images = imgs.split(',');
    } else {
      images = imgs;
    }
    // 网络
    uni.previewImage({
      current: i,
      urls: images,
      longPressActions: {
        itemList: ['发送给朋友', '保存图片', '收藏'],
        success: function success(data) {
          console.log('选中了第' + (data.tapIndex + 1) + '个按钮,第' + (data.index + 1) + '张图片');
        },
        fail: function fail(err) {
          console.log(err.errMsg);
        }
      }
    });
  },
  /**
   * 打电话
   * @param {string} num 手机号
   */
  ploneFn: function ploneFn(num) {
    if (!num) {
      uni.showToast({
        title: "暂未配置手机号",
        icon: 'none'
      });
      return;
    }
    uni.makePhoneCall({
      phoneNumber: num
    });
  },
  /**
   * 页面跳转
   * @param {string} latitude 经纬度
   * @param {string} name 位置名字
   */
  // 地图导航
  mapNavigate: function mapNavigate(latitude, longitude, name) {
    uni.openLocation({
      type: "gcj02",
      name: name || '',
      latitude: Number(latitude) || '',
      longitude: Number(longitude) || '',
      success: function success(res) {
        console.log(res, '地图返回');
      }
    });
  },
  /**
   * 页面跳转
   * @param {string} to 跳转链接 /pages/idnex/index
   * @param {Object} param 参数 {key : value, ...}
   * @param {string} mode 模式 
   */
  redirectTo: function redirectTo(to, param, mode) {
    var url = to;
    // console.log({"$store":store.state.$store.tabbarList.list}) 
    var tabbarList = ['/pages/index/index', '/pages/vip/vip', '/pages/shop/shop', '/pages/my/my'];
    for (var i = 0; i < tabbarList.length; i++) {
      if (to.indexOf(tabbarList[i]) != -1) {
        uni.switchTab({
          url: url
        });
        return;
      }
    }

    //

    if (param != undefined) {
      Object.keys(param).forEach(function (key) {
        if (url.indexOf('?') != -1) {
          url += "&" + key + "=" + param[key];
        } else {
          url += "?" + key + "=" + param[key];
        }
      });
    }
    switch (mode) {
      case 'tabbar':
        // 跳转到 tabBar 页面，并关闭其他所有非 tabBar 页面。
        uni.switchTab({
          url: url
        });
        break;
      case 'redirectTo':
        // 关闭当前页面，跳转到应用内的某个页面。
        uni.redirectTo({
          url: url
        });
        break;
      case 'reLaunch':
        // 关闭所有页面，打开到应用内的某个页面。
        uni.reLaunch({
          url: url
        });
        break;
      default:
        // 保留当前页面，跳转到应用内的某个页面
        uni.navigateTo({
          url: url
        });
    }
  },
  /**
   * 图片路径转换
   * @param {String} img_path 图片地址
   * @param {Object} params 参数，针对商品、相册里面的图片区分大中小，size: big、mid、small
   */
  img: function img(img_path, params) {
    var path = "";
    if (img_path && img_path != undefined && img_path != "") {
      if (params && img_path != this.getDefaultImage().default_goods_img) {
        // 过滤默认图
        var arr = img_path.split(".");
        var suffix = arr[arr.length - 1];
        arr.pop();
        arr[arr.length - 1] = arr[arr.length - 1] + "_" + params.size.toUpperCase();
        arr.push(suffix);
        img_path = arr.join(".");
      }
      if (img_path.indexOf("http://") == -1 && img_path.indexOf("https://") == -1) {
        path = _config.default.imgDomain + "/" + img_path;
      } else {
        path = img_path;
      }
      // 处理商品助手的图片路径
      path = path.replace("addons/NsGoodsAssist/", "").replace("shop/goods/", "");
    } else {
      return '/static/none.png';
    }
    return path;
  },
  /**
   * 时间戳转日期格式
   * @param {Object} timeStamp
   */
  timeStampTurnTime: function timeStampTurnTime(timeStamp) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "";
    if (timeStamp != undefined && timeStamp != "" && timeStamp > 0) {
      var date = new Date();
      date.setTime(timeStamp);
      var y = date.getFullYear();
      var m = date.getMonth() + 1;
      m = m < 10 ? '0' + m : m;
      var d = date.getDate();
      d = d < 10 ? '0' + d : d;
      var h = date.getHours();
      h = h < 10 ? '0' + h : h;
      var minute = date.getMinutes();
      var second = date.getSeconds();
      minute = minute < 10 ? '0' + minute : minute;
      second = second < 10 ? '0' + second : second;
      if (type == 'ymd') {
        return y + '-' + m + '-' + d;
      } else if (type == 'ym') {
        return y + '-' + m;
      } else if (type == 'md') {
        return m + '-' + d;
      } else {
        return y + '-' + m + '-' + d + ' ' + h + ':' + minute + ':' + second;
      }
    } else {
      return "";
    }
  },
  /**
   * 日期格式转时间戳
   * @param {Object} timeStamp
   */
  timeTurnTimeStamp: function timeTurnTimeStamp(string) {
    var f = string.split(' ', 2);
    var d = (f[0] ? f[0] : '').split('-', 3);
    var t = (f[1] ? f[1] : '').split(':', 3);
    return new Date(parseInt(d[0], 10) || null, (parseInt(d[1], 10) || 1) - 1, parseInt(d[2], 10) || null, parseInt(t[0], 10) || null, parseInt(t[1], 10) || null, parseInt(t[2], 10) || null).getTime() / 1000;
  },
  /**
   * 倒计时
   * @param {Object} seconds 秒
   */
  countDown: function countDown(seconds) {
    var day = 0,
      hour = 0,
      minute = 0,
      second = 0;
    if (seconds > 0) {
      day = Math.floor(seconds / (60 * 60 * 24));
      hour = Math.floor(seconds / (60 * 60)) - day * 24;
      minute = Math.floor(seconds / 60) - day * 24 * 60 - hour * 60;
      second = Math.floor(seconds) - day * 24 * 60 * 60 - hour * 60 * 60 - minute * 60;
    }
    if (day < 10) {
      day = '0' + day;
    }
    if (hour < 10) {
      hour = '0' + hour;
    }
    if (minute < 10) {
      minute = '0' + minute;
    }
    if (second < 10) {
      second = '0' + second;
    }
    return {
      d: day,
      h: hour,
      i: minute,
      s: second
    };
  },
  /**
   * 数值去重
   * @param {Array} arr 数组
   * @param {string} field 字段
   */
  unique: function unique(arr, field) {
    var res = new Map();
    return arr.filter(function (a) {
      return !res.has(a[field]) && res.set(a[field], 1);
    });
  },
  /**
   * 判断值是否在数组中
   * @param {Object} elem
   * @param {Object} arr
   * @param {Object} i
   */
  inArray: function inArray(elem, arr) {
    return arr == null ? -1 : arr.indexOf(elem);
  },
  /**
   * 获取某天日期
   * @param {Object} day
   */
  getDay: function getDay(day) {
    var today = new Date();
    var targetday_milliseconds = today.getTime() + 1000 * 60 * 60 * 24 * day;
    today.setTime(targetday_milliseconds);
    var doHandleMonth = function doHandleMonth(month) {
      var m = month;
      if (month.toString().length == 1) {
        m = "0" + month;
      }
      return m;
    };
    var tYear = today.getFullYear();
    var tMonth = today.getMonth();
    var tDate = today.getDate();
    var tWeek = today.getDay();
    var time = parseInt(today.getTime() / 1000);
    tMonth = doHandleMonth(tMonth + 1);
    tDate = doHandleMonth(tDate);
    var week = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
    return {
      't': time,
      'y': tYear,
      'm': tMonth,
      'd': tDate,
      'w': week[tWeek]
    };
  },
  /**
   * 图片选择加上传
   * @param number num
   * @param {Object} params
   * @param {Object} callback
   * @param string url
   * return array
   */
  upload: function upload(num, params, callback, url) {
    var data = {
      token: uni.getStorageSync('token')
    };
    data = Object.assign(data, params);
    var imgs_num = num;
    var _self = this;
    uni.chooseImage({
      count: imgs_num,
      sizeType: ['compressed'],
      //可以指定是原图还是压缩图，默认二者都有
      sourceType: ['album', 'camera'],
      //从相册或者拍照
      success: function () {
        var _success = (0, _asyncToGenerator2.default)( /*#__PURE__*/_regenerator.default.mark(function _callee(res) {
          var tempFilePaths, _data, imgs, i, path;
          return _regenerator.default.wrap(function _callee$(_context) {
            while (1) {
              switch (_context.prev = _context.next) {
                case 0:
                  tempFilePaths = res.tempFilePaths;
                  _data = data;
                  imgs = [];
                  i = 0;
                case 4:
                  if (!(i < tempFilePaths.length)) {
                    _context.next = 12;
                    break;
                  }
                  _context.next = 7;
                  return _self.upload_file_server(tempFilePaths[i], _data, params.path, url);
                case 7:
                  path = _context.sent;
                  imgs.push(path);
                case 9:
                  i++;
                  _context.next = 4;
                  break;
                case 12:
                  typeof callback == 'function' && callback(imgs);
                case 13:
                case "end":
                  return _context.stop();
              }
            }
          }, _callee);
        }));
        function success(_x) {
          return _success.apply(this, arguments);
        }
        return success;
      }()
    });
  },
  //上传
  upload_file_server: function upload_file_server(tempFilePath, data, path) {
    var url = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : "";
    if (url) {
      var uploadUrl = _config.default.baseUrl + url;
    } else {
      var uploadUrl = _config.default.baseUrl + '/api/localUpload';
    }
    return new Promise(function (resolve, reject) {
      uni.uploadFile({
        url: uploadUrl,
        filePath: tempFilePath,
        name: 'file',
        formData: data,
        success: function success(res) {
          var path_str = JSON.parse(res.data);
          if (path_str.code >= 0) {
            resolve(path_str.data);
          } else {
            reject("error");
          }
        }
      });
    });
  },
  /**
   * 复制
   * @param {Object} value 内容
   * @param {Object} callback
   */
  copy: function copy(value, callback) {
    var _this = this;
    uni.setClipboardData({
      data: value,
      success: function success() {
        _this.showToast({
          title: '复制成功'
        });
      }
    });
  },
  /**
   * 是否是微信浏览器
   */
  isWeiXin: function isWeiXin() {
    return false;
    var ua = navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == "micromessenger") {
      return true;
    } else {
      return false;
    }
  },
  /**
   * 显示消息提示框
   *  @param {Object} params 参数
   */
  showToast: function showToast() {
    var params = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    params.title = params.title || "";
    params.icon = params.icon || "none";
    // params.position = params.position || 'bottom';
    params.duration = 1500;
    uni.showToast(params);
    // store.commit('updateShowToastValue', params)
    setTimeout(function () {
      // store.commit('updateShowToastValue', {})
    }, 1500);
    if (params.success) params.success();
  },
  /**
   * 检测苹果X以上的手机
   */
  isIPhoneX: function isIPhoneX() {
    var res = uni.getSystemInfoSync();
    if (res.model.search('iPhone X') != -1) {
      return true;
    }
    return false;
  },
  //判断安卓还是iOS
  isAndroid: function isAndroid() {
    var platform = uni.getSystemInfoSync().platform;
    if (platform == 'ios') {
      return false;
    } else if (platform == 'android') {
      return true;
    }
  },
  /**
   * 深度拷贝对象
   * @param {Object} obj
   */
  deepClone: function deepClone(obj) {
    var isObject = function isObject(obj) {
      return (0, _typeof2.default)(obj) == 'object';
    };
    if (!isObject(obj)) {
      throw new Error('obj 不是一个对象！');
    }
    //判断传进来的是对象还是数组
    var isArray = Array.isArray(obj);
    var cloneObj = isArray ? [] : {};
    //通过for...in来拷贝
    for (var key in obj) {
      cloneObj[key] = isObject(obj[key]) ? this.deepClone(obj[key]) : obj[key];
    }
    return cloneObj;
  },
  refreshBottomNav: function refreshBottomNav() {
    var bottomNav = uni.getStorageSync("bottom_nav");
    bottomNav = JSON.parse(bottomNav);
    for (var i = 0; i < bottomNav.list.length; i++) {
      var item = bottomNav.list[i];
      var obj = {
        index: i
      };
      obj.text = item.title;
      obj.iconPath = this.img(item.iconPath);
      obj.selectedIconPath = this.img(item.selectedIconPath);
      if (bottomNav.type == 1) {
        // 图文
      } else if (bottomNav.type == 2) {
        // 图片
      } else if (bottomNav.type == 3) {
        // 文字
      }
      uni.setTabBarItem(obj);
    }
  },
  /**
   * 自定义模板的跳转链接
   * @param {Object} link
   */
  diyRedirectTo: function diyRedirectTo(link, method) {
    if (link == null || link == '' || !link.wap_url && !link.appid && !link.mobile) return;
    if (link.wap_url.indexOf('http') != -1 || link.wap_url.indexOf('http') != -1) {
      this.redirectTo('/otherpages/web/web?src=' + link.wap_url);
    } else if (link.appid) {
      uni.navigateToMiniProgram({
        appId: link.appid,
        path: link.page
      });
    } else if (link.mobile) {
      uni.makePhoneCall({
        phoneNumber: link.mobile,
        fail: function fail(res) {
          console.log(res);
        }
      });
    } else {
      this.redirectTo(link.wap_url);
    }
  },
  /**
   * 获取默认图
   * @param {Object} link
   */
  getDefaultImage: function getDefaultImage() {
    var defaultImg = uni.getStorageSync('default_img');
    if (defaultImg) {
      defaultImg = JSON.parse(defaultImg);
      defaultImg.default_goods_img = this.img(defaultImg.default_goods_img);
      defaultImg.default_headimg = this.img(defaultImg.default_headimg);
      defaultImg.default_storeimg = this.img(defaultImg.default_storeimg);
      return defaultImg;
    } else {
      return {
        default_goods_img: '',
        default_headimg: '',
        default_storeimg: ''
      };
    }
  },
  /**
   * 判断手机是否为iphoneX系列
   */
  uniappIsIPhoneX: function uniappIsIPhoneX() {
    var isIphoneX = false;
    var systemInfo = uni.getSystemInfoSync();
    if (systemInfo.model.search('iPhone X') != -1 || systemInfo.model.search('iPhone 11') != -1 || systemInfo.model.search('iPhone 12') != -1) {
      isIphoneX = true;
    }
    return isIphoneX;
  },
  /**
   * 判断手机是否为iphone11系列
   */
  uniappIsIPhone11: function uniappIsIPhone11() {
    var isIphone11 = false;
    var systemInfo = uni.getSystemInfoSync();
    if (systemInfo.model.search('iPhone 11') != -1) {
      isIphone11 = true;
    }
    return isIphone11;
  },
  /**
   * 判断手机是否为iphoneX系列   	判断当前页面栈是否存在，如果存在，则返回该页面栈，否则跳转到该页面
   */
  jumpPage: function jumpPage(url) {
    var jump = true;
    var arr = getCurrentPages().reverse();
    for (var i = 0; i < arr.length; i++) {
      if (url.indexOf(arr[i].route) != -1) {
        jump = false;
        uni.navigateBack({
          delta: i
        });
        break;
      }
    }
    if (jump) {
      this.$util.diyRedirectTo(url);
    }
  },
  // 计算两个经纬度之间的距离
  getDistance: function getDistance(lat1, lng1, lat2, lng2) {
    var radLat1 = lat1 * Math.PI / 180.0;
    var radLat2 = lat2 * Math.PI / 180.0;
    var a = radLat1 - radLat2;
    var b = lng1 * Math.PI / 180.0 - lng2 * Math.PI / 180.0;
    var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a / 2), 2) + Math.cos(radLat1) * Math.cos(radLat2) * Math.pow(Math.sin(b / 2), 2)));
    s = s * 6378.137; // EARTH_RADIUS;
    s = Math.round(s * 10000) / 10000;
    return s;
  },
  goBack: function goBack() {
    var backUrl = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '/pages/index/index/index';
    if (getCurrentPages().length == 1) {
      this.redirectTo(backUrl);
    } else {
      uni.navigateBack();
    }
  },
  numberFixed: function numberFixed(e, f) {
    if (!f) {
      f = 0;
    }
    return Number(e).toFixed(f);
  },
  /**
   * 获取url参数
   */
  getUrlCode: function getUrlCode(callback) {
    var url = location.search;
    var theRequest = new Object();
    if (url.indexOf('?') != -1) {
      var str = url.substr(1);
      var strs = str.split('&');
      for (var i = 0; i < strs.length; i++) {
        theRequest[strs[i].split('=')[0]] = strs[i].split('=')[1];
      }
    }
    typeof callback == 'function' && callback(theRequest);
  },
  /**
   * @param {Object} 转化时间字符串 （转化时分秒）
   */
  getTimeStr: function getTimeStr(val) {
    var h = parseInt(val / 3600).toString();
    var m = parseInt(val % 3600 / 60).toString();
    if (m.length == 1) {
      m = '0' + m;
    }
    if (h.length == 1) {
      h = '0' + h;
    }
    return h + ':' + m;
  },
  getLocation: function getLocation() {
    var currTime = new Date().getTime();
    var location = uni.getStorageSync('userLocation');
    if (location.time && location.info && currTime - location.time < 3600 * 1000 * 24) {
      return location.info;
    }
    return new Promise(function (resolve, reject) {
      uni.getLocation({
        type: 'gcj02',
        success: function success(res) {
          var info = {
            latitude: res.latitude,
            longitude: res.longitude
          };
          uni.setStorage({
            key: 'userLocation',
            data: {
              time: new Date().getTime(),
              info: info
            }
          });
          resolve(info);
        },
        fail: function fail(res) {}
      });
    });
  },
  replaceImg: function replaceImg(html) {
    //script methods 方法  处理富文本默认图片大小
    if (!html) return;
    var newContent = html.replace(/<img[^>]*>/gi, function (match, capture) {
      match = match.replace(/style="[^"]+"/gi, '').replace(/style='[^']+'/gi, '');
      match = match.replace(/width="[^"]+"/gi, '').replace(/width='[^']+'/gi, '');
      match = match.replace(/height="[^"]+"/gi, '').replace(/height='[^']+'/gi, '');
      return match;
    });
    newContent = newContent.replace(/style="[^"]+"/gi, function (match, capture) {
      match = match.replace(/width:[^;]+;/gi, 'max-width:100%;').replace(/width:[^;]+;/gi, 'max-width:100%;');
      return match;
    });
    newContent = newContent.replace(/<br[^>]*\/>/gi, '');
    newContent = newContent.replace(/\<img/gi, '<img style="max-width:100%;height:auto;margin:0 auto;display:inline-block;"');
    return newContent;
  },
  //记录分享人
  onSourceMember: function onSourceMember(id) {
    _http.default.sendRequest({
      url: '/api/Member/alterShareRelation',
      data: {
        share_member: id
      },
      success: function success(res) {
        if (res.code >= 0) {
          uni.removeStorage({
            key: 'source_member',
            success: function success(res) {
              console.log('删除成功', res);
            }
          });
        }
      }
    });
  }
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 36:
/*!************************************************************************************************!*\
  !*** ./node_modules/@dcloudio/vue-cli-plugin-uni/packages/@babel/runtime/regenerator/index.js ***!
  \************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// TODO(Babel 8): Remove this file.

var runtime = __webpack_require__(/*! @babel/runtime/helpers/regeneratorRuntime */ 37)();
module.exports = runtime;

/***/ }),

/***/ 37:
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/regeneratorRuntime.js ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var _typeof = __webpack_require__(/*! ./typeof.js */ 13)["default"];
function _regeneratorRuntime() {
  "use strict";

  /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */
  module.exports = _regeneratorRuntime = function _regeneratorRuntime() {
    return e;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports;
  var t,
    e = {},
    r = Object.prototype,
    n = r.hasOwnProperty,
    o = Object.defineProperty || function (t, e, r) {
      t[e] = r.value;
    },
    i = "function" == typeof Symbol ? Symbol : {},
    a = i.iterator || "@@iterator",
    c = i.asyncIterator || "@@asyncIterator",
    u = i.toStringTag || "@@toStringTag";
  function define(t, e, r) {
    return Object.defineProperty(t, e, {
      value: r,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }), t[e];
  }
  try {
    define({}, "");
  } catch (t) {
    define = function define(t, e, r) {
      return t[e] = r;
    };
  }
  function wrap(t, e, r, n) {
    var i = e && e.prototype instanceof Generator ? e : Generator,
      a = Object.create(i.prototype),
      c = new Context(n || []);
    return o(a, "_invoke", {
      value: makeInvokeMethod(t, r, c)
    }), a;
  }
  function tryCatch(t, e, r) {
    try {
      return {
        type: "normal",
        arg: t.call(e, r)
      };
    } catch (t) {
      return {
        type: "throw",
        arg: t
      };
    }
  }
  e.wrap = wrap;
  var h = "suspendedStart",
    l = "suspendedYield",
    f = "executing",
    s = "completed",
    y = {};
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}
  var p = {};
  define(p, a, function () {
    return this;
  });
  var d = Object.getPrototypeOf,
    v = d && d(d(values([])));
  v && v !== r && n.call(v, a) && (p = v);
  var g = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(p);
  function defineIteratorMethods(t) {
    ["next", "throw", "return"].forEach(function (e) {
      define(t, e, function (t) {
        return this._invoke(e, t);
      });
    });
  }
  function AsyncIterator(t, e) {
    function invoke(r, o, i, a) {
      var c = tryCatch(t[r], t, o);
      if ("throw" !== c.type) {
        var u = c.arg,
          h = u.value;
        return h && "object" == _typeof(h) && n.call(h, "__await") ? e.resolve(h.__await).then(function (t) {
          invoke("next", t, i, a);
        }, function (t) {
          invoke("throw", t, i, a);
        }) : e.resolve(h).then(function (t) {
          u.value = t, i(u);
        }, function (t) {
          return invoke("throw", t, i, a);
        });
      }
      a(c.arg);
    }
    var r;
    o(this, "_invoke", {
      value: function value(t, n) {
        function callInvokeWithMethodAndArg() {
          return new e(function (e, r) {
            invoke(t, n, e, r);
          });
        }
        return r = r ? r.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg();
      }
    });
  }
  function makeInvokeMethod(e, r, n) {
    var o = h;
    return function (i, a) {
      if (o === f) throw Error("Generator is already running");
      if (o === s) {
        if ("throw" === i) throw a;
        return {
          value: t,
          done: !0
        };
      }
      for (n.method = i, n.arg = a;;) {
        var c = n.delegate;
        if (c) {
          var u = maybeInvokeDelegate(c, n);
          if (u) {
            if (u === y) continue;
            return u;
          }
        }
        if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) {
          if (o === h) throw o = s, n.arg;
          n.dispatchException(n.arg);
        } else "return" === n.method && n.abrupt("return", n.arg);
        o = f;
        var p = tryCatch(e, r, n);
        if ("normal" === p.type) {
          if (o = n.done ? s : l, p.arg === y) continue;
          return {
            value: p.arg,
            done: n.done
          };
        }
        "throw" === p.type && (o = s, n.method = "throw", n.arg = p.arg);
      }
    };
  }
  function maybeInvokeDelegate(e, r) {
    var n = r.method,
      o = e.iterator[n];
    if (o === t) return r.delegate = null, "throw" === n && e.iterator["return"] && (r.method = "return", r.arg = t, maybeInvokeDelegate(e, r), "throw" === r.method) || "return" !== n && (r.method = "throw", r.arg = new TypeError("The iterator does not provide a '" + n + "' method")), y;
    var i = tryCatch(o, e.iterator, r.arg);
    if ("throw" === i.type) return r.method = "throw", r.arg = i.arg, r.delegate = null, y;
    var a = i.arg;
    return a ? a.done ? (r[e.resultName] = a.value, r.next = e.nextLoc, "return" !== r.method && (r.method = "next", r.arg = t), r.delegate = null, y) : a : (r.method = "throw", r.arg = new TypeError("iterator result is not an object"), r.delegate = null, y);
  }
  function pushTryEntry(t) {
    var e = {
      tryLoc: t[0]
    };
    1 in t && (e.catchLoc = t[1]), 2 in t && (e.finallyLoc = t[2], e.afterLoc = t[3]), this.tryEntries.push(e);
  }
  function resetTryEntry(t) {
    var e = t.completion || {};
    e.type = "normal", delete e.arg, t.completion = e;
  }
  function Context(t) {
    this.tryEntries = [{
      tryLoc: "root"
    }], t.forEach(pushTryEntry, this), this.reset(!0);
  }
  function values(e) {
    if (e || "" === e) {
      var r = e[a];
      if (r) return r.call(e);
      if ("function" == typeof e.next) return e;
      if (!isNaN(e.length)) {
        var o = -1,
          i = function next() {
            for (; ++o < e.length;) {
              if (n.call(e, o)) return next.value = e[o], next.done = !1, next;
            }
            return next.value = t, next.done = !0, next;
          };
        return i.next = i;
      }
    }
    throw new TypeError(_typeof(e) + " is not iterable");
  }
  return GeneratorFunction.prototype = GeneratorFunctionPrototype, o(g, "constructor", {
    value: GeneratorFunctionPrototype,
    configurable: !0
  }), o(GeneratorFunctionPrototype, "constructor", {
    value: GeneratorFunction,
    configurable: !0
  }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, u, "GeneratorFunction"), e.isGeneratorFunction = function (t) {
    var e = "function" == typeof t && t.constructor;
    return !!e && (e === GeneratorFunction || "GeneratorFunction" === (e.displayName || e.name));
  }, e.mark = function (t) {
    return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, define(t, u, "GeneratorFunction")), t.prototype = Object.create(g), t;
  }, e.awrap = function (t) {
    return {
      __await: t
    };
  }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, c, function () {
    return this;
  }), e.AsyncIterator = AsyncIterator, e.async = function (t, r, n, o, i) {
    void 0 === i && (i = Promise);
    var a = new AsyncIterator(wrap(t, r, n, o), i);
    return e.isGeneratorFunction(r) ? a : a.next().then(function (t) {
      return t.done ? t.value : a.next();
    });
  }, defineIteratorMethods(g), define(g, u, "Generator"), define(g, a, function () {
    return this;
  }), define(g, "toString", function () {
    return "[object Generator]";
  }), e.keys = function (t) {
    var e = Object(t),
      r = [];
    for (var n in e) {
      r.push(n);
    }
    return r.reverse(), function next() {
      for (; r.length;) {
        var t = r.pop();
        if (t in e) return next.value = t, next.done = !1, next;
      }
      return next.done = !0, next;
    };
  }, e.values = values, Context.prototype = {
    constructor: Context,
    reset: function reset(e) {
      if (this.prev = 0, this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(resetTryEntry), !e) for (var r in this) {
        "t" === r.charAt(0) && n.call(this, r) && !isNaN(+r.slice(1)) && (this[r] = t);
      }
    },
    stop: function stop() {
      this.done = !0;
      var t = this.tryEntries[0].completion;
      if ("throw" === t.type) throw t.arg;
      return this.rval;
    },
    dispatchException: function dispatchException(e) {
      if (this.done) throw e;
      var r = this;
      function handle(n, o) {
        return a.type = "throw", a.arg = e, r.next = n, o && (r.method = "next", r.arg = t), !!o;
      }
      for (var o = this.tryEntries.length - 1; o >= 0; --o) {
        var i = this.tryEntries[o],
          a = i.completion;
        if ("root" === i.tryLoc) return handle("end");
        if (i.tryLoc <= this.prev) {
          var c = n.call(i, "catchLoc"),
            u = n.call(i, "finallyLoc");
          if (c && u) {
            if (this.prev < i.catchLoc) return handle(i.catchLoc, !0);
            if (this.prev < i.finallyLoc) return handle(i.finallyLoc);
          } else if (c) {
            if (this.prev < i.catchLoc) return handle(i.catchLoc, !0);
          } else {
            if (!u) throw Error("try statement without catch or finally");
            if (this.prev < i.finallyLoc) return handle(i.finallyLoc);
          }
        }
      }
    },
    abrupt: function abrupt(t, e) {
      for (var r = this.tryEntries.length - 1; r >= 0; --r) {
        var o = this.tryEntries[r];
        if (o.tryLoc <= this.prev && n.call(o, "finallyLoc") && this.prev < o.finallyLoc) {
          var i = o;
          break;
        }
      }
      i && ("break" === t || "continue" === t) && i.tryLoc <= e && e <= i.finallyLoc && (i = null);
      var a = i ? i.completion : {};
      return a.type = t, a.arg = e, i ? (this.method = "next", this.next = i.finallyLoc, y) : this.complete(a);
    },
    complete: function complete(t, e) {
      if ("throw" === t.type) throw t.arg;
      return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && e && (this.next = e), y;
    },
    finish: function finish(t) {
      for (var e = this.tryEntries.length - 1; e >= 0; --e) {
        var r = this.tryEntries[e];
        if (r.finallyLoc === t) return this.complete(r.completion, r.afterLoc), resetTryEntry(r), y;
      }
    },
    "catch": function _catch(t) {
      for (var e = this.tryEntries.length - 1; e >= 0; --e) {
        var r = this.tryEntries[e];
        if (r.tryLoc === t) {
          var n = r.completion;
          if ("throw" === n.type) {
            var o = n.arg;
            resetTryEntry(r);
          }
          return o;
        }
      }
      throw Error("illegal catch attempt");
    },
    delegateYield: function delegateYield(e, r, n) {
      return this.delegate = {
        iterator: values(e),
        resultName: r,
        nextLoc: n
      }, "next" === this.method && (this.arg = t), y;
    }
  }, e;
}
module.exports = _regeneratorRuntime, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 38:
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/asyncToGenerator.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
  try {
    var info = gen[key](arg);
    var value = info.value;
  } catch (error) {
    reject(error);
    return;
  }
  if (info.done) {
    resolve(value);
  } else {
    Promise.resolve(value).then(_next, _throw);
  }
}
function _asyncToGenerator(fn) {
  return function () {
    var self = this,
      args = arguments;
    return new Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);
      function _next(value) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
      }
      function _throw(err) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
      }
      _next(undefined);
    });
  };
}
module.exports = _asyncToGenerator, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 39:
/*!************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/common/js/config.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var config = {
  // api请求地址
  baseUrl: 'http://tuanshenghuo.xing-peng.com',
  // 图片域名	
  imgDomain: 'http://tuanshenghuo.xing-peng.com',
  // /index.php/api
  // H5端域名
  h5Domain: 'http://tuanshenghuo.xing-peng.com',
  // 腾讯地图key
  mpKey: '',
  //客服地址
  webSocket: '',
  //本地端主动给服务器ping的时间, 0 则不开启 , 单位秒
  pingInterval: 1500
};
var _default = config;
exports.default = _default;

/***/ }),

/***/ 4:
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/interopRequireDefault.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _interopRequireDefault(obj) {
  return obj && obj.__esModule ? obj : {
    "default": obj
  };
}
module.exports = _interopRequireDefault, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 40:
/*!**********************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/common/js/http.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _config = _interopRequireDefault(__webpack_require__(/*! ./config.js */ 39));
var _util = _interopRequireDefault(__webpack_require__(/*! ./util.js */ 35));
var app_type = 'weapp';
var app_type_name = '微信小程序';
var _default = {
  sendRequest: function sendRequest(params) {
    uni.showLoading({
      title: '加载中'
    });
    var method = params.data != undefined ? 'POST' : 'GET',
      // 请求方式

      url = _config.default.baseUrl + params.url,
      // 请求路径
      data = {
        // app_type,
        // app_type_name
      },
      token = '';
    // token
    if (uni.getStorageSync('token')) token = uni.getStorageSync('token');

    // 参数
    if (params.data != undefined) Object.assign(data, params.data);
    if (params.method) {
      method = params.method;
    }
    uni.request({
      url: url,
      method: method,
      data: data,
      header: params.header || {
        'content-type': 'application/json',
        "token": token ? token : ''
      },
      dataType: params.dataType || 'json',
      responseType: params.responseType || 'text',
      success: function success(res) {
        console.log(res, '+++++++++++');
        uni.hideLoading();
        if (res.data.msg == "请登录后操作" || res.data.code == "401" || res.msg == "请登录后操作" || res.msg == "401") {
          uni.removeStorageSync('token');
          uni.removeStorageSync('identity_key');
          //登录失效页面返回
          uni.showModal({
            title: '登录过期',
            content: '请前往登录',
            success: function success(res) {
              if (res.confirm) {
                uni.navigateTo({
                  url: '/pages/Home/Home'
                });
              } else if (res.cancel) {}
            }
          });
        } else {
          typeof params.success == 'function' && params.success(res.data);
        }
      },
      fail: function fail(res) {
        uni.hideLoading();
        typeof params.fail == 'function' && params.fail(res);
      },
      complete: function complete(res) {
        uni.hideLoading();
        typeof params.complete == 'function' && params.complete(res);
      }
    });
  }
};
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 41:
/*!***********************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/common/js/share.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(uni) {

var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ 4);
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ 11));
var MemberNO = uni.getStorageSync('MemberNO');
var _default = (0, _defineProperty2.default)({
  data: function data() {
    return {
      // 设置默认的分享参数
      // 如果页面不设置share，就触发这个默认的分享
      share: {
        title: '快来和我一起体验吧~',
        path: '/pages/index/index'
      }
    };
  },
  onShareAppMessage: function onShareAppMessage(res) {
    var that = this;
    return {
      title: that.share.title,
      path: that.share.path,
      success: function success(res) {
        uni.showToast({
          title: '分享成功'
        });
      },
      fail: function fail(res) {
        uni.showToast({
          title: '分享失败',
          icon: 'none'
        });
      }
    };
  },
  onShareTimeline: function onShareTimeline(res) {
    // 分享到朋友圈
    var that = this;
    return {
      title: that.share.title,
      path: that.share.path
    };
  }
}, "onShareAppMessage", function onShareAppMessage() {
  var that = this;
  return {
    title: that.share.title,
    path: that.share.path
  };
});
exports.default = _default;
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./node_modules/@dcloudio/uni-mp-weixin/dist/index.js */ 2)["default"]))

/***/ }),

/***/ 48:
/*!***********************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/static/home/n1.png ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "data:image/png;base64,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"

/***/ }),

/***/ 49:
/*!***********************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/static/home/n2.png ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "data:image/png;base64,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"

/***/ }),

/***/ 5:
/*!**************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/slicedToArray.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles.js */ 6);
var iterableToArrayLimit = __webpack_require__(/*! ./iterableToArrayLimit.js */ 7);
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ 8);
var nonIterableRest = __webpack_require__(/*! ./nonIterableRest.js */ 10);
function _slicedToArray(arr, i) {
  return arrayWithHoles(arr) || iterableToArrayLimit(arr, i) || unsupportedIterableToArray(arr, i) || nonIterableRest();
}
module.exports = _slicedToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 50:
/*!***********************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/static/home/n3.png ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "data:image/png;base64,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"

/***/ }),

/***/ 51:
/*!***********************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/static/home/n4.png ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "data:image/png;base64,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"

/***/ }),

/***/ 52:
/*!********************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/static/logo.png ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "data:image/png;base64,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UmVhZHlxyWU8AAADKmlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxMzIgNzkuMTU5Mjg0LCAyMDE2LzA0LzE5LTEzOjEzOjQwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGRkE0MjcxNTdEQzYxMUU4QkZBOERDOEVCQ0U0NTBGMSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGRkE0MjcxNDdEQzYxMUU4QkZBOERDOEVCQ0U0NTBGMSIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNS41IChNYWNpbnRvc2gpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QkE4RkFCN0M3REM1MTFFOEJGQThEQzhFQkNFNDUwRjEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QkE4RkFCN0Q3REM1MTFFOEJGQThEQzhFQkNFNDUwRjEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5BZZ+3AAAB1ElEQVR42mJkAALtmZb/GfAAJkIKwIoYiAA4FV1JO0Ylk0hWxILLHTgV6cyywqoIIIAYiQinb8S4iYs036E7esgEJq6ABAGAACImMBmo5m6yDcLlR5gcNnnaumhADWIhJoOTbRC+9ILPa9+o4TWAAAIlyDVAOphCc1SYqGAICNwZxumIidi8NILz2qhBdCyPaOcicgq1wRnYAAFErRKSgZo+GzSOoWpQD1sHsRCjCDnzkpp90DM+If2jUTbqoFEHjZZDpJYroyFESeNmNFHTykEqg8g9bwACCNRiVAYyLgEx1wA7Zu3V9OMhVBt1opajBlsaCh7NZaMOGnXQgFeupHZjKO1CjUbZqINGHTTqoFEHjTpo1EGjDhqMgw342kejUTaahggpoOdg1WiUjTpoODoIvL7tzSBykB5AgPbtGIdBGIYCaBR16swROEQvzT06cxjm1lRFDC0LcpXC+xJzpIdJhOW8e4z359MVWSde1C32xRYasC0mCmascDZzrQz+7NgABAgQINnRY/iUrb5D9v9l9toqCBAgQIAAAQIESAABAgQIEKCD5ZK9QPaMigoCdIJP7NdjOyoIECBAgGQBGjB8zVDjam153T0OqInJbBAWfdg8AExKZVcA71uIAAAAAElFTkSuQmCC"

/***/ }),

/***/ 6:
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayWithHoles.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}
module.exports = _arrayWithHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 61:
/*!***************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/static/index/icon1.png ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "data:image/png;base64,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"

/***/ }),

/***/ 62:
/*!***************************************************!*\
  !*** D:/项目/新项目的模版/shopBIZ/static/index/icon2.png ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "data:image/png;base64,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"

/***/ }),

/***/ 7:
/*!*********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _iterableToArrayLimit(r, l) {
  var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
  if (null != t) {
    var e,
      n,
      i,
      u,
      a = [],
      f = !0,
      o = !1;
    try {
      if (i = (t = t.call(r)).next, 0 === l) {
        if (Object(t) !== t) return;
        f = !1;
      } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0) {
        ;
      }
    } catch (r) {
      o = !0, n = r;
    } finally {
      try {
        if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return;
      } finally {
        if (o) throw n;
      }
    }
    return a;
  }
}
module.exports = _iterableToArrayLimit, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 8:
/*!***************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ 9);
function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return arrayLikeToArray(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return arrayLikeToArray(o, minLen);
}
module.exports = _unsupportedIterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 9:
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;
  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }
  return arr2;
}
module.exports = _arrayLikeToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ })

}]);
//# sourceMappingURL=../../.sourcemap/mp-weixin/common/vendor.js.map