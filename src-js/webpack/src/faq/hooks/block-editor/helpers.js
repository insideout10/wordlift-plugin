/**
 * List of helpers in order to extract the html from the selected gutenberg
 * block.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

function getSingleBlockSelectionHTML() {
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

/**
 * Returns html string from selected blocks.
 * @return {string} HTML String
 */
export function getCurrentSelectionHTML() {
  let html = "";
  /** Check if it multi selection, if multi selection is done then
   * the user can only select the contents in the block fully, so we
   * can append the html in to single string */
  const blocks = wp.data.select("core/block-editor").getMultiSelectedBlocks();

  if (blocks.length > 1) {
    // its a multi selection, loop through blocks and get html.
    for (let block of blocks) {
      html += block.originalContent;
    }
  } else {
    // it is a single selection, get selected blocks original content.
    html += getSingleBlockSelectionHTML();
  }
  return html;
}

/**
 * Returns the text from the selected blocks.
 * @return {string} Text
 */
export function getCurrentSelectionText() {
  // Create a dummy element and render it.
  const el = document.createElement("div");
  el.innerHTML = getCurrentSelectionHTML();
  return el.innerText;
}

/**
 * Renders the html from the blockvalue string and insert
 * highlight tags to produce a valid HTML.
 * @param htmlValue {string} which may contain html.
 * @param tagName {string} Name of the highlight tag.
 * @return {string} string with valid html tags.
 */
export function renderHTMLAndApplyHighlightingCorrectly(htmlValue, tagName) {
  const blockWrapper = document.createElement("div");
  blockWrapper.innerHTML = htmlValue;
  /**
   * We apply highlighting for every child node, if the node
   * is a text node then we are creating our highlighting tag and
   * replace the text node with our highlighting node.
   */
  for (let node of blockWrapper.childNodes) {
    const currentHTML = node.innerHTML;
    if (node.nodeType === Node.TEXT_NODE) {
      const textContent = node.textContent;
      const newNode = document.createElement(`${tagName}`);
      newNode.innerHTML = textContent;
      blockWrapper.replaceChild(newNode, node);
    } else {
      node.innerHTML = `<${tagName}>${currentHTML}</${tagName}>`;
    }
  }
  return blockWrapper.innerHTML;
}
