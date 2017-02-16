/**
 * Events: Update Occurrences for Entity.
 *
 * A redux-thunk action which hooks to Backbone events and dispatches redux
 * actions. Note the `setTimeout` on the dispatch to avoid errors which might
 * arise if we're inside a reducer call.
 *
 * @since 3.11.0
 */

/**
 * Internal dependencies
 */
import { updateOccurrencesForEntity } from '../actions';

/**
 * Define the `UpdateOccurrencesForEntityEvent` event.
 *
 * @since 3.11.0
 * @returns {Function} The redux-thunk function.
 */
function UpdateOccurrencesForEntityEvent() {
	return function( dispatch ) {
		// Hook other events.
		wp.wordlift.on( 'updateOccurrencesForEntity', function( { entityId, occurrences } ) {
			// Asynchronously call the dispatch. We need this because we
			// might be inside a reducer call.
			setTimeout( function() {
				dispatch( updateOccurrencesForEntity( entityId, occurrences ) );
			}, 0 );
		} );
	};
}

// Finally export the function.
export default UpdateOccurrencesForEntityEvent;
