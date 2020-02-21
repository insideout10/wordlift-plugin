/**
 * TinyMceClickHandler handles the click events on the tinymce editor.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import { trigger } from "backbone";

/**
 * Internal dependencies.
 */
import { FAQ_ANSWER_HIGHLIGHTING_CLASS, FAQ_QUESTION_HIGHLIGHTING_CLASS } from "./tinymce-highlight-handler";
import { FAQ_ITEM_SELECTED_ON_TEXT_EDITOR } from "../../constants/faq-hook-constants";

class TinymceClickHandler {
  constructor(editor) {
    this.editor = editor;
    this.bindClickEventsForQuestionAndAnswer();
  }
  /**
   * Parse the html id in to faq id
   * @param id {string}
   * @return {string} returns the parsed id
   */
  parseHtmlIdToFAQId(id) {
    return id.split("--")[2];
  }

  /**
   * Fires the event to event handler from the tinymce hook.
   * @param id {string} Html id.
   */
  fireEventToEventHandlerToOpenQuestionOrAnswer(id) {
    console.log("faq id before parsing " + id);
    const faqId = this.parseHtmlIdToFAQId(id);
    console.log("faq id after parsing" + id);
    trigger(FAQ_ITEM_SELECTED_ON_TEXT_EDITOR, faqId);
  }
  /**
   * Listen for click events from question and answer.
   */
  bindClickEventsForQuestionAndAnswer() {
    const self = this;
    this.editor.onClick.add(function(ed, event) {
      if (event.target !== "undefined" && event.target.classList.length > 0) {
        if (
          event.target.classList.contains(FAQ_QUESTION_HIGHLIGHTING_CLASS) ||
          event.target.classList.contains(FAQ_ANSWER_HIGHLIGHTING_CLASS)
        ) {
          self.fireEventToEventHandlerToOpenQuestionOrAnswer(event.target.id);
        }
      }
    });
  }
}

export default TinymceClickHandler;
