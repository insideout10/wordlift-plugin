/**
 * This file is automatically loaded by the tinymce via registering in backend.
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Internal dependencies.
 */
import TinymceToolbarHandler from "./tinymce-toolbar-handler";

const FAQ_TINYMCE_PLUGIN_NAME = "faq-tinymce-plugin";

tinymce.PluginManager.add(FAQ_TINYMCE_PLUGIN_NAME, editor => {
  const toolBarHandler = new TinymceToolbarHandler(editor);
  toolBarHandler.addButtonToToolBar();
});
