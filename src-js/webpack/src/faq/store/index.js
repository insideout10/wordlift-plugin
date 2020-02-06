/**
 * This file provides the redux store for FAQ meta box.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 *
 */

import createSagaMiddleware from "redux-saga";
import {applyMiddleware, createStore} from "redux";
import rootSaga from "../sagas";
import {faqItemsListReducer} from "../reducers/faq-items-list-reducer";

/**
 * External dependencies
 */

const FAQ_INITIAL_STATE = {
  question: "",
  faqItems: [],
};

const sagaMiddleware = createSagaMiddleware();
const store = createStore(faqItemsListReducer, FAQ_INITIAL_STATE, applyMiddleware(sagaMiddleware));
sagaMiddleware.run(rootSaga);

export default store