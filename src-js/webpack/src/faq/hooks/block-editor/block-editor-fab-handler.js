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
import { on, trigger } from "backbone";
/**
 * Internal dependencies.
 */
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED, FAQ_ITEMS_CHANGED } from "../../constants/faq-hook-constants";
import { SELECTION_CHANGED } from "../../../common/constants";
import TinymceToolbarHandler from "../tinymce/tinymce-toolbar-handler";
import { FAB_ID, FAB_WRAPPER_ID } from "./block-editor-fab-button-register";
import FaqValidator from "../validators/faq-validator";
import { getCurrentSelectionHTML, getCurrentSelectionText } from "./helpers";

class BlockEditorFabHandler {
  constructor() {
    this.faqItems = [];
    on(FAQ_ITEMS_CHANGED, faqItems => {
      this.faqItems = faqItems;
    });
    this.startListeningForSelectionChangesAndSetState();
    this.dispatchTextSelectedToEventHandler();
    this.addQuestionText = global["_wlFaqSettings"]["addQuestionText"];
    this.addAnswerText = global["_wlFaqSettings"]["addAnswerText"];
  }

  startListeningForSelectionChangesAndSetState() {
    /**
     * Listen for event emitted by wordlift hook.
     */
    on(SELECTION_CHANGED, ({ selection }) => {
      this.setStateBasedOnStore(getCurrentSelectionText());
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
   * When the user clicks on the add question or add answer button
   * then dispatch the text to the event handler.
   */
  dispatchTextSelectedToEventHandler() {
    const button = document.getElementById(FAB_ID);
    button.addEventListener("click", event => {
      trigger(FAQ_EVENT_HANDLER_SELECTION_CHANGED, {
        selectedText: getCurrentSelectionText(),
        selectedHTML: getCurrentSelectionHTML()
      });
      // Hide the button after getting clicked.
      this.hideFabWrapper(this.getFabWrapper());
    });
  }

  setFabButtonTextBasedOnSelectedText() {
    const button = document.getElementById(FAB_ID);
    if (button !== null) {
      const selection = getCurrentSelectionText();
      if (FaqValidator.isQuestion(selection)) {
        button.innerText = this.addQuestionText;
      } else {
        button.innerText = this.addAnswerText;
      }
    }
  }

  /**
   * Set the state of toolbar button instances with text selection.
   * @param selectedText
   */
  setStateBasedOnStore(selectedText) {
    const wrapper = this.getFabWrapper();
    if (wrapper === null) {
      /** Return early, wrapper is not added to DOM yet **/
      return;
    }
    if (window.getSelection().rangeCount === 0) {
      /** Return early if the range is not defined yet **/
      return;
    }
    this.setFabButtonTextBasedOnSelectedText();
    const shouldDisableButton = TinymceToolbarHandler.shouldDisableButton(selectedText, this.faqItems);
    if (shouldDisableButton) {
      this.hideFabWrapper(wrapper);
    } else {
      // get the selection coordinates.
      const range = window.getSelection().getRangeAt(0)
      // we get the coordinates and then we place the button
      const { right, bottom, height } = range.getBoundingClientRect()
      const offset = height / 2;
      wrapper.style.position = "fixed";
      wrapper.style.left = `${right + 30}px`;
      wrapper.style.top = `${bottom - offset - 10}px`;
      wrapper.zIndex = 999;
      this.showFabWrapper(wrapper);
    }
  }
}

export default BlockEditorFabHandler;
