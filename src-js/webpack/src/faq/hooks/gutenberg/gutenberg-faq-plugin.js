/**
 *
 */
import GutenbergFormatTypeHandler from "./gutenberg-format-type-handler";
import GutenbergAddFAQItemHandler from "./gutenberg-add-faq-item-handler";
import { trigger } from "backbone";
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED } from "../../constants/faq-hook-constants";

/**
 * Register all the format types required by FAQ
 * for the gutenberg
 */
const formatTypeHandler = new GutenbergFormatTypeHandler();
formatTypeHandler.registerAllFormatTypes();

(function(wp) {
  const addFAQButton = function(props) {
    return wp.element.createElement(wp.blockEditor.RichTextToolbarButton, {
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
      },
      isActive: props.isActive
    });
  };
  const handler = new GutenbergAddFAQItemHandler(addFAQButton);
  handler.registerToolBarButton();
})(window.wp);

const addFAQItemHandler = new GutenbergAddFAQItemHandler();
addFAQItemHandler.registerToolBarButton();
