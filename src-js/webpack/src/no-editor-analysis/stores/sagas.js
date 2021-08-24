/**
 * This file contains the side effects managed via redux-sagas.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.6
 */

/**
 * External dependencies
 */
import {call, put, select, takeEvery, takeLatest} from "redux-saga/effects";
import React from "react";
import {doAction} from "@wordpress/hooks";
/**
 * Internal dependencies
 */
import {ADD_ENTITY, SET_CURRENT_ENTITY, TOGGLE_ENTITY,} from "../../Edit/constants/ActionTypes";
import {getEntity, getAllSelectedEntities} from "./selectors";
import {addEntityRequest, addEntitySuccess, createEntityRequest} from "../../Edit/components/AddEntity/actions";
import {requestAnalysis} from "../../block-editor/stores/actions";
import parseAnalysisResponse from "../../block-editor/stores/compat";
import {receiveAnalysisResults, updateOccurrencesForEntity} from "../../Edit/actions";
import {analysisStateChanged, syncFormData} from "../actions";
import {NO_EDITOR_SYNC_FORM_DATA} from "../actions/types";
import AnalysisStorage from "../analysis-storage";
import uuid from "../../Edit/uuid";

/**
 * Handle the {@link TOGGLE_ENTITY} action.
 *
 *  @param {{entity:{id}}} payload A payload containing an entity.
 */
function* toggleEntity(payload) {
  const entity = yield select(getEntity, payload.entity.id);
  if ( entity.occurrences.length === 0) {
    // turn on the entity
    yield put(updateOccurrencesForEntity(entity.id, ["placeholder-annotation"]));
  }
  else {
    yield put(updateOccurrencesForEntity(entity.id, []));
  }
  yield put(syncFormData())
}

function* handleRequestAnalysis() {
  const settings = global["wlSettings"];
  const canCreateEntities =
      "undefined" !== typeof settings["can_create_entities"] && "yes" === settings["can_create_entities"];
  const _wlMetaBoxSettings = global["_wlMetaBoxSettings"].settings;
  const request = {
    contentLanguage: settings["can_create_entities"]["language"],
        contentType: "text/html",
      scope: canCreateEntities ? "all" : "local",
      version: "1.0.0",
      content: "",
      exclude: [_wlMetaBoxSettings["currentPostUri"]]
  };

  yield put( analysisStateChanged(true) )

  const response = yield call(global["wp"].ajax.post, "wl_analyze", {
    _wpnonce: settings["analysis"]["_wpnonce"],
    data: JSON.stringify(request),
    postId: settings["post_id"]
  });
  yield put( analysisStateChanged(false) )
  const parsed = parseAnalysisResponse(response);
  yield put(receiveAnalysisResults(parsed));
  yield put(syncFormData())
}

function* addEntity({ payload }) {
  // Add them to the state and sync it.
  payload.occurrences = ["placeholder-annotation"]
  payload.id = payload.id ? payload.id : 'local-entity-' + uuid()
  yield put({type: ADD_ENTITY, payload: payload});
  yield put(addEntitySuccess());
  doAction("unstable_wordlift.closeEntitySelect")
  yield put(syncFormData())
}



function* handleSyncFormData() {
  const selectedEntities = yield select(getAllSelectedEntities)
  const storage = new AnalysisStorage("wl-no-editor-analysis-meta-box-storage");
  storage.syncData(selectedEntities)
}

function* handleCreateEntityRequest() {
  // Call the WP hook to close the entity select (see ../../Edit/components/AddEntity/index.js).
  doAction("unstable_wordlift.closeEntitySelect");
}

function* handleSetCurrentEntity({entity}) {
  const url = `${window["wp"].ajax.settings.url}?action=wordlift_redirect&uri=${encodeURIComponent(entity.id)}&to=edit`;
  window.open(url, "_blank");
}

/**
 * Connect the side effects.
 */
function* sagas() {
  yield takeEvery(TOGGLE_ENTITY, toggleEntity);
  yield takeLatest(requestAnalysis, handleRequestAnalysis);
  yield takeEvery(addEntityRequest, addEntity);
  yield takeEvery(NO_EDITOR_SYNC_FORM_DATA, handleSyncFormData)
  yield takeEvery(createEntityRequest, handleCreateEntityRequest);
  yield takeEvery(SET_CURRENT_ENTITY, handleSetCurrentEntity)
}

export default sagas;
