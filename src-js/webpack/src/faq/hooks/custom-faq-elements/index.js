/**
 * Registers the custom elements needed for FAQ in to customElements
 * windows variable.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

export const FAQ_QUESTION_TAG_NAME = "wl-faq-question";

export const FAQ_ANSWER_TAG_NAME = "wl-faq-answer";

class FaqQuestion extends HTMLElement {
  constructor() {
    super();
  }
}
class FaqAnswer extends HTMLElement {
  constructor() {
    super();
  }
}

class CustomFaqElementsRegistry {
  static registerFaqQuestionElement() {
    if ( customElements.get(FAQ_QUESTION_TAG_NAME) === undefined ) {
      customElements.define(FAQ_QUESTION_TAG_NAME, FaqQuestion, {extends: "div"});
    }
  }
  static registerFaqAnswerElement() {
    if ( customElements.get(FAQ_ANSWER_TAG_NAME) === undefined ) {
      customElements.define(FAQ_ANSWER_TAG_NAME, FaqAnswer, {extends: "div"});
    }
  }
  static registerAllElements() {
    CustomFaqElementsRegistry.registerFaqQuestionElement();
    CustomFaqElementsRegistry.registerFaqAnswerElement();
  }
}

export default CustomFaqElementsRegistry;
