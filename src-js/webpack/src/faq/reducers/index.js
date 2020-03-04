/**
 * This file provides the reducers for redux store.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 *
 */

/**
 * External dependencies.
 */
import { combineReducers } from "redux";
/**
 * Internal dependencies.
 */
import { faqItemsListReducer } from "./faq-items-list-reducer";
import { faqModalReducer } from "./faq-modal-reducer";
import { faqNotificationReducer } from "./faq-notification-reducer";

export const faqReducer = combineReducers({
  faqListOptions: faqItemsListReducer,
  faqModalOptions: faqModalReducer,
  faqNotificationArea: faqNotificationReducer
});
