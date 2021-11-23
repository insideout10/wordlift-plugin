/**
 * External dependencies
 */
import {put, select, takeLatest, fork} from "redux-saga/effects";

/**
 * Internal dependencies.
 */
import {acceptEntity, addEntityToCache, rejectEntity} from "../../api";
import {getApiConfig, getTermId} from "./selectors";
import {entityAddedToCache, setEntityActive, setEntityInActive} from "./actions";


function* acceptAndSaveEntity(action) {
    const {entityIndex, entityData} = action.payload
    yield fork(acceptEntity, yield select(getTermId), {
        ...entityData.meta,
        "@id": entityData.entityId
    }, yield select(getApiConfig));
    yield put(setEntityActive({entityIndex}))
}


function* rejectAndSaveEntity(action) {
    const {entityIndex, entityData} = action.payload
    yield fork(rejectEntity, yield select(getTermId), {...entityData, ...entityData.meta}, yield select(getApiConfig));
    yield put(setEntityInActive({entityIndex}))
}

function* addEntityFromSearch(action) {
    const entityData = action.payload[0]

    yield fork(addEntityToCache,
        yield select(getTermId),
        yield select(getApiConfig),
        {...entityData, ...entityData.meta}
    );
    yield put(entityAddedToCache(entityData))
}

export function* entitySaga() {
    yield takeLatest("ENTITY_ACCEPTED", acceptAndSaveEntity)
    yield takeLatest("ENTITY_REJECTED", rejectAndSaveEntity)
    yield takeLatest("ADD_ENTITY_FROM_SEARCH", addEntityFromSearch)
}