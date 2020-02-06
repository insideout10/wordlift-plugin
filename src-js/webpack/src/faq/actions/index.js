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
import { REQUEST_FAQ_ADD_NEW_QUESTION } from "../constants/action-types";

export const requestAddNewQuestion = createAction(REQUEST_FAQ_ADD_NEW_QUESTION);
