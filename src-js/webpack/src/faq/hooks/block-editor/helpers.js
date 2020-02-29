/**
 * List of helpers in order to extract the html from the selected gutenberg
 * block.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
/**
 * Returns html string from selected blocks.
 * @return {string} HTML String
 */
export function getCurrentSelectionHTML() {
  let html = "";
  /** Check if it multi selection */
  const blocks = wp.data.select('core/block-editor').getMultiSelectedBlocks()
  if ( blocks.length === 0 ) {
    // Not a valid selection, return empty html
    return html
  }
  if ( blocks.length > 1 ) {
    // its a multi selection, loop through blocks and get html.
    for (let block of blocks) {
      html += block.originalContent
    }
  }
  else {
    // it is a single selection, get selected blocks original content.
    html += wp.data.select('core/block-editor').getSelectedBlock().originalContent
  }
  return html
}
