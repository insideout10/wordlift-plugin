/*
 * External dependencies.
 */
import { applyMiddleware, createStore } from "redux";
import thunk from "redux-thunk";
import logger from "redux-logger";

/*
 * Internal dependencies.
 */
import reducer from "../reducers";

const store1 = createStore(reducer, applyMiddleware(thunk, logger));

window.wordlift.store1 = store1;

export default store1;
