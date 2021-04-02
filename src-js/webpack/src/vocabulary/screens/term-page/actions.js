import {createAction} from "redux-actions";

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

export const setEntityActive = createAction("SET_ENTITY_ACTIVE");

export const setEntityInActive = createAction("SET_ENTITY_INACTIVE");