/**
 * TinyMceFaqHook implements the editor hook, and handles the tinymce text editor.
 *
 * @since ???
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import FaqTextEditorHook from "./faq-text-editor-hook";

export const FAQ_TINY_MCE_PLUGIN_NAME = 'wl_faq'

class TinyMceFaqHook extends FaqTextEditorHook {

    constructor() {
        super();
        this.listenForTextSelection()
    }

    listenForTextSelection() {
        super.listenForTextSelection();
    }
}

export default TinyMceFaqHook