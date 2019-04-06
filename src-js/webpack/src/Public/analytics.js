/**
 * A collection of functions and logic to handle sending of entity data to an
 * external analytics tracker.
 *
 * Objects: `ga`, `__gaTracker` are supported as is `gtag`.
 *
 * NOTE: the `__gaTracker` object is a common remap name in WordPress.
 */

(function() {
  // Only run after page has loaded.
  document.addEventListener("DOMContentLoaded", function(event) {
    // We should have an entity object by here but if not short circuit.
    if (typeof wordliftAnalyticsEntityData === "undefined") {
      return;
    }

    /**
     * Promise to handle detection and return of an analytics object.
     *
     * @type {Promise}
     */
    var detectAnalyticsObject = new Promise(function(resolve, reject) {
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
    var sendAnalyticsData = function(analyticsObj) {
      return new Promise(function(resolve, reject) {
        // if we dont have an object to push into and an object
        // with config then this is a failure - reject.
        if ("undefined" === typeof analyticsObj || "undefined" === typeof wordliftAnalyticsConfigData) {
          return reject();
        }

        // setup the custom dimention names.
        var dimX = "dimension" + wordliftAnalyticsConfigData.entity_uri_dimension;
        var dimY = "dimension" + wordliftAnalyticsConfigData.entity_type_dimension;

        // Create an array of all the individual entities.
        var entities = [];
        for (var key in wordliftAnalyticsEntityData) {
          if (wordliftAnalyticsEntityData.hasOwnProperty(key)) {
            entities.push(wordliftAnalyticsEntityData[key]);
          }
        }

        // Count the total entities we have to send.
        var entitiesTotal = entities.length;

        // console.log( `Going to send analytics events using ${analyticsObj.__wl_type} object type.` );

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
        }
        // @TODO handle failure.
        // resolve to finish.
        return resolve(true);
      });
    };

    // Fire off the promise chain to detect and send analytics data.
    detectAnalyticsObject.then(analyticsObj => sendAnalyticsData(analyticsObj));
  });

  /**
   * Detects and returns a supported analytics object if one exists.
   *
   * @method getAnalyticsObject
   * @return {object|bool}
   */
  function getAnalyticsObject() {
    var obj = false;
    // gtag must be first.
    if (window.gtag) {
      obj = window.gtag;
      obj.__wl_type = "gtag";
    } else if (window.ga) {
      obj = window.ga;
      obj.__wl_type = "ga";
    } else if (window.__gaTracker) {
      obj = window.__gaTracker;
      obj.__wl_type = "ga";
    }

    // console.log( `Found a ${obj.__wl_type} analytics object.` );

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
    }

    // console.log("Sending gtag event ...");

    analyticsObj("event", "Mentions", {
      event_category: "WordLift",
      event_label: label,
      value: 1,
      [dimX]: uri,
      [dimY]: type,
      non_interaction: true
    });
  }
})();
