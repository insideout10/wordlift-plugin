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
    const previousFloatingActionButtonInstances = this.editor.dom.select("." + FLOATING_ACTION_BUTTON_CLASS_NAME);
    this.editor.dom.remove(previousFloatingActionButtonInstances);
  }
  static getFloatingActionButtonText(isQuestion) {
    if (isQuestion) {
      return "Add Question";
    } else {
      return "Add Answer";
    }
  }
  /**
   * Insert the floating action button at the text selection.
   * @param isQuestion Boolean indicating if the text is a question.
   */
  insertFloatingActionButtonAtSelection(isQuestion) {
    const range = this.editor.selection.getRng();
    const newNode = this.editor.getDoc().createElement("button");
    newNode.innerHTML = TinymceFloatingActionButtonHandler.getFloatingActionButtonText(isQuestion);
    newNode.className = FLOATING_ACTION_BUTTON_CLASS_NAME;
    newNode.setAttribute("style", "user-select: none;");
    const oldEnd = range.endContainer;
    const oldStart = range.startContainer;
    /**
     * Insert at the end of the selection.
     */
    range.setStartAfter(oldEnd);
    range.insertNode(newNode);
    /**
     * Reset the Range
     */
    range.setStart(oldStart, 0);
    range.setEnd(oldEnd, 0);
  }

  /**
   * Show the floating action button for the user to Add question
   * @param isQuestion Boolean indicating if the text is a question.
   */
  showFloatingActionButton(isQuestion) {
    this.removePreviousFloatingActionButtons();
    // Add the new floating action button
    this.insertFloatingActionButtonAtSelection(isQuestion);
  }
}

export default TinymceFloatingActionButtonHandler;
