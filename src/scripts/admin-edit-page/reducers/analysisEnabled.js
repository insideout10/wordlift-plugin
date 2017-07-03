/**
 * Reducers: Annotation Filter.
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
 * @since 3.14.0
 * @param {object} state The `state`.
 * @param {object} action The `action`.
 * @returns {object} The new state.
 */
const analysisEnabled = function( state = true, action ) {
	switch ( action.type ) {

		case types.SWITCH_ANALYSIS_ON_OFF:
			return ! state;

		default:
			return state;
	}
};

// Finally export the reducer.
export default analysisEnabled;
