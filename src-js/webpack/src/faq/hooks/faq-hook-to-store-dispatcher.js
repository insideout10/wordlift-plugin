import FaqValidator from "./faq-validator";
import {answerSelectedByUser, updateQuestionOnInputChange} from "../actions";

/**
 * FaqHookToStoreDispatcher Dispatches the events from hook to
 * the redux store.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

class FaqHookToStoreDispatcher {
    /**
     * @param store Redux store for Faq.
     */
    constructor( store ) {
        this.store = store
    }
    dispatchTextSelectedAction(text) {
        // Check if this is a question
        if (FaqValidator.isQuestion(text)) {
            const action = updateQuestionOnInputChange();
            action.payload = text;
            this.store.dispatch(action);
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

export default FaqHookToStoreDispatcher