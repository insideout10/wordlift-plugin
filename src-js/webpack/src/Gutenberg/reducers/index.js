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
import { combineReducers } from "redux";

/**
 * Internal dependencies
 */
import entities from "./entities";

/**
 * Define the root reducer.
 *
 * @since 3.11.0
 * @type {Reducer<S>}
 */
const rootReducer = combineReducers({
  entities
});

// Finally export the root reducer.
export default rootReducer;
