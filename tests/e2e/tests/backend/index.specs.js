/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.l = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };

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

/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};

/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var Admin = function Admin() {
	return describe('while in WordPress backend, admin', function () {

		// `paneX` represents the expected horizontal offset of the current pane.
		// It is set the first time, when the _wl-setup_ page is opened.
		var paneX = void 0;

		/**
   * Click on the next button in the pane at `index` (1-based).
   *
   * @since 3.9.0
   *
   * @param {Number} index The pane index (1-based).
   */
		var clickNextAndWaitForPane = function clickNextAndWaitForPane(index) {

			// Click on the next button.
			browser.click('.viewport > ul > li:nth-child(' + index + ') [data-wl-next]');

			// Wait until the next pane is visible.
			browser.waitUntil(function () {
				// console.log(browser.getLocation('.viewport > ul >
				// li:nth-child()', 'x'));
				return paneX === browser.getLocation('.viewport > ul > li:nth-child(' + (index + 1) + ')', 'x');
			}, 750, 'expected pane to be visible within 750ms');
		};

		it('opens the plugins page and activates WordLift', function () {

			// Navigate to the plugins page.
			browser.url('/wp-admin/plugins.php');

			// Check the URL.
			// expect(browser.getUrl()).toMatch(/\/wp-admin\/plugins\.php$/);

			// Get WordLift's row in the plugins' list.
			browser.waitForExist('[data-slug="wordlift"]');

			var wordlift = browser.element('[data-slug="wordlift"]');

			// Check that WordLift's row is there.
			// expect(wordlift).not.toBeUndefined();

			// Activate WordLift.
			wordlift.click('.activate a');

			// We got redirected to the `wl-setup` page.
			// expect(browser.getUrl()).toMatch(/\/wp-admin\/index\.php\?page=wl-setup$/);

			// Wait until the element becomes invalid.
			browser.waitForExist('.viewport > ul > li:first-child');

			// Set the x offset for the current visible pane.
			paneX = browser.getLocation('.viewport > ul > li:first-child', 'x');
		});

		it('continues to License Key', function () {

			// Click next and wait for the 2nd pane.
			clickNextAndWaitForPane(1);

			// Set an invalid key.
			browser.setValue('input#key', 'an-invalid-key');

			// Wait until the element becomes invalid.
			browser.waitForExist('input#key.invalid');

			// Set a valid key.
			browser.setValue('input#key', __webpack_require__.i({"NODE_ENV":"production"}).WORDLIFT_KEY);

			// Wait until the element becomes valid.
			browser.waitForExist('input#key.valid');
		});

		it('continues to Vocabulary', function () {

			// Click next and wait for the 3rd pane.
			clickNextAndWaitForPane(2);

			// browser.click('input#vocabulary');
			//
			// // Set an invalid vocabulary path.
			// browser.keys(['Backspace', '_']);
			//
			// browser.saveScreenshot();
			//
			// // Wait until the element becomes invalid.
			// browser.waitForExist('input#vocabulary.invalid');
			//
			// // Set a valid vocabulary.
			// browser.keys('Backspace');

			// Wait until the element becomes valid.
			browser.waitForExist('input#vocabulary.valid');
		});

		it('continues to Language', function () {

			// Click next and wait for the 4th pane.
			clickNextAndWaitForPane(3);
		});

		it('continues to Publisher', function () {

			// Click next and wait for the 5th pane.
			clickNextAndWaitForPane(4);

			// browser.waitForExist('input#company');
			//
			// // Click on the company radio.
			// browser.click('input#company');

			browser.waitForExist('input#name');

			// Set the company name.
			browser.setValue('input#name', 'Acme Inc.');

			// Click on finish.
			browser.click('input#btn-finish');

			// Check that we got back to the admin area.
			// expect(browser.getUrl()).toMatch(/\/wp-admin\/$/);
		});
	});
};

/* harmony default export */ __webpack_exports__["a"] = Admin;

/***/ }),
/* 1 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__admin__ = __webpack_require__(0);


'use strict';

describe('Open the WordPress web site', function () {

	it('admin logs in', function () {

		browser.url('/wp-login.php');

		browser.waitForVisible('#wp-submit');

		browser.setValue('#user_login', 'admin');
		browser.setValue('#user_pass', 'admin');
		browser.click('#wp-submit');

		browser.waitForExist('body.wp-admin');
	});

	__webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__admin__["a" /* default */])();
});

/***/ })
/******/ ]);