import {createAction} from "redux-actions";

export const getAllVideos = createAction("GET_ALL_VIDEOS_FROM_NETWORK");

export const updateVideos = createAction("UPDATE_VIDEOS")

export const openModal = createAction("OPEN_MODAL")

export const closeModal = createAction("CLOSE_MODAL")

export const previousVideo = createAction("PREVIOUS_VIDEO")

export const nextVideo = createAction("NEXT_VIDEO")

export const addNewThumbnail = createAction("ADD_NEW_THUMBNAIL")

export const removeThumbnail = createAction("REMOVE_THUMBNAIL")

export const saveVideoDataRequest = createAction("SAVE_VIDEO_DATA_REQUEST")

export const modalFieldChanged = createAction("MODAL_FIELD_CHANGED")

export const thumbnailFieldChanged = createAction("THUMBNAIL_FIELD_CHANGED")

export const closeModalAndRefresh = createAction("CLOSE_MODAL_AND_REFRESH")