/**
 * This file provides the saga for the mapping list screen.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import { call, put, takeLatest, select } from "redux-saga/effects";

/**
 * Internal dependencies
 */
import API from "../api/api";
import {
  MAPPING_ITEMS_BULK_APPLY,
  MAPPING_LIST_CHANGED,
  MAPPINGS_REQUEST,
  MAPPINGS_REQUEST_CLONE_MAPPINGS,
  MAPPINGS_REQUEST_DELETE_OR_UPDATE
} from "../actions/action-types";
import {
  MAPPING_ITEMS_BULK_APPLY_ACTION,
  MAPPINGS_REQUEST_ACTION,
  MAPPINGS_RESET_UI_AFTER_BULK_APPLY_ACTION
} from "../actions/actions";
import { getSelectedBulkOption, getSelectedMappingItems } from "./selectors";
import { BULK_OPTIONS } from "../components/bulk-action-sub-components";
import { ACTIVE_CATEGORY, TRASH_CATEGORY } from "../components/category-component";

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
function* requestUpdateOrDeleteMappings(action) {
  yield call(API.deleteOrUpdateMappings, action.payload.type, action.payload.mappingItems);
  // Refresh the screen by fetching the new data.
  yield put(MAPPINGS_REQUEST_ACTION);
}

/**
 * Calls the REST API to clone the mappings.
 * @param action MAPPINGS_REQUEST_CLONE_MAPPINGS action
 * @returns {Generator<*, void, ?>}
 */
function* requestCloneMappings(action) {
  yield call(API.cloneMappings, action.payload.mappingItems);
  // Refresh the screen by fetching the new data.
  yield put(MAPPINGS_REQUEST_ACTION);
}

/**
 * When the bulk action is made, send the request to API depending on the chosen
 * bulk action, after that dispatch to store to reset the config.
 * @param action {Object} MAPPING_ITEMS_BULK_APPLY_ACTION
 * @returns void
 */
function* requestMappingsBulkAction(action) {
  const selectedBulkOption = yield select(getSelectedBulkOption);
  let selectedMappingItems = yield select(getSelectedMappingItems);
  if (selectedBulkOption === BULK_OPTIONS.TRASH) {
    selectedMappingItems = selectedMappingItems.map(el => ({ ...el, mappingStatus: TRASH_CATEGORY }));
    yield call(API.deleteOrUpdateMappings, "PUT", selectedMappingItems);
  }
  if (selectedBulkOption === BULK_OPTIONS.RESTORE) {
    selectedMappingItems = selectedMappingItems.map(el => ({ ...el, mappingStatus: ACTIVE_CATEGORY }));
    yield call(API.deleteOrUpdateMappings, "PUT", selectedMappingItems);
  }
  if (selectedBulkOption === BULK_OPTIONS.DELETE_PERMANENTLY) {
    yield call(API.deleteOrUpdateMappings, "DELETE", selectedMappingItems);
  }
  if (selectedBulkOption === BULK_OPTIONS.DUPLICATE) {
    yield call(API.cloneMappings, selectedMappingItems);
  }
  yield put(MAPPINGS_RESET_UI_AFTER_BULK_APPLY_ACTION);
  yield put(MAPPINGS_REQUEST_ACTION);
}

function* saga() {
  yield takeLatest(MAPPINGS_REQUEST, requestMappings);
  yield takeLatest(MAPPINGS_REQUEST_DELETE_OR_UPDATE, requestUpdateOrDeleteMappings);
  yield takeLatest(MAPPINGS_REQUEST_CLONE_MAPPINGS, requestCloneMappings);
  yield takeLatest(MAPPING_ITEMS_BULK_APPLY, requestMappingsBulkAction);
}

export default saga;
