/**
 *
 * @param {array} collector
 * @param {{name, innerBlocks}[]} blocks
 */
import BlockOps from "./BlockOps";

export const collectBlocks = (accumulator, blocks) => {
  blocks.forEach(block => {
    if ("core/paragraph" === block.name || "core/freeform" === block.name) {
      accumulator.push(block);
    }

    collectBlocks(accumulator, block.innerBlocks);
  });

  return accumulator;
};

export default class BlocksOps {
  constructor(editor, dispatch) {
    this._editor = editor;
    this._dispatch = dispatch;
    this._blocks = collectBlocks([], this._editor.getBlocks());
    /** @var {BlockOps[]} */
    this._blockOps = [];
    this._blockSeparator = ".\n";
    this._blockSeparatorLength = this._blockSeparator.length;

    this.getHtml();
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

            this._blockOps.push(
              new BlockOps(this._dispatch, start, cursor, block.clientId, block.attributes.content, false)
            );

            return content;
          })
          .join(this._blockSeparator));
  }

  /**
   * Get the block index for the specified absolute position.
   *
   * @param {number} position The absolute position.
   * @returns {{false}|number} The block index (zero-based) or false if not found.
   */
  getBlockIndex(position) {
    // Cycle through the blocks until we found the one for the provided position.
    for (let i = 0; i < this._blockOps.length; i++) {
      const blockOps = this._blockOps[i];

      if (position >= blockOps.start || position < blockOps.end) {
        return i;
      }
    }

    return false;
  }

  getBlock(position) {
    const idx = this.getBlockIndex(position);

    if (false === idx) return false;

    return this._blockOps[idx];
  }

  applyChanges() {
    this._blockOps.forEach(blockOps => blockOps.applyChanges());
  }
}
