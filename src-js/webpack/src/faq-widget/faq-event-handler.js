/**
 * FaqEventHandler Provides two way binding between store and text editor hooks.
 *
 * Text Editor hooks <--> Event handler <--> Redux Store.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import TinyMceFaqHook from "./hooks/tiny-mce-faq-hook";
const GUTENBERG = "gutenberg";

const TINY_MCE = "tiny_mce";

export const textEditors = {
  GUTENBERG: GUTENBERG,
  TINY_MCE: TINY_MCE
};

class FaqEventHandler {
  constructor() {
    this._hook = this.getHookForCurrentEnvironment();
  }
  getHook() {
    return this._hook;
  }

  /**
   * Returns hook instance based on the current environment
   * @return FaqTextEditorHook|null
   */
  getHookForCurrentEnvironment() {
    let textEditor = null;
    if (global["_wlFaqSettings"] !== undefined) {
      textEditor = global["_wlFaqSettings"]["textEditor"];
    }
    switch (textEditor) {
      case textEditors.TINY_MCE:
        return new TinyMceFaqHook();
      default:
        return null;
    }
  }
}

export default FaqEventHandler;
