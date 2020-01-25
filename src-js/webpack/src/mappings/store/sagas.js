/**
 *
 */

/**
 * External dependencies
 */
import { call, put, takeLatest, select } from "redux-saga/effects";

/**
 * Internal dependencies
 */
import API from "./api";
import {
  MAPPING_ITEMS_BULK_SELECT,
  MAPPING_LIST_CHANGED,
  MAPPINGS_REQUEST,
  MAPPINGS_REQUEST_CLONE_MAPPINGS,
  MAPPINGS_REQUEST_DELETE_OR_UPDATE
} from "../actions/action-types";
import {MAPPING_ITEMS_BULK_ACTION, MAPPINGS_REQUEST_ACTION} from "../actions/actions";
import {getSelectedBulkOption, getSelectedMappingItems} from "./selectors";

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

/**
 * When the bulk action is made, send the request to API depending on the chosen
 * bulk action, after that dispatch to store to reset the config.
 * @param action {Object} MAPPING_ITEMS_BULK_ACTION
 * @returns void
 */
function* requestMappingsBulkAction( action ) {
  const selectedBulkOption = yield select( getSelectedBulkOption );
  const selectedMappingItems = yield select( getSelectedMappingItems );
  if ( selectedBulkOption === 'trash' )
  {
    yield call( API.deleteOrUpdateMappings, 'PUT', selectedMappingItems )
  }
  yield put( MAPPINGS_REQUEST_ACTION )
}

function* saga() {
  yield takeLatest(MAPPINGS_REQUEST, requestMappings);
  yield takeLatest(MAPPINGS_REQUEST_DELETE_OR_UPDATE, requestUpdateOrDeleteMappings);
  yield takeLatest(MAPPINGS_REQUEST_CLONE_MAPPINGS, requestCloneMappings);
  yield takeLatest(MAPPING_ITEMS_BULK_SELECT, requestMappingsBulkAction);
}

export default saga;
