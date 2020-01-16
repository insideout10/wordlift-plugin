/**
 * Shows the list of mapping items in the screen, the user can do
 * CRUD operations on this ui.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
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
  mappingItems: [],
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
