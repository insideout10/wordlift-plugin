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
import EDIT_MAPPING_API from "./edit-mapping-api";
import {EDIT_MAPPING_TERMS_FETCHED_FOR_TAXONOMY_ACTION, MAPPING_TERMS_CHANGED_ACTION} from "../actions/actions";
import {EDIT_MAPPING_REQUEST_TERMS} from "../actions/action-types";

function* getTermsForSelectedTaxonomy(action) {
    // Mark the taxonomy as terms fetched, since we dont want to send another request to get the same terms.
    EDIT_MAPPING_TERMS_FETCHED_FOR_TAXONOMY_ACTION.payload = {
        taxonomy: action.payload.taxonomy
    };
    yield put( EDIT_MAPPING_TERMS_FETCHED_FOR_TAXONOMY_ACTION )

    const response  = yield call(EDIT_MAPPING_API.getTermsFromAPI, action.payload.taxonomy)
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
    yield put(MAPPING_TERMS_CHANGED_ACTION)
}

function* editMappingSaga() {
    yield takeLatest(EDIT_MAPPING_REQUEST_TERMS, getTermsForSelectedTaxonomy)
}

export default editMappingSaga;