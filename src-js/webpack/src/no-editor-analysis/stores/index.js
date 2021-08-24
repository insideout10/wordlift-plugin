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
import {applyMiddleware, combineReducers, createStore} from "redux";
import createSagaMiddleware from "redux-saga";
import thunk from "redux-thunk";

/**
 * Internal dependencies.
 */
import sagas from "./sagas";
import entities from "../../Edit/reducers/entities";
import annotationFilter from "../../Edit/reducers/annotationFilter";
import visibilityFilter from "../../Edit/reducers/visibilityFilter";
import editor from "../../Edit/reducers/editor";
import {ANALYSIS_COMPLETE, ANALYSIS_RUNNING} from "../actions/types";

// Create the store.
const sagaMiddleware = createSagaMiddleware();

const analysisRunning = function (state = false, action ) {

    const type = action.type

    if ( type === ANALYSIS_COMPLETE ) {
        return false
    }

    if ( type === ANALYSIS_RUNNING ) {
        return true
    }

    return state;
}

const reducer = combineReducers({
    entities,
    annotationFilter,
    visibilityFilter,
    editor,
    analysisRunning
});


const store = createStore(reducer, { analysisRunning: false}, applyMiddleware(sagaMiddleware, thunk, logger));
sagaMiddleware.run(sagas);

// Finally export the store.
export default store;
