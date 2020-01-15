/**
 * @@todo add a description about this file.
 */

/**
 * External dependencies
 */
import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
import { createStore } from "redux";

/**
 * Internal dependencies
 */
import MappingComponent from "./components/mapping-component";
import "./mappings.css";
import { MappingListReducer } from "./reducers/mapping-list-reducers";
import { ACTIVE_CATEGORY } from "./components/category-component";

const MAPPINGS_INITIAL_STATE = {
  // @@todo follow JavaScript developer guidelines, i.e. camelCase.
  mappingItems: [],
  // @@todo follow JavaScript developer guidelines, i.e. camelCase.
  choosenCategory: ACTIVE_CATEGORY,
  headerCheckBoxSelected: false,
  selectedBulkOption: null
};

const store = createStore(MappingListReducer, MAPPINGS_INITIAL_STATE);

window.addEventListener("load", () => {
  ReactDOM.render(
    <Provider store={store}>
      <MappingComponent />
    </Provider>,
    document.getElementById("wl-mappings-container")
  );
});
