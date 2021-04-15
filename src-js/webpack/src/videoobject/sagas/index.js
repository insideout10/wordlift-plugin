import store from "../store";
import {fork, put, call, takeLatest} from "redux-saga/effects";
import {getVideosFromApi} from "../api";
import {updateVideos} from "../actions";


function* getVideos(action) {
    const videos = yield call(getVideosFromApi, store.getState().apiConfig);
    // set videos on store.
    yield put(updateVideos({videos: videos}))

}

export function* rootSaga() {
    yield takeLatest("GET_ALL_VIDEOS_FROM_NETWORK", getVideos);
}

