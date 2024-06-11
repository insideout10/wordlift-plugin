import Block from "./block";
import TextBlock from "./block-types/text-block";
import BlockFactory from "./block-factory";

/**
 *
 * @param {{name,innerBlocks}[]} blocks
 * @param predicate
 * @param mapper
 * @param accumulator
 * @returns {Array}
 */
const collectBlocks = function(blocks, predicate = () => true, accumulator = []) {
  blocks.forEach(block => {
    // Add the block to the collection if it satisfies the predicate.
    if (predicate(block)) {
      accumulator.push(block);
    }

    // Collect inner blocks.
    collectBlocks(block.innerBlocks, predicate, accumulator);
  });

  return accumulator;
};

/**
 *
 */
export class Blocks {
  /**
   *
   * @param blocks
   * @param dispatch
   */
  constructor(blocks, dispatch) {
    this._blockSeparator = ".\n";
    this._blockSeparatorLength = this._blockSeparator.length;
    /** @var {Block[]} */
    this._blocks = [];

    let cursor = 0;
    this._html = blocks
      .map(block => {
        const start = cursor;
        const blockObj = BlockFactory.getBlock(block, dispatch, start);
        const content = blockObj.content;
        cursor += content.toString().length + this._blockSeparatorLength;
        blockObj.end = cursor;
        this._blocks.push(blockObj);
        return content;
      })
      .join(this._blockSeparator);

    console.debug("Blocks.c`tor", { html: this._html, blocks: this._blocks });
  }

  *[Symbol.iterator]() {
    yield this._blocks;
  }

  get html() {
    return this._html;
  }

  /**
   * Get the block index for the specified absolute position.
   *
   * @param {number} position The absolute position.
   * @returns {{false}|number} The block index (zero-based) or false if not found.
   */
  getBlockIndex(position) {
    // Cycle through the blocks until we found the one for the provided position.
    for (let i = 0; i < this._blocks.length; i++) {
      const block = this._blocks[i];

      if (position >= block.start && position < block.end) {
        return i;
      }
    }

    return false;
  }

  getBlock(position) {
    const idx = this.getBlockIndex(position);

    if (false === idx) return false;

    return this._blocks[idx];
  }

  replace(pattern, replacement) {
    for (const block of this._blocks) block.replace(pattern, replacement);
  }

  apply() {
    for (const block of this._blocks) block.apply();
  }

  static create(blocks, dispatch) {
    return new this(
      collectBlocks(blocks, block => "core/paragraph" === block.name || "core/freeform" === block.name
          || "core/list" === block.name || "core/table" === block.name),
      dispatch
    );
  }
}
