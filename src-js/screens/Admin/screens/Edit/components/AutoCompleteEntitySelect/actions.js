import { createActions, handleActions } from "redux-actions";

export const {
  loadItemsRequest,
  loadItemsSuccess,
  loadItemsError,
  createEntityRequest,
  createEntitySuccess,
  addEntityRequest,
  addEntitySuccess
} = createActions(
  "LOAD_ITEMS_REQUEST",
  "LOAD_ITEMS_SUCCESS",
  "LOAD_ITEMS_ERROR",
  "CREATE_ENTITY_REQUEST",
  "CREATE_ENTITY_SUCCESS",
  "ADD_ENTITY_REQUEST",
  "ADD_ENTITY_SUCCESS"
);

export const reducer = handleActions(
  {
    [loadItemsSuccess]: (state, action) => ({ ...state, items: action.payload })
  },
  { items: [] }
);
