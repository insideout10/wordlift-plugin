/**
 * External dependencies
 */
import { applyMiddleware, combineReducers, createStore } from "redux";
import createSagaMiddleware from "redux-saga";
import { logger } from "redux-logger";
import { Map } from "immutable";

/**
 * WordPress dependencies
 */
import { registerGenericStore } from "@wordpress/data";

/**
 * Internal dependencies
 */
import { EDITOR_STORE } from "../../Gutenberg/constants";
import entities from "../../Edit/reducers/entities";
import annotationFilter from "../../Edit/reducers/annotationFilter";
import visibilityFilter from "../../Edit/reducers/visibilityFilter";
import actions from "./actions";
import { getAnnotationFilter, getEditor, getEntities, getSelectedEntities } from "./selectors";
import saga from "./sagas";
import { editorSelectionChanged } from "../../Edit/actions";

const initialState = { entities: Map() };
const sagaMiddleware = createSagaMiddleware();
const store = createStore(
  combineReducers({ entities, annotationFilter, visibilityFilter }),
  initialState,
  applyMiddleware(sagaMiddleware, logger)
);
sagaMiddleware.run(saga);

// Register the store with WordPress.
registerGenericStore(EDITOR_STORE, {
  getSelectors() {
    return {
      getAnnotationFilter: (...args) => getAnnotationFilter(store.getState(), ...args),
      getEditor: (...args) => getEditor(store.getState(), ...args),
      getEntities: (...args) => getEntities(store.getState(), ...args),
      getSelectedEntities: (...args) => getSelectedEntities(store.getState(), ...args)
    };
  },
  getActions() {
    return {
      requestAnalysis: (...args) => store.dispatch(actions.requestAnalysis(...args)),
      editorSelectionChanged: args => store.dispatch(editorSelectionChanged(args))
    };
  },
  subscribe: store.subscribe
});

export default store;
