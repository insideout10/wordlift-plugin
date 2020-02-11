/**
 * FaqFloatingActionButtonHandler Provides a helper class to show/hide the floating action
 * button
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

class FaqFloatingActionButtonHandler {
  /**
   * Use two variables to track position of cursor in order to
   * show the floating action button once the text was selected.
   */
  constructor() {
    this.left_position = 0;
    this.top_position = 0;
    this.listenForMouseMoveAction();
  }

  listenForMouseMoveAction() {
    window.addEventListener("mousemove", event => {
      this.left_position = event.x;
      this.top_position = event.y;
    });
  }

  showFloatingActionButton() {
    const fab = document.getElementById("wl-faq-fab");
    console.log(this);
    console.log("showing fab");
    fab.style.position = "fixed";
    fab.style.top = this.top_position + "px";
    fab.style.left = this.left_position + "px";
  }
}

export default FaqFloatingActionButtonHandler;
