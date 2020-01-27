/**
 * This file contains list of actions
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */
import {
  ADD_NEW_RULE,
  ADD_NEW_RULE_GROUP,
  DELETE_RULE,
  CHANGE_RULE_FIELD_VALUE,
  OPEN_OR_CLOSE_PROPERTY,
  PROPERTY_DATA_CHANGED,
  ADD_MAPPING,
  TITLE_CHANGED,
  PROPERTY_LIST_CHANGED,
  RULE_GROUP_LIST_CHANGED,
  MAPPING_HEADER_CHANGED,
  NOTIFICATION_CHANGED,
  MAPPING_LIST_CHANGED,
  MAPPING_ITEM_CATEGORY_CHANGED,
  MAPPING_LIST_BULK_SELECT,
  MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED,
  MAPPING_ITEM_SELECTED,
  PROPERTY_ITEM_CATEGORY_CHANGED,
  PROPERTY_LIST_SELECTED_CATEGORY_CHANGED,
  PROPERTY_ITEM_CRUD_OPERATION,
  BULK_ACTION_SELECTION_CHANGED,
  PROPERTY_ITEM_SELECTED,
  PROPERTY_ITEM_SELECT_ALL,
  PROPERTY_ITEMS_BULK_SELECT,
  MAPPING_ITEMS_BULK_APPLY,
  MAPPING_ID_CHANGED_FROM_API,
  MAPPING_LIST_SORT_TITLE_CHANGED,
  MAPPING_TERMS_CHANGED,
  MAPPINGS_REQUEST,
  MAPPINGS_REQUEST_DELETE_OR_UPDATE,
  MAPPINGS_REQUEST_CLONE_MAPPINGS,
  MAPPINGS_RESET_UI_AFTER_BULK_APPLY,
  EDIT_MAPPING_REQUEST_TERMS,
  EDIT_MAPPING_TERMS_FETCHED_FOR_TAXONOMY,
  EDIT_MAPPING_SAVE_MAPPING_ITEM, EDIT_MAPPING_REQUEST_MAPPING_ITEM
} from "./action-types";
/**
 * @const {object} ADD_NEW_RULE_ACTION
 * Dispatches this action when add new rule is clicked
 */
export const ADD_NEW_RULE_ACTION = {
  type: ADD_NEW_RULE
};

/**
 * @const {object} ADD_NEW_RULE_GROUP_ACTION
 *  Dispatches this action when add new rule group is clicked
 */
export const ADD_NEW_RULE_GROUP_ACTION = {
  type: ADD_NEW_RULE_GROUP
};

/**
 * @const {object} DELETE_RULE_ACTION
 *  Dispatches this action when delete rule is clicked
 */
export const DELETE_RULE_ACTION = {
  type: DELETE_RULE
};

/**
 * @const {object} CHANGE_RULE_FIELD_VALUE_ACTION
 *  Dispatches this action when a selection box is changed
 */
export const CHANGE_RULE_FIELD_VALUE_ACTION = {
  type: CHANGE_RULE_FIELD_VALUE
};

/**
 * @const {object} OPEN_OR_CLOSE_PROPERTY_ACTION
 *  Dispatches this action to open or close a property item
 */
export const OPEN_OR_CLOSE_PROPERTY_ACTION = {
  type: OPEN_OR_CLOSE_PROPERTY
};

/**
 * @const {object} PROPERTY_DATA_CHANGED_ACTION
 *  Dispatches this action when a single property field gets changed
 */
export const PROPERTY_DATA_CHANGED_ACTION = {
  type: PROPERTY_DATA_CHANGED
};
/**
 * @const {object} ADD_MAPPING_ACTION
 *  Dispatches this action to create a new mapping item
 */
export const ADD_MAPPING_ACTION = {
  type: ADD_MAPPING
};

/**
 * @const {object} TITLE_CHANGED_ACTION
 *  Dispatches this action when the mapping item title is changed
 */
export const TITLE_CHANGED_ACTION = {
  type: TITLE_CHANGED
};

/**
 * @const {object} PROPERTY_LIST_CHANGED_ACTION
 *  Dispatches this action when the property list is changed from the api.
 */
export const PROPERTY_LIST_CHANGED_ACTION = {
  type: PROPERTY_LIST_CHANGED
};

/**
 * @const {object} RULE_GROUP_LIST_CHANGED_ACTION
 *  Dispatches this action when the rule group list is changed from the api.
 */
export const RULE_GROUP_LIST_CHANGED_ACTION = {
  type: RULE_GROUP_LIST_CHANGED
};

/**
 * @const {object} MAPPING_HEADER_CHANGED_ACTION
 *  Dispatches this action when the mapping header is changed from the api.
 */
export const MAPPING_HEADER_CHANGED_ACTION = {
  type: MAPPING_HEADER_CHANGED
};

/**
 * @const {object} NOTIFICATION_CHANGED_ACTION
 *  Dispatches this action when the notification of edit section changes
 */
export const NOTIFICATION_CHANGED_ACTION = {
  type: NOTIFICATION_CHANGED
};

/**
 * @const {object} MAPPING_LIST_CHANGED_ACTION
 *  Dispatches this action when the mapping list changes
 */
export const MAPPING_LIST_CHANGED_ACTION = {
  type: MAPPING_LIST_CHANGED
};

/**
 * @const {object} MAPPING_ITEM_CATEGORY_CHANGED_ACTION
 *  Dispatches this action when the mapping list changes
 */
export const MAPPING_ITEM_CATEGORY_CHANGED_ACTION = {
  type: MAPPING_ITEM_CATEGORY_CHANGED
};

/**
 * @const {object} MAPPING_LIST_BULK_SELECT_ACTION
 * Dispatches this action when the mapping list needed to be selected
 * or vice versa
 */
export const MAPPING_LIST_BULK_SELECT_ACTION = {
  type: MAPPING_LIST_BULK_SELECT
};

/**
 * @const {object} MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION
 * Dispatches this action when the user chooses different category
 */
export const MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION = {
  type: MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED
};

/**
 * @const {object} MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION
 * Dispatches this action when the user checks the checkbox of mapping item
 */
export const MAPPING_ITEM_SELECTED_ACTION = {
  type: MAPPING_ITEM_SELECTED
};

/**
 * @const {Object} MAPPING_LIST_SORT_TITLE_CHANGED_ACTION
 * Dispatches this action when the user wants to sort the title asc/desc
 */
export const MAPPING_LIST_SORT_TITLE_CHANGED_ACTION = {
  type: MAPPING_LIST_SORT_TITLE_CHANGED
};

/**
 * @const {object} PROPERTY_ITEM_CATEGORY_CHANGED_ACTION
 * Dispatches this action when the user checks the checkbox of mapping item
 */
export const PROPERTY_ITEM_CATEGORY_CHANGED_ACTION = {
  type: PROPERTY_ITEM_CATEGORY_CHANGED
};

/**
 * @const {object} PROPERTY_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION
 * Dispatches this action when the user clicks on category
 * of the property list.
 */
export const PROPERTY_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION = {
  type: PROPERTY_LIST_SELECTED_CATEGORY_CHANGED
};

/**
 * @const {object} PROPERTY_ITEM_CRUD_OPERATION_ACTION
 * Dispatches this action when the user wants to permanently delete/duplicate
 * a property.
 */
export const PROPERTY_ITEM_CRUD_OPERATION_ACTION = {
  type: PROPERTY_ITEM_CRUD_OPERATION
};

/**
 * @const {object} BULK_ACTION_SELECTION_CHANGED_ACTION
 * Dispatches this action when the user changes the bulk action
 * selection box.
 */
export const BULK_ACTION_SELECTION_CHANGED_ACTION = {
  type: BULK_ACTION_SELECTION_CHANGED
};

/**
 * @const {object} PROPERTY_ITEM_SELECTED_ACTION
 * Dispatches this action when the user clicks on property
 * item checkbox
 */
export const PROPERTY_ITEM_SELECTED_ACTION = {
  type: PROPERTY_ITEM_SELECTED
};

/**
 * @const {object} PROPERTY_ITEM_SELECT_ALL_ACTION
 * Dispatches this action when the user clicks on select all
 * checkbox
 */
export const PROPERTY_ITEM_SELECT_ALL_ACTION = {
  type: PROPERTY_ITEM_SELECT_ALL
};

/**
 * @const {object} PROPERTY_ITEMS_BULK_ACTION
 * Dispatches this action when the user makes bulk
 * action
 */
export const PROPERTY_ITEMS_BULK_ACTION = {
  type: PROPERTY_ITEMS_BULK_SELECT
};

/**
 * @const {object} MAPPING_ITEMS_BULK_APPLY_ACTION
 * Dispatches this action when the user makes bulk
 * action on mapping list
 */
export const MAPPING_ITEMS_BULK_APPLY_ACTION = {
  type: MAPPING_ITEMS_BULK_APPLY
};

/**
 * @const {object} EDIT_MAPPING_REQUEST_TERMS_ACTION
 * Dispatches this action when the user wants to load
 * terms for the particular taxonomy.
 */
export const EDIT_MAPPING_REQUEST_TERMS_ACTION = {
  type: EDIT_MAPPING_REQUEST_TERMS
};

/**
 * @const {object} EDIT_MAPPING_TERMS_FETCHED_FOR_TAXONOMY_ACTION
 * Dispatches this action when the network request is sent by the ui to get the terms,
 * to prevent duplication we do this to send only one request for each taxonomy.
 */
export const EDIT_MAPPING_TERMS_FETCHED_FOR_TAXONOMY_ACTION = {
  type: EDIT_MAPPING_TERMS_FETCHED_FOR_TAXONOMY
};

/**
 * @const {object} EDIT_MAPPING_SAVE_MAPPING_ITEM_ACTION
 * Dispatches this action when the network request is sent by the ui to save the
 * mapping item.
 */
export const EDIT_MAPPING_SAVE_MAPPING_ITEM_ACTION = {
  type: EDIT_MAPPING_SAVE_MAPPING_ITEM
};

/**
 * @const {object} EDIT_MAPPING_REQUEST_MAPPING_ITEM_ACTION
 * Dispatches this action when we need to get the mapping item by the mappingId.
 */
export const EDIT_MAPPING_REQUEST_MAPPING_ITEM_ACTION = {
  type: EDIT_MAPPING_REQUEST_MAPPING_ITEM
};

/**
 * @const {object} MAPPING_ID_CHANGED_FROM_API_ACTION
 * Dispatches this action when the mapping id changes
 * on add new mapping save
 */
export const MAPPING_ID_CHANGED_FROM_API_ACTION = {
  type: MAPPING_ID_CHANGED_FROM_API
};

/**
 * @const {object} Whenever the taxonomies are loaded from the rest api,
 * this action is fired setting the options, the options are cached in the redux
 * state store.
 */
export const MAPPING_TERMS_CHANGED_ACTION = {
  type: MAPPING_TERMS_CHANGED
};

/**
 * @const {object} Whenever the ui needs to load the mapping items, this action is
 * fired.
 */
export const MAPPINGS_REQUEST_ACTION = {
  type: MAPPINGS_REQUEST
};

/**
 * @const {object} Whenever the ui needs to delete or update mappings this action is fired
 */
export const MAPPINGS_REQUEST_DELETE_OR_UPDATE_ACTION = {
  type: MAPPINGS_REQUEST_DELETE_OR_UPDATE
};


/**
 * @const {object} Whenever the ui needs to clone the mapping this action is fired.
 */
export const MAPPINGS_REQUEST_CLONE_MAPPINGS_ACTION = {
  type: MAPPINGS_REQUEST_CLONE_MAPPINGS
};

/**
 * @const {object} Whenever the ui needs to reset the state after bulk apply this action is fired.
 */
export const MAPPINGS_RESET_UI_AFTER_BULK_APPLY_ACTION = {
  type: MAPPINGS_RESET_UI_AFTER_BULK_APPLY
};
