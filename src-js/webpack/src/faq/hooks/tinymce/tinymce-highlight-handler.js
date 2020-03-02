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
import {FAQ_ANSWER_TAG_NAME, FAQ_QUESTION_TAG_NAME} from "../custom-faq-elements";

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
   * Return answer or question tag based on the selected
   * text.
   */
  getTagBasedOnHighlightedText(isQuestion) {
    if ( isQuestion ) {
      return FAQ_QUESTION_TAG_NAME
    }
    else {
      return FAQ_ANSWER_TAG_NAME
    }
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
    const tagName = this.getTagBasedOnHighlightedText(isQuestion)
    const highlightedElement = `<${tagName}>${html}</${tagName}>`;
    this.selection.setContent(highlightedElement);
  }
}

export default TinymceHighlightHandler;
