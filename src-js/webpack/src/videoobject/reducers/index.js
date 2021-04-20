import {createReducer} from "@reduxjs/toolkit";

export const reducer = createReducer(null, {
    "UPDATE_VIDEOS": (state, action) => {
        const {videos} = action.payload
        state.videos = videos
    },
    "OPEN_MODAL": (state, action) => {
        const {videoIndex} = action.payload
        state.isModalOpened = true
        state.videoIndex = videoIndex
    },
    "NEXT_VIDEO": (state, action) => {
        // check if its with in the limits, else dont increment
        if (state.videoIndex + 1 < state.videos.length) {
            state.videoIndex += 1;
        }
    },

    "PREVIOUS_VIDEO": (state, action) => {
        if (state.videoIndex - 1 >= 0) {
            state.videoIndex -= 1;
        }
    },

    "CLOSE_MODAL" : (state, action) => {
        state.isModalOpened = false
    },

    "ADD_NEW_THUMBNAIL": (state, action) =>  {
        const {videoIndex} = action.payload
        state.videos[videoIndex].thumbnail_urls.push("")
    },

    "REMOVE_THUMBNAIL": (state, action) => {
        const {videoIndex, thumbnailIndex} = action.payload
        state.videos[videoIndex].thumbnail_urls.splice(thumbnailIndex, 1);
    },

    "MODAL_FIELD_CHANGED" : (state, action) => {
        const {key, value} = action.payload
        state.videos[state.videoIndex][key] = value
    }
});