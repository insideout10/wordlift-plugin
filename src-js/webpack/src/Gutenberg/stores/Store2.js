/**
 * External dependencies.
 */
import { createStore, applyMiddleware } from "redux";
import createSagaMiddleware from "redux-saga";
import logger from "redux-logger";

/*
 * Internal dependencies.
 */
import saga from "../components/AddEntityPanel/AddEntity/sagas";
import { reducer } from "../../Edit/components/AddEntity/actions";

// Create the saga middleware.
const sagaMiddleware = createSagaMiddleware();
const store = createStore(reducer, applyMiddleware(sagaMiddleware, logger));

// Run the saga.
sagaMiddleware.run(saga);

export default store;
