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
import rootSaga from "../sagas";
import {reducer} from "../reducers";

export const INITIAL_STATE = {
    tags: [],
    isRequestInProgress: false,
    offset: 0
};


const sagaMiddleware = createSagaMiddleware();
export const store = createStore(reducer, INITIAL_STATE, applyMiddleware(sagaMiddleware, thunk, logger));
sagaMiddleware.run(rootSaga);

export default store;
