/**
 * Events: Receive Analysis Results Event.
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
import { receiveAnalysisResults } from '../actions';

/**
 * Define the `ReceiveAnalysisResultsEvent` event.
 *
 * @since 3.11.0
 * @returns {Function} The redux-thunk function.
 */
function ReceiveAnalysisResultsEvent() {
	return function( dispatch ) {
		// Hook other events.
		wp.wordlift.on( 'analysis.result', function( results ) {
			// Asynchronously call the dispatch. We need this because we
			// might be inside a reducer call.
			setTimeout( function() {
				dispatch( receiveAnalysisResults( results ) );
			}, 0 );
		} );
	};
}

// Finally export the function.
export default ReceiveAnalysisResultsEvent;
