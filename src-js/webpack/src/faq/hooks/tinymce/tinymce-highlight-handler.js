/**
 * TinyMceHighlightHandler handles the toolbar button.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import {on} from "backbone"
import {FAQ_EVENT_HANDLER_SELECTION_CHANGED} from "../../constants/faq-hook-constants";

class TinymceHighlightHandler {
  /**
   * Construct highlight handler instance.
   * @param editor The Tinymce editor instance.
   * @param store Redux store.
   */
  constructor(editor, store) {
    this.editor = editor;
    this.store = store;
    this.registerAnnotation();
  }

  /**
   * Register an tinymce annotation via the tinymce annotation
   * API.
   */
  registerAnnotation() {
    const editor = this.editor;

    editor.on("init", function() {
      editor.annotator.register("alpha", {
        persistent: true,
        decorate: function(uid, data) {
          return {
            attributes: {
              "data-mce-comment": data.comment ? data.comment : "",
              "data-mce-author": data.author ? data.author : "anonymous"
            }
          };
        }
      });
    });
  }


}


export default TinymceHighlightHandler