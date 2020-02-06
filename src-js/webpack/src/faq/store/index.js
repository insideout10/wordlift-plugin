/**
 * This file provides the redux store for FAQ meta box.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 *
 */
/**
 * External dependencies
 */
import createSagaMiddleware from "redux-saga";
import {applyMiddleware, createStore} from "redux";
import logger from "redux-logger";

/**
 * Internal dependencies
 */
import rootSaga from "../sagas";
import {faqItemsListReducer} from "../reducers/faq-items-list-reducer";



const FAQ_INITIAL_STATE = {
  question: "",
  faqItems: [],
};

const sagaMiddleware = createSagaMiddleware();
const store = createStore(faqItemsListReducer, FAQ_INITIAL_STATE, applyMiddleware(sagaMiddleware, logger));
sagaMiddleware.run(rootSaga);

export default store