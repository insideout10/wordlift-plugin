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
import FaqValidator from "../validators/faq-validator";

/** Floating action button wrapper id **/
export const FAB_WRAPPER_ID = "wl-block-editor-fab-wrapper";
/** Floating action button id **/
export const FAB_ID = "wl-block-editor-fab-button";

/**
 * BlockEditorFabButtonRegister Registers the floating action
 * button to the block editor.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class BlockEditorFabButtonRegister {
  constructor(wp, highlightHandler) {
    this.wp = wp;
    this.highlightHandler = highlightHandler;
    /** Translated Text from PHP **/
    this.addQuestionOrAnswerText = global["_wlFaqSettings"]["addQuestionOrAnswerText"];
    this.addQuestionText = global["_wlFaqSettings"]["addQuestionText"];
    this.addAnswerText = global["_wlFaqSettings"]["addAnswerText"];
  }
  showFloatingActionButtonOnTextSelectionEvent() {
    on(SELECTION_CHANGED, ({ selection }) => {
      if (selection.length > 0) {
        if (FaqValidator.isQuestion(selection)) {
          document.getElementById(FAB_ID).innerText = this.addQuestionText;
        } else {
          document.getElementById(FAB_ID).innerText = this.addAnswerText;
        }

      } else {
        // hide the button
        this.fab.style.display = "none";
      }
    });
  }

  /**
   * Adding a floating action button to the gutenberg editor
   * it doesnt affect the DOM of gutenberg, it floats near the block
   */
  registerFabButton() {
    const fabWrapper = document.createElement("div");
    fabWrapper.id = FAB_WRAPPER_ID;
    fabWrapper.innerHTML = `
      <div class="wl-fab">
            <div class="wl-fab-body">
                <button class="wl-fab-button" id="${FAB_ID}">Add Answer</button>
            </div>
      </div>
    `;
    document.body.appendChild(fabWrapper);
  }
}

export default BlockEditorFabButtonRegister;
