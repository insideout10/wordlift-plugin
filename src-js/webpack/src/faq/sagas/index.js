/**
 * This files provide the sagas for FAQ
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import { call, delay, put, select, takeLatest } from "redux-saga/effects";
/**
 * Internal dependencies.
 */
import {
  REQUEST_DELETE_FAQ_ITEMS,
  REQUEST_FAQ_ADD_NEW_QUESTION,
  REQUEST_GET_FAQ_ITEMS,
  UPDATE_FAQ_ITEM
} from "../constants/action-types";
import API from "../api/index";
import { getAllFAQItems, getCurrentQuestion } from "../selectors";
import {
  closeEditScreen,
  requestGetFaqItems,
  resetTypedQuestion,
  updateFaqItems,
  updateFaqModalVisibility,
  updateNotificationArea
} from "../actions";
import { transformAPIDataToUi } from "./filters";
import { faqEditItemType } from "../components/faq-edit-item";
import { FAQ_HIGHLIGHT_TEXT, FAQ_ITEMS_CHANGED } from "../constants/faq-hook-constants";
import { trigger } from "backbone";

/**
 * Dispatch notification when a event occurs on the store.
 * @param response
 * @return {Generator<<"CALL", CallEffectDescriptor>|<"PUT", PutEffectDescriptor<{type: *}>>, void, ?>}
 */
function* dispatchNotification(response) {
  const notificationAction = updateNotificationArea();
  notificationAction.payload = {
    notificationMessage: response.message,
    notificationType: response.status
  };
  yield put(notificationAction);
  /**
   * After 2 seconds, remove the notification.
   */
  yield delay(2000);
  notificationAction.payload = {
    notificationMessage: "",
    notificationType: ""
  };
  yield put(notificationAction);
}

function* handleAddNewQuestion(action) {
  const currentQuestion = yield select(getCurrentQuestion);
  const faqItems = [
    {
      question: currentQuestion,
      answer: ""
    }
  ];
  const response = yield call(API.saveFAQItems, faqItems);
  // Event emitted to global namespace in order to highlight text in the editor.
  trigger(FAQ_HIGHLIGHT_TEXT, {
    text: currentQuestion,
    isQuestion: true,
    id: response.id
  });
  yield dispatchNotification(response);
  yield put(resetTypedQuestion());
  // Refresh the screen by getting new FAQ items.
  yield put(requestGetFaqItems());
}

/**
 * Get the FAQ items from the API.
 * @return {Generator<*, void, ?>}
 */
function* handleGetFaqItems() {
  const faqItems = yield call(API.getFAQItems);
  const action = updateFaqItems();
  action.payload = transformAPIDataToUi(faqItems);
  trigger(FAQ_ITEMS_CHANGED, action.payload);
  yield put(action);
}

/**
 * Update the FAQ items when the user changes the data.
 * @param action
 * @return {Generator<<"CALL", CallEffectDescriptor>|*, void, ?>}
 */
function* handleUpdateFaqItems(action) {
  const faqItems = yield select(getAllFAQItems);
  const payload = action.payload;
  const faqItemIndex = faqItems.map(e => e.id).indexOf(payload.id);
  /**
   * Update the changed faq item to the API.
   */
  const changedFaqItem = faqItems[faqItemIndex];
  const changedFaqItems = [Object.assign({}, changedFaqItem)];
  switch (payload.type) {
    case faqEditItemType.ANSWER:
      changedFaqItems[0]["answer"] = payload.value;
      break;
    case faqEditItemType.QUESTION:
      changedFaqItems[0]["question"] = payload.value;
      break;
  }
  trigger(FAQ_HIGHLIGHT_TEXT, {
    text: payload.value,
    isQuestion: payload.type === faqEditItemType.QUESTION,
    id: faqItems[faqItemIndex].id
  });
  const response = yield call(API.updateFAQItems, changedFaqItems);
  yield dispatchNotification(response);
  yield put(requestGetFaqItems());
  // Close the modal on apply.
  const modalAction = updateFaqModalVisibility();
  modalAction.payload = false;
  yield put(modalAction);
}

/**
 * Delete Faq items.
 * @param action
 * @return {Generator<*, void, ?>}
 */
function* handleDeleteFaqItems(action) {
  // close the edit screen
  yield put(closeEditScreen());
  const allFaqItems = yield select(getAllFAQItems);
  const payload = action.payload;
  const faqItemIndex = allFaqItems.map(e => e.id).indexOf(payload.id);
  const deletedFaqItems = [allFaqItems[faqItemIndex]];
  const response = yield call(API.deleteFaqItems, deletedFaqItems);
  yield dispatchNotification(response);
  // Refresh the screen by getting new FAQ items.
  yield put(requestGetFaqItems());

}

function* rootSaga() {
  yield takeLatest(REQUEST_FAQ_ADD_NEW_QUESTION, handleAddNewQuestion);
  yield takeLatest(REQUEST_GET_FAQ_ITEMS, handleGetFaqItems);
  yield takeLatest(UPDATE_FAQ_ITEM, handleUpdateFaqItems);
  yield takeLatest(REQUEST_DELETE_FAQ_ITEMS, handleDeleteFaqItems);
}

export default rootSaga;
