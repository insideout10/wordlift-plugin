/**
 * External dependencies
 */
import {call, delay, put, select, takeLatest, fork} from "redux-saga/effects";
import {acceptEntity} from "../../api";
import store from "../../store";
import {getApiConfig, getTermId} from "./selectors";
import {setEntityActive} from "./actions";


function* acceptAndSaveEntity(action) {

    const {entityIndex, entityData} = action.payload

    yield fork(acceptEntity, yield select(getTermId), {...entityData, ...entityData.meta}, yield select(getApiConfig));

    yield put(setEntityActive({entityIndex}))

}


function* rejectAndSaveEntity(action) {

    const {entityIndex, entityData} = action.payload


}

export function* entitySaga() {
    yield takeLatest("ENTITY_ACCEPTED", acceptAndSaveEntity)
}