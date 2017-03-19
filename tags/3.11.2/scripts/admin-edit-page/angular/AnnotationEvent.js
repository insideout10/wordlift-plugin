/**
 * Events: Annotation Event.
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
import { setCurrentAnnotation } from '../actions';

/**
 * Define the `AnnotationEvent` event.
 *
 * @since 3.11.0
 * @returns {Function} The redux-thunk function.
 */
function AnnotationEvent() {
	return function( dispatch ) {
		// Hook other events.
		wp.wordlift.on( 'annotation', function( annotation ) {
			// Asynchronously call the dispatch. We need this because we
			// might be inside a reducer call.
			setTimeout( function() {
				dispatch( setCurrentAnnotation( annotation ) );
			}, 0 );
		} );
	};
}

// Finally export the function.
export default AnnotationEvent;
