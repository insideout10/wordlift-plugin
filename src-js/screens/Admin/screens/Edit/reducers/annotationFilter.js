/**
 * Reducers: Annotation Filter.
 *
 * @since 3.11.0
 */
/**
 * Internal dependencies
 */
import * as types from '../constants/ActionTypes';
// import log from '../../modules/log';

/**
 * Define the reducers.
 *
 * @since 3.11.0
 * @param {object} state The `state`.
 * @param {object} action The `action`.
 * @returns {object} The new state.
 */
const annotationFilter = function( state = null, action ) {
	switch ( action.type ) {

		// Handle the `ANNOTATION` action, which sets the current selected
		// annotation or null if not set.
		case types.ANNOTATION:
			// We might receive an undefined annotation (when no annotation is
			// selected. In that case we send `null`.
			//
			// Note that this action is handled also by the `visibilityFilter`
			// which sets itself to `SHOW_ANNOTATION` | `SHOW_ALL` according
			// to whether or not an annotation has been selected.
			return action.annotation === undefined ? null : action.annotation;

		default:
			return state;
	}
};

// Finally export the reducer.
export default annotationFilter;
