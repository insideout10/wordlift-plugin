/**
 * Internal dependencies.
 */
import BlockEditorFormatTypeHandler from "./block-editor-format-type-handler";
import BlockEditorHighlightHandler from "./block-editor-highlight-handler";
import BlockEditorToolbarHandler from "./block-editor-handler";
import FaqTextEditorHook from "../interface/faq-text-editor-hook";
import BlockEditorToolbarButtonRegister from "./block-editor-toolbar-button-register";

export const FAQ_GUTENBERG_TOOLBAR_BUTTON_CLASS_NAME = "wl-faq-gutenberg-toolbar-button";

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
    const toolbarRegister = new BlockEditorToolbarButtonRegister(this.wp, highlightHandler);
    toolbarRegister.registerToolbarButton();
  }

  showFloatingActionButton() {
    /**
     * Initialize event handler to listen for text selection,
     * enable/disable the toolbar button.
     */
    new BlockEditorToolbarHandler();
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
