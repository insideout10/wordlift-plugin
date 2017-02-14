/**
 * Actions.
 *
 * Define the list of actions for the app.
 *
 * @since 3.11.0
 */

/**
 * Internal dependencies
 */
import * as types from '../constants/ActionTypes';

/**
 * The `toggleEntity` action toggles the selection flag for the provided entity.
 *
 * @since 3.11.0
 * @param {object} entity The entity being toggled.
 * @return {function} The action's function.
 */
export const toggleEntity = entity => (
	{ type: types.TOGGLE_ENTITY, entity }
);

/**
 * The `selectEntity` action updates an entity when it is selected. This action
 * is typically fired when the `entitySelected` event is received from the
 * legacy Angular application.
 *
 * @since 3.11.0
 * @param {object} entity The entity being selected.
 * @return {function} The action's function.
 */
export const selectEntity = entity => (
	{ type: types.SELECT_ENTITY, entity }
);
