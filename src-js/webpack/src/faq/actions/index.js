/**
 * This file provides the actions used by FAQ meta box.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import { createAction } from "redux-actions";

/**
 * Internal dependencies
 */
import { REQUEST_FAQ_ADD_NEW_QUESTION, REQUEST_GET_FAQ_ITEMS, UPDATE_FAQ_ITEMS } from "../constants/action-types";

/**
 * Action for adding new question.
 * @type {function(): {type: *}}
 */
export const requestAddNewQuestion = createAction(REQUEST_FAQ_ADD_NEW_QUESTION);

/**
 * Action for getting FAQ items from API.
 * @type {function(): {type: *}}
 */
export const requestGetFaqItems = createAction(REQUEST_GET_FAQ_ITEMS);

/**
 * Action for updating FAQ items in store.
 * @type {function(): {type: *}}
 */
export const updateFaqItems = createAction(UPDATE_FAQ_ITEMS);
