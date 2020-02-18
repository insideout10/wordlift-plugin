/**
 * FaqHookToStoreDispatcher Dispatches the events from hook to
 * the redux store.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import { answerSelectedByUser, requestAddNewQuestion, updateFaqItem, updateQuestionOnInputChange } from "../actions";
import FaqValidator from "./validators/faq-validator";
import { getAllFAQItems } from "../selectors";
import { faqEditItemType } from "../components/faq-edit-item";

class FaqHookToStoreDispatcher {
  /**
   * @param store Redux store for Faq.
   */
  constructor(store) {
    this.store = store;
  }

  /**
   * Apply an answer to a particulat question
   * @param store Redux store
   * @param id Id of the question to be applied against.
   * @param answer Selected answer.
   */
  applyAnswerToQuestion(store, id, answer) {
    const action = updateFaqItem();
    action.payload = {
      id: id,
      type: faqEditItemType.ANSWER,
      value: answer
    };
    store.dispatch(action);
  }
  dispatchAnswerSelected(text) {
    // Answer selected by user, but check if there is only one question
    // If only one question present then dispatch the apply action for that question.
    const unansweredQuestions = getAllFAQItems(this.store.getState()).filter(e => e.answer === "");
    if (unansweredQuestions.length === 1) {
      const selectedQuestion = unansweredQuestions[0];
      this.applyAnswerToQuestion(this.store, selectedQuestion.id, text);
    } else {
      const action = answerSelectedByUser();
      action.payload = {
        selectedAnswer: text
      };
      this.store.dispatch(action);
    }
  }
  dispatchTextSelectedAction(text) {
    // // Check if this is a question
    if (FaqValidator.isQuestion(text)) {
      const action = updateQuestionOnInputChange();
      action.payload = text;
      this.store.dispatch(action);
      // Add it to the API
      this.store.dispatch(requestAddNewQuestion());
    } else {
      this.dispatchAnswerSelected(text);
    }
  }
}

export default FaqHookToStoreDispatcher;
