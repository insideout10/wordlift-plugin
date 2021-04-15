import {createReducer} from "@reduxjs/toolkit";

export const reducer = createReducer(null, {
    "UPDATE_VIDEOS": (state, action) => {
        const {videos} = action.payload
        state.videos = videos
    },
});