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
import CustomFaqElementsRegistry, { FAQ_ANSWER_TAG_NAME, FAQ_QUESTION_TAG_NAME } from "../custom-faq-elements";

export const FAQ_ANSWER_FORMAT_NAME = "wordlift/faq-answer";
export const FAQ_QUESTION_FORMAT_NAME = "wordlift/faq-question";

class BlockEditorFormatTypeHandler {
  registerAnswerFormatType() {
    CustomFaqElementsRegistry.registerFaqAnswerElement();
    registerFormatType(FAQ_QUESTION_FORMAT_NAME, {
      title: "Question",
      tagName: FAQ_QUESTION_TAG_NAME,
      className: null,
      attributes: { class: "class" },
    });
  }
  registerQuestionFormatType() {
    CustomFaqElementsRegistry.registerFaqQuestionElement();
    registerFormatType(FAQ_ANSWER_FORMAT_NAME, {
      title: "Answer",
      tagName: FAQ_ANSWER_TAG_NAME,
      className: null,
      attributes: { class: "class" },
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

export default BlockEditorFormatTypeHandler;
