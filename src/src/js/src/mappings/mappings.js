import React from "react";
import MappingComponent from "./components/mapping-component";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
import { createStore } from "redux";
import "./mappings.css";
import { MappingListReducer } from "./reducers/mapping-list-reducers";
import { ACTIVE_CATEGORY } from "./components/category-component";

const MAPPINGS_INITIAL_STATE = {
  mapping_items: [],
  choosen_category: ACTIVE_CATEGORY,
  headerCheckBoxSelected: false,
  selectedBulkOption: null
};

const store = createStore(MappingListReducer, MAPPINGS_INITIAL_STATE);

ReactDOM.render(
  <Provider store={store}>
    <MappingComponent />
  </Provider>,
  document.getElementById("container")
);
