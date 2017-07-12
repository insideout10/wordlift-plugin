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
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 148);
/******/ })
/************************************************************************/
/******/ ({

/***/ 148:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__common_delay__ = __webpack_require__(32);
/**
 * TinyMCE Plugins: manage annotations visibility.
 *
 * This plugin will fade out/in annotations according on the user actions on the
 * TinyMCE editor.
 *
 * @since 3.12.0
 */

/**
 * Internal dependencies
 */


// Set a reference to jQuery.
var $ = jQuery;

// Add our plugin.
tinymce.PluginManager.add('wl_tinymce', function (editor) {
	// Listen for `KeyPress` events.
	//
	// See https://www.tinymce.com/docs/api/tinymce/tinymce.editor/#on
	editor.on('KeyDown', function () {
		// Set a reference to the container. We cannot do it before since the
		// Area Container isn't set yet.
		var $body = $(editor.getBody());

		// Add the typing class.
		$body.addClass('wl-tinymce-typing');

		// Delay a timer in 3 secs to remove the class.
		__webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__common_delay__["a" /* default */])($body, function () {
			$body.removeClass('wl-tinymce-typing');
		}, 3000);
	});
});

/***/ }),

/***/ 32:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * Delay a function call by half a second.
 *
 * Any function can be delayed using `delay`. The timeout for the call is bound
 * to the provided element. If another function call is delayed on the same
 * element, any previous timeout is cancelled.
 *
 * This function is used to validate in real-time inputs when the user presses
 * a key, but allowing the user to press more keys (hence the delay).
 *
 * @since 3.9.0
 *
 * @param {Object} $elem A jQuery element reference which will hold the timeout
 *     reference.
 * @param {Function} fn The function to call.
 * @param {number} timeout The timeout, by default 500 ms.
 * @param {...Object} args Additional arguments for the callback.
 */
var delay = function delay($elem, fn) {
  for (var _len = arguments.length, args = Array(_len > 3 ? _len - 3 : 0), _key = 3; _key < _len; _key++) {
    args[_key - 3] = arguments[_key];
  }

  var timeout = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 500;

  // Clear a validation timeout.
  clearTimeout($elem.data('timeout'));

  // Validate the key, after a delay, so that another key is pressed, this
  // validation is cancelled.
  $elem.data('timeout', setTimeout.apply(undefined, [fn, timeout].concat(args)));
};

// Finally export the `delay` function.
/* harmony default export */ __webpack_exports__["a"] = (delay);

/***/ })

/******/ });
//# sourceMappingURL=wordlift-admin-tinymce.bundle.js.map