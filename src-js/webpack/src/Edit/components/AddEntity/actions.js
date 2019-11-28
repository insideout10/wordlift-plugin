/**
 * External dependencies
 */
import { createActions, handleActions } from "redux-actions";

export const {
  loadItemsRequest,
  loadItemsSuccess,
  loadItemsError,
  createEntityRequest,
  createEntitySuccess,
  addEntityRequest,
  addEntitySuccess,
  close,
  open,
  setValue
} = createActions(
  "LOAD_ITEMS_REQUEST",
  "LOAD_ITEMS_SUCCESS",
  "LOAD_ITEMS_ERROR",
  "CREATE_ENTITY_REQUEST",
  "CREATE_ENTITY_SUCCESS",
  "ADD_ENTITY_REQUEST",
  "ADD_ENTITY_SUCCESS",
  "CLOSE",
  "OPEN",
  "SET_VALUE"
);

export const reducer = handleActions(
  {
    [loadItemsSuccess]: (state, action) => ({
      ...state,
      items: action.payload
    }),
    [close]: state => ({ ...state, open: false }),
    [open]: state => ({ ...state, open: state.enabled }),
    [setValue]: (state, action) => ({
      ...state,
      value: action.payload,
      enabled: "undefined" !== typeof action.payload && "" !== action.payload
    })
  },
  { open: false, items: [], value: "", enabled: false }
);
