/**
 * This files provide the sagas for FAQ
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import { call, select, takeLatest, put } from "redux-saga/effects";
import { REQUEST_FAQ_ADD_NEW_QUESTION, REQUEST_GET_FAQ_ITEMS } from "../constants/action-types";
import API from "../api/index";
import { getCurrentQuestion } from "../selectors";
import {requestGetFaqItems, updateFaqItems} from "../actions";
import {transformAPIDataToUi} from "./filters";

function* handleAddNewQuestion(action) {
  const currentQuestion = yield select(getCurrentQuestion);
  const faqItems = [
    {
      question: currentQuestion,
      answer: ""
    }
  ];
  yield call(API.saveFAQItems, faqItems);
  // Refresh the screen by getting new FAQ items.
  yield put(requestGetFaqItems())
}

function* handleGetFaqItems() {
  const faqItems = yield call(API.getFAQItems);
  const action = updateFaqItems();
  action.payload = transformAPIDataToUi(faqItems);
  yield put(action);
}

function* rootSaga() {
  yield takeLatest(REQUEST_FAQ_ADD_NEW_QUESTION, handleAddNewQuestion);
  yield takeLatest(REQUEST_GET_FAQ_ITEMS, handleGetFaqItems);
}

export default rootSaga;
