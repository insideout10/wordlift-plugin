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
/******/ 	return __webpack_require__(__webpack_require__.s = 4);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * Tests: Login to WordPress.
 *
 * Provides a function to login to WordPress.
 *
 * @since 3.11.0
 */

/**
 * Define the `LoginToWordPress` function.
 *
 * @since 3.11.0
 * @constructor
 */
var LoginToWordPress = function LoginToWordPress() {
  // Open the login page.
  browser.url('/wp-login.php');
  browser.pause(5000);

  // Wait for the login button.
  browser.waitForExist('#wp-submit');

  // Type username and password, then submit.
  browser.setValue('#user_login', 'admin');
  browser.setValue('#user_pass', 'admin');
  browser.click('#wp-submit');

  // Wait for the admin screen to load.
  browser.pause(5000);
};

// Finally export the `LoginToWordPress` function.
/* harmony default export */ __webpack_exports__["a"] = (LoginToWordPress);

/***/ }),
/* 1 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * Tests: Post Edit Page.
 *
 * Test the analysis.
 *
 * @since 3.11.0
 */

/**
 * Define the `PostEditPage` test.
 *
 * @since 3.11.0
 * @constructor
 */
var PostEditPage = function PostEditPage() {
	it('opens a post edit page and waits for the analysis results', function () {
		// @todo: enable creating a post when Safari and FF will support it.
		//	browser.waitForExist( '#menu-posts > a[href="edit.php"]' );
		//
		//	browser.click( '#menu-posts > a[href="edit.php"]' );
		//
		//	browser.waitForExist( 'a.page-title-action' );
		//
		//	browser.click( 'a.page-title-action' );
		//
		//	browser.waitForExist( '#content_ifr' );
		//
		//	browser.pause(5000);
		//
		//	browser.frame( browser.element( '#content_ifr' ).value );
		//
		//	browser.waitForExist( '#tinymce' );
		//
		//	browser.click( '#tinymce' );
		//
		//	browser.keys( 'WordLift brings the power of Artificial
		// Intelligence to help you produce richer content and organize it
		// around your audience.' );  // Set the company name. //
		// browser.setValue( '#tinymce p', 'WordLift brings the power of //
		// Artificial Intelligence to help you produce richer content and //
		// organize it around your audience.' ); browser.frameParent();
		// browser.element( '#publish' ).scroll(); browser.click( '#publish' );

		// Open a post page.
		browser.url('/wp-admin/post.php?post=3&action=edit');
		browser.pause(2500);

		// Wait for the analysis results to load.
		browser.waitForExist('#wl-entity-list ul li');

		// Click on the first analysis result.
		browser.click('#wl-entity-list ul li:nth-child(1) > div:nth-child(1)');
		browser.pause(1000);

		// Open the drawer.
		browser.click('#wl-entity-list ul li:nth-child(1) > div:nth-child(3)');
		browser.pause(1000);

		// Disable link.
		browser.click('#wl-entity-list ul li:nth-child(1) > div:nth-child(2) > div:nth-child(1)');
		browser.pause(1000);

		// Re-enable link.
		browser.click('#wl-entity-list ul li:nth-child(1) > div:nth-child(2) > div:nth-child(1)');
		browser.pause(1000);

		// Click on the second analysis result.
		browser.click('#wl-entity-list ul li:nth-child(2) > div:nth-child(1)');
		browser.pause(1000);

		// @todo: Safari doesn't correctly handle the click / focus event.
		// Expect the drawer of the 1st tile to be invisible.
		//		expect( browser.isVisible( '#wl-entity-list ul
		// li:nth-child(1)' + '> div:nth-child(2)' ) ) .toBe( false );

		// Expect the drawer of the 2nd tile to be visible.
		expect(browser.isVisible('#wl-entity-list ul li:nth-child(2) > div:nth-child(2)')).toBe(false);
	});
};

// Finally export the `PostEditPage`.
/* harmony default export */ __webpack_exports__["a"] = (PostEditPage);

/***/ }),
/* 2 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * Tests: WordLift Settings Page.
 *
 * @since 3.11.0
 */

/**
 * Define the `SettingsPage` tests.
 *
 * @since 3.11.0
 */
var SettingsPage = function SettingsPage() {
	// Opens the settings page and check for the initial settings.
	it('opens the settings page', function () {
		// The link to the settings page.
		var settingsPageLink = '[href="admin.php?page=wl_configuration_admin_menu"]';

		// Wait for the link to exists, then click.
		browser.waitForExist(settingsPageLink);
		browser.click(settingsPageLink);

		// Wait for the `wl-key` element to exist and to have a `valid` css
		// class indicating that the key is valid.
		browser.waitForExist('#wl-key.valid');

		// Expect the entity base path with `vocabulary` as path and to be
		// readonly.
		expect(browser.getValue('#wl-entity-base-path')).toBe('vocabulary');
		expect(browser.getAttribute('#wl-entity-base-path', 'readonly')).toBe('true');

		// Expect English to be selected as language.
		expect(browser.getValue('#wl-site-language option[selected="selected"]')).toBe('en');

		// Check that a publisher is set.
		expect(browser.getValue('#wl-publisher-id')).not.toBe('');
	});

	// Try changing the license key and see how the input reacts.
	it('change the license key', function () {
		// Get the existing license key.
		var licenseKey = browser.getValue('#wl-key');

		// Set an invalid key.
		browser.setValue('#wl-key', 'xyz');

		// Wait for the `wl-key` to turn invalid.
		browser.waitForExist('#wl-key.invalid');

		// Set the valid key again.
		browser.setValue('#wl-key', licenseKey);

		// Wait for the `wl-key` to turn valid.
		browser.waitForExist('#wl-key.valid');

		// @todo: replace with sendKeys when FF will support it.
		//		// Set an empty key.
		//		browser.setValue( '#wl-key', '' );
		//
		//		// Wait for the `wl-key` to turn invalid.
		//		browser.waitForExist( '#wl-key.invalid' );
		//		// Set the valid key again.
		//		browser.setValue( '#wl-key', licenseKey );
		//
		//		// Wait for the `wl-key` to turn valid.
		//		browser.waitForExist( '#wl-key.valid' );
	});

	// Test changing the settings and create a publisher.
	it('change the settings', function () {
		// Click on the 'Create a New Publisher' tab.
		browser.click('[href="#tabs-2"]');

		// Wait for the `wp_publisher[name]` field to be visible.
		browser.waitForVisible('[name="wl_publisher\[name\]"]');

		// Click on the `Add Logo` button.
		browser.click('#wl-publisher-media-uploader');

		// Check that the `.media-modal` is visible.
		browser.waitForVisible('.media-modal');

		// @todo: add image upload and selection.

		// Then close it.
		browser.click('.media-modal-close');

		// Set the name.
		browser.setValue('[name="wl_publisher\[name\]"]', 'John Smith');

		// Submit the form.
		browser.scroll('#submit');
		browser.click('#submit');
		browser.pause(5000);

		// Wait for the `wl-key` element to exist and to have a `valid` css
		// class indicating that the key is valid.
		browser.waitForExist('#wl-key.valid');

		// Check that the publisher is set.
		// @todo: FF has issues in getting the value for the selected option.
		expect(browser.getAttribute('#select2-wl-publisher-id-container', 'title')).toBe('John Smith');

		// @todo: also test changing the language.
	});
};

// Finally export the `SettingsPage`.
/* harmony default export */ __webpack_exports__["a"] = (SettingsPage);

/***/ }),
/* 3 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * Tests: Setup Page.
 *
 * @since 3.11.0
 */

/**
 * Define the `SetupPage` test.
 *
 * @since 3.11.0
 * @constructor
 */
var SetupPage = function SetupPage() {
	// `paneX` represents the expected horizontal offset of the current
	// pane. It is set the first time, when the _wl-setup_ page is opened.
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

		// Activate WordLift.
		browser.click('[data-slug="wordlift"] .activate a');
		browser.pause(2500);

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
		browser.setValue('#key', 'an-invalid-key');

		// Wait until the element becomes invalid.
		browser.waitForExist('#key.invalid');

		// Set a valid key.
		browser.setValue('#key', process.env.WORDLIFT_KEY);

		// Wait until the element becomes valid.
		browser.waitForExist('#key.valid');
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

		browser.waitForExist('input#name');

		// Set the company name.
		browser.setValue('input#name', 'Acme Inc.');
		browser.pause(2500);

		// Click on finish.
		browser.waitForExist('#btn-finish');
		browser.click('#btn-finish');
		browser.pause(5000);

		// Check that we got back to the admin area.
		browser.waitForExist('.wp-admin');
	});
};

// Finally export the `SetupPage`.
/* harmony default export */ __webpack_exports__["a"] = (SetupPage);

/***/ }),
/* 4 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__LoginToWordPress__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__SetupPage__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__SettingsPage__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__PostEditPage__ = __webpack_require__(1);
/**
 * Internal dependencies
 */





// Define the overall tests.
describe('test WordLift', function () {
	// Test logging into WordPress.
	it('log into WordPress backend', __WEBPACK_IMPORTED_MODULE_0__LoginToWordPress__["a" /* default */]);

	// Test the Set-up Page. A clean WordPress install is required for this test
	// to work. After the set-up, the Settings Page and the Post Edit Page will
	// be tested.
	describe('test the Setup Page', __WEBPACK_IMPORTED_MODULE_1__SetupPage__["a" /* default */]);

	// Test the WordLift Settings Page.
	describe('test the Settings Page', __WEBPACK_IMPORTED_MODULE_2__SettingsPage__["a" /* default */]);

	// Test the Post Edit Page.
	describe('test the Post Edit Page', __WEBPACK_IMPORTED_MODULE_3__PostEditPage__["a" /* default */]);
});

/***/ })
/******/ ]);
//# sourceMappingURL=indexSpec.js.map