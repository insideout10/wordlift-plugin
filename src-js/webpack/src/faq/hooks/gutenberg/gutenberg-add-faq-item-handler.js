/**
 * GutenbergAddFAQItemHandler Registers the add faq item button to the toolbar in
 * the gutenberg.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
/**
 * External dependencies.
 */
import React from "react";

/**
 * WordPress dependencies
 */
import { registerFormatType } from "@wordpress/rich-text";
import { trigger } from "backbone";
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED } from "../../constants/faq-hook-constants";

const wp = global["wp"];

class GutenbergAddFAQItemHandler {
  constructor(addFAQButton) {
    this.addFAQButton = addFAQButton
  }
  /**
   * Registers the toolbar button.
   */
  registerToolBarButton() {
    registerFormatType("wordlift/faq-plugin", {
      title: "Add Question/Answer",
      tagName: "faq-gutenberg",
      className: null,
      edit: this.addFAQButton
    });
  }
}

export default GutenbergAddFAQItemHandler;
