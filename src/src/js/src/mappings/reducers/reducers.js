/**
 * This file has reducers for mappings screen
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * Internal dependancies
 */
import { ADD_NEW_RULE, ADD_NEW_RULE_GROUP, DELETE_RULE, CHANGE_RULE_FIELD_VALUE, OPEN_OR_CLOSE_PROPERTY, PROPERTY_DATA_CHANGED, ADD_MAPPING, TITLE_CHANGED, PROPERTY_LIST_CHANGED, MAPPING_HEADER_CHANGED, RULE_GROUP_LIST_CHANGED, NOTIFICATION_CHANGED, PROPERTY_ITEM_CATEGORY_CHANGED, PROPERTY_LIST_SELECTED_CATEGORY_CHANGED, PROPERTY_ITEM_CRUD_OPERATION } from '../actions/actionTypes'
import { createReducer } from '@reduxjs/toolkit'
import { DELETE_PROPERTY_PERMANENT, DUPLICATE_PROPERTY } from '../components/PropertyListItemComponent'

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
    },

    /** When rule group list is changed by data from api, this below
     * handler set the new rule group data
     */
    [RULE_GROUP_LIST_CHANGED]: ( state, action )=> {
        state.ruleGroupList = action.payload.value
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
        const {propertyId} = action.payload
        const propertyIndex = state.propertyList
        .map( el => el.property_id )
        .indexOf( propertyId )
        const prevState = state.propertyList[propertyIndex].isOpenedOrAddedByUser
        // invert the previous state
        state.propertyList[propertyIndex].isOpenedOrAddedByUser = !prevState
    },
    /**
     * When any of the property data is changed then this action is dispatched from ui
     * and it is saved based on the fieldKey which identifies the field
     */
    [PROPERTY_DATA_CHANGED]: ( state, action )=> {
        const {fieldKey, value, propertyId } = action.payload
        const propertyIndex = state.propertyList
        .map( el => el.property_id )
        .indexOf( propertyId )
        state.propertyList[propertyIndex][fieldKey] = value
    },
    /**
     * When add mapping button is clicked then this action is dispatched, then we
     * add a property to the propertylist
     */
    [ADD_MAPPING]: ( state, action )=> {
        // push an empty property item
        state.propertyList.push({
            property_id: state.propertyList.length + 1,
            isOpenedOrAddedByUser: true,
            propertyHelpText:"",
            fieldTypeHelpText: "",
            fieldHelpText: "",
            transformHelpText: "",
            // Default category is active
            property_status: 'active'
        })
    },
    /**
     * When property list is changed from api, then change
     * it on the store, this action is dispatched after the 
     * network request.
     */
    [PROPERTY_LIST_CHANGED]: ( state, action )=> {
        state.propertyList = action.payload.value
    },

    /**
     * When the property category is changed like moved to 
     * trash, or moved back to active this action is handled
     * here
     */
    [ PROPERTY_ITEM_CATEGORY_CHANGED ]: ( state, action ) => {
        const { propertyId, propertyCategory } = action.payload
        const propertyIndex = state.propertyList
        .map( el => el.property_id )
        .indexOf( propertyId )
        state.propertyList[propertyIndex].property_status = propertyCategory
    },

    /** When the user clicks on the category of the property list
     * this action is dispatched to change the choosen category
     */
    [ PROPERTY_LIST_SELECTED_CATEGORY_CHANGED ] : ( state, action ) => {
        const { choosenCategory } = action.payload
        state.choosenPropertyCategory = choosenCategory
    },

    /**
     * Whenever user makes delete/duplicate operation on property id
     * the below action handler makes the operation
     */
    [ PROPERTY_ITEM_CRUD_OPERATION ] : ( state, action ) => {
        const { propertyId, operationName } = action.payload
        const propertyArray = state.propertyList
        .map( el => el.property_id )
        const propertyIndex = propertyArray
        .indexOf( propertyId )
        
        switch ( operationName ) {
            // Delete a property permanently
            case DELETE_PROPERTY_PERMANENT:
                state.propertyList = state.propertyList.splice(propertyIndex, 1);
                break
            case DUPLICATE_PROPERTY:
                // Copy the property values and change the property id
                const cloned_property = { ...state.propertyList[ propertyIndex ] }
                cloned_property.property_id = Math.max( propertyArray ) + 1
                state.propertyList.splice( propertyIndex + 1, 0,  cloned_property );
            default:
                break;
        }
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
    },

    /**
     * When the mapping header is changed by api, then this callback is triggered.
     */
    [MAPPING_HEADER_CHANGED]: ( state, action )=> {
        state.title = action.payload.title
        state.mapping_id = action.payload.mapping_id
    },
})

/**
  * Reducer to handle the notification section
  */
 export const NotificationReducer = createReducer(null, {
    /**
     * When the notification is changed, then we trigger the action
     */
    [NOTIFICATION_CHANGED]: ( state, action )=> {
        state.message = action.payload.message
        state.type = action.payload.type
    },
})
