/* global wp */

import { applyMiddleware, combineReducers, createStore } from "redux";
import createSagaMiddleware from "redux-saga";
import { logger } from "redux-logger";
import { Map } from "immutable";

const { registerGenericStore } = wp.data;

import Constants from "../../Gutenberg/constants";
import entities from "../../Edit/reducers/entities";
import annotationFilter from "../../Edit/reducers/annotationFilter";
import visibilityFilter from "../../Edit/reducers/visibilityFilter";
import { editor } from "./actions";
import actions from "./actions";
import selectors from "./selectors";
import saga from "./sagas";

const initialState = { entities: Map() };
const sagaMiddleware = createSagaMiddleware();
const store = createStore(
  combineReducers({ entities, annotationFilter, visibilityFilter }),
  initialState,
  applyMiddleware(sagaMiddleware, logger)
);
sagaMiddleware.run(saga);

// Register the store with WordPress.
registerGenericStore(Constants.EDITOR_STORE, {
  getSelectors() {
    return {
      getEditor: (...args) => selectors.getEditor(store.getState(), ...args)
    };
  },
  getActions() {
    return {
      requestAnalysis: (...args) => store.dispatch(actions.requestAnalysis(...args))
    };
  },
  subscribe: store.subscribe
});

export default store;
