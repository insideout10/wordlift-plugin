/**
 * This file contains list of actions 
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */
import { ADD_NEW_RULE, ADD_NEW_RULE_GROUP, DELETE_RULE, CHANGE_RULE_FIELD_VALUE } from './actionTypes'
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