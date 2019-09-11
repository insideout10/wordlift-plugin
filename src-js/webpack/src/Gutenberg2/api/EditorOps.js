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

  insertAnnotation(id, start, end) {
    const block = this._blocksOps.getBlock(start);
    const endBlock = this._blocksOps.getBlock(end);

    // @@todo: add support for different blocks, i.e. an annotation which crosses boundaries.
    if (false === block || false === endBlock || block !== endBlock) return false;

    const relativeStart = start - block.start,
      relativeEnd = end - block.start;

    block.insertHtml(relativeEnd, "</span>");
    block.insertHtml(relativeStart, `<span id="urn:${id}" class="textannotation">`);

    this._annotations[id] = block;
  }

  toggleAnnotation() {
    // ed.dom.addClass annotationId, "disambiguated"
    // for type in configuration.types
    //   ed.dom.removeClass annotationId, type.css
    // ed.dom.removeClass annotationId, "unlinked"
    // ed.dom.addClass annotationId, "wl-#{entity.mainType}"
    // discardedItemId = ed.dom.getAttrib annotationId, "itemid"
    // ed.dom.setAttrib annotationId, "itemid", entity.id
    // discardedItemId
  }

  switchOnAnnotation(id) {

  }

  switchOffAnnotation() {}

  applyChanges() {
    this._blocksOps.applyChanges();
  }
}
