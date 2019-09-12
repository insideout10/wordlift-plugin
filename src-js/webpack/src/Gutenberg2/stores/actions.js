import { createAction, handleActions } from "redux-actions";

// /**
//  * Selects the editor.
//  *
//  * `core/editor` is the default editor.
//  */
// const selectEditor = createAction("SELECT_EDITOR");
//
// /**
//  * Selection succeeded, receive the EditorOps.
//  */
// const selectEditorSucceeded = createAction("SELECT_EDITOR_SUCCEEDED");

/**
 * Requests an analysis on the selected editor.
 */
const requestAnalysis = createAction("REQUEST_ANALYSIS");

// export const editor = handleActions(
//   {
//     // Save the editor to the state.
//     [selectEditorSucceeded]: (state, action) => action.payload
//   },
//   {
//     editor: null
//   }
// );

export default {
  // selectEditor,
  // selectEditorSucceeded,
  requestAnalysis
};
