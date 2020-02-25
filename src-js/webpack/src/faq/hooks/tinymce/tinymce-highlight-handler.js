/**
 * TinyMceHighlightHandler handles the toolbar button.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import { on } from "backbone";
/**
 * Internal dependencies.
 */
import { FAQ_HIGHLIGHT_TEXT } from "../../constants/faq-hook-constants";
import { classExtractor } from "../../../mappings/blocks/helper";

export const FAQ_QUESTION_HIGHLIGHTING_CLASS = "wl-faq--question";
export const FAQ_ANSWER_HIGHLIGHTING_CLASS = "wl-faq--answer";

class TinymceHighlightHandler {
  /**
   * Construct highlight handler instance.
   * @param editor {tinymce.Editor} The Tinymce editor instance.
   */
  constructor(editor) {
    this.editor = editor;
    this.selection = null;
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
   * Save the currently selection to a instance
   * variable, used for highlighting the text later even
   * if the user unselected the text.
   */
  saveSelection() {
    this.selection = this.editor.selection;
  }
  /**
   * Highlight the selection done by the user.
   * @param selectedText The text which was selected by the user.
   * @param isQuestion {Boolean} Indicates if its question or answer.
   * @param id {Int} Unique id for question and answer.
   */
  highlightSelectedText(selectedText, isQuestion, id) {
    if (this.selection === null) {
      /**
       * Bail out if there is no selection on the editor.
       */
      return;
    }
    const html = this.selection.getContent();
    const className = classExtractor({
      [FAQ_QUESTION_HIGHLIGHTING_CLASS]: isQuestion,
      [FAQ_ANSWER_HIGHLIGHTING_CLASS]: !isQuestion
    });
    /**
     * Prepare unique identifier for the string, we are appending the classname because ids should
     * be unique.
     * @type {string}
     */
    const identifier = `${className}--${id}`;
    const highlightedElement = `<span id="${identifier}" class="${className}">${html}</span>`;
    this.selection.setContent(highlightedElement);
  }
}

export default TinymceHighlightHandler;
