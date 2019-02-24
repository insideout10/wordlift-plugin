/**
 * Actions.
 *
 * Define the list of actions specific to Gutenberg app.
 *
 * @since 3.2x
 */

/**
 * Internal dependencies
 */
import * as types from "../constants/ActionTypes";

export const processingBlockAdd = blockClientId => ({ type: types.PROCESSING_BLOCK_ADD, blockClientId });
export const processingBlockRemove = blockClientId => ({ type: types.PROCESSING_BLOCK_REMOVE, blockClientId });
export const relatedPostsUpdate = relatedPosts => ({ type: types.RELATED_POSTS_UPDATE, relatedPosts });
