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

		default:
			return state;
	}
};

// Finally export the reducer.
export default visibilityFilter;
