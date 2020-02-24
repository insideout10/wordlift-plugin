/**
 * GutenbergToolbarHandler handles the disable and enabling
 * the add question or answer button based on the store state.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Internal dependencies.
 */
import { on } from "backbone";
import { FAQ_ITEMS_CHANGED } from "../../constants/faq-hook-constants";
import { SELECTION_CHANGED } from "../../../common/constants";
import { FAQ_GUTENBERG_TOOLBAR_BUTTON_CLASS_NAME } from "./gutenberg-faq-plugin";
import TinymceToolbarHandler from "../tinymce/tinymce-toolbar-handler";

class GutenbergToolbarHandler {
  constructor() {
    this.faqItems = [];
    on(FAQ_ITEMS_CHANGED, faqItems => {
      this.faqItems = faqItems;
    });
    this.startListeningForSelectionChangesAndSetState();
    // When initailised set the button to disabled state.
    this.disableButtons(document.getElementsByClassName(FAQ_GUTENBERG_TOOLBAR_BUTTON_CLASS_NAME))
  }

  startListeningForSelectionChangesAndSetState() {
    /**
     * Listen for event emitted by wordlift hook.
     */
    on(SELECTION_CHANGED, ({selection}) => {
      this.setStateBasedOnStore(selection);
    });

  }

  /**
   * Disabling the buttons on selection change.
   * @param toolbarButtons
   */
  disableButtons(toolbarButtons) {
    for ( let button of toolbarButtons) {
      button.disabled = true
    }
  }

  /**
   * Enable the buttons when the selection is changed.
   * @param toolbarButtons
   */
  enableButtons(toolbarButtons) {
      for ( let button of toolbarButtons) {
          button.disabled = false
      }
  }

  /**
   * Set the state of toolbar button instances with text selection.
   * @param selectedText
   */
  setStateBasedOnStore(selectedText) {
    const toolbarButtons = document.getElementsByClassName(FAQ_GUTENBERG_TOOLBAR_BUTTON_CLASS_NAME);
    const shouldDisableButton = TinymceToolbarHandler.shouldDisableButton(selectedText, this.faqItems);
    if ( shouldDisableButton ) {
        this.disableButtons(toolbarButtons)
    }
    else {
        this.enableButtons(toolbarButtons)
    }
  }
}

export default GutenbergToolbarHandler;
