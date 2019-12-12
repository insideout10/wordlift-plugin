import { ADD_NEW_RULE, ADD_NEW_RULE_GROUP, DELETE_RULE, CHANGE_RULE_FIELD_VALUE } from '../actions/actionTypes'
import { createReducer } from '@reduxjs/toolkit'
import { CHANGE_RULE_FIELD_VALUE_ACTION } from '../actions/actions'
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
        // clicked index is given, add an item after that index
        state.ruleGroupList[action.payload.ruleGroupIndex].rules
        .splice(action.payload.ruleIndex + 1, 0, {
            ruleFieldOneValue: state.ruleFieldOneOptions[0].value,
            ruleFieldTwoValue: state.ruleFieldTwoOptions[1].value,
            ruleLogicFieldValue: state.ruleLogicFieldOptions[0].value

        })
    },
    [DELETE_RULE]: (state, action)=> {
        const {ruleGroupIndex,ruleIndex} = action.payload
        // if the rule group has only one item, then it should be removed
        if (state.ruleGroupList[ruleGroupIndex].rules.length === 1) {
            state.ruleGroupList.splice(ruleGroupIndex, 1)
        }
        else {
            state.ruleGroupList[ruleGroupIndex].rules.splice(ruleIndex, 1)
        }
    },
    [CHANGE_RULE_FIELD_VALUE]: (state,action)=> {
        const { ruleGroupIndex,ruleIndex,fieldKey,value } = action.payload
        state.ruleGroupList[ruleGroupIndex].rules[ruleIndex][fieldKey] = value
    }
  })