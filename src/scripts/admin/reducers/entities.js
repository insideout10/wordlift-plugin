/**
 * Reducers: Entities.
 *
 * Define the reducers related to entities.
 *
 * @since 3.11.0
 */

/**
 * Internal dependencies
 */
import { TOGGLE_ENTITY } from '../constants/ActionTypes';
import log from '../../modules/log';

/**
 * Define the reducers.
 *
 * @since 3.11.0
 * @param {object} state The `state`.
 * @param {object} action The `action`.
 * @returns {object} The new state.
 */
const entities = function( state = {}, action ) {
	switch ( action.type ) {

		case TOGGLE_ENTITY:
			// Get the entity from the collection.
			const entity = state.get( action.entity.id );

			log( 'Going to select an entity', action, state, state.get( entity.id ) );

			// Update the state by replacing the entity with toggled version.
			return state.set(
				action.entity.id,
				Object.assign( action.entity, { selected: ! entity.selected } )
			);

		default:
			return state;
	}
};

// Finally export the reducer.
export default entities;
