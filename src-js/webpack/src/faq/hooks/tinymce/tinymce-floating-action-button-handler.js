/**
 * FaqFloatingActionButtonHandler Provides a helper class to show/hide the floating action
 * button
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */

const FLOATING_ACTION_BUTTON_CLASS_NAME = "wl-faq-fab";

class TinymceFloatingActionButtonHandler {
  /**
   * Accepts an editor instance.
   * @param editor Editor instance.
   */
  constructor(editor) {
    this.editor = editor;
  }

  /**
   * When the user selects the text, remove all
   * the floating action button in the tinymce dom
   */
  removePreviousFloatingActionButtons() {
    const previousFloatingActionButtonInstances = this.editor.dom.select(
      "." + FLOATING_ACTION_BUTTON_CLASS_NAME
    );
    console.log(previousFloatingActionButtonInstances)
    this.editor.dom.remove(previousFloatingActionButtonInstances);
  }

  /**
   * Insert the floating action button at the text selection.
   */
  insertFloatingActionButtonAtSelection() {
    const range = this.editor.selection.getRng();
    const newNode = this.editor.getDoc().createElement("button");
    newNode.innerHTML = "Add Question";
    newNode.className = FLOATING_ACTION_BUTTON_CLASS_NAME
    range.insertNode(newNode);
  }

  /**
   * Show the floating action button for the user to Add question
   */
  showFloatingActionButton() {
    this.removePreviousFloatingActionButtons();
    // Add the new floating action button
    this.insertFloatingActionButtonAtSelection();
  }
}

export default TinymceFloatingActionButtonHandler;
