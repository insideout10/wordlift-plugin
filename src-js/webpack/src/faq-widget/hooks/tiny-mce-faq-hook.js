/**
 * TinyMceFaqHook implements the editor hook, and handles the tinymce text editor.
 *
 * @since ???
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import FaqTextEditorHook from "./faq-text-editor-hook";

class TinyMceFaqHook extends FaqTextEditorHook {

    listenForTextSelection() {
        super.listenForTextSelection();
    }
}

export default TinyMceFaqHook