/**
 * List of helpers in order to extract the html from the selected gutenberg
 * block.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
/**
 * Gutenberg returns a selected object that represents a
 * text value, there is no way to return the selected html,
 * we can get the HTML manually from DOM using this method.
 * This method should only be called when the user clicks on
 * Add question/Answer in order to get the correct selected html.
 *
 * @return {string} HTML String
 */
export function getCurrentSelectionHTML() {
  let html = "";
  /** Check for selection */
  if (window.getSelection() !== undefined) {
    const selection = window.getSelection();
    const rangeCount = selection.rangeCount;
    // Loop through all the ranges and append the child.
    const container = document.createElement("div");
    for (let i = 0; i < rangeCount; ++i) {
      container.appendChild(selection.getRangeAt(i).cloneContents());
    }
    // once we have appended the child to the dummy container, return the innerHTML
    html = container.innerHTML;
  }
  return html;
}
