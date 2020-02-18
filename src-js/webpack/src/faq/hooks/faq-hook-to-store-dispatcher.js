/**
 * FaqHookToStoreDispatcher Dispatches the events from hook to
 * the redux store.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import { answerSelectedByUser, requestAddNewQuestion, updateQuestionOnInputChange } from "../actions";
import FaqValidator from "./validators/faq-validator";

class FaqHookToStoreDispatcher {
  /**
   * @param store Redux store for Faq.
   */
  constructor(store) {
    this.store = store;
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
      // This is an answer, show the  add answer button.
      const action = answerSelectedByUser();
      action.payload = {
        selectedAnswer: text
      };
      this.store.dispatch(action);
    }
  }
}

export default FaqHookToStoreDispatcher;
