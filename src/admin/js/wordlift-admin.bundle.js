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
/******/ 	return __webpack_require__(__webpack_require__.s = 113);
/******/ })
/************************************************************************/
/******/ ({

/***/ 100:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
window.wp = window.wp || {};
window.wp.wordlift = window.wp.wordlift || {};

if (typeof window.wp.wordlift.trigger === 'undefined') {
	_.extend(window.wp.wordlift, Backbone.Events);
}

/* unused harmony default export */ var _unused_webpack_default_export = (window.wp.wordlift);

/***/ }),

/***/ 113:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__modules_wordlift__ = __webpack_require__(100);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__modules_delay__ = __webpack_require__(99);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__modules_check__ = __webpack_require__(98);
var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/**
 * Define our global hooks.
 *
 * @since 3.0.0
 */

/**
 * Internal dependencies
 */
// eslint-disable-next-line no-unused-vars




(function ($) {
	/**
  * Execute when the document is ready.
  *
  * @since 3.1.0
  */
	$(function () {
		// The Entity Types Taxonomy is exclusive, one cannot choose more
		// than a type. Therefore from the PHP code we provide a Walker
		// that changes checkboxes into radios. However the quickedit on the
		// client side is applied only to checkboxes, so we override the
		// function here to apply the selection also to radios.

		// Do not hook, if we're not on a page with the inlineEditPost.
		if ('undefined' === typeof inlineEditPost || null === inlineEditPost) {
			return;
		}

		// Create a reference to the original function.
		var fnEdit = inlineEditPost.edit;

		// Override the edit function.
		inlineEditPost.edit = function (id) {
			// Call the original function.
			fnEdit.apply(this, arguments);

			// Get the id (this is a copy of what happens in the original
			// edit function).
			if ((typeof id === 'undefined' ? 'undefined' : _typeof(id)) === 'object') {
				id = this.getId(id);
			}

			// Get a reference to the row data (holding the post data) and
			// to the newly displayed inline edit row.
			var rowData = $('#inline_' + id);
			var editRow = $('#edit-' + id);

			// Select the terms for the taxonomy (this is a copy of the
			// original lines in the edit function but we're targeting
			// radios instead of checkboxes).
			$('.post_category', rowData).each(function () {
				var terms = $(this).text();

				if (terms) {
					var taxname = $(this).attr('id').replace('_' + id, '');
					// Target radios (instead of checkboxes).
					$('ul.' + taxname + '-checklist :radio', editRow).val(terms.split(','));
				}
			});
		};
	});

	/**
  * Handle the alternative labels, by providing an 'Add more titles'
  * button and input texts where to add the labels.
  *
  * @since 3.2.0
  */
	$(function () {
		// Add the delete button to the existing input texts.
		$('.wl-alternative-label > .wl-delete-button').on('click', function (event) {
			$(event.delegateTarget).parent().remove();
		});

		// Handle the click on the 'Add more titles' button and bind the
		// event of the (new) delete button.
		$('#wl-add-alternative-labels-button').on('click', function (event) {
			$(event.delegateTarget).before(function () {
				var $element = $($('#wl-tmpl-alternative-label-input').html());
				$element.children('.wl-delete-button').on('click', function () {
					$element.remove();
				});
				return $element;
			});
		});
	});

	/**
  * Check for duplicate title/labels via AJAX while the user is typing.
  *
  * @since 3.2.0
  */
	$(function () {
		// return if we are not in the entity editor page (the *wlSettings*
		// json is only enqueued there) wlSettings.entityBeingEdited comes
		// from `wp_localize_script`, so '1' (true) or '' (false).
		if (typeof wlSettings === 'undefined' || '1' !== wlSettings.entityBeingEdited) {
			return;
		}

		// Print error message in page and hide it.
		var duplicatedEntityErrorDiv = $('<div class="wl-notice notice wl-suggestion"' + ' id="wl-same-title-error" ><p></p></div>').insertBefore('div.wrap [name=post]').hide();

		/**
   * Check whether the specified title is already used by other
   * entities.
   *
   * @since 3.10.0
   */
		var callback = function callback() {
			// A jQuery reference to the element firing the event.
			var $this = $(this);

			// Delay execution of the check.
			__webpack_require__.i(__WEBPACK_IMPORTED_MODULE_1__modules_delay__["a" /* default */])($this, __WEBPACK_IMPORTED_MODULE_2__modules_check__["a" /* default */], $, wp.ajax, $this.val(), wlSettings.post_id, wlSettings.l10n['You already published an entity with the same name'], function (html) {
				// Set the error div content.
				$('#wl-same-title-error p').html(html);

				// If the html code isn't empty then show the error.
				if ('' !== html) {
					duplicatedEntityErrorDiv.show();
				} else {
					// If the html code is empty, hide the error div.
					duplicatedEntityErrorDiv.hide();
				}
			});
		};

		// Whenever something happens in the entity title...
		$('[name=post_title]').on('change paste keyup', callback).each(callback);
	});

	/**
  * Draw dashboard if needed
  *
  * @since 3.4.0
  */
	$(function () {
		// return if not needed
		if (!$('#wl-dashboard-widget-inner-wrapper').length) {
			return;
		}

		$.getJSON(ajaxurl + '?action=wordlift_get_stats', function (stats) {
			// Get the triples, 0 by default if triples is not a number.
			var triples = isNaN(stats.triples) ? 0 : stats.triples;

			// Calculate wikidata ratio
			// TODO percentage should be added via css
			var percent = triples * 100 / 947690143;
			stats.wikidata = percent.toFixed(5) + '%';
			// Calculate annotated posts ratio
			var annotated = stats.annotated_posts * 100 / stats.posts;
			stats.annotatedPostsPercentage = annotated.toFixed(1);
			// Convert NaN to zero if needed
			//
			// See https://github.com/insideout10/wordlift-plugin/issues/269
			stats.annotatedPostsPercentage = stats.annotatedPostsPercentage || 0;
			// TODO percentage should be added via css
			stats.annotatedPostsPercentage = stats.annotatedPostsPercentage + '%';

			// Populate annotated posts pie chart
			$('#wl-posts-pie-chart circle').css('stroke-dasharray', stats.annotated_posts * 100 / stats.posts + ' 100');
			// Populate avarage entity ratings gauge chart
			$('#wl-entities-gauge-chart .stat').css('stroke-dasharray', stats.rating / 2 + ' 100');

			// TODO percentage should be added via css
			stats.rating = stats.rating + '%';
			// populate value placeholders
			for (var property in stats) {
				$('#wl-dashboard-widget-' + property).text(stats[property]);
			}

			// Finally show the widget
			$('#wl-dashboard-widget-inner-wrapper').show();

			// Set the same height for stat graph wrappers
			// Links not working with css alternatives
			var minHeight = 0;
			$('.wl-stat-graph-wrapper').each(function () {
				var stat = $(this);
				if (stat.height() > minHeight) {
					minHeight = stat.height();
				}
			});

			$('.wl-stat-graph-wrapper').css('min-height', minHeight);
		});
	});
})(jQuery);

/***/ }),

/***/ 98:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = check;
/**
 * Check for duplicate titles.
 *
 * @since 3.10.0
 *
 * @param {Object} $ A jQuery instance.
 * @param {Object} ajax A `wp.ajax` class used to perform `post` requests to `admin-ajax.php`.
 * @param {String} title The title to check for duplicates.
 * @param {Number} postId The current post id, excluded from the duplicates results.
 * @param {String} message The error message to display in case there are duplicates.
 * @param {Function} callback A callback function to call to deliver the results.
 */

function check($, ajax, title, postId, message, callback) {
	// Use `wp.ajax` to post a request to find an existing entity with the specified title.
	ajax.post('entity_by_title', { title: title }).done(function (response) {
		// Prepare the html code to show in the error div.
		var html = $.map(response.results, function (item) {
			// If the item is the current post, ignore it.
			if (item.id === postId) {
				return '';
			}

			// Create the edit link.
			var editLink = response.edit_link.replace('%d', item.id);

			// Return the html code.
			return message + '<a target="_blank" href="' + editLink + '">' + item.title + '</a><br />';
		}).join(''); // Join the html codes together.

		// Call the callback function.
		callback(html);
	});
}

/***/ }),

/***/ 99:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = delay;
/**
 * Delay a function call by half a second.
 *
 * Any function can be delayed using `delay`. The timeout for the call is bound
 * to the provided element. If another function call is delayed on the same
 * element, any previous timeout is cancelled.
 *
 * This function is used to validate in real-time inputs when the user presses a
 * key, but allowing the user to press more keys (hence the delay).
 *
 * @since 3.9.0
 *
 * @param {Object} $elem A jQuery element reference which will hold the timeout
 *                       reference.
 * @param {Function} fn The function to call.
 * @param {...} args Parameters to pass to the callback.
 */

function delay($elem, fn) {
  // Clear a validation timeout.
  clearTimeout($elem.data('timeout'));

  // Validate the key, after a delay, so that another key is pressed, this
  // validation is cancelled.

  for (var _len = arguments.length, args = Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
    args[_key - 2] = arguments[_key];
  }

  $elem.data('timeout', setTimeout.apply(undefined, [fn, 500].concat(args)));
}

/***/ })

/******/ });
//# sourceMappingURL=wordlift-admin.bundle.js.map