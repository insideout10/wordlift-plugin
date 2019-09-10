/* globals wp */

import actions from "./actions";
import controls from "./controls";
import * as Constants from "../constants";

const { registerStore } = wp.data;

const DEFAULT_STATE = {
  entities: {}
};

// registerStore(Constants.WORDLIFT_EDITOR_STORE, {
//   reducer(state = DEFAULT_STATE, action) {
//     switch (action.type) {
//       case "RECEIVE_ANALYSIS":
//         const { entities } = action.payload;
//
//         return { ...state, entities };
//     }
//     return state;
//   },
//
//   actions,
//
//   selectors: {
//     getEntities(state) {
//       return state.entities;
//     }
//   },
//
//   controls,
//
//   resolvers: {
//     getEntities() {
//       return actions.requestAnalysis();
//     }
//   }
// });
