/**
 * External dependencies.
 */
import { on, trigger } from "backbone";
/**
 * Internal dependencies.
 */
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED } from "../../constants/faq-hook-constants";
import { getCurrentSelectionHTML } from "./helpers";
import { FAQ_GUTENBERG_TOOLBAR_BUTTON_CLASS_NAME } from "./block-editor-faq-plugin";
import { SELECTION_CHANGED } from "../../../common/constants";

/**
 * GutenbergToolbarButtonRegister Registers the toolbar button for the
 * gutenberg.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

class BlockEditorToolbarButtonRegister {
  constructor(wp, highlightHandler) {
    this.wp = wp;
    this.highlightHandler = highlightHandler;
    this.addQuestionOrAnswerText = global["_wlFaqSettings"]["addQuestionOrAnswerText"];
    this.floatingActionButton = null;
  }
  showFloatingActionButtonOnTextSelectionEvent() {
    on(SELECTION_CHANGED, ({ selection }) => {
      if (selection.length > 0) {
        // get the selection coordinates.
        const node = window.getSelection().getRangeAt(0).commonAncestorContainer;
        const parentElement = node.parentElement;
        // we get the coordinates and then we place the button
        const { right, bottom, height } = parentElement.getBoundingClientRect();
        const offset = height / 2;
        this.floatingActionButton.style.display = "block";
        this.floatingActionButton.style.position = "absolute";
        this.floatingActionButton.style.left = `${right + 20}px`;
        this.floatingActionButton.style.top = `${bottom - offset}px`;
        this.floatingActionButton.classList = [FAQ_GUTENBERG_TOOLBAR_BUTTON_CLASS_NAME];
        console.log(this.floatingActionButton);
      } else {
        // hide the button
        this.floatingActionButton.style.display = "none";
      }
    });
  }
  registerToolbarButton() {
    this.floatingActionButton = document.createElement("div");
    this.floatingActionButton.innerHTML = `
      <div class="wl-fab">
            <div class="wl-fab-body">
                <button class="wl-fab-button">Add Answer</button>
            </div>
      </div>
    `;
    this.floatingActionButton.style.zIndex = 9999;
    document.body.appendChild(this.floatingActionButton);
    this.showFloatingActionButtonOnTextSelectionEvent();
  }
}

export default BlockEditorToolbarButtonRegister;
