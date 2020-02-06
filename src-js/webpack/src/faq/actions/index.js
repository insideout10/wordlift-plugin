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
import { ADD_NEW_QUESTION } from "../constants/action-types";

export const addNewQuestion = createAction(ADD_NEW_QUESTION);
