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
}

function* setCurrentEntity({ entity }) {
  // Call the `EditPostWidgetController` to set the current entity.
  //EditPostWidgetController().$apply(EditPostWidgetController().setCurrentEntity(entity, "entity"));
}

function* addEntity({ payload }) {
  // TODO: add some code for the entity.

  yield put(addEntitySuccess());
}

function* createEntity({ payload }) {

  // TODO: add the code to create entity in DOM.

  yield put(createEntitySuccess());
}

export const getMainType = types => {
  for (let i = 0; i < window._wlEntityTypes.length; i++) {
    const type = window._wlEntityTypes[i];

    if (-1 < types.indexOf(type.uri)) return type.slug;
  }
  return "thing";
};

let popover;

function* handleEditorSelectionChanged({ payload }) {
  yield delay(300);

  console.log("handleEditorSelectionChanged", payload);
  const editor = payload.editor;

  // Get the selection. Bail out is the selection is collapsed (is just a caret).
  const selection = editor.selection;
  if (selection.isCollapsed() || "" === selection.getContent({ format: "text" })) {
    if (popover) popover.unmount();
    return;
  }

  // Get the selection range and bail out if it's null.
  const range = selection.getRng();
  if (null == range) {
    if (popover) popover.unmount();
    return;
  }

  // Get the editor's selection bounding rect. The rect's coordinates are relative to TinyMCE's editor's iframe.
  const editorRect = range.getBoundingClientRect();

  // Get TinyMCE's iframe element's bounding rect.
  const iframe = editor.iframeElement;
  const iframeRect = iframe.getBoundingClientRect();

  // Calculate our target rect by summing the iframe and the editor rects along with the window's scroll positions.
  const rect = {
    top: iframeRect.top + editorRect.top + window.scrollY,
    right: iframeRect.left + editorRect.right + window.scrollX,
    bottom: iframeRect.top + editorRect.bottom + window.scrollY,
    left: iframeRect.left + editorRect.left + window.scrollX
  };
}

/**
 * Connect the side effects.
 */
function* sagas() {
  yield takeEvery(TOGGLE_ENTITY, toggleEntity);
  yield takeLatest(requestAnalysis, handleRequestAnalysis);
  yield takeEvery(SET_CURRENT_ENTITY, setCurrentEntity);
  yield takeEvery(addEntityRequest, addEntity);
  yield takeEvery(createEntityRequest, createEntity);
  yield takeLatest(EDITOR_SELECTION_CHANGED, handleEditorSelectionChanged);
}

export default sagas;
