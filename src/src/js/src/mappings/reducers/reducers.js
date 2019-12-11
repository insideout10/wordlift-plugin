import { ADD_NEW_RULE, ADD_NEW_RULE_GROUP, DELETE_RULE } from '../actions/actionTypes'
import { createReducer } from '@reduxjs/toolkit'
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
export const RuleGroupReducer = createReducer(null, {
    [ADD_NEW_RULE_GROUP]: (state, action) => {
      state.ruleGroupList.push({rules: [{}]})
    },
    [ADD_NEW_RULE]: (state, action)=> {
        state.ruleGroupList[action.payload.ruleGroupIndex].rules
        .splice(action.payload.ruleIndex + 1, 0, {})
    },
    [DELETE_RULE]: (state, action)=> {
        const ruleGroupIndex = action.payload.ruleGroupIndex
        const ruleIndex = action.payload.ruleIndex
        // if the rule group has only one item, then it should be removed
        if (state.ruleGroupList[ruleGroupIndex].rules.length === 1) {
            state.ruleGroupList.splice(ruleGroupIndex, 1)
        }
        else {
            state.ruleGroupList[ruleGroupIndex].rules.splice(ruleIndex, 1)
        }
    }
  })