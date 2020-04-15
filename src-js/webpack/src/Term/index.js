/*
 * External dependencies.
 */
import React from "react";
import ReactDOM from "react-dom";

/*
 * Internal dependencies.
 */
import AutocompleteSelect from "../classic-editor/components/Autocomplete/AutocompleteSelect";

/*
 * Styles
 */
import "../classic-editor/index.scss";

// Set a reference to the WordLift's settings stored in the window instance.
const settings = window["wlSettings"] || {};

let autocompleteTimeout = null;

const autocomplete = (query, callback) => {
  // Minimum 3 characters.
  if (3 > query.length) {
    callback(null, { options: [] });
    return;
  }

  // Clear any existing query.
  if (null !== autocompleteTimeout) clearTimeout(autocompleteTimeout);

  // Send our query.
  autocompleteTimeout = setTimeout(
    () =>
      wp.ajax
        .post("wl_autocomplete", {
          query,
          _wpnonce: settings["wl_autocomplete_nonce"],
          exclude: settings["itemId"]
        })
        .done(json => callback(null, { options: json }))
        .fail(() => {
          console.log("error");
          callback(null, { options: [] });
        }),
    1000
  );
};

// ### Render the sameAs metabox field autocomplete select.
window.addEventListener("load", () => {
  const element = document.getElementById("wl-term-entity-id");

  // Check that the document element is there.
  if (null === element) {
    return;
  }

  ReactDOM.render(
    <AutocompleteSelect
      loadOptions={autocomplete}
      name="wl_entity_id[]"
      placeholder=""
      filterOption={(option, filter) => true}
      searchPromptText={
        settings.l10n["Type at least 3 characters to search..."]
      }
      loadingPlaceholder={
        settings.l10n[
          "Please wait while we look for entities in the linked data cloud..."
        ]
      }
      noResultsText={settings.l10n["No results found for your search."]}
    />,
    element
  );
});
