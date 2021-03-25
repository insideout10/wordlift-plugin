/**
 * External dependencies
 */
import createSagaMiddleware from "redux-saga";
import {applyMiddleware, createStore} from "redux";
import logger from "redux-logger";
import thunk from "redux-thunk";
/**
 * Internal dependencies
 */
import {rootSaga} from "../sagas";
import {reducer} from "../reducers";

export const ASC = 'sort_asc'

export const DESC = 'sort_desc'

export const INITIAL_STATE = {
    tags: [],
    isRequestInProgress: false,
    offset: 0,
    apiConfig: global["_wlVocabularyMatchTermsConfig"] ? global["_wlVocabularyMatchTermsConfig"] : {}
};


const sagaMiddleware = createSagaMiddleware();
export const store = createStore(reducer, INITIAL_STATE, applyMiddleware(sagaMiddleware, thunk, logger));
sagaMiddleware.run(rootSaga);

export default store;
