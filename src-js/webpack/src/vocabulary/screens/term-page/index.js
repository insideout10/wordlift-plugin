/**
 * External dependencies.
 */
import ReactDOM from "react-dom";
import React from "react";
import {Provider} from "react-redux";
import createSagaMiddleware from "redux-saga";
import {applyMiddleware, createStore} from "redux";
import thunk from "redux-thunk";
import logger from "redux-logger";
import {entitySaga} from "../../sagas";
import {createReducer} from "@reduxjs/toolkit";

/**
 * Internal dependencies.
 */
import Entity from "../../components/entity";


/**
 * Internal dependencies.
 */


export const TERMS_PAGE_SETTINGS_CONFIG = "_wlVocabularyTermPageSettings";


const INITIAL_STATE = {};
const sagaMiddleware = createSagaMiddleware();
const reducer = createReducer(null, {});
const store = createStore(reducer, INITIAL_STATE, applyMiddleware(sagaMiddleware, thunk, logger));
sagaMiddleware.run(entitySaga);


window.addEventListener("load", () => {
    const pageSettings = window[TERMS_PAGE_SETTINGS_CONFIG];
    const el = document.getElementById("wl_vocabulary_terms_widget");
    const entities = pageSettings["termData"]["entities"];
    if (el) {
        ReactDOM.render(
            <Provider store={store}>
                <React.Fragment>
                    {entities.map((entity) => {
                        return (<Entity {...entity} />)
                    })}
                </React.Fragment>
            </Provider>,
            el
        );
    }


})
