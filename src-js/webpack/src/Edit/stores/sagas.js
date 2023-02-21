/**
 * This file contains the side effects managed via redux-sagas.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */
import {delay, put, select, takeEvery, takeLatest} from "redux-saga/effects";
/**
 * Internal dependencies
 */
import {
  default as types,
  EDITOR_SELECTION_CHANGED,
  SET_CURRENT_ENTITY,
  TOGGLE_ENTITY,
  TOGGLE_LINK
} from "../constants/ActionTypes";
import EditPostWidgetController from "../angular/EditPostWidgetController";
import {getEntity} from "./selectors";
import {toggleLinkSuccess} from "../actions";
import {
  addEntityRequest,
  addEntitySuccess,
  createEntityRequest,
  createEntitySuccess
} from "../components/AddEntity/actions";
import React from "react";
import LinkServiceFactory from "../services/link/LinkServiceFactory";
import {doAction} from "@wordpress/hooks";

/**
 * Handle the {@link TOGGLE_ENTITY} action.
 *
 *  @param {{entity:{id}}} payload A payload containing an entity.
 */
function* toggleEntity(payload) {
  const entity = yield select(getEntity, payload.entity.id);
  // @@todo
  //
  // $.ajax({
  //   url: wpApiSettings.root + 'wordlift/v1/entities/1',
  //   method: 'POST',
  //   beforeSend: function (xhr) {
  //     xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
  //   },
  //   data: {
  //     'title': 'Hello Moon'
  //   }
  // }).done(function (response) {
  //   console.log(response);
  // });

  EditPostWidgetController().$apply(
      EditPostWidgetController().onSelectedEntityTile(entity));
}

function* toggleLink({entity}) {
  const linkService = LinkServiceFactory.getInstance();
  // Toggle the link/no link on entity's occurrences.
  // Toggle the link on the occurrences.
  linkService.setLink(entity.occurrences, !entity.link);

  yield put(
      toggleLinkSuccess({
        id: entity.id,
        link: linkService.getLink(entity.occurrences)
      })
  );
}

function* setCurrentEntity({entity}) {
  // Call the `EditPostWidgetController` to set the current entity.
  EditPostWidgetController().$apply(
      EditPostWidgetController().setCurrentEntity(entity, "entity"));
}

function* addEntity({payload}) {
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
  doAction("unstable_wordlift.closeEntitySelect")

  yield put(addEntitySuccess());
}

function* createEntity({payload}) {
  const ctrl = EditPostWidgetController();

  ctrl.$apply(ctrl.setCurrentEntity(undefined, undefined, payload));

  yield put(createEntitySuccess());
}

export const getMainType = types => {
  for (let i = 0; i < window._wlEntityTypes.length; i++) {
    const type = window._wlEntityTypes[i];

    if (-1 < types.indexOf(type.uri)) {
      return type.slug;
    }
  }
  return "thing";
};

let popover;

function* handleEditorSelectionChanged({payload}) {
  yield delay(300);

  console.log("handleEditorSelectionChanged", payload);
  const editor = payload.editor;

  // Get the selection. Bail out is the selection is collapsed (is just a caret).
  const selection = editor.selection;
  if (selection.isCollapsed() || "" === selection.getContent(
      {format: "text"})) {
    if (popover) {
      popover.unmount();
    }
    return;
  }

  // Get the selection range and bail out if it's null.
  const range = selection.getRng();
  if (null == range) {
    if (popover) {
      popover.unmount();
    }
    return;
  }

  // Get the editor's selection bounding rect. The rect's coordinates are relative to TinyMCE's editor's iframe.
  const editorRect = range.getBoundingClientRect();

  // Get TinyMCE's iframe element's bounding rect.
  const iframe = editor.iframeElement;
  const iframeRect = iframe.getBoundingClientRect();

  // Calculate our target rect by summing the iframe and the editor rects along with the window's scroll positions.
  const rect = {
    top: iframeRect.top + editorRect.top + window.scrollY,
    right: iframeRect.left + editorRect.right + window.scrollX,
    bottom: iframeRect.top + editorRect.bottom + window.scrollY,
    left: iframeRect.left + editorRect.left + window.scrollX
  };
}

/**
 * Connect the side effects.
 */
function* sagas() {
  yield takeEvery(TOGGLE_ENTITY, toggleEntity);
  yield takeEvery(TOGGLE_LINK, toggleLink);
  yield takeEvery(SET_CURRENT_ENTITY, setCurrentEntity);
  yield takeEvery(addEntityRequest, addEntity);
  yield takeEvery(createEntityRequest, createEntity);
  yield takeLatest(EDITOR_SELECTION_CHANGED, handleEditorSelectionChanged);
}

export default sagas;
