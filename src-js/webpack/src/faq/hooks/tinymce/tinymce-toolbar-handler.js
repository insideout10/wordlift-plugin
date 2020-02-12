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
import FaqValidator from "../validators/faq-validator";

const TINYMCE_TOOLBAR_BUTTON_NAME = "wl-faq-toolbar-button";

class TinymceToolbarHandler {
  /**
   * Construct the TinymceToolbarHandler
   * @param editor {tinymce.Editor} instance.
   */
  constructor(editor) {
    this.editor = editor;
  }

  /**
   * Sets the button text based on the text selected by user.
   * @param selectedText The text selected by user.
   * @param button Button present in toolbar.
   * @param container This container holds the button.
   */
  setButtonTextBasedOnSelectedText(selectedText, button, container) {
    if (FaqValidator.isQuestion(selectedText)) {
      button.innerText = "Add Question";
      container.setAttribute("aria-label", "Add Question");
    } else {
      button.innerText = "Add Answer";
      container.setAttribute("aria-label", "Add Answer");
    }
  }

  /**
   * When there is no selection disable the button, determine
   * if it is question or answer and change the button text.
   */
  changeButtonStateOnSelectedText() {
    const editor = this.editor;
    const selectedText = editor.selection.getContent({ format: "text" });
    const container = document.getElementById(TINYMCE_TOOLBAR_BUTTON_NAME);
    const button = container.getElementsByTagName("button")[0];
    if (selectedText.length > 0) {
      container.classList.remove("mce-disabled");
      button.disabled = false;
      this.setButtonTextBasedOnSelectedText(selectedText, button, container);
    } else {
      container.classList.add("mce-disabled");
      button.disabled = true;
    }
  }

  changeToolBarButtonStateBasedOnTextSelected() {
    const editor = this.editor;
    editor.on("NodeChange", e => {
      this.changeButtonStateOnSelectedText();
    });
  }

  addButtonToToolBar() {
    const editor = this.editor;
    editor.addButton(TINYMCE_TOOLBAR_BUTTON_NAME, {
      text: "Add Question or Answer",
      id: TINYMCE_TOOLBAR_BUTTON_NAME,
      onclick: function() {
        trigger(FAQ_EVENT_HANDLER_SELECTION_CHANGED, editor.selection.getContent({ format: "text" }));
      }
    });
    this.changeToolBarButtonStateBasedOnTextSelected();
  }
}

export default TinymceToolbarHandler;
