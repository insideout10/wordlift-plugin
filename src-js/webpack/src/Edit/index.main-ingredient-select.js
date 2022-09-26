/**
 * Load the Main Ingredient Select.
 *
 * @since 3.38.3
 */
import ReactDOM from "react-dom";
import AutocompleteSelect from "./components/Autocomplete/AutocompleteSelect";
import React from "react";
import Select from "react-select";

// ### Render the sameAs metabox field autocomplete select.
window.addEventListener("load", () => {
  // Set a reference to the WordLift's settings stored in the window instance.
  const settings = window["_wlRecipeIngredientSettings"] || {};

  let autocompleteTimeout = null;

  const DEFAULT_OPTIONS = [
    { label: settings.l10n["(don't change)"], value: "DONT_CHANGE" },
    { label: settings.l10n["(unset)"], value: "UNSET" }
  ];

  const autocomplete = (query, callback) => {
    // Minimum 3 characters.
    if (3 > query.length) {
      callback(null, {
        options: DEFAULT_OPTIONS
      });
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
            _wpnonce: settings.acNonce
          })
          .done(json => callback(null, { options: DEFAULT_OPTIONS.concat(json) }))
          .fail(() => {
            console.log("error");
            callback(null, { options: [] });
          }),
      1000
    );
  };

  class MainIngredientSelect extends React.Component {

    constructor(props) {
      super(props);
      this.onChange = this.onChange.bind(this);
      this.state = { value: DEFAULT_OPTIONS[0] };
    }

    onChange(value) {
      this.setState({ value });
    }
    render() {
      return (
        <Select.Async
          multi={false}
          value={this.state.value}
          onChange={this.onChange}
          loadOptions={autocomplete}
        ></Select.Async>
      );
    }
  }

  document.querySelectorAll(".wl-select-main-ingredient").forEach(el => {
    ReactDOM.render(<MainIngredientSelect />, el);
  });
});

/**
 *       <AutocompleteSelect
 *         value={DEFAULT_OPTIONS[0]}
 *         multi={false}
 *         loadOptions={autocomplete}
 *         name="main_ingredient[]"
 *         placeholder=""
 *         filterOption={(option, filter) => true}
 *         searchPromptText={settings.l10n["Type at least 3 characters to search..."]}
 *         loadingPlaceholder={settings.l10n["Looking for main ingredients..."]}
 *         noResultsText={settings.l10n["No results found for your search."]}
 *         optionRenderer={props => (
 *           <Option instancePrefix={"main-ingredient-"} option={props}>
 *             <div>{props.label}</div>
 *           </Option>
 *         )}
 *         valueComponent={Value}
 *       />,
 */
