/**
 *
 * @param {array} collector
 * @param {{name, innerBlocks}[]} blocks
 */
const collectBlocks = (collector, blocks) => {
  blocks.forEach(block => {
    if ("core/paragraph" === block.name || "core/freeform" === block.name) {
      collector.push(block);
    }

    collectBlocks(collector, block.innerBlocks);
  });

  return collector;
};

export default class BlocksOps {
  constructor(editor, dispatch) {
    this._editor = editor;
    this._dispatch = dispatch;
    this._blocks = collectBlocks([], this._editor.getBlocks());
    this._mappings = [];
    this._blockSeparator = ".\n";
    this._blockSeparatorLength = this._blockSeparator.length;
  }

  getHtml() {
    let cursor = 0;

    return this.html
      ? this.html
      : (this.html = this._blocks
          .map(block => {
            const content = block.attributes.content;
            const start = cursor;
            cursor += content.length + this._blockSeparatorLength;

            this._mappings.push([start, cursor, block.clientId, block.attributes.content, false]);

            return content;
          })
          .join(this._blockSeparator));
  }

  insertHtml(at, fragment) {
    for (let i = 0; i < this._mappings.length; i++) {
      const mapping = this._mappings[i];

      if (at < mapping[0] || at >= mapping[1]) {
        continue;
      }

      const localAt = at - mapping[0];

      mapping[3] = mapping[3].substring(0, localAt) + fragment + mapping[3].substring(localAt);

      // Dirty.
      mapping[4] = true;
    }
  }

  applyChanges() {
    this._mappings.forEach(mapping => {
      const dirty = mapping[4];

      if (dirty) {
        const blockId = mapping[2];
        const content = mapping[3];
        this._dispatch.updateBlock(blockId, {
          attributes: { content }
        });
      }
    });
  }
}
