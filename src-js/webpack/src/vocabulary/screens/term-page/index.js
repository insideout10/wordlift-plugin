/**
 * External dependencies.
 */
import ReactDOM from "react-dom";
import React from "react";
import { Provider } from "react-redux";
import createSagaMiddleware from "redux-saga";
import { applyMiddleware, createStore } from "redux";
import thunk from "redux-thunk";
import logger from "redux-logger";

import { createReducer } from "@reduxjs/toolkit";

/**
 * Internal dependencies.
 */
import { entitySaga } from "./saga";
import {reducer} from "./reducer"
import EntityList from "./entity-list";
import SearchEntity from "./search-entity";

/**
 * Internal dependencies.
 */
export const TERMS_PAGE_SETTINGS_CONFIG = "_wlVocabularyTermPageSettings";

const sagaMiddleware = createSagaMiddleware();



window.addEventListener("load", () => {


  const pageSettings = window[TERMS_PAGE_SETTINGS_CONFIG];
  const el = document.getElementById("wl_vocabulary_terms_widget");
  const entities = pageSettings["termData"]["entities"];

  const store = createStore(reducer, {
    entities: entities,
    termId: pageSettings["termData"]["tagId"],
    apiConfig: pageSettings["apiConfig"]

  }, applyMiddleware(sagaMiddleware, thunk, logger));
  sagaMiddleware.run(entitySaga);

  console.log(store.getState())

  function convertToAnalysisData(selectedEntities) {

    return selectedEntities.map(e => {
      return {
        "entityId": e.id,
        "confidence": e.confidence,
        "mainType": e.mainType,
        "types": e.types,
        "label": e.label,
        "images": [],
        "sameAs": e.sameAss,
        "meta": e.meta,
        "isActive": true
      }
    })
  }

  if (el) {
    ReactDOM.render(
      <Provider store={store}>
        <React.Fragment>
          <EntityList />
          <SearchEntity addNewEntity={(selectedEntities) => {
            store.dispatch({
              type: "ADD_ENTITY_FROM_SEARCH",
              payload: convertToAnalysisData(selectedEntities)
            })
          }} />
        </React.Fragment>
      </Provider>,
      el
    );
  }
});
