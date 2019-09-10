import { eventChannel } from "redux-saga";
import { call, delay, fork, put, race, select, take, takeEvery, takeLatest } from "redux-saga/effects";

import {
  addEntityRequest,
  addEntitySuccess,
  createEntityRequest,
  createEntitySuccess,
  setValue,
  loadItemsSuccess,
  close,
  open
} from "./actions";
import { autocomplete } from "./api";
import EditPostWidgetController from "../../angular/EditPostWidgetController";

function* loadItems({ payload }) {
  if ("undefined" === typeof payload || "" === payload) return;

  yield call(delay, 500);

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

function* createEntity({ payload }) {
  const ctrl = EditPostWidgetController();

  ctrl.$apply(ctrl.setCurrentEntity(undefined, undefined, payload));

  yield put(createEntitySuccess());
}

function* addEntity({ payload }) {
  const ctrl = EditPostWidgetController();
  ctrl.$apply(() => {
    // Create the text annotation.
    ctrl.setCurrentEntity();
    // Update the entity data.
    ctrl.currentEntity.description = payload.descriptions[0];
    ctrl.currentEntity.id = payload.id;
    ctrl.currentEntity.images = payload.images;
    ctrl.currentEntity.label = payload.label;
    ctrl.currentEntity.mainType = getMainType(payload.types);
    ctrl.currentEntity.types = payload.types;
    ctrl.currentEntity.sameAs = payload.sameAss;
    // Save the entity.
    ctrl.storeCurrentEntity();
  });

  yield put(addEntitySuccess());
}

function* requestClose() {
  yield put(close());
}

function editorSelectionChangedChannel() {
  return eventChannel(emitter => {
    const hook = selection => emitter(selection);

    // eslint-disable-next-line
    wp.wordlift.on("editorSelectionChanged", hook);

    // eslint-disable-next-line
    return () => wp.wordlift.off("editorSelectionChanged", hook);
  });
}

function* watchForEditorSelectionChanges() {
  const channel = yield call(editorSelectionChangedChannel);

  while (true) {
    const value = yield take(channel);
    yield put(setValue(value));

    if ("" === value) yield put(close());
  }
}

function* watchForSetValue() {
  yield takeLatest(setValue, loadItems);
}

const getMainType = types => {
  for (let i = 0; i < window.wordlift.types.length; i++) {
    const type = window.wordlift.types[i];

    if (-1 < types.indexOf(type.uri)) return type.slug;
  }
  return "thing";
};

function* saga() {
  yield fork(watchForEditorSelectionChanges);

  yield takeEvery(createEntityRequest, createEntity);

  yield takeEvery(addEntityRequest, addEntity);

  yield takeEvery(createEntitySuccess, requestClose);

  yield takeEvery(addEntitySuccess, requestClose);

  // Watch for setValue only after an open.
  while (true) {
    yield take(open);
    yield call(loadItems, yield select(state => ({ payload: state.value })));

    yield race({
      task: call(watchForSetValue),
      cancel: take(close)
    });
  }
}

export default saga;
