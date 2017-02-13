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
