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
import {FAQ_ANSWER_HIGHLIGHTING_CLASS, FAQ_QUESTION_HIGHLIGHTING_CLASS} from "../tinymce/tinymce-highlight-handler";

class BlockEditorHighlightHandler {
  constructor() {
    this.props = null;
  }
  /**
   * Start listening for highlight events from
   * the store.
   */
  listenForHighlightEvent() {
    on(FAQ_HIGHLIGHT_TEXT, result => {
      if (this.props !== null) {
        const {isQuestion, id} = result;
        const format = {
          attributes: {}
        };
        /**
         * Apply format depending on the type.
         */
        if (isQuestion) {
          format.attributes.id = `${FAQ_QUESTION_HIGHLIGHTING_CLASS}--${id}`;
          format.type = FAQ_QUESTION_FORMAT_NAME;
          this.props.onChange(applyFormat(this.props.value, format));
        } else {
          format.attributes.id = `${FAQ_ANSWER_HIGHLIGHTING_CLASS}--${id}`;
          format.type = FAQ_ANSWER_FORMAT_NAME;
          this.props.onChange(applyFormat(this.props.value, format));
        }
      }
    });
  }
}

export default BlockEditorHighlightHandler;
