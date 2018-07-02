import { takeEvery } from "redux-saga";
import { put } from "redux-saga/effects";
import { selectNode, clickTile } from "../actions";

function* handleTileClick({ payload }) {
  yield put(selectNode(payload.data));
}

function* root() {
  yield takeEvery(clickTile, handleTileClick);
}

export default root;
