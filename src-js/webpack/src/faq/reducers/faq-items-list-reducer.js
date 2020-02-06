/**
 * This file provides the reducers for the list view of FAQ items.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 *
 */

/**
 * External dependencies
 */
import { createReducer } from "@reduxjs/toolkit";
import {UPDATE_FAQ_ITEMS} from "../constants/action-types";

export const faqItemsListReducer = createReducer(null, {
   [UPDATE_FAQ_ITEMS]: ( state, action ) => {
       state.faqItems = action.payload
   }
});
