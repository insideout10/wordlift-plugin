/**
 * This file defines the store for the Edit Post screen.
 *
 * The store we create here is connected to 2 middlewares:
 *  - sagas, defined in ./sagas.js which handles the side effects.
 *  - thunk, connecting the store to events (mainly coming from the Angular legacy code) defined in ../index.classification-box.js
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.4
 */

/**
 * External dependencies.
 */
import logger from "redux-logger";
import { applyMiddleware, createStore } from "redux";
import createSagaMiddleware from "redux-saga";
import thunk from "redux-thunk";

/**
 * Internal dependencies.
 */
import reducer from "../../Edit/reducers";
import sagas from "./sagas";

// Create the store.
const sagaMiddleware = createSagaMiddleware();
const store = createStore(reducer, applyMiddleware(sagaMiddleware, thunk, logger));
sagaMiddleware.run(sagas);

// Finally export the store.
export default store;
