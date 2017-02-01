/**
 * Internal dependencies
 */
import * as types from '../constants/ActionTypes';

export const selectEntity = text => (
	{ type: types.SELECT_ENTITY, text }
);
