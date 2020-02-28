/**
 * Internal dependencies.
 */
import BlockEditorFormatTypeHandler from "./block-editor-format-type-handler";
import BlockEditorHighlightHandler from "./block-editor-highlight-handler";
import BlockEditorFabHandler from "./block-editor-fab-handler";
import FaqTextEditorHook from "../interface/faq-text-editor-hook";
import BlockEditorFabButtonRegister from "./block-editor-fab-button-register";


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
    const toolbarRegister = new BlockEditorFabButtonRegister(this.wp, highlightHandler);
    toolbarRegister.registerFabButton();
  }

  showFloatingActionButton() {
    /**
     * Initialize event handler to listen for text selection,
     * enable/disable the toolbar button.
     */
    new BlockEditorFabHandler();
  }

  initialize() {
    this.performTextHighlighting();
    this.showFloatingActionButton();
  }
}

/**
 * This hook is automatically loaded with block editor, so
 * we can just initailize the hook here.
 */
const adapter = new BlockEditorFaqPlugin(global["wp"]);
adapter.initialize();
