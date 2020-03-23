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
    // initially it should be hidden for user.
    fabWrapper.style.display = "none";
    document.body.appendChild(fabWrapper);
  }
}

export default BlockEditorFabButtonRegister;
