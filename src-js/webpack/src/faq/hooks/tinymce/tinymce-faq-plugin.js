/**
 * This file is automatically loaded by the tinymce via registering in backend.
 * It emits events captured by the faq event handler class.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
/**
 * Internal dependencies.
 */
import TinymceHighlightHandler from "./tinymce-highlight-handler";
import FaqTextEditorHook from "../interface/faq-text-editor-hook";
import PopoverElement from "../popover/popover-element";

const FAQ_TINYMCE_PLUGIN_NAME = "wl_faq_tinymce";
const tinymce = global["tinymce"];

/**
 * This class extends from abstract class FaqTextEditorHook which defines
 * how the FAQ text editor hook should function.
 */
class TinymceFaqPlugin extends FaqTextEditorHook {
  constructor() {
    super();
    this.editor = null;
    this.highlightHandler = null;
    const self = this;
    tinymce.PluginManager.add(FAQ_TINYMCE_PLUGIN_NAME, function(editor) {
      self.editor = editor;
      // initialize the handlers.
      self.initialize();
    });
  }

  performTextHighlighting() {
    this.highlightHandler = new TinymceHighlightHandler(this.editor);
  }

  showFloatingActionButton() {
    // Should be replaced with new popover element here.
    new PopoverElement();
  }

  initialize() {
    this.performTextHighlighting();
    this.showFloatingActionButton();
  }
}

/**
 * This hook is called by tinymce for registering plugin, so initialize the hook
 * in this file itself.
 */
new TinymceFaqPlugin();
