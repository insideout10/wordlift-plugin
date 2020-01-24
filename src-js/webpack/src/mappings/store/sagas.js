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
import {
  MAPPING_LIST_CHANGED,
  MAPPINGS_REQUEST,
  MAPPINGS_REQUEST_CLONE_MAPPINGS,
  MAPPINGS_REQUEST_DELETE_OR_UPDATE
} from "../actions/action-types";
import {MAPPINGS_REQUEST_ACTION} from "../actions/actions";

/**
 * Calls the REST API to retrieve the mappings.
 */
function* requestMappings() {
  const mappings = yield call(API.getMappings);
  yield put({ type: MAPPING_LIST_CHANGED, payload: { value: mappings } });
}

/**
 * Calls the REST API to update/delete the mappings.
 * @param action MAPPINGS_REQUEST_DELETE_OR_UPDATE action
 * @returns {Generator<*, void, ?>}
 */
function* requestUpdateOrDeleteMappings( action ) {
  yield call(API.deleteOrUpdateMappings, action.payload.type, action.payload.mappingItems);
  // Refresh the screen by fetching the new data.
  yield put( MAPPINGS_REQUEST_ACTION );
}

/**
 * Calls the REST API to clone the mappings.
 * @param action MAPPINGS_REQUEST_CLONE_MAPPINGS action
 * @returns {Generator<*, void, ?>}
 */
function* requestCloneMappings( action ) {
  yield call(API.cloneMappings, action.payload.mappingItems);
  // Refresh the screen by fetching the new data.
  yield put( MAPPINGS_REQUEST_ACTION );
}


function* saga() {
  yield takeLatest(MAPPINGS_REQUEST, requestMappings);
  yield takeLatest(MAPPINGS_REQUEST_DELETE_OR_UPDATE, requestUpdateOrDeleteMappings)
  yield takeLatest(MAPPINGS_REQUEST_CLONE_MAPPINGS, requestCloneMappings)
}

export default saga;
