/**
 * External dependencies
 */
import createSagaMiddleware from "redux-saga";
import thunk from "redux-thunk";
import logger from "redux-logger";
import {applyMiddleware, createStore} from "redux";


/**
 * Internal dependencies
 */
import {reducer} from "../reducers";
import {rootSaga} from "../sagas";


export const INITIAL_STATE = {
    videos: [],
    apiConfig: global["_wlVideoobjectConfig"] ? global["_wlVideoobjectConfig"] : {}
};


const sagaMiddleware = createSagaMiddleware();
export const store = createStore(reducer, INITIAL_STATE, applyMiddleware(sagaMiddleware, thunk, logger));
sagaMiddleware.run(rootSaga);

export default store;
