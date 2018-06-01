const settings = window.wlSettings;

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

  const params = {
    action: "wl_jsonld"
  };

  // Check that we have a post id, and add it to the params.
  if (typeof settings.postId !== "undefined") {
    params.id = settings.postId;
  }

  // Check that we have param that indicates we are on homepage, and add it
  // to the params.
  if (typeof settings.isHome !== "undefined") {
    params.homepage = true;
  }

  // Request the JSON-LD data.
  //
  // See https://fetch.spec.whatwg.org/#fetch-api.
  const url = new URL(settings.apiUrl);
  Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

  fetch(url)
    .then(function(response) {
      return response.text();
    })
    .then(function(body) {
      // Use `document.createElement`. See https://github.com/insideout10/wordlift-plugin/issues/810.
      const script = document.createElement("script");
      script.type = "application/ld+json";
      script.innerText = JSON.stringify(body);
      document.head.appendChild(script);
    });
};

window.addEventListener("load", loadJsonLd);
