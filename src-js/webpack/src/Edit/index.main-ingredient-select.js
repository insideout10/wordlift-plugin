/**
 * Load the Main Ingredient Select.
 *
 * @since 3.38.3
 */
import ReactDOM from "react-dom";
import AutocompleteSelect from "./components/Autocomplete/AutocompleteSelect";
import React from "react";

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
        .post("wl_ingredient_autocomplete", {
          query,
          _wpnonce: _wlRecipeIngredient.acNonce
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
  document.querySelectorAll(".wl-select-main-ingredient").forEach(el => {
    ReactDOM.render(
      <AutocompleteSelect
        loadOptions={autocomplete}
        name="main_ingredient[]"
        placeholder=""
        filterOption={(option, filter) => true}
        searchPromptText={settings.l10n["Type at least 3 characters to search..."]}
        loadingPlaceholder={settings.l10n["Please wait while we look for entities in the linked data cloud..."]}
        noResultsText={settings.l10n["No results found for your search."]}
        optionRenderer={props => <>{props}</>}
      />,
      el
    );
  });
});
