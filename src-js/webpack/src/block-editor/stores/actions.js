/**
 * Define the actions.
 *
 * @since 3.23.0
 */

/**
 * External dependencies
 */
import { createAction } from "redux-actions";

/**
 * Internal dependencies
 */
import { EDITOR_SELECTION_CHANGED } from "../../Edit/constants/ActionTypes";

/**
 * Requests an analysis on the selected editor.
 */
const requestAnalysis = createAction("REQUEST_ANALYSIS");

const editorSelectionChanged = createAction(EDITOR_SELECTION_CHANGED);

export default {
  requestAnalysis,
  editorSelectionChanged
};
