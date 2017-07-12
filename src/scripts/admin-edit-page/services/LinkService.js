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
	 * Create an `LinkService` instance.
	 *
	 * @since 3.13.0
	 * @param {boolean} linkByDefault Whether to link by default.
	 */
	constructor( linkByDefault ) {
		// Set the `link by default` setting.
		this.linkByDefault = linkByDefault;
	}

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
			occurrences.forEach( x => this.setYesLink( x ) );
		} else {
			// If the request is to disable links, add the `wl-no-link` class to
			// all occurrences.
			occurrences.forEach( x => this.setNoLink( x ) );
		}
	}

	/**
	 * Switch the link on.
	 *
	 * @since 3.13.0
	 * @param {object} elem A DOM element.
	 */
	setYesLink( elem ) {
		const dom = EditorService.get().dom;
		dom.removeClass( elem, 'wl-no-link' );
		dom.addClass( elem, 'wl-link' );
	}

	/**
	 * Switch the link off.
	 *
	 * @since 3.13.0
	 * @param {object} elem A DOM element.
	 */
	setNoLink( elem ) {
		const dom = EditorService.get().dom;
		dom.removeClass( elem, 'wl-link' );
		dom.addClass( elem, 'wl-no-link' );
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
			const dom = EditorService.get().dom;

			return acc || (
					this.linkByDefault
						? ! dom.hasClass( id, 'wl-no-link' )
						: dom.hasClass( id, 'wl-link' )
				);
		}, false );
	}

}

// Finally export the `LinkService`.
export default new LinkService( '1' === wlSettings.link_by_default );
