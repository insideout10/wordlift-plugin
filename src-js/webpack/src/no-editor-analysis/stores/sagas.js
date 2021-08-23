/**
 * This file contains the side effects managed via redux-sagas.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */
import {call, delay, put, select, takeEvery, takeLatest} from "redux-saga/effects";
/**
 * Internal dependencies
 */
import {
  ADD_ENTITY,
  EDITOR_SELECTION_CHANGED,
  SET_CURRENT_ENTITY,
  TOGGLE_ENTITY,
} from "../../Edit/constants/ActionTypes";
import { getEntity } from "./selectors";
import {
  addEntityRequest,
  addEntitySuccess,
  createEntityRequest,
  createEntitySuccess
} from "../../Edit/components/AddEntity/actions";
import React from "react";
import {requestAnalysis} from "../../block-editor/stores/actions";
import parseAnalysisResponse from "../../block-editor/stores/compat";
import {receiveAnalysisResults, updateOccurrencesForEntity} from "../../Edit/actions";
import {syncFormData} from "../actions";
import {NO_EDITOR_SYNC_FORM_DATA} from "../actions/types";
import AnalysisStorage from "../analysis-storage";
import {getAllSelectedEntities} from "../selectors";
import {doAction} from "@wordpress/hooks";

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

  const response = yield call(global["wp"].ajax.post, "wl_analyze", {
    _wpnonce: settings["analysis"]["_wpnonce"],
    data: JSON.stringify(request),
    postId: settings["post_id"]
  });
  const parsed = parseAnalysisResponse(response);
  yield put(receiveAnalysisResults(parsed));
  yield put(syncFormData())
}

function* addEntity({ payload }) {
  // Add them to the state and sync it.
  payload.occurrences = ["placeholder-annotation"]
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

/**
 * Connect the side effects.
 */
function* sagas() {
  yield takeEvery(TOGGLE_ENTITY, toggleEntity);
  yield takeLatest(requestAnalysis, handleRequestAnalysis);
  yield takeEvery(addEntityRequest, addEntity);
  yield takeEvery(NO_EDITOR_SYNC_FORM_DATA, handleSyncFormData)
}

export default sagas;
