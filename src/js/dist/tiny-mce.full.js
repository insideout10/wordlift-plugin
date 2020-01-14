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

eval("var g;\n\n// This works in non-strict mode\ng = (function() {\n\treturn this;\n})();\n\ntry {\n\t// This works if eval is allowed (see CSP)\n\tg = g || new Function(\"return this\")();\n} catch (e) {\n\t// This works if the window reference is available\n\tif (typeof window === \"object\") g = window;\n}\n\n// g can still be undefined, but nothing to do about it...\n// We return undefined, instead of nothing here, so it's\n// easier to handle this case. if(!global) { ...}\n\nmodule.exports = g;\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vKHdlYnBhY2spL2J1aWxkaW4vZ2xvYmFsLmpzPzk4NWMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsQ0FBQzs7QUFFRDtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSw0Q0FBNEM7O0FBRTVDIiwiZmlsZSI6Ii4vbm9kZV9tb2R1bGVzL3dlYnBhY2svYnVpbGRpbi9nbG9iYWwuanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyJ2YXIgZztcblxuLy8gVGhpcyB3b3JrcyBpbiBub24tc3RyaWN0IG1vZGVcbmcgPSAoZnVuY3Rpb24oKSB7XG5cdHJldHVybiB0aGlzO1xufSkoKTtcblxudHJ5IHtcblx0Ly8gVGhpcyB3b3JrcyBpZiBldmFsIGlzIGFsbG93ZWQgKHNlZSBDU1ApXG5cdGcgPSBnIHx8IG5ldyBGdW5jdGlvbihcInJldHVybiB0aGlzXCIpKCk7XG59IGNhdGNoIChlKSB7XG5cdC8vIFRoaXMgd29ya3MgaWYgdGhlIHdpbmRvdyByZWZlcmVuY2UgaXMgYXZhaWxhYmxlXG5cdGlmICh0eXBlb2Ygd2luZG93ID09PSBcIm9iamVjdFwiKSBnID0gd2luZG93O1xufVxuXG4vLyBnIGNhbiBzdGlsbCBiZSB1bmRlZmluZWQsIGJ1dCBub3RoaW5nIHRvIGRvIGFib3V0IGl0Li4uXG4vLyBXZSByZXR1cm4gdW5kZWZpbmVkLCBpbnN0ZWFkIG9mIG5vdGhpbmcgaGVyZSwgc28gaXQnc1xuLy8gZWFzaWVyIHRvIGhhbmRsZSB0aGlzIGNhc2UuIGlmKCFnbG9iYWwpIHsgLi4ufVxuXG5tb2R1bGUuZXhwb3J0cyA9IGc7XG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./node_modules/webpack/buildin/global.js\n");

/***/ }),

/***/ "./src/common/constants.js":
/*!*********************************!*\
  !*** ./src/common/constants.js ***!
  \*********************************/
/*! exports provided: SELECTION_CHANGED, ANNOTATION_CHANGED, PLUGIN_NAMESPACE, EDITOR_STORE, EDITOR_ELEMENT_ID, WORDLIFT_STORE */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"SELECTION_CHANGED\", function() { return SELECTION_CHANGED; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"ANNOTATION_CHANGED\", function() { return ANNOTATION_CHANGED; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"PLUGIN_NAMESPACE\", function() { return PLUGIN_NAMESPACE; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"EDITOR_STORE\", function() { return EDITOR_STORE; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"EDITOR_ELEMENT_ID\", function() { return EDITOR_ELEMENT_ID; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"WORDLIFT_STORE\", function() { return WORDLIFT_STORE; });\n/**\n * This file defines constants used across different files and components.\n *\n * @author David Riccitelli <david@wordlift.io>\n * @since 3.23.0\n */\n\n/**\n * WordPress' action hook to signal that a selection has changed.\n *\n * @since 3.23.0\n * @type {string}\n */\nvar SELECTION_CHANGED = \"wordlift.selectionChanged\";\n/**\n * WordPress' action hook to signal that an annotation has changed. The action\n * provides the annotation id as `{ annotationId }`. The annotation id usually\n * matches the element id that caused the action to be fired.\n *\n * @since 3.23.0\n * @type {string}\n */\n\nvar ANNOTATION_CHANGED = \"wordlift.annotationChanged\";\n/**\n * The plugin namespace.\n *\n * @type {string}\n */\n\nvar PLUGIN_NAMESPACE = \"wordlift\";\n/**\n * Define the G'berg editor store name.\n *\n * @since 3.23.0\n * @type {string}\n */\n\nvar EDITOR_STORE = \"core/editor\";\n/**\n * Define the editor element id.\n *\n * @since 3.23.0\n * @type {string}\n */\n\nvar EDITOR_ELEMENT_ID = \"editor\";\n/**\n * Define the WordLift Store name used for {@link select} and {@link dispatch}\n * functions.\n *\n * @type {string}\n */\n\nvar WORDLIFT_STORE = \"wordlift/editor\";//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvY29tbW9uL2NvbnN0YW50cy5qcz85YTM2Il0sIm5hbWVzIjpbIlNFTEVDVElPTl9DSEFOR0VEIiwiQU5OT1RBVElPTl9DSEFOR0VEIiwiUExVR0lOX05BTUVTUEFDRSIsIkVESVRPUl9TVE9SRSIsIkVESVRPUl9FTEVNRU5UX0lEIiwiV09SRExJRlRfU1RPUkUiXSwibWFwcGluZ3MiOiJBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7Ozs7Ozs7QUFPQTs7Ozs7O0FBTU8sSUFBTUEsaUJBQWlCLEdBQUcsMkJBQTFCO0FBRVA7Ozs7Ozs7OztBQVFPLElBQU1DLGtCQUFrQixHQUFHLDRCQUEzQjtBQUVQOzs7Ozs7QUFLTyxJQUFNQyxnQkFBZ0IsR0FBRyxVQUF6QjtBQUVQOzs7Ozs7O0FBTU8sSUFBTUMsWUFBWSxHQUFHLGFBQXJCO0FBRVA7Ozs7Ozs7QUFNTyxJQUFNQyxpQkFBaUIsR0FBRyxRQUExQjtBQUVQOzs7Ozs7O0FBTU8sSUFBTUMsY0FBYyxHQUFHLGlCQUF2QiIsImZpbGUiOiIuL3NyYy9jb21tb24vY29uc3RhbnRzLmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBUaGlzIGZpbGUgZGVmaW5lcyBjb25zdGFudHMgdXNlZCBhY3Jvc3MgZGlmZmVyZW50IGZpbGVzIGFuZCBjb21wb25lbnRzLlxuICpcbiAqIEBhdXRob3IgRGF2aWQgUmljY2l0ZWxsaSA8ZGF2aWRAd29yZGxpZnQuaW8+XG4gKiBAc2luY2UgMy4yMy4wXG4gKi9cblxuLyoqXG4gKiBXb3JkUHJlc3MnIGFjdGlvbiBob29rIHRvIHNpZ25hbCB0aGF0IGEgc2VsZWN0aW9uIGhhcyBjaGFuZ2VkLlxuICpcbiAqIEBzaW5jZSAzLjIzLjBcbiAqIEB0eXBlIHtzdHJpbmd9XG4gKi9cbmV4cG9ydCBjb25zdCBTRUxFQ1RJT05fQ0hBTkdFRCA9IFwid29yZGxpZnQuc2VsZWN0aW9uQ2hhbmdlZFwiO1xuXG4vKipcbiAqIFdvcmRQcmVzcycgYWN0aW9uIGhvb2sgdG8gc2lnbmFsIHRoYXQgYW4gYW5ub3RhdGlvbiBoYXMgY2hhbmdlZC4gVGhlIGFjdGlvblxuICogcHJvdmlkZXMgdGhlIGFubm90YXRpb24gaWQgYXMgYHsgYW5ub3RhdGlvbklkIH1gLiBUaGUgYW5ub3RhdGlvbiBpZCB1c3VhbGx5XG4gKiBtYXRjaGVzIHRoZSBlbGVtZW50IGlkIHRoYXQgY2F1c2VkIHRoZSBhY3Rpb24gdG8gYmUgZmlyZWQuXG4gKlxuICogQHNpbmNlIDMuMjMuMFxuICogQHR5cGUge3N0cmluZ31cbiAqL1xuZXhwb3J0IGNvbnN0IEFOTk9UQVRJT05fQ0hBTkdFRCA9IFwid29yZGxpZnQuYW5ub3RhdGlvbkNoYW5nZWRcIjtcblxuLyoqXG4gKiBUaGUgcGx1Z2luIG5hbWVzcGFjZS5cbiAqXG4gKiBAdHlwZSB7c3RyaW5nfVxuICovXG5leHBvcnQgY29uc3QgUExVR0lOX05BTUVTUEFDRSA9IFwid29yZGxpZnRcIjtcblxuLyoqXG4gKiBEZWZpbmUgdGhlIEcnYmVyZyBlZGl0b3Igc3RvcmUgbmFtZS5cbiAqXG4gKiBAc2luY2UgMy4yMy4wXG4gKiBAdHlwZSB7c3RyaW5nfVxuICovXG5leHBvcnQgY29uc3QgRURJVE9SX1NUT1JFID0gXCJjb3JlL2VkaXRvclwiO1xuXG4vKipcbiAqIERlZmluZSB0aGUgZWRpdG9yIGVsZW1lbnQgaWQuXG4gKlxuICogQHNpbmNlIDMuMjMuMFxuICogQHR5cGUge3N0cmluZ31cbiAqL1xuZXhwb3J0IGNvbnN0IEVESVRPUl9FTEVNRU5UX0lEID0gXCJlZGl0b3JcIjtcblxuLyoqXG4gKiBEZWZpbmUgdGhlIFdvcmRMaWZ0IFN0b3JlIG5hbWUgdXNlZCBmb3Ige0BsaW5rIHNlbGVjdH0gYW5kIHtAbGluayBkaXNwYXRjaH1cbiAqIGZ1bmN0aW9ucy5cbiAqXG4gKiBAdHlwZSB7c3RyaW5nfVxuICovXG5leHBvcnQgY29uc3QgV09SRExJRlRfU1RPUkUgPSBcIndvcmRsaWZ0L2VkaXRvclwiO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./src/common/constants.js\n");

/***/ }),

/***/ "./src/common/helpers.js":
/*!*******************************!*\
  !*** ./src/common/helpers.js ***!
  \*******************************/
/*! exports provided: isAnnotationElement */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"isAnnotationElement\", function() { return isAnnotationElement; });\n/**\n * This file provides helper functions.\n *\n * @author David Riccitelli <david@wordlift.io>\n * @since 3.23.0\n */\n\n/**\n * Check whether the provided HTMLElement is an annotation.\n *\n * An {@link HTMLElement} is considered an annotation if it satisfies the following\n * requirements:\n *  - it has a `span` tagName.\n *  - it has an `id` attribute.\n *  - it has a `textannotation` class name.\n *\n * @since 3.23.0\n * @param {HTMLElement} el The {@link HTMLElement}.\n * @returns {boolean} True if it's annotation span otherwise false.\n */\nvar isAnnotationElement = function isAnnotationElement(el) {\n  return \"undefined\" !== typeof el && \"undefined\" !== typeof el.tagName && \"undefined\" !== typeof el.id && \"undefined\" !== typeof el.classList && \"SPAN\" === el.tagName && el.classList.contains(\"textannotation\");\n};//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvY29tbW9uL2hlbHBlcnMuanM/YTJjYyJdLCJuYW1lcyI6WyJpc0Fubm90YXRpb25FbGVtZW50IiwiZWwiLCJ0YWdOYW1lIiwiaWQiLCJjbGFzc0xpc3QiLCJjb250YWlucyJdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQTtBQUFBOzs7Ozs7O0FBT0E7Ozs7Ozs7Ozs7Ozs7QUFhTyxJQUFNQSxtQkFBbUIsR0FBRyxTQUF0QkEsbUJBQXNCLENBQUFDLEVBQUUsRUFBSTtBQUN2QyxTQUNFLGdCQUFnQixPQUFPQSxFQUF2QixJQUNBLGdCQUFnQixPQUFPQSxFQUFFLENBQUNDLE9BRDFCLElBRUEsZ0JBQWdCLE9BQU9ELEVBQUUsQ0FBQ0UsRUFGMUIsSUFHQSxnQkFBZ0IsT0FBT0YsRUFBRSxDQUFDRyxTQUgxQixJQUlBLFdBQVdILEVBQUUsQ0FBQ0MsT0FKZCxJQUtBRCxFQUFFLENBQUNHLFNBQUgsQ0FBYUMsUUFBYixDQUFzQixnQkFBdEIsQ0FORjtBQVFELENBVE0iLCJmaWxlIjoiLi9zcmMvY29tbW9uL2hlbHBlcnMuanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIFRoaXMgZmlsZSBwcm92aWRlcyBoZWxwZXIgZnVuY3Rpb25zLlxuICpcbiAqIEBhdXRob3IgRGF2aWQgUmljY2l0ZWxsaSA8ZGF2aWRAd29yZGxpZnQuaW8+XG4gKiBAc2luY2UgMy4yMy4wXG4gKi9cblxuLyoqXG4gKiBDaGVjayB3aGV0aGVyIHRoZSBwcm92aWRlZCBIVE1MRWxlbWVudCBpcyBhbiBhbm5vdGF0aW9uLlxuICpcbiAqIEFuIHtAbGluayBIVE1MRWxlbWVudH0gaXMgY29uc2lkZXJlZCBhbiBhbm5vdGF0aW9uIGlmIGl0IHNhdGlzZmllcyB0aGUgZm9sbG93aW5nXG4gKiByZXF1aXJlbWVudHM6XG4gKiAgLSBpdCBoYXMgYSBgc3BhbmAgdGFnTmFtZS5cbiAqICAtIGl0IGhhcyBhbiBgaWRgIGF0dHJpYnV0ZS5cbiAqICAtIGl0IGhhcyBhIGB0ZXh0YW5ub3RhdGlvbmAgY2xhc3MgbmFtZS5cbiAqXG4gKiBAc2luY2UgMy4yMy4wXG4gKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBlbCBUaGUge0BsaW5rIEhUTUxFbGVtZW50fS5cbiAqIEByZXR1cm5zIHtib29sZWFufSBUcnVlIGlmIGl0J3MgYW5ub3RhdGlvbiBzcGFuIG90aGVyd2lzZSBmYWxzZS5cbiAqL1xuZXhwb3J0IGNvbnN0IGlzQW5ub3RhdGlvbkVsZW1lbnQgPSBlbCA9PiB7XG4gIHJldHVybiAoXG4gICAgXCJ1bmRlZmluZWRcIiAhPT0gdHlwZW9mIGVsICYmXG4gICAgXCJ1bmRlZmluZWRcIiAhPT0gdHlwZW9mIGVsLnRhZ05hbWUgJiZcbiAgICBcInVuZGVmaW5lZFwiICE9PSB0eXBlb2YgZWwuaWQgJiZcbiAgICBcInVuZGVmaW5lZFwiICE9PSB0eXBlb2YgZWwuY2xhc3NMaXN0ICYmXG4gICAgXCJTUEFOXCIgPT09IGVsLnRhZ05hbWUgJiZcbiAgICBlbC5jbGFzc0xpc3QuY29udGFpbnMoXCJ0ZXh0YW5ub3RhdGlvblwiKVxuICApO1xufTtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/common/helpers.js\n");

/***/ }),

/***/ "./src/tiny-mce/index.js":
/*!*******************************!*\
  !*** ./src/tiny-mce/index.js ***!
  \*******************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var backbone__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! backbone */ \"backbone\");\n/* harmony import */ var backbone__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(backbone__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _common_constants__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../common/constants */ \"./src/common/constants.js\");\n/* harmony import */ var _common_helpers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../common/helpers */ \"./src/common/helpers.js\");\n/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./index.scss */ \"./src/tiny-mce/index.scss\");\n/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_index_scss__WEBPACK_IMPORTED_MODULE_3__);\n/**\n * This file provides a TinyMCE plugin for integration with WordLift.\n *\n * TinyMCE is loaded in different places within WordPress. We're specifically\n * targeting TinyMCE used as editor in Gutenberg's `classic` block.\n *\n * We're aiming to send an `action` every time the text selection changes. The\n * action should be caught by other components in page to update the UI (namely\n * the `Add ...` button in the classification box.\n *\n * The plugin name `wl_tinymce_2` is also defined in\n * src/includes/class-wordlift-tinymce-adapter.php and *must* match.\n *\n * @author David Riccitelli <david@wordlift.io>\n * @since 3.23.0\n */\n\n/**\n * External dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n\n\n\nvar tinymce = global[\"tinymce\"];\ntinymce.PluginManager.add(\"wl_tinymce_2\", function (ed) {\n  // Capture `NodeChange` events and broadcast the selected text.\n  ed.on(\"NodeChange\", function (e) {\n    Object(backbone__WEBPACK_IMPORTED_MODULE_0__[\"trigger\"])(_common_constants__WEBPACK_IMPORTED_MODULE_1__[\"SELECTION_CHANGED\"], {\n      selection: ed.selection.getContent({\n        format: \"text\"\n      })\n    }); // Fire the annotation change.\n\n    var payload = \"undefined\" !== typeof e && Object(_common_helpers__WEBPACK_IMPORTED_MODULE_2__[\"isAnnotationElement\"])(e.element) ? // Set the payload to `{ annotationId }` if it's an annotation otherwise to null.\n    e.element.id : undefined;\n    Object(backbone__WEBPACK_IMPORTED_MODULE_0__[\"trigger\"])(_common_constants__WEBPACK_IMPORTED_MODULE_1__[\"ANNOTATION_CHANGED\"], payload);\n  });\n});\n/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../node_modules/webpack/buildin/global.js */ \"./node_modules/webpack/buildin/global.js\")))//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvdGlueS1tY2UvaW5kZXguanM/MDcyOSJdLCJuYW1lcyI6WyJ0aW55bWNlIiwiZ2xvYmFsIiwiUGx1Z2luTWFuYWdlciIsImFkZCIsImVkIiwib24iLCJlIiwidHJpZ2dlciIsIlNFTEVDVElPTl9DSEFOR0VEIiwic2VsZWN0aW9uIiwiZ2V0Q29udGVudCIsImZvcm1hdCIsInBheWxvYWQiLCJpc0Fubm90YXRpb25FbGVtZW50IiwiZWxlbWVudCIsImlkIiwidW5kZWZpbmVkIiwiQU5OT1RBVElPTl9DSEFOR0VEIl0sIm1hcHBpbmdzIjoiQUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBOzs7Ozs7Ozs7Ozs7Ozs7OztBQWlCQTs7O0FBR0E7QUFFQTs7OztBQUdBO0FBQ0E7QUFFQTtBQUVBLElBQU1BLE9BQU8sR0FBR0MsTUFBTSxDQUFDLFNBQUQsQ0FBdEI7QUFDQUQsT0FBTyxDQUFDRSxhQUFSLENBQXNCQyxHQUF0QixDQUEwQixjQUExQixFQUEwQyxVQUFTQyxFQUFULEVBQWE7QUFDckQ7QUFDQUEsSUFBRSxDQUFDQyxFQUFILENBQU0sWUFBTixFQUFvQixVQUFBQyxDQUFDLEVBQUk7QUFDdkJDLDREQUFPLENBQUNDLG1FQUFELEVBQW9CO0FBQUVDLGVBQVMsRUFBRUwsRUFBRSxDQUFDSyxTQUFILENBQWFDLFVBQWIsQ0FBd0I7QUFBRUMsY0FBTSxFQUFFO0FBQVYsT0FBeEI7QUFBYixLQUFwQixDQUFQLENBRHVCLENBR3ZCOztBQUNBLFFBQU1DLE9BQU8sR0FDWCxnQkFBZ0IsT0FBT04sQ0FBdkIsSUFBNEJPLDJFQUFtQixDQUFDUCxDQUFDLENBQUNRLE9BQUgsQ0FBL0MsR0FDSTtBQUNBUixLQUFDLENBQUNRLE9BQUYsQ0FBVUMsRUFGZCxHQUdJQyxTQUpOO0FBS0FULDREQUFPLENBQUNVLG9FQUFELEVBQXFCTCxPQUFyQixDQUFQO0FBQ0QsR0FWRDtBQVdELENBYkQsRSIsImZpbGUiOiIuL3NyYy90aW55LW1jZS9pbmRleC5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogVGhpcyBmaWxlIHByb3ZpZGVzIGEgVGlueU1DRSBwbHVnaW4gZm9yIGludGVncmF0aW9uIHdpdGggV29yZExpZnQuXG4gKlxuICogVGlueU1DRSBpcyBsb2FkZWQgaW4gZGlmZmVyZW50IHBsYWNlcyB3aXRoaW4gV29yZFByZXNzLiBXZSdyZSBzcGVjaWZpY2FsbHlcbiAqIHRhcmdldGluZyBUaW55TUNFIHVzZWQgYXMgZWRpdG9yIGluIEd1dGVuYmVyZydzIGBjbGFzc2ljYCBibG9jay5cbiAqXG4gKiBXZSdyZSBhaW1pbmcgdG8gc2VuZCBhbiBgYWN0aW9uYCBldmVyeSB0aW1lIHRoZSB0ZXh0IHNlbGVjdGlvbiBjaGFuZ2VzLiBUaGVcbiAqIGFjdGlvbiBzaG91bGQgYmUgY2F1Z2h0IGJ5IG90aGVyIGNvbXBvbmVudHMgaW4gcGFnZSB0byB1cGRhdGUgdGhlIFVJIChuYW1lbHlcbiAqIHRoZSBgQWRkIC4uLmAgYnV0dG9uIGluIHRoZSBjbGFzc2lmaWNhdGlvbiBib3guXG4gKlxuICogVGhlIHBsdWdpbiBuYW1lIGB3bF90aW55bWNlXzJgIGlzIGFsc28gZGVmaW5lZCBpblxuICogc3JjL2luY2x1ZGVzL2NsYXNzLXdvcmRsaWZ0LXRpbnltY2UtYWRhcHRlci5waHAgYW5kICptdXN0KiBtYXRjaC5cbiAqXG4gKiBAYXV0aG9yIERhdmlkIFJpY2NpdGVsbGkgPGRhdmlkQHdvcmRsaWZ0LmlvPlxuICogQHNpbmNlIDMuMjMuMFxuICovXG5cbi8qKlxuICogRXh0ZXJuYWwgZGVwZW5kZW5jaWVzXG4gKi9cbmltcG9ydCB7IHRyaWdnZXIgfSBmcm9tIFwiYmFja2JvbmVcIjtcblxuLyoqXG4gKiBJbnRlcm5hbCBkZXBlbmRlbmNpZXNcbiAqL1xuaW1wb3J0IHsgQU5OT1RBVElPTl9DSEFOR0VELCBTRUxFQ1RJT05fQ0hBTkdFRCB9IGZyb20gXCIuLi9jb21tb24vY29uc3RhbnRzXCI7XG5pbXBvcnQgeyBpc0Fubm90YXRpb25FbGVtZW50IH0gZnJvbSBcIi4uL2NvbW1vbi9oZWxwZXJzXCI7XG5cbmltcG9ydCBcIi4vaW5kZXguc2Nzc1wiO1xuXG5jb25zdCB0aW55bWNlID0gZ2xvYmFsW1widGlueW1jZVwiXTtcbnRpbnltY2UuUGx1Z2luTWFuYWdlci5hZGQoXCJ3bF90aW55bWNlXzJcIiwgZnVuY3Rpb24oZWQpIHtcbiAgLy8gQ2FwdHVyZSBgTm9kZUNoYW5nZWAgZXZlbnRzIGFuZCBicm9hZGNhc3QgdGhlIHNlbGVjdGVkIHRleHQuXG4gIGVkLm9uKFwiTm9kZUNoYW5nZVwiLCBlID0+IHtcbiAgICB0cmlnZ2VyKFNFTEVDVElPTl9DSEFOR0VELCB7IHNlbGVjdGlvbjogZWQuc2VsZWN0aW9uLmdldENvbnRlbnQoeyBmb3JtYXQ6IFwidGV4dFwiIH0pIH0pO1xuXG4gICAgLy8gRmlyZSB0aGUgYW5ub3RhdGlvbiBjaGFuZ2UuXG4gICAgY29uc3QgcGF5bG9hZCA9XG4gICAgICBcInVuZGVmaW5lZFwiICE9PSB0eXBlb2YgZSAmJiBpc0Fubm90YXRpb25FbGVtZW50KGUuZWxlbWVudClcbiAgICAgICAgPyAvLyBTZXQgdGhlIHBheWxvYWQgdG8gYHsgYW5ub3RhdGlvbklkIH1gIGlmIGl0J3MgYW4gYW5ub3RhdGlvbiBvdGhlcndpc2UgdG8gbnVsbC5cbiAgICAgICAgICBlLmVsZW1lbnQuaWRcbiAgICAgICAgOiB1bmRlZmluZWQ7XG4gICAgdHJpZ2dlcihBTk5PVEFUSU9OX0NIQU5HRUQsIHBheWxvYWQpO1xuICB9KTtcbn0pO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./src/tiny-mce/index.js\n");

/***/ }),

/***/ "./src/tiny-mce/index.scss":
/*!*********************************!*\
  !*** ./src/tiny-mce/index.scss ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvdGlueS1tY2UvaW5kZXguc2Nzcz9hMzQzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBIiwiZmlsZSI6Ii4vc3JjL3RpbnktbWNlL2luZGV4LnNjc3MuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvLyBleHRyYWN0ZWQgYnkgbWluaS1jc3MtZXh0cmFjdC1wbHVnaW4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./src/tiny-mce/index.scss\n");

/***/ }),

/***/ "backbone":
/*!***************************!*\
  !*** external "Backbone" ***!
  \***************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = Backbone;//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vZXh0ZXJuYWwgXCJCYWNrYm9uZVwiPzViYzAiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUEiLCJmaWxlIjoiYmFja2JvbmUuanMiLCJzb3VyY2VzQ29udGVudCI6WyJtb2R1bGUuZXhwb3J0cyA9IEJhY2tib25lOyJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///backbone\n");

/***/ })

/******/ });