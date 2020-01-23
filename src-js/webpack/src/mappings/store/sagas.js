/**
 *
 */

/**
 * External dependencies
 */
import { call, put, takeLatest } from "redux-saga/effects";

/**
 * Internal dependencies
 */
import API from "./api";
import { MAPPING_LIST_CHANGED } from "../actions/action-types";

/**
 * Calls the REST API to retrieve the mappings.
 */
function* requestMappings() {
  const mappings = yield call(API.getMappings);

  yield put({ type: MAPPING_LIST_CHANGED, payload: { value: mappings } });
}

function* saga() {
  yield takeLatest("MAPPINGS_REQUEST", requestMappings);
}

export default saga;
