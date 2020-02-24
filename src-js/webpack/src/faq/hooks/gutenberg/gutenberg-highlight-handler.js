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
import { FAQ_ANSWER_FORMAT_NAME, FAQ_QUESTION_FORMAT_NAME } from "./gutenberg-format-type-handler";
import { applyFormat } from "@wordpress/rich-text";

class GutenbergHighlightHandler {
  constructor() {
    this.props = null;
  }
  /**
   * Start listening for highlight events from
   * the store.
   */
  listenForHighlightEvent() {
    on(FAQ_HIGHLIGHT_TEXT, result => {
      const { isQuestion, id } = result;
      /**
       * Apply format depending on the type.
       */
      if (isQuestion) {
        this.props.onChange(applyFormat(this.props.value, { type: FAQ_QUESTION_FORMAT_NAME }));
      } else {
        this.props.onChange(applyFormat(this.props.value, { type: FAQ_ANSWER_FORMAT_NAME }));
      }
    });
  }
}

export default GutenbergHighlightHandler;
