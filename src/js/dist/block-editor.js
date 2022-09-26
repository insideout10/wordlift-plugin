/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/block-editor/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "../../src/images/svg/wl-author-icon.svg":
/*!***********************************************************************!*\
  !*** /var/www/html/wordlift-plugin/src/images/svg/wl-author-icon.svg ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var React = __webpack_require__(/*! react */ "react");

function WlAuthorIcon (props) {
    return React.createElement("svg",props,[React.createElement("defs",{"key":0},[React.createElement("style",{"key":0},"\n      .a {\n        clip-path: url(\"#b\");\n      }\n      .b {\n        fill: #fff;\n      }\n      .b {\n        stroke: #007AFF;\n        stroke-width: 1px;\n      }\n    "),React.createElement("clipPath",{"id":"b","key":1},React.createElement("rect",{"x":"494","y":"4373","width":"19","height":"21"}))]),React.createElement("g",{"id":"a","className":"a","key":1},React.createElement("g",{"transform":"translate(-181.255 2596)"},[React.createElement("path",{"className":"b","d":"M17.609,6.012H3.12C3.645,2.191,6.286,0,10.365,0s6.72,2.191,7.245,6.011h0Z","transform":"translate(674.135 1789.488)","key":0}),React.createElement("path",{"className":"b","d":"M3.5.5a3,3,0,0,1,3,3c0,1.657-1.343,4.209-3,4.209S.5,5.157.5,3.5A3,3,0,0,1,3.5.5Z","transform":"translate(681 1780)","key":1})]))]);
}

WlAuthorIcon.defaultProps = {"viewBox":"494 4373 19 21"};

module.exports = WlAuthorIcon;

WlAuthorIcon.default = WlAuthorIcon;


/***/ }),

/***/ "../../src/images/svg/wl-calendar-icon.svg":
/*!*************************************************************************!*\
  !*** /var/www/html/wordlift-plugin/src/images/svg/wl-calendar-icon.svg ***!
  \*************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var React = __webpack_require__(/*! react */ "react");

function WlCalendarIcon (props) {
    return React.createElement("svg",props,[React.createElement("defs",{"key":0},[React.createElement("style",{"key":0},"\n      .a {\n        clip-path: url(\"#b\");\n      }\n      .b {\n        fill: #fff;\n      }\n      .b,\n      .c {\n        stroke: #007AFF;\n        stroke-width: 1px;\n      }\n      .c,\n      .f {\n        fill: none;\n      }\n      .e {\n        stroke: none;\n      }\n    "),React.createElement("clipPath",{"id":"b","key":1},React.createElement("rect",{"x":"494","y":"4402","width":"19","height":"21"}))]),React.createElement("g",{"id":"a","className":"a","key":1},React.createElement("g",{"transform":"translate(0 1)"},[React.createElement("g",{"className":"b","transform":"translate(496 4404.918)","key":0},[React.createElement("rect",{"className":"e","width":"14.691","height":"11.937","key":0}),React.createElement("rect",{"className":"f","x":"0.5","y":"0.5","width":"13.691","height":"10.937","key":1})]),React.createElement("g",{"className":"b","transform":"translate(498.755 4404)","key":1},[React.createElement("rect",{"className":"e","width":"2.755","height":"3.673","key":0}),React.createElement("rect",{"className":"f","x":"0.5","y":"0.5","width":"1.755","height":"2.673","key":1})]),React.createElement("g",{"className":"b","transform":"translate(505.182 4404)","key":2},[React.createElement("rect",{"className":"e","width":"2.755","height":"3.673","key":0}),React.createElement("rect",{"className":"f","x":"0.5","y":"0.5","width":"1.755","height":"2.673","key":1})]),React.createElement("line",{"className":"c","x2":"13.773","transform":"translate(496.459 4409.05)","key":3})]))]);
}

WlCalendarIcon.defaultProps = {"viewBox":"494 4402 19 21"};

module.exports = WlCalendarIcon;

WlCalendarIcon.default = WlCalendarIcon;


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _assertThisInitialized; });
function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _defineProperty; });
function _defineProperty(obj, key, value) {
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

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/extends.js":
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/extends.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _extends; });
function _extends() {
  _extends = Object.assign ? Object.assign.bind() : function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };
  return _extends.apply(this, arguments);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _inheritsLoose; });
/* harmony import */ var _setPrototypeOf_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./setPrototypeOf.js */ "./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js");

function _inheritsLoose(subClass, superClass) {
  subClass.prototype = Object.create(superClass.prototype);
  subClass.prototype.constructor = subClass;
  Object(_setPrototypeOf_js__WEBPACK_IMPORTED_MODULE_0__["default"])(subClass, superClass);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectSpread2.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _objectSpread2; });
/* harmony import */ var _defineProperty_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./defineProperty.js */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");


function ownKeys(object, enumerableOnly) {
  var keys = Object.keys(object);

  if (Object.getOwnPropertySymbols) {
    var symbols = Object.getOwnPropertySymbols(object);
    enumerableOnly && (symbols = symbols.filter(function (sym) {
      return Object.getOwnPropertyDescriptor(object, sym).enumerable;
    })), keys.push.apply(keys, symbols);
  }

  return keys;
}

function _objectSpread2(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = null != arguments[i] ? arguments[i] : {};
    i % 2 ? ownKeys(Object(source), !0).forEach(function (key) {
      Object(_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__["default"])(target, key, source[key]);
    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) {
      Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
    });
  }

  return target;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _objectWithoutPropertiesLoose; });
function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _setPrototypeOf; });
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };
  return _setPrototypeOf(o, p);
}

/***/ }),

/***/ "./node_modules/@emotion/is-prop-valid/dist/is-prop-valid.browser.esm.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@emotion/is-prop-valid/dist/is-prop-valid.browser.esm.js ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _emotion_memoize__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @emotion/memoize */ "./node_modules/@emotion/memoize/dist/memoize.browser.esm.js");


var reactPropsRegex = /^((children|dangerouslySetInnerHTML|key|ref|autoFocus|defaultValue|defaultChecked|innerHTML|suppressContentEditableWarning|suppressHydrationWarning|valueLink|accept|acceptCharset|accessKey|action|allow|allowUserMedia|allowPaymentRequest|allowFullScreen|allowTransparency|alt|async|autoComplete|autoPlay|capture|cellPadding|cellSpacing|challenge|charSet|checked|cite|classID|className|cols|colSpan|content|contentEditable|contextMenu|controls|controlsList|coords|crossOrigin|data|dateTime|decoding|default|defer|dir|disabled|disablePictureInPicture|download|draggable|encType|form|formAction|formEncType|formMethod|formNoValidate|formTarget|frameBorder|headers|height|hidden|high|href|hrefLang|htmlFor|httpEquiv|id|inputMode|integrity|is|keyParams|keyType|kind|label|lang|list|loading|loop|low|marginHeight|marginWidth|max|maxLength|media|mediaGroup|method|min|minLength|multiple|muted|name|nonce|noValidate|open|optimum|pattern|placeholder|playsInline|poster|preload|profile|radioGroup|readOnly|referrerPolicy|rel|required|reversed|role|rows|rowSpan|sandbox|scope|scoped|scrolling|seamless|selected|shape|size|sizes|slot|span|spellCheck|src|srcDoc|srcLang|srcSet|start|step|style|summary|tabIndex|target|title|type|useMap|value|width|wmode|wrap|about|datatype|inlist|prefix|property|resource|typeof|vocab|autoCapitalize|autoCorrect|autoSave|color|inert|itemProp|itemScope|itemType|itemID|itemRef|on|results|security|unselectable|accentHeight|accumulate|additive|alignmentBaseline|allowReorder|alphabetic|amplitude|arabicForm|ascent|attributeName|attributeType|autoReverse|azimuth|baseFrequency|baselineShift|baseProfile|bbox|begin|bias|by|calcMode|capHeight|clip|clipPathUnits|clipPath|clipRule|colorInterpolation|colorInterpolationFilters|colorProfile|colorRendering|contentScriptType|contentStyleType|cursor|cx|cy|d|decelerate|descent|diffuseConstant|direction|display|divisor|dominantBaseline|dur|dx|dy|edgeMode|elevation|enableBackground|end|exponent|externalResourcesRequired|fill|fillOpacity|fillRule|filter|filterRes|filterUnits|floodColor|floodOpacity|focusable|fontFamily|fontSize|fontSizeAdjust|fontStretch|fontStyle|fontVariant|fontWeight|format|from|fr|fx|fy|g1|g2|glyphName|glyphOrientationHorizontal|glyphOrientationVertical|glyphRef|gradientTransform|gradientUnits|hanging|horizAdvX|horizOriginX|ideographic|imageRendering|in|in2|intercept|k|k1|k2|k3|k4|kernelMatrix|kernelUnitLength|kerning|keyPoints|keySplines|keyTimes|lengthAdjust|letterSpacing|lightingColor|limitingConeAngle|local|markerEnd|markerMid|markerStart|markerHeight|markerUnits|markerWidth|mask|maskContentUnits|maskUnits|mathematical|mode|numOctaves|offset|opacity|operator|order|orient|orientation|origin|overflow|overlinePosition|overlineThickness|panose1|paintOrder|pathLength|patternContentUnits|patternTransform|patternUnits|pointerEvents|points|pointsAtX|pointsAtY|pointsAtZ|preserveAlpha|preserveAspectRatio|primitiveUnits|r|radius|refX|refY|renderingIntent|repeatCount|repeatDur|requiredExtensions|requiredFeatures|restart|result|rotate|rx|ry|scale|seed|shapeRendering|slope|spacing|specularConstant|specularExponent|speed|spreadMethod|startOffset|stdDeviation|stemh|stemv|stitchTiles|stopColor|stopOpacity|strikethroughPosition|strikethroughThickness|string|stroke|strokeDasharray|strokeDashoffset|strokeLinecap|strokeLinejoin|strokeMiterlimit|strokeOpacity|strokeWidth|surfaceScale|systemLanguage|tableValues|targetX|targetY|textAnchor|textDecoration|textRendering|textLength|to|transform|u1|u2|underlinePosition|underlineThickness|unicode|unicodeBidi|unicodeRange|unitsPerEm|vAlphabetic|vHanging|vIdeographic|vMathematical|values|vectorEffect|version|vertAdvY|vertOriginX|vertOriginY|viewBox|viewTarget|visibility|widths|wordSpacing|writingMode|x|xHeight|x1|x2|xChannelSelector|xlinkActuate|xlinkArcrole|xlinkHref|xlinkRole|xlinkShow|xlinkTitle|xlinkType|xmlBase|xmlns|xmlnsXlink|xmlLang|xmlSpace|y|y1|y2|yChannelSelector|z|zoomAndPan|for|class|autofocus)|(([Dd][Aa][Tt][Aa]|[Aa][Rr][Ii][Aa]|x)-.*))$/; // https://esbench.com/bench/5bfee68a4cd7e6009ef61d23

var index = Object(_emotion_memoize__WEBPACK_IMPORTED_MODULE_0__["default"])(function (prop) {
  return reactPropsRegex.test(prop) || prop.charCodeAt(0) === 111
  /* o */
  && prop.charCodeAt(1) === 110
  /* n */
  && prop.charCodeAt(2) < 91;
}
/* Z+1 */
);

/* harmony default export */ __webpack_exports__["default"] = (index);


/***/ }),

/***/ "./node_modules/@emotion/memoize/dist/memoize.browser.esm.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@emotion/memoize/dist/memoize.browser.esm.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
function memoize(fn) {
  var cache = {};
  return function (arg) {
    if (cache[arg] === undefined) cache[arg] = fn(arg);
    return cache[arg];
  };
}

/* harmony default export */ __webpack_exports__["default"] = (memoize);


/***/ }),

/***/ "./node_modules/@emotion/unitless/dist/unitless.browser.esm.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@emotion/unitless/dist/unitless.browser.esm.js ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var unitlessKeys = {
  animationIterationCount: 1,
  borderImageOutset: 1,
  borderImageSlice: 1,
  borderImageWidth: 1,
  boxFlex: 1,
  boxFlexGroup: 1,
  boxOrdinalGroup: 1,
  columnCount: 1,
  columns: 1,
  flex: 1,
  flexGrow: 1,
  flexPositive: 1,
  flexShrink: 1,
  flexNegative: 1,
  flexOrder: 1,
  gridRow: 1,
  gridRowEnd: 1,
  gridRowSpan: 1,
  gridRowStart: 1,
  gridColumn: 1,
  gridColumnEnd: 1,
  gridColumnSpan: 1,
  gridColumnStart: 1,
  msGridRow: 1,
  msGridRowSpan: 1,
  msGridColumn: 1,
  msGridColumnSpan: 1,
  fontWeight: 1,
  lineHeight: 1,
  opacity: 1,
  order: 1,
  orphans: 1,
  tabSize: 1,
  widows: 1,
  zIndex: 1,
  zoom: 1,
  WebkitLineClamp: 1,
  // SVG-related properties
  fillOpacity: 1,
  floodOpacity: 1,
  stopOpacity: 1,
  strokeDasharray: 1,
  strokeDashoffset: 1,
  strokeMiterlimit: 1,
  strokeOpacity: 1,
  strokeWidth: 1
};

/* harmony default export */ __webpack_exports__["default"] = (unitlessKeys);


/***/ }),

/***/ "./node_modules/@redux-saga/core/dist/io-40341e1a.js":
/*!***********************************************************!*\
  !*** ./node_modules/@redux-saga/core/dist/io-40341e1a.js ***!
  \***********************************************************/
/*! exports provided: $, A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z, _, a, a0, a1, a2, a3, a4, a5, a6, a7, b, c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t, u, v, w, x, y, z */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "$", function() { return apply; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "A", function() { return ALL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "B", function() { return logError; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "C", function() { return CALL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "D", function() { return wrapSagaDispatch; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "E", function() { return identity; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "F", function() { return FORK; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "G", function() { return GET_CONTEXT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "H", function() { return buffers; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "I", function() { return detach; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "J", function() { return JOIN; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "K", function() { return take; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "L", function() { return fork; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "M", function() { return cancel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "N", function() { return call; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "O", function() { return actionChannel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "P", function() { return PUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Q", function() { return sliding; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "R", function() { return RACE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "S", function() { return SELECT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "T", function() { return TAKE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "U", function() { return delay; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "V", function() { return race; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "W", function() { return effectTypes; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "X", function() { return takeMaybe; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Y", function() { return put; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Z", function() { return putResolve; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "_", function() { return all; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return CPS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a0", function() { return cps; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a1", function() { return spawn; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a2", function() { return join; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a3", function() { return select; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a4", function() { return cancelled; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a5", function() { return flush; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a6", function() { return getContext; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a7", function() { return setContext; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return CANCEL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return check; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return ACTION_CHANNEL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "e", function() { return expanding; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "f", function() { return CANCELLED; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "g", function() { return FLUSH; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "h", function() { return SET_CONTEXT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "i", function() { return internalErr; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "j", function() { return getMetaInfo; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "k", function() { return kTrue; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "l", function() { return createAllStyleChildCallbacks; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "m", function() { return createEmptyArray; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "n", function() { return none; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "o", function() { return once; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "p", function() { return assignWithSymbols; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "q", function() { return makeIterator; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "r", function() { return remove; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "s", function() { return shouldComplete; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "t", function() { return noop; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "u", function() { return flatMap; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "v", function() { return getLocation; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "w", function() { return createSetContextWarning; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "x", function() { return asyncIteratorSymbol; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "y", function() { return shouldCancel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "z", function() { return shouldTerminate; });
/* harmony import */ var _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @redux-saga/symbols */ "./node_modules/@redux-saga/symbols/dist/redux-saga-symbols.esm.js");
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @redux-saga/is */ "./node_modules/@redux-saga/is/dist/redux-saga-is.esm.js");
/* harmony import */ var _redux_saga_delay_p__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @redux-saga/delay-p */ "./node_modules/@redux-saga/delay-p/dist/redux-saga-delay-p.esm.js");





var konst = function konst(v) {
  return function () {
    return v;
  };
};
var kTrue =
/*#__PURE__*/
konst(true);

var noop = function noop() {};

if ( true && typeof Proxy !== 'undefined') {
  noop =
  /*#__PURE__*/
  new Proxy(noop, {
    set: function set() {
      throw internalErr('There was an attempt to assign a property to internal `noop` function.');
    }
  });
}
var identity = function identity(v) {
  return v;
};
var hasSymbol = typeof Symbol === 'function';
var asyncIteratorSymbol = hasSymbol && Symbol.asyncIterator ? Symbol.asyncIterator : '@@asyncIterator';
function check(value, predicate, error) {
  if (!predicate(value)) {
    throw new Error(error);
  }
}
var assignWithSymbols = function assignWithSymbols(target, source) {
  Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__["default"])(target, source);

  if (Object.getOwnPropertySymbols) {
    Object.getOwnPropertySymbols(source).forEach(function (s) {
      target[s] = source[s];
    });
  }
};
var flatMap = function flatMap(mapper, arr) {
  var _ref;

  return (_ref = []).concat.apply(_ref, arr.map(mapper));
};
function remove(array, item) {
  var index = array.indexOf(item);

  if (index >= 0) {
    array.splice(index, 1);
  }
}
function once(fn) {
  var called = false;
  return function () {
    if (called) {
      return;
    }

    called = true;
    fn();
  };
}

var kThrow = function kThrow(err) {
  throw err;
};

var kReturn = function kReturn(value) {
  return {
    value: value,
    done: true
  };
};

function makeIterator(next, thro, name) {
  if (thro === void 0) {
    thro = kThrow;
  }

  if (name === void 0) {
    name = 'iterator';
  }

  var iterator = {
    meta: {
      name: name
    },
    next: next,
    throw: thro,
    return: kReturn,
    isSagaIterator: true
  };

  if (typeof Symbol !== 'undefined') {
    iterator[Symbol.iterator] = function () {
      return iterator;
    };
  }

  return iterator;
}
function logError(error, _ref2) {
  var sagaStack = _ref2.sagaStack;

  /*eslint-disable no-console*/
  console.error(error);
  console.error(sagaStack);
}
var internalErr = function internalErr(err) {
  return new Error("\n  redux-saga: Error checking hooks detected an inconsistent state. This is likely a bug\n  in redux-saga code and not yours. Thanks for reporting this in the project's github repo.\n  Error: " + err + "\n");
};
var createSetContextWarning = function createSetContextWarning(ctx, props) {
  return (ctx ? ctx + '.' : '') + "setContext(props): argument " + props + " is not a plain object";
};
var FROZEN_ACTION_ERROR = "You can't put (a.k.a. dispatch from saga) frozen actions.\nWe have to define a special non-enumerable property on those actions for scheduling purposes.\nOtherwise you wouldn't be able to communicate properly between sagas & other subscribers (action ordering would become far less predictable).\nIf you are using redux and you care about this behaviour (frozen actions),\nthen you might want to switch to freezing actions in a middleware rather than in action creator.\nExample implementation:\n\nconst freezeActions = store => next => action => next(Object.freeze(action))\n"; // creates empty, but not-holey array

var createEmptyArray = function createEmptyArray(n) {
  return Array.apply(null, new Array(n));
};
var wrapSagaDispatch = function wrapSagaDispatch(dispatch) {
  return function (action) {
    if (true) {
      check(action, function (ac) {
        return !Object.isFrozen(ac);
      }, FROZEN_ACTION_ERROR);
    }

    return dispatch(Object.defineProperty(action, _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["SAGA_ACTION"], {
      value: true
    }));
  };
};
var shouldTerminate = function shouldTerminate(res) {
  return res === _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["TERMINATE"];
};
var shouldCancel = function shouldCancel(res) {
  return res === _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["TASK_CANCEL"];
};
var shouldComplete = function shouldComplete(res) {
  return shouldTerminate(res) || shouldCancel(res);
};
function createAllStyleChildCallbacks(shape, parentCallback) {
  var keys = Object.keys(shape);
  var totalCount = keys.length;

  if (true) {
    check(totalCount, function (c) {
      return c > 0;
    }, 'createAllStyleChildCallbacks: get an empty array or object');
  }

  var completedCount = 0;
  var completed;
  var results = Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["array"])(shape) ? createEmptyArray(totalCount) : {};
  var childCallbacks = {};

  function checkEnd() {
    if (completedCount === totalCount) {
      completed = true;
      parentCallback(results);
    }
  }

  keys.forEach(function (key) {
    var chCbAtKey = function chCbAtKey(res, isErr) {
      if (completed) {
        return;
      }

      if (isErr || shouldComplete(res)) {
        parentCallback.cancel();
        parentCallback(res, isErr);
      } else {
        results[key] = res;
        completedCount++;
        checkEnd();
      }
    };

    chCbAtKey.cancel = noop;
    childCallbacks[key] = chCbAtKey;
  });

  parentCallback.cancel = function () {
    if (!completed) {
      completed = true;
      keys.forEach(function (key) {
        return childCallbacks[key].cancel();
      });
    }
  };

  return childCallbacks;
}
function getMetaInfo(fn) {
  return {
    name: fn.name || 'anonymous',
    location: getLocation(fn)
  };
}
function getLocation(instrumented) {
  return instrumented[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["SAGA_LOCATION"]];
}

var BUFFER_OVERFLOW = "Channel's Buffer overflow!";
var ON_OVERFLOW_THROW = 1;
var ON_OVERFLOW_DROP = 2;
var ON_OVERFLOW_SLIDE = 3;
var ON_OVERFLOW_EXPAND = 4;
var zeroBuffer = {
  isEmpty: kTrue,
  put: noop,
  take: noop
};

function ringBuffer(limit, overflowAction) {
  if (limit === void 0) {
    limit = 10;
  }

  var arr = new Array(limit);
  var length = 0;
  var pushIndex = 0;
  var popIndex = 0;

  var push = function push(it) {
    arr[pushIndex] = it;
    pushIndex = (pushIndex + 1) % limit;
    length++;
  };

  var take = function take() {
    if (length != 0) {
      var it = arr[popIndex];
      arr[popIndex] = null;
      length--;
      popIndex = (popIndex + 1) % limit;
      return it;
    }
  };

  var flush = function flush() {
    var items = [];

    while (length) {
      items.push(take());
    }

    return items;
  };

  return {
    isEmpty: function isEmpty() {
      return length == 0;
    },
    put: function put(it) {
      if (length < limit) {
        push(it);
      } else {
        var doubledLimit;

        switch (overflowAction) {
          case ON_OVERFLOW_THROW:
            throw new Error(BUFFER_OVERFLOW);

          case ON_OVERFLOW_SLIDE:
            arr[pushIndex] = it;
            pushIndex = (pushIndex + 1) % limit;
            popIndex = pushIndex;
            break;

          case ON_OVERFLOW_EXPAND:
            doubledLimit = 2 * limit;
            arr = flush();
            length = arr.length;
            pushIndex = arr.length;
            popIndex = 0;
            arr.length = doubledLimit;
            limit = doubledLimit;
            push(it);
            break;

          default: // DROP

        }
      }
    },
    take: take,
    flush: flush
  };
}

var none = function none() {
  return zeroBuffer;
};
var fixed = function fixed(limit) {
  return ringBuffer(limit, ON_OVERFLOW_THROW);
};
var dropping = function dropping(limit) {
  return ringBuffer(limit, ON_OVERFLOW_DROP);
};
var sliding = function sliding(limit) {
  return ringBuffer(limit, ON_OVERFLOW_SLIDE);
};
var expanding = function expanding(initialSize) {
  return ringBuffer(initialSize, ON_OVERFLOW_EXPAND);
};

var buffers = /*#__PURE__*/Object.freeze({
  __proto__: null,
  none: none,
  fixed: fixed,
  dropping: dropping,
  sliding: sliding,
  expanding: expanding
});

var TAKE = 'TAKE';
var PUT = 'PUT';
var ALL = 'ALL';
var RACE = 'RACE';
var CALL = 'CALL';
var CPS = 'CPS';
var FORK = 'FORK';
var JOIN = 'JOIN';
var CANCEL = 'CANCEL';
var SELECT = 'SELECT';
var ACTION_CHANNEL = 'ACTION_CHANNEL';
var CANCELLED = 'CANCELLED';
var FLUSH = 'FLUSH';
var GET_CONTEXT = 'GET_CONTEXT';
var SET_CONTEXT = 'SET_CONTEXT';

var effectTypes = /*#__PURE__*/Object.freeze({
  __proto__: null,
  TAKE: TAKE,
  PUT: PUT,
  ALL: ALL,
  RACE: RACE,
  CALL: CALL,
  CPS: CPS,
  FORK: FORK,
  JOIN: JOIN,
  CANCEL: CANCEL,
  SELECT: SELECT,
  ACTION_CHANNEL: ACTION_CHANNEL,
  CANCELLED: CANCELLED,
  FLUSH: FLUSH,
  GET_CONTEXT: GET_CONTEXT,
  SET_CONTEXT: SET_CONTEXT
});

var TEST_HINT = '\n(HINT: if you are getting these errors in tests, consider using createMockTask from @redux-saga/testing-utils)';

var makeEffect = function makeEffect(type, payload) {
  var _ref;

  return _ref = {}, _ref[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["IO"]] = true, _ref.combinator = false, _ref.type = type, _ref.payload = payload, _ref;
};

var isForkEffect = function isForkEffect(eff) {
  return Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["effect"])(eff) && eff.type === FORK;
};

var detach = function detach(eff) {
  if (true) {
    check(eff, isForkEffect, 'detach(eff): argument must be a fork effect');
  }

  return makeEffect(FORK, Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__["default"])({}, eff.payload, {
    detached: true
  }));
};
function take(patternOrChannel, multicastPattern) {
  if (patternOrChannel === void 0) {
    patternOrChannel = '*';
  }

  if ( true && arguments.length) {
    check(arguments[0], _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], 'take(patternOrChannel): patternOrChannel is undefined');
  }

  if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["pattern"])(patternOrChannel)) {
    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"])(multicastPattern)) {
      console.warn("take(pattern) takes one argument but two were provided. Consider passing an array for listening to several action types");
    }

    return makeEffect(TAKE, {
      pattern: patternOrChannel
    });
  }

  if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["multicast"])(patternOrChannel) && Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"])(multicastPattern) && Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["pattern"])(multicastPattern)) {
    return makeEffect(TAKE, {
      channel: patternOrChannel,
      pattern: multicastPattern
    });
  }

  if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["channel"])(patternOrChannel)) {
    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"])(multicastPattern)) {
      console.warn("take(channel) takes one argument but two were provided. Second argument is ignored.");
    }

    return makeEffect(TAKE, {
      channel: patternOrChannel
    });
  }

  if (true) {
    throw new Error("take(patternOrChannel): argument " + patternOrChannel + " is not valid channel or a valid pattern");
  }
}
var takeMaybe = function takeMaybe() {
  var eff = take.apply(void 0, arguments);
  eff.payload.maybe = true;
  return eff;
};
function put(channel$1, action) {
  if (true) {
    if (arguments.length > 1) {
      check(channel$1, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], 'put(channel, action): argument channel is undefined');
      check(channel$1, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["channel"], "put(channel, action): argument " + channel$1 + " is not a valid channel");
      check(action, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], 'put(channel, action): argument action is undefined');
    } else {
      check(channel$1, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], 'put(action): argument action is undefined');
    }
  }

  if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["undef"])(action)) {
    action = channel$1; // `undefined` instead of `null` to make default parameter work

    channel$1 = undefined;
  }

  return makeEffect(PUT, {
    channel: channel$1,
    action: action
  });
}
var putResolve = function putResolve() {
  var eff = put.apply(void 0, arguments);
  eff.payload.resolve = true;
  return eff;
};
function all(effects) {
  var eff = makeEffect(ALL, effects);
  eff.combinator = true;
  return eff;
}
function race(effects) {
  var eff = makeEffect(RACE, effects);
  eff.combinator = true;
  return eff;
} // this match getFnCallDescriptor logic

var validateFnDescriptor = function validateFnDescriptor(effectName, fnDescriptor) {
  check(fnDescriptor, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], effectName + ": argument fn is undefined or null");

  if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["func"])(fnDescriptor)) {
    return;
  }

  var context = null;
  var fn;

  if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["array"])(fnDescriptor)) {
    context = fnDescriptor[0];
    fn = fnDescriptor[1];
    check(fn, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], effectName + ": argument of type [context, fn] has undefined or null `fn`");
  } else if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["object"])(fnDescriptor)) {
    context = fnDescriptor.context;
    fn = fnDescriptor.fn;
    check(fn, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], effectName + ": argument of type {context, fn} has undefined or null `fn`");
  } else {
    check(fnDescriptor, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["func"], effectName + ": argument fn is not function");
    return;
  }

  if (context && Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["string"])(fn)) {
    check(context[fn], _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["func"], effectName + ": context arguments has no such method - \"" + fn + "\"");
    return;
  }

  check(fn, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["func"], effectName + ": unpacked fn argument (from [context, fn] or {context, fn}) is not a function");
};

function getFnCallDescriptor(fnDescriptor, args) {
  var context = null;
  var fn;

  if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["func"])(fnDescriptor)) {
    fn = fnDescriptor;
  } else {
    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["array"])(fnDescriptor)) {
      context = fnDescriptor[0];
      fn = fnDescriptor[1];
    } else {
      context = fnDescriptor.context;
      fn = fnDescriptor.fn;
    }

    if (context && Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["string"])(fn) && Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["func"])(context[fn])) {
      fn = context[fn];
    }
  }

  return {
    context: context,
    fn: fn,
    args: args
  };
}

var isNotDelayEffect = function isNotDelayEffect(fn) {
  return fn !== delay;
};

function call(fnDescriptor) {
  for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    args[_key - 1] = arguments[_key];
  }

  if (true) {
    var arg0 = typeof args[0] === 'number' ? args[0] : 'ms';
    check(fnDescriptor, isNotDelayEffect, "instead of writing `yield call(delay, " + arg0 + ")` where delay is an effect from `redux-saga/effects` you should write `yield delay(" + arg0 + ")`");
    validateFnDescriptor('call', fnDescriptor);
  }

  return makeEffect(CALL, getFnCallDescriptor(fnDescriptor, args));
}
function apply(context, fn, args) {
  if (args === void 0) {
    args = [];
  }

  var fnDescriptor = [context, fn];

  if (true) {
    validateFnDescriptor('apply', fnDescriptor);
  }

  return makeEffect(CALL, getFnCallDescriptor([context, fn], args));
}
function cps(fnDescriptor) {
  if (true) {
    validateFnDescriptor('cps', fnDescriptor);
  }

  for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
    args[_key2 - 1] = arguments[_key2];
  }

  return makeEffect(CPS, getFnCallDescriptor(fnDescriptor, args));
}
function fork(fnDescriptor) {
  if (true) {
    validateFnDescriptor('fork', fnDescriptor);
    check(fnDescriptor, function (arg) {
      return !Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["effect"])(arg);
    }, 'fork: argument must not be an effect');
  }

  for (var _len3 = arguments.length, args = new Array(_len3 > 1 ? _len3 - 1 : 0), _key3 = 1; _key3 < _len3; _key3++) {
    args[_key3 - 1] = arguments[_key3];
  }

  return makeEffect(FORK, getFnCallDescriptor(fnDescriptor, args));
}
function spawn(fnDescriptor) {
  if (true) {
    validateFnDescriptor('spawn', fnDescriptor);
  }

  for (var _len4 = arguments.length, args = new Array(_len4 > 1 ? _len4 - 1 : 0), _key4 = 1; _key4 < _len4; _key4++) {
    args[_key4 - 1] = arguments[_key4];
  }

  return detach(fork.apply(void 0, [fnDescriptor].concat(args)));
}
function join(taskOrTasks) {
  if (true) {
    if (arguments.length > 1) {
      throw new Error('join(...tasks) is not supported any more. Please use join([...tasks]) to join multiple tasks.');
    }

    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["array"])(taskOrTasks)) {
      taskOrTasks.forEach(function (t) {
        check(t, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["task"], "join([...tasks]): argument " + t + " is not a valid Task object " + TEST_HINT);
      });
    } else {
      check(taskOrTasks, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["task"], "join(task): argument " + taskOrTasks + " is not a valid Task object " + TEST_HINT);
    }
  }

  return makeEffect(JOIN, taskOrTasks);
}
function cancel(taskOrTasks) {
  if (taskOrTasks === void 0) {
    taskOrTasks = _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["SELF_CANCELLATION"];
  }

  if (true) {
    if (arguments.length > 1) {
      throw new Error('cancel(...tasks) is not supported any more. Please use cancel([...tasks]) to cancel multiple tasks.');
    }

    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["array"])(taskOrTasks)) {
      taskOrTasks.forEach(function (t) {
        check(t, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["task"], "cancel([...tasks]): argument " + t + " is not a valid Task object " + TEST_HINT);
      });
    } else if (taskOrTasks !== _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["SELF_CANCELLATION"] && Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"])(taskOrTasks)) {
      check(taskOrTasks, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["task"], "cancel(task): argument " + taskOrTasks + " is not a valid Task object " + TEST_HINT);
    }
  }

  return makeEffect(CANCEL, taskOrTasks);
}
function select(selector) {
  if (selector === void 0) {
    selector = identity;
  }

  for (var _len5 = arguments.length, args = new Array(_len5 > 1 ? _len5 - 1 : 0), _key5 = 1; _key5 < _len5; _key5++) {
    args[_key5 - 1] = arguments[_key5];
  }

  if ( true && arguments.length) {
    check(arguments[0], _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], 'select(selector, [...]): argument selector is undefined');
    check(selector, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["func"], "select(selector, [...]): argument " + selector + " is not a function");
  }

  return makeEffect(SELECT, {
    selector: selector,
    args: args
  });
}
/**
  channel(pattern, [buffer])    => creates a proxy channel for store actions
**/

function actionChannel(pattern$1, buffer$1) {
  if (true) {
    check(pattern$1, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["pattern"], 'actionChannel(pattern,...): argument pattern is not valid');

    if (arguments.length > 1) {
      check(buffer$1, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], 'actionChannel(pattern, buffer): argument buffer is undefined');
      check(buffer$1, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["buffer"], "actionChannel(pattern, buffer): argument " + buffer$1 + " is not a valid buffer");
    }
  }

  return makeEffect(ACTION_CHANNEL, {
    pattern: pattern$1,
    buffer: buffer$1
  });
}
function cancelled() {
  return makeEffect(CANCELLED, {});
}
function flush(channel$1) {
  if (true) {
    check(channel$1, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["channel"], "flush(channel): argument " + channel$1 + " is not valid channel");
  }

  return makeEffect(FLUSH, channel$1);
}
function getContext(prop) {
  if (true) {
    check(prop, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["string"], "getContext(prop): argument " + prop + " is not a string");
  }

  return makeEffect(GET_CONTEXT, prop);
}
function setContext(props) {
  if (true) {
    check(props, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["object"], createSetContextWarning(null, props));
  }

  return makeEffect(SET_CONTEXT, props);
}
var delay =
/*#__PURE__*/
call.bind(null, _redux_saga_delay_p__WEBPACK_IMPORTED_MODULE_3__["default"]);




/***/ }),

/***/ "./node_modules/@redux-saga/core/dist/redux-saga-core.esm.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@redux-saga/core/dist/redux-saga-core.esm.js ***!
  \*******************************************************************/
/*! exports provided: CANCEL, SAGA_LOCATION, buffers, detach, default, END, channel, eventChannel, isEnd, multicastChannel, runSaga, stdChannel */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "END", function() { return END; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "channel", function() { return channel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "eventChannel", function() { return eventChannel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isEnd", function() { return isEnd; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "multicastChannel", function() { return multicastChannel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "runSaga", function() { return runSaga; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "stdChannel", function() { return stdChannel; });
/* harmony import */ var _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @redux-saga/symbols */ "./node_modules/@redux-saga/symbols/dist/redux-saga-symbols.esm.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "CANCEL", function() { return _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["CANCEL"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "SAGA_LOCATION", function() { return _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["SAGA_LOCATION"]; });

/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutPropertiesLoose */ "./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js");
/* harmony import */ var _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @redux-saga/is */ "./node_modules/@redux-saga/is/dist/redux-saga-is.esm.js");
/* harmony import */ var _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./io-40341e1a.js */ "./node_modules/@redux-saga/core/dist/io-40341e1a.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "buffers", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["H"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "detach", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["I"]; });

/* harmony import */ var redux__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! redux */ "./node_modules/redux/es/redux.js");
/* harmony import */ var _redux_saga_deferred__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @redux-saga/deferred */ "./node_modules/@redux-saga/deferred/dist/redux-saga-deferred.esm.js");
/* harmony import */ var _redux_saga_delay_p__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @redux-saga/delay-p */ "./node_modules/@redux-saga/delay-p/dist/redux-saga-delay-p.esm.js");











var queue = [];
/**
  Variable to hold a counting semaphore
  - Incrementing adds a lock and puts the scheduler in a `suspended` state (if it's not
    already suspended)
  - Decrementing releases a lock. Zero locks puts the scheduler in a `released` state. This
    triggers flushing the queued tasks.
**/

var semaphore = 0;
/**
  Executes a task 'atomically'. Tasks scheduled during this execution will be queued
  and flushed after this task has finished (assuming the scheduler endup in a released
  state).
**/

function exec(task) {
  try {
    suspend();
    task();
  } finally {
    release();
  }
}
/**
  Executes or queues a task depending on the state of the scheduler (`suspended` or `released`)
**/


function asap(task) {
  queue.push(task);

  if (!semaphore) {
    suspend();
    flush();
  }
}
/**
 * Puts the scheduler in a `suspended` state and executes a task immediately.
 */

function immediately(task) {
  try {
    suspend();
    return task();
  } finally {
    flush();
  }
}
/**
  Puts the scheduler in a `suspended` state. Scheduled tasks will be queued until the
  scheduler is released.
**/

function suspend() {
  semaphore++;
}
/**
  Puts the scheduler in a `released` state.
**/


function release() {
  semaphore--;
}
/**
  Releases the current lock. Executes all queued tasks if the scheduler is in the released state.
**/


function flush() {
  release();
  var task;

  while (!semaphore && (task = queue.shift()) !== undefined) {
    exec(task);
  }
}

var array = function array(patterns) {
  return function (input) {
    return patterns.some(function (p) {
      return matcher(p)(input);
    });
  };
};
var predicate = function predicate(_predicate) {
  return function (input) {
    return _predicate(input);
  };
};
var string = function string(pattern) {
  return function (input) {
    return input.type === String(pattern);
  };
};
var symbol = function symbol(pattern) {
  return function (input) {
    return input.type === pattern;
  };
};
var wildcard = function wildcard() {
  return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["k"];
};
function matcher(pattern) {
  // prettier-ignore
  var matcherCreator = pattern === '*' ? wildcard : Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["string"])(pattern) ? string : Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["array"])(pattern) ? array : Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["stringableFunc"])(pattern) ? string : Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"])(pattern) ? predicate : Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["symbol"])(pattern) ? symbol : null;

  if (matcherCreator === null) {
    throw new Error("invalid pattern: " + pattern);
  }

  return matcherCreator(pattern);
}

var END = {
  type: _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["CHANNEL_END_TYPE"]
};
var isEnd = function isEnd(a) {
  return a && a.type === _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["CHANNEL_END_TYPE"];
};
var CLOSED_CHANNEL_WITH_TAKERS = 'Cannot have a closed channel with pending takers';
var INVALID_BUFFER = 'invalid buffer passed to channel factory function';
var UNDEFINED_INPUT_ERROR = "Saga or channel was provided with an undefined action\nHints:\n  - check that your Action Creator returns a non-undefined value\n  - if the Saga was started using runSaga, check that your subscribe source provides the action to its listeners";
function channel(buffer$1) {
  if (buffer$1 === void 0) {
    buffer$1 = Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["e"])();
  }

  var closed = false;
  var takers = [];

  if (true) {
    Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(buffer$1, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["buffer"], INVALID_BUFFER);
  }

  function checkForbiddenStates() {
    if (closed && takers.length) {
      throw Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["i"])(CLOSED_CHANNEL_WITH_TAKERS);
    }

    if (takers.length && !buffer$1.isEmpty()) {
      throw Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["i"])('Cannot have pending takers with non empty buffer');
    }
  }

  function put(input) {
    if (true) {
      checkForbiddenStates();
      Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(input, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["notUndef"], UNDEFINED_INPUT_ERROR);
    }

    if (closed) {
      return;
    }

    if (takers.length === 0) {
      return buffer$1.put(input);
    }

    var cb = takers.shift();
    cb(input);
  }

  function take(cb) {
    if (true) {
      checkForbiddenStates();
      Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(cb, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"], "channel.take's callback must be a function");
    }

    if (closed && buffer$1.isEmpty()) {
      cb(END);
    } else if (!buffer$1.isEmpty()) {
      cb(buffer$1.take());
    } else {
      takers.push(cb);

      cb.cancel = function () {
        Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["r"])(takers, cb);
      };
    }
  }

  function flush(cb) {
    if (true) {
      checkForbiddenStates();
      Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(cb, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"], "channel.flush' callback must be a function");
    }

    if (closed && buffer$1.isEmpty()) {
      cb(END);
      return;
    }

    cb(buffer$1.flush());
  }

  function close() {
    if (true) {
      checkForbiddenStates();
    }

    if (closed) {
      return;
    }

    closed = true;
    var arr = takers;
    takers = [];

    for (var i = 0, len = arr.length; i < len; i++) {
      var taker = arr[i];
      taker(END);
    }
  }

  return {
    take: take,
    put: put,
    flush: flush,
    close: close
  };
}
function eventChannel(subscribe, buffer) {
  if (buffer === void 0) {
    buffer = Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["n"])();
  }

  var closed = false;
  var unsubscribe;
  var chan = channel(buffer);

  var close = function close() {
    if (closed) {
      return;
    }

    closed = true;

    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"])(unsubscribe)) {
      unsubscribe();
    }

    chan.close();
  };

  unsubscribe = subscribe(function (input) {
    if (isEnd(input)) {
      close();
      return;
    }

    chan.put(input);
  });

  if (true) {
    Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(unsubscribe, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"], 'in eventChannel: subscribe should return a function to unsubscribe');
  }

  unsubscribe = Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["o"])(unsubscribe);

  if (closed) {
    unsubscribe();
  }

  return {
    take: chan.take,
    flush: chan.flush,
    close: close
  };
}
function multicastChannel() {
  var _ref;

  var closed = false;
  var currentTakers = [];
  var nextTakers = currentTakers;

  function checkForbiddenStates() {
    if (closed && nextTakers.length) {
      throw Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["i"])(CLOSED_CHANNEL_WITH_TAKERS);
    }
  }

  var ensureCanMutateNextTakers = function ensureCanMutateNextTakers() {
    if (nextTakers !== currentTakers) {
      return;
    }

    nextTakers = currentTakers.slice();
  };

  var close = function close() {
    if (true) {
      checkForbiddenStates();
    }

    closed = true;
    var takers = currentTakers = nextTakers;
    nextTakers = [];
    takers.forEach(function (taker) {
      taker(END);
    });
  };

  return _ref = {}, _ref[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["MULTICAST"]] = true, _ref.put = function put(input) {
    if (true) {
      checkForbiddenStates();
      Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(input, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["notUndef"], UNDEFINED_INPUT_ERROR);
    }

    if (closed) {
      return;
    }

    if (isEnd(input)) {
      close();
      return;
    }

    var takers = currentTakers = nextTakers;

    for (var i = 0, len = takers.length; i < len; i++) {
      var taker = takers[i];

      if (taker[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["MATCH"]](input)) {
        taker.cancel();
        taker(input);
      }
    }
  }, _ref.take = function take(cb, matcher) {
    if (matcher === void 0) {
      matcher = wildcard;
    }

    if (true) {
      checkForbiddenStates();
    }

    if (closed) {
      cb(END);
      return;
    }

    cb[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["MATCH"]] = matcher;
    ensureCanMutateNextTakers();
    nextTakers.push(cb);
    cb.cancel = Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["o"])(function () {
      ensureCanMutateNextTakers();
      Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["r"])(nextTakers, cb);
    });
  }, _ref.close = close, _ref;
}
function stdChannel() {
  var chan = multicastChannel();
  var put = chan.put;

  chan.put = function (input) {
    if (input[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["SAGA_ACTION"]]) {
      put(input);
      return;
    }

    asap(function () {
      put(input);
    });
  };

  return chan;
}

var RUNNING = 0;
var CANCELLED = 1;
var ABORTED = 2;
var DONE = 3;

function resolvePromise(promise, cb) {
  var cancelPromise = promise[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["CANCEL"]];

  if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"])(cancelPromise)) {
    cb.cancel = cancelPromise;
  }

  promise.then(cb, function (error) {
    cb(error, true);
  });
}

var current = 0;
var nextSagaId = (function () {
  return ++current;
});

var _effectRunnerMap;

function getIteratorMetaInfo(iterator, fn) {
  if (iterator.isSagaIterator) {
    return {
      name: iterator.meta.name
    };
  }

  return Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["j"])(fn);
}

function createTaskIterator(_ref) {
  var context = _ref.context,
      fn = _ref.fn,
      args = _ref.args;

  // catch synchronous failures; see #152 and #441
  try {
    var result = fn.apply(context, args); // i.e. a generator function returns an iterator

    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["iterator"])(result)) {
      return result;
    }

    var resolved = false;

    var next = function next(arg) {
      if (!resolved) {
        resolved = true; // Only promises returned from fork will be interpreted. See #1573

        return {
          value: result,
          done: !Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["promise"])(result)
        };
      } else {
        return {
          value: arg,
          done: true
        };
      }
    };

    return Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["q"])(next);
  } catch (err) {
    // do not bubble up synchronous failures for detached forks
    // instead create a failed task. See #152 and #441
    return Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["q"])(function () {
      throw err;
    });
  }
}

function runPutEffect(env, _ref2, cb) {
  var channel = _ref2.channel,
      action = _ref2.action,
      resolve = _ref2.resolve;

  /**
   Schedule the put in case another saga is holding a lock.
   The put will be executed atomically. ie nested puts will execute after
   this put has terminated.
   **/
  asap(function () {
    var result;

    try {
      result = (channel ? channel.put : env.dispatch)(action);
    } catch (error) {
      cb(error, true);
      return;
    }

    if (resolve && Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["promise"])(result)) {
      resolvePromise(result, cb);
    } else {
      cb(result);
    }
  }); // Put effects are non cancellables
}

function runTakeEffect(env, _ref3, cb) {
  var _ref3$channel = _ref3.channel,
      channel = _ref3$channel === void 0 ? env.channel : _ref3$channel,
      pattern = _ref3.pattern,
      maybe = _ref3.maybe;

  var takeCb = function takeCb(input) {
    if (input instanceof Error) {
      cb(input, true);
      return;
    }

    if (isEnd(input) && !maybe) {
      cb(_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["TERMINATE"]);
      return;
    }

    cb(input);
  };

  try {
    channel.take(takeCb, Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["notUndef"])(pattern) ? matcher(pattern) : null);
  } catch (err) {
    cb(err, true);
    return;
  }

  cb.cancel = takeCb.cancel;
}

function runCallEffect(env, _ref4, cb, _ref5) {
  var context = _ref4.context,
      fn = _ref4.fn,
      args = _ref4.args;
  var task = _ref5.task;

  // catch synchronous failures; see #152
  try {
    var result = fn.apply(context, args);

    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["promise"])(result)) {
      resolvePromise(result, cb);
      return;
    }

    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["iterator"])(result)) {
      // resolve iterator
      proc(env, result, task.context, current, Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["j"])(fn),
      /* isRoot */
      false, cb);
      return;
    }

    cb(result);
  } catch (error) {
    cb(error, true);
  }
}

function runCPSEffect(env, _ref6, cb) {
  var context = _ref6.context,
      fn = _ref6.fn,
      args = _ref6.args;

  // CPS (ie node style functions) can define their own cancellation logic
  // by setting cancel field on the cb
  // catch synchronous failures; see #152
  try {
    var cpsCb = function cpsCb(err, res) {
      if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["undef"])(err)) {
        cb(res);
      } else {
        cb(err, true);
      }
    };

    fn.apply(context, args.concat(cpsCb));

    if (cpsCb.cancel) {
      cb.cancel = cpsCb.cancel;
    }
  } catch (error) {
    cb(error, true);
  }
}

function runForkEffect(env, _ref7, cb, _ref8) {
  var context = _ref7.context,
      fn = _ref7.fn,
      args = _ref7.args,
      detached = _ref7.detached;
  var parent = _ref8.task;
  var taskIterator = createTaskIterator({
    context: context,
    fn: fn,
    args: args
  });
  var meta = getIteratorMetaInfo(taskIterator, fn);
  immediately(function () {
    var child = proc(env, taskIterator, parent.context, current, meta, detached, undefined);

    if (detached) {
      cb(child);
    } else {
      if (child.isRunning()) {
        parent.queue.addTask(child);
        cb(child);
      } else if (child.isAborted()) {
        parent.queue.abort(child.error());
      } else {
        cb(child);
      }
    }
  }); // Fork effects are non cancellables
}

function runJoinEffect(env, taskOrTasks, cb, _ref9) {
  var task = _ref9.task;

  var joinSingleTask = function joinSingleTask(taskToJoin, cb) {
    if (taskToJoin.isRunning()) {
      var joiner = {
        task: task,
        cb: cb
      };

      cb.cancel = function () {
        if (taskToJoin.isRunning()) Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["r"])(taskToJoin.joiners, joiner);
      };

      taskToJoin.joiners.push(joiner);
    } else {
      if (taskToJoin.isAborted()) {
        cb(taskToJoin.error(), true);
      } else {
        cb(taskToJoin.result());
      }
    }
  };

  if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["array"])(taskOrTasks)) {
    if (taskOrTasks.length === 0) {
      cb([]);
      return;
    }

    var childCallbacks = Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["l"])(taskOrTasks, cb);
    taskOrTasks.forEach(function (t, i) {
      joinSingleTask(t, childCallbacks[i]);
    });
  } else {
    joinSingleTask(taskOrTasks, cb);
  }
}

function cancelSingleTask(taskToCancel) {
  if (taskToCancel.isRunning()) {
    taskToCancel.cancel();
  }
}

function runCancelEffect(env, taskOrTasks, cb, _ref10) {
  var task = _ref10.task;

  if (taskOrTasks === _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["SELF_CANCELLATION"]) {
    cancelSingleTask(task);
  } else if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["array"])(taskOrTasks)) {
    taskOrTasks.forEach(cancelSingleTask);
  } else {
    cancelSingleTask(taskOrTasks);
  }

  cb(); // cancel effects are non cancellables
}

function runAllEffect(env, effects, cb, _ref11) {
  var digestEffect = _ref11.digestEffect;
  var effectId = current;
  var keys = Object.keys(effects);

  if (keys.length === 0) {
    cb(Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["array"])(effects) ? [] : {});
    return;
  }

  var childCallbacks = Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["l"])(effects, cb);
  keys.forEach(function (key) {
    digestEffect(effects[key], effectId, childCallbacks[key], key);
  });
}

function runRaceEffect(env, effects, cb, _ref12) {
  var digestEffect = _ref12.digestEffect;
  var effectId = current;
  var keys = Object.keys(effects);
  var response = Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["array"])(effects) ? Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["m"])(keys.length) : {};
  var childCbs = {};
  var completed = false;
  keys.forEach(function (key) {
    var chCbAtKey = function chCbAtKey(res, isErr) {
      if (completed) {
        return;
      }

      if (isErr || Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["s"])(res)) {
        // Race Auto cancellation
        cb.cancel();
        cb(res, isErr);
      } else {
        cb.cancel();
        completed = true;
        response[key] = res;
        cb(response);
      }
    };

    chCbAtKey.cancel = _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"];
    childCbs[key] = chCbAtKey;
  });

  cb.cancel = function () {
    // prevents unnecessary cancellation
    if (!completed) {
      completed = true;
      keys.forEach(function (key) {
        return childCbs[key].cancel();
      });
    }
  };

  keys.forEach(function (key) {
    if (completed) {
      return;
    }

    digestEffect(effects[key], effectId, childCbs[key], key);
  });
}

function runSelectEffect(env, _ref13, cb) {
  var selector = _ref13.selector,
      args = _ref13.args;

  try {
    var state = selector.apply(void 0, [env.getState()].concat(args));
    cb(state);
  } catch (error) {
    cb(error, true);
  }
}

function runChannelEffect(env, _ref14, cb) {
  var pattern = _ref14.pattern,
      buffer = _ref14.buffer;
  var chan = channel(buffer);
  var match = matcher(pattern);

  var taker = function taker(action) {
    if (!isEnd(action)) {
      env.channel.take(taker, match);
    }

    chan.put(action);
  };

  var close = chan.close;

  chan.close = function () {
    taker.cancel();
    close();
  };

  env.channel.take(taker, match);
  cb(chan);
}

function runCancelledEffect(env, data, cb, _ref15) {
  var task = _ref15.task;
  cb(task.isCancelled());
}

function runFlushEffect(env, channel, cb) {
  channel.flush(cb);
}

function runGetContextEffect(env, prop, cb, _ref16) {
  var task = _ref16.task;
  cb(task.context[prop]);
}

function runSetContextEffect(env, props, cb, _ref17) {
  var task = _ref17.task;
  Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["p"])(task.context, props);
  cb();
}

var effectRunnerMap = (_effectRunnerMap = {}, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["T"]] = runTakeEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["P"]] = runPutEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["A"]] = runAllEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["R"]] = runRaceEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["C"]] = runCallEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["a"]] = runCPSEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["F"]] = runForkEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["J"]] = runJoinEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["b"]] = runCancelEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["S"]] = runSelectEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["d"]] = runChannelEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["f"]] = runCancelledEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["g"]] = runFlushEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["G"]] = runGetContextEffect, _effectRunnerMap[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["h"]] = runSetContextEffect, _effectRunnerMap);

/**
 Used to track a parent task and its forks
 In the fork model, forked tasks are attached by default to their parent
 We model this using the concept of Parent task && main Task
 main task is the main flow of the current Generator, the parent tasks is the
 aggregation of the main tasks + all its forked tasks.
 Thus the whole model represents an execution tree with multiple branches (vs the
 linear execution tree in sequential (non parallel) programming)

 A parent tasks has the following semantics
 - It completes if all its forks either complete or all cancelled
 - If it's cancelled, all forks are cancelled as well
 - It aborts if any uncaught error bubbles up from forks
 - If it completes, the return value is the one returned by the main task
 **/

function forkQueue(mainTask, onAbort, cont) {
  var tasks = [];
  var result;
  var completed = false;
  addTask(mainTask);

  var getTasks = function getTasks() {
    return tasks;
  };

  function abort(err) {
    onAbort();
    cancelAll();
    cont(err, true);
  }

  function addTask(task) {
    tasks.push(task);

    task.cont = function (res, isErr) {
      if (completed) {
        return;
      }

      Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["r"])(tasks, task);
      task.cont = _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"];

      if (isErr) {
        abort(res);
      } else {
        if (task === mainTask) {
          result = res;
        }

        if (!tasks.length) {
          completed = true;
          cont(result);
        }
      }
    };
  }

  function cancelAll() {
    if (completed) {
      return;
    }

    completed = true;
    tasks.forEach(function (t) {
      t.cont = _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"];
      t.cancel();
    });
    tasks = [];
  }

  return {
    addTask: addTask,
    cancelAll: cancelAll,
    abort: abort,
    getTasks: getTasks
  };
}

// there can be only a single saga error created at any given moment

function formatLocation(fileName, lineNumber) {
  return fileName + "?" + lineNumber;
}

function effectLocationAsString(effect) {
  var location = Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["v"])(effect);

  if (location) {
    var code = location.code,
        fileName = location.fileName,
        lineNumber = location.lineNumber;
    var source = code + "  " + formatLocation(fileName, lineNumber);
    return source;
  }

  return '';
}

function sagaLocationAsString(sagaMeta) {
  var name = sagaMeta.name,
      location = sagaMeta.location;

  if (location) {
    return name + "  " + formatLocation(location.fileName, location.lineNumber);
  }

  return name;
}

function cancelledTasksAsString(sagaStack) {
  var cancelledTasks = Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["u"])(function (i) {
    return i.cancelledTasks;
  }, sagaStack);

  if (!cancelledTasks.length) {
    return '';
  }

  return ['Tasks cancelled due to error:'].concat(cancelledTasks).join('\n');
}

var crashedEffect = null;
var sagaStack = [];
var addSagaFrame = function addSagaFrame(frame) {
  frame.crashedEffect = crashedEffect;
  sagaStack.push(frame);
};
var clear = function clear() {
  crashedEffect = null;
  sagaStack.length = 0;
}; // this sets crashed effect for the soon-to-be-reported saga frame
// this slightly streatches the singleton nature of this module into wrong direction
// as it's even less obvious what's the data flow here, but it is what it is for now

var setCrashedEffect = function setCrashedEffect(effect) {
  crashedEffect = effect;
};
/**
  @returns {string}

  @example
  The above error occurred in task errorInPutSaga {pathToFile}
  when executing effect put({type: 'REDUCER_ACTION_ERROR_IN_PUT'}) {pathToFile}
      created by fetchSaga {pathToFile}
      created by rootSaga {pathToFile}
*/

var toString = function toString() {
  var firstSaga = sagaStack[0],
      otherSagas = sagaStack.slice(1);
  var crashedEffectLocation = firstSaga.crashedEffect ? effectLocationAsString(firstSaga.crashedEffect) : null;
  var errorMessage = "The above error occurred in task " + sagaLocationAsString(firstSaga.meta) + (crashedEffectLocation ? " \n when executing effect " + crashedEffectLocation : '');
  return [errorMessage].concat(otherSagas.map(function (s) {
    return "    created by " + sagaLocationAsString(s.meta);
  }), [cancelledTasksAsString(sagaStack)]).join('\n');
};

function newTask(env, mainTask, parentContext, parentEffectId, meta, isRoot, cont) {
  var _task;

  if (cont === void 0) {
    cont = _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"];
  }

  var status = RUNNING;
  var taskResult;
  var taskError;
  var deferredEnd = null;
  var cancelledDueToErrorTasks = [];
  var context = Object.create(parentContext);
  var queue = forkQueue(mainTask, function onAbort() {
    cancelledDueToErrorTasks.push.apply(cancelledDueToErrorTasks, queue.getTasks().map(function (t) {
      return t.meta.name;
    }));
  }, end);
  /**
   This may be called by a parent generator to trigger/propagate cancellation
   cancel all pending tasks (including the main task), then end the current task.
    Cancellation propagates down to the whole execution tree held by this Parent task
   It's also propagated to all joiners of this task and their execution tree/joiners
    Cancellation is noop for terminated/Cancelled tasks tasks
   **/

  function cancel() {
    if (status === RUNNING) {
      // Setting status to CANCELLED does not necessarily mean that the task/iterators are stopped
      // effects in the iterator's finally block will still be executed
      status = CANCELLED;
      queue.cancelAll(); // Ending with a TASK_CANCEL will propagate the Cancellation to all joiners

      end(_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["TASK_CANCEL"], false);
    }
  }

  function end(result, isErr) {
    if (!isErr) {
      // The status here may be RUNNING or CANCELLED
      // If the status is CANCELLED, then we do not need to change it here
      if (result === _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["TASK_CANCEL"]) {
        status = CANCELLED;
      } else if (status !== CANCELLED) {
        status = DONE;
      }

      taskResult = result;
      deferredEnd && deferredEnd.resolve(result);
    } else {
      status = ABORTED;
      addSagaFrame({
        meta: meta,
        cancelledTasks: cancelledDueToErrorTasks
      });

      if (task.isRoot) {
        var sagaStack = toString(); // we've dumped the saga stack to string and are passing it to user's code
        // we know that it won't be needed anymore and we need to clear it

        clear();
        env.onError(result, {
          sagaStack: sagaStack
        });
      }

      taskError = result;
      deferredEnd && deferredEnd.reject(result);
    }

    task.cont(result, isErr);
    task.joiners.forEach(function (joiner) {
      joiner.cb(result, isErr);
    });
    task.joiners = null;
  }

  function setContext(props) {
    if (true) {
      Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(props, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["object"], Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["w"])('task', props));
    }

    Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["p"])(context, props);
  }

  function toPromise() {
    if (deferredEnd) {
      return deferredEnd.promise;
    }

    deferredEnd = Object(_redux_saga_deferred__WEBPACK_IMPORTED_MODULE_6__["default"])();

    if (status === ABORTED) {
      deferredEnd.reject(taskError);
    } else if (status !== RUNNING) {
      deferredEnd.resolve(taskResult);
    }

    return deferredEnd.promise;
  }

  var task = (_task = {}, _task[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["TASK"]] = true, _task.id = parentEffectId, _task.meta = meta, _task.isRoot = isRoot, _task.context = context, _task.joiners = [], _task.queue = queue, _task.cancel = cancel, _task.cont = cont, _task.end = end, _task.setContext = setContext, _task.toPromise = toPromise, _task.isRunning = function isRunning() {
    return status === RUNNING;
  }, _task.isCancelled = function isCancelled() {
    return status === CANCELLED || status === RUNNING && mainTask.status === CANCELLED;
  }, _task.isAborted = function isAborted() {
    return status === ABORTED;
  }, _task.result = function result() {
    return taskResult;
  }, _task.error = function error() {
    return taskError;
  }, _task);
  return task;
}

function proc(env, iterator$1, parentContext, parentEffectId, meta, isRoot, cont) {
  if ( true && iterator$1[_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["x"]]) {
    throw new Error("redux-saga doesn't support async generators, please use only regular ones");
  }

  var finalRunEffect = env.finalizeRunEffect(runEffect);
  /**
    Tracks the current effect cancellation
    Each time the generator progresses. calling runEffect will set a new value
    on it. It allows propagating cancellation to child effects
  **/

  next.cancel = _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"];
  /** Creates a main task to track the main flow */

  var mainTask = {
    meta: meta,
    cancel: cancelMain,
    status: RUNNING
  };
  /**
   Creates a new task descriptor for this generator.
   A task is the aggregation of it's mainTask and all it's forked tasks.
   **/

  var task = newTask(env, mainTask, parentContext, parentEffectId, meta, isRoot, cont);
  var executingContext = {
    task: task,
    digestEffect: digestEffect
  };
  /**
    cancellation of the main task. We'll simply resume the Generator with a TASK_CANCEL
  **/

  function cancelMain() {
    if (mainTask.status === RUNNING) {
      mainTask.status = CANCELLED;
      next(_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["TASK_CANCEL"]);
    }
  }
  /**
    attaches cancellation logic to this task's continuation
    this will permit cancellation to propagate down the call chain
  **/


  if (cont) {
    cont.cancel = task.cancel;
  } // kicks up the generator


  next(); // then return the task descriptor to the caller

  return task;
  /**
   * This is the generator driver
   * It's a recursive async/continuation function which calls itself
   * until the generator terminates or throws
   * @param {internal commands(TASK_CANCEL | TERMINATE) | any} arg - value, generator will be resumed with.
   * @param {boolean} isErr - the flag shows if effect finished with an error
   *
   * receives either (command | effect result, false) or (any thrown thing, true)
   */

  function next(arg, isErr) {
    try {
      var result;

      if (isErr) {
        result = iterator$1.throw(arg); // user handled the error, we can clear bookkept values

        clear();
      } else if (Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["y"])(arg)) {
        /**
          getting TASK_CANCEL automatically cancels the main task
          We can get this value here
           - By cancelling the parent task manually
          - By joining a Cancelled task
        **/
        mainTask.status = CANCELLED;
        /**
          Cancels the current effect; this will propagate the cancellation down to any called tasks
        **/

        next.cancel();
        /**
          If this Generator has a `return` method then invokes it
          This will jump to the finally block
        **/

        result = Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"])(iterator$1.return) ? iterator$1.return(_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["TASK_CANCEL"]) : {
          done: true,
          value: _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["TASK_CANCEL"]
        };
      } else if (Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["z"])(arg)) {
        // We get TERMINATE flag, i.e. by taking from a channel that ended using `take` (and not `takem` used to trap End of channels)
        result = Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"])(iterator$1.return) ? iterator$1.return() : {
          done: true
        };
      } else {
        result = iterator$1.next(arg);
      }

      if (!result.done) {
        digestEffect(result.value, parentEffectId, next);
      } else {
        /**
          This Generator has ended, terminate the main task and notify the fork queue
        **/
        if (mainTask.status !== CANCELLED) {
          mainTask.status = DONE;
        }

        mainTask.cont(result.value);
      }
    } catch (error) {
      if (mainTask.status === CANCELLED) {
        throw error;
      }

      mainTask.status = ABORTED;
      mainTask.cont(error, true);
    }
  }

  function runEffect(effect, effectId, currCb) {
    /**
      each effect runner must attach its own logic of cancellation to the provided callback
      it allows this generator to propagate cancellation downward.
       ATTENTION! effect runners must setup the cancel logic by setting cb.cancel = [cancelMethod]
      And the setup must occur before calling the callback
       This is a sort of inversion of control: called async functions are responsible
      of completing the flow by calling the provided continuation; while caller functions
      are responsible for aborting the current flow by calling the attached cancel function
       Library users can attach their own cancellation logic to promises by defining a
      promise[CANCEL] method in their returned promises
      ATTENTION! calling cancel must have no effect on an already completed or cancelled effect
    **/
    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["promise"])(effect)) {
      resolvePromise(effect, currCb);
    } else if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["iterator"])(effect)) {
      // resolve iterator
      proc(env, effect, task.context, effectId, meta,
      /* isRoot */
      false, currCb);
    } else if (effect && effect[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["IO"]]) {
      var effectRunner = effectRunnerMap[effect.type];
      effectRunner(env, effect.payload, currCb, executingContext);
    } else {
      // anything else returned as is
      currCb(effect);
    }
  }

  function digestEffect(effect, parentEffectId, cb, label) {
    if (label === void 0) {
      label = '';
    }

    var effectId = nextSagaId();
    env.sagaMonitor && env.sagaMonitor.effectTriggered({
      effectId: effectId,
      parentEffectId: parentEffectId,
      label: label,
      effect: effect
    });
    /**
      completion callback and cancel callback are mutually exclusive
      We can't cancel an already completed effect
      And We can't complete an already cancelled effectId
    **/

    var effectSettled; // Completion callback passed to the appropriate effect runner

    function currCb(res, isErr) {
      if (effectSettled) {
        return;
      }

      effectSettled = true;
      cb.cancel = _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"]; // defensive measure

      if (env.sagaMonitor) {
        if (isErr) {
          env.sagaMonitor.effectRejected(effectId, res);
        } else {
          env.sagaMonitor.effectResolved(effectId, res);
        }
      }

      if (isErr) {
        setCrashedEffect(effect);
      }

      cb(res, isErr);
    } // tracks down the current cancel


    currCb.cancel = _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"]; // setup cancellation logic on the parent cb

    cb.cancel = function () {
      // prevents cancelling an already completed effect
      if (effectSettled) {
        return;
      }

      effectSettled = true;
      currCb.cancel(); // propagates cancel downward

      currCb.cancel = _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"]; // defensive measure

      env.sagaMonitor && env.sagaMonitor.effectCancelled(effectId);
    };

    finalRunEffect(effect, effectId, currCb);
  }
}

var RUN_SAGA_SIGNATURE = 'runSaga(options, saga, ...args)';
var NON_GENERATOR_ERR = RUN_SAGA_SIGNATURE + ": saga argument must be a Generator function!";
function runSaga(_ref, saga) {
  var _ref$channel = _ref.channel,
      channel = _ref$channel === void 0 ? stdChannel() : _ref$channel,
      dispatch = _ref.dispatch,
      getState = _ref.getState,
      _ref$context = _ref.context,
      context = _ref$context === void 0 ? {} : _ref$context,
      sagaMonitor = _ref.sagaMonitor,
      effectMiddlewares = _ref.effectMiddlewares,
      _ref$onError = _ref.onError,
      onError = _ref$onError === void 0 ? _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["B"] : _ref$onError;

  if (true) {
    Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(saga, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"], NON_GENERATOR_ERR);
  }

  for (var _len = arguments.length, args = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
    args[_key - 2] = arguments[_key];
  }

  var iterator$1 = saga.apply(void 0, args);

  if (true) {
    Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(iterator$1, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["iterator"], NON_GENERATOR_ERR);
  }

  var effectId = nextSagaId();

  if (sagaMonitor) {
    // monitors are expected to have a certain interface, let's fill-in any missing ones
    sagaMonitor.rootSagaStarted = sagaMonitor.rootSagaStarted || _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"];
    sagaMonitor.effectTriggered = sagaMonitor.effectTriggered || _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"];
    sagaMonitor.effectResolved = sagaMonitor.effectResolved || _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"];
    sagaMonitor.effectRejected = sagaMonitor.effectRejected || _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"];
    sagaMonitor.effectCancelled = sagaMonitor.effectCancelled || _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"];
    sagaMonitor.actionDispatched = sagaMonitor.actionDispatched || _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["t"];
    sagaMonitor.rootSagaStarted({
      effectId: effectId,
      saga: saga,
      args: args
    });
  }

  if (true) {
    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["notUndef"])(dispatch)) {
      Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(dispatch, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"], 'dispatch must be a function');
    }

    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["notUndef"])(getState)) {
      Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(getState, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"], 'getState must be a function');
    }

    if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["notUndef"])(effectMiddlewares)) {
      var MIDDLEWARE_TYPE_ERROR = 'effectMiddlewares must be an array of functions';
      Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(effectMiddlewares, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["array"], MIDDLEWARE_TYPE_ERROR);
      effectMiddlewares.forEach(function (effectMiddleware) {
        return Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(effectMiddleware, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"], MIDDLEWARE_TYPE_ERROR);
      });
    }

    Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(onError, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["func"], 'onError passed to the redux-saga is not a function!');
  }

  var finalizeRunEffect;

  if (effectMiddlewares) {
    var middleware = redux__WEBPACK_IMPORTED_MODULE_5__["compose"].apply(void 0, effectMiddlewares);

    finalizeRunEffect = function finalizeRunEffect(runEffect) {
      return function (effect, effectId, currCb) {
        var plainRunEffect = function plainRunEffect(eff) {
          return runEffect(eff, effectId, currCb);
        };

        return middleware(plainRunEffect)(effect);
      };
    };
  } else {
    finalizeRunEffect = _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["E"];
  }

  var env = {
    channel: channel,
    dispatch: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["D"])(dispatch),
    getState: getState,
    sagaMonitor: sagaMonitor,
    onError: onError,
    finalizeRunEffect: finalizeRunEffect
  };
  return immediately(function () {
    var task = proc(env, iterator$1, context, effectId, Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["j"])(saga),
    /* isRoot */
    true, undefined);

    if (sagaMonitor) {
      sagaMonitor.effectResolved(effectId, task);
    }

    return task;
  });
}

function sagaMiddlewareFactory(_temp) {
  var _ref = _temp === void 0 ? {} : _temp,
      _ref$context = _ref.context,
      context = _ref$context === void 0 ? {} : _ref$context,
      _ref$channel = _ref.channel,
      channel = _ref$channel === void 0 ? stdChannel() : _ref$channel,
      sagaMonitor = _ref.sagaMonitor,
      options = Object(_babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_2__["default"])(_ref, ["context", "channel", "sagaMonitor"]);

  var boundRunSaga;

  if (true) {
    Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(channel, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["channel"], 'options.channel passed to the Saga middleware is not a channel');
  }

  function sagaMiddleware(_ref2) {
    var getState = _ref2.getState,
        dispatch = _ref2.dispatch;
    boundRunSaga = runSaga.bind(null, Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__["default"])({}, options, {
      context: context,
      channel: channel,
      dispatch: dispatch,
      getState: getState,
      sagaMonitor: sagaMonitor
    }));
    return function (next) {
      return function (action) {
        if (sagaMonitor && sagaMonitor.actionDispatched) {
          sagaMonitor.actionDispatched(action);
        }

        var result = next(action); // hit reducers

        channel.put(action);
        return result;
      };
    };
  }

  sagaMiddleware.run = function () {
    if ( true && !boundRunSaga) {
      throw new Error('Before running a Saga, you must mount the Saga middleware on the Store using applyMiddleware');
    }

    return boundRunSaga.apply(void 0, arguments);
  };

  sagaMiddleware.setContext = function (props) {
    if (true) {
      Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["c"])(props, _redux_saga_is__WEBPACK_IMPORTED_MODULE_3__["object"], Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["w"])('sagaMiddleware', props));
    }

    Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_4__["p"])(context, props);
  };

  return sagaMiddleware;
}

/* harmony default export */ __webpack_exports__["default"] = (sagaMiddlewareFactory);



/***/ }),

/***/ "./node_modules/@redux-saga/core/dist/redux-saga-effects.esm.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@redux-saga/core/dist/redux-saga-effects.esm.js ***!
  \**********************************************************************/
/*! exports provided: actionChannel, all, apply, call, cancel, cancelled, cps, delay, effectTypes, flush, fork, getContext, join, put, putResolve, race, select, setContext, spawn, take, takeMaybe, debounce, retry, takeEvery, takeLatest, takeLeading, throttle */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "debounce", function() { return debounce; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "retry", function() { return retry$1; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "takeEvery", function() { return takeEvery$1; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "takeLatest", function() { return takeLatest$1; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "takeLeading", function() { return takeLeading$1; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "throttle", function() { return throttle$1; });
/* harmony import */ var _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @redux-saga/symbols */ "./node_modules/@redux-saga/symbols/dist/redux-saga-symbols.esm.js");
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @redux-saga/is */ "./node_modules/@redux-saga/is/dist/redux-saga-is.esm.js");
/* harmony import */ var _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./io-40341e1a.js */ "./node_modules/@redux-saga/core/dist/io-40341e1a.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "actionChannel", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["O"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "all", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["_"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "apply", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["$"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "call", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["N"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "cancel", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["M"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "cancelled", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["a4"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "cps", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["a0"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "delay", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["U"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "effectTypes", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["W"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "flush", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["a5"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "fork", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["L"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "getContext", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["a6"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "join", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["a2"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "put", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["Y"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "putResolve", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["Z"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "race", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["V"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "select", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["a3"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "setContext", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["a7"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "spawn", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["a1"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "take", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["K"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "takeMaybe", function() { return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["X"]; });

/* harmony import */ var _redux_saga_delay_p__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @redux-saga/delay-p */ "./node_modules/@redux-saga/delay-p/dist/redux-saga-delay-p.esm.js");







var done = function done(value) {
  return {
    done: true,
    value: value
  };
};

var qEnd = {};
function safeName(patternOrChannel) {
  if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["channel"])(patternOrChannel)) {
    return 'channel';
  }

  if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["stringableFunc"])(patternOrChannel)) {
    return String(patternOrChannel);
  }

  if (Object(_redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["func"])(patternOrChannel)) {
    return patternOrChannel.name;
  }

  return String(patternOrChannel);
}
function fsmIterator(fsm, startState, name) {
  var stateUpdater,
      errorState,
      effect,
      nextState = startState;

  function next(arg, error) {
    if (nextState === qEnd) {
      return done(arg);
    }

    if (error && !errorState) {
      nextState = qEnd;
      throw error;
    } else {
      stateUpdater && stateUpdater(arg);
      var currentState = error ? fsm[errorState](error) : fsm[nextState]();
      nextState = currentState.nextState;
      effect = currentState.effect;
      stateUpdater = currentState.stateUpdater;
      errorState = currentState.errorState;
      return nextState === qEnd ? done(arg) : effect;
    }
  }

  return Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["q"])(next, function (error) {
    return next(null, error);
  }, name);
}

function takeEvery(patternOrChannel, worker) {
  for (var _len = arguments.length, args = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
    args[_key - 2] = arguments[_key];
  }

  var yTake = {
    done: false,
    value: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["K"])(patternOrChannel)
  };

  var yFork = function yFork(ac) {
    return {
      done: false,
      value: _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["L"].apply(void 0, [worker].concat(args, [ac]))
    };
  };

  var action,
      setAction = function setAction(ac) {
    return action = ac;
  };

  return fsmIterator({
    q1: function q1() {
      return {
        nextState: 'q2',
        effect: yTake,
        stateUpdater: setAction
      };
    },
    q2: function q2() {
      return {
        nextState: 'q1',
        effect: yFork(action)
      };
    }
  }, 'q1', "takeEvery(" + safeName(patternOrChannel) + ", " + worker.name + ")");
}

function takeLatest(patternOrChannel, worker) {
  for (var _len = arguments.length, args = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
    args[_key - 2] = arguments[_key];
  }

  var yTake = {
    done: false,
    value: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["K"])(patternOrChannel)
  };

  var yFork = function yFork(ac) {
    return {
      done: false,
      value: _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["L"].apply(void 0, [worker].concat(args, [ac]))
    };
  };

  var yCancel = function yCancel(task) {
    return {
      done: false,
      value: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["M"])(task)
    };
  };

  var task, action;

  var setTask = function setTask(t) {
    return task = t;
  };

  var setAction = function setAction(ac) {
    return action = ac;
  };

  return fsmIterator({
    q1: function q1() {
      return {
        nextState: 'q2',
        effect: yTake,
        stateUpdater: setAction
      };
    },
    q2: function q2() {
      return task ? {
        nextState: 'q3',
        effect: yCancel(task)
      } : {
        nextState: 'q1',
        effect: yFork(action),
        stateUpdater: setTask
      };
    },
    q3: function q3() {
      return {
        nextState: 'q1',
        effect: yFork(action),
        stateUpdater: setTask
      };
    }
  }, 'q1', "takeLatest(" + safeName(patternOrChannel) + ", " + worker.name + ")");
}

function takeLeading(patternOrChannel, worker) {
  for (var _len = arguments.length, args = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
    args[_key - 2] = arguments[_key];
  }

  var yTake = {
    done: false,
    value: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["K"])(patternOrChannel)
  };

  var yCall = function yCall(ac) {
    return {
      done: false,
      value: _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["N"].apply(void 0, [worker].concat(args, [ac]))
    };
  };

  var action;

  var setAction = function setAction(ac) {
    return action = ac;
  };

  return fsmIterator({
    q1: function q1() {
      return {
        nextState: 'q2',
        effect: yTake,
        stateUpdater: setAction
      };
    },
    q2: function q2() {
      return {
        nextState: 'q1',
        effect: yCall(action)
      };
    }
  }, 'q1', "takeLeading(" + safeName(patternOrChannel) + ", " + worker.name + ")");
}

function throttle(delayLength, pattern, worker) {
  for (var _len = arguments.length, args = new Array(_len > 3 ? _len - 3 : 0), _key = 3; _key < _len; _key++) {
    args[_key - 3] = arguments[_key];
  }

  var action, channel;
  var yActionChannel = {
    done: false,
    value: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["O"])(pattern, Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["Q"])(1))
  };

  var yTake = function yTake() {
    return {
      done: false,
      value: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["K"])(channel)
    };
  };

  var yFork = function yFork(ac) {
    return {
      done: false,
      value: _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["L"].apply(void 0, [worker].concat(args, [ac]))
    };
  };

  var yDelay = {
    done: false,
    value: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["U"])(delayLength)
  };

  var setAction = function setAction(ac) {
    return action = ac;
  };

  var setChannel = function setChannel(ch) {
    return channel = ch;
  };

  return fsmIterator({
    q1: function q1() {
      return {
        nextState: 'q2',
        effect: yActionChannel,
        stateUpdater: setChannel
      };
    },
    q2: function q2() {
      return {
        nextState: 'q3',
        effect: yTake(),
        stateUpdater: setAction
      };
    },
    q3: function q3() {
      return {
        nextState: 'q4',
        effect: yFork(action)
      };
    },
    q4: function q4() {
      return {
        nextState: 'q2',
        effect: yDelay
      };
    }
  }, 'q1', "throttle(" + safeName(pattern) + ", " + worker.name + ")");
}

function retry(maxTries, delayLength, fn) {
  var counter = maxTries;

  for (var _len = arguments.length, args = new Array(_len > 3 ? _len - 3 : 0), _key = 3; _key < _len; _key++) {
    args[_key - 3] = arguments[_key];
  }

  var yCall = {
    done: false,
    value: _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["N"].apply(void 0, [fn].concat(args))
  };
  var yDelay = {
    done: false,
    value: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["U"])(delayLength)
  };
  return fsmIterator({
    q1: function q1() {
      return {
        nextState: 'q2',
        effect: yCall,
        errorState: 'q10'
      };
    },
    q2: function q2() {
      return {
        nextState: qEnd
      };
    },
    q10: function q10(error) {
      counter -= 1;

      if (counter <= 0) {
        throw error;
      }

      return {
        nextState: 'q1',
        effect: yDelay
      };
    }
  }, 'q1', "retry(" + fn.name + ")");
}

function debounceHelper(delayLength, patternOrChannel, worker) {
  for (var _len = arguments.length, args = new Array(_len > 3 ? _len - 3 : 0), _key = 3; _key < _len; _key++) {
    args[_key - 3] = arguments[_key];
  }

  var action, raceOutput;
  var yTake = {
    done: false,
    value: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["K"])(patternOrChannel)
  };
  var yRace = {
    done: false,
    value: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["V"])({
      action: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["K"])(patternOrChannel),
      debounce: Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["U"])(delayLength)
    })
  };

  var yFork = function yFork(ac) {
    return {
      done: false,
      value: _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["L"].apply(void 0, [worker].concat(args, [ac]))
    };
  };

  var yNoop = function yNoop(value) {
    return {
      done: false,
      value: value
    };
  };

  var setAction = function setAction(ac) {
    return action = ac;
  };

  var setRaceOutput = function setRaceOutput(ro) {
    return raceOutput = ro;
  };

  return fsmIterator({
    q1: function q1() {
      return {
        nextState: 'q2',
        effect: yTake,
        stateUpdater: setAction
      };
    },
    q2: function q2() {
      return {
        nextState: 'q3',
        effect: yRace,
        stateUpdater: setRaceOutput
      };
    },
    q3: function q3() {
      return raceOutput.debounce ? {
        nextState: 'q1',
        effect: yFork(action)
      } : {
        nextState: 'q2',
        effect: yNoop(raceOutput.action),
        stateUpdater: setAction
      };
    }
  }, 'q1', "debounce(" + safeName(patternOrChannel) + ", " + worker.name + ")");
}

var validateTakeEffect = function validateTakeEffect(fn, patternOrChannel, worker) {
  Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["c"])(patternOrChannel, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], fn.name + " requires a pattern or channel");
  Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["c"])(worker, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], fn.name + " requires a saga parameter");
};

function takeEvery$1(patternOrChannel, worker) {
  if (true) {
    validateTakeEffect(takeEvery$1, patternOrChannel, worker);
  }

  for (var _len = arguments.length, args = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
    args[_key - 2] = arguments[_key];
  }

  return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["L"].apply(void 0, [takeEvery, patternOrChannel, worker].concat(args));
}
function takeLatest$1(patternOrChannel, worker) {
  if (true) {
    validateTakeEffect(takeLatest$1, patternOrChannel, worker);
  }

  for (var _len2 = arguments.length, args = new Array(_len2 > 2 ? _len2 - 2 : 0), _key2 = 2; _key2 < _len2; _key2++) {
    args[_key2 - 2] = arguments[_key2];
  }

  return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["L"].apply(void 0, [takeLatest, patternOrChannel, worker].concat(args));
}
function takeLeading$1(patternOrChannel, worker) {
  if (true) {
    validateTakeEffect(takeLeading$1, patternOrChannel, worker);
  }

  for (var _len3 = arguments.length, args = new Array(_len3 > 2 ? _len3 - 2 : 0), _key3 = 2; _key3 < _len3; _key3++) {
    args[_key3 - 2] = arguments[_key3];
  }

  return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["L"].apply(void 0, [takeLeading, patternOrChannel, worker].concat(args));
}
function throttle$1(ms, pattern, worker) {
  if (true) {
    Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["c"])(pattern, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], 'throttle requires a pattern');
    Object(_io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["c"])(worker, _redux_saga_is__WEBPACK_IMPORTED_MODULE_2__["notUndef"], 'throttle requires a saga parameter');
  }

  for (var _len4 = arguments.length, args = new Array(_len4 > 3 ? _len4 - 3 : 0), _key4 = 3; _key4 < _len4; _key4++) {
    args[_key4 - 3] = arguments[_key4];
  }

  return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["L"].apply(void 0, [throttle, ms, pattern, worker].concat(args));
}
function retry$1(maxTries, delayLength, worker) {
  for (var _len5 = arguments.length, args = new Array(_len5 > 3 ? _len5 - 3 : 0), _key5 = 3; _key5 < _len5; _key5++) {
    args[_key5 - 3] = arguments[_key5];
  }

  return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["N"].apply(void 0, [retry, maxTries, delayLength, worker].concat(args));
}
function debounce(delayLength, pattern, worker) {
  for (var _len6 = arguments.length, args = new Array(_len6 > 3 ? _len6 - 3 : 0), _key6 = 3; _key6 < _len6; _key6++) {
    args[_key6 - 3] = arguments[_key6];
  }

  return _io_40341e1a_js__WEBPACK_IMPORTED_MODULE_3__["L"].apply(void 0, [debounceHelper, delayLength, pattern, worker].concat(args));
}




/***/ }),

/***/ "./node_modules/@redux-saga/deferred/dist/redux-saga-deferred.esm.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@redux-saga/deferred/dist/redux-saga-deferred.esm.js ***!
  \***************************************************************************/
/*! exports provided: default, arrayOfDeferred */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "arrayOfDeferred", function() { return arrayOfDeferred; });
function deferred() {
  var def = {};
  def.promise = new Promise(function (resolve, reject) {
    def.resolve = resolve;
    def.reject = reject;
  });
  return def;
}
function arrayOfDeferred(length) {
  var arr = [];

  for (var i = 0; i < length; i++) {
    arr.push(deferred());
  }

  return arr;
}

/* harmony default export */ __webpack_exports__["default"] = (deferred);



/***/ }),

/***/ "./node_modules/@redux-saga/delay-p/dist/redux-saga-delay-p.esm.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@redux-saga/delay-p/dist/redux-saga-delay-p.esm.js ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @redux-saga/symbols */ "./node_modules/@redux-saga/symbols/dist/redux-saga-symbols.esm.js");


var MAX_SIGNED_INT = 2147483647;
function delayP(ms, val) {
  if (val === void 0) {
    val = true;
  }

  // https://developer.mozilla.org/en-US/docs/Web/API/setTimeout#maximum_delay_value
  if ( true && ms > MAX_SIGNED_INT) {
    throw new Error('delay only supports a maximum value of ' + MAX_SIGNED_INT + 'ms');
  }

  var timeoutId;
  var promise = new Promise(function (resolve) {
    timeoutId = setTimeout(resolve, Math.min(MAX_SIGNED_INT, ms), val);
  });

  promise[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["CANCEL"]] = function () {
    clearTimeout(timeoutId);
  };

  return promise;
}

/* harmony default export */ __webpack_exports__["default"] = (delayP);


/***/ }),

/***/ "./node_modules/@redux-saga/is/dist/redux-saga-is.esm.js":
/*!***************************************************************!*\
  !*** ./node_modules/@redux-saga/is/dist/redux-saga-is.esm.js ***!
  \***************************************************************/
/*! exports provided: array, buffer, channel, effect, func, iterable, iterator, multicast, notUndef, number, object, observable, pattern, promise, sagaAction, string, stringableFunc, symbol, task, undef */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "array", function() { return array; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "buffer", function() { return buffer; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "channel", function() { return channel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "effect", function() { return effect; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "func", function() { return func; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "iterable", function() { return iterable; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "iterator", function() { return iterator; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "multicast", function() { return multicast; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "notUndef", function() { return notUndef; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "number", function() { return number; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "object", function() { return object; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "observable", function() { return observable; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "pattern", function() { return pattern; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "promise", function() { return promise; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "sagaAction", function() { return sagaAction; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "string", function() { return string; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "stringableFunc", function() { return stringableFunc; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "symbol", function() { return symbol; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "task", function() { return task; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "undef", function() { return undef; });
/* harmony import */ var _redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @redux-saga/symbols */ "./node_modules/@redux-saga/symbols/dist/redux-saga-symbols.esm.js");


var undef = function undef(v) {
  return v === null || v === undefined;
};
var notUndef = function notUndef(v) {
  return v !== null && v !== undefined;
};
var func = function func(f) {
  return typeof f === 'function';
};
var number = function number(n) {
  return typeof n === 'number';
};
var string = function string(s) {
  return typeof s === 'string';
};
var array = Array.isArray;
var object = function object(obj) {
  return obj && !array(obj) && typeof obj === 'object';
};
var promise = function promise(p) {
  return p && func(p.then);
};
var iterator = function iterator(it) {
  return it && func(it.next) && func(it.throw);
};
var iterable = function iterable(it) {
  return it && func(Symbol) ? func(it[Symbol.iterator]) : array(it);
};
var task = function task(t) {
  return t && t[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["TASK"]];
};
var sagaAction = function sagaAction(a) {
  return Boolean(a && a[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["SAGA_ACTION"]]);
};
var observable = function observable(ob) {
  return ob && func(ob.subscribe);
};
var buffer = function buffer(buf) {
  return buf && func(buf.isEmpty) && func(buf.take) && func(buf.put);
};
var pattern = function pattern(pat) {
  return pat && (string(pat) || symbol(pat) || func(pat) || array(pat) && pat.every(pattern));
};
var channel = function channel(ch) {
  return ch && func(ch.take) && func(ch.close);
};
var stringableFunc = function stringableFunc(f) {
  return func(f) && f.hasOwnProperty('toString');
};
var symbol = function symbol(sym) {
  return Boolean(sym) && typeof Symbol === 'function' && sym.constructor === Symbol && sym !== Symbol.prototype;
};
var multicast = function multicast(ch) {
  return channel(ch) && ch[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["MULTICAST"]];
};
var effect = function effect(eff) {
  return eff && eff[_redux_saga_symbols__WEBPACK_IMPORTED_MODULE_0__["IO"]];
};




/***/ }),

/***/ "./node_modules/@redux-saga/symbols/dist/redux-saga-symbols.esm.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@redux-saga/symbols/dist/redux-saga-symbols.esm.js ***!
  \*************************************************************************/
/*! exports provided: CANCEL, CHANNEL_END_TYPE, IO, MATCH, MULTICAST, SAGA_ACTION, SAGA_LOCATION, SELF_CANCELLATION, TASK, TASK_CANCEL, TERMINATE */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "CANCEL", function() { return CANCEL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "CHANNEL_END_TYPE", function() { return CHANNEL_END_TYPE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "IO", function() { return IO; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "MATCH", function() { return MATCH; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "MULTICAST", function() { return MULTICAST; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SAGA_ACTION", function() { return SAGA_ACTION; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SAGA_LOCATION", function() { return SAGA_LOCATION; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SELF_CANCELLATION", function() { return SELF_CANCELLATION; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TASK", function() { return TASK; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TASK_CANCEL", function() { return TASK_CANCEL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TERMINATE", function() { return TERMINATE; });
var createSymbol = function createSymbol(name) {
  return "@@redux-saga/" + name;
};

var CANCEL =
/*#__PURE__*/
createSymbol('CANCEL_PROMISE');
var CHANNEL_END_TYPE =
/*#__PURE__*/
createSymbol('CHANNEL_END');
var IO =
/*#__PURE__*/
createSymbol('IO');
var MATCH =
/*#__PURE__*/
createSymbol('MATCH');
var MULTICAST =
/*#__PURE__*/
createSymbol('MULTICAST');
var SAGA_ACTION =
/*#__PURE__*/
createSymbol('SAGA_ACTION');
var SELF_CANCELLATION =
/*#__PURE__*/
createSymbol('SELF_CANCELLATION');
var TASK =
/*#__PURE__*/
createSymbol('TASK');
var TASK_CANCEL =
/*#__PURE__*/
createSymbol('TASK_CANCEL');
var TERMINATE =
/*#__PURE__*/
createSymbol('TERMINATE');
var SAGA_LOCATION =
/*#__PURE__*/
createSymbol('LOCATION');




/***/ }),

/***/ "./node_modules/hoist-non-react-statics/dist/hoist-non-react-statics.cjs.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/hoist-non-react-statics/dist/hoist-non-react-statics.cjs.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var reactIs = __webpack_require__(/*! react-is */ "./node_modules/react-is/index.js");

/**
 * Copyright 2015, Yahoo! Inc.
 * Copyrights licensed under the New BSD License. See the accompanying LICENSE file for terms.
 */
var REACT_STATICS = {
  childContextTypes: true,
  contextType: true,
  contextTypes: true,
  defaultProps: true,
  displayName: true,
  getDefaultProps: true,
  getDerivedStateFromError: true,
  getDerivedStateFromProps: true,
  mixins: true,
  propTypes: true,
  type: true
};
var KNOWN_STATICS = {
  name: true,
  length: true,
  prototype: true,
  caller: true,
  callee: true,
  arguments: true,
  arity: true
};
var FORWARD_REF_STATICS = {
  '$$typeof': true,
  render: true,
  defaultProps: true,
  displayName: true,
  propTypes: true
};
var MEMO_STATICS = {
  '$$typeof': true,
  compare: true,
  defaultProps: true,
  displayName: true,
  propTypes: true,
  type: true
};
var TYPE_STATICS = {};
TYPE_STATICS[reactIs.ForwardRef] = FORWARD_REF_STATICS;
TYPE_STATICS[reactIs.Memo] = MEMO_STATICS;

function getStatics(component) {
  // React v16.11 and below
  if (reactIs.isMemo(component)) {
    return MEMO_STATICS;
  } // React v16.12 and above


  return TYPE_STATICS[component['$$typeof']] || REACT_STATICS;
}

var defineProperty = Object.defineProperty;
var getOwnPropertyNames = Object.getOwnPropertyNames;
var getOwnPropertySymbols = Object.getOwnPropertySymbols;
var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;
var getPrototypeOf = Object.getPrototypeOf;
var objectPrototype = Object.prototype;
function hoistNonReactStatics(targetComponent, sourceComponent, blacklist) {
  if (typeof sourceComponent !== 'string') {
    // don't hoist over string (html) components
    if (objectPrototype) {
      var inheritedComponent = getPrototypeOf(sourceComponent);

      if (inheritedComponent && inheritedComponent !== objectPrototype) {
        hoistNonReactStatics(targetComponent, inheritedComponent, blacklist);
      }
    }

    var keys = getOwnPropertyNames(sourceComponent);

    if (getOwnPropertySymbols) {
      keys = keys.concat(getOwnPropertySymbols(sourceComponent));
    }

    var targetStatics = getStatics(targetComponent);
    var sourceStatics = getStatics(sourceComponent);

    for (var i = 0; i < keys.length; ++i) {
      var key = keys[i];

      if (!KNOWN_STATICS[key] && !(blacklist && blacklist[key]) && !(sourceStatics && sourceStatics[key]) && !(targetStatics && targetStatics[key])) {
        var descriptor = getOwnPropertyDescriptor(sourceComponent, key);

        try {
          // Avoid failures from read-only properties
          defineProperty(targetComponent, key, descriptor);
        } catch (e) {}
      }
    }
  }

  return targetComponent;
}

module.exports = hoistNonReactStatics;


/***/ }),

/***/ "./node_modules/immutable/dist/immutable.js":
/*!**************************************************!*\
  !*** ./node_modules/immutable/dist/immutable.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

(function (global, factory) {
   true ? module.exports = factory() :
  undefined;
}(this, function () { 'use strict';var SLICE$0 = Array.prototype.slice;

  function createClass(ctor, superClass) {
    if (superClass) {
      ctor.prototype = Object.create(superClass.prototype);
    }
    ctor.prototype.constructor = ctor;
  }

  function Iterable(value) {
      return isIterable(value) ? value : Seq(value);
    }


  createClass(KeyedIterable, Iterable);
    function KeyedIterable(value) {
      return isKeyed(value) ? value : KeyedSeq(value);
    }


  createClass(IndexedIterable, Iterable);
    function IndexedIterable(value) {
      return isIndexed(value) ? value : IndexedSeq(value);
    }


  createClass(SetIterable, Iterable);
    function SetIterable(value) {
      return isIterable(value) && !isAssociative(value) ? value : SetSeq(value);
    }



  function isIterable(maybeIterable) {
    return !!(maybeIterable && maybeIterable[IS_ITERABLE_SENTINEL]);
  }

  function isKeyed(maybeKeyed) {
    return !!(maybeKeyed && maybeKeyed[IS_KEYED_SENTINEL]);
  }

  function isIndexed(maybeIndexed) {
    return !!(maybeIndexed && maybeIndexed[IS_INDEXED_SENTINEL]);
  }

  function isAssociative(maybeAssociative) {
    return isKeyed(maybeAssociative) || isIndexed(maybeAssociative);
  }

  function isOrdered(maybeOrdered) {
    return !!(maybeOrdered && maybeOrdered[IS_ORDERED_SENTINEL]);
  }

  Iterable.isIterable = isIterable;
  Iterable.isKeyed = isKeyed;
  Iterable.isIndexed = isIndexed;
  Iterable.isAssociative = isAssociative;
  Iterable.isOrdered = isOrdered;

  Iterable.Keyed = KeyedIterable;
  Iterable.Indexed = IndexedIterable;
  Iterable.Set = SetIterable;


  var IS_ITERABLE_SENTINEL = '@@__IMMUTABLE_ITERABLE__@@';
  var IS_KEYED_SENTINEL = '@@__IMMUTABLE_KEYED__@@';
  var IS_INDEXED_SENTINEL = '@@__IMMUTABLE_INDEXED__@@';
  var IS_ORDERED_SENTINEL = '@@__IMMUTABLE_ORDERED__@@';

  // Used for setting prototype methods that IE8 chokes on.
  var DELETE = 'delete';

  // Constants describing the size of trie nodes.
  var SHIFT = 5; // Resulted in best performance after ______?
  var SIZE = 1 << SHIFT;
  var MASK = SIZE - 1;

  // A consistent shared value representing "not set" which equals nothing other
  // than itself, and nothing that could be provided externally.
  var NOT_SET = {};

  // Boolean references, Rough equivalent of `bool &`.
  var CHANGE_LENGTH = { value: false };
  var DID_ALTER = { value: false };

  function MakeRef(ref) {
    ref.value = false;
    return ref;
  }

  function SetRef(ref) {
    ref && (ref.value = true);
  }

  // A function which returns a value representing an "owner" for transient writes
  // to tries. The return value will only ever equal itself, and will not equal
  // the return of any subsequent call of this function.
  function OwnerID() {}

  // http://jsperf.com/copy-array-inline
  function arrCopy(arr, offset) {
    offset = offset || 0;
    var len = Math.max(0, arr.length - offset);
    var newArr = new Array(len);
    for (var ii = 0; ii < len; ii++) {
      newArr[ii] = arr[ii + offset];
    }
    return newArr;
  }

  function ensureSize(iter) {
    if (iter.size === undefined) {
      iter.size = iter.__iterate(returnTrue);
    }
    return iter.size;
  }

  function wrapIndex(iter, index) {
    // This implements "is array index" which the ECMAString spec defines as:
    //
    //     A String property name P is an array index if and only if
    //     ToString(ToUint32(P)) is equal to P and ToUint32(P) is not equal
    //     to 2^32−1.
    //
    // http://www.ecma-international.org/ecma-262/6.0/#sec-array-exotic-objects
    if (typeof index !== 'number') {
      var uint32Index = index >>> 0; // N >>> 0 is shorthand for ToUint32
      if ('' + uint32Index !== index || uint32Index === 4294967295) {
        return NaN;
      }
      index = uint32Index;
    }
    return index < 0 ? ensureSize(iter) + index : index;
  }

  function returnTrue() {
    return true;
  }

  function wholeSlice(begin, end, size) {
    return (begin === 0 || (size !== undefined && begin <= -size)) &&
      (end === undefined || (size !== undefined && end >= size));
  }

  function resolveBegin(begin, size) {
    return resolveIndex(begin, size, 0);
  }

  function resolveEnd(end, size) {
    return resolveIndex(end, size, size);
  }

  function resolveIndex(index, size, defaultIndex) {
    return index === undefined ?
      defaultIndex :
      index < 0 ?
        Math.max(0, size + index) :
        size === undefined ?
          index :
          Math.min(size, index);
  }

  /* global Symbol */

  var ITERATE_KEYS = 0;
  var ITERATE_VALUES = 1;
  var ITERATE_ENTRIES = 2;

  var REAL_ITERATOR_SYMBOL = typeof Symbol === 'function' && Symbol.iterator;
  var FAUX_ITERATOR_SYMBOL = '@@iterator';

  var ITERATOR_SYMBOL = REAL_ITERATOR_SYMBOL || FAUX_ITERATOR_SYMBOL;


  function Iterator(next) {
      this.next = next;
    }

    Iterator.prototype.toString = function() {
      return '[Iterator]';
    };


  Iterator.KEYS = ITERATE_KEYS;
  Iterator.VALUES = ITERATE_VALUES;
  Iterator.ENTRIES = ITERATE_ENTRIES;

  Iterator.prototype.inspect =
  Iterator.prototype.toSource = function () { return this.toString(); }
  Iterator.prototype[ITERATOR_SYMBOL] = function () {
    return this;
  };


  function iteratorValue(type, k, v, iteratorResult) {
    var value = type === 0 ? k : type === 1 ? v : [k, v];
    iteratorResult ? (iteratorResult.value = value) : (iteratorResult = {
      value: value, done: false
    });
    return iteratorResult;
  }

  function iteratorDone() {
    return { value: undefined, done: true };
  }

  function hasIterator(maybeIterable) {
    return !!getIteratorFn(maybeIterable);
  }

  function isIterator(maybeIterator) {
    return maybeIterator && typeof maybeIterator.next === 'function';
  }

  function getIterator(iterable) {
    var iteratorFn = getIteratorFn(iterable);
    return iteratorFn && iteratorFn.call(iterable);
  }

  function getIteratorFn(iterable) {
    var iteratorFn = iterable && (
      (REAL_ITERATOR_SYMBOL && iterable[REAL_ITERATOR_SYMBOL]) ||
      iterable[FAUX_ITERATOR_SYMBOL]
    );
    if (typeof iteratorFn === 'function') {
      return iteratorFn;
    }
  }

  function isArrayLike(value) {
    return value && typeof value.length === 'number';
  }

  createClass(Seq, Iterable);
    function Seq(value) {
      return value === null || value === undefined ? emptySequence() :
        isIterable(value) ? value.toSeq() : seqFromValue(value);
    }

    Seq.of = function(/*...values*/) {
      return Seq(arguments);
    };

    Seq.prototype.toSeq = function() {
      return this;
    };

    Seq.prototype.toString = function() {
      return this.__toString('Seq {', '}');
    };

    Seq.prototype.cacheResult = function() {
      if (!this._cache && this.__iterateUncached) {
        this._cache = this.entrySeq().toArray();
        this.size = this._cache.length;
      }
      return this;
    };

    // abstract __iterateUncached(fn, reverse)

    Seq.prototype.__iterate = function(fn, reverse) {
      return seqIterate(this, fn, reverse, true);
    };

    // abstract __iteratorUncached(type, reverse)

    Seq.prototype.__iterator = function(type, reverse) {
      return seqIterator(this, type, reverse, true);
    };



  createClass(KeyedSeq, Seq);
    function KeyedSeq(value) {
      return value === null || value === undefined ?
        emptySequence().toKeyedSeq() :
        isIterable(value) ?
          (isKeyed(value) ? value.toSeq() : value.fromEntrySeq()) :
          keyedSeqFromValue(value);
    }

    KeyedSeq.prototype.toKeyedSeq = function() {
      return this;
    };



  createClass(IndexedSeq, Seq);
    function IndexedSeq(value) {
      return value === null || value === undefined ? emptySequence() :
        !isIterable(value) ? indexedSeqFromValue(value) :
        isKeyed(value) ? value.entrySeq() : value.toIndexedSeq();
    }

    IndexedSeq.of = function(/*...values*/) {
      return IndexedSeq(arguments);
    };

    IndexedSeq.prototype.toIndexedSeq = function() {
      return this;
    };

    IndexedSeq.prototype.toString = function() {
      return this.__toString('Seq [', ']');
    };

    IndexedSeq.prototype.__iterate = function(fn, reverse) {
      return seqIterate(this, fn, reverse, false);
    };

    IndexedSeq.prototype.__iterator = function(type, reverse) {
      return seqIterator(this, type, reverse, false);
    };



  createClass(SetSeq, Seq);
    function SetSeq(value) {
      return (
        value === null || value === undefined ? emptySequence() :
        !isIterable(value) ? indexedSeqFromValue(value) :
        isKeyed(value) ? value.entrySeq() : value
      ).toSetSeq();
    }

    SetSeq.of = function(/*...values*/) {
      return SetSeq(arguments);
    };

    SetSeq.prototype.toSetSeq = function() {
      return this;
    };



  Seq.isSeq = isSeq;
  Seq.Keyed = KeyedSeq;
  Seq.Set = SetSeq;
  Seq.Indexed = IndexedSeq;

  var IS_SEQ_SENTINEL = '@@__IMMUTABLE_SEQ__@@';

  Seq.prototype[IS_SEQ_SENTINEL] = true;



  createClass(ArraySeq, IndexedSeq);
    function ArraySeq(array) {
      this._array = array;
      this.size = array.length;
    }

    ArraySeq.prototype.get = function(index, notSetValue) {
      return this.has(index) ? this._array[wrapIndex(this, index)] : notSetValue;
    };

    ArraySeq.prototype.__iterate = function(fn, reverse) {
      var array = this._array;
      var maxIndex = array.length - 1;
      for (var ii = 0; ii <= maxIndex; ii++) {
        if (fn(array[reverse ? maxIndex - ii : ii], ii, this) === false) {
          return ii + 1;
        }
      }
      return ii;
    };

    ArraySeq.prototype.__iterator = function(type, reverse) {
      var array = this._array;
      var maxIndex = array.length - 1;
      var ii = 0;
      return new Iterator(function() 
        {return ii > maxIndex ?
          iteratorDone() :
          iteratorValue(type, ii, array[reverse ? maxIndex - ii++ : ii++])}
      );
    };



  createClass(ObjectSeq, KeyedSeq);
    function ObjectSeq(object) {
      var keys = Object.keys(object);
      this._object = object;
      this._keys = keys;
      this.size = keys.length;
    }

    ObjectSeq.prototype.get = function(key, notSetValue) {
      if (notSetValue !== undefined && !this.has(key)) {
        return notSetValue;
      }
      return this._object[key];
    };

    ObjectSeq.prototype.has = function(key) {
      return this._object.hasOwnProperty(key);
    };

    ObjectSeq.prototype.__iterate = function(fn, reverse) {
      var object = this._object;
      var keys = this._keys;
      var maxIndex = keys.length - 1;
      for (var ii = 0; ii <= maxIndex; ii++) {
        var key = keys[reverse ? maxIndex - ii : ii];
        if (fn(object[key], key, this) === false) {
          return ii + 1;
        }
      }
      return ii;
    };

    ObjectSeq.prototype.__iterator = function(type, reverse) {
      var object = this._object;
      var keys = this._keys;
      var maxIndex = keys.length - 1;
      var ii = 0;
      return new Iterator(function()  {
        var key = keys[reverse ? maxIndex - ii : ii];
        return ii++ > maxIndex ?
          iteratorDone() :
          iteratorValue(type, key, object[key]);
      });
    };

  ObjectSeq.prototype[IS_ORDERED_SENTINEL] = true;


  createClass(IterableSeq, IndexedSeq);
    function IterableSeq(iterable) {
      this._iterable = iterable;
      this.size = iterable.length || iterable.size;
    }

    IterableSeq.prototype.__iterateUncached = function(fn, reverse) {
      if (reverse) {
        return this.cacheResult().__iterate(fn, reverse);
      }
      var iterable = this._iterable;
      var iterator = getIterator(iterable);
      var iterations = 0;
      if (isIterator(iterator)) {
        var step;
        while (!(step = iterator.next()).done) {
          if (fn(step.value, iterations++, this) === false) {
            break;
          }
        }
      }
      return iterations;
    };

    IterableSeq.prototype.__iteratorUncached = function(type, reverse) {
      if (reverse) {
        return this.cacheResult().__iterator(type, reverse);
      }
      var iterable = this._iterable;
      var iterator = getIterator(iterable);
      if (!isIterator(iterator)) {
        return new Iterator(iteratorDone);
      }
      var iterations = 0;
      return new Iterator(function()  {
        var step = iterator.next();
        return step.done ? step : iteratorValue(type, iterations++, step.value);
      });
    };



  createClass(IteratorSeq, IndexedSeq);
    function IteratorSeq(iterator) {
      this._iterator = iterator;
      this._iteratorCache = [];
    }

    IteratorSeq.prototype.__iterateUncached = function(fn, reverse) {
      if (reverse) {
        return this.cacheResult().__iterate(fn, reverse);
      }
      var iterator = this._iterator;
      var cache = this._iteratorCache;
      var iterations = 0;
      while (iterations < cache.length) {
        if (fn(cache[iterations], iterations++, this) === false) {
          return iterations;
        }
      }
      var step;
      while (!(step = iterator.next()).done) {
        var val = step.value;
        cache[iterations] = val;
        if (fn(val, iterations++, this) === false) {
          break;
        }
      }
      return iterations;
    };

    IteratorSeq.prototype.__iteratorUncached = function(type, reverse) {
      if (reverse) {
        return this.cacheResult().__iterator(type, reverse);
      }
      var iterator = this._iterator;
      var cache = this._iteratorCache;
      var iterations = 0;
      return new Iterator(function()  {
        if (iterations >= cache.length) {
          var step = iterator.next();
          if (step.done) {
            return step;
          }
          cache[iterations] = step.value;
        }
        return iteratorValue(type, iterations, cache[iterations++]);
      });
    };




  // # pragma Helper functions

  function isSeq(maybeSeq) {
    return !!(maybeSeq && maybeSeq[IS_SEQ_SENTINEL]);
  }

  var EMPTY_SEQ;

  function emptySequence() {
    return EMPTY_SEQ || (EMPTY_SEQ = new ArraySeq([]));
  }

  function keyedSeqFromValue(value) {
    var seq =
      Array.isArray(value) ? new ArraySeq(value).fromEntrySeq() :
      isIterator(value) ? new IteratorSeq(value).fromEntrySeq() :
      hasIterator(value) ? new IterableSeq(value).fromEntrySeq() :
      typeof value === 'object' ? new ObjectSeq(value) :
      undefined;
    if (!seq) {
      throw new TypeError(
        'Expected Array or iterable object of [k, v] entries, '+
        'or keyed object: ' + value
      );
    }
    return seq;
  }

  function indexedSeqFromValue(value) {
    var seq = maybeIndexedSeqFromValue(value);
    if (!seq) {
      throw new TypeError(
        'Expected Array or iterable object of values: ' + value
      );
    }
    return seq;
  }

  function seqFromValue(value) {
    var seq = maybeIndexedSeqFromValue(value) ||
      (typeof value === 'object' && new ObjectSeq(value));
    if (!seq) {
      throw new TypeError(
        'Expected Array or iterable object of values, or keyed object: ' + value
      );
    }
    return seq;
  }

  function maybeIndexedSeqFromValue(value) {
    return (
      isArrayLike(value) ? new ArraySeq(value) :
      isIterator(value) ? new IteratorSeq(value) :
      hasIterator(value) ? new IterableSeq(value) :
      undefined
    );
  }

  function seqIterate(seq, fn, reverse, useKeys) {
    var cache = seq._cache;
    if (cache) {
      var maxIndex = cache.length - 1;
      for (var ii = 0; ii <= maxIndex; ii++) {
        var entry = cache[reverse ? maxIndex - ii : ii];
        if (fn(entry[1], useKeys ? entry[0] : ii, seq) === false) {
          return ii + 1;
        }
      }
      return ii;
    }
    return seq.__iterateUncached(fn, reverse);
  }

  function seqIterator(seq, type, reverse, useKeys) {
    var cache = seq._cache;
    if (cache) {
      var maxIndex = cache.length - 1;
      var ii = 0;
      return new Iterator(function()  {
        var entry = cache[reverse ? maxIndex - ii : ii];
        return ii++ > maxIndex ?
          iteratorDone() :
          iteratorValue(type, useKeys ? entry[0] : ii - 1, entry[1]);
      });
    }
    return seq.__iteratorUncached(type, reverse);
  }

  function fromJS(json, converter) {
    return converter ?
      fromJSWith(converter, json, '', {'': json}) :
      fromJSDefault(json);
  }

  function fromJSWith(converter, json, key, parentJSON) {
    if (Array.isArray(json)) {
      return converter.call(parentJSON, key, IndexedSeq(json).map(function(v, k)  {return fromJSWith(converter, v, k, json)}));
    }
    if (isPlainObj(json)) {
      return converter.call(parentJSON, key, KeyedSeq(json).map(function(v, k)  {return fromJSWith(converter, v, k, json)}));
    }
    return json;
  }

  function fromJSDefault(json) {
    if (Array.isArray(json)) {
      return IndexedSeq(json).map(fromJSDefault).toList();
    }
    if (isPlainObj(json)) {
      return KeyedSeq(json).map(fromJSDefault).toMap();
    }
    return json;
  }

  function isPlainObj(value) {
    return value && (value.constructor === Object || value.constructor === undefined);
  }

  /**
   * An extension of the "same-value" algorithm as [described for use by ES6 Map
   * and Set](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Map#Key_equality)
   *
   * NaN is considered the same as NaN, however -0 and 0 are considered the same
   * value, which is different from the algorithm described by
   * [`Object.is`](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/is).
   *
   * This is extended further to allow Objects to describe the values they
   * represent, by way of `valueOf` or `equals` (and `hashCode`).
   *
   * Note: because of this extension, the key equality of Immutable.Map and the
   * value equality of Immutable.Set will differ from ES6 Map and Set.
   *
   * ### Defining custom values
   *
   * The easiest way to describe the value an object represents is by implementing
   * `valueOf`. For example, `Date` represents a value by returning a unix
   * timestamp for `valueOf`:
   *
   *     var date1 = new Date(1234567890000); // Fri Feb 13 2009 ...
   *     var date2 = new Date(1234567890000);
   *     date1.valueOf(); // 1234567890000
   *     assert( date1 !== date2 );
   *     assert( Immutable.is( date1, date2 ) );
   *
   * Note: overriding `valueOf` may have other implications if you use this object
   * where JavaScript expects a primitive, such as implicit string coercion.
   *
   * For more complex types, especially collections, implementing `valueOf` may
   * not be performant. An alternative is to implement `equals` and `hashCode`.
   *
   * `equals` takes another object, presumably of similar type, and returns true
   * if the it is equal. Equality is symmetrical, so the same result should be
   * returned if this and the argument are flipped.
   *
   *     assert( a.equals(b) === b.equals(a) );
   *
   * `hashCode` returns a 32bit integer number representing the object which will
   * be used to determine how to store the value object in a Map or Set. You must
   * provide both or neither methods, one must not exist without the other.
   *
   * Also, an important relationship between these methods must be upheld: if two
   * values are equal, they *must* return the same hashCode. If the values are not
   * equal, they might have the same hashCode; this is called a hash collision,
   * and while undesirable for performance reasons, it is acceptable.
   *
   *     if (a.equals(b)) {
   *       assert( a.hashCode() === b.hashCode() );
   *     }
   *
   * All Immutable collections implement `equals` and `hashCode`.
   *
   */
  function is(valueA, valueB) {
    if (valueA === valueB || (valueA !== valueA && valueB !== valueB)) {
      return true;
    }
    if (!valueA || !valueB) {
      return false;
    }
    if (typeof valueA.valueOf === 'function' &&
        typeof valueB.valueOf === 'function') {
      valueA = valueA.valueOf();
      valueB = valueB.valueOf();
      if (valueA === valueB || (valueA !== valueA && valueB !== valueB)) {
        return true;
      }
      if (!valueA || !valueB) {
        return false;
      }
    }
    if (typeof valueA.equals === 'function' &&
        typeof valueB.equals === 'function' &&
        valueA.equals(valueB)) {
      return true;
    }
    return false;
  }

  function deepEqual(a, b) {
    if (a === b) {
      return true;
    }

    if (
      !isIterable(b) ||
      a.size !== undefined && b.size !== undefined && a.size !== b.size ||
      a.__hash !== undefined && b.__hash !== undefined && a.__hash !== b.__hash ||
      isKeyed(a) !== isKeyed(b) ||
      isIndexed(a) !== isIndexed(b) ||
      isOrdered(a) !== isOrdered(b)
    ) {
      return false;
    }

    if (a.size === 0 && b.size === 0) {
      return true;
    }

    var notAssociative = !isAssociative(a);

    if (isOrdered(a)) {
      var entries = a.entries();
      return b.every(function(v, k)  {
        var entry = entries.next().value;
        return entry && is(entry[1], v) && (notAssociative || is(entry[0], k));
      }) && entries.next().done;
    }

    var flipped = false;

    if (a.size === undefined) {
      if (b.size === undefined) {
        if (typeof a.cacheResult === 'function') {
          a.cacheResult();
        }
      } else {
        flipped = true;
        var _ = a;
        a = b;
        b = _;
      }
    }

    var allEqual = true;
    var bSize = b.__iterate(function(v, k)  {
      if (notAssociative ? !a.has(v) :
          flipped ? !is(v, a.get(k, NOT_SET)) : !is(a.get(k, NOT_SET), v)) {
        allEqual = false;
        return false;
      }
    });

    return allEqual && a.size === bSize;
  }

  createClass(Repeat, IndexedSeq);

    function Repeat(value, times) {
      if (!(this instanceof Repeat)) {
        return new Repeat(value, times);
      }
      this._value = value;
      this.size = times === undefined ? Infinity : Math.max(0, times);
      if (this.size === 0) {
        if (EMPTY_REPEAT) {
          return EMPTY_REPEAT;
        }
        EMPTY_REPEAT = this;
      }
    }

    Repeat.prototype.toString = function() {
      if (this.size === 0) {
        return 'Repeat []';
      }
      return 'Repeat [ ' + this._value + ' ' + this.size + ' times ]';
    };

    Repeat.prototype.get = function(index, notSetValue) {
      return this.has(index) ? this._value : notSetValue;
    };

    Repeat.prototype.includes = function(searchValue) {
      return is(this._value, searchValue);
    };

    Repeat.prototype.slice = function(begin, end) {
      var size = this.size;
      return wholeSlice(begin, end, size) ? this :
        new Repeat(this._value, resolveEnd(end, size) - resolveBegin(begin, size));
    };

    Repeat.prototype.reverse = function() {
      return this;
    };

    Repeat.prototype.indexOf = function(searchValue) {
      if (is(this._value, searchValue)) {
        return 0;
      }
      return -1;
    };

    Repeat.prototype.lastIndexOf = function(searchValue) {
      if (is(this._value, searchValue)) {
        return this.size;
      }
      return -1;
    };

    Repeat.prototype.__iterate = function(fn, reverse) {
      for (var ii = 0; ii < this.size; ii++) {
        if (fn(this._value, ii, this) === false) {
          return ii + 1;
        }
      }
      return ii;
    };

    Repeat.prototype.__iterator = function(type, reverse) {var this$0 = this;
      var ii = 0;
      return new Iterator(function() 
        {return ii < this$0.size ? iteratorValue(type, ii++, this$0._value) : iteratorDone()}
      );
    };

    Repeat.prototype.equals = function(other) {
      return other instanceof Repeat ?
        is(this._value, other._value) :
        deepEqual(other);
    };


  var EMPTY_REPEAT;

  function invariant(condition, error) {
    if (!condition) throw new Error(error);
  }

  createClass(Range, IndexedSeq);

    function Range(start, end, step) {
      if (!(this instanceof Range)) {
        return new Range(start, end, step);
      }
      invariant(step !== 0, 'Cannot step a Range by 0');
      start = start || 0;
      if (end === undefined) {
        end = Infinity;
      }
      step = step === undefined ? 1 : Math.abs(step);
      if (end < start) {
        step = -step;
      }
      this._start = start;
      this._end = end;
      this._step = step;
      this.size = Math.max(0, Math.ceil((end - start) / step - 1) + 1);
      if (this.size === 0) {
        if (EMPTY_RANGE) {
          return EMPTY_RANGE;
        }
        EMPTY_RANGE = this;
      }
    }

    Range.prototype.toString = function() {
      if (this.size === 0) {
        return 'Range []';
      }
      return 'Range [ ' +
        this._start + '...' + this._end +
        (this._step !== 1 ? ' by ' + this._step : '') +
      ' ]';
    };

    Range.prototype.get = function(index, notSetValue) {
      return this.has(index) ?
        this._start + wrapIndex(this, index) * this._step :
        notSetValue;
    };

    Range.prototype.includes = function(searchValue) {
      var possibleIndex = (searchValue - this._start) / this._step;
      return possibleIndex >= 0 &&
        possibleIndex < this.size &&
        possibleIndex === Math.floor(possibleIndex);
    };

    Range.prototype.slice = function(begin, end) {
      if (wholeSlice(begin, end, this.size)) {
        return this;
      }
      begin = resolveBegin(begin, this.size);
      end = resolveEnd(end, this.size);
      if (end <= begin) {
        return new Range(0, 0);
      }
      return new Range(this.get(begin, this._end), this.get(end, this._end), this._step);
    };

    Range.prototype.indexOf = function(searchValue) {
      var offsetValue = searchValue - this._start;
      if (offsetValue % this._step === 0) {
        var index = offsetValue / this._step;
        if (index >= 0 && index < this.size) {
          return index
        }
      }
      return -1;
    };

    Range.prototype.lastIndexOf = function(searchValue) {
      return this.indexOf(searchValue);
    };

    Range.prototype.__iterate = function(fn, reverse) {
      var maxIndex = this.size - 1;
      var step = this._step;
      var value = reverse ? this._start + maxIndex * step : this._start;
      for (var ii = 0; ii <= maxIndex; ii++) {
        if (fn(value, ii, this) === false) {
          return ii + 1;
        }
        value += reverse ? -step : step;
      }
      return ii;
    };

    Range.prototype.__iterator = function(type, reverse) {
      var maxIndex = this.size - 1;
      var step = this._step;
      var value = reverse ? this._start + maxIndex * step : this._start;
      var ii = 0;
      return new Iterator(function()  {
        var v = value;
        value += reverse ? -step : step;
        return ii > maxIndex ? iteratorDone() : iteratorValue(type, ii++, v);
      });
    };

    Range.prototype.equals = function(other) {
      return other instanceof Range ?
        this._start === other._start &&
        this._end === other._end &&
        this._step === other._step :
        deepEqual(this, other);
    };


  var EMPTY_RANGE;

  createClass(Collection, Iterable);
    function Collection() {
      throw TypeError('Abstract');
    }


  createClass(KeyedCollection, Collection);function KeyedCollection() {}

  createClass(IndexedCollection, Collection);function IndexedCollection() {}

  createClass(SetCollection, Collection);function SetCollection() {}


  Collection.Keyed = KeyedCollection;
  Collection.Indexed = IndexedCollection;
  Collection.Set = SetCollection;

  var imul =
    typeof Math.imul === 'function' && Math.imul(0xffffffff, 2) === -2 ?
    Math.imul :
    function imul(a, b) {
      a = a | 0; // int
      b = b | 0; // int
      var c = a & 0xffff;
      var d = b & 0xffff;
      // Shift by 0 fixes the sign on the high part.
      return (c * d) + ((((a >>> 16) * d + c * (b >>> 16)) << 16) >>> 0) | 0; // int
    };

  // v8 has an optimization for storing 31-bit signed numbers.
  // Values which have either 00 or 11 as the high order bits qualify.
  // This function drops the highest order bit in a signed number, maintaining
  // the sign bit.
  function smi(i32) {
    return ((i32 >>> 1) & 0x40000000) | (i32 & 0xBFFFFFFF);
  }

  function hash(o) {
    if (o === false || o === null || o === undefined) {
      return 0;
    }
    if (typeof o.valueOf === 'function') {
      o = o.valueOf();
      if (o === false || o === null || o === undefined) {
        return 0;
      }
    }
    if (o === true) {
      return 1;
    }
    var type = typeof o;
    if (type === 'number') {
      if (o !== o || o === Infinity) {
        return 0;
      }
      var h = o | 0;
      if (h !== o) {
        h ^= o * 0xFFFFFFFF;
      }
      while (o > 0xFFFFFFFF) {
        o /= 0xFFFFFFFF;
        h ^= o;
      }
      return smi(h);
    }
    if (type === 'string') {
      return o.length > STRING_HASH_CACHE_MIN_STRLEN ? cachedHashString(o) : hashString(o);
    }
    if (typeof o.hashCode === 'function') {
      return o.hashCode();
    }
    if (type === 'object') {
      return hashJSObj(o);
    }
    if (typeof o.toString === 'function') {
      return hashString(o.toString());
    }
    throw new Error('Value type ' + type + ' cannot be hashed.');
  }

  function cachedHashString(string) {
    var hash = stringHashCache[string];
    if (hash === undefined) {
      hash = hashString(string);
      if (STRING_HASH_CACHE_SIZE === STRING_HASH_CACHE_MAX_SIZE) {
        STRING_HASH_CACHE_SIZE = 0;
        stringHashCache = {};
      }
      STRING_HASH_CACHE_SIZE++;
      stringHashCache[string] = hash;
    }
    return hash;
  }

  // http://jsperf.com/hashing-strings
  function hashString(string) {
    // This is the hash from JVM
    // The hash code for a string is computed as
    // s[0] * 31 ^ (n - 1) + s[1] * 31 ^ (n - 2) + ... + s[n - 1],
    // where s[i] is the ith character of the string and n is the length of
    // the string. We "mod" the result to make it between 0 (inclusive) and 2^31
    // (exclusive) by dropping high bits.
    var hash = 0;
    for (var ii = 0; ii < string.length; ii++) {
      hash = 31 * hash + string.charCodeAt(ii) | 0;
    }
    return smi(hash);
  }

  function hashJSObj(obj) {
    var hash;
    if (usingWeakMap) {
      hash = weakMap.get(obj);
      if (hash !== undefined) {
        return hash;
      }
    }

    hash = obj[UID_HASH_KEY];
    if (hash !== undefined) {
      return hash;
    }

    if (!canDefineProperty) {
      hash = obj.propertyIsEnumerable && obj.propertyIsEnumerable[UID_HASH_KEY];
      if (hash !== undefined) {
        return hash;
      }

      hash = getIENodeHash(obj);
      if (hash !== undefined) {
        return hash;
      }
    }

    hash = ++objHashUID;
    if (objHashUID & 0x40000000) {
      objHashUID = 0;
    }

    if (usingWeakMap) {
      weakMap.set(obj, hash);
    } else if (isExtensible !== undefined && isExtensible(obj) === false) {
      throw new Error('Non-extensible objects are not allowed as keys.');
    } else if (canDefineProperty) {
      Object.defineProperty(obj, UID_HASH_KEY, {
        'enumerable': false,
        'configurable': false,
        'writable': false,
        'value': hash
      });
    } else if (obj.propertyIsEnumerable !== undefined &&
               obj.propertyIsEnumerable === obj.constructor.prototype.propertyIsEnumerable) {
      // Since we can't define a non-enumerable property on the object
      // we'll hijack one of the less-used non-enumerable properties to
      // save our hash on it. Since this is a function it will not show up in
      // `JSON.stringify` which is what we want.
      obj.propertyIsEnumerable = function() {
        return this.constructor.prototype.propertyIsEnumerable.apply(this, arguments);
      };
      obj.propertyIsEnumerable[UID_HASH_KEY] = hash;
    } else if (obj.nodeType !== undefined) {
      // At this point we couldn't get the IE `uniqueID` to use as a hash
      // and we couldn't use a non-enumerable property to exploit the
      // dontEnum bug so we simply add the `UID_HASH_KEY` on the node
      // itself.
      obj[UID_HASH_KEY] = hash;
    } else {
      throw new Error('Unable to set a non-enumerable property on object.');
    }

    return hash;
  }

  // Get references to ES5 object methods.
  var isExtensible = Object.isExtensible;

  // True if Object.defineProperty works as expected. IE8 fails this test.
  var canDefineProperty = (function() {
    try {
      Object.defineProperty({}, '@', {});
      return true;
    } catch (e) {
      return false;
    }
  }());

  // IE has a `uniqueID` property on DOM nodes. We can construct the hash from it
  // and avoid memory leaks from the IE cloneNode bug.
  function getIENodeHash(node) {
    if (node && node.nodeType > 0) {
      switch (node.nodeType) {
        case 1: // Element
          return node.uniqueID;
        case 9: // Document
          return node.documentElement && node.documentElement.uniqueID;
      }
    }
  }

  // If possible, use a WeakMap.
  var usingWeakMap = typeof WeakMap === 'function';
  var weakMap;
  if (usingWeakMap) {
    weakMap = new WeakMap();
  }

  var objHashUID = 0;

  var UID_HASH_KEY = '__immutablehash__';
  if (typeof Symbol === 'function') {
    UID_HASH_KEY = Symbol(UID_HASH_KEY);
  }

  var STRING_HASH_CACHE_MIN_STRLEN = 16;
  var STRING_HASH_CACHE_MAX_SIZE = 255;
  var STRING_HASH_CACHE_SIZE = 0;
  var stringHashCache = {};

  function assertNotInfinite(size) {
    invariant(
      size !== Infinity,
      'Cannot perform this action with an infinite size.'
    );
  }

  createClass(Map, KeyedCollection);

    // @pragma Construction

    function Map(value) {
      return value === null || value === undefined ? emptyMap() :
        isMap(value) && !isOrdered(value) ? value :
        emptyMap().withMutations(function(map ) {
          var iter = KeyedIterable(value);
          assertNotInfinite(iter.size);
          iter.forEach(function(v, k)  {return map.set(k, v)});
        });
    }

    Map.of = function() {var keyValues = SLICE$0.call(arguments, 0);
      return emptyMap().withMutations(function(map ) {
        for (var i = 0; i < keyValues.length; i += 2) {
          if (i + 1 >= keyValues.length) {
            throw new Error('Missing value for key: ' + keyValues[i]);
          }
          map.set(keyValues[i], keyValues[i + 1]);
        }
      });
    };

    Map.prototype.toString = function() {
      return this.__toString('Map {', '}');
    };

    // @pragma Access

    Map.prototype.get = function(k, notSetValue) {
      return this._root ?
        this._root.get(0, undefined, k, notSetValue) :
        notSetValue;
    };

    // @pragma Modification

    Map.prototype.set = function(k, v) {
      return updateMap(this, k, v);
    };

    Map.prototype.setIn = function(keyPath, v) {
      return this.updateIn(keyPath, NOT_SET, function()  {return v});
    };

    Map.prototype.remove = function(k) {
      return updateMap(this, k, NOT_SET);
    };

    Map.prototype.deleteIn = function(keyPath) {
      return this.updateIn(keyPath, function()  {return NOT_SET});
    };

    Map.prototype.update = function(k, notSetValue, updater) {
      return arguments.length === 1 ?
        k(this) :
        this.updateIn([k], notSetValue, updater);
    };

    Map.prototype.updateIn = function(keyPath, notSetValue, updater) {
      if (!updater) {
        updater = notSetValue;
        notSetValue = undefined;
      }
      var updatedValue = updateInDeepMap(
        this,
        forceIterator(keyPath),
        notSetValue,
        updater
      );
      return updatedValue === NOT_SET ? undefined : updatedValue;
    };

    Map.prototype.clear = function() {
      if (this.size === 0) {
        return this;
      }
      if (this.__ownerID) {
        this.size = 0;
        this._root = null;
        this.__hash = undefined;
        this.__altered = true;
        return this;
      }
      return emptyMap();
    };

    // @pragma Composition

    Map.prototype.merge = function(/*...iters*/) {
      return mergeIntoMapWith(this, undefined, arguments);
    };

    Map.prototype.mergeWith = function(merger) {var iters = SLICE$0.call(arguments, 1);
      return mergeIntoMapWith(this, merger, iters);
    };

    Map.prototype.mergeIn = function(keyPath) {var iters = SLICE$0.call(arguments, 1);
      return this.updateIn(
        keyPath,
        emptyMap(),
        function(m ) {return typeof m.merge === 'function' ?
          m.merge.apply(m, iters) :
          iters[iters.length - 1]}
      );
    };

    Map.prototype.mergeDeep = function(/*...iters*/) {
      return mergeIntoMapWith(this, deepMerger, arguments);
    };

    Map.prototype.mergeDeepWith = function(merger) {var iters = SLICE$0.call(arguments, 1);
      return mergeIntoMapWith(this, deepMergerWith(merger), iters);
    };

    Map.prototype.mergeDeepIn = function(keyPath) {var iters = SLICE$0.call(arguments, 1);
      return this.updateIn(
        keyPath,
        emptyMap(),
        function(m ) {return typeof m.mergeDeep === 'function' ?
          m.mergeDeep.apply(m, iters) :
          iters[iters.length - 1]}
      );
    };

    Map.prototype.sort = function(comparator) {
      // Late binding
      return OrderedMap(sortFactory(this, comparator));
    };

    Map.prototype.sortBy = function(mapper, comparator) {
      // Late binding
      return OrderedMap(sortFactory(this, comparator, mapper));
    };

    // @pragma Mutability

    Map.prototype.withMutations = function(fn) {
      var mutable = this.asMutable();
      fn(mutable);
      return mutable.wasAltered() ? mutable.__ensureOwner(this.__ownerID) : this;
    };

    Map.prototype.asMutable = function() {
      return this.__ownerID ? this : this.__ensureOwner(new OwnerID());
    };

    Map.prototype.asImmutable = function() {
      return this.__ensureOwner();
    };

    Map.prototype.wasAltered = function() {
      return this.__altered;
    };

    Map.prototype.__iterator = function(type, reverse) {
      return new MapIterator(this, type, reverse);
    };

    Map.prototype.__iterate = function(fn, reverse) {var this$0 = this;
      var iterations = 0;
      this._root && this._root.iterate(function(entry ) {
        iterations++;
        return fn(entry[1], entry[0], this$0);
      }, reverse);
      return iterations;
    };

    Map.prototype.__ensureOwner = function(ownerID) {
      if (ownerID === this.__ownerID) {
        return this;
      }
      if (!ownerID) {
        this.__ownerID = ownerID;
        this.__altered = false;
        return this;
      }
      return makeMap(this.size, this._root, ownerID, this.__hash);
    };


  function isMap(maybeMap) {
    return !!(maybeMap && maybeMap[IS_MAP_SENTINEL]);
  }

  Map.isMap = isMap;

  var IS_MAP_SENTINEL = '@@__IMMUTABLE_MAP__@@';

  var MapPrototype = Map.prototype;
  MapPrototype[IS_MAP_SENTINEL] = true;
  MapPrototype[DELETE] = MapPrototype.remove;
  MapPrototype.removeIn = MapPrototype.deleteIn;


  // #pragma Trie Nodes



    function ArrayMapNode(ownerID, entries) {
      this.ownerID = ownerID;
      this.entries = entries;
    }

    ArrayMapNode.prototype.get = function(shift, keyHash, key, notSetValue) {
      var entries = this.entries;
      for (var ii = 0, len = entries.length; ii < len; ii++) {
        if (is(key, entries[ii][0])) {
          return entries[ii][1];
        }
      }
      return notSetValue;
    };

    ArrayMapNode.prototype.update = function(ownerID, shift, keyHash, key, value, didChangeSize, didAlter) {
      var removed = value === NOT_SET;

      var entries = this.entries;
      var idx = 0;
      for (var len = entries.length; idx < len; idx++) {
        if (is(key, entries[idx][0])) {
          break;
        }
      }
      var exists = idx < len;

      if (exists ? entries[idx][1] === value : removed) {
        return this;
      }

      SetRef(didAlter);
      (removed || !exists) && SetRef(didChangeSize);

      if (removed && entries.length === 1) {
        return; // undefined
      }

      if (!exists && !removed && entries.length >= MAX_ARRAY_MAP_SIZE) {
        return createNodes(ownerID, entries, key, value);
      }

      var isEditable = ownerID && ownerID === this.ownerID;
      var newEntries = isEditable ? entries : arrCopy(entries);

      if (exists) {
        if (removed) {
          idx === len - 1 ? newEntries.pop() : (newEntries[idx] = newEntries.pop());
        } else {
          newEntries[idx] = [key, value];
        }
      } else {
        newEntries.push([key, value]);
      }

      if (isEditable) {
        this.entries = newEntries;
        return this;
      }

      return new ArrayMapNode(ownerID, newEntries);
    };




    function BitmapIndexedNode(ownerID, bitmap, nodes) {
      this.ownerID = ownerID;
      this.bitmap = bitmap;
      this.nodes = nodes;
    }

    BitmapIndexedNode.prototype.get = function(shift, keyHash, key, notSetValue) {
      if (keyHash === undefined) {
        keyHash = hash(key);
      }
      var bit = (1 << ((shift === 0 ? keyHash : keyHash >>> shift) & MASK));
      var bitmap = this.bitmap;
      return (bitmap & bit) === 0 ? notSetValue :
        this.nodes[popCount(bitmap & (bit - 1))].get(shift + SHIFT, keyHash, key, notSetValue);
    };

    BitmapIndexedNode.prototype.update = function(ownerID, shift, keyHash, key, value, didChangeSize, didAlter) {
      if (keyHash === undefined) {
        keyHash = hash(key);
      }
      var keyHashFrag = (shift === 0 ? keyHash : keyHash >>> shift) & MASK;
      var bit = 1 << keyHashFrag;
      var bitmap = this.bitmap;
      var exists = (bitmap & bit) !== 0;

      if (!exists && value === NOT_SET) {
        return this;
      }

      var idx = popCount(bitmap & (bit - 1));
      var nodes = this.nodes;
      var node = exists ? nodes[idx] : undefined;
      var newNode = updateNode(node, ownerID, shift + SHIFT, keyHash, key, value, didChangeSize, didAlter);

      if (newNode === node) {
        return this;
      }

      if (!exists && newNode && nodes.length >= MAX_BITMAP_INDEXED_SIZE) {
        return expandNodes(ownerID, nodes, bitmap, keyHashFrag, newNode);
      }

      if (exists && !newNode && nodes.length === 2 && isLeafNode(nodes[idx ^ 1])) {
        return nodes[idx ^ 1];
      }

      if (exists && newNode && nodes.length === 1 && isLeafNode(newNode)) {
        return newNode;
      }

      var isEditable = ownerID && ownerID === this.ownerID;
      var newBitmap = exists ? newNode ? bitmap : bitmap ^ bit : bitmap | bit;
      var newNodes = exists ? newNode ?
        setIn(nodes, idx, newNode, isEditable) :
        spliceOut(nodes, idx, isEditable) :
        spliceIn(nodes, idx, newNode, isEditable);

      if (isEditable) {
        this.bitmap = newBitmap;
        this.nodes = newNodes;
        return this;
      }

      return new BitmapIndexedNode(ownerID, newBitmap, newNodes);
    };




    function HashArrayMapNode(ownerID, count, nodes) {
      this.ownerID = ownerID;
      this.count = count;
      this.nodes = nodes;
    }

    HashArrayMapNode.prototype.get = function(shift, keyHash, key, notSetValue) {
      if (keyHash === undefined) {
        keyHash = hash(key);
      }
      var idx = (shift === 0 ? keyHash : keyHash >>> shift) & MASK;
      var node = this.nodes[idx];
      return node ? node.get(shift + SHIFT, keyHash, key, notSetValue) : notSetValue;
    };

    HashArrayMapNode.prototype.update = function(ownerID, shift, keyHash, key, value, didChangeSize, didAlter) {
      if (keyHash === undefined) {
        keyHash = hash(key);
      }
      var idx = (shift === 0 ? keyHash : keyHash >>> shift) & MASK;
      var removed = value === NOT_SET;
      var nodes = this.nodes;
      var node = nodes[idx];

      if (removed && !node) {
        return this;
      }

      var newNode = updateNode(node, ownerID, shift + SHIFT, keyHash, key, value, didChangeSize, didAlter);
      if (newNode === node) {
        return this;
      }

      var newCount = this.count;
      if (!node) {
        newCount++;
      } else if (!newNode) {
        newCount--;
        if (newCount < MIN_HASH_ARRAY_MAP_SIZE) {
          return packNodes(ownerID, nodes, newCount, idx);
        }
      }

      var isEditable = ownerID && ownerID === this.ownerID;
      var newNodes = setIn(nodes, idx, newNode, isEditable);

      if (isEditable) {
        this.count = newCount;
        this.nodes = newNodes;
        return this;
      }

      return new HashArrayMapNode(ownerID, newCount, newNodes);
    };




    function HashCollisionNode(ownerID, keyHash, entries) {
      this.ownerID = ownerID;
      this.keyHash = keyHash;
      this.entries = entries;
    }

    HashCollisionNode.prototype.get = function(shift, keyHash, key, notSetValue) {
      var entries = this.entries;
      for (var ii = 0, len = entries.length; ii < len; ii++) {
        if (is(key, entries[ii][0])) {
          return entries[ii][1];
        }
      }
      return notSetValue;
    };

    HashCollisionNode.prototype.update = function(ownerID, shift, keyHash, key, value, didChangeSize, didAlter) {
      if (keyHash === undefined) {
        keyHash = hash(key);
      }

      var removed = value === NOT_SET;

      if (keyHash !== this.keyHash) {
        if (removed) {
          return this;
        }
        SetRef(didAlter);
        SetRef(didChangeSize);
        return mergeIntoNode(this, ownerID, shift, keyHash, [key, value]);
      }

      var entries = this.entries;
      var idx = 0;
      for (var len = entries.length; idx < len; idx++) {
        if (is(key, entries[idx][0])) {
          break;
        }
      }
      var exists = idx < len;

      if (exists ? entries[idx][1] === value : removed) {
        return this;
      }

      SetRef(didAlter);
      (removed || !exists) && SetRef(didChangeSize);

      if (removed && len === 2) {
        return new ValueNode(ownerID, this.keyHash, entries[idx ^ 1]);
      }

      var isEditable = ownerID && ownerID === this.ownerID;
      var newEntries = isEditable ? entries : arrCopy(entries);

      if (exists) {
        if (removed) {
          idx === len - 1 ? newEntries.pop() : (newEntries[idx] = newEntries.pop());
        } else {
          newEntries[idx] = [key, value];
        }
      } else {
        newEntries.push([key, value]);
      }

      if (isEditable) {
        this.entries = newEntries;
        return this;
      }

      return new HashCollisionNode(ownerID, this.keyHash, newEntries);
    };




    function ValueNode(ownerID, keyHash, entry) {
      this.ownerID = ownerID;
      this.keyHash = keyHash;
      this.entry = entry;
    }

    ValueNode.prototype.get = function(shift, keyHash, key, notSetValue) {
      return is(key, this.entry[0]) ? this.entry[1] : notSetValue;
    };

    ValueNode.prototype.update = function(ownerID, shift, keyHash, key, value, didChangeSize, didAlter) {
      var removed = value === NOT_SET;
      var keyMatch = is(key, this.entry[0]);
      if (keyMatch ? value === this.entry[1] : removed) {
        return this;
      }

      SetRef(didAlter);

      if (removed) {
        SetRef(didChangeSize);
        return; // undefined
      }

      if (keyMatch) {
        if (ownerID && ownerID === this.ownerID) {
          this.entry[1] = value;
          return this;
        }
        return new ValueNode(ownerID, this.keyHash, [key, value]);
      }

      SetRef(didChangeSize);
      return mergeIntoNode(this, ownerID, shift, hash(key), [key, value]);
    };



  // #pragma Iterators

  ArrayMapNode.prototype.iterate =
  HashCollisionNode.prototype.iterate = function (fn, reverse) {
    var entries = this.entries;
    for (var ii = 0, maxIndex = entries.length - 1; ii <= maxIndex; ii++) {
      if (fn(entries[reverse ? maxIndex - ii : ii]) === false) {
        return false;
      }
    }
  }

  BitmapIndexedNode.prototype.iterate =
  HashArrayMapNode.prototype.iterate = function (fn, reverse) {
    var nodes = this.nodes;
    for (var ii = 0, maxIndex = nodes.length - 1; ii <= maxIndex; ii++) {
      var node = nodes[reverse ? maxIndex - ii : ii];
      if (node && node.iterate(fn, reverse) === false) {
        return false;
      }
    }
  }

  ValueNode.prototype.iterate = function (fn, reverse) {
    return fn(this.entry);
  }

  createClass(MapIterator, Iterator);

    function MapIterator(map, type, reverse) {
      this._type = type;
      this._reverse = reverse;
      this._stack = map._root && mapIteratorFrame(map._root);
    }

    MapIterator.prototype.next = function() {
      var type = this._type;
      var stack = this._stack;
      while (stack) {
        var node = stack.node;
        var index = stack.index++;
        var maxIndex;
        if (node.entry) {
          if (index === 0) {
            return mapIteratorValue(type, node.entry);
          }
        } else if (node.entries) {
          maxIndex = node.entries.length - 1;
          if (index <= maxIndex) {
            return mapIteratorValue(type, node.entries[this._reverse ? maxIndex - index : index]);
          }
        } else {
          maxIndex = node.nodes.length - 1;
          if (index <= maxIndex) {
            var subNode = node.nodes[this._reverse ? maxIndex - index : index];
            if (subNode) {
              if (subNode.entry) {
                return mapIteratorValue(type, subNode.entry);
              }
              stack = this._stack = mapIteratorFrame(subNode, stack);
            }
            continue;
          }
        }
        stack = this._stack = this._stack.__prev;
      }
      return iteratorDone();
    };


  function mapIteratorValue(type, entry) {
    return iteratorValue(type, entry[0], entry[1]);
  }

  function mapIteratorFrame(node, prev) {
    return {
      node: node,
      index: 0,
      __prev: prev
    };
  }

  function makeMap(size, root, ownerID, hash) {
    var map = Object.create(MapPrototype);
    map.size = size;
    map._root = root;
    map.__ownerID = ownerID;
    map.__hash = hash;
    map.__altered = false;
    return map;
  }

  var EMPTY_MAP;
  function emptyMap() {
    return EMPTY_MAP || (EMPTY_MAP = makeMap(0));
  }

  function updateMap(map, k, v) {
    var newRoot;
    var newSize;
    if (!map._root) {
      if (v === NOT_SET) {
        return map;
      }
      newSize = 1;
      newRoot = new ArrayMapNode(map.__ownerID, [[k, v]]);
    } else {
      var didChangeSize = MakeRef(CHANGE_LENGTH);
      var didAlter = MakeRef(DID_ALTER);
      newRoot = updateNode(map._root, map.__ownerID, 0, undefined, k, v, didChangeSize, didAlter);
      if (!didAlter.value) {
        return map;
      }
      newSize = map.size + (didChangeSize.value ? v === NOT_SET ? -1 : 1 : 0);
    }
    if (map.__ownerID) {
      map.size = newSize;
      map._root = newRoot;
      map.__hash = undefined;
      map.__altered = true;
      return map;
    }
    return newRoot ? makeMap(newSize, newRoot) : emptyMap();
  }

  function updateNode(node, ownerID, shift, keyHash, key, value, didChangeSize, didAlter) {
    if (!node) {
      if (value === NOT_SET) {
        return node;
      }
      SetRef(didAlter);
      SetRef(didChangeSize);
      return new ValueNode(ownerID, keyHash, [key, value]);
    }
    return node.update(ownerID, shift, keyHash, key, value, didChangeSize, didAlter);
  }

  function isLeafNode(node) {
    return node.constructor === ValueNode || node.constructor === HashCollisionNode;
  }

  function mergeIntoNode(node, ownerID, shift, keyHash, entry) {
    if (node.keyHash === keyHash) {
      return new HashCollisionNode(ownerID, keyHash, [node.entry, entry]);
    }

    var idx1 = (shift === 0 ? node.keyHash : node.keyHash >>> shift) & MASK;
    var idx2 = (shift === 0 ? keyHash : keyHash >>> shift) & MASK;

    var newNode;
    var nodes = idx1 === idx2 ?
      [mergeIntoNode(node, ownerID, shift + SHIFT, keyHash, entry)] :
      ((newNode = new ValueNode(ownerID, keyHash, entry)), idx1 < idx2 ? [node, newNode] : [newNode, node]);

    return new BitmapIndexedNode(ownerID, (1 << idx1) | (1 << idx2), nodes);
  }

  function createNodes(ownerID, entries, key, value) {
    if (!ownerID) {
      ownerID = new OwnerID();
    }
    var node = new ValueNode(ownerID, hash(key), [key, value]);
    for (var ii = 0; ii < entries.length; ii++) {
      var entry = entries[ii];
      node = node.update(ownerID, 0, undefined, entry[0], entry[1]);
    }
    return node;
  }

  function packNodes(ownerID, nodes, count, excluding) {
    var bitmap = 0;
    var packedII = 0;
    var packedNodes = new Array(count);
    for (var ii = 0, bit = 1, len = nodes.length; ii < len; ii++, bit <<= 1) {
      var node = nodes[ii];
      if (node !== undefined && ii !== excluding) {
        bitmap |= bit;
        packedNodes[packedII++] = node;
      }
    }
    return new BitmapIndexedNode(ownerID, bitmap, packedNodes);
  }

  function expandNodes(ownerID, nodes, bitmap, including, node) {
    var count = 0;
    var expandedNodes = new Array(SIZE);
    for (var ii = 0; bitmap !== 0; ii++, bitmap >>>= 1) {
      expandedNodes[ii] = bitmap & 1 ? nodes[count++] : undefined;
    }
    expandedNodes[including] = node;
    return new HashArrayMapNode(ownerID, count + 1, expandedNodes);
  }

  function mergeIntoMapWith(map, merger, iterables) {
    var iters = [];
    for (var ii = 0; ii < iterables.length; ii++) {
      var value = iterables[ii];
      var iter = KeyedIterable(value);
      if (!isIterable(value)) {
        iter = iter.map(function(v ) {return fromJS(v)});
      }
      iters.push(iter);
    }
    return mergeIntoCollectionWith(map, merger, iters);
  }

  function deepMerger(existing, value, key) {
    return existing && existing.mergeDeep && isIterable(value) ?
      existing.mergeDeep(value) :
      is(existing, value) ? existing : value;
  }

  function deepMergerWith(merger) {
    return function(existing, value, key)  {
      if (existing && existing.mergeDeepWith && isIterable(value)) {
        return existing.mergeDeepWith(merger, value);
      }
      var nextValue = merger(existing, value, key);
      return is(existing, nextValue) ? existing : nextValue;
    };
  }

  function mergeIntoCollectionWith(collection, merger, iters) {
    iters = iters.filter(function(x ) {return x.size !== 0});
    if (iters.length === 0) {
      return collection;
    }
    if (collection.size === 0 && !collection.__ownerID && iters.length === 1) {
      return collection.constructor(iters[0]);
    }
    return collection.withMutations(function(collection ) {
      var mergeIntoMap = merger ?
        function(value, key)  {
          collection.update(key, NOT_SET, function(existing )
            {return existing === NOT_SET ? value : merger(existing, value, key)}
          );
        } :
        function(value, key)  {
          collection.set(key, value);
        }
      for (var ii = 0; ii < iters.length; ii++) {
        iters[ii].forEach(mergeIntoMap);
      }
    });
  }

  function updateInDeepMap(existing, keyPathIter, notSetValue, updater) {
    var isNotSet = existing === NOT_SET;
    var step = keyPathIter.next();
    if (step.done) {
      var existingValue = isNotSet ? notSetValue : existing;
      var newValue = updater(existingValue);
      return newValue === existingValue ? existing : newValue;
    }
    invariant(
      isNotSet || (existing && existing.set),
      'invalid keyPath'
    );
    var key = step.value;
    var nextExisting = isNotSet ? NOT_SET : existing.get(key, NOT_SET);
    var nextUpdated = updateInDeepMap(
      nextExisting,
      keyPathIter,
      notSetValue,
      updater
    );
    return nextUpdated === nextExisting ? existing :
      nextUpdated === NOT_SET ? existing.remove(key) :
      (isNotSet ? emptyMap() : existing).set(key, nextUpdated);
  }

  function popCount(x) {
    x = x - ((x >> 1) & 0x55555555);
    x = (x & 0x33333333) + ((x >> 2) & 0x33333333);
    x = (x + (x >> 4)) & 0x0f0f0f0f;
    x = x + (x >> 8);
    x = x + (x >> 16);
    return x & 0x7f;
  }

  function setIn(array, idx, val, canEdit) {
    var newArray = canEdit ? array : arrCopy(array);
    newArray[idx] = val;
    return newArray;
  }

  function spliceIn(array, idx, val, canEdit) {
    var newLen = array.length + 1;
    if (canEdit && idx + 1 === newLen) {
      array[idx] = val;
      return array;
    }
    var newArray = new Array(newLen);
    var after = 0;
    for (var ii = 0; ii < newLen; ii++) {
      if (ii === idx) {
        newArray[ii] = val;
        after = -1;
      } else {
        newArray[ii] = array[ii + after];
      }
    }
    return newArray;
  }

  function spliceOut(array, idx, canEdit) {
    var newLen = array.length - 1;
    if (canEdit && idx === newLen) {
      array.pop();
      return array;
    }
    var newArray = new Array(newLen);
    var after = 0;
    for (var ii = 0; ii < newLen; ii++) {
      if (ii === idx) {
        after = 1;
      }
      newArray[ii] = array[ii + after];
    }
    return newArray;
  }

  var MAX_ARRAY_MAP_SIZE = SIZE / 4;
  var MAX_BITMAP_INDEXED_SIZE = SIZE / 2;
  var MIN_HASH_ARRAY_MAP_SIZE = SIZE / 4;

  createClass(List, IndexedCollection);

    // @pragma Construction

    function List(value) {
      var empty = emptyList();
      if (value === null || value === undefined) {
        return empty;
      }
      if (isList(value)) {
        return value;
      }
      var iter = IndexedIterable(value);
      var size = iter.size;
      if (size === 0) {
        return empty;
      }
      assertNotInfinite(size);
      if (size > 0 && size < SIZE) {
        return makeList(0, size, SHIFT, null, new VNode(iter.toArray()));
      }
      return empty.withMutations(function(list ) {
        list.setSize(size);
        iter.forEach(function(v, i)  {return list.set(i, v)});
      });
    }

    List.of = function(/*...values*/) {
      return this(arguments);
    };

    List.prototype.toString = function() {
      return this.__toString('List [', ']');
    };

    // @pragma Access

    List.prototype.get = function(index, notSetValue) {
      index = wrapIndex(this, index);
      if (index >= 0 && index < this.size) {
        index += this._origin;
        var node = listNodeFor(this, index);
        return node && node.array[index & MASK];
      }
      return notSetValue;
    };

    // @pragma Modification

    List.prototype.set = function(index, value) {
      return updateList(this, index, value);
    };

    List.prototype.remove = function(index) {
      return !this.has(index) ? this :
        index === 0 ? this.shift() :
        index === this.size - 1 ? this.pop() :
        this.splice(index, 1);
    };

    List.prototype.insert = function(index, value) {
      return this.splice(index, 0, value);
    };

    List.prototype.clear = function() {
      if (this.size === 0) {
        return this;
      }
      if (this.__ownerID) {
        this.size = this._origin = this._capacity = 0;
        this._level = SHIFT;
        this._root = this._tail = null;
        this.__hash = undefined;
        this.__altered = true;
        return this;
      }
      return emptyList();
    };

    List.prototype.push = function(/*...values*/) {
      var values = arguments;
      var oldSize = this.size;
      return this.withMutations(function(list ) {
        setListBounds(list, 0, oldSize + values.length);
        for (var ii = 0; ii < values.length; ii++) {
          list.set(oldSize + ii, values[ii]);
        }
      });
    };

    List.prototype.pop = function() {
      return setListBounds(this, 0, -1);
    };

    List.prototype.unshift = function(/*...values*/) {
      var values = arguments;
      return this.withMutations(function(list ) {
        setListBounds(list, -values.length);
        for (var ii = 0; ii < values.length; ii++) {
          list.set(ii, values[ii]);
        }
      });
    };

    List.prototype.shift = function() {
      return setListBounds(this, 1);
    };

    // @pragma Composition

    List.prototype.merge = function(/*...iters*/) {
      return mergeIntoListWith(this, undefined, arguments);
    };

    List.prototype.mergeWith = function(merger) {var iters = SLICE$0.call(arguments, 1);
      return mergeIntoListWith(this, merger, iters);
    };

    List.prototype.mergeDeep = function(/*...iters*/) {
      return mergeIntoListWith(this, deepMerger, arguments);
    };

    List.prototype.mergeDeepWith = function(merger) {var iters = SLICE$0.call(arguments, 1);
      return mergeIntoListWith(this, deepMergerWith(merger), iters);
    };

    List.prototype.setSize = function(size) {
      return setListBounds(this, 0, size);
    };

    // @pragma Iteration

    List.prototype.slice = function(begin, end) {
      var size = this.size;
      if (wholeSlice(begin, end, size)) {
        return this;
      }
      return setListBounds(
        this,
        resolveBegin(begin, size),
        resolveEnd(end, size)
      );
    };

    List.prototype.__iterator = function(type, reverse) {
      var index = 0;
      var values = iterateList(this, reverse);
      return new Iterator(function()  {
        var value = values();
        return value === DONE ?
          iteratorDone() :
          iteratorValue(type, index++, value);
      });
    };

    List.prototype.__iterate = function(fn, reverse) {
      var index = 0;
      var values = iterateList(this, reverse);
      var value;
      while ((value = values()) !== DONE) {
        if (fn(value, index++, this) === false) {
          break;
        }
      }
      return index;
    };

    List.prototype.__ensureOwner = function(ownerID) {
      if (ownerID === this.__ownerID) {
        return this;
      }
      if (!ownerID) {
        this.__ownerID = ownerID;
        return this;
      }
      return makeList(this._origin, this._capacity, this._level, this._root, this._tail, ownerID, this.__hash);
    };


  function isList(maybeList) {
    return !!(maybeList && maybeList[IS_LIST_SENTINEL]);
  }

  List.isList = isList;

  var IS_LIST_SENTINEL = '@@__IMMUTABLE_LIST__@@';

  var ListPrototype = List.prototype;
  ListPrototype[IS_LIST_SENTINEL] = true;
  ListPrototype[DELETE] = ListPrototype.remove;
  ListPrototype.setIn = MapPrototype.setIn;
  ListPrototype.deleteIn =
  ListPrototype.removeIn = MapPrototype.removeIn;
  ListPrototype.update = MapPrototype.update;
  ListPrototype.updateIn = MapPrototype.updateIn;
  ListPrototype.mergeIn = MapPrototype.mergeIn;
  ListPrototype.mergeDeepIn = MapPrototype.mergeDeepIn;
  ListPrototype.withMutations = MapPrototype.withMutations;
  ListPrototype.asMutable = MapPrototype.asMutable;
  ListPrototype.asImmutable = MapPrototype.asImmutable;
  ListPrototype.wasAltered = MapPrototype.wasAltered;



    function VNode(array, ownerID) {
      this.array = array;
      this.ownerID = ownerID;
    }

    // TODO: seems like these methods are very similar

    VNode.prototype.removeBefore = function(ownerID, level, index) {
      if (index === level ? 1 << level :  false || this.array.length === 0) {
        return this;
      }
      var originIndex = (index >>> level) & MASK;
      if (originIndex >= this.array.length) {
        return new VNode([], ownerID);
      }
      var removingFirst = originIndex === 0;
      var newChild;
      if (level > 0) {
        var oldChild = this.array[originIndex];
        newChild = oldChild && oldChild.removeBefore(ownerID, level - SHIFT, index);
        if (newChild === oldChild && removingFirst) {
          return this;
        }
      }
      if (removingFirst && !newChild) {
        return this;
      }
      var editable = editableVNode(this, ownerID);
      if (!removingFirst) {
        for (var ii = 0; ii < originIndex; ii++) {
          editable.array[ii] = undefined;
        }
      }
      if (newChild) {
        editable.array[originIndex] = newChild;
      }
      return editable;
    };

    VNode.prototype.removeAfter = function(ownerID, level, index) {
      if (index === (level ? 1 << level : 0) || this.array.length === 0) {
        return this;
      }
      var sizeIndex = ((index - 1) >>> level) & MASK;
      if (sizeIndex >= this.array.length) {
        return this;
      }

      var newChild;
      if (level > 0) {
        var oldChild = this.array[sizeIndex];
        newChild = oldChild && oldChild.removeAfter(ownerID, level - SHIFT, index);
        if (newChild === oldChild && sizeIndex === this.array.length - 1) {
          return this;
        }
      }

      var editable = editableVNode(this, ownerID);
      editable.array.splice(sizeIndex + 1);
      if (newChild) {
        editable.array[sizeIndex] = newChild;
      }
      return editable;
    };



  var DONE = {};

  function iterateList(list, reverse) {
    var left = list._origin;
    var right = list._capacity;
    var tailPos = getTailOffset(right);
    var tail = list._tail;

    return iterateNodeOrLeaf(list._root, list._level, 0);

    function iterateNodeOrLeaf(node, level, offset) {
      return level === 0 ?
        iterateLeaf(node, offset) :
        iterateNode(node, level, offset);
    }

    function iterateLeaf(node, offset) {
      var array = offset === tailPos ? tail && tail.array : node && node.array;
      var from = offset > left ? 0 : left - offset;
      var to = right - offset;
      if (to > SIZE) {
        to = SIZE;
      }
      return function()  {
        if (from === to) {
          return DONE;
        }
        var idx = reverse ? --to : from++;
        return array && array[idx];
      };
    }

    function iterateNode(node, level, offset) {
      var values;
      var array = node && node.array;
      var from = offset > left ? 0 : (left - offset) >> level;
      var to = ((right - offset) >> level) + 1;
      if (to > SIZE) {
        to = SIZE;
      }
      return function()  {
        do {
          if (values) {
            var value = values();
            if (value !== DONE) {
              return value;
            }
            values = null;
          }
          if (from === to) {
            return DONE;
          }
          var idx = reverse ? --to : from++;
          values = iterateNodeOrLeaf(
            array && array[idx], level - SHIFT, offset + (idx << level)
          );
        } while (true);
      };
    }
  }

  function makeList(origin, capacity, level, root, tail, ownerID, hash) {
    var list = Object.create(ListPrototype);
    list.size = capacity - origin;
    list._origin = origin;
    list._capacity = capacity;
    list._level = level;
    list._root = root;
    list._tail = tail;
    list.__ownerID = ownerID;
    list.__hash = hash;
    list.__altered = false;
    return list;
  }

  var EMPTY_LIST;
  function emptyList() {
    return EMPTY_LIST || (EMPTY_LIST = makeList(0, 0, SHIFT));
  }

  function updateList(list, index, value) {
    index = wrapIndex(list, index);

    if (index !== index) {
      return list;
    }

    if (index >= list.size || index < 0) {
      return list.withMutations(function(list ) {
        index < 0 ?
          setListBounds(list, index).set(0, value) :
          setListBounds(list, 0, index + 1).set(index, value)
      });
    }

    index += list._origin;

    var newTail = list._tail;
    var newRoot = list._root;
    var didAlter = MakeRef(DID_ALTER);
    if (index >= getTailOffset(list._capacity)) {
      newTail = updateVNode(newTail, list.__ownerID, 0, index, value, didAlter);
    } else {
      newRoot = updateVNode(newRoot, list.__ownerID, list._level, index, value, didAlter);
    }

    if (!didAlter.value) {
      return list;
    }

    if (list.__ownerID) {
      list._root = newRoot;
      list._tail = newTail;
      list.__hash = undefined;
      list.__altered = true;
      return list;
    }
    return makeList(list._origin, list._capacity, list._level, newRoot, newTail);
  }

  function updateVNode(node, ownerID, level, index, value, didAlter) {
    var idx = (index >>> level) & MASK;
    var nodeHas = node && idx < node.array.length;
    if (!nodeHas && value === undefined) {
      return node;
    }

    var newNode;

    if (level > 0) {
      var lowerNode = node && node.array[idx];
      var newLowerNode = updateVNode(lowerNode, ownerID, level - SHIFT, index, value, didAlter);
      if (newLowerNode === lowerNode) {
        return node;
      }
      newNode = editableVNode(node, ownerID);
      newNode.array[idx] = newLowerNode;
      return newNode;
    }

    if (nodeHas && node.array[idx] === value) {
      return node;
    }

    SetRef(didAlter);

    newNode = editableVNode(node, ownerID);
    if (value === undefined && idx === newNode.array.length - 1) {
      newNode.array.pop();
    } else {
      newNode.array[idx] = value;
    }
    return newNode;
  }

  function editableVNode(node, ownerID) {
    if (ownerID && node && ownerID === node.ownerID) {
      return node;
    }
    return new VNode(node ? node.array.slice() : [], ownerID);
  }

  function listNodeFor(list, rawIndex) {
    if (rawIndex >= getTailOffset(list._capacity)) {
      return list._tail;
    }
    if (rawIndex < 1 << (list._level + SHIFT)) {
      var node = list._root;
      var level = list._level;
      while (node && level > 0) {
        node = node.array[(rawIndex >>> level) & MASK];
        level -= SHIFT;
      }
      return node;
    }
  }

  function setListBounds(list, begin, end) {
    // Sanitize begin & end using this shorthand for ToInt32(argument)
    // http://www.ecma-international.org/ecma-262/6.0/#sec-toint32
    if (begin !== undefined) {
      begin = begin | 0;
    }
    if (end !== undefined) {
      end = end | 0;
    }
    var owner = list.__ownerID || new OwnerID();
    var oldOrigin = list._origin;
    var oldCapacity = list._capacity;
    var newOrigin = oldOrigin + begin;
    var newCapacity = end === undefined ? oldCapacity : end < 0 ? oldCapacity + end : oldOrigin + end;
    if (newOrigin === oldOrigin && newCapacity === oldCapacity) {
      return list;
    }

    // If it's going to end after it starts, it's empty.
    if (newOrigin >= newCapacity) {
      return list.clear();
    }

    var newLevel = list._level;
    var newRoot = list._root;

    // New origin might need creating a higher root.
    var offsetShift = 0;
    while (newOrigin + offsetShift < 0) {
      newRoot = new VNode(newRoot && newRoot.array.length ? [undefined, newRoot] : [], owner);
      newLevel += SHIFT;
      offsetShift += 1 << newLevel;
    }
    if (offsetShift) {
      newOrigin += offsetShift;
      oldOrigin += offsetShift;
      newCapacity += offsetShift;
      oldCapacity += offsetShift;
    }

    var oldTailOffset = getTailOffset(oldCapacity);
    var newTailOffset = getTailOffset(newCapacity);

    // New size might need creating a higher root.
    while (newTailOffset >= 1 << (newLevel + SHIFT)) {
      newRoot = new VNode(newRoot && newRoot.array.length ? [newRoot] : [], owner);
      newLevel += SHIFT;
    }

    // Locate or create the new tail.
    var oldTail = list._tail;
    var newTail = newTailOffset < oldTailOffset ?
      listNodeFor(list, newCapacity - 1) :
      newTailOffset > oldTailOffset ? new VNode([], owner) : oldTail;

    // Merge Tail into tree.
    if (oldTail && newTailOffset > oldTailOffset && newOrigin < oldCapacity && oldTail.array.length) {
      newRoot = editableVNode(newRoot, owner);
      var node = newRoot;
      for (var level = newLevel; level > SHIFT; level -= SHIFT) {
        var idx = (oldTailOffset >>> level) & MASK;
        node = node.array[idx] = editableVNode(node.array[idx], owner);
      }
      node.array[(oldTailOffset >>> SHIFT) & MASK] = oldTail;
    }

    // If the size has been reduced, there's a chance the tail needs to be trimmed.
    if (newCapacity < oldCapacity) {
      newTail = newTail && newTail.removeAfter(owner, 0, newCapacity);
    }

    // If the new origin is within the tail, then we do not need a root.
    if (newOrigin >= newTailOffset) {
      newOrigin -= newTailOffset;
      newCapacity -= newTailOffset;
      newLevel = SHIFT;
      newRoot = null;
      newTail = newTail && newTail.removeBefore(owner, 0, newOrigin);

    // Otherwise, if the root has been trimmed, garbage collect.
    } else if (newOrigin > oldOrigin || newTailOffset < oldTailOffset) {
      offsetShift = 0;

      // Identify the new top root node of the subtree of the old root.
      while (newRoot) {
        var beginIndex = (newOrigin >>> newLevel) & MASK;
        if (beginIndex !== (newTailOffset >>> newLevel) & MASK) {
          break;
        }
        if (beginIndex) {
          offsetShift += (1 << newLevel) * beginIndex;
        }
        newLevel -= SHIFT;
        newRoot = newRoot.array[beginIndex];
      }

      // Trim the new sides of the new root.
      if (newRoot && newOrigin > oldOrigin) {
        newRoot = newRoot.removeBefore(owner, newLevel, newOrigin - offsetShift);
      }
      if (newRoot && newTailOffset < oldTailOffset) {
        newRoot = newRoot.removeAfter(owner, newLevel, newTailOffset - offsetShift);
      }
      if (offsetShift) {
        newOrigin -= offsetShift;
        newCapacity -= offsetShift;
      }
    }

    if (list.__ownerID) {
      list.size = newCapacity - newOrigin;
      list._origin = newOrigin;
      list._capacity = newCapacity;
      list._level = newLevel;
      list._root = newRoot;
      list._tail = newTail;
      list.__hash = undefined;
      list.__altered = true;
      return list;
    }
    return makeList(newOrigin, newCapacity, newLevel, newRoot, newTail);
  }

  function mergeIntoListWith(list, merger, iterables) {
    var iters = [];
    var maxSize = 0;
    for (var ii = 0; ii < iterables.length; ii++) {
      var value = iterables[ii];
      var iter = IndexedIterable(value);
      if (iter.size > maxSize) {
        maxSize = iter.size;
      }
      if (!isIterable(value)) {
        iter = iter.map(function(v ) {return fromJS(v)});
      }
      iters.push(iter);
    }
    if (maxSize > list.size) {
      list = list.setSize(maxSize);
    }
    return mergeIntoCollectionWith(list, merger, iters);
  }

  function getTailOffset(size) {
    return size < SIZE ? 0 : (((size - 1) >>> SHIFT) << SHIFT);
  }

  createClass(OrderedMap, Map);

    // @pragma Construction

    function OrderedMap(value) {
      return value === null || value === undefined ? emptyOrderedMap() :
        isOrderedMap(value) ? value :
        emptyOrderedMap().withMutations(function(map ) {
          var iter = KeyedIterable(value);
          assertNotInfinite(iter.size);
          iter.forEach(function(v, k)  {return map.set(k, v)});
        });
    }

    OrderedMap.of = function(/*...values*/) {
      return this(arguments);
    };

    OrderedMap.prototype.toString = function() {
      return this.__toString('OrderedMap {', '}');
    };

    // @pragma Access

    OrderedMap.prototype.get = function(k, notSetValue) {
      var index = this._map.get(k);
      return index !== undefined ? this._list.get(index)[1] : notSetValue;
    };

    // @pragma Modification

    OrderedMap.prototype.clear = function() {
      if (this.size === 0) {
        return this;
      }
      if (this.__ownerID) {
        this.size = 0;
        this._map.clear();
        this._list.clear();
        return this;
      }
      return emptyOrderedMap();
    };

    OrderedMap.prototype.set = function(k, v) {
      return updateOrderedMap(this, k, v);
    };

    OrderedMap.prototype.remove = function(k) {
      return updateOrderedMap(this, k, NOT_SET);
    };

    OrderedMap.prototype.wasAltered = function() {
      return this._map.wasAltered() || this._list.wasAltered();
    };

    OrderedMap.prototype.__iterate = function(fn, reverse) {var this$0 = this;
      return this._list.__iterate(
        function(entry ) {return entry && fn(entry[1], entry[0], this$0)},
        reverse
      );
    };

    OrderedMap.prototype.__iterator = function(type, reverse) {
      return this._list.fromEntrySeq().__iterator(type, reverse);
    };

    OrderedMap.prototype.__ensureOwner = function(ownerID) {
      if (ownerID === this.__ownerID) {
        return this;
      }
      var newMap = this._map.__ensureOwner(ownerID);
      var newList = this._list.__ensureOwner(ownerID);
      if (!ownerID) {
        this.__ownerID = ownerID;
        this._map = newMap;
        this._list = newList;
        return this;
      }
      return makeOrderedMap(newMap, newList, ownerID, this.__hash);
    };


  function isOrderedMap(maybeOrderedMap) {
    return isMap(maybeOrderedMap) && isOrdered(maybeOrderedMap);
  }

  OrderedMap.isOrderedMap = isOrderedMap;

  OrderedMap.prototype[IS_ORDERED_SENTINEL] = true;
  OrderedMap.prototype[DELETE] = OrderedMap.prototype.remove;



  function makeOrderedMap(map, list, ownerID, hash) {
    var omap = Object.create(OrderedMap.prototype);
    omap.size = map ? map.size : 0;
    omap._map = map;
    omap._list = list;
    omap.__ownerID = ownerID;
    omap.__hash = hash;
    return omap;
  }

  var EMPTY_ORDERED_MAP;
  function emptyOrderedMap() {
    return EMPTY_ORDERED_MAP || (EMPTY_ORDERED_MAP = makeOrderedMap(emptyMap(), emptyList()));
  }

  function updateOrderedMap(omap, k, v) {
    var map = omap._map;
    var list = omap._list;
    var i = map.get(k);
    var has = i !== undefined;
    var newMap;
    var newList;
    if (v === NOT_SET) { // removed
      if (!has) {
        return omap;
      }
      if (list.size >= SIZE && list.size >= map.size * 2) {
        newList = list.filter(function(entry, idx)  {return entry !== undefined && i !== idx});
        newMap = newList.toKeyedSeq().map(function(entry ) {return entry[0]}).flip().toMap();
        if (omap.__ownerID) {
          newMap.__ownerID = newList.__ownerID = omap.__ownerID;
        }
      } else {
        newMap = map.remove(k);
        newList = i === list.size - 1 ? list.pop() : list.set(i, undefined);
      }
    } else {
      if (has) {
        if (v === list.get(i)[1]) {
          return omap;
        }
        newMap = map;
        newList = list.set(i, [k, v]);
      } else {
        newMap = map.set(k, list.size);
        newList = list.set(list.size, [k, v]);
      }
    }
    if (omap.__ownerID) {
      omap.size = newMap.size;
      omap._map = newMap;
      omap._list = newList;
      omap.__hash = undefined;
      return omap;
    }
    return makeOrderedMap(newMap, newList);
  }

  createClass(ToKeyedSequence, KeyedSeq);
    function ToKeyedSequence(indexed, useKeys) {
      this._iter = indexed;
      this._useKeys = useKeys;
      this.size = indexed.size;
    }

    ToKeyedSequence.prototype.get = function(key, notSetValue) {
      return this._iter.get(key, notSetValue);
    };

    ToKeyedSequence.prototype.has = function(key) {
      return this._iter.has(key);
    };

    ToKeyedSequence.prototype.valueSeq = function() {
      return this._iter.valueSeq();
    };

    ToKeyedSequence.prototype.reverse = function() {var this$0 = this;
      var reversedSequence = reverseFactory(this, true);
      if (!this._useKeys) {
        reversedSequence.valueSeq = function()  {return this$0._iter.toSeq().reverse()};
      }
      return reversedSequence;
    };

    ToKeyedSequence.prototype.map = function(mapper, context) {var this$0 = this;
      var mappedSequence = mapFactory(this, mapper, context);
      if (!this._useKeys) {
        mappedSequence.valueSeq = function()  {return this$0._iter.toSeq().map(mapper, context)};
      }
      return mappedSequence;
    };

    ToKeyedSequence.prototype.__iterate = function(fn, reverse) {var this$0 = this;
      var ii;
      return this._iter.__iterate(
        this._useKeys ?
          function(v, k)  {return fn(v, k, this$0)} :
          ((ii = reverse ? resolveSize(this) : 0),
            function(v ) {return fn(v, reverse ? --ii : ii++, this$0)}),
        reverse
      );
    };

    ToKeyedSequence.prototype.__iterator = function(type, reverse) {
      if (this._useKeys) {
        return this._iter.__iterator(type, reverse);
      }
      var iterator = this._iter.__iterator(ITERATE_VALUES, reverse);
      var ii = reverse ? resolveSize(this) : 0;
      return new Iterator(function()  {
        var step = iterator.next();
        return step.done ? step :
          iteratorValue(type, reverse ? --ii : ii++, step.value, step);
      });
    };

  ToKeyedSequence.prototype[IS_ORDERED_SENTINEL] = true;


  createClass(ToIndexedSequence, IndexedSeq);
    function ToIndexedSequence(iter) {
      this._iter = iter;
      this.size = iter.size;
    }

    ToIndexedSequence.prototype.includes = function(value) {
      return this._iter.includes(value);
    };

    ToIndexedSequence.prototype.__iterate = function(fn, reverse) {var this$0 = this;
      var iterations = 0;
      return this._iter.__iterate(function(v ) {return fn(v, iterations++, this$0)}, reverse);
    };

    ToIndexedSequence.prototype.__iterator = function(type, reverse) {
      var iterator = this._iter.__iterator(ITERATE_VALUES, reverse);
      var iterations = 0;
      return new Iterator(function()  {
        var step = iterator.next();
        return step.done ? step :
          iteratorValue(type, iterations++, step.value, step)
      });
    };



  createClass(ToSetSequence, SetSeq);
    function ToSetSequence(iter) {
      this._iter = iter;
      this.size = iter.size;
    }

    ToSetSequence.prototype.has = function(key) {
      return this._iter.includes(key);
    };

    ToSetSequence.prototype.__iterate = function(fn, reverse) {var this$0 = this;
      return this._iter.__iterate(function(v ) {return fn(v, v, this$0)}, reverse);
    };

    ToSetSequence.prototype.__iterator = function(type, reverse) {
      var iterator = this._iter.__iterator(ITERATE_VALUES, reverse);
      return new Iterator(function()  {
        var step = iterator.next();
        return step.done ? step :
          iteratorValue(type, step.value, step.value, step);
      });
    };



  createClass(FromEntriesSequence, KeyedSeq);
    function FromEntriesSequence(entries) {
      this._iter = entries;
      this.size = entries.size;
    }

    FromEntriesSequence.prototype.entrySeq = function() {
      return this._iter.toSeq();
    };

    FromEntriesSequence.prototype.__iterate = function(fn, reverse) {var this$0 = this;
      return this._iter.__iterate(function(entry ) {
        // Check if entry exists first so array access doesn't throw for holes
        // in the parent iteration.
        if (entry) {
          validateEntry(entry);
          var indexedIterable = isIterable(entry);
          return fn(
            indexedIterable ? entry.get(1) : entry[1],
            indexedIterable ? entry.get(0) : entry[0],
            this$0
          );
        }
      }, reverse);
    };

    FromEntriesSequence.prototype.__iterator = function(type, reverse) {
      var iterator = this._iter.__iterator(ITERATE_VALUES, reverse);
      return new Iterator(function()  {
        while (true) {
          var step = iterator.next();
          if (step.done) {
            return step;
          }
          var entry = step.value;
          // Check if entry exists first so array access doesn't throw for holes
          // in the parent iteration.
          if (entry) {
            validateEntry(entry);
            var indexedIterable = isIterable(entry);
            return iteratorValue(
              type,
              indexedIterable ? entry.get(0) : entry[0],
              indexedIterable ? entry.get(1) : entry[1],
              step
            );
          }
        }
      });
    };


  ToIndexedSequence.prototype.cacheResult =
  ToKeyedSequence.prototype.cacheResult =
  ToSetSequence.prototype.cacheResult =
  FromEntriesSequence.prototype.cacheResult =
    cacheResultThrough;


  function flipFactory(iterable) {
    var flipSequence = makeSequence(iterable);
    flipSequence._iter = iterable;
    flipSequence.size = iterable.size;
    flipSequence.flip = function()  {return iterable};
    flipSequence.reverse = function () {
      var reversedSequence = iterable.reverse.apply(this); // super.reverse()
      reversedSequence.flip = function()  {return iterable.reverse()};
      return reversedSequence;
    };
    flipSequence.has = function(key ) {return iterable.includes(key)};
    flipSequence.includes = function(key ) {return iterable.has(key)};
    flipSequence.cacheResult = cacheResultThrough;
    flipSequence.__iterateUncached = function (fn, reverse) {var this$0 = this;
      return iterable.__iterate(function(v, k)  {return fn(k, v, this$0) !== false}, reverse);
    }
    flipSequence.__iteratorUncached = function(type, reverse) {
      if (type === ITERATE_ENTRIES) {
        var iterator = iterable.__iterator(type, reverse);
        return new Iterator(function()  {
          var step = iterator.next();
          if (!step.done) {
            var k = step.value[0];
            step.value[0] = step.value[1];
            step.value[1] = k;
          }
          return step;
        });
      }
      return iterable.__iterator(
        type === ITERATE_VALUES ? ITERATE_KEYS : ITERATE_VALUES,
        reverse
      );
    }
    return flipSequence;
  }


  function mapFactory(iterable, mapper, context) {
    var mappedSequence = makeSequence(iterable);
    mappedSequence.size = iterable.size;
    mappedSequence.has = function(key ) {return iterable.has(key)};
    mappedSequence.get = function(key, notSetValue)  {
      var v = iterable.get(key, NOT_SET);
      return v === NOT_SET ?
        notSetValue :
        mapper.call(context, v, key, iterable);
    };
    mappedSequence.__iterateUncached = function (fn, reverse) {var this$0 = this;
      return iterable.__iterate(
        function(v, k, c)  {return fn(mapper.call(context, v, k, c), k, this$0) !== false},
        reverse
      );
    }
    mappedSequence.__iteratorUncached = function (type, reverse) {
      var iterator = iterable.__iterator(ITERATE_ENTRIES, reverse);
      return new Iterator(function()  {
        var step = iterator.next();
        if (step.done) {
          return step;
        }
        var entry = step.value;
        var key = entry[0];
        return iteratorValue(
          type,
          key,
          mapper.call(context, entry[1], key, iterable),
          step
        );
      });
    }
    return mappedSequence;
  }


  function reverseFactory(iterable, useKeys) {
    var reversedSequence = makeSequence(iterable);
    reversedSequence._iter = iterable;
    reversedSequence.size = iterable.size;
    reversedSequence.reverse = function()  {return iterable};
    if (iterable.flip) {
      reversedSequence.flip = function () {
        var flipSequence = flipFactory(iterable);
        flipSequence.reverse = function()  {return iterable.flip()};
        return flipSequence;
      };
    }
    reversedSequence.get = function(key, notSetValue) 
      {return iterable.get(useKeys ? key : -1 - key, notSetValue)};
    reversedSequence.has = function(key )
      {return iterable.has(useKeys ? key : -1 - key)};
    reversedSequence.includes = function(value ) {return iterable.includes(value)};
    reversedSequence.cacheResult = cacheResultThrough;
    reversedSequence.__iterate = function (fn, reverse) {var this$0 = this;
      return iterable.__iterate(function(v, k)  {return fn(v, k, this$0)}, !reverse);
    };
    reversedSequence.__iterator =
      function(type, reverse)  {return iterable.__iterator(type, !reverse)};
    return reversedSequence;
  }


  function filterFactory(iterable, predicate, context, useKeys) {
    var filterSequence = makeSequence(iterable);
    if (useKeys) {
      filterSequence.has = function(key ) {
        var v = iterable.get(key, NOT_SET);
        return v !== NOT_SET && !!predicate.call(context, v, key, iterable);
      };
      filterSequence.get = function(key, notSetValue)  {
        var v = iterable.get(key, NOT_SET);
        return v !== NOT_SET && predicate.call(context, v, key, iterable) ?
          v : notSetValue;
      };
    }
    filterSequence.__iterateUncached = function (fn, reverse) {var this$0 = this;
      var iterations = 0;
      iterable.__iterate(function(v, k, c)  {
        if (predicate.call(context, v, k, c)) {
          iterations++;
          return fn(v, useKeys ? k : iterations - 1, this$0);
        }
      }, reverse);
      return iterations;
    };
    filterSequence.__iteratorUncached = function (type, reverse) {
      var iterator = iterable.__iterator(ITERATE_ENTRIES, reverse);
      var iterations = 0;
      return new Iterator(function()  {
        while (true) {
          var step = iterator.next();
          if (step.done) {
            return step;
          }
          var entry = step.value;
          var key = entry[0];
          var value = entry[1];
          if (predicate.call(context, value, key, iterable)) {
            return iteratorValue(type, useKeys ? key : iterations++, value, step);
          }
        }
      });
    }
    return filterSequence;
  }


  function countByFactory(iterable, grouper, context) {
    var groups = Map().asMutable();
    iterable.__iterate(function(v, k)  {
      groups.update(
        grouper.call(context, v, k, iterable),
        0,
        function(a ) {return a + 1}
      );
    });
    return groups.asImmutable();
  }


  function groupByFactory(iterable, grouper, context) {
    var isKeyedIter = isKeyed(iterable);
    var groups = (isOrdered(iterable) ? OrderedMap() : Map()).asMutable();
    iterable.__iterate(function(v, k)  {
      groups.update(
        grouper.call(context, v, k, iterable),
        function(a ) {return (a = a || [], a.push(isKeyedIter ? [k, v] : v), a)}
      );
    });
    var coerce = iterableClass(iterable);
    return groups.map(function(arr ) {return reify(iterable, coerce(arr))});
  }


  function sliceFactory(iterable, begin, end, useKeys) {
    var originalSize = iterable.size;

    // Sanitize begin & end using this shorthand for ToInt32(argument)
    // http://www.ecma-international.org/ecma-262/6.0/#sec-toint32
    if (begin !== undefined) {
      begin = begin | 0;
    }
    if (end !== undefined) {
      if (end === Infinity) {
        end = originalSize;
      } else {
        end = end | 0;
      }
    }

    if (wholeSlice(begin, end, originalSize)) {
      return iterable;
    }

    var resolvedBegin = resolveBegin(begin, originalSize);
    var resolvedEnd = resolveEnd(end, originalSize);

    // begin or end will be NaN if they were provided as negative numbers and
    // this iterable's size is unknown. In that case, cache first so there is
    // a known size and these do not resolve to NaN.
    if (resolvedBegin !== resolvedBegin || resolvedEnd !== resolvedEnd) {
      return sliceFactory(iterable.toSeq().cacheResult(), begin, end, useKeys);
    }

    // Note: resolvedEnd is undefined when the original sequence's length is
    // unknown and this slice did not supply an end and should contain all
    // elements after resolvedBegin.
    // In that case, resolvedSize will be NaN and sliceSize will remain undefined.
    var resolvedSize = resolvedEnd - resolvedBegin;
    var sliceSize;
    if (resolvedSize === resolvedSize) {
      sliceSize = resolvedSize < 0 ? 0 : resolvedSize;
    }

    var sliceSeq = makeSequence(iterable);

    // If iterable.size is undefined, the size of the realized sliceSeq is
    // unknown at this point unless the number of items to slice is 0
    sliceSeq.size = sliceSize === 0 ? sliceSize : iterable.size && sliceSize || undefined;

    if (!useKeys && isSeq(iterable) && sliceSize >= 0) {
      sliceSeq.get = function (index, notSetValue) {
        index = wrapIndex(this, index);
        return index >= 0 && index < sliceSize ?
          iterable.get(index + resolvedBegin, notSetValue) :
          notSetValue;
      }
    }

    sliceSeq.__iterateUncached = function(fn, reverse) {var this$0 = this;
      if (sliceSize === 0) {
        return 0;
      }
      if (reverse) {
        return this.cacheResult().__iterate(fn, reverse);
      }
      var skipped = 0;
      var isSkipping = true;
      var iterations = 0;
      iterable.__iterate(function(v, k)  {
        if (!(isSkipping && (isSkipping = skipped++ < resolvedBegin))) {
          iterations++;
          return fn(v, useKeys ? k : iterations - 1, this$0) !== false &&
                 iterations !== sliceSize;
        }
      });
      return iterations;
    };

    sliceSeq.__iteratorUncached = function(type, reverse) {
      if (sliceSize !== 0 && reverse) {
        return this.cacheResult().__iterator(type, reverse);
      }
      // Don't bother instantiating parent iterator if taking 0.
      var iterator = sliceSize !== 0 && iterable.__iterator(type, reverse);
      var skipped = 0;
      var iterations = 0;
      return new Iterator(function()  {
        while (skipped++ < resolvedBegin) {
          iterator.next();
        }
        if (++iterations > sliceSize) {
          return iteratorDone();
        }
        var step = iterator.next();
        if (useKeys || type === ITERATE_VALUES) {
          return step;
        } else if (type === ITERATE_KEYS) {
          return iteratorValue(type, iterations - 1, undefined, step);
        } else {
          return iteratorValue(type, iterations - 1, step.value[1], step);
        }
      });
    }

    return sliceSeq;
  }


  function takeWhileFactory(iterable, predicate, context) {
    var takeSequence = makeSequence(iterable);
    takeSequence.__iterateUncached = function(fn, reverse) {var this$0 = this;
      if (reverse) {
        return this.cacheResult().__iterate(fn, reverse);
      }
      var iterations = 0;
      iterable.__iterate(function(v, k, c) 
        {return predicate.call(context, v, k, c) && ++iterations && fn(v, k, this$0)}
      );
      return iterations;
    };
    takeSequence.__iteratorUncached = function(type, reverse) {var this$0 = this;
      if (reverse) {
        return this.cacheResult().__iterator(type, reverse);
      }
      var iterator = iterable.__iterator(ITERATE_ENTRIES, reverse);
      var iterating = true;
      return new Iterator(function()  {
        if (!iterating) {
          return iteratorDone();
        }
        var step = iterator.next();
        if (step.done) {
          return step;
        }
        var entry = step.value;
        var k = entry[0];
        var v = entry[1];
        if (!predicate.call(context, v, k, this$0)) {
          iterating = false;
          return iteratorDone();
        }
        return type === ITERATE_ENTRIES ? step :
          iteratorValue(type, k, v, step);
      });
    };
    return takeSequence;
  }


  function skipWhileFactory(iterable, predicate, context, useKeys) {
    var skipSequence = makeSequence(iterable);
    skipSequence.__iterateUncached = function (fn, reverse) {var this$0 = this;
      if (reverse) {
        return this.cacheResult().__iterate(fn, reverse);
      }
      var isSkipping = true;
      var iterations = 0;
      iterable.__iterate(function(v, k, c)  {
        if (!(isSkipping && (isSkipping = predicate.call(context, v, k, c)))) {
          iterations++;
          return fn(v, useKeys ? k : iterations - 1, this$0);
        }
      });
      return iterations;
    };
    skipSequence.__iteratorUncached = function(type, reverse) {var this$0 = this;
      if (reverse) {
        return this.cacheResult().__iterator(type, reverse);
      }
      var iterator = iterable.__iterator(ITERATE_ENTRIES, reverse);
      var skipping = true;
      var iterations = 0;
      return new Iterator(function()  {
        var step, k, v;
        do {
          step = iterator.next();
          if (step.done) {
            if (useKeys || type === ITERATE_VALUES) {
              return step;
            } else if (type === ITERATE_KEYS) {
              return iteratorValue(type, iterations++, undefined, step);
            } else {
              return iteratorValue(type, iterations++, step.value[1], step);
            }
          }
          var entry = step.value;
          k = entry[0];
          v = entry[1];
          skipping && (skipping = predicate.call(context, v, k, this$0));
        } while (skipping);
        return type === ITERATE_ENTRIES ? step :
          iteratorValue(type, k, v, step);
      });
    };
    return skipSequence;
  }


  function concatFactory(iterable, values) {
    var isKeyedIterable = isKeyed(iterable);
    var iters = [iterable].concat(values).map(function(v ) {
      if (!isIterable(v)) {
        v = isKeyedIterable ?
          keyedSeqFromValue(v) :
          indexedSeqFromValue(Array.isArray(v) ? v : [v]);
      } else if (isKeyedIterable) {
        v = KeyedIterable(v);
      }
      return v;
    }).filter(function(v ) {return v.size !== 0});

    if (iters.length === 0) {
      return iterable;
    }

    if (iters.length === 1) {
      var singleton = iters[0];
      if (singleton === iterable ||
          isKeyedIterable && isKeyed(singleton) ||
          isIndexed(iterable) && isIndexed(singleton)) {
        return singleton;
      }
    }

    var concatSeq = new ArraySeq(iters);
    if (isKeyedIterable) {
      concatSeq = concatSeq.toKeyedSeq();
    } else if (!isIndexed(iterable)) {
      concatSeq = concatSeq.toSetSeq();
    }
    concatSeq = concatSeq.flatten(true);
    concatSeq.size = iters.reduce(
      function(sum, seq)  {
        if (sum !== undefined) {
          var size = seq.size;
          if (size !== undefined) {
            return sum + size;
          }
        }
      },
      0
    );
    return concatSeq;
  }


  function flattenFactory(iterable, depth, useKeys) {
    var flatSequence = makeSequence(iterable);
    flatSequence.__iterateUncached = function(fn, reverse) {
      var iterations = 0;
      var stopped = false;
      function flatDeep(iter, currentDepth) {var this$0 = this;
        iter.__iterate(function(v, k)  {
          if ((!depth || currentDepth < depth) && isIterable(v)) {
            flatDeep(v, currentDepth + 1);
          } else if (fn(v, useKeys ? k : iterations++, this$0) === false) {
            stopped = true;
          }
          return !stopped;
        }, reverse);
      }
      flatDeep(iterable, 0);
      return iterations;
    }
    flatSequence.__iteratorUncached = function(type, reverse) {
      var iterator = iterable.__iterator(type, reverse);
      var stack = [];
      var iterations = 0;
      return new Iterator(function()  {
        while (iterator) {
          var step = iterator.next();
          if (step.done !== false) {
            iterator = stack.pop();
            continue;
          }
          var v = step.value;
          if (type === ITERATE_ENTRIES) {
            v = v[1];
          }
          if ((!depth || stack.length < depth) && isIterable(v)) {
            stack.push(iterator);
            iterator = v.__iterator(type, reverse);
          } else {
            return useKeys ? step : iteratorValue(type, iterations++, v, step);
          }
        }
        return iteratorDone();
      });
    }
    return flatSequence;
  }


  function flatMapFactory(iterable, mapper, context) {
    var coerce = iterableClass(iterable);
    return iterable.toSeq().map(
      function(v, k)  {return coerce(mapper.call(context, v, k, iterable))}
    ).flatten(true);
  }


  function interposeFactory(iterable, separator) {
    var interposedSequence = makeSequence(iterable);
    interposedSequence.size = iterable.size && iterable.size * 2 -1;
    interposedSequence.__iterateUncached = function(fn, reverse) {var this$0 = this;
      var iterations = 0;
      iterable.__iterate(function(v, k) 
        {return (!iterations || fn(separator, iterations++, this$0) !== false) &&
        fn(v, iterations++, this$0) !== false},
        reverse
      );
      return iterations;
    };
    interposedSequence.__iteratorUncached = function(type, reverse) {
      var iterator = iterable.__iterator(ITERATE_VALUES, reverse);
      var iterations = 0;
      var step;
      return new Iterator(function()  {
        if (!step || iterations % 2) {
          step = iterator.next();
          if (step.done) {
            return step;
          }
        }
        return iterations % 2 ?
          iteratorValue(type, iterations++, separator) :
          iteratorValue(type, iterations++, step.value, step);
      });
    };
    return interposedSequence;
  }


  function sortFactory(iterable, comparator, mapper) {
    if (!comparator) {
      comparator = defaultComparator;
    }
    var isKeyedIterable = isKeyed(iterable);
    var index = 0;
    var entries = iterable.toSeq().map(
      function(v, k)  {return [k, v, index++, mapper ? mapper(v, k, iterable) : v]}
    ).toArray();
    entries.sort(function(a, b)  {return comparator(a[3], b[3]) || a[2] - b[2]}).forEach(
      isKeyedIterable ?
      function(v, i)  { entries[i].length = 2; } :
      function(v, i)  { entries[i] = v[1]; }
    );
    return isKeyedIterable ? KeyedSeq(entries) :
      isIndexed(iterable) ? IndexedSeq(entries) :
      SetSeq(entries);
  }


  function maxFactory(iterable, comparator, mapper) {
    if (!comparator) {
      comparator = defaultComparator;
    }
    if (mapper) {
      var entry = iterable.toSeq()
        .map(function(v, k)  {return [v, mapper(v, k, iterable)]})
        .reduce(function(a, b)  {return maxCompare(comparator, a[1], b[1]) ? b : a});
      return entry && entry[0];
    } else {
      return iterable.reduce(function(a, b)  {return maxCompare(comparator, a, b) ? b : a});
    }
  }

  function maxCompare(comparator, a, b) {
    var comp = comparator(b, a);
    // b is considered the new max if the comparator declares them equal, but
    // they are not equal and b is in fact a nullish value.
    return (comp === 0 && b !== a && (b === undefined || b === null || b !== b)) || comp > 0;
  }


  function zipWithFactory(keyIter, zipper, iters) {
    var zipSequence = makeSequence(keyIter);
    zipSequence.size = new ArraySeq(iters).map(function(i ) {return i.size}).min();
    // Note: this a generic base implementation of __iterate in terms of
    // __iterator which may be more generically useful in the future.
    zipSequence.__iterate = function(fn, reverse) {
      /* generic:
      var iterator = this.__iterator(ITERATE_ENTRIES, reverse);
      var step;
      var iterations = 0;
      while (!(step = iterator.next()).done) {
        iterations++;
        if (fn(step.value[1], step.value[0], this) === false) {
          break;
        }
      }
      return iterations;
      */
      // indexed:
      var iterator = this.__iterator(ITERATE_VALUES, reverse);
      var step;
      var iterations = 0;
      while (!(step = iterator.next()).done) {
        if (fn(step.value, iterations++, this) === false) {
          break;
        }
      }
      return iterations;
    };
    zipSequence.__iteratorUncached = function(type, reverse) {
      var iterators = iters.map(function(i )
        {return (i = Iterable(i), getIterator(reverse ? i.reverse() : i))}
      );
      var iterations = 0;
      var isDone = false;
      return new Iterator(function()  {
        var steps;
        if (!isDone) {
          steps = iterators.map(function(i ) {return i.next()});
          isDone = steps.some(function(s ) {return s.done});
        }
        if (isDone) {
          return iteratorDone();
        }
        return iteratorValue(
          type,
          iterations++,
          zipper.apply(null, steps.map(function(s ) {return s.value}))
        );
      });
    };
    return zipSequence
  }


  // #pragma Helper Functions

  function reify(iter, seq) {
    return isSeq(iter) ? seq : iter.constructor(seq);
  }

  function validateEntry(entry) {
    if (entry !== Object(entry)) {
      throw new TypeError('Expected [K, V] tuple: ' + entry);
    }
  }

  function resolveSize(iter) {
    assertNotInfinite(iter.size);
    return ensureSize(iter);
  }

  function iterableClass(iterable) {
    return isKeyed(iterable) ? KeyedIterable :
      isIndexed(iterable) ? IndexedIterable :
      SetIterable;
  }

  function makeSequence(iterable) {
    return Object.create(
      (
        isKeyed(iterable) ? KeyedSeq :
        isIndexed(iterable) ? IndexedSeq :
        SetSeq
      ).prototype
    );
  }

  function cacheResultThrough() {
    if (this._iter.cacheResult) {
      this._iter.cacheResult();
      this.size = this._iter.size;
      return this;
    } else {
      return Seq.prototype.cacheResult.call(this);
    }
  }

  function defaultComparator(a, b) {
    return a > b ? 1 : a < b ? -1 : 0;
  }

  function forceIterator(keyPath) {
    var iter = getIterator(keyPath);
    if (!iter) {
      // Array might not be iterable in this environment, so we need a fallback
      // to our wrapped type.
      if (!isArrayLike(keyPath)) {
        throw new TypeError('Expected iterable or array-like: ' + keyPath);
      }
      iter = getIterator(Iterable(keyPath));
    }
    return iter;
  }

  createClass(Record, KeyedCollection);

    function Record(defaultValues, name) {
      var hasInitialized;

      var RecordType = function Record(values) {
        if (values instanceof RecordType) {
          return values;
        }
        if (!(this instanceof RecordType)) {
          return new RecordType(values);
        }
        if (!hasInitialized) {
          hasInitialized = true;
          var keys = Object.keys(defaultValues);
          setProps(RecordTypePrototype, keys);
          RecordTypePrototype.size = keys.length;
          RecordTypePrototype._name = name;
          RecordTypePrototype._keys = keys;
          RecordTypePrototype._defaultValues = defaultValues;
        }
        this._map = Map(values);
      };

      var RecordTypePrototype = RecordType.prototype = Object.create(RecordPrototype);
      RecordTypePrototype.constructor = RecordType;

      return RecordType;
    }

    Record.prototype.toString = function() {
      return this.__toString(recordName(this) + ' {', '}');
    };

    // @pragma Access

    Record.prototype.has = function(k) {
      return this._defaultValues.hasOwnProperty(k);
    };

    Record.prototype.get = function(k, notSetValue) {
      if (!this.has(k)) {
        return notSetValue;
      }
      var defaultVal = this._defaultValues[k];
      return this._map ? this._map.get(k, defaultVal) : defaultVal;
    };

    // @pragma Modification

    Record.prototype.clear = function() {
      if (this.__ownerID) {
        this._map && this._map.clear();
        return this;
      }
      var RecordType = this.constructor;
      return RecordType._empty || (RecordType._empty = makeRecord(this, emptyMap()));
    };

    Record.prototype.set = function(k, v) {
      if (!this.has(k)) {
        throw new Error('Cannot set unknown key "' + k + '" on ' + recordName(this));
      }
      if (this._map && !this._map.has(k)) {
        var defaultVal = this._defaultValues[k];
        if (v === defaultVal) {
          return this;
        }
      }
      var newMap = this._map && this._map.set(k, v);
      if (this.__ownerID || newMap === this._map) {
        return this;
      }
      return makeRecord(this, newMap);
    };

    Record.prototype.remove = function(k) {
      if (!this.has(k)) {
        return this;
      }
      var newMap = this._map && this._map.remove(k);
      if (this.__ownerID || newMap === this._map) {
        return this;
      }
      return makeRecord(this, newMap);
    };

    Record.prototype.wasAltered = function() {
      return this._map.wasAltered();
    };

    Record.prototype.__iterator = function(type, reverse) {var this$0 = this;
      return KeyedIterable(this._defaultValues).map(function(_, k)  {return this$0.get(k)}).__iterator(type, reverse);
    };

    Record.prototype.__iterate = function(fn, reverse) {var this$0 = this;
      return KeyedIterable(this._defaultValues).map(function(_, k)  {return this$0.get(k)}).__iterate(fn, reverse);
    };

    Record.prototype.__ensureOwner = function(ownerID) {
      if (ownerID === this.__ownerID) {
        return this;
      }
      var newMap = this._map && this._map.__ensureOwner(ownerID);
      if (!ownerID) {
        this.__ownerID = ownerID;
        this._map = newMap;
        return this;
      }
      return makeRecord(this, newMap, ownerID);
    };


  var RecordPrototype = Record.prototype;
  RecordPrototype[DELETE] = RecordPrototype.remove;
  RecordPrototype.deleteIn =
  RecordPrototype.removeIn = MapPrototype.removeIn;
  RecordPrototype.merge = MapPrototype.merge;
  RecordPrototype.mergeWith = MapPrototype.mergeWith;
  RecordPrototype.mergeIn = MapPrototype.mergeIn;
  RecordPrototype.mergeDeep = MapPrototype.mergeDeep;
  RecordPrototype.mergeDeepWith = MapPrototype.mergeDeepWith;
  RecordPrototype.mergeDeepIn = MapPrototype.mergeDeepIn;
  RecordPrototype.setIn = MapPrototype.setIn;
  RecordPrototype.update = MapPrototype.update;
  RecordPrototype.updateIn = MapPrototype.updateIn;
  RecordPrototype.withMutations = MapPrototype.withMutations;
  RecordPrototype.asMutable = MapPrototype.asMutable;
  RecordPrototype.asImmutable = MapPrototype.asImmutable;


  function makeRecord(likeRecord, map, ownerID) {
    var record = Object.create(Object.getPrototypeOf(likeRecord));
    record._map = map;
    record.__ownerID = ownerID;
    return record;
  }

  function recordName(record) {
    return record._name || record.constructor.name || 'Record';
  }

  function setProps(prototype, names) {
    try {
      names.forEach(setProp.bind(undefined, prototype));
    } catch (error) {
      // Object.defineProperty failed. Probably IE8.
    }
  }

  function setProp(prototype, name) {
    Object.defineProperty(prototype, name, {
      get: function() {
        return this.get(name);
      },
      set: function(value) {
        invariant(this.__ownerID, 'Cannot set on an immutable record.');
        this.set(name, value);
      }
    });
  }

  createClass(Set, SetCollection);

    // @pragma Construction

    function Set(value) {
      return value === null || value === undefined ? emptySet() :
        isSet(value) && !isOrdered(value) ? value :
        emptySet().withMutations(function(set ) {
          var iter = SetIterable(value);
          assertNotInfinite(iter.size);
          iter.forEach(function(v ) {return set.add(v)});
        });
    }

    Set.of = function(/*...values*/) {
      return this(arguments);
    };

    Set.fromKeys = function(value) {
      return this(KeyedIterable(value).keySeq());
    };

    Set.prototype.toString = function() {
      return this.__toString('Set {', '}');
    };

    // @pragma Access

    Set.prototype.has = function(value) {
      return this._map.has(value);
    };

    // @pragma Modification

    Set.prototype.add = function(value) {
      return updateSet(this, this._map.set(value, true));
    };

    Set.prototype.remove = function(value) {
      return updateSet(this, this._map.remove(value));
    };

    Set.prototype.clear = function() {
      return updateSet(this, this._map.clear());
    };

    // @pragma Composition

    Set.prototype.union = function() {var iters = SLICE$0.call(arguments, 0);
      iters = iters.filter(function(x ) {return x.size !== 0});
      if (iters.length === 0) {
        return this;
      }
      if (this.size === 0 && !this.__ownerID && iters.length === 1) {
        return this.constructor(iters[0]);
      }
      return this.withMutations(function(set ) {
        for (var ii = 0; ii < iters.length; ii++) {
          SetIterable(iters[ii]).forEach(function(value ) {return set.add(value)});
        }
      });
    };

    Set.prototype.intersect = function() {var iters = SLICE$0.call(arguments, 0);
      if (iters.length === 0) {
        return this;
      }
      iters = iters.map(function(iter ) {return SetIterable(iter)});
      var originalSet = this;
      return this.withMutations(function(set ) {
        originalSet.forEach(function(value ) {
          if (!iters.every(function(iter ) {return iter.includes(value)})) {
            set.remove(value);
          }
        });
      });
    };

    Set.prototype.subtract = function() {var iters = SLICE$0.call(arguments, 0);
      if (iters.length === 0) {
        return this;
      }
      iters = iters.map(function(iter ) {return SetIterable(iter)});
      var originalSet = this;
      return this.withMutations(function(set ) {
        originalSet.forEach(function(value ) {
          if (iters.some(function(iter ) {return iter.includes(value)})) {
            set.remove(value);
          }
        });
      });
    };

    Set.prototype.merge = function() {
      return this.union.apply(this, arguments);
    };

    Set.prototype.mergeWith = function(merger) {var iters = SLICE$0.call(arguments, 1);
      return this.union.apply(this, iters);
    };

    Set.prototype.sort = function(comparator) {
      // Late binding
      return OrderedSet(sortFactory(this, comparator));
    };

    Set.prototype.sortBy = function(mapper, comparator) {
      // Late binding
      return OrderedSet(sortFactory(this, comparator, mapper));
    };

    Set.prototype.wasAltered = function() {
      return this._map.wasAltered();
    };

    Set.prototype.__iterate = function(fn, reverse) {var this$0 = this;
      return this._map.__iterate(function(_, k)  {return fn(k, k, this$0)}, reverse);
    };

    Set.prototype.__iterator = function(type, reverse) {
      return this._map.map(function(_, k)  {return k}).__iterator(type, reverse);
    };

    Set.prototype.__ensureOwner = function(ownerID) {
      if (ownerID === this.__ownerID) {
        return this;
      }
      var newMap = this._map.__ensureOwner(ownerID);
      if (!ownerID) {
        this.__ownerID = ownerID;
        this._map = newMap;
        return this;
      }
      return this.__make(newMap, ownerID);
    };


  function isSet(maybeSet) {
    return !!(maybeSet && maybeSet[IS_SET_SENTINEL]);
  }

  Set.isSet = isSet;

  var IS_SET_SENTINEL = '@@__IMMUTABLE_SET__@@';

  var SetPrototype = Set.prototype;
  SetPrototype[IS_SET_SENTINEL] = true;
  SetPrototype[DELETE] = SetPrototype.remove;
  SetPrototype.mergeDeep = SetPrototype.merge;
  SetPrototype.mergeDeepWith = SetPrototype.mergeWith;
  SetPrototype.withMutations = MapPrototype.withMutations;
  SetPrototype.asMutable = MapPrototype.asMutable;
  SetPrototype.asImmutable = MapPrototype.asImmutable;

  SetPrototype.__empty = emptySet;
  SetPrototype.__make = makeSet;

  function updateSet(set, newMap) {
    if (set.__ownerID) {
      set.size = newMap.size;
      set._map = newMap;
      return set;
    }
    return newMap === set._map ? set :
      newMap.size === 0 ? set.__empty() :
      set.__make(newMap);
  }

  function makeSet(map, ownerID) {
    var set = Object.create(SetPrototype);
    set.size = map ? map.size : 0;
    set._map = map;
    set.__ownerID = ownerID;
    return set;
  }

  var EMPTY_SET;
  function emptySet() {
    return EMPTY_SET || (EMPTY_SET = makeSet(emptyMap()));
  }

  createClass(OrderedSet, Set);

    // @pragma Construction

    function OrderedSet(value) {
      return value === null || value === undefined ? emptyOrderedSet() :
        isOrderedSet(value) ? value :
        emptyOrderedSet().withMutations(function(set ) {
          var iter = SetIterable(value);
          assertNotInfinite(iter.size);
          iter.forEach(function(v ) {return set.add(v)});
        });
    }

    OrderedSet.of = function(/*...values*/) {
      return this(arguments);
    };

    OrderedSet.fromKeys = function(value) {
      return this(KeyedIterable(value).keySeq());
    };

    OrderedSet.prototype.toString = function() {
      return this.__toString('OrderedSet {', '}');
    };


  function isOrderedSet(maybeOrderedSet) {
    return isSet(maybeOrderedSet) && isOrdered(maybeOrderedSet);
  }

  OrderedSet.isOrderedSet = isOrderedSet;

  var OrderedSetPrototype = OrderedSet.prototype;
  OrderedSetPrototype[IS_ORDERED_SENTINEL] = true;

  OrderedSetPrototype.__empty = emptyOrderedSet;
  OrderedSetPrototype.__make = makeOrderedSet;

  function makeOrderedSet(map, ownerID) {
    var set = Object.create(OrderedSetPrototype);
    set.size = map ? map.size : 0;
    set._map = map;
    set.__ownerID = ownerID;
    return set;
  }

  var EMPTY_ORDERED_SET;
  function emptyOrderedSet() {
    return EMPTY_ORDERED_SET || (EMPTY_ORDERED_SET = makeOrderedSet(emptyOrderedMap()));
  }

  createClass(Stack, IndexedCollection);

    // @pragma Construction

    function Stack(value) {
      return value === null || value === undefined ? emptyStack() :
        isStack(value) ? value :
        emptyStack().unshiftAll(value);
    }

    Stack.of = function(/*...values*/) {
      return this(arguments);
    };

    Stack.prototype.toString = function() {
      return this.__toString('Stack [', ']');
    };

    // @pragma Access

    Stack.prototype.get = function(index, notSetValue) {
      var head = this._head;
      index = wrapIndex(this, index);
      while (head && index--) {
        head = head.next;
      }
      return head ? head.value : notSetValue;
    };

    Stack.prototype.peek = function() {
      return this._head && this._head.value;
    };

    // @pragma Modification

    Stack.prototype.push = function(/*...values*/) {
      if (arguments.length === 0) {
        return this;
      }
      var newSize = this.size + arguments.length;
      var head = this._head;
      for (var ii = arguments.length - 1; ii >= 0; ii--) {
        head = {
          value: arguments[ii],
          next: head
        };
      }
      if (this.__ownerID) {
        this.size = newSize;
        this._head = head;
        this.__hash = undefined;
        this.__altered = true;
        return this;
      }
      return makeStack(newSize, head);
    };

    Stack.prototype.pushAll = function(iter) {
      iter = IndexedIterable(iter);
      if (iter.size === 0) {
        return this;
      }
      assertNotInfinite(iter.size);
      var newSize = this.size;
      var head = this._head;
      iter.reverse().forEach(function(value ) {
        newSize++;
        head = {
          value: value,
          next: head
        };
      });
      if (this.__ownerID) {
        this.size = newSize;
        this._head = head;
        this.__hash = undefined;
        this.__altered = true;
        return this;
      }
      return makeStack(newSize, head);
    };

    Stack.prototype.pop = function() {
      return this.slice(1);
    };

    Stack.prototype.unshift = function(/*...values*/) {
      return this.push.apply(this, arguments);
    };

    Stack.prototype.unshiftAll = function(iter) {
      return this.pushAll(iter);
    };

    Stack.prototype.shift = function() {
      return this.pop.apply(this, arguments);
    };

    Stack.prototype.clear = function() {
      if (this.size === 0) {
        return this;
      }
      if (this.__ownerID) {
        this.size = 0;
        this._head = undefined;
        this.__hash = undefined;
        this.__altered = true;
        return this;
      }
      return emptyStack();
    };

    Stack.prototype.slice = function(begin, end) {
      if (wholeSlice(begin, end, this.size)) {
        return this;
      }
      var resolvedBegin = resolveBegin(begin, this.size);
      var resolvedEnd = resolveEnd(end, this.size);
      if (resolvedEnd !== this.size) {
        // super.slice(begin, end);
        return IndexedCollection.prototype.slice.call(this, begin, end);
      }
      var newSize = this.size - resolvedBegin;
      var head = this._head;
      while (resolvedBegin--) {
        head = head.next;
      }
      if (this.__ownerID) {
        this.size = newSize;
        this._head = head;
        this.__hash = undefined;
        this.__altered = true;
        return this;
      }
      return makeStack(newSize, head);
    };

    // @pragma Mutability

    Stack.prototype.__ensureOwner = function(ownerID) {
      if (ownerID === this.__ownerID) {
        return this;
      }
      if (!ownerID) {
        this.__ownerID = ownerID;
        this.__altered = false;
        return this;
      }
      return makeStack(this.size, this._head, ownerID, this.__hash);
    };

    // @pragma Iteration

    Stack.prototype.__iterate = function(fn, reverse) {
      if (reverse) {
        return this.reverse().__iterate(fn);
      }
      var iterations = 0;
      var node = this._head;
      while (node) {
        if (fn(node.value, iterations++, this) === false) {
          break;
        }
        node = node.next;
      }
      return iterations;
    };

    Stack.prototype.__iterator = function(type, reverse) {
      if (reverse) {
        return this.reverse().__iterator(type);
      }
      var iterations = 0;
      var node = this._head;
      return new Iterator(function()  {
        if (node) {
          var value = node.value;
          node = node.next;
          return iteratorValue(type, iterations++, value);
        }
        return iteratorDone();
      });
    };


  function isStack(maybeStack) {
    return !!(maybeStack && maybeStack[IS_STACK_SENTINEL]);
  }

  Stack.isStack = isStack;

  var IS_STACK_SENTINEL = '@@__IMMUTABLE_STACK__@@';

  var StackPrototype = Stack.prototype;
  StackPrototype[IS_STACK_SENTINEL] = true;
  StackPrototype.withMutations = MapPrototype.withMutations;
  StackPrototype.asMutable = MapPrototype.asMutable;
  StackPrototype.asImmutable = MapPrototype.asImmutable;
  StackPrototype.wasAltered = MapPrototype.wasAltered;


  function makeStack(size, head, ownerID, hash) {
    var map = Object.create(StackPrototype);
    map.size = size;
    map._head = head;
    map.__ownerID = ownerID;
    map.__hash = hash;
    map.__altered = false;
    return map;
  }

  var EMPTY_STACK;
  function emptyStack() {
    return EMPTY_STACK || (EMPTY_STACK = makeStack(0));
  }

  /**
   * Contributes additional methods to a constructor
   */
  function mixin(ctor, methods) {
    var keyCopier = function(key ) { ctor.prototype[key] = methods[key]; };
    Object.keys(methods).forEach(keyCopier);
    Object.getOwnPropertySymbols &&
      Object.getOwnPropertySymbols(methods).forEach(keyCopier);
    return ctor;
  }

  Iterable.Iterator = Iterator;

  mixin(Iterable, {

    // ### Conversion to other types

    toArray: function() {
      assertNotInfinite(this.size);
      var array = new Array(this.size || 0);
      this.valueSeq().__iterate(function(v, i)  { array[i] = v; });
      return array;
    },

    toIndexedSeq: function() {
      return new ToIndexedSequence(this);
    },

    toJS: function() {
      return this.toSeq().map(
        function(value ) {return value && typeof value.toJS === 'function' ? value.toJS() : value}
      ).__toJS();
    },

    toJSON: function() {
      return this.toSeq().map(
        function(value ) {return value && typeof value.toJSON === 'function' ? value.toJSON() : value}
      ).__toJS();
    },

    toKeyedSeq: function() {
      return new ToKeyedSequence(this, true);
    },

    toMap: function() {
      // Use Late Binding here to solve the circular dependency.
      return Map(this.toKeyedSeq());
    },

    toObject: function() {
      assertNotInfinite(this.size);
      var object = {};
      this.__iterate(function(v, k)  { object[k] = v; });
      return object;
    },

    toOrderedMap: function() {
      // Use Late Binding here to solve the circular dependency.
      return OrderedMap(this.toKeyedSeq());
    },

    toOrderedSet: function() {
      // Use Late Binding here to solve the circular dependency.
      return OrderedSet(isKeyed(this) ? this.valueSeq() : this);
    },

    toSet: function() {
      // Use Late Binding here to solve the circular dependency.
      return Set(isKeyed(this) ? this.valueSeq() : this);
    },

    toSetSeq: function() {
      return new ToSetSequence(this);
    },

    toSeq: function() {
      return isIndexed(this) ? this.toIndexedSeq() :
        isKeyed(this) ? this.toKeyedSeq() :
        this.toSetSeq();
    },

    toStack: function() {
      // Use Late Binding here to solve the circular dependency.
      return Stack(isKeyed(this) ? this.valueSeq() : this);
    },

    toList: function() {
      // Use Late Binding here to solve the circular dependency.
      return List(isKeyed(this) ? this.valueSeq() : this);
    },


    // ### Common JavaScript methods and properties

    toString: function() {
      return '[Iterable]';
    },

    __toString: function(head, tail) {
      if (this.size === 0) {
        return head + tail;
      }
      return head + ' ' + this.toSeq().map(this.__toStringMapper).join(', ') + ' ' + tail;
    },


    // ### ES6 Collection methods (ES6 Array and Map)

    concat: function() {var values = SLICE$0.call(arguments, 0);
      return reify(this, concatFactory(this, values));
    },

    includes: function(searchValue) {
      return this.some(function(value ) {return is(value, searchValue)});
    },

    entries: function() {
      return this.__iterator(ITERATE_ENTRIES);
    },

    every: function(predicate, context) {
      assertNotInfinite(this.size);
      var returnValue = true;
      this.__iterate(function(v, k, c)  {
        if (!predicate.call(context, v, k, c)) {
          returnValue = false;
          return false;
        }
      });
      return returnValue;
    },

    filter: function(predicate, context) {
      return reify(this, filterFactory(this, predicate, context, true));
    },

    find: function(predicate, context, notSetValue) {
      var entry = this.findEntry(predicate, context);
      return entry ? entry[1] : notSetValue;
    },

    forEach: function(sideEffect, context) {
      assertNotInfinite(this.size);
      return this.__iterate(context ? sideEffect.bind(context) : sideEffect);
    },

    join: function(separator) {
      assertNotInfinite(this.size);
      separator = separator !== undefined ? '' + separator : ',';
      var joined = '';
      var isFirst = true;
      this.__iterate(function(v ) {
        isFirst ? (isFirst = false) : (joined += separator);
        joined += v !== null && v !== undefined ? v.toString() : '';
      });
      return joined;
    },

    keys: function() {
      return this.__iterator(ITERATE_KEYS);
    },

    map: function(mapper, context) {
      return reify(this, mapFactory(this, mapper, context));
    },

    reduce: function(reducer, initialReduction, context) {
      assertNotInfinite(this.size);
      var reduction;
      var useFirst;
      if (arguments.length < 2) {
        useFirst = true;
      } else {
        reduction = initialReduction;
      }
      this.__iterate(function(v, k, c)  {
        if (useFirst) {
          useFirst = false;
          reduction = v;
        } else {
          reduction = reducer.call(context, reduction, v, k, c);
        }
      });
      return reduction;
    },

    reduceRight: function(reducer, initialReduction, context) {
      var reversed = this.toKeyedSeq().reverse();
      return reversed.reduce.apply(reversed, arguments);
    },

    reverse: function() {
      return reify(this, reverseFactory(this, true));
    },

    slice: function(begin, end) {
      return reify(this, sliceFactory(this, begin, end, true));
    },

    some: function(predicate, context) {
      return !this.every(not(predicate), context);
    },

    sort: function(comparator) {
      return reify(this, sortFactory(this, comparator));
    },

    values: function() {
      return this.__iterator(ITERATE_VALUES);
    },


    // ### More sequential methods

    butLast: function() {
      return this.slice(0, -1);
    },

    isEmpty: function() {
      return this.size !== undefined ? this.size === 0 : !this.some(function()  {return true});
    },

    count: function(predicate, context) {
      return ensureSize(
        predicate ? this.toSeq().filter(predicate, context) : this
      );
    },

    countBy: function(grouper, context) {
      return countByFactory(this, grouper, context);
    },

    equals: function(other) {
      return deepEqual(this, other);
    },

    entrySeq: function() {
      var iterable = this;
      if (iterable._cache) {
        // We cache as an entries array, so we can just return the cache!
        return new ArraySeq(iterable._cache);
      }
      var entriesSequence = iterable.toSeq().map(entryMapper).toIndexedSeq();
      entriesSequence.fromEntrySeq = function()  {return iterable.toSeq()};
      return entriesSequence;
    },

    filterNot: function(predicate, context) {
      return this.filter(not(predicate), context);
    },

    findEntry: function(predicate, context, notSetValue) {
      var found = notSetValue;
      this.__iterate(function(v, k, c)  {
        if (predicate.call(context, v, k, c)) {
          found = [k, v];
          return false;
        }
      });
      return found;
    },

    findKey: function(predicate, context) {
      var entry = this.findEntry(predicate, context);
      return entry && entry[0];
    },

    findLast: function(predicate, context, notSetValue) {
      return this.toKeyedSeq().reverse().find(predicate, context, notSetValue);
    },

    findLastEntry: function(predicate, context, notSetValue) {
      return this.toKeyedSeq().reverse().findEntry(predicate, context, notSetValue);
    },

    findLastKey: function(predicate, context) {
      return this.toKeyedSeq().reverse().findKey(predicate, context);
    },

    first: function() {
      return this.find(returnTrue);
    },

    flatMap: function(mapper, context) {
      return reify(this, flatMapFactory(this, mapper, context));
    },

    flatten: function(depth) {
      return reify(this, flattenFactory(this, depth, true));
    },

    fromEntrySeq: function() {
      return new FromEntriesSequence(this);
    },

    get: function(searchKey, notSetValue) {
      return this.find(function(_, key)  {return is(key, searchKey)}, undefined, notSetValue);
    },

    getIn: function(searchKeyPath, notSetValue) {
      var nested = this;
      // Note: in an ES6 environment, we would prefer:
      // for (var key of searchKeyPath) {
      var iter = forceIterator(searchKeyPath);
      var step;
      while (!(step = iter.next()).done) {
        var key = step.value;
        nested = nested && nested.get ? nested.get(key, NOT_SET) : NOT_SET;
        if (nested === NOT_SET) {
          return notSetValue;
        }
      }
      return nested;
    },

    groupBy: function(grouper, context) {
      return groupByFactory(this, grouper, context);
    },

    has: function(searchKey) {
      return this.get(searchKey, NOT_SET) !== NOT_SET;
    },

    hasIn: function(searchKeyPath) {
      return this.getIn(searchKeyPath, NOT_SET) !== NOT_SET;
    },

    isSubset: function(iter) {
      iter = typeof iter.includes === 'function' ? iter : Iterable(iter);
      return this.every(function(value ) {return iter.includes(value)});
    },

    isSuperset: function(iter) {
      iter = typeof iter.isSubset === 'function' ? iter : Iterable(iter);
      return iter.isSubset(this);
    },

    keyOf: function(searchValue) {
      return this.findKey(function(value ) {return is(value, searchValue)});
    },

    keySeq: function() {
      return this.toSeq().map(keyMapper).toIndexedSeq();
    },

    last: function() {
      return this.toSeq().reverse().first();
    },

    lastKeyOf: function(searchValue) {
      return this.toKeyedSeq().reverse().keyOf(searchValue);
    },

    max: function(comparator) {
      return maxFactory(this, comparator);
    },

    maxBy: function(mapper, comparator) {
      return maxFactory(this, comparator, mapper);
    },

    min: function(comparator) {
      return maxFactory(this, comparator ? neg(comparator) : defaultNegComparator);
    },

    minBy: function(mapper, comparator) {
      return maxFactory(this, comparator ? neg(comparator) : defaultNegComparator, mapper);
    },

    rest: function() {
      return this.slice(1);
    },

    skip: function(amount) {
      return this.slice(Math.max(0, amount));
    },

    skipLast: function(amount) {
      return reify(this, this.toSeq().reverse().skip(amount).reverse());
    },

    skipWhile: function(predicate, context) {
      return reify(this, skipWhileFactory(this, predicate, context, true));
    },

    skipUntil: function(predicate, context) {
      return this.skipWhile(not(predicate), context);
    },

    sortBy: function(mapper, comparator) {
      return reify(this, sortFactory(this, comparator, mapper));
    },

    take: function(amount) {
      return this.slice(0, Math.max(0, amount));
    },

    takeLast: function(amount) {
      return reify(this, this.toSeq().reverse().take(amount).reverse());
    },

    takeWhile: function(predicate, context) {
      return reify(this, takeWhileFactory(this, predicate, context));
    },

    takeUntil: function(predicate, context) {
      return this.takeWhile(not(predicate), context);
    },

    valueSeq: function() {
      return this.toIndexedSeq();
    },


    // ### Hashable Object

    hashCode: function() {
      return this.__hash || (this.__hash = hashIterable(this));
    }


    // ### Internal

    // abstract __iterate(fn, reverse)

    // abstract __iterator(type, reverse)
  });

  // var IS_ITERABLE_SENTINEL = '@@__IMMUTABLE_ITERABLE__@@';
  // var IS_KEYED_SENTINEL = '@@__IMMUTABLE_KEYED__@@';
  // var IS_INDEXED_SENTINEL = '@@__IMMUTABLE_INDEXED__@@';
  // var IS_ORDERED_SENTINEL = '@@__IMMUTABLE_ORDERED__@@';

  var IterablePrototype = Iterable.prototype;
  IterablePrototype[IS_ITERABLE_SENTINEL] = true;
  IterablePrototype[ITERATOR_SYMBOL] = IterablePrototype.values;
  IterablePrototype.__toJS = IterablePrototype.toArray;
  IterablePrototype.__toStringMapper = quoteString;
  IterablePrototype.inspect =
  IterablePrototype.toSource = function() { return this.toString(); };
  IterablePrototype.chain = IterablePrototype.flatMap;
  IterablePrototype.contains = IterablePrototype.includes;

  mixin(KeyedIterable, {

    // ### More sequential methods

    flip: function() {
      return reify(this, flipFactory(this));
    },

    mapEntries: function(mapper, context) {var this$0 = this;
      var iterations = 0;
      return reify(this,
        this.toSeq().map(
          function(v, k)  {return mapper.call(context, [k, v], iterations++, this$0)}
        ).fromEntrySeq()
      );
    },

    mapKeys: function(mapper, context) {var this$0 = this;
      return reify(this,
        this.toSeq().flip().map(
          function(k, v)  {return mapper.call(context, k, v, this$0)}
        ).flip()
      );
    }

  });

  var KeyedIterablePrototype = KeyedIterable.prototype;
  KeyedIterablePrototype[IS_KEYED_SENTINEL] = true;
  KeyedIterablePrototype[ITERATOR_SYMBOL] = IterablePrototype.entries;
  KeyedIterablePrototype.__toJS = IterablePrototype.toObject;
  KeyedIterablePrototype.__toStringMapper = function(v, k)  {return JSON.stringify(k) + ': ' + quoteString(v)};



  mixin(IndexedIterable, {

    // ### Conversion to other types

    toKeyedSeq: function() {
      return new ToKeyedSequence(this, false);
    },


    // ### ES6 Collection methods (ES6 Array and Map)

    filter: function(predicate, context) {
      return reify(this, filterFactory(this, predicate, context, false));
    },

    findIndex: function(predicate, context) {
      var entry = this.findEntry(predicate, context);
      return entry ? entry[0] : -1;
    },

    indexOf: function(searchValue) {
      var key = this.keyOf(searchValue);
      return key === undefined ? -1 : key;
    },

    lastIndexOf: function(searchValue) {
      var key = this.lastKeyOf(searchValue);
      return key === undefined ? -1 : key;
    },

    reverse: function() {
      return reify(this, reverseFactory(this, false));
    },

    slice: function(begin, end) {
      return reify(this, sliceFactory(this, begin, end, false));
    },

    splice: function(index, removeNum /*, ...values*/) {
      var numArgs = arguments.length;
      removeNum = Math.max(removeNum | 0, 0);
      if (numArgs === 0 || (numArgs === 2 && !removeNum)) {
        return this;
      }
      // If index is negative, it should resolve relative to the size of the
      // collection. However size may be expensive to compute if not cached, so
      // only call count() if the number is in fact negative.
      index = resolveBegin(index, index < 0 ? this.count() : this.size);
      var spliced = this.slice(0, index);
      return reify(
        this,
        numArgs === 1 ?
          spliced :
          spliced.concat(arrCopy(arguments, 2), this.slice(index + removeNum))
      );
    },


    // ### More collection methods

    findLastIndex: function(predicate, context) {
      var entry = this.findLastEntry(predicate, context);
      return entry ? entry[0] : -1;
    },

    first: function() {
      return this.get(0);
    },

    flatten: function(depth) {
      return reify(this, flattenFactory(this, depth, false));
    },

    get: function(index, notSetValue) {
      index = wrapIndex(this, index);
      return (index < 0 || (this.size === Infinity ||
          (this.size !== undefined && index > this.size))) ?
        notSetValue :
        this.find(function(_, key)  {return key === index}, undefined, notSetValue);
    },

    has: function(index) {
      index = wrapIndex(this, index);
      return index >= 0 && (this.size !== undefined ?
        this.size === Infinity || index < this.size :
        this.indexOf(index) !== -1
      );
    },

    interpose: function(separator) {
      return reify(this, interposeFactory(this, separator));
    },

    interleave: function(/*...iterables*/) {
      var iterables = [this].concat(arrCopy(arguments));
      var zipped = zipWithFactory(this.toSeq(), IndexedSeq.of, iterables);
      var interleaved = zipped.flatten(true);
      if (zipped.size) {
        interleaved.size = zipped.size * iterables.length;
      }
      return reify(this, interleaved);
    },

    keySeq: function() {
      return Range(0, this.size);
    },

    last: function() {
      return this.get(-1);
    },

    skipWhile: function(predicate, context) {
      return reify(this, skipWhileFactory(this, predicate, context, false));
    },

    zip: function(/*, ...iterables */) {
      var iterables = [this].concat(arrCopy(arguments));
      return reify(this, zipWithFactory(this, defaultZipper, iterables));
    },

    zipWith: function(zipper/*, ...iterables */) {
      var iterables = arrCopy(arguments);
      iterables[0] = this;
      return reify(this, zipWithFactory(this, zipper, iterables));
    }

  });

  IndexedIterable.prototype[IS_INDEXED_SENTINEL] = true;
  IndexedIterable.prototype[IS_ORDERED_SENTINEL] = true;



  mixin(SetIterable, {

    // ### ES6 Collection methods (ES6 Array and Map)

    get: function(value, notSetValue) {
      return this.has(value) ? value : notSetValue;
    },

    includes: function(value) {
      return this.has(value);
    },


    // ### More sequential methods

    keySeq: function() {
      return this.valueSeq();
    }

  });

  SetIterable.prototype.has = IterablePrototype.includes;
  SetIterable.prototype.contains = SetIterable.prototype.includes;


  // Mixin subclasses

  mixin(KeyedSeq, KeyedIterable.prototype);
  mixin(IndexedSeq, IndexedIterable.prototype);
  mixin(SetSeq, SetIterable.prototype);

  mixin(KeyedCollection, KeyedIterable.prototype);
  mixin(IndexedCollection, IndexedIterable.prototype);
  mixin(SetCollection, SetIterable.prototype);


  // #pragma Helper functions

  function keyMapper(v, k) {
    return k;
  }

  function entryMapper(v, k) {
    return [k, v];
  }

  function not(predicate) {
    return function() {
      return !predicate.apply(this, arguments);
    }
  }

  function neg(predicate) {
    return function() {
      return -predicate.apply(this, arguments);
    }
  }

  function quoteString(value) {
    return typeof value === 'string' ? JSON.stringify(value) : String(value);
  }

  function defaultZipper() {
    return arrCopy(arguments);
  }

  function defaultNegComparator(a, b) {
    return a < b ? 1 : a > b ? -1 : 0;
  }

  function hashIterable(iterable) {
    if (iterable.size === Infinity) {
      return 0;
    }
    var ordered = isOrdered(iterable);
    var keyed = isKeyed(iterable);
    var h = ordered ? 1 : 0;
    var size = iterable.__iterate(
      keyed ?
        ordered ?
          function(v, k)  { h = 31 * h + hashMerge(hash(v), hash(k)) | 0; } :
          function(v, k)  { h = h + hashMerge(hash(v), hash(k)) | 0; } :
        ordered ?
          function(v ) { h = 31 * h + hash(v) | 0; } :
          function(v ) { h = h + hash(v) | 0; }
    );
    return murmurHashOfSize(size, h);
  }

  function murmurHashOfSize(size, h) {
    h = imul(h, 0xCC9E2D51);
    h = imul(h << 15 | h >>> -15, 0x1B873593);
    h = imul(h << 13 | h >>> -13, 5);
    h = (h + 0xE6546B64 | 0) ^ size;
    h = imul(h ^ h >>> 16, 0x85EBCA6B);
    h = imul(h ^ h >>> 13, 0xC2B2AE35);
    h = smi(h ^ h >>> 16);
    return h;
  }

  function hashMerge(a, b) {
    return a ^ b + 0x9E3779B9 + (a << 6) + (a >> 2) | 0; // int
  }

  var Immutable = {

    Iterable: Iterable,

    Seq: Seq,
    Collection: Collection,
    Map: Map,
    OrderedMap: OrderedMap,
    List: List,
    Stack: Stack,
    Set: Set,
    OrderedSet: OrderedSet,

    Record: Record,
    Range: Range,
    Repeat: Repeat,

    is: is,
    fromJS: fromJS

  };

  return Immutable;

}));

/***/ }),

/***/ "./node_modules/invariant/browser.js":
/*!*******************************************!*\
  !*** ./node_modules/invariant/browser.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



/**
 * Use invariant() to assert state which your program assumes to be true.
 *
 * Provide sprintf-style format (only %s is supported) and arguments
 * to provide information about what broke and what you were
 * expecting.
 *
 * The invariant message will be stripped in production, but the invariant
 * will remain to ensure logic does not differ in production.
 */

var invariant = function(condition, format, a, b, c, d, e, f) {
  if (true) {
    if (format === undefined) {
      throw new Error('invariant requires an error message argument');
    }
  }

  if (!condition) {
    var error;
    if (format === undefined) {
      error = new Error(
        'Minified exception occurred; use the non-minified dev environment ' +
        'for the full error message and additional helpful warnings.'
      );
    } else {
      var args = [a, b, c, d, e, f];
      var argIndex = 0;
      error = new Error(
        format.replace(/%s/g, function() { return args[argIndex++]; })
      );
      error.name = 'Invariant Violation';
    }

    error.framesToPop = 1; // we don't care about invariant's own frame
    throw error;
  }
};

module.exports = invariant;


/***/ }),

/***/ "./node_modules/is-what/dist/index.esm.js":
/*!************************************************!*\
  !*** ./node_modules/is-what/dist/index.esm.js ***!
  \************************************************/
/*! exports provided: getType, isAnyObject, isArray, isBlob, isBoolean, isDate, isEmptyArray, isEmptyObject, isEmptyString, isError, isFile, isFullArray, isFullObject, isFullString, isFunction, isMap, isNaNValue, isNull, isNullOrUndefined, isNumber, isObject, isObjectLike, isOneOf, isPlainObject, isPrimitive, isPromise, isRegExp, isSet, isString, isSymbol, isType, isUndefined, isWeakMap, isWeakSet */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getType", function() { return getType; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isAnyObject", function() { return isAnyObject; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isArray", function() { return isArray; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isBlob", function() { return isBlob; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isBoolean", function() { return isBoolean; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isDate", function() { return isDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isEmptyArray", function() { return isEmptyArray; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isEmptyObject", function() { return isEmptyObject; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isEmptyString", function() { return isEmptyString; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isError", function() { return isError; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isFile", function() { return isFile; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isFullArray", function() { return isFullArray; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isFullObject", function() { return isFullObject; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isFullString", function() { return isFullString; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isFunction", function() { return isFunction; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isMap", function() { return isMap; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isNaNValue", function() { return isNaNValue; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isNull", function() { return isNull; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isNullOrUndefined", function() { return isNullOrUndefined; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isNumber", function() { return isNumber; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isObject", function() { return isObject; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isObjectLike", function() { return isObjectLike; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isOneOf", function() { return isOneOf; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isPlainObject", function() { return isPlainObject; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isPrimitive", function() { return isPrimitive; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isPromise", function() { return isPromise; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isRegExp", function() { return isRegExp; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isSet", function() { return isSet; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isString", function() { return isString; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isSymbol", function() { return isSymbol; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isType", function() { return isType; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isUndefined", function() { return isUndefined; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isWeakMap", function() { return isWeakMap; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isWeakSet", function() { return isWeakSet; });
/**
 * Returns the object type of the given payload
 *
 * @param {*} payload
 * @returns {string}
 */
function getType(payload) {
    return Object.prototype.toString.call(payload).slice(8, -1);
}
/**
 * Returns whether the payload is undefined
 *
 * @param {*} payload
 * @returns {payload is undefined}
 */
function isUndefined(payload) {
    return getType(payload) === 'Undefined';
}
/**
 * Returns whether the payload is null
 *
 * @param {*} payload
 * @returns {payload is null}
 */
function isNull(payload) {
    return getType(payload) === 'Null';
}
/**
 * Returns whether the payload is a plain JavaScript object (excluding special classes or objects with other prototypes)
 *
 * @param {*} payload
 * @returns {payload is PlainObject}
 */
function isPlainObject(payload) {
    if (getType(payload) !== 'Object')
        return false;
    return payload.constructor === Object && Object.getPrototypeOf(payload) === Object.prototype;
}
/**
 * Returns whether the payload is a plain JavaScript object (excluding special classes or objects with other prototypes)
 *
 * @param {*} payload
 * @returns {payload is PlainObject}
 */
function isObject(payload) {
    return isPlainObject(payload);
}
/**
 * Returns whether the payload is a an empty object (excluding special classes or objects with other prototypes)
 *
 * @param {*} payload
 * @returns {payload is { [K in any]: never }}
 */
function isEmptyObject(payload) {
    return isPlainObject(payload) && Object.keys(payload).length === 0;
}
/**
 * Returns whether the payload is a an empty object (excluding special classes or objects with other prototypes)
 *
 * @param {*} payload
 * @returns {payload is PlainObject}
 */
function isFullObject(payload) {
    return isPlainObject(payload) && Object.keys(payload).length > 0;
}
/**
 * Returns whether the payload is an any kind of object (including special classes or objects with different prototypes)
 *
 * @param {*} payload
 * @returns {payload is PlainObject}
 */
function isAnyObject(payload) {
    return getType(payload) === 'Object';
}
/**
 * Returns whether the payload is an object like a type passed in < >
 *
 * Usage: isObjectLike<{id: any}>(payload) // will make sure it's an object and has an `id` prop.
 *
 * @template T this must be passed in < >
 * @param {*} payload
 * @returns {payload is T}
 */
function isObjectLike(payload) {
    return isAnyObject(payload);
}
/**
 * Returns whether the payload is a function (regular or async)
 *
 * @param {*} payload
 * @returns {payload is AnyFunction}
 */
function isFunction(payload) {
    return typeof payload === 'function';
}
/**
 * Returns whether the payload is an array
 *
 * @param {any} payload
 * @returns {payload is any[]}
 */
function isArray(payload) {
    return getType(payload) === 'Array';
}
/**
 * Returns whether the payload is a an array with at least 1 item
 *
 * @param {*} payload
 * @returns {payload is any[]}
 */
function isFullArray(payload) {
    return isArray(payload) && payload.length > 0;
}
/**
 * Returns whether the payload is a an empty array
 *
 * @param {*} payload
 * @returns {payload is []}
 */
function isEmptyArray(payload) {
    return isArray(payload) && payload.length === 0;
}
/**
 * Returns whether the payload is a string
 *
 * @param {*} payload
 * @returns {payload is string}
 */
function isString(payload) {
    return getType(payload) === 'String';
}
/**
 * Returns whether the payload is a string, BUT returns false for ''
 *
 * @param {*} payload
 * @returns {payload is string}
 */
function isFullString(payload) {
    return isString(payload) && payload !== '';
}
/**
 * Returns whether the payload is ''
 *
 * @param {*} payload
 * @returns {payload is string}
 */
function isEmptyString(payload) {
    return payload === '';
}
/**
 * Returns whether the payload is a number (but not NaN)
 *
 * This will return `false` for `NaN`!!
 *
 * @param {*} payload
 * @returns {payload is number}
 */
function isNumber(payload) {
    return getType(payload) === 'Number' && !isNaN(payload);
}
/**
 * Returns whether the payload is a boolean
 *
 * @param {*} payload
 * @returns {payload is boolean}
 */
function isBoolean(payload) {
    return getType(payload) === 'Boolean';
}
/**
 * Returns whether the payload is a regular expression (RegExp)
 *
 * @param {*} payload
 * @returns {payload is RegExp}
 */
function isRegExp(payload) {
    return getType(payload) === 'RegExp';
}
/**
 * Returns whether the payload is a Map
 *
 * @param {*} payload
 * @returns {payload is Map<any, any>}
 */
function isMap(payload) {
    return getType(payload) === 'Map';
}
/**
 * Returns whether the payload is a WeakMap
 *
 * @param {*} payload
 * @returns {payload is WeakMap<any, any>}
 */
function isWeakMap(payload) {
    return getType(payload) === 'WeakMap';
}
/**
 * Returns whether the payload is a Set
 *
 * @param {*} payload
 * @returns {payload is Set<any>}
 */
function isSet(payload) {
    return getType(payload) === 'Set';
}
/**
 * Returns whether the payload is a WeakSet
 *
 * @param {*} payload
 * @returns {payload is WeakSet<any>}
 */
function isWeakSet(payload) {
    return getType(payload) === 'WeakSet';
}
/**
 * Returns whether the payload is a Symbol
 *
 * @param {*} payload
 * @returns {payload is symbol}
 */
function isSymbol(payload) {
    return getType(payload) === 'Symbol';
}
/**
 * Returns whether the payload is a Date, and that the date is valid
 *
 * @param {*} payload
 * @returns {payload is Date}
 */
function isDate(payload) {
    return getType(payload) === 'Date' && !isNaN(payload);
}
/**
 * Returns whether the payload is a Blob
 *
 * @param {*} payload
 * @returns {payload is Blob}
 */
function isBlob(payload) {
    return getType(payload) === 'Blob';
}
/**
 * Returns whether the payload is a File
 *
 * @param {*} payload
 * @returns {payload is File}
 */
function isFile(payload) {
    return getType(payload) === 'File';
}
/**
 * Returns whether the payload is a Promise
 *
 * @param {*} payload
 * @returns {payload is Promise<any>}
 */
function isPromise(payload) {
    return getType(payload) === 'Promise';
}
/**
 * Returns whether the payload is an Error
 *
 * @param {*} payload
 * @returns {payload is Error}
 */
function isError(payload) {
    return getType(payload) === 'Error';
}
/**
 * Returns whether the payload is literally the value `NaN` (it's `NaN` and also a `number`)
 *
 * @param {*} payload
 * @returns {payload is typeof NaN}
 */
function isNaNValue(payload) {
    return getType(payload) === 'Number' && isNaN(payload);
}
/**
 * Returns whether the payload is a primitive type (eg. Boolean | Null | Undefined | Number | String | Symbol)
 *
 * @param {*} payload
 * @returns {(payload is boolean | null | undefined | number | string | symbol)}
 */
function isPrimitive(payload) {
    return (isBoolean(payload) ||
        isNull(payload) ||
        isUndefined(payload) ||
        isNumber(payload) ||
        isString(payload) ||
        isSymbol(payload));
}
/**
 * Returns true whether the payload is null or undefined
 *
 * @param {*} payload
 * @returns {(payload is null | undefined)}
 */
var isNullOrUndefined = isOneOf(isNull, isUndefined);
function isOneOf(a, b, c, d, e) {
    return function (value) {
        return a(value) || b(value) || (!!c && c(value)) || (!!d && d(value)) || (!!e && e(value));
    };
}
/**
 * Does a generic check to check that the given payload is of a given type.
 * In cases like Number, it will return true for NaN as NaN is a Number (thanks javascript!);
 * It will, however, differentiate between object and null
 *
 * @template T
 * @param {*} payload
 * @param {T} type
 * @throws {TypeError} Will throw type error if type is an invalid type
 * @returns {payload is T}
 */
function isType(payload, type) {
    if (!(type instanceof Function)) {
        throw new TypeError('Type must be a function');
    }
    if (!Object.prototype.hasOwnProperty.call(type, 'prototype')) {
        throw new TypeError('Type is not a class');
    }
    // Classes usually have names (as functions usually have names)
    var name = type.name;
    return getType(payload) === name || Boolean(payload && payload.constructor === type);
}




/***/ }),

/***/ "./node_modules/just-curry-it/index.js":
/*!*********************************************!*\
  !*** ./node_modules/just-curry-it/index.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = curry;

/*
  function add(a, b, c) {
    return a + b + c;
  }
  curry(add)(1)(2)(3); // 6
  curry(add)(1)(2)(2); // 5
  curry(add)(2)(4, 3); // 9

  function add(...args) {
    return args.reduce((sum, n) => sum + n, 0)
  }
  var curryAdd4 = curry(add, 4)
  curryAdd4(1)(2, 3)(4); // 10

  function converter(ratio, input) {
    return (input*ratio).toFixed(1);
  }
  const curriedConverter = curry(converter)
  const milesToKm = curriedConverter(1.62);
  milesToKm(35); // 56.7
  milesToKm(10); // 16.2
*/

function curry(fn, arity) {
  return function curried() {
    if (arity == null) {
      arity = fn.length;
    }
    var args = [].slice.call(arguments);
    if (args.length >= arity) {
      return fn.apply(this, args);
    } else {
      return function() {
        return curried.apply(this, args.concat([].slice.call(arguments)));
      };
    }
  };
}


/***/ }),

/***/ "./node_modules/memoize-one/dist/memoize-one.esm.js":
/*!**********************************************************!*\
  !*** ./node_modules/memoize-one/dist/memoize-one.esm.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var safeIsNaN = Number.isNaN ||
    function ponyfill(value) {
        return typeof value === 'number' && value !== value;
    };
function isEqual(first, second) {
    if (first === second) {
        return true;
    }
    if (safeIsNaN(first) && safeIsNaN(second)) {
        return true;
    }
    return false;
}
function areInputsEqual(newInputs, lastInputs) {
    if (newInputs.length !== lastInputs.length) {
        return false;
    }
    for (var i = 0; i < newInputs.length; i++) {
        if (!isEqual(newInputs[i], lastInputs[i])) {
            return false;
        }
    }
    return true;
}

function memoizeOne(resultFn, isEqual) {
    if (isEqual === void 0) { isEqual = areInputsEqual; }
    var lastThis;
    var lastArgs = [];
    var lastResult;
    var calledOnce = false;
    function memoized() {
        var newArgs = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            newArgs[_i] = arguments[_i];
        }
        if (calledOnce && lastThis === this && isEqual(newArgs, lastArgs)) {
            return lastResult;
        }
        lastResult = resultFn.apply(this, newArgs);
        calledOnce = true;
        lastThis = this;
        lastArgs = newArgs;
        return lastResult;
    }
    return memoized;
}

/* harmony default export */ __webpack_exports__["default"] = (memoizeOne);


/***/ }),

/***/ "./node_modules/merge-anything/dist/index.esm.js":
/*!*******************************************************!*\
  !*** ./node_modules/merge-anything/dist/index.esm.js ***!
  \*******************************************************/
/*! exports provided: default, concatArrays, merge */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "concatArrays", function() { return concatArrays; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "merge", function() { return merge; });
/* harmony import */ var is_what__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! is-what */ "./node_modules/is-what/dist/index.esm.js");


/*! *****************************************************************************
Copyright (c) Microsoft Corporation. All rights reserved.
Licensed under the Apache License, Version 2.0 (the "License"); you may not use
this file except in compliance with the License. You may obtain a copy of the
License at http://www.apache.org/licenses/LICENSE-2.0

THIS CODE IS PROVIDED ON AN *AS IS* BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
KIND, EITHER EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION ANY IMPLIED
WARRANTIES OR CONDITIONS OF TITLE, FITNESS FOR A PARTICULAR PURPOSE,
MERCHANTABLITY OR NON-INFRINGEMENT.

See the Apache Version 2.0 License for specific language governing permissions
and limitations under the License.
***************************************************************************** */

function __spreadArrays() {
    for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
    for (var r = Array(s), k = 0, i = 0; i < il; i++)
        for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
            r[k] = a[j];
    return r;
}

function assignProp(carry, key, newVal, originalObject) {
    var propType = originalObject.propertyIsEnumerable(key)
        ? 'enumerable'
        : 'nonenumerable';
    if (propType === 'enumerable')
        carry[key] = newVal;
    if (propType === 'nonenumerable') {
        Object.defineProperty(carry, key, {
            value: newVal,
            enumerable: false,
            writable: true,
            configurable: true
        });
    }
}
function mergeRecursively(origin, newComer, extensions) {
    // work directly on newComer if its not an object
    if (!Object(is_what__WEBPACK_IMPORTED_MODULE_0__["isPlainObject"])(newComer)) {
        // extend merge rules
        if (extensions && Object(is_what__WEBPACK_IMPORTED_MODULE_0__["isArray"])(extensions)) {
            extensions.forEach(function (extend) {
                newComer = extend(origin, newComer);
            });
        }
        return newComer;
    }
    // define newObject to merge all values upon
    var newObject = {};
    if (Object(is_what__WEBPACK_IMPORTED_MODULE_0__["isPlainObject"])(origin)) {
        var props_1 = Object.getOwnPropertyNames(origin);
        var symbols_1 = Object.getOwnPropertySymbols(origin);
        newObject = __spreadArrays(props_1, symbols_1).reduce(function (carry, key) {
            // @ts-ignore
            var targetVal = origin[key];
            if ((!Object(is_what__WEBPACK_IMPORTED_MODULE_0__["isSymbol"])(key) && !Object.getOwnPropertyNames(newComer).includes(key)) ||
                (Object(is_what__WEBPACK_IMPORTED_MODULE_0__["isSymbol"])(key) && !Object.getOwnPropertySymbols(newComer).includes(key))) {
                assignProp(carry, key, targetVal, origin);
            }
            return carry;
        }, {});
    }
    var props = Object.getOwnPropertyNames(newComer);
    var symbols = Object.getOwnPropertySymbols(newComer);
    var result = __spreadArrays(props, symbols).reduce(function (carry, key) {
        // re-define the origin and newComer as targetVal and newVal
        var newVal = newComer[key];
        var targetVal = (Object(is_what__WEBPACK_IMPORTED_MODULE_0__["isPlainObject"])(origin))
            // @ts-ignore
            ? origin[key]
            : undefined;
        // extend merge rules
        if (extensions && Object(is_what__WEBPACK_IMPORTED_MODULE_0__["isArray"])(extensions)) {
            extensions.forEach(function (extend) {
                newVal = extend(targetVal, newVal);
            });
        }
        // When newVal is an object do the merge recursively
        if (targetVal !== undefined && Object(is_what__WEBPACK_IMPORTED_MODULE_0__["isPlainObject"])(newVal)) {
            newVal = mergeRecursively(targetVal, newVal, extensions);
        }
        assignProp(carry, key, newVal, newComer);
        return carry;
    }, newObject);
    return result;
}
/**
 * Merge anything recursively.
 * Objects get merged, special objects (classes etc.) are re-assigned "as is".
 * Basic types overwrite objects or other basic types.
 *
 * @param {(IConfig | any)} origin
 * @param {...any[]} newComers
 * @returns the result
 */
function merge(origin) {
    var newComers = [];
    for (var _i = 1; _i < arguments.length; _i++) {
        newComers[_i - 1] = arguments[_i];
    }
    var extensions = null;
    var base = origin;
    if (Object(is_what__WEBPACK_IMPORTED_MODULE_0__["isPlainObject"])(origin) && origin.extensions && Object.keys(origin).length === 1) {
        base = {};
        extensions = origin.extensions;
    }
    return newComers.reduce(function (result, newComer) {
        return mergeRecursively(result, newComer, extensions);
    }, base);
}

function concatArrays(originVal, newVal) {
    if (Object(is_what__WEBPACK_IMPORTED_MODULE_0__["isArray"])(originVal) && Object(is_what__WEBPACK_IMPORTED_MODULE_0__["isArray"])(newVal)) {
        // concat logic
        return originVal.concat(newVal);
    }
    return newVal; // always return newVal as fallback!!
}

/* harmony default export */ __webpack_exports__["default"] = (merge);



/***/ }),

/***/ "./node_modules/object-assign/index.js":
/*!*********************************************!*\
  !*** ./node_modules/object-assign/index.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/*
object-assign
(c) Sindre Sorhus
@license MIT
*/


/* eslint-disable no-unused-vars */
var getOwnPropertySymbols = Object.getOwnPropertySymbols;
var hasOwnProperty = Object.prototype.hasOwnProperty;
var propIsEnumerable = Object.prototype.propertyIsEnumerable;

function toObject(val) {
	if (val === null || val === undefined) {
		throw new TypeError('Object.assign cannot be called with null or undefined');
	}

	return Object(val);
}

function shouldUseNative() {
	try {
		if (!Object.assign) {
			return false;
		}

		// Detect buggy property enumeration order in older V8 versions.

		// https://bugs.chromium.org/p/v8/issues/detail?id=4118
		var test1 = new String('abc');  // eslint-disable-line no-new-wrappers
		test1[5] = 'de';
		if (Object.getOwnPropertyNames(test1)[0] === '5') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test2 = {};
		for (var i = 0; i < 10; i++) {
			test2['_' + String.fromCharCode(i)] = i;
		}
		var order2 = Object.getOwnPropertyNames(test2).map(function (n) {
			return test2[n];
		});
		if (order2.join('') !== '0123456789') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test3 = {};
		'abcdefghijklmnopqrst'.split('').forEach(function (letter) {
			test3[letter] = letter;
		});
		if (Object.keys(Object.assign({}, test3)).join('') !==
				'abcdefghijklmnopqrst') {
			return false;
		}

		return true;
	} catch (err) {
		// We don't expect any of the above to throw, but better to be safe.
		return false;
	}
}

module.exports = shouldUseNative() ? Object.assign : function (target, source) {
	var from;
	var to = toObject(target);
	var symbols;

	for (var s = 1; s < arguments.length; s++) {
		from = Object(arguments[s]);

		for (var key in from) {
			if (hasOwnProperty.call(from, key)) {
				to[key] = from[key];
			}
		}

		if (getOwnPropertySymbols) {
			symbols = getOwnPropertySymbols(from);
			for (var i = 0; i < symbols.length; i++) {
				if (propIsEnumerable.call(from, symbols[i])) {
					to[symbols[i]] = from[symbols[i]];
				}
			}
		}
	}

	return to;
};


/***/ }),

/***/ "./node_modules/process/browser.js":
/*!*****************************************!*\
  !*** ./node_modules/process/browser.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ }),

/***/ "./node_modules/prop-types/checkPropTypes.js":
/*!***************************************************!*\
  !*** ./node_modules/prop-types/checkPropTypes.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var printWarning = function() {};

if (true) {
  var ReactPropTypesSecret = __webpack_require__(/*! ./lib/ReactPropTypesSecret */ "./node_modules/prop-types/lib/ReactPropTypesSecret.js");
  var loggedTypeFailures = {};
  var has = __webpack_require__(/*! ./lib/has */ "./node_modules/prop-types/lib/has.js");

  printWarning = function(text) {
    var message = 'Warning: ' + text;
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) { /**/ }
  };
}

/**
 * Assert that the values match with the type specs.
 * Error messages are memorized and will only be shown once.
 *
 * @param {object} typeSpecs Map of name to a ReactPropType
 * @param {object} values Runtime values that need to be type-checked
 * @param {string} location e.g. "prop", "context", "child context"
 * @param {string} componentName Name of the component for error messages.
 * @param {?Function} getStack Returns the component stack.
 * @private
 */
function checkPropTypes(typeSpecs, values, location, componentName, getStack) {
  if (true) {
    for (var typeSpecName in typeSpecs) {
      if (has(typeSpecs, typeSpecName)) {
        var error;
        // Prop type validation may throw. In case they do, we don't want to
        // fail the render phase where it didn't fail before. So we log it.
        // After these have been cleaned up, we'll let them throw.
        try {
          // This is intentionally an invariant that gets caught. It's the same
          // behavior as without this statement except with a better message.
          if (typeof typeSpecs[typeSpecName] !== 'function') {
            var err = Error(
              (componentName || 'React class') + ': ' + location + ' type `' + typeSpecName + '` is invalid; ' +
              'it must be a function, usually from the `prop-types` package, but received `' + typeof typeSpecs[typeSpecName] + '`.' +
              'This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.'
            );
            err.name = 'Invariant Violation';
            throw err;
          }
          error = typeSpecs[typeSpecName](values, typeSpecName, componentName, location, null, ReactPropTypesSecret);
        } catch (ex) {
          error = ex;
        }
        if (error && !(error instanceof Error)) {
          printWarning(
            (componentName || 'React class') + ': type specification of ' +
            location + ' `' + typeSpecName + '` is invalid; the type checker ' +
            'function must return `null` or an `Error` but returned a ' + typeof error + '. ' +
            'You may have forgotten to pass an argument to the type checker ' +
            'creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and ' +
            'shape all require an argument).'
          );
        }
        if (error instanceof Error && !(error.message in loggedTypeFailures)) {
          // Only monitor this failure once because there tends to be a lot of the
          // same error.
          loggedTypeFailures[error.message] = true;

          var stack = getStack ? getStack() : '';

          printWarning(
            'Failed ' + location + ' type: ' + error.message + (stack != null ? stack : '')
          );
        }
      }
    }
  }
}

/**
 * Resets warning cache when testing.
 *
 * @private
 */
checkPropTypes.resetWarningCache = function() {
  if (true) {
    loggedTypeFailures = {};
  }
}

module.exports = checkPropTypes;


/***/ }),

/***/ "./node_modules/prop-types/factoryWithTypeCheckers.js":
/*!************************************************************!*\
  !*** ./node_modules/prop-types/factoryWithTypeCheckers.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactIs = __webpack_require__(/*! react-is */ "./node_modules/react-is/index.js");
var assign = __webpack_require__(/*! object-assign */ "./node_modules/object-assign/index.js");

var ReactPropTypesSecret = __webpack_require__(/*! ./lib/ReactPropTypesSecret */ "./node_modules/prop-types/lib/ReactPropTypesSecret.js");
var has = __webpack_require__(/*! ./lib/has */ "./node_modules/prop-types/lib/has.js");
var checkPropTypes = __webpack_require__(/*! ./checkPropTypes */ "./node_modules/prop-types/checkPropTypes.js");

var printWarning = function() {};

if (true) {
  printWarning = function(text) {
    var message = 'Warning: ' + text;
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) {}
  };
}

function emptyFunctionThatReturnsNull() {
  return null;
}

module.exports = function(isValidElement, throwOnDirectAccess) {
  /* global Symbol */
  var ITERATOR_SYMBOL = typeof Symbol === 'function' && Symbol.iterator;
  var FAUX_ITERATOR_SYMBOL = '@@iterator'; // Before Symbol spec.

  /**
   * Returns the iterator method function contained on the iterable object.
   *
   * Be sure to invoke the function with the iterable as context:
   *
   *     var iteratorFn = getIteratorFn(myIterable);
   *     if (iteratorFn) {
   *       var iterator = iteratorFn.call(myIterable);
   *       ...
   *     }
   *
   * @param {?object} maybeIterable
   * @return {?function}
   */
  function getIteratorFn(maybeIterable) {
    var iteratorFn = maybeIterable && (ITERATOR_SYMBOL && maybeIterable[ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL]);
    if (typeof iteratorFn === 'function') {
      return iteratorFn;
    }
  }

  /**
   * Collection of methods that allow declaration and validation of props that are
   * supplied to React components. Example usage:
   *
   *   var Props = require('ReactPropTypes');
   *   var MyArticle = React.createClass({
   *     propTypes: {
   *       // An optional string prop named "description".
   *       description: Props.string,
   *
   *       // A required enum prop named "category".
   *       category: Props.oneOf(['News','Photos']).isRequired,
   *
   *       // A prop named "dialog" that requires an instance of Dialog.
   *       dialog: Props.instanceOf(Dialog).isRequired
   *     },
   *     render: function() { ... }
   *   });
   *
   * A more formal specification of how these methods are used:
   *
   *   type := array|bool|func|object|number|string|oneOf([...])|instanceOf(...)
   *   decl := ReactPropTypes.{type}(.isRequired)?
   *
   * Each and every declaration produces a function with the same signature. This
   * allows the creation of custom validation functions. For example:
   *
   *  var MyLink = React.createClass({
   *    propTypes: {
   *      // An optional string or URI prop named "href".
   *      href: function(props, propName, componentName) {
   *        var propValue = props[propName];
   *        if (propValue != null && typeof propValue !== 'string' &&
   *            !(propValue instanceof URI)) {
   *          return new Error(
   *            'Expected a string or an URI for ' + propName + ' in ' +
   *            componentName
   *          );
   *        }
   *      }
   *    },
   *    render: function() {...}
   *  });
   *
   * @internal
   */

  var ANONYMOUS = '<<anonymous>>';

  // Important!
  // Keep this list in sync with production version in `./factoryWithThrowingShims.js`.
  var ReactPropTypes = {
    array: createPrimitiveTypeChecker('array'),
    bigint: createPrimitiveTypeChecker('bigint'),
    bool: createPrimitiveTypeChecker('boolean'),
    func: createPrimitiveTypeChecker('function'),
    number: createPrimitiveTypeChecker('number'),
    object: createPrimitiveTypeChecker('object'),
    string: createPrimitiveTypeChecker('string'),
    symbol: createPrimitiveTypeChecker('symbol'),

    any: createAnyTypeChecker(),
    arrayOf: createArrayOfTypeChecker,
    element: createElementTypeChecker(),
    elementType: createElementTypeTypeChecker(),
    instanceOf: createInstanceTypeChecker,
    node: createNodeChecker(),
    objectOf: createObjectOfTypeChecker,
    oneOf: createEnumTypeChecker,
    oneOfType: createUnionTypeChecker,
    shape: createShapeTypeChecker,
    exact: createStrictShapeTypeChecker,
  };

  /**
   * inlined Object.is polyfill to avoid requiring consumers ship their own
   * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/is
   */
  /*eslint-disable no-self-compare*/
  function is(x, y) {
    // SameValue algorithm
    if (x === y) {
      // Steps 1-5, 7-10
      // Steps 6.b-6.e: +0 != -0
      return x !== 0 || 1 / x === 1 / y;
    } else {
      // Step 6.a: NaN == NaN
      return x !== x && y !== y;
    }
  }
  /*eslint-enable no-self-compare*/

  /**
   * We use an Error-like object for backward compatibility as people may call
   * PropTypes directly and inspect their output. However, we don't use real
   * Errors anymore. We don't inspect their stack anyway, and creating them
   * is prohibitively expensive if they are created too often, such as what
   * happens in oneOfType() for any type before the one that matched.
   */
  function PropTypeError(message, data) {
    this.message = message;
    this.data = data && typeof data === 'object' ? data: {};
    this.stack = '';
  }
  // Make `instanceof Error` still work for returned errors.
  PropTypeError.prototype = Error.prototype;

  function createChainableTypeChecker(validate) {
    if (true) {
      var manualPropTypeCallCache = {};
      var manualPropTypeWarningCount = 0;
    }
    function checkType(isRequired, props, propName, componentName, location, propFullName, secret) {
      componentName = componentName || ANONYMOUS;
      propFullName = propFullName || propName;

      if (secret !== ReactPropTypesSecret) {
        if (throwOnDirectAccess) {
          // New behavior only for users of `prop-types` package
          var err = new Error(
            'Calling PropTypes validators directly is not supported by the `prop-types` package. ' +
            'Use `PropTypes.checkPropTypes()` to call them. ' +
            'Read more at http://fb.me/use-check-prop-types'
          );
          err.name = 'Invariant Violation';
          throw err;
        } else if ( true && typeof console !== 'undefined') {
          // Old behavior for people using React.PropTypes
          var cacheKey = componentName + ':' + propName;
          if (
            !manualPropTypeCallCache[cacheKey] &&
            // Avoid spamming the console because they are often not actionable except for lib authors
            manualPropTypeWarningCount < 3
          ) {
            printWarning(
              'You are manually calling a React.PropTypes validation ' +
              'function for the `' + propFullName + '` prop on `' + componentName + '`. This is deprecated ' +
              'and will throw in the standalone `prop-types` package. ' +
              'You may be seeing this warning due to a third-party PropTypes ' +
              'library. See https://fb.me/react-warning-dont-call-proptypes ' + 'for details.'
            );
            manualPropTypeCallCache[cacheKey] = true;
            manualPropTypeWarningCount++;
          }
        }
      }
      if (props[propName] == null) {
        if (isRequired) {
          if (props[propName] === null) {
            return new PropTypeError('The ' + location + ' `' + propFullName + '` is marked as required ' + ('in `' + componentName + '`, but its value is `null`.'));
          }
          return new PropTypeError('The ' + location + ' `' + propFullName + '` is marked as required in ' + ('`' + componentName + '`, but its value is `undefined`.'));
        }
        return null;
      } else {
        return validate(props, propName, componentName, location, propFullName);
      }
    }

    var chainedCheckType = checkType.bind(null, false);
    chainedCheckType.isRequired = checkType.bind(null, true);

    return chainedCheckType;
  }

  function createPrimitiveTypeChecker(expectedType) {
    function validate(props, propName, componentName, location, propFullName, secret) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== expectedType) {
        // `propValue` being instance of, say, date/regexp, pass the 'object'
        // check, but we can offer a more precise error message here rather than
        // 'of type `object`'.
        var preciseType = getPreciseType(propValue);

        return new PropTypeError(
          'Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + preciseType + '` supplied to `' + componentName + '`, expected ') + ('`' + expectedType + '`.'),
          {expectedType: expectedType}
        );
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createAnyTypeChecker() {
    return createChainableTypeChecker(emptyFunctionThatReturnsNull);
  }

  function createArrayOfTypeChecker(typeChecker) {
    function validate(props, propName, componentName, location, propFullName) {
      if (typeof typeChecker !== 'function') {
        return new PropTypeError('Property `' + propFullName + '` of component `' + componentName + '` has invalid PropType notation inside arrayOf.');
      }
      var propValue = props[propName];
      if (!Array.isArray(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected an array.'));
      }
      for (var i = 0; i < propValue.length; i++) {
        var error = typeChecker(propValue, i, componentName, location, propFullName + '[' + i + ']', ReactPropTypesSecret);
        if (error instanceof Error) {
          return error;
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createElementTypeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      if (!isValidElement(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected a single ReactElement.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createElementTypeTypeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      if (!ReactIs.isValidElementType(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected a single ReactElement type.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createInstanceTypeChecker(expectedClass) {
    function validate(props, propName, componentName, location, propFullName) {
      if (!(props[propName] instanceof expectedClass)) {
        var expectedClassName = expectedClass.name || ANONYMOUS;
        var actualClassName = getClassName(props[propName]);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + actualClassName + '` supplied to `' + componentName + '`, expected ') + ('instance of `' + expectedClassName + '`.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createEnumTypeChecker(expectedValues) {
    if (!Array.isArray(expectedValues)) {
      if (true) {
        if (arguments.length > 1) {
          printWarning(
            'Invalid arguments supplied to oneOf, expected an array, got ' + arguments.length + ' arguments. ' +
            'A common mistake is to write oneOf(x, y, z) instead of oneOf([x, y, z]).'
          );
        } else {
          printWarning('Invalid argument supplied to oneOf, expected an array.');
        }
      }
      return emptyFunctionThatReturnsNull;
    }

    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      for (var i = 0; i < expectedValues.length; i++) {
        if (is(propValue, expectedValues[i])) {
          return null;
        }
      }

      var valuesString = JSON.stringify(expectedValues, function replacer(key, value) {
        var type = getPreciseType(value);
        if (type === 'symbol') {
          return String(value);
        }
        return value;
      });
      return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of value `' + String(propValue) + '` ' + ('supplied to `' + componentName + '`, expected one of ' + valuesString + '.'));
    }
    return createChainableTypeChecker(validate);
  }

  function createObjectOfTypeChecker(typeChecker) {
    function validate(props, propName, componentName, location, propFullName) {
      if (typeof typeChecker !== 'function') {
        return new PropTypeError('Property `' + propFullName + '` of component `' + componentName + '` has invalid PropType notation inside objectOf.');
      }
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected an object.'));
      }
      for (var key in propValue) {
        if (has(propValue, key)) {
          var error = typeChecker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
          if (error instanceof Error) {
            return error;
          }
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createUnionTypeChecker(arrayOfTypeCheckers) {
    if (!Array.isArray(arrayOfTypeCheckers)) {
       true ? printWarning('Invalid argument supplied to oneOfType, expected an instance of array.') : undefined;
      return emptyFunctionThatReturnsNull;
    }

    for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
      var checker = arrayOfTypeCheckers[i];
      if (typeof checker !== 'function') {
        printWarning(
          'Invalid argument supplied to oneOfType. Expected an array of check functions, but ' +
          'received ' + getPostfixForTypeWarning(checker) + ' at index ' + i + '.'
        );
        return emptyFunctionThatReturnsNull;
      }
    }

    function validate(props, propName, componentName, location, propFullName) {
      var expectedTypes = [];
      for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
        var checker = arrayOfTypeCheckers[i];
        var checkerResult = checker(props, propName, componentName, location, propFullName, ReactPropTypesSecret);
        if (checkerResult == null) {
          return null;
        }
        if (checkerResult.data && has(checkerResult.data, 'expectedType')) {
          expectedTypes.push(checkerResult.data.expectedType);
        }
      }
      var expectedTypesMessage = (expectedTypes.length > 0) ? ', expected one of type [' + expectedTypes.join(', ') + ']': '';
      return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` supplied to ' + ('`' + componentName + '`' + expectedTypesMessage + '.'));
    }
    return createChainableTypeChecker(validate);
  }

  function createNodeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      if (!isNode(props[propName])) {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` supplied to ' + ('`' + componentName + '`, expected a ReactNode.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function invalidValidatorError(componentName, location, propFullName, key, type) {
    return new PropTypeError(
      (componentName || 'React class') + ': ' + location + ' type `' + propFullName + '.' + key + '` is invalid; ' +
      'it must be a function, usually from the `prop-types` package, but received `' + type + '`.'
    );
  }

  function createShapeTypeChecker(shapeTypes) {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type `' + propType + '` ' + ('supplied to `' + componentName + '`, expected `object`.'));
      }
      for (var key in shapeTypes) {
        var checker = shapeTypes[key];
        if (typeof checker !== 'function') {
          return invalidValidatorError(componentName, location, propFullName, key, getPreciseType(checker));
        }
        var error = checker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
        if (error) {
          return error;
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createStrictShapeTypeChecker(shapeTypes) {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type `' + propType + '` ' + ('supplied to `' + componentName + '`, expected `object`.'));
      }
      // We need to check all keys in case some are required but missing from props.
      var allKeys = assign({}, props[propName], shapeTypes);
      for (var key in allKeys) {
        var checker = shapeTypes[key];
        if (has(shapeTypes, key) && typeof checker !== 'function') {
          return invalidValidatorError(componentName, location, propFullName, key, getPreciseType(checker));
        }
        if (!checker) {
          return new PropTypeError(
            'Invalid ' + location + ' `' + propFullName + '` key `' + key + '` supplied to `' + componentName + '`.' +
            '\nBad object: ' + JSON.stringify(props[propName], null, '  ') +
            '\nValid keys: ' + JSON.stringify(Object.keys(shapeTypes), null, '  ')
          );
        }
        var error = checker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
        if (error) {
          return error;
        }
      }
      return null;
    }

    return createChainableTypeChecker(validate);
  }

  function isNode(propValue) {
    switch (typeof propValue) {
      case 'number':
      case 'string':
      case 'undefined':
        return true;
      case 'boolean':
        return !propValue;
      case 'object':
        if (Array.isArray(propValue)) {
          return propValue.every(isNode);
        }
        if (propValue === null || isValidElement(propValue)) {
          return true;
        }

        var iteratorFn = getIteratorFn(propValue);
        if (iteratorFn) {
          var iterator = iteratorFn.call(propValue);
          var step;
          if (iteratorFn !== propValue.entries) {
            while (!(step = iterator.next()).done) {
              if (!isNode(step.value)) {
                return false;
              }
            }
          } else {
            // Iterator will provide entry [k,v] tuples rather than values.
            while (!(step = iterator.next()).done) {
              var entry = step.value;
              if (entry) {
                if (!isNode(entry[1])) {
                  return false;
                }
              }
            }
          }
        } else {
          return false;
        }

        return true;
      default:
        return false;
    }
  }

  function isSymbol(propType, propValue) {
    // Native Symbol.
    if (propType === 'symbol') {
      return true;
    }

    // falsy value can't be a Symbol
    if (!propValue) {
      return false;
    }

    // 19.4.3.5 Symbol.prototype[@@toStringTag] === 'Symbol'
    if (propValue['@@toStringTag'] === 'Symbol') {
      return true;
    }

    // Fallback for non-spec compliant Symbols which are polyfilled.
    if (typeof Symbol === 'function' && propValue instanceof Symbol) {
      return true;
    }

    return false;
  }

  // Equivalent of `typeof` but with special handling for array and regexp.
  function getPropType(propValue) {
    var propType = typeof propValue;
    if (Array.isArray(propValue)) {
      return 'array';
    }
    if (propValue instanceof RegExp) {
      // Old webkits (at least until Android 4.0) return 'function' rather than
      // 'object' for typeof a RegExp. We'll normalize this here so that /bla/
      // passes PropTypes.object.
      return 'object';
    }
    if (isSymbol(propType, propValue)) {
      return 'symbol';
    }
    return propType;
  }

  // This handles more types than `getPropType`. Only used for error messages.
  // See `createPrimitiveTypeChecker`.
  function getPreciseType(propValue) {
    if (typeof propValue === 'undefined' || propValue === null) {
      return '' + propValue;
    }
    var propType = getPropType(propValue);
    if (propType === 'object') {
      if (propValue instanceof Date) {
        return 'date';
      } else if (propValue instanceof RegExp) {
        return 'regexp';
      }
    }
    return propType;
  }

  // Returns a string that is postfixed to a warning about an invalid type.
  // For example, "undefined" or "of type array"
  function getPostfixForTypeWarning(value) {
    var type = getPreciseType(value);
    switch (type) {
      case 'array':
      case 'object':
        return 'an ' + type;
      case 'boolean':
      case 'date':
      case 'regexp':
        return 'a ' + type;
      default:
        return type;
    }
  }

  // Returns class name of the object, if any.
  function getClassName(propValue) {
    if (!propValue.constructor || !propValue.constructor.name) {
      return ANONYMOUS;
    }
    return propValue.constructor.name;
  }

  ReactPropTypes.checkPropTypes = checkPropTypes;
  ReactPropTypes.resetWarningCache = checkPropTypes.resetWarningCache;
  ReactPropTypes.PropTypes = ReactPropTypes;

  return ReactPropTypes;
};


/***/ }),

/***/ "./node_modules/prop-types/index.js":
/*!******************************************!*\
  !*** ./node_modules/prop-types/index.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (true) {
  var ReactIs = __webpack_require__(/*! react-is */ "./node_modules/react-is/index.js");

  // By explicitly using `prop-types` you are opting into new development behavior.
  // http://fb.me/prop-types-in-prod
  var throwOnDirectAccess = true;
  module.exports = __webpack_require__(/*! ./factoryWithTypeCheckers */ "./node_modules/prop-types/factoryWithTypeCheckers.js")(ReactIs.isElement, throwOnDirectAccess);
} else {}


/***/ }),

/***/ "./node_modules/prop-types/lib/ReactPropTypesSecret.js":
/*!*************************************************************!*\
  !*** ./node_modules/prop-types/lib/ReactPropTypesSecret.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED';

module.exports = ReactPropTypesSecret;


/***/ }),

/***/ "./node_modules/prop-types/lib/has.js":
/*!********************************************!*\
  !*** ./node_modules/prop-types/lib/has.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = Function.call.bind(Object.prototype.hasOwnProperty);


/***/ }),

/***/ "./node_modules/react-is/cjs/react-is.development.js":
/*!***********************************************************!*\
  !*** ./node_modules/react-is/cjs/react-is.development.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/** @license React v16.13.1
 * react-is.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */





if (true) {
  (function() {
'use strict';

// The Symbol used to tag the ReactElement-like types. If there is no native Symbol
// nor polyfill, then a plain number is used for performance.
var hasSymbol = typeof Symbol === 'function' && Symbol.for;
var REACT_ELEMENT_TYPE = hasSymbol ? Symbol.for('react.element') : 0xeac7;
var REACT_PORTAL_TYPE = hasSymbol ? Symbol.for('react.portal') : 0xeaca;
var REACT_FRAGMENT_TYPE = hasSymbol ? Symbol.for('react.fragment') : 0xeacb;
var REACT_STRICT_MODE_TYPE = hasSymbol ? Symbol.for('react.strict_mode') : 0xeacc;
var REACT_PROFILER_TYPE = hasSymbol ? Symbol.for('react.profiler') : 0xead2;
var REACT_PROVIDER_TYPE = hasSymbol ? Symbol.for('react.provider') : 0xeacd;
var REACT_CONTEXT_TYPE = hasSymbol ? Symbol.for('react.context') : 0xeace; // TODO: We don't use AsyncMode or ConcurrentMode anymore. They were temporary
// (unstable) APIs that have been removed. Can we remove the symbols?

var REACT_ASYNC_MODE_TYPE = hasSymbol ? Symbol.for('react.async_mode') : 0xeacf;
var REACT_CONCURRENT_MODE_TYPE = hasSymbol ? Symbol.for('react.concurrent_mode') : 0xeacf;
var REACT_FORWARD_REF_TYPE = hasSymbol ? Symbol.for('react.forward_ref') : 0xead0;
var REACT_SUSPENSE_TYPE = hasSymbol ? Symbol.for('react.suspense') : 0xead1;
var REACT_SUSPENSE_LIST_TYPE = hasSymbol ? Symbol.for('react.suspense_list') : 0xead8;
var REACT_MEMO_TYPE = hasSymbol ? Symbol.for('react.memo') : 0xead3;
var REACT_LAZY_TYPE = hasSymbol ? Symbol.for('react.lazy') : 0xead4;
var REACT_BLOCK_TYPE = hasSymbol ? Symbol.for('react.block') : 0xead9;
var REACT_FUNDAMENTAL_TYPE = hasSymbol ? Symbol.for('react.fundamental') : 0xead5;
var REACT_RESPONDER_TYPE = hasSymbol ? Symbol.for('react.responder') : 0xead6;
var REACT_SCOPE_TYPE = hasSymbol ? Symbol.for('react.scope') : 0xead7;

function isValidElementType(type) {
  return typeof type === 'string' || typeof type === 'function' || // Note: its typeof might be other than 'symbol' or 'number' if it's a polyfill.
  type === REACT_FRAGMENT_TYPE || type === REACT_CONCURRENT_MODE_TYPE || type === REACT_PROFILER_TYPE || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || type === REACT_SUSPENSE_LIST_TYPE || typeof type === 'object' && type !== null && (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_PROVIDER_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE || type.$$typeof === REACT_FUNDAMENTAL_TYPE || type.$$typeof === REACT_RESPONDER_TYPE || type.$$typeof === REACT_SCOPE_TYPE || type.$$typeof === REACT_BLOCK_TYPE);
}

function typeOf(object) {
  if (typeof object === 'object' && object !== null) {
    var $$typeof = object.$$typeof;

    switch ($$typeof) {
      case REACT_ELEMENT_TYPE:
        var type = object.type;

        switch (type) {
          case REACT_ASYNC_MODE_TYPE:
          case REACT_CONCURRENT_MODE_TYPE:
          case REACT_FRAGMENT_TYPE:
          case REACT_PROFILER_TYPE:
          case REACT_STRICT_MODE_TYPE:
          case REACT_SUSPENSE_TYPE:
            return type;

          default:
            var $$typeofType = type && type.$$typeof;

            switch ($$typeofType) {
              case REACT_CONTEXT_TYPE:
              case REACT_FORWARD_REF_TYPE:
              case REACT_LAZY_TYPE:
              case REACT_MEMO_TYPE:
              case REACT_PROVIDER_TYPE:
                return $$typeofType;

              default:
                return $$typeof;
            }

        }

      case REACT_PORTAL_TYPE:
        return $$typeof;
    }
  }

  return undefined;
} // AsyncMode is deprecated along with isAsyncMode

var AsyncMode = REACT_ASYNC_MODE_TYPE;
var ConcurrentMode = REACT_CONCURRENT_MODE_TYPE;
var ContextConsumer = REACT_CONTEXT_TYPE;
var ContextProvider = REACT_PROVIDER_TYPE;
var Element = REACT_ELEMENT_TYPE;
var ForwardRef = REACT_FORWARD_REF_TYPE;
var Fragment = REACT_FRAGMENT_TYPE;
var Lazy = REACT_LAZY_TYPE;
var Memo = REACT_MEMO_TYPE;
var Portal = REACT_PORTAL_TYPE;
var Profiler = REACT_PROFILER_TYPE;
var StrictMode = REACT_STRICT_MODE_TYPE;
var Suspense = REACT_SUSPENSE_TYPE;
var hasWarnedAboutDeprecatedIsAsyncMode = false; // AsyncMode should be deprecated

function isAsyncMode(object) {
  {
    if (!hasWarnedAboutDeprecatedIsAsyncMode) {
      hasWarnedAboutDeprecatedIsAsyncMode = true; // Using console['warn'] to evade Babel and ESLint

      console['warn']('The ReactIs.isAsyncMode() alias has been deprecated, ' + 'and will be removed in React 17+. Update your code to use ' + 'ReactIs.isConcurrentMode() instead. It has the exact same API.');
    }
  }

  return isConcurrentMode(object) || typeOf(object) === REACT_ASYNC_MODE_TYPE;
}
function isConcurrentMode(object) {
  return typeOf(object) === REACT_CONCURRENT_MODE_TYPE;
}
function isContextConsumer(object) {
  return typeOf(object) === REACT_CONTEXT_TYPE;
}
function isContextProvider(object) {
  return typeOf(object) === REACT_PROVIDER_TYPE;
}
function isElement(object) {
  return typeof object === 'object' && object !== null && object.$$typeof === REACT_ELEMENT_TYPE;
}
function isForwardRef(object) {
  return typeOf(object) === REACT_FORWARD_REF_TYPE;
}
function isFragment(object) {
  return typeOf(object) === REACT_FRAGMENT_TYPE;
}
function isLazy(object) {
  return typeOf(object) === REACT_LAZY_TYPE;
}
function isMemo(object) {
  return typeOf(object) === REACT_MEMO_TYPE;
}
function isPortal(object) {
  return typeOf(object) === REACT_PORTAL_TYPE;
}
function isProfiler(object) {
  return typeOf(object) === REACT_PROFILER_TYPE;
}
function isStrictMode(object) {
  return typeOf(object) === REACT_STRICT_MODE_TYPE;
}
function isSuspense(object) {
  return typeOf(object) === REACT_SUSPENSE_TYPE;
}

exports.AsyncMode = AsyncMode;
exports.ConcurrentMode = ConcurrentMode;
exports.ContextConsumer = ContextConsumer;
exports.ContextProvider = ContextProvider;
exports.Element = Element;
exports.ForwardRef = ForwardRef;
exports.Fragment = Fragment;
exports.Lazy = Lazy;
exports.Memo = Memo;
exports.Portal = Portal;
exports.Profiler = Profiler;
exports.StrictMode = StrictMode;
exports.Suspense = Suspense;
exports.isAsyncMode = isAsyncMode;
exports.isConcurrentMode = isConcurrentMode;
exports.isContextConsumer = isContextConsumer;
exports.isContextProvider = isContextProvider;
exports.isElement = isElement;
exports.isForwardRef = isForwardRef;
exports.isFragment = isFragment;
exports.isLazy = isLazy;
exports.isMemo = isMemo;
exports.isPortal = isPortal;
exports.isProfiler = isProfiler;
exports.isStrictMode = isStrictMode;
exports.isSuspense = isSuspense;
exports.isValidElementType = isValidElementType;
exports.typeOf = typeOf;
  })();
}


/***/ }),

/***/ "./node_modules/react-is/index.js":
/*!****************************************!*\
  !*** ./node_modules/react-is/index.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


if (false) {} else {
  module.exports = __webpack_require__(/*! ./cjs/react-is.development.js */ "./node_modules/react-is/cjs/react-is.development.js");
}


/***/ }),

/***/ "./node_modules/react-redux/es/components/Provider.js":
/*!************************************************************!*\
  !*** ./node_modules/react-redux/es/components/Provider.js ***!
  \************************************************************/
/*! exports provided: createProvider, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createProvider", function() { return createProvider; });
/* harmony import */ var _babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inheritsLoose */ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! prop-types */ "./node_modules/prop-types/index.js");
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(prop_types__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_PropTypes__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/PropTypes */ "./node_modules/react-redux/es/utils/PropTypes.js");
/* harmony import */ var _utils_warning__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../utils/warning */ "./node_modules/react-redux/es/utils/warning.js");





var prefixUnsafeLifecycleMethods = typeof react__WEBPACK_IMPORTED_MODULE_1___default.a.forwardRef !== "undefined";
var didWarnAboutReceivingStore = false;

function warnAboutReceivingStore() {
  if (didWarnAboutReceivingStore) {
    return;
  }

  didWarnAboutReceivingStore = true;
  Object(_utils_warning__WEBPACK_IMPORTED_MODULE_4__["default"])('<Provider> does not support changing `store` on the fly. ' + 'It is most likely that you see this error because you updated to ' + 'Redux 2.x and React Redux 2.x which no longer hot reload reducers ' + 'automatically. See https://github.com/reduxjs/react-redux/releases/' + 'tag/v2.0.0 for the migration instructions.');
}

function createProvider(storeKey) {
  var _Provider$childContex;

  if (storeKey === void 0) {
    storeKey = 'store';
  }

  var subscriptionKey = storeKey + "Subscription";

  var Provider =
  /*#__PURE__*/
  function (_Component) {
    Object(_babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(Provider, _Component);

    var _proto = Provider.prototype;

    _proto.getChildContext = function getChildContext() {
      var _ref;

      return _ref = {}, _ref[storeKey] = this[storeKey], _ref[subscriptionKey] = null, _ref;
    };

    function Provider(props, context) {
      var _this;

      _this = _Component.call(this, props, context) || this;
      _this[storeKey] = props.store;
      return _this;
    }

    _proto.render = function render() {
      return react__WEBPACK_IMPORTED_MODULE_1__["Children"].only(this.props.children);
    };

    return Provider;
  }(react__WEBPACK_IMPORTED_MODULE_1__["Component"]);

  if (true) {
    // Use UNSAFE_ event name where supported
    var eventName = prefixUnsafeLifecycleMethods ? 'UNSAFE_componentWillReceiveProps' : 'componentWillReceiveProps';

    Provider.prototype[eventName] = function (nextProps) {
      if (this[storeKey] !== nextProps.store) {
        warnAboutReceivingStore();
      }
    };
  }

  Provider.propTypes = {
    store: _utils_PropTypes__WEBPACK_IMPORTED_MODULE_3__["storeShape"].isRequired,
    children: prop_types__WEBPACK_IMPORTED_MODULE_2___default.a.element.isRequired
  };
  Provider.childContextTypes = (_Provider$childContex = {}, _Provider$childContex[storeKey] = _utils_PropTypes__WEBPACK_IMPORTED_MODULE_3__["storeShape"].isRequired, _Provider$childContex[subscriptionKey] = _utils_PropTypes__WEBPACK_IMPORTED_MODULE_3__["subscriptionShape"], _Provider$childContex);
  return Provider;
}
/* harmony default export */ __webpack_exports__["default"] = (createProvider());

/***/ }),

/***/ "./node_modules/react-redux/es/components/connectAdvanced.js":
/*!*******************************************************************!*\
  !*** ./node_modules/react-redux/es/components/connectAdvanced.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return connectAdvanced; });
/* harmony import */ var _babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inheritsLoose */ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutPropertiesLoose */ "./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js");
/* harmony import */ var hoist_non_react_statics__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! hoist-non-react-statics */ "./node_modules/hoist-non-react-statics/dist/hoist-non-react-statics.cjs.js");
/* harmony import */ var hoist_non_react_statics__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(hoist_non_react_statics__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! invariant */ "./node_modules/invariant/browser.js");
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(invariant__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var react_is__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! react-is */ "./node_modules/react-is/index.js");
/* harmony import */ var react_is__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(react_is__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _utils_Subscription__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../utils/Subscription */ "./node_modules/react-redux/es/utils/Subscription.js");
/* harmony import */ var _utils_PropTypes__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../utils/PropTypes */ "./node_modules/react-redux/es/utils/PropTypes.js");










var prefixUnsafeLifecycleMethods = typeof react__WEBPACK_IMPORTED_MODULE_6___default.a.forwardRef !== "undefined";
var hotReloadingVersion = 0;
var dummyState = {};

function noop() {}

function makeSelectorStateful(sourceSelector, store) {
  // wrap the selector in an object that tracks its results between runs.
  var selector = {
    run: function runComponentSelector(props) {
      try {
        var nextProps = sourceSelector(store.getState(), props);

        if (nextProps !== selector.props || selector.error) {
          selector.shouldComponentUpdate = true;
          selector.props = nextProps;
          selector.error = null;
        }
      } catch (error) {
        selector.shouldComponentUpdate = true;
        selector.error = error;
      }
    }
  };
  return selector;
}

function connectAdvanced(
/*
  selectorFactory is a func that is responsible for returning the selector function used to
  compute new props from state, props, and dispatch. For example:
     export default connectAdvanced((dispatch, options) => (state, props) => ({
      thing: state.things[props.thingId],
      saveThing: fields => dispatch(actionCreators.saveThing(props.thingId, fields)),
    }))(YourComponent)
   Access to dispatch is provided to the factory so selectorFactories can bind actionCreators
  outside of their selector as an optimization. Options passed to connectAdvanced are passed to
  the selectorFactory, along with displayName and WrappedComponent, as the second argument.
   Note that selectorFactory is responsible for all caching/memoization of inbound and outbound
  props. Do not use connectAdvanced directly without memoizing results between calls to your
  selector, otherwise the Connect component will re-render on every state or props change.
*/
selectorFactory, // options object:
_ref) {
  var _contextTypes, _childContextTypes;

  if (_ref === void 0) {
    _ref = {};
  }

  var _ref2 = _ref,
      _ref2$getDisplayName = _ref2.getDisplayName,
      getDisplayName = _ref2$getDisplayName === void 0 ? function (name) {
    return "ConnectAdvanced(" + name + ")";
  } : _ref2$getDisplayName,
      _ref2$methodName = _ref2.methodName,
      methodName = _ref2$methodName === void 0 ? 'connectAdvanced' : _ref2$methodName,
      _ref2$renderCountProp = _ref2.renderCountProp,
      renderCountProp = _ref2$renderCountProp === void 0 ? undefined : _ref2$renderCountProp,
      _ref2$shouldHandleSta = _ref2.shouldHandleStateChanges,
      shouldHandleStateChanges = _ref2$shouldHandleSta === void 0 ? true : _ref2$shouldHandleSta,
      _ref2$storeKey = _ref2.storeKey,
      storeKey = _ref2$storeKey === void 0 ? 'store' : _ref2$storeKey,
      _ref2$withRef = _ref2.withRef,
      withRef = _ref2$withRef === void 0 ? false : _ref2$withRef,
      connectOptions = Object(_babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_3__["default"])(_ref2, ["getDisplayName", "methodName", "renderCountProp", "shouldHandleStateChanges", "storeKey", "withRef"]);

  var subscriptionKey = storeKey + 'Subscription';
  var version = hotReloadingVersion++;
  var contextTypes = (_contextTypes = {}, _contextTypes[storeKey] = _utils_PropTypes__WEBPACK_IMPORTED_MODULE_9__["storeShape"], _contextTypes[subscriptionKey] = _utils_PropTypes__WEBPACK_IMPORTED_MODULE_9__["subscriptionShape"], _contextTypes);
  var childContextTypes = (_childContextTypes = {}, _childContextTypes[subscriptionKey] = _utils_PropTypes__WEBPACK_IMPORTED_MODULE_9__["subscriptionShape"], _childContextTypes);
  return function wrapWithConnect(WrappedComponent) {
    invariant__WEBPACK_IMPORTED_MODULE_5___default()(Object(react_is__WEBPACK_IMPORTED_MODULE_7__["isValidElementType"])(WrappedComponent), "You must pass a component to the function returned by " + (methodName + ". Instead received " + JSON.stringify(WrappedComponent)));
    var wrappedComponentName = WrappedComponent.displayName || WrappedComponent.name || 'Component';
    var displayName = getDisplayName(wrappedComponentName);

    var selectorFactoryOptions = Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_2__["default"])({}, connectOptions, {
      getDisplayName: getDisplayName,
      methodName: methodName,
      renderCountProp: renderCountProp,
      shouldHandleStateChanges: shouldHandleStateChanges,
      storeKey: storeKey,
      withRef: withRef,
      displayName: displayName,
      wrappedComponentName: wrappedComponentName,
      WrappedComponent: WrappedComponent // TODO Actually fix our use of componentWillReceiveProps

      /* eslint-disable react/no-deprecated */

    });

    var Connect =
    /*#__PURE__*/
    function (_Component) {
      Object(_babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(Connect, _Component);

      function Connect(props, context) {
        var _this;

        _this = _Component.call(this, props, context) || this;
        _this.version = version;
        _this.state = {};
        _this.renderCount = 0;
        _this.store = props[storeKey] || context[storeKey];
        _this.propsMode = Boolean(props[storeKey]);
        _this.setWrappedInstance = _this.setWrappedInstance.bind(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__["default"])(Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__["default"])(_this)));
        invariant__WEBPACK_IMPORTED_MODULE_5___default()(_this.store, "Could not find \"" + storeKey + "\" in either the context or props of " + ("\"" + displayName + "\". Either wrap the root component in a <Provider>, ") + ("or explicitly pass \"" + storeKey + "\" as a prop to \"" + displayName + "\"."));

        _this.initSelector();

        _this.initSubscription();

        return _this;
      }

      var _proto = Connect.prototype;

      _proto.getChildContext = function getChildContext() {
        var _ref3;

        // If this component received store from props, its subscription should be transparent
        // to any descendants receiving store+subscription from context; it passes along
        // subscription passed to it. Otherwise, it shadows the parent subscription, which allows
        // Connect to control ordering of notifications to flow top-down.
        var subscription = this.propsMode ? null : this.subscription;
        return _ref3 = {}, _ref3[subscriptionKey] = subscription || this.context[subscriptionKey], _ref3;
      };

      _proto.componentDidMount = function componentDidMount() {
        if (!shouldHandleStateChanges) return; // componentWillMount fires during server side rendering, but componentDidMount and
        // componentWillUnmount do not. Because of this, trySubscribe happens during ...didMount.
        // Otherwise, unsubscription would never take place during SSR, causing a memory leak.
        // To handle the case where a child component may have triggered a state change by
        // dispatching an action in its componentWillMount, we have to re-run the select and maybe
        // re-render.

        this.subscription.trySubscribe();
        this.selector.run(this.props);
        if (this.selector.shouldComponentUpdate) this.forceUpdate();
      }; // Note: this is renamed below to the UNSAFE_ version in React >=16.3.0


      _proto.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
        this.selector.run(nextProps);
      };

      _proto.shouldComponentUpdate = function shouldComponentUpdate() {
        return this.selector.shouldComponentUpdate;
      };

      _proto.componentWillUnmount = function componentWillUnmount() {
        if (this.subscription) this.subscription.tryUnsubscribe();
        this.subscription = null;
        this.notifyNestedSubs = noop;
        this.store = null;
        this.selector.run = noop;
        this.selector.shouldComponentUpdate = false;
      };

      _proto.getWrappedInstance = function getWrappedInstance() {
        invariant__WEBPACK_IMPORTED_MODULE_5___default()(withRef, "To access the wrapped instance, you need to specify " + ("{ withRef: true } in the options argument of the " + methodName + "() call."));
        return this.wrappedInstance;
      };

      _proto.setWrappedInstance = function setWrappedInstance(ref) {
        this.wrappedInstance = ref;
      };

      _proto.initSelector = function initSelector() {
        var sourceSelector = selectorFactory(this.store.dispatch, selectorFactoryOptions);
        this.selector = makeSelectorStateful(sourceSelector, this.store);
        this.selector.run(this.props);
      };

      _proto.initSubscription = function initSubscription() {
        if (!shouldHandleStateChanges) return; // parentSub's source should match where store came from: props vs. context. A component
        // connected to the store via props shouldn't use subscription from context, or vice versa.

        var parentSub = (this.propsMode ? this.props : this.context)[subscriptionKey];
        this.subscription = new _utils_Subscription__WEBPACK_IMPORTED_MODULE_8__["default"](this.store, parentSub, this.onStateChange.bind(this)); // `notifyNestedSubs` is duplicated to handle the case where the component is unmounted in
        // the middle of the notification loop, where `this.subscription` will then be null. An
        // extra null check every change can be avoided by copying the method onto `this` and then
        // replacing it with a no-op on unmount. This can probably be avoided if Subscription's
        // listeners logic is changed to not call listeners that have been unsubscribed in the
        // middle of the notification loop.

        this.notifyNestedSubs = this.subscription.notifyNestedSubs.bind(this.subscription);
      };

      _proto.onStateChange = function onStateChange() {
        this.selector.run(this.props);

        if (!this.selector.shouldComponentUpdate) {
          this.notifyNestedSubs();
        } else {
          this.componentDidUpdate = this.notifyNestedSubsOnComponentDidUpdate;
          this.setState(dummyState);
        }
      };

      _proto.notifyNestedSubsOnComponentDidUpdate = function notifyNestedSubsOnComponentDidUpdate() {
        // `componentDidUpdate` is conditionally implemented when `onStateChange` determines it
        // needs to notify nested subs. Once called, it unimplements itself until further state
        // changes occur. Doing it this way vs having a permanent `componentDidUpdate` that does
        // a boolean check every time avoids an extra method call most of the time, resulting
        // in some perf boost.
        this.componentDidUpdate = undefined;
        this.notifyNestedSubs();
      };

      _proto.isSubscribed = function isSubscribed() {
        return Boolean(this.subscription) && this.subscription.isSubscribed();
      };

      _proto.addExtraProps = function addExtraProps(props) {
        if (!withRef && !renderCountProp && !(this.propsMode && this.subscription)) return props; // make a shallow copy so that fields added don't leak to the original selector.
        // this is especially important for 'ref' since that's a reference back to the component
        // instance. a singleton memoized selector would then be holding a reference to the
        // instance, preventing the instance from being garbage collected, and that would be bad

        var withExtras = Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_2__["default"])({}, props);

        if (withRef) withExtras.ref = this.setWrappedInstance;
        if (renderCountProp) withExtras[renderCountProp] = this.renderCount++;
        if (this.propsMode && this.subscription) withExtras[subscriptionKey] = this.subscription;
        return withExtras;
      };

      _proto.render = function render() {
        var selector = this.selector;
        selector.shouldComponentUpdate = false;

        if (selector.error) {
          throw selector.error;
        } else {
          return Object(react__WEBPACK_IMPORTED_MODULE_6__["createElement"])(WrappedComponent, this.addExtraProps(selector.props));
        }
      };

      return Connect;
    }(react__WEBPACK_IMPORTED_MODULE_6__["Component"]);

    if (prefixUnsafeLifecycleMethods) {
      // Use UNSAFE_ event name where supported
      Connect.prototype.UNSAFE_componentWillReceiveProps = Connect.prototype.componentWillReceiveProps;
      delete Connect.prototype.componentWillReceiveProps;
    }
    /* eslint-enable react/no-deprecated */


    Connect.WrappedComponent = WrappedComponent;
    Connect.displayName = displayName;
    Connect.childContextTypes = childContextTypes;
    Connect.contextTypes = contextTypes;
    Connect.propTypes = contextTypes;

    if (true) {
      // Use UNSAFE_ event name where supported
      var eventName = prefixUnsafeLifecycleMethods ? 'UNSAFE_componentWillUpdate' : 'componentWillUpdate';

      Connect.prototype[eventName] = function componentWillUpdate() {
        var _this2 = this;

        // We are hot reloading!
        if (this.version !== version) {
          this.version = version;
          this.initSelector(); // If any connected descendants don't hot reload (and resubscribe in the process), their
          // listeners will be lost when we unsubscribe. Unfortunately, by copying over all
          // listeners, this does mean that the old versions of connected descendants will still be
          // notified of state changes; however, their onStateChange function is a no-op so this
          // isn't a huge deal.

          var oldListeners = [];

          if (this.subscription) {
            oldListeners = this.subscription.listeners.get();
            this.subscription.tryUnsubscribe();
          }

          this.initSubscription();

          if (shouldHandleStateChanges) {
            this.subscription.trySubscribe();
            oldListeners.forEach(function (listener) {
              return _this2.subscription.listeners.subscribe(listener);
            });
          }
        }
      };
    }

    return hoist_non_react_statics__WEBPACK_IMPORTED_MODULE_4___default()(Connect, WrappedComponent);
  };
}

/***/ }),

/***/ "./node_modules/react-redux/es/connect/connect.js":
/*!********************************************************!*\
  !*** ./node_modules/react-redux/es/connect/connect.js ***!
  \********************************************************/
/*! exports provided: createConnect, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createConnect", function() { return createConnect; });
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutPropertiesLoose */ "./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js");
/* harmony import */ var _components_connectAdvanced__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/connectAdvanced */ "./node_modules/react-redux/es/components/connectAdvanced.js");
/* harmony import */ var _utils_shallowEqual__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/shallowEqual */ "./node_modules/react-redux/es/utils/shallowEqual.js");
/* harmony import */ var _mapDispatchToProps__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./mapDispatchToProps */ "./node_modules/react-redux/es/connect/mapDispatchToProps.js");
/* harmony import */ var _mapStateToProps__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./mapStateToProps */ "./node_modules/react-redux/es/connect/mapStateToProps.js");
/* harmony import */ var _mergeProps__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./mergeProps */ "./node_modules/react-redux/es/connect/mergeProps.js");
/* harmony import */ var _selectorFactory__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./selectorFactory */ "./node_modules/react-redux/es/connect/selectorFactory.js");








/*
  connect is a facade over connectAdvanced. It turns its args into a compatible
  selectorFactory, which has the signature:

    (dispatch, options) => (nextState, nextOwnProps) => nextFinalProps
  
  connect passes its args to connectAdvanced as options, which will in turn pass them to
  selectorFactory each time a Connect component instance is instantiated or hot reloaded.

  selectorFactory returns a final props selector from its mapStateToProps,
  mapStateToPropsFactories, mapDispatchToProps, mapDispatchToPropsFactories, mergeProps,
  mergePropsFactories, and pure args.

  The resulting final props selector is called by the Connect component instance whenever
  it receives new props or store state.
 */

function match(arg, factories, name) {
  for (var i = factories.length - 1; i >= 0; i--) {
    var result = factories[i](arg);
    if (result) return result;
  }

  return function (dispatch, options) {
    throw new Error("Invalid value of type " + typeof arg + " for " + name + " argument when connecting component " + options.wrappedComponentName + ".");
  };
}

function strictEqual(a, b) {
  return a === b;
} // createConnect with default args builds the 'official' connect behavior. Calling it with
// different options opens up some testing and extensibility scenarios


function createConnect(_temp) {
  var _ref = _temp === void 0 ? {} : _temp,
      _ref$connectHOC = _ref.connectHOC,
      connectHOC = _ref$connectHOC === void 0 ? _components_connectAdvanced__WEBPACK_IMPORTED_MODULE_2__["default"] : _ref$connectHOC,
      _ref$mapStateToPropsF = _ref.mapStateToPropsFactories,
      mapStateToPropsFactories = _ref$mapStateToPropsF === void 0 ? _mapStateToProps__WEBPACK_IMPORTED_MODULE_5__["default"] : _ref$mapStateToPropsF,
      _ref$mapDispatchToPro = _ref.mapDispatchToPropsFactories,
      mapDispatchToPropsFactories = _ref$mapDispatchToPro === void 0 ? _mapDispatchToProps__WEBPACK_IMPORTED_MODULE_4__["default"] : _ref$mapDispatchToPro,
      _ref$mergePropsFactor = _ref.mergePropsFactories,
      mergePropsFactories = _ref$mergePropsFactor === void 0 ? _mergeProps__WEBPACK_IMPORTED_MODULE_6__["default"] : _ref$mergePropsFactor,
      _ref$selectorFactory = _ref.selectorFactory,
      selectorFactory = _ref$selectorFactory === void 0 ? _selectorFactory__WEBPACK_IMPORTED_MODULE_7__["default"] : _ref$selectorFactory;

  return function connect(mapStateToProps, mapDispatchToProps, mergeProps, _ref2) {
    if (_ref2 === void 0) {
      _ref2 = {};
    }

    var _ref3 = _ref2,
        _ref3$pure = _ref3.pure,
        pure = _ref3$pure === void 0 ? true : _ref3$pure,
        _ref3$areStatesEqual = _ref3.areStatesEqual,
        areStatesEqual = _ref3$areStatesEqual === void 0 ? strictEqual : _ref3$areStatesEqual,
        _ref3$areOwnPropsEqua = _ref3.areOwnPropsEqual,
        areOwnPropsEqual = _ref3$areOwnPropsEqua === void 0 ? _utils_shallowEqual__WEBPACK_IMPORTED_MODULE_3__["default"] : _ref3$areOwnPropsEqua,
        _ref3$areStatePropsEq = _ref3.areStatePropsEqual,
        areStatePropsEqual = _ref3$areStatePropsEq === void 0 ? _utils_shallowEqual__WEBPACK_IMPORTED_MODULE_3__["default"] : _ref3$areStatePropsEq,
        _ref3$areMergedPropsE = _ref3.areMergedPropsEqual,
        areMergedPropsEqual = _ref3$areMergedPropsE === void 0 ? _utils_shallowEqual__WEBPACK_IMPORTED_MODULE_3__["default"] : _ref3$areMergedPropsE,
        extraOptions = Object(_babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_1__["default"])(_ref3, ["pure", "areStatesEqual", "areOwnPropsEqual", "areStatePropsEqual", "areMergedPropsEqual"]);

    var initMapStateToProps = match(mapStateToProps, mapStateToPropsFactories, 'mapStateToProps');
    var initMapDispatchToProps = match(mapDispatchToProps, mapDispatchToPropsFactories, 'mapDispatchToProps');
    var initMergeProps = match(mergeProps, mergePropsFactories, 'mergeProps');
    return connectHOC(selectorFactory, Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({
      // used in error messages
      methodName: 'connect',
      // used to compute Connect's displayName from the wrapped component's displayName.
      getDisplayName: function getDisplayName(name) {
        return "Connect(" + name + ")";
      },
      // if mapStateToProps is falsy, the Connect component doesn't subscribe to store state changes
      shouldHandleStateChanges: Boolean(mapStateToProps),
      // passed through to selectorFactory
      initMapStateToProps: initMapStateToProps,
      initMapDispatchToProps: initMapDispatchToProps,
      initMergeProps: initMergeProps,
      pure: pure,
      areStatesEqual: areStatesEqual,
      areOwnPropsEqual: areOwnPropsEqual,
      areStatePropsEqual: areStatePropsEqual,
      areMergedPropsEqual: areMergedPropsEqual
    }, extraOptions));
  };
}
/* harmony default export */ __webpack_exports__["default"] = (createConnect());

/***/ }),

/***/ "./node_modules/react-redux/es/connect/mapDispatchToProps.js":
/*!*******************************************************************!*\
  !*** ./node_modules/react-redux/es/connect/mapDispatchToProps.js ***!
  \*******************************************************************/
/*! exports provided: whenMapDispatchToPropsIsFunction, whenMapDispatchToPropsIsMissing, whenMapDispatchToPropsIsObject, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "whenMapDispatchToPropsIsFunction", function() { return whenMapDispatchToPropsIsFunction; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "whenMapDispatchToPropsIsMissing", function() { return whenMapDispatchToPropsIsMissing; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "whenMapDispatchToPropsIsObject", function() { return whenMapDispatchToPropsIsObject; });
/* harmony import */ var redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux */ "./node_modules/redux/es/redux.js");
/* harmony import */ var _wrapMapToProps__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./wrapMapToProps */ "./node_modules/react-redux/es/connect/wrapMapToProps.js");


function whenMapDispatchToPropsIsFunction(mapDispatchToProps) {
  return typeof mapDispatchToProps === 'function' ? Object(_wrapMapToProps__WEBPACK_IMPORTED_MODULE_1__["wrapMapToPropsFunc"])(mapDispatchToProps, 'mapDispatchToProps') : undefined;
}
function whenMapDispatchToPropsIsMissing(mapDispatchToProps) {
  return !mapDispatchToProps ? Object(_wrapMapToProps__WEBPACK_IMPORTED_MODULE_1__["wrapMapToPropsConstant"])(function (dispatch) {
    return {
      dispatch: dispatch
    };
  }) : undefined;
}
function whenMapDispatchToPropsIsObject(mapDispatchToProps) {
  return mapDispatchToProps && typeof mapDispatchToProps === 'object' ? Object(_wrapMapToProps__WEBPACK_IMPORTED_MODULE_1__["wrapMapToPropsConstant"])(function (dispatch) {
    return Object(redux__WEBPACK_IMPORTED_MODULE_0__["bindActionCreators"])(mapDispatchToProps, dispatch);
  }) : undefined;
}
/* harmony default export */ __webpack_exports__["default"] = ([whenMapDispatchToPropsIsFunction, whenMapDispatchToPropsIsMissing, whenMapDispatchToPropsIsObject]);

/***/ }),

/***/ "./node_modules/react-redux/es/connect/mapStateToProps.js":
/*!****************************************************************!*\
  !*** ./node_modules/react-redux/es/connect/mapStateToProps.js ***!
  \****************************************************************/
/*! exports provided: whenMapStateToPropsIsFunction, whenMapStateToPropsIsMissing, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "whenMapStateToPropsIsFunction", function() { return whenMapStateToPropsIsFunction; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "whenMapStateToPropsIsMissing", function() { return whenMapStateToPropsIsMissing; });
/* harmony import */ var _wrapMapToProps__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./wrapMapToProps */ "./node_modules/react-redux/es/connect/wrapMapToProps.js");

function whenMapStateToPropsIsFunction(mapStateToProps) {
  return typeof mapStateToProps === 'function' ? Object(_wrapMapToProps__WEBPACK_IMPORTED_MODULE_0__["wrapMapToPropsFunc"])(mapStateToProps, 'mapStateToProps') : undefined;
}
function whenMapStateToPropsIsMissing(mapStateToProps) {
  return !mapStateToProps ? Object(_wrapMapToProps__WEBPACK_IMPORTED_MODULE_0__["wrapMapToPropsConstant"])(function () {
    return {};
  }) : undefined;
}
/* harmony default export */ __webpack_exports__["default"] = ([whenMapStateToPropsIsFunction, whenMapStateToPropsIsMissing]);

/***/ }),

/***/ "./node_modules/react-redux/es/connect/mergeProps.js":
/*!***********************************************************!*\
  !*** ./node_modules/react-redux/es/connect/mergeProps.js ***!
  \***********************************************************/
/*! exports provided: defaultMergeProps, wrapMergePropsFunc, whenMergePropsIsFunction, whenMergePropsIsOmitted, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "defaultMergeProps", function() { return defaultMergeProps; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "wrapMergePropsFunc", function() { return wrapMergePropsFunc; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "whenMergePropsIsFunction", function() { return whenMergePropsIsFunction; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "whenMergePropsIsOmitted", function() { return whenMergePropsIsOmitted; });
/* harmony import */ var _babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _utils_verifyPlainObject__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/verifyPlainObject */ "./node_modules/react-redux/es/utils/verifyPlainObject.js");


function defaultMergeProps(stateProps, dispatchProps, ownProps) {
  return Object(_babel_runtime_helpers_esm_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({}, ownProps, stateProps, dispatchProps);
}
function wrapMergePropsFunc(mergeProps) {
  return function initMergePropsProxy(dispatch, _ref) {
    var displayName = _ref.displayName,
        pure = _ref.pure,
        areMergedPropsEqual = _ref.areMergedPropsEqual;
    var hasRunOnce = false;
    var mergedProps;
    return function mergePropsProxy(stateProps, dispatchProps, ownProps) {
      var nextMergedProps = mergeProps(stateProps, dispatchProps, ownProps);

      if (hasRunOnce) {
        if (!pure || !areMergedPropsEqual(nextMergedProps, mergedProps)) mergedProps = nextMergedProps;
      } else {
        hasRunOnce = true;
        mergedProps = nextMergedProps;
        if (true) Object(_utils_verifyPlainObject__WEBPACK_IMPORTED_MODULE_1__["default"])(mergedProps, displayName, 'mergeProps');
      }

      return mergedProps;
    };
  };
}
function whenMergePropsIsFunction(mergeProps) {
  return typeof mergeProps === 'function' ? wrapMergePropsFunc(mergeProps) : undefined;
}
function whenMergePropsIsOmitted(mergeProps) {
  return !mergeProps ? function () {
    return defaultMergeProps;
  } : undefined;
}
/* harmony default export */ __webpack_exports__["default"] = ([whenMergePropsIsFunction, whenMergePropsIsOmitted]);

/***/ }),

/***/ "./node_modules/react-redux/es/connect/selectorFactory.js":
/*!****************************************************************!*\
  !*** ./node_modules/react-redux/es/connect/selectorFactory.js ***!
  \****************************************************************/
/*! exports provided: impureFinalPropsSelectorFactory, pureFinalPropsSelectorFactory, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "impureFinalPropsSelectorFactory", function() { return impureFinalPropsSelectorFactory; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "pureFinalPropsSelectorFactory", function() { return pureFinalPropsSelectorFactory; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return finalPropsSelectorFactory; });
/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutPropertiesLoose */ "./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js");
/* harmony import */ var _verifySubselectors__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./verifySubselectors */ "./node_modules/react-redux/es/connect/verifySubselectors.js");


function impureFinalPropsSelectorFactory(mapStateToProps, mapDispatchToProps, mergeProps, dispatch) {
  return function impureFinalPropsSelector(state, ownProps) {
    return mergeProps(mapStateToProps(state, ownProps), mapDispatchToProps(dispatch, ownProps), ownProps);
  };
}
function pureFinalPropsSelectorFactory(mapStateToProps, mapDispatchToProps, mergeProps, dispatch, _ref) {
  var areStatesEqual = _ref.areStatesEqual,
      areOwnPropsEqual = _ref.areOwnPropsEqual,
      areStatePropsEqual = _ref.areStatePropsEqual;
  var hasRunAtLeastOnce = false;
  var state;
  var ownProps;
  var stateProps;
  var dispatchProps;
  var mergedProps;

  function handleFirstCall(firstState, firstOwnProps) {
    state = firstState;
    ownProps = firstOwnProps;
    stateProps = mapStateToProps(state, ownProps);
    dispatchProps = mapDispatchToProps(dispatch, ownProps);
    mergedProps = mergeProps(stateProps, dispatchProps, ownProps);
    hasRunAtLeastOnce = true;
    return mergedProps;
  }

  function handleNewPropsAndNewState() {
    stateProps = mapStateToProps(state, ownProps);
    if (mapDispatchToProps.dependsOnOwnProps) dispatchProps = mapDispatchToProps(dispatch, ownProps);
    mergedProps = mergeProps(stateProps, dispatchProps, ownProps);
    return mergedProps;
  }

  function handleNewProps() {
    if (mapStateToProps.dependsOnOwnProps) stateProps = mapStateToProps(state, ownProps);
    if (mapDispatchToProps.dependsOnOwnProps) dispatchProps = mapDispatchToProps(dispatch, ownProps);
    mergedProps = mergeProps(stateProps, dispatchProps, ownProps);
    return mergedProps;
  }

  function handleNewState() {
    var nextStateProps = mapStateToProps(state, ownProps);
    var statePropsChanged = !areStatePropsEqual(nextStateProps, stateProps);
    stateProps = nextStateProps;
    if (statePropsChanged) mergedProps = mergeProps(stateProps, dispatchProps, ownProps);
    return mergedProps;
  }

  function handleSubsequentCalls(nextState, nextOwnProps) {
    var propsChanged = !areOwnPropsEqual(nextOwnProps, ownProps);
    var stateChanged = !areStatesEqual(nextState, state);
    state = nextState;
    ownProps = nextOwnProps;
    if (propsChanged && stateChanged) return handleNewPropsAndNewState();
    if (propsChanged) return handleNewProps();
    if (stateChanged) return handleNewState();
    return mergedProps;
  }

  return function pureFinalPropsSelector(nextState, nextOwnProps) {
    return hasRunAtLeastOnce ? handleSubsequentCalls(nextState, nextOwnProps) : handleFirstCall(nextState, nextOwnProps);
  };
} // TODO: Add more comments
// If pure is true, the selector returned by selectorFactory will memoize its results,
// allowing connectAdvanced's shouldComponentUpdate to return false if final
// props have not changed. If false, the selector will always return a new
// object and shouldComponentUpdate will always return true.

function finalPropsSelectorFactory(dispatch, _ref2) {
  var initMapStateToProps = _ref2.initMapStateToProps,
      initMapDispatchToProps = _ref2.initMapDispatchToProps,
      initMergeProps = _ref2.initMergeProps,
      options = Object(_babel_runtime_helpers_esm_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(_ref2, ["initMapStateToProps", "initMapDispatchToProps", "initMergeProps"]);

  var mapStateToProps = initMapStateToProps(dispatch, options);
  var mapDispatchToProps = initMapDispatchToProps(dispatch, options);
  var mergeProps = initMergeProps(dispatch, options);

  if (true) {
    Object(_verifySubselectors__WEBPACK_IMPORTED_MODULE_1__["default"])(mapStateToProps, mapDispatchToProps, mergeProps, options.displayName);
  }

  var selectorFactory = options.pure ? pureFinalPropsSelectorFactory : impureFinalPropsSelectorFactory;
  return selectorFactory(mapStateToProps, mapDispatchToProps, mergeProps, dispatch, options);
}

/***/ }),

/***/ "./node_modules/react-redux/es/connect/verifySubselectors.js":
/*!*******************************************************************!*\
  !*** ./node_modules/react-redux/es/connect/verifySubselectors.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return verifySubselectors; });
/* harmony import */ var _utils_warning__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/warning */ "./node_modules/react-redux/es/utils/warning.js");


function verify(selector, methodName, displayName) {
  if (!selector) {
    throw new Error("Unexpected value for " + methodName + " in " + displayName + ".");
  } else if (methodName === 'mapStateToProps' || methodName === 'mapDispatchToProps') {
    if (!selector.hasOwnProperty('dependsOnOwnProps')) {
      Object(_utils_warning__WEBPACK_IMPORTED_MODULE_0__["default"])("The selector for " + methodName + " of " + displayName + " did not specify a value for dependsOnOwnProps.");
    }
  }
}

function verifySubselectors(mapStateToProps, mapDispatchToProps, mergeProps, displayName) {
  verify(mapStateToProps, 'mapStateToProps', displayName);
  verify(mapDispatchToProps, 'mapDispatchToProps', displayName);
  verify(mergeProps, 'mergeProps', displayName);
}

/***/ }),

/***/ "./node_modules/react-redux/es/connect/wrapMapToProps.js":
/*!***************************************************************!*\
  !*** ./node_modules/react-redux/es/connect/wrapMapToProps.js ***!
  \***************************************************************/
/*! exports provided: wrapMapToPropsConstant, getDependsOnOwnProps, wrapMapToPropsFunc */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "wrapMapToPropsConstant", function() { return wrapMapToPropsConstant; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getDependsOnOwnProps", function() { return getDependsOnOwnProps; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "wrapMapToPropsFunc", function() { return wrapMapToPropsFunc; });
/* harmony import */ var _utils_verifyPlainObject__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/verifyPlainObject */ "./node_modules/react-redux/es/utils/verifyPlainObject.js");

function wrapMapToPropsConstant(getConstant) {
  return function initConstantSelector(dispatch, options) {
    var constant = getConstant(dispatch, options);

    function constantSelector() {
      return constant;
    }

    constantSelector.dependsOnOwnProps = false;
    return constantSelector;
  };
} // dependsOnOwnProps is used by createMapToPropsProxy to determine whether to pass props as args
// to the mapToProps function being wrapped. It is also used by makePurePropsSelector to determine
// whether mapToProps needs to be invoked when props have changed.
// 
// A length of one signals that mapToProps does not depend on props from the parent component.
// A length of zero is assumed to mean mapToProps is getting args via arguments or ...args and
// therefore not reporting its length accurately..

function getDependsOnOwnProps(mapToProps) {
  return mapToProps.dependsOnOwnProps !== null && mapToProps.dependsOnOwnProps !== undefined ? Boolean(mapToProps.dependsOnOwnProps) : mapToProps.length !== 1;
} // Used by whenMapStateToPropsIsFunction and whenMapDispatchToPropsIsFunction,
// this function wraps mapToProps in a proxy function which does several things:
// 
//  * Detects whether the mapToProps function being called depends on props, which
//    is used by selectorFactory to decide if it should reinvoke on props changes.
//    
//  * On first call, handles mapToProps if returns another function, and treats that
//    new function as the true mapToProps for subsequent calls.
//    
//  * On first call, verifies the first result is a plain object, in order to warn
//    the developer that their mapToProps function is not returning a valid result.
//    

function wrapMapToPropsFunc(mapToProps, methodName) {
  return function initProxySelector(dispatch, _ref) {
    var displayName = _ref.displayName;

    var proxy = function mapToPropsProxy(stateOrDispatch, ownProps) {
      return proxy.dependsOnOwnProps ? proxy.mapToProps(stateOrDispatch, ownProps) : proxy.mapToProps(stateOrDispatch);
    }; // allow detectFactoryAndVerify to get ownProps


    proxy.dependsOnOwnProps = true;

    proxy.mapToProps = function detectFactoryAndVerify(stateOrDispatch, ownProps) {
      proxy.mapToProps = mapToProps;
      proxy.dependsOnOwnProps = getDependsOnOwnProps(mapToProps);
      var props = proxy(stateOrDispatch, ownProps);

      if (typeof props === 'function') {
        proxy.mapToProps = props;
        proxy.dependsOnOwnProps = getDependsOnOwnProps(props);
        props = proxy(stateOrDispatch, ownProps);
      }

      if (true) Object(_utils_verifyPlainObject__WEBPACK_IMPORTED_MODULE_0__["default"])(props, displayName, methodName);
      return props;
    };

    return proxy;
  };
}

/***/ }),

/***/ "./node_modules/react-redux/es/index.js":
/*!**********************************************!*\
  !*** ./node_modules/react-redux/es/index.js ***!
  \**********************************************/
/*! exports provided: Provider, createProvider, connectAdvanced, connect */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_Provider__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/Provider */ "./node_modules/react-redux/es/components/Provider.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "Provider", function() { return _components_Provider__WEBPACK_IMPORTED_MODULE_0__["default"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "createProvider", function() { return _components_Provider__WEBPACK_IMPORTED_MODULE_0__["createProvider"]; });

/* harmony import */ var _components_connectAdvanced__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/connectAdvanced */ "./node_modules/react-redux/es/components/connectAdvanced.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "connectAdvanced", function() { return _components_connectAdvanced__WEBPACK_IMPORTED_MODULE_1__["default"]; });

/* harmony import */ var _connect_connect__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./connect/connect */ "./node_modules/react-redux/es/connect/connect.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "connect", function() { return _connect_connect__WEBPACK_IMPORTED_MODULE_2__["default"]; });






/***/ }),

/***/ "./node_modules/react-redux/es/utils/PropTypes.js":
/*!********************************************************!*\
  !*** ./node_modules/react-redux/es/utils/PropTypes.js ***!
  \********************************************************/
/*! exports provided: subscriptionShape, storeShape */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "subscriptionShape", function() { return subscriptionShape; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "storeShape", function() { return storeShape; });
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! prop-types */ "./node_modules/prop-types/index.js");
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(prop_types__WEBPACK_IMPORTED_MODULE_0__);

var subscriptionShape = prop_types__WEBPACK_IMPORTED_MODULE_0___default.a.shape({
  trySubscribe: prop_types__WEBPACK_IMPORTED_MODULE_0___default.a.func.isRequired,
  tryUnsubscribe: prop_types__WEBPACK_IMPORTED_MODULE_0___default.a.func.isRequired,
  notifyNestedSubs: prop_types__WEBPACK_IMPORTED_MODULE_0___default.a.func.isRequired,
  isSubscribed: prop_types__WEBPACK_IMPORTED_MODULE_0___default.a.func.isRequired
});
var storeShape = prop_types__WEBPACK_IMPORTED_MODULE_0___default.a.shape({
  subscribe: prop_types__WEBPACK_IMPORTED_MODULE_0___default.a.func.isRequired,
  dispatch: prop_types__WEBPACK_IMPORTED_MODULE_0___default.a.func.isRequired,
  getState: prop_types__WEBPACK_IMPORTED_MODULE_0___default.a.func.isRequired
});

/***/ }),

/***/ "./node_modules/react-redux/es/utils/Subscription.js":
/*!***********************************************************!*\
  !*** ./node_modules/react-redux/es/utils/Subscription.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Subscription; });
// encapsulates the subscription logic for connecting a component to the redux store, as
// well as nesting subscriptions of descendant components, so that we can ensure the
// ancestor components re-render before descendants
var CLEARED = null;
var nullListeners = {
  notify: function notify() {}
};

function createListenerCollection() {
  // the current/next pattern is copied from redux's createStore code.
  // TODO: refactor+expose that code to be reusable here?
  var current = [];
  var next = [];
  return {
    clear: function clear() {
      next = CLEARED;
      current = CLEARED;
    },
    notify: function notify() {
      var listeners = current = next;

      for (var i = 0; i < listeners.length; i++) {
        listeners[i]();
      }
    },
    get: function get() {
      return next;
    },
    subscribe: function subscribe(listener) {
      var isSubscribed = true;
      if (next === current) next = current.slice();
      next.push(listener);
      return function unsubscribe() {
        if (!isSubscribed || current === CLEARED) return;
        isSubscribed = false;
        if (next === current) next = current.slice();
        next.splice(next.indexOf(listener), 1);
      };
    }
  };
}

var Subscription =
/*#__PURE__*/
function () {
  function Subscription(store, parentSub, onStateChange) {
    this.store = store;
    this.parentSub = parentSub;
    this.onStateChange = onStateChange;
    this.unsubscribe = null;
    this.listeners = nullListeners;
  }

  var _proto = Subscription.prototype;

  _proto.addNestedSub = function addNestedSub(listener) {
    this.trySubscribe();
    return this.listeners.subscribe(listener);
  };

  _proto.notifyNestedSubs = function notifyNestedSubs() {
    this.listeners.notify();
  };

  _proto.isSubscribed = function isSubscribed() {
    return Boolean(this.unsubscribe);
  };

  _proto.trySubscribe = function trySubscribe() {
    if (!this.unsubscribe) {
      this.unsubscribe = this.parentSub ? this.parentSub.addNestedSub(this.onStateChange) : this.store.subscribe(this.onStateChange);
      this.listeners = createListenerCollection();
    }
  };

  _proto.tryUnsubscribe = function tryUnsubscribe() {
    if (this.unsubscribe) {
      this.unsubscribe();
      this.unsubscribe = null;
      this.listeners.clear();
      this.listeners = nullListeners;
    }
  };

  return Subscription;
}();



/***/ }),

/***/ "./node_modules/react-redux/es/utils/isPlainObject.js":
/*!************************************************************!*\
  !*** ./node_modules/react-redux/es/utils/isPlainObject.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return isPlainObject; });
/**
 * @param {any} obj The object to inspect.
 * @returns {boolean} True if the argument appears to be a plain object.
 */
function isPlainObject(obj) {
  if (typeof obj !== 'object' || obj === null) return false;
  var proto = Object.getPrototypeOf(obj);
  if (proto === null) return true;
  var baseProto = proto;

  while (Object.getPrototypeOf(baseProto) !== null) {
    baseProto = Object.getPrototypeOf(baseProto);
  }

  return proto === baseProto;
}

/***/ }),

/***/ "./node_modules/react-redux/es/utils/shallowEqual.js":
/*!***********************************************************!*\
  !*** ./node_modules/react-redux/es/utils/shallowEqual.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return shallowEqual; });
var hasOwn = Object.prototype.hasOwnProperty;

function is(x, y) {
  if (x === y) {
    return x !== 0 || y !== 0 || 1 / x === 1 / y;
  } else {
    return x !== x && y !== y;
  }
}

function shallowEqual(objA, objB) {
  if (is(objA, objB)) return true;

  if (typeof objA !== 'object' || objA === null || typeof objB !== 'object' || objB === null) {
    return false;
  }

  var keysA = Object.keys(objA);
  var keysB = Object.keys(objB);
  if (keysA.length !== keysB.length) return false;

  for (var i = 0; i < keysA.length; i++) {
    if (!hasOwn.call(objB, keysA[i]) || !is(objA[keysA[i]], objB[keysA[i]])) {
      return false;
    }
  }

  return true;
}

/***/ }),

/***/ "./node_modules/react-redux/es/utils/verifyPlainObject.js":
/*!****************************************************************!*\
  !*** ./node_modules/react-redux/es/utils/verifyPlainObject.js ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return verifyPlainObject; });
/* harmony import */ var _isPlainObject__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isPlainObject */ "./node_modules/react-redux/es/utils/isPlainObject.js");
/* harmony import */ var _warning__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./warning */ "./node_modules/react-redux/es/utils/warning.js");


function verifyPlainObject(value, displayName, methodName) {
  if (!Object(_isPlainObject__WEBPACK_IMPORTED_MODULE_0__["default"])(value)) {
    Object(_warning__WEBPACK_IMPORTED_MODULE_1__["default"])(methodName + "() in " + displayName + " must return a plain object. Instead received " + value + ".");
  }
}

/***/ }),

/***/ "./node_modules/react-redux/es/utils/warning.js":
/*!******************************************************!*\
  !*** ./node_modules/react-redux/es/utils/warning.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return warning; });
/**
 * Prints a warning in the console if it exists.
 *
 * @param {String} message The warning message.
 * @returns {void}
 */
function warning(message) {
  /* eslint-disable no-console */
  if (typeof console !== 'undefined' && typeof console.error === 'function') {
    console.error(message);
  }
  /* eslint-enable no-console */


  try {
    // This error was thrown as a convenience so that if you enable
    // "break on all exceptions" in your console,
    // it would pause the execution at this line.
    throw new Error(message);
    /* eslint-disable no-empty */
  } catch (e) {}
  /* eslint-enable no-empty */

}

/***/ }),

/***/ "./node_modules/reduce-reducers/es/index.js":
/*!**************************************************!*\
  !*** ./node_modules/reduce-reducers/es/index.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function () {
  for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
    args[_key] = arguments[_key];
  }

  var initialState = typeof args[args.length - 1] !== 'function' && args.pop();
  var reducers = args;

  if (typeof initialState === 'undefined') {
    throw new TypeError('The initial state may not be undefined. If you do not want to set a value for this reducer, you can use null instead of undefined.');
  }

  return function (prevState, value) {
    for (var _len2 = arguments.length, args = Array(_len2 > 2 ? _len2 - 2 : 0), _key2 = 2; _key2 < _len2; _key2++) {
      args[_key2 - 2] = arguments[_key2];
    }

    var prevStateIsUndefined = typeof prevState === 'undefined';
    var valueIsUndefined = typeof value === 'undefined';

    if (prevStateIsUndefined && valueIsUndefined && initialState) {
      return initialState;
    }

    return reducers.reduce(function (newState, reducer) {
      return reducer.apply(undefined, [newState, value].concat(args));
    }, prevStateIsUndefined && !valueIsUndefined && initialState ? initialState : prevState);
  };
});

/***/ }),

/***/ "./node_modules/redux-actions/es/combineActions.js":
/*!*********************************************************!*\
  !*** ./node_modules/redux-actions/es/combineActions.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return combineActions; });
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! invariant */ "./node_modules/invariant/browser.js");
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(invariant__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_isFunction__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/isFunction */ "./node_modules/redux-actions/es/utils/isFunction.js");
/* harmony import */ var _utils_isSymbol__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/isSymbol */ "./node_modules/redux-actions/es/utils/isSymbol.js");
/* harmony import */ var _utils_isEmpty__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/isEmpty */ "./node_modules/redux-actions/es/utils/isEmpty.js");
/* harmony import */ var _utils_toString__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/toString */ "./node_modules/redux-actions/es/utils/toString.js");
/* harmony import */ var _utils_isString__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./utils/isString */ "./node_modules/redux-actions/es/utils/isString.js");
/* harmony import */ var _constants__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./constants */ "./node_modules/redux-actions/es/constants.js");








function isValidActionType(type) {
  return Object(_utils_isString__WEBPACK_IMPORTED_MODULE_5__["default"])(type) || Object(_utils_isFunction__WEBPACK_IMPORTED_MODULE_1__["default"])(type) || Object(_utils_isSymbol__WEBPACK_IMPORTED_MODULE_2__["default"])(type);
}

function isValidActionTypes(types) {
  if (Object(_utils_isEmpty__WEBPACK_IMPORTED_MODULE_3__["default"])(types)) {
    return false;
  }

  return types.every(isValidActionType);
}

function combineActions() {
  for (var _len = arguments.length, actionsTypes = new Array(_len), _key = 0; _key < _len; _key++) {
    actionsTypes[_key] = arguments[_key];
  }

  invariant__WEBPACK_IMPORTED_MODULE_0___default()(isValidActionTypes(actionsTypes), 'Expected action types to be strings, symbols, or action creators');
  var combinedActionType = actionsTypes.map(_utils_toString__WEBPACK_IMPORTED_MODULE_4__["default"]).join(_constants__WEBPACK_IMPORTED_MODULE_6__["ACTION_TYPE_DELIMITER"]);
  return {
    toString: function toString() {
      return combinedActionType;
    }
  };
}

/***/ }),

/***/ "./node_modules/redux-actions/es/constants.js":
/*!****************************************************!*\
  !*** ./node_modules/redux-actions/es/constants.js ***!
  \****************************************************/
/*! exports provided: DEFAULT_NAMESPACE, ACTION_TYPE_DELIMITER */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "DEFAULT_NAMESPACE", function() { return DEFAULT_NAMESPACE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ACTION_TYPE_DELIMITER", function() { return ACTION_TYPE_DELIMITER; });
var DEFAULT_NAMESPACE = '/';
var ACTION_TYPE_DELIMITER = '||';

/***/ }),

/***/ "./node_modules/redux-actions/es/createAction.js":
/*!*******************************************************!*\
  !*** ./node_modules/redux-actions/es/createAction.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return createAction; });
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! invariant */ "./node_modules/invariant/browser.js");
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(invariant__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_isFunction__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/isFunction */ "./node_modules/redux-actions/es/utils/isFunction.js");
/* harmony import */ var _utils_identity__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/identity */ "./node_modules/redux-actions/es/utils/identity.js");
/* harmony import */ var _utils_isNull__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/isNull */ "./node_modules/redux-actions/es/utils/isNull.js");




function createAction(type, payloadCreator, metaCreator) {
  if (payloadCreator === void 0) {
    payloadCreator = _utils_identity__WEBPACK_IMPORTED_MODULE_2__["default"];
  }

  invariant__WEBPACK_IMPORTED_MODULE_0___default()(Object(_utils_isFunction__WEBPACK_IMPORTED_MODULE_1__["default"])(payloadCreator) || Object(_utils_isNull__WEBPACK_IMPORTED_MODULE_3__["default"])(payloadCreator), 'Expected payloadCreator to be a function, undefined or null');
  var finalPayloadCreator = Object(_utils_isNull__WEBPACK_IMPORTED_MODULE_3__["default"])(payloadCreator) || payloadCreator === _utils_identity__WEBPACK_IMPORTED_MODULE_2__["default"] ? _utils_identity__WEBPACK_IMPORTED_MODULE_2__["default"] : function (head) {
    for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      args[_key - 1] = arguments[_key];
    }

    return head instanceof Error ? head : payloadCreator.apply(void 0, [head].concat(args));
  };
  var hasMeta = Object(_utils_isFunction__WEBPACK_IMPORTED_MODULE_1__["default"])(metaCreator);
  var typeString = type.toString();

  var actionCreator = function actionCreator() {
    var payload = finalPayloadCreator.apply(void 0, arguments);
    var action = {
      type: type
    };

    if (payload instanceof Error) {
      action.error = true;
    }

    if (payload !== undefined) {
      action.payload = payload;
    }

    if (hasMeta) {
      action.meta = metaCreator.apply(void 0, arguments);
    }

    return action;
  };

  actionCreator.toString = function () {
    return typeString;
  };

  return actionCreator;
}

/***/ }),

/***/ "./node_modules/redux-actions/es/createActions.js":
/*!********************************************************!*\
  !*** ./node_modules/redux-actions/es/createActions.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return createActions; });
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! invariant */ "./node_modules/invariant/browser.js");
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(invariant__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_isPlainObject__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/isPlainObject */ "./node_modules/redux-actions/es/utils/isPlainObject.js");
/* harmony import */ var _utils_isFunction__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/isFunction */ "./node_modules/redux-actions/es/utils/isFunction.js");
/* harmony import */ var _utils_identity__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/identity */ "./node_modules/redux-actions/es/utils/identity.js");
/* harmony import */ var _utils_isArray__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/isArray */ "./node_modules/redux-actions/es/utils/isArray.js");
/* harmony import */ var _utils_isString__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./utils/isString */ "./node_modules/redux-actions/es/utils/isString.js");
/* harmony import */ var _utils_isNil__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./utils/isNil */ "./node_modules/redux-actions/es/utils/isNil.js");
/* harmony import */ var _utils_getLastElement__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./utils/getLastElement */ "./node_modules/redux-actions/es/utils/getLastElement.js");
/* harmony import */ var _utils_camelCase__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/camelCase */ "./node_modules/redux-actions/es/utils/camelCase.js");
/* harmony import */ var _utils_arrayToObject__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./utils/arrayToObject */ "./node_modules/redux-actions/es/utils/arrayToObject.js");
/* harmony import */ var _utils_flattenActionMap__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./utils/flattenActionMap */ "./node_modules/redux-actions/es/utils/flattenActionMap.js");
/* harmony import */ var _utils_unflattenActionCreators__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./utils/unflattenActionCreators */ "./node_modules/redux-actions/es/utils/unflattenActionCreators.js");
/* harmony import */ var _createAction__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./createAction */ "./node_modules/redux-actions/es/createAction.js");
/* harmony import */ var _constants__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./constants */ "./node_modules/redux-actions/es/constants.js");
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }















function createActions(actionMap) {
  for (var _len = arguments.length, identityActions = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    identityActions[_key - 1] = arguments[_key];
  }

  var options = Object(_utils_isPlainObject__WEBPACK_IMPORTED_MODULE_1__["default"])(Object(_utils_getLastElement__WEBPACK_IMPORTED_MODULE_7__["default"])(identityActions)) ? identityActions.pop() : {};
  invariant__WEBPACK_IMPORTED_MODULE_0___default()(identityActions.every(_utils_isString__WEBPACK_IMPORTED_MODULE_5__["default"]) && (Object(_utils_isString__WEBPACK_IMPORTED_MODULE_5__["default"])(actionMap) || Object(_utils_isPlainObject__WEBPACK_IMPORTED_MODULE_1__["default"])(actionMap)), 'Expected optional object followed by string action types');

  if (Object(_utils_isString__WEBPACK_IMPORTED_MODULE_5__["default"])(actionMap)) {
    return actionCreatorsFromIdentityActions([actionMap].concat(identityActions), options);
  }

  return _objectSpread({}, actionCreatorsFromActionMap(actionMap, options), actionCreatorsFromIdentityActions(identityActions, options));
}

function actionCreatorsFromActionMap(actionMap, options) {
  var flatActionMap = Object(_utils_flattenActionMap__WEBPACK_IMPORTED_MODULE_10__["default"])(actionMap, options);
  var flatActionCreators = actionMapToActionCreators(flatActionMap);
  return Object(_utils_unflattenActionCreators__WEBPACK_IMPORTED_MODULE_11__["default"])(flatActionCreators, options);
}

function actionMapToActionCreators(actionMap, _temp) {
  var _ref = _temp === void 0 ? {} : _temp,
      prefix = _ref.prefix,
      _ref$namespace = _ref.namespace,
      namespace = _ref$namespace === void 0 ? _constants__WEBPACK_IMPORTED_MODULE_13__["DEFAULT_NAMESPACE"] : _ref$namespace;

  function isValidActionMapValue(actionMapValue) {
    if (Object(_utils_isFunction__WEBPACK_IMPORTED_MODULE_2__["default"])(actionMapValue) || Object(_utils_isNil__WEBPACK_IMPORTED_MODULE_6__["default"])(actionMapValue)) {
      return true;
    }

    if (Object(_utils_isArray__WEBPACK_IMPORTED_MODULE_4__["default"])(actionMapValue)) {
      var _actionMapValue$ = actionMapValue[0],
          payload = _actionMapValue$ === void 0 ? _utils_identity__WEBPACK_IMPORTED_MODULE_3__["default"] : _actionMapValue$,
          meta = actionMapValue[1];
      return Object(_utils_isFunction__WEBPACK_IMPORTED_MODULE_2__["default"])(payload) && Object(_utils_isFunction__WEBPACK_IMPORTED_MODULE_2__["default"])(meta);
    }

    return false;
  }

  return Object(_utils_arrayToObject__WEBPACK_IMPORTED_MODULE_9__["default"])(Object.keys(actionMap), function (partialActionCreators, type) {
    var _objectSpread2;

    var actionMapValue = actionMap[type];
    invariant__WEBPACK_IMPORTED_MODULE_0___default()(isValidActionMapValue(actionMapValue), 'Expected function, undefined, null, or array with payload and meta ' + ("functions for " + type));
    var prefixedType = prefix ? "" + prefix + namespace + type : type;
    var actionCreator = Object(_utils_isArray__WEBPACK_IMPORTED_MODULE_4__["default"])(actionMapValue) ? _createAction__WEBPACK_IMPORTED_MODULE_12__["default"].apply(void 0, [prefixedType].concat(actionMapValue)) : Object(_createAction__WEBPACK_IMPORTED_MODULE_12__["default"])(prefixedType, actionMapValue);
    return _objectSpread({}, partialActionCreators, (_objectSpread2 = {}, _objectSpread2[type] = actionCreator, _objectSpread2));
  });
}

function actionCreatorsFromIdentityActions(identityActions, options) {
  var actionMap = Object(_utils_arrayToObject__WEBPACK_IMPORTED_MODULE_9__["default"])(identityActions, function (partialActionMap, type) {
    var _objectSpread3;

    return _objectSpread({}, partialActionMap, (_objectSpread3 = {}, _objectSpread3[type] = _utils_identity__WEBPACK_IMPORTED_MODULE_3__["default"], _objectSpread3));
  });
  var actionCreators = actionMapToActionCreators(actionMap, options);
  return Object(_utils_arrayToObject__WEBPACK_IMPORTED_MODULE_9__["default"])(Object.keys(actionCreators), function (partialActionCreators, type) {
    var _objectSpread4;

    return _objectSpread({}, partialActionCreators, (_objectSpread4 = {}, _objectSpread4[Object(_utils_camelCase__WEBPACK_IMPORTED_MODULE_8__["default"])(type)] = actionCreators[type], _objectSpread4));
  });
}

/***/ }),

/***/ "./node_modules/redux-actions/es/createCurriedAction.js":
/*!**************************************************************!*\
  !*** ./node_modules/redux-actions/es/createCurriedAction.js ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var just_curry_it__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! just-curry-it */ "./node_modules/just-curry-it/index.js");
/* harmony import */ var just_curry_it__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(just_curry_it__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _createAction__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./createAction */ "./node_modules/redux-actions/es/createAction.js");


/* harmony default export */ __webpack_exports__["default"] = (function (type, payloadCreator) {
  return just_curry_it__WEBPACK_IMPORTED_MODULE_0___default()(Object(_createAction__WEBPACK_IMPORTED_MODULE_1__["default"])(type, payloadCreator), payloadCreator.length);
});

/***/ }),

/***/ "./node_modules/redux-actions/es/handleAction.js":
/*!*******************************************************!*\
  !*** ./node_modules/redux-actions/es/handleAction.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return handleAction; });
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! invariant */ "./node_modules/invariant/browser.js");
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(invariant__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_isFunction__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/isFunction */ "./node_modules/redux-actions/es/utils/isFunction.js");
/* harmony import */ var _utils_isPlainObject__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/isPlainObject */ "./node_modules/redux-actions/es/utils/isPlainObject.js");
/* harmony import */ var _utils_identity__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/identity */ "./node_modules/redux-actions/es/utils/identity.js");
/* harmony import */ var _utils_isNil__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/isNil */ "./node_modules/redux-actions/es/utils/isNil.js");
/* harmony import */ var _utils_isUndefined__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./utils/isUndefined */ "./node_modules/redux-actions/es/utils/isUndefined.js");
/* harmony import */ var _utils_toString__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./utils/toString */ "./node_modules/redux-actions/es/utils/toString.js");
/* harmony import */ var _constants__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./constants */ "./node_modules/redux-actions/es/constants.js");








function handleAction(type, reducer, defaultState) {
  if (reducer === void 0) {
    reducer = _utils_identity__WEBPACK_IMPORTED_MODULE_3__["default"];
  }

  var types = Object(_utils_toString__WEBPACK_IMPORTED_MODULE_6__["default"])(type).split(_constants__WEBPACK_IMPORTED_MODULE_7__["ACTION_TYPE_DELIMITER"]);
  invariant__WEBPACK_IMPORTED_MODULE_0___default()(!Object(_utils_isUndefined__WEBPACK_IMPORTED_MODULE_5__["default"])(defaultState), "defaultState for reducer handling " + types.join(', ') + " should be defined");
  invariant__WEBPACK_IMPORTED_MODULE_0___default()(Object(_utils_isFunction__WEBPACK_IMPORTED_MODULE_1__["default"])(reducer) || Object(_utils_isPlainObject__WEBPACK_IMPORTED_MODULE_2__["default"])(reducer), 'Expected reducer to be a function or object with next and throw reducers');

  var _ref = Object(_utils_isFunction__WEBPACK_IMPORTED_MODULE_1__["default"])(reducer) ? [reducer, reducer] : [reducer.next, reducer.throw].map(function (aReducer) {
    return Object(_utils_isNil__WEBPACK_IMPORTED_MODULE_4__["default"])(aReducer) ? _utils_identity__WEBPACK_IMPORTED_MODULE_3__["default"] : aReducer;
  }),
      nextReducer = _ref[0],
      throwReducer = _ref[1];

  return function (state, action) {
    if (state === void 0) {
      state = defaultState;
    }

    var actionType = action.type;

    if (!actionType || types.indexOf(Object(_utils_toString__WEBPACK_IMPORTED_MODULE_6__["default"])(actionType)) === -1) {
      return state;
    }

    return (action.error === true ? throwReducer : nextReducer)(state, action);
  };
}

/***/ }),

/***/ "./node_modules/redux-actions/es/handleActions.js":
/*!********************************************************!*\
  !*** ./node_modules/redux-actions/es/handleActions.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return handleActions; });
/* harmony import */ var reduce_reducers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! reduce-reducers */ "./node_modules/reduce-reducers/es/index.js");
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! invariant */ "./node_modules/invariant/browser.js");
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(invariant__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_isPlainObject__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/isPlainObject */ "./node_modules/redux-actions/es/utils/isPlainObject.js");
/* harmony import */ var _utils_isMap__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/isMap */ "./node_modules/redux-actions/es/utils/isMap.js");
/* harmony import */ var _utils_ownKeys__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils/ownKeys */ "./node_modules/redux-actions/es/utils/ownKeys.js");
/* harmony import */ var _utils_flattenReducerMap__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./utils/flattenReducerMap */ "./node_modules/redux-actions/es/utils/flattenReducerMap.js");
/* harmony import */ var _handleAction__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./handleAction */ "./node_modules/redux-actions/es/handleAction.js");
/* harmony import */ var _utils_get__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./utils/get */ "./node_modules/redux-actions/es/utils/get.js");








function handleActions(handlers, defaultState, options) {
  if (options === void 0) {
    options = {};
  }

  invariant__WEBPACK_IMPORTED_MODULE_1___default()(Object(_utils_isPlainObject__WEBPACK_IMPORTED_MODULE_2__["default"])(handlers) || Object(_utils_isMap__WEBPACK_IMPORTED_MODULE_3__["default"])(handlers), 'Expected handlers to be a plain object.');
  var flattenedReducerMap = Object(_utils_flattenReducerMap__WEBPACK_IMPORTED_MODULE_5__["default"])(handlers, options);
  var reducers = Object(_utils_ownKeys__WEBPACK_IMPORTED_MODULE_4__["default"])(flattenedReducerMap).map(function (type) {
    return Object(_handleAction__WEBPACK_IMPORTED_MODULE_6__["default"])(type, Object(_utils_get__WEBPACK_IMPORTED_MODULE_7__["default"])(type, flattenedReducerMap), defaultState);
  });
  var reducer = reduce_reducers__WEBPACK_IMPORTED_MODULE_0__["default"].apply(void 0, reducers.concat([defaultState]));
  return function (state, action) {
    if (state === void 0) {
      state = defaultState;
    }

    return reducer(state, action);
  };
}

/***/ }),

/***/ "./node_modules/redux-actions/es/index.js":
/*!************************************************!*\
  !*** ./node_modules/redux-actions/es/index.js ***!
  \************************************************/
/*! exports provided: combineActions, createAction, createActions, createCurriedAction, handleAction, handleActions */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _combineActions__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./combineActions */ "./node_modules/redux-actions/es/combineActions.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "combineActions", function() { return _combineActions__WEBPACK_IMPORTED_MODULE_0__["default"]; });

/* harmony import */ var _createAction__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./createAction */ "./node_modules/redux-actions/es/createAction.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "createAction", function() { return _createAction__WEBPACK_IMPORTED_MODULE_1__["default"]; });

/* harmony import */ var _createActions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./createActions */ "./node_modules/redux-actions/es/createActions.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "createActions", function() { return _createActions__WEBPACK_IMPORTED_MODULE_2__["default"]; });

/* harmony import */ var _createCurriedAction__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./createCurriedAction */ "./node_modules/redux-actions/es/createCurriedAction.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "createCurriedAction", function() { return _createCurriedAction__WEBPACK_IMPORTED_MODULE_3__["default"]; });

/* harmony import */ var _handleAction__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./handleAction */ "./node_modules/redux-actions/es/handleAction.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "handleAction", function() { return _handleAction__WEBPACK_IMPORTED_MODULE_4__["default"]; });

/* harmony import */ var _handleActions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./handleActions */ "./node_modules/redux-actions/es/handleActions.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "handleActions", function() { return _handleActions__WEBPACK_IMPORTED_MODULE_5__["default"]; });









/***/ }),

/***/ "./node_modules/redux-actions/es/utils/arrayToObject.js":
/*!**************************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/arrayToObject.js ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (array, callback) {
  return array.reduce(function (partialObject, element) {
    return callback(partialObject, element);
  }, {});
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/camelCase.js":
/*!**********************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/camelCase.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var to_camel_case__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! to-camel-case */ "./node_modules/to-camel-case/index.js");
/* harmony import */ var to_camel_case__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(to_camel_case__WEBPACK_IMPORTED_MODULE_0__);

var namespacer = '/';
/* harmony default export */ __webpack_exports__["default"] = (function (type) {
  return type.indexOf(namespacer) === -1 ? to_camel_case__WEBPACK_IMPORTED_MODULE_0___default()(type) : type.split(namespacer).map(to_camel_case__WEBPACK_IMPORTED_MODULE_0___default.a).join(namespacer);
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/flattenActionMap.js":
/*!*****************************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/flattenActionMap.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _isPlainObject__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isPlainObject */ "./node_modules/redux-actions/es/utils/isPlainObject.js");
/* harmony import */ var _flattenWhenNode__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./flattenWhenNode */ "./node_modules/redux-actions/es/utils/flattenWhenNode.js");


/* harmony default export */ __webpack_exports__["default"] = (Object(_flattenWhenNode__WEBPACK_IMPORTED_MODULE_1__["default"])(_isPlainObject__WEBPACK_IMPORTED_MODULE_0__["default"]));

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/flattenReducerMap.js":
/*!******************************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/flattenReducerMap.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _isPlainObject__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isPlainObject */ "./node_modules/redux-actions/es/utils/isPlainObject.js");
/* harmony import */ var _isMap__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isMap */ "./node_modules/redux-actions/es/utils/isMap.js");
/* harmony import */ var _hasGeneratorInterface__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./hasGeneratorInterface */ "./node_modules/redux-actions/es/utils/hasGeneratorInterface.js");
/* harmony import */ var _flattenWhenNode__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./flattenWhenNode */ "./node_modules/redux-actions/es/utils/flattenWhenNode.js");




/* harmony default export */ __webpack_exports__["default"] = (Object(_flattenWhenNode__WEBPACK_IMPORTED_MODULE_3__["default"])(function (node) {
  return (Object(_isPlainObject__WEBPACK_IMPORTED_MODULE_0__["default"])(node) || Object(_isMap__WEBPACK_IMPORTED_MODULE_1__["default"])(node)) && !Object(_hasGeneratorInterface__WEBPACK_IMPORTED_MODULE_2__["default"])(node);
}));

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/flattenWhenNode.js":
/*!****************************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/flattenWhenNode.js ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _constants__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../constants */ "./node_modules/redux-actions/es/constants.js");
/* harmony import */ var _ownKeys__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ownKeys */ "./node_modules/redux-actions/es/utils/ownKeys.js");
/* harmony import */ var _get__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./get */ "./node_modules/redux-actions/es/utils/get.js");



/* harmony default export */ __webpack_exports__["default"] = (function (predicate) {
  return function flatten(map, _temp, partialFlatMap, partialFlatActionType) {
    var _ref = _temp === void 0 ? {} : _temp,
        _ref$namespace = _ref.namespace,
        namespace = _ref$namespace === void 0 ? _constants__WEBPACK_IMPORTED_MODULE_0__["DEFAULT_NAMESPACE"] : _ref$namespace,
        prefix = _ref.prefix;

    if (partialFlatMap === void 0) {
      partialFlatMap = {};
    }

    if (partialFlatActionType === void 0) {
      partialFlatActionType = '';
    }

    function connectNamespace(type) {
      var _ref2;

      if (!partialFlatActionType) return type;
      var types = type.toString().split(_constants__WEBPACK_IMPORTED_MODULE_0__["ACTION_TYPE_DELIMITER"]);
      var partials = partialFlatActionType.split(_constants__WEBPACK_IMPORTED_MODULE_0__["ACTION_TYPE_DELIMITER"]);
      return (_ref2 = []).concat.apply(_ref2, partials.map(function (p) {
        return types.map(function (t) {
          return "" + p + namespace + t;
        });
      })).join(_constants__WEBPACK_IMPORTED_MODULE_0__["ACTION_TYPE_DELIMITER"]);
    }

    function connectPrefix(type) {
      if (partialFlatActionType || !prefix || prefix && new RegExp("^" + prefix + namespace).test(type)) {
        return type;
      }

      return "" + prefix + namespace + type;
    }

    Object(_ownKeys__WEBPACK_IMPORTED_MODULE_1__["default"])(map).forEach(function (type) {
      var nextNamespace = connectPrefix(connectNamespace(type));
      var mapValue = Object(_get__WEBPACK_IMPORTED_MODULE_2__["default"])(type, map);

      if (predicate(mapValue)) {
        flatten(mapValue, {
          namespace: namespace,
          prefix: prefix
        }, partialFlatMap, nextNamespace);
      } else {
        partialFlatMap[nextNamespace] = mapValue;
      }
    });
    return partialFlatMap;
  };
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/get.js":
/*!****************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/get.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return get; });
/* harmony import */ var _isMap__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isMap */ "./node_modules/redux-actions/es/utils/isMap.js");

function get(key, x) {
  return Object(_isMap__WEBPACK_IMPORTED_MODULE_0__["default"])(x) ? x.get(key) : x[key];
}

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/getLastElement.js":
/*!***************************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/getLastElement.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (array) {
  return array[array.length - 1];
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/hasGeneratorInterface.js":
/*!**********************************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/hasGeneratorInterface.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return hasGeneratorInterface; });
/* harmony import */ var _ownKeys__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ownKeys */ "./node_modules/redux-actions/es/utils/ownKeys.js");

function hasGeneratorInterface(handler) {
  var keys = Object(_ownKeys__WEBPACK_IMPORTED_MODULE_0__["default"])(handler);
  var hasOnlyInterfaceNames = keys.every(function (ownKey) {
    return ownKey === 'next' || ownKey === 'throw';
  });
  return keys.length && keys.length <= 2 && hasOnlyInterfaceNames;
}

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/identity.js":
/*!*********************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/identity.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (value) {
  return value;
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/isArray.js":
/*!********************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/isArray.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (value) {
  return Array.isArray(value);
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/isEmpty.js":
/*!********************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/isEmpty.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (value) {
  return value.length === 0;
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/isFunction.js":
/*!***********************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/isFunction.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (value) {
  return typeof value === 'function';
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/isMap.js":
/*!******************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/isMap.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (value) {
  return typeof Map !== 'undefined' && value instanceof Map;
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/isNil.js":
/*!******************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/isNil.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (value) {
  return value === null || value === undefined;
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/isNull.js":
/*!*******************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/isNull.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (value) {
  return value === null;
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/isPlainObject.js":
/*!**************************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/isPlainObject.js ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (value) {
  if (typeof value !== 'object' || value === null) return false;
  var proto = value;

  while (Object.getPrototypeOf(proto) !== null) {
    proto = Object.getPrototypeOf(proto);
  }

  return Object.getPrototypeOf(value) === proto;
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/isString.js":
/*!*********************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/isString.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (value) {
  return typeof value === 'string';
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/isSymbol.js":
/*!*********************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/isSymbol.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (value) {
  return typeof value === 'symbol' || typeof value === 'object' && Object.prototype.toString.call(value) === '[object Symbol]';
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/isUndefined.js":
/*!************************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/isUndefined.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (value) {
  return value === undefined;
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/ownKeys.js":
/*!********************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/ownKeys.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ownKeys; });
/* harmony import */ var _isMap__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./isMap */ "./node_modules/redux-actions/es/utils/isMap.js");

function ownKeys(object) {
  if (Object(_isMap__WEBPACK_IMPORTED_MODULE_0__["default"])(object)) {
    // We are using loose transforms in babel. Here we are trying to convert an
    // interable to an array. Loose mode expects everything to already be an
    // array. The problem is that our eslint rules encourage us to prefer
    // spread over Array.from.
    //
    // Instead of disabling loose mode we simply disable the warning.
    // eslint-disable-next-line unicorn/prefer-spread
    return Array.from(object.keys());
  }

  if (typeof Reflect !== 'undefined' && typeof Reflect.ownKeys === 'function') {
    return Reflect.ownKeys(object);
  }

  var keys = Object.getOwnPropertyNames(object);

  if (typeof Object.getOwnPropertySymbols === 'function') {
    keys = keys.concat(Object.getOwnPropertySymbols(object));
  }

  return keys;
}

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/toString.js":
/*!*********************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/toString.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (value) {
  return value.toString();
});

/***/ }),

/***/ "./node_modules/redux-actions/es/utils/unflattenActionCreators.js":
/*!************************************************************************!*\
  !*** ./node_modules/redux-actions/es/utils/unflattenActionCreators.js ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return unflattenActionCreators; });
/* harmony import */ var _constants__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../constants */ "./node_modules/redux-actions/es/constants.js");
/* harmony import */ var _isEmpty__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./isEmpty */ "./node_modules/redux-actions/es/utils/isEmpty.js");
/* harmony import */ var _camelCase__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./camelCase */ "./node_modules/redux-actions/es/utils/camelCase.js");



function unflattenActionCreators(flatActionCreators, _temp) {
  var _ref = _temp === void 0 ? {} : _temp,
      _ref$namespace = _ref.namespace,
      namespace = _ref$namespace === void 0 ? _constants__WEBPACK_IMPORTED_MODULE_0__["DEFAULT_NAMESPACE"] : _ref$namespace,
      prefix = _ref.prefix;

  function unflatten(flatActionType, partialNestedActionCreators, partialFlatActionTypePath) {
    var nextNamespace = Object(_camelCase__WEBPACK_IMPORTED_MODULE_2__["default"])(partialFlatActionTypePath.shift());

    if (Object(_isEmpty__WEBPACK_IMPORTED_MODULE_1__["default"])(partialFlatActionTypePath)) {
      partialNestedActionCreators[nextNamespace] = flatActionCreators[flatActionType];
    } else {
      if (!partialNestedActionCreators[nextNamespace]) {
        partialNestedActionCreators[nextNamespace] = {};
      }

      unflatten(flatActionType, partialNestedActionCreators[nextNamespace], partialFlatActionTypePath);
    }
  }

  var nestedActionCreators = {};
  Object.getOwnPropertyNames(flatActionCreators).forEach(function (type) {
    var unprefixedType = prefix ? type.replace("" + prefix + namespace, '') : type;
    return unflatten(type, nestedActionCreators, unprefixedType.split(namespace));
  });
  return nestedActionCreators;
}

/***/ }),

/***/ "./node_modules/redux-logger/dist/redux-logger.js":
/*!********************************************************!*\
  !*** ./node_modules/redux-logger/dist/redux-logger.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {!function(e,t){ true?t(exports):undefined}(this,function(e){"use strict";function t(e,t){e.super_=t,e.prototype=Object.create(t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}})}function r(e,t){Object.defineProperty(this,"kind",{value:e,enumerable:!0}),t&&t.length&&Object.defineProperty(this,"path",{value:t,enumerable:!0})}function n(e,t,r){n.super_.call(this,"E",e),Object.defineProperty(this,"lhs",{value:t,enumerable:!0}),Object.defineProperty(this,"rhs",{value:r,enumerable:!0})}function o(e,t){o.super_.call(this,"N",e),Object.defineProperty(this,"rhs",{value:t,enumerable:!0})}function i(e,t){i.super_.call(this,"D",e),Object.defineProperty(this,"lhs",{value:t,enumerable:!0})}function a(e,t,r){a.super_.call(this,"A",e),Object.defineProperty(this,"index",{value:t,enumerable:!0}),Object.defineProperty(this,"item",{value:r,enumerable:!0})}function f(e,t,r){var n=e.slice((r||t)+1||e.length);return e.length=t<0?e.length+t:t,e.push.apply(e,n),e}function u(e){var t="undefined"==typeof e?"undefined":N(e);return"object"!==t?t:e===Math?"math":null===e?"null":Array.isArray(e)?"array":"[object Date]"===Object.prototype.toString.call(e)?"date":"function"==typeof e.toString&&/^\/.*\//.test(e.toString())?"regexp":"object"}function l(e,t,r,c,s,d,p){s=s||[],p=p||[];var g=s.slice(0);if("undefined"!=typeof d){if(c){if("function"==typeof c&&c(g,d))return;if("object"===("undefined"==typeof c?"undefined":N(c))){if(c.prefilter&&c.prefilter(g,d))return;if(c.normalize){var h=c.normalize(g,d,e,t);h&&(e=h[0],t=h[1])}}}g.push(d)}"regexp"===u(e)&&"regexp"===u(t)&&(e=e.toString(),t=t.toString());var y="undefined"==typeof e?"undefined":N(e),v="undefined"==typeof t?"undefined":N(t),b="undefined"!==y||p&&p[p.length-1].lhs&&p[p.length-1].lhs.hasOwnProperty(d),m="undefined"!==v||p&&p[p.length-1].rhs&&p[p.length-1].rhs.hasOwnProperty(d);if(!b&&m)r(new o(g,t));else if(!m&&b)r(new i(g,e));else if(u(e)!==u(t))r(new n(g,e,t));else if("date"===u(e)&&e-t!==0)r(new n(g,e,t));else if("object"===y&&null!==e&&null!==t)if(p.filter(function(t){return t.lhs===e}).length)e!==t&&r(new n(g,e,t));else{if(p.push({lhs:e,rhs:t}),Array.isArray(e)){var w;e.length;for(w=0;w<e.length;w++)w>=t.length?r(new a(g,w,new i(void 0,e[w]))):l(e[w],t[w],r,c,g,w,p);for(;w<t.length;)r(new a(g,w,new o(void 0,t[w++])))}else{var x=Object.keys(e),S=Object.keys(t);x.forEach(function(n,o){var i=S.indexOf(n);i>=0?(l(e[n],t[n],r,c,g,n,p),S=f(S,i)):l(e[n],void 0,r,c,g,n,p)}),S.forEach(function(e){l(void 0,t[e],r,c,g,e,p)})}p.length=p.length-1}else e!==t&&("number"===y&&isNaN(e)&&isNaN(t)||r(new n(g,e,t)))}function c(e,t,r,n){return n=n||[],l(e,t,function(e){e&&n.push(e)},r),n.length?n:void 0}function s(e,t,r){if(r.path&&r.path.length){var n,o=e[t],i=r.path.length-1;for(n=0;n<i;n++)o=o[r.path[n]];switch(r.kind){case"A":s(o[r.path[n]],r.index,r.item);break;case"D":delete o[r.path[n]];break;case"E":case"N":o[r.path[n]]=r.rhs}}else switch(r.kind){case"A":s(e[t],r.index,r.item);break;case"D":e=f(e,t);break;case"E":case"N":e[t]=r.rhs}return e}function d(e,t,r){if(e&&t&&r&&r.kind){for(var n=e,o=-1,i=r.path?r.path.length-1:0;++o<i;)"undefined"==typeof n[r.path[o]]&&(n[r.path[o]]="number"==typeof r.path[o]?[]:{}),n=n[r.path[o]];switch(r.kind){case"A":s(r.path?n[r.path[o]]:n,r.index,r.item);break;case"D":delete n[r.path[o]];break;case"E":case"N":n[r.path[o]]=r.rhs}}}function p(e,t,r){if(r.path&&r.path.length){var n,o=e[t],i=r.path.length-1;for(n=0;n<i;n++)o=o[r.path[n]];switch(r.kind){case"A":p(o[r.path[n]],r.index,r.item);break;case"D":o[r.path[n]]=r.lhs;break;case"E":o[r.path[n]]=r.lhs;break;case"N":delete o[r.path[n]]}}else switch(r.kind){case"A":p(e[t],r.index,r.item);break;case"D":e[t]=r.lhs;break;case"E":e[t]=r.lhs;break;case"N":e=f(e,t)}return e}function g(e,t,r){if(e&&t&&r&&r.kind){var n,o,i=e;for(o=r.path.length-1,n=0;n<o;n++)"undefined"==typeof i[r.path[n]]&&(i[r.path[n]]={}),i=i[r.path[n]];switch(r.kind){case"A":p(i[r.path[n]],r.index,r.item);break;case"D":i[r.path[n]]=r.lhs;break;case"E":i[r.path[n]]=r.lhs;break;case"N":delete i[r.path[n]]}}}function h(e,t,r){if(e&&t){var n=function(n){r&&!r(e,t,n)||d(e,t,n)};l(e,t,n)}}function y(e){return"color: "+F[e].color+"; font-weight: bold"}function v(e){var t=e.kind,r=e.path,n=e.lhs,o=e.rhs,i=e.index,a=e.item;switch(t){case"E":return[r.join("."),n,"→",o];case"N":return[r.join("."),o];case"D":return[r.join(".")];case"A":return[r.join(".")+"["+i+"]",a];default:return[]}}function b(e,t,r,n){var o=c(e,t);try{n?r.groupCollapsed("diff"):r.group("diff")}catch(e){r.log("diff")}o?o.forEach(function(e){var t=e.kind,n=v(e);r.log.apply(r,["%c "+F[t].text,y(t)].concat(P(n)))}):r.log("—— no diff ——");try{r.groupEnd()}catch(e){r.log("—— diff end —— ")}}function m(e,t,r,n){switch("undefined"==typeof e?"undefined":N(e)){case"object":return"function"==typeof e[n]?e[n].apply(e,P(r)):e[n];case"function":return e(t);default:return e}}function w(e){var t=e.timestamp,r=e.duration;return function(e,n,o){var i=["action"];return i.push("%c"+String(e.type)),t&&i.push("%c@ "+n),r&&i.push("%c(in "+o.toFixed(2)+" ms)"),i.join(" ")}}function x(e,t){var r=t.logger,n=t.actionTransformer,o=t.titleFormatter,i=void 0===o?w(t):o,a=t.collapsed,f=t.colors,u=t.level,l=t.diff,c="undefined"==typeof t.titleFormatter;e.forEach(function(o,s){var d=o.started,p=o.startedTime,g=o.action,h=o.prevState,y=o.error,v=o.took,w=o.nextState,x=e[s+1];x&&(w=x.prevState,v=x.started-d);var S=n(g),k="function"==typeof a?a(function(){return w},g,o):a,j=D(p),E=f.title?"color: "+f.title(S)+";":"",A=["color: gray; font-weight: lighter;"];A.push(E),t.timestamp&&A.push("color: gray; font-weight: lighter;"),t.duration&&A.push("color: gray; font-weight: lighter;");var O=i(S,j,v);try{k?f.title&&c?r.groupCollapsed.apply(r,["%c "+O].concat(A)):r.groupCollapsed(O):f.title&&c?r.group.apply(r,["%c "+O].concat(A)):r.group(O)}catch(e){r.log(O)}var N=m(u,S,[h],"prevState"),P=m(u,S,[S],"action"),C=m(u,S,[y,h],"error"),F=m(u,S,[w],"nextState");if(N)if(f.prevState){var L="color: "+f.prevState(h)+"; font-weight: bold";r[N]("%c prev state",L,h)}else r[N]("prev state",h);if(P)if(f.action){var T="color: "+f.action(S)+"; font-weight: bold";r[P]("%c action    ",T,S)}else r[P]("action    ",S);if(y&&C)if(f.error){var M="color: "+f.error(y,h)+"; font-weight: bold;";r[C]("%c error     ",M,y)}else r[C]("error     ",y);if(F)if(f.nextState){var _="color: "+f.nextState(w)+"; font-weight: bold";r[F]("%c next state",_,w)}else r[F]("next state",w);l&&b(h,w,r,k);try{r.groupEnd()}catch(e){r.log("—— log end ——")}})}function S(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},t=Object.assign({},L,e),r=t.logger,n=t.stateTransformer,o=t.errorTransformer,i=t.predicate,a=t.logErrors,f=t.diffPredicate;if("undefined"==typeof r)return function(){return function(e){return function(t){return e(t)}}};if(e.getState&&e.dispatch)return console.error("[redux-logger] redux-logger not installed. Make sure to pass logger instance as middleware:\n// Logger with default options\nimport { logger } from 'redux-logger'\nconst store = createStore(\n  reducer,\n  applyMiddleware(logger)\n)\n// Or you can create your own logger with custom options http://bit.ly/redux-logger-options\nimport createLogger from 'redux-logger'\nconst logger = createLogger({\n  // ...options\n});\nconst store = createStore(\n  reducer,\n  applyMiddleware(logger)\n)\n"),function(){return function(e){return function(t){return e(t)}}};var u=[];return function(e){var r=e.getState;return function(e){return function(l){if("function"==typeof i&&!i(r,l))return e(l);var c={};u.push(c),c.started=O.now(),c.startedTime=new Date,c.prevState=n(r()),c.action=l;var s=void 0;if(a)try{s=e(l)}catch(e){c.error=o(e)}else s=e(l);c.took=O.now()-c.started,c.nextState=n(r());var d=t.diff&&"function"==typeof f?f(r,l):t.diff;if(x(u,Object.assign({},t,{diff:d})),u.length=0,c.error)throw c.error;return s}}}}var k,j,E=function(e,t){return new Array(t+1).join(e)},A=function(e,t){return E("0",t-e.toString().length)+e},D=function(e){return A(e.getHours(),2)+":"+A(e.getMinutes(),2)+":"+A(e.getSeconds(),2)+"."+A(e.getMilliseconds(),3)},O="undefined"!=typeof performance&&null!==performance&&"function"==typeof performance.now?performance:Date,N="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},P=function(e){if(Array.isArray(e)){for(var t=0,r=Array(e.length);t<e.length;t++)r[t]=e[t];return r}return Array.from(e)},C=[];k="object"===("undefined"==typeof global?"undefined":N(global))&&global?global:"undefined"!=typeof window?window:{},j=k.DeepDiff,j&&C.push(function(){"undefined"!=typeof j&&k.DeepDiff===c&&(k.DeepDiff=j,j=void 0)}),t(n,r),t(o,r),t(i,r),t(a,r),Object.defineProperties(c,{diff:{value:c,enumerable:!0},observableDiff:{value:l,enumerable:!0},applyDiff:{value:h,enumerable:!0},applyChange:{value:d,enumerable:!0},revertChange:{value:g,enumerable:!0},isConflict:{value:function(){return"undefined"!=typeof j},enumerable:!0},noConflict:{value:function(){return C&&(C.forEach(function(e){e()}),C=null),c},enumerable:!0}});var F={E:{color:"#2196F3",text:"CHANGED:"},N:{color:"#4CAF50",text:"ADDED:"},D:{color:"#F44336",text:"DELETED:"},A:{color:"#2196F3",text:"ARRAY:"}},L={level:"log",logger:console,logErrors:!0,collapsed:void 0,predicate:void 0,duration:!1,timestamp:!0,stateTransformer:function(e){return e},actionTransformer:function(e){return e},errorTransformer:function(e){return e},colors:{title:function(){return"inherit"},prevState:function(){return"#9E9E9E"},action:function(){return"#03A9F4"},nextState:function(){return"#4CAF50"},error:function(){return"#F20404"}},diff:!1,diffPredicate:void 0,transformer:void 0},T=function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},t=e.dispatch,r=e.getState;return"function"==typeof t||"function"==typeof r?S()({dispatch:t,getState:r}):void console.error("\n[redux-logger v3] BREAKING CHANGE\n[redux-logger v3] Since 3.0.0 redux-logger exports by default logger with default settings.\n[redux-logger v3] Change\n[redux-logger v3] import createLogger from 'redux-logger'\n[redux-logger v3] to\n[redux-logger v3] import { createLogger } from 'redux-logger'\n")};e.defaults=L,e.createLogger=S,e.logger=T,e.default=T,Object.defineProperty(e,"__esModule",{value:!0})});

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/redux-saga/dist/redux-saga-core-npm-proxy.esm.js":
/*!***********************************************************************!*\
  !*** ./node_modules/redux-saga/dist/redux-saga-core-npm-proxy.esm.js ***!
  \***********************************************************************/
/*! exports provided: CANCEL, SAGA_LOCATION, buffers, detach, END, channel, eventChannel, isEnd, multicastChannel, runSaga, stdChannel, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _redux_saga_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @redux-saga/core */ "./node_modules/@redux-saga/core/dist/redux-saga-core.esm.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "CANCEL", function() { return _redux_saga_core__WEBPACK_IMPORTED_MODULE_0__["CANCEL"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "SAGA_LOCATION", function() { return _redux_saga_core__WEBPACK_IMPORTED_MODULE_0__["SAGA_LOCATION"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "buffers", function() { return _redux_saga_core__WEBPACK_IMPORTED_MODULE_0__["buffers"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "detach", function() { return _redux_saga_core__WEBPACK_IMPORTED_MODULE_0__["detach"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "END", function() { return _redux_saga_core__WEBPACK_IMPORTED_MODULE_0__["END"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "channel", function() { return _redux_saga_core__WEBPACK_IMPORTED_MODULE_0__["channel"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "eventChannel", function() { return _redux_saga_core__WEBPACK_IMPORTED_MODULE_0__["eventChannel"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "isEnd", function() { return _redux_saga_core__WEBPACK_IMPORTED_MODULE_0__["isEnd"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "multicastChannel", function() { return _redux_saga_core__WEBPACK_IMPORTED_MODULE_0__["multicastChannel"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "runSaga", function() { return _redux_saga_core__WEBPACK_IMPORTED_MODULE_0__["runSaga"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "stdChannel", function() { return _redux_saga_core__WEBPACK_IMPORTED_MODULE_0__["stdChannel"]; });






/* harmony default export */ __webpack_exports__["default"] = (_redux_saga_core__WEBPACK_IMPORTED_MODULE_0__["default"]);


/***/ }),

/***/ "./node_modules/redux-saga/dist/redux-saga-effects-npm-proxy.esm.js":
/*!**************************************************************************!*\
  !*** ./node_modules/redux-saga/dist/redux-saga-effects-npm-proxy.esm.js ***!
  \**************************************************************************/
/*! exports provided: actionChannel, all, apply, call, cancel, cancelled, cps, delay, effectTypes, flush, fork, getContext, join, put, putResolve, race, select, setContext, spawn, take, takeMaybe, debounce, retry, takeEvery, takeLatest, takeLeading, throttle */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @redux-saga/core/effects */ "./node_modules/@redux-saga/core/dist/redux-saga-effects.esm.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "actionChannel", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["actionChannel"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "all", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["all"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "apply", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["apply"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "call", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["call"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "cancel", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["cancel"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "cancelled", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["cancelled"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "cps", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["cps"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "delay", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["delay"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "effectTypes", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["effectTypes"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "flush", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["flush"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "fork", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["fork"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "getContext", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["getContext"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "join", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["join"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "put", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["put"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "putResolve", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["putResolve"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "race", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["race"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "select", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["select"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "setContext", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["setContext"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "spawn", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["spawn"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "take", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["take"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "takeMaybe", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["takeMaybe"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "debounce", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["debounce"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "retry", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["retry"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "takeEvery", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "takeLatest", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["takeLatest"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "takeLeading", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["takeLeading"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "throttle", function() { return _redux_saga_core_effects__WEBPACK_IMPORTED_MODULE_0__["throttle"]; });




/***/ }),

/***/ "./node_modules/redux/es/redux.js":
/*!****************************************!*\
  !*** ./node_modules/redux/es/redux.js ***!
  \****************************************/
/*! exports provided: __DO_NOT_USE__ActionTypes, applyMiddleware, bindActionCreators, combineReducers, compose, createStore, legacy_createStore */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "__DO_NOT_USE__ActionTypes", function() { return ActionTypes; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "applyMiddleware", function() { return applyMiddleware; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "bindActionCreators", function() { return bindActionCreators; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "combineReducers", function() { return combineReducers; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "compose", function() { return compose; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createStore", function() { return createStore; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "legacy_createStore", function() { return legacy_createStore; });
/* harmony import */ var _babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread2 */ "./node_modules/@babel/runtime/helpers/esm/objectSpread2.js");


/**
 * Adapted from React: https://github.com/facebook/react/blob/master/packages/shared/formatProdErrorMessage.js
 *
 * Do not require this module directly! Use normal throw error calls. These messages will be replaced with error codes
 * during build.
 * @param {number} code
 */
function formatProdErrorMessage(code) {
  return "Minified Redux error #" + code + "; visit https://redux.js.org/Errors?code=" + code + " for the full message or " + 'use the non-minified dev environment for full errors. ';
}

// Inlined version of the `symbol-observable` polyfill
var $$observable = (function () {
  return typeof Symbol === 'function' && Symbol.observable || '@@observable';
})();

/**
 * These are private action types reserved by Redux.
 * For any unknown actions, you must return the current state.
 * If the current state is undefined, you must return the initial state.
 * Do not reference these action types directly in your code.
 */
var randomString = function randomString() {
  return Math.random().toString(36).substring(7).split('').join('.');
};

var ActionTypes = {
  INIT: "@@redux/INIT" + randomString(),
  REPLACE: "@@redux/REPLACE" + randomString(),
  PROBE_UNKNOWN_ACTION: function PROBE_UNKNOWN_ACTION() {
    return "@@redux/PROBE_UNKNOWN_ACTION" + randomString();
  }
};

/**
 * @param {any} obj The object to inspect.
 * @returns {boolean} True if the argument appears to be a plain object.
 */
function isPlainObject(obj) {
  if (typeof obj !== 'object' || obj === null) return false;
  var proto = obj;

  while (Object.getPrototypeOf(proto) !== null) {
    proto = Object.getPrototypeOf(proto);
  }

  return Object.getPrototypeOf(obj) === proto;
}

// Inlined / shortened version of `kindOf` from https://github.com/jonschlinkert/kind-of
function miniKindOf(val) {
  if (val === void 0) return 'undefined';
  if (val === null) return 'null';
  var type = typeof val;

  switch (type) {
    case 'boolean':
    case 'string':
    case 'number':
    case 'symbol':
    case 'function':
      {
        return type;
      }
  }

  if (Array.isArray(val)) return 'array';
  if (isDate(val)) return 'date';
  if (isError(val)) return 'error';
  var constructorName = ctorName(val);

  switch (constructorName) {
    case 'Symbol':
    case 'Promise':
    case 'WeakMap':
    case 'WeakSet':
    case 'Map':
    case 'Set':
      return constructorName;
  } // other


  return type.slice(8, -1).toLowerCase().replace(/\s/g, '');
}

function ctorName(val) {
  return typeof val.constructor === 'function' ? val.constructor.name : null;
}

function isError(val) {
  return val instanceof Error || typeof val.message === 'string' && val.constructor && typeof val.constructor.stackTraceLimit === 'number';
}

function isDate(val) {
  if (val instanceof Date) return true;
  return typeof val.toDateString === 'function' && typeof val.getDate === 'function' && typeof val.setDate === 'function';
}

function kindOf(val) {
  var typeOfVal = typeof val;

  if (true) {
    typeOfVal = miniKindOf(val);
  }

  return typeOfVal;
}

/**
 * @deprecated
 *
 * **We recommend using the `configureStore` method
 * of the `@reduxjs/toolkit` package**, which replaces `createStore`.
 *
 * Redux Toolkit is our recommended approach for writing Redux logic today,
 * including store setup, reducers, data fetching, and more.
 *
 * **For more details, please read this Redux docs page:**
 * **https://redux.js.org/introduction/why-rtk-is-redux-today**
 *
 * `configureStore` from Redux Toolkit is an improved version of `createStore` that
 * simplifies setup and helps avoid common bugs.
 *
 * You should not be using the `redux` core package by itself today, except for learning purposes.
 * The `createStore` method from the core `redux` package will not be removed, but we encourage
 * all users to migrate to using Redux Toolkit for all Redux code.
 *
 * If you want to use `createStore` without this visual deprecation warning, use
 * the `legacy_createStore` import instead:
 *
 * `import { legacy_createStore as createStore} from 'redux'`
 *
 */

function createStore(reducer, preloadedState, enhancer) {
  var _ref2;

  if (typeof preloadedState === 'function' && typeof enhancer === 'function' || typeof enhancer === 'function' && typeof arguments[3] === 'function') {
    throw new Error( false ? undefined : 'It looks like you are passing several store enhancers to ' + 'createStore(). This is not supported. Instead, compose them ' + 'together to a single function. See https://redux.js.org/tutorials/fundamentals/part-4-store#creating-a-store-with-enhancers for an example.');
  }

  if (typeof preloadedState === 'function' && typeof enhancer === 'undefined') {
    enhancer = preloadedState;
    preloadedState = undefined;
  }

  if (typeof enhancer !== 'undefined') {
    if (typeof enhancer !== 'function') {
      throw new Error( false ? undefined : "Expected the enhancer to be a function. Instead, received: '" + kindOf(enhancer) + "'");
    }

    return enhancer(createStore)(reducer, preloadedState);
  }

  if (typeof reducer !== 'function') {
    throw new Error( false ? undefined : "Expected the root reducer to be a function. Instead, received: '" + kindOf(reducer) + "'");
  }

  var currentReducer = reducer;
  var currentState = preloadedState;
  var currentListeners = [];
  var nextListeners = currentListeners;
  var isDispatching = false;
  /**
   * This makes a shallow copy of currentListeners so we can use
   * nextListeners as a temporary list while dispatching.
   *
   * This prevents any bugs around consumers calling
   * subscribe/unsubscribe in the middle of a dispatch.
   */

  function ensureCanMutateNextListeners() {
    if (nextListeners === currentListeners) {
      nextListeners = currentListeners.slice();
    }
  }
  /**
   * Reads the state tree managed by the store.
   *
   * @returns {any} The current state tree of your application.
   */


  function getState() {
    if (isDispatching) {
      throw new Error( false ? undefined : 'You may not call store.getState() while the reducer is executing. ' + 'The reducer has already received the state as an argument. ' + 'Pass it down from the top reducer instead of reading it from the store.');
    }

    return currentState;
  }
  /**
   * Adds a change listener. It will be called any time an action is dispatched,
   * and some part of the state tree may potentially have changed. You may then
   * call `getState()` to read the current state tree inside the callback.
   *
   * You may call `dispatch()` from a change listener, with the following
   * caveats:
   *
   * 1. The subscriptions are snapshotted just before every `dispatch()` call.
   * If you subscribe or unsubscribe while the listeners are being invoked, this
   * will not have any effect on the `dispatch()` that is currently in progress.
   * However, the next `dispatch()` call, whether nested or not, will use a more
   * recent snapshot of the subscription list.
   *
   * 2. The listener should not expect to see all state changes, as the state
   * might have been updated multiple times during a nested `dispatch()` before
   * the listener is called. It is, however, guaranteed that all subscribers
   * registered before the `dispatch()` started will be called with the latest
   * state by the time it exits.
   *
   * @param {Function} listener A callback to be invoked on every dispatch.
   * @returns {Function} A function to remove this change listener.
   */


  function subscribe(listener) {
    if (typeof listener !== 'function') {
      throw new Error( false ? undefined : "Expected the listener to be a function. Instead, received: '" + kindOf(listener) + "'");
    }

    if (isDispatching) {
      throw new Error( false ? undefined : 'You may not call store.subscribe() while the reducer is executing. ' + 'If you would like to be notified after the store has been updated, subscribe from a ' + 'component and invoke store.getState() in the callback to access the latest state. ' + 'See https://redux.js.org/api/store#subscribelistener for more details.');
    }

    var isSubscribed = true;
    ensureCanMutateNextListeners();
    nextListeners.push(listener);
    return function unsubscribe() {
      if (!isSubscribed) {
        return;
      }

      if (isDispatching) {
        throw new Error( false ? undefined : 'You may not unsubscribe from a store listener while the reducer is executing. ' + 'See https://redux.js.org/api/store#subscribelistener for more details.');
      }

      isSubscribed = false;
      ensureCanMutateNextListeners();
      var index = nextListeners.indexOf(listener);
      nextListeners.splice(index, 1);
      currentListeners = null;
    };
  }
  /**
   * Dispatches an action. It is the only way to trigger a state change.
   *
   * The `reducer` function, used to create the store, will be called with the
   * current state tree and the given `action`. Its return value will
   * be considered the **next** state of the tree, and the change listeners
   * will be notified.
   *
   * The base implementation only supports plain object actions. If you want to
   * dispatch a Promise, an Observable, a thunk, or something else, you need to
   * wrap your store creating function into the corresponding middleware. For
   * example, see the documentation for the `redux-thunk` package. Even the
   * middleware will eventually dispatch plain object actions using this method.
   *
   * @param {Object} action A plain object representing “what changed”. It is
   * a good idea to keep actions serializable so you can record and replay user
   * sessions, or use the time travelling `redux-devtools`. An action must have
   * a `type` property which may not be `undefined`. It is a good idea to use
   * string constants for action types.
   *
   * @returns {Object} For convenience, the same action object you dispatched.
   *
   * Note that, if you use a custom middleware, it may wrap `dispatch()` to
   * return something else (for example, a Promise you can await).
   */


  function dispatch(action) {
    if (!isPlainObject(action)) {
      throw new Error( false ? undefined : "Actions must be plain objects. Instead, the actual type was: '" + kindOf(action) + "'. You may need to add middleware to your store setup to handle dispatching other values, such as 'redux-thunk' to handle dispatching functions. See https://redux.js.org/tutorials/fundamentals/part-4-store#middleware and https://redux.js.org/tutorials/fundamentals/part-6-async-logic#using-the-redux-thunk-middleware for examples.");
    }

    if (typeof action.type === 'undefined') {
      throw new Error( false ? undefined : 'Actions may not have an undefined "type" property. You may have misspelled an action type string constant.');
    }

    if (isDispatching) {
      throw new Error( false ? undefined : 'Reducers may not dispatch actions.');
    }

    try {
      isDispatching = true;
      currentState = currentReducer(currentState, action);
    } finally {
      isDispatching = false;
    }

    var listeners = currentListeners = nextListeners;

    for (var i = 0; i < listeners.length; i++) {
      var listener = listeners[i];
      listener();
    }

    return action;
  }
  /**
   * Replaces the reducer currently used by the store to calculate the state.
   *
   * You might need this if your app implements code splitting and you want to
   * load some of the reducers dynamically. You might also need this if you
   * implement a hot reloading mechanism for Redux.
   *
   * @param {Function} nextReducer The reducer for the store to use instead.
   * @returns {void}
   */


  function replaceReducer(nextReducer) {
    if (typeof nextReducer !== 'function') {
      throw new Error( false ? undefined : "Expected the nextReducer to be a function. Instead, received: '" + kindOf(nextReducer));
    }

    currentReducer = nextReducer; // This action has a similiar effect to ActionTypes.INIT.
    // Any reducers that existed in both the new and old rootReducer
    // will receive the previous state. This effectively populates
    // the new state tree with any relevant data from the old one.

    dispatch({
      type: ActionTypes.REPLACE
    });
  }
  /**
   * Interoperability point for observable/reactive libraries.
   * @returns {observable} A minimal observable of state changes.
   * For more information, see the observable proposal:
   * https://github.com/tc39/proposal-observable
   */


  function observable() {
    var _ref;

    var outerSubscribe = subscribe;
    return _ref = {
      /**
       * The minimal observable subscription method.
       * @param {Object} observer Any object that can be used as an observer.
       * The observer object should have a `next` method.
       * @returns {subscription} An object with an `unsubscribe` method that can
       * be used to unsubscribe the observable from the store, and prevent further
       * emission of values from the observable.
       */
      subscribe: function subscribe(observer) {
        if (typeof observer !== 'object' || observer === null) {
          throw new Error( false ? undefined : "Expected the observer to be an object. Instead, received: '" + kindOf(observer) + "'");
        }

        function observeState() {
          if (observer.next) {
            observer.next(getState());
          }
        }

        observeState();
        var unsubscribe = outerSubscribe(observeState);
        return {
          unsubscribe: unsubscribe
        };
      }
    }, _ref[$$observable] = function () {
      return this;
    }, _ref;
  } // When a store is created, an "INIT" action is dispatched so that every
  // reducer returns their initial state. This effectively populates
  // the initial state tree.


  dispatch({
    type: ActionTypes.INIT
  });
  return _ref2 = {
    dispatch: dispatch,
    subscribe: subscribe,
    getState: getState,
    replaceReducer: replaceReducer
  }, _ref2[$$observable] = observable, _ref2;
}
/**
 * Creates a Redux store that holds the state tree.
 *
 * **We recommend using `configureStore` from the
 * `@reduxjs/toolkit` package**, which replaces `createStore`:
 * **https://redux.js.org/introduction/why-rtk-is-redux-today**
 *
 * The only way to change the data in the store is to call `dispatch()` on it.
 *
 * There should only be a single store in your app. To specify how different
 * parts of the state tree respond to actions, you may combine several reducers
 * into a single reducer function by using `combineReducers`.
 *
 * @param {Function} reducer A function that returns the next state tree, given
 * the current state tree and the action to handle.
 *
 * @param {any} [preloadedState] The initial state. You may optionally specify it
 * to hydrate the state from the server in universal apps, or to restore a
 * previously serialized user session.
 * If you use `combineReducers` to produce the root reducer function, this must be
 * an object with the same shape as `combineReducers` keys.
 *
 * @param {Function} [enhancer] The store enhancer. You may optionally specify it
 * to enhance the store with third-party capabilities such as middleware,
 * time travel, persistence, etc. The only store enhancer that ships with Redux
 * is `applyMiddleware()`.
 *
 * @returns {Store} A Redux store that lets you read the state, dispatch actions
 * and subscribe to changes.
 */

var legacy_createStore = createStore;

/**
 * Prints a warning in the console if it exists.
 *
 * @param {String} message The warning message.
 * @returns {void}
 */
function warning(message) {
  /* eslint-disable no-console */
  if (typeof console !== 'undefined' && typeof console.error === 'function') {
    console.error(message);
  }
  /* eslint-enable no-console */


  try {
    // This error was thrown as a convenience so that if you enable
    // "break on all exceptions" in your console,
    // it would pause the execution at this line.
    throw new Error(message);
  } catch (e) {} // eslint-disable-line no-empty

}

function getUnexpectedStateShapeWarningMessage(inputState, reducers, action, unexpectedKeyCache) {
  var reducerKeys = Object.keys(reducers);
  var argumentName = action && action.type === ActionTypes.INIT ? 'preloadedState argument passed to createStore' : 'previous state received by the reducer';

  if (reducerKeys.length === 0) {
    return 'Store does not have a valid reducer. Make sure the argument passed ' + 'to combineReducers is an object whose values are reducers.';
  }

  if (!isPlainObject(inputState)) {
    return "The " + argumentName + " has unexpected type of \"" + kindOf(inputState) + "\". Expected argument to be an object with the following " + ("keys: \"" + reducerKeys.join('", "') + "\"");
  }

  var unexpectedKeys = Object.keys(inputState).filter(function (key) {
    return !reducers.hasOwnProperty(key) && !unexpectedKeyCache[key];
  });
  unexpectedKeys.forEach(function (key) {
    unexpectedKeyCache[key] = true;
  });
  if (action && action.type === ActionTypes.REPLACE) return;

  if (unexpectedKeys.length > 0) {
    return "Unexpected " + (unexpectedKeys.length > 1 ? 'keys' : 'key') + " " + ("\"" + unexpectedKeys.join('", "') + "\" found in " + argumentName + ". ") + "Expected to find one of the known reducer keys instead: " + ("\"" + reducerKeys.join('", "') + "\". Unexpected keys will be ignored.");
  }
}

function assertReducerShape(reducers) {
  Object.keys(reducers).forEach(function (key) {
    var reducer = reducers[key];
    var initialState = reducer(undefined, {
      type: ActionTypes.INIT
    });

    if (typeof initialState === 'undefined') {
      throw new Error( false ? undefined : "The slice reducer for key \"" + key + "\" returned undefined during initialization. " + "If the state passed to the reducer is undefined, you must " + "explicitly return the initial state. The initial state may " + "not be undefined. If you don't want to set a value for this reducer, " + "you can use null instead of undefined.");
    }

    if (typeof reducer(undefined, {
      type: ActionTypes.PROBE_UNKNOWN_ACTION()
    }) === 'undefined') {
      throw new Error( false ? undefined : "The slice reducer for key \"" + key + "\" returned undefined when probed with a random type. " + ("Don't try to handle '" + ActionTypes.INIT + "' or other actions in \"redux/*\" ") + "namespace. They are considered private. Instead, you must return the " + "current state for any unknown actions, unless it is undefined, " + "in which case you must return the initial state, regardless of the " + "action type. The initial state may not be undefined, but can be null.");
    }
  });
}
/**
 * Turns an object whose values are different reducer functions, into a single
 * reducer function. It will call every child reducer, and gather their results
 * into a single state object, whose keys correspond to the keys of the passed
 * reducer functions.
 *
 * @param {Object} reducers An object whose values correspond to different
 * reducer functions that need to be combined into one. One handy way to obtain
 * it is to use ES6 `import * as reducers` syntax. The reducers may never return
 * undefined for any action. Instead, they should return their initial state
 * if the state passed to them was undefined, and the current state for any
 * unrecognized action.
 *
 * @returns {Function} A reducer function that invokes every reducer inside the
 * passed object, and builds a state object with the same shape.
 */


function combineReducers(reducers) {
  var reducerKeys = Object.keys(reducers);
  var finalReducers = {};

  for (var i = 0; i < reducerKeys.length; i++) {
    var key = reducerKeys[i];

    if (true) {
      if (typeof reducers[key] === 'undefined') {
        warning("No reducer provided for key \"" + key + "\"");
      }
    }

    if (typeof reducers[key] === 'function') {
      finalReducers[key] = reducers[key];
    }
  }

  var finalReducerKeys = Object.keys(finalReducers); // This is used to make sure we don't warn about the same
  // keys multiple times.

  var unexpectedKeyCache;

  if (true) {
    unexpectedKeyCache = {};
  }

  var shapeAssertionError;

  try {
    assertReducerShape(finalReducers);
  } catch (e) {
    shapeAssertionError = e;
  }

  return function combination(state, action) {
    if (state === void 0) {
      state = {};
    }

    if (shapeAssertionError) {
      throw shapeAssertionError;
    }

    if (true) {
      var warningMessage = getUnexpectedStateShapeWarningMessage(state, finalReducers, action, unexpectedKeyCache);

      if (warningMessage) {
        warning(warningMessage);
      }
    }

    var hasChanged = false;
    var nextState = {};

    for (var _i = 0; _i < finalReducerKeys.length; _i++) {
      var _key = finalReducerKeys[_i];
      var reducer = finalReducers[_key];
      var previousStateForKey = state[_key];
      var nextStateForKey = reducer(previousStateForKey, action);

      if (typeof nextStateForKey === 'undefined') {
        var actionType = action && action.type;
        throw new Error( false ? undefined : "When called with an action of type " + (actionType ? "\"" + String(actionType) + "\"" : '(unknown type)') + ", the slice reducer for key \"" + _key + "\" returned undefined. " + "To ignore an action, you must explicitly return the previous state. " + "If you want this reducer to hold no value, you can return null instead of undefined.");
      }

      nextState[_key] = nextStateForKey;
      hasChanged = hasChanged || nextStateForKey !== previousStateForKey;
    }

    hasChanged = hasChanged || finalReducerKeys.length !== Object.keys(state).length;
    return hasChanged ? nextState : state;
  };
}

function bindActionCreator(actionCreator, dispatch) {
  return function () {
    return dispatch(actionCreator.apply(this, arguments));
  };
}
/**
 * Turns an object whose values are action creators, into an object with the
 * same keys, but with every function wrapped into a `dispatch` call so they
 * may be invoked directly. This is just a convenience method, as you can call
 * `store.dispatch(MyActionCreators.doSomething())` yourself just fine.
 *
 * For convenience, you can also pass an action creator as the first argument,
 * and get a dispatch wrapped function in return.
 *
 * @param {Function|Object} actionCreators An object whose values are action
 * creator functions. One handy way to obtain it is to use ES6 `import * as`
 * syntax. You may also pass a single function.
 *
 * @param {Function} dispatch The `dispatch` function available on your Redux
 * store.
 *
 * @returns {Function|Object} The object mimicking the original object, but with
 * every action creator wrapped into the `dispatch` call. If you passed a
 * function as `actionCreators`, the return value will also be a single
 * function.
 */


function bindActionCreators(actionCreators, dispatch) {
  if (typeof actionCreators === 'function') {
    return bindActionCreator(actionCreators, dispatch);
  }

  if (typeof actionCreators !== 'object' || actionCreators === null) {
    throw new Error( false ? undefined : "bindActionCreators expected an object or a function, but instead received: '" + kindOf(actionCreators) + "'. " + "Did you write \"import ActionCreators from\" instead of \"import * as ActionCreators from\"?");
  }

  var boundActionCreators = {};

  for (var key in actionCreators) {
    var actionCreator = actionCreators[key];

    if (typeof actionCreator === 'function') {
      boundActionCreators[key] = bindActionCreator(actionCreator, dispatch);
    }
  }

  return boundActionCreators;
}

/**
 * Composes single-argument functions from right to left. The rightmost
 * function can take multiple arguments as it provides the signature for
 * the resulting composite function.
 *
 * @param {...Function} funcs The functions to compose.
 * @returns {Function} A function obtained by composing the argument functions
 * from right to left. For example, compose(f, g, h) is identical to doing
 * (...args) => f(g(h(...args))).
 */
function compose() {
  for (var _len = arguments.length, funcs = new Array(_len), _key = 0; _key < _len; _key++) {
    funcs[_key] = arguments[_key];
  }

  if (funcs.length === 0) {
    return function (arg) {
      return arg;
    };
  }

  if (funcs.length === 1) {
    return funcs[0];
  }

  return funcs.reduce(function (a, b) {
    return function () {
      return a(b.apply(void 0, arguments));
    };
  });
}

/**
 * Creates a store enhancer that applies middleware to the dispatch method
 * of the Redux store. This is handy for a variety of tasks, such as expressing
 * asynchronous actions in a concise manner, or logging every action payload.
 *
 * See `redux-thunk` package as an example of the Redux middleware.
 *
 * Because middleware is potentially asynchronous, this should be the first
 * store enhancer in the composition chain.
 *
 * Note that each middleware will be given the `dispatch` and `getState` functions
 * as named arguments.
 *
 * @param {...Function} middlewares The middleware chain to be applied.
 * @returns {Function} A store enhancer applying the middleware.
 */

function applyMiddleware() {
  for (var _len = arguments.length, middlewares = new Array(_len), _key = 0; _key < _len; _key++) {
    middlewares[_key] = arguments[_key];
  }

  return function (createStore) {
    return function () {
      var store = createStore.apply(void 0, arguments);

      var _dispatch = function dispatch() {
        throw new Error( false ? undefined : 'Dispatching while constructing your middleware is not allowed. ' + 'Other middleware would not be applied to this dispatch.');
      };

      var middlewareAPI = {
        getState: store.getState,
        dispatch: function dispatch() {
          return _dispatch.apply(void 0, arguments);
        }
      };
      var chain = middlewares.map(function (middleware) {
        return middleware(middlewareAPI);
      });
      _dispatch = compose.apply(void 0, chain)(store.dispatch);
      return Object(_babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__["default"])(Object(_babel_runtime_helpers_esm_objectSpread2__WEBPACK_IMPORTED_MODULE_0__["default"])({}, store), {}, {
        dispatch: _dispatch
      });
    };
  };
}

/*
 * This is a dummy function to check if the function name has been altered by minification.
 * If the function has been minified and NODE_ENV !== 'production', warn the user.
 */

function isCrushed() {}

if ( true && typeof isCrushed.name === 'string' && isCrushed.name !== 'isCrushed') {
  warning('You are currently using minified code outside of NODE_ENV === "production". ' + 'This means that you are running a slower development build of Redux. ' + 'You can use loose-envify (https://github.com/zertosh/loose-envify) for browserify ' + 'or setting mode to production in webpack (https://webpack.js.org/concepts/mode/) ' + 'to ensure you have the correct code for your production build.');
}




/***/ }),

/***/ "./node_modules/styled-components/dist/styled-components.browser.esm.js":
/*!******************************************************************************!*\
  !*** ./node_modules/styled-components/dist/styled-components.browser.esm.js ***!
  \******************************************************************************/
/*! exports provided: default, createGlobalStyle, css, isStyledComponent, keyframes, ServerStyleSheet, StyleSheetConsumer, StyleSheetContext, StyleSheetManager, ThemeConsumer, ThemeContext, ThemeProvider, withTheme, __DO_NOT_USE_OR_YOU_WILL_BE_HAUNTED_BY_SPOOKY_GHOSTS */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(process) {/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createGlobalStyle", function() { return createGlobalStyle; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "css", function() { return css; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isStyledComponent", function() { return isStyledComponent; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "keyframes", function() { return keyframes; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ServerStyleSheet", function() { return ServerStyleSheet; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "StyleSheetConsumer", function() { return StyleSheetConsumer; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "StyleSheetContext", function() { return StyleSheetContext; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "StyleSheetManager", function() { return StyleSheetManager; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ThemeConsumer", function() { return ThemeConsumer; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ThemeContext", function() { return ThemeContext; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ThemeProvider", function() { return ThemeProvider; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "withTheme", function() { return withTheme; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "__DO_NOT_USE_OR_YOU_WILL_BE_HAUNTED_BY_SPOOKY_GHOSTS", function() { return __DO_NOT_USE_OR_YOU_WILL_BE_HAUNTED_BY_SPOOKY_GHOSTS; });
/* harmony import */ var stylis_stylis_min__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! stylis/stylis.min */ "./node_modules/stylis/stylis.min.js");
/* harmony import */ var stylis_stylis_min__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(stylis_stylis_min__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var stylis_rule_sheet__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! stylis-rule-sheet */ "./node_modules/stylis-rule-sheet/index.js");
/* harmony import */ var stylis_rule_sheet__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(stylis_rule_sheet__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _emotion_unitless__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @emotion/unitless */ "./node_modules/@emotion/unitless/dist/unitless.browser.esm.js");
/* harmony import */ var react_is__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react-is */ "./node_modules/react-is/index.js");
/* harmony import */ var react_is__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_is__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var memoize_one__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! memoize-one */ "./node_modules/memoize-one/dist/memoize-one.esm.js");
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! prop-types */ "./node_modules/prop-types/index.js");
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(prop_types__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _emotion_is_prop_valid__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @emotion/is-prop-valid */ "./node_modules/@emotion/is-prop-valid/dist/is-prop-valid.browser.esm.js");
/* harmony import */ var merge_anything__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! merge-anything */ "./node_modules/merge-anything/dist/index.esm.js");










// 

var interleave = (function (strings, interpolations) {
  var result = [strings[0]];

  for (var i = 0, len = interpolations.length; i < len; i += 1) {
    result.push(interpolations[i], strings[i + 1]);
  }

  return result;
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) {
  return typeof obj;
} : function (obj) {
  return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
};

var classCallCheck = function (instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

var createClass = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
}();

var _extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];

    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }

  return target;
};

var inherits = function (subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function, not " + typeof superClass);
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      enumerable: false,
      writable: true,
      configurable: true
    }
  });
  if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass;
};

var objectWithoutProperties = function (obj, keys) {
  var target = {};

  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }

  return target;
};

var possibleConstructorReturn = function (self, call) {
  if (!self) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return call && (typeof call === "object" || typeof call === "function") ? call : self;
};

// 
var isPlainObject = (function (x) {
  return (typeof x === 'undefined' ? 'undefined' : _typeof(x)) === 'object' && x.constructor === Object;
});

// 
var EMPTY_ARRAY = Object.freeze([]);
var EMPTY_OBJECT = Object.freeze({});

// 
function isFunction(test) {
  return typeof test === 'function';
}

// 

function getComponentName(target) {
  return ( true ? typeof target === 'string' && target : undefined) || target.displayName || target.name || 'Component';
}

// 
function isStatelessFunction(test) {
  return typeof test === 'function' && !(test.prototype && test.prototype.isReactComponent);
}

// 
function isStyledComponent(target) {
  return target && typeof target.styledComponentId === 'string';
}

// 

var SC_ATTR = typeof process !== 'undefined' && (process.env.REACT_APP_SC_ATTR || process.env.SC_ATTR) || 'data-styled';

var SC_VERSION_ATTR = 'data-styled-version';

var SC_STREAM_ATTR = 'data-styled-streamed';

var IS_BROWSER = typeof window !== 'undefined' && 'HTMLElement' in window;

var DISABLE_SPEEDY = typeof SC_DISABLE_SPEEDY === 'boolean' && SC_DISABLE_SPEEDY || typeof process !== 'undefined' && (process.env.REACT_APP_SC_DISABLE_SPEEDY || process.env.SC_DISABLE_SPEEDY) || "development" !== 'production';

// Shared empty execution context when generating static styles
var STATIC_EXECUTION_CONTEXT = {};

// 


/**
 * Parse errors.md and turn it into a simple hash of code: message
 */
var ERRORS =  true ? {
  "1": "Cannot create styled-component for component: %s.\n\n",
  "2": "Can't collect styles once you've consumed a `ServerStyleSheet`'s styles! `ServerStyleSheet` is a one off instance for each server-side render cycle.\n\n- Are you trying to reuse it across renders?\n- Are you accidentally calling collectStyles twice?\n\n",
  "3": "Streaming SSR is only supported in a Node.js environment; Please do not try to call this method in the browser.\n\n",
  "4": "The `StyleSheetManager` expects a valid target or sheet prop!\n\n- Does this error occur on the client and is your target falsy?\n- Does this error occur on the server and is the sheet falsy?\n\n",
  "5": "The clone method cannot be used on the client!\n\n- Are you running in a client-like environment on the server?\n- Are you trying to run SSR on the client?\n\n",
  "6": "Trying to insert a new style tag, but the given Node is unmounted!\n\n- Are you using a custom target that isn't mounted?\n- Does your document not have a valid head element?\n- Have you accidentally removed a style tag manually?\n\n",
  "7": "ThemeProvider: Please return an object from your \"theme\" prop function, e.g.\n\n```js\ntheme={() => ({})}\n```\n\n",
  "8": "ThemeProvider: Please make your \"theme\" prop an object.\n\n",
  "9": "Missing document `<head>`\n\n",
  "10": "Cannot find a StyleSheet instance. Usually this happens if there are multiple copies of styled-components loaded at once. Check out this issue for how to troubleshoot and fix the common cases where this situation can happen: https://github.com/styled-components/styled-components/issues/1941#issuecomment-417862021\n\n",
  "11": "_This error was replaced with a dev-time warning, it will be deleted for v4 final._ [createGlobalStyle] received children which will not be rendered. Please use the component without passing children elements.\n\n",
  "12": "It seems you are interpolating a keyframe declaration (%s) into an untagged string. This was supported in styled-components v3, but is not longer supported in v4 as keyframes are now injected on-demand. Please wrap your string in the css\\`\\` helper which ensures the styles are injected correctly. See https://www.styled-components.com/docs/api#css\n\n",
  "13": "%s is not a styled component and cannot be referred to via component selector. See https://www.styled-components.com/docs/advanced#referring-to-other-components for more details.\n"
} : undefined;

/**
 * super basic version of sprintf
 */
function format() {
  var a = arguments.length <= 0 ? undefined : arguments[0];
  var b = [];

  for (var c = 1, len = arguments.length; c < len; c += 1) {
    b.push(arguments.length <= c ? undefined : arguments[c]);
  }

  b.forEach(function (d) {
    a = a.replace(/%[a-z]/, d);
  });

  return a;
}

/**
 * Create an error file out of errors.md for development and a simple web link to the full errors
 * in production mode.
 */

var StyledComponentsError = function (_Error) {
  inherits(StyledComponentsError, _Error);

  function StyledComponentsError(code) {
    classCallCheck(this, StyledComponentsError);

    for (var _len = arguments.length, interpolations = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      interpolations[_key - 1] = arguments[_key];
    }

    if (false) { var _this; } else {
      var _this = possibleConstructorReturn(this, _Error.call(this, format.apply(undefined, [ERRORS[code]].concat(interpolations)).trim()));
    }
    return possibleConstructorReturn(_this);
  }

  return StyledComponentsError;
}(Error);

// 
var SC_COMPONENT_ID = /^[^\S\n]*?\/\* sc-component-id:\s*(\S+)\s+\*\//gm;

var extractComps = (function (maybeCSS) {
  var css = '' + (maybeCSS || ''); // Definitely a string, and a clone
  var existingComponents = [];
  css.replace(SC_COMPONENT_ID, function (match, componentId, matchIndex) {
    existingComponents.push({ componentId: componentId, matchIndex: matchIndex });
    return match;
  });
  return existingComponents.map(function (_ref, i) {
    var componentId = _ref.componentId,
        matchIndex = _ref.matchIndex;

    var nextComp = existingComponents[i + 1];
    var cssFromDOM = nextComp ? css.slice(matchIndex, nextComp.matchIndex) : css.slice(matchIndex);
    return { componentId: componentId, cssFromDOM: cssFromDOM };
  });
});

// 

var COMMENT_REGEX = /^\s*\/\/.*$/gm;

// NOTE: This stylis instance is only used to split rules from SSR'd style tags
var stylisSplitter = new stylis_stylis_min__WEBPACK_IMPORTED_MODULE_0___default.a({
  global: false,
  cascade: true,
  keyframe: false,
  prefix: false,
  compress: false,
  semicolon: true
});

var stylis = new stylis_stylis_min__WEBPACK_IMPORTED_MODULE_0___default.a({
  global: false,
  cascade: true,
  keyframe: false,
  prefix: true,
  compress: false,
  semicolon: false // NOTE: This means "autocomplete missing semicolons"
});

// Wrap `insertRulePlugin to build a list of rules,
// and then make our own plugin to return the rules. This
// makes it easier to hook into the existing SSR architecture

var parsingRules = [];

// eslint-disable-next-line consistent-return
var returnRulesPlugin = function returnRulesPlugin(context) {
  if (context === -2) {
    var parsedRules = parsingRules;
    parsingRules = [];
    return parsedRules;
  }
};

var parseRulesPlugin = stylis_rule_sheet__WEBPACK_IMPORTED_MODULE_1___default()(function (rule) {
  parsingRules.push(rule);
});

var _componentId = void 0;
var _selector = void 0;
var _selectorRegexp = void 0;

var selfReferenceReplacer = function selfReferenceReplacer(match, offset, string) {
  if (
  // the first self-ref is always untouched
  offset > 0 &&
  // there should be at least two self-refs to do a replacement (.b > .b)
  string.slice(0, offset).indexOf(_selector) !== -1 &&
  // no consecutive self refs (.b.b); that is a precedence boost and treated differently
  string.slice(offset - _selector.length, offset) !== _selector) {
    return '.' + _componentId;
  }

  return match;
};

/**
 * When writing a style like
 *
 * & + & {
 *   color: red;
 * }
 *
 * The second ampersand should be a reference to the static component class. stylis
 * has no knowledge of static class so we have to intelligently replace the base selector.
 */
var selfReferenceReplacementPlugin = function selfReferenceReplacementPlugin(context, _, selectors) {
  if (context === 2 && selectors.length && selectors[0].lastIndexOf(_selector) > 0) {
    // eslint-disable-next-line no-param-reassign
    selectors[0] = selectors[0].replace(_selectorRegexp, selfReferenceReplacer);
  }
};

stylis.use([selfReferenceReplacementPlugin, parseRulesPlugin, returnRulesPlugin]);
stylisSplitter.use([parseRulesPlugin, returnRulesPlugin]);

var splitByRules = function splitByRules(css) {
  return stylisSplitter('', css);
};

function stringifyRules(rules, selector, prefix) {
  var componentId = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '&';

  var flatCSS = rules.join('').replace(COMMENT_REGEX, ''); // replace JS comments

  var cssStr = selector && prefix ? prefix + ' ' + selector + ' { ' + flatCSS + ' }' : flatCSS;

  // stylis has no concept of state to be passed to plugins
  // but since JS is single=threaded, we can rely on that to ensure
  // these properties stay in sync with the current stylis run
  _componentId = componentId;
  _selector = selector;
  _selectorRegexp = new RegExp('\\' + _selector + '\\b', 'g');

  return stylis(prefix || !selector ? '' : selector, cssStr);
}

// 
/* eslint-disable camelcase, no-undef */

var getNonce = (function () {
  return  true ? __webpack_require__.nc : undefined;
});

// 
/* These are helpers for the StyleTags to keep track of the injected
 * rule names for each (component) ID that they're keeping track of.
 * They're crucial for detecting whether a name has already been
 * injected.
 * (This excludes rehydrated names) */

/* adds a new ID:name pairing to a names dictionary */
var addNameForId = function addNameForId(names, id, name) {
  if (name) {
    // eslint-disable-next-line no-param-reassign
    var namesForId = names[id] || (names[id] = Object.create(null));
    namesForId[name] = true;
  }
};

/* resets an ID entirely by overwriting it in the dictionary */
var resetIdNames = function resetIdNames(names, id) {
  // eslint-disable-next-line no-param-reassign
  names[id] = Object.create(null);
};

/* factory for a names dictionary checking the existance of an ID:name pairing */
var hasNameForId = function hasNameForId(names) {
  return function (id, name) {
    return names[id] !== undefined && names[id][name];
  };
};

/* stringifies names for the html/element output */
var stringifyNames = function stringifyNames(names) {
  var str = '';
  // eslint-disable-next-line guard-for-in
  for (var id in names) {
    str += Object.keys(names[id]).join(' ') + ' ';
  }
  return str.trim();
};

/* clones the nested names dictionary */
var cloneNames = function cloneNames(names) {
  var clone = Object.create(null);
  // eslint-disable-next-line guard-for-in
  for (var id in names) {
    clone[id] = _extends({}, names[id]);
  }
  return clone;
};

// 

/* These are helpers that deal with the insertRule (aka speedy) API
 * They are used in the StyleTags and specifically the speedy tag
 */

/* retrieve a sheet for a given style tag */
var sheetForTag = function sheetForTag(tag) {
  // $FlowFixMe
  if (tag.sheet) return tag.sheet;

  /* Firefox quirk requires us to step through all stylesheets to find one owned by the given tag */
  var size = tag.ownerDocument.styleSheets.length;
  for (var i = 0; i < size; i += 1) {
    var sheet = tag.ownerDocument.styleSheets[i];
    // $FlowFixMe
    if (sheet.ownerNode === tag) return sheet;
  }

  /* we should always be able to find a tag */
  throw new StyledComponentsError(10);
};

/* insert a rule safely and return whether it was actually injected */
var safeInsertRule = function safeInsertRule(sheet, cssRule, index) {
  /* abort early if cssRule string is falsy */
  if (!cssRule) return false;

  var maxIndex = sheet.cssRules.length;

  try {
    /* use insertRule and cap passed index with maxIndex (no of cssRules) */
    sheet.insertRule(cssRule, index <= maxIndex ? index : maxIndex);
  } catch (err) {
    /* any error indicates an invalid rule */
    return false;
  }

  return true;
};

/* deletes `size` rules starting from `removalIndex` */
var deleteRules = function deleteRules(sheet, removalIndex, size) {
  var lowerBound = removalIndex - size;
  for (var i = removalIndex; i > lowerBound; i -= 1) {
    sheet.deleteRule(i);
  }
};

// 

/* this marker separates component styles and is important for rehydration */
var makeTextMarker = function makeTextMarker(id) {
  return '\n/* sc-component-id: ' + id + ' */\n';
};

/* add up all numbers in array up until and including the index */
var addUpUntilIndex = function addUpUntilIndex(sizes, index) {
  var totalUpToIndex = 0;
  for (var i = 0; i <= index; i += 1) {
    totalUpToIndex += sizes[i];
  }

  return totalUpToIndex;
};

/* create a new style tag after lastEl */
var makeStyleTag = function makeStyleTag(target, tagEl, insertBefore) {
  var targetDocument = document;
  if (target) targetDocument = target.ownerDocument;else if (tagEl) targetDocument = tagEl.ownerDocument;

  var el = targetDocument.createElement('style');
  el.setAttribute(SC_ATTR, '');
  el.setAttribute(SC_VERSION_ATTR, "4.4.1");

  var nonce = getNonce();
  if (nonce) {
    el.setAttribute('nonce', nonce);
  }

  /* Work around insertRule quirk in EdgeHTML */
  el.appendChild(targetDocument.createTextNode(''));

  if (target && !tagEl) {
    /* Append to target when no previous element was passed */
    target.appendChild(el);
  } else {
    if (!tagEl || !target || !tagEl.parentNode) {
      throw new StyledComponentsError(6);
    }

    /* Insert new style tag after the previous one */
    tagEl.parentNode.insertBefore(el, insertBefore ? tagEl : tagEl.nextSibling);
  }

  return el;
};

/* takes a css factory function and outputs an html styled tag factory */
var wrapAsHtmlTag = function wrapAsHtmlTag(css, names) {
  return function (additionalAttrs) {
    var nonce = getNonce();
    var attrs = [nonce && 'nonce="' + nonce + '"', SC_ATTR + '="' + stringifyNames(names) + '"', SC_VERSION_ATTR + '="' + "4.4.1" + '"', additionalAttrs];

    var htmlAttr = attrs.filter(Boolean).join(' ');
    return '<style ' + htmlAttr + '>' + css() + '</style>';
  };
};

/* takes a css factory function and outputs an element factory */
var wrapAsElement = function wrapAsElement(css, names) {
  return function () {
    var _props;

    var props = (_props = {}, _props[SC_ATTR] = stringifyNames(names), _props[SC_VERSION_ATTR] = "4.4.1", _props);

    var nonce = getNonce();
    if (nonce) {
      // $FlowFixMe
      props.nonce = nonce;
    }

    // eslint-disable-next-line react/no-danger
    return react__WEBPACK_IMPORTED_MODULE_2___default.a.createElement('style', _extends({}, props, { dangerouslySetInnerHTML: { __html: css() } }));
  };
};

var getIdsFromMarkersFactory = function getIdsFromMarkersFactory(markers) {
  return function () {
    return Object.keys(markers);
  };
};

/* speedy tags utilise insertRule */
var makeSpeedyTag = function makeSpeedyTag(el, getImportRuleTag) {
  var names = Object.create(null);
  var markers = Object.create(null);
  var sizes = [];

  var extractImport = getImportRuleTag !== undefined;
  /* indicates whether getImportRuleTag was called */
  var usedImportRuleTag = false;

  var insertMarker = function insertMarker(id) {
    var prev = markers[id];
    if (prev !== undefined) {
      return prev;
    }

    markers[id] = sizes.length;
    sizes.push(0);
    resetIdNames(names, id);

    return markers[id];
  };

  var insertRules = function insertRules(id, cssRules, name) {
    var marker = insertMarker(id);
    var sheet = sheetForTag(el);
    var insertIndex = addUpUntilIndex(sizes, marker);

    var injectedRules = 0;
    var importRules = [];
    var cssRulesSize = cssRules.length;

    for (var i = 0; i < cssRulesSize; i += 1) {
      var cssRule = cssRules[i];
      var mayHaveImport = extractImport; /* @import rules are reordered to appear first */
      if (mayHaveImport && cssRule.indexOf('@import') !== -1) {
        importRules.push(cssRule);
      } else if (safeInsertRule(sheet, cssRule, insertIndex + injectedRules)) {
        mayHaveImport = false;
        injectedRules += 1;
      }
    }

    if (extractImport && importRules.length > 0) {
      usedImportRuleTag = true;
      // $FlowFixMe
      getImportRuleTag().insertRules(id + '-import', importRules);
    }

    sizes[marker] += injectedRules; /* add up no of injected rules */
    addNameForId(names, id, name);
  };

  var removeRules = function removeRules(id) {
    var marker = markers[id];
    if (marker === undefined) return;
    // $FlowFixMe
    if (el.isConnected === false) return;

    var size = sizes[marker];
    var sheet = sheetForTag(el);
    var removalIndex = addUpUntilIndex(sizes, marker) - 1;
    deleteRules(sheet, removalIndex, size);
    sizes[marker] = 0;
    resetIdNames(names, id);

    if (extractImport && usedImportRuleTag) {
      // $FlowFixMe
      getImportRuleTag().removeRules(id + '-import');
    }
  };

  var css = function css() {
    var _sheetForTag = sheetForTag(el),
        cssRules = _sheetForTag.cssRules;

    var str = '';

    // eslint-disable-next-line guard-for-in
    for (var id in markers) {
      str += makeTextMarker(id);
      var marker = markers[id];
      var end = addUpUntilIndex(sizes, marker);
      var size = sizes[marker];
      for (var i = end - size; i < end; i += 1) {
        var rule = cssRules[i];
        if (rule !== undefined) {
          str += rule.cssText;
        }
      }
    }

    return str;
  };

  return {
    clone: function clone() {
      throw new StyledComponentsError(5);
    },

    css: css,
    getIds: getIdsFromMarkersFactory(markers),
    hasNameForId: hasNameForId(names),
    insertMarker: insertMarker,
    insertRules: insertRules,
    removeRules: removeRules,
    sealed: false,
    styleTag: el,
    toElement: wrapAsElement(css, names),
    toHTML: wrapAsHtmlTag(css, names)
  };
};

var makeTextNode = function makeTextNode(targetDocument, id) {
  return targetDocument.createTextNode(makeTextMarker(id));
};

var makeBrowserTag = function makeBrowserTag(el, getImportRuleTag) {
  var names = Object.create(null);
  var markers = Object.create(null);

  var extractImport = getImportRuleTag !== undefined;

  /* indicates whether getImportRuleTag was called */
  var usedImportRuleTag = false;

  var insertMarker = function insertMarker(id) {
    var prev = markers[id];
    if (prev !== undefined) {
      return prev;
    }

    markers[id] = makeTextNode(el.ownerDocument, id);
    el.appendChild(markers[id]);
    names[id] = Object.create(null);

    return markers[id];
  };

  var insertRules = function insertRules(id, cssRules, name) {
    var marker = insertMarker(id);
    var importRules = [];
    var cssRulesSize = cssRules.length;

    for (var i = 0; i < cssRulesSize; i += 1) {
      var rule = cssRules[i];
      var mayHaveImport = extractImport;
      if (mayHaveImport && rule.indexOf('@import') !== -1) {
        importRules.push(rule);
      } else {
        mayHaveImport = false;
        var separator = i === cssRulesSize - 1 ? '' : ' ';
        marker.appendData('' + rule + separator);
      }
    }

    addNameForId(names, id, name);

    if (extractImport && importRules.length > 0) {
      usedImportRuleTag = true;
      // $FlowFixMe
      getImportRuleTag().insertRules(id + '-import', importRules);
    }
  };

  var removeRules = function removeRules(id) {
    var marker = markers[id];
    if (marker === undefined) return;

    /* create new empty text node and replace the current one */
    var newMarker = makeTextNode(el.ownerDocument, id);
    el.replaceChild(newMarker, marker);
    markers[id] = newMarker;
    resetIdNames(names, id);

    if (extractImport && usedImportRuleTag) {
      // $FlowFixMe
      getImportRuleTag().removeRules(id + '-import');
    }
  };

  var css = function css() {
    var str = '';

    // eslint-disable-next-line guard-for-in
    for (var id in markers) {
      str += markers[id].data;
    }

    return str;
  };

  return {
    clone: function clone() {
      throw new StyledComponentsError(5);
    },

    css: css,
    getIds: getIdsFromMarkersFactory(markers),
    hasNameForId: hasNameForId(names),
    insertMarker: insertMarker,
    insertRules: insertRules,
    removeRules: removeRules,
    sealed: false,
    styleTag: el,
    toElement: wrapAsElement(css, names),
    toHTML: wrapAsHtmlTag(css, names)
  };
};

var makeServerTag = function makeServerTag(namesArg, markersArg) {
  var names = namesArg === undefined ? Object.create(null) : namesArg;
  var markers = markersArg === undefined ? Object.create(null) : markersArg;

  var insertMarker = function insertMarker(id) {
    var prev = markers[id];
    if (prev !== undefined) {
      return prev;
    }

    return markers[id] = [''];
  };

  var insertRules = function insertRules(id, cssRules, name) {
    var marker = insertMarker(id);
    marker[0] += cssRules.join(' ');
    addNameForId(names, id, name);
  };

  var removeRules = function removeRules(id) {
    var marker = markers[id];
    if (marker === undefined) return;
    marker[0] = '';
    resetIdNames(names, id);
  };

  var css = function css() {
    var str = '';
    // eslint-disable-next-line guard-for-in
    for (var id in markers) {
      var cssForId = markers[id][0];
      if (cssForId) {
        str += makeTextMarker(id) + cssForId;
      }
    }
    return str;
  };

  var clone = function clone() {
    var namesClone = cloneNames(names);
    var markersClone = Object.create(null);

    // eslint-disable-next-line guard-for-in
    for (var id in markers) {
      markersClone[id] = [markers[id][0]];
    }

    return makeServerTag(namesClone, markersClone);
  };

  var tag = {
    clone: clone,
    css: css,
    getIds: getIdsFromMarkersFactory(markers),
    hasNameForId: hasNameForId(names),
    insertMarker: insertMarker,
    insertRules: insertRules,
    removeRules: removeRules,
    sealed: false,
    styleTag: null,
    toElement: wrapAsElement(css, names),
    toHTML: wrapAsHtmlTag(css, names)
  };

  return tag;
};

var makeTag = function makeTag(target, tagEl, forceServer, insertBefore, getImportRuleTag) {
  if (IS_BROWSER && !forceServer) {
    var el = makeStyleTag(target, tagEl, insertBefore);

    if (DISABLE_SPEEDY) {
      return makeBrowserTag(el, getImportRuleTag);
    } else {
      return makeSpeedyTag(el, getImportRuleTag);
    }
  }

  return makeServerTag();
};

var rehydrate = function rehydrate(tag, els, extracted) {
  /* add all extracted components to the new tag */
  for (var i = 0, len = extracted.length; i < len; i += 1) {
    var _extracted$i = extracted[i],
        componentId = _extracted$i.componentId,
        cssFromDOM = _extracted$i.cssFromDOM;

    var cssRules = splitByRules(cssFromDOM);
    tag.insertRules(componentId, cssRules);
  }

  /* remove old HTMLStyleElements, since they have been rehydrated */
  for (var _i = 0, _len = els.length; _i < _len; _i += 1) {
    var el = els[_i];
    if (el.parentNode) {
      el.parentNode.removeChild(el);
    }
  }
};

// 

var SPLIT_REGEX = /\s+/;

/* determine the maximum number of components before tags are sharded */
var MAX_SIZE = void 0;
if (IS_BROWSER) {
  /* in speedy mode we can keep a lot more rules in a sheet before a slowdown can be expected */
  MAX_SIZE = DISABLE_SPEEDY ? 40 : 1000;
} else {
  /* for servers we do not need to shard at all */
  MAX_SIZE = -1;
}

var sheetRunningId = 0;
var master = void 0;

var StyleSheet = function () {

  /* a map from ids to tags */

  /* deferred rules for a given id */

  /* this is used for not reinjecting rules via hasNameForId() */

  /* when rules for an id are removed using remove() we have to ignore rehydratedNames for it */

  /* a list of tags belonging to this StyleSheet */

  /* a tag for import rules */

  /* current capacity until a new tag must be created */

  /* children (aka clones) of this StyleSheet inheriting all and future injections */

  function StyleSheet() {
    var _this = this;

    var target = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : IS_BROWSER ? document.head : null;
    var forceServer = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
    classCallCheck(this, StyleSheet);

    this.getImportRuleTag = function () {
      var importRuleTag = _this.importRuleTag;

      if (importRuleTag !== undefined) {
        return importRuleTag;
      }

      var firstTag = _this.tags[0];
      var insertBefore = true;

      return _this.importRuleTag = makeTag(_this.target, firstTag ? firstTag.styleTag : null, _this.forceServer, insertBefore);
    };

    sheetRunningId += 1;
    this.id = sheetRunningId;
    this.forceServer = forceServer;
    this.target = forceServer ? null : target;
    this.tagMap = {};
    this.deferred = {};
    this.rehydratedNames = {};
    this.ignoreRehydratedNames = {};
    this.tags = [];
    this.capacity = 1;
    this.clones = [];
  }

  /* rehydrate all SSR'd style tags */


  StyleSheet.prototype.rehydrate = function rehydrate$$1() {
    if (!IS_BROWSER || this.forceServer) return this;

    var els = [];
    var extracted = [];
    var isStreamed = false;

    /* retrieve all of our SSR style elements from the DOM */
    var nodes = document.querySelectorAll('style[' + SC_ATTR + '][' + SC_VERSION_ATTR + '="' + "4.4.1" + '"]');

    var nodesSize = nodes.length;

    /* abort rehydration if no previous style tags were found */
    if (!nodesSize) return this;

    for (var i = 0; i < nodesSize; i += 1) {
      var el = nodes[i];

      /* check if style tag is a streamed tag */
      if (!isStreamed) isStreamed = !!el.getAttribute(SC_STREAM_ATTR);

      /* retrieve all component names */
      var elNames = (el.getAttribute(SC_ATTR) || '').trim().split(SPLIT_REGEX);
      var elNamesSize = elNames.length;
      for (var j = 0, name; j < elNamesSize; j += 1) {
        name = elNames[j];
        /* add rehydrated name to sheet to avoid re-adding styles */
        this.rehydratedNames[name] = true;
      }

      /* extract all components and their CSS */
      extracted.push.apply(extracted, extractComps(el.textContent));

      /* store original HTMLStyleElement */
      els.push(el);
    }

    /* abort rehydration if nothing was extracted */
    var extractedSize = extracted.length;
    if (!extractedSize) return this;

    /* create a tag to be used for rehydration */
    var tag = this.makeTag(null);

    rehydrate(tag, els, extracted);

    /* reset capacity and adjust MAX_SIZE by the initial size of the rehydration */
    this.capacity = Math.max(1, MAX_SIZE - extractedSize);
    this.tags.push(tag);

    /* retrieve all component ids */
    for (var _j = 0; _j < extractedSize; _j += 1) {
      this.tagMap[extracted[_j].componentId] = tag;
    }

    return this;
  };

  /* retrieve a "master" instance of StyleSheet which is typically used when no other is available
   * The master StyleSheet is targeted by createGlobalStyle, keyframes, and components outside of any
    * StyleSheetManager's context */


  /* reset the internal "master" instance */
  StyleSheet.reset = function reset() {
    var forceServer = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

    master = new StyleSheet(undefined, forceServer).rehydrate();
  };

  /* adds "children" to the StyleSheet that inherit all of the parents' rules
   * while their own rules do not affect the parent */


  StyleSheet.prototype.clone = function clone() {
    var sheet = new StyleSheet(this.target, this.forceServer);

    /* add to clone array */
    this.clones.push(sheet);

    /* clone all tags */
    sheet.tags = this.tags.map(function (tag) {
      var ids = tag.getIds();
      var newTag = tag.clone();

      /* reconstruct tagMap */
      for (var i = 0; i < ids.length; i += 1) {
        sheet.tagMap[ids[i]] = newTag;
      }

      return newTag;
    });

    /* clone other maps */
    sheet.rehydratedNames = _extends({}, this.rehydratedNames);
    sheet.deferred = _extends({}, this.deferred);

    return sheet;
  };

  /* force StyleSheet to create a new tag on the next injection */


  StyleSheet.prototype.sealAllTags = function sealAllTags() {
    this.capacity = 1;

    this.tags.forEach(function (tag) {
      // eslint-disable-next-line no-param-reassign
      tag.sealed = true;
    });
  };

  StyleSheet.prototype.makeTag = function makeTag$$1(tag) {
    var lastEl = tag ? tag.styleTag : null;
    var insertBefore = false;

    return makeTag(this.target, lastEl, this.forceServer, insertBefore, this.getImportRuleTag);
  };

  /* get a tag for a given componentId, assign the componentId to one, or shard */
  StyleSheet.prototype.getTagForId = function getTagForId(id) {
    /* simply return a tag, when the componentId was already assigned one */
    var prev = this.tagMap[id];
    if (prev !== undefined && !prev.sealed) {
      return prev;
    }

    var tag = this.tags[this.tags.length - 1];

    /* shard (create a new tag) if the tag is exhausted (See MAX_SIZE) */
    this.capacity -= 1;

    if (this.capacity === 0) {
      this.capacity = MAX_SIZE;
      tag = this.makeTag(tag);
      this.tags.push(tag);
    }

    return this.tagMap[id] = tag;
  };

  /* mainly for createGlobalStyle to check for its id */


  StyleSheet.prototype.hasId = function hasId(id) {
    return this.tagMap[id] !== undefined;
  };

  /* caching layer checking id+name to already have a corresponding tag and injected rules */


  StyleSheet.prototype.hasNameForId = function hasNameForId(id, name) {
    /* exception for rehydrated names which are checked separately */
    if (this.ignoreRehydratedNames[id] === undefined && this.rehydratedNames[name]) {
      return true;
    }

    var tag = this.tagMap[id];
    return tag !== undefined && tag.hasNameForId(id, name);
  };

  /* registers a componentId and registers it on its tag */


  StyleSheet.prototype.deferredInject = function deferredInject(id, cssRules) {
    /* don't inject when the id is already registered */
    if (this.tagMap[id] !== undefined) return;

    var clones = this.clones;

    for (var i = 0; i < clones.length; i += 1) {
      clones[i].deferredInject(id, cssRules);
    }

    this.getTagForId(id).insertMarker(id);
    this.deferred[id] = cssRules;
  };

  /* injects rules for a given id with a name that will need to be cached */


  StyleSheet.prototype.inject = function inject(id, cssRules, name) {
    var clones = this.clones;


    for (var i = 0; i < clones.length; i += 1) {
      clones[i].inject(id, cssRules, name);
    }

    var tag = this.getTagForId(id);

    /* add deferred rules for component */
    if (this.deferred[id] !== undefined) {
      // Combine passed cssRules with previously deferred CSS rules
      // NOTE: We cannot mutate the deferred array itself as all clones
      // do the same (see clones[i].inject)
      var rules = this.deferred[id].concat(cssRules);
      tag.insertRules(id, rules, name);

      this.deferred[id] = undefined;
    } else {
      tag.insertRules(id, cssRules, name);
    }
  };

  /* removes all rules for a given id, which doesn't remove its marker but resets it */


  StyleSheet.prototype.remove = function remove(id) {
    var tag = this.tagMap[id];
    if (tag === undefined) return;

    var clones = this.clones;

    for (var i = 0; i < clones.length; i += 1) {
      clones[i].remove(id);
    }

    /* remove all rules from the tag */
    tag.removeRules(id);

    /* ignore possible rehydrated names */
    this.ignoreRehydratedNames[id] = true;

    /* delete possible deferred rules */
    this.deferred[id] = undefined;
  };

  StyleSheet.prototype.toHTML = function toHTML() {
    return this.tags.map(function (tag) {
      return tag.toHTML();
    }).join('');
  };

  StyleSheet.prototype.toReactElements = function toReactElements() {
    var id = this.id;


    return this.tags.map(function (tag, i) {
      var key = 'sc-' + id + '-' + i;
      return Object(react__WEBPACK_IMPORTED_MODULE_2__["cloneElement"])(tag.toElement(), { key: key });
    });
  };

  createClass(StyleSheet, null, [{
    key: 'master',
    get: function get$$1() {
      return master || (master = new StyleSheet().rehydrate());
    }

    /* NOTE: This is just for backwards-compatibility with jest-styled-components */

  }, {
    key: 'instance',
    get: function get$$1() {
      return StyleSheet.master;
    }
  }]);
  return StyleSheet;
}();

// 

var Keyframes = function () {
  function Keyframes(name, rules) {
    var _this = this;

    classCallCheck(this, Keyframes);

    this.inject = function (styleSheet) {
      if (!styleSheet.hasNameForId(_this.id, _this.name)) {
        styleSheet.inject(_this.id, _this.rules, _this.name);
      }
    };

    this.toString = function () {
      throw new StyledComponentsError(12, String(_this.name));
    };

    this.name = name;
    this.rules = rules;

    this.id = 'sc-keyframes-' + name;
  }

  Keyframes.prototype.getName = function getName() {
    return this.name;
  };

  return Keyframes;
}();

// 

/**
 * inlined version of
 * https://github.com/facebook/fbjs/blob/master/packages/fbjs/src/core/hyphenateStyleName.js
 */

var uppercasePattern = /([A-Z])/g;
var msPattern = /^ms-/;

/**
 * Hyphenates a camelcased CSS property name, for example:
 *
 *   > hyphenateStyleName('backgroundColor')
 *   < "background-color"
 *   > hyphenateStyleName('MozTransition')
 *   < "-moz-transition"
 *   > hyphenateStyleName('msTransition')
 *   < "-ms-transition"
 *
 * As Modernizr suggests (http://modernizr.com/docs/#prefixed), an `ms` prefix
 * is converted to `-ms-`.
 *
 * @param {string} string
 * @return {string}
 */
function hyphenateStyleName(string) {
  return string.replace(uppercasePattern, '-$1').toLowerCase().replace(msPattern, '-ms-');
}

// 

// Taken from https://github.com/facebook/react/blob/b87aabdfe1b7461e7331abb3601d9e6bb27544bc/packages/react-dom/src/shared/dangerousStyleValue.js
function addUnitIfNeeded(name, value) {
  // https://github.com/amilajack/eslint-plugin-flowtype-errors/issues/133
  // $FlowFixMe
  if (value == null || typeof value === 'boolean' || value === '') {
    return '';
  }

  if (typeof value === 'number' && value !== 0 && !(name in _emotion_unitless__WEBPACK_IMPORTED_MODULE_3__["default"])) {
    return value + 'px'; // Presumes implicit 'px' suffix for unitless numbers
  }

  return String(value).trim();
}

// 

/**
 * It's falsish not falsy because 0 is allowed.
 */
var isFalsish = function isFalsish(chunk) {
  return chunk === undefined || chunk === null || chunk === false || chunk === '';
};

var objToCssArray = function objToCssArray(obj, prevKey) {
  var rules = [];
  var keys = Object.keys(obj);

  keys.forEach(function (key) {
    if (!isFalsish(obj[key])) {
      if (isPlainObject(obj[key])) {
        rules.push.apply(rules, objToCssArray(obj[key], key));

        return rules;
      } else if (isFunction(obj[key])) {
        rules.push(hyphenateStyleName(key) + ':', obj[key], ';');

        return rules;
      }
      rules.push(hyphenateStyleName(key) + ': ' + addUnitIfNeeded(key, obj[key]) + ';');
    }
    return rules;
  });

  return prevKey ? [prevKey + ' {'].concat(rules, ['}']) : rules;
};

function flatten(chunk, executionContext, styleSheet) {
  if (Array.isArray(chunk)) {
    var ruleSet = [];

    for (var i = 0, len = chunk.length, result; i < len; i += 1) {
      result = flatten(chunk[i], executionContext, styleSheet);

      if (result === null) continue;else if (Array.isArray(result)) ruleSet.push.apply(ruleSet, result);else ruleSet.push(result);
    }

    return ruleSet;
  }

  if (isFalsish(chunk)) {
    return null;
  }

  /* Handle other components */
  if (isStyledComponent(chunk)) {
    return '.' + chunk.styledComponentId;
  }

  /* Either execute or defer the function */
  if (isFunction(chunk)) {
    if (isStatelessFunction(chunk) && executionContext) {
      var _result = chunk(executionContext);

      if ( true && Object(react_is__WEBPACK_IMPORTED_MODULE_4__["isElement"])(_result)) {
        // eslint-disable-next-line no-console
        console.warn(getComponentName(chunk) + ' is not a styled component and cannot be referred to via component selector. See https://www.styled-components.com/docs/advanced#referring-to-other-components for more details.');
      }

      return flatten(_result, executionContext, styleSheet);
    } else return chunk;
  }

  if (chunk instanceof Keyframes) {
    if (styleSheet) {
      chunk.inject(styleSheet);
      return chunk.getName();
    } else return chunk;
  }

  /* Handle objects */
  return isPlainObject(chunk) ? objToCssArray(chunk) : chunk.toString();
}

// 

function css(styles) {
  for (var _len = arguments.length, interpolations = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    interpolations[_key - 1] = arguments[_key];
  }

  if (isFunction(styles) || isPlainObject(styles)) {
    // $FlowFixMe
    return flatten(interleave(EMPTY_ARRAY, [styles].concat(interpolations)));
  }

  // $FlowFixMe
  return flatten(interleave(styles, interpolations));
}

// 

function constructWithOptions(componentConstructor, tag) {
  var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : EMPTY_OBJECT;

  if (!Object(react_is__WEBPACK_IMPORTED_MODULE_4__["isValidElementType"])(tag)) {
    throw new StyledComponentsError(1, String(tag));
  }

  /* This is callable directly as a template function */
  // $FlowFixMe: Not typed to avoid destructuring arguments
  var templateFunction = function templateFunction() {
    return componentConstructor(tag, options, css.apply(undefined, arguments));
  };

  /* If config methods are called, wrap up a new template function and merge options */
  templateFunction.withConfig = function (config) {
    return constructWithOptions(componentConstructor, tag, _extends({}, options, config));
  };

  /* Modify/inject new props at runtime */
  templateFunction.attrs = function (attrs) {
    return constructWithOptions(componentConstructor, tag, _extends({}, options, {
      attrs: Array.prototype.concat(options.attrs, attrs).filter(Boolean)
    }));
  };

  return templateFunction;
}

// 
// Source: https://github.com/garycourt/murmurhash-js/blob/master/murmurhash2_gc.js
function murmurhash(c) {
  for (var e = c.length | 0, a = e | 0, d = 0, b; e >= 4;) {
    b = c.charCodeAt(d) & 255 | (c.charCodeAt(++d) & 255) << 8 | (c.charCodeAt(++d) & 255) << 16 | (c.charCodeAt(++d) & 255) << 24, b = 1540483477 * (b & 65535) + ((1540483477 * (b >>> 16) & 65535) << 16), b ^= b >>> 24, b = 1540483477 * (b & 65535) + ((1540483477 * (b >>> 16) & 65535) << 16), a = 1540483477 * (a & 65535) + ((1540483477 * (a >>> 16) & 65535) << 16) ^ b, e -= 4, ++d;
  }
  switch (e) {
    case 3:
      a ^= (c.charCodeAt(d + 2) & 255) << 16;
    case 2:
      a ^= (c.charCodeAt(d + 1) & 255) << 8;
    case 1:
      a ^= c.charCodeAt(d) & 255, a = 1540483477 * (a & 65535) + ((1540483477 * (a >>> 16) & 65535) << 16);
  }
  a ^= a >>> 13;
  a = 1540483477 * (a & 65535) + ((1540483477 * (a >>> 16) & 65535) << 16);
  return (a ^ a >>> 15) >>> 0;
}

// 
/* eslint-disable no-bitwise */

/* This is the "capacity" of our alphabet i.e. 2x26 for all letters plus their capitalised
 * counterparts */
var charsLength = 52;

/* start at 75 for 'a' until 'z' (25) and then start at 65 for capitalised letters */
var getAlphabeticChar = function getAlphabeticChar(code) {
  return String.fromCharCode(code + (code > 25 ? 39 : 97));
};

/* input a number, usually a hash and convert it to base-52 */
function generateAlphabeticName(code) {
  var name = '';
  var x = void 0;

  /* get a char and divide by alphabet-length */
  for (x = code; x > charsLength; x = Math.floor(x / charsLength)) {
    name = getAlphabeticChar(x % charsLength) + name;
  }

  return getAlphabeticChar(x % charsLength) + name;
}

// 

function hasFunctionObjectKey(obj) {
  // eslint-disable-next-line guard-for-in, no-restricted-syntax
  for (var key in obj) {
    if (isFunction(obj[key])) {
      return true;
    }
  }

  return false;
}

function isStaticRules(rules, attrs) {
  for (var i = 0; i < rules.length; i += 1) {
    var rule = rules[i];

    // recursive case
    if (Array.isArray(rule) && !isStaticRules(rule, attrs)) {
      return false;
    } else if (isFunction(rule) && !isStyledComponent(rule)) {
      // functions are allowed to be static if they're just being
      // used to get the classname of a nested styled component
      return false;
    }
  }

  if (attrs.some(function (x) {
    return isFunction(x) || hasFunctionObjectKey(x);
  })) return false;

  return true;
}

// 

/* combines hashStr (murmurhash) and nameGenerator for convenience */
var hasher = function hasher(str) {
  return generateAlphabeticName(murmurhash(str));
};

/*
 ComponentStyle is all the CSS-specific stuff, not
 the React-specific stuff.
 */

var ComponentStyle = function () {
  function ComponentStyle(rules, attrs, componentId) {
    classCallCheck(this, ComponentStyle);

    this.rules = rules;
    this.isStatic =  false && false;
    this.componentId = componentId;

    if (!StyleSheet.master.hasId(componentId)) {
      StyleSheet.master.deferredInject(componentId, []);
    }
  }

  /*
   * Flattens a rule set into valid CSS
   * Hashes it, wraps the whole chunk in a .hash1234 {}
   * Returns the hash to be injected on render()
   * */


  ComponentStyle.prototype.generateAndInjectStyles = function generateAndInjectStyles(executionContext, styleSheet) {
    var isStatic = this.isStatic,
        componentId = this.componentId,
        lastClassName = this.lastClassName;

    if (IS_BROWSER && isStatic && typeof lastClassName === 'string' && styleSheet.hasNameForId(componentId, lastClassName)) {
      return lastClassName;
    }

    var flatCSS = flatten(this.rules, executionContext, styleSheet);
    var name = hasher(this.componentId + flatCSS.join(''));
    if (!styleSheet.hasNameForId(componentId, name)) {
      styleSheet.inject(this.componentId, stringifyRules(flatCSS, '.' + name, undefined, componentId), name);
    }

    this.lastClassName = name;
    return name;
  };

  ComponentStyle.generateName = function generateName(str) {
    return hasher(str);
  };

  return ComponentStyle;
}();

// 

var LIMIT = 200;

var createWarnTooManyClasses = (function (displayName) {
  var generatedClasses = {};
  var warningSeen = false;

  return function (className) {
    if (!warningSeen) {
      generatedClasses[className] = true;
      if (Object.keys(generatedClasses).length >= LIMIT) {
        // Unable to find latestRule in test environment.
        /* eslint-disable no-console, prefer-template */
        console.warn('Over ' + LIMIT + ' classes were generated for component ' + displayName + '. \n' + 'Consider using the attrs method, together with a style object for frequently changed styles.\n' + 'Example:\n' + '  const Component = styled.div.attrs(props => ({\n' + '    style: {\n' + '      background: props.background,\n' + '    },\n' + '  }))`width: 100%;`\n\n' + '  <Component />');
        warningSeen = true;
        generatedClasses = {};
      }
    }
  };
});

// 

var determineTheme = (function (props, fallbackTheme) {
  var defaultProps = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : EMPTY_OBJECT;

  // Props should take precedence over ThemeProvider, which should take precedence over
  // defaultProps, but React automatically puts defaultProps on props.

  /* eslint-disable react/prop-types, flowtype-errors/show-errors */
  var isDefaultTheme = defaultProps ? props.theme === defaultProps.theme : false;
  var theme = props.theme && !isDefaultTheme ? props.theme : fallbackTheme || defaultProps.theme;
  /* eslint-enable */

  return theme;
});

// 
var escapeRegex = /[[\].#*$><+~=|^:(),"'`-]+/g;
var dashesAtEnds = /(^-|-$)/g;

/**
 * TODO: Explore using CSS.escape when it becomes more available
 * in evergreen browsers.
 */
function escape(str) {
  return str
  // Replace all possible CSS selectors
  .replace(escapeRegex, '-')

  // Remove extraneous hyphens at the start and end
  .replace(dashesAtEnds, '');
}

// 

function isTag(target) {
  return typeof target === 'string' && ( true ? target.charAt(0) === target.charAt(0).toLowerCase() : undefined);
}

// 

function generateDisplayName(target) {
  // $FlowFixMe
  return isTag(target) ? 'styled.' + target : 'Styled(' + getComponentName(target) + ')';
}

var _TYPE_STATICS;

var REACT_STATICS = {
  childContextTypes: true,
  contextTypes: true,
  defaultProps: true,
  displayName: true,
  getDerivedStateFromProps: true,
  propTypes: true,
  type: true
};

var KNOWN_STATICS = {
  name: true,
  length: true,
  prototype: true,
  caller: true,
  callee: true,
  arguments: true,
  arity: true
};

var TYPE_STATICS = (_TYPE_STATICS = {}, _TYPE_STATICS[react_is__WEBPACK_IMPORTED_MODULE_4__["ForwardRef"]] = {
  $$typeof: true,
  render: true
}, _TYPE_STATICS);

var defineProperty$1 = Object.defineProperty,
    getOwnPropertyNames = Object.getOwnPropertyNames,
    _Object$getOwnPropert = Object.getOwnPropertySymbols,
    getOwnPropertySymbols = _Object$getOwnPropert === undefined ? function () {
  return [];
} : _Object$getOwnPropert,
    getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor,
    getPrototypeOf = Object.getPrototypeOf,
    objectPrototype = Object.prototype;
var arrayPrototype = Array.prototype;


function hoistNonReactStatics(targetComponent, sourceComponent, blacklist) {
  if (typeof sourceComponent !== 'string') {
    // don't hoist over string (html) components

    var inheritedComponent = getPrototypeOf(sourceComponent);

    if (inheritedComponent && inheritedComponent !== objectPrototype) {
      hoistNonReactStatics(targetComponent, inheritedComponent, blacklist);
    }

    var keys = arrayPrototype.concat(getOwnPropertyNames(sourceComponent),
    // $FlowFixMe
    getOwnPropertySymbols(sourceComponent));

    var targetStatics = TYPE_STATICS[targetComponent.$$typeof] || REACT_STATICS;

    var sourceStatics = TYPE_STATICS[sourceComponent.$$typeof] || REACT_STATICS;

    var i = keys.length;
    var descriptor = void 0;
    var key = void 0;

    // eslint-disable-next-line no-plusplus
    while (i--) {
      key = keys[i];

      if (
      // $FlowFixMe
      !KNOWN_STATICS[key] && !(blacklist && blacklist[key]) && !(sourceStatics && sourceStatics[key]) &&
      // $FlowFixMe
      !(targetStatics && targetStatics[key])) {
        descriptor = getOwnPropertyDescriptor(sourceComponent, key);

        if (descriptor) {
          try {
            // Avoid failures from read-only properties
            defineProperty$1(targetComponent, key, descriptor);
          } catch (e) {
            /* fail silently */
          }
        }
      }
    }

    return targetComponent;
  }

  return targetComponent;
}

// 
function isDerivedReactComponent(fn) {
  return !!(fn && fn.prototype && fn.prototype.isReactComponent);
}

// 
// Helper to call a given function, only once
var once = (function (cb) {
  var called = false;

  return function () {
    if (!called) {
      called = true;
      cb.apply(undefined, arguments);
    }
  };
});

// 

var ThemeContext = Object(react__WEBPACK_IMPORTED_MODULE_2__["createContext"])();

var ThemeConsumer = ThemeContext.Consumer;

/**
 * Provide a theme to an entire react component tree via context
 */

var ThemeProvider = function (_Component) {
  inherits(ThemeProvider, _Component);

  function ThemeProvider(props) {
    classCallCheck(this, ThemeProvider);

    var _this = possibleConstructorReturn(this, _Component.call(this, props));

    _this.getContext = Object(memoize_one__WEBPACK_IMPORTED_MODULE_5__["default"])(_this.getContext.bind(_this));
    _this.renderInner = _this.renderInner.bind(_this);
    return _this;
  }

  ThemeProvider.prototype.render = function render() {
    if (!this.props.children) return null;

    return react__WEBPACK_IMPORTED_MODULE_2___default.a.createElement(
      ThemeContext.Consumer,
      null,
      this.renderInner
    );
  };

  ThemeProvider.prototype.renderInner = function renderInner(outerTheme) {
    var context = this.getContext(this.props.theme, outerTheme);

    return react__WEBPACK_IMPORTED_MODULE_2___default.a.createElement(
      ThemeContext.Provider,
      { value: context },
      this.props.children
    );
  };

  /**
   * Get the theme from the props, supporting both (outerTheme) => {}
   * as well as object notation
   */


  ThemeProvider.prototype.getTheme = function getTheme(theme, outerTheme) {
    if (isFunction(theme)) {
      var mergedTheme = theme(outerTheme);

      if ( true && (mergedTheme === null || Array.isArray(mergedTheme) || (typeof mergedTheme === 'undefined' ? 'undefined' : _typeof(mergedTheme)) !== 'object')) {
        throw new StyledComponentsError(7);
      }

      return mergedTheme;
    }

    if (theme === null || Array.isArray(theme) || (typeof theme === 'undefined' ? 'undefined' : _typeof(theme)) !== 'object') {
      throw new StyledComponentsError(8);
    }

    return _extends({}, outerTheme, theme);
  };

  ThemeProvider.prototype.getContext = function getContext(theme, outerTheme) {
    return this.getTheme(theme, outerTheme);
  };

  return ThemeProvider;
}(react__WEBPACK_IMPORTED_MODULE_2__["Component"]);

// 

var CLOSING_TAG_R = /^\s*<\/[a-z]/i;

var ServerStyleSheet = function () {
  function ServerStyleSheet() {
    classCallCheck(this, ServerStyleSheet);

    /* The master sheet might be reset, so keep a reference here */
    this.masterSheet = StyleSheet.master;
    this.instance = this.masterSheet.clone();
    this.sealed = false;
  }

  /**
   * Mark the ServerStyleSheet as being fully emitted and manually GC it from the
   * StyleSheet singleton.
   */


  ServerStyleSheet.prototype.seal = function seal() {
    if (!this.sealed) {
      /* Remove sealed StyleSheets from the master sheet */
      var index = this.masterSheet.clones.indexOf(this.instance);
      this.masterSheet.clones.splice(index, 1);
      this.sealed = true;
    }
  };

  ServerStyleSheet.prototype.collectStyles = function collectStyles(children) {
    if (this.sealed) {
      throw new StyledComponentsError(2);
    }

    return react__WEBPACK_IMPORTED_MODULE_2___default.a.createElement(
      StyleSheetManager,
      { sheet: this.instance },
      children
    );
  };

  ServerStyleSheet.prototype.getStyleTags = function getStyleTags() {
    this.seal();
    return this.instance.toHTML();
  };

  ServerStyleSheet.prototype.getStyleElement = function getStyleElement() {
    this.seal();
    return this.instance.toReactElements();
  };

  ServerStyleSheet.prototype.interleaveWithNodeStream = function interleaveWithNodeStream(readableStream) {
    var _this = this;

    {
      throw new StyledComponentsError(3);
    }

    /* the tag index keeps track of which tags have already been emitted */
    var instance = this.instance;

    var instanceTagIndex = 0;

    var streamAttr = SC_STREAM_ATTR + '="true"';

    var transformer = new stream.Transform({
      transform: function appendStyleChunks(chunk, /* encoding */_, callback) {
        var tags = instance.tags;

        var html = '';

        /* retrieve html for each new style tag */
        for (; instanceTagIndex < tags.length; instanceTagIndex += 1) {
          var tag = tags[instanceTagIndex];
          html += tag.toHTML(streamAttr);
        }

        /* force our StyleSheets to emit entirely new tags */
        instance.sealAllTags();

        var renderedHtml = chunk.toString();

        /* prepend style html to chunk, unless the start of the chunk is a closing tag in which case append right after that */
        if (CLOSING_TAG_R.test(renderedHtml)) {
          var endOfClosingTag = renderedHtml.indexOf('>');

          this.push(renderedHtml.slice(0, endOfClosingTag + 1) + html + renderedHtml.slice(endOfClosingTag + 1));
        } else this.push(html + renderedHtml);

        callback();
      }
    });

    readableStream.on('end', function () {
      return _this.seal();
    });

    readableStream.on('error', function (err) {
      _this.seal();

      // forward the error to the transform stream
      transformer.emit('error', err);
    });

    return readableStream.pipe(transformer);
  };

  return ServerStyleSheet;
}();

// 

var StyleSheetContext = Object(react__WEBPACK_IMPORTED_MODULE_2__["createContext"])();
var StyleSheetConsumer = StyleSheetContext.Consumer;

var StyleSheetManager = function (_Component) {
  inherits(StyleSheetManager, _Component);

  function StyleSheetManager(props) {
    classCallCheck(this, StyleSheetManager);

    var _this = possibleConstructorReturn(this, _Component.call(this, props));

    _this.getContext = Object(memoize_one__WEBPACK_IMPORTED_MODULE_5__["default"])(_this.getContext);
    return _this;
  }

  StyleSheetManager.prototype.getContext = function getContext(sheet, target) {
    if (sheet) {
      return sheet;
    } else if (target) {
      return new StyleSheet(target);
    } else {
      throw new StyledComponentsError(4);
    }
  };

  StyleSheetManager.prototype.render = function render() {
    var _props = this.props,
        children = _props.children,
        sheet = _props.sheet,
        target = _props.target;


    return react__WEBPACK_IMPORTED_MODULE_2___default.a.createElement(
      StyleSheetContext.Provider,
      { value: this.getContext(sheet, target) },
       true ? react__WEBPACK_IMPORTED_MODULE_2___default.a.Children.only(children) : undefined
    );
  };

  return StyleSheetManager;
}(react__WEBPACK_IMPORTED_MODULE_2__["Component"]);
 true ? StyleSheetManager.propTypes = {
  sheet: prop_types__WEBPACK_IMPORTED_MODULE_6___default.a.oneOfType([prop_types__WEBPACK_IMPORTED_MODULE_6___default.a.instanceOf(StyleSheet), prop_types__WEBPACK_IMPORTED_MODULE_6___default.a.instanceOf(ServerStyleSheet)]),

  target: prop_types__WEBPACK_IMPORTED_MODULE_6___default.a.shape({
    appendChild: prop_types__WEBPACK_IMPORTED_MODULE_6___default.a.func.isRequired
  })
} : undefined;

// 

var identifiers = {};

/* We depend on components having unique IDs */
function generateId(_ComponentStyle, _displayName, parentComponentId) {
  var displayName = typeof _displayName !== 'string' ? 'sc' : escape(_displayName);

  /**
   * This ensures uniqueness if two components happen to share
   * the same displayName.
   */
  var nr = (identifiers[displayName] || 0) + 1;
  identifiers[displayName] = nr;

  var componentId = displayName + '-' + _ComponentStyle.generateName(displayName + nr);

  return parentComponentId ? parentComponentId + '-' + componentId : componentId;
}

// $FlowFixMe

var StyledComponent = function (_Component) {
  inherits(StyledComponent, _Component);

  function StyledComponent() {
    classCallCheck(this, StyledComponent);

    var _this = possibleConstructorReturn(this, _Component.call(this));

    _this.attrs = {};

    _this.renderOuter = _this.renderOuter.bind(_this);
    _this.renderInner = _this.renderInner.bind(_this);

    if (true) {
      _this.warnInnerRef = once(function (displayName) {
        return (
          // eslint-disable-next-line no-console
          console.warn('The "innerRef" API has been removed in styled-components v4 in favor of React 16 ref forwarding, use "ref" instead like a typical component. "innerRef" was detected on component "' + displayName + '".')
        );
      });

      _this.warnAttrsFnObjectKeyDeprecated = once(function (key, displayName) {
        return (
          // eslint-disable-next-line no-console
          console.warn('Functions as object-form attrs({}) keys are now deprecated and will be removed in a future version of styled-components. Switch to the new attrs(props => ({})) syntax instead for easier and more powerful composition. The attrs key in question is "' + key + '" on component "' + displayName + '".', '\n ' + new Error().stack)
        );
      });

      _this.warnNonStyledComponentAttrsObjectKey = once(function (key, displayName) {
        return (
          // eslint-disable-next-line no-console
          console.warn('It looks like you\'ve used a non styled-component as the value for the "' + key + '" prop in an object-form attrs constructor of "' + displayName + '".\n' + 'You should use the new function-form attrs constructor which avoids this issue: attrs(props => ({ yourStuff }))\n' + "To continue using the deprecated object syntax, you'll need to wrap your component prop in a function to make it available inside the styled component (you'll still get the deprecation warning though.)\n" + ('For example, { ' + key + ': () => InnerComponent } instead of { ' + key + ': InnerComponent }'))
        );
      });
    }
    return _this;
  }

  StyledComponent.prototype.render = function render() {
    return react__WEBPACK_IMPORTED_MODULE_2___default.a.createElement(
      StyleSheetConsumer,
      null,
      this.renderOuter
    );
  };

  StyledComponent.prototype.renderOuter = function renderOuter() {
    var styleSheet = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : StyleSheet.master;

    this.styleSheet = styleSheet;

    // No need to subscribe a static component to theme changes, it won't change anything
    if (this.props.forwardedComponent.componentStyle.isStatic) return this.renderInner();

    return react__WEBPACK_IMPORTED_MODULE_2___default.a.createElement(
      ThemeConsumer,
      null,
      this.renderInner
    );
  };

  StyledComponent.prototype.renderInner = function renderInner(theme) {
    var _props$forwardedCompo = this.props.forwardedComponent,
        componentStyle = _props$forwardedCompo.componentStyle,
        defaultProps = _props$forwardedCompo.defaultProps,
        displayName = _props$forwardedCompo.displayName,
        foldedComponentIds = _props$forwardedCompo.foldedComponentIds,
        styledComponentId = _props$forwardedCompo.styledComponentId,
        target = _props$forwardedCompo.target;


    var generatedClassName = void 0;
    if (componentStyle.isStatic) {
      generatedClassName = this.generateAndInjectStyles(EMPTY_OBJECT, this.props);
    } else {
      generatedClassName = this.generateAndInjectStyles(determineTheme(this.props, theme, defaultProps) || EMPTY_OBJECT, this.props);
    }

    var elementToBeCreated = this.props.as || this.attrs.as || target;
    var isTargetTag = isTag(elementToBeCreated);

    var propsForElement = {};
    var computedProps = _extends({}, this.props, this.attrs);

    var key = void 0;
    // eslint-disable-next-line guard-for-in
    for (key in computedProps) {
      if ( true && key === 'innerRef' && isTargetTag) {
        this.warnInnerRef(displayName);
      }

      if (key === 'forwardedComponent' || key === 'as') {
        continue;
      } else if (key === 'forwardedRef') propsForElement.ref = computedProps[key];else if (key === 'forwardedAs') propsForElement.as = computedProps[key];else if (!isTargetTag || Object(_emotion_is_prop_valid__WEBPACK_IMPORTED_MODULE_7__["default"])(key)) {
        // Don't pass through non HTML tags through to HTML elements
        propsForElement[key] = computedProps[key];
      }
    }

    if (this.props.style && this.attrs.style) {
      propsForElement.style = _extends({}, this.attrs.style, this.props.style);
    }

    propsForElement.className = Array.prototype.concat(foldedComponentIds, styledComponentId, generatedClassName !== styledComponentId ? generatedClassName : null, this.props.className, this.attrs.className).filter(Boolean).join(' ');

    return Object(react__WEBPACK_IMPORTED_MODULE_2__["createElement"])(elementToBeCreated, propsForElement);
  };

  StyledComponent.prototype.buildExecutionContext = function buildExecutionContext(theme, props, attrs) {
    var _this2 = this;

    var context = _extends({}, props, { theme: theme });

    if (!attrs.length) return context;

    this.attrs = {};

    attrs.forEach(function (attrDef) {
      var resolvedAttrDef = attrDef;
      var attrDefWasFn = false;
      var attr = void 0;
      var key = void 0;

      if (isFunction(resolvedAttrDef)) {
        // $FlowFixMe
        resolvedAttrDef = resolvedAttrDef(context);
        attrDefWasFn = true;
      }

      /* eslint-disable guard-for-in */
      // $FlowFixMe
      for (key in resolvedAttrDef) {
        attr = resolvedAttrDef[key];

        if (!attrDefWasFn) {
          if (isFunction(attr) && !isDerivedReactComponent(attr) && !isStyledComponent(attr)) {
            if (true) {
              _this2.warnAttrsFnObjectKeyDeprecated(key, props.forwardedComponent.displayName);
            }

            attr = attr(context);

            if ( true && react__WEBPACK_IMPORTED_MODULE_2___default.a.isValidElement(attr)) {
              _this2.warnNonStyledComponentAttrsObjectKey(key, props.forwardedComponent.displayName);
            }
          }
        }

        _this2.attrs[key] = attr;
        context[key] = attr;
      }
      /* eslint-enable */
    });

    return context;
  };

  StyledComponent.prototype.generateAndInjectStyles = function generateAndInjectStyles(theme, props) {
    var _props$forwardedCompo2 = props.forwardedComponent,
        attrs = _props$forwardedCompo2.attrs,
        componentStyle = _props$forwardedCompo2.componentStyle,
        warnTooManyClasses = _props$forwardedCompo2.warnTooManyClasses;

    // statically styled-components don't need to build an execution context object,
    // and shouldn't be increasing the number of class names

    if (componentStyle.isStatic && !attrs.length) {
      return componentStyle.generateAndInjectStyles(EMPTY_OBJECT, this.styleSheet);
    }

    var className = componentStyle.generateAndInjectStyles(this.buildExecutionContext(theme, props, attrs), this.styleSheet);

    if ( true && warnTooManyClasses) warnTooManyClasses(className);

    return className;
  };

  return StyledComponent;
}(react__WEBPACK_IMPORTED_MODULE_2__["Component"]);

function createStyledComponent(target, options, rules) {
  var isTargetStyledComp = isStyledComponent(target);
  var isClass = !isTag(target);

  var _options$displayName = options.displayName,
      displayName = _options$displayName === undefined ? generateDisplayName(target) : _options$displayName,
      _options$componentId = options.componentId,
      componentId = _options$componentId === undefined ? generateId(ComponentStyle, options.displayName, options.parentComponentId) : _options$componentId,
      _options$ParentCompon = options.ParentComponent,
      ParentComponent = _options$ParentCompon === undefined ? StyledComponent : _options$ParentCompon,
      _options$attrs = options.attrs,
      attrs = _options$attrs === undefined ? EMPTY_ARRAY : _options$attrs;


  var styledComponentId = options.displayName && options.componentId ? escape(options.displayName) + '-' + options.componentId : options.componentId || componentId;

  // fold the underlying StyledComponent attrs up (implicit extend)
  var finalAttrs =
  // $FlowFixMe
  isTargetStyledComp && target.attrs ? Array.prototype.concat(target.attrs, attrs).filter(Boolean) : attrs;

  var componentStyle = new ComponentStyle(isTargetStyledComp ? // fold the underlying StyledComponent rules up (implicit extend)
  // $FlowFixMe
  target.componentStyle.rules.concat(rules) : rules, finalAttrs, styledComponentId);

  /**
   * forwardRef creates a new interim component, which we'll take advantage of
   * instead of extending ParentComponent to create _another_ interim class
   */
  var WrappedStyledComponent = void 0;
  var forwardRef = function forwardRef(props, ref) {
    return react__WEBPACK_IMPORTED_MODULE_2___default.a.createElement(ParentComponent, _extends({}, props, { forwardedComponent: WrappedStyledComponent, forwardedRef: ref }));
  };
  forwardRef.displayName = displayName;
  WrappedStyledComponent = react__WEBPACK_IMPORTED_MODULE_2___default.a.forwardRef(forwardRef);
  WrappedStyledComponent.displayName = displayName;

  // $FlowFixMe
  WrappedStyledComponent.attrs = finalAttrs;
  // $FlowFixMe
  WrappedStyledComponent.componentStyle = componentStyle;

  // $FlowFixMe
  WrappedStyledComponent.foldedComponentIds = isTargetStyledComp ? // $FlowFixMe
  Array.prototype.concat(target.foldedComponentIds, target.styledComponentId) : EMPTY_ARRAY;

  // $FlowFixMe
  WrappedStyledComponent.styledComponentId = styledComponentId;

  // fold the underlying StyledComponent target up since we folded the styles
  // $FlowFixMe
  WrappedStyledComponent.target = isTargetStyledComp ? target.target : target;

  // $FlowFixMe
  WrappedStyledComponent.withComponent = function withComponent(tag) {
    var previousComponentId = options.componentId,
        optionsToCopy = objectWithoutProperties(options, ['componentId']);


    var newComponentId = previousComponentId && previousComponentId + '-' + (isTag(tag) ? tag : escape(getComponentName(tag)));

    var newOptions = _extends({}, optionsToCopy, {
      attrs: finalAttrs,
      componentId: newComponentId,
      ParentComponent: ParentComponent
    });

    return createStyledComponent(tag, newOptions, rules);
  };

  // $FlowFixMe
  Object.defineProperty(WrappedStyledComponent, 'defaultProps', {
    get: function get$$1() {
      return this._foldedDefaultProps;
    },
    set: function set$$1(obj) {
      // $FlowFixMe
      this._foldedDefaultProps = isTargetStyledComp ? Object(merge_anything__WEBPACK_IMPORTED_MODULE_8__["default"])(target.defaultProps, obj) : obj;
    }
  });

  if (true) {
    // $FlowFixMe
    WrappedStyledComponent.warnTooManyClasses = createWarnTooManyClasses(displayName);
  }

  // $FlowFixMe
  WrappedStyledComponent.toString = function () {
    return '.' + WrappedStyledComponent.styledComponentId;
  };

  if (isClass) {
    hoistNonReactStatics(WrappedStyledComponent, target, {
      // all SC-specific things should not be hoisted
      attrs: true,
      componentStyle: true,
      displayName: true,
      foldedComponentIds: true,
      styledComponentId: true,
      target: true,
      withComponent: true
    });
  }

  return WrappedStyledComponent;
}

// 
// Thanks to ReactDOMFactories for this handy list!

var domElements = ['a', 'abbr', 'address', 'area', 'article', 'aside', 'audio', 'b', 'base', 'bdi', 'bdo', 'big', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'cite', 'code', 'col', 'colgroup', 'data', 'datalist', 'dd', 'del', 'details', 'dfn', 'dialog', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'figcaption', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup', 'hr', 'html', 'i', 'iframe', 'img', 'input', 'ins', 'kbd', 'keygen', 'label', 'legend', 'li', 'link', 'main', 'map', 'mark', 'marquee', 'menu', 'menuitem', 'meta', 'meter', 'nav', 'noscript', 'object', 'ol', 'optgroup', 'option', 'output', 'p', 'param', 'picture', 'pre', 'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp', 'script', 'section', 'select', 'small', 'source', 'span', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'time', 'title', 'tr', 'track', 'u', 'ul', 'var', 'video', 'wbr',

// SVG
'circle', 'clipPath', 'defs', 'ellipse', 'foreignObject', 'g', 'image', 'line', 'linearGradient', 'marker', 'mask', 'path', 'pattern', 'polygon', 'polyline', 'radialGradient', 'rect', 'stop', 'svg', 'text', 'tspan'];

// 

var styled = function styled(tag) {
  return constructWithOptions(createStyledComponent, tag);
};

// Shorthands for all valid HTML Elements
domElements.forEach(function (domElement) {
  styled[domElement] = styled(domElement);
});

// 

var GlobalStyle = function () {
  function GlobalStyle(rules, componentId) {
    classCallCheck(this, GlobalStyle);

    this.rules = rules;
    this.componentId = componentId;
    this.isStatic = isStaticRules(rules, EMPTY_ARRAY);

    if (!StyleSheet.master.hasId(componentId)) {
      StyleSheet.master.deferredInject(componentId, []);
    }
  }

  GlobalStyle.prototype.createStyles = function createStyles(executionContext, styleSheet) {
    var flatCSS = flatten(this.rules, executionContext, styleSheet);
    var css = stringifyRules(flatCSS, '');

    styleSheet.inject(this.componentId, css);
  };

  GlobalStyle.prototype.removeStyles = function removeStyles(styleSheet) {
    var componentId = this.componentId;

    if (styleSheet.hasId(componentId)) {
      styleSheet.remove(componentId);
    }
  };

  // TODO: overwrite in-place instead of remove+create?


  GlobalStyle.prototype.renderStyles = function renderStyles(executionContext, styleSheet) {
    this.removeStyles(styleSheet);
    this.createStyles(executionContext, styleSheet);
  };

  return GlobalStyle;
}();

// 

// place our cache into shared context so it'll persist between HMRs
if (IS_BROWSER) {
  window.scCGSHMRCache = {};
}

function createGlobalStyle(strings) {
  for (var _len = arguments.length, interpolations = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    interpolations[_key - 1] = arguments[_key];
  }

  var rules = css.apply(undefined, [strings].concat(interpolations));
  var id = 'sc-global-' + murmurhash(JSON.stringify(rules));
  var style = new GlobalStyle(rules, id);

  var GlobalStyleComponent = function (_React$Component) {
    inherits(GlobalStyleComponent, _React$Component);

    function GlobalStyleComponent(props) {
      classCallCheck(this, GlobalStyleComponent);

      var _this = possibleConstructorReturn(this, _React$Component.call(this, props));

      var _this$constructor = _this.constructor,
          globalStyle = _this$constructor.globalStyle,
          styledComponentId = _this$constructor.styledComponentId;


      if (IS_BROWSER) {
        window.scCGSHMRCache[styledComponentId] = (window.scCGSHMRCache[styledComponentId] || 0) + 1;
      }

      /**
       * This fixes HMR compatibility. Don't ask me why, but this combination of
       * caching the closure variables via statics and then persisting the statics in
       * state works across HMR where no other combination did. ¯\_(ツ)_/¯
       */
      _this.state = {
        globalStyle: globalStyle,
        styledComponentId: styledComponentId
      };
      return _this;
    }

    GlobalStyleComponent.prototype.componentWillUnmount = function componentWillUnmount() {
      if (window.scCGSHMRCache[this.state.styledComponentId]) {
        window.scCGSHMRCache[this.state.styledComponentId] -= 1;
      }
      /**
       * Depending on the order "render" is called this can cause the styles to be lost
       * until the next render pass of the remaining instance, which may
       * not be immediate.
       */
      if (window.scCGSHMRCache[this.state.styledComponentId] === 0) {
        this.state.globalStyle.removeStyles(this.styleSheet);
      }
    };

    GlobalStyleComponent.prototype.render = function render() {
      var _this2 = this;

      if ( true && react__WEBPACK_IMPORTED_MODULE_2___default.a.Children.count(this.props.children)) {
        // eslint-disable-next-line no-console
        console.warn('The global style component ' + this.state.styledComponentId + ' was given child JSX. createGlobalStyle does not render children.');
      }

      return react__WEBPACK_IMPORTED_MODULE_2___default.a.createElement(
        StyleSheetConsumer,
        null,
        function (styleSheet) {
          _this2.styleSheet = styleSheet || StyleSheet.master;

          var globalStyle = _this2.state.globalStyle;


          if (globalStyle.isStatic) {
            globalStyle.renderStyles(STATIC_EXECUTION_CONTEXT, _this2.styleSheet);

            return null;
          } else {
            return react__WEBPACK_IMPORTED_MODULE_2___default.a.createElement(
              ThemeConsumer,
              null,
              function (theme) {
                // $FlowFixMe
                var defaultProps = _this2.constructor.defaultProps;


                var context = _extends({}, _this2.props);

                if (typeof theme !== 'undefined') {
                  context.theme = determineTheme(_this2.props, theme, defaultProps);
                }

                globalStyle.renderStyles(context, _this2.styleSheet);

                return null;
              }
            );
          }
        }
      );
    };

    return GlobalStyleComponent;
  }(react__WEBPACK_IMPORTED_MODULE_2___default.a.Component);

  GlobalStyleComponent.globalStyle = style;
  GlobalStyleComponent.styledComponentId = id;


  return GlobalStyleComponent;
}

// 

var replaceWhitespace = function replaceWhitespace(str) {
  return str.replace(/\s|\\n/g, '');
};

function keyframes(strings) {
  /* Warning if you've used keyframes on React Native */
  if ( true && typeof navigator !== 'undefined' && navigator.product === 'ReactNative') {
    // eslint-disable-next-line no-console
    console.warn('`keyframes` cannot be used on ReactNative, only on the web. To do animation in ReactNative please use Animated.');
  }

  for (var _len = arguments.length, interpolations = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    interpolations[_key - 1] = arguments[_key];
  }

  var rules = css.apply(undefined, [strings].concat(interpolations));

  var name = generateAlphabeticName(murmurhash(replaceWhitespace(JSON.stringify(rules))));

  return new Keyframes(name, stringifyRules(rules, name, '@keyframes'));
}

// 

var withTheme = (function (Component$$1) {
  var WithTheme = react__WEBPACK_IMPORTED_MODULE_2___default.a.forwardRef(function (props, ref) {
    return react__WEBPACK_IMPORTED_MODULE_2___default.a.createElement(
      ThemeConsumer,
      null,
      function (theme) {
        // $FlowFixMe
        var defaultProps = Component$$1.defaultProps;

        var themeProp = determineTheme(props, theme, defaultProps);

        if ( true && themeProp === undefined) {
          // eslint-disable-next-line no-console
          console.warn('[withTheme] You are not using a ThemeProvider nor passing a theme prop or a theme in defaultProps in component class "' + getComponentName(Component$$1) + '"');
        }

        return react__WEBPACK_IMPORTED_MODULE_2___default.a.createElement(Component$$1, _extends({}, props, { theme: themeProp, ref: ref }));
      }
    );
  });

  hoistNonReactStatics(WithTheme, Component$$1);

  WithTheme.displayName = 'WithTheme(' + getComponentName(Component$$1) + ')';

  return WithTheme;
});

// 

/* eslint-disable */
var __DO_NOT_USE_OR_YOU_WILL_BE_HAUNTED_BY_SPOOKY_GHOSTS = {
  StyleSheet: StyleSheet
};

// 

/* Warning if you've imported this file on React Native */
if ( true && typeof navigator !== 'undefined' && navigator.product === 'ReactNative') {
  // eslint-disable-next-line no-console
  console.warn("It looks like you've imported 'styled-components' on React Native.\n" + "Perhaps you're looking to import 'styled-components/native'?\n" + 'Read more about this at https://www.styled-components.com/docs/basics#react-native');
}

/* Warning if there are several instances of styled-components */
if ( true && typeof window !== 'undefined' && typeof navigator !== 'undefined' && typeof navigator.userAgent === 'string' && navigator.userAgent.indexOf('Node.js') === -1 && navigator.userAgent.indexOf('jsdom') === -1) {
  window['__styled-components-init__'] = window['__styled-components-init__'] || 0;

  if (window['__styled-components-init__'] === 1) {
    // eslint-disable-next-line no-console
    console.warn("It looks like there are several instances of 'styled-components' initialized in this application. " + 'This may cause dynamic styles not rendering properly, errors happening during rehydration process ' + 'and makes your application bigger without a good reason.\n\n' + 'See https://s-c.sh/2BAXzed for more info.');
  }

  window['__styled-components-init__'] += 1;
}

//

/* harmony default export */ __webpack_exports__["default"] = (styled);


/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../process/browser.js */ "./node_modules/process/browser.js")))

/***/ }),

/***/ "./node_modules/stylis-rule-sheet/index.js":
/*!*************************************************!*\
  !*** ./node_modules/stylis-rule-sheet/index.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

(function (factory) {
	 true ? (module['exports'] = factory()) :
		undefined
}(function () {

	'use strict'

	return function (insertRule) {
		var delimiter = '/*|*/'
		var needle = delimiter+'}'

		function toSheet (block) {
			if (block)
				try {
					insertRule(block + '}')
				} catch (e) {}
		}

		return function ruleSheet (context, content, selectors, parents, line, column, length, ns, depth, at) {
			switch (context) {
				// property
				case 1:
					// @import
					if (depth === 0 && content.charCodeAt(0) === 64)
						return insertRule(content+';'), ''
					break
				// selector
				case 2:
					if (ns === 0)
						return content + delimiter
					break
				// at-rule
				case 3:
					switch (ns) {
						// @font-face, @page
						case 102:
						case 112:
							return insertRule(selectors[0]+content), ''
						default:
							return content + (at === 0 ? delimiter : '')
					}
				case -2:
					content.split(needle).forEach(toSheet)
			}
		}
	}
}))


/***/ }),

/***/ "./node_modules/stylis/stylis.min.js":
/*!*******************************************!*\
  !*** ./node_modules/stylis/stylis.min.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

!function(e){ true?module.exports=e(null):undefined}(function e(a){"use strict";var r=/^\0+/g,c=/[\0\r\f]/g,s=/: */g,t=/zoo|gra/,i=/([,: ])(transform)/g,f=/,+\s*(?![^(]*[)])/g,n=/ +\s*(?![^(]*[)])/g,l=/ *[\0] */g,o=/,\r+?/g,h=/([\t\r\n ])*\f?&/g,u=/:global\(((?:[^\(\)\[\]]*|\[.*\]|\([^\(\)]*\))*)\)/g,d=/\W+/g,b=/@(k\w+)\s*(\S*)\s*/,p=/::(place)/g,k=/:(read-only)/g,g=/\s+(?=[{\];=:>])/g,A=/([[}=:>])\s+/g,C=/(\{[^{]+?);(?=\})/g,w=/\s{2,}/g,v=/([^\(])(:+) */g,m=/[svh]\w+-[tblr]{2}/,x=/\(\s*(.*)\s*\)/g,$=/([\s\S]*?);/g,y=/-self|flex-/g,O=/[^]*?(:[rp][el]a[\w-]+)[^]*/,j=/stretch|:\s*\w+\-(?:conte|avail)/,z=/([^-])(image-set\()/,N="-webkit-",S="-moz-",F="-ms-",W=59,q=125,B=123,D=40,E=41,G=91,H=93,I=10,J=13,K=9,L=64,M=32,P=38,Q=45,R=95,T=42,U=44,V=58,X=39,Y=34,Z=47,_=62,ee=43,ae=126,re=0,ce=12,se=11,te=107,ie=109,fe=115,ne=112,le=111,oe=105,he=99,ue=100,de=112,be=1,pe=1,ke=0,ge=1,Ae=1,Ce=1,we=0,ve=0,me=0,xe=[],$e=[],ye=0,Oe=null,je=-2,ze=-1,Ne=0,Se=1,Fe=2,We=3,qe=0,Be=1,De="",Ee="",Ge="";function He(e,a,s,t,i){for(var f,n,o=0,h=0,u=0,d=0,g=0,A=0,C=0,w=0,m=0,$=0,y=0,O=0,j=0,z=0,R=0,we=0,$e=0,Oe=0,je=0,ze=s.length,Je=ze-1,Re="",Te="",Ue="",Ve="",Xe="",Ye="";R<ze;){if(C=s.charCodeAt(R),R===Je)if(h+d+u+o!==0){if(0!==h)C=h===Z?I:Z;d=u=o=0,ze++,Je++}if(h+d+u+o===0){if(R===Je){if(we>0)Te=Te.replace(c,"");if(Te.trim().length>0){switch(C){case M:case K:case W:case J:case I:break;default:Te+=s.charAt(R)}C=W}}if(1===$e)switch(C){case B:case q:case W:case Y:case X:case D:case E:case U:$e=0;case K:case J:case I:case M:break;default:for($e=0,je=R,g=C,R--,C=W;je<ze;)switch(s.charCodeAt(je++)){case I:case J:case W:++R,C=g,je=ze;break;case V:if(we>0)++R,C=g;case B:je=ze}}switch(C){case B:for(g=(Te=Te.trim()).charCodeAt(0),y=1,je=++R;R<ze;){switch(C=s.charCodeAt(R)){case B:y++;break;case q:y--;break;case Z:switch(A=s.charCodeAt(R+1)){case T:case Z:R=Qe(A,R,Je,s)}break;case G:C++;case D:C++;case Y:case X:for(;R++<Je&&s.charCodeAt(R)!==C;);}if(0===y)break;R++}if(Ue=s.substring(je,R),g===re)g=(Te=Te.replace(r,"").trim()).charCodeAt(0);switch(g){case L:if(we>0)Te=Te.replace(c,"");switch(A=Te.charCodeAt(1)){case ue:case ie:case fe:case Q:f=a;break;default:f=xe}if(je=(Ue=He(a,f,Ue,A,i+1)).length,me>0&&0===je)je=Te.length;if(ye>0)if(f=Ie(xe,Te,Oe),n=Pe(We,Ue,f,a,pe,be,je,A,i,t),Te=f.join(""),void 0!==n)if(0===(je=(Ue=n.trim()).length))A=0,Ue="";if(je>0)switch(A){case fe:Te=Te.replace(x,Me);case ue:case ie:case Q:Ue=Te+"{"+Ue+"}";break;case te:if(Ue=(Te=Te.replace(b,"$1 $2"+(Be>0?De:"")))+"{"+Ue+"}",1===Ae||2===Ae&&Le("@"+Ue,3))Ue="@"+N+Ue+"@"+Ue;else Ue="@"+Ue;break;default:if(Ue=Te+Ue,t===de)Ve+=Ue,Ue=""}else Ue="";break;default:Ue=He(a,Ie(a,Te,Oe),Ue,t,i+1)}Xe+=Ue,O=0,$e=0,z=0,we=0,Oe=0,j=0,Te="",Ue="",C=s.charCodeAt(++R);break;case q:case W:if((je=(Te=(we>0?Te.replace(c,""):Te).trim()).length)>1){if(0===z)if((g=Te.charCodeAt(0))===Q||g>96&&g<123)je=(Te=Te.replace(" ",":")).length;if(ye>0)if(void 0!==(n=Pe(Se,Te,a,e,pe,be,Ve.length,t,i,t)))if(0===(je=(Te=n.trim()).length))Te="\0\0";switch(g=Te.charCodeAt(0),A=Te.charCodeAt(1),g){case re:break;case L:if(A===oe||A===he){Ye+=Te+s.charAt(R);break}default:if(Te.charCodeAt(je-1)===V)break;Ve+=Ke(Te,g,A,Te.charCodeAt(2))}}O=0,$e=0,z=0,we=0,Oe=0,Te="",C=s.charCodeAt(++R)}}switch(C){case J:case I:if(h+d+u+o+ve===0)switch($){case E:case X:case Y:case L:case ae:case _:case T:case ee:case Z:case Q:case V:case U:case W:case B:case q:break;default:if(z>0)$e=1}if(h===Z)h=0;else if(ge+O===0&&t!==te&&Te.length>0)we=1,Te+="\0";if(ye*qe>0)Pe(Ne,Te,a,e,pe,be,Ve.length,t,i,t);be=1,pe++;break;case W:case q:if(h+d+u+o===0){be++;break}default:switch(be++,Re=s.charAt(R),C){case K:case M:if(d+o+h===0)switch(w){case U:case V:case K:case M:Re="";break;default:if(C!==M)Re=" "}break;case re:Re="\\0";break;case ce:Re="\\f";break;case se:Re="\\v";break;case P:if(d+h+o===0&&ge>0)Oe=1,we=1,Re="\f"+Re;break;case 108:if(d+h+o+ke===0&&z>0)switch(R-z){case 2:if(w===ne&&s.charCodeAt(R-3)===V)ke=w;case 8:if(m===le)ke=m}break;case V:if(d+h+o===0)z=R;break;case U:if(h+u+d+o===0)we=1,Re+="\r";break;case Y:case X:if(0===h)d=d===C?0:0===d?C:d;break;case G:if(d+h+u===0)o++;break;case H:if(d+h+u===0)o--;break;case E:if(d+h+o===0)u--;break;case D:if(d+h+o===0){if(0===O)switch(2*w+3*m){case 533:break;default:y=0,O=1}u++}break;case L:if(h+u+d+o+z+j===0)j=1;break;case T:case Z:if(d+o+u>0)break;switch(h){case 0:switch(2*C+3*s.charCodeAt(R+1)){case 235:h=Z;break;case 220:je=R,h=T}break;case T:if(C===Z&&w===T&&je+2!==R){if(33===s.charCodeAt(je+2))Ve+=s.substring(je,R+1);Re="",h=0}}}if(0===h){if(ge+d+o+j===0&&t!==te&&C!==W)switch(C){case U:case ae:case _:case ee:case E:case D:if(0===O){switch(w){case K:case M:case I:case J:Re+="\0";break;default:Re="\0"+Re+(C===U?"":"\0")}we=1}else switch(C){case D:if(z+7===R&&108===w)z=0;O=++y;break;case E:if(0==(O=--y))we=1,Re+="\0"}break;case K:case M:switch(w){case re:case B:case q:case W:case U:case ce:case K:case M:case I:case J:break;default:if(0===O)we=1,Re+="\0"}}if(Te+=Re,C!==M&&C!==K)$=C}}m=w,w=C,R++}if(je=Ve.length,me>0)if(0===je&&0===Xe.length&&0===a[0].length==false)if(t!==ie||1===a.length&&(ge>0?Ee:Ge)===a[0])je=a.join(",").length+2;if(je>0){if(f=0===ge&&t!==te?function(e){for(var a,r,s=0,t=e.length,i=Array(t);s<t;++s){for(var f=e[s].split(l),n="",o=0,h=0,u=0,d=0,b=f.length;o<b;++o){if(0===(h=(r=f[o]).length)&&b>1)continue;if(u=n.charCodeAt(n.length-1),d=r.charCodeAt(0),a="",0!==o)switch(u){case T:case ae:case _:case ee:case M:case D:break;default:a=" "}switch(d){case P:r=a+Ee;case ae:case _:case ee:case M:case E:case D:break;case G:r=a+r+Ee;break;case V:switch(2*r.charCodeAt(1)+3*r.charCodeAt(2)){case 530:if(Ce>0){r=a+r.substring(8,h-1);break}default:if(o<1||f[o-1].length<1)r=a+Ee+r}break;case U:a="";default:if(h>1&&r.indexOf(":")>0)r=a+r.replace(v,"$1"+Ee+"$2");else r=a+r+Ee}n+=r}i[s]=n.replace(c,"").trim()}return i}(a):a,ye>0)if(void 0!==(n=Pe(Fe,Ve,f,e,pe,be,je,t,i,t))&&0===(Ve=n).length)return Ye+Ve+Xe;if(Ve=f.join(",")+"{"+Ve+"}",Ae*ke!=0){if(2===Ae&&!Le(Ve,2))ke=0;switch(ke){case le:Ve=Ve.replace(k,":"+S+"$1")+Ve;break;case ne:Ve=Ve.replace(p,"::"+N+"input-$1")+Ve.replace(p,"::"+S+"$1")+Ve.replace(p,":"+F+"input-$1")+Ve}ke=0}}return Ye+Ve+Xe}function Ie(e,a,r){var c=a.trim().split(o),s=c,t=c.length,i=e.length;switch(i){case 0:case 1:for(var f=0,n=0===i?"":e[0]+" ";f<t;++f)s[f]=Je(n,s[f],r,i).trim();break;default:f=0;var l=0;for(s=[];f<t;++f)for(var h=0;h<i;++h)s[l++]=Je(e[h]+" ",c[f],r,i).trim()}return s}function Je(e,a,r,c){var s=a,t=s.charCodeAt(0);if(t<33)t=(s=s.trim()).charCodeAt(0);switch(t){case P:switch(ge+c){case 0:case 1:if(0===e.trim().length)break;default:return s.replace(h,"$1"+e.trim())}break;case V:switch(s.charCodeAt(1)){case 103:if(Ce>0&&ge>0)return s.replace(u,"$1").replace(h,"$1"+Ge);break;default:return e.trim()+s.replace(h,"$1"+e.trim())}default:if(r*ge>0&&s.indexOf("\f")>0)return s.replace(h,(e.charCodeAt(0)===V?"":"$1")+e.trim())}return e+s}function Ke(e,a,r,c){var l,o=0,h=e+";",u=2*a+3*r+4*c;if(944===u)return function(e){var a=e.length,r=e.indexOf(":",9)+1,c=e.substring(0,r).trim(),s=e.substring(r,a-1).trim();switch(e.charCodeAt(9)*Be){case 0:break;case Q:if(110!==e.charCodeAt(10))break;default:for(var t=s.split((s="",f)),i=0,r=0,a=t.length;i<a;r=0,++i){for(var l=t[i],o=l.split(n);l=o[r];){var h=l.charCodeAt(0);if(1===Be&&(h>L&&h<90||h>96&&h<123||h===R||h===Q&&l.charCodeAt(1)!==Q))switch(isNaN(parseFloat(l))+(-1!==l.indexOf("("))){case 1:switch(l){case"infinite":case"alternate":case"backwards":case"running":case"normal":case"forwards":case"both":case"none":case"linear":case"ease":case"ease-in":case"ease-out":case"ease-in-out":case"paused":case"reverse":case"alternate-reverse":case"inherit":case"initial":case"unset":case"step-start":case"step-end":break;default:l+=De}}o[r++]=l}s+=(0===i?"":",")+o.join(" ")}}if(s=c+s+";",1===Ae||2===Ae&&Le(s,1))return N+s+s;return s}(h);else if(0===Ae||2===Ae&&!Le(h,1))return h;switch(u){case 1015:return 97===h.charCodeAt(10)?N+h+h:h;case 951:return 116===h.charCodeAt(3)?N+h+h:h;case 963:return 110===h.charCodeAt(5)?N+h+h:h;case 1009:if(100!==h.charCodeAt(4))break;case 969:case 942:return N+h+h;case 978:return N+h+S+h+h;case 1019:case 983:return N+h+S+h+F+h+h;case 883:if(h.charCodeAt(8)===Q)return N+h+h;if(h.indexOf("image-set(",11)>0)return h.replace(z,"$1"+N+"$2")+h;return h;case 932:if(h.charCodeAt(4)===Q)switch(h.charCodeAt(5)){case 103:return N+"box-"+h.replace("-grow","")+N+h+F+h.replace("grow","positive")+h;case 115:return N+h+F+h.replace("shrink","negative")+h;case 98:return N+h+F+h.replace("basis","preferred-size")+h}return N+h+F+h+h;case 964:return N+h+F+"flex-"+h+h;case 1023:if(99!==h.charCodeAt(8))break;return l=h.substring(h.indexOf(":",15)).replace("flex-","").replace("space-between","justify"),N+"box-pack"+l+N+h+F+"flex-pack"+l+h;case 1005:return t.test(h)?h.replace(s,":"+N)+h.replace(s,":"+S)+h:h;case 1e3:switch(o=(l=h.substring(13).trim()).indexOf("-")+1,l.charCodeAt(0)+l.charCodeAt(o)){case 226:l=h.replace(m,"tb");break;case 232:l=h.replace(m,"tb-rl");break;case 220:l=h.replace(m,"lr");break;default:return h}return N+h+F+l+h;case 1017:if(-1===h.indexOf("sticky",9))return h;case 975:switch(o=(h=e).length-10,u=(l=(33===h.charCodeAt(o)?h.substring(0,o):h).substring(e.indexOf(":",7)+1).trim()).charCodeAt(0)+(0|l.charCodeAt(7))){case 203:if(l.charCodeAt(8)<111)break;case 115:h=h.replace(l,N+l)+";"+h;break;case 207:case 102:h=h.replace(l,N+(u>102?"inline-":"")+"box")+";"+h.replace(l,N+l)+";"+h.replace(l,F+l+"box")+";"+h}return h+";";case 938:if(h.charCodeAt(5)===Q)switch(h.charCodeAt(6)){case 105:return l=h.replace("-items",""),N+h+N+"box-"+l+F+"flex-"+l+h;case 115:return N+h+F+"flex-item-"+h.replace(y,"")+h;default:return N+h+F+"flex-line-pack"+h.replace("align-content","").replace(y,"")+h}break;case 973:case 989:if(h.charCodeAt(3)!==Q||122===h.charCodeAt(4))break;case 931:case 953:if(true===j.test(e))if(115===(l=e.substring(e.indexOf(":")+1)).charCodeAt(0))return Ke(e.replace("stretch","fill-available"),a,r,c).replace(":fill-available",":stretch");else return h.replace(l,N+l)+h.replace(l,S+l.replace("fill-",""))+h;break;case 962:if(h=N+h+(102===h.charCodeAt(5)?F+h:"")+h,r+c===211&&105===h.charCodeAt(13)&&h.indexOf("transform",10)>0)return h.substring(0,h.indexOf(";",27)+1).replace(i,"$1"+N+"$2")+h}return h}function Le(e,a){var r=e.indexOf(1===a?":":"{"),c=e.substring(0,3!==a?r:10),s=e.substring(r+1,e.length-1);return Oe(2!==a?c:c.replace(O,"$1"),s,a)}function Me(e,a){var r=Ke(a,a.charCodeAt(0),a.charCodeAt(1),a.charCodeAt(2));return r!==a+";"?r.replace($," or ($1)").substring(4):"("+a+")"}function Pe(e,a,r,c,s,t,i,f,n,l){for(var o,h=0,u=a;h<ye;++h)switch(o=$e[h].call(Te,e,u,r,c,s,t,i,f,n,l)){case void 0:case false:case true:case null:break;default:u=o}if(u!==a)return u}function Qe(e,a,r,c){for(var s=a+1;s<r;++s)switch(c.charCodeAt(s)){case Z:if(e===T)if(c.charCodeAt(s-1)===T&&a+2!==s)return s+1;break;case I:if(e===Z)return s+1}return s}function Re(e){for(var a in e){var r=e[a];switch(a){case"keyframe":Be=0|r;break;case"global":Ce=0|r;break;case"cascade":ge=0|r;break;case"compress":we=0|r;break;case"semicolon":ve=0|r;break;case"preserve":me=0|r;break;case"prefix":if(Oe=null,!r)Ae=0;else if("function"!=typeof r)Ae=1;else Ae=2,Oe=r}}return Re}function Te(a,r){if(void 0!==this&&this.constructor===Te)return e(a);var s=a,t=s.charCodeAt(0);if(t<33)t=(s=s.trim()).charCodeAt(0);if(Be>0)De=s.replace(d,t===G?"":"-");if(t=1,1===ge)Ge=s;else Ee=s;var i,f=[Ge];if(ye>0)if(void 0!==(i=Pe(ze,r,f,f,pe,be,0,0,0,0))&&"string"==typeof i)r=i;var n=He(xe,f,r,0,0);if(ye>0)if(void 0!==(i=Pe(je,n,f,f,pe,be,n.length,0,0,0))&&"string"!=typeof(n=i))t=0;return De="",Ge="",Ee="",ke=0,pe=1,be=1,we*t==0?n:n.replace(c,"").replace(g,"").replace(A,"$1").replace(C,"$1").replace(w," ")}if(Te.use=function e(a){switch(a){case void 0:case null:ye=$e.length=0;break;default:if("function"==typeof a)$e[ye++]=a;else if("object"==typeof a)for(var r=0,c=a.length;r<c;++r)e(a[r]);else qe=0|!!a}return e},Te.set=Re,void 0!==a)Re(a);return Te});


/***/ }),

/***/ "./node_modules/to-camel-case/index.js":
/*!*********************************************!*\
  !*** ./node_modules/to-camel-case/index.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var space = __webpack_require__(/*! to-space-case */ "./node_modules/to-space-case/index.js")

/**
 * Export.
 */

module.exports = toCamelCase

/**
 * Convert a `string` to camel case.
 *
 * @param {String} string
 * @return {String}
 */

function toCamelCase(string) {
  return space(string).replace(/\s(\w)/g, function (matches, letter) {
    return letter.toUpperCase()
  })
}


/***/ }),

/***/ "./node_modules/to-no-case/index.js":
/*!******************************************!*\
  !*** ./node_modules/to-no-case/index.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {


/**
 * Export.
 */

module.exports = toNoCase

/**
 * Test whether a string is camel-case.
 */

var hasSpace = /\s/
var hasSeparator = /(_|-|\.|:)/
var hasCamel = /([a-z][A-Z]|[A-Z][a-z])/

/**
 * Remove any starting case from a `string`, like camel or snake, but keep
 * spaces and punctuation that may be important otherwise.
 *
 * @param {String} string
 * @return {String}
 */

function toNoCase(string) {
  if (hasSpace.test(string)) return string.toLowerCase()
  if (hasSeparator.test(string)) return (unseparate(string) || string).toLowerCase()
  if (hasCamel.test(string)) return uncamelize(string).toLowerCase()
  return string.toLowerCase()
}

/**
 * Separator splitter.
 */

var separatorSplitter = /[\W_]+(.|$)/g

/**
 * Un-separate a `string`.
 *
 * @param {String} string
 * @return {String}
 */

function unseparate(string) {
  return string.replace(separatorSplitter, function (m, next) {
    return next ? ' ' + next : ''
  })
}

/**
 * Camelcase splitter.
 */

var camelSplitter = /(.)([A-Z]+)/g

/**
 * Un-camelcase a `string`.
 *
 * @param {String} string
 * @return {String}
 */

function uncamelize(string) {
  return string.replace(camelSplitter, function (m, previous, uppers) {
    return previous + ' ' + uppers.toLowerCase().split('').join(' ')
  })
}


/***/ }),

/***/ "./node_modules/to-space-case/index.js":
/*!*********************************************!*\
  !*** ./node_modules/to-space-case/index.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var clean = __webpack_require__(/*! to-no-case */ "./node_modules/to-no-case/index.js")

/**
 * Export.
 */

module.exports = toSpaceCase

/**
 * Convert a `string` to space case.
 *
 * @param {String} string
 * @return {String}
 */

function toSpaceCase(string) {
  return clean(string).replace(/[\W_]+(.|$)/g, function (matches, match) {
    return match ? ' ' + match : ''
  }).trim()
}


/***/ }),

/***/ "./node_modules/webpack/buildin/global.js":
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

/***/ "./src/Edit/actions/index.js":
/*!***********************************!*\
  !*** ./src/Edit/actions/index.js ***!
  \***********************************/
/*! exports provided: setCurrentAnnotation, receiveAnalysisResults, setCurrentEntity, setEntityVisibility, toggleEntity, toggleLink, updateOccurrencesForEntity, editorSelectionChanged, toggleLinkSuccess */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setCurrentAnnotation", function() { return setCurrentAnnotation; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "receiveAnalysisResults", function() { return receiveAnalysisResults; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setCurrentEntity", function() { return setCurrentEntity; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setEntityVisibility", function() { return setEntityVisibility; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toggleEntity", function() { return toggleEntity; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toggleLink", function() { return toggleLink; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "updateOccurrencesForEntity", function() { return updateOccurrencesForEntity; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "editorSelectionChanged", function() { return editorSelectionChanged; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toggleLinkSuccess", function() { return toggleLinkSuccess; });
/* harmony import */ var _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../constants/ActionTypes */ "./src/Edit/constants/ActionTypes.js");
/* harmony import */ var redux_actions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! redux-actions */ "./node_modules/redux-actions/es/index.js");
/**
 * Actions.
 *
 * Define the list of actions for the app.
 *
 * @since 3.11.0
 */

/**
 * Internal dependencies
 */



/**
 * Set the current annotation. If `undefined` no annotation is selected.
 *
 * @since 3.11.0
 * @param {string|undefined} annotation The annotation id or `undefined` if no annotation
 *     is selected.
 * @return {Function} The action's function.
 */

const setCurrentAnnotation = annotation => ({
  type: _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__["ANNOTATION"],
  annotation
});
/**
 * The `receiveAnalysisResults` action is dispatched when new analysis results
 * are received.
 *
 * @since 3.11.0
 * @param {{entities: (Array)}} results The analysis results.
 * @return {Function} The action's function.
 */

const receiveAnalysisResults = results => ({
  type: _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__["RECEIVE_ANALYSIS_RESULTS"],
  results
});
/**
 * The `setCurrentEntity` action is dispatched in order to send an event to the
 * legacy Angular app which in turn show the provided `entity` in the inline
 * entity edit box.
 *
 * @since 3.11.0
 * @param {Object} entity The entity to display in the inline edit box.
 * @return {Function} The action's function.
 */

const setCurrentEntity = entity => ({
  type: _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__["SET_CURRENT_ENTITY"],
  entity
});
const setEntityVisibility = filter => ({
  type: _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__["SET_ENTITY_FILTER"],
  filter
});
/**
 * The `toggleEntity` action toggles the selection flag for the provided entity.
 *
 * @since 3.11.0
 * @param {Object} entity The entity being toggled.
 * @return {Function} The action's function.
 */

const toggleEntity = entity => ({
  type: _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__["TOGGLE_ENTITY"],
  entity
});
/**
 * The `toggleLink` action is dispatched to enable/disable linking an entity.
 *
 * @since 3.11.0
 * @param {Object} entity The entity.
 * @return {Function} The action's function.
 */

const toggleLink = entity => ({
  type: _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__["TOGGLE_LINK"],
  entity
});
/**
 * The `updateOccurrencesForEntity` action is dispatched when the number of
 * occurrences for an entity is updated (by the legacy Angular application).
 *
 * @since 3.11.0
 *
 * @param {string} entityId The entity unique id.
 * @param {Array} occurrences An array of occurrences.
 * @return {Function} The action's function.
 */

const updateOccurrencesForEntity = (entityId, occurrences) => ({
  type: _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__["UPDATE_OCCURRENCES_FOR_ENTITY"],
  entityId,
  occurrences
});
/**
 * The `editorSelectionChanged` action is dispatched when the editor text selection
 * changes.
 *
 * @since 3.18.4
 * @param {{selection: string, editor: *, source: ("tinymce"|"block-editor")}} payload The editor text selection.
 * @returns {{type: string, selection: *}}
 */

const editorSelectionChanged = payload => ({
  type: _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__["EDITOR_SELECTION_CHANGED"],
  payload
});
const toggleLinkSuccess = Object(redux_actions__WEBPACK_IMPORTED_MODULE_1__["createAction"])(_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__["TOGGLE_LINK_SUCCESS"]);

/***/ }),

/***/ "./src/Edit/angular/EditPostWidgetController.js":
/*!******************************************************!*\
  !*** ./src/Edit/angular/EditPostWidgetController.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/*global angular jQuery*/

/**
 * Angular Controllers: Edit Post Widget Controller.
 *
 * This adapter provides a bridge to the legacy AngularJS app.
 *
 * @since 3.11.0
 */
// Cache the instance.
let scope;
/**
 * Get the `EditPostWidgetController`'s scope.
 *
 * @since 3.11.0
 * @returns {{onSelectedEntityTile: (function)}} The `EditPostWidgetController`
 *     instance.
 * @constructor
 */

function EditPostWidgetController() {
  // Return the cached instance or get the instance and cache it.
  return scope ? scope : scope = angular.element(jQuery('[ng-controller="EditPostWidgetController"]')).scope();
} // Finally export the function.


/* harmony default export */ __webpack_exports__["default"] = (EditPostWidgetController);

/***/ }),

/***/ "./src/Edit/components/Accordion/Lead.js":
/*!***********************************************!*\
  !*** ./src/Edit/components/Accordion/Lead.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");

const Lead = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
    ${props => props.open && styled_components__WEBPACK_IMPORTED_MODULE_0__["css"]`
        border-top : 0 !important;
        border-bottom: 5px solid #007aff;
    `}
`;
/* harmony default export */ __webpack_exports__["default"] = (Lead);

/***/ }),

/***/ "./src/Edit/components/Accordion/index.js":
/*!************************************************!*\
  !*** ./src/Edit/components/Accordion/index.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Lead__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Lead */ "./src/Edit/components/Accordion/Lead.js");
/**
 * External dependencies
 */



class Accordion extends react__WEBPACK_IMPORTED_MODULE_0___default.a.Component {
  constructor(props) {
    super(props);
    this.state = {
      open: props.open
    };
    this.switch = this.switch.bind(this);
  }

  switch() {
    this.setState(prevState => ({
      open: !prevState.open
    }));
  }

  render() {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
      className: "wl-tab-lead",
      onClick: this.switch
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
      className: "wl-tab-lead-wrap"
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("h1", {
      className: "wl-tab-lead-text"
    }, this.props.label), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Lead__WEBPACK_IMPORTED_MODULE_1__["default"], {
      className: "wl-tab-lead-text wl-tab-lead-btn",
      open: this.state.open
    }))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
      className: "wl-tab-wrap",
      style: {
        display: this.state.open ? 'block' : 'none'
      }
    }, this.props.children));
  }

}

/* harmony default export */ __webpack_exports__["default"] = (Accordion);

/***/ }),

/***/ "./src/Edit/components/AddEntity/ButtonContainer.js":
/*!**********************************************************!*\
  !*** ./src/Edit/components/AddEntity/ButtonContainer.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./actions */ "./src/Edit/components/AddEntity/actions.js");
/* harmony import */ var _Button__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../Button */ "./src/Edit/components/Button/index.js");




const mapStateToProps = ({
  enabled,
  value
}) => ({
  enabled,
  label: `Add ${value}`
});

const mapDispatchToProps = dispatch => ({
  onClick: () => dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_1__["open"])())
});

/* harmony default export */ __webpack_exports__["default"] = (Object(react_redux__WEBPACK_IMPORTED_MODULE_0__["connect"])(mapStateToProps, mapDispatchToProps)(_Button__WEBPACK_IMPORTED_MODULE_2__["default"]));

/***/ }),

/***/ "./src/Edit/components/AddEntity/EntitySelect.js":
/*!*******************************************************!*\
  !*** ./src/Edit/components/AddEntity/EntitySelect.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Select__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../Select */ "./src/Edit/components/Select/index.js");
/* harmony import */ var _Item_CreateItem__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Item/CreateItem */ "./src/Edit/components/AddEntity/Item/CreateItem.js");
/* harmony import */ var _Item_SelectItem__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./Item/SelectItem */ "./src/Edit/components/AddEntity/Item/SelectItem.js");
function _extends() { _extends = Object.assign ? Object.assign.bind() : function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }






const EntitySelect = ({
  createEntity,
  selectEntity,
  items,
  value,
  showCreate,
  ...props
}) => {
  const elements = (showCreate ? [/*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Item_CreateItem__WEBPACK_IMPORTED_MODULE_2__["default"], {
    key: 1000,
    label: value,
    onClick: () => createEntity(value)
  })] : []).concat(items.map((item, index) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Item_SelectItem__WEBPACK_IMPORTED_MODULE_3__["default"], {
    key: index,
    item: item,
    onClick: () => selectEntity(item)
  })));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Select__WEBPACK_IMPORTED_MODULE_1__["default"], _extends({
    value: value
  }, props), elements);
};

/* harmony default export */ __webpack_exports__["default"] = (EntitySelect);

/***/ }),

/***/ "./src/Edit/components/AddEntity/EntitySelectContainer.js":
/*!****************************************************************!*\
  !*** ./src/Edit/components/AddEntity/EntitySelectContainer.js ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var _EntitySelect__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./EntitySelect */ "./src/Edit/components/AddEntity/EntitySelect.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./actions */ "./src/Edit/components/AddEntity/actions.js");




const mapStateToProps = ({
  open,
  value,
  items
}, ownProps) => ({
  open,
  value,
  items,
  ...ownProps
});

const mapDispatchToProps = dispatch => ({
  onInputChange: value => dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_2__["setValue"])(value)),
  onCancel: () => dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_2__["close"])())
});

/* harmony default export */ __webpack_exports__["default"] = (Object(react_redux__WEBPACK_IMPORTED_MODULE_0__["connect"])(mapStateToProps, mapDispatchToProps)(_EntitySelect__WEBPACK_IMPORTED_MODULE_1__["default"]));

/***/ }),

/***/ "./src/Edit/components/AddEntity/Item/CreateItem.js":
/*!**********************************************************!*\
  !*** ./src/Edit/components/AddEntity/Item/CreateItem.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Wrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Wrapper */ "./src/Edit/components/AddEntity/Item/Wrapper.js");
/* harmony import */ var _Label__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Label */ "./src/Edit/components/AddEntity/Item/Label.js");




const CreateItem = ({
  label,
  onClick
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Wrapper__WEBPACK_IMPORTED_MODULE_1__["default"], {
  onClick: onClick
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Label__WEBPACK_IMPORTED_MODULE_2__["default"], null, "Create ", label, "..."));

/* harmony default export */ __webpack_exports__["default"] = (CreateItem);

/***/ }),

/***/ "./src/Edit/components/AddEntity/Item/Description.js":
/*!***********************************************************!*\
  !*** ./src/Edit/components/AddEntity/Item/Description.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");

const Description = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
  // Same as the Cloud Icon.
  color: #cbcbcb;
  font-size: 10px;
  width: calc(100% - 10px);
  text-overflow: ellipsis;
  overflow: hidden;
  line-height: 12px;
`;
/* harmony default export */ __webpack_exports__["default"] = (Description);

/***/ }),

/***/ "./src/Edit/components/AddEntity/Item/DisplayTypes.js":
/*!************************************************************!*\
  !*** ./src/Edit/components/AddEntity/Item/DisplayTypes.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");

const DisplayTypes = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
  // Same as the Cloud Icon.
  color: #cbcbcb;
  font-size: 10px;
  width: calc(100% - 10px);
  text-overflow: ellipsis;
  overflow: hidden;
  line-height: 12px;
`;
/* harmony default export */ __webpack_exports__["default"] = (DisplayTypes);

/***/ }),

/***/ "./src/Edit/components/AddEntity/Item/Label.js":
/*!*****************************************************!*\
  !*** ./src/Edit/components/AddEntity/Item/Label.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");

const Label = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
  display: inline-block;
  position: relative;
  box-sizing: border-box;
  max-width: 180px;
  text-overflow: ellipsis;
  overflow: hidden;
  margin-top: 4px;
  min-height: 16px;
  line-height: 16px;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
    Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
  font-weight: 600;
  font-size: 12px;
  -moz-user-select: none;
  hyphens: auto;
`;
/* harmony default export */ __webpack_exports__["default"] = (Label);

/***/ }),

/***/ "./src/Edit/components/AddEntity/Item/SelectItem.js":
/*!**********************************************************!*\
  !*** ./src/Edit/components/AddEntity/Item/SelectItem.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Wrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Wrapper */ "./src/Edit/components/AddEntity/Item/Wrapper.js");
/* harmony import */ var _Label__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Label */ "./src/Edit/components/AddEntity/Item/Label.js");
/* harmony import */ var _EntityTile_Cloud__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../EntityTile/Cloud */ "./src/Edit/components/EntityTile/Cloud.js");
/* harmony import */ var _Description__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./Description */ "./src/Edit/components/AddEntity/Item/Description.js");
/* harmony import */ var _DisplayTypes__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./DisplayTypes */ "./src/Edit/components/AddEntity/Item/DisplayTypes.js");






/**
 * The `local` attribute is handled as such:
 * `"local" === item.scope ? 1 : 0`
 *
 * See here for more information:
 * https://github.com/styled-components/styled-components/issues/1198
 *
 * @param item
 * @param key
 * @param props
 * @returns {*}
 * @constructor
 */

const SelectItem = ({
  item,
  ...props
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Wrapper__WEBPACK_IMPORTED_MODULE_1__["default"], props, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Label__WEBPACK_IMPORTED_MODULE_2__["default"], {
  title: item.label
}, item.label), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_EntityTile_Cloud__WEBPACK_IMPORTED_MODULE_3__["default"], {
  className: "fa fa-cloud",
  local: "local" === item.scope ? 1 : 0
}), 0 < item.descriptions.length && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Description__WEBPACK_IMPORTED_MODULE_4__["default"], {
  title: item.descriptions[0]
}, item.descriptions[0]), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_DisplayTypes__WEBPACK_IMPORTED_MODULE_5__["default"], null, item.displayTypes));

/* harmony default export */ __webpack_exports__["default"] = (SelectItem);

/***/ }),

/***/ "./src/Edit/components/AddEntity/Item/Wrapper.js":
/*!*******************************************************!*\
  !*** ./src/Edit/components/AddEntity/Item/Wrapper.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");

const Wrapper = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
  padding: 4px 8px;
  box-sizing: border-box;
  position: relative;
  margin: 0 auto;
  min-height: 32px;
  color: rgb(102, 102, 102);
`;
/* harmony default export */ __webpack_exports__["default"] = (Wrapper);

/***/ }),

/***/ "./src/Edit/components/AddEntity/Wrapper.js":
/*!**************************************************!*\
  !*** ./src/Edit/components/AddEntity/Wrapper.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");


const VerticalContainer = styled_components__WEBPACK_IMPORTED_MODULE_1__["default"].div`
  position: relative;
  overflow-y: visible;
  width: calc(100% + 6px);
  margin-top: 10px;
  margin-bottom: 8px;
  margin-left: -3px;
`;
const HorizontalContainer = styled_components__WEBPACK_IMPORTED_MODULE_1__["default"].div`
  white-space: nowrap;
  overflow-x: hidden;

  > * {
    display: inline-block;
    box-sizing: border-box;

    &:first-child {
      // Slightly smaller to accommodate the hover effect.
      width: calc(100% - 2px);
      margin: 0 1px;

      margin-left: ${props => props.open ? "-100%" : "1px"};
      transition: all 200ms ease-out;
    }

    &:last-child {
      width: 100%;
    }
  }
`;

const Wrapper = ({
  children,
  ...props
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(VerticalContainer, props, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(HorizontalContainer, props, children));

/* harmony default export */ __webpack_exports__["default"] = (Wrapper);

/***/ }),

/***/ "./src/Edit/components/AddEntity/WrapperContainer.js":
/*!***********************************************************!*\
  !*** ./src/Edit/components/AddEntity/WrapperContainer.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var _Wrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Wrapper */ "./src/Edit/components/AddEntity/Wrapper.js");



const mapStateToProps = state => ({
  open: state.open
});

/* harmony default export */ __webpack_exports__["default"] = (Object(react_redux__WEBPACK_IMPORTED_MODULE_0__["connect"])(mapStateToProps)(_Wrapper__WEBPACK_IMPORTED_MODULE_1__["default"]));

/***/ }),

/***/ "./src/Edit/components/AddEntity/actions.js":
/*!**************************************************!*\
  !*** ./src/Edit/components/AddEntity/actions.js ***!
  \**************************************************/
/*! exports provided: loadItemsRequest, loadItemsSuccess, loadItemsError, createEntityRequest, createEntitySuccess, addEntityRequest, addEntitySuccess, close, open, setValue, createEntityForm, reducer */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "loadItemsRequest", function() { return loadItemsRequest; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "loadItemsSuccess", function() { return loadItemsSuccess; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "loadItemsError", function() { return loadItemsError; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createEntityRequest", function() { return createEntityRequest; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createEntitySuccess", function() { return createEntitySuccess; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "addEntityRequest", function() { return addEntityRequest; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "addEntitySuccess", function() { return addEntitySuccess; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "close", function() { return close; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "open", function() { return open; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setValue", function() { return setValue; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createEntityForm", function() { return createEntityForm; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "reducer", function() { return reducer; });
/* harmony import */ var redux_actions__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux-actions */ "./node_modules/redux-actions/es/index.js");
/**
 * External dependencies
 */

const {
  loadItemsRequest,
  loadItemsSuccess,
  loadItemsError,
  createEntityRequest,
  createEntitySuccess,
  addEntityRequest,
  addEntitySuccess,
  close,
  open,
  setValue,
  createEntityForm
} = Object(redux_actions__WEBPACK_IMPORTED_MODULE_0__["createActions"])("LOAD_ITEMS_REQUEST", "LOAD_ITEMS_SUCCESS", "LOAD_ITEMS_ERROR", "CREATE_ENTITY_REQUEST", "CREATE_ENTITY_SUCCESS", "ADD_ENTITY_REQUEST", "ADD_ENTITY_SUCCESS", "CLOSE", "OPEN", "SET_VALUE", "CREATE_ENTITY_FORM");
const reducer = Object(redux_actions__WEBPACK_IMPORTED_MODULE_0__["handleActions"])({
  [loadItemsSuccess]: (state, action) => ({ ...state,
    items: action.payload
  }),
  [close]: state => ({ ...state,
    open: false
  }),
  [open]: state => ({ ...state,
    open: state.enabled
  }),
  [setValue]: (state, action) => ({ ...state,
    value: action.payload,
    enabled: "undefined" !== typeof action.payload && "" !== action.payload
  })
}, {
  open: false,
  items: [],
  value: "",
  enabled: false
});

/***/ }),

/***/ "./src/Edit/components/AddEntity/api.js":
/*!**********************************************!*\
  !*** ./src/Edit/components/AddEntity/api.js ***!
  \**********************************************/
/*! exports provided: autocomplete */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "autocomplete", function() { return autocomplete; });
const settings = window["wlSettings"];
function autocomplete(query, language, ...excludes) {
  // eslint-disable-next-line
  if ("undefined" !== wp.ajax) {
    // eslint-disable-next-line
    return wp.ajax.post("wl_autocomplete", {
      query,
      // show local entities on post edit screen.
      show_local_entities: true,
      // eslint-disable-next-line
      _wpnonce: settings["wl_autocomplete_nonce"],
      // eslint-disable-next-line
      exclude: settings["itemId"],
      // eslint-disable-next-line
      scope: settings["autocomplete_scope"]
    });
  } else {
    const url = new URL("http://localhost:8080/wordlift-api/autocomplete");
    url.searchParams.append("query", query);
    url.searchParams.append("language", language);
    if (0 === excludes.length) url.searchParams.append("exclude", "");else excludes.forEach(value => url.searchParams.append("exclude", value));
    return fetch(url).then(response => response.json());
  }
}

/***/ }),

/***/ "./src/Edit/components/AddEntity/index.js":
/*!************************************************!*\
  !*** ./src/Edit/components/AddEntity/index.js ***!
  \************************************************/
/*! exports provided: getSelection, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getSelection", function() { return getSelection; });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var redux__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! redux */ "./node_modules/redux/es/redux.js");
/* harmony import */ var redux_saga__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! redux-saga */ "./node_modules/redux-saga/dist/redux-saga-core-npm-proxy.esm.js");
/* harmony import */ var redux_logger__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! redux-logger */ "./node_modules/redux-logger/dist/redux-logger.js");
/* harmony import */ var redux_logger__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(redux_logger__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! prop-types */ "./node_modules/prop-types/index.js");
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(prop_types__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var backbone__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! backbone */ "backbone");
/* harmony import */ var backbone__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(backbone__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _ButtonContainer__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./ButtonContainer */ "./src/Edit/components/AddEntity/ButtonContainer.js");
/* harmony import */ var _EntitySelectContainer__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./EntitySelectContainer */ "./src/Edit/components/AddEntity/EntitySelectContainer.js");
/* harmony import */ var _WrapperContainer__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./WrapperContainer */ "./src/Edit/components/AddEntity/WrapperContainer.js");
/* harmony import */ var _Arrow__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../Arrow */ "./src/Edit/components/Arrow/index.js");
/* harmony import */ var _sagas__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./sagas */ "./src/Edit/components/AddEntity/sagas.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./actions */ "./src/Edit/components/AddEntity/actions.js");
/* harmony import */ var _common_constants__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../../../common/constants */ "./src/common/constants.js");
/**
 * External dependencies
 */







/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */







 // Create the saga middleware.

const sagaMiddleware = Object(redux_saga__WEBPACK_IMPORTED_MODULE_3__["default"])();
const store = Object(redux__WEBPACK_IMPORTED_MODULE_2__["createStore"])(_actions__WEBPACK_IMPORTED_MODULE_13__["reducer"], Object(redux__WEBPACK_IMPORTED_MODULE_2__["applyMiddleware"])(sagaMiddleware, redux_logger__WEBPACK_IMPORTED_MODULE_4___default.a)); // Selector to retrieve the previous selection.

function getSelection() {
  return store.getState().value;
} // Run the saga.

sagaMiddleware.run(_sagas__WEBPACK_IMPORTED_MODULE_12__["default"]); // Receives events that the selection has changed in the editor.

Object(backbone__WEBPACK_IMPORTED_MODULE_6__["on"])(_common_constants__WEBPACK_IMPORTED_MODULE_14__["SELECTION_CHANGED"], ({
  selection
}) => store.dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_13__["setValue"])(selection))); // Receives events that an entity has been successfully added.
//
// These events are raised from the Block Editor (block-editor/stores/sagas) and are required to update the AddEntity
// state.

Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_7__["addAction"])("wordlift.addEntitySuccess", "wordlift", () => store.dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_13__["addEntitySuccess"])())); // Temporary hack to allow 3rd parties to close the Entity Select.

Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_7__["addAction"])("unstable_wordlift.closeEntitySelect", "wordlift", () => store.dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_13__["close"])()));

const AddEntity = ({
  createEntity,
  selectEntity,
  showCreate
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(react_redux__WEBPACK_IMPORTED_MODULE_1__["Provider"], {
    store: store
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(react__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, // Allow 3rd parties to hook and add additional components.
  Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_7__["applyFilters"])("wordlift.AddEntity.beforeWrapperContainer", []), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_WrapperContainer__WEBPACK_IMPORTED_MODULE_10__["default"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_ButtonContainer__WEBPACK_IMPORTED_MODULE_8__["default"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Arrow__WEBPACK_IMPORTED_MODULE_11__["default"], {
    height: "8px",
    color: "white"
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_EntitySelectContainer__WEBPACK_IMPORTED_MODULE_9__["default"], {
    createEntity: createEntity,
    selectEntity: selectEntity,
    showCreate: showCreate
  })), // Allow 3rd parties to hook and add additional components.
  Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_7__["applyFilters"])("wordlift.AddEntity.afterWrapperContainer", [])));
};

AddEntity.propTypes = {
  selectEntity: prop_types__WEBPACK_IMPORTED_MODULE_5___default.a.func.isRequired,
  showCreate: prop_types__WEBPACK_IMPORTED_MODULE_5___default.a.bool.isRequired
};
/* harmony default export */ __webpack_exports__["default"] = (AddEntity);

/***/ }),

/***/ "./src/Edit/components/AddEntity/sagas.js":
/*!************************************************!*\
  !*** ./src/Edit/components/AddEntity/sagas.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var redux_saga__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux-saga */ "./node_modules/redux-saga/dist/redux-saga-core-npm-proxy.esm.js");
/* harmony import */ var redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! redux-saga/effects */ "./node_modules/redux-saga/dist/redux-saga-effects-npm-proxy.esm.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./actions */ "./src/Edit/components/AddEntity/actions.js");
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./api */ "./src/Edit/components/AddEntity/api.js");
/* harmony import */ var _angular_EditPostWidgetController__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../angular/EditPostWidgetController */ "./src/Edit/angular/EditPostWidgetController.js");
/* global wp */






function* loadItems({
  payload
}) {
  if ("undefined" === typeof payload || "" === payload) return;
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["delay"])(500); // eslint-disable-next-line

  "undefined" !== typeof wp.wordlift && wp.wordlift.trigger("loading", true);
  const language = // eslint-disable-next-line
  "undefined" !== typeof wlSettings.language ? wlSettings.language : "en";
  const items = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["call"])(_api__WEBPACK_IMPORTED_MODULE_3__["autocomplete"], payload, language);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["put"])(Object(_actions__WEBPACK_IMPORTED_MODULE_2__["loadItemsSuccess"])(items)); // eslint-disable-next-line

  "undefined" !== typeof wp.wordlift && wp.wordlift.trigger("loading", false);
}

function* requestClose() {
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["put"])(Object(_actions__WEBPACK_IMPORTED_MODULE_2__["close"])());
}

function editorSelectionChangedChannel() {
  return Object(redux_saga__WEBPACK_IMPORTED_MODULE_0__["eventChannel"])(emitter => {
    const hook = params => emitter(params); // eslint-disable-next-line


    wp.wordlift.on("editorSelectionChanged", hook); // eslint-disable-next-line

    return () => wp.wordlift.off("editorSelectionChanged", hook);
  });
}

function* watchForEditorSelectionChanges() {
  const channel = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["call"])(editorSelectionChangedChannel);

  while (true) {
    const {
      selection
    } = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["take"])(channel);
    yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["put"])(Object(_actions__WEBPACK_IMPORTED_MODULE_2__["setValue"])(selection));
    if ("" === selection) yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["put"])(Object(_actions__WEBPACK_IMPORTED_MODULE_2__["close"])());
  }
}

function* watchForSetValue() {
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["takeLatest"])(_actions__WEBPACK_IMPORTED_MODULE_2__["setValue"], loadItems);
}

function* saga() {
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["fork"])(watchForEditorSelectionChanges);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["takeEvery"])(_actions__WEBPACK_IMPORTED_MODULE_2__["createEntitySuccess"], requestClose);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["takeEvery"])(_actions__WEBPACK_IMPORTED_MODULE_2__["addEntitySuccess"], requestClose); // Watch for setValue only after an open.

  while (true) {
    yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["take"])(_actions__WEBPACK_IMPORTED_MODULE_2__["open"]);
    yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["call"])(loadItems, yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["select"])(state => ({
      payload: state.value
    })));
    yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["race"])({
      task: Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["call"])(watchForSetValue),
      cancel: Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_1__["take"])(_actions__WEBPACK_IMPORTED_MODULE_2__["close"])
    });
  }
}

/* harmony default export */ __webpack_exports__["default"] = (saga);

/***/ }),

/***/ "./src/Edit/components/App/Wrapper.js":
/*!********************************************!*\
  !*** ./src/Edit/components/App/Wrapper.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Wrapper.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Wrapper = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
	background-color: #fff;

	// Compensate accordion margin bottom.
	position: relative;

	// Size.
	max-width: 254px;

	// Fixing position in responsive.
	margin: auto;
	margin-bottom: 8px;
`; // Finally export the `Wrapper`.

/* harmony default export */ __webpack_exports__["default"] = (Wrapper);

/***/ }),

/***/ "./src/Edit/components/App/index.js":
/*!******************************************!*\
  !*** ./src/Edit/components/App/index.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var _Wrapper__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Wrapper */ "./src/Edit/components/App/Wrapper.js");
/* harmony import */ var _Header__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../Header */ "./src/Edit/components/Header/index.js");
/* harmony import */ var _containers_VisibleEntityList__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../containers/VisibleEntityList */ "./src/Edit/containers/VisibleEntityList.js");
/* harmony import */ var _Accordion__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../Accordion */ "./src/Edit/components/Accordion/index.js");
/* harmony import */ var _components_AddEntity__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../components/AddEntity */ "./src/Edit/components/AddEntity/index.js");
/* harmony import */ var _AddEntity_actions__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../AddEntity/actions */ "./src/Edit/components/AddEntity/actions.js");
/**
 * Applications: Classification Box.
 *
 * This is the main entry point for the Classification Box application published
 * from the `index.js` file inside a Redux `Provider`.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */







const wlSettings = global["wlSettings"];
const canCreateEntities = "undefined" !== wlSettings["can_create_entities"] && "yes" === wlSettings["can_create_entities"];
/**
 * Define the {@link App}.
 *
 * @since 3.11.0
 * @return {Function} The `render` function.
 */

const App = ({
  addEntityRequest,
  createEntityRequest
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Wrapper__WEBPACK_IMPORTED_MODULE_2__["default"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_components_AddEntity__WEBPACK_IMPORTED_MODULE_6__["default"], {
  createEntity: createEntityRequest,
  showCreate: canCreateEntities,
  selectEntity: addEntityRequest
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Accordion__WEBPACK_IMPORTED_MODULE_5__["default"], {
  open: true,
  label: "Content classification"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Header__WEBPACK_IMPORTED_MODULE_3__["default"], null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_containers_VisibleEntityList__WEBPACK_IMPORTED_MODULE_4__["default"], null))); // Finally export the `App`.


/* harmony default export */ __webpack_exports__["default"] = (Object(react_redux__WEBPACK_IMPORTED_MODULE_1__["connect"])(null, {
  addEntityRequest: _AddEntity_actions__WEBPACK_IMPORTED_MODULE_7__["addEntityRequest"],
  createEntityRequest: _AddEntity_actions__WEBPACK_IMPORTED_MODULE_7__["createEntityRequest"]
})(App));
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./src/Edit/components/Arrow/index.js":
/*!********************************************!*\
  !*** ./src/Edit/components/Arrow/index.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");

const Arrow = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
  width: 0;
  height: 0;

  ${props => styled_components__WEBPACK_IMPORTED_MODULE_0__["css"]`
    border-top: ${props.height} solid transparent;
    border-bottom: ${props.height} solid transparent;
    border-left: ${props.height} solid ${props.color};
  `};
`;
/* harmony default export */ __webpack_exports__["default"] = (Arrow);

/***/ }),

/***/ "./src/Edit/components/ArrowToggle/Arrow.js":
/*!**************************************************!*\
  !*** ./src/Edit/components/ArrowToggle/Arrow.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Arrow.
 *
 * The `Arrow` component is an arrow on the tile right side which opens/closes
 * the Drawer when clicked.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Arrow = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
  box-sizing: border-box;
	display: block;
	position: absolute;
	top: 8px;
	width: 8px;
	height: 8px;
	border-top: 8px solid transparent;
	border-bottom: 8px solid transparent;
	border-left: 8px solid #7d7d7d;
	border-radius: 16px;
	transition: transform 150ms ease;
	transform: rotate( ${props => props.open ? 180 : 0}deg );
	&:hover {
		border-left-color: #fccd34;
	} 
`; // Finally export the `Arrow.

/* harmony default export */ __webpack_exports__["default"] = (Arrow);

/***/ }),

/***/ "./src/Edit/components/ArrowToggle/Wrapper.js":
/*!****************************************************!*\
  !*** ./src/Edit/components/ArrowToggle/Wrapper.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Wrapper component.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Wrapper = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
	cursor: pointer;
	transition: opacity 150ms ease;
	position: absolute;
	right: 0;
	top: 0;
	bottom: 0;
	box-sizing: border-box;
	width: 16px;
	min-height: 32px;
	padding: 8px 4px;
	background-color: #f1f1f1;
	
	// Control the visibility of the element according to the 'show' property.
	display: ${props => props.show ? 'block' : 'none'};
	opacity: ${props => props.show ? 1 : 0}
`; // Finally export the `Wrapper`.

/* harmony default export */ __webpack_exports__["default"] = (Wrapper);

/***/ }),

/***/ "./src/Edit/components/ArrowToggle/index.js":
/*!**************************************************!*\
  !*** ./src/Edit/components/ArrowToggle/index.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Wrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Wrapper */ "./src/Edit/components/ArrowToggle/Wrapper.js");
/* harmony import */ var _Arrow__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Arrow */ "./src/Edit/components/ArrowToggle/Arrow.js");
/**
 * Components: Arrow Toggle component.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */



/**
 * @inheritDoc
 */

class ArrowToggle extends react__WEBPACK_IMPORTED_MODULE_0___default.a.PureComponent {
  /**
   * @inheritDoc
   */
  constructor(props) {
    super(props); // Bind this.

    this.onClick = this.onClick.bind(this);
  }
  /**
   * Handle clicks and forward them to the parent handler.
   *
   * @since 3.11.0
   *
   * @param {Event} e The source {@link Event}.
   */


  onClick(e) {
    e.preventDefault(); // Forward the click.

    this.props.onClick(e);
  }
  /**
   * @inheritDoc
   */


  render() {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Wrapper__WEBPACK_IMPORTED_MODULE_1__["default"], {
      onClick: this.onClick,
      show: this.props.show
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Arrow__WEBPACK_IMPORTED_MODULE_2__["default"], {
      open: this.props.open
    }));
  }

} // Finally export the `ArrowToggle`.


/* harmony default export */ __webpack_exports__["default"] = (ArrowToggle);

/***/ }),

/***/ "./src/Edit/components/Button/Wrapper.js":
/*!***********************************************!*\
  !*** ./src/Edit/components/Button/Wrapper.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");

const Wrapper = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
    white-space: initial;
    line-height: 16px;
    color: white;
    padding: 11px 8px 6px;
    min-height: 40px;
    margin-bottom: 10px;
    font-family: BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
        
    > div {
        display: inline-block;
        
        &:last-child {
            float: right;
        }
    }

    ${props => props.enabled && styled_components__WEBPACK_IMPORTED_MODULE_0__["css"]`
        background-color: #007aff;
        cursor: pointer;

        &:hover {
          box-shadow: 0 3px 3px rgba(0, 0, 0, 0.2);
          transform: scale(1.01);
          transition: all 200ms ease-out;
        }

        &:active {
          box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
          transform: scale(1);
        }
      `}
    
    ${props => !props.enabled && styled_components__WEBPACK_IMPORTED_MODULE_0__["css"]`
        background-color: #cbcbcb;
        cursor: initial;
      `}
       
}
`;
/* harmony default export */ __webpack_exports__["default"] = (Wrapper);

/***/ }),

/***/ "./src/Edit/components/Button/index.js":
/*!*********************************************!*\
  !*** ./src/Edit/components/Button/index.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Wrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Wrapper */ "./src/Edit/components/Button/Wrapper.js");
/**
 * External dependencies
 */



const Button = ({
  children,
  label,
  ...props
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Wrapper__WEBPACK_IMPORTED_MODULE_1__["default"], props, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
  style: {
    width: "calc(100% - 16px)",
    overflow: "hidden",
    textOverflow: "ellipsis",
    whiteSpace: "nowrap"
  }
}, label), children);

/* harmony default export */ __webpack_exports__["default"] = (Button);

/***/ }),

/***/ "./src/Edit/components/EntityList/List.js":
/*!************************************************!*\
  !*** ./src/Edit/components/EntityList/List.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: List.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const List = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].ul`
	margin: 0 auto;
	padding-bottom: 8px;
`; // Finally export the `List` component.

/* harmony default export */ __webpack_exports__["default"] = (List);

/***/ }),

/***/ "./src/Edit/components/EntityList/index.js":
/*!*************************************************!*\
  !*** ./src/Edit/components/EntityList/index.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _List__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./List */ "./src/Edit/components/EntityList/List.js");
/* harmony import */ var _EntityTile__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../EntityTile */ "./src/Edit/components/EntityTile/index.js");
/**
 * Components: Entity List.
 *
 * The `EntityList` components is a list of `EntityTile`s. The `EntityList`
 * feeds each `EntityTile` with the `entity` and with the `onClick` handler.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */



/**
 * The `EntityList` component.
 *
 * @since 3.11.0
 *
 * @param {Object} entities A map of entities, keyed by their id.
 * @param {Function} onClick The click handler.
 * @param {Function} onLinkClick Handler for the link/no link click.
 * @param {Function} onEditClick Handler for the edit click.
 * @constructor
 */

const EntityList = ({
  entities,
  onClick,
  onLinkClick,
  onEditClick
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_List__WEBPACK_IMPORTED_MODULE_1__["default"], null, // Map each entity to an `EntityTile`.
  entities.toList().map(entity => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_EntityTile__WEBPACK_IMPORTED_MODULE_2__["default"], {
    entity: entity,
    tile: {
      open: false
    },
    onClick: onClick,
    onEditClick: onEditClick,
    onLinkClick: onLinkClick,
    key: entity.id
  })));
}; // Finally export the `EntityList`.


/* harmony default export */ __webpack_exports__["default"] = (EntityList);

/***/ }),

/***/ "./src/Edit/components/EntityTile/Category.js":
/*!****************************************************!*\
  !*** ./src/Edit/components/EntityTile/Category.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Category.
 *
 * The category component.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

const Category = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
	display: inline-block;
	position: relative;
	top: -4px;	
	line-height: 16px;
	font-size: 12px;
`; // Finally export the `Category`.

/* harmony default export */ __webpack_exports__["default"] = (Category);

/***/ }),

/***/ "./src/Edit/components/EntityTile/Cloud.js":
/*!*************************************************!*\
  !*** ./src/Edit/components/EntityTile/Cloud.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Cloud.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Cloud = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].i`
  display: ${props => props.local ? "none" : "inline-block !important"};
  position: absolute;
  right: 20px;
  top: 8px;
  font-size: 14px;
  line-height: 1;
  color: #cbcbcb;
  user-select: none;
  transition: opacity 150ms ease;
`; // Finally export the `Cloud`.

/* harmony default export */ __webpack_exports__["default"] = (Cloud);

/***/ }),

/***/ "./src/Edit/components/EntityTile/Drawer.js":
/*!**************************************************!*\
  !*** ./src/Edit/components/EntityTile/Drawer.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Drawer.
 *
 * A container for elements that display when the drawer is open.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Drawer = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
	display: block;
	position: absolute;
	left: 248px;
	top: 0;
	bottom: 0;
	box-sizing: border-box;
	width: 248px;
	min-height: 24px;
	padding: 8px;
	color: #626162;
	transition: left 200ms ease;
	left: ${props => props.open ? 0 : '248px'};
`; // Finally export the `Drawer`.

/* harmony default export */ __webpack_exports__["default"] = (Drawer);

/***/ }),

/***/ "./src/Edit/components/EntityTile/EditLink.js":
/*!****************************************************!*\
  !*** ./src/Edit/components/EntityTile/EditLink.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Edit Link.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const EditLink = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].i`
	cursor: pointer;
	display: ${props => props.edit ? 'block' : 'none !important'};
	position: absolute;
	right: 20px;
	top: 9px;
	width: 16px;
	height: 16px;
	text-align: center;
	line-height: 1;
	background-color: #666;
	color: #fff;
	border-radius: 2px;
	
	&:before {
		position: absolute;
		top: 50%;
		left: 50%;
		margin-top: -7px;
		margin-left: -6px;
		font-size: 14px;
	}
	
	&:hover {
		background-color: #fccd34;
	}
`; // Finally export the `EditLink`.

/* harmony default export */ __webpack_exports__["default"] = (EditLink);

/***/ }),

/***/ "./src/Edit/components/EntityTile/IconImg.js":
/*!***************************************************!*\
  !*** ./src/Edit/components/EntityTile/IconImg.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Cloud Image.
 *
 * @since 3.27.3
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const IconImg = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].img`
  border: 0;
  position: absolute;
  right: 20px;
  top: calc(50% - 10px);
  max-width: 20px;
  max-height: 20px;
`; // Finally export the `IconImg`.

/* harmony default export */ __webpack_exports__["default"] = (IconImg);

/***/ }),

/***/ "./src/Edit/components/EntityTile/Label.js":
/*!*************************************************!*\
  !*** ./src/Edit/components/EntityTile/Label.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Label.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Label = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
	display: inline-block;
	position: relative;
	box-sizing: border-box;
	max-width: 180px;
	margin-top: 4px;
	min-height: 16px;
	line-height: 16px;
	font-weight: 600;
	font-size: 12px;
	user-select: none;
	hyphens: auto;
	color: ${props => 0 < props.entity.occurrences.length ? '#2e92ff' : '#666'};
`; // Finally export the `Label`.

/* harmony default export */ __webpack_exports__["default"] = (Label);

/***/ }),

/***/ "./src/Edit/components/EntityTile/Main.js":
/*!************************************************!*\
  !*** ./src/Edit/components/EntityTile/Main.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Main.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Main = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
	cursor: pointer;
	display: block;
	position: relative;
	left: ${props => props.open ? '-248px' : 0};
	top: 0;
	box-sizing: border-box;
	width: 236px;
	min-height: 24px;
	transition: left 200ms ease;
`; // Finally export `Main`.

/* harmony default export */ __webpack_exports__["default"] = (Main);

/***/ }),

/***/ "./src/Edit/components/EntityTile/MainType.js":
/*!****************************************************!*\
  !*** ./src/Edit/components/EntityTile/MainType.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Label.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const MainType = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
  display: ${props => props.entity.duplicateLabel ? 'inline-block' : 'none'};
	margin-left: 2px;
	position: relative;
	font-weight: 300;
	font-size: 10px;
`; // Finally export the `MainType`.

/* harmony default export */ __webpack_exports__["default"] = (MainType);

/***/ }),

/***/ "./src/Edit/components/EntityTile/Wrapper.js":
/*!***************************************************!*\
  !*** ./src/Edit/components/EntityTile/Wrapper.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Wrapper.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Wrapper = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].li`
	display: 'block';
	position: relative;
	box-sizing: border-box;
	overflow: hidden;
	border-radius: 2px;
	margin: 8px auto;
	width: 248px;
	min-height: 32px;
	padding: 4px 0;
	background-color: #f5f5f5;
	box-shadow: 0 4px 4px -3px rgba(0,0,0,.25), 0 8px 8px -6px rgba(0,0,0,.25);
	transition: all 100ms linear;
	// Prevent dotted outline in FF
	outline: 0;
	
	// Since we removed the box with the number of occurrences we need to pad left.
	//
	// See https://github.com/insideout10/wordlift-plugin/issues/735
	padding-left: 12px;

	&:hover {
		transform: scale( ${props => 0 < props.entity.occurrences.length ? 1 : 1.01} ); 
		background-color: ${props => 0 < props.entity.occurrences.length ? '#f5f5f5' : '#fafafa'}
		// Prevent dotted outline in FF
		outline: 0;
	};

	&:active {
		transform: scale(0.99)
		background-color: #f5f5f5;
		// Prevent dotted outline in FF
		outline: 0;
	};

	&:focus {
		// Prevent dotted outline in FF
		outline: 0;
	}

`; // Finally export the `Wrapper`.

/* harmony default export */ __webpack_exports__["default"] = (Wrapper);

/***/ }),

/***/ "./src/Edit/components/EntityTile/index.js":
/*!*************************************************!*\
  !*** ./src/Edit/components/EntityTile/index.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Wrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Wrapper */ "./src/Edit/components/EntityTile/Wrapper.js");
/* harmony import */ var _Main__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Main */ "./src/Edit/components/EntityTile/Main.js");
/* harmony import */ var _Label__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./Label */ "./src/Edit/components/EntityTile/Label.js");
/* harmony import */ var _MainType__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./MainType */ "./src/Edit/components/EntityTile/MainType.js");
/* harmony import */ var _IconImg__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./IconImg */ "./src/Edit/components/EntityTile/IconImg.js");
/* harmony import */ var _Drawer__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./Drawer */ "./src/Edit/components/EntityTile/Drawer.js");
/* harmony import */ var _Switch__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../Switch */ "./src/Edit/components/Switch/index.js");
/* harmony import */ var _Category__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./Category */ "./src/Edit/components/EntityTile/Category.js");
/* harmony import */ var _EditLink__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./EditLink */ "./src/Edit/components/EntityTile/EditLink.js");
/* harmony import */ var _ArrowToggle__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../ArrowToggle */ "./src/Edit/components/ArrowToggle/index.js");
/**
 * Components: Entity Tile.
 *
 * The `EntityTile` component is loaded from an `EntityList` component. It's
 * tile representing a single entity. It expects two properties:
 *  * `entity`, representing the entity for the tile,
 *
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */











const applyFilters = window.wp && window.wp.hooks && window.wp.hooks.applyFilters;
const defaultCloudIconURL = `${window.wlSettings.wl_root}images/svg/wl-cloud-icon.svg`;
const defaultNetworkIconURL = `${window.wlSettings.wl_root}images/svg/wl-network-icon.svg`;
const defaultLocalIconURL = "";
/**
 * @inheritDoc
 */

class EntityTile extends react__WEBPACK_IMPORTED_MODULE_0___default.a.Component {
  /**
   * @inheritDoc
   */
  constructor(props) {
    super(props); // Create a reference to the DOM element, sine we want focus on the tiles
    // when they're active.

    this.ref = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createRef(); // Bind our functions.

    this.onEditClick = this.onEditClick.bind(this);
    this.onSwitchClick = this.onSwitchClick.bind(this);
    this.onMainClick = this.onMainClick.bind(this);
    this.onArrowToggleClick = this.onArrowToggleClick.bind(this);
    this.close = this.close.bind(this); // Set the initial state.

    this.state = {
      open: false
    }; // Hooking into the cloud icons, compute icon filters

    this.computeIconFilters();
  }
  /**
   * Handles clicks on the `QuickEdit` element and forwards it to the parent
   * handlers.
   *
   * @since 3.11.0
   *
   * @param {Event} e The source {@link Event}.
   */


  onEditClick(e) {
    // Prevent propagation.
    e.preventDefault(); // Call the handler.

    this.props.onEditClick(this.props.entity); // Close the drawer.

    this.setState({
      open: false
    });
  }
  /**
   * Handle clicks by forwarding the event to the handler (defined in
   * `EntityListContainer`).
   *
   * @since 3.11.0
   *
   * @param {Event} e The source {@link Event}.
   */


  onMainClick(e) {
    // Prevent propagation.
    e.preventDefault(); // Call the handler.

    this.props.onClick(this.props.entity);
  }
  /**
   * Handles clicks on the `LinkWrap` element and forwards them to the parent
   * handler.
   *
   * @since 3.11.0
   *
   * @param {Event} e The source {@link Event}.
   */


  onSwitchClick(e) {
    // Prevent propagation.
    e.preventDefault(); // Call the handler.

    this.props.onLinkClick(this.props.entity);
  }
  /**
   * Handle trigger clicks, toggling the drawer's open/close state.
   *
   * @since 3.11.0
   *
   * @param {Event} e The source {@link Event}.
   */


  onArrowToggleClick(e) {
    e.preventDefault(); // Call the handler.

    this.setState({
      open: !this.state.open
    });
  }
  /**
   * Close the `Drawer` (if open).
   *
   * @since 3.11.0
   * @param {Event} e The source {@link Event}.
   */


  close(e) {
    e.preventDefault(); // Close if open.

    if (!e.currentTarget.contains(document.activeElement) && this.state.open) {
      this.setState({
        open: false
      });
    }
  }
  /**
   * When the component is updated with the open flag, set the focus.
   *
   * @since 3.11.0
   */


  componentDidUpdate() {
    if (this.state.open && this.ref && this.ref.current && this.ref.current.focus) {
      this.ref.current.focus();
    }
  }
  /**
   * Hooking into the Cloud icons
   *
   * @see https://github.com/insideout10/wordlift-plugin/issues/1118
   * @see https://github.com/insideout10/wordlift-plugin/issues/1153
   * @since 3.27.3
   *
   */


  computeIconFilters() {
    // Possibly applyFilters and addFilter may not be available (for example in WP 4.7)
    this.cloudIconURL = applyFilters ? applyFilters("wl_cloud_icon_url", defaultCloudIconURL) : defaultCloudIconURL;
    this.networkIconURL = applyFilters ? applyFilters("wl_network_icon_url", defaultNetworkIconURL) : defaultNetworkIconURL;
    let isLocalEntity = this.props.entity.local || this.props.entity.id && this.props.entity.id.startsWith(window.location.origin);
    this.iconURL = isLocalEntity ? defaultLocalIconURL : this.props.entity.id && this.props.entity.id.match(/https?:\/\/(?:\w+\.)?(dbpedia|wikidata)\.org/) ? this.cloudIconURL : this.networkIconURL;
    this.iconURL = applyFilters ? applyFilters("wl_icon_url", this.iconURL, this.props.entity) : this.iconURL;
  }
  /**
   * Render the component.
   *
   * @since 3.11.0
   * @returns {XML} The render tree.
   */


  render() {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Wrapper__WEBPACK_IMPORTED_MODULE_1__["default"], {
      entity: this.props.entity,
      onBlur: this.close,
      ref: this.ref,
      tabIndex: "0",
      key: this.props.entity.id
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Main__WEBPACK_IMPORTED_MODULE_2__["default"], {
      onClick: this.onMainClick,
      open: this.state.open
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Label__WEBPACK_IMPORTED_MODULE_3__["default"], {
      entity: this.props.entity
    }, this.props.entity.label, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_MainType__WEBPACK_IMPORTED_MODULE_4__["default"], {
      entity: this.props.entity
    }, this.props.entity.mainType)), this.iconURL && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_IconImg__WEBPACK_IMPORTED_MODULE_5__["default"], {
      src: this.iconURL
    })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Drawer__WEBPACK_IMPORTED_MODULE_6__["default"], {
      open: this.state.open
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Switch__WEBPACK_IMPORTED_MODULE_7__["default"], {
      onClick: this.onSwitchClick,
      selected: this.props.entity.link
    }, "Link", " "), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Category__WEBPACK_IMPORTED_MODULE_8__["default"], null, this.props.entity.mainType), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_EditLink__WEBPACK_IMPORTED_MODULE_9__["default"], {
      onClick: this.onEditClick,
      edit: this.props.entity.edit,
      className: "fa fa-pencil"
    })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_ArrowToggle__WEBPACK_IMPORTED_MODULE_10__["default"], {
      onClick: this.onArrowToggleClick,
      open: this.state.open,
      show: this.props.entity.occurrences && 0 < this.props.entity.occurrences.length
    }));
  }

}
/*
 *
 * Example implementation of wl_icon_url
 *
wp.hooks.addFilter(
  "wl_icon_url",
  "wordlift",
  (content, entity) =>
    entity.local
      ? entity.sameAs.some(element => element.match(/https?:\/\/(?:\w+\\.)?yago-knowledge\.org/))
        ? "https://image.flaticon.com/icons/svg/1163/1163624.svg"
        : ""
      : content
);
*/
// Finally export the class.


/* harmony default export */ __webpack_exports__["default"] = (EntityTile);

/***/ }),

/***/ "./src/Edit/components/Header/Wrapper.js":
/*!***********************************************!*\
  !*** ./src/Edit/components/Header/Wrapper.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Wrapper component.
 *
 * The Wrapper component applies styling to its children in order to make them
 * event.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Wrapper = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
	position: relative;
	margin: auto;
	max-width: 248px;
	border-radius: 2px;
	border: 1px solid #666;
	overflow: hidden;

	* {
		box-sizing: border-box !important;
		display: inline-block;
		width: 20%;
		border-right: 1px solid #666;
		color: #666;
		text-align: center;
		text-decoration: none;
		
		&:hover {
			color: #a0a0a0;
		}

		&:focus {
			// Overrides WordPress' styles.
			box-shadow: none
		}
	}

	*:last-child {
		border-right: none;

		// Fix annoying pixel gap in responsive.
		&.wl-active {
			box-shadow: 3px 0 0 0 #666;
		}
	}
	
	*.wl-active {
		background: #666;
		color: #fff;
	}
`; // Finally export the `Wrapper`.

/* harmony default export */ __webpack_exports__["default"] = (Wrapper);

/***/ }),

/***/ "./src/Edit/components/Header/index.js":
/*!*********************************************!*\
  !*** ./src/Edit/components/Header/index.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Wrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Wrapper */ "./src/Edit/components/Header/Wrapper.js");
/* harmony import */ var _containers_EntityFilter__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../containers/EntityFilter */ "./src/Edit/containers/EntityFilter.js");
/**
 * Components: the classification box header.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */



/**
 * Define the `Header` with the entity filters.
 *
 * @since 3.11.0
 * @return {Function} The `render` function.
 */

const Header = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Wrapper__WEBPACK_IMPORTED_MODULE_1__["default"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_containers_EntityFilter__WEBPACK_IMPORTED_MODULE_2__["default"], {
  filter: "SHOW_WHAT"
}, "what"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_containers_EntityFilter__WEBPACK_IMPORTED_MODULE_2__["default"], {
  filter: "SHOW_WHERE"
}, "where"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_containers_EntityFilter__WEBPACK_IMPORTED_MODULE_2__["default"], {
  filter: "SHOW_WHEN"
}, "when"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_containers_EntityFilter__WEBPACK_IMPORTED_MODULE_2__["default"], {
  filter: "SHOW_WHO"
}, "who"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_containers_EntityFilter__WEBPACK_IMPORTED_MODULE_2__["default"], {
  filter: "SHOW_ALL"
}, "all")); // Finally export the `Header`.


/* harmony default export */ __webpack_exports__["default"] = (Header);

/***/ }),

/***/ "./src/Edit/components/Input/Wrapper.js":
/*!**********************************************!*\
  !*** ./src/Edit/components/Input/Wrapper.js ***!
  \**********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");

const Wrapper = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
  padding: 8px 0 6px;
  border-bottom: 2px solid #007aff;
  display: inline-block;
  width: 100%;

  > input {
    border: 0;
    outline: none;
    float: left;
    width: calc(100% - 36px) !important;
    font-family: BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu,
      Cantarell, "Helvetica Neue", sans-serif;
    line-height: 20px;
    font-size: 14px;
    padding: 0 6px;
  }
`;
/* harmony default export */ __webpack_exports__["default"] = (Wrapper);

/***/ }),

/***/ "./src/Edit/components/Input/X.js":
/*!****************************************!*\
  !*** ./src/Edit/components/Input/X.js ***!
  \****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");

const X = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
  font-family: sans-serif;
  background-color: #007aff;
  color: white;
  width: 12px;
  height: 12px;
  display: inline-block;
  text-align: center;
  line-height: 12px;
  font-size: 12px;
  padding: 1px;
  cursor: pointer;
  border-radius: 100%;
  transform: rotate(45deg);

  &::after {
    content: "+";
    font-size: 12px;
    width: 100%;
    height: 100%;
  }
`;
/* harmony default export */ __webpack_exports__["default"] = (X);

/***/ }),

/***/ "./src/Edit/components/Input/index.js":
/*!********************************************!*\
  !*** ./src/Edit/components/Input/index.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Wrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Wrapper */ "./src/Edit/components/Input/Wrapper.js");
/* harmony import */ var _X__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./X */ "./src/Edit/components/Input/X.js");
function _extends() { _extends = Object.assign ? Object.assign.bind() : function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }





const Input = ({
  onCancel,
  onInputChange,
  ...props
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Wrapper__WEBPACK_IMPORTED_MODULE_1__["default"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("input", _extends({
  onChange: e => onInputChange(e.target.value)
}, props)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_X__WEBPACK_IMPORTED_MODULE_2__["default"], {
  onClick: onCancel
}));

/* harmony default export */ __webpack_exports__["default"] = (Input);

/***/ }),

/***/ "./src/Edit/components/Link/index.js":
/*!*******************************************!*\
  !*** ./src/Edit/components/Link/index.js ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/**
 * Components: Link.
 *
 * The `Link` component is a link which handles an `onClick` event.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * Define the `Link` class.
 *
 * @since 3.11.0
 */

class Link extends react__WEBPACK_IMPORTED_MODULE_0___default.a.PureComponent {
  /**
   * @inheritDoc
   */
  constructor(props) {
    super(props);
    this.onClick = this.onClick.bind(this);
  }
  /**
   * Reroute clicks to the container.
   *
   * @since 3.11.0
   *
   * @param {Event} e The source {@link Event}.
   */


  onClick(e) {
    e.preventDefault();
    this.props.onClick(this.props.filter);
  }
  /**
   * @inheritDoc
   */


  render() {
    return (
      /*#__PURE__*/
      // eslint-disable-next-line
      react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("a", {
        className: this.props.active ? 'wl-active' : '',
        onClick: this.onClick
      }, this.props.children)
    );
  }

} // Finally export the `Link`.


/* harmony default export */ __webpack_exports__["default"] = (Link);

/***/ }),

/***/ "./src/Edit/components/List/Wrapper.js":
/*!*********************************************!*\
  !*** ./src/Edit/components/List/Wrapper.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");

const Wrapper = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].ul`
  background: white;
  // border-top: 0;
  // border-bottom: 1px solid #007aff;
  // border-left: 1px solid #007aff;
  // border-right: 1px solid #007aff;
  border: 1px solid #007aff;
  box-shadow: 0 3px 3px rgba(0, 0, 0, 0.2);
  padding: 0;
  margin: 0;
  box-sizing: border-box;
  max-height: 280px;
  overflow-y: scroll;
  font-family: BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu,
    Cantarell, "Helvetica Neue", sans-serif;
  line-height: 20px;
  font-size: 14px;
  position: absolute;
  // width: calc(100% - 1px);
  // margin-left: 1px;
  width: 110%;
  margin-left: calc( -5% + 1px );
  overflow-x: hidden;
  z-index: 1001;

  display: ${props => props.open ? "block" : "none"};

  > li {
    cursor: pointer;
    margin-bottom: 0;
    padding-bottom: 4px;

    &:hover {
      background-color: rgba(46, 146, 255, 0.2);
    }
  }
`;
/* harmony default export */ __webpack_exports__["default"] = (Wrapper);

/***/ }),

/***/ "./src/Edit/components/List/index.js":
/*!*******************************************!*\
  !*** ./src/Edit/components/List/index.js ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Wrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Wrapper */ "./src/Edit/components/List/Wrapper.js");



const List = ({
  open,
  children
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Wrapper__WEBPACK_IMPORTED_MODULE_1__["default"], {
  open: open
}, 0 < children.length && children.map((item, index) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("li", {
  key: index
}, item)), 0 === children.length && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("li", {
  style: {
    padding: "8px",
    cursor: "initial"
  },
  key: 0
}, "No results."));

/* harmony default export */ __webpack_exports__["default"] = (List);

/***/ }),

/***/ "./src/Edit/components/Select/index.js":
/*!*********************************************!*\
  !*** ./src/Edit/components/Select/index.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Input__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../Input */ "./src/Edit/components/Input/index.js");
/* harmony import */ var _List__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../List */ "./src/Edit/components/List/index.js");




const Select = ({
  open,
  value,
  onCancel,
  onInputChange,
  children
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Input__WEBPACK_IMPORTED_MODULE_1__["default"], {
  onCancel: onCancel,
  onInputChange: onInputChange,
  value: value
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_List__WEBPACK_IMPORTED_MODULE_2__["default"], {
  open: open
}, children));

/* harmony default export */ __webpack_exports__["default"] = (Select);

/***/ }),

/***/ "./src/Edit/components/Switch/Background.js":
/*!**************************************************!*\
  !*** ./src/Edit/components/Switch/Background.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Background.
 *
 * The {@link Switch} background.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Background = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
	display: inline-block;
	position: relative;
	box-sizing: border-box;
	width: 24px;
	height: 16px;
	background: ${props => props.selected ? '#7ed321' : '#c7c7c7'};
	transition: background 200ms ease;
	border-radius: 10px;
`; // Finally export the `Background`.

/* harmony default export */ __webpack_exports__["default"] = (Background);

/***/ }),

/***/ "./src/Edit/components/Switch/Bullet.js":
/*!**********************************************!*\
  !*** ./src/Edit/components/Switch/Bullet.js ***!
  \**********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Bullet.
 *
 * The {@link Switch} bullet.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Bullet = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
	cursor: pointer;
	display: inline-block;
	position: absolute;
	top: 2px;
	left: ${props => props.selected ? 10 : 2}px;
	transition: left 150ms ease;
	width: 12px;
	height: 12px;
	background: #FFFFFF;
	border-radius: 50%;
`; // Finally export the `Bullet`.

/* harmony default export */ __webpack_exports__["default"] = (Bullet);

/***/ }),

/***/ "./src/Edit/components/Switch/Label.js":
/*!*********************************************!*\
  !*** ./src/Edit/components/Switch/Label.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Label.
 *
 * The {@link Switch} label.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Label = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
	display: inline-block;
	position: relative;
	top: -4px;
	line-height: 16px;
	font-size: 12px;
	margin: 0 12px 0 4px;
	transition: opacity 150ms ease;
	opacity: ${props => props.selected ? 1 : 0.5};
	
	&:after {
		content: ' ';
		display: inline-block;
		position: absolute;
		right: -12px;
		top: 6px;
		width: 4px;
		height: 4px;
		border-radius: 50%;
		background-color: #626162;
		margin-right: 4px;
	}
`; // Finally export the `Label`.

/* harmony default export */ __webpack_exports__["default"] = (Label);

/***/ }),

/***/ "./src/Edit/components/Switch/Wrapper.js":
/*!***********************************************!*\
  !*** ./src/Edit/components/Switch/Wrapper.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/**
 * Components: Wrapper component.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * @inheritDoc
 */

const Wrapper = styled_components__WEBPACK_IMPORTED_MODULE_0__["default"].div`
	display: inline-block;
	position: relative;
	height: 16px;
`; // Finally export the `Wrapper`.

/* harmony default export */ __webpack_exports__["default"] = (Wrapper);

/***/ }),

/***/ "./src/Edit/components/Switch/index.js":
/*!*********************************************!*\
  !*** ./src/Edit/components/Switch/index.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Wrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Wrapper */ "./src/Edit/components/Switch/Wrapper.js");
/* harmony import */ var _Background__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Background */ "./src/Edit/components/Switch/Background.js");
/* harmony import */ var _Bullet__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./Bullet */ "./src/Edit/components/Switch/Bullet.js");
/* harmony import */ var _Label__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./Label */ "./src/Edit/components/Switch/Label.js");
/**
 * Components: Switch component.
 *
 * Represents a switch, forwards clicks to the parent. State is handled by the
 * parent via the `on` property.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Define the `Switch`.
 *
 * @since 3.11.0
 */

class Switch extends react__WEBPACK_IMPORTED_MODULE_0___default.a.PureComponent {
  /**
   * @inheritDoc
   */
  constructor(props) {
    super(props); // Bind the event handler.

    this.onClick = this.onClick.bind(this);
  }
  /**
   * Handle clicks.
   *
   * @since 3.11.0
   * @param {Event} e The source {@link Event}.
   */


  onClick(e) {
    e.preventDefault(); // Forward the click event.

    this.props.onClick(e);
  }
  /**
   * @inheritDoc
   */


  render() {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Wrapper__WEBPACK_IMPORTED_MODULE_1__["default"], {
      onClick: this.onClick
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Background__WEBPACK_IMPORTED_MODULE_2__["default"], {
      selected: this.props.selected
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Bullet__WEBPACK_IMPORTED_MODULE_3__["default"], {
      selected: this.props.selected
    })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_Label__WEBPACK_IMPORTED_MODULE_4__["default"], {
      selected: this.props.selected
    }, this.props.children));
  }

} // Finally export the `Switch`.


/* harmony default export */ __webpack_exports__["default"] = (Switch);

/***/ }),

/***/ "./src/Edit/constants/ActionTypes.js":
/*!*******************************************!*\
  !*** ./src/Edit/constants/ActionTypes.js ***!
  \*******************************************/
/*! exports provided: ANNOTATION, TOGGLE_ENTITY, UPDATE_OCCURRENCES_FOR_ENTITY, TOGGLE_LINK, RECEIVE_ANALYSIS_RESULTS, SET_CURRENT_ENTITY, SET_ENTITY_FILTER, EDITOR_SELECTION_CHANGED, TOGGLE_LINK_SUCCESS, ADD_ENTITY */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ANNOTATION", function() { return ANNOTATION; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TOGGLE_ENTITY", function() { return TOGGLE_ENTITY; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "UPDATE_OCCURRENCES_FOR_ENTITY", function() { return UPDATE_OCCURRENCES_FOR_ENTITY; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TOGGLE_LINK", function() { return TOGGLE_LINK; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "RECEIVE_ANALYSIS_RESULTS", function() { return RECEIVE_ANALYSIS_RESULTS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_CURRENT_ENTITY", function() { return SET_CURRENT_ENTITY; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_ENTITY_FILTER", function() { return SET_ENTITY_FILTER; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "EDITOR_SELECTION_CHANGED", function() { return EDITOR_SELECTION_CHANGED; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TOGGLE_LINK_SUCCESS", function() { return TOGGLE_LINK_SUCCESS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ADD_ENTITY", function() { return ADD_ENTITY; });
/**
 * Define the `ANNOTATION` action name, used when an annotation is (de)selected
 * in TinyMCE.
 *
 * @since 3.11.0
 * @type {string}
 */
const ANNOTATION = "ANNOTATION";
/**
 * Define the `TOGGLE_ENTITY` action name, used when the selection state of an
 * an entity must be toggled on / off.
 *
 * @since 3.11.0
 * @type {string}
 */

const TOGGLE_ENTITY = "TOGGLE_ENTITY";
/**
 * Define the `UPDATE_OCCURRENCES_FOR_ENTITY` action name, used to catch
 * `updateOccur(r)encesForEntity` events from the legacy Angular application.
 *
 * @since 3.11.0
 * @type {string}
 */

const UPDATE_OCCURRENCES_FOR_ENTITY = "UPDATE_OCCURRENCES_FOR_ENTITY";
/**
 * Define the `TOGGLE_LINK` action name, used to enable/disable linking an
 * entity.
 *
 * @since 3.11.0
 * @type {string}
 */

const TOGGLE_LINK = "TOGGLE_LINK";
/**
 * Define the `RECEIVE_ANALYSIS_RESULTS` action name, fired when analysis
 * results are received.
 *
 * @since 3.11.0
 * @type {string}
 */

const RECEIVE_ANALYSIS_RESULTS = "RECEIVE_ANALYSIS_RESULTS";
/**
 * Define the `SET_CURRENT_ENTITY` action name, fired to edit an entity inline.
 *
 * @since 3.11.0
 * @type {string}
 */

const SET_CURRENT_ENTITY = "SET_CURRENT_ENTITY";
const SET_ENTITY_FILTER = "SET_ENTITY_FILTER";
/**
 * Define the `EDITOR_SELECTION_CHANGED` action name, fired when the selection has changed
 * in the main editor.
 *
 * @since 3.18.4
 * @type {string}
 */

const EDITOR_SELECTION_CHANGED = "EDITOR_SELECTION_CHANGED";
const TOGGLE_LINK_SUCCESS = "TOGGLE_LINK_SUCCESS";
const ADD_ENTITY = "ADD_ENTITY";

/***/ }),

/***/ "./src/Edit/containers/EntityFilter.js":
/*!*********************************************!*\
  !*** ./src/Edit/containers/EntityFilter.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../actions */ "./src/Edit/actions/index.js");
/* harmony import */ var _components_Link__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/Link */ "./src/Edit/components/Link/index.js");
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */



/**
 * @inheritDoc
 */

const mapStateToProps = (state, ownProps) => {
  return {
    active: ownProps.filter === state.visibilityFilter
  };
};
/**
 * @inheritDoc
 */


const mapDispatchToProps = dispatch => {
  return {
    /**
     * Set the entity visibility filter when a link is clicked.
     *
     * @since 3.11.0
     *
     * @param {string} filter The filter (who, where, when, what, all).
     */
    onClick: filter => {
      dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_1__["setEntityVisibility"])(filter));
    }
  };
};
/**
 * The `EntityListContainer` instance built by `react-redux` by connecting the
 * store with the state and dispatchers merged to properties passed to the React
 * components.
 *
 * @since 3.11.0
 */


const EntityFilter = Object(react_redux__WEBPACK_IMPORTED_MODULE_0__["connect"])(mapStateToProps, mapDispatchToProps)(_components_Link__WEBPACK_IMPORTED_MODULE_2__["default"]); // Finally export the `EntityFilter`.

/* harmony default export */ __webpack_exports__["default"] = (EntityFilter);

/***/ }),

/***/ "./src/Edit/containers/VisibleEntityList.js":
/*!**************************************************!*\
  !*** ./src/Edit/containers/VisibleEntityList.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../actions */ "./src/Edit/actions/index.js");
/* harmony import */ var _components_EntityList__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/EntityList */ "./src/Edit/components/EntityList/index.js");
/**
 * Containers: Entity List.
 *
 * The `EntityListContainer` follows the `react-redux` pattern to bind specific
 * parts of the application state and dispatchers to the contained components.
 *
 * The `EntityListContainer` contains the `EntityList` component.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */



/**
 * Filters the provided map of entities according to the specified filter.
 *
 * @since 3.11.0
 *
 * @param {Object} entities A keyed map of entities.
 * @param {string} annotation The annotation id.
 * @param {string} filter The filter.
 * @returns {Object} The filtered keyed-map of entities.
 */

const getVisibleEntities = (entities, annotation, filter) => {
  switch (filter) {
    // When showing an annotation, we check that the annotation id is
    // present as key in the annotations property.
    case "SHOW_ANNOTATION":
      return entities.filter(x => "undefined" !== typeof x.annotations && annotation in x.annotations);

    case "SHOW_WHO":
      return entities.filter(x => "undefined" !== typeof x.w && "who" === x.w);

    case "SHOW_WHERE":
      return entities.filter(x => "undefined" !== typeof x.w && "where" === x.w);

    case "SHOW_WHEN":
      return entities.filter(x => "undefined" !== typeof x.w && "when" === x.w);

    case "SHOW_WHAT":
      return entities.filter(x => "undefined" !== typeof x.w && "what" === x.w);

    default:
      // When showing all the entities, show only the shortlisted ones,
      // i.e. the most relevant. The `shortlist` flag is set in the
      // `entities` reducer and is assigned to the first 20 entities
      // ordered by descending confidence.
      //
      // We also show selected entities.
      return entities.filter(x => x.shortlist || 0 < x.occurrences.length);
  }
};
/**
 * Map the state to React components' properties.
 *
 * @since 3.11.0
 *
 * @param {object} state A state instance.
 * @returns {{entities}} An object with the list of entities.
 */


const mapStateToProps = state => {
  return {
    entities: getVisibleEntities(state.entities, state.annotationFilter, state.visibilityFilter)
  };
};
/**
 * Map dispatchers to React components' properties.
 *
 * @since 3.11.0
 * @param {function} dispatch Redux's dispatch function.
 * @returns {{onClick: (Function), onLinkClick: (Function)}} A list of
 *     dispatchers.
 */


const mapDispatchToProps = dispatch => {
  return {
    /**
     * The `onClick` dispatchers used by `EntityTile` component.
     *
     * @since 3.11.0
     * @param {Object} entity The entity instance being clicked.
     */
    onClick: entity => {
      dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_1__["toggleEntity"])(entity));
    },

    /**
     * The `onLinkClick` function is called when the Link switch is
     * clicked.
     * This function will toggle the link/no link on the entity's
     * occurrences.
     *
     * @since 3.11.0
     * @param {Object} entity The entity.
     */
    onLinkClick: entity => {
      dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_1__["toggleLink"])(entity));
    },
    onEditClick: entity => {
      dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_1__["setCurrentEntity"])(entity));
    }
  };
};
/**
 * The `EntityListContainer` instance built by `react-redux` by connecting the
 * store with the state and dispatchers merged to properties passed to the React
 * components.
 *
 * @since 3.11.0
 */


const VisibleEntityList = Object(react_redux__WEBPACK_IMPORTED_MODULE_0__["connect"])(mapStateToProps, mapDispatchToProps)(_components_EntityList__WEBPACK_IMPORTED_MODULE_2__["default"]); // Finally export the `VisibleEntityList`.

/* harmony default export */ __webpack_exports__["default"] = (VisibleEntityList);

/***/ }),

/***/ "./src/Edit/reducers/annotationFilter.js":
/*!***********************************************!*\
  !*** ./src/Edit/reducers/annotationFilter.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../constants/ActionTypes */ "./src/Edit/constants/ActionTypes.js");
/**
 * Reducers: Annotation Filter.
 *
 * @since 3.11.0
 */

/**
 * Internal dependencies
 */
 // import log from '../../modules/log';

/**
 * Define the reducers.
 *
 * @since 3.11.0
 * @param {object} state The `state`.
 * @param {object} action The `action`.
 * @returns {object} The new state.
 */

const annotationFilter = function (state = null, action) {
  switch (action.type) {
    // Handle the `ANNOTATION` action, which sets the current selected
    // annotation or null if not set.
    case _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__["ANNOTATION"]:
      // We might receive an undefined annotation (when no annotation is
      // selected. In that case we send `null`.
      //
      // Note that this action is handled also by the `visibilityFilter`
      // which sets itself to `SHOW_ANNOTATION` | `SHOW_ALL` according
      // to whether or not an annotation has been selected.
      return action.annotation === undefined ? null : action.annotation;

    default:
      return state;
  }
}; // Finally export the reducer.


/* harmony default export */ __webpack_exports__["default"] = (annotationFilter);

/***/ }),

/***/ "./src/Edit/reducers/entities.js":
/*!***************************************!*\
  !*** ./src/Edit/reducers/entities.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var immutable__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! immutable */ "./node_modules/immutable/dist/immutable.js");
/* harmony import */ var immutable__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(immutable__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../constants/ActionTypes */ "./src/Edit/constants/ActionTypes.js");
/* harmony import */ var _services_WsService__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../services/WsService */ "./src/Edit/services/WsService.js");
/* harmony import */ var _services_link_LinkServiceFactory__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../services/link/LinkServiceFactory */ "./src/Edit/services/link/LinkServiceFactory.js");
/*global wlSettings*/

/**
 * Reducers: Entities.
 *
 * Define the reducers related to entities.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Define the reducers.
 *
 * @since 3.11.0
 * @param {object} state The `state`.
 * @param {object} action The `action`.
 * @returns {object} The new state.
 */

const entities = function (state = Object(immutable__WEBPACK_IMPORTED_MODULE_0__["Map"])(), action) {
  const linkService = _services_link_LinkServiceFactory__WEBPACK_IMPORTED_MODULE_3__["default"].getInstance();

  switch (action.type) {
    case _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_1__["ADD_ENTITY"]:
      return state.merge(Object(immutable__WEBPACK_IMPORTED_MODULE_0__["Map"])({
        [action.payload.id]: action.payload
      }));
    // Legacy: receive analysis' results.

    case _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_1__["RECEIVE_ANALYSIS_RESULTS"]:
      // Calculate the labels.
      const labels = Object(immutable__WEBPACK_IMPORTED_MODULE_0__["Map"])(action.results.entities).groupBy((v, k) => v.label); // Return a new map of the received entities. The legacy Angular
      // app doesn't set the `link` property on the entity, therefore we
      // preset it here according to the `occurrences` settings.

      return Object(immutable__WEBPACK_IMPORTED_MODULE_0__["Map"])(action.results.entities).map(x => Object.assign({}, x, {
        link: linkService.getLink(x.occurrences),
        local: 0 === x.id.indexOf(wlSettings["datasetUri"]),
        w: _services_WsService__WEBPACK_IMPORTED_MODULE_2__["default"].getW(x),
        edit: "no" !== wlSettings["can_create_entities"],
        duplicateLabel: 1 < labels.get(x.label).count()
      })) // Sort by (1) local/not local, (2) confidence, (3) number of annotations.
      .sort((x, y) => {
        // First the local entities.
        if (x.local !== y.local) return y.local - x.local; // Get the delta confidence.

        const delta = y.confidence - x.confidence; // If the confidence is equal, sort by number of annotations.

        return 0 !== delta ? delta : y.annotations.length - x.annotations.length;
      }) // Set the shortlist flag to true for the first 20.
      .mapEntries(([k, v], i) => {
        v.shortlist = i < 20;
        return [k, v];
      }) //          // Then resort them by label.
      //          .sortBy( x => x.label.toLowerCase() )
      ;

    case _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_1__["TOGGLE_LINK_SUCCESS"]:
      const {
        id,
        link
      } = action.payload;
      return state.set(id, // A new object instance with the existing props and the new
      // occurrences.
      Object.assign({}, state.get(id), {
        link
      }));
    // Update the entity's occurrences. This action is dispatched following
    // a legacy Angular event. The event is configured in the admin/index.js
    // app.

    case _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_1__["UPDATE_OCCURRENCES_FOR_ENTITY"]:
      // Update the entity.
      return state.set(action.entityId, // A new object instance with the existing props and the new
      // occurrences.
      Object.assign({}, state.get(action.entityId), {
        occurrences: action.occurrences,
        link: linkService.getLink(action.occurrences)
      }));

    default:
      return state;
  }
}; // Finally export the reducer.


/* harmony default export */ __webpack_exports__["default"] = (entities);

/***/ }),

/***/ "./src/Edit/reducers/visibilityFilter.js":
/*!***********************************************!*\
  !*** ./src/Edit/reducers/visibilityFilter.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../constants/ActionTypes */ "./src/Edit/constants/ActionTypes.js");
/**
 * Reducers: Visibility Filter.
 *
 * @since 3.11.0
 */

/**
 * Internal dependencies
 */

/**
 * Define the reducers.
 *
 * @since 3.11.0
 * @param {object} state The `state`.
 * @param {object} action The `action`.
 * @returns {object} The new state.
 */

const visibilityFilter = function (state = 'SHOW_ALL', action) {
  switch (action.type) {
    // Handle the `SET_ENTITY_FILTER` action, which changes the current
    // filter.
    case _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__["SET_ENTITY_FILTER"]:
      return action.filter;
    // Handle the `ANNOTATION` action, which notifies us of a selected
    // annotation in TinyMCE. In that case we switch to a `SHOW_ANNOTATION`
    // filter.

    case _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_0__["ANNOTATION"]:
      // We might receive an undefined annotation (when no annotation is
      // selected).
      //
      // Note that selected annotation is set by the `annotationFilter`
      // function.
      return action.annotation === undefined ? 'SHOW_ALL' : 'SHOW_ANNOTATION';

    default:
      return state;
  }
}; // Finally export the reducer.


/* harmony default export */ __webpack_exports__["default"] = (visibilityFilter);

/***/ }),

/***/ "./src/Edit/services/EditorService.js":
/*!********************************************!*\
  !*** ./src/Edit/services/EditorService.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/*global tinyMCE*/

/**
 * Services: EditorService.
 *
 * Provide TinyMCE editor services.
 *
 * @since 3.11.0
 */

/**
 * Cached TinyMCE instances.
 *
 * @since 3.11.0
 */
const instances = [];
/**
 * The `EditorService` class provides access to the in-page TinyMCE editor.
 *
 * @since 3.11.0
 */

class EditorService {
  /**
   * Get the TinyMCE editor with the specified id.
   *
   * @since 3.11.0
   * @param {string} [id=content] The editor id, by default `content`.
   * @return {Object} A TinyMCE editor instance.
   */
  get(id = window["wlSettings"]["default_editor_id"]) {
    // Get the editor id from the `wlSettings` or use `content`.
    // Allow 3rd parties to change the editor id.
    //
    // @see https://github.com/insideout10/wordlift-plugin/issues/850.
    // @see https://github.com/insideout10/wordlift-plugin/issues/851.
    const editorId = "undefined" !== typeof window["wp"].hooks ? window["wp"].hooks.applyFilters("wl_default_editor_id", id) : id;
    return instances[editorId] ? instances[editorId] : instances[editorId] = tinyMCE.get(editorId);
  }

} // Finally export the `EditorService`.


/* harmony default export */ __webpack_exports__["default"] = (new EditorService());

/***/ }),

/***/ "./src/Edit/services/WsService.js":
/*!****************************************!*\
  !*** ./src/Edit/services/WsService.js ***!
  \****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/**
 * Services: Ws Service.
 *
 * A service which determines the `W` according to WordLift's configuration.
 *
 * @since 3.11.0
 */
class WsService {
  /**
   * Get the `W` (who, where, when, what) corresponding to the provided
   * entity.
   *
   * @since 3.11.0
   *
   * @param {Object} entity The entity.
   * @returns {string} The W, or 'unknown' if there's no match.
   */
  getW(entity) {
    return window['_wlMetaBoxSettings'].settings.classificationBoxes.reduce((acc, box) => -1 === box.registeredTypes.indexOf(entity.mainType) ? acc : box.id, "unknown");
  }

} // Finally export the `WsService`.


/* harmony default export */ __webpack_exports__["default"] = (new WsService());

/***/ }),

/***/ "./src/Edit/services/link/LinkService.js":
/*!***********************************************!*\
  !*** ./src/Edit/services/link/LinkService.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return LinkService; });
/* harmony import */ var _EditorService__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../EditorService */ "./src/Edit/services/EditorService.js");
/* harmony import */ var _LinkServiceInterface__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LinkServiceInterface */ "./src/Edit/services/link/LinkServiceInterface.js");
/**
 * Services: Link Service.
 *
 * A service which handles the link/no link attribute for entity's occurrences.
 *
 * @since 3.11.0
 */

/**
 * Internal dependencies
 */


/**
 * Define the `LinkService` class.
 *
 * @since 3.11.0
 */

class LinkService extends _LinkServiceInterface__WEBPACK_IMPORTED_MODULE_1__["LinkServiceInterface"] {
  /**
   * Create an `LinkService` instance.
   *
   * @since 3.13.0
   */
  constructor() {
    // Set the `link by default` setting.
    super();
    this.linkByDefault = "1" === wlSettings.link_by_default;
  }
  /**
   * Set the link flag on the provided `occurrences`.
   *
   * @since 3.11.0
   * @param {Array} occurrences An array of occurrences ids (which match dom
   *     elements).
   * @param {boolean} value True to enable linking or false to disable it.
   */


  setLink(occurrences, value) {
    // If the request is to enable links, remove the `wl-no-link` class on
    // all the occurrences.
    if (value) {
      occurrences.forEach(x => this.setYesLink(x));
    } else {
      // If the request is to disable links, add the `wl-no-link` class to
      // all occurrences.
      occurrences.forEach(x => this.setNoLink(x));
    }
  }
  /**
   * Switch the link on.
   *
   * @since 3.13.0
   * @param {object} elem A DOM element.
   */


  setYesLink(elem) {
    const dom = _EditorService__WEBPACK_IMPORTED_MODULE_0__["default"].get().dom;
    dom.removeClass(elem, "wl-no-link");
    dom.addClass(elem, "wl-link");
  }
  /**
   * Switch the link off.
   *
   * @since 3.13.0
   * @param {object} elem A DOM element.
   */


  setNoLink(elem) {
    const dom = _EditorService__WEBPACK_IMPORTED_MODULE_0__["default"].get().dom;
    dom.removeClass(elem, "wl-link");
    dom.addClass(elem, "wl-no-link");
  }
  /**
   * Get the link flag given the provided `occurrences`. A link flag is
   * considered true when at least one occurrences enables linking.
   *
   * @since 3.11.0
   * @param {Array} occurrences An array of occurrences dom ids.
   * @return {boolean} True if at least one occurrences enables linking,
   *     otherwise false.
   */


  getLink(occurrences) {
    const ed = _EditorService__WEBPACK_IMPORTED_MODULE_0__["default"].get();

    if (ed) {
      // Handle classic editor
      return occurrences.reduce((acc, id) => {
        const dom = ed.dom;
        return acc || (this.linkByDefault ? !dom.hasClass(id, "wl-no-link") : dom.hasClass(id, "wl-link"));
      }, false);
    } else {
      // Handle block editor
      const divElem = document.createElement("div");
      divElem.innerHTML = wp.data.select("core/editor").getBlocks().map(block => block.originalContent).join();
      return occurrences.reduce((acc, id) => {
        return acc || (this.linkByDefault ? !(divElem.querySelector(`[id="${id}"]`) && divElem.querySelector(`[id="${id}"]`).classList.contains("wl-no-link")) : divElem.querySelector(`[id="${id}"]`) && divElem.querySelector(`[id="${id}"]`).classList.contains("wl-link"));
      }, false);
    }
  }

}

/***/ }),

/***/ "./src/Edit/services/link/LinkServiceFactory.js":
/*!******************************************************!*\
  !*** ./src/Edit/services/link/LinkServiceFactory.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return LinkServiceFactory; });
/* harmony import */ var _LinkService__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LinkService */ "./src/Edit/services/link/LinkService.js");
/* harmony import */ var _NoAnnotationLinkService__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./NoAnnotationLinkService */ "./src/Edit/services/link/NoAnnotationLinkService.js");
/**
 * @since 3.32.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class provides link service based on the analysis enabled.
 */


class LinkServiceFactory {
  /**
   * @return LinkServiceInterface
   * @param linkByDefault
   */
  static getInstance() {
    if (this.isNoEditorAnalysisActive()) {
      return new _NoAnnotationLinkService__WEBPACK_IMPORTED_MODULE_1__["default"]();
    }

    return new _LinkService__WEBPACK_IMPORTED_MODULE_0__["default"]();
  }

  static isNoEditorAnalysisActive() {
    return wlSettings !== undefined && wlSettings.analysis !== undefined && wlSettings.analysis.isNoEditorAnalysisActive !== undefined && wlSettings.analysis.isNoEditorAnalysisActive === true;
  }

}

/***/ }),

/***/ "./src/Edit/services/link/LinkServiceInterface.js":
/*!********************************************************!*\
  !*** ./src/Edit/services/link/LinkServiceInterface.js ***!
  \********************************************************/
/*! exports provided: LinkServiceInterface */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "LinkServiceInterface", function() { return LinkServiceInterface; });
/**
 *  @author Naveen Muthusamy <naveen@wordlift.io>
 *  @since 3.32.6
 *  @abstract
 */
class LinkServiceInterface {
  /**
   * Set the link flag on the provided `occurrences`.
   *
   * @since 3.11.0
   * @param {Array} occurrences An array of occurrences ids (which match dom
   *     elements).
   * @param {boolean} value True to enable linking or false to disable it.
   * @abstract
   */
  setLink(occurrences, value) {// Child class should implement this.
  }
  /**
   * Switch the link on.
   *
   * @since 3.13.0
   * @param {object} elem A DOM element.
   * @abstract
   */


  setYesLink(elem) {// Child class should implement this.
  }
  /**
   * Switch the link off.
   *
   * @since 3.13.0
   * @param {object} elem A DOM element.
   * @abstract
   */


  setNoLink(elem) {// Child class should implement this.
  }
  /**
   * Get the link flag given the provided `occurrences`. A link flag is
   * considered true when at least one occurrences enables linking.
   *
   * @since 3.11.0
   * @param {Array} occurrences An array of occurrences dom ids.
   * @return {boolean} True if at least one occurrences enables linking,
   *     otherwise false.
   * @abstract
   */


  getLink(occurrences) {// Child class should implement this.
  }

}

/***/ }),

/***/ "./src/Edit/services/link/NoAnnotationLinkService.js":
/*!***********************************************************!*\
  !*** ./src/Edit/services/link/NoAnnotationLinkService.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return NoAnnotationLinkService; });
/* harmony import */ var _LinkServiceInterface__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LinkServiceInterface */ "./src/Edit/services/link/LinkServiceInterface.js");
/**
 * Services: Link Service.
 *
 * A service which handles the link/no link attribute for entity's occurrences.
 *
 * @since 3.32.6
 */

/**
 * Internal dependencies
 */

/**
 * Define the `NoAnnotationLinkService` class, this shouldn't perform any functions.
 *
 * @since 3.32.6
 */

class NoAnnotationLinkService extends _LinkServiceInterface__WEBPACK_IMPORTED_MODULE_0__["LinkServiceInterface"] {
  /**
   * Create an `LinkService` instance.
   *
   * @since 3.13.0
   */
  constructor() {
    super();
  }
  /**
   * Set the link flag on the provided `occurrences`.
   *
   * @since 3.11.0
   * @param {Array} occurrences An array of occurrences ids (which match dom
   *     elements).
   * @param {boolean} value True to enable linking or false to disable it.
   */


  setLink(occurrences, value) {// Perform no link action
  }
  /**
   * Switch the link on.
   *
   * @since 3.13.0
   * @param {object} elem A DOM element.
   */


  setYesLink(elem) {// Perform no link action
  }
  /**
   * Switch the link off.
   *
   * @since 3.13.0
   * @param {object} elem A DOM element.
   */


  setNoLink(elem) {// Perform no link action
  }
  /**
   * Get the link flag given the provided `occurrences`. A link flag is
   * considered true when at least one occurrences enables linking.
   *
   * @since 3.11.0
   * @param {Array} occurrences An array of occurrences dom ids.
   * @return {boolean} True if at least one occurrences enables linking,
   *     otherwise false.
   */


  getLink(occurrences) {// Perform no link action
  }

}

/***/ }),

/***/ "./src/Edit/stores/sagas.js":
/*!**********************************!*\
  !*** ./src/Edit/stores/sagas.js ***!
  \**********************************/
/*! exports provided: getMainType, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getMainType", function() { return getMainType; });
/* harmony import */ var redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux-saga/effects */ "./node_modules/redux-saga/dist/redux-saga-effects-npm-proxy.esm.js");
/* harmony import */ var _constants_ActionTypes__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../constants/ActionTypes */ "./src/Edit/constants/ActionTypes.js");
/* harmony import */ var _angular_EditPostWidgetController__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../angular/EditPostWidgetController */ "./src/Edit/angular/EditPostWidgetController.js");
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./selectors */ "./src/Edit/stores/selectors.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../actions */ "./src/Edit/actions/index.js");
/* harmony import */ var _components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../components/AddEntity/actions */ "./src/Edit/components/AddEntity/actions.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _services_link_LinkServiceFactory__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../services/link/LinkServiceFactory */ "./src/Edit/services/link/LinkServiceFactory.js");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_8__);
/**
 * This file contains the side effects managed via redux-sagas.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */









/**
 * Handle the {@link TOGGLE_ENTITY} action.
 *
 *  @param {{entity:{id}}} payload A payload containing an entity.
 */

function* toggleEntity(payload) {
  const entity = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["select"])(_selectors__WEBPACK_IMPORTED_MODULE_3__["getEntity"], payload.entity.id);
  Object(_angular_EditPostWidgetController__WEBPACK_IMPORTED_MODULE_2__["default"])().$apply(Object(_angular_EditPostWidgetController__WEBPACK_IMPORTED_MODULE_2__["default"])().onSelectedEntityTile(entity));
}

function* toggleLink({
  entity
}) {
  const linkService = _services_link_LinkServiceFactory__WEBPACK_IMPORTED_MODULE_7__["default"].getInstance(); // Toggle the link/no link on entity's occurrences.
  // Toggle the link on the occurrences.

  linkService.setLink(entity.occurrences, !entity.link);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["put"])(Object(_actions__WEBPACK_IMPORTED_MODULE_4__["toggleLinkSuccess"])({
    id: entity.id,
    link: linkService.getLink(entity.occurrences)
  }));
}

function* setCurrentEntity({
  entity
}) {
  // Call the `EditPostWidgetController` to set the current entity.
  Object(_angular_EditPostWidgetController__WEBPACK_IMPORTED_MODULE_2__["default"])().$apply(Object(_angular_EditPostWidgetController__WEBPACK_IMPORTED_MODULE_2__["default"])().setCurrentEntity(entity, "entity"));
}

function* addEntity({
  payload
}) {
  const ctrl = Object(_angular_EditPostWidgetController__WEBPACK_IMPORTED_MODULE_2__["default"])();
  ctrl.$apply(() => {
    // Create the text annotation.
    ctrl.setCurrentEntity(); // Update the entity data.

    ctrl.currentEntity.description = payload.descriptions[0];
    ctrl.currentEntity.id = payload.id;
    ctrl.currentEntity.images = payload.images;
    ctrl.currentEntity.label = payload.label;
    ctrl.currentEntity.mainType = getMainType(payload.types);
    ctrl.currentEntity.types = payload.types;
    ctrl.currentEntity.sameAs = payload.sameAss; // Save the entity.

    ctrl.storeCurrentEntity();
  });
  Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_8__["doAction"])("unstable_wordlift.closeEntitySelect");
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["put"])(Object(_components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_5__["addEntitySuccess"])());
}

function* createEntity({
  payload
}) {
  const ctrl = Object(_angular_EditPostWidgetController__WEBPACK_IMPORTED_MODULE_2__["default"])();
  ctrl.$apply(ctrl.setCurrentEntity(undefined, undefined, payload));
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["put"])(Object(_components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_5__["createEntitySuccess"])());
}

const getMainType = types => {
  for (let i = 0; i < window._wlEntityTypes.length; i++) {
    const type = window._wlEntityTypes[i];
    if (-1 < types.indexOf(type.uri)) return type.slug;
  }

  return "thing";
};
let popover;

function* handleEditorSelectionChanged({
  payload
}) {
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["delay"])(300);
  console.log("handleEditorSelectionChanged", payload);
  const editor = payload.editor; // Get the selection. Bail out is the selection is collapsed (is just a caret).

  const selection = editor.selection;

  if (selection.isCollapsed() || "" === selection.getContent({
    format: "text"
  })) {
    if (popover) popover.unmount();
    return;
  } // Get the selection range and bail out if it's null.


  const range = selection.getRng();

  if (null == range) {
    if (popover) popover.unmount();
    return;
  } // Get the editor's selection bounding rect. The rect's coordinates are relative to TinyMCE's editor's iframe.


  const editorRect = range.getBoundingClientRect(); // Get TinyMCE's iframe element's bounding rect.

  const iframe = editor.iframeElement;
  const iframeRect = iframe.getBoundingClientRect(); // Calculate our target rect by summing the iframe and the editor rects along with the window's scroll positions.

  const rect = {
    top: iframeRect.top + editorRect.top + window.scrollY,
    right: iframeRect.left + editorRect.right + window.scrollX,
    bottom: iframeRect.top + editorRect.bottom + window.scrollY,
    left: iframeRect.left + editorRect.left + window.scrollX
  };
}
/**
 * Connect the side effects.
 */


function* sagas() {
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"])(_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_1__["TOGGLE_ENTITY"], toggleEntity);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"])(_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_1__["TOGGLE_LINK"], toggleLink);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"])(_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_1__["SET_CURRENT_ENTITY"], setCurrentEntity);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"])(_components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_5__["addEntityRequest"], addEntity);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"])(_components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_5__["createEntityRequest"], createEntity);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeLatest"])(_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_1__["EDITOR_SELECTION_CHANGED"], handleEditorSelectionChanged);
}

/* harmony default export */ __webpack_exports__["default"] = (sagas);

/***/ }),

/***/ "./src/Edit/stores/selectors.js":
/*!**************************************!*\
  !*** ./src/Edit/stores/selectors.js ***!
  \**************************************/
/*! exports provided: getEntity */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getEntity", function() { return getEntity; });
/**
 * This file defines the selectors.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.4
 */

/**
 * Get an entity given its item id.
 */
const getEntity = (state, id) => state.entities.get(id);

/***/ }),

/***/ "./src/block-editor/api/block-factory.js":
/*!***********************************************!*\
  !*** ./src/block-editor/api/block-factory.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return BlockFactory; });
/* harmony import */ var _block_types_text_block__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./block-types/text-block */ "./src/block-editor/api/block-types/text-block.js");
/* harmony import */ var _block_types_list_block__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./block-types/list-block */ "./src/block-editor/api/block-types/list-block.js");
/* harmony import */ var _block_types_table_block_table_block__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./block-types/table-block/table-block */ "./src/block-editor/api/block-types/table-block/table-block.js");
/**
 * Factory to create different types of the blocks.
 */



class BlockFactory {
  static getBlock(block, dispatch, start) {
    if ("core/paragraph" === block.name || "core/freeform" === block.name) {
      return new _block_types_text_block__WEBPACK_IMPORTED_MODULE_0__["default"](block, dispatch, start);
    } else if ("core/list" === block.name) {
      return new _block_types_list_block__WEBPACK_IMPORTED_MODULE_1__["default"](block, dispatch, start);
    } else if ("core/table" === block.name) {
      return new _block_types_table_block_table_block__WEBPACK_IMPORTED_MODULE_2__["default"](block, dispatch, start);
    }
  }

}

/***/ }),

/***/ "./src/block-editor/api/block-types/abstract-block.js":
/*!************************************************************!*\
  !*** ./src/block-editor/api/block-types/abstract-block.js ***!
  \************************************************************/
/*! exports provided: AbstractBlock */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "AbstractBlock", function() { return AbstractBlock; });
/**
 *  @abstract
 */
class AbstractBlock {
  constructor(block, dispatch, start) {
    this._block = block;
    this._dispatch = dispatch;
    this._start = start;
    this._end = -1;
    this._dirty = false;
  }

  get block() {
    return this._block;
  }

  set block(block) {
    this._block = block;
  }

  get content() {
    return this._content;
  }

  set content(value) {
    this._content = value;
    this._dirty = true;
  }

  get end() {
    return this._end;
  }

  set end(end) {
    this._end = end;
  }

  get clientId() {
    return this._block.clientId;
  }

  get start() {
    return this._start;
  }

  insertHtml(at, fragment) {
    // Insert the HTML.
    const newContent = this.content.substring(0, at) + fragment + this.content.substring(at); // Bail out if the content didn't change.

    if (newContent === this.content) return;
    this.content = newContent;
    this._dirty = true;
  }

  replace(pattern, replacement) {
    const newContent = this.content.replace(pattern, replacement); // Bail out if the content didn't change.

    if (newContent === this.content) return;
    this.content = newContent;
    this._dirty = true;
  }
  /**
   *  @abstract
   */


  apply() {}

}

/***/ }),

/***/ "./src/block-editor/api/block-types/list-block.js":
/*!********************************************************!*\
  !*** ./src/block-editor/api/block-types/list-block.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ListBlock; });
/* harmony import */ var _abstract_block__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract-block */ "./src/block-editor/api/block-types/abstract-block.js");

/**
 * List block represents the core/list
 * in the gutenberg editor.
 */

class ListBlock extends _abstract_block__WEBPACK_IMPORTED_MODULE_0__["AbstractBlock"] {
  constructor(block, dispatch, start) {
    super(block, dispatch, start);
    this._content = block.attributes.values;
  }

  apply() {
    if (this._dirty) {
      console.debug("Block.apply", {
        clientId: this.clientId,
        content: this.content,
        dispatch: this._dispatch,
        updateBlockAttributes: this._dispatch.updateBlockAttributes
      }); // WP 5.0 returns undefined to this call.

      this._dispatch.updateBlockAttributes(this.clientId, {
        values: this.content
      });

      this._dirty = false;
    }
  }

}

/***/ }),

/***/ "./src/block-editor/api/block-types/table-block/table-block.js":
/*!*********************************************************************!*\
  !*** ./src/block-editor/api/block-types/table-block/table-block.js ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TableBlock; });
/* harmony import */ var _abstract_block__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../abstract-block */ "./src/block-editor/api/block-types/abstract-block.js");
/* harmony import */ var _table_section__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./table-section */ "./src/block-editor/api/block-types/table-block/table-section.js");
/* harmony import */ var _table_row__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./table-row */ "./src/block-editor/api/block-types/table-block/table-row.js");
/* harmony import */ var _table__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./table */ "./src/block-editor/api/block-types/table-block/table.js");




/**
 * Table block represents the core/table
 * in the gutenberg editor.
 */

class TableBlock extends _abstract_block__WEBPACK_IMPORTED_MODULE_0__["AbstractBlock"] {
  constructor(block, dispatch, start) {
    super(block, dispatch, start);
    this.table = new _table__WEBPACK_IMPORTED_MODULE_3__["default"](block.attributes.head, block.attributes.body, block.attributes.foot);
    this.content = this.table.getAnalysisHtml();
  }

  apply() {
    if (this._dirty) {
      // delimit and update the blocks.
      this.table.updateFromAnalysisHtml(this.content); // WP 5.0 returns undefined to this call.

      this._dispatch.updateBlockAttributes(this.clientId, this.table.getAttributeData());

      this._dirty = false;
    }
  }

}

/***/ }),

/***/ "./src/block-editor/api/block-types/table-block/table-column.js":
/*!**********************************************************************!*\
  !*** ./src/block-editor/api/block-types/table-block/table-column.js ***!
  \**********************************************************************/
/*! exports provided: TABLE_COLUMN_DELIMITER, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TABLE_COLUMN_DELIMITER", function() { return TABLE_COLUMN_DELIMITER; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TableColumn; });
/**
 * Represents a single column of table
 * @since 3.29.1
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
const TABLE_COLUMN_DELIMITER = '<wl-table-column-delimiter/>';
class TableColumn {
  /**
   * @param columnData A single column data
   */
  constructor(columnData) {
    this._data = columnData;
  }

  get data() {
    return this._data;
  }

  getAnalysisHtml() {
    let content = "";

    if (this.data && this.data.content) {
      content = this.data.content;
    }

    return content + TABLE_COLUMN_DELIMITER;
  }

  getAttributeData() {
    return this.data;
  }
  /**
   *
   * @param html {String}
   */


  updateFromAnalysisHtml(html) {
    this.data.content = html;
  }

}

/***/ }),

/***/ "./src/block-editor/api/block-types/table-block/table-row.js":
/*!*******************************************************************!*\
  !*** ./src/block-editor/api/block-types/table-block/table-row.js ***!
  \*******************************************************************/
/*! exports provided: TABLE_ROW_DELIMITER, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TABLE_ROW_DELIMITER", function() { return TABLE_ROW_DELIMITER; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TableRow; });
/* harmony import */ var _table_column__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./table-column */ "./src/block-editor/api/block-types/table-block/table-column.js");
/**
 * Represents a single row of table.
 * @since 3.29.1
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

const TABLE_ROW_DELIMITER = '<wl-table-row-delimiter/>';
class TableRow {
  /**
   * @param rowData An array of table rows data
   */
  constructor(rowData) {
    this.columns = [];

    if (rowData && rowData.cells && Array.isArray(rowData.cells)) {
      this.columns = rowData.cells.map(column => {
        return new _table_column__WEBPACK_IMPORTED_MODULE_0__["default"](column);
      });
    }
  }

  getAnalysisHtml() {
    const columns = this.columns.map(column => {
      return column.getAnalysisHtml();
    });
    return columns.join("") + TABLE_ROW_DELIMITER;
  }

  getAttributeData() {
    const columns = [];
    this.columns.map(column => {
      columns.push(column.getAttributeData());
    });
    return {
      "cells": columns
    };
  }
  /**
   *
   * @param html {String}
   */


  updateFromAnalysisHtml(html) {
    // Set the columns after parsing.
    html.split(_table_column__WEBPACK_IMPORTED_MODULE_0__["TABLE_COLUMN_DELIMITER"]).map((rowHtml, index) => {
      if (this.columns[index]) {
        this.columns[index].updateFromAnalysisHtml(rowHtml);
      }
    });
  }

}

/***/ }),

/***/ "./src/block-editor/api/block-types/table-block/table-section.js":
/*!***********************************************************************!*\
  !*** ./src/block-editor/api/block-types/table-block/table-section.js ***!
  \***********************************************************************/
/*! exports provided: TABLE_SECTION_DELIMITER, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TABLE_SECTION_DELIMITER", function() { return TABLE_SECTION_DELIMITER; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TableSection; });
/* harmony import */ var _table_row__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./table-row */ "./src/block-editor/api/block-types/table-block/table-row.js");
/**
 * Represents a single section of table.
 * @since 3.29.1
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

const TABLE_SECTION_DELIMITER = '<wl-table-section-delimiter/>';
class TableSection {
  /**
   * @param tableSectionData An array of table rows data
   */
  constructor(tableSectionData) {
    this._rows = [];

    if (tableSectionData && Array.isArray(tableSectionData)) {
      this._rows = tableSectionData.map(row => {
        return new _table_row__WEBPACK_IMPORTED_MODULE_0__["default"](row);
      });
    }
  }

  get rows() {
    return this._rows;
  }

  set rows(rows) {
    this._rows = rows;
  }

  getAnalysisHtml() {
    const rows = this.rows.map(row => {
      return row.getAnalysisHtml();
    });
    return rows.join("") + TABLE_SECTION_DELIMITER;
  }
  /**
   *
   * @param html {String}
   */


  updateFromAnalysisHtml(html) {
    // Set the rows after parsing.
    html.split(_table_row__WEBPACK_IMPORTED_MODULE_0__["TABLE_ROW_DELIMITER"]).map((rowHtml, index) => {
      if (this.rows[index]) {
        this.rows[index].updateFromAnalysisHtml(rowHtml);
      }
    });
  }

  getAttributeData() {
    const sectionRows = [];
    this.rows.map(row => {
      sectionRows.push(row.getAttributeData());
    });
    return sectionRows;
  }

}

/***/ }),

/***/ "./src/block-editor/api/block-types/table-block/table.js":
/*!***************************************************************!*\
  !*** ./src/block-editor/api/block-types/table-block/table.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Table; });
/* harmony import */ var _table_section__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./table-section */ "./src/block-editor/api/block-types/table-block/table-section.js");

class Table {
  constructor(head, body, foot) {
    this.sections = [new _table_section__WEBPACK_IMPORTED_MODULE_0__["default"](head), new _table_section__WEBPACK_IMPORTED_MODULE_0__["default"](body), new _table_section__WEBPACK_IMPORTED_MODULE_0__["default"](foot)];
  }

  getAnalysisHtml() {
    return this.sections.map(section => {
      return section.getAnalysisHtml();
    }).join("");
  }

  updateFromAnalysisHtml(html) {
    html.split(_table_section__WEBPACK_IMPORTED_MODULE_0__["TABLE_SECTION_DELIMITER"]).map((section, index) => {
      if (this.sections[index]) {
        this.sections[index].updateFromAnalysisHtml(section);
      }
    });
  }

  getAttributeData() {
    return {
      head: this.sections[0].getAttributeData(),
      body: this.sections[1].getAttributeData(),
      foot: this.sections[2].getAttributeData()
    };
  }

}

/***/ }),

/***/ "./src/block-editor/api/block-types/text-block.js":
/*!********************************************************!*\
  !*** ./src/block-editor/api/block-types/text-block.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TextBlock; });
/* harmony import */ var _abstract_block__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract-block */ "./src/block-editor/api/block-types/abstract-block.js");

/**
 * Text block represents the core/paragraph and also the core/freeform block
 * in the gutenberg editor.
 */

class TextBlock extends _abstract_block__WEBPACK_IMPORTED_MODULE_0__["AbstractBlock"] {
  constructor(block, dispatch, start) {
    super(block, dispatch, start);
    this._content = block.attributes.content;
  }

  apply() {
    if (this._dirty) {
      console.debug("Block.apply", {
        clientId: this.clientId,
        content: this.content,
        dispatch: this._dispatch,
        updateBlockAttributes: this._dispatch.updateBlockAttributes
      }); // WP 5.0 returns undefined to this call.

      this._dispatch.updateBlockAttributes(this.clientId, {
        content: this.content
      });

      this._dirty = false;
    }
  }

}

/***/ }),

/***/ "./src/block-editor/api/block.js":
/*!***************************************!*\
  !*** ./src/block-editor/api/block.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Block; });
class Block {
  constructor(block, dispatch, content, start = 0, end = -1) {
    this._block = block;
    this._dispatch = dispatch;
    this._content = content;
    this._start = start;
    this._end = 0 <= end ? end : block.attributes.content.length;
    this._dirty = false;
  }

  get content() {
    return this._content;
  }

  set content(value) {
    this._content = value;
    this._dirty = true;
  }

  get clientId() {
    return this._block.clientId;
  }

  get start() {
    return this._start;
  }

  get end() {
    return this._end;
  }

  insertHtml(at, fragment) {
    // Insert the HTML.
    const newContent = this.content.substring(0, at) + fragment + this.content.substring(at); // Bail out if the content didn't change.

    if (newContent === this.content) return;
    this.content = newContent;
    this._dirty = true;
  }

  replace(pattern, replacement) {
    const newContent = this.content.replace(pattern, replacement); // Bail out if the content didn't change.

    if (newContent === this.content) return;
    this.content = newContent;
    this._dirty = true;
  }

  apply() {
    if (this._dirty) {
      console.debug("Block.apply", {
        clientId: this.clientId,
        content: this.content,
        dispatch: this._dispatch,
        updateBlockAttributes: this._dispatch.updateBlockAttributes
      }); // WP 5.0 returns undefined to this call.

      this._dispatch.updateBlockAttributes(this.clientId, {
        content: this.content
      });

      this._dirty = false;
    }
  }

}

/***/ }),

/***/ "./src/block-editor/api/blocks.js":
/*!****************************************!*\
  !*** ./src/block-editor/api/blocks.js ***!
  \****************************************/
/*! exports provided: Blocks */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Blocks", function() { return Blocks; });
/* harmony import */ var _block__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./block */ "./src/block-editor/api/block.js");
/* harmony import */ var _block_types_text_block__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./block-types/text-block */ "./src/block-editor/api/block-types/text-block.js");
/* harmony import */ var _block_factory__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./block-factory */ "./src/block-editor/api/block-factory.js");



/**
 *
 * @param {{name,innerBlocks}[]} blocks
 * @param predicate
 * @param mapper
 * @param accumulator
 * @returns {Array}
 */

const collectBlocks = function (blocks, predicate = () => true, accumulator = []) {
  blocks.forEach(block => {
    // Add the block to the collection if it satisfies the predicate.
    if (predicate(block)) {
      accumulator.push(block);
    } // Collect inner blocks.


    collectBlocks(block.innerBlocks, predicate, accumulator);
  });
  return accumulator;
};
/**
 *
 */


class Blocks {
  /**
   *
   * @param blocks
   * @param dispatch
   */
  constructor(blocks, dispatch) {
    this._blockSeparator = ".\n";
    this._blockSeparatorLength = this._blockSeparator.length;
    /** @var {Block[]} */

    this._blocks = [];
    let cursor = 0;
    this._html = blocks.map(block => {
      const start = cursor;
      const blockObj = _block_factory__WEBPACK_IMPORTED_MODULE_2__["default"].getBlock(block, dispatch, start);
      const content = blockObj.content;
      cursor += content.length + this._blockSeparatorLength;
      blockObj.end = cursor;

      this._blocks.push(blockObj);

      return content;
    }).join(this._blockSeparator);
    console.debug("Blocks.c`tor", {
      html: this._html,
      blocks: this._blocks
    });
  }

  *[Symbol.iterator]() {
    yield this._blocks;
  }

  get html() {
    return this._html;
  }
  /**
   * Get the block index for the specified absolute position.
   *
   * @param {number} position The absolute position.
   * @returns {{false}|number} The block index (zero-based) or false if not found.
   */


  getBlockIndex(position) {
    // Cycle through the blocks until we found the one for the provided position.
    for (let i = 0; i < this._blocks.length; i++) {
      const block = this._blocks[i];

      if (position >= block.start && position < block.end) {
        return i;
      }
    }

    return false;
  }

  getBlock(position) {
    const idx = this.getBlockIndex(position);
    if (false === idx) return false;
    return this._blocks[idx];
  }

  replace(pattern, replacement) {
    for (const block of this._blocks) block.replace(pattern, replacement);
  }

  apply() {
    for (const block of this._blocks) block.apply();
  }

  static create(blocks, dispatch) {
    return new this(collectBlocks(blocks, block => "core/paragraph" === block.name || "core/freeform" === block.name || "core/list" === block.name || "core/table" === block.name), dispatch);
  }

}

/***/ }),

/***/ "./src/block-editor/api/classic-editor-block.js":
/*!******************************************************!*\
  !*** ./src/block-editor/api/classic-editor-block.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _formats_register_format_type_wordlift_annotation__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../formats/register-format-type-wordlift-annotation */ "./src/block-editor/formats/register-format-type-wordlift-annotation.js");
/**
 * The classic editor block class is designed to create entities on the
 * block editor, this block cant be altered by using applyFormat.
 *
 * see here: https://github.com/insideout10/wordlift-plugin/issues/1039
 * @since 3.26.1
 */


class ClassicEditorBlock {
  /**
   * @param blockId
   * @param attributeName {string} Attribute name of the classic editor block, usually defaults
   * to content
   * @param content  {string} The HTML Content value of this block.
   */
  constructor(blockId, content, attributeName = "content") {
    this._content = content;
    this._attributeName = attributeName;
    this._blockId = blockId;
  }
  /**
   * This method replaces the string with the annotation id,
   * it does not hard code the html, it generates the wrapper html
   * from the format registered for annotation.
   * @param stringToBeFound The string which needs to be wrapped with span tags.
   * @param attrs The extra attributes which needs to be present in html tag.
   */


  replaceWithAnnotation(stringToBeFound, attrs = {}) {
    const {
      openTag,
      closeTag
    } = this.generateOpenAndCloseTag(_formats_register_format_type_wordlift_annotation__WEBPACK_IMPORTED_MODULE_0__["annotationSettings"], attrs);
    this._content = this._content.replace(stringToBeFound, openTag + stringToBeFound + closeTag);
  }

  getContent() {
    return this._content;
  }

  generateOpenAndCloseTag(annotationSettings, attributes) {
    let closeTag = `</${annotationSettings.tagName}>`; // We add the class name since it is already present in the registration.

    if (attributes.class !== undefined) {
      attributes.class = attributes.class + " " + annotationSettings.className;
    } // create a placeholder dom element


    const el = document.createElement(annotationSettings.tagName); // Process the settings and add attributes.
    // Loop through all annotation attributes and create value in the tag if they have it.

    let annotationAttrs = annotationSettings.attributes || {};

    for (let key in annotationAttrs) {
      const value = attributes[key] || "";

      if (value !== "") {
        el.setAttribute(key, value);
      }
    } // Remove the close tag, since we have no text content in the element, that
    // should be open tag.


    let openTag = el.outerHTML.replace(closeTag, "");
    return {
      openTag,
      closeTag
    };
  }
  /**
   * Update the block editor after replacing the content.
   */


  update() {
    // Up to WP 5.4 Classic editor store the html content in content attribute
    const attrName = this._attributeName;
    const attrs = {};
    attrs[attrName] = this._content;
    wp.data.dispatch("core/editor").updateBlockAttributes(this._blockId, attrs);
  }

}

/* harmony default export */ __webpack_exports__["default"] = (ClassicEditorBlock);

/***/ }),

/***/ "./src/block-editor/api/create-entity.js":
/*!***********************************************!*\
  !*** ./src/block-editor/api/create-entity.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/**
 * This file provides an API to create entities using the WordPress wp-json end-point.
 *
 * In order to work the API looks for the `_wlBlockEditorSettings` global object.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */
// Check that our settings are present.
if ("undefined" === typeof global["_wlBlockEditorSettings"]) {
  console.warn("create-entity requires a global `_wlBlockEditorSettings` to be defined.");
} // Create a reference to the settings.


const settings = global["_wlBlockEditorSettings"];
/**
 * Create an entity.
 *
 * @param {{title, status, excerpt}} value The entity data.
 * @returns {Promise<Response>}
 */

/* harmony default export */ __webpack_exports__["default"] = (value => {
  return fetch(`${settings.root}wp/v2/entities`, {
    method: "POST",
    headers: {
      "content-type": "application/json",
      "X-WP-Nonce": settings.nonce
    },
    body: JSON.stringify(value)
  }).then(response => response.json());
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./src/block-editor/api/editor-ops.js":
/*!********************************************!*\
  !*** ./src/block-editor/api/editor-ops.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return EditorOps; });
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./blocks */ "./src/block-editor/api/blocks.js");
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


class EditorOps {
  constructor(editor) {
    // Holds the annotations to blocks mappings.
    this._annotations = {};
    this._editor = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__["select"])(editor);
    /** @field {Blocks} _blocks */

    this._blocks = _blocks__WEBPACK_IMPORTED_MODULE_1__["Blocks"].create(this._editor.getBlocks(), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__["dispatch"])(editor));
  }

  buildAnalysisRequest(language, exclude, canCreateEntities) {
    return {
      contentLanguage: language,
      contentType: "text/html",
      scope: canCreateEntities ? "all" : "local",
      version: "1.0.0",
      content: this._blocks.html,
      exclude: exclude
    };
  }

  insertAnnotation(id, start, end) {
    const block = this._blocks.getBlock(start);

    const endBlock = this._blocks.getBlock(end); // @@todo: add support for different blocks, i.e. an annotation which crosses boundaries.


    if (false === block || false === endBlock || block !== endBlock) return false;
    const relativeStart = start - block.start,
          relativeEnd = end - block.start;
    console.log("EditorOps.insertAnnotation", {
      id,
      start,
      end,
      clientId: block.clientId
    }); // Insert the block only if not found.

    if (-1 === block.content.indexOf(`<span id="${id}" `)) {
      block.insertHtml(relativeEnd, "</span>");
      block.insertHtml(relativeStart, `<span id="${id}" class="textannotation">`);
    }

    this._annotations[id] = block;
  }

  applyChanges() {
    this._blocks.apply();
  }

}

/***/ }),

/***/ "./src/block-editor/api/utils.js":
/*!***************************************!*\
  !*** ./src/block-editor/api/utils.js ***!
  \***************************************/
/*! exports provided: collectBlocks, mergeArray, makeEntityAnnotationsSelector */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "collectBlocks", function() { return collectBlocks; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "mergeArray", function() { return mergeArray; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "makeEntityAnnotationsSelector", function() { return makeEntityAnnotationsSelector; });
/**
 *
 * @param accumulator
 * @param {{name, innerBlocks}[]} blocks
 * @param callback
 * @returns {*}
 */
function collectBlocks(accumulator, blocks, callback = x => x) {
  blocks.forEach(block => {
    if ("core/paragraph" === block.name || "core/freeform" === block.name) {
      accumulator.push(callback(block));
    }

    collectBlocks(accumulator, block.innerBlocks);
  });
  return accumulator;
}
function mergeArray(a1, a2) {
  const newArray = a1.splice(0);

  for (let i = 0; i < a2.length; i++) {
    if (-1 === a1.indexOf(a2[i])) newArray.push(a2[i]);
  }

  return newArray;
}
function makeEntityAnnotationsSelector(entity) {
  return Object.keys(entity.annotations).join("|");
}

/***/ }),

/***/ "./src/block-editor/blocks/blocks.scss":
/*!*********************************************!*\
  !*** ./src/block-editor/blocks/blocks.scss ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "./src/block-editor/blocks/icons/chord.svg":
/*!*************************************************!*\
  !*** ./src/block-editor/blocks/icons/chord.svg ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var React = __webpack_require__(/*! react */ "react");

function Chord (props) {
    return React.createElement("svg",props,[React.createElement("title",{"key":0},"Chord"),React.createElement("g",{"id":"Page-1","stroke":"none","strokeWidth":"1","fill":"none","fillRule":"evenodd","key":1},React.createElement("g",{"id":"Entities-Timeline-Copy","transform":"translate(-794.000000, -215.000000)"},React.createElement("g",{"id":"Chord","transform":"translate(794.000000, 215.000000)"},[React.createElement("rect",{"id":"Rectangle-Copy-6","x":"0","y":"0","width":"42","height":"42","key":0}),React.createElement("g",{"id":"chord-copy","transform":"translate(4.000000, 5.000000)","fill":"#000000","fillRule":"nonzero","key":1},React.createElement("path",{"d":"M33.8171937,11.4337945 C33.5335968,10.6106719 33.1116601,9.83596838 32.7173913,9.05434783 C32.6482213,8.91600791 32.4061265,8.78458498 32.2539526,8.79150198 C32.1225296,8.79841897 31.9565217,8.99209486 31.8942688,9.13735178 C31.8527668,9.24110672 31.9357708,9.42094862 31.9980237,9.55237154 C32.3922925,10.458498 32.8349802,11.3438735 33.18083,12.270751 C33.4644269,13.0385375 33.6304348,13.8547431 33.8517787,14.6501976 C33.8102767,14.6778656 33.7687747,14.6986166 33.7341897,14.7262846 C31.8250988,13.6333992 29.9090909,12.5405138 28.006917,11.4476285 C28.034585,11.3784585 28.041502,11.3507905 28.055336,11.3369565 C29.1274704,10.3893281 30.1926877,9.4416996 31.2648221,8.50098814 C31.3201581,8.45256917 31.4031621,8.41106719 31.479249,8.3972332 C32.0395257,8.25197628 32.1294466,8.01679842 31.7974308,7.54644269 C29.798419,4.70355731 27.1768775,2.64229249 23.9604743,1.34189723 C21.0968379,0.186758893 18.1225296,-0.186758893 15.0513834,0.235177866 C14.7539526,0.276679842 14.5741107,0.415019763 14.5879447,0.726284585 C14.5948617,1.00296443 14.8438735,1.14822134 15.1689723,1.11363636 C15.708498,1.0583004 16.2480237,1.01679842 16.7875494,0.954545455 C17.0088933,0.92687747 17.1126482,1.00988142 17.2164032,1.20355731 C17.9496047,2.61462451 18.6966403,4.0187747 19.4367589,5.4229249 C19.4782609,5.50592885 19.5128458,5.5958498 19.5474308,5.67885375 C18.6482213,5.01482213 17.8181818,4.30237154 16.9881423,3.59683794 C15.9782609,2.73913043 14.9614625,1.89525692 13.951581,1.0444664 C13.9031621,1.00296443 13.8409091,0.954545455 13.8201581,0.89229249 C13.6818182,0.567193676 13.4328063,0.532608696 13.1353755,0.615612648 C7.88537549,2.07509881 4.05335968,5.25 1.65316206,10.1403162 C1.49407115,10.458498 1.55632411,10.6936759 1.80533597,10.8250988 C2.04743083,10.9496047 2.2826087,10.8458498 2.4416996,10.541502 C2.71146245,10.0296443 2.9743083,9.51778656 3.25098814,9.01284585 C3.34782609,8.83992095 3.46541502,8.68083004 3.57608696,8.52173913 C3.60375494,8.53557312 3.62450593,8.54249012 3.65217391,8.55632411 C3.79051383,10.437747 3.92885375,12.326087 4.06719368,14.2075099 C4.0256917,14.2282609 3.98418972,14.2490119 3.94268775,14.2697628 C3.26482213,13.5849802 2.58695652,12.9001976 1.90909091,12.215415 C1.88833992,12.194664 1.85375494,12.1600791 1.85375494,12.1324111 C1.83992095,11.7450593 1.59782609,11.5721344 1.24505929,11.4960474 C1.17588933,11.5790514 1.07905138,11.6620553 1.01679842,11.7658103 C0.954545455,11.8695652 0.919960474,11.9940711 0.878458498,12.1185771 C-0.0138339921,14.8577075 -0.200592885,17.6452569 0.269762846,20.4812253 C0.511857708,21.9407115 0.933794466,23.3517787 1.54940711,24.7005929 C1.70849802,25.0464427 1.95059289,25.1294466 2.23418972,25.0049407 C2.58003953,24.8596838 2.94664032,24.7559289 3.36166008,24.6175889 C3.18873518,25.2677866 3.01581028,25.4822134 2.57312253,25.7312253 C2.33794466,25.8626482 2.26185771,26.0770751 2.38636364,26.31917 C2.50395257,26.5543478 2.64920949,26.7687747 2.7944664,26.9901186 C4.14328063,29.0859684 5.86561265,30.8152174 7.94762846,32.1709486 C8.45948617,32.5029644 8.99209486,32.8003953 9.53162055,33.0909091 C9.83596838,33.256917 10.1472332,33.1600791 10.2094862,32.8764822 C10.3478261,32.2608696 10.9081028,31.9703557 11.2401186,31.5 C11.2332016,31.7420949 11.1986166,31.9703557 11.1432806,32.1847826 C11.0741107,32.4614625 11.0464427,32.7658103 10.9081028,33.0009881 C10.6798419,33.3952569 10.7144269,33.6442688 11.1432806,33.8033597 C11.7173913,34.0108696 12.291502,34.2114625 12.8794466,34.3774704 C14.083004,34.7164032 15.3073123,34.916996 16.5523715,34.972332 C17.0296443,34.993083 17.1749012,34.8270751 17.1195652,34.3567194 C17.1057312,34.2252964 17.1403162,34.0869565 17.1749012,33.9624506 C17.7697628,32.0049407 18.2539526,30.0266798 18.4130435,27.986166 C18.5375494,26.4505929 18.6274704,24.8873518 17.527668,23.4901186 C17.7559289,23.3448617 17.9565217,23.1166008 18.1778656,23.0958498 C20.3567194,22.9090909 22.5424901,22.75 24.7282609,22.5909091 C24.7766798,22.5839921 24.8320158,22.6047431 24.8873518,22.6116601 C24.8942688,22.68083 24.9011858,22.722332 24.9011858,22.770751 C24.9496047,26.4021739 24.1264822,29.8468379 22.6116601,33.1393281 C22.5563241,33.263834 22.3972332,33.3883399 22.2658103,33.4229249 C21.4980237,33.6096838 20.7233202,33.7895257 19.9416996,33.9347826 C19.4644269,34.0247036 18.9802372,34.0385375 18.5029644,34.0938735 C18.2262846,34.1284585 18.0395257,34.2806324 18.0602767,34.5711462 C18.0810277,34.8547431 18.2747036,34.993083 18.5583004,34.979249 C18.6966403,34.972332 18.8349802,34.965415 18.9733202,34.951581 C20.9031621,34.7786561 22.756917,34.3083004 24.534585,33.5266798 C26.5059289,32.6551383 28.2628458,31.472332 29.784585,29.9505929 C29.9021739,29.833004 29.9851779,29.6047431 29.9644269,29.4387352 C29.9160079,29.0583004 29.75,28.6986166 29.701581,28.3181818 C29.4525692,26.3952569 29.0029644,24.513834 28.4150198,22.673913 C28.3804348,22.5701581 28.3596838,22.4664032 28.3320158,22.3418972 C30.0750988,22.2035573 31.7628458,22.0721344 33.4920949,21.9337945 C33.4575099,22.0583004 33.4436759,22.1274704 33.4298419,22.1897233 C32.7934783,24.3478261 31.7490119,26.298419 30.3310277,28.041502 C30.1096838,28.3181818 30.1166008,28.5671937 30.3310277,28.7401186 C30.5385375,28.9061265 30.8152174,28.8577075 31.0158103,28.5879447 C31.4100791,28.062253 31.8250988,27.5503953 32.1709486,26.9970356 C35.1729249,22.0790514 35.7193676,16.8843874 33.8171937,11.4337945 Z M29.6185771,12.9901186 C30.9673913,13.7579051 32.3231225,14.5118577 33.6719368,15.2865613 C33.7895257,15.3557312 33.8794466,15.4733202 33.9762846,15.5701581 C33.9486166,15.6116601 33.9140316,15.6600791 33.8863636,15.701581 C31.6383399,14.5741107 29.3903162,13.4397233 27.0800395,12.284585 C27.2944664,12.0978261 27.4397233,11.9733202 27.6126482,11.8211462 C28.2490119,12.194664 28.9268775,12.6027668 29.6185771,12.9901186 Z M22.3557312,10.0780632 C22.6600791,10.2994071 23.0820158,10.451581 23.2411067,10.7490119 C23.5108696,11.2608696 23.5869565,11.2401186 22.9367589,11.6482213 C22.722332,11.1432806 22.5148221,10.6383399 22.3003953,10.1403162 C22.3211462,10.1195652 22.3349802,10.0988142 22.3557312,10.0780632 Z M21.1660079,7.63636364 C21.6709486,7.98913043 22.1966403,8.24505929 22.5632411,8.63932806 C22.812253,8.91600791 22.8537549,9.38636364 22.9920949,9.78754941 C21.8922925,9.48320158 21.5395257,8.59782609 21.1660079,7.63636364 Z M23.3586957,9.14426877 C23.9120553,9.50395257 24.3893281,9.8083004 24.9011858,10.1472332 C24.7005929,10.2994071 24.534585,10.437747 24.3547431,10.562253 C24.2233202,10.6521739 23.7667984,10.444664 23.6837945,10.2302372 C23.5662055,9.89822134 23.4832016,9.55928854 23.3586957,9.14426877 Z M23.0059289,13.3567194 C23.1926877,13.9031621 23.3863636,14.4426877 23.5454545,14.9960474 C23.5800395,15.1205534 23.5523715,15.3211462 23.4693676,15.4179842 C22.3073123,16.8359684 21.1383399,18.2401186 19.9555336,19.6442688 C19.8794466,19.7341897 19.7272727,19.8448617 19.6304348,19.8310277 C18.4476285,19.6788538 17.2648221,19.5059289 15.9851779,19.326087 C18.0948617,16.8567194 20.2667984,14.6225296 22.715415,12.5681818 C22.81917,12.8517787 22.9229249,13.1007905 23.0059289,13.3567194 Z M23.7598814,15.8814229 C23.8013834,15.8883399 23.8428854,15.8883399 23.8843874,15.8952569 C24.1749012,17.3270751 24.465415,18.7658103 24.7766798,20.2875494 C23.2756917,20.1768775 21.8853755,20.0731225 20.3774704,19.9555336 C21.5533597,18.5444664 22.6531621,17.2164032 23.7598814,15.8814229 Z M23.7114625,13.7648221 C23.5800395,13.3843874 23.4416996,13.0039526 23.3033597,12.6304348 C23.1304348,12.1462451 23.1304348,12.1531621 23.6353755,11.8626482 C23.9120553,12.6096838 24.1887352,13.3290514 24.437747,14.055336 C24.472332,14.1521739 24.4100791,14.3043478 24.3478261,14.4081028 C24.2786561,14.5256917 24.1679842,14.6156126 24.0434783,14.7539526 C23.9328063,14.4011858 23.8290514,14.083004 23.7114625,13.7648221 Z M24.1403162,11.5721344 C24.1195652,11.5167984 24.1610672,11.3853755 24.2025692,11.3784585 C24.2855731,11.3577075 24.4031621,11.3507905 24.479249,11.3922925 C25.0533597,11.7104743 25.6205534,12.0424901 26.222332,12.3883399 C25.7934783,12.8517787 25.3853755,13.2875494 24.9357708,13.7717391 C24.6590909,13.0108696 24.3962451,12.298419 24.1403162,11.5721344 Z M24.7766798,10.9496047 C24.8250988,10.8942688 24.8458498,10.8666008 24.8735178,10.8389328 C25.3715415,10.423913 25.3646245,10.423913 25.9179842,10.7697628 C26.298419,11.0118577 26.6788538,11.2401186 27.0938735,11.4960474 C26.9347826,11.6620553 26.7826087,11.8211462 26.6304348,11.9802372 C26.5197628,11.9249012 26.4298419,11.8764822 26.3399209,11.8280632 C25.8211462,11.5444664 25.3162055,11.2539526 24.7766798,10.9496047 Z M31.0019763,7.95454545 C29.826087,8.99901186 28.6501976,10.0296443 27.4535573,11.0948617 C26.9555336,10.7905138 26.4160079,10.465415 25.8142292,10.0918972 C27.3498024,9.06818182 28.8300395,8.07905138 30.3448617,7.07608696 C30.5938735,7.40810277 30.8013834,7.68478261 31.0019763,7.95454545 Z M21.9545455,1.55632411 C25.1363636,2.46936759 27.8063241,4.16403162 29.9851779,6.6541502 C28.4150198,7.7055336 26.8863636,8.72924901 25.3162055,9.78063241 C24.6452569,9.33794466 23.9328063,8.87450593 23.2272727,8.4041502 C23.1511858,8.35573123 23.0958498,8.24505929 23.0681818,8.14822134 C22.5079051,6.01778656 22.0998024,3.86660079 21.9199605,1.66699605 C21.9268775,1.64624506 21.9407115,1.62549407 21.9545455,1.55632411 Z M17.8043478,1.19664032 C17.7697628,1.12747036 17.7490119,1.0513834 17.7075099,0.947628458 C18.9041502,0.975296443 20.0523715,1.10671937 21.1867589,1.36956522 C21.2697628,1.39031621 21.3735178,1.51482213 21.3804348,1.59782609 C21.5602767,3.69367589 21.9268775,5.76185771 22.4456522,7.88537549 C21.8300395,7.44268775 21.2697628,7.04150198 20.7164032,6.63339921 C20.6403162,6.57806324 20.5780632,6.4812253 20.5296443,6.39130435 C19.6166008,4.65513834 18.7104743,2.92588933 17.8043478,1.19664032 Z M14.0899209,1.78458498 C15.0583004,2.65612648 16.0405138,3.51383399 17.0296443,4.35079051 C17.9150198,5.10474308 18.8142292,5.83794466 19.7341897,6.55731225 C20.0385375,6.79940711 20.2944664,7.05533597 20.4466403,7.42193676 C20.5918972,7.76778656 20.7717391,8.09980237 20.9031621,8.46640316 C17.9496047,6.50197628 15.2173913,4.28162055 12.7756917,1.66699605 C13.2598814,1.41106719 13.6749012,1.4041502 14.0899209,1.78458498 Z M10.3063241,2.51086957 C10.9011858,3.33399209 11.4683794,4.08794466 12.0009881,4.86264822 C12.9624506,6.25988142 13.8339921,7.7055336 14.4565217,9.28952569 C14.7608696,10.0642292 14.9822134,10.8596838 14.9199605,11.7104743 C14.8300395,12.9071146 13.8201581,13.6749012 12.6719368,13.3428854 C12.0424901,13.1561265 11.4130435,12.8656126 10.8873518,12.4851779 C8.58399209,10.8320158 6.88241107,8.62549407 5.18774704,6.2944664 C6.8270751,4.95256917 8.50790514,3.70059289 10.3063241,2.51086957 Z M5.09090909,7.11067194 C5.66501976,7.83003953 6.18379447,8.51482213 6.73715415,9.17193676 C8.02371542,10.6867589 9.35869565,12.1600791 11.0810277,13.1907115 C11.6205534,13.5158103 12.25,13.7579051 12.8656126,13.8824111 C14.1452569,14.1383399 15.2519763,13.2529644 15.3903162,11.9525692 C15.4940711,10.9980237 15.2658103,10.0988142 14.9337945,9.22035573 C14.2282609,7.33201581 13.1699605,5.63043478 12.0217391,3.98418972 C11.6482213,3.44466403 11.2539526,2.91897233 10.8250988,2.34486166 C11.2401186,2.18577075 11.6205534,2.02667984 12.0148221,1.90217391 C12.1047431,1.87450593 12.263834,1.9298419 12.326087,2.00592885 C14.9683794,4.82806324 17.9703557,7.22826087 21.1867589,9.37252964 C21.3320158,9.46936759 21.4565217,9.6215415 21.5256917,9.78063241 C21.8369565,10.458498 22.1136364,11.1571146 22.4179842,11.8418972 C22.4871542,12.0009881 22.4664032,12.0978261 22.3280632,12.215415 C19.8656126,14.2628458 17.6452569,16.5523715 15.5632411,18.9802372 C15.4802372,19.0770751 15.2796443,19.1600791 15.1620553,19.1324111 C11.8972332,18.4476285 8.76383399,17.416996 5.90711462,15.6462451 C5.66501976,15.4940711 5.54051383,15.3349802 5.52667984,15.0306324 C5.39525692,12.5197628 5.23616601,10.0158103 5.08399209,7.50494071 C5.08399209,7.38043478 5.09090909,7.27667984 5.09090909,7.11067194 Z M5.49209486,23.4278656 C5.49901186,23.5523715 5.49209486,23.6768775 5.49209486,23.8359684 C5.04249012,23.8982213 4.60671937,23.9604743 4.11561265,24.0296443 C4.7173913,21.1867589 4.70355731,18.3300395 4.61363636,15.4387352 C4.8972332,15.5563241 5.05632411,15.68083 5.0701581,15.9782609 C5.20158103,18.4683794 5.35375494,20.944664 5.49209486,23.4278656 Z M4.5444664,7.20750988 C4.69664032,9.8083004 4.84881423,12.3745059 5.00098814,14.9683794 C4.66205534,14.8577075 4.58596838,14.6571146 4.56521739,14.3527668 C4.42687747,12.277668 4.26778656,10.1956522 4.11561265,8.12055336 C4.08102767,7.66403162 4.12252964,7.59486166 4.5444664,7.20750988 Z M3.20256917,24.1749012 C2.2826087,24.2924901 2.30335968,24.2924901 1.95750988,23.4347826 C1.0583004,21.2213439 0.85770751,18.9041502 1.03754941,16.5523715 C1.12747036,15.3972332 1.32806324,14.2559289 1.48023715,13.1007905 C1.49407115,12.9901186 1.54249012,12.8863636 1.5770751,12.7756917 C2.37944664,13.4950593 3.16106719,14.1867589 3.92885375,14.8922925 C4.00494071,14.9614625 4.06027668,15.0859684 4.06027668,15.1897233 C4.14328063,18.0533597 4.25395257,20.923913 3.63833992,23.7529644 C3.58300395,24.0227273 3.47924901,24.1403162 3.20256917,24.1749012 Z M3.24407115,26.0839921 C3.46541502,25.6136364 3.6729249,25.1709486 3.88735178,24.7351779 C3.91501976,24.6729249 3.99110672,24.6037549 4.05335968,24.5968379 C4.53754941,24.520751 5.02173913,24.451581 5.54051383,24.3754941 C5.63735178,26.0424901 5.73418972,27.6679842 5.83794466,29.3764822 C4.78656126,28.3458498 3.94960474,27.2460474 3.24407115,26.0839921 Z M5.5958498,16.0543478 C8.53557312,17.8596838 11.6828063,18.9041502 14.9960474,19.6235178 C14.8300395,19.8310277 14.6847826,20.0108696 14.5395257,20.1837945 C13.8201581,21.06917 13.1077075,21.9545455 12.3745059,22.826087 C12.270751,22.9505929 12.0909091,23.0681818 11.9318182,23.0889328 C9.98814229,23.3171937 8.0444664,23.5177866 6.04545455,23.7391304 C5.90019763,21.2075099 5.74802372,18.6689723 5.5958498,16.0543478 Z M6.08003953,24.2924901 C7.94071146,24.0780632 9.76679842,23.8705534 11.5928854,23.6630435 C10.0088933,25.7727273 8.30039526,27.7509881 6.39130435,29.666996 C6.28754941,27.8409091 6.18379447,26.0909091 6.08003953,24.2924901 Z M9.78754941,32.3231225 C8.73616601,31.6175889 7.7055336,30.9328063 6.63339921,30.2134387 C7.13833992,29.68083 7.58794466,29.2519763 7.99604743,28.7816206 C9.42786561,27.1422925 10.8458498,25.4960474 12.263834,23.8428854 C12.4160079,23.6630435 12.5750988,23.5731225 12.8171937,23.5592885 C13.5158103,23.5108696 14.2075099,23.4416996 14.9337945,23.3725296 C12.9901186,25.1571146 12.3675889,27.5503953 11.6551383,29.9021739 C11.6205534,30.0197628 11.5928854,30.1581028 11.5237154,30.2480237 C10.9634387,30.9258893 10.4031621,31.5899209 9.78754941,32.3231225 Z M15.0167984,24.0088933 C15.4871542,23.5662055 16.0681818,23.4278656 16.7598814,23.7114625 C15.3349802,25.5029644 13.937747,27.2667984 12.4782609,29.0998024 C12.4782609,28.9614625 12.4644269,28.9130435 12.4782609,28.8715415 C12.8586957,27.6403162 13.2598814,26.4160079 13.944664,25.3092885 C14.2420949,24.8320158 14.6156126,24.3893281 15.0167984,24.0088933 Z M17.9357708,26.0217391 C18.0533597,27.6610672 17.7835968,29.2588933 17.416996,30.8428854 C17.1679842,31.9288538 16.8636364,33.0009881 16.5731225,34.1146245 C14.8092885,33.9555336 13.1561265,33.5750988 11.4683794,33.0355731 C11.6620553,32.1778656 11.8488142,31.3547431 12.0355731,30.5247036 C12.0563241,30.4416996 12.1254941,30.3656126 12.18083,30.2964427 C13.7994071,28.2628458 15.4249012,26.229249 17.0434783,24.1887352 C17.0849802,24.1333992 17.1333992,24.0849802 17.2025692,24.0088933 C17.7490119,24.6037549 17.8873518,25.3023715 17.9357708,26.0217391 Z M17.6867589,22.5148221 C17.6383399,22.5770751 17.527668,22.6116601 17.444664,22.6185771 C15.9920949,22.75 14.5326087,22.8745059 13.0800395,23.0059289 C13.0662055,23.0059289 13.0454545,22.9851779 12.9970356,22.9575099 C13.4743083,22.3764822 13.944664,21.7954545 14.4150198,21.2213439 C14.7677866,20.7924901 15.1136364,20.3636364 15.4733202,19.9486166 C15.5424901,19.8656126 15.68083,19.7756917 15.770751,19.7895257 C16.9535573,19.9762846 18.1294466,20.1768775 19.3745059,20.3843874 C18.8003953,21.1106719 18.2470356,21.8162055 17.6867589,22.5148221 Z M18.3853755,22.5424901 C18.4614625,22.4249012 18.5029644,22.3488142 18.5583004,22.2796443 C18.9940711,21.7262846 19.4229249,21.1729249 19.8725296,20.6333992 C19.9486166,20.5434783 20.0938735,20.4466403 20.1976285,20.4535573 C21.7124506,20.5711462 23.2203557,20.7025692 24.7697628,20.8339921 C24.7974308,21.2351779 24.8250988,21.6225296 24.8527668,22.0583004 C22.701581,22.2173913 20.5780632,22.3764822 18.3853755,22.5424901 Z M24.3685771,15.8814229 C24.2440711,15.2865613 24.2302372,15.2519763 24.7628458,14.8369565 C25.4960474,16.6630435 26.229249,18.4752964 26.9693676,20.3013834 C26.4160079,20.3013834 25.9110672,20.3083004 25.4130435,20.2875494 C25.3438735,20.2875494 25.2401186,20.1284585 25.2193676,20.0316206 C24.9288538,18.6551383 24.6660079,17.2648221 24.3685771,15.8814229 Z M27.0454545,20.8893281 C27.1146245,20.8893281 27.2114625,21 27.2460474,21.083004 C27.3428854,21.3112648 27.4189723,21.5533597 27.5227273,21.8646245 C26.7826087,21.9199605 26.1047431,21.9683794 25.3853755,22.0237154 C25.3577075,21.6225296 25.3369565,21.2490119 25.3162055,20.8824111 C25.9041502,20.8754941 26.4782609,20.8754941 27.0454545,20.8893281 Z M28.9960474,27.4535573 C29.1136364,27.944664 29.1551383,28.4565217 29.1966403,28.9545455 C29.2104743,29.0928854 29.1620553,29.2865613 29.0652174,29.3764822 C27.4743083,30.9535573 25.6343874,32.1501976 23.5523715,32.9733202 C23.4693676,33.0079051 23.3794466,33.0217391 23.2065217,33.0770751 C24.6936759,29.687747 25.4683794,26.222332 25.4061265,22.5563241 C26.1531621,22.5009881 26.8586957,22.4318182 27.5711462,22.4041502 C27.6472332,22.3972332 27.7786561,22.5632411 27.8063241,22.673913 C28.2144269,24.2579051 28.6225296,25.8557312 28.9960474,27.4535573 Z M33.18083,21.4288538 C31.5968379,21.5326087 30.0128458,21.6571146 28.4288538,21.8023715 C28.1521739,21.8300395 28.027668,21.7539526 27.972332,21.4980237 C27.93083,21.3112648 27.8547431,21.1314229 27.7786561,20.9100791 C29.763834,20.9100791 31.7144269,20.9100791 33.6857708,20.9100791 C33.7203557,21.2974308 33.5405138,21.4011858 33.18083,21.4288538 Z M33.4851779,20.3636364 C32.5513834,20.3498024 31.6175889,20.3567194 30.6837945,20.3567194 C30.6837945,20.3567194 30.6837945,20.3636364 30.6837945,20.3636364 C29.75,20.3636364 28.8162055,20.3567194 27.8824111,20.3705534 C27.6541502,20.3705534 27.5434783,20.3083004 27.4535573,20.0869565 C26.7480237,18.2885375 26.0286561,16.4970356 25.2954545,14.7124506 C25.1778656,14.4288538 25.1986166,14.2420949 25.4268775,14.020751 C25.8073123,13.6541502 26.1600791,13.2529644 26.5128458,12.8656126 C26.6442688,12.7203557 26.7480237,12.6788538 26.9486166,12.7826087 C29.2104743,13.937747 31.486166,15.0790514 33.7549407,16.2134387 C33.9486166,16.3102767 34.0454545,16.4209486 34.0316206,16.6284585 C33.9762846,17.7559289 33.9140316,18.8764822 33.8725296,20.0039526 C33.8656126,20.2737154 33.7687747,20.3705534 33.4851779,20.3636364 Z","id":"Shape"}))])))]);
}

Chord.defaultProps = {"width":"42px","height":"42px","viewBox":"0 0 42 42","version":"1.1"};

module.exports = Chord;

Chord.default = Chord;


/***/ }),

/***/ "./src/block-editor/blocks/icons/entities-cloud.svg":
/*!**********************************************************!*\
  !*** ./src/block-editor/blocks/icons/entities-cloud.svg ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var React = __webpack_require__(/*! react */ "react");

function EntitiesCloud (props) {
    return React.createElement("svg",props,[React.createElement("title",{"key":0},"Entity_Cloud"),React.createElement("g",{"id":"Page-1","stroke":"none","strokeWidth":"1","fill":"none","fillRule":"evenodd","key":1},React.createElement("g",{"id":"Entities-Timeline-Copy","transform":"translate(-848.000000, -215.000000)"},React.createElement("g",{"id":"Entity_Cloud","transform":"translate(848.000000, 215.000000)"},[React.createElement("rect",{"id":"Rectangle-Copy-7","x":"0","y":"0","width":"42","height":"42","key":0}),React.createElement("g",{"id":"cloud-computing","transform":"translate(1.000000, 9.000000)","fill":"#000000","fillRule":"nonzero","key":1},[React.createElement("path",{"d":"M38.213029,13.3491112 C37.1558996,12.14558 35.7016165,11.355478 34.1177524,11.1241944 L34.1177524,5.55672395 C34.1177524,4.60114125 33.3403437,3.82373255 32.384761,3.82373255 L23.3721778,3.82373255 C23.3544227,3.78962406 23.3330075,3.75683942 23.3079323,3.72576799 C21.5234667,1.51946294 18.8718045,0.17490333 16.0328571,0.0369119227 C13.9839339,-0.0629994629 11.9811117,0.462489259 10.2413453,1.55575188 C9.96797433,1.72738453 9.88600967,2.0879377 10.0575644,2.36096133 C10.2291971,2.63406284 10.5898281,2.71645274 10.8628518,2.54474221 C12.3991353,1.57942535 14.1675591,1.11561224 15.9761654,1.20360902 C18.1967213,1.31154135 20.2869173,2.25575456 21.8305209,3.82365467 L14.8689635,3.82365467 C13.9133808,3.82365467 13.1358942,4.60106337 13.1358942,5.55664608 L13.1358942,17.8135849 C12.3401853,18.3477954 11.8153974,19.2557975 11.8153974,20.2841917 C11.8153974,20.6067427 12.0768179,20.8682411 12.3994468,20.8682411 C12.7220757,20.8682411 12.9834962,20.6067427 12.9834962,20.2841917 C12.9834962,19.2884264 13.7935338,18.4783888 14.7892213,18.4783888 C15.2633136,18.4783888 15.7114742,18.6611574 16.0510016,18.9930532 C16.1953786,19.1340816 16.4020542,19.1904619 16.598217,19.1426477 C16.794224,19.0945999 16.9515279,18.9488212 17.0142938,18.7570193 C17.4065414,17.5583163 18.5177927,16.753029 19.7794173,16.753029 C21.0112943,16.753029 22.1141353,17.5336305 22.5236708,18.6954995 C22.6061386,18.9295086 22.8272207,19.0854108 23.0743904,19.0854108 C23.078985,19.0854108 23.0835795,19.0854108 23.0882519,19.0852551 C23.3409506,19.0792589 23.5610983,18.911442 23.6338319,18.6694119 C24.0678195,17.2247852 25.4251504,16.2158593 26.9347234,16.2158593 C28.3789608,16.2158593 29.6810016,17.1273657 30.1746402,18.4839957 C30.2440252,18.6745516 30.4071697,18.8155021 30.6058244,18.8563856 C30.8047127,18.8974248 31.0099866,18.8320892 31.1489903,18.6844415 C31.4942803,18.3172691 31.961442,18.1149544 32.4643475,18.1149544 C33.4600349,18.1149544 34.2701504,18.9249919 34.2701504,19.9206794 C34.2701504,20.2432304 34.5315709,20.5047288 34.8541998,20.5047288 C35.1768287,20.5047288 35.4382492,20.2432304 35.4382492,19.9206794 C35.4382492,18.8922852 34.9134613,17.9843609 34.1177524,17.4501504 L34.1177524,12.3084909 C35.361544,12.5294952 36.4988829,13.1676665 37.3353195,14.1199785 C38.217312,15.1240763 38.7030075,16.4110875 38.7030075,17.7439662 C38.7030075,17.808601 38.7019173,17.8729243 38.6997368,17.9369361 C38.6572959,19.1610258 38.1318072,20.3051396 37.2198335,21.1584748 C36.3088722,22.0109533 35.1160097,22.4804511 33.8612379,22.4804511 L14.7357223,22.4804511 C14.4130934,22.4804511 14.1516729,22.7419495 14.1516729,23.0645005 C14.1516729,23.3870516 14.4130934,23.6485499 14.7357223,23.6485499 L33.8613158,23.6485499 C35.4136412,23.6485499 36.8898067,23.0671482 38.0180344,22.0114205 C39.1573201,20.9454135 39.8140252,19.5127014 39.8671348,17.9772744 C39.8697825,17.8998684 39.8711842,17.822073 39.8711842,17.744044 C39.8711842,16.1273174 39.2823067,14.5665038 38.213029,13.3491112 L38.213029,13.3491112 Z M14.8690414,4.99183136 L32.3848389,4.99183136 C32.6963319,4.99183136 32.9497315,5.24523093 32.9497315,5.55672395 L32.9497315,7.03234425 L14.3040709,7.03234425 L14.3040709,5.55672395 C14.3040709,5.24523093 14.5575483,4.99183136 14.8690414,4.99183136 Z M32.4644253,16.9467777 C31.9249194,16.9467777 31.3972503,17.0959049 30.9397449,17.3688507 C30.1272154,15.9548281 28.6017562,15.0476826 26.9348013,15.0476826 C25.3361412,15.0476826 23.864804,15.8931525 23.0321053,17.203993 C22.27471,16.2041004 21.0782653,15.5849302 19.7794952,15.5849302 C18.2772422,15.5849302 16.9271536,16.399718 16.2179619,17.6751262 C15.7845193,17.4369898 15.2959425,17.3103679 14.7892991,17.3103679 C14.6240521,17.3103679 14.4620757,17.3245408 14.3040709,17.3506284 L14.3040709,8.20044307 L32.9497315,8.20044307 L32.9497315,16.9870381 C32.7916488,16.9610285 32.6296724,16.9467777 32.4644253,16.9467777 L32.4644253,16.9467777 Z","id":"Shape","key":0}),React.createElement("path",{"d":"M12.3995247,22.4803733 L6.00986842,22.4803733 C4.75509667,22.4803733 3.56231203,22.0108754 2.65127282,21.1583969 C1.73937701,20.3050618 1.21381042,19.1610258 1.17144737,17.937014 C1.16926692,17.8729243 1.16809882,17.8085231 1.16809882,17.7439662 C1.16809882,15.09721 3.06991944,12.8163024 5.69012084,12.3205612 C6.38591837,12.1889554 6.87200322,11.5341192 6.79662191,10.8299114 C6.76227981,10.508217 6.74577068,10.1811493 6.74756176,9.85774168 C6.75955424,7.65626477 7.61444683,5.55477712 9.15454619,3.94038668 C9.37718582,3.70700054 9.36846402,3.3373362 9.13507787,3.11461869 C8.90169173,2.89190118 8.53194952,2.90070086 8.30930988,3.134087 C6.56261278,4.96504296 5.59309076,7.35061224 5.57938507,9.85120032 C5.57736037,10.2180612 5.59604995,10.5891273 5.63514232,10.9541192 C5.64620032,11.057457 5.57486842,11.1535526 5.47285446,11.1728652 C2.30177766,11.7728786 1.10664551e-15,14.5363668 1.10664551e-15,17.7439662 C1.10664551e-15,17.822073 0.00147959184,17.8997905 0.00412728249,17.9773523 C0.0572368421,19.5126235 0.713864125,20.9452578 1.85314984,22.0113426 C2.98137755,23.0670704 4.45762084,23.6484721 6.00986842,23.6484721 L12.3995247,23.6484721 C12.7221536,23.6484721 12.9835741,23.3869737 12.9835741,23.0644227 C12.9835741,22.7418716 12.7220757,22.4803733 12.3995247,22.4803733 L12.3995247,22.4803733 Z","id":"Path","key":1}),React.createElement("path",{"d":"M19.4246267,10.2025644 L17.5467132,10.2025644 C17.0276101,10.2025644 16.6053034,10.6248711 16.6053034,11.1439742 L16.6053034,13.0218878 C16.6053034,13.5409909 17.0276101,13.9632975 17.5467132,13.9632975 L19.4246267,13.9632975 C19.9437299,13.9632975 20.3660365,13.5409909 20.3660365,13.0218878 L20.3660365,11.1439742 C20.3660365,10.6248711 19.9437299,10.2025644 19.4246267,10.2025644 L19.4246267,10.2025644 Z M19.1979377,12.7951987 L17.7734023,12.7951987 L17.7734023,11.3706633 L19.1979377,11.3706633 L19.1979377,12.7951987 Z","id":"Shape","key":2}),React.createElement("path",{"d":"M24.765486,10.2025644 L22.8875725,10.2025644 C22.3684694,10.2025644 21.9461627,10.6248711 21.9461627,11.1439742 L21.9461627,13.0218878 C21.9461627,13.5409909 22.3684694,13.9632975 22.8875725,13.9632975 L24.765486,13.9632975 C25.2845892,13.9632975 25.7068958,13.5409909 25.7068958,13.0218878 L25.7068958,11.1439742 C25.7068958,10.6248711 25.284667,10.2025644 24.765486,10.2025644 Z M24.538797,12.7951987 L23.1142615,12.7951987 L23.1142615,11.3706633 L24.538797,11.3706633 L24.538797,12.7951987 Z","id":"Shape","key":3}),React.createElement("path",{"d":"M30.0184264,10.2025644 L28.1405129,10.2025644 C27.6214098,10.2025644 27.1991031,10.6248711 27.1991031,11.1439742 L27.1991031,13.0218878 C27.1991031,13.5409909 27.6214098,13.9632975 28.1405129,13.9632975 L30.0184264,13.9632975 C30.5375295,13.9632975 30.9598362,13.5409909 30.9598362,13.0218878 L30.9598362,11.1439742 C30.9598362,10.6248711 30.5376074,10.2025644 30.0184264,10.2025644 Z M29.7917374,12.7951987 L28.3672019,12.7951987 L28.3672019,11.3706633 L29.7917374,11.3706633 L29.7917374,12.7951987 Z","id":"Shape","key":4})])])))]);
}

EntitiesCloud.defaultProps = {"width":"42px","height":"42px","viewBox":"0 0 42 42","version":"1.1"};

module.exports = EntitiesCloud;

EntitiesCloud.default = EntitiesCloud;


/***/ }),

/***/ "./src/block-editor/blocks/icons/faceted-search.svg":
/*!**********************************************************!*\
  !*** ./src/block-editor/blocks/icons/faceted-search.svg ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var React = __webpack_require__(/*! react */ "react");

function FacetedSearch (props) {
    return React.createElement("svg",props,[React.createElement("title",{"key":0},"Faceted_Search"),React.createElement("g",{"id":"Page-1","stroke":"none","strokeWidth":"1","fill":"none","fillRule":"evenodd","key":1},React.createElement("g",{"id":"Entities-Timeline-Copy","transform":"translate(-848.000000, -164.000000)"},React.createElement("g",{"id":"Faceted_Search","transform":"translate(848.000000, 164.000000)"},[React.createElement("rect",{"id":"Rectangle-Copy-2","x":"0","y":"0","width":"42","height":"42","key":0}),React.createElement("g",{"id":"browser-(1)","transform":"translate(2.000000, 5.000000)","fill":"#000000","fillRule":"nonzero","key":1},[React.createElement("path",{"d":"M27.8448876,32.9181093 C27.7384682,32.9181093 27.6323733,32.8891521 27.5389319,32.8318815 C27.3672982,32.7266707 27.26315,32.5410235 27.26315,32.3412195 L27.26315,25.2409386 L21.6229234,20.4478951 C21.4937926,20.3385016 21.4194937,20.1782724 21.4194937,20.0099994 L21.4194937,14.7150329 C21.4194937,14.3965047 21.6800265,14.1381431 22.0012313,14.1381431 L36.9609395,14.1381431 C37.2821443,14.1381431 37.5426771,14.3965047 37.5426771,14.7150329 L37.5426771,20.0099994 C37.5426771,20.1782724 37.4683782,20.3385016 37.3395718,20.4478951 L31.6990208,25.2409386 L31.6990208,30.7199437 C31.6990208,30.9384089 31.5744322,31.1382129 31.3774916,31.2360235 L28.1047715,32.8572993 C28.0226857,32.8978393 27.9337867,32.9181093 27.8448876,32.9181093 L27.8448876,32.9181093 Z M22.5829689,19.7445594 L28.2231955,24.5376028 C28.3523262,24.6469963 28.4266252,24.8069039 28.4266252,24.9754985 L28.4266252,31.4081573 L30.5355456,30.3631278 L30.5355456,24.9754985 C30.5355456,24.8069039 30.6098445,24.6469963 30.7386508,24.5376028 L36.3792019,19.7445594 L36.3792019,15.2919226 L22.5829689,15.2919226 L22.5829689,19.7445594 Z","id":"Shape","key":0}),React.createElement("path",{"d":"M36.9609395,17.8018597 L22.2286702,17.8018597 C21.907141,17.8018597 21.6469327,17.5434981 21.6469327,17.2249699 C21.6469327,16.9064418 21.907141,16.6480802 22.2286702,16.6480802 L36.9609395,16.6480802 C37.2821443,16.6480802 37.5426771,16.9064418 37.5426771,17.2249699 C37.5426771,17.5434981 37.2821443,17.8018597 36.9609395,17.8018597 L36.9609395,17.8018597 Z","id":"Path","key":1}),React.createElement("path",{"d":"M25.5198839,25.7953062 C25.5176127,25.7953062 25.5156661,25.7953062 25.5133949,25.7953062 L3.90604511,25.7953062 C1.75235063,25.7953062 2.36449515e-15,24.0578802 2.36449515e-15,21.9218115 L2.36449515e-15,3.90502576 C2.36449515e-15,1.76895701 1.75235063,0.031209298 3.90604511,0.031209298 L25.5143683,0.031209298 C27.6683872,0.031209298 29.4204134,1.76895701 29.4204134,3.90502576 L29.4204134,14.689615 C29.4204134,15.0081431 29.160205,15.2665047 28.8386757,15.2665047 C28.517471,15.2665047 28.2569382,15.0081431 28.2569382,14.689615 L28.2569382,3.90502576 C28.2569382,2.40536975 27.0266264,1.18531062 25.5143683,1.18531062 L3.90604511,1.18531062 C2.39378687,1.18531062 1.16347514,2.40536975 1.16347514,3.90502576 L1.16347514,21.9218115 C1.16347514,23.4214675 2.39378687,24.6415267 3.90604511,24.6415267 L25.5143683,24.6415267 L25.5189105,24.6415267 C26.0662565,24.6415267 26.59446,24.4809756 27.0473912,24.1775696 C27.3137641,23.9990008 27.6752006,24.0684979 27.85527,24.3323291 C28.0353393,24.5961606 27.9652582,24.9549068 27.6988854,25.1334756 C27.0529068,25.5665452 26.2995356,25.7953062 25.5198839,25.7953062 L25.5198839,25.7953062 Z","id":"Path","key":2}),React.createElement("path",{"d":"M28.8386757,7.88920096 L0.581737569,7.88920096 C0.260532806,7.88920096 -2.36449515e-15,7.63083926 -2.36449515e-15,7.3123112 C-2.36449515e-15,6.99378306 0.260532806,6.73542145 0.581737569,6.73542145 L28.8386757,6.73542145 C29.160205,6.73542145 29.4204134,6.99378306 29.4204134,7.3123112 C29.4204134,7.63083926 29.160205,7.88920096 28.8386757,7.88920096 Z","id":"Path","key":3}),React.createElement("path",{"d":"M8.34224033,5.5996596 C7.4457869,5.5996596 6.71642488,4.8763756 6.71642488,3.98739262 C6.71642488,3.09808791 7.4457869,2.37480391 8.34224033,2.37480391 C9.23901818,2.37480391 9.9683802,3.09808791 9.9683802,3.98739262 C9.9683802,4.8763756 9.23901818,5.5996596 8.34224033,5.5996596 Z M8.34224033,3.5285835 C8.08722322,3.5285835 7.8799001,3.73450065 7.8799001,3.98739262 C7.8799001,4.24028466 8.08722322,4.44588001 8.34224033,4.44588001 C8.59725743,4.44588001 8.80490498,4.24028466 8.80490498,3.98739262 C8.80490498,3.73450065 8.59725743,3.5285835 8.34224033,3.5285835 Z","id":"Shape","key":4}),React.createElement("path",{"d":"M3.65135243,5.58518107 C2.754899,5.58518107 2.02553698,4.86189707 2.02553698,3.97259236 C2.02553698,3.08360938 2.754899,2.36032538 3.65135243,2.36032538 C4.5481302,2.36032538 5.27749222,3.08360938 5.27749222,3.97259236 C5.27749222,4.86189707 4.5481302,5.58518107 3.65135243,5.58518107 Z M3.65135243,3.51410489 C3.39633524,3.51410489 3.18901212,3.72002204 3.18901212,3.97259236 C3.18901212,4.22548433 3.39633524,4.43140148 3.65135243,4.43140148 C3.90636954,4.43140148 4.11401709,4.22548433 4.11401709,3.97259236 C4.11401709,3.72002204 3.90636954,3.51410489 3.65135243,3.51410489 Z","id":"Shape","key":5}),React.createElement("path",{"d":"M13.0331282,5.61445986 C12.1366749,5.61445986 11.4073129,4.89085413 11.4073129,4.00187123 C11.4073129,3.11288825 12.1366749,2.38928243 13.0331282,2.38928243 C13.9299062,2.38928243 14.6592682,3.11288825 14.6592682,4.00187123 C14.6592682,4.89085413 13.9299062,5.61445986 13.0331282,5.61445986 Z M13.0331282,3.54338375 C12.7781111,3.54338375 12.570788,3.74897918 12.570788,4.00187123 C12.570788,4.25476319 12.7781111,4.46068035 13.0331282,4.46068035 C13.2881454,4.46068035 13.495793,4.25476319 13.495793,4.00187123 C13.495793,3.74897918 13.2881454,3.54338375 13.0331282,3.54338375 Z","id":"Shape","key":6})])])))]);
}

FacetedSearch.defaultProps = {"width":"42px","height":"42px","viewBox":"0 0 42 42","version":"1.1"};

module.exports = FacetedSearch;

FacetedSearch.default = FacetedSearch;


/***/ }),

/***/ "./src/block-editor/blocks/icons/geomap.svg":
/*!**************************************************!*\
  !*** ./src/block-editor/blocks/icons/geomap.svg ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var React = __webpack_require__(/*! react */ "react");

function Geomap (props) {
    return React.createElement("svg",props,[React.createElement("title",{"key":0},"Geomap"),React.createElement("g",{"id":"Page-1","stroke":"none","strokeWidth":"1","fill":"none","fillRule":"evenodd","key":1},React.createElement("g",{"id":"Entities-Timeline-Copy","transform":"translate(-740.000000, -215.000000)"},React.createElement("g",{"id":"Geomap","transform":"translate(740.000000, 215.000000)"},[React.createElement("rect",{"id":"Rectangle-Copy-4","x":"0","y":"0","width":"42","height":"42","key":0}),React.createElement("g",{"id":"pin","transform":"translate(8.000000, 4.000000)","fill":"#000000","fillRule":"nonzero","key":1},[React.createElement("path",{"d":"M17.2765446,10.1353166 C17.2765446,7.6342165 15.2490024,5.60667429 12.7479024,5.60667429 C10.2468023,5.60667429 8.21926011,7.6342165 8.21926011,10.1353166 C8.21926011,12.6364166 10.2468023,14.6639588 12.7479024,14.6639588 C15.2477827,14.6610166 17.2736024,12.6351969 17.2765446,10.1353166 L17.2765446,10.1353166 Z M12.7479024,13.7295576 C11.294011,13.7296656 9.98322412,12.8539314 9.42679411,11.5107316 C8.87036409,10.1675318 9.17787797,8.62140505 10.2059344,7.5933486 C11.2339909,6.56529216 12.7801176,6.25777828 14.1233174,6.81420829 C15.4665172,7.37063831 16.3422514,8.68142519 16.3421434,10.1353166 C16.3400833,12.1195071 14.7320929,13.7274975 12.7479024,13.7295576 L12.7479024,13.7295576 Z","id":"Shape","key":0}),React.createElement("path",{"d":"M15.3300534,25.0553013 C16.3362629,23.0622094 17.1624414,20.9832273 17.7987414,18.843135 C21.7378908,16.5588795 23.6536377,11.9151587 22.4709185,7.51789859 C21.2881994,3.12063849 17.3014417,0.0644982954 12.7479024,0.0644982954 C8.19436298,0.0644982954 4.20760535,3.12063849 3.02488622,7.51789859 C1.84216708,11.9151587 3.75791394,16.5588795 7.69706331,18.843135 C8.33336337,20.9832273 9.15954186,23.0622094 10.1657513,25.0553013 C5.13920671,25.4213196 -1.70725371e-14,26.9540046 -1.70725371e-14,29.6004958 C-1.70725371e-14,32.6132723 6.56803966,34.2391304 12.7479024,34.2391304 C18.9277651,34.2391304 25.4958047,32.6132723 25.4958047,29.6004958 C25.4958047,26.9540046 20.356598,25.4213196 15.3300534,25.0553013 L15.3300534,25.0553013 Z M8.32257818,18.1231121 C4.67948767,16.1055082 2.86230308,11.8791899 3.90424422,7.84716891 C4.94618535,3.81514796 8.5834295,0.998344575 12.7479024,0.998344575 C16.9123752,0.998344575 20.5496194,3.81514796 21.5915605,7.84716891 C22.6335017,11.8791899 20.8163171,16.1055082 17.1732265,18.1231121 C17.0631516,18.184295 16.9823993,18.2872807 16.9492372,18.4087719 C15.9005721,22.2566362 13.6580092,26.3175439 12.7479024,27.876926 C11.8377956,26.3180778 9.59603356,22.2593059 8.54656751,18.407704 C8.51315028,18.2866105 8.43242851,18.1840487 8.32257818,18.1231121 L8.32257818,18.1231121 Z M12.7479024,33.3047292 C5.78607933,33.3047292 0.93440122,31.3526316 0.93440122,29.6004958 C0.93440122,28.1121281 4.63009153,26.3188787 10.6236079,25.9595347 C11.5390542,27.7276888 12.287643,28.9319985 12.3519832,29.0345156 C12.4389371,29.1687394 12.5879743,29.2497673 12.7479024,29.2497673 C12.9078305,29.2497673 13.0568676,29.1687394 13.1438215,29.0345156 C13.2081617,28.9319985 13.9567506,27.7276888 14.8721968,25.9595347 C20.8657132,26.3188787 24.5614035,28.1121281 24.5614035,29.6004958 C24.5614035,31.3526316 19.7097254,33.3047292 12.7479024,33.3047292 Z","id":"Shape","key":1})])])))]);
}

Geomap.defaultProps = {"width":"42px","height":"42px","viewBox":"0 0 42 42","version":"1.1"};

module.exports = Geomap;

Geomap.default = Geomap;


/***/ }),

/***/ "./src/block-editor/blocks/icons/navigator.svg":
/*!*****************************************************!*\
  !*** ./src/block-editor/blocks/icons/navigator.svg ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var React = __webpack_require__(/*! react */ "react");

function Navigator (props) {
    return React.createElement("svg",props,[React.createElement("title",{"key":0},"Navigator"),React.createElement("g",{"id":"Page-1","stroke":"none","strokeWidth":"1","fill":"none","fillRule":"evenodd","key":1},React.createElement("g",{"id":"Entities-Timeline-Copy","transform":"translate(-794.000000, -164.000000)"},React.createElement("g",{"id":"Navigator","transform":"translate(794.000000, 164.000000)"},[React.createElement("rect",{"id":"Rectangle-Copy","x":"0","y":"0","width":"42","height":"42","key":0}),React.createElement("g",{"id":"compass","transform":"translate(3.000000, 4.000000)","fill":"#000000","fillRule":"nonzero","key":1},[React.createElement("path",{"d":"M17.5,0 C7.83498992,0 0,7.83498992 0,17.5 C0,27.1650101 7.83498992,35 17.5,35 C27.1650101,35 35,27.1650101 35,17.5 C34.9894153,7.83943548 27.1605645,0.0105846774 17.5,0 Z M17.5,33.8709677 C8.45856855,33.8709677 1.12903226,26.5414315 1.12903226,17.5 C1.12903226,8.45856855 8.45856855,1.12903226 17.5,1.12903226 C26.5414315,1.12903226 33.8709677,8.45856855 33.8709677,17.5 C33.8603831,26.5370565 26.5370565,33.8603831 17.5,33.8709677 Z","id":"Shape","key":0}),React.createElement("path",{"d":"M25.7296573,9.0091129 C25.5742036,8.93375 25.3927823,8.93375 25.2372581,9.0091129 L25.2372581,9.00854839 L14.4877419,14.2258065 C14.471371,14.2337097 14.4617742,14.2506452 14.4465323,14.2602419 C14.3727923,14.3090726 14.3096371,14.3722278 14.2608065,14.4459677 C14.2512097,14.4612097 14.2342742,14.4708065 14.226371,14.4871774 L9.00854839,25.2372581 C8.87257056,25.5178226 8.98977823,25.8554738 9.27034274,25.9914516 C9.42579637,26.0668145 9.60721774,26.0668145 9.76274194,25.9914516 L20.5122581,20.7741935 C20.528629,20.7662903 20.5382258,20.7493548 20.5534677,20.7397581 C20.6272077,20.6909274 20.6903629,20.6277722 20.7391935,20.5540323 C20.7487903,20.5387903 20.7657258,20.5291935 20.773629,20.5128226 L25.9914516,9.76330645 C26.1274294,9.48274194 26.0102218,9.14509073 25.7296573,9.0091129 Z M10.7359677,24.2640323 L14.8958871,15.6935484 L19.3064516,20.1041129 L10.7359677,24.2640323 Z M20.1069355,19.3058871 L15.6935484,14.8958871 L24.2634677,10.7359677 L20.1069355,19.3058871 Z","id":"Shape","key":1})])])))]);
}

Navigator.defaultProps = {"width":"42px","height":"42px","viewBox":"0 0 42 42","version":"1.1"};

module.exports = Navigator;

Navigator.default = Navigator;


/***/ }),

/***/ "./src/block-editor/blocks/icons/products-navigator.svg":
/*!**************************************************************!*\
  !*** ./src/block-editor/blocks/icons/products-navigator.svg ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var React = __webpack_require__(/*! react */ "react");

function ProductsNavigator (props) {
    return React.createElement("svg",props,React.createElement("g",{"fill":"none","fillRule":"evenodd"},[React.createElement("path",{"d":"M0 0H81.233V81.233H0z","key":0}),React.createElement("g",{"fill":"#000","fillRule":"nonzero","key":1},[React.createElement("path",{"d":"M59.052 40.036c-.273 0-.55-.084-.787-.26-.59-.435-.716-1.266-.28-1.856l1.559-2.115-1.56-2.119c-.435-.59-.309-1.422.282-1.857.59-.434 1.422-.308 1.857.282l2.14 2.907c.345.468.345 1.107 0 1.575l-2.14 2.903c-.26.353-.663.54-1.07.54zM51.495 9.644c.734 0 1.329-.595 1.329-1.329 0-.733-.595-1.328-1.329-1.328h-.007c-.734 0-1.325.595-1.325 1.328 0 .734.599 1.329 1.332 1.329zM58.665 9.644c.734 0 1.329-.595 1.329-1.329 0-.733-.595-1.328-1.329-1.328h-.007c-.734 0-1.325.595-1.325 1.328 0 .734.599 1.329 1.332 1.329zM44.33 9.644c.733 0 1.327-.595 1.327-1.329 0-.733-.594-1.328-1.328-1.328h-.007c-.734 0-1.325.595-1.325 1.328 0 .734.599 1.329 1.332 1.329zM9.335 9.644h19.953c.734 0 1.329-.595 1.329-1.329 0-.733-.595-1.328-1.329-1.328H9.335c-.734 0-1.329.595-1.329 1.328 0 .734.595 1.329 1.329 1.329z","transform":"translate(7 7)","key":0}),React.createElement("path",{"d":"M63.737 0H4.259C1.911 0 0 1.91 0 4.257v59.482C0 66.09 1.91 68 4.26 68h59.477C66.087 68 68 66.089 68 63.74V4.256C68 1.91 66.088 0 63.737 0zM4.259 2.656h59.478c.886 0 1.607.718 1.607 1.601v9.718H2.656V4.257c0-.883.72-1.6 1.603-1.6zm59.478 62.688H4.259c-.884 0-1.603-.72-1.603-1.605V16.631h62.688v47.108c0 .885-.721 1.605-1.607 1.605z","transform":"translate(7 7)","key":1}),React.createElement("path",{"d":"M46.047 56.049h-3.862c-.733 0-1.328.594-1.328 1.328 0 .733.595 1.328 1.328 1.328h3.862c.734 0 1.328-.595 1.328-1.328 0-.734-.594-1.328-1.328-1.328zM36.357 56.049h-3.862c-.734 0-1.328.594-1.328 1.328 0 .733.594 1.328 1.328 1.328h3.862c.733 0 1.328-.595 1.328-1.328 0-.734-.595-1.328-1.328-1.328zM14.7 56.049h-3.862c-.734 0-1.328.594-1.328 1.328 0 .733.594 1.328 1.328 1.328H14.7c.733 0 1.328-.595 1.328-1.328 0-.734-.595-1.328-1.328-1.328zM23.595 52.988c-2.42 0-4.39 1.969-4.39 4.389 0 2.422 1.97 4.392 4.39 4.392 2.424 0 4.396-1.97 4.396-4.392 0-2.42-1.972-4.389-4.396-4.389zm0 6.125c-.956 0-1.735-.779-1.735-1.736 0-.955.779-1.733 1.735-1.733.96 0 1.74.778 1.74 1.733 0 .957-.78 1.736-1.74 1.736zM55.742 56.049H51.88c-.734 0-1.328.594-1.328 1.328 0 .733.594 1.328 1.328 1.328h3.862c.733 0 1.328-.595 1.328-1.328 0-.734-.595-1.328-1.328-1.328zM40.563 49.414h12.521c.73 0 1.335-.614 1.33-1.344V21.53c0-.734-.595-1.329-1.329-1.329H39.966c-.733 0-1.328.595-1.328 1.328 0 .734.595 1.329 1.328 1.329h11.791V43.82l-8.105-11.752c-.248-.359-.657-.574-1.093-.574-.437 0-.846.215-1.094.574l-5.177 7.504-7.466-10.824c-.248-.36-.657-.574-1.093-.574-.437 0-.846.215-1.093.574L16.239 43.821V22.858H28.03c.734 0 1.328-.595 1.328-1.329 0-.733-.594-1.328-1.328-1.328H14.91c-.733 0-1.327.595-1.327 1.328v26.542c-.007.729.6 1.343 1.329 1.343h25.651zm1.995-14.251l7.998 11.595h-9.312l-3.342-4.845 4.656-6.75zm-14.83-3.32l10.289 14.915H17.44L27.73 31.842z","transform":"translate(7 7)","key":2}),React.createElement("path",{"d":"M33.998 22.858h.008c.733 0 1.324-.595 1.324-1.329 0-.733-.598-1.328-1.332-1.328-.733 0-1.328.595-1.328 1.328 0 .734.595 1.329 1.328 1.329zM60.123 32.111c-.435-.59-1.266-.717-1.857-.282-.59.435-.717 1.267-.282 1.857l1.56 2.119-1.56 2.114c-.435.59-.31 1.422.281 1.858.238.174.514.259.787.259.408 0 .81-.187 1.07-.54l2.14-2.903c.346-.468.346-1.107.001-1.575l-2.14-2.907zM10.016 37.92l-1.56-2.115 1.56-2.119c.435-.59.309-1.422-.282-1.857-.59-.434-1.422-.308-1.857.282l-2.14 2.907c-.345.468-.345 1.107 0 1.575l2.14 2.903c.261.353.663.54 1.07.54.274 0 .55-.085.788-.26.59-.435.716-1.266.28-1.856z","transform":"translate(7 7)","key":3})])]));
}

ProductsNavigator.defaultProps = {"width":"82","height":"82","viewBox":"0 0 82 82"};

module.exports = ProductsNavigator;

ProductsNavigator.default = ProductsNavigator;


/***/ }),

/***/ "./src/block-editor/blocks/icons/timeline.svg":
/*!****************************************************!*\
  !*** ./src/block-editor/blocks/icons/timeline.svg ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var React = __webpack_require__(/*! react */ "react");

function Timeline (props) {
    return React.createElement("svg",props,[React.createElement("title",{"key":0},"Entities_Timeline"),React.createElement("g",{"id":"Page-1","stroke":"none","strokeWidth":"1","fill":"none","fillRule":"evenodd","key":1},React.createElement("g",{"id":"Entities-Timeline-Copy","transform":"translate(-902.000000, -164.000000)"},React.createElement("g",{"id":"Entities_Timeline","transform":"translate(902.000000, 164.000000)"},[React.createElement("rect",{"id":"Rectangle-Copy-3","x":"0","y":"0","width":"42","height":"42","key":0}),React.createElement("g",{"id":"calendar","transform":"translate(4.000000, 4.000000)","fill":"#000000","fillRule":"nonzero","key":1},[React.createElement("path",{"d":"M25.8873276,2.71551724 C25.6067241,1.34025862 24.3883621,0.301724138 22.9310345,0.301724138 C22.597931,0.301724138 22.3275862,0.571465517 22.3275862,0.905172414 C22.3275862,1.23887931 22.597931,1.50862069 22.9310345,1.50862069 C23.9291379,1.50862069 24.7413793,2.32086207 24.7413793,3.31896552 C24.7413793,4.31706897 23.9291379,5.12931034 22.9310345,5.12931034 C22.597931,5.12931034 22.3275862,5.39905172 22.3275862,5.73275862 C22.3275862,6.06646552 22.597931,6.3362069 22.9310345,6.3362069 C24.3877586,6.3362069 25.6067241,5.29767241 25.8873276,3.92241379 L33.7931034,3.92241379 L33.7931034,9.35344828 L1.20689655,9.35344828 L1.20689655,3.92241379 L9.65517241,3.92241379 L11.4655172,3.92241379 C11.7986207,3.92241379 12.0689655,3.65267241 12.0689655,3.31896552 C12.0689655,2.98525862 11.7986207,2.71551724 11.4655172,2.71551724 L10.3696552,2.71551724 C10.6194828,2.01431034 11.2832759,1.50862069 12.0689655,1.50862069 C13.067069,1.50862069 13.8793103,2.32086207 13.8793103,3.31896552 C13.8793103,4.31706897 13.067069,5.12931034 12.0689655,5.12931034 C11.7358621,5.12931034 11.4655172,5.39905172 11.4655172,5.73275862 C11.4655172,6.06646552 11.7358621,6.3362069 12.0689655,6.3362069 C13.7326724,6.3362069 15.0862069,4.98267241 15.0862069,3.31896552 C15.0862069,1.65525862 13.7326724,0.301724138 12.0689655,0.301724138 C10.6122414,0.301724138 9.39327586,1.34025862 9.11267241,2.71551724 L0,2.71551724 L0,10.5603448 L0,34.6982759 L35,34.6982759 L35,10.5603448 L35,2.71551724 L25.8873276,2.71551724 Z M33.7931034,33.4913793 L1.20689655,33.4913793 L1.20689655,10.5603448 L33.7931034,10.5603448 L33.7931034,33.4913793 Z","id":"Shape","key":0}),React.createElement("path",{"d":"M15.6896552,1.50862069 C16.6877586,1.50862069 17.5,2.32086207 17.5,3.31896552 C17.5,4.31706897 16.6877586,5.12931034 15.6896552,5.12931034 C15.3565517,5.12931034 15.0862069,5.39905172 15.0862069,5.73275862 C15.0862069,6.06646552 15.3565517,6.3362069 15.6896552,6.3362069 C17.3533621,6.3362069 18.7068966,4.98267241 18.7068966,3.31896552 C18.7068966,1.65525862 17.3533621,0.301724138 15.6896552,0.301724138 C15.3565517,0.301724138 15.0862069,0.571465517 15.0862069,0.905172414 C15.0862069,1.23887931 15.3565517,1.50862069 15.6896552,1.50862069 Z","id":"Path","key":1}),React.createElement("path",{"d":"M19.3103448,1.50862069 C20.3084483,1.50862069 21.1206897,2.32086207 21.1206897,3.31896552 C21.1206897,4.31706897 20.3084483,5.12931034 19.3103448,5.12931034 C18.9772414,5.12931034 18.7068966,5.39905172 18.7068966,5.73275862 C18.7068966,6.06646552 18.9772414,6.3362069 19.3103448,6.3362069 C20.9740517,6.3362069 22.3275862,4.98267241 22.3275862,3.31896552 C22.3275862,1.65525862 20.9740517,0.301724138 19.3103448,0.301724138 C18.9772414,0.301724138 18.7068966,0.571465517 18.7068966,0.905172414 C18.7068966,1.23887931 18.9772414,1.50862069 19.3103448,1.50862069 Z","id":"Path","key":2}),React.createElement("path",{"d":"M5.43103448,15.387931 L18.7068966,15.387931 C19.04,15.387931 19.3103448,15.1181897 19.3103448,14.7844828 C19.3103448,14.4507759 19.04,14.1810345 18.7068966,14.1810345 L5.43103448,14.1810345 C5.09793103,14.1810345 4.82758621,14.4507759 4.82758621,14.7844828 C4.82758621,15.1181897 5.09793103,15.387931 5.43103448,15.387931 Z","id":"Path","key":3}),React.createElement("path",{"d":"M29.5689655,23.8362069 L20.5172414,23.8362069 C20.1841379,23.8362069 19.9137931,24.1059483 19.9137931,24.4396552 C19.9137931,24.7733621 20.1841379,25.0431034 20.5172414,25.0431034 L29.5689655,25.0431034 C29.902069,25.0431034 30.1724138,24.7733621 30.1724138,24.4396552 C30.1724138,24.1059483 29.902069,23.8362069 29.5689655,23.8362069 Z","id":"Path","key":4}),React.createElement("path",{"d":"M5.43103448,20.2155172 L11.4655172,20.2155172 C11.7986207,20.2155172 12.0689655,19.9457759 12.0689655,19.612069 C12.0689655,19.2783621 11.7986207,19.0086207 11.4655172,19.0086207 L5.43103448,19.0086207 C5.09793103,19.0086207 4.82758621,19.2783621 4.82758621,19.612069 C4.82758621,19.9457759 5.09793103,20.2155172 5.43103448,20.2155172 Z","id":"Path","key":5}),React.createElement("path",{"d":"M29.5689655,28.6637931 L23.5344828,28.6637931 C23.2013793,28.6637931 22.9310345,28.9335345 22.9310345,29.2672414 C22.9310345,29.6009483 23.2013793,29.8706897 23.5344828,29.8706897 L29.5689655,29.8706897 C29.902069,29.8706897 30.1724138,29.6009483 30.1724138,29.2672414 C30.1724138,28.9335345 29.902069,28.6637931 29.5689655,28.6637931 Z","id":"Path","key":6}),React.createElement("path",{"d":"M16.8965517,19.612069 C16.8965517,19.9457759 17.1668966,20.2155172 17.5,20.2155172 L26.5517241,20.2155172 C26.8848276,20.2155172 27.1551724,19.9457759 27.1551724,19.612069 C27.1551724,19.2783621 26.8848276,19.0086207 26.5517241,19.0086207 L17.5,19.0086207 C17.1668966,19.0086207 16.8965517,19.2783621 16.8965517,19.612069 Z","id":"Path","key":7}),React.createElement("path",{"d":"M14.9112069,20.0405172 C15.0198276,19.9258621 15.0862069,19.7689655 15.0862069,19.612069 C15.0862069,19.454569 15.0198276,19.2976724 14.9112069,19.1836207 C14.687931,18.9603448 14.2775862,18.9603448 14.0543103,19.1836207 C13.9456897,19.2976724 13.8793103,19.454569 13.8793103,19.612069 C13.8793103,19.7689655 13.9456897,19.9258621 14.0543103,20.0405172 C14.1689655,20.154569 14.3258621,20.2155172 14.4827586,20.2155172 C14.6396552,20.2155172 14.7965517,20.154569 14.9112069,20.0405172 Z","id":"Path","key":8}),React.createElement("path",{"d":"M7.84482759,24.4396552 C7.84482759,24.7733621 8.11517241,25.0431034 8.44827586,25.0431034 L17.5,25.0431034 C17.8331034,25.0431034 18.1034483,24.7733621 18.1034483,24.4396552 C18.1034483,24.1059483 17.8331034,23.8362069 17.5,23.8362069 L8.44827586,23.8362069 C8.11517241,23.8362069 7.84482759,24.1059483 7.84482759,24.4396552 Z","id":"Path","key":9}),React.createElement("path",{"d":"M5.43103448,25.0431034 C5.58793103,25.0431034 5.74482759,24.9767241 5.85948276,24.8681034 C5.96810345,24.7534483 6.03448276,24.5965517 6.03448276,24.4396552 C6.03448276,24.2821552 5.96810345,24.1252586 5.85948276,24.0172414 C5.6362069,23.787931 5.22586207,23.787931 5.00258621,24.0112069 C4.89396552,24.1252586 4.82758621,24.2821552 4.82758621,24.4396552 C4.82758621,24.5965517 4.89396552,24.7534483 5.00258621,24.8681034 C5.11724138,24.9767241 5.26810345,25.0431034 5.43103448,25.0431034 Z","id":"Path","key":10}),React.createElement("path",{"d":"M29.5689655,20.2155172 C29.7258621,20.2155172 29.8827586,20.154569 29.9974138,20.0405172 C30.112069,19.9258621 30.1724138,19.7689655 30.1724138,19.612069 C30.1724138,19.454569 30.112069,19.2976724 29.9974138,19.1836207 C29.7681034,18.9603448 29.3637931,18.9603448 29.1405172,19.1836207 C29.0318966,19.2976724 28.9655172,19.454569 28.9655172,19.612069 C28.9655172,19.7689655 29.0318966,19.9258621 29.1405172,20.0405172 C29.2551724,20.154569 29.412069,20.2155172 29.5689655,20.2155172 Z","id":"Path","key":11}),React.createElement("path",{"d":"M20.5172414,28.6637931 L5.43103448,28.6637931 C5.09793103,28.6637931 4.82758621,28.9335345 4.82758621,29.2672414 C4.82758621,29.6009483 5.09793103,29.8706897 5.43103448,29.8706897 L20.5172414,29.8706897 C20.8503448,29.8706897 21.1206897,29.6009483 21.1206897,29.2672414 C21.1206897,28.9335345 20.8503448,28.6637931 20.5172414,28.6637931 Z","id":"Path","key":12})])])))]);
}

Timeline.defaultProps = {"width":"42px","height":"42px","viewBox":"0 0 42 42","version":"1.1"};

module.exports = Timeline;

Timeline.default = Timeline;


/***/ }),

/***/ "./src/block-editor/blocks/icons/vocabulary.svg":
/*!******************************************************!*\
  !*** ./src/block-editor/blocks/icons/vocabulary.svg ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var React = __webpack_require__(/*! react */ "react");

function Vocabulary (props) {
    return React.createElement("svg",props,[React.createElement("title",{"key":0},"Glossary"),React.createElement("g",{"id":"Page-1","stroke":"none","strokeWidth":"1","fill":"none","fillRule":"evenodd","key":1},React.createElement("g",{"id":"Entities-Timeline-Copy","transform":"translate(-902.000000, -215.000000)"},React.createElement("g",{"id":"Glossary","transform":"translate(902.000000, 215.000000)"},[React.createElement("rect",{"id":"Rectangle-Copy-8","x":"0","y":"0","width":"42","height":"42","key":0}),React.createElement("g",{"id":"abc","transform":"translate(4.000000, 4.000000)","fill":"#000000","fillRule":"nonzero","key":1},[React.createElement("path",{"d":"M14.3812402,27.6707129 L13.3195508,27.6707129 C12.4514551,27.6707129 11.745166,28.377002 11.745166,29.2450977 L11.745166,31.1031055 C11.745166,31.9712012 12.4514551,32.6774902 13.3195508,32.6774902 L14.3812402,32.6774902 C14.9566211,32.6774902 15.4248145,32.2092969 15.4248145,31.633916 L15.4248145,31.3684766 C15.4248145,31.085332 15.195332,30.8557812 14.9121191,30.8557812 C14.6289063,30.8557812 14.3994238,31.085332 14.3994238,31.3684766 L14.3994238,31.633916 C14.3994238,31.6439648 14.3912207,31.6520996 14.3812402,31.6520996 L13.3195508,31.6520996 C13.0168555,31.6520996 12.7705566,31.4058008 12.7705566,31.1031055 L12.7705566,29.2450977 C12.7705566,28.9424023 13.0167871,28.6961035 13.3195508,28.6961035 L14.3812402,28.6961035 C14.3912891,28.6961035 14.3994238,28.7043066 14.3994238,28.7142871 L14.3994238,28.9797266 C14.3994238,29.2628711 14.6289063,29.4924219 14.9121191,29.4924219 C15.195332,29.4924219 15.4248145,29.2628711 15.4248145,28.9797266 L15.4248145,28.7142871 C15.4248145,28.1388379 14.9566211,27.6707129 14.3812402,27.6707129 Z","id":"Path","key":0}),React.createElement("path",{"d":"M13.5849902,25.3482715 C10.9239648,25.3482715 8.7590918,27.5131445 8.7590918,30.1741699 C8.7590918,32.8351953 10.9239648,35 13.5849902,35 C16.2460156,35 18.4108887,32.835127 18.4108887,30.1741016 C18.4108887,27.5130762 16.2459473,25.3482715 13.5849902,25.3482715 Z M13.5849902,33.9746094 C11.4894336,33.9746094 9.78448242,32.2697266 9.78448242,30.1741016 C9.78448242,28.0784766 11.4893652,26.3735937 13.5849902,26.3735937 C15.6806152,26.3735937 17.385498,28.0784766 17.385498,30.1741016 C17.385498,32.2697266 15.6805469,33.9746094 13.5849902,33.9746094 Z","id":"Shape","key":1}),React.createElement("path",{"d":"M26.6129883,16.5891797 L24.8435059,16.5891797 C24.560293,16.5891797 24.3308105,16.8187305 24.3308105,17.101875 L24.3308105,23.7375195 C24.3308105,24.0206641 24.560293,24.2502148 24.8435059,24.2502148 L27.7189062,24.2502148 C29.0992871,24.2502148 30.2222949,23.127207 30.2222949,21.7468262 C30.2222949,20.543291 29.3685547,19.5354004 28.2348828,19.2968945 C28.3738574,19.0382227 28.4528125,18.7426367 28.4528125,18.4290039 C28.4528125,17.4145508 27.6274414,16.5891797 26.6129883,16.5891797 Z M25.3562012,17.6145703 L26.6129883,17.6145703 C27.062041,17.6145703 27.4274219,17.9799512 27.4274219,18.4290039 C27.4274219,18.8780566 27.062041,19.2434375 26.6129883,19.2434375 L25.3562012,19.2434375 L25.3562012,17.6145703 Z M29.1969043,21.7468262 C29.1969043,22.5618066 28.5338867,23.2248242 27.7189062,23.2248242 L25.3562012,23.2248242 L25.3562012,20.2688281 L27.7189062,20.2688281 C28.5338867,20.2688281 29.1969043,20.9318457 29.1969043,21.7468262 Z","id":"Shape","key":2}),React.createElement("path",{"d":"M8.29834961,16.932207 L9.50865234,13.3013672 L14.0780664,13.3013672 L15.2883691,16.932207 C15.3600098,17.1470605 15.5600293,17.2828906 15.7746777,17.2828906 C15.8284082,17.2828906 15.8830957,17.2743457 15.9368945,17.2564355 C16.2055469,17.1668848 16.3506738,16.8764941 16.261123,16.6079102 L14.9378906,12.6381445 C14.935498,12.6302832 14.9329004,12.6224902 14.9300977,12.6147656 L13.1531641,7.28396484 C12.957793,6.69771484 12.4112598,6.30389648 11.7933594,6.30389648 C11.175459,6.30389648 10.6289941,6.6977832 10.4335547,7.28396484 L8.65662109,12.6147656 C8.65381836,12.6224902 8.6512207,12.6302832 8.64882813,12.6382129 L7.3255957,16.6079785 C7.23604492,16.8765625 7.38124023,17.1669531 7.64982422,17.2565039 C7.91833984,17.3458496 8.20873047,17.200791 8.29834961,16.932207 Z M11.4063086,7.60819336 C11.4618848,7.44132813 11.6174707,7.32921875 11.793291,7.32921875 C11.9691113,7.32921875 12.1246973,7.44132813 12.1802734,7.60819336 L13.7362012,12.2759766 L9.85038086,12.2759766 L11.4063086,7.60819336 Z","id":"Shape","key":3}),React.createElement("path",{"d":"M31.522832,13.5743945 C31.2941016,13.4071875 30.9734961,13.4569531 30.8063574,13.6855469 C30.6392187,13.9140723 30.6889844,14.2348828 30.9175781,14.4020215 C32.8318457,15.8019531 33.9746777,18.0515918 33.9746777,20.419834 C33.9746777,24.5278223 30.6325879,27.8699121 26.5245996,27.8699121 C22.666875,27.8699121 19.4183691,24.8772754 19.100293,21.0443652 C21.3746777,19.2440527 22.9725098,16.6237695 23.4432324,13.6346191 C24.4129785,13.1933594 25.4495117,12.9696875 26.5245996,12.9696875 C27.5995508,12.9696875 28.6364258,13.1934961 29.6064453,13.6348926 C29.8643652,13.7522656 30.1682227,13.6383105 30.285459,13.3805273 C30.4027637,13.1228125 30.288877,12.8188184 30.0311621,12.7015137 C28.9268164,12.1990039 27.7470703,11.9442285 26.5245996,11.9442285 C25.5023535,11.9442285 24.5103223,12.1222363 23.5673047,12.4742188 C23.5801563,12.2488379 23.5867188,12.0219531 23.5867188,11.7933594 C23.5867187,5.29046875 18.29625,0 11.7933594,0 C5.29046875,0 0,5.29046875 0,11.7933594 C0,14.842666 1.15978516,17.7357715 3.26573242,19.9396777 C3.46130859,20.1444141 3.78581055,20.1517285 3.99061523,19.9561523 C4.1952832,19.7605762 4.20266602,19.4360059 4.00708984,19.2312695 C2.08427734,17.2191113 1.02539062,14.5775684 1.02539062,11.7933594 C1.02539062,5.85586914 5.8559375,1.02539063 11.7933594,1.02539063 C17.7307813,1.02539063 22.5613281,5.85573242 22.5613281,11.7932227 C22.5613281,17.7307129 17.7307813,22.5611914 11.7933594,22.5611914 C9.43393555,22.5611914 7.19407227,21.8124512 5.31576172,20.3958398 C5.08969727,20.2252832 4.76813477,20.2704004 4.59771484,20.4964648 C4.42722656,20.7225293 4.47227539,21.0440234 4.6984082,21.2145117 C6.75595703,22.7664063 9.209375,23.5866504 11.7933594,23.5866504 C14.1319336,23.5866504 16.3134863,22.9025781 18.1487988,21.7238574 C18.4376855,23.5947168 19.3448828,25.3132715 20.7451562,26.619209 C22.3189941,28.0868848 24.371416,28.895166 26.5245312,28.895166 C31.1979199,28.895166 35,25.0930859 35,20.4196973 C35,17.7255859 33.7000781,15.1665527 31.522832,13.5743945 Z","id":"Path","key":4})])])))]);
}

Vocabulary.defaultProps = {"width":"42px","height":"42px","viewBox":"0 0 42 42","version":"1.1"};

module.exports = Vocabulary;

Vocabulary.default = Vocabulary;


/***/ }),

/***/ "./src/block-editor/blocks/index.js":
/*!******************************************!*\
  !*** ./src/block-editor/blocks/index.js ***!
  \******************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _icons_chord_svg__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./icons/chord.svg */ "./src/block-editor/blocks/icons/chord.svg");
/* harmony import */ var _icons_chord_svg__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_icons_chord_svg__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _icons_entities_cloud_svg__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./icons/entities-cloud.svg */ "./src/block-editor/blocks/icons/entities-cloud.svg");
/* harmony import */ var _icons_entities_cloud_svg__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_icons_entities_cloud_svg__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _icons_faceted_search_svg__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./icons/faceted-search.svg */ "./src/block-editor/blocks/icons/faceted-search.svg");
/* harmony import */ var _icons_faceted_search_svg__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_icons_faceted_search_svg__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _icons_geomap_svg__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./icons/geomap.svg */ "./src/block-editor/blocks/icons/geomap.svg");
/* harmony import */ var _icons_geomap_svg__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_icons_geomap_svg__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _icons_navigator_svg__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./icons/navigator.svg */ "./src/block-editor/blocks/icons/navigator.svg");
/* harmony import */ var _icons_navigator_svg__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_icons_navigator_svg__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _icons_products_navigator_svg__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./icons/products-navigator.svg */ "./src/block-editor/blocks/icons/products-navigator.svg");
/* harmony import */ var _icons_products_navigator_svg__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_icons_products_navigator_svg__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _icons_timeline_svg__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./icons/timeline.svg */ "./src/block-editor/blocks/icons/timeline.svg");
/* harmony import */ var _icons_timeline_svg__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_icons_timeline_svg__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var _icons_vocabulary_svg__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./icons/vocabulary.svg */ "./src/block-editor/blocks/icons/vocabulary.svg");
/* harmony import */ var _icons_vocabulary_svg__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_icons_vocabulary_svg__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var _common_constants__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../../common/constants */ "./src/common/constants.js");
/* harmony import */ var _blocks_scss__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./blocks.scss */ "./src/block-editor/blocks/blocks.scss");
/* harmony import */ var _blocks_scss__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(_blocks_scss__WEBPACK_IMPORTED_MODULE_15__);
/**
 * External dependencies.
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */











const wlSettings = global["wlSettings"];

const humanize = str => {
  return str.replace(/^[\s_]+|[\s_]+$/g, "").replace(/[_\s]+/g, " ").replace(/^[a-z]/, function (m) {
    return m.toUpperCase();
  });
};

const BlockPreview = ({
  title,
  attributes,
  icon
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("h4", null, title), attributes && Object.keys(attributes).map(key => !["preview", "preview_src"].includes(key) && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
  style: {
    fontSize: "0.8rem"
  }
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("span", {
  style: {
    width: "140px",
    display: "inline-block",
    fontWeight: "bold"
  }
}, humanize(key)), " ", typeof attributes[key] === "boolean" ? JSON.stringify(attributes[key]) : attributes[key])));

const blocks = {
  [`${_common_constants__WEBPACK_IMPORTED_MODULE_14__["PLUGIN_NAMESPACE"]}/faceted-search`]: {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Faceted Search", "wordlift"),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Using the Faceted Search Widget readers, selecting concepts they are interested in, can find all related articles.", "wordlift"),
    category: "wordlift",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_icons_faceted_search_svg__WEBPACK_IMPORTED_MODULE_8___default.a, null),
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({
      attributes,
      setAttributes
    }) => {
      const {
        title,
        template_id,
        post_id,
        uniqid,
        limit,
        preview,
        preview_src,
        post_types
      } = attributes;

      if (preview) {
        return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("img", {
          src: preview_src
        }));
      }

      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(BlockPreview, {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("WordLift Faceted Search", "wordlift"),
        attributes: attributes
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InspectorControls"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Widget Settings", "wordlift"),
        className: "blocks-font-size"
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Title", "wordlift"),
        value: title,
        onChange: title => setAttributes({
          title
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["RangeControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Limit", "wordlift"),
        value: limit,
        min: 2,
        max: 20,
        onChange: limit => setAttributes({
          limit
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Template ID", "wordlift"),
        help: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("ID of the script tag that has mustache template to be used for Faceted Search widget.", "wordlift"),
        value: template_id,
        onChange: template_id => setAttributes({
          template_id
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Post ID", "wordlift"),
        help: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Post ID of the post of which Faceted Search widget has to be shown.", "wordlift"),
        type: "number",
        value: post_id,
        onChange: post_id => setAttributes({
          post_id
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Unique ID", "wordlift"),
        value: uniqid,
        onChange: uniqid => setAttributes({
          uniqid
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Post types", "wordlift"),
        help: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("The Post types which should be shown on faceted search results separated by comma, For example: post,page", "wordlift"),
        value: post_types,
        onChange: post_types => setAttributes({
          post_types
        })
      }))));
    },

    save() {
      return null; //save has to exist. This all we need
    }

  },
  [`${_common_constants__WEBPACK_IMPORTED_MODULE_14__["PLUGIN_NAMESPACE"]}/navigator`]: {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Navigator", "wordlift"),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("The Navigator Widget provides content recommendations by presenting relevant links to other blog posts on your website.", "wordlift"),
    category: "wordlift",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_icons_navigator_svg__WEBPACK_IMPORTED_MODULE_10___default.a, null),
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({
      attributes,
      setAttributes
    }) => {
      const {
        title,
        limit,
        template_id,
        post_id,
        offset,
        uniqid,
        order_by,
        preview,
        preview_src,
        post_types
      } = attributes;

      if (preview) {
        return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("img", {
          src: preview_src
        }));
      }

      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(BlockPreview, {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("WordLift Navigator", "wordlift"),
        attributes: attributes
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InspectorControls"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
        title: "Widget Settings",
        className: "blocks-font-size"
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Title",
        value: title,
        onChange: title => setAttributes({
          title
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["RangeControl"], {
        label: "Limit",
        value: limit,
        min: 2,
        max: 20,
        onChange: limit => setAttributes({
          limit
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["RangeControl"], {
        label: "Offset",
        value: offset,
        min: 0,
        max: 20,
        onChange: offset => setAttributes({
          offset
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Template ID",
        help: "ID of the script tag that has mustache template to be used for navigator.",
        value: template_id,
        onChange: template_id => setAttributes({
          template_id
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Post ID",
        help: "Post ID of the post of which navigator has to be shown.",
        type: "number",
        value: post_id,
        onChange: post_id => setAttributes({
          post_id
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Unique ID",
        value: uniqid,
        onChange: uniqid => setAttributes({
          uniqid
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Order by",
        help: "Valid SQL \u2018order by\u2019 clause",
        value: order_by,
        onChange: order_by => setAttributes({
          order_by
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Post types", "wordlift"),
        help: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("The Post types which should be shown on navigator results separated by comma, For example: post,page", "wordlift"),
        value: post_types,
        onChange: post_types => setAttributes({
          post_types
        })
      }))));
    },

    save() {
      return null; //save has to exist. This all we need
    }

  },
  [`${_common_constants__WEBPACK_IMPORTED_MODULE_14__["PLUGIN_NAMESPACE"]}/products-navigator`]: {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Products Navigator", "wordlift"),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("The Products' Navigator Widget provides product recommendations by presenting relevant links to other products on your website.", "wordlift"),
    category: "wordlift",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_icons_products_navigator_svg__WEBPACK_IMPORTED_MODULE_11___default.a, null),
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({
      attributes,
      setAttributes
    }) => {
      const {
        title,
        limit,
        template_id,
        post_id,
        offset,
        uniqid,
        order_by,
        preview,
        preview_src
      } = attributes;

      if (preview) {
        return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("img", {
          src: preview_src
        }));
      }

      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(BlockPreview, {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("WordLift Products Navigator", "wordlift"),
        attributes: attributes
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InspectorControls"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
        title: "Widget Settings",
        className: "blocks-font-size"
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Title",
        value: title,
        onChange: title => setAttributes({
          title
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["RangeControl"], {
        label: "Limit",
        value: limit,
        min: 2,
        max: 20,
        onChange: limit => setAttributes({
          limit
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["RangeControl"], {
        label: "Offset",
        value: offset,
        min: 0,
        max: 20,
        onChange: offset => setAttributes({
          offset
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Template ID",
        help: "ID of the script tag that has mustache template to be used for navigator.",
        value: template_id,
        onChange: template_id => setAttributes({
          template_id
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Post ID",
        help: "Post ID of the post of which navigator has to be shown.",
        type: "number",
        value: post_id,
        onChange: post_id => setAttributes({
          post_id
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Unique ID",
        value: uniqid,
        onChange: uniqid => setAttributes({
          uniqid
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Order by",
        help: "Valid SQL \u2018order by\u2019 clause",
        value: order_by,
        onChange: order_by => setAttributes({
          order_by
        })
      }))));
    },

    save() {
      return null; //save has to exist. This all we need
    }

  },
  [`${_common_constants__WEBPACK_IMPORTED_MODULE_14__["PLUGIN_NAMESPACE"]}/chord`]: {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Chord", "wordlift"),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("The Chord Widget visualizes the relations between entities within a given article.", "wordlift"),
    category: "wordlift",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_icons_chord_svg__WEBPACK_IMPORTED_MODULE_6___default.a, null),
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({
      attributes,
      setAttributes
    }) => {
      const {
        width,
        height,
        main_color,
        depth,
        global,
        preview,
        preview_src
      } = attributes;

      if (preview) {
        return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("img", {
          src: preview_src
        }));
      }

      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(BlockPreview, {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("WordLift Chord", "wordlift"),
        attributes: attributes
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InspectorControls"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
        title: "Widget Settings",
        className: "blocks-font-size"
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Width",
        value: width,
        onChange: width => setAttributes({
          width
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Height",
        value: height,
        onChange: height => setAttributes({
          height
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["RangeControl"], {
        label: "Depth",
        value: depth,
        min: 1,
        max: 10,
        onChange: depth => setAttributes({
          depth
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("label", {
        className: "components-base-control__label"
      }, "Main color"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["ColorPicker"], {
        color: main_color,
        onChangeComplete: value => setAttributes({
          main_color: value.hex
        }),
        disableAlpha: true
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["CheckboxControl"], {
        label: "Global",
        checked: global,
        onChange: global => setAttributes({
          global
        })
      }))));
    },

    save() {
      return null; //save has to exist. This all we need
    }

  },
  [`${_common_constants__WEBPACK_IMPORTED_MODULE_14__["PLUGIN_NAMESPACE"]}/geomap`]: {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Geomap", "wordlift"),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("This Widget displays entities of type “Place” mentioned in the article on a Geomap.", "wordlift"),
    category: "wordlift",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_icons_geomap_svg__WEBPACK_IMPORTED_MODULE_9___default.a, null),
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({
      attributes,
      setAttributes
    }) => {
      const {
        width,
        height,
        global,
        preview,
        preview_src
      } = attributes;

      if (preview) {
        return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("img", {
          src: preview_src
        }));
      }

      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(BlockPreview, {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("WordLift Geomap", "wordlift"),
        attributes: attributes
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InspectorControls"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
        title: "Widget Settings",
        className: "blocks-font-size"
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Width",
        value: width,
        onChange: width => setAttributes({
          width
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Height",
        value: height,
        onChange: height => setAttributes({
          height
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["CheckboxControl"], {
        label: "Global",
        checked: global,
        onChange: global => setAttributes({
          global
        })
      }))));
    },

    save() {
      return null; //save has to exist. This all we need
    }

  },
  [`${_common_constants__WEBPACK_IMPORTED_MODULE_14__["PLUGIN_NAMESPACE"]}/cloud`]: {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Entities Cloud", "wordlift"),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("The Entity Cloud Widget is a site-wide Widget and a shortcode that displays entities related to the current post/entity in a tag cloud.", "wordlift"),
    category: "wordlift",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_icons_entities_cloud_svg__WEBPACK_IMPORTED_MODULE_7___default.a, null),
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({
      attributes,
      setAttributes
    }) => {
      const {
        preview,
        preview_src
      } = attributes;

      if (preview) {
        return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("img", {
          src: preview_src
        }));
      }

      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(BlockPreview, {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("WordLift Entities Cloud", "wordlift")
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InspectorControls"], null));
    },

    save() {
      return null; //save has to exist. This all we need
    }

  },
  [`${_common_constants__WEBPACK_IMPORTED_MODULE_14__["PLUGIN_NAMESPACE"]}/vocabulary`]: {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Glossary", "wordlift"),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("The Glossary is a site-wide Widget that displays all the entities in alphabetical order.", "wordlift"),
    category: "wordlift",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_icons_vocabulary_svg__WEBPACK_IMPORTED_MODULE_13___default.a, null),
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({
      attributes,
      setAttributes
    }) => {
      const {
        type,
        limit,
        orderby,
        order,
        cat,
        preview,
        preview_src
      } = attributes;
      const typeOptions = [{
        value: "all",
        label: "All"
      }];
      const orderbyOptions = [{
        value: "post_date",
        label: "Date"
      }, {
        value: "ID",
        label: "Post ID"
      }, {
        value: "author",
        label: "Author"
      }, {
        value: "title",
        label: "Title"
      }, {
        value: "name",
        label: "Name (post slug)"
      }, {
        value: "type",
        label: "Post type"
      }, {
        value: "date",
        label: "Date"
      }, {
        value: "modified",
        label: "Last modified date"
      }, {
        value: "parent",
        label: "Post/page parent ID"
      }, {
        value: "comment_count",
        label: "Number of comments"
      }, {
        value: "menu_order",
        label: "Page Order"
      }, {
        value: "rand",
        label: "Random order"
      }, {
        value: "none",
        label: "None"
      }];
      const orderOptions = [{
        value: "ASC",
        label: "Ascending"
      }, {
        value: "DESC",
        label: "Descending"
      }];
      window["_wlEntityTypes"].forEach(item => {
        typeOptions.push({
          value: item.slug,
          label: item.label
        });
      });

      if (preview) {
        return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("img", {
          src: preview_src
        }));
      }

      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(BlockPreview, {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("WordLift Vocabulary", "wordlift"),
        attributes: attributes
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InspectorControls"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
        title: "Widget Settings",
        className: "blocks-font-size"
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["SelectControl"], {
        label: "Type",
        value: type,
        onChange: type => setAttributes({
          type
        }),
        options: typeOptions
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["SelectControl"], {
        label: "Order by",
        value: orderby,
        onChange: orderby => setAttributes({
          orderby
        }),
        options: orderbyOptions
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["RadioControl"], {
        label: "Order",
        selected: order,
        options: orderOptions,
        onChange: order => setAttributes({
          order
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["RangeControl"], {
        label: "Limit",
        value: limit,
        min: -1,
        max: 200,
        onChange: limit => setAttributes({
          limit
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
        label: "Category ID",
        value: cat,
        onChange: cat => setAttributes({
          cat
        })
      }))));
    },

    save() {
      return null; //save has to exist. This all we need
    }

  },
  [`${_common_constants__WEBPACK_IMPORTED_MODULE_14__["PLUGIN_NAMESPACE"]}/timeline`]: {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("Timeline", "wordlift"),
    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("WordLift uses the powerful TimelineJS to create beautiful, interactive timelines.", "wordlift"),
    category: "wordlift",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_icons_timeline_svg__WEBPACK_IMPORTED_MODULE_12___default.a, null),
    example: {
      attributes: {
        preview: true
      }
    },
    //display the edit interface + preview
    edit: ({
      attributes,
      setAttributes
    }) => {
      const {
        display_images_as,
        excerpt_length,
        global,
        timelinejs_options,
        preview,
        preview_src
      } = attributes;

      if (preview) {
        return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("img", {
          src: preview_src
        }));
      }

      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(BlockPreview, {
        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])("WordLift Timeline", "wordlift"),
        attributes: attributes
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InspectorControls"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], {
        title: "Widget Settings",
        className: "blocks-font-size"
      }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["RadioControl"], {
        label: "Display images as",
        selected: display_images_as,
        onChange: display_images_as => setAttributes({
          display_images_as
        }),
        options: [{
          value: "media",
          label: "Media"
        }, {
          value: "background",
          label: "Background"
        }]
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["RangeControl"], {
        label: "Excerpt length",
        value: excerpt_length,
        min: 10,
        max: 200,
        onChange: excerpt_length => setAttributes({
          excerpt_length
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["CheckboxControl"], {
        label: "Global",
        checked: global,
        onChange: global => setAttributes({
          global
        })
      }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextareaControl"], {
        label: "Timelinejs options",
        help: "Enter options as JSON string",
        value: timelinejs_options,
        rows: 8,
        onChange: timelinejs_options => setAttributes({
          timelinejs_options
        })
      }))));
    },

    save() {
      return null; //save has to exist. This all we need
    }

  }
};
/**
 * Register all blocks (widgets)
 */

for (let block in blocks) {
  if (window.wlEnabledBlocks && window.wlEnabledBlocks.includes(block)) {
    Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_5__["registerBlockType"])(block, blocks[block]);
  }
}
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./src/block-editor/blocks/register-block-type-wordlift-classification.js":
/*!********************************************************************************!*\
  !*** ./src/block-editor/blocks/register-block-type-wordlift-classification.js ***!
  \********************************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/**
 * This file registers the WordLift Entities block type.
 *
 * The WordLift Entities block type is used to store entity data for the current
 * post.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-registration/
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 * @see https://github.com/insideout10/wordlift-plugin/issues/944
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


 // Registering my block with a unique name

Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__["registerBlockType"])("wordlift/classification", {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])("WordLift Classification", "wordlift"),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])("A block holding the classification data for the current post.", "wordlift"),
  category: "wordlift",
  attributes: {
    entities: {
      type: "array"
    }
  },
  supports: {
    // Do not support HTML editing.
    html: false,
    // Only support being inserted programmatically.
    inserter: false,
    // Only allow one block.
    multiple: false,
    // Do not allow reusability.
    reusable: false
  },
  edit: () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, "WordLift Classification (edit)"),
  save: () => null
});

/***/ }),

/***/ "./src/block-editor/containers/sidebar.js":
/*!************************************************!*\
  !*** ./src/block-editor/containers/sidebar.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var _common_components_spinner_with_spinner__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../common/components/spinner/with-spinner */ "./src/common/components/spinner/with-spinner.js");
/* harmony import */ var _Edit_components_App__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../Edit/components/App */ "./src/Edit/components/App/index.js");
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


 // Add the spinner to the sidebar component.

const SidebarWithSpinner = Object(_common_components_spinner_with_spinner__WEBPACK_IMPORTED_MODULE_1__["default"])(_Edit_components_App__WEBPACK_IMPORTED_MODULE_2__["default"]); // Connect the spinner to the `blockEditor.loading` state.

/* harmony default export */ __webpack_exports__["default"] = (Object(react_redux__WEBPACK_IMPORTED_MODULE_0__["connect"])(({
  blockEditor
}) => ({
  loading: blockEditor.loading
}))(SidebarWithSpinner));

/***/ }),

/***/ "./src/block-editor/filters/add-entity-notice-container.js":
/*!*****************************************************************!*\
  !*** ./src/block-editor/filters/add-entity-notice-container.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/**
 * External dependencies.
 */


/**
 * WordPress dependencies
 */



const mapStateToProps = ({
  showNotice
}) => ({
  showNotice
});

const AddEntityNotice = ({
  showNotice
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
  className: "wl-notice"
}, showNotice && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Notice"], {
  status: "success",
  isDismissible: false
}, "The entity was created!"));

/* harmony default export */ __webpack_exports__["default"] = (Object(react_redux__WEBPACK_IMPORTED_MODULE_1__["connect"])(mapStateToProps)(AddEntityNotice));

/***/ }),

/***/ "./src/block-editor/filters/add-entity.filters.js":
/*!********************************************************!*\
  !*** ./src/block-editor/filters/add-entity.filters.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _add_entity_notice_container__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./add-entity-notice-container */ "./src/block-editor/filters/add-entity-notice-container.js");
/* harmony import */ var _common_containers_create_entity_form__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../common/containers/create-entity-form */ "./src/common/containers/create-entity-form/index.js");
/**
 * This file contains filters' hooks for the {@link AddEntity} component.
 *
 * The first is `wordlift.AddEntity.preWrapperContainer` to display notices.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



/* harmony default export */ __webpack_exports__["default"] = (store => {
  // Hook to `wordlift.AddEntity.preWrapperContainer` in order to display notices.
  Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__["addFilter"])("wordlift.AddEntity.beforeWrapperContainer", "wordlift", values => values.concat( /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_add_entity_notice_container__WEBPACK_IMPORTED_MODULE_3__["default"], null))); // Hook to `wordlift.AddEntity.preWrapperContainer` in order to display notices.

  Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__["addFilter"])("wordlift.AddEntity.afterWrapperContainer", "wordlift", values => values.concat( /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(react_redux__WEBPACK_IMPORTED_MODULE_1__["Provider"], {
    store: store
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_common_containers_create_entity_form__WEBPACK_IMPORTED_MODULE_4__["default"], null))));
});

/***/ }),

/***/ "./src/block-editor/formats/edit-component.js":
/*!****************************************************!*\
  !*** ./src/block-editor/formats/edit-component.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var backbone__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! backbone */ "backbone");
/* harmony import */ var backbone__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(backbone__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _common_constants__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../common/constants */ "./src/common/constants.js");
/**
 * This file registers the "wordlift/annotation" format type.
 *
 * The format type uses the {@link EditComponent} for the `edit` property. The
 * EditComponent is hooked to the {@link dispatch} of `wordlift/editor` store
 * to set the value.
 *
 * The format type also broadcasts the selection using WordPress hooks in order
 * to have the AddEntity component receive the selection. This is needed because
 * the AddEntity component has still its own store.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

 // Keeps the window timeout reference to delay sending events while the user
// is performing the selection.

let delay;
const ignoredBlockNames = ["core/gallery"];
let lastSelection = "";
let lastPayload = undefined;
/**
 * The EditComponent.
 *
 * @param {{start, end, text}} value The selection start/end.
 * @param {boolean} isActive Whether the format is active.
 * @param {{id}} activeAttributes The active attributes, i.e. the annotation id.
 * @param {Function} onSelectionChange The function to call when the selection changes.
 * @returns {Function} A stateless component.
 * @constructor
 */

const EditComponent = ({
  onChange,
  value,
  isActive,
  activeAttributes,
  onSelectionChange,
  setFormat
}) => {
  // Process only if it is not an ignored block
  const selectedBlock = wp.data.select("core/editor").getSelectedBlock();

  if (selectedBlock && !ignoredBlockNames.includes(selectedBlock.name)) {
    // Send the selection change event.
    if (delay) clearTimeout(delay);
    delay = setTimeout(() => {
      const selection = value.text.substring(value.start, value.end); // Check to break recursion

      if (lastSelection === selection) return;
      lastSelection = selection;
      onSelectionChange(selection);
      setFormat({
        onChange,
        value
      });
      Object(backbone__WEBPACK_IMPORTED_MODULE_1__["trigger"])(_common_constants__WEBPACK_IMPORTED_MODULE_4__["SELECTION_CHANGED"], {
        selection,
        value,
        onChange
      });
    }, 200);
    setTimeout(() => {
      // Send the annotation change event.
      const payload = "undefined" !== typeof isActive && "undefined" !== typeof activeAttributes && "undefined" !== typeof activeAttributes.id ? activeAttributes.id : undefined; // Check to break recursion

      if (lastPayload === payload) return;
      lastPayload = payload;
      Object(backbone__WEBPACK_IMPORTED_MODULE_1__["trigger"])(_common_constants__WEBPACK_IMPORTED_MODULE_4__["ANNOTATION_CHANGED"], payload);
    }, 10);
  } else {
    selectedBlock && console.log(`EditComponent ignored ${selectedBlock.name} block`);
  }

  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["Fragment"], null);
};
/**
 * Connect the `onSelectionChange` function to the `setValue` dispatch.
 */


/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["withDispatch"])((dispatch, ownProps) => {
  const {
    setValue,
    setFormat
  } = dispatch(_common_constants__WEBPACK_IMPORTED_MODULE_4__["WORDLIFT_STORE"]);
  return {
    onSelectionChange: setValue,
    setFormat
  };
})(EditComponent));

/***/ }),

/***/ "./src/block-editor/formats/register-format-type-wordlift-annotation.js":
/*!******************************************************************************!*\
  !*** ./src/block-editor/formats/register-format-type-wordlift-annotation.js ***!
  \******************************************************************************/
/*! exports provided: annotationSettings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "annotationSettings", function() { return annotationSettings; });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/rich-text */ "@wordpress/rich-text");
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _edit_component__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./edit-component */ "./src/block-editor/formats/edit-component.js");
/**
 * This file register WordLift's `wordlift/annotation` format type.
 *
 * The `wordlift/annotation` format type is used to receive selection changes
 * event from `paragraph` blocks and broadcasts them as WordPress' action.
 *
 * Its use is similar to the `tiny-mce` adapter which listens to selection changes
 * in `classic` blocks.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


/**
 * @see https://developer.wordpress.org/block-editor/tutorials/format-api/1-register-format/
 */

const annotationSettings = {
  /*
   * The `attributes` property is undocumented as basically the `WPFormat` class.
   *
   * Run this in the Developer Tools > Console to see what other formats return
   * as WPFormat:
   *  wp.data.select( 'core/rich-text' ).getFormatTypes();
   */
  attributes: {
    id: "id",
    class: "class",
    itemid: "itemid"
  },
  tagName: "span",
  className: "textannotation",
  title: "Annotation",
  edit: _edit_component__WEBPACK_IMPORTED_MODULE_4__["default"]
};
Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_1__["registerFormatType"])("wordlift/annotation", annotationSettings);

/***/ }),

/***/ "./src/block-editor/index.js":
/*!***********************************!*\
  !*** ./src/block-editor/index.js ***!
  \***********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var backbone__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! backbone */ "backbone");
/* harmony import */ var backbone__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(backbone__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/edit-post */ "@wordpress/edit-post");
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _containers_sidebar__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./containers/sidebar */ "./src/block-editor/containers/sidebar.js");
/* harmony import */ var _common_components_with_did_mount_callback__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../common/components/with-did-mount-callback */ "./src/common/components/with-did-mount-callback.js");
/* harmony import */ var _stores_actions__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./stores/actions */ "./src/block-editor/stores/actions.js");
/* harmony import */ var _stores__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./stores */ "./src/block-editor/stores/index.js");
/* harmony import */ var _wl_logo_big_svg__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./wl-logo-big.svg */ "./src/block-editor/wl-logo-big.svg");
/* harmony import */ var _wl_logo_big_svg__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(_wl_logo_big_svg__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./index.scss */ "./src/block-editor/index.scss");
/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_index_scss__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var _formats_register_format_type_wordlift_annotation__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./formats/register-format-type-wordlift-annotation */ "./src/block-editor/formats/register-format-type-wordlift-annotation.js");
/* harmony import */ var _blocks_register_block_type_wordlift_classification__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./blocks/register-block-type-wordlift-classification */ "./src/block-editor/blocks/register-block-type-wordlift-classification.js");
/* harmony import */ var _common_constants__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ../common/constants */ "./src/common/constants.js");
/* harmony import */ var _Edit_actions__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ../Edit/actions */ "./src/Edit/actions/index.js");
/* harmony import */ var _stores_selectors__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./stores/selectors */ "./src/block-editor/stores/selectors.js");
/* harmony import */ var _filters_add_entity_filters__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./filters/add-entity.filters */ "./src/block-editor/filters/add-entity.filters.js");
/* harmony import */ var _common_components_article_metadata_panel__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ../common/components/article-metadata-panel */ "./src/common/components/article-metadata-panel.js");
/* harmony import */ var _common_components_suggested_images_panel__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ../common/components/suggested-images-panel */ "./src/common/components/suggested-images-panel.js");
/* harmony import */ var _common_components_faq_panel__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ../common/components/faq-panel */ "./src/common/components/faq-panel.js");
/* harmony import */ var _common_components_synonyms_panel__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ../common/components/synonyms-panel */ "./src/common/components/synonyms-panel.js");
/* harmony import */ var _common_containers_related_posts__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ../common/containers/related-posts */ "./src/common/containers/related-posts/index.js");
/* harmony import */ var _blocks__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./blocks */ "./src/block-editor/blocks/index.js");
/* harmony import */ var _common_components_videos_panel__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ../common/components/videos-panel */ "./src/common/components/videos-panel/index.js");
/**
 * This file is the entry point for G'berg.
 *
 * This file registers the {@link Sidebar} component along with {@link WordLiftIcon}.
 */

/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */





















const wlSettings = global["wlSettings"];
const canAddSynonyms = wlSettings && "can_add_synonyms" in wlSettings ? wlSettings["can_add_synonyms"] == 1 : true;
const showClassificationSidebar = wlSettings && "show_classification_sidebar" in wlSettings ? wlSettings["show_classification_sidebar"] == 1 : true;
const showVideoobject = wlSettings && "show_videoobject" in wlSettings ? wlSettings["show_videoobject"] == 1 : true; // Register our filters to display additional elements in the CreateEntityForm. Pass our store to connect them to
// our state.

Object(_filters_add_entity_filters__WEBPACK_IMPORTED_MODULE_19__["default"])(_stores__WEBPACK_IMPORTED_MODULE_11__["default"]);
/**
 * Hook WordPress' action `ANNOTATION_CHANGED` to dispatching the annotation
 * to the store.
 */

Object(backbone__WEBPACK_IMPORTED_MODULE_2__["on"])(_common_constants__WEBPACK_IMPORTED_MODULE_16__["ANNOTATION_CHANGED"], payload => _stores__WEBPACK_IMPORTED_MODULE_11__["default"].dispatch(Object(_Edit_actions__WEBPACK_IMPORTED_MODULE_17__["setCurrentAnnotation"])(payload)));
/**
 * Connect the Sidebar to the analysis to be run as soon as the component is
 * mounted.
 */

const SidebarWithDidMountCallback = Object(_common_components_with_did_mount_callback__WEBPACK_IMPORTED_MODULE_9__["default"])(_containers_sidebar__WEBPACK_IMPORTED_MODULE_8__["default"], () => {
  // Request the analysis.
  _stores__WEBPACK_IMPORTED_MODULE_11__["default"].dispatch(Object(_stores_actions__WEBPACK_IMPORTED_MODULE_10__["requestAnalysis"])()); // Add the WordLift Classification block is not yet available.

  if ("undefined" === typeof Object(_stores_selectors__WEBPACK_IMPORTED_MODULE_18__["getClassificationBlock"])()) Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["dispatch"])(_common_constants__WEBPACK_IMPORTED_MODULE_16__["EDITOR_STORE"]).insertBlock(Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__["createBlock"])("wordlift/classification", {}));
});
/**
 * Register the sidebar plugin.
 *
 * We assign the `wl-sidebar` class name to the {@link PluginSidebar} to allow
 * custom styling (specifically to increase the top padding, see index.scss).
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/plugin-sidebar-0/plugin-sidebar-1-up-and-running/
 */

showClassificationSidebar && Object(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_5__["registerPlugin"])(_common_constants__WEBPACK_IMPORTED_MODULE_16__["PLUGIN_NAMESPACE"], {
  render: () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["Fragment"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_3__["PluginSidebarMoreMenuItem"], {
    target: "wordlift-sidebar",
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wl_logo_big_svg__WEBPACK_IMPORTED_MODULE_12___default.a, null)
  }, "WordLift"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_3__["PluginSidebar"], {
    name: "wordlift-sidebar",
    title: "WordLift",
    className: "wl-sidebar"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(react_redux__WEBPACK_IMPORTED_MODULE_1__["Provider"], {
    store: _stores__WEBPACK_IMPORTED_MODULE_11__["default"]
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__["Fragment"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(SidebarWithDidMountCallback, null), canAddSynonyms && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_common_components_synonyms_panel__WEBPACK_IMPORTED_MODULE_23__["default"], null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_common_components_article_metadata_panel__WEBPACK_IMPORTED_MODULE_20__["default"], null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_common_components_suggested_images_panel__WEBPACK_IMPORTED_MODULE_21__["default"], null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_common_containers_related_posts__WEBPACK_IMPORTED_MODULE_24__["default"], null), showVideoobject && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_common_components_videos_panel__WEBPACK_IMPORTED_MODULE_26__["default"], null))))),
  icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wl_logo_big_svg__WEBPACK_IMPORTED_MODULE_12___default.a, null)
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./src/block-editor/index.scss":
/*!*************************************!*\
  !*** ./src/block-editor/index.scss ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "./src/block-editor/stores/actions.js":
/*!********************************************!*\
  !*** ./src/block-editor/stores/actions.js ***!
  \********************************************/
/*! exports provided: editorSelectionChanged, requestAnalysis, setFormat, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "editorSelectionChanged", function() { return editorSelectionChanged; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "requestAnalysis", function() { return requestAnalysis; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setFormat", function() { return setFormat; });
/* harmony import */ var redux_actions__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux-actions */ "./node_modules/redux-actions/es/index.js");
/* harmony import */ var _Edit_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../Edit/constants/ActionTypes */ "./src/Edit/constants/ActionTypes.js");
/**
 * Define the actions.
 *
 * @since 3.23.0
 */

/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


const {
  editorSelectionChanged,
  requestAnalysis,
  setFormat
} = Object(redux_actions__WEBPACK_IMPORTED_MODULE_0__["createActions"])(_Edit_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_1__["EDITOR_SELECTION_CHANGED"], "REQUEST_ANALYSIS", "SET_FORMAT");
/* harmony default export */ __webpack_exports__["default"] = (Object(redux_actions__WEBPACK_IMPORTED_MODULE_0__["handleActions"])({
  REQUEST_ANALYSIS: state => ({
    loading: true
  }),
  RECEIVE_ANALYSIS_RESULTS: state => ({
    loading: false
  }),
  SET_FORMAT: (state, action) => ({
    format: action.payload
  })
}, {
  format: {
    onChange: () => {},
    value: ""
  },
  loading: false,
  showCreate: true
}));

/***/ }),

/***/ "./src/block-editor/stores/classic-editor-block-validator.js":
/*!*******************************************************************!*\
  !*** ./src/block-editor/stores/classic-editor-block-validator.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ClassicEditorBlockValidator; });
/**
 * @since 3.26.1
 * This class provides compatibility to classic editor block present
 * present inside the block editor.
 */
class ClassicEditorBlockValidator {
  /**
   * Render the html on dom element, return the text.
   * @param html {string}
   * @return text {string}
   */
  static getTextValue(html) {
    const wrapper = document.createElement("div");
    wrapper.innerHTML = html;
    return wrapper.textContent;
  }

  static getValue(entityLabel) {
    let selectedBlock = wp.data.select("core/editor").getSelectedBlock(); // Return early if there is no selected block.

    if (!selectedBlock) {
      return false;
    } // Return early if there is no attributes


    if (!selectedBlock.attributes) {
      return false;
    } // If there isn o content on the block we cant create the value dependency.


    if (!selectedBlock.attributes.content) {
      return false;
    }

    const blockHtml = selectedBlock.attributes.content;
    const startIndex = ClassicEditorBlockValidator.getTextValue(blockHtml).indexOf(entityLabel);

    if (startIndex === -1) {
      // we cant find the string, return early.
      return false;
    } // we have constructed the value dependency.


    return {
      start: startIndex,
      end: startIndex + entityLabel.length
    };
  }

}

/***/ }),

/***/ "./src/block-editor/stores/compat.js":
/*!*******************************************!*\
  !*** ./src/block-editor/stores/compat.js ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = (function (data) {
  var annotation, ea, entity, i, id, index, len, ref, ref1, ref2;
  ref = data.entities;

  for (id in ref) {
    entity = ref[id];
    entity.id = id;

    if (entity.occurrences == null) {
      entity.occurrences = [];
    }

    if (entity.annotations == null) {
      entity.annotations = {};
    }
  }

  ref1 = data.annotations;

  for (id in ref1) {
    annotation = ref1[id];
    annotation.id = id;
    annotation.entities = {}; // Filter out annotations that don't have a corresponding entity. The entities list might be filtered, in order
    // to remove the local entity.

    data.annotations[id].entityMatches = function () {
      var i, len, ref2, results;
      ref2 = annotation.entityMatches;
      results = [];

      for (i = 0, len = ref2.length; i < len; i++) {
        ea = ref2[i];

        if (ea.entityId in data.entities) {
          results.push(ea);
        }
      }

      return results;
    }(); // Remove the annotation if there's no entity matches left.
    // See https://github.com/insideout10/wordlift-plugin/issues/437
    // See https://github.com/insideout10/wordlift-plugin/issues/345


    if (0 === data.annotations[id].entityMatches.length) {
      delete data.annotations[id];
      continue;
    }

    ref2 = data.annotations[id].entityMatches;

    for (index = i = 0, len = ref2.length; i < len; index = ++i) {
      ea = ref2[index];

      if (!data.entities[ea.entityId].label) {
        data.entities[ea.entityId].label = annotation.text;
        $log.debug(`Missing label retrieved from related annotation for entity ${ea.entityId}`);
      }

      if (data.entities[ea.entityId].annotations == null) {
        data.entities[ea.entityId].annotations = {};
      }

      data.entities[ea.entityId].annotations[id] = annotation;
      data.annotations[id].entities[ea.entityId] = data.entities[ea.entityId];
    }
  }

  return data;
});

/***/ }),

/***/ "./src/block-editor/stores/index.js":
/*!******************************************!*\
  !*** ./src/block-editor/stores/index.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux */ "./node_modules/redux/es/redux.js");
/* harmony import */ var redux_saga__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! redux-saga */ "./node_modules/redux-saga/dist/redux-saga-core-npm-proxy.esm.js");
/* harmony import */ var redux_logger__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! redux-logger */ "./node_modules/redux-logger/dist/redux-logger.js");
/* harmony import */ var redux_logger__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(redux_logger__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var immutable__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! immutable */ "./node_modules/immutable/dist/immutable.js");
/* harmony import */ var immutable__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(immutable__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _Edit_reducers_entities__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../Edit/reducers/entities */ "./src/Edit/reducers/entities.js");
/* harmony import */ var _Edit_reducers_annotationFilter__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../Edit/reducers/annotationFilter */ "./src/Edit/reducers/annotationFilter.js");
/* harmony import */ var _Edit_reducers_visibilityFilter__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../Edit/reducers/visibilityFilter */ "./src/Edit/reducers/visibilityFilter.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./actions */ "./src/block-editor/stores/actions.js");
/* harmony import */ var _common_containers_related_posts_actions__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../../common/containers/related-posts/actions */ "./src/common/containers/related-posts/actions.js");
/* harmony import */ var _common_containers_create_entity_form_actions__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../common/containers/create-entity-form/actions */ "./src/common/containers/create-entity-form/actions.js");
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./selectors */ "./src/block-editor/stores/selectors.js");
/* harmony import */ var _sagas__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./sagas */ "./src/block-editor/stores/sagas.js");
/* harmony import */ var _Edit_actions__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../../Edit/actions */ "./src/Edit/actions/index.js");
/* harmony import */ var _Edit_components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../../Edit/components/AddEntity/actions */ "./src/Edit/components/AddEntity/actions.js");
/* harmony import */ var _common_constants__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ../../common/constants */ "./src/common/constants.js");
/**
 * This file defines the store.
 *
 * @since 3.23.0
 */

/**
 * External dependencies
 */




/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */












const initialState = {
  entities: Object(immutable__WEBPACK_IMPORTED_MODULE_3__["Map"])()
};
const sagaMiddleware = Object(redux_saga__WEBPACK_IMPORTED_MODULE_1__["default"])();
const store = Object(redux__WEBPACK_IMPORTED_MODULE_0__["createStore"])(Object(redux__WEBPACK_IMPORTED_MODULE_0__["combineReducers"])({
  entities: _Edit_reducers_entities__WEBPACK_IMPORTED_MODULE_5__["default"],
  annotationFilter: _Edit_reducers_annotationFilter__WEBPACK_IMPORTED_MODULE_6__["default"],
  visibilityFilter: _Edit_reducers_visibilityFilter__WEBPACK_IMPORTED_MODULE_7__["default"],
  blockEditor: _actions__WEBPACK_IMPORTED_MODULE_8__["default"],
  createEntityForm: _common_containers_create_entity_form_actions__WEBPACK_IMPORTED_MODULE_10__["default"],
  relatedPosts: _common_containers_related_posts_actions__WEBPACK_IMPORTED_MODULE_9__["default"]
}), initialState, Object(redux__WEBPACK_IMPORTED_MODULE_0__["applyMiddleware"])(sagaMiddleware, redux_logger__WEBPACK_IMPORTED_MODULE_2__["logger"]));
sagaMiddleware.run(_sagas__WEBPACK_IMPORTED_MODULE_12__["default"]); // Register the store with WordPress.

Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["registerGenericStore"])(_common_constants__WEBPACK_IMPORTED_MODULE_15__["WORDLIFT_STORE"], {
  getSelectors() {
    return {
      getAnnotationFilter: (...args) => Object(_selectors__WEBPACK_IMPORTED_MODULE_11__["getAnnotationFilter"])(store.getState(), ...args),
      getEditor: (...args) => Object(_selectors__WEBPACK_IMPORTED_MODULE_11__["getEditor"])(store.getState(), ...args),
      getEntities: (...args) => Object(_selectors__WEBPACK_IMPORTED_MODULE_11__["getEntities"])(store.getState(), ...args),
      getSelectedEntities: (...args) => Object(_selectors__WEBPACK_IMPORTED_MODULE_11__["getSelectedEntities"])(store.getState(), ...args),
      getBlockEditor: (...args) => Object(_selectors__WEBPACK_IMPORTED_MODULE_11__["getBlockEditor"])(store.getState(), ...args),
      getBlockEditorFormat: (...args) => Object(_selectors__WEBPACK_IMPORTED_MODULE_11__["getBlockEditorFormat"])(store.getState(), ...args)
    };
  },

  getActions() {
    return {
      editorSelectionChanged: args => store.dispatch(Object(_Edit_actions__WEBPACK_IMPORTED_MODULE_13__["editorSelectionChanged"])(args)),
      requestAnalysis: (...args) => store.dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_8__["requestAnalysis"])(...args)),
      // Called when the selection changes in editor.
      setValue: args => store.dispatch(Object(_Edit_components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_14__["setValue"])(args)),
      // Called to set the block editor current selection as Block Editor format value.
      setFormat: args => store.dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_8__["setFormat"])(args))
    };
  },

  subscribe: store.subscribe
});
/* harmony default export */ __webpack_exports__["default"] = (store);

/***/ }),

/***/ "./src/block-editor/stores/sagas.js":
/*!******************************************!*\
  !*** ./src/block-editor/stores/sagas.js ***!
  \******************************************/
/*! exports provided: getEntityType, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getEntityType", function() { return getEntityType; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return saga; });
/* harmony import */ var redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux-saga/effects */ "./node_modules/redux-saga/dist/redux-saga-effects-npm-proxy.esm.js");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _Edit_actions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../Edit/actions */ "./src/Edit/actions/index.js");
/* harmony import */ var _Edit_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../Edit/constants/ActionTypes */ "./src/Edit/constants/ActionTypes.js");
/* harmony import */ var _Edit_components_AddEntity__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../Edit/components/AddEntity */ "./src/Edit/components/AddEntity/index.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./actions */ "./src/block-editor/stores/actions.js");
/* harmony import */ var _compat__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./compat */ "./src/block-editor/stores/compat.js");
/* harmony import */ var _common_constants__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../common/constants */ "./src/common/constants.js");
/* harmony import */ var _api_editor_ops__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../api/editor-ops */ "./src/block-editor/api/editor-ops.js");
/* harmony import */ var _api_utils__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../api/utils */ "./src/block-editor/api/utils.js");
/* harmony import */ var _api_blocks__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../api/blocks */ "./src/block-editor/api/blocks.js");
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./selectors */ "./src/block-editor/stores/selectors.js");
/* harmony import */ var _Edit_components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../../Edit/components/AddEntity/actions */ "./src/Edit/components/AddEntity/actions.js");
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! @wordpress/rich-text */ "@wordpress/rich-text");
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var _common_containers_create_entity_form_actions__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ../../common/containers/create-entity-form/actions */ "./src/common/containers/create-entity-form/actions.js");
/* harmony import */ var _api_create_entity__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ../api/create-entity */ "./src/block-editor/api/create-entity.js");
/* harmony import */ var _common_containers_related_posts_actions__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ../../common/containers/related-posts/actions */ "./src/common/containers/related-posts/actions.js");
/* harmony import */ var _common_api_get_related_posts__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ../../common/api/get-related-posts */ "./src/common/api/get-related-posts.js");
/* harmony import */ var _api_classic_editor_block__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ../api/classic-editor-block */ "./src/block-editor/api/classic-editor-block.js");
/* harmony import */ var _classic_editor_block_validator__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./classic-editor-block-validator */ "./src/block-editor/stores/classic-editor-block-validator.js");
/* harmony import */ var _Edit_stores_sagas__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ../../Edit/stores/sagas */ "./src/Edit/stores/sagas.js");
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */






















function* handleRequestAnalysis() {
  const editorOps = new _api_editor_ops__WEBPACK_IMPORTED_MODULE_8__["default"](_common_constants__WEBPACK_IMPORTED_MODULE_7__["EDITOR_STORE"]);
  const settings = global["wlSettings"];
  const canCreateEntities = "undefined" !== typeof settings["can_create_entities"] && "yes" === settings["can_create_entities"];
  const _wlMetaBoxSettings = global["_wlMetaBoxSettings"].settings;
  const request = editorOps.buildAnalysisRequest(settings["can_create_entities"]["language"], [_wlMetaBoxSettings["currentPostUri"]], canCreateEntities);
  const response = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["call"])(global["wp"].ajax.post, "wl_analyze", {
    _wpnonce: settings["analysis"]["_wpnonce"],
    data: JSON.stringify(request),
    postId: wp.data.select("core/editor").getCurrentPostId()
  });
  embedAnalysis(editorOps, response);
  const parsed = Object(_compat__WEBPACK_IMPORTED_MODULE_6__["default"])(response);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["put"])(Object(_Edit_actions__WEBPACK_IMPORTED_MODULE_2__["receiveAnalysisResults"])(parsed));
}

function embedAnalysis(editorOps, response) {
  // Bail out if the response doesn't contain results.
  if ("undefined" === typeof response || "undefined" === typeof response.annotations) return;
  const annotations = Object.values(response.annotations).sort(function (a1, a2) {
    if (a1.end > a2.end) return -1;
    if (a1.end < a2.end) return 1;
    return 0;
  });
  annotations.forEach(annotation => editorOps.insertAnnotation(annotation.annotationId, annotation.start, annotation.end));
  editorOps.applyChanges();
}

function* toggleEntity({
  entity
}) {
  // Get the supported blocks.
  const blocks = _api_blocks__WEBPACK_IMPORTED_MODULE_10__["Blocks"].create(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["select"](_common_constants__WEBPACK_IMPORTED_MODULE_7__["EDITOR_STORE"]).getBlocks(), _wordpress_data__WEBPACK_IMPORTED_MODULE_1__["dispatch"](_common_constants__WEBPACK_IMPORTED_MODULE_7__["EDITOR_STORE"]));
  const mainType = entity.mainType || "thing";
  const onClassNames = ["disambiguated", `wl-${mainType.replace(/\s/, "-")}`]; // Build a css selector to select all the annotations for the provided entity.

  const annotationSelector = Object(_api_utils__WEBPACK_IMPORTED_MODULE_9__["makeEntityAnnotationsSelector"])(entity); // Collect the annotations that have been switch on/off.

  const occurrences = [];

  if (0 === entity.occurrences.length) {
    // Switch on.
    blocks.replace(new RegExp(`<span\\s+id="(${annotationSelector})"\\sclass="([^"]*)">`, "gi"), (match, annotationId, classNames) => {
      const newClassNames = Object(_api_utils__WEBPACK_IMPORTED_MODULE_9__["mergeArray"])(classNames.split(/\s+/), onClassNames);
      occurrences.push(annotationId);
      return `<span id="${annotationId}" class="${newClassNames.join(" ")}" itemid="${entity.id}">`;
    });
  } else {
    console.debug(`Looking for "<span\\s+id="(${annotationSelector})"\\sclass="([^"]*)"\\sitemid="[^"]*">"...`); // Switch off.

    blocks.replace(new RegExp(`<span\\s+id="(${annotationSelector})"\\sclass="([^"]*)"\\sitemid="[^"]*">`, "gi"), (match, annotationId, classNames) => {
      const newClassNames = classNames.split(/\s+/).filter(x => -1 === onClassNames.indexOf(x));
      return `<span id="${annotationId}" class="${newClassNames.join(" ")}">`;
    });
  }

  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["put"])(Object(_Edit_actions__WEBPACK_IMPORTED_MODULE_2__["updateOccurrencesForEntity"])(entity.id, occurrences)); // Send the selected entities to the WordLift Classification box.

  _wordpress_data__WEBPACK_IMPORTED_MODULE_1__["dispatch"](_common_constants__WEBPACK_IMPORTED_MODULE_7__["EDITOR_STORE"]).updateBlockAttributes(Object(_selectors__WEBPACK_IMPORTED_MODULE_11__["getClassificationBlock"])().clientId, {
    entities: yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["select"])(_selectors__WEBPACK_IMPORTED_MODULE_11__["getSelectedEntities"])
  }); // Apply the changes.

  blocks.apply();
}

function* toggleLink({
  entity
}) {
  // Get the supported blocks.
  const blocks = _api_blocks__WEBPACK_IMPORTED_MODULE_10__["Blocks"].create(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["select"](_common_constants__WEBPACK_IMPORTED_MODULE_7__["EDITOR_STORE"]).getBlocks(), _wordpress_data__WEBPACK_IMPORTED_MODULE_1__["dispatch"](_common_constants__WEBPACK_IMPORTED_MODULE_7__["EDITOR_STORE"])); // Build a css selector to select all the annotations for the provided entity.

  const annotationSelector = Object(_api_utils__WEBPACK_IMPORTED_MODULE_9__["makeEntityAnnotationsSelector"])(entity);
  const cssClasses = ["wl-link", "wl-no-link"];
  const link = !entity.link;
  blocks.replace(new RegExp(`<span\\s+id="(${annotationSelector})"\\sclass="([^"]*)"\\sitemid="([^"]*)">`, "gi"), (match, annotationId, classNames) => {
    // Remove existing `wl-link` / `wl-no-link` classes.
    const newClassNames = classNames.split(/\s+/).filter(x => -1 === cssClasses.indexOf(x)); // Add the `wl-link` / `wl-no-link` class according to the desired outcome.

    newClassNames.push(link ? "wl-link" : "wl-no-link");
    return `<span id="${annotationId}" class="${newClassNames.join(" ")}" itemid="${entity.id}">`;
  }); // Apply the changes.

  blocks.apply();
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["put"])(Object(_Edit_actions__WEBPACK_IMPORTED_MODULE_2__["toggleLinkSuccess"])({
    id: entity.id,
    link
  }));
}
/**
 * Handle `ANNOTATION` actions.
 *
 * When the `ANNOTATION` action is fired, the `selected` css class will be added
 * to the selected annotation and removed from the others.
 *
 * The annotation id should match the element id.
 *
 * @since 3.23.0
 * @param {string|undefined} annotationId The annotation id.
 */


function* toggleAnnotation({
  annotation
}) {
  // Bail out if the annotation didn't change.
  const selectedAnnotation = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["select"])(_selectors__WEBPACK_IMPORTED_MODULE_11__["getAnnotationFilter"]);
  if (annotation === selectedAnnotation) return null; // Get the supported blocks.

  const blocks = _api_blocks__WEBPACK_IMPORTED_MODULE_10__["Blocks"].create(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["select"](_common_constants__WEBPACK_IMPORTED_MODULE_7__["EDITOR_STORE"]).getBlocks(), _wordpress_data__WEBPACK_IMPORTED_MODULE_1__["dispatch"](_common_constants__WEBPACK_IMPORTED_MODULE_7__["EDITOR_STORE"]));
  blocks.replace(new RegExp(`<span\\s+id="([^"]+)"\\sclass="(textannotation(?:\\s[^"]*)?)"`, "gi"), (match, annotationId, classNames) => {
    // Get the class names removing any potential `selected` class.
    const newClassNames = classNames.split(/\s+/).filter(x => "selected" !== x); // Add the `selected` class if the annotation match.

    if (annotation === annotationId) newClassNames.push("selected"); // Return the new span.

    return `<span id="${annotationId}" class="${newClassNames.join(" ")}"`;
  }); // Apply the changes.

  blocks.apply();
}

function getEntityType(entityToAdd) {
  if (entityToAdd.types) {
    // Set the main type using the same function used by classic editor.
    return Object(_Edit_stores_sagas__WEBPACK_IMPORTED_MODULE_21__["getMainType"])(entityToAdd.types);
  } else if (entityToAdd.category) {
    // When the user manually adds entity in the editor
    // set the mainType from category
    return entityToAdd.category.replace("http://schema.org/", "").replace("https://schema.org/", "");
  }
}
/**
 * Handles the request to add an entity.
 *
 * First we toggle the wordlift/annotation in Block Editor to create the annotation.
 */

function* handleAddEntityRequest({
  payload
}) {
  // See https://developer.wordpress.org/block-editor/packages/packages-rich-text/#applyFormat
  const blockEditorFormat = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["select"])(_selectors__WEBPACK_IMPORTED_MODULE_11__["getBlockEditorFormat"]);
  let value, onChange;
  let selectedBlock = wp.data.select("core/editor").getSelectedBlock();
  let isClassicEditorBlock = false;

  if (blockEditorFormat !== undefined) {
    onChange = blockEditorFormat.onChange;
    value = blockEditorFormat.value;
  }

  if (blockEditorFormat === undefined) {
    value = _classic_editor_block_validator__WEBPACK_IMPORTED_MODULE_20__["default"].getValue(Object(_Edit_components_AddEntity__WEBPACK_IMPORTED_MODULE_4__["getSelection"])());

    if (value === false) {
      // This is not a valid classic block,return early.
      return false;
    } // mark it as classic editor block.


    isClassicEditorBlock = true;
  }

  const annotationId = "urn:local-annotation-" + Math.floor(Math.random() * 999999); // Create the entity if the `id` isn't defined.

  const id = payload.id || (yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["call"])(_api_create_entity__WEBPACK_IMPORTED_MODULE_16__["default"], {
    title: payload.label,
    // Set entity type to the created entity.
    wlEntityMainType: [payload.category],
    // Inherit the status from the edited post.
    status: wp.data.select('core/editor').getEditedPostAttribute('status'),
    // wp rest api uses content as the alias for description
    content: payload.description,
    excerpt: ""
  }))["wl:entity_url"];
  let entityToAdd = {
    id,
    ...payload,
    annotations: {
      [annotationId]: {
        annotationId,
        start: value.start,
        end: value.end
      }
    },
    occurrences: [annotationId]
  };
  entityToAdd.mainType = getEntityType(entityToAdd);
  console.debug("Adding Entity", entityToAdd);
  const annotationAttributes = {
    id: annotationId,
    class: "disambiguated",
    itemid: entityToAdd.id
  };
  const format = {
    type: "wordlift/annotation",
    attributes: annotationAttributes
  };

  if (isClassicEditorBlock) {
    // classic editor block should be updated differently.
    const instance = new _api_classic_editor_block__WEBPACK_IMPORTED_MODULE_19__["default"](selectedBlock.clientId, selectedBlock.attributes.content);
    instance.replaceWithAnnotation(Object(_Edit_components_AddEntity__WEBPACK_IMPORTED_MODULE_4__["getSelection"])(), annotationAttributes);
    instance.update();
  } else {
    // update the block
    yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["call"])(onChange, Object(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_13__["applyFormat"])(value, format));
  } // update the state.


  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["put"])({
    type: _Edit_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_3__["ADD_ENTITY"],
    payload: entityToAdd
  }); // Send the selected entities to the WordLift Classification box.

  _wordpress_data__WEBPACK_IMPORTED_MODULE_1__["dispatch"](_common_constants__WEBPACK_IMPORTED_MODULE_7__["EDITOR_STORE"]).updateBlockAttributes(Object(_selectors__WEBPACK_IMPORTED_MODULE_11__["getClassificationBlock"])().clientId, {
    entities: yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["select"])(_selectors__WEBPACK_IMPORTED_MODULE_11__["getSelectedEntities"])
  });
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["put"])(Object(_Edit_components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_12__["addEntitySuccess"])());
}
/**
 * Broadcast the `wordlift.addEntitySuccess` action in order to have the AddEntity local store capture it.
 */


function* handleAddEntitySuccess() {
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["call"])(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_14__["doAction"], "wordlift.addEntitySuccess");
}
/**
 * Handles the action when the entity edit link is clicked in the Classification Box.
 *
 * Within the Block Editor we open a new window to the WordPress edit post screen.
 *
 * @since 3.23.0
 * @param Object entity The entity object.
 */


function* handleSetCurrentEntity({
  entity
}) {
  const url = `${window["wp"].ajax.settings.url}?action=wordlift_redirect&uri=${encodeURIComponent(entity.id)}&to=edit`;
  window.open(url, "_blank");
}
/**
 * Handle the Create Entity Request, which is supposed to open a form in the sidebar.
 */


function* handleCreateEntityRequest() {
  // Call the WP hook to close the entity select (see ../../Edit/components/AddEntity/index.js).
  Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_14__["doAction"])("unstable_wordlift.closeEntitySelect");
}
/**
 * Handles the request to load the related posts.
 */


function* handleRelatedPostsRequest() {
  const posts = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["call"])(_common_api_get_related_posts__WEBPACK_IMPORTED_MODULE_18__["default"]);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["put"])(Object(_common_containers_related_posts_actions__WEBPACK_IMPORTED_MODULE_17__["relatedPostsSuccess"])(posts));
}

function* saga() {
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeLatest"])(_actions__WEBPACK_IMPORTED_MODULE_5__["requestAnalysis"], handleRequestAnalysis);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"])(_Edit_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_3__["TOGGLE_ENTITY"], toggleEntity);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"])(_Edit_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_3__["TOGGLE_LINK"], toggleLink);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeLatest"])(_Edit_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_3__["ANNOTATION"], toggleAnnotation);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"])(_Edit_components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_12__["addEntityRequest"], handleAddEntityRequest);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"])(_Edit_components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_12__["addEntitySuccess"], handleAddEntitySuccess);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"])(_Edit_constants_ActionTypes__WEBPACK_IMPORTED_MODULE_3__["SET_CURRENT_ENTITY"], handleSetCurrentEntity);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"])(_common_containers_create_entity_form_actions__WEBPACK_IMPORTED_MODULE_15__["createEntityRequest"], handleCreateEntityRequest);
  yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_0__["takeEvery"])(_common_containers_related_posts_actions__WEBPACK_IMPORTED_MODULE_17__["relatedPostsRequest"], handleRelatedPostsRequest);
}
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./src/block-editor/stores/selectors.js":
/*!**********************************************!*\
  !*** ./src/block-editor/stores/selectors.js ***!
  \**********************************************/
/*! exports provided: getAnnotationFilter, getEditor, getEntities, getSelectedEntities, getClassificationBlock, getBlockEditor, getBlockEditorFormat */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getAnnotationFilter", function() { return getAnnotationFilter; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getEditor", function() { return getEditor; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getEntities", function() { return getEntities; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getSelectedEntities", function() { return getSelectedEntities; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getClassificationBlock", function() { return getClassificationBlock; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getBlockEditor", function() { return getBlockEditor; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getBlockEditorFormat", function() { return getBlockEditorFormat; });
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/**
 * Define the selectors.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * WordPress dependencies
 */


const getAnnotationFilter = state => state.annotationFilter;
const getEditor = state => state.editor;
const getEntities = state => state.entities;
const getSelectedEntities = state => getEntities(state).filter(entity => "undefined" !== typeof entity.occurrences && 0 < entity.occurrences.length).map(({
  annotations,
  description,
  id,
  label,
  mainType,
  occurrences,
  properties,
  sameAs,
  synonyms,
  types
}) => {
  const annotationsWithoutCyclicReferences = {};
  Object.getOwnPropertyNames(annotations).forEach(propName => {
    annotationsWithoutCyclicReferences[propName] = {
      start: annotations[propName].start,
      end: annotations[propName].end,
      text: annotations[propName].text
    };
  });
  return {
    annotations: annotationsWithoutCyclicReferences,
    description,
    id,
    label,
    mainType,
    occurrences,
    properties,
    sameAs,
    synonyms,
    types
  };
}).toArray();
const getClassificationBlock = () => Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__["select"])("core/editor").getBlocks().find(block => "wordlift/classification" === block.name);
const getBlockEditor = state => state.blockEditor;
/**
 * Get the Block Editor current format.
 */

const getBlockEditorFormat = state => getBlockEditor(state).format;

/***/ }),

/***/ "./src/block-editor/wl-logo-big.svg":
/*!******************************************!*\
  !*** ./src/block-editor/wl-logo-big.svg ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var React = __webpack_require__(/*! react */ "react");

function WlLogoBig (props) {
    return React.createElement("svg",props,React.createElement("path",{"d":"M100.3,3.1C47,3.1,3.8,46.3,3.8,99.6s43.2,96.5,96.5,96.5c53.3,0,96.5-43.2,96.5-96.5S153.6,3.1,100.3,3.1z\n\t M129.8,156.7h-1.1l-27.4-58.1l-27.4,58.1h-2.2L32.3,64.5h23l19.8,53.8l26.3-58.2h1.1l25.2,58.2l20.8-52.6h21.9L129.8,156.7z","style":{"fill":"#007AFF"}}));
}

WlLogoBig.defaultProps = {"version":"1.1","id":"Layer_1","x":"0px","y":"0px","viewBox":"0 0 200 200","style":{"enableBackground":"new 0 0 200 200"},"xmlSpace":"preserve","width":"20px","height":"20px"};

module.exports = WlLogoBig;

WlLogoBig.default = WlLogoBig;


/***/ }),

/***/ "./src/common/api/get-related-posts.js":
/*!*********************************************!*\
  !*** ./src/common/api/get-related-posts.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {const {
  ajax
} = global["wp"];
const settings = global["wlSettings"];
const _wlMetaBoxSettings = global["_wlMetaBoxSettings"].settings;
/* harmony default export */ __webpack_exports__["default"] = (() => {
  const entities = _wlMetaBoxSettings.entities;
  return fetch(`${ajax.settings.url}?action=wordlift_related_posts&post_id=${settings["post_id"]}`, {
    method: "POST",
    headers: {
      "content-type": "application/json"
    },
    body: JSON.stringify(Object.keys(entities))
  }).then(response => response.json());
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./src/common/components/article-metadata-panel.js":
/*!*********************************************************!*\
  !*** ./src/common/components/article-metadata-panel.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _src_images_svg_wl_author_icon_svg__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../src/images/svg/wl-author-icon.svg */ "../../src/images/svg/wl-author-icon.svg");
/* harmony import */ var _src_images_svg_wl_author_icon_svg__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_src_images_svg_wl_author_icon_svg__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _src_images_svg_wl_calendar_icon_svg__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../../src/images/svg/wl-calendar-icon.svg */ "../../src/images/svg/wl-calendar-icon.svg");
/* harmony import */ var _src_images_svg_wl_calendar_icon_svg__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_src_images_svg_wl_calendar_icon_svg__WEBPACK_IMPORTED_MODULE_4__);
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



const StyledWlAuthorIcon = Object(styled_components__WEBPACK_IMPORTED_MODULE_1__["default"])(_src_images_svg_wl_author_icon_svg__WEBPACK_IMPORTED_MODULE_3___default.a)`
  width: 18px;
  margin-right: 5px;
`;
const StyledWlCalenderIcon = Object(styled_components__WEBPACK_IMPORTED_MODULE_1__["default"])(_src_images_svg_wl_calendar_icon_svg__WEBPACK_IMPORTED_MODULE_4___default.a)`
  width: 18px;
  margin-right: 5px;
`;
const settings = global["_wlMetaBoxSettings"].settings;

const ArticleMetadataPanel = () => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Panel"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelBody"], {
  title: "Article metadata",
  initialOpen: false
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelRow"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(StyledWlAuthorIcon, null), settings["currentUser"]), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(StyledWlCalenderIcon, null), settings["publishedDate"]))));

/* harmony default export */ __webpack_exports__["default"] = (ArticleMetadataPanel);
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./src/common/components/create-entity-form.js":
/*!*****************************************************!*\
  !*** ./src/common/components/create-entity-form.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return CreateEntityForm; });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


class CreateEntityForm extends react__WEBPACK_IMPORTED_MODULE_0___default.a.Component {
  constructor(props) {
    super(props);
    this.state = {
      category: window["_wlEntityTypes"][0].slug,
      description: ""
    };
    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  handleChange(event) {
    const target = event.target;
    const value = target.type === "checkbox" ? target.checked : target.value;
    const name = target.name;
    this.setState({
      [name]: value
    });
  }

  handleSubmit(event) {
    this.props.onSubmit(this.state);
    event.preventDefault();
  }

  componentDidUpdate(prevProps) {
    if (this.props.value !== prevProps.value) {
      this.setState({
        label: this.props.value
      });
    }
  }

  render() {
    return this.props.showCreate ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("form", {
      onSubmit: this.handleSubmit
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, "Category", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("select", {
      name: "category",
      style: {
        width: "100%"
      },
      value: this.state.category,
      onChange: this.handleChange
    }, global["_wlEntityTypes"].map(item => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("option", {
      value: item.uri
    }, item.label)))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, "Description", /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("textarea", {
      name: "description",
      rows: "5",
      style: {
        width: "100%"
      },
      value: this.state.description,
      onChange: this.handleChange
    })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Button"], {
      isPrimary: true,
      type: "submit",
      style: {
        marginRight: "5px"
      }
    }, "Create Entity"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Button"], {
      isDefault: true,
      onClick: () => this.props.onCancel()
    }, "Cancel"))) : "";
  }

}
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./src/common/components/faq-panel.js":
/*!********************************************!*\
  !*** ./src/common/components/faq-panel.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/* harmony default export */ __webpack_exports__["default"] = (() => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Panel"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelBody"], {
  title: "FAQ",
  initialOpen: true
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
  id: "wl-faq-meta-list-box"
}))));

/***/ }),

/***/ "./src/common/components/related-posts-panel/index.js":
/*!************************************************************!*\
  !*** ./src/common/components/related-posts-panel/index.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return RelatedPostsPanel; });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _post__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./post */ "./src/common/components/related-posts-panel/post.js");
/* harmony import */ var _spinner__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../spinner */ "./src/common/components/spinner/index.js");
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



class RelatedPostsPanel extends react__WEBPACK_IMPORTED_MODULE_0___default.a.Component {
  componentDidMount() {
    this.props.requestRelatedPosts();
  }

  render() {
    const {
      posts
    } = this.props;
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Panel"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelBody"], {
      title: "Related posts",
      initialOpen: false
    }, !posts && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_spinner__WEBPACK_IMPORTED_MODULE_3__["default"], {
      running: true
    }) || posts.length > 0 && posts.map(item => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelRow"], {
      key: item.ID
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_post__WEBPACK_IMPORTED_MODULE_2__["default"], item))) || /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelRow"], null, "No results found")));
  }

}

/***/ }),

/***/ "./src/common/components/related-posts-panel/post.js":
/*!***********************************************************!*\
  !*** ./src/common/components/related-posts-panel/post.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__);
function _extends() { _extends = Object.assign ? Object.assign.bind() : function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



const Image = styled_components__WEBPACK_IMPORTED_MODULE_1__["default"].img`
  width: 80px;
  margin: 0 0.5rem 0 0;
`;
const Heading = styled_components__WEBPACK_IMPORTED_MODULE_1__["default"].h5`
  margin: 0 0 0.5rem 0;
`;
const LinkClipboardButton = Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__["withState"])({
  hasCopied: false
})(({
  hasCopied,
  setState,
  ...props
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["ClipboardButton"], _extends({}, props, {
  onCopy: () => setState({
    hasCopied: true
  }),
  onFinishCopy: () => setState({
    hasCopied: false
  })
}), hasCopied ? "Link Copied!" : "Copy Link"));
/* harmony default export */ __webpack_exports__["default"] = (props => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(react__WEBPACK_IMPORTED_MODULE_0___default.a.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(Image, {
  src: props.thumbnail
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(Heading, null, props.post_title), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(LinkClipboardButton, {
  text: props.permalink,
  isSmall: true,
  isDefault: true
}))));

/***/ }),

/***/ "./src/common/components/spinner/index.js":
/*!************************************************!*\
  !*** ./src/common/components/spinner/index.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./index.scss */ "./src/common/components/spinner/index.scss");
/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_index_scss__WEBPACK_IMPORTED_MODULE_1__);
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/* harmony default export */ __webpack_exports__["default"] = (({
  running
}) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
  className: `wl-spinner${running && " wl-spinner--running"}`
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("svg", {
  "transform-origin": "10 10",
  className: "wl-spinner__shape wl-spinner__shape--circle"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("circle", {
  cx: "10",
  cy: "10",
  r: "6",
  className: "wl-spinner__shape__path"
})), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("svg", {
  "transform-origin": "10 10",
  className: "wl-spinner__shape wl-spinner__shape--rect"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("rect", {
  x: "4",
  y: "4",
  width: "12",
  height: "12",
  className: "wl-spinner__shape__path"
})), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("svg", {
  "transform-origin": "10 10",
  className: "wl-spinner__shape wl-spinner__shape--hexagon"
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("polygon", {
  points: "3,10 6.5,4 13.4,4 16.9,10 13.4,16 6.5,16",
  className: "wl-spinner__shape__path"
}))));

/***/ }),

/***/ "./src/common/components/spinner/index.scss":
/*!**************************************************!*\
  !*** ./src/common/components/spinner/index.scss ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "./src/common/components/spinner/with-spinner.js":
/*!*******************************************************!*\
  !*** ./src/common/components/spinner/with-spinner.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _index__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./index */ "./src/common/components/spinner/index.js");
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/* harmony default export */ __webpack_exports__["default"] = (Component => props => {
  return props.loading && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_index__WEBPACK_IMPORTED_MODULE_1__["default"], {
    running: true
  }) || /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(Component, props);
});

/***/ }),

/***/ "./src/common/components/suggested-images-panel.js":
/*!*********************************************************!*\
  !*** ./src/common/components/suggested-images-panel.js ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var styled_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! styled-components */ "./node_modules/styled-components/dist/styled-components.browser.esm.js");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


const RelatedImage = styled_components__WEBPACK_IMPORTED_MODULE_1__["default"].img`
  max-width: 100%;
  cursor: move;
`;
const wordlift = global["wordlift"];

const Images = () => {
  let images = [];
  let entities = window["_wlMetaBoxSettings"].settings["entities"];

  for (const wlEntities in entities) {
    images = images.concat(entities[wlEntities].images);
  }

  return [...new Set(images)];
};

/* harmony default export */ __webpack_exports__["default"] = (() => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["Panel"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelBody"], {
  title: "Suggested images",
  initialOpen: false
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("h4", null, "Drag & Drop in editor"), Images().map(item => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelRow"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(RelatedImage, {
  src: item
}))))));
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./src/common/components/synonyms-panel.js":
/*!*************************************************!*\
  !*** ./src/common/components/synonyms-panel.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





const settings = global["_wlMetaBoxSettings"].settings;

let SynonymsPanel = props => settings.isEntity ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Panel"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])("Synonyms", "wordlift"),
  intialOpen: false
}, props.altLabels.map((altLabel, altLabelN, altLabels) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelRow"], {
  key: altLabelN
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
  value: altLabel,
  onChange: value => {
    let altLabelsModified = [...altLabels];
    altLabelsModified[altLabelN] = value;
    props.onMetaFieldChange(altLabelsModified);
  }
}), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["IconButton"], {
  icon: "trash",
  label: "Delete",
  onClick: () => {
    let altLabelsModified = altLabels.filter((item, index) => index !== altLabelN);
    props.onMetaFieldChange(altLabelsModified);
  }
}))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelRow"], {
  key: -1
}, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Button"], {
  isDefault: true,
  onClick: () => {
    let altLabelsModified = [...props.altLabels];
    altLabelsModified.push("");
    props.onMetaFieldChange(altLabelsModified);
  }
}, "Add")))) : null;

/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__["compose"])(Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["withSelect"])(select => {
  const meta = select("core/editor").getEditedPostAttribute("_wl_alt_label");

  if (undefined !== meta) {
    return {
      altLabels: meta
    };
  }

  return {
    altLabels: []
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["withDispatch"])(dispatch => {
  return {
    onMetaFieldChange: value => {
      dispatch("core/editor").editPost({
        _wl_alt_label: value
      });
    }
  };
}))(SynonymsPanel));
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./src/common/components/videos-panel/index.js":
/*!*****************************************************!*\
  !*** ./src/common/components/videos-panel/index.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return VideosPanel; });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__);
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



class VideosPanel extends react__WEBPACK_IMPORTED_MODULE_0___default.a.Component {
  render() {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Panel"], null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelBody"], {
      title: "Videos",
      initialOpen: false,
      onToggle: () => {
        setTimeout(() => {
          Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__["doAction"])("wordlift.renderVideoList");
        }, 500);
      }
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
      id: "wl-video-list"
    })));
  }

}

/***/ }),

/***/ "./src/common/components/with-did-mount-callback.js":
/*!**********************************************************!*\
  !*** ./src/common/components/with-did-mount-callback.js ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/**
 * This file defines the `withDidMountCallback` HOC function.
 *
 * The `withDidMountCallback` adds a callback to do something when a component
 * is first mounted.
 *
 * This is currently used in index.js in order to automatically start the analysis
 * when the Sidebar is first mounted.
 *
 * @see https://reactjs.org/docs/higher-order-components.html
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies.
 */

/**
 * The `withDidMountCallback` function.
 *
 * @param WrapperComponent The wrapper component.
 * @param {Function} callback The callback function to call when the component is mounted.
 * @returns {Object} The higher-order component.
 */

const withDidMountCallback = (WrapperComponent, callback) => {
  return class extends react__WEBPACK_IMPORTED_MODULE_0___default.a.Component {
    render() {
      return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(WrapperComponent, this.props);
    }

    componentDidMount() {
      callback();
    }

  };
};

/* harmony default export */ __webpack_exports__["default"] = (withDidMountCallback);

/***/ }),

/***/ "./src/common/constants.js":
/*!*********************************!*\
  !*** ./src/common/constants.js ***!
  \*********************************/
/*! exports provided: SELECTION_CHANGED, ANNOTATION_CHANGED, PLUGIN_NAMESPACE, EDITOR_STORE, EDITOR_ELEMENT_ID, WORDLIFT_STORE */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SELECTION_CHANGED", function() { return SELECTION_CHANGED; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ANNOTATION_CHANGED", function() { return ANNOTATION_CHANGED; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PLUGIN_NAMESPACE", function() { return PLUGIN_NAMESPACE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "EDITOR_STORE", function() { return EDITOR_STORE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "EDITOR_ELEMENT_ID", function() { return EDITOR_ELEMENT_ID; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "WORDLIFT_STORE", function() { return WORDLIFT_STORE; });
/**
 * This file defines constants used across different files and components.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * WordPress' action hook to signal that a selection has changed.
 *
 * @since 3.23.0
 * @type {string}
 */
const SELECTION_CHANGED = "wordlift.selectionChanged";
/**
 * WordPress' action hook to signal that an annotation has changed. The action
 * provides the annotation id as `{ annotationId }`. The annotation id usually
 * matches the element id that caused the action to be fired.
 *
 * @since 3.23.0
 * @type {string}
 */

const ANNOTATION_CHANGED = "wordlift.annotationChanged";
/**
 * The plugin namespace.
 *
 * @type {string}
 */

const PLUGIN_NAMESPACE = "wordlift";
/**
 * Define the G'berg editor store name.
 *
 * @since 3.23.0
 * @type {string}
 */

const EDITOR_STORE = "core/editor";
/**
 * Define the editor element id.
 *
 * @since 3.23.0
 * @type {string}
 */

const EDITOR_ELEMENT_ID = "editor";
/**
 * Define the WordLift Store name used for {@link select} and {@link dispatch}
 * functions.
 *
 * @type {string}
 */

const WORDLIFT_STORE = "wordlift/editor";

/***/ }),

/***/ "./src/common/containers/create-entity-form/actions.js":
/*!*************************************************************!*\
  !*** ./src/common/containers/create-entity-form/actions.js ***!
  \*************************************************************/
/*! exports provided: createEntityRequest, createEntityClose, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createEntityRequest", function() { return createEntityRequest; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createEntityClose", function() { return createEntityClose; });
/* harmony import */ var redux_actions__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux-actions */ "./node_modules/redux-actions/es/index.js");
/**
 * This file contains actions and reducer associated with the Create Entity Form container.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */

/**
 * Actions
 */

const {
  createEntityRequest
} = Object(redux_actions__WEBPACK_IMPORTED_MODULE_0__["createActions"])("CREATE_ENTITY_REQUEST");
const {
  createEntityClose
} = Object(redux_actions__WEBPACK_IMPORTED_MODULE_0__["createActions"])("CREATE_ENTITY_CLOSE");
/**
 * Reducer
 */

/* harmony default export */ __webpack_exports__["default"] = (Object(redux_actions__WEBPACK_IMPORTED_MODULE_0__["handleActions"])({
  ADD_ENTITY_SUCCESS: state => ({
    showCreate: false,
    value: null
  }),
  CREATE_ENTITY_REQUEST: (state, action) => ({
    showCreate: true,
    value: action.payload
  }),
  CREATE_ENTITY_CLOSE: state => ({
    showCreate: false,
    value: null
  })
}, {
  showCreate: false,
  value: null
}));

/***/ }),

/***/ "./src/common/containers/create-entity-form/index.js":
/*!***********************************************************!*\
  !*** ./src/common/containers/create-entity-form/index.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var _components_create_entity_form__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/create-entity-form */ "./src/common/components/create-entity-form.js");
/* harmony import */ var _Edit_components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../Edit/components/AddEntity/actions */ "./src/Edit/components/AddEntity/actions.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./actions */ "./src/common/containers/create-entity-form/actions.js");
/**
 * External dependencies.
 */


/**
 * Internal dependencies
 */





const mapStateToProps = ({
  createEntityForm
}) => createEntityForm;

const mapDispatchToProps = dispatch => ({
  onCancel: () => dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_4__["createEntityClose"])()),
  onSubmit: value => dispatch(Object(_Edit_components_AddEntity_actions__WEBPACK_IMPORTED_MODULE_3__["addEntityRequest"])(value))
});

/* harmony default export */ __webpack_exports__["default"] = (Object(react_redux__WEBPACK_IMPORTED_MODULE_1__["connect"])(mapStateToProps, mapDispatchToProps)(_components_create_entity_form__WEBPACK_IMPORTED_MODULE_2__["default"]));

/***/ }),

/***/ "./src/common/containers/related-posts/actions.js":
/*!********************************************************!*\
  !*** ./src/common/containers/related-posts/actions.js ***!
  \********************************************************/
/*! exports provided: relatedPostsRequest, relatedPostsSuccess, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "relatedPostsRequest", function() { return relatedPostsRequest; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "relatedPostsSuccess", function() { return relatedPostsSuccess; });
/* harmony import */ var redux_actions__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! redux-actions */ "./node_modules/redux-actions/es/index.js");
/**
 * This file contains actions to retrieve and display the related posts.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */

/**
 * Actions
 */

const {
  relatedPostsRequest,
  relatedPostsSuccess
} = Object(redux_actions__WEBPACK_IMPORTED_MODULE_0__["createActions"])("RELATED_POSTS_REQUEST", "RELATED_POSTS_SUCCESS");
/**
 * Reducer
 */

/* harmony default export */ __webpack_exports__["default"] = (Object(redux_actions__WEBPACK_IMPORTED_MODULE_0__["handleActions"])({
  RELATED_POSTS_SUCCESS: (state, {
    payload
  }) => ({
    posts: payload
  })
}, {
  posts: []
}));

/***/ }),

/***/ "./src/common/containers/related-posts/index.js":
/*!******************************************************!*\
  !*** ./src/common/containers/related-posts/index.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react_redux__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react-redux */ "./node_modules/react-redux/es/index.js");
/* harmony import */ var _components_related_posts_panel__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/related-posts-panel */ "./src/common/components/related-posts-panel/index.js");
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./actions */ "./src/common/containers/related-posts/actions.js");
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */




const mapStateToProps = ({
  relatedPosts
}) => relatedPosts;

const mapDispatchToProps = dispatch => ({
  requestRelatedPosts: () => dispatch(Object(_actions__WEBPACK_IMPORTED_MODULE_2__["relatedPostsRequest"])())
});

/* harmony default export */ __webpack_exports__["default"] = (Object(react_redux__WEBPACK_IMPORTED_MODULE_0__["connect"])(mapStateToProps, mapDispatchToProps)(_components_related_posts_panel__WEBPACK_IMPORTED_MODULE_1__["default"]));

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blocks"]; }());

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/compose":
/*!*********************************!*\
  !*** external ["wp","compose"] ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "@wordpress/edit-post":
/*!**********************************!*\
  !*** external ["wp","editPost"] ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["editPost"]; }());

/***/ }),

/***/ "@wordpress/editor":
/*!********************************!*\
  !*** external ["wp","editor"] ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["editor"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/hooks":
/*!*******************************!*\
  !*** external ["wp","hooks"] ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["hooks"]; }());

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ "@wordpress/plugins":
/*!*********************************!*\
  !*** external ["wp","plugins"] ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["plugins"]; }());

/***/ }),

/***/ "@wordpress/rich-text":
/*!**********************************!*\
  !*** external ["wp","richText"] ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["richText"]; }());

/***/ }),

/***/ "backbone":
/*!***************************!*\
  !*** external "Backbone" ***!
  \***************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = Backbone;

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["React"]; }());

/***/ })

/******/ });
//# sourceMappingURL=block-editor.js.map