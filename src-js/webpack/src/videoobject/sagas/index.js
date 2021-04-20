import store from "../store";
import {call, fork, put, takeLatest} from "redux-saga/effects";
import {getVideosFromApi, saveVideosInApi} from "../api";
import {closeModal, updateVideos} from "../actions";


function* getVideos(action) {
    const videos = yield call(getVideosFromApi, store.getState().apiConfig);
    // set videos on store.
    yield put(updateVideos({videos: videos}))

}

function* saveVideos() {
    yield fork(saveVideosInApi, store.getState().apiConfig, store.getState().videos);
    yield put(closeModal())
}

function* refresh() {
    yield put(closeModal())
    yield getVideos()
}

export function* rootSaga() {
    yield takeLatest("GET_ALL_VIDEOS_FROM_NETWORK", getVideos);
    yield takeLatest("SAVE_VIDEO_DATA_REQUEST", saveVideos)
    yield takeLatest("CLOSE_MODAL_AND_REFRESH", refresh)
}

