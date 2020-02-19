/**
 * TinyMceHighlightHandler handles the toolbar button.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import { FAQ_HIGHLIGHT_TEXT, FAQ_ITEMS_CHANGED } from "../../constants/faq-hook-constants";
import { on } from "backbone";
import { classExtractor } from "../../../mappings/blocks/helper";

const QUESTION_HIGHLIGHT_COLOR = "#00ff00";

const ANSWER_HIGHLIGHT_COLOR = "#00FFFF";

class TinymceHighlightHandler {
  /**
   * Construct highlight handler instance.
   * @param editor {tinymce.Editor} The Tinymce editor instance.
   * @param store Redux store.
   */
  constructor(editor, store) {
    this.editor = editor;
    this.store = store;
    /**
     * Listen for highlighting events, then highlight the text.
     * Expected object from the event
     * {
     *     text: string,
     *     isQuestion:Boolean
     * }
     */
    on(FAQ_HIGHLIGHT_TEXT, result => {
      this.highlightSelectedText(result.text, result.isQuestion);
    });
  }


  highlightSelectedText(selectedText, isQuestion) {
    const className = classExtractor({
      "wl-faq__question": isQuestion,
      "wl-faq__answer": !isQuestion
    });
    const editor = this.editor;
    const highlightedElement = `<span class="${className}">${selectedText}</span>`;
    editor.selection.setContent(highlightedElement);
  }
}

export default TinymceHighlightHandler;
