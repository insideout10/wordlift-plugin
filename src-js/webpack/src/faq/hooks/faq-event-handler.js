/**
 * FaqEventHandler Provides two way binding between store and text editor hooks.
 *
 * Text Editor hooks <--> Event handler <--> Redux Store.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import { on } from "backbone";
/**
 * Internal dependencies.
 */
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED, FAQ_ITEM_SELECTED_ON_TEXT_EDITOR } from "../constants/faq-hook-constants";
import FaqHookToStoreDispatcher from "./faq-hook-to-store-dispatcher";

const GUTENBERG = "gutenberg";

const TINY_MCE = "tiny_mce";

export const textEditors = {
  GUTENBERG: GUTENBERG,
  TINY_MCE: TINY_MCE
};

class FaqEventHandler {
  constructor(store) {
    this.listenEventsFromHooks();
    this.dispatcher = new FaqHookToStoreDispatcher(store);
  }

  /**
   * Listens for events from hooks and dispatch to
   * the store.
   */
  listenEventsFromHooks() {
    on(FAQ_EVENT_HANDLER_SELECTION_CHANGED, data => {
      this.dispatcher.dispatchTextSelectedAction(data);
    });
    on(FAQ_ITEM_SELECTED_ON_TEXT_EDITOR, faqId => {
      console.log("faq item selected");
      console.log(faqId);
      this.dispatcher.dispatchQuestionOrAnswerClickedByUser(faqId);
    });
  }
}

export default FaqEventHandler;
