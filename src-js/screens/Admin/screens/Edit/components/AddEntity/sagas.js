import { delay } from "redux-saga";
import { call, put, takeEvery, takeLatest } from "redux-saga/effects";

import {
  addEntityRequest,
  addEntitySuccess,
  createEntityRequest,
  createEntitySuccess,
  setValue,
  loadItemsSuccess,
  close
} from "./actions";
import { autocomplete } from "./api";
import EditPostWidgetController from "../../angular/EditPostWidgetController";

function* loadItems({ payload }) {
  yield call(delay, 500);

  console.trace(`Going to fetch data for ${payload}...`);
  const items = yield call(autocomplete, payload, "en");

  yield put(loadItemsSuccess(items));
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

const getMainType = types => {
  for (let i = 0; i < window.wordlift.types.length; i++) {
    const type = window.wordlift.types[i];

    if (-1 < types.indexOf(type.uri)) return type.slug;
  }
  return "thing";
};

function* saga() {
  yield takeLatest(setValue, loadItems);

  yield takeEvery(createEntityRequest, createEntity);

  yield takeEvery(addEntityRequest, addEntity);

  yield takeEvery(createEntitySuccess, requestClose);

  yield takeEvery(addEntitySuccess, requestClose);
}

export default saga;
