import * as wordliftCloud from "wordlift-cloud";

window.wordliftCloud = wordliftCloud;

window.addEventListener("load", function() {
  // Bail out if `wlNavigators` isn't defined.
  if ("undefined" === typeof window["wlNavigators"] || !Array.isArray(window["wlNavigators"])) return;

  // Create a reference to the ids.
  const ids = window["wlNavigators"];

  const loadNext = () => {
    // Bail out if we don't have any more items.
    if (0 === ids.length) return;

    // Get the next item.
    const id = ids.shift();

    console.debug("Loading the next Navigator...");

    wordliftCloud.navigator(`#${id}`, loadNext);
  };

  // Load the first navigator.
  loadNext();
});
