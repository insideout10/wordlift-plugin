/*
 * External dependencies.
 */
import { applyMiddleware, createStore } from "redux";
import thunk from "redux-thunk";
import logger from 'redux-logger';

/*
 * Internal dependencies.
 */
import reducer from "./reducers"

export default createStore(reducer, applyMiddleware(thunk, logger));