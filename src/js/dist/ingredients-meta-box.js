<<<<<<< HEAD
!function(e){var t={};function n(r){if(t[r])return t[r].exports;var i=t[r]={i:r,l:!1,exports:{}};return e[r].call(i.exports,i,i.exports,n),i.l=!0,i.exports}n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var i in e)n.d(r,i,function(t){return e[t]}.bind(null,i));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=217)}({217:function(e,t){jQuery((function(e){e(".wl-recipe-ingredient-form__submit").on("click",(function(t){t.preventDefault(t);const n=e(".wl-table--main-ingredient__data");let r=[];n.each((t,n)=>{const i=e(n).find("#recipe-id").val(),o=e(n).find("input[name='main_ingredient[]']").val();i&&o&&r.push({recipe_id:i,ingredient:o})});const i={_wpnonce:_wlRecipeIngredient.nonce,data:JSON.stringify(r)};wp.ajax.post("wl_update_ingredient_post_meta",i).done((function(e){wp.data.dispatch("core/notices").createNotice("success",e.message,{type:"snackbar",isDismissible:!0})})).fail((function(e){wp.data.dispatch("core/notices").createNotice("error",e.message,{type:"snackbar",isDismissible:!0})}))}))}))}});
=======
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/ingredients/meta-box.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/ingredients/meta-box.js":
/*!*************************************!*\
  !*** ./src/ingredients/meta-box.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// jQuery Code.
jQuery(function ($) {
  // Update Ingredient.
  const ingredientFormSubmitBtn = $('.wl-recipe-ingredient-form__submit');
  ingredientFormSubmitBtn.on('click', function (e) {
    e.preventDefault(e);
    const ingredientsData = $('.wl-table--main-ingredient__data');
    let recipeData = [];
    ingredientsData.each((index, element) => {
      const recipeID = $(element).find('#recipe-id').val();
      const ingredient = $(element).find("input[name='recipe_main_ingredient[]']").val();

      if (!recipeID || !ingredient) {
        return;
      }

      recipeData.push({
        recipe_id: recipeID,
        ingredient: ingredient
      });
    });
    const data = {
      _wpnonce: _wlRecipeIngredientSettings.nonce,
      data: JSON.stringify(recipeData)
    }; // Save the ingredient.

    wp.ajax.post("wl_update_ingredient_post_meta", data).done(function (response) {
      wp.data.dispatch('core/notices').createNotice('success', response.message, {
        type: 'snackbar',
        isDismissible: true
      });
    }).fail(function (error) {
      wp.data.dispatch('core/notices').createNotice('error', error.message, {
        type: 'snackbar',
        isDismissible: true
      });
    });
  });
});

/***/ })

/******/ });
//# sourceMappingURL=ingredients-meta-box.js.map
>>>>>>> 565192e80e2a8462c14291e70e6d4d42180787e7
