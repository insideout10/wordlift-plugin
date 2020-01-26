/**
 * This file defines the store.
 *
 * @since 3.23.0
 */

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
import entities from "../../Edit/reducers/entities";
import annotationFilter from "../../Edit/reducers/annotationFilter";
import visibilityFilter from "../../Edit/reducers/visibilityFilter";
import blockEditor, { requestAnalysis, setFormat } from "./actions";
import relatedPosts from "../../common/containers/related-posts/actions";
import createEntityForm from "../../common/containers/create-entity-form/actions";
import {
  getAnnotationFilter,
  getBlockEditor,
  getBlockEditorFormat,
  getEditor,
  getEntities,
  getSelectedEntities
} from "./selectors";
import saga from "./sagas";
import { editorSelectionChanged } from "../../Edit/actions";
import { setValue } from "../../Edit/components/AddEntity/actions";
import { WORDLIFT_STORE } from "../../common/constants";

const initialState = { entities: Map() };
const sagaMiddleware = createSagaMiddleware();
const store = createStore(
  combineReducers({ entities, annotationFilter, visibilityFilter, blockEditor, createEntityForm, relatedPosts }),
  initialState,
  applyMiddleware(sagaMiddleware, logger)
);
sagaMiddleware.run(saga);

// Register the store with WordPress.
registerGenericStore(WORDLIFT_STORE, {
  getSelectors() {
    return {
      getAnnotationFilter: (...args) => getAnnotationFilter(store.getState(), ...args),
      getEditor: (...args) => getEditor(store.getState(), ...args),
      getEntities: (...args) => getEntities(store.getState(), ...args),
      getSelectedEntities: (...args) => getSelectedEntities(store.getState(), ...args),
      getBlockEditor: (...args) => getBlockEditor(store.getState(), ...args),
      getBlockEditorFormat: (...args) => getBlockEditorFormat(store.getState(), ...args)
    };
  },
  getActions() {
    return {
      editorSelectionChanged: args => store.dispatch(editorSelectionChanged(args)),
      requestAnalysis: (...args) => store.dispatch(requestAnalysis(...args)),
      // Called when the selection changes in editor.
      setValue: args => store.dispatch(setValue(args)),
      // Called to set the block editor current selection as Block Editor format value.
      setFormat: args => store.dispatch(setFormat(args))
    };
  },
  subscribe: store.subscribe
});

export default store;
