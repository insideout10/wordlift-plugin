/**
 * Internal dependencies
 */
import { SELECT_ENTITY } from '../constants/actionTypes';

const initialState = {};

export default function entities( state = initialState, action ) {
	switch ( action.type ) {

		case SELECT_ENTITY:

		default:
			return state;
	}
}
