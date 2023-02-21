/**
 * WordPress dependencies
 */
import {dispatch, select} from "@wordpress/data";

/**
 * Internal dependencies
 */
import {Blocks} from "./blocks";

export default class EditorOps {
  constructor(editor) {
    // Holds the annotations to blocks mappings.
    this._annotations = {};
    this._editor = select(editor);
    /** @field {Blocks} _blocks */
    this._blocks = Blocks.create(this._editor.getBlocks(), dispatch(editor));
  }

  buildAnalysisRequest(language, exclude, canCreateEntities) {
    var request = {
      contentLanguage: language,
      contentType: "text/html",
      scope: canCreateEntities ? "all" : "local",
      version: "1.0.0",
      content: this._blocks.html,
      exclude: exclude,
    };

    if (window['wlSettings']?.post_id) {
      request['post_id'] = window['wlSettings']?.post_id;
    }

    return request;
  }

  insertAnnotation(id, start, end) {
    const block = this._blocks.getBlock(start);
    const endBlock = this._blocks.getBlock(end);

    // @@todo: add support for different blocks, i.e. an annotation which crosses boundaries.
    if (false === block || false === endBlock || block
        !== endBlock) {
      return false;
    }

    const relativeStart = start - block.start,
        relativeEnd = end - block.start;

    console.log("EditorOps.insertAnnotation",
        {id, start, end, clientId: block.clientId});

    // Insert the block only if not found.
    if (-1 === block.content.indexOf(`<span id="${id}" `)) {
      block.insertHtml(relativeEnd, "</span>");
      block.insertHtml(relativeStart,
          `<span id="${id}" class="textannotation">`);
    }

    this._annotations[id] = block;
  }

  applyChanges() {
    this._blocks.apply();
  }
}
