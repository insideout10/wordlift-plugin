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
     *     id: Int
     * }
     */
    on(FAQ_HIGHLIGHT_TEXT, result => {
      this.highlightSelectedText(result.text, result.isQuestion, result.id);
    });
  }

  /**
   * Highlight the selection done by the user.
   * @param selectedText The text which was selected by the user.
   * @param isQuestion {Boolean} Indicates if its question or answer.
   * @param id {Int} Unique id for question and answer.
   */
  highlightSelectedText(selectedText, isQuestion, id) {
    const html = this.editor.selection.getContent();
    const className = classExtractor({
      "wl-faq__question": isQuestion,
      "wl-faq__answer": !isQuestion
    });
    /**
     * Prepare unique identifier for the string, we are appending the classname because ids should
     * be unique.
     * @type {string}
     */
    const identifier = `${className}--${id}`;
    const editor = this.editor;
    const highlightedElement = `<span class="${className}" id="${identifier}">${html}</span>`;
    editor.selection.setContent(highlightedElement);
  }
}

export default TinymceHighlightHandler;
