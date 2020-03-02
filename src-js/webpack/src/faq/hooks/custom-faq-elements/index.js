/**
 * Registers the custom elements needed for FAQ in to customElements
 * windows variable.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

export const FAQ_QUESTION_TAG_NAME = "wl-faq-question";

export const FAQ_ANSWER_TAG_NAME = "wl-faq-answer";

class FaqQuestion extends Element {}
class FaqAnswer extends Element {}

class CustomFaqElementsRegistry {
  static registerFaqQuestionElement() {
    customElements.define(FAQ_QUESTION_TAG_NAME, FaqQuestion, { extends: "div" });
  }
  static registerFaqAnswerElement() {
    customElements.define(FAQ_ANSWER_TAG_NAME, FaqAnswer, { extends: "div" });
  }
  static registerAllElements() {
    CustomFaqElementsRegistry.registerFaqQuestionElement();
    CustomFaqElementsRegistry.registerFaqAnswerElement();
  }
}

export default CustomFaqElementsRegistry;
