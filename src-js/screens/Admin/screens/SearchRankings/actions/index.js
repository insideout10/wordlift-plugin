import { createAction, handleActions } from "redux-actions";

export const clickTile = createAction("CLICK_TILE");

export const selectNode = createAction("SELECT_NODE");

const reducer = handleActions(
  {
    SELECT_NODE: (state, action) => ({ node: action.payload })
  },
  { node: null }
);

export default reducer;
