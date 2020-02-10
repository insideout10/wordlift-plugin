/**
 * This file provides the reducers for the FAQ modal.
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
import { UPDATE_MODAL_STATUS } from "../constants/action-types";

export const faqModalReducer = createReducer(null, {
  [UPDATE_MODAL_STATUS]: (state, action) => {
    state.isModalOpened = action.payload;
  }
});
