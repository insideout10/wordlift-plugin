/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies.
 */
import {createReducer} from "@reduxjs/toolkit";


const setEntityStatus = (state, entityIndex, status) => {
    state.entities.map((entity, index) => {
        if ( index === entityIndex) {
            entity.isActive = status
        }
        return entity;
    })
}

export const reducer = createReducer(null, {

    "SET_ENTITY_ACTIVE" : (state, action) => {
        const {entityIndex} = action.payload
        setEntityStatus(state, entityIndex, true)
    },

    "SET_ENTITY_INACTIVE" : (state, action) => {
        const {entityIndex} = action.payload
        setEntityStatus(state, entityIndex, false)
    },

    "ENTITY_ADDED_TO_CACHE": (state, action) => {
        if ( ! state.entities ) {
            state.entities = []
        }
        state.entities.push(action.payload)
    }


})