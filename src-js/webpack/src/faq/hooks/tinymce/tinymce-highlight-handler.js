/**
 * TinyMceHighlightHandler handles the toolbar button.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import { FAQ_ITEMS_CHANGED } from "../../constants/faq-hook-constants";
import {on} from "backbone"

const QUESTION_HIGHLIGHT_COLOR = "#00ff00";

const ANSWER_HIGHLIGHT_COLOR = "#00FFFF";

class TinymceHighlightHandler {
  /**
   * Construct highlight handler instance.
   * @param editor The Tinymce editor instance.
   * @param store Redux store.
   */
  constructor(editor, store) {
    this.editor = editor;
    this.store = store;
    this.highlightWhenStoreDataChange()
  }

  highlightWhenStoreDataChange() {
    on(FAQ_ITEMS_CHANGED, faqItems => {
      faqItems.map(e => {
        this.highlightSelectedText(e.question);
        this.highlightSelectedText(e.answer);
      });
    });
  }

  highlightSelectedText(selectedText) {
    const editor = this.editor;
    const highlightClassName = global["_wlFaqSettings"]["faqHighlightClass"];
    editor.selection.setContent(
      "<span class='" + highlightClassName + "' style='background-color: #ffff00;'>" + selectedText + "</span>"
    );
  }
}

export default TinymceHighlightHandler;
