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
    this.nodesToBeHighlighted = [];
    this.nodesToBeAddedOnStartContainer = [];
    this.nodesToBeAddedOnEndContainer = [];
    this.processRange(range);
  }
  getProcessedRange() {
    return {
      nodesToBeHighlighted: this.nodesToBeHighlighted,
      nodesShouldNotBeHighlighted: this.nodesShouldNotBeHighlighted
    }
  }

  ifTextContentNotEmptyPushNode(node, container, shouldBeHighlighted = false) {
    if (node.textContent !== "") {
      if ( shouldBeHighlighted ) {
        this.nodesToBeHighlighted.push(node)
      }
      else {
        this.nodesShouldNotBeHighlighted.push(node);
      }
      container.push(node);
    }
  }

  splitToTwoNodesByOffset(text, offset) {
    const startNode = document.createTextNode(text.slice(0, offset));
    const endNode = document.createTextNode(text.slice(offset, text.length));
    return {startNode, endNode}
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
      this.ifTextContentNotEmptyPushNode(startNode, this.nodesToBeAddedOnStartContainer);
      this.nodesToBeAddedOnStartContainer.push(middleNode);
      this.nodesToBeHighlighted.push(middleNode);
      this.ifTextContentNotEmptyPushNode(endNode, this.nodesToBeAddedOnStartContainer);
    }
    else {
      // the start and end containers are in different parent element

      /**
       * For the start container the end node is the node
       * which should be highlighted, start node should not be highlighted.
       */
      let {startNode, endNode } = this.splitToTwoNodesByOffset(range.startContainer.textContent, range.startOffset)
      this.ifTextContentNotEmptyPushNode(startNode, this.nodesToBeAddedOnStartContainer);
      this.ifTextContentNotEmptyPushNode(endNode, this.nodesToBeAddedOnStartContainer, true);

      /**
       * For the end container, the start node should be highlighted
       * and the end node shouldn't be highlighted.
       */
      let endContainerNodes = this.splitToTwoNodesByOffset(range.endContainer.textContent, range.endOffset);
      this.ifTextContentNotEmptyPushNode(endContainerNodes.startNode, this.nodesToBeAddedOnEndContainer, true);
      this.ifTextContentNotEmptyPushNode(endContainerNodes.endNode, this.nodesToBeAddedOnEndContainer);

    }
    /**
     * After creating text nodes we append it to the parent
     * element of start container.
     */
    this.appendCreatedNodesToParentElement(
        range.startContainer.parentElement,
        range.startContainer,
        this.nodesToBeAddedOnStartContainer
    );
    /**
     * After creating text nodes we append it to the parent
     * element of end container.
     */
    this.appendCreatedNodesToParentElement(
        range.endContainer.parentElement,
        range.endContainer,
        this.nodesToBeAddedOnEndContainer
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
