/**
 * Internal dependencies.
 */
import "./analytics";

// Set a reference to the WordLift settings.
const settings = window.wlSettings;

/**
 * Build the request URL, inclusive of the query string parameters.
 *
 * @since 3.19.1
 *
 * @param params {{apiUrl, postId, isHome}} The query parameters.
 * @returns {string} The request URl.
 */
const buildUrl = function(params) {
  // Join with `?` or `&`.
  const joinChar = 0 <= params.apiUrl.indexOf("?") ? "&" : "?";

  // Build the URL
  const url =
    params.apiUrl +
    joinChar +
    "action=wl_jsonld" +
    // Append the post id parameter.
    ("undefined" !== typeof params.postId ? "&id=" + params.postId : "") +
    // Append the homepage parameter.
    ("undefined" !== typeof params.isHome ? "&homepage=true" : "");

  return url;
};

/**
 * Load the JSON-LD.
 *
 * @since 3.0.0
 */
const loadJsonLd = function() {
  // Bail out it the container doesn't now about fetch.
  if ("undefined" === typeof fetch) return;

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
    "undefined" === typeof settings.postId &&
    "undefined" === typeof settings.isHome
  ) {
    return;
  }

  // Get the request URL.
  const url = buildUrl(settings);

  // Finally fetch the URL.
  //
  // DO NOT use here `new URL(...)` / `URL.searchParams`: Google SDTT doesn't understand them.
  fetch(url)
    .then(function(response) {
      return response.text();
    })
    .then(function(body) {
      // Use `document.createElement`. See https://github.com/insideout10/wordlift-plugin/issues/810.
      const script = document.createElement("script");
      script.type = "application/ld+json";
      script.innerText = body;
      document.head.appendChild(script);
    });
};

loadJsonLd();

//
// window.addEventListener("load", loadJsonLd);
