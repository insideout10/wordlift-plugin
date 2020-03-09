/**
 * This files provide the sagas for post excerpt
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
/**
 * External dependencies.
 */
import createSagaMiddleware from "redux-saga";
import {applyMiddleware, createStore} from "redux";
import {logger} from "redux-logger";
/**
 * Internal dependencies.
 */
import {reducer} from "../actions";
import rootSaga from "../sagas";

export const POST_EXCERPT_INITIAL_STATE = {
  isRequestInProgress: false,
  // Empty at starting
  currentPostExcerpt: ""
};

const sagaMiddleware = createSagaMiddleware();
const store = createStore(reducer, POST_EXCERPT_INITIAL_STATE, applyMiddleware(sagaMiddleware, logger));
sagaMiddleware.run(rootSaga);

export default store;
