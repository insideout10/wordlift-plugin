/**
 *
 */

import { takeLatest } from "redux-saga/effects";

function* fetchUser() {
    yield null;
}

function* saga() {
  yield takeLatest("USER_FETCH_REQUESTED", fetchUser);
}

export default saga;
