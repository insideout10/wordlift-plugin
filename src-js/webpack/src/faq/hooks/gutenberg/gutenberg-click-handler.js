/**
 * GutenbergClickHandler handles the click events on the block editor.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Internal dependencies.
 */
import TinymceClickHandler from "../tinymce/tinymce-click-handler";
import { FAQ_ANSWER_HIGHLIGHTING_CLASS, FAQ_QUESTION_HIGHLIGHTING_CLASS } from "../tinymce/tinymce-highlight-handler";

class GutenbergClickHandler extends TinymceClickHandler {
  constructor() {
    super(null);
    this.bindClickEventsForQuestionAndAnswer();
  }
  bindClickEventsForQuestionAndAnswer() {
    const self = this;
    document.getElementsByClassName("block-editor")[0].addEventListener("click", event => {
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

export default GutenbergClickHandler;
