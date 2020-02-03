/**
 * FaqEventHandler Provides two way binding between store and text editor hooks.
 *
 * Text Editor hooks <--> Event handler <--> Redux Store.
 *
 * @since ???
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import TinyMceFaqHook from "./hooks/tiny-mce-faq-hook";

class FaqEventHandler {
    constructor() {
        this._hook = this.getHookForCurrentEnvironment()
    }
    getHook() {
        return this._hook
    }

    /**
     * Returns hook instance based on the current environment
     * @return FaqTextEditorHook|null
     */
    getHookForCurrentEnvironment() {
        if ( global["tinymce"] !== undefined ) {
            return new TinyMceFaqHook()
        }
        return null
    }
}

export default FaqEventHandler