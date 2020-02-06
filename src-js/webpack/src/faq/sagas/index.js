/**
 * This files provide the sagas for FAQ
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import { call, select, takeLatest } from "redux-saga/effects";
import { REQUEST_FAQ_ADD_NEW_QUESTION } from "../constants/action-types";
import API from "../api/index";
import { getCurrentQuestion } from "../selectors";

function* handleAddNewQuestion(action) {
  const currentQuestion = yield select(getCurrentQuestion);
  const faqItems = [{
    question: currentQuestion,
    answer: ""
  }];
  yield call(API.saveFAQItems, faqItems);
  // Refresh the screen by getting new FAQ items.
}

function* rootSaga() {
  yield takeLatest(REQUEST_FAQ_ADD_NEW_QUESTION, handleAddNewQuestion);
}

export default rootSaga;
