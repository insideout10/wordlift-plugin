/**
 * External dependencies.
 */
import {createAction} from "redux-actions";


export const getTagsAction = createAction("GET_TAGS_FROM_NETWORK_CALL");

export const updateTags = createAction("UPDATE_TAGS")

export const setEntityActive = createAction("SET_ENTITY_ACTIVE");

export const acceptEntity = createAction("ACCEPT_ENTITY")

export const markTagAsNoMatch = createAction("MARK_TAG_AS_NO_MATCH")

export const hideEntity = createAction("HIDE_ENTITY")

// Reset the card back to initial state.
export const undo = createAction("UNDO")

export const showTag = createAction("SHOW_TAG")

export const hideTag = createAction("HIDE_TAG")

export const requestInProgress = createAction("REQUEST_IN_PROGRESS")

export const requestCompleted = createAction("REQUEST_ENDED")

export const sortByPostCountAsc = createAction("SORT_BY_POST_COUNT_ASC")

export const sortByPostCountDesc = createAction("SORT_BY_POST_COUNT_DESC")

export const sortByTermNameAsc = createAction("SORT_BY_TERM_NAME_ASC")

export const sortByTermNameDesc = createAction("SORT_BY_TERM_NAME_DESC")

/**
 * This action is fired on the term screen when the user selects an entity.
 * @type {function(): {type: *}}
 */
export const entityAccepted = createAction("ENTITY_ACCEPTED")
/**
 * This action is fired on the term screen when the user want to remove the previously
 * selected entity.
 * @type {function(): {type: *}}
 */
export const entityRejected = createAction("ENTITY_REJECTED");