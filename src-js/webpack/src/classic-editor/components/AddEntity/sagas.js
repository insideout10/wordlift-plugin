/* global wp */

import { eventChannel } from "redux-saga";
import { call, delay, fork, put, race, select, take, takeEvery, takeLatest } from "redux-saga/effects";

import {
  addEntitySuccess,
  createEntityRequest,
  createEntitySuccess,
  setValue,
  loadItemsSuccess,
  close,
  open,
} from "./actions";
import { autocomplete } from "./api";
import EditPostWidgetController from "../../angular/EditPostWidgetController";

function* loadItems({ payload }) {
  if ("undefined" === typeof payload || "" === payload) return;

  yield delay(500);

  // eslint-disable-next-line
  "undefined" !== typeof wp.wordlift && wp.wordlift.trigger("loading", true);

  const language =
    // eslint-disable-next-line
    "undefined" !== typeof wlSettings.language ? wlSettings.language : "en";
  const items = yield call(autocomplete, payload, language);

  yield put(loadItemsSuccess(items));

  // eslint-disable-next-line
  "undefined" !== typeof wp.wordlift && wp.wordlift.trigger("loading", false);
}

function* requestClose() {
  yield put(close());
}

function editorSelectionChangedChannel() {
  return eventChannel((emitter) => {
    const hook = (params) => emitter(params);

    // eslint-disable-next-line
    wp.wordlift.on("editorSelectionChanged", hook);

    // eslint-disable-next-line
    return () => wp.wordlift.off("editorSelectionChanged", hook);
  });
}

function* watchForEditorSelectionChanges() {
  const channel = yield call(editorSelectionChangedChannel);

  while (true) {
    const { selection } = yield take(channel);
    yield put(setValue(selection));

    if ("" === selection) yield put(close());
  }
}

function* watchForSetValue() {
  yield takeLatest(setValue, loadItems);
}

function* saga() {
  yield fork(watchForEditorSelectionChanges);

  yield takeEvery(createEntitySuccess, requestClose);

  yield takeEvery(addEntitySuccess, requestClose);

  // Watch for setValue only after an open.
  while (true) {
    yield take(open);
    yield call(loadItems, yield select((state) => ({ payload: state.value })));

    yield race({
      task: call(watchForSetValue),
      cancel: take(close),
    });
  }
}

export default saga;
