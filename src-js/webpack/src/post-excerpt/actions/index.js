/**
 * This files provides the actions for post excerpt
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import { createAction, handleActions } from "redux-actions";
import { createReducer } from "@reduxjs/toolkit";
import { UPDATE_FAQ_ITEMS } from "../../faq/constants/action-types";

export const requestPostExcerpt = createAction("GET_POST_EXCERPT");
export const updateRequestStatus = createAction("UPDATE_REQUEST_STATUS");
export const applyPostExcerpt = createAction("APPLY_POST_EXCERPT");
export const updatePostExcerpt = createAction("UPDATE_POST_EXCERPT");

export const reducer = createReducer(null, {
  [updateRequestStatus().type]: (state, action) => {
    state.isRequestInProgress = action.payload;
  },
  [updatePostExcerpt().type]: (state, action) => {
    state.currentPostExcerpt = action.payload;
  }
});
