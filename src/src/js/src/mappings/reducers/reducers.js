import { ADD_NEW_RULE, ADD_NEW_RULE_GROUP, DELETE_RULE, CHANGE_RULE_FIELD_VALUE, OPEN_OR_CLOSE_PROPERTY, PROPERTY_DATA_CHANGED } from '../actions/actionTypes'
import { createReducer } from '@reduxjs/toolkit'
/**
 * This file has reducers for mappings screen
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

 /**
  * Reducer to handle the rule group and rule section
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

/**
  * Reducer to handle the property section
  */
 export const PropertyReducer = createReducer(null, {
    [OPEN_OR_CLOSE_PROPERTY]: (state,action)=> {
        const {propertyIndex} = action.payload
        const prevState = state.propertyList[propertyIndex].isOpenedOrAddedByUser
        // invert the previous state
        state.propertyList[propertyIndex].isOpenedOrAddedByUser = !prevState
    },
    [PROPERTY_DATA_CHANGED]: ( state, action )=> {
        console.log(action)
        const {fieldKey, value, propertyIndex } = action.payload
        state.propertyList[propertyIndex][fieldKey] = value
    }
})

