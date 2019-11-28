/**
 * Define the actions.
 *
 * @since 3.23.0
 */

/**
 * External dependencies
 */
import { createActions, handleActions } from "redux-actions";

/**
 * Internal dependencies
 */
import { EDITOR_SELECTION_CHANGED } from "../../Edit/constants/ActionTypes";

export const { addEntity, editorSelectionChanged, requestAnalysis, setFormat } = createActions(
  EDITOR_SELECTION_CHANGED,
  "REQUEST_ANALYSIS",
  "SET_FORMAT"
);

export default handleActions(
  {
    SET_FORMAT: (state, action) => ({ format: action.payload })
  },
  { format: null, showCreate: true }
);
