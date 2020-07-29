/**
 * Internal dependencies.
 */
import BlockEditorFormatTypeHandler from "./block-editor-format-type-handler";
import BlockEditorHighlightHandler from "./block-editor-highlight-handler";
import FaqTextEditorHook from "../interface/faq-text-editor-hook";

class BlockEditorFaqPlugin extends FaqTextEditorHook {
  constructor(wp) {
    super();
    this.wp = wp;
  }

  performTextHighlighting() {
    /**
     * Register all the format types required by FAQ
     * for the gutenberg
     */
    const formatTypeHandler = new BlockEditorFormatTypeHandler();
    formatTypeHandler.registerAllFormatTypes();
    /**
     * Event handler / store emits highlight event upon faqitem
     * save or edit.
     */
    const highlightHandler = new BlockEditorHighlightHandler();
    highlightHandler.listenForHighlightEvent();
  }

  initialize() {
    this.performTextHighlighting();
  }
}

/**
 * This hook is automatically loaded with block editor, so
 * we can just initialize the hook here.
 */
const adapter = new BlockEditorFaqPlugin(global["wp"]);
adapter.initialize();
