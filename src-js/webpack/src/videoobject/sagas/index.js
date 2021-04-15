import store from "../../vocabulary/store";
import {fork, put, call, takeLatest} from "redux-saga/effects";
import {getVideosFromApi} from "../api";
import {setVideos} from "../actions";


function* getVideos(action) {
    const videos = yield call(getVideosFromApi, store.getState().apiConfig);
    // set videos on store.
    yield put(setVideos({videos: videos}))

}

export function* rootSaga() {
    yield takeLatest("GET_ALL_VIDEOS_FROM_NETWORK", getVideos);
}

