import { delay } from "redux-saga";
import { call, put, takeLatest } from "redux-saga/effects";

import { loadItemsRequest, loadItemsSuccess } from "./actions";
import { autocomplete } from "./api";

function* loadItems({ payload }) {
  yield call(delay, 500);

  console.trace(`Going to fetch data for ${payload}...`);
  const items = yield call(autocomplete, payload, "en");

  yield put(loadItemsSuccess(items));
}

function* saga() {
  yield takeLatest(loadItemsRequest, loadItems);
}

export default saga;
