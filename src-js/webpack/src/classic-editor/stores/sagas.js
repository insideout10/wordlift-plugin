/**
 * This file contains the side effects managed via redux-sagas.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */
import React from "react";

import { fork, put, select, takeEvery } from "redux-saga/effects";
/**
 * Internal dependencies
 */
import { default as types, SET_CURRENT_ENTITY, TOGGLE_ENTITY, TOGGLE_LINK } from "../constants/ActionTypes";
import EditPostWidgetController from "../angular/EditPostWidgetController";
import { getEntity } from "./selectors";
import LinkService from "../services/LinkService";
import { toggleLinkSuccess } from "../actions";
import {
	addEntityRequest,
	addEntitySuccess,
	createEntityRequest,
	createEntitySuccess
} from "../components/AddEntity/actions";
import { watchForEditorSelectionChanges } from "../../common/editor-selection/watch-for-editor-selection-changes";
import { default as faqSaga } from "../../faq/sagas/index";

/**
 * Handle the {@link TOGGLE_ENTITY} action.
 *
 *  @param {{entity:{id}}} payload A payload containing an entity.
 */
function* toggleEntity(payload) {
	const entity = yield select(getEntity, payload.entity.id);
	EditPostWidgetController().$apply(
		EditPostWidgetController().onSelectedEntityTile(entity)
	);
}

function* toggleLink({ entity }) {
	// Toggle the link/no link on entity's occurrences.
	// Toggle the link on the occurrences.
	LinkService.setLink(entity.occurrences, !entity.link);

	yield put(
		toggleLinkSuccess({
			id: entity.id,
			link: LinkService.getLink(entity.occurrences),
		})
	);
}

function* setCurrentEntity({ entity }) {
	// Call the `EditPostWidgetController` to set the current entity.
	EditPostWidgetController().$apply(
		EditPostWidgetController().setCurrentEntity(entity, 'entity')
	);
}

function* addEntity({ payload }) {
	const ctrl = EditPostWidgetController();
	ctrl.$apply(() => {
		// Create the text annotation.
		ctrl.setCurrentEntity();
		// Update the entity data.
		ctrl.currentEntity.description = payload.descriptions[0];
		ctrl.currentEntity.id = payload.id;
		ctrl.currentEntity.images = payload.images;
		ctrl.currentEntity.label = payload.label;
		ctrl.currentEntity.mainType = getMainType(payload.types);
		ctrl.currentEntity.types = payload.types;
		ctrl.currentEntity.sameAs = payload.sameAss;
		// Save the entity.
		ctrl.storeCurrentEntity();
	});

	yield put(addEntitySuccess());
}

function* createEntity({ payload }) {
	const ctrl = EditPostWidgetController();

	ctrl.$apply(ctrl.setCurrentEntity(undefined, undefined, payload));

	yield put(createEntitySuccess());
}

const getMainType = (types) => {
	for (let i = 0; i < window._wlEntityTypes.length; i++) {
		const type = window._wlEntityTypes[i];

		if (-1 < types.indexOf(type.uri)) return type.slug;
	}
	return 'thing';
};

/**
 * Connect the side effects.
 */
function* sagas() {
	yield takeEvery(TOGGLE_ENTITY, toggleEntity);
	yield takeEvery(TOGGLE_LINK, toggleLink);
	yield takeEvery(SET_CURRENT_ENTITY, setCurrentEntity);
	yield takeEvery(addEntityRequest, addEntity);
	yield takeEvery(createEntityRequest, createEntity);

	yield fork(watchForEditorSelectionChanges);

	yield fork(faqSaga);
}

export default sagas;
