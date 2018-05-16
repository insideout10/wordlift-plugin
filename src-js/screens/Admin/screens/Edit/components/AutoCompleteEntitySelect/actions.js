import { createActions, handleActions } from "redux-actions";

export const {
  loadItemsRequest,
  loadItemsSuccess,
  loadItemsError
} = createActions(
  "LOAD_ITEMS_REQUEST",
  "LOAD_ITEMS_SUCCESS",
  "LOAD_ITEMS_ERROR"
);

export const reducer = handleActions(
  {
    [loadItemsSuccess]: (state, action) => ({ ...state, items: action.payload })
  },
  { items: [] }
);
