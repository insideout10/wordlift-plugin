/**
 * This files provide the sagas for FAQ
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import { call, delay, put, select, takeEvery, takeLatest } from 'redux-saga/effects';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
/**
 * Internal dependencies.
 */
import {
	REQUEST_DELETE_FAQ_ITEMS,
	REQUEST_FAQ_ADD_NEW_QUESTION,
	REQUEST_GET_FAQ_ITEMS,
	UPDATE_FAQ_ITEM,
} from '../constants/action-types';
import API from '../api/index';
import { getAllFAQItems, getCurrentQuestion } from '../selectors';
import {
	changeRequestStatus,
	closeEditScreen,
	maybeAnswerSelected,
	maybeQuestionSelected,
	requestAddNewQuestion,
	requestGetFaqItems,
	resetTypedQuestion,
	updateFaqItems,
	updateFaqModalVisibility,
	updateNotificationArea,
} from '../actions';
import { transformAPIDataToUi } from './filters';
import { faqEditItemType } from '../components/faq-edit-item';
import {
	FAQ_HIGHLIGHT_TEXT,
	FAQ_ITEM_DELETED,
	FAQ_ITEMS_CHANGED,
} from '../constants/faq-hook-constants';
import { trigger } from 'backbone';
import { EDITOR_SELECTION_CHANGED } from '../../classic-editor/constants/ActionTypes';
import { Button, createPopover } from '@wordlift/design';
import React from 'react';

/**
 * Dispatch notification when a event occurs on the store.
 * @param response
 * @return {Generator<<"CALL", CallEffectDescriptor>|<"PUT", PutEffectDescriptor<{type: *}>>, void, ?>}
 */
function* dispatchNotification(response) {
	const notificationAction = updateNotificationArea({
		notificationMessage: response.message,
		notificationType: response.status,
	});
	yield put(notificationAction);
	/**
	 * After 2 seconds, remove the notification.
	 */
	yield delay(2000);
	notificationAction.payload = {
		notificationMessage: '',
		notificationType: '',
	};
	yield put(notificationAction);
}

function* handleAddNewQuestion(action) {
	const currentQuestion = yield select(getCurrentQuestion);
	const faqItems = [
		{
			question: currentQuestion,
			answer: '',
		},
	];
	yield put(changeRequestStatus(true));
	const response = yield call(API.saveFAQItems, faqItems);
	yield put(changeRequestStatus(false));
	// Event emitted to global namespace in order to highlight text in the editor.
	trigger(FAQ_HIGHLIGHT_TEXT, {
		text: currentQuestion,
		isQuestion: true,
		id: response.id,
	});
	yield dispatchNotification(response);
	yield put(resetTypedQuestion());
	// Refresh the screen by getting new FAQ items.
	yield put(requestGetFaqItems());
}

/**
 * Get the FAQ items from the API.
 * @return {Generator<*, void, ?>}
 */
function* handleGetFaqItems() {
	yield put(changeRequestStatus(true));
	const faqItems = yield call(API.getFAQItems);
	const payload = transformAPIDataToUi(faqItems);
	const action = updateFaqItems(payload);
	trigger(FAQ_ITEMS_CHANGED, payload);
	yield put(action);
	yield put(changeRequestStatus(false));
}

/**
 * Update the FAQ items when the user changes the data.
 * @param action
 * @return {Generator<<"CALL", CallEffectDescriptor>|*, void, ?>}
 */
function* handleUpdateFaqItems(action) {
	const faqItems = yield select(getAllFAQItems);
	const payload = action.payload;
	const faqItemIndex = faqItems.map((e) => e.id).indexOf(payload.id);
	/**
	 * Update the changed faq item to the API.
	 */
	const changedFaqItem = faqItems[faqItemIndex];
	const changedFaqItems = [Object.assign({}, changedFaqItem)];
	switch (payload.type) {
		case faqEditItemType.ANSWER:
			changedFaqItems[0]['answer'] = payload.value;
			break;
		case faqEditItemType.QUESTION:
			changedFaqItems[0]['question'] = payload.value;
			break;
	}
	trigger(FAQ_HIGHLIGHT_TEXT, {
		text: payload.value,
		isQuestion: payload.type === faqEditItemType.QUESTION,
		id: faqItems[faqItemIndex].id,
	});
	// Close the modal immediately on apply.
	yield put(updateFaqModalVisibility(false));
	yield put(changeRequestStatus(true));
	const response = yield call(API.updateFAQItems, changedFaqItems);
	yield put(changeRequestStatus(false));
	yield put(requestGetFaqItems());
	yield dispatchNotification(response);
}

/**
 * Delete Faq items.
 * @param action
 * @return {Generator<*, void, ?>}
 */
function* handleDeleteFaqItems(action) {
	// close the edit screen
	yield put(closeEditScreen());
	const allFaqItems = yield select(getAllFAQItems);
	const { id, type } = action.payload;
	const faqItemIndex = allFaqItems.map((e) => e.id).indexOf(id);
	const faqItemToBeDeleted = Object.assign({}, allFaqItems[faqItemIndex]);
	faqItemToBeDeleted.fieldToBeDeleted = type;
	const deletedFaqItems = [faqItemToBeDeleted];
	yield put(changeRequestStatus(true));
	const response = yield call(API.deleteFaqItems, deletedFaqItems);
	/**
	 * Send a delete signal to the hooks in order to remove the highlighting
	 * from the editor.
	 */
	trigger(FAQ_ITEM_DELETED, {
		id: id,
		type: type,
	});
	yield put(changeRequestStatus(false));
	// Refresh the screen by getting new FAQ items.
	yield put(requestGetFaqItems());
	yield dispatchNotification(response);
}

/**
 * When the editor selection has changed, we check whether the selection is a potential question or
 * or answer. In which case we fire the Maybe Question Selected or Maybe Answer Selected actions.
 *
 * @param {{payload: {selection: string}}} A payload with the current selection.
 * @returns {Generator<*, void, *>}
 */
function* handleEditorSelectionChange({ payload }) {
	// Get the selection.
	const { selection } = payload;

	// Potential question.
	if (selection.endsWith('?')) {
		yield put(maybeQuestionSelected(payload));
		return;
	}

	// Potential answer.
	// const allFaqItems = yield select(getAllFAQItems);
	// if (0 < allFaqItems.length) yield put(maybeAnswerSelected(payload));
}

/**
 * Displays a popover when a potential question has been selected.
 *
 * @param {{payload: {rect: Object}}} payload
 */
function* handleMaybeQuestionSelected({ payload }) {
	// Wait 300 ms. before displaying anything.
	yield delay(300);

	const { rect } = payload;

	// Finally create the popover.
	const popover = yield call(
		createPopover,
		<Button onClick={() => put(requestAddNewQuestion())}>
			{__('Add Question', 'wordlift')}
		</Button>,
		{ ...rect, positions: ['right', 'left', 'bottom', 'top'] }
	);
}

function* handleRequestAddNewQuestion() {
	console.debug('handleRequestAddNewQuestion');
}

function* rootSaga() {
	yield takeLatest(REQUEST_FAQ_ADD_NEW_QUESTION, handleAddNewQuestion);
	yield takeLatest(REQUEST_GET_FAQ_ITEMS, handleGetFaqItems);
	yield takeLatest(UPDATE_FAQ_ITEM, handleUpdateFaqItems);
	yield takeLatest(REQUEST_DELETE_FAQ_ITEMS, handleDeleteFaqItems);

	yield takeLatest(EDITOR_SELECTION_CHANGED, handleEditorSelectionChange);
	yield takeLatest(maybeQuestionSelected, handleMaybeQuestionSelected);

	yield takeEvery(REQUEST_FAQ_ADD_NEW_QUESTION, handleRequestAddNewQuestion);
}

export default rootSaga;
