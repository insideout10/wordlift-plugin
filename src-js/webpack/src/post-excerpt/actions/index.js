/**
 * This files provides the actions for post excerpt
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import { createActions, handleActions } from "redux-actions";

export const { updateRequestStatus, requestPostExcerpt, applyPostExcerpt, updatePostExcerpt } = createActions(
  "UPDATE_REQUEST_STATUS",
  "GET_POST_EXCERPT",
  "APPLY_POST_EXCERPT",
  "UPDATE_POST_EXCERPT"
);

export const reducer = handleActions(
  {
    [updateRequestStatus]: (state, action) => ({
      ...state,
      isRequestInProgress: action.payload
    }),
    [updatePostExcerpt]: (state, action) => ({
      ...state,
      currentPostExcerpt: action.payload
    })
  },
  {
    isRequestInProgress: false,
    currentPostExcerpt: ""
  }
);
