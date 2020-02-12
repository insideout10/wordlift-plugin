/**
 * TinyMceFaqHook implements the editor hook, and handles the tinymce text editor.
 * NOTE: this hooks is called when the tinymce editor is used, so there is no need
 * to check for edge cases since we will have an activeEditor
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import FaqTextEditorHook from "../interface/faq-text-editor-hook";
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED } from "../../constants/faq-hook-constants";
import { trigger } from "backbone";
import FaqValidator from "../validators/faq-validator";

export const FAQ_TINY_MCE_PLUGIN_NAME = "wl_faq";

class TinymceFaqHook extends FaqTextEditorHook {
  constructor() {
    super();
    /**
     * Store the last emitted text from this hook, to prevent duplication we use
     * this reference to compare the string.
     * @type {string}
     * @private
     */
    this._lastEmittedSelection = "";
  }

  listenForTextSelection() {
    const editor = window["tinymce"].get()[0];
    editor.on("NodeChange", e => {
      /**
       * To prevent the multiple events getting emitted for the same
       * selected text, we are checking if the same text was posted last time
       */
      const selectedText = editor.selection.getContent({ format: "text" });
      if (selectedText !== this._lastEmittedSelection && selectedText.length > 0) {
        this._lastEmittedSelection = selectedText;
      }

    });
  }
}

export default TinymceFaqHook;
