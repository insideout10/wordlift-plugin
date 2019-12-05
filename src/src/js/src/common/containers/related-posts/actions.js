/**
 * This file contains actions to retrieve and display the related posts.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */
import { createActions, handleActions } from "redux-actions";

/**
 * Actions
 */
export const { relatedPostsRequest, relatedPostsSuccess } = createActions(
  "RELATED_POSTS_REQUEST",
  "RELATED_POSTS_SUCCESS"
);

/**
 * Reducer
 */
export default handleActions(
  {
    RELATED_POSTS_SUCCESS: (state, { payload }) => ({ posts: payload })
  },
  { posts: [] }
);
