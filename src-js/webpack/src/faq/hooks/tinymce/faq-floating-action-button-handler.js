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
import { on } from "backbone";

const FLOATING_ACTION_BUTTON_CLASS_NAME = "wl-faq-fab";

class FaqFloatingActionButtonHandler {
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
    const previousFloatingActionButtonInstances = this.editor.dom.Selection.select('.' + FLOATING_ACTION_BUTTON_CLASS_NAME)
    this.editor.dom.remove( previousFloatingActionButtonInstances )
  }

  /**
   * Show the floating action button for the user to Add question
   */
  showFloatingActionButton() {
    this.removePreviousFloatingActionButtons()
  }

}

export default FaqFloatingActionButtonHandler;
