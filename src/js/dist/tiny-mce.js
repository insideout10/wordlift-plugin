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
/*! exports provided: SELECTION_CHANGED, ANNOTATION_CHANGED */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"SELECTION_CHANGED\", function() { return SELECTION_CHANGED; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"ANNOTATION_CHANGED\", function() { return ANNOTATION_CHANGED; });\n/**\n * This file defines constants used across different files and components.\n *\n * @author David Riccitelli <david@wordlift.io>\n * @since 3.23.0\n */\n\n/**\n * WordPress' action hook to signal that a selection has changed.\n *\n * @since 3.23.0\n * @type {string}\n */\nvar SELECTION_CHANGED = \"wordlift.selectionChanged\";\n/**\n * WordPress' action hook to signal that an annotation has changed. The action\n * provides the annotation id as `{ annotationId }`. The annotation id usually\n * matches the element id that caused the action to be fired.\n *\n * @since 3.23.0\n * @type {string}\n */\n\nvar ANNOTATION_CHANGED = \"wordlift.annotationChanged\";//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvY29tbW9uL2NvbnN0YW50cy5qcz85YTM2Il0sIm5hbWVzIjpbIlNFTEVDVElPTl9DSEFOR0VEIiwiQU5OT1RBVElPTl9DSEFOR0VEIl0sIm1hcHBpbmdzIjoiQUFBQTtBQUFBO0FBQUE7QUFBQTs7Ozs7OztBQU9BOzs7Ozs7QUFNTyxJQUFNQSxpQkFBaUIsR0FBRywyQkFBMUI7QUFFUDs7Ozs7Ozs7O0FBUU8sSUFBTUMsa0JBQWtCLEdBQUcsNEJBQTNCIiwiZmlsZSI6Ii4vc3JjL2NvbW1vbi9jb25zdGFudHMuanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIFRoaXMgZmlsZSBkZWZpbmVzIGNvbnN0YW50cyB1c2VkIGFjcm9zcyBkaWZmZXJlbnQgZmlsZXMgYW5kIGNvbXBvbmVudHMuXG4gKlxuICogQGF1dGhvciBEYXZpZCBSaWNjaXRlbGxpIDxkYXZpZEB3b3JkbGlmdC5pbz5cbiAqIEBzaW5jZSAzLjIzLjBcbiAqL1xuXG4vKipcbiAqIFdvcmRQcmVzcycgYWN0aW9uIGhvb2sgdG8gc2lnbmFsIHRoYXQgYSBzZWxlY3Rpb24gaGFzIGNoYW5nZWQuXG4gKlxuICogQHNpbmNlIDMuMjMuMFxuICogQHR5cGUge3N0cmluZ31cbiAqL1xuZXhwb3J0IGNvbnN0IFNFTEVDVElPTl9DSEFOR0VEID0gXCJ3b3JkbGlmdC5zZWxlY3Rpb25DaGFuZ2VkXCI7XG5cbi8qKlxuICogV29yZFByZXNzJyBhY3Rpb24gaG9vayB0byBzaWduYWwgdGhhdCBhbiBhbm5vdGF0aW9uIGhhcyBjaGFuZ2VkLiBUaGUgYWN0aW9uXG4gKiBwcm92aWRlcyB0aGUgYW5ub3RhdGlvbiBpZCBhcyBgeyBhbm5vdGF0aW9uSWQgfWAuIFRoZSBhbm5vdGF0aW9uIGlkIHVzdWFsbHlcbiAqIG1hdGNoZXMgdGhlIGVsZW1lbnQgaWQgdGhhdCBjYXVzZWQgdGhlIGFjdGlvbiB0byBiZSBmaXJlZC5cbiAqXG4gKiBAc2luY2UgMy4yMy4wXG4gKiBAdHlwZSB7c3RyaW5nfVxuICovXG5leHBvcnQgY29uc3QgQU5OT1RBVElPTl9DSEFOR0VEID0gXCJ3b3JkbGlmdC5hbm5vdGF0aW9uQ2hhbmdlZFwiO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./src/common/constants.js\n");

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
eval("__webpack_require__.r(__webpack_exports__);\n/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ \"@wordpress/hooks\");\n/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _common_constants__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../common/constants */ \"./src/common/constants.js\");\n/* harmony import */ var _common_helpers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../common/helpers */ \"./src/common/helpers.js\");\n/**\n * This file provides a TinyMCE plugin for integration with WordLift.\n *\n * TinyMCE is loaded in different places within WordPress. We're specifically\n * targeting TinyMCE used as editor in Gutenberg's `classic` block.\n *\n * We're aiming to send an `action` every time the text selection changes. The\n * action should be caught by other components in page to update the UI (namely\n * the `Add ...` button in the classification box.\n *\n * The plugin name `wl_tinymce_2` is also defined in\n * src/includes/class-wordlift-tinymce-adapter.php and *must* match.\n *\n * @author David Riccitelli <david@wordlift.io>\n * @since 3.23.0\n */\n\n/**\n * WordPress dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n\n\nvar tinymce = global[\"tinymce\"];\ntinymce.PluginManager.add(\"wl_tinymce_2\", function (ed) {\n  // Capture `NodeChange` events and broadcast the selected text.\n  ed.on(\"NodeChange\", function (e) {\n    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__[\"doAction\"])(_common_constants__WEBPACK_IMPORTED_MODULE_1__[\"SELECTION_CHANGED\"], {\n      selection: ed.selection.getContent({\n        format: \"text\"\n      })\n    }); // Fire the annotation change.\n\n    var payload = \"undefined\" !== typeof e && Object(_common_helpers__WEBPACK_IMPORTED_MODULE_2__[\"isAnnotationElement\"])(e.element) ? // Set the payload to `{ annotationId }` if it's an annotation otherwise to null.\n    e.element.id : undefined;\n    Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__[\"doAction\"])(_common_constants__WEBPACK_IMPORTED_MODULE_1__[\"ANNOTATION_CHANGED\"], payload);\n  });\n});\n/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../node_modules/webpack/buildin/global.js */ \"./node_modules/webpack/buildin/global.js\")))//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvdGlueS1tY2UvaW5kZXguanM/MDcyOSJdLCJuYW1lcyI6WyJ0aW55bWNlIiwiZ2xvYmFsIiwiUGx1Z2luTWFuYWdlciIsImFkZCIsImVkIiwib24iLCJlIiwiZG9BY3Rpb24iLCJTRUxFQ1RJT05fQ0hBTkdFRCIsInNlbGVjdGlvbiIsImdldENvbnRlbnQiLCJmb3JtYXQiLCJwYXlsb2FkIiwiaXNBbm5vdGF0aW9uRWxlbWVudCIsImVsZW1lbnQiLCJpZCIsInVuZGVmaW5lZCIsIkFOTk9UQVRJT05fQ0hBTkdFRCJdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBOzs7Ozs7Ozs7Ozs7Ozs7OztBQWlCQTs7O0FBR0E7QUFFQTs7OztBQUdBO0FBQ0E7QUFFQSxJQUFNQSxPQUFPLEdBQUdDLE1BQU0sQ0FBQyxTQUFELENBQXRCO0FBQ0FELE9BQU8sQ0FBQ0UsYUFBUixDQUFzQkMsR0FBdEIsQ0FBMEIsY0FBMUIsRUFBMEMsVUFBU0MsRUFBVCxFQUFhO0FBQ3JEO0FBQ0FBLElBQUUsQ0FBQ0MsRUFBSCxDQUFNLFlBQU4sRUFBb0IsVUFBQUMsQ0FBQyxFQUFJO0FBQ3ZCQyxxRUFBUSxDQUFDQyxtRUFBRCxFQUFvQjtBQUFFQyxlQUFTLEVBQUVMLEVBQUUsQ0FBQ0ssU0FBSCxDQUFhQyxVQUFiLENBQXdCO0FBQUVDLGNBQU0sRUFBRTtBQUFWLE9BQXhCO0FBQWIsS0FBcEIsQ0FBUixDQUR1QixDQUd2Qjs7QUFDQSxRQUFNQyxPQUFPLEdBQ1gsZ0JBQWdCLE9BQU9OLENBQXZCLElBQTRCTywyRUFBbUIsQ0FBQ1AsQ0FBQyxDQUFDUSxPQUFILENBQS9DLEdBQ0k7QUFDQVIsS0FBQyxDQUFDUSxPQUFGLENBQVVDLEVBRmQsR0FHSUMsU0FKTjtBQUtBVCxxRUFBUSxDQUFDVSxvRUFBRCxFQUFxQkwsT0FBckIsQ0FBUjtBQUNELEdBVkQ7QUFXRCxDQWJELEUiLCJmaWxlIjoiLi9zcmMvdGlueS1tY2UvaW5kZXguanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIFRoaXMgZmlsZSBwcm92aWRlcyBhIFRpbnlNQ0UgcGx1Z2luIGZvciBpbnRlZ3JhdGlvbiB3aXRoIFdvcmRMaWZ0LlxuICpcbiAqIFRpbnlNQ0UgaXMgbG9hZGVkIGluIGRpZmZlcmVudCBwbGFjZXMgd2l0aGluIFdvcmRQcmVzcy4gV2UncmUgc3BlY2lmaWNhbGx5XG4gKiB0YXJnZXRpbmcgVGlueU1DRSB1c2VkIGFzIGVkaXRvciBpbiBHdXRlbmJlcmcncyBgY2xhc3NpY2AgYmxvY2suXG4gKlxuICogV2UncmUgYWltaW5nIHRvIHNlbmQgYW4gYGFjdGlvbmAgZXZlcnkgdGltZSB0aGUgdGV4dCBzZWxlY3Rpb24gY2hhbmdlcy4gVGhlXG4gKiBhY3Rpb24gc2hvdWxkIGJlIGNhdWdodCBieSBvdGhlciBjb21wb25lbnRzIGluIHBhZ2UgdG8gdXBkYXRlIHRoZSBVSSAobmFtZWx5XG4gKiB0aGUgYEFkZCAuLi5gIGJ1dHRvbiBpbiB0aGUgY2xhc3NpZmljYXRpb24gYm94LlxuICpcbiAqIFRoZSBwbHVnaW4gbmFtZSBgd2xfdGlueW1jZV8yYCBpcyBhbHNvIGRlZmluZWQgaW5cbiAqIHNyYy9pbmNsdWRlcy9jbGFzcy13b3JkbGlmdC10aW55bWNlLWFkYXB0ZXIucGhwIGFuZCAqbXVzdCogbWF0Y2guXG4gKlxuICogQGF1dGhvciBEYXZpZCBSaWNjaXRlbGxpIDxkYXZpZEB3b3JkbGlmdC5pbz5cbiAqIEBzaW5jZSAzLjIzLjBcbiAqL1xuXG4vKipcbiAqIFdvcmRQcmVzcyBkZXBlbmRlbmNpZXNcbiAqL1xuaW1wb3J0IHsgZG9BY3Rpb24gfSBmcm9tIFwiQHdvcmRwcmVzcy9ob29rc1wiO1xuXG4vKipcbiAqIEludGVybmFsIGRlcGVuZGVuY2llc1xuICovXG5pbXBvcnQgeyBBTk5PVEFUSU9OX0NIQU5HRUQsIFNFTEVDVElPTl9DSEFOR0VEIH0gZnJvbSBcIi4uL2NvbW1vbi9jb25zdGFudHNcIjtcbmltcG9ydCB7IGlzQW5ub3RhdGlvbkVsZW1lbnQgfSBmcm9tIFwiLi4vY29tbW9uL2hlbHBlcnNcIjtcblxuY29uc3QgdGlueW1jZSA9IGdsb2JhbFtcInRpbnltY2VcIl07XG50aW55bWNlLlBsdWdpbk1hbmFnZXIuYWRkKFwid2xfdGlueW1jZV8yXCIsIGZ1bmN0aW9uKGVkKSB7XG4gIC8vIENhcHR1cmUgYE5vZGVDaGFuZ2VgIGV2ZW50cyBhbmQgYnJvYWRjYXN0IHRoZSBzZWxlY3RlZCB0ZXh0LlxuICBlZC5vbihcIk5vZGVDaGFuZ2VcIiwgZSA9PiB7XG4gICAgZG9BY3Rpb24oU0VMRUNUSU9OX0NIQU5HRUQsIHsgc2VsZWN0aW9uOiBlZC5zZWxlY3Rpb24uZ2V0Q29udGVudCh7IGZvcm1hdDogXCJ0ZXh0XCIgfSkgfSk7XG5cbiAgICAvLyBGaXJlIHRoZSBhbm5vdGF0aW9uIGNoYW5nZS5cbiAgICBjb25zdCBwYXlsb2FkID1cbiAgICAgIFwidW5kZWZpbmVkXCIgIT09IHR5cGVvZiBlICYmIGlzQW5ub3RhdGlvbkVsZW1lbnQoZS5lbGVtZW50KVxuICAgICAgICA/IC8vIFNldCB0aGUgcGF5bG9hZCB0byBgeyBhbm5vdGF0aW9uSWQgfWAgaWYgaXQncyBhbiBhbm5vdGF0aW9uIG90aGVyd2lzZSB0byBudWxsLlxuICAgICAgICAgIGUuZWxlbWVudC5pZFxuICAgICAgICA6IHVuZGVmaW5lZDtcbiAgICBkb0FjdGlvbihBTk5PVEFUSU9OX0NIQU5HRUQsIHBheWxvYWQpO1xuICB9KTtcbn0pO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./src/tiny-mce/index.js\n");

/***/ }),

/***/ "@wordpress/hooks":
/*!****************************************!*\
  !*** external {"this":["wp","hooks"]} ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"hooks\"]; }());//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vZXh0ZXJuYWwge1widGhpc1wiOltcIndwXCIsXCJob29rc1wiXX0/NGIxMiJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQSxhQUFhLHNDQUFzQyxFQUFFIiwiZmlsZSI6IkB3b3JkcHJlc3MvaG9va3MuanMiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24oKSB7IG1vZHVsZS5leHBvcnRzID0gdGhpc1tcIndwXCJdW1wiaG9va3NcIl07IH0oKSk7Il0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///@wordpress/hooks\n");

/***/ })

/******/ });