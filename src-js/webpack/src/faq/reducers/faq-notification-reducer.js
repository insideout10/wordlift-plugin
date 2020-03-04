/**
 * This file provides the reducers for the FAQ notification area.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 *
 */

/**
 * External dependencies
 */
import { createReducer } from "@reduxjs/toolkit";
/**
 * Internal dependencies.
 */
import { UPDATE_NOTIFICATION_AREA } from "../constants/action-types";

export const faqNotificationReducer = createReducer(null, {
  [UPDATE_NOTIFICATION_AREA]: (state, action) => {
    const { notificationType, notificationMessage } = action.payload;
    state.notificationMessage = notificationMessage;
    state.notificationType = notificationType;
  }
});
