import * as types from "../constants/ActionTypes";

const relatedPosts = function(state = [], action) {
  switch (action.type) {
    case types.RELATED_POSTS_UPDATE:
      return action.relatedPosts;

    default:
      return state;
  }
};

export default relatedPosts;
