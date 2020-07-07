const settings = window["wlSettings"];

export function autocomplete(query, language, ...excludes) {
  // eslint-disable-next-line
  if ("undefined" !== wp.ajax) {
    // eslint-disable-next-line
    return wp.ajax.post("wl_autocomplete", {
      query,
      // show local entities on post edit screen if scope set to cloud
      show_local_entities: settings["autocomplete_scope"] === 'cloud',
      // eslint-disable-next-line
      _wpnonce: settings["wl_autocomplete_nonce"],
      // eslint-disable-next-line
      exclude: settings["itemId"],
      // eslint-disable-next-line
      scope: settings["autocomplete_scope"]
    });
  } else {
    const url = new URL("http://localhost:8080/wordlift-api/autocomplete");

    url.searchParams.append("query", query);
    url.searchParams.append("language", language);

    if (0 === excludes.length) url.searchParams.append("exclude", "");
    else excludes.forEach(value => url.searchParams.append("exclude", value));

    return fetch(url).then(response => response.json());
  }
}
