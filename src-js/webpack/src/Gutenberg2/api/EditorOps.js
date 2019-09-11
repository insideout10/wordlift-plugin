/* global wp */

import BlocksOps from "./BlocksOps";

const { dispatch, select } = wp.data;

export default class EditorOps {
  constructor(editor) {
    // Holds the annotations to blocks mappings.
    this._annotations = {};
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

  insertAnnotation(id, start, end) {
    const block = this._blocksOps.getBlock(start);
    const endBlock = this._blocksOps.getBlock(end);

    // @@todo: add support for different blocks, i.e. an annotation which crosses boundaries.
    if (false === block || false === endBlock || block !== endBlock) return false;

    block.insertHtml(end, '</span>');
    block.insertHtml(start, '<span id="urn:${id} class="textannotation">');

    this._annotations[id] = block;
  }
}
