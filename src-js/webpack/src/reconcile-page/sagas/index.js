/**
 * External dependencies
 */
import {call, delay, put, select, takeLatest, fork} from "redux-saga/effects";
import {getTagsFromApi, acceptEntity, markTagAsNoMatch, undoApiCall} from "../api";
import {updateTags, hideEntity, showTag, hideTag, requestInProgress, requestCompleted, getTagsAction} from "../actions";
import {convertApiResponseToUiObject} from "../api/filters";
import store from "../store/index";

/**
 */
function* getTags(action) {
    // Allow only one request to be run with in a time.
    if ( ! store.getState().isRequestInProgress) {
        const {limit} = action.payload
        yield put(requestInProgress())
        const tags = yield call(getTagsFromApi, store.getState().offset, limit, store.getState().apiConfig);
        yield put(updateTags({tags: convertApiResponseToUiObject(tags), limit}))
        yield put(requestCompleted())
    }
}

function getTermIdAndEntity(state, tagIndex) {
    // Get the meta of entity which is currently active.
    let entityData = null;
    let entityIndex = 0
    for (let [index, entity] of state.tags[tagIndex].entities.entries()) {
        if (entity.isActive) {
            entityData = entity
            entityIndex = index
            // return early after first match.
            break;
        }
    }


    return {
        termId: state.tags[tagIndex].tagId,
        entity: entityData,
        entityIndex: entityIndex
    }

}

function* acceptEntitySaga(action) {
    const {tagIndex} = action.payload
    const {termId, entity, entityIndex} = getTermIdAndEntity(store.getState(), tagIndex)

    yield fork(acceptEntity, termId, {...entity, ...entity.meta}, store.getState().apiConfig);
    // Hide tag on ui.
    yield put(hideTag({
        tagIndex: tagIndex,
    }))
    // On Accept load tags to the page.
    yield put(getTagsAction({limit: 20}))
}

function* noMatchTagSaga(action) {
    const {tagIndex} = action.payload
    const state = store.getState()
    const {termId} = getTermIdAndEntity(state, tagIndex)
    yield fork(markTagAsNoMatch, termId, store.getState().apiConfig);
    // Hide tag on ui.
    yield put(hideTag({
        tagIndex: tagIndex,
    }))
    // if there is no pending requests start the new one
    if (!store.getState().isRequestInProgress) {
        // restart the request since there are less tags present in the store.
        yield put(getTagsAction({limit: 20}))
    }
}


function* undoSaga(action) {
    const {tagIndex} = action.payload
    const termId = store.getState().tags[tagIndex].tagId;
    // remove all the meta
    yield fork(undoApiCall, termId, store.getState().apiConfig);
    // show tag on ui.
    yield put(showTag(action.payload))

}

function* rootSaga() {
    yield takeLatest("GET_TAGS_FROM_NETWORK_CALL", getTags);
    yield takeLatest("ACCEPT_ENTITY", acceptEntitySaga)
    yield takeLatest("MARK_TAG_AS_NO_MATCH", noMatchTagSaga)
    yield takeLatest("UNDO", undoSaga)
}

export default rootSaga;
