/**
 * Services: Ws Service.
 *
 * A service which determines the `W` according to WordLift's configuration.
 *
 * @since 3.11.0
 */
class WsService {

	/**
	 * Get the `W` (who, where, when, what) corresponding to the provided
	 * entity.
	 *
	 * @since 3.11.0
	 *
	 * @param {Object} entity The entity.
	 * @returns {string} The W, or 'unknown' if there's no match.
	 */
	getW( entity ) {
		return wordlift.classificationBoxes.reduce( ( acc, box ) => (
			-1 === box.registeredTypes.indexOf( entity.mainType ) ? acc : box.id
		), 'unknown' );
	}

}

// Finally export the `WsService`.
export default new WsService();
