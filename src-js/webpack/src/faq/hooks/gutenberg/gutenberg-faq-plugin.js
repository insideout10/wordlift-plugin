import { trigger } from "backbone";
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED } from "../../constants/faq-hook-constants";

(function(wp) {
  const AddFaqButton = function(props) {
    return wp.element.createElement(wp.editor.RichTextToolbarButton, {
      title: "Add Question / Answer",
      icon: "plus-alt",
      onClick: function() {
        console.log(props)
        const { text, start, end } = props.value;
        const selectedText = text.slice(start, end);
        console.log(selectedText)
        trigger(FAQ_EVENT_HANDLER_SELECTION_CHANGED, { selectedText: selectedText, selectedHTML: selectedText });
      }
    });
  };
  wp.richText.registerFormatType("faq-gutenberg-plugin/faq-plugin", {
    title: "Add Question/Answer",
    tagName: "faq-gutenberg",
    className: null,
    edit: AddFaqButton
  });
})(window.wp);
