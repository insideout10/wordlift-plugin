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
import { FAQ_HIGHLIGHT_TEXT, FAQ_ITEM_DELETED } from "../../constants/faq-hook-constants";
import { FAQ_ANSWER_FORMAT_NAME, FAQ_QUESTION_FORMAT_NAME } from "./block-editor-format-type-handler";
import TinymceHighlightHandler from "../tinymce/tinymce-highlight-handler";
import { WORDLIFT_STORE } from "../../../common/constants";
import HighlightHelper from "../helpers/highlight-helper";
import { toggleFormat } from "@wordpress/rich-text";

class BlockEditorHighlightHandler {
  constructor() {
    this.removeHighlightingFromEditorOnDeleteEvent();
  }

  /**
   * Remove highlighting if the faq item is deleted from
   * the faq items list, listen for the delete event and delete
   * the highlighting.
   */
  removeHighlightingFromEditorOnDeleteEvent() {
    on(FAQ_ITEM_DELETED, ({ id, type }) => {
      const blocks = wp.data.select("core/block-editor").getBlocks();
      for (let block of blocks) {
        const { blockValue, attributeKeyName } = BlockEditorHighlightHandler.getBlockValueAndKeyName(block);
        if (blockValue !== null && attributeKeyName !== null) {
          const attributes = {};
          attributes[attributeKeyName] = HighlightHelper.removeHighlightingBasedOnType(id.toString(), type, blockValue);
          // Set the altered HTML to the block.
          wp.data.dispatch("core/block-editor").updateBlockAttributes(block.clientId, attributes);
        }
      }
    });
  }

  /**
   * Get the format type which needs to be applied on the the selection
   * @param data
   * @return {{attributes: {}}}
   */
  getFormatFromEventData(data) {
    const { isQuestion, id } = data;
    const format = {
      attributes: {
        class: id.toString()
      }
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
   * Apply format for a single block.
   * @param formatToBeApplied
   */
  applyFormattingForSingleBlock(formatToBeApplied) {
    const selectedBlock = wp.data.select("core/block-editor").getSelectedBlock();
    /**
     * If the selected block is classic editor, then dont apply the format.
     */
    if (selectedBlock.name !== "core/freeform") {
      const { onChange, value } = wp.data.select(WORDLIFT_STORE).getBlockEditorFormat();
      onChange(toggleFormat(value, formatToBeApplied));
    }
  }

  /**
   * Returns the block value and the attribute key name, ie the innerHTML with formatting applied.
   * @param block
   * @return
   */
  static getBlockValueAndKeyName(block) {
    // Every block have an attribute in different name other than content
    // so this code determines the attribute key name by assuming the following
    const attrs = block.attributes;
    // Bail out if we dont have attributes.
    if (attrs === undefined) {
      return {
        blockValue: null,
        attributeKeyName: null
      };
    }
    /**
     * Assumed conditions.
     * The attribute would definitely will have some string in the original content
     * 1. The type of key name would be string, since html is a string
     * 2. The more the length of the match, the higher is chance for the attribute key.
     */
    let length = 0;
    let attributeKeyName = null;
    let blockValue = null;
    for (let key of Object.keys(attrs)) {
      const value = attrs[key];
      if (typeof value === "string" && value.length > length) {
        length = value.length;
        attributeKeyName = key;
        blockValue = attrs[attributeKeyName];
      }
    }
    return {
      blockValue: blockValue,
      attributeKeyName: attributeKeyName
    };
  }

  /**
   * Loops through the blocks and apply formatting for all.
   * @param formatToBeApplied
   * @param blocks
   * @param eventData
   */
  applyFormattingForMultipleBlocks(formatToBeApplied, blocks, eventData) {
    for (let block of blocks) {
      const { blockValue, attributeKeyName } = BlockEditorHighlightHandler.getBlockValueAndKeyName(block);
      if (blockValue !== null && attributeKeyName !== null) {
        const attributes = {};
        const tagName = TinymceHighlightHandler.getTagBasedOnHighlightedText(eventData.isQuestion);
        attributes[attributeKeyName] = HighlightHelper.highlightHTML(blockValue, tagName, eventData.id.toString());
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
