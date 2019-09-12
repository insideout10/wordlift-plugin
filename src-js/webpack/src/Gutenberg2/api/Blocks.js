import Block from "./Block";

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
   */
  constructor(blocks, dispatch) {
    /** @var {Block[]} */
    this._blocks = blocks.map(x => new Block(x, dispatch));
  }

  *[Symbol.iterator]() {
    yield this._blocks;
  }

  replace(pattern, replacement) {
    for (const block of this._blocks) block.replace(pattern, replacement);
  }

  apply() {
    for (const block of this._blocks) block.apply();
  }

  static create(blocks, dispatch) {
    return new this(
      collectBlocks(blocks, block => "core/paragraph" === block.name || "core/freeform" === block.name),
      dispatch
    );
  }
}
