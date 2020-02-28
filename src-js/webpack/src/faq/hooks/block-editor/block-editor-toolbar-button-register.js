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
const FAB_ID = 'wl-block-editor-fab-button'
class BlockEditorToolbarButtonRegister {
  constructor(wp, highlightHandler) {
    this.wp = wp;
    this.highlightHandler = highlightHandler;
    this.addQuestionOrAnswerText = global["_wlFaqSettings"]["addQuestionOrAnswerText"];
    this.fab = null;
    this.last_selection = null
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
        this.fab.style.display = "block";
        this.fab.style.position = "absolute";
        this.fab.style.left = `${right + 30}px`;
        this.fab.style.top = `${bottom - offset}px`;
        this.last_selection = selection
      } else {
        // hide the button
        this.fab.style.display = "none";
      }
    });
  }
  registerToolbarButton() {
    this.fab = document.createElement("div");
    this.fab.innerHTML = `
      <div class="wl-fab">
            <div class="wl-fab-body">
                <button class="wl-fab-button" id="${FAB_ID}">Add Answer</button>
            </div>
      </div>
    `;
    this.fab.style.zIndex = 99;
    document.body.appendChild(this.fab);
    this.showFloatingActionButtonOnTextSelectionEvent();
    document.getElementById(FAB_ID).addEventListener("click", (event) => {
      if ( this.last_selection !== null && this.last_selection.length > 0 ) {
        trigger(FAQ_EVENT_HANDLER_SELECTION_CHANGED, {
          selectedText: this.last_selection,
          selectedHTML: getCurrentSelectionHTML()
        })
        this.fab.style.display = "none"
      }
    })
  }
}

export default BlockEditorToolbarButtonRegister;
