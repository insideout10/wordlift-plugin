/**
 * This file provides the store configuration.
 *
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import createSagaMiddleware from "redux-saga";
import { applyMiddleware, createStore } from "redux";
import logger from "redux-logger";

/**
 * Internal dependencies
 */
import saga from "./sagas";
import { MappingListReducer } from "../reducers/mapping-list-reducers";
import { ACTIVE_CATEGORY } from "../components/category-component";
import { SORT_BY_ASC } from "../constants";

const MAPPINGS_INITIAL_STATE = {
  mappingItems: [],
  chosenCategory: ACTIVE_CATEGORY,
  headerCheckBoxSelected: false,
  selectedBulkOption: null,
  titleSortBy: SORT_BY_ASC,
  titleIcon: "dashicons-arrow-up"
};

const sagaMiddleware = createSagaMiddleware();
const store = createStore(MappingListReducer, MAPPINGS_INITIAL_STATE, applyMiddleware(sagaMiddleware, logger));
sagaMiddleware.run(saga);

export default store;
