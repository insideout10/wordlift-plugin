/**
 * TinyMceToolbarHandler handles the toolbar button.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
/**
 * Internal dependencies.
 */
import { trigger } from "backbone";
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED } from "../../constants/faq-hook-constants";

const TINYMCE_TOOLBAR_BUTTON_NAME = "wl-faq-toolbar-button";

class TinymceToolbarHandler {
  /**
   * Construct the TinymceToolbarHandler
   * @param editor {tinymce.Editor} instance.
   */
  constructor(editor) {
    this.editor = editor;
  }
  addButtonToToolBar() {
    const editor = this.editor;
    editor.addButton(TINYMCE_TOOLBAR_BUTTON_NAME, {
      title: "Add question or answer",
      text: "Add Question",
      onclick: function() {
        trigger(FAQ_EVENT_HANDLER_SELECTION_CHANGED, editor.selection.getContent({ format: "text" }));
      }
    });
  }
}

export default TinymceToolbarHandler;
