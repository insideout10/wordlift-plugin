/**
 * This file contains actions and reducer associated with the Create Entity Form container.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.23.0
 */

/**
 * @const ADD_NEW_RULE
 * constant used for add new rule action
 */
export const ADD_NEW_RULE = "ADD_NEW_RULE"

/**
 * @const ADD_NEW_RULE_GROUP
 * constant used for add new rule group action
 */
export const ADD_NEW_RULE_GROUP = "ADD_NEW_RULE_GROUP"

/**
 * @const DELETE_RULE
 * constant used for add delete rule
 */
export const DELETE_RULE = "DELETE_RULE"

/**
 * @const CHANGE_RULE_FIELD_VALUE
 * change a individual rule field option
 */
export const CHANGE_RULE_FIELD_VALUE = "CHANGE_RULE_FIELD_VALUE"

/**
 * @const OPEN_OR_CLOSE_PROPERTY
 * Action to open/close a property item for editing
 */
export const OPEN_OR_CLOSE_PROPERTY = "OPEN_OR_CLOSE_PROPERTY"

/**
 * @const PROPERTY_DATA_CHANGED
 * Action when a property data of an item gets changed
 */
export const PROPERTY_DATA_CHANGED = "PROPERTY_DATA_CHANGED"

/**
 * @const ADD_MAPPING
 * Action when add mapping button is clicked
 */
export const ADD_MAPPING = "ADD_MAPPING"

/**
 * @const TITLE_CHANGED
 * Action when add mapping button is clicked
 */
export const TITLE_CHANGED = "TITLE_CHANGED"

/**
 * @const PROPERTY_LIST_CHANGED
 * Action when property list changed by data from api.
 */
export const PROPERTY_LIST_CHANGED = "PROPERTY_LIST_CHANGED"

/**
 * @const RULE_GROUP_LIST_CHANGED
 * Action when rule group list changed by data from api.
 */
export const RULE_GROUP_LIST_CHANGED = "RULE_GROUP_LIST_CHANGED"

/**
 * @const MAPPING_HEADER_CHANGED
 * Action when mapping header is changed by api, usually consists of title and mapping id. 
 */
export const MAPPING_HEADER_CHANGED = "MAPPING_HEADER_CHANGED"

/**
 * @const NOTIFICATION_CHANGED
 * Action when notification is sent by api.
 */
export const NOTIFICATION_CHANGED = "NOTIFICATION_CHANGED"

/**
 * @const MAPPING_LIST_CHANGED
 * Action when notification is sent by api.
 */
export const MAPPING_LIST_CHANGED = "MAPPING_LIST_CHANGED"

/**
 * @const MAPPING_ITEM_CATEGORY_CHANGED
 * Action when notification is sent by api.
 */
export const MAPPING_ITEM_CATEGORY_CHANGED = "MAPPING_ITEM_CATEGORY_CHANGED"

/**
 * @const MAPPING_LIST_BULK_SELECT
 * Action when mapping items needed to be selected / unselected
 */
export const MAPPING_LIST_BULK_SELECT = "MAPPING_LIST_BULK_SELECT"


/**
 * @const MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED
 * Action when mapping items needed to be selected / unselected
 */
export const MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED = "MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED"

/**
 * @const MAPPING_ITEM_SELECTED
 * Action when mapping item is selected.
 */
export const MAPPING_ITEM_SELECTED = "MAPPING_ITEM_SELECTED"