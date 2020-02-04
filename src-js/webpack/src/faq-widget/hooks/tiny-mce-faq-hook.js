/**
 * TinyMceFaqHook implements the editor hook, and handles the tinymce text editor.
 * NOTE: this hooks is called when the tinymce editor is used, so there is no need
 * to check for edge cases since we will have an activeEditor
 * @since ???
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import FaqTextEditorHook from "./faq-text-editor-hook";

export const FAQ_TINY_MCE_PLUGIN_NAME = "wl_faq";

class TinyMceFaqHook extends FaqTextEditorHook {
  constructor() {
    super();
  }

  listenForTextSelection() {
    const tinymce = global["tinymce"];
    tinymce.activeEditor.on("NodeChange", e => {
      console.log(e);
    });
  }
}

export default TinyMceFaqHook;
