/**
 * This file is automatically loaded by the tinymce via registering in backend.
 * It emits events captured by the faq event handler class.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Internal dependencies.
 */
import TinymceToolbarHandler from "./tinymce-toolbar-handler";
import TinymceHighlightHandler from "./tinymce-highlight-handler";

const FAQ_TINYMCE_PLUGIN_NAME = "wl_faq_tinymce";
const tinymce = global["tinymce"];
tinymce.PluginManager.add(FAQ_TINYMCE_PLUGIN_NAME, function (editor) {
  const toolBarHandler = new TinymceToolbarHandler(editor);
  toolBarHandler.addButtonToToolBar();
  const hightlightHandler = new TinymceHighlightHandler(editor)
});
