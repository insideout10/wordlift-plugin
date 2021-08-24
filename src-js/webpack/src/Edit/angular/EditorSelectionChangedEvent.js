/*global wp*/
/**
 * Events: Editor Selection Changed Event.
 *
 * A redux-thunk action which hooks to Backbone events and dispatches redux
 * actions. Note the `setTimeout` on the dispatch to avoid errors which might
 * arise if we're inside a reducer call.
 *
 * @since 3.18.4
 */

/**
 * Internal dependencies
 */
import {editorSelectionChanged} from "../actions";

/**
 * Define the `EditorSelectionChangedEvent` event.
 *
 * @since 3.18.4
 * @returns {Function} The redux-thunk function.
 */
function EditorSelectionChangedEvent() {
  return function (dispatch) {
    // Hook other events.
    wp.wordlift.on("editorSelectionChanged", function (args) {
      // Asynchronously call the dispatch. We need this because we
      // might be inside a reducer call.
      setTimeout(function () {
        dispatch(editorSelectionChanged(args));
      }, 0);
    });
  };
}

// Finally export the function.
export default EditorSelectionChangedEvent;
