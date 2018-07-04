function t(node, tilingFn, width, height) {
  tilingFn(node, 0, 0, width, height);

  return node;
}

function u(node, minWidth, minHeight) {
  for (var i = 0; i < node.children.length; i++) {
    const child = node.children[i];
    const width = child.x1 - child.x0;
    const height = child.y1 - child.y0;
    if (minWidth > width || minHeight > height) {
      // console.debug(
      //   `Node ${i} is too small [ ${width} over ${minWidth} ][ ${height} over ${minHeight} ].`
      // );
      return false;
    }
  }

  return true;
}

class TilingStrategy {
  constructor(minWidth, minHeight, delegatingTilingStrategy, scoreFn) {
    this.minWidth = minWidth;
    this.minHeight = minHeight;
    this.delegatingTilingStrategy = delegatingTilingStrategy;
    this.scoreFn = scoreFn;

    this.tile = this.tile.bind(this);
  }

  tile(node, x0, y0, x1, y1) {
    // console.debug("Tiling starting...", { node, x0, y0, x1, y1 });

    if (0 === x0 && 0 === y0) {
      const containerWidth = x1 - x0;
      const containerHeight = y1 - y0;

      this.collect(node, containerWidth, containerHeight);
    }

    this.delegatingTilingStrategy(node, x0, y0, x1, y1);

    // console.debug("Tiling complete.", { node, x0, y0, x1, y1 });
  }

  collect(node, containerWidth, containerHeight) {
    if (2 >= node.children.length) {
      return node;
    }

    let isMinimumWidthAndHeight = false;
    const collectedNodes = [];
    while (!isMinimumWidthAndHeight && 0 < node.children.length) {
      // Recalculate the total.
      node.sum(this.scoreFn);

      // console.debug(
      //   "Minimum width and height: ",
        (isMinimumWidthAndHeight = u(
          t(
            node,
            this.delegatingTilingStrategy,
            containerWidth,
            containerHeight
          ),
          this.minWidth,
          this.minHeight
        ));
      // );

      collectedNodes.splice(0, 0, node.children.pop());
    }

    // If we collected only one node for the 'others...' linked screen, we push
    // it back since it doesn't make sense to show one screen with only one tile.
    if (1 === collectedNodes.length) {
      // Push back the single node.
      node.children.push(collectedNodes[0]);
      return node;
    }

    // Update the children array by creating an "others..." node with the collected
    // nodes as children.
    node.children.push(this.createOthersNode(collectedNodes));
  }

  /**
   * Move the array of nodes into a new "others..." node's children property.
   *
   * @since 1.0.0
   * @param nodes
   * @returns {*}
   */
  createOthersNode(nodes) {
    const node = nodes[0];
    const othersNode = node.copy();
    othersNode.data = { name: "...", other: true };
    othersNode.parent = node;
    othersNode.children = nodes;
    othersNode.children.forEach(n => n.depth++);
    othersNode.listeners = {
      click: () => this.update(othersNode.copy())
    };

    return othersNode;
  }

  update(nodes) {}
}

export default TilingStrategy;
