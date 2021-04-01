/**
 * This file provides the sagas for the edit mappings screen.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import { call, put, takeEvery, takeLatest } from "redux-saga/effects";
/**
 * Internal dependencies
 */
import EDIT_MAPPING_API from "../api/edit-mapping-api";
import {
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
import EditComponentFilters from "../filters/edit-component-filters";
import {getOptionsFromApi, getRuleFieldOneOptionByValue} from "./selectors";
import editMappingStore from "./edit-mapping-store";

function* getTermsForSelectedTaxonomy(action) {
  const ruleFieldOneSelectedValue = action.payload.value;
  const parentOptions = getRuleFieldOneOptionByValue(editMappingStore.getState(), ruleFieldOneSelectedValue);
  // If there is no rule field one option or more than one option, then return
  if (parentOptions.length !== 1) {
    return;
  }
  // Now we have a option, check the api source.
  const parentOption = parentOptions[0];

  // If there is no api source, then dont send the request.
  if (parentOption.apiSource === undefined || parentOption.apiSource === "") {
    return;
  }

  const existingOptions = getOptionsFromApi(editMappingStore.getState(), ruleFieldOneSelectedValue);

  if (0 !== existingOptions.length) {
    // It means the terms are already present for the taxonomy, not needed to fetch it again from API.
    return;
  }

  const callAPILink = ruleFieldOneSelectedValue === 'post_taxonomy' ?  EDIT_MAPPING_API.getTaxonomyTermsFromAPI : EDIT_MAPPING_API.getTermsFromAPI;
  const response = yield call(callAPILink, ruleFieldOneSelectedValue);
  const terms =  response.map(e => {
    if (e.hasOwnProperty('group_name')) {
      return {
        group_name: e.group_name,
        group_options: e.group_options,
        parentValue: e.parentValue
      }
    } else {
      return {
        label: e.name,
        value: e.slug,
        parentValue: e.taxonomy
      };
    }
  });

  MAPPING_TERMS_CHANGED_ACTION.payload = {
    taxonomy: ruleFieldOneSelectedValue,
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

function* loadTermOptions(ruleGroupList) {
  const taxonomies = EditComponentFilters.getUniqueTaxonomiesSelected(ruleGroupList);
  for (let taxonomy of taxonomies) {
    yield put({
      type: EDIT_MAPPING_REQUEST_TERMS,
      payload: {
        value: taxonomy
      }
    });
  }
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
    value: EditComponentFilters.mapPropertyAPIKeysToUi(data.property_list)
  };
  yield put(PROPERTY_LIST_CHANGED_ACTION);

  const ruleGroupList = EditComponentFilters.mapRuleGroupListAPIKeysToUi(data.rule_group_list);
  RULE_GROUP_LIST_CHANGED_ACTION.payload = {
    value: ruleGroupList
  };

  yield call(loadTermOptions, ruleGroupList);

  yield put(RULE_GROUP_LIST_CHANGED_ACTION);
}

function* editMappingSaga() {
  yield takeEvery(EDIT_MAPPING_REQUEST_TERMS, getTermsForSelectedTaxonomy);
  yield takeLatest(EDIT_MAPPING_SAVE_MAPPING_ITEM, saveMappingItem);
  yield takeLatest(EDIT_MAPPING_REQUEST_MAPPING_ITEM, getMappingItem);
}

export default editMappingSaga;
