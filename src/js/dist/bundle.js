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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/Public/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/Public/analytics.js":
/*!*********************************!*\
  !*** ./src/Public/analytics.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

/**
 * A collection of functions and logic to handle sending of entity data to an
 * external analytics tracker.
 *
 * Objects: `ga`, `__gaTracker` are supported as is `gtag`.
 *
 * NOTE: the `__gaTracker` object is a common remap name in WordPress.
 */
(function () {
  // Only run after page has loaded.
  window.addEventListener("load", function (event) {
    // We should have an entity object by here but if not short circuit.
    if (typeof wordliftAnalyticsEntityData === "undefined") {
      return;
    }
    /**
     * Promise to handle detection and return of an analytics object.
     *
     * @type {Promise}
     */


    var detectAnalyticsObject = new Promise(function (resolve, reject) {
      var analyticsObj = getAnalyticsObject();
      return resolve(analyticsObj);
    });
    /**
     * A function returning the promise that deals with creating items
     * to send and passing them to the correct send function.
     *
     * @method
     * @param  {object} analyticsObj an analytics tracking object that is the resolve of the detect function.
     * @return {Promise}
     */

    var sendAnalyticsData = function (analyticsObj) {
      return new Promise(function (resolve, reject) {
        // if we dont have an object to push into and an object
        // with config then this is a failure - reject.
        if ("undefined" === typeof analyticsObj || "undefined" === typeof wordliftAnalyticsConfigData) {
          return reject();
        } // setup the custom dimention names.


        var dimX = "dimension" + wordliftAnalyticsConfigData.entity_uri_dimension;
        var dimY = "dimension" + wordliftAnalyticsConfigData.entity_type_dimension; // Create an array of all the individual entities.

        var entities = [];

        for (var key in wordliftAnalyticsEntityData) {
          if (wordliftAnalyticsEntityData.hasOwnProperty(key)) {
            entities.push(wordliftAnalyticsEntityData[key]);
          }
        } // Count the total entities we have to send.


        var entitiesTotal = entities.length; // console.log( `Going to send analytics events using ${analyticsObj.__wl_type} object type.` );

        /**
         * Depending on the tracking object type send the data
         * to the correspending service.
         */

        if ("ga" === analyticsObj.__wl_type) {
          // This is `ga` style object.
          for (var i = 0; i < entitiesTotal; i++) {
            sendGaEvent(analyticsObj, dimX, dimY, entities[i].label, entities[i].uri, entities[i].type);
          }
        } else if ("gtag" === analyticsObj.__wl_type) {
          // This is `gtag` style object.
          for (var i = 0; i < entitiesTotal; i++) {
            sendGtagEvent(analyticsObj, dimX, dimY, entities[i].label, entities[i].uri, entities[i].type);
          }
        } else if ("gtm" === analyticsObj.__wl_type) {
          // This is `gtag` style object.
          for (var i = 0; i < entitiesTotal; i++) {
            sendGtmEvent(analyticsObj, dimX, dimY, entities[i].label, entities[i].uri, entities[i].type);
          }
        } // @TODO handle failure.
        // resolve to finish.


        return resolve(true);
      });
    }; // Fire off the promise chain to detect and send analytics data.


    detectAnalyticsObject.then(analyticsObj => sendAnalyticsData(analyticsObj));
  });
  /**
   * Detects and returns a supported analytics object if one exists.
   *
   * @method getAnalyticsObject
   * @return {object|bool}
   */

  function getAnalyticsObject() {
    var obj = false; // detect GTAG, GTM, GA in that order.

    if (window.gtag) {
      obj = window.gtag;
      obj.__wl_type = "gtag";
    } else if (window.dataLayer) {
      obj = window.dataLayer;
      obj.__wl_type = "gtm";
    } else if (window.ga) {
      obj = window.ga;
      obj.__wl_type = "ga";
    } else if (window.__gaTracker) {
      obj = window.__gaTracker;
      obj.__wl_type = "ga";
    } // console.log( `Found a ${obj.__wl_type} analytics object.` );


    return obj;
  }
  /**
   * Wrapper function for pushing entity analytics data to ga style tracker.
   *
   * @method sendGaEvent
   * @param  {ga} analyticsObject The anlytics object we push into.
   * @param  {string} dimX the name of the first custom dimension.
   * @param  {string} dimY the name of the second custom dimension.
   * @param  {string} label a string to use as the label.
   * @param  {string} uri the uri of this entity.
   * @param  {string} type the entity type.
   */


  function sendGaEvent(analyticsObj, dimX, dimY, label, uri, type) {
    // Double check we have the config object before continuing.
    if ("undefined" === typeof wordliftAnalyticsConfigData) {
      return false;
    }

    analyticsObj("send", "event", "WordLift", "Mentions", label, 1, {
      [dimX]: uri,
      [dimY]: type,
      nonInteraction: true
    });
  }
  /**
   * Wrapper function for pushing entity analytics data to gtag.
   *
   * @method sendGtagEvent
   * @param  {gtag} analyticsObject The anlytics object we push into.
   * @param  {string} dimX the name of the first custom dimension.
   * @param  {string} dimY the name of the second custom dimension.
   * @param  {string} label a string to use as the label.
   * @param  {string} uri the uri of this entity.
   * @param  {string} type the entity type.
   */


  function sendGtagEvent(analyticsObj, dimX, dimY, label, uri, type) {
    // Double check we have the config object before continuing.
    if ("undefined" === typeof wordliftAnalyticsConfigData) {
      return false;
    } // console.log("Sending gtag event ...");


    analyticsObj("event", "Mentions", {
      event_category: "WordLift",
      event_label: label,
      value: 1,
      [dimX]: uri,
      [dimY]: type,
      non_interaction: true
    });
  }
  /**
   * Wrapper function for pushing entity analytics data to gtag.
   *
   * @method sendGtagEvent
   * @param  {gtag} analyticsObject The anlytics object we push into.
   * @param  {string} dimX the name of the first custom dimension.
   * @param  {string} dimY the name of the second custom dimension.
   * @param  {string} label a string to use as the label.
   * @param  {string} uri the uri of this entity.
   * @param  {string} type the entity type.
   */


  function sendGtmEvent(analyticsObj, dimX, dimY, label, uri, type) {
    // Double check we have the config object before continuing.
    if ("undefined" === typeof wordliftAnalyticsConfigData) {
      return false;
    } // console.log("Sending gtm event...");


    analyticsObj.push({
      "event": "Mentions",
      "wl_event_action": "Mentions",
      "wl_event_category": "WordLift",
      "wl_event_label": label,
      "wl_event_value": 1,
      "wl_event_uri": uri,
      "wl_index_uri": dimX.replace(/^\D+/g, ''),
      "wl_event_type": type,
      "wl_index_type": dimY.replace(/^\D+/g, ''),
      "non_interaction": true
    });
  }
})();

/***/ }),

/***/ "./src/Public/index.js":
/*!*****************************!*\
  !*** ./src/Public/index.js ***!
  \*****************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _analytics__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./analytics */ "./src/Public/analytics.js");
/* harmony import */ var _analytics__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_analytics__WEBPACK_IMPORTED_MODULE_0__);
/**
 * Internal dependencies.
 */
 // Set a reference to the WordLift settings.

const settings = window.wlSettings;
/**
 * Build the request URL, inclusive of the query string parameters.
 *
 * @since 3.19.1
 *
 * @param params {{jsonld_url, postId, isHome}} The query parameters.
 * @returns {string} The request URl.
 */

const buildUrl = function (params) {
  // Join with `?` or `&`.
  // const joinChar = 0 <= params.apiUrl.indexOf("?") ? "&" : "?";
  // Build the URL
  const url = params['jsonld_url'] + ("undefined" !== typeof params.isHome ? 0 : params.postId); // params['jsonld_url'] +
  // // joinChar +
  // // "action=wl_jsonld" +
  // // Append the post id parameter.
  // ("undefined" !== typeof params.postId ? "&id=" + params.postId : "") +
  // // Append the homepage parameter.
  // ("undefined" !== typeof params.isHome ? "&homepage=true" : "");

  return url;
};
/**
 * Load the JSON-LD.
 *
 * @since 3.0.0
 */


const loadJsonLd = function () {
  // Bail out it the container doesn't now about fetch.
  if ("undefined" === typeof fetch) return; // Check if the JSON-LD is disabled, i.e. if there's a `jsonld_enabled`
  // setting explicitly defined with a value different from '1'.

  if ("undefined" !== typeof settings["jsonld_enabled"] && "1" !== settings["jsonld_enabled"]) {
    return;
  } // Check that we have a post id or it's homepage, otherwise exit.


  if ("undefined" === typeof settings.postId && "undefined" === typeof settings.isHome) {
    return;
  } // Get the request URL.


  const url = buildUrl(settings); // Finally fetch the URL.
  //
  // DO NOT use here `new URL(...)` / `URL.searchParams`: Google SDTT doesn't understand them.

  fetch(url).then(function (response) {
    return response.text();
  }).then(function (body) {
    // Use `document.createElement`. See https://github.com/insideout10/wordlift-plugin/issues/810.
    const script = document.createElement("script");
    script.type = "application/ld+json";
    script.innerText = body;
    document.head.appendChild(script);
  });
};

loadJsonLd(); //
// window.addEventListener("load", loadJsonLd);

/***/ })

/******/ });
//# sourceMappingURL=bundle.js.map