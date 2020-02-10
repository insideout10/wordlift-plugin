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

/**
 * Internal dependencies.
 */
import { REQUEST_FAQ_ADD_NEW_QUESTION, REQUEST_GET_FAQ_ITEMS, UPDATE_FAQ_ITEM } from "../constants/action-types";
import API from "../api/index";
import { getAllFAQItems, getCurrentQuestion } from "../selectors";
import { requestGetFaqItems, updateFaqItems } from "../actions";
import { transformAPIDataToUi } from "./filters";
import { faqEditItemType } from "../components/faq-edit-item";

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
  yield put(requestGetFaqItems());
}

function* handleGetFaqItems() {
  const faqItems = yield call(API.getFAQItems);
  const action = updateFaqItems();
  action.payload = transformAPIDataToUi(faqItems);
  yield put(action);
}

function* handleUpdateFaqItems(action) {
  const faqItems = yield select(getAllFAQItems);
  const payload = action.payload;
  const faqItemIndex = faqItems.map(e => e.id).indexOf(payload.id);
  switch (payload.type) {
    case faqEditItemType.ANSWER:
      faqItems[faqItemIndex]["answer"] = payload.value;
      break;
    case faqEditItemType.QUESTION:
      faqItems[faqItemIndex]["question"] = payload.value;
      break;
  }
  yield call(API.updateFAQItems, faqItems);
  yield put(requestGetFaqItems());
}

function* rootSaga() {
  yield takeLatest(REQUEST_FAQ_ADD_NEW_QUESTION, handleAddNewQuestion);
  yield takeLatest(REQUEST_GET_FAQ_ITEMS, handleGetFaqItems);
  yield takeLatest(UPDATE_FAQ_ITEM, handleUpdateFaqItems);
}

export default rootSaga;
