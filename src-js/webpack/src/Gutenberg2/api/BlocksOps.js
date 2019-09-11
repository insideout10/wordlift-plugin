/**
 *
 * @param {array} collector
 * @param {{name, innerBlocks}[]} blocks
 */
import BlockOps from "./BlockOps";

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
    /** @var {BlockOps[]} */
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

            this._mappings.push(new BlockOps(start, cursor, block.clientId, block.attributes.content, false));

            return content;
          })
          .join(this._blockSeparator));
  }

  insertHtml(at, fragment) {
    // Get the block index at the specified position.
    const idx = this.getBlockIndex(at);

    // Return if a block isn't found.
    if (false === idx) return;

    // Get the block mapping.
    const mapping = this._mappings[idx];

    // Calculate the position relative to the block.
    const localAt = at - mapping.start;

    // Insert the HTML.
    mapping.content = mapping.content.substring(0, localAt) + fragment + mapping.content.substring(localAt);
  }

  /**
   * Get the block index for the specified absolute position.
   *
   * @param {number} position The absolute position.
   * @returns {{false}|number} The block index (zero-based) or false if not found.
   */
  getBlockIndex(position) {
    // Cycle through the blocks until we found the one for the provided position.
    for (let i = 0; i < this._mappings.length; i++) {
      const mapping = this._mappings[i];

      if (position >= mapping.start || position < mapping.end) {
        return i;
      }
    }

    return false;
  }

  getBlock(position) {
    const idx = this.getBlockIndex(position);

    if (false === idx) return false;

    return this._mappings[idx];
  }

  applyChanges() {
    this._mappings.forEach(mapping => {
      if (mapping.dirty) {
        const blockId = mapping.clientId;
        const content = mapping.content;
        this._dispatch.updateBlock(blockId, {
          attributes: { content }
        });
      }
    });
  }
}
