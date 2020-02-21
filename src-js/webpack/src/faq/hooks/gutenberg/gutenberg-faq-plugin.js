import { trigger, on } from "backbone";
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED, FAQ_HIGHLIGHT_TEXT } from "../../constants/faq-hook-constants";
import GutenbergFormatTypeHandler from "./gutenberg-format-type-handler";

/**
 * Register all the format types required by FAQ
 * for the gutenberg
 */
const formatTypeHandler = new GutenbergFormatTypeHandler();
formatTypeHandler.registerAllFormatTypes();

(function(wp) {
  const AddFaqButton = function(props) {
    return wp.element.createElement(wp.editor.RichTextToolbarButton, {
      title: "Add Question / Answer",
      icon: "plus-alt",
      onClick: function() {
        /**
         * We pass props.value in to extras, in order to make
         * gutenberg highlight on the highlight event.
         */
        const { text, start, end } = props.value;
        const selectedText = text.slice(start, end);
        trigger(FAQ_EVENT_HANDLER_SELECTION_CHANGED, {
          selectedText: selectedText,
          selectedHTML: selectedText,
          extras: props.value
        });
      }
    });
  };

  wp.richText.registerFormatType("wordlift/faq-plugin", {
    title: "Add Question/Answer",
    tagName: "faq-gutenberg",
    className: null,
    edit: AddFaqButton
  });
})(window.wp);
