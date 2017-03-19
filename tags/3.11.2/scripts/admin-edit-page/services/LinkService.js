/**
 * Services: Link Service.
 *
 * A service which handles the link/no link attribute for entity's occurrences.
 *
 * @since 3.11.0
 */

/**
 * Internal dependencies
 */
import EditorService from './EditorService';

/**
 * Define the `LinkService` class.
 *
 * @since 3.11.0
 */
class LinkService {

	/**
	 * Set the link flag on the provided `occurrences`.
	 *
	 * @since 3.11.0
	 * @param {Array} occurrences An array of occurrences ids (which match dom
	 *     elements).
	 * @param {boolean} value True to enable linking or false to disable it.
	 */
	setLink( occurrences, value ) {
		// If the request is to enable links, remove the `wl-no-link` class on
		// all the occurrences.
		if ( value ) {
			occurrences
				.forEach( x => EditorService.get().dom.removeClass( x, 'wl-no-link' ) );
		} else {
			// If the request is to disable links, add the `wl-no-link` class to
			// all occurrences.
			occurrences
				.forEach( x => EditorService.get().dom.addClass( x, 'wl-no-link' ) );
		}
	}

	/**
	 * Get the link flag given the provided `occurrences`. A link flag is
	 * considered true when at least one occurrences enables linking.
	 *
	 * @since 3.11.0
	 * @param {Array} occurrences An array of occurrences dom ids.
	 * @return {boolean} True if at least one occurrences enables linking,
	 *     otherwise false.
	 */
	getLink( occurrences ) {
		return occurrences.reduce( ( acc, id ) => {
			return acc || ! EditorService.get().dom.hasClass( id, 'wl-no-link' );
		}, false );
	}

}

// Finally export the `LinkService`.
export default new LinkService();
