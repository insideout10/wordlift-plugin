/* global wp */

import { call, put, select, takeEvery, takeLatest } from "redux-saga/effects";

const { apiFetch } = wp;

/**
 * Legacy actions.
 */
import { receiveAnalysisResults } from "../../Edit/actions";
import { TOGGLE_ENTITY } from "../../Edit/constants/ActionTypes";

import actions from "./actions";
import { getEditor } from "./selectors";
import parseAnalysisResponse from "./compat";
import EditorOps from "../api/EditorOps";

function* selectEditor(action) {
  const editor = action.payload;

  yield put(actions.selectEditorSucceeded(new EditorOps(editor)));
}

function* requestAnalysis() {
  const editorOps = yield select(getEditor);

  const request = editorOps.buildAnalysisRequest(window["wlSettings"]["language"], [
    window["wordlift"]["currentPostUri"]
  ]);

  const response = yield call(apiFetch, {
    url: `${window["wlSettings"]["ajax_url"]}?action=wordlift_analyze`,
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(request)
  });

  embedAnalysis(editorOps, response);

  yield put(receiveAnalysisResults(parseAnalysisResponse(window["wordlift"], response)));
}

function embedAnalysis(editorOps, response) {
  const annotations = Object.values(response.annotations).sort(function(a1, a2) {
    if (a1.end > a2.end) return -1;
    if (a1.end < a2.end) return 1;

    return 0;
  });

  annotations.forEach(annotation => {
    const fragment = '<span id="urn:' + annotation.annotationId + '"' + ' class="textannotation">';

    editorOps.insertHtml(annotation.end, "</span>");
    editorOps.insertHtml(annotation.start, fragment);
  });

  editorOps.applyChanges();
}

function* toggleEntity(entity) {
  const editorOps = yield select(getEditor);

  console.info({ toggleEntity: entity });
}

export default function* saga() {
  yield takeLatest(actions.selectEditor, selectEditor);
  yield takeLatest([actions.selectEditorSucceeded, actions.requestAnalysis], requestAnalysis);
  yield takeEvery(TOGGLE_ENTITY, toggleEntity);
}
