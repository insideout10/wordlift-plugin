/**
 * This file contains list of actions 
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */
import { ADD_NEW_RULE, ADD_NEW_RULE_GROUP, DELETE_RULE, CHANGE_RULE_FIELD_VALUE, OPEN_OR_CLOSE_PROPERTY, PROPERTY_DATA_CHANGED, ADD_MAPPING } from './actionTypes'
/**
 * @const {object} ADD_NEW_RULE_ACTION
 * Dispatches this action when add new rule is clicked
 */
export const ADD_NEW_RULE_ACTION =  {
    type: ADD_NEW_RULE
}

/**
 * @const {object} ADD_NEW_RULE_GROUP_ACTION
 *  Dispatches this action when add new rule group is clicked
 */
export const ADD_NEW_RULE_GROUP_ACTION = { 
    type: ADD_NEW_RULE_GROUP
}

/**
 * @const {object} DELETE_RULE_ACTION
 *  Dispatches this action when delete rule is clicked
 */
export const DELETE_RULE_ACTION = { 
    type: DELETE_RULE
}

/**
 * @const {object} CHANGE_RULE_FIELD_VALUE_ACTION
 *  Dispatches this action when a selection box is changed
 */
export const CHANGE_RULE_FIELD_VALUE_ACTION = { 
    type: CHANGE_RULE_FIELD_VALUE
}

/**
 * @const {object} OPEN_OR_CLOSE_PROPERTY_ACTION
 *  Dispatches this action to open or close a property item
 */
export const OPEN_OR_CLOSE_PROPERTY_ACTION = { 
    type: OPEN_OR_CLOSE_PROPERTY
}

/**
 * @const {object} PROPERTY_DATA_CHANGED_ACTION
 *  Dispatches this action when a single property field gets changed
 */
export const PROPERTY_DATA_CHANGED_ACTION = { 
    type: PROPERTY_DATA_CHANGED
}
/**
 * @const {object} ADD_MAPPING_ACTION
 *  Dispatches this action to create a new mapping item
 */
export const ADD_MAPPING_ACTION = {
    type:ADD_MAPPING
}