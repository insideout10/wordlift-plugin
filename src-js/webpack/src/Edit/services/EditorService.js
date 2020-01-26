/*global tinyMCE*/
/**
 * Services: EditorService.
 *
 * Provide TinyMCE editor services.
 *
 * @since 3.11.0
 */

/**
 * Cached TinyMCE instances.
 *
 * @since 3.11.0
 */
const instances = [];

/**
 * The `EditorService` class provides access to the in-page TinyMCE editor.
 *
 * @since 3.11.0
 */
class EditorService {
  /**
   * Get the TinyMCE editor with the specified id.
   *
   * @since 3.11.0
   * @param {string} [id=content] The editor id, by default `content`.
   * @return {Object} A TinyMCE editor instance.
   */
  get(id = window["wlSettings"]["default_editor_id"]) {
    // Get the editor id from the `wlSettings` or use `content`.

    // Allow 3rd parties to change the editor id.
    //
    // @see https://github.com/insideout10/wordlift-plugin/issues/850.
    // @see https://github.com/insideout10/wordlift-plugin/issues/851.
    const editorId = "undefined" !== typeof window["wp"].hooks ? window["wp"].hooks.applyFilters(
      "wl_default_editor_id",
      id
    ) : id;

    return instances[editorId]
      ? instances[editorId]
      : (instances[editorId] = tinyMCE.get(editorId));
  }
}

// Finally export the `EditorService`.
export default new EditorService();
