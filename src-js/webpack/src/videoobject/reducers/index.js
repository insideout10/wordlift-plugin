import {createReducer} from "@reduxjs/toolkit";

export const reducer = createReducer(null, {
    "UPDATE_VIDEOS": (state, action) => {
        const {videos} = action.payload
        state.videos = videos
    },
    "OPEN_MODAL" : (state, action) => {
        const {videoIndex} = action.payload
        state.isModalOpened = true
        state.videoIndex = videoIndex
     }
});