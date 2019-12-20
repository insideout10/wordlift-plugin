/**
 * This file has reducers for mappings screen
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * Internal dependancies
 */
import { ADD_NEW_RULE, ADD_NEW_RULE_GROUP, DELETE_RULE, CHANGE_RULE_FIELD_VALUE, OPEN_OR_CLOSE_PROPERTY, PROPERTY_DATA_CHANGED, ADD_MAPPING, TITLE_CHANGED, PROPERTY_LIST_CHANGED } from '../actions/actionTypes'
import { createReducer } from '@reduxjs/toolkit'

 /**
  * Reducer to handle the rule group and rule section
  */
export const RuleGroupReducer = createReducer(null, {
    /**
     * When add rule group is clicked then this action is fired from ui
     */
    [ADD_NEW_RULE_GROUP]: ( state, action ) => {
      state.ruleGroupList.push({rules: [{}]})
    },
    /**
     * When `AND` button is clicked, this action is dispatched with the rule index
     * and the rule is added after the index.
     */
    [ADD_NEW_RULE]: ( state, action )=> {
        // clicked index is given, add an item after that index
        state.ruleGroupList[action.payload.ruleGroupIndex].rules
        .splice(action.payload.ruleIndex + 1, 0, {
            ruleFieldOneValue: state.ruleFieldOneOptions[0].value,
            ruleFieldTwoValue: state.ruleFieldTwoOptions[0].value,
            ruleLogicFieldValue: state.ruleLogicFieldOptions[0].value

        })
    },
    /**
     * When `-` button is clicked, this action is dispatched with the rule index
     * and the rule is deleted at the index.
     */
    [DELETE_RULE]: ( state, action )=> {
        const {ruleGroupIndex,ruleIndex} = action.payload
        // if the rule group has only one item, then it should be removed
        if (1 === state.ruleGroupList[ruleGroupIndex].rules.length) {
            state.ruleGroupList.splice(ruleGroupIndex, 1)
        }
        else {
            state.ruleGroupList[ruleGroupIndex].rules.splice(ruleIndex, 1)
        }
    },
    /**
     * When any of the selection button in rule component values are changed, they 
     * are dispatched with ruleIndex and ruleGroupIndex.
     */
    [CHANGE_RULE_FIELD_VALUE]: ( state, action )=> {
        const { ruleGroupIndex,ruleIndex,fieldKey,value } = action.payload
        state.ruleGroupList[ruleGroupIndex].rules[ruleIndex][fieldKey] = value
    }
  })

/**
  * Reducer to handle the property section
  */
 export const PropertyReducer = createReducer(null, {
    /**
     * When the `edit` or `close mapping` is clicked then the property changes the state
     * it switches to edit mode and list mode depends on the state
     */
    [OPEN_OR_CLOSE_PROPERTY]: ( state, action )=> {
        const {propertyIndex} = action.payload
        const prevState = state.propertyList[propertyIndex].isOpenedOrAddedByUser
        // invert the previous state
        state.propertyList[propertyIndex].isOpenedOrAddedByUser = !prevState
    },
    /**
     * When any of the property data is changed then this action is dispatched from ui
     * and it is saved based on the fieldKey which identifies the field
     */
    [PROPERTY_DATA_CHANGED]: ( state, action )=> {
        const {fieldKey, value, propertyIndex } = action.payload
        state.propertyList[propertyIndex][fieldKey] = value
    },
    /**
     * When add mapping button is clicked then this action is dispatched, then we
     * add a property to the propertylist
     */
    [ADD_MAPPING]: ( state, action )=> {
        // push an empty property item
        state.propertyList.push({
            isOpenedOrAddedByUser: true,
            propertyHelpText:"",
            fieldTypeHelpText: "",
            fieldHelpText: "",
            transformHelpText: ""
        })
    },
    /**
     * When property list is changed from api, then change
     * it on the store, this action is dispatched after the 
     * network request.
     */
    [PROPERTY_LIST_CHANGED]: ( state, action )=> {
        state.propertyList = action.payload.value
    }
})

/**
  * Reducer to handle the title section
  */
 export const TitleReducer = createReducer(null, {
    /**
     * When the mapping title is changed in add/edit mode then this event is fired.
     */
    [TITLE_CHANGED]: ( state, action )=> {
        state.title = action.payload.value
    }
})

