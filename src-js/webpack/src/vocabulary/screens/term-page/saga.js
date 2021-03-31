/**
 * External dependencies
 */
import {put, select, takeLatest, fork} from "redux-saga/effects";

/**
 * Internal dependencies.
 */
import {acceptEntity, rejectEntity} from "../../api";
import {getApiConfig, getTermId} from "./selectors";
import {setEntityActive, setEntityInActive} from "./actions";


function* acceptAndSaveEntity(action) {
    const {entityIndex, entityData} = action.payload
    yield fork(acceptEntity, yield select(getTermId), {...entityData, ...entityData.meta}, yield select(getApiConfig));
    yield put(setEntityActive({entityIndex}))
}


function* rejectAndSaveEntity(action) {
    const {entityIndex, entityData} = action.payload
    yield fork(rejectEntity, yield select(getTermId), {...entityData, ...entityData.meta}, yield select(getApiConfig));
    yield put(setEntityInActive({entityIndex}))
}

export function* entitySaga() {
    yield takeLatest("ENTITY_ACCEPTED", acceptAndSaveEntity)
    yield takeLatest("ENTITY_REJECTED", rejectAndSaveEntity)
}