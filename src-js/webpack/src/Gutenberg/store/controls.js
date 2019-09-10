/* global wp */

import EditorOps from "../services/EditorOps";

const { apiFetch } = wp;

function PREPARE_ANALYSIS(action) {
  const { editor } = action.payload;

  const editorOps = new EditorOps(editor);

  return {
    editorOps,
    request: editorOps.buildAnalysisRequest(window["wlSettings"]["language"], [window["wordlift"]["currentPostUri"]])
  };
}

function FETCH_ANALYSIS(action) {
  return apiFetch({
    url: `${window["wlSettings"]["ajax_url"]}?action=wordlift_analyze`,
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(action.payload)
  });
}

function EMBED_ANALYSIS(action) {
  const { editorOps, response } = action.payload;

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

  return action.payload;
}

export default { PREPARE_ANALYSIS, FETCH_ANALYSIS, EMBED_ANALYSIS };
