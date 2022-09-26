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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/vocabulary/screens/settings/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/vocabulary/components/analysis-progress-bar/api.js":
/*!****************************************************************!*\
  !*** ./src/vocabulary/components/analysis-progress-bar/api.js ***!
  \****************************************************************/
/*! exports provided: startBackgroundAnalysis, stopBackgroundAnalysis, restartAnalysis, getAnalysisStats */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "startBackgroundAnalysis", function() { return startBackgroundAnalysis; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "stopBackgroundAnalysis", function() { return stopBackgroundAnalysis; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "restartAnalysis", function() { return restartAnalysis; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getAnalysisStats", function() { return getAnalysisStats; });
/**
 * Api functions for start, stop, get stats about analysis.
 * @since 1.1.0
 */
function startBackgroundAnalysis(apiConfig) {
  const {
    baseUrl,
    nonce
  } = apiConfig;
  return fetch(baseUrl + "/background_analysis/start", {
    method: "POST",
    headers: {
      "X-WP-Nonce": nonce
    }
  }).then(response => response.json()).then(json => json);
}
function stopBackgroundAnalysis(apiConfig) {
  const {
    baseUrl,
    nonce
  } = apiConfig;
  return fetch(baseUrl + "/background_analysis/stop", {
    method: "POST",
    headers: {
      "X-WP-Nonce": nonce
    }
  }).then(response => response.json()).then(json => json);
}
/**
 * Removes previously cached analysis results.
 * @returns {*}
 */

function restartAnalysis(apiConfig) {
  const {
    baseUrl,
    nonce
  } = apiConfig;
  return fetch(baseUrl + "/background_analysis/restart", {
    method: "POST",
    headers: {
      "X-WP-Nonce": nonce
    }
  }).then(response => response.json()).then(json => json);
}
function getAnalysisStats(apiConfig) {
  const {
    baseUrl,
    nonce
  } = apiConfig;
  return fetch(baseUrl + "/background_analysis/stats", {
    method: "POST",
    headers: {
      "X-WP-Nonce": nonce
    }
  }).then(response => response.json()).then(json => json);
}

/***/ }),

/***/ "./src/vocabulary/components/analysis-progress-bar/index.js":
/*!******************************************************************!*\
  !*** ./src/vocabulary/components/analysis-progress-bar/index.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return AnalysisProgressBar; });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./index.scss */ "./src/vocabulary/components/analysis-progress-bar/index.scss");
/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_index_scss__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./api */ "./src/vocabulary/components/analysis-progress-bar/api.js");
/* harmony import */ var _progress_bar__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../progress-bar */ "./src/vocabulary/components/progress-bar/index.js");
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */




class AnalysisProgressBar extends react__WEBPACK_IMPORTED_MODULE_0___default.a.Component {
  constructor(props) {
    super(props);
    this.state = {
      stats: {
        index: 0,
        count: 0
      },
      isRequestInProgress: false
    };
    this.buttonClickListener = this.buttonClickListener.bind(this);
    this.restartClickListener = this.restartClickListener.bind(this); // Start updating progress bar

    this.getStats(); // Update progress bar every 5 seconds.

    this.interval = setInterval(() => this.getStats(), 5000);
  }

  componentWillUnmount() {
    clearInterval(this.interval);
  }

  getStats() {
    this.setState({
      isRequestInProgress: true
    });
    Object(_api__WEBPACK_IMPORTED_MODULE_2__["getAnalysisStats"])(this.props.apiConfig).then(data => {
      this.setState({
        stats: data,
        isRequestInProgress: false
      });
    });
  }

  buttonClickListener() {
    this.setState({
      isRequestInProgress: true
    });

    if (this.isAnalysisRunning(this.state.stats)) {
      Object(_api__WEBPACK_IMPORTED_MODULE_2__["stopBackgroundAnalysis"])(this.props.apiConfig).then(() => {
        this.updateAnalysisState('stopped');
      });
    } else {
      Object(_api__WEBPACK_IMPORTED_MODULE_2__["startBackgroundAnalysis"])(this.props.apiConfig).then(() => {
        this.updateAnalysisState('started');
      });
    }
  }

  updateAnalysisState(analysisState) {
    this.setState(prevState => ({ ...prevState,
      stats: { ...prevState.stats,
        state: analysisState
      }
    }));
  }

  render() {
    const stats = this.state.stats;
    let progress = this.calcProgress(stats);

    if (progress > 100) {
      progress = 100;
    }

    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
      className: "wl_cmkg_analysis_progress_bar_container"
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
      style: {
        width: "90%"
      }
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("h3", null, "Analysis background task (", stats.index + "/" + stats.count, ")"), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("br", null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_progress_bar__WEBPACK_IMPORTED_MODULE_3__["ProgressBar"], {
      progress: progress
    })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
      style: {
        width: "10%"
      }
    }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("span", {
      style: {
        cursor: "pointer",
        fontSize: "30px",
        marginTop: "10px"
      },
      className: this.getIconName(stats),
      onClick: this.buttonClickListener
    }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("span", {
      style: {
        cursor: "pointer",
        fontSize: "30px",
        marginTop: "10px",
        marginLeft: "20px"
      },
      className: "dashicons dashicons-image-rotate",
      title: "Restart Analysis",
      onClick: this.restartClickListener
    })));
  }

  getIconName(stats) {
    // check if we need to disable them.
    // if (stats.count === 0) {
    //     iconName += " wl_cmkg_icon--disabled"
    // }
    return this.isAnalysisRunning(stats) ? 'dashicons dashicons-controls-pause' : 'dashicons dashicons-controls-play';
  }

  isAnalysisRunning(stats) {
    return stats.state === "started";
  }

  calcProgress(stats) {
    if (stats.count === 0) {
      return 0;
    }

    if (stats.index === 0) {
      return 0;
    }

    return stats.index / stats.count * 100;
  }

  restartClickListener() {
    const result = confirm("Restarting analysis will remove the previous results, are you sure you want to proceed ? ");

    if (result === true) {
      // send the restart request.
      Object(_api__WEBPACK_IMPORTED_MODULE_2__["restartAnalysis"])(this.props.apiConfig).then(() => {
        // update stats after restart
        this.getStats();
      });
    }
  }

}

/***/ }),

/***/ "./src/vocabulary/components/analysis-progress-bar/index.scss":
/*!********************************************************************!*\
  !*** ./src/vocabulary/components/analysis-progress-bar/index.scss ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "./src/vocabulary/components/progress-bar/index.js":
/*!*********************************************************!*\
  !*** ./src/vocabulary/components/progress-bar/index.js ***!
  \*********************************************************/
/*! exports provided: ProgressBar */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ProgressBar", function() { return ProgressBar; });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const ProgressBar = ({
  progress
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
    style: {
      width: "100%",
      height: "10px",
      backgroundColor: "#eee"
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement("div", {
    style: {
      width: progress + "%",
      height: "10px",
      backgroundColor: "green",
      textAlign: "center"
    }
  }));
};

/***/ }),

/***/ "./src/vocabulary/screens/settings/index.js":
/*!**************************************************!*\
  !*** ./src/vocabulary/screens/settings/index.js ***!
  \**************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react-dom */ "react-dom");
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react_dom__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_analysis_progress_bar__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/analysis-progress-bar */ "./src/vocabulary/components/analysis-progress-bar/index.js");
/**
 * External dependencies.
 */


/**
 * Internal dependencies.
 */


window.addEventListener("load", () => {
  const el = document.getElementById("wl_vocabulary_analysis_progress_bar");

  if (el) {
    react_dom__WEBPACK_IMPORTED_MODULE_0___default.a.render( /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_1___default.a.createElement(_components_analysis_progress_bar__WEBPACK_IMPORTED_MODULE_2__["default"], {
      apiConfig: window["wlSettings"]["matchTerms"]
    }), el);
  }
});

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["React"]; }());

/***/ }),

/***/ "react-dom":
/*!***************************!*\
  !*** external "ReactDOM" ***!
  \***************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["ReactDOM"]; }());

/***/ })

/******/ });
//# sourceMappingURL=vocabulary-settings-page.js.map