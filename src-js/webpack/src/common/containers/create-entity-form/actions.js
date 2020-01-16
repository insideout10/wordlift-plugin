/**
 * This file contains actions and reducer associated with the Create Entity Form container.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

/**
 * External dependencies
 */
import { createActions, handleActions } from "redux-actions";

/**
 * Actions
 */
export const { createEntityRequest } = createActions("CREATE_ENTITY_REQUEST");

/**
 * Reducer
 */
export default handleActions(
  {
    ADD_ENTITY_SUCCESS: state => ({ showCreate: false, value: null }),
    CREATE_ENTITY_REQUEST: (state, action) => ({ showCreate: true, value: action.payload })
  },
  { showCreate: false, value: null }
);
