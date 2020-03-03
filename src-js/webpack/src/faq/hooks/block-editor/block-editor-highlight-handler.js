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
import TinymceHighlightHandler from "../tinymce/tinymce-highlight-handler";
import { SELECTION_CHANGED } from "../../../common/constants";

class BlockEditorHighlightHandler {
  constructor() {
    this.props = null;
    this.singleBlockSelectionValue = null;
    this.onChange = null;
    /**
     * When the single block is selected then we need to get
     * all the format types of that block, along with the value
     * property
     */
    on(SELECTION_CHANGED, ({ value, onChange }) => {
      this.singleBlockSelectionValue = value;
      this.onChange = onChange;
    });
  }

  /**
   * Get the format type which needs to be applied on the the selection
   * @param data
   * @return {{attributes: {}}}
   */
  getFormatFromEventData(data) {
    const { isQuestion } = data;
    const format = {
      attributes: {}
    };
    /**
     * Apply format depending on the type.
     */
    if (isQuestion) {
      format.type = FAQ_QUESTION_FORMAT_NAME;
    } else {
      format.type = FAQ_ANSWER_FORMAT_NAME;
    }
    return format;
  }

  /**
   * Create a rich text element from the supplied range.
   * @param range {Range}
   * @param element {Element}
   * @return element {Object: {key: {String}}}
   */
  createRichTextElementFromRange(element, range) {
    return wp.richText.create({
      range,
      element
    });
  }

  /**
   * Apply format for a single block.
   * @param formatToBeApplied
   */
  applyFormattingForSingleBlock(formatToBeApplied) {
    if (this.onChange !== null && this.singleBlockSelectionValue !== null) {
      this.onChange(wp.richText.applyFormat(this.singleBlockSelectionValue, formatToBeApplied));
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
  applyFormattingForMultipleBlocks(formatToBeApplied, blocks, eventData) {
    for (let block of blocks) {
      const blockValue = this.getBlockValue(block);
      console.log(block)
      if (blockValue !== undefined) {
        const attributes = {};
        const tagName = TinymceHighlightHandler.getTagBasedOnHighlightedText(eventData.isQuestion);
        const resultHTML = `<${tagName}>${blockValue}</${tagName}>`;
        // check if the block has content attribute or values attribute.
        if (block.attributes.content !== undefined) {
          attributes.content = resultHTML;
        } else if (block.values !== undefined) {
          attributes.values = resultHTML;
        }
        // Set the altered HTML to the block.
        wp.data.dispatch("core/block-editor").updateBlockAttributes(block.clientId, attributes);
      }
    }
  }

  /**
   * Selection can be either multiple blocks or a single block
   * with start and end index.
   * @param formatToBeApplied {object}
   * @param eventData {object}
   */
  applyFormattingBasedOnType(formatToBeApplied, eventData) {
    const blocks = wp.data.select("core/block-editor").getMultiSelectedBlocks();
    if (blocks.length > 0) {
      // it indicates all the blocks are selected without needing to use
      // the start or end index, so loop through the blocks and highlight it.
      this.applyFormattingForMultipleBlocks(formatToBeApplied, blocks, eventData);
    } else {
      this.applyFormattingForSingleBlock(formatToBeApplied, eventData);
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
      this.applyFormattingBasedOnType(format, data);
    });
  }
}

export default BlockEditorHighlightHandler;
