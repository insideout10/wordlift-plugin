(function($, settings) {
  "use strict";

  const loadJsonLd = function() {
    // Check if the JSON-LD is disabled, i.e. if there's a `jsonld_enabled`
    // setting explicitly defined with a value different from '1'.
    if (
      "undefined" !== typeof settings["jsonld_enabled"] &&
      "1" !== settings["jsonld_enabled"]
    ) {
      return;
    }

    // Check that we have a post id or it's homepage, otherwise exit.
    if (
      typeof settings.postId === "undefined" &&
      typeof settings.isHome === "undefined"
    ) {
      return;
    }

    const requestData = {
      action: "wl_jsonld"
    };

    // Check that we have a post id, and add it to the requestData.
    if (typeof settings.postId !== "undefined") {
      requestData.id = settings.postId;
    }

    // Check that we have param that indicates we are on homepage, and add it
    // to the requestData.
    if (typeof settings.isHome !== "undefined") {
      requestData.homepage = true;
    }

    // Request the JSON-LD data.
    $.get(settings.apiUrl, requestData, function(data) {
      // Use `document.createElement`. See https://github.com/insideout10/wordlift-plugin/issues/810.
      const script = document.createElement("script");
      script.type = "application/ld+json";
      script.innerText = JSON.stringify(data);
      document.head.appendChild(script);
    });
  };

  window.addEventListener("load", loadJsonLd);
})(jQuery, wlSettings);
