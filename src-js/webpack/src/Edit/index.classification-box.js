/**
 * Load the Classification Box.
 *
 * @since 3.20.0
 */

/*
 * External dependencies.
 */
import React from "react";
import ReactDOM from "react-dom";
import { applyMiddleware, createStore } from "redux";
import thunk from "redux-thunk";
import logger from 'redux-logger';
import Provider from "react-redux/es/components/Provider";

/*
 * Internal dependencies.
 */
import reducer from "./reducers";
import App from "./components/App";
import AnnotationEvent from "./angular/AnnotationEvent";
import ReceiveAnalysisResultsEvent from "./angular/ReceiveAnalysisResultsEvent";
import UpdateOccurrencesForEntityEvent from "./angular/UpdateOccurrencesForEntityEvent";
import EditorSelectionChangedEvent from "./angular/EditorSelectionChangedEvent";

// Start-up the application when the `wlEntityList` Angular directive is
// loaded.
wp.wordlift.on("wlEntityList.loaded", function() {
  // Create the `store` with the reducer, using the analysis result as
  // `initialState`.
  const store = createStore(reducer, applyMiddleware(thunk, logger));

  window.store1 = store;

  // Render the `React` tree at the `wl-entity-list` element.
  ReactDOM.render(
    // Following is `react-redux` syntax for binding the `store` with the
    // container down to the components.
    <Provider store={store}>
      <App />
    </Provider>,
    document.getElementById("wl-entity-list")
  );

  // Listen for annotation selections in TinyMCE and dispatch the
  // `AnnotationEvent` action.
  store.dispatch(AnnotationEvent());

  // Listen for analysis results and dispatch the `receiveAnalysisResults`
  // action when new results are received.
  store.dispatch(ReceiveAnalysisResultsEvent());

  // Dispatch an redux-thunk action, which hooks to the legacy
  // `updateOccurrencesForEntity` event and dispatches the related action in
  // Redux.
  store.dispatch(UpdateOccurrencesForEntityEvent());

  // Dispatch the `editorSelectionChanged` action with the new editor selection.
  store.dispatch(EditorSelectionChangedEvent());
});
