/* global wp */

import { call, put, takeEvery, takeLatest } from "redux-saga/effects";

const { apiFetch } = wp;
const { select, dispatch } = wp.data;

/**
 * Legacy actions.
 */
import { receiveAnalysisResults, updateOccurrencesForEntity } from "../../Edit/actions";
import { TOGGLE_ENTITY } from "../../Edit/constants/ActionTypes";

import actions from "./actions";
import { getEditor } from "./selectors";
import parseAnalysisResponse from "./compat";
import { EDITOR_STORE } from "../constants";
import EditorOps from "../api/EditorOps";
import { collectBlocks, mergeArray, switchOn } from "../api/utils";
import BlockOps from "../api/BlockOps";
import { Blocks } from "../api/Blocks";

// function* selectEditor(action) {
//   const editor = action.payload;
//
//   yield put(actions.selectEditorSucceeded(new EditorOps(editor)));
// }

function* requestAnalysis() {
  const editorOps = new EditorOps(EDITOR_STORE);

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

  annotations.forEach(annotation =>
    editorOps.insertAnnotation(annotation.annotationId, annotation.start, annotation.end)
  );

  editorOps.applyChanges();
}

function* toggleEntity({ entity }) {
  // Get the supported blocks.
  const blocks = Blocks.create(select(EDITOR_STORE).getBlocks(), dispatch(EDITOR_STORE));

  const onClassNames = ["disambiguated", `wl-${entity.mainType.replace(/\s/, "-")}`];

  const annotationSelector = Object.values(entity.annotations)
    .map(annotation => annotation.annotationId)
    .join("|");

  // Collect the annotations that have been switch on/off.
  const occurrences = [];

  if (0 === entity.occurrences.length) {
    // Switch on.
    blocks.replace(
      new RegExp(`<span\\s+id="(${annotationSelector})"\\sclass="([^"]*)">`),
      (match, annotationId, classNames) => {
        const newClassNames = mergeArray(classNames.split(/\s+/), onClassNames).join(" ");
        occurrences.push(annotationId);
        return `<span id="${annotationId}" class="${newClassNames}" itemid="${entity.id}">`;
      }
    );
  } else {
    // Switch off.
    blocks.replace(
      new RegExp(`<span\\s+id="(${annotationSelector})"\\sclass="([^"]*)"\\sitemid="[^"]*">`),
      (match, annotationId, classNames) => {
        const newClassNames = classNames.split(/\s+/).filter(x => -1 === onClassNames.indexOf(x));
        return `<span id="${annotationId}" class="${newClassNames}">`;
      }
    );
  }

  yield put(updateOccurrencesForEntity(entity.id, occurrences));

  // Apply the changes.
  blocks.apply();
  //
  // Object.values(entity.annotations).forEach(annotation => {
  //   switchOn(blocks, dispatch(EDITOR_STORE), annotation.annotationId, entity.mainType, entity.id);
  // });
  //
  console.info({ toggleEntity: entity });
}

export default function* saga() {
  // yield takeLatest(actions.selectEditor, selectEditor);
  // yield takeLatest([actions.selectEditorSucceeded, actions.requestAnalysis], requestAnalysis);
  yield takeLatest(actions.requestAnalysis, requestAnalysis);
  yield takeEvery(TOGGLE_ENTITY, toggleEntity);
}
