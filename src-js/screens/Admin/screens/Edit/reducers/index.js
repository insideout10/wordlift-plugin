/**
 * Reducers: Root.
 *
 * The root reducers combining other reducers: entities, visibilityFilter.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */
import { combineReducers } from 'redux';

/**
 * Internal dependencies
 */
import entities from './entities';
import annotationFilter from './annotationFilter';
import visibilityFilter from './visibilityFilter';

/**
 * Define the root reducer.
 *
 * @since 3.11.0
 * @type {Reducer<S>}
 */
const rootReducer = combineReducers(
	{
		entities,
		annotationFilter,
		visibilityFilter
	} );

// Finally export the root reducer.
export default rootReducer;
