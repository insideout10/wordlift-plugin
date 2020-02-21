import { trigger, on } from "backbone";
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED, FAQ_HIGHLIGHT_TEXT } from "../../constants/faq-hook-constants";
import GutenbergFormatTypeHandler, { FAQ_QUESTION_FORMAT_NAME } from "./gutenberg-format-type-handler";
import GutenbergHighlightHandler from "./gutenberg-highlight-handler";

/**
 * Register all the format types required by FAQ
 * for the gutenberg
 */
const formatTypeHandler = new GutenbergFormatTypeHandler();
formatTypeHandler.registerAllFormatTypes();

const highlightHandler = new GutenbergHighlightHandler();
/**
 * Event handler / store emits highlight event upon faqitem
 * save or edit.
 */
highlightHandler.listenForHighlightEvent();




/**
 * Register the toolbar button and the format.
 */
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
        highlightHandler.selectedTextObject = props.value;
        const { text, start, end } = props.value;
        const selectedText = text.slice(start, end);
        trigger(FAQ_EVENT_HANDLER_SELECTION_CHANGED, {
          selectedText: selectedText,
          selectedHTML: selectedText
        });
      },
      isActive: false
    });
  };

  wp.richText.registerFormatType("wordlift/faq-plugin", {
    title: "Add Question/Answer",
    tagName: "faq-gutenberg",
    className: null,
    edit: AddFaqButton
  });
})(window.wp);
