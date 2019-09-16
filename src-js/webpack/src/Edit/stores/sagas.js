/**
 * This file contains the side effects managed via redux-sagas.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.4
 */

/**
 * External dependencies.
 */
import { select, put, takeEvery } from "redux-saga/effects";

/**
 * Internal dependencies.
 */
import { SET_CURRENT_ENTITY, TOGGLE_ENTITY, TOGGLE_LINK, default as types } from "../constants/ActionTypes";
import EditPostWidgetController from "../angular/EditPostWidgetController";
import { getEntity } from "./selectors";
import LinkService from "../services/LinkService";
import { toggleLinkSuccess } from "../actions";

/**
 * Handle the {@link TOGGLE_ENTITY} action.
 *
 *  @param {{entity:{id}}} payload A payload containing an entity.
 */
function* toggleEntity(payload) {
  const entity = yield select(getEntity, payload.entity.id);
  EditPostWidgetController().$apply(EditPostWidgetController().onSelectedEntityTile(entity));
}

function* toggleLink({ entity }) {
  // Toggle the link/no link on entity's occurrences.
  // Toggle the link on the occurrences.
  LinkService.setLink(entity.occurrences, !entity.link);

  yield put(
    toggleLinkSuccess({
      id: entity.id,
      link: LinkService.getLink(entity.occurrences)
    })
  );
}

function* setCurrentEntity(entity) {
  // Call the `EditPostWidgetController` to set the current entity.
  EditPostWidgetController().$apply(EditPostWidgetController().setCurrentEntity(entity, "entity"));
}

/**
 * Connect the side effects.
 */
function* sagas() {
  yield takeEvery(TOGGLE_ENTITY, toggleEntity);
  yield takeEvery(TOGGLE_LINK, toggleLink);
  yield takeEvery(SET_CURRENT_ENTITY, setCurrentEntity);
}

export default sagas;
