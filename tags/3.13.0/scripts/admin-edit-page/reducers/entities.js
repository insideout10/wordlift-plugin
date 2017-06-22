/**
 * Reducers: Entities.
 *
 * Define the reducers related to entities.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */
import { Map } from 'immutable';

/**
 * Internal dependencies
 */
import * as types from '../constants/ActionTypes';
import EditPostWidgetController from '../angular/EditPostWidgetController';
import LinkService from '../services/LinkService';
import WsService from '../services/WsService';

/**
 * Define the reducers.
 *
 * @since 3.11.0
 * @param {object} state The `state`.
 * @param {object} action The `action`.
 * @returns {object} The new state.
 */
const entities = function( state = Map(), action ) {
	switch ( action.type ) {

		// Legacy: receive analysis' results.
		case types.RECEIVE_ANALYSIS_RESULTS:
			// Return a new map of the received entities. The legacy Angular
			// app doesn't set the `link` property on the entity, therefore we
			// preset it here according to the `occurrences` settings.
			return Map( action.results.entities )
				.map(
					x => Object.assign( x, {
						link: LinkService.getLink( x.occurrences ),
						local: 0 === x.id.indexOf( wlSettings.datasetUri ),
						w: WsService.getW( x )
					} )
				)
				// Sort by confidence.
				.sort( ( x, y ) => {
					// Get the delta confidence.
					const delta = y.confidence - x.confidence;

					// If the confidence is equal, sort by number of annotations.
					return 0 !== delta ? delta : y.annotations.length - x.annotations.length;
				} )
				// Set the shortlist flag to true for the first 20.
				.mapEntries( ( [ k, v ], i ) => {
					v.shortlist = i < 20;
					return [ k, v ];
				} )
				// Then resort them by label.
				.sortBy( x => x.label.toLowerCase() );

		// Legacy: set the current entity on the `EditPostWidgetController`.
		case types.SET_CURRENT_ENTITY:
			// Call the `EditPostWidgetController` to set the current entity.
			EditPostWidgetController().$apply(
				EditPostWidgetController().setCurrentEntity( action.entity, 'entity' )
			);

			// Finally return the original state.
			return state;

		// Legacy: toggle the entity selection, fired when clicking on an
		// entity tile.
		case types.TOGGLE_ENTITY:
			// Call the legacy AngularJS controller.
			EditPostWidgetController().$apply(
				EditPostWidgetController().onSelectedEntityTile( state.get( action.entity.id ) )
			);

			// Update the state by replacing the entity with toggled version.
			return state;

		// Toggle the link/no link on entity's occurrences.
		case types.TOGGLE_LINK:
			// Toggle the link on the occurrences.
			LinkService.setLink( action.entity.occurrences, ! action.entity.link );

			// Update the entity in the state.
			return state.set(
				action.entity.id,
				// A new object instance with the existing props and the new
				// occurrences.
				Object.assign(
					{},
					state.get( action.entity.id ),
					{
						occurrences: action.entity.occurrences,
						link: LinkService.getLink( action.entity.occurrences )
					}
				)
			);

		// Update the entity's occurrences. This action is dispatched following
		// a legacy Angular event. The event is configured in the admin/index.js
		// app.
		case types.UPDATE_OCCURRENCES_FOR_ENTITY:
			// Update the entity.
			return state.set(
				action.entityId,
				// A new object instance with the existing props and the new
				// occurrences.
				Object.assign(
					{},
					state.get( action.entityId ),
					{
						occurrences: action.occurrences,
						link: LinkService.getLink( action.occurrences )
					}
				)
			);

		default:
			return state;
	}
};

// Finally export the reducer.
export default entities;
