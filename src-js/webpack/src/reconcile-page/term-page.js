import ReactDOM from "react-dom";
import {Provider} from "react-redux";
import React from "react";
import Tag from "./components/tag";
import createSagaMiddleware from "redux-saga";
import {applyMiddleware, createStore} from "redux";
import {reducer} from "./reducers";
import thunk from "redux-thunk";
import logger from "redux-logger";
import rootSaga from "./sagas";

export const TERMS_PAGE_SETTINGS_CONFIG = "_wlVocabularyMatchTermsConfig";





window.addEventListener("load", () => {

    const INITIAL_STATE = {
        tags: [ window[TERMS_PAGE_SETTINGS_CONFIG]["termData"] ],
        isRequestInProgress: false,
        offset: 0
    };


    const sagaMiddleware = createSagaMiddleware();
    const store = createStore(reducer, INITIAL_STATE, applyMiddleware(sagaMiddleware, thunk, logger));
    sagaMiddleware.run(rootSaga);

    const el = document.getElementById("wl_vocabulary_terms_widget");

    if (el) {
        ReactDOM.render(
            <Provider store={store}>
                <Tag />
            </Provider>,
            el
        );
    }

})
