import { ADD_NEW_RULE_GROUP_ACTION } from "../actions/actions";

/**
 * This file has reducers for mappings screen
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

 /**
  * 
  * @param {object} state The state of the edit mapping screen
  * @param {object} action The action to be performed on the state
  * mapped to action/actionTypes.js
  */
export function RuleGroupReducer(state=null, action) {
    switch(action) {
        case ADD_NEW_RULE_GROUP_ACTION:
            return { 
                ...state,
                ruleGroupList:[...state.ruleGroupList, {}]
            }
        default:
            return state
    }
}
