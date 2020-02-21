/**
 * GutenbergFormatTypeHandler Registers the format type required for the FAQ section in
 * the gutenberg.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * WordPress dependencies
 */
import { registerFormatType } from "@wordpress/rich-text";
import { FAQ_ANSWER_HIGHLIGHTING_CLASS, FAQ_QUESTION_HIGHLIGHTING_CLASS } from "../tinymce/tinymce-highlight-handler";

class GutenbergFormatTypeHandler {
  registerAnswerFormatType() {
    registerFormatType("wordlift/faq-answer", {
      title: "Question",
      tagName: "Span",
      className: FAQ_ANSWER_HIGHLIGHTING_CLASS
    });
  }
  registerQuestionFormatType() {
    registerFormatType("wordlift/faq-question", {
      title: "Question",
      tagName: "Span",
      className: FAQ_QUESTION_HIGHLIGHTING_CLASS
    });
  }

  /**
   * Registers all the format types needed by FAQ
   */
  registerAllFormatTypes() {
    this.registerQuestionFormatType();
    this.registerAnswerFormatType();
  }
}

export default GutenbergFormatTypeHandler;
