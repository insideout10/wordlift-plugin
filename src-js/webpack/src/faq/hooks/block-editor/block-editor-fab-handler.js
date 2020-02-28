/**
 * BlockEditorFabHandler handles the disable and enabling
 * the add question or answer button based on the store state.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import { on } from "backbone";
/**
 * Internal dependencies.
 */
import { FAQ_ITEMS_CHANGED } from "../../constants/faq-hook-constants";
import { SELECTION_CHANGED } from "../../../common/constants";
import { FAQ_GUTENBERG_TOOLBAR_BUTTON_CLASS_NAME } from "./block-editor-faq-plugin";
import TinymceToolbarHandler from "../tinymce/tinymce-toolbar-handler";
import { FAB_WRAPPER_ID } from "./block-editor-fab-button-register";

class BlockEditorFabHandler {
  constructor() {
    this.faqItems = [];
    on(FAQ_ITEMS_CHANGED, faqItems => {
      this.faqItems = faqItems;
    });
    this.startListeningForSelectionChangesAndSetState();
  }

  startListeningForSelectionChangesAndSetState() {
    /**
     * Listen for event emitted by wordlift hook.
     */
    on(SELECTION_CHANGED, ({ selection }) => {
      this.setStateBasedOnStore(selection);
    });
  }

  /**
   * Disabling the buttons on selection change.
   * @param fabWrapper {Element} Wrapper div for fab button
   */
  hideFabWrapper(fabWrapper) {
    fabWrapper.style.display = "none";
  }

  /**
   * Enable the buttons when the selection is changed.
   * @param fabWrapper Wrapper div for fab button
   */
  showFabWrapper(fabWrapper) {
    fabWrapper.style.display = "block";
  }

  /**
   * Returns the floating action button from the DOM.
   */
  getFabWrapper() {
    return document.getElementById(FAB_WRAPPER_ID);
  }

  /**
   * Set the state of toolbar button instances with text selection.
   * @param selectedText
   */
  setStateBasedOnStore(selectedText) {
    const shouldDisableButton = TinymceToolbarHandler.shouldDisableButton(selectedText, this.faqItems);
    if (shouldDisableButton) {
      this.hideFabWrapper(wrapper);
    } else {
      const wrapper = this.getFabWrapper();
      if (wrapper !== null) {
        // get the selection coordinates.
        const node = window.getSelection().getRangeAt(0).commonAncestorContainer;
        const parentElement = node.parentElement;
        // we get the coordinates and then we place the button
        const { right, bottom, height } = parentElement.getBoundingClientRect();
        const offset = height / 2;
        wrapper.style.position = "absolute";
        wrapper.style.left = `${right + 30}px`;
        wrapper.style.top = `${bottom - offset}px`;
      }
      this.showFabWrapper(wrapper);
    }
  }
}

export default BlockEditorFabHandler;
