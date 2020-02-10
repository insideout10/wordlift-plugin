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
import {faqModalReducer} from "../reducers/faq-modal-reducer";

const FAQ_INITIAL_STATE = {
  faqListOptions: {
    question: "",
    faqItems: [],
    selectedFaqId: null,
  },
  faqModalOptions: {
    isModalOpened: false,
  }
};

const sagaMiddleware = createSagaMiddleware();
const store = createStore(faqModalReducer, FAQ_INITIAL_STATE, applyMiddleware(sagaMiddleware, logger));
sagaMiddleware.run(rootSaga);

export default store