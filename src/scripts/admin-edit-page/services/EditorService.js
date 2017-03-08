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
	get( id = 'content' ) {
		return instances[ id ] ? instances[ id ] : instances[ id ] = tinyMCE.get( id );
	}

}

// Finally export the `EditorService`.
export default new EditorService;
