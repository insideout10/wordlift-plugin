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
import {on} from "backbone";

/**
 * Internal dependencies.
 */
import {FAQ_HIGHLIGHT_TEXT} from "../../constants/faq-hook-constants";
import {FAQ_QUESTION_FORMAT_NAME} from "./gutenberg-format-type-handler";

class GutenbergHighlightHandler {
    constructor(wp) {
        this.wp = wp
    }
    /**
     * Start listening for highlight events from
     * the store.
     */
    listenForHighlightEvent() {
        on(FAQ_HIGHLIGHT_TEXT, result => {
            this.wp.richText.toggleFormat(
                result.extras,
                { type: FAQ_QUESTION_FORMAT_NAME }
            );
        });
    }
}

export default GutenbergHighlightHandler