/**
 * This files provides the actions for post excerpt
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import { createAction } from "redux-actions";
import { createReducer } from "@reduxjs/toolkit";

/**
 * Action to Request the post excerpt from the external api.
 * @type {actionCreator}
 */
export const requestPostExcerpt = createAction("GET_POST_EXCERPT");

/**
 * Action to update whether the request is in progress or not.
 * @type {actionCreator}
 */
export const updateRequestStatus = createAction("UPDATE_REQUEST_STATUS");

/**
 * When this action is emitted, the post excerpt is copied to the wordpress excerpt box.
 * @type {function(): {type: *}}
 */
export const applyPostExcerpt = createAction("APPLY_POST_EXCERPT");

/**
 * When this action is dispatched we set the notification message and notification type.
 * @type {function(): {type: *}}
 */
export const setNotificationData = createAction("SET_NOTIFICATION_DATA");

/**
 * Update the post excerpt in global state after fetching it from api.
 * @type {function(): {type: *}}
 */
export const updatePostExcerpt = createAction("UPDATE_POST_EXCERPT");

export const reducer = createReducer(null, {
  [updateRequestStatus().type]: (state, action) => {
    state.isRequestInProgress = action.payload;
  },
  [updatePostExcerpt().type]: (state, action) => {
    state.currentPostExcerpt = action.payload;
  },
  [setNotificationData().type]: (state, action) => {
    const { notificationMessage, notificationType } = action.payload;
    state.notificationMessage = notificationMessage;
    state.notificationType = notificationType;
  }
});
