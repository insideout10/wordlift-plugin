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
import annotationFilter from "../../Edit/reducers/annotationFilter";
import visibilityFilter from "../../Edit/reducers/visibilityFilter";
import editor from "../../Edit/reducers/editor";
import processingBlocks from "./processingBlocks";
import relatedPosts from "./relatedPosts";

/**
 * Define the root reducer.
 *
 * @since 3.11.0
 * @type {Reducer<S>}
 */
const rootReducer = combineReducers({
  entities,
  annotationFilter,
  visibilityFilter,
  editor,
  processingBlocks,
  relatedPosts
});

// Finally export the root reducer.
export default rootReducer;
