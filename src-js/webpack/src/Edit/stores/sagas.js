/**
 * This file contains the side effects managed via redux-sagas.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.4
 */

/**
 * External dependencies.
 */
import { select, takeEvery } from "redux-saga/effects";

/**
 * Internal dependencies.
 */
import { TOGGLE_ENTITY } from "../constants/ActionTypes";
import EditPostWidgetController from "../angular/EditPostWidgetController";
import { getEntity } from "./selectors";

/**
 * Handle the {@link TOGGLE_ENTITY} action.
 *
 *  @param {{entity:{id}}} payload A payload containing an entity.
 */
function* toggleEntity(payload) {
  const entity = yield select(getEntity, payload.entity.id);
  EditPostWidgetController().$apply(EditPostWidgetController().onSelectedEntityTile(entity));
}

/**
 * Connect the side effects.
 */
function* sagas() {
  yield takeEvery(TOGGLE_ENTITY, toggleEntity);
}

export default sagas;
