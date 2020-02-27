/**
 * External dependencies.
 */
import { trigger } from "backbone";
/**
 * Internal dependencies.
 */
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED } from "../../constants/faq-hook-constants";
import { getCurrentSelectionHTML } from "./helpers";
import { FAQ_GUTENBERG_TOOLBAR_BUTTON_CLASS_NAME } from "./gutenberg-faq-plugin";

/**
 * GutenbergToolbarButtonRegister Registers the toolbar button for the
 * gutenberg.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

class GutenbergToolbarButtonRegister {
  constructor(wp, highlightHandler) {
    this.wp = wp;
    this.highlightHandler = highlightHandler;
    this.addQuestionOrAnswerText = global["_wlFaqSettings"]["addQuestionOrAnswerText"]
  }
  registerToolbarButton() {
    this.wp.richText.registerFormatType("wordlift/faq-plugin", {
      title: this.addQuestionOrAnswerText,
      tagName: "faq-gutenberg",
      className: null,
      edit: this.getFAQButton()
    });
  }
  getFAQButton() {
    const self = this;
    return function(props) {
      return wp.element.createElement(wp.editor.RichTextToolbarButton, {
        title: self.addQuestionOrAnswerText,
        icon: "plus",
        className: FAQ_GUTENBERG_TOOLBAR_BUTTON_CLASS_NAME,
        onClick: function() {
          /**
           * We pass props.value in to extras, in order to make
           * gutenberg highlight on the highlight event.
           */
          self.highlightHandler.props = props;
          const { text, start, end } = props.value;
          const selectedText = text.slice(start, end);
          trigger(FAQ_EVENT_HANDLER_SELECTION_CHANGED, {
            selectedText: selectedText,
            selectedHTML: getCurrentSelectionHTML()
          });
        }
      });
    };
  }
}

export default GutenbergToolbarButtonRegister;
