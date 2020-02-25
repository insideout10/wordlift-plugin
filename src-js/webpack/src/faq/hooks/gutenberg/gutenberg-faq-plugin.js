/**
 * Internal dependencies.
 */
import GutenbergFormatTypeHandler from "./gutenberg-format-type-handler";
import GutenbergHighlightHandler from "./gutenberg-highlight-handler";
import GutenbergToolbarHandler from "./gutenberg-toolbar-handler";
import FaqTextEditorHook from "../interface/faq-text-editor-hook";
import GutenbergToolbarButtonRegister from "./gutenberg-toolbar-button-register";
import GutenbergClickHandler from "./gutenberg-click-handler";

export const FAQ_GUTENBERG_TOOLBAR_BUTTON_CLASS_NAME = "wl-faq-gutenberg-toolbar-button";

class GutenbergFaqPlugin extends FaqTextEditorHook {
  constructor(wp) {
    super();
    this.wp = wp;
  }

  performTextHighlighting() {
    /**
     * Register all the format types required by FAQ
     * for the gutenberg
     */
    const formatTypeHandler = new GutenbergFormatTypeHandler();
    formatTypeHandler.registerAllFormatTypes();
    /**
     * Event handler / store emits highlight event upon faqitem
     * save or edit.
     */
    const highlightHandler = new GutenbergHighlightHandler();
    highlightHandler.listenForHighlightEvent();
    const toolbarRegister = new GutenbergToolbarButtonRegister(this.wp, highlightHandler);
    toolbarRegister.registerToolbarButton();
  }

  showFloatingActionButton() {
    /**
     * Initialize event handler to listen for text selection,
     * enable/disable the toolbar button.
     */
    new GutenbergToolbarHandler();
    new GutenbergClickHandler();
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
const adapter = new GutenbergFaqPlugin(window.wp);
adapter.initialize();
