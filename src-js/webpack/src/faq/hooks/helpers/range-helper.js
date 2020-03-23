/**
 * Range helper creates the WlRange object by
 * splitting the text nodes selected by the user and return data
 * to be used for highlighting node.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * @typedef {Object} WlRange WlRange splits the text nodes by offset and returns the nodes.
 * @property range {Range} DOM range object.
 * @property nodesToNotBeHighlighted {Array} A list of text nodes which should not be highlighted.
 * @property nodesToBeHighlighted {Array}  A list of nodes which needs to be highlighted.
 */
class RangeHelper {
  constructor(range) {
    this.range = range;
    this.nodesShouldNotBeHighlighted = [];
    this.nodesToBeAddedOnStartContainer = [];
    this.nodesToBeHighlighted = [];
    this.processRange(range);
  }

  ifTextContentNotEmptyPushNode(node) {
    if (node.textContent !== "") {
      this.nodesShouldNotBeHighlighted.push(node);
      this.nodesToBeAddedOnStartContainer.push(node);
    }
  }
  /**
   * Window Range Object.
   * @param range {Range}
   */
  processRange(range) {
    /**
     * If the start and end containers are equal then the range
     * is in a single parent node.
     */
    if (range.startContainer === range.endContainer) {
      const { startNode, middleNode, endNode } = this.createTextNodesFromRange(range);
      this.ifTextContentNotEmptyPushNode(startNode);
      this.nodesToBeAddedOnStartContainer.push(middleNode);
      this.nodesToBeHighlighted.push(middleNode);
      this.ifTextContentNotEmptyPushNode(endNode);
    }
    /**
     * After creating text nodes we append it to the parent
     * element.
     */
    this.appendCreatedNodesToParentElement(
        range.startContainer.parentElement,
        range.startContainer,
        this.nodesToBeAddedOnStartContainer
    );
  }

  /**
   * The text nodes which are created by range helper is appended to parent
   * element.
   */
  appendCreatedNodesToParentElement(parentElement, referenceNode, nodesToBeInserted) {
    for ( let node of nodesToBeInserted ) {
      parentElement.insertBefore(node, referenceNode)
    }
    referenceNode.remove()
  }

  createTextNodesFromRange(range) {
    const text = range.startContainer.textContent;
    // split it by offset.
    const startNode = document.createTextNode(text.slice(0, range.startOffset));
    const middleNode = document.createTextNode(text.slice(range.startOffset, range.endOffset));
    const endNode = document.createTextNode(text.slice(range.endOffset, text.length));
    return { startNode, middleNode, endNode };
  }
}

export default RangeHelper;
