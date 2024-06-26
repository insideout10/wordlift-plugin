/**
 * External dependencies
 */
import {createReducer} from "@reduxjs/toolkit";
import {ASC, DESC} from "../store";

function hideAlreadyExistingUndoCards(state) {
    // First check if there are any tags with undo state, if it is
    // then set them to hidden.
    state.tags.map((tag) => {
        if (tag.isUndo) {
            // set it to hidden
            tag.isHidden = true
            // remove undo mode from that tag, we dont need the mode.
            tag.isUndo = false;
        }
        return tag
    })
}

/**
 * Internal dependencies.
 */
export const reducer = createReducer(null, {
    "UPDATE_TAGS": (state, action) => {
        const {tags, limit} = action.payload
        if (tags.length !== 0) {
            state.tags = state.tags.concat(tags)
            state.offset += limit
        }
    },
    "SET_ENTITY_ACTIVE": (state, action) => {
        const {entityIndex, tagIndex} = action.payload
        // set the active state.
        state.tags[tagIndex].entities[entityIndex].isActive = !state.tags[tagIndex].entities[entityIndex].isActive
    },


    "HIDE_ENTITY": (state, action) => {
        const {entityIndex, tagIndex} = action.payload

        state.tags[tagIndex].entities[entityIndex].isHidden = true

        // Check if all entities are hidden, if yes then undo the card
        if (state.tags[tagIndex].entities.length ===
            state.tags[tagIndex].entities.filter(entity => entity.isHidden).length) {
            // remove existing undo cards
            hideAlreadyExistingUndoCards(state);
            // undo the card.
            state.tags[tagIndex].isUndo = true
        }
    },

    "SHOW_TAG": (state, action) => {
        const {tagIndex} = action.payload
        // show all the entities and set first entity to active.
        state.tags[tagIndex].entities.map((entity, index) => {
            entity.isActive = index === 0;
            entity.isHidden = false
        })
        state.tags[tagIndex].isUndo = false
    },

    "REQUEST_IN_PROGRESS": (state, action) => {
        state.isRequestInProgress = true
    },

    "REQUEST_ENDED": (state, action) => {
        state.isRequestInProgress = false
    },

    "HIDE_TAG": (state, action) => {
        const {tagIndex} = action.payload
        hideAlreadyExistingUndoCards(state);
        state.tags[tagIndex].isUndo = true
    },

    "SORT_BY_POST_COUNT_ASC": (state) => {
        state.tags.sort((l, r) => (l.tagPostCount >= r.tagPostCount ? 1 : -1))
    },

    "SORT_BY_POST_COUNT_DESC": (state) => {
        state.tags.sort((l, r) => (l.tagPostCount >= r.tagPostCount ? -1 : 1))
    },


    "SORT_BY_TERM_NAME_ASC": (state) => {
        state.tags.sort((a, b) => a.tagName.toLowerCase().localeCompare(b.tagName.toLowerCase()))
    },

    "SORT_BY_TERM_NAME_DESC": (state) => {
        state.tags.sort((a, b) => a.tagName.toLowerCase().localeCompare(b.tagName.toLowerCase())).reverse()
    },

    "UPDATE_API_CONFIG": (state, action) => {
        state.apiConfig = action.payload.config
    }

});
