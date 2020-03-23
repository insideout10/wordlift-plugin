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
    /**
     * The list of the nodes which should not be highlighted.
     * @type {Node[]}
     */
    this.nodesShouldNotBeHighlighted = [];
    /**
     * The list of the nodes which should be highlighted.
     * @type {Node[]}
     */
    this.nodesToBeHighlighted = [];
    /**
     * The list of the nodes which should be added to start container,
     * they are created by using the startOffset, also endOffset if the selected nodes are
     * in same parent.
     * @type {Node[]}
     */
    this.nodesToBeAddedOnStartContainer = [];
    /**
     * The list of the nodes which should be added to start container,
     * they are created by using the endOffset
     * in same parent.
     * @type {Node[]}
     */
    this.nodesToBeAddedOnEndContainer = [];
    /**
     * Process the range, and extract the nodes to be highlighted
     * and the nodes which should not be highlighted.
     */
    this.processRange(range);
  }
  getProcessedRange() {
    return {
      nodesToBeHighlighted: this.nodesToBeHighlighted,
      nodesShouldNotBeHighlighted: this.nodesShouldNotBeHighlighted
    }
  }

  /**
   * Process a text node and add it to the highlighted or not highlighted list
   * if the textcontent is not empty.
   * @param node {Text}
   * @param container {Array}
   * @param shouldBeHighlighted {Boolean}
   */
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

  /**
   * Split in to two nodes by the offset, used to split the text nodes in
   * to two.
   * @param text Text content which needs to split.
   * @param offset {Integer}
   * @return {{startNode: Text, endNode: Text}}
   */
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
    // Dont remove the reference node, set it to empty (Tinymce compatibility)
    referenceNode.textContent = ""
  }

  /**
   * If the startContainer and endContainer are same then apply the offsets
   * and return the nodes.
   * @param range {Range}
   * @return {{startNode: Text, middleNode: Text, endNode: Text}}
   */
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
