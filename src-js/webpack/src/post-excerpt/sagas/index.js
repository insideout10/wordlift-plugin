/**
 * This files provide the sagas for post excerpt
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import { takeLatest } from "redux-saga/effects";
import {requestPostExcerpt} from "../actions";

/**
 * Internal dependencies.
 */

function* handleRefreshPostExcerpt(action) {

}


function* rootSaga() {
  yield takeLatest(requestPostExcerpt, handleRefreshPostExcerpt);
}

export default rootSaga;
