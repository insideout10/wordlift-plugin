import { trigger } from "backbone";
import { FAQ_EVENT_HANDLER_SELECTION_CHANGED } from "../../constants/faq-hook-constants";
import GutenbergFormatTypeHandler from "./gutenberg-format-type-handler";
import GutenbergHighlightHandler from "./gutenberg-highlight-handler";
import { getCurrentSelectionHTML } from "./helpers";
import GutenbergToolbarHandler from "./gutenberg-toolbar-handler";
import FaqTextEditorHook from "../interface/faq-text-editor-hook";
import GutenbergToolbarButtonRegister from "./gutenberg-toolbar-button-register";

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
    new GutenbergToolbarButtonRegister(this.wp, highlightHandler);
  }

  showFloatingActionButton() {
    /**
     * Initialize event handler to listen for text selection,
     * enable/disable the toolbar button.
     */
    new GutenbergToolbarHandler();
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
