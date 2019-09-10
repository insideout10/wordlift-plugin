/* global wp */

import BlocksOps from "./BlocksOps";

const { dispatch, select } = wp.data;

export default class EditorOps {
  constructor(editor) {
    this._editor = select(editor);
    this._blocksOps = new BlocksOps(this._editor, dispatch(editor));
  }

  buildAnalysisRequest(language, exclude) {
    return {
      contentLanguage: language,
      contentType: "text/html",
      scope: "all",
      version: "1.0.0",
      content: this._blocksOps.getHtml(),
      exclude: exclude
    };
  }

  insertHtml(at, fragment) {
    this._blocksOps.insertHtml(at, fragment);
  }

  applyChanges() {
    this._blocksOps.applyChanges();
  }
}
