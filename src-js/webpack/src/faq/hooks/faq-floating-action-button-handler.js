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
import {on} from 'backbone'
import {FAQ_TINY_MCE_HOOK_MOUSE_UP_EVENT} from "../constants/faq-hook-constants";

class FaqFloatingActionButtonHandler {
  /**
   * Use two variables to track position of cursor in order to
   * show the floating action button once the text was selected.
   */
  constructor() {
    this.left_position = 0;
    this.top_position = 0;
    this.listenForMouseMoveAction();
    on(FAQ_TINY_MCE_HOOK_MOUSE_UP_EVENT, event => {
      this.left_position = event.x;
      this.top_position = event.y;
    })
  }

  listenForMouseMoveAction() {
    document.addEventListener("mousedown", event => {
      this.left_position = event.x;
      this.top_position = event.y;
    });
  }

  showFloatingActionButton() {
    console.log("changing element location " + this.top_position + " " + this.left_position);
    const fab = document.getElementById("wl-faq-fab-panel");
    fab.style.position = "fixed";
    fab.style.top = this.top_position + "px";
    fab.style.left = this.left_position + "px";
  }
}

export default FaqFloatingActionButtonHandler;
