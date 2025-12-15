/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/css/app.css":
/*!*******************************!*\
  !*** ./resources/css/app.css ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/css/layout.css":
/*!**********************************!*\
  !*** ./resources/css/layout.css ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/css/loading-in-btn.css":
/*!******************************************!*\
  !*** ./resources/css/loading-in-btn.css ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/css/loading.css":
/*!***********************************!*\
  !*** ./resources/css/loading.css ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/css/table.css":
/*!*********************************!*\
  !*** ./resources/css/table.css ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/js/api.js":
/*!*****************************!*\
  !*** ./resources/js/api.js ***!
  \*****************************/
/***/ (() => {

function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
// Hàm lõi: Chỉ lo việc gửi, check lỗi và refresh token
function baseApiRequest(_x) {
  return _baseApiRequest.apply(this, arguments);
}
/**
 * Dùng cho dữ liệu JSON thuần túy (Object, Array...)
 * @param {string} url - URL endpoint
 * @param {string} method - HTTP method (GET, POST, PUT, DELETE...)
 * @param {Object|null} body - Request body (sẽ được stringify)
 * @param {Object|null} queryParams - Query parameters (sẽ được append vào URL)
 */
function _baseApiRequest() {
  _baseApiRequest = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee(url) {
    var options,
      headers,
      response,
      refreshResponse,
      data,
      _args = arguments,
      _t,
      _t2;
    return _regenerator().w(function (_context) {
      while (1) switch (_context.p = _context.n) {
        case 0:
          options = _args.length > 1 && _args[1] !== undefined ? _args[1] : {};
          // Merge headers mặc định (nếu cần thêm Authorization header chung thì thêm ở đây)
          headers = _objectSpread({
            'Accept': 'application/json'
          }, options.headers);
          _context.p = 1;
          _context.n = 2;
          return fetch("/api" + url, _objectSpread(_objectSpread({}, options), {}, {
            headers: headers,
            credentials: 'include'
          }));
        case 2:
          response = _context.v;
          if (!(response.status === 401)) {
            _context.n = 8;
            break;
          }
          _context.p = 3;
          _context.n = 4;
          return fetch("/api/auth/refresh", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            credentials: 'include'
          });
        case 4:
          refreshResponse = _context.v;
          if (!refreshResponse.ok) {
            _context.n = 6;
            break;
          }
          _context.n = 5;
          return baseApiRequest(url, options);
        case 5:
          return _context.a(2, _context.v);
        case 6:
          _context.n = 8;
          break;
        case 7:
          _context.p = 7;
          _t = _context.v;
          // Nếu không có refresh token hoặc refresh thất bại
          window.location.href = '/admin/login';
          throw new Error('Phiên đăng nhập hết hạn');
        case 8:
          _context.n = 9;
          return response.json();
        case 9:
          data = _context.v;
          if (response.ok) {
            _context.n = 10;
            break;
          }
          throw new Error(data.message || 'Có lỗi xảy ra');
        case 10:
          return _context.a(2, data);
        case 11:
          _context.p = 11;
          _t2 = _context.v;
          console.error('API Error:', _t2);
          throw _t2;
        case 12:
          return _context.a(2);
      }
    }, _callee, null, [[3, 7], [1, 11]]);
  }));
  return _baseApiRequest.apply(this, arguments);
}
function apiJsonRequest(_x2) {
  return _apiJsonRequest.apply(this, arguments);
}
/**
 * Dùng cho Upload File hoặc FormData
 */
function _apiJsonRequest() {
  _apiJsonRequest = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee2(url) {
    var method,
      body,
      queryParams,
      params,
      _i,
      _Object$entries,
      _Object$entries$_i,
      key,
      value,
      queryString,
      options,
      _args2 = arguments;
    return _regenerator().w(function (_context2) {
      while (1) switch (_context2.n) {
        case 0:
          method = _args2.length > 1 && _args2[1] !== undefined ? _args2[1] : 'GET';
          body = _args2.length > 2 && _args2[2] !== undefined ? _args2[2] : null;
          queryParams = _args2.length > 3 && _args2[3] !== undefined ? _args2[3] : null;
          // Xử lý query params
          if (queryParams && _typeof(queryParams) === 'object') {
            params = new URLSearchParams();
            for (_i = 0, _Object$entries = Object.entries(queryParams); _i < _Object$entries.length; _i++) {
              _Object$entries$_i = _slicedToArray(_Object$entries[_i], 2), key = _Object$entries$_i[0], value = _Object$entries$_i[1];
              if (value !== null && value !== undefined && value !== '') {
                params.append(key, value);
              }
            }
            queryString = params.toString();
            if (queryString) {
              url += (url.includes('?') ? '&' : '?') + queryString;
            }
          }
          options = {
            method: method,
            headers: {
              'Content-Type': 'application/json' // Bắt buộc phải có
            }
          };
          if (body) {
            options.body = JSON.stringify(body); // Tự động stringify
          }

          // Gọi hàm lõi
          _context2.n = 1;
          return baseApiRequest(url, options);
        case 1:
          return _context2.a(2, _context2.v);
      }
    }, _callee2);
  }));
  return _apiJsonRequest.apply(this, arguments);
}
function apiFormDataRequest(_x3) {
  return _apiFormDataRequest.apply(this, arguments);
}
function _apiFormDataRequest() {
  _apiFormDataRequest = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee3(url) {
    var method,
      formData,
      options,
      _args3 = arguments;
    return _regenerator().w(function (_context3) {
      while (1) switch (_context3.n) {
        case 0:
          method = _args3.length > 1 && _args3[1] !== undefined ? _args3[1] : 'POST';
          formData = _args3.length > 2 ? _args3[2] : undefined;
          options = {
            method: method,
            headers: {
              // QUAN TRỌNG: Không được set 'Content-Type' ở đây.
              // Để trình duyệt tự động set thành 'multipart/form-data; boundary=...'
            },
            body: formData // Truyền trực tiếp object FormData
          }; // Gọi hàm lõi
          _context3.n = 1;
          return baseApiRequest(url, options);
        case 1:
          return _context3.a(2, _context3.v);
      }
    }, _callee3);
  }));
  return _apiFormDataRequest.apply(this, arguments);
}
window.apiJsonRequest = apiJsonRequest;
window.apiFormDataRequest = apiFormDataRequest;

/***/ }),

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

__webpack_require__(/*! ./bootstrap */ "./resources/js/bootstrap.js");
__webpack_require__(/*! ./handle-navigate */ "./resources/js/handle-navigate.js");
__webpack_require__(/*! ./api */ "./resources/js/api.js");
__webpack_require__(/*! ./providers/user.provider */ "./resources/js/providers/user.provider.js");
__webpack_require__(/*! ./providers/role.provider */ "./resources/js/providers/role.provider.js");
__webpack_require__(/*! ./util */ "./resources/js/util.js");
__webpack_require__(/*! ./pagination */ "./resources/js/pagination.js");
__webpack_require__(/*! ./render-error */ "./resources/js/render-error.js");

/***/ }),

/***/ "./resources/js/bootstrap.js":
/*!***********************************!*\
  !*** ./resources/js/bootstrap.js ***!
  \***********************************/
/***/ (() => {

// window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// window.axios = require('axios');

// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

/***/ }),

/***/ "./resources/js/handle-navigate.js":
/*!*****************************************!*\
  !*** ./resources/js/handle-navigate.js ***!
  \*****************************************/
/***/ (() => {

function handleGotoBackPage_Global() {
  var targetPage = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '/admin';
  var currentRoute = window.location.pathname + (window.location.search || '');
  var prevPageIndex = currentRoute.indexOf('prev_page_url=');
  if (prevPageIndex !== -1) {
    var afterPrevPage = currentRoute.substring(prevPageIndex + 'prev_page_url='.length);
    targetPage = decodeURIComponent(afterPrevPage.split('&')[0]);
  }
  window.location.href = targetPage;
}

// expose to Blade inline scripts
window.handleGotoBackPage_Global = handleGotoBackPage_Global;

/***/ }),

/***/ "./resources/js/pagination.js":
/*!************************************!*\
  !*** ./resources/js/pagination.js ***!
  \************************************/
/***/ (() => {

function renderPaginationInfo_Global() {
  var htmlElementId = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  if (!htmlElementId) return '';
  var element = document.getElementById(htmlElementId);
  if (!element) return '';
  element.innerHTML = "\n        Hi\u1EC3n th\u1ECB <span class=\"font-semibold text-gray-900 dark:text-gray-100\">".concat((data === null || data === void 0 ? void 0 : data.from) || 0, "</span>\n        \u2013 <span class=\"font-semibold text-gray-900 dark:text-gray-100\">").concat((data === null || data === void 0 ? void 0 : data.to) || 0, "</span>\n        trong <span class=\"font-semibold text-gray-900 dark:text-gray-100\">").concat((data === null || data === void 0 ? void 0 : data.total) || 0, "</span>\n    ");
}
function renderPaginationChangeItemPerPage_Global() {
  var htmlItemPerPageElementId = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var itemPerPage = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 50;
  var paginationData = arguments.length > 2 ? arguments[2] : undefined;
  var functionCallback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : function () {};
  if (!htmlItemPerPageElementId) return '';
  var element = document.getElementById(htmlItemPerPageElementId);
  if (!element) return '';
  var html = '<label for="itemPerPage" class="text-gray-600 dark:text-gray-400 whitespace-nowrap">Số mục mỗi trang</label>';
  html += "\n        <select id=\"itemPerPage\"\n            class=\"px-2 py-0.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500\">\n            <option value=\"10\" ".concat(itemPerPage == 10 ? 'selected' : '', ">10</option>\n            <option value=\"15\" ").concat(itemPerPage == 15 ? 'selected' : '', ">15</option>\n            <option value=\"25\" ").concat(itemPerPage == 25 ? 'selected' : '', ">25</option>\n            <option value=\"50\" ").concat(itemPerPage == 50 ? 'selected' : '', ">50</option>\n            <option value=\"100\" ").concat(itemPerPage == 100 ? 'selected' : '', ">100</option>\n            ").concat([10, 15, 25, 50, 100].includes(itemPerPage) ? '' : "<option value=\"".concat(itemPerPage, "\" selected>").concat(itemPerPage, "</option>"), "\n        </select>\n    ");
  element.innerHTML = html;
  var selectElement = element.querySelector('#itemPerPage');
  if (selectElement) {
    selectElement.addEventListener('change', function (e) {
      var numberItemPerPage = parseInt(e.target.value);
      functionCallback(1, numberItemPerPage);
    });
  }
}
function renderPaginationChangePage_Global() {
  var _document$getElementB;
  var htmlChangePageElementId = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var htmlChangeItemPerPageElementId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  var paginationData = arguments.length > 2 ? arguments[2] : undefined;
  var functionCallback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : function () {};
  if (!htmlChangePageElementId) return '';
  var pagination = document.getElementById(htmlChangePageElementId);
  if (!pagination) return '';
  var selectElement = ((_document$getElementB = document.getElementById(htmlChangeItemPerPageElementId)) === null || _document$getElementB === void 0 ? void 0 : _document$getElementB.getElementsByTagName('select')[0]) || null;
  var itemPerPage = selectElement != null ? parseInt(selectElement.value || 50) : 50;
  var current = paginationData.current_page;
  var last = paginationData.last_page;
  if (last <= 1) {
    pagination.innerHTML = '';
    return;
  }
  var html = '';

  // Previous Button
  if (current > 1) {
    html += "<button type=\"button\" data-page=\"".concat(current - 1, "\" class=\"pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700\">Tr\u01B0\u1EDBc</button>");
  } else {
    html += "<button type=\"button\" disabled class=\"px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-700 opacity-50 cursor-not-allowed\">Tr\u01B0\u1EDBc</button>";
  }

  // Page Numbers
  if (last <= 7) {
    // Show all pages if 7 or fewer
    for (var i = 1; i <= last; i++) {
      if (i === current) {
        html += "<button type=\"button\" class=\"px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600\">".concat(i, "</button>");
      } else {
        html += "<button type=\"button\" data-page=\"".concat(i, "\" class=\"pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700\">").concat(i, "</button>");
      }
    }
  } else {
    // Complex pagination for many pages
    if (current <= 3) {
      // Show first 3, ellipsis, last
      for (var _i = 1; _i <= 3; _i++) {
        if (_i === current) {
          html += "<button type=\"button\" class=\"px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600\">".concat(_i, "</button>");
        } else {
          html += "<button type=\"button\" data-page=\"".concat(_i, "\" class=\"pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700\">").concat(_i, "</button>");
        }
      }
      html += "<span class=\"inline-flex items-center justify-center px-3 py-1 text-gray-400\">...</span>";
      html += "<button type=\"button\" data-page=\"".concat(last, "\" class=\"pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700\">").concat(last, "</button>");
    } else if (current >= last - 2) {
      // Show first, ellipsis, last 3
      html += "<button type=\"button\" data-page=\"1\" class=\"pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700\">1</button>";
      html += "<span class=\"inline-flex items-center justify-center px-3 py-1 text-gray-400\">...</span>";
      for (var _i2 = last - 2; _i2 <= last; _i2++) {
        if (_i2 === current) {
          html += "<button type=\"button\" class=\"px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600\">".concat(_i2, "</button>");
        } else {
          html += "<button type=\"button\" data-page=\"".concat(_i2, "\" class=\"pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700\">").concat(_i2, "</button>");
        }
      }
    } else {
      // Show first, ellipsis, current-1, current, current+1, ellipsis, last
      html += "<button type=\"button\" data-page=\"1\" class=\"pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700\">1</button>";
      html += "<span class=\"inline-flex items-center justify-center px-3 py-1 text-gray-400\">...</span>";
      for (var _i3 = current - 1; _i3 <= current + 1; _i3++) {
        if (_i3 === current) {
          html += "<button type=\"button\" class=\"px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600\">".concat(_i3, "</button>");
        } else {
          html += "<button type=\"button\" data-page=\"".concat(_i3, "\" class=\"pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700\">").concat(_i3, "</button>");
        }
      }
      html += "<span class=\"inline-flex items-center justify-center px-3 py-1 text-gray-400\">...</span>";
      html += "<button type=\"button\" data-page=\"".concat(last, "\" class=\"pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700\">").concat(last, "</button>");
    }
  }

  // Next Button
  if (current < last) {
    html += "<button type=\"button\" data-page=\"".concat(current + 1, "\" class=\"pagination-btn px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700\">Sau</button>");
  } else {
    html += "<button type=\"button\" disabled class=\"px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-700 opacity-50 cursor-not-allowed\">Sau</button>";
  }
  pagination.innerHTML = html;

  // Attach event listeners to pagination buttons
  pagination.querySelectorAll('.pagination-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var page = parseInt(this.dataset.page);
      functionCallback(page, itemPerPage);
    });
  });
}

// expose to Blade inline scripts
window.renderPaginationInfo_Global = renderPaginationInfo_Global;
window.renderPaginationChangeItemPerPage_Global = renderPaginationChangeItemPerPage_Global;
window.renderPaginationChangePage_Global = renderPaginationChangePage_Global;

/***/ }),

/***/ "./resources/js/providers/role.provider.js":
/*!*************************************************!*\
  !*** ./resources/js/providers/role.provider.js ***!
  \*************************************************/
/***/ (() => {

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var RoleProvider = /*#__PURE__*/function () {
  function RoleProvider() {
    _classCallCheck(this, RoleProvider);
  }
  return _createClass(RoleProvider, [{
    key: "handleGetRoles",
    value: function () {
      var _handleGetRoles = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee() {
        var queryParams,
          data,
          _args = arguments,
          _t;
        return _regenerator().w(function (_context) {
          while (1) switch (_context.p = _context.n) {
            case 0:
              queryParams = _args.length > 0 && _args[0] !== undefined ? _args[0] : {};
              _context.p = 1;
              _context.n = 2;
              return apiJsonRequest('/roles', 'GET', null, queryParams);
            case 2:
              data = _context.v;
              return _context.a(2, data);
            case 3:
              _context.p = 3;
              _t = _context.v;
              return _context.a(2, {
                success: false,
                message: _t.message
              });
          }
        }, _callee, null, [[1, 3]]);
      }));
      function handleGetRoles() {
        return _handleGetRoles.apply(this, arguments);
      }
      return handleGetRoles;
    }()
  }]);
}();
window.RoleProvider = new RoleProvider();

/***/ }),

/***/ "./resources/js/providers/user.provider.js":
/*!*************************************************!*\
  !*** ./resources/js/providers/user.provider.js ***!
  \*************************************************/
/***/ (() => {

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var UserProvider = /*#__PURE__*/function () {
  function UserProvider() {
    _classCallCheck(this, UserProvider);
  }
  return _createClass(UserProvider, [{
    key: "handleGetUsers",
    value: function () {
      var _handleGetUsers = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee() {
        var queryParams,
          data,
          _args = arguments,
          _t;
        return _regenerator().w(function (_context) {
          while (1) switch (_context.p = _context.n) {
            case 0:
              queryParams = _args.length > 0 && _args[0] !== undefined ? _args[0] : {};
              _context.p = 1;
              _context.n = 2;
              return apiJsonRequest('/users', 'GET', null, queryParams);
            case 2:
              data = _context.v;
              return _context.a(2, data);
            case 3:
              _context.p = 3;
              _t = _context.v;
              return _context.a(2, {
                success: false,
                message: _t.message
              });
          }
        }, _callee, null, [[1, 3]]);
      }));
      function handleGetUsers() {
        return _handleGetUsers.apply(this, arguments);
      }
      return handleGetUsers;
    }()
  }, {
    key: "handleGetUserById",
    value: function () {
      var _handleGetUserById = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee2(userId) {
        var data, _t2;
        return _regenerator().w(function (_context2) {
          while (1) switch (_context2.p = _context2.n) {
            case 0:
              _context2.p = 0;
              _context2.n = 1;
              return apiJsonRequest("/users/".concat(userId), 'GET');
            case 1:
              data = _context2.v;
              return _context2.a(2, data);
            case 2:
              _context2.p = 2;
              _t2 = _context2.v;
              return _context2.a(2, {
                success: false,
                message: _t2.message
              });
          }
        }, _callee2, null, [[0, 2]]);
      }));
      function handleGetUserById(_x) {
        return _handleGetUserById.apply(this, arguments);
      }
      return handleGetUserById;
    }()
  }, {
    key: "handleDeleteUsers",
    value: function () {
      var _handleDeleteUsers = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee3() {
        var arrayIds,
          data,
          _args3 = arguments,
          _t3;
        return _regenerator().w(function (_context3) {
          while (1) switch (_context3.p = _context3.n) {
            case 0:
              arrayIds = _args3.length > 0 && _args3[0] !== undefined ? _args3[0] : [];
              _context3.p = 1;
              _context3.n = 2;
              return apiJsonRequest('/users', 'DELETE', {
                user_ids: arrayIds
              });
            case 2:
              data = _context3.v;
              return _context3.a(2, data);
            case 3:
              _context3.p = 3;
              _t3 = _context3.v;
              return _context3.a(2, {
                success: false,
                message: _t3.message
              });
          }
        }, _callee3, null, [[1, 3]]);
      }));
      function handleDeleteUsers() {
        return _handleDeleteUsers.apply(this, arguments);
      }
      return handleDeleteUsers;
    }()
  }]);
}();
window.UserProvider = new UserProvider();

/***/ }),

/***/ "./resources/js/render-error.js":
/*!**************************************!*\
  !*** ./resources/js/render-error.js ***!
  \**************************************/
/***/ (() => {

function renderInputErrors_Global() {
  var errors = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var isClear = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  if (isClear) {
    // Clear previous errors first
    document.querySelectorAll('label[name$="_LABEL"]').forEach(function (el) {
      el.classList.remove('text-red-500');
      el.classList.add('text-blue-700', 'dark:text-gray-300');
    });
    document.querySelectorAll('input.border-red-500').forEach(function (el) {
      el.classList.remove('border-red-500');
    });
    document.querySelectorAll('span[id$="_MSG"]').forEach(function (el) {
      el.classList.add('hidden', 'text-gray-500', 'dark:text-gray-400');
      el.classList.remove('text-red-500');
      el.textContent = '';
    });
  }
  for (var field in errors) {
    var labelElement = document.getElementsByName(field + '_LABEL')[0];
    var inputElement = document.getElementById(field);
    var msgElement = document.getElementById(field + '_MSG');
    if (labelElement) {
      labelElement.classList.remove('text-blue-700', 'dark:text-gray-300');
      labelElement.classList.add('text-red-500');
    }
    if (inputElement) {
      inputElement.classList.add('border-red-500');
    }
    if (msgElement) {
      msgElement.classList.remove('hidden', 'text-gray-500', 'dark:text-gray-400');
      msgElement.classList.add('text-red-500');
      msgElement.textContent = errors[field][0];
    }
  }
}

// expose to Blade inline scripts
window.renderInputErrors_Global = renderInputErrors_Global;

/***/ }),

/***/ "./resources/js/util.js":
/*!******************************!*\
  !*** ./resources/js/util.js ***!
  \******************************/
/***/ (() => {

function formatSecondsToHHMMSS_Global(totalSeconds) {
  var displayUnit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var hours = Math.floor(totalSeconds / 3600);
  var minutes = Math.floor(totalSeconds % 3600 / 60);
  var seconds = totalSeconds % 60;
  var paddedHours = String(hours).padStart(2, '0');
  var paddedMinutes = String(minutes).padStart(2, '0');
  var paddedSeconds = String(seconds).padStart(2, '0');
  if (displayUnit) {
    return "".concat(paddedHours, " gi\u1EDD ").concat(paddedMinutes, " ph\xFAt ").concat(paddedSeconds, " gi\xE2y");
  }
  return "".concat(paddedHours, ":").concat(paddedMinutes, ":").concat(paddedSeconds);
}
function removeVietnameseAccentsInString_Global(str) {
  str = str.toLowerCase();
  str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
  str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
  str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
  str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
  str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
  str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
  str = str.replace(/đ/g, "d");

  // Loại bỏ các ký tự đặc biệt khác nếu cần
  // str = str.replace(/[^0-9a-z ]/g, "");

  return str;
}
function escapeHtml_Global(str) {
  return String(str).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}

// ===== Timezone Utilities =====
// Tự động nhận biết timezone của client
function getClientTimezone_Global() {
  try {
    return Intl.DateTimeFormat().resolvedOptions().timeZone;
  } catch (e) {
    // Fallback: tính toán offset
    var offset = -new Date().getTimezoneOffset();
    var hours = Math.floor(Math.abs(offset) / 60);
    var minutes = Math.abs(offset) % 60;
    var sign = offset >= 0 ? '+' : '-';
    return "UTC".concat(sign).concat(String(hours).padStart(2, '0'), ":").concat(String(minutes).padStart(2, '0'));
  }
}

// Chuyển đổi UTC datetime từ Laravel sang local time format cho datetime-local input
// Tự động sử dụng timezone của client
function formatDateTimeLocal(utcDateTimeString) {
  if (!utcDateTimeString) return '';

  // Parse UTC datetime từ Laravel (ISO 8601 với Z hoặc +00:00)
  var date = new Date(utcDateTimeString);
  if (isNaN(date.getTime())) return '';

  // JavaScript Date tự động chuyển UTC sang local time của client
  // Format: YYYY-MM-DDTHH:mm (local time, không có timezone)
  var year = date.getFullYear();
  var month = String(date.getMonth() + 1).padStart(2, '0');
  var day = String(date.getDate()).padStart(2, '0');
  var hours = String(date.getHours()).padStart(2, '0');
  var minutes = String(date.getMinutes()).padStart(2, '0');
  return "".concat(year, "-").concat(month, "-").concat(day, "T").concat(hours, ":").concat(minutes);
}

// Format date - hiển thị theo timezone của client
// Laravel lưu và trả về datetime ở UTC, JavaScript tự động chuyển sang local time
function formatDate_Global(dateString) {
  if (!dateString) return '-';
  var date = new Date(dateString);
  if (isNaN(date.getTime())) return '-';

  // Laravel trả về datetime ở UTC (có timezone Z hoặc +00:00)
  // JavaScript Date tự động parse và chuyển đổi sang local time của client
  // Sử dụng local time methods để hiển thị đúng theo timezone của client
  var year = date.getFullYear();
  var month = String(date.getMonth() + 1).padStart(2, '0');
  var day = String(date.getDate()).padStart(2, '0');
  var hours = String(date.getHours()).padStart(2, '0');
  var minutes = String(date.getMinutes()).padStart(2, '0');
  return "".concat(hours, ":").concat(minutes, " ").concat(day, "/").concat(month, "/").concat(year);
}

// expose to Blade inline scripts
window.formatSecondsToHHMMSS_Global = formatSecondsToHHMMSS_Global;
window.removeVietnameseAccentsInString_Global = removeVietnameseAccentsInString_Global;
window.escapeHtml_Global = escapeHtml_Global;
window.getClientTimezone_Global = getClientTimezone_Global;
window.formatDateTimeLocal = formatDateTimeLocal;
window.formatDate_Global = formatDate_Global;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/js/app": 0,
/******/ 			"css/table": 0,
/******/ 			"css/loading-in-btn": 0,
/******/ 			"css/loading": 0,
/******/ 			"css/layout": 0,
/******/ 			"css/app": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunk"] = self["webpackChunk"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["css/table","css/loading-in-btn","css/loading","css/layout","css/app"], () => (__webpack_require__("./resources/js/app.js")))
/******/ 	__webpack_require__.O(undefined, ["css/table","css/loading-in-btn","css/loading","css/layout","css/app"], () => (__webpack_require__("./resources/css/app.css")))
/******/ 	__webpack_require__.O(undefined, ["css/table","css/loading-in-btn","css/loading","css/layout","css/app"], () => (__webpack_require__("./resources/css/layout.css")))
/******/ 	__webpack_require__.O(undefined, ["css/table","css/loading-in-btn","css/loading","css/layout","css/app"], () => (__webpack_require__("./resources/css/loading.css")))
/******/ 	__webpack_require__.O(undefined, ["css/table","css/loading-in-btn","css/loading","css/layout","css/app"], () => (__webpack_require__("./resources/css/loading-in-btn.css")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["css/table","css/loading-in-btn","css/loading","css/layout","css/app"], () => (__webpack_require__("./resources/css/table.css")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;