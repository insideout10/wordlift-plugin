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
import { createReducer } from '@reduxjs/toolkit';
/**
 * Internal dependencies.
 */
import {
	ANSWER_SELECTED_BY_USER,
	UPDATE_MODAL_STATUS,
} from '../constants/action-types';

export const faqModalReducer = createReducer(null, {
	[UPDATE_MODAL_STATUS]: (state, action) => {
		state.isModalOpened = action.payload;
	},
	[ANSWER_SELECTED_BY_USER]: (state, action) => {
		const { selectedAnswer } = action.payload;
		state.selectedAnswer = selectedAnswer;
		// Open the modal when the answer is selected by user.
		state.isModalOpened = true;
	},
});
