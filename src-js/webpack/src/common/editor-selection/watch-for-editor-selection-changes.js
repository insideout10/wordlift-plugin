/**
 * This file defines a Backbone event listener for editor selection changes (tinymce, block editor) and fires the
 * related `editorSelectionChanged` action.
 */

/**
 * External dependencies
 */
import { eventChannel } from "redux-saga";
import { call, put, take } from "redux-saga/effects";
import { off, on } from "backbone";
/**
 * Internal dependencies
 */
import { SELECTION_CHANGED } from "../constants";
import { editorSelectionChanged } from "../../classic-editor/actions";

/**
 * Create an editor's selection changed channel.
 *
 * @returns {Channel<unknown>}
 */
function createEditorSelectionChangedChannel() {
  // Create our listener, the reference is used to turn it off.
  const listener = (emitter) => (payload) => emitter(payload);

  // Create the event channel
  return eventChannel((emitter) => {
    // Listen to Backbone events, binding our listener.
    on(SELECTION_CHANGED, listener(emitter));

    // Return the unsubscribe function.
    return () => off(SELECTION_CHANGED, listener(emitter));
  });
}

/**
 * Watch for emissions from the `EditorSelectionChangedChannel`. This function is referenced from the classic editor
 * edit post screen and block editor edit post screen.
 *
 * @returns {Generator<<"TAKE", TakeEffectDescriptor>|*, void, ?>}
 */
export function* watchForEditorSelectionChanges() {
  // Create the channel.
  const channel = yield call(createEditorSelectionChangedChannel);

  try {
    while (true) {
      // Wait for a new payload from the channel.
      let payload = yield take(channel);

      // Send the action.
      yield put(editorSelectionChanged(payload));
    }
  } finally {
  }
}
