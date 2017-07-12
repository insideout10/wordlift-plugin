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
/******/ 	return __webpack_require__(__webpack_require__.s = 147);
/******/ })
/************************************************************************/
/******/ ({

/***/ 106:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__delay__ = __webpack_require__(32);
/**
 * Validators: Key Validator.
 *
 * Validate WordLift's key in inputs.
 *
 * @since 3.11.0
 */

/**
 * Internal dependencies
 */


// Map $ to jQuery.
var $ = jQuery;

/**
 * Create a key validator on the element with the specified selector.
 *
 * @since 3.11.0
 * @param {string} selector The element selector.
 */
var KeyValidator = function KeyValidator(selector) {
	$(selector).on('keyup', function () {
		// Get a jQuery reference to the object.
		var $this = $(this);

		// Remove any preexisting states, including the `untouched` class
		// which is set initially to prevent displaying the
		// `valid`/`invalid` indicator.
		$this.removeClass('untouched valid invalid');

		// Delay execution of the validation.
		__webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__delay__["a" /* default */])($this, function () {
			// Post the validation request.
			wp.ajax.post('wl_validate_key', { key: $this.val() }).done(function (data) {
				// If the key is valid then set the process class.
				if (data && data.valid) {
					$this.addClass('valid');
				} else {
					$this.addClass('invalid');
				}
			});
		});
	});
};

// Finally export the `KeyValidator` function.
/* harmony default export */ __webpack_exports__["a"] = (KeyValidator);

/***/ }),

/***/ 107:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * Media Uploader.
 *
 * Provide a function to use WordPress' Media Uploader by binding a button's
 * click event.
 *
 * @since 3.11.0
 */

// Set a reference to jQuery.
var $ = jQuery;

/**
 * Hook WordPress' Media Uploader.
 *
 * @since 3.11.0
 * @param {string} selector The button's selector.
 * @param {Object} options The Media Uploader's options.
 * @param {Function} callback A callback function which will receive the
 *     selected attachment.
 * @constructor
 */
var MediaUploader = function MediaUploader(selector, options, callback) {
  // Create a WP media uploader.
  var uploader = wp.media(options);

  // Catch `select` events on the uploader.
  uploader.on('select', function () {
    // Get the selected attachment.
    callback(uploader.state().get('selection').first().toJSON());
  });

  // Add logo.
  $(selector).on('click', function () {
    uploader.open();
  });
};

// Finally export the `MediaUploader`.
/* harmony default export */ __webpack_exports__["a"] = (MediaUploader);

/***/ }),

/***/ 108:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

/**
 * Created by david on 21/02/2017.
 */

var $ = jQuery;

/**
 * Create a Select2 element on the element identified by the selector.
 *
 * @since 3.11.0
 * @param {string} selector The element selector.
 * @param {Object} args Custom options.
 * @constructor
 */
var Select2 = function Select2(selector) {
	var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

	// Cycle through each element to create `Select2`.
	$(selector).each(function () {
		//
		var $this = $(this);

		var options = _extends({}, {
			width: '100%',
			data: $this.data('wl-select2-data'),
			escapeMarkup: function escapeMarkup(markup) {
				return markup;
			},
			templateResult: _.template($this.data('wl-select2-template-result')),
			templateSelection: _.template($this.data('wl-select2-template-selection'))
		}, args);

		// Create the tabs and set the default active element.
		$this.select2(options);
	});
};

// Finally export `Select2`.
/* harmony default export */ __webpack_exports__["a"] = (Select2);

/***/ }),

/***/ 109:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * Tabs.
 *
 * Create a tabbed UI.
 *
 * @since 3.11.0
 */

// Set a reference to jQuery.
var $ = jQuery;

/**
 * Create a tabbed UI on the element with the specified selector.
 *
 * @since 3.11.0
 * @param {string} selector The selector.
 * @constructor
 */
var Tabs = function Tabs(selector) {
  // Although in jQuery UI 1.12 it's possible to configure the css
  // classes, WP 4.2 uses jQuery 1.11.
  $(selector).each(function () {
    //
    var $this = $(this);

    // Create the tabs and set the default active element.
    $this.tabs({ active: $this.data('active') });
  });
};

// Finally export `Tabs`.
/* harmony default export */ __webpack_exports__["a"] = (Tabs);

/***/ }),

/***/ 112:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(269);
if(typeof content === 'string') content = [[module.i, content, '']];
// Prepare cssTransformation
var transform;

var options = {}
options.transform = transform
// add the styles to the DOM
var update = __webpack_require__(59)(content, options);
if(content.locals) module.exports = content.locals;
// Hot Module Replacement
if(false) {
	// When the styles change, update the <style> tags
	if(!content.locals) {
		module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/sass-loader/lib/loader.js!./index.scss", function() {
			var newContent = require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/sass-loader/lib/loader.js!./index.scss");
			if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
			update(newContent);
		});
	}
	// When the module is disposed, remove the <style> tags
	module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 147:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__styles_index_scss__ = __webpack_require__(112);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__styles_index_scss___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__styles_index_scss__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__common_key_validator__ = __webpack_require__(106);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__common_media_uploader__ = __webpack_require__(107);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__common_tabs__ = __webpack_require__(109);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__common_select2__ = __webpack_require__(108);
/**
 * Entry: wordlift-admin-settings-page.js
 */

/**
 * Internal dependencies
 */






/**
 * UI interactions on the WordLift Settings page
 *
 * @since 3.11.0
 */
(function ($, settings) {
	$(function () {
		// Attach the WL key validator to the `#wl-key` element.
		__webpack_require__.i(__WEBPACK_IMPORTED_MODULE_1__common_key_validator__["a" /* default */])('#wl-key');

		// Attach the Media Uploader to the #wl-publisher-logo
		__webpack_require__.i(__WEBPACK_IMPORTED_MODULE_2__common_media_uploader__["a" /* default */])('#wl-publisher-media-uploader', {
			title: settings.l10n.logo_selection_title,
			button: settings.l10n.logo_selection_button,
			multiple: false,
			library: { type: 'image' }
		}, function (attachment) {
			// Set the selected image as the preview image
			$('#wl-publisher-media-uploader-preview').attr('src', attachment.url).show();

			// Set the logo id.
			$('#wl-publisher-media-uploader-id').val(attachment.id);
		});

		// Create the tabs.
		__webpack_require__.i(__WEBPACK_IMPORTED_MODULE_3__common_tabs__["a" /* default */])('.wl-tabs-element');

		// Create the Select2.
		__webpack_require__.i(__WEBPACK_IMPORTED_MODULE_4__common_select2__["a" /* default */])('.wl-select2-element', {
			containerCssClass: 'wl-admin-settings-page-select2',
			dropdownCssClass: 'wl-admin-settings-page-select2'
		});
	});
})(jQuery, wlSettings);

/***/ }),

/***/ 269:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(35)(undefined);
// imports


// module
exports.push([module.i, "#wl-settings-page input::-ms-clear {\n  display: none; }\n\n#wl-settings-page #wl-entity-base-path,\n#wl-settings-page #wl-key {\n  width: 100%;\n  max-width: 480px;\n  height: 32px;\n  padding: 0 8px;\n  line-height: 32px;\n  font-size: 14px;\n  color: #32373c;\n  border-radius: 4px;\n  border: 1px solid #ddd;\n  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.07);\n  background-color: #fff;\n  outline: 0;\n  transition: 50ms border-color ease-in-out;\n  background-position: 448px 8px;\n  background-repeat: no-repeat; }\n  #wl-settings-page #wl-entity-base-path.valid,\n  #wl-settings-page #wl-key.valid {\n    background-image: url(" + __webpack_require__(272) + ");\n    padding-right: 32px; }\n  #wl-settings-page #wl-entity-base-path.invalid,\n  #wl-settings-page #wl-key.invalid {\n    background-image: url(" + __webpack_require__(271) + ");\n    padding-right: 32px; }\n\n#wl-settings-page #wl-entity-base-path {\n  background-color: #f5f5f5; }\n\n#wl-settings-page .wl-tabs-element {\n  border-radius: 0;\n  border: 0;\n  background: none; }\n  #wl-settings-page .wl-tabs-element .nav-tab-wrapper {\n    border-radius: 0;\n    border: 0;\n    border-bottom: 1px solid #ccc;\n    background: none;\n    padding-bottom: 0;\n    padding-left: 6px; }\n    #wl-settings-page .wl-tabs-element .nav-tab-wrapper .nav-tab {\n      border-radius: 0;\n      border: 1px solid #ccc !important;\n      background: #e4e4e4;\n      color: #555;\n      margin: 0 3px 0 0;\n      vertical-align: top;\n      white-space: nowrap; }\n      #wl-settings-page .wl-tabs-element .nav-tab-wrapper .nav-tab:focus {\n        box-shadow: none;\n        outline: none; }\n    #wl-settings-page .wl-tabs-element .nav-tab-wrapper .ui-state-active, #wl-settings-page .wl-tabs-element .nav-tab-wrapper .ui-state-active:hover {\n      border-bottom: 1px solid #f1f1f1 !important;\n      background: #f1f1f1;\n      color: #000; }\n\n.wl-admin-settings-page-select2.select2-selection {\n  min-height: 32px;\n  max-height: 32px;\n  overflow-y: auto; }\n\n.wl-admin-settings-page-select2 .wl-select2-thumbnail {\n  width: 24px;\n  height: 24px;\n  display: inline-block;\n  background: no-repeat center;\n  background-size: contain;\n  margin: 4px 8px 4px 0; }\n\n.wl-admin-settings-page-select2 .wl-select2-type {\n  float: right;\n  line-height: 32px;\n  font-weight: 600; }\n\n.select2-results__options {\n  color: #666 !important; }\n\n.select2-results__option .wl-select2-type {\n  float: right;\n  line-height: 32px;\n  font-weight: 600; }\n\n.select2-results__option--highlighted {\n  background-color: #f5f5f5 !important;\n  color: #2E92FF !important; }\n\n.ui-tabs .ui-tabs-panel {\n  max-width: 480px;\n  min-height: 32px;\n  border-radius: 2px;\n  padding: 0 !important;\n  margin: 16px 0; }\n  .ui-tabs .ui-tabs-panel * {\n    vertical-align: middle; }\n  .ui-tabs .ui-tabs-panel p {\n    max-height: 32px;\n    margin: 0;\n    width: 100%;\n    color: #4a4a4a; }\n\n.select2-container--default .select2-selection--single {\n  overflow: hidden;\n  padding: 0 0;\n  line-height: 28px;\n  font-size: 12px; }\n\n.wl-select2-thumbnail {\n  margin-right: 8px; }\n\n#tabs-2 p {\n  margin: 8px 0; }\n  #tabs-2 p:first-of-type {\n    margin-top: 24px; }\n\n#wl-publisher-name input,\n#wl-site-language {\n  width: 100%;\n  max-width: 480px;\n  min-height: 32px;\n  margin-top: 8px;\n  padding: 4px 8px;\n  color: #32373c;\n  border-radius: 4px;\n  border: 1px solid #ddd;\n  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.07);\n  background-color: #fff;\n  outline: 0; }\n\n#wl-publisher-type {\n  max-width: 480px;\n  width: 100%; }\n  #wl-publisher-type span {\n    width: 45%;\n    display: inline-block; }\n\n#wl-publisher-media-uploader {\n  height: 32px;\n  float: left; }\n\n#wl-publisher-media-uploader-preview {\n  max-width: 256px;\n  max-height: 256px;\n  margin-bottom: 16px;\n  display: block; }\n", ""]);

// exports


/***/ }),

/***/ 271:
/***/ (function(module, exports) {

module.exports = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAA8ElEQVR42p3UOw7CMBAEUNNADRI3ACQogVPkqxQu4A40FHAgSiipcpVQUnEAqkjLDsJRkMU4wtIqv/FLnNgxaCLSkzi2kiR7sXZsAk2zK62jZNkax23kJFEkKN2/a2DyC9HMRjM1su9tmm6dbh3iYQRpZZ9aQ4Ph4ATBPMQr5PBO0IlhBEHmbFxDuMH8ehDkKrvdAEZHLIyEse6Ij2E4BCo5wr8OnxphhGN/IxwLIyU6BTFJ0yWdJ9b2ESZYhQyWyIEhnaZGni/cL6Gm84Rjt+aG+BVgFX8uXDzEx6oGKYrZV0DVkQ5zajo0PIFi8/bQXxTkJIW2nf39AAAAAElFTkSuQmCC"

/***/ }),

/***/ 272:
/***/ (function(module, exports) {

module.exports = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAARCAYAAAA2cze9AAAA0ElEQVR4Aa3NgeYCQRDH8fMH/AkgAZrZVAQIhFCP0TuUIgB70wUCBAlIz7S7LgQgvUAh6jodN8Gmxh4D7refbxTyo1P3nzL6C4vmYGxgrS08tMHzwqpBONjiThvI3u5KKTYDw3xk1LwcJmmjF1tYklNDOcyXOOgXQ4ejHL6XVacmApjP4uw1XB1aldjAhX9yQArz2HVqniEHJDAHcPMpIIb5Iey9AYvTb2FRQAB7AgJYEBDAgoAAFgQ8sCxABrcFjDft1DgK/VHartNRVX999wQwVJB5+G9izgAAAABJRU5ErkJggg=="

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

/***/ }),

/***/ 35:
/***/ (function(module, exports) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
module.exports = function (useSourceMap) {
	var list = [];

	// return the list of modules as css string
	list.toString = function toString() {
		return this.map(function (item) {
			var content = cssWithMappingToString(item, useSourceMap);
			if (item[2]) {
				return "@media " + item[2] + "{" + content + "}";
			} else {
				return content;
			}
		}).join("");
	};

	// import a list of modules into the list
	list.i = function (modules, mediaQuery) {
		if (typeof modules === "string") modules = [[null, modules, ""]];
		var alreadyImportedModules = {};
		for (var i = 0; i < this.length; i++) {
			var id = this[i][0];
			if (typeof id === "number") alreadyImportedModules[id] = true;
		}
		for (i = 0; i < modules.length; i++) {
			var item = modules[i];
			// skip already imported module
			// this implementation is not 100% perfect for weird media query combinations
			//  when a module is imported multiple times with different media queries.
			//  I hope this will never occur (Hey this way we have smaller bundles)
			if (typeof item[0] !== "number" || !alreadyImportedModules[item[0]]) {
				if (mediaQuery && !item[2]) {
					item[2] = mediaQuery;
				} else if (mediaQuery) {
					item[2] = "(" + item[2] + ") and (" + mediaQuery + ")";
				}
				list.push(item);
			}
		}
	};
	return list;
};

function cssWithMappingToString(item, useSourceMap) {
	var content = item[1] || '';
	var cssMapping = item[3];
	if (!cssMapping) {
		return content;
	}

	if (useSourceMap && typeof btoa === 'function') {
		var sourceMapping = toComment(cssMapping);
		var sourceURLs = cssMapping.sources.map(function (source) {
			return '/*# sourceURL=' + cssMapping.sourceRoot + source + ' */';
		});

		return [content].concat(sourceURLs).concat([sourceMapping]).join('\n');
	}

	return [content].join('\n');
}

// Adapted from convert-source-map (MIT)
function toComment(sourceMap) {
	// eslint-disable-next-line no-undef
	var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));
	var data = 'sourceMappingURL=data:application/json;charset=utf-8;base64,' + base64;

	return '/*# ' + data + ' */';
}

/***/ }),

/***/ 58:
/***/ (function(module, exports) {


/**
 * When source maps are enabled, `style-loader` uses a link element with a data-uri to
 * embed the css on the page. This breaks all relative urls because now they are relative to a
 * bundle instead of the current page.
 *
 * One solution is to only use full urls, but that may be impossible.
 *
 * Instead, this function "fixes" the relative urls to be absolute according to the current page location.
 *
 * A rudimentary test suite is located at `test/fixUrls.js` and can be run via the `npm test` command.
 *
 */

module.exports = function (css) {
	// get current location
	var location = typeof window !== "undefined" && window.location;

	if (!location) {
		throw new Error("fixUrls requires window.location");
	}

	// blank or null?
	if (!css || typeof css !== "string") {
		return css;
	}

	var baseUrl = location.protocol + "//" + location.host;
	var currentDir = baseUrl + location.pathname.replace(/\/[^\/]*$/, "/");

	// convert each url(...)
	/*
 This regular expression is just a way to recursively match brackets within
 a string.
 	 /url\s*\(  = Match on the word "url" with any whitespace after it and then a parens
    (  = Start a capturing group
      (?:  = Start a non-capturing group
          [^)(]  = Match anything that isn't a parentheses
          |  = OR
          \(  = Match a start parentheses
              (?:  = Start another non-capturing groups
                  [^)(]+  = Match anything that isn't a parentheses
                  |  = OR
                  \(  = Match a start parentheses
                      [^)(]*  = Match anything that isn't a parentheses
                  \)  = Match a end parentheses
              )  = End Group
              *\) = Match anything and then a close parens
          )  = Close non-capturing group
          *  = Match anything
       )  = Close capturing group
  \)  = Match a close parens
 	 /gi  = Get all matches, not the first.  Be case insensitive.
  */
	var fixedCss = css.replace(/url\s*\(((?:[^)(]|\((?:[^)(]+|\([^)(]*\))*\))*)\)/gi, function (fullMatch, origUrl) {
		// strip quotes (if they exist)
		var unquotedOrigUrl = origUrl.trim().replace(/^"(.*)"$/, function (o, $1) {
			return $1;
		}).replace(/^'(.*)'$/, function (o, $1) {
			return $1;
		});

		// already a full url? no change
		if (/^(#|data:|http:\/\/|https:\/\/|file:\/\/\/)/i.test(unquotedOrigUrl)) {
			return fullMatch;
		}

		// convert the url to a full url
		var newUrl;

		if (unquotedOrigUrl.indexOf("//") === 0) {
			//TODO: should we add protocol?
			newUrl = unquotedOrigUrl;
		} else if (unquotedOrigUrl.indexOf("/") === 0) {
			// path should be relative to the base url
			newUrl = baseUrl + unquotedOrigUrl; // already starts with '/'
		} else {
			// path should be relative to current directory
			newUrl = currentDir + unquotedOrigUrl.replace(/^\.\//, ""); // Strip leading './'
		}

		// send back the fixed url(...)
		return "url(" + JSON.stringify(newUrl) + ")";
	});

	// send back the fixed css
	return fixedCss;
};

/***/ }),

/***/ 59:
/***/ (function(module, exports, __webpack_require__) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/

var stylesInDom = {};

var	memoize = function (fn) {
	var memo;

	return function () {
		if (typeof memo === "undefined") memo = fn.apply(this, arguments);
		return memo;
	};
};

var isOldIE = memoize(function () {
	// Test for IE <= 9 as proposed by Browserhacks
	// @see http://browserhacks.com/#hack-e71d8692f65334173fee715c222cb805
	// Tests for existence of standard globals is to allow style-loader
	// to operate correctly into non-standard environments
	// @see https://github.com/webpack-contrib/style-loader/issues/177
	return window && document && document.all && !window.atob;
});

var getElement = (function (fn) {
	var memo = {};

	return function(selector) {
		if (typeof memo[selector] === "undefined") {
			memo[selector] = fn.call(this, selector);
		}

		return memo[selector]
	};
})(function (target) {
	return document.querySelector(target)
});

var singleton = null;
var	singletonCounter = 0;
var	stylesInsertedAtTop = [];

var	fixUrls = __webpack_require__(58);

module.exports = function(list, options) {
	if (typeof DEBUG !== "undefined" && DEBUG) {
		if (typeof document !== "object") throw new Error("The style-loader cannot be used in a non-browser environment");
	}

	options = options || {};

	options.attrs = typeof options.attrs === "object" ? options.attrs : {};

	// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
	// tags it will allow on a page
	if (!options.singleton) options.singleton = isOldIE();

	// By default, add <style> tags to the <head> element
	if (!options.insertInto) options.insertInto = "head";

	// By default, add <style> tags to the bottom of the target
	if (!options.insertAt) options.insertAt = "bottom";

	var styles = listToStyles(list, options);

	addStylesToDom(styles, options);

	return function update (newList) {
		var mayRemove = [];

		for (var i = 0; i < styles.length; i++) {
			var item = styles[i];
			var domStyle = stylesInDom[item.id];

			domStyle.refs--;
			mayRemove.push(domStyle);
		}

		if(newList) {
			var newStyles = listToStyles(newList, options);
			addStylesToDom(newStyles, options);
		}

		for (var i = 0; i < mayRemove.length; i++) {
			var domStyle = mayRemove[i];

			if(domStyle.refs === 0) {
				for (var j = 0; j < domStyle.parts.length; j++) domStyle.parts[j]();

				delete stylesInDom[domStyle.id];
			}
		}
	};
};

function addStylesToDom (styles, options) {
	for (var i = 0; i < styles.length; i++) {
		var item = styles[i];
		var domStyle = stylesInDom[item.id];

		if(domStyle) {
			domStyle.refs++;

			for(var j = 0; j < domStyle.parts.length; j++) {
				domStyle.parts[j](item.parts[j]);
			}

			for(; j < item.parts.length; j++) {
				domStyle.parts.push(addStyle(item.parts[j], options));
			}
		} else {
			var parts = [];

			for(var j = 0; j < item.parts.length; j++) {
				parts.push(addStyle(item.parts[j], options));
			}

			stylesInDom[item.id] = {id: item.id, refs: 1, parts: parts};
		}
	}
}

function listToStyles (list, options) {
	var styles = [];
	var newStyles = {};

	for (var i = 0; i < list.length; i++) {
		var item = list[i];
		var id = options.base ? item[0] + options.base : item[0];
		var css = item[1];
		var media = item[2];
		var sourceMap = item[3];
		var part = {css: css, media: media, sourceMap: sourceMap};

		if(!newStyles[id]) styles.push(newStyles[id] = {id: id, parts: [part]});
		else newStyles[id].parts.push(part);
	}

	return styles;
}

function insertStyleElement (options, style) {
	var target = getElement(options.insertInto)

	if (!target) {
		throw new Error("Couldn't find a style target. This probably means that the value for the 'insertInto' parameter is invalid.");
	}

	var lastStyleElementInsertedAtTop = stylesInsertedAtTop[stylesInsertedAtTop.length - 1];

	if (options.insertAt === "top") {
		if (!lastStyleElementInsertedAtTop) {
			target.insertBefore(style, target.firstChild);
		} else if (lastStyleElementInsertedAtTop.nextSibling) {
			target.insertBefore(style, lastStyleElementInsertedAtTop.nextSibling);
		} else {
			target.appendChild(style);
		}
		stylesInsertedAtTop.push(style);
	} else if (options.insertAt === "bottom") {
		target.appendChild(style);
	} else {
		throw new Error("Invalid value for parameter 'insertAt'. Must be 'top' or 'bottom'.");
	}
}

function removeStyleElement (style) {
	if (style.parentNode === null) return false;
	style.parentNode.removeChild(style);

	var idx = stylesInsertedAtTop.indexOf(style);
	if(idx >= 0) {
		stylesInsertedAtTop.splice(idx, 1);
	}
}

function createStyleElement (options) {
	var style = document.createElement("style");

	options.attrs.type = "text/css";

	addAttrs(style, options.attrs);
	insertStyleElement(options, style);

	return style;
}

function createLinkElement (options) {
	var link = document.createElement("link");

	options.attrs.type = "text/css";
	options.attrs.rel = "stylesheet";

	addAttrs(link, options.attrs);
	insertStyleElement(options, link);

	return link;
}

function addAttrs (el, attrs) {
	Object.keys(attrs).forEach(function (key) {
		el.setAttribute(key, attrs[key]);
	});
}

function addStyle (obj, options) {
	var style, update, remove, result;

	// If a transform function was defined, run it on the css
	if (options.transform && obj.css) {
	    result = options.transform(obj.css);

	    if (result) {
	    	// If transform returns a value, use that instead of the original css.
	    	// This allows running runtime transformations on the css.
	    	obj.css = result;
	    } else {
	    	// If the transform function returns a falsy value, don't add this css.
	    	// This allows conditional loading of css
	    	return function() {
	    		// noop
	    	};
	    }
	}

	if (options.singleton) {
		var styleIndex = singletonCounter++;

		style = singleton || (singleton = createStyleElement(options));

		update = applyToSingletonTag.bind(null, style, styleIndex, false);
		remove = applyToSingletonTag.bind(null, style, styleIndex, true);

	} else if (
		obj.sourceMap &&
		typeof URL === "function" &&
		typeof URL.createObjectURL === "function" &&
		typeof URL.revokeObjectURL === "function" &&
		typeof Blob === "function" &&
		typeof btoa === "function"
	) {
		style = createLinkElement(options);
		update = updateLink.bind(null, style, options);
		remove = function () {
			removeStyleElement(style);

			if(style.href) URL.revokeObjectURL(style.href);
		};
	} else {
		style = createStyleElement(options);
		update = applyToTag.bind(null, style);
		remove = function () {
			removeStyleElement(style);
		};
	}

	update(obj);

	return function updateStyle (newObj) {
		if (newObj) {
			if (
				newObj.css === obj.css &&
				newObj.media === obj.media &&
				newObj.sourceMap === obj.sourceMap
			) {
				return;
			}

			update(obj = newObj);
		} else {
			remove();
		}
	};
}

var replaceText = (function () {
	var textStore = [];

	return function (index, replacement) {
		textStore[index] = replacement;

		return textStore.filter(Boolean).join('\n');
	};
})();

function applyToSingletonTag (style, index, remove, obj) {
	var css = remove ? "" : obj.css;

	if (style.styleSheet) {
		style.styleSheet.cssText = replaceText(index, css);
	} else {
		var cssNode = document.createTextNode(css);
		var childNodes = style.childNodes;

		if (childNodes[index]) style.removeChild(childNodes[index]);

		if (childNodes.length) {
			style.insertBefore(cssNode, childNodes[index]);
		} else {
			style.appendChild(cssNode);
		}
	}
}

function applyToTag (style, obj) {
	var css = obj.css;
	var media = obj.media;

	if(media) {
		style.setAttribute("media", media)
	}

	if(style.styleSheet) {
		style.styleSheet.cssText = css;
	} else {
		while(style.firstChild) {
			style.removeChild(style.firstChild);
		}

		style.appendChild(document.createTextNode(css));
	}
}

function updateLink (link, options, obj) {
	var css = obj.css;
	var sourceMap = obj.sourceMap;

	/*
		If convertToAbsoluteUrls isn't defined, but sourcemaps are enabled
		and there is no publicPath defined then lets turn convertToAbsoluteUrls
		on by default.  Otherwise default to the convertToAbsoluteUrls option
		directly
	*/
	var autoFixUrls = options.convertToAbsoluteUrls === undefined && sourceMap;

	if (options.convertToAbsoluteUrls || autoFixUrls) {
		css = fixUrls(css);
	}

	if (sourceMap) {
		// http://stackoverflow.com/a/26603875
		css += "\n/*# sourceMappingURL=data:application/json;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + " */";
	}

	var blob = new Blob([css], { type: "text/css" });

	var oldSrc = link.href;

	link.href = URL.createObjectURL(blob);

	if(oldSrc) URL.revokeObjectURL(oldSrc);
}


/***/ })

/******/ });
//# sourceMappingURL=wordlift-admin-settings-page.bundle.js.map