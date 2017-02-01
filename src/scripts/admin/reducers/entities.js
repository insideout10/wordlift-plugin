/**
 * Internal dependencies
 */
import { SELECT_ENTITY } from '../constants/ActionTypes';
import log from '../../modules/log';

const initialState = {};

export default function entities( state = initialState, action ) {
	switch ( action.type ) {

		case SELECT_ENTITY:
			log( 'Going to select an entity', action );
			return state;

		default:
			return state;
	}
}
