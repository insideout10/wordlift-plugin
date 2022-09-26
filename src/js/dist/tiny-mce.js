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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/tiny-mce/index.js");
/******/ })
/************************************************************************/
/******/ ({

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

/***/ "./src/common/helpers.js":
/*!*******************************!*\
  !*** ./src/common/helpers.js ***!
  \*******************************/
/*! exports provided: isAnnotationElement */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isAnnotationElement", function() { return isAnnotationElement; });
/**
 * This file provides helper functions.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * Check whether the provided HTMLElement is an annotation.
 *
 * An {@link HTMLElement} is considered an annotation if it satisfies the following
 * requirements:
 *  - it has a `span` tagName.
 *  - it has an `id` attribute.
 *  - it has a `textannotation` class name.
 *
 * @since 3.23.0
 * @param {HTMLElement} el The {@link HTMLElement}.
 * @returns {boolean} True if it's annotation span otherwise false.
 */
const isAnnotationElement = el => {
  return "undefined" !== typeof el && "undefined" !== typeof el.tagName && "undefined" !== typeof el.id && "undefined" !== typeof el.classList && "SPAN" === el.tagName && el.classList.contains("textannotation");
};

/***/ }),

/***/ "./src/tiny-mce/index.js":
/*!*******************************!*\
  !*** ./src/tiny-mce/index.js ***!
  \*******************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var backbone__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! backbone */ "backbone");
/* harmony import */ var backbone__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(backbone__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _common_constants__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../common/constants */ "./src/common/constants.js");
/* harmony import */ var _common_helpers__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../common/helpers */ "./src/common/helpers.js");
/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./index.scss */ "./src/tiny-mce/index.scss");
/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_index_scss__WEBPACK_IMPORTED_MODULE_4__);
/**
 * This file provides a TinyMCE plugin for integration with WordLift.
 *
 * TinyMCE is loaded in different places within WordPress. We're specifically
 * targeting TinyMCE used as editor in Gutenberg's `classic` block.
 *
 * We're aiming to send an `action` every time the text selection changes. The
 * action should be caught by other components in page to update the UI (namely
 * the `Add ...` button in the classification box.
 *
 * The plugin name `wl_tinymce_2` is also defined in
 * src/includes/class-wordlift-tinymce-adapter.php and *must* match.
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




const tinymce = global["tinymce"];
tinymce.PluginManager.add("wl_tinymce_2", function (ed) {
  // Capture `NodeChange` events and broadcast the selected text.
  ed.on("NodeChange", e => {
    Object(backbone__WEBPACK_IMPORTED_MODULE_1__["trigger"])(_common_constants__WEBPACK_IMPORTED_MODULE_2__["SELECTION_CHANGED"], {
      selection: ed.selection.getContent({
        format: "text"
      }),
      editor: ed,
      editorType: "tinymce"
    });
    console.log(_common_constants__WEBPACK_IMPORTED_MODULE_2__["SELECTION_CHANGED"], {
      selection: ed.selection.getContent({
        format: "text"
      }),
      editor: ed,
      editorType: "tinymce"
    }); // Fire the annotation change.

    const payload = "undefined" !== typeof e && Object(_common_helpers__WEBPACK_IMPORTED_MODULE_3__["isAnnotationElement"])(e.element) ? // Set the payload to `{ annotationId }` if it's an annotation otherwise to null.
    e.element.id : undefined;
    Object(backbone__WEBPACK_IMPORTED_MODULE_1__["trigger"])(_common_constants__WEBPACK_IMPORTED_MODULE_2__["ANNOTATION_CHANGED"], payload);
  });
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./src/tiny-mce/index.scss":
/*!*********************************!*\
  !*** ./src/tiny-mce/index.scss ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

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
//# sourceMappingURL=tiny-mce.js.map