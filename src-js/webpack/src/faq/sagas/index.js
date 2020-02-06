/**
 * This files provide the sagas for FAQ
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */


/**
 * External dependencies
 */
import { call, put, select, takeLatest } from "redux-saga/effects";
import {REQUEST_FAQ_ADD_NEW_QUESTION} from "../constants/action-types";

function* handleAddNewQuestion() {
    const items = yield
}


function* rootSaga() {
    yield takeLatest( REQUEST_FAQ_ADD_NEW_QUESTION, handleAddNewQuestion)
}

export default rootSaga