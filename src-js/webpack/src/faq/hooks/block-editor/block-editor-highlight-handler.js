/**
 * GutenbergHighlightHandler handles the highlight event from event handler and
 * applies the format type to gutenberg
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import { on } from "backbone";
/**
 * Internal dependencies.
 */
import { FAQ_HIGHLIGHT_TEXT } from "../../constants/faq-hook-constants";
import { FAQ_ANSWER_FORMAT_NAME, FAQ_QUESTION_FORMAT_NAME } from "./block-editor-format-type-handler";
import { applyFormat } from "@wordpress/rich-text";
import { FAQ_ANSWER_HIGHLIGHTING_CLASS, FAQ_QUESTION_HIGHLIGHTING_CLASS } from "../tinymce/tinymce-highlight-handler";

class BlockEditorHighlightHandler {
  constructor() {
    this.props = null;
  }

  /**
   * Get the format type which needs to be applied on the the selection
   * @param data
   * @return {{attributes: {}}}
   */
  getFormatFromEventData(data) {
    const { isQuestion, id } = data;
    const format = {
      attributes: {}
    };
    /**
     * Apply format depending on the type.
     */
    if (isQuestion) {
      format.attributes.id = `${FAQ_QUESTION_HIGHLIGHTING_CLASS}--${id}`;
      format.type = FAQ_QUESTION_FORMAT_NAME;
    } else {
      format.attributes.id = `${FAQ_ANSWER_HIGHLIGHTING_CLASS}--${id}`;
      format.type = FAQ_ANSWER_FORMAT_NAME;
    }
    return format;
  }

  /**
   * Apply the format to the block value and return the applied value
   * @param formatToBeApplied
   * @param blockValue
   */
  applyFormatAndReturnAppliedValue(formatToBeApplied, blockValue) {
    const el = document.createElement("span");
    el.innerHTML = blockValue;
    const richText = wp.richText.create({
      html: blockValue,
      element: el
    });
    return wp.richText.applyFormat(richText, formatToBeApplied);
  }

  /**
   * Apply format for a single block.
   * @param formatToBeApplied
   */
  applyFormattingForSingleBlock(formatToBeApplied) {
    // single block, so we need to find start and end index.
    const startIndex = wp.data.select("core/block-editor").getSelectionStart().offset;
    const endIndex = wp.data.select("core/block-editor").getSelectionEnd().offset;
    // we can get the selected block content and create a rich text element.
    const selectedBlock = wp.data.select("core/block-editor").getSelectedBlock();
    const blockValue = this.getBlockValue(selectedBlock);
    if (blockValue !== undefined) {
      const content = `<span id="234234" class="wl-faq--answer">${blockValue}</span>`;
      wp.data.dispatch( 'core/block-editor' ).updateBlockAttributes( selectedBlock.clientId, {content: content} )
    }
  }

  /**
   * Returns the block value, ie the innerHTML with formatting applied.
   * @param block
   * @return {*}
   */
  getBlockValue(block) {
    const attrs = block.attributes;
    return attrs.content ? attrs.content : attrs.values;
  }

  /**
   * Loops through the blocks and apply formatting for all.
   * @param formatToBeApplied
   * @param blocks
   */
  applyFormattingForMultipleBlocks(formatToBeApplied, blocks) {
    for (let block of blocks) {
      const blockValue = this.getBlockValue(block);
      if (blockValue !== undefined) {
        /**
         * We need to create a rich text element in order
         * to automatically parse the formats in the text
         * for us, so to do that we are creating a fake element
         * span and then append the block html in to it.
         */
        console.log(this.applyFormatAndReturnAppliedValue(formatToBeApplied, blockValue));
      }
    }
  }

  /**
   * Selection can be either multiple blocks or a single block
   * with start and end index.
   * @param formatToBeApplied {object}
   */
  applyFormattingBasedOnType(formatToBeApplied) {
    const blocks = wp.data.select("core/block-editor").getMultiSelectedBlocks();
    if (blocks.length > 0) {
      // it indicates all the blocks are selected without needing to use
      // the start or end index, so loop through the blocks and highlight it.
      this.applyFormattingForMultipleBlocks(formatToBeApplied, blocks);
    } else {
      this.applyFormattingForSingleBlock(formatToBeApplied);
    }
  }
  /**
   * Start listening for highlight events from
   * the store.
   */
  listenForHighlightEvent() {
    on(FAQ_HIGHLIGHT_TEXT, data => {
      const format = this.getFormatFromEventData(data);
      // check if it is a multi selection.
      this.applyFormattingBasedOnType(format);
    });
  }
}

export default BlockEditorHighlightHandler;
