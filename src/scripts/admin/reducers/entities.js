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
import {
	TOGGLE_ENTITY,
	UPDATE_OCCURRENCES_FOR_ENTITY
} from '../constants/ActionTypes';
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

		// Toggle the entity selection, fired when clicking on an entity tile.
		case TOGGLE_ENTITY:
			// Call the legacy AngularJS controller.
			EditPostWidgetController().onSelectedEntityTile( state.get( action.entity.id ) );

			// Update the state by replacing the entity with toggled version.
			return state;

		// Update the entity's occurrences. This action is dispatched following
		// a legacy Angular event. The event is configured in the admin/index.js
		// app.
		case UPDATE_OCCURRENCES_FOR_ENTITY:
			// Update the entity.
			return state.set(
				action.entityId,
				// A new object instance with the existing props and the new
				// occurrences.
				Object.assign(
					{},
					state.get( action.entityId ),
					{ occurrences: action.occurrences }
				)
			);

		default:
			return state;
	}
};

// Finally export the reducer.
export default entities;
