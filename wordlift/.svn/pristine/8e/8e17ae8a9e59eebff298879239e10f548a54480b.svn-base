/**
 * Reducers: Visibility Filter.
 *
 * @since 3.11.0
 */
/**
 * Internal dependencies
 */
import * as types from '../constants/ActionTypes';

/**
 * Define the reducers.
 *
 * @since 3.11.0
 * @param {object} state The `state`.
 * @param {object} action The `action`.
 * @returns {object} The new state.
 */
const visibilityFilter = function( state = 'SHOW_ALL', action ) {
	switch ( action.type ) {

		// Handle the `SET_ENTITY_FILTER` action, which changes the current
		// filter.
		case types.SET_ENTITY_FILTER:
			return action.filter;

		// Handle the `ANNOTATION` action, which notifies us of a selected
		// annotation in TinyMCE. In that case we switch to a `SHOW_ANNOTATION`
		// filter.
		case types.ANNOTATION:
			// We might receive an undefined annotation (when no annotation is
			// selected).
			//
			// Note that selected annotation is set by the `annotationFilter`
			// function.
			return action.annotation === undefined ? 'SHOW_ALL' : 'SHOW_ANNOTATION';

		default:
			return state;
	}
};

// Finally export the reducer.
export default visibilityFilter;
