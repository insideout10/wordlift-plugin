/**
 * This file provides the sagas for the edit mappings screen.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import { call, put, takeLatest, select } from "redux-saga/effects";

/**
 * Internal dependencies
 */
import EDIT_MAPPING_API from "../api/edit-mapping-api";
import {
  EDIT_MAPPING_SAVE_MAPPING_ITEM_ACTION,
  EDIT_MAPPING_TERMS_FETCHED_FOR_TAXONOMY_ACTION,
  MAPPING_HEADER_CHANGED_ACTION,
  MAPPING_ID_CHANGED_FROM_API_ACTION,
  MAPPING_TERMS_CHANGED_ACTION,
  NOTIFICATION_CHANGED_ACTION,
  PROPERTY_LIST_CHANGED_ACTION,
  RULE_GROUP_LIST_CHANGED_ACTION
} from "../actions/actions";
import {
  EDIT_MAPPING_REQUEST_MAPPING_ITEM,
  EDIT_MAPPING_REQUEST_TERMS,
  EDIT_MAPPING_SAVE_MAPPING_ITEM
} from "../actions/action-types";
import EditComponentMapping from "../mappings/edit-component-mapping";

function* getTermsForSelectedTaxonomy(action) {
  // Mark the taxonomy as terms fetched, since we dont want to send another request to get the same terms.
  EDIT_MAPPING_TERMS_FETCHED_FOR_TAXONOMY_ACTION.payload = {
    taxonomy: action.payload.taxonomy
  };
  yield put(EDIT_MAPPING_TERMS_FETCHED_FOR_TAXONOMY_ACTION);

  const response = yield call(EDIT_MAPPING_API.getTermsFromAPI, action.payload.taxonomy);
  const terms = response.map(e => {
    return {
      label: e.name,
      value: e.slug,
      taxonomy: e.taxonomy
    };
  });
  MAPPING_TERMS_CHANGED_ACTION.payload = {
    taxonomy: action.payload.taxonomy,
    terms: terms
  };
  yield put(MAPPING_TERMS_CHANGED_ACTION);
}

function* saveMappingItem(action) {
  const { mappingData } = action.payload;
  const { mapping_id, message, status } = yield call(EDIT_MAPPING_API.saveMappingItem, mappingData);
  MAPPING_ID_CHANGED_FROM_API_ACTION.payload = {
    mappingId: parseInt(mapping_id)
  };
  yield put(MAPPING_ID_CHANGED_FROM_API_ACTION);
  // Send notification after saving.
  window !== undefined ? window.scrollTo(0, 0) : undefined;
  NOTIFICATION_CHANGED_ACTION.payload = {
    message: message,
    type: status
  };
  yield put(NOTIFICATION_CHANGED_ACTION);
}

function* getMappingItem(action) {
  const { mappingId } = action.payload;
  const data = yield call(EDIT_MAPPING_API.getMappingItemByMappingId, mappingId);
  MAPPING_HEADER_CHANGED_ACTION.payload = {
    title: data.mapping_title,
    mapping_id: data.mapping_id
  };
  yield put(MAPPING_HEADER_CHANGED_ACTION);

  PROPERTY_LIST_CHANGED_ACTION.payload = {
    value: EditComponentMapping.mapPropertyAPIKeysToUi(data.property_list)
  };
  yield put(PROPERTY_LIST_CHANGED_ACTION);

  RULE_GROUP_LIST_CHANGED_ACTION.payload = {
    value: EditComponentMapping.mapRuleGroupListAPIKeysToUi(data.rule_group_list)
  };
  yield put(RULE_GROUP_LIST_CHANGED_ACTION);
}
function* editMappingSaga() {
  yield takeLatest(EDIT_MAPPING_REQUEST_TERMS, getTermsForSelectedTaxonomy);
  yield takeLatest(EDIT_MAPPING_SAVE_MAPPING_ITEM, saveMappingItem);
  yield takeLatest(EDIT_MAPPING_REQUEST_MAPPING_ITEM, getMappingItem);
}

export default editMappingSaga;
