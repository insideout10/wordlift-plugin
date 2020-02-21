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

/**
 * Internal dependencies.
 */
import { FAQ_ANSWER_HIGHLIGHTING_CLASS, FAQ_QUESTION_HIGHLIGHTING_CLASS } from "../tinymce/tinymce-highlight-handler";

export const FAQ_ANSWER_FORMAT_NAME = "wordlift/faq-answer";
export const FAQ_QUESTION_FORMAT_NAME = "wordlift/faq-question";

class GutenbergFormatTypeHandler {
  registerAnswerFormatType() {
    registerFormatType(FAQ_ANSWER_FORMAT_NAME, {
      title: "Question",
      tagName: "span",
      className: FAQ_ANSWER_HIGHLIGHTING_CLASS
    });
  }
  registerQuestionFormatType() {
    registerFormatType(FAQ_QUESTION_FORMAT_NAME, {
      title: "Question",
      tagName: "span",
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
