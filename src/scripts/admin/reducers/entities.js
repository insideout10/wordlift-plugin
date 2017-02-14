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
import { SELECT_ENTITY, TOGGLE_ENTITY } from '../constants/ActionTypes';
import EditPostWidgetController from '../angular/EditPostWidgetController';
// import log from '../../modules/log';

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

		case SELECT_ENTITY:
			// Update the entity.
			return state.set( action.entity.id, Object.assign( {}, action.entity ) );

		case TOGGLE_ENTITY:
			// Call the legacy AngularJS controller.
			EditPostWidgetController().onSelectedEntityTile( state.get( action.entity.id ) );

			// Update the state by replacing the entity with toggled version.
			return state;

		default:
			return state;
	}
};

// Finally export the reducer.
export default entities;
