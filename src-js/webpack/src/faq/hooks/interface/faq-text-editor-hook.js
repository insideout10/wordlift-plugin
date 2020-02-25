/**
 * FaqTextEditorHook Provides a abstract class for the hooks to implement
 * the methods.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class FaqTextEditorHook {
  constructor() {}
  /**
   * Perform text highlighting when the event is sent from store.
   */
  performTextHighlighting() {
    this._throwFunctionNotImplementedError("doTextHighlighting()");
  }

  /**
   * Show the floating action button to be used to add question or answer based
   * on the text selected.
   */
  showFloatingActionButton() {
    this._throwFunctionNotImplementedError("showFloatingActionButton()");
  }
  /**
   * Initialize the hook in correct order, the order might
   * be changed by the child class if it wants to change.
   */
  initialize() {
    this.performTextHighlighting();
    this.showFloatingActionButton();
  }

  _throwFunctionNotImplementedError(functionName) {
    throw new Error(functionName + " should be implemented by the parent class ");
  }
}
export default FaqTextEditorHook;
