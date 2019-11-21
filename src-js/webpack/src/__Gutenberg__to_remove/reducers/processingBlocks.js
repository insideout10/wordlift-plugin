import * as types from "../constants/ActionTypes";

const processingBlocks = function(state = [], action) {
  switch (action.type) {
    case types.PROCESSING_BLOCK_ADD:
      return [...state, action.blockClientId];

    case types.PROCESSING_BLOCK_REMOVE:
      return state.filter(item => {
        return item !== action.blockClientId; // return all the items not matching the action.id
      });

    default:
      return state;
  }
};

export default processingBlocks;
