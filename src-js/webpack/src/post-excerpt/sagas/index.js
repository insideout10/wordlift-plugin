/**
 * This files provide the sagas for post excerpt
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * External dependencies
 */
import { call, delay, put, takeLatest } from "redux-saga/effects";
/**
 * Internal dependencies.
 */
import { requestPostExcerpt, setNotificationData, updatePostExcerpt, updateRequestStatus } from "../actions";
import getPostExcerpt from "../api";

function* handleRefreshPostExcerpt(action) {
  yield put(updateRequestStatus(true));
  const { postBody } = action.payload;
  // set request state to in progress.
  const response = yield call(getPostExcerpt, postBody);
  if (response.post_excerpt !== undefined) {
    yield put(updatePostExcerpt(response.post_excerpt));
  } else {
    console.error( response );
  }
  // Request is complete, now dont show the loading icon.
  yield put(updateRequestStatus(false));
  /**
   * After 2 seconds, remove the notification.
   */
  yield delay(2000);
  yield put(
    setNotificationData({
      notificationMessage: "",
      notificationType: ""
    })
  );
}

function* rootSaga() {
  yield takeLatest(requestPostExcerpt, handleRefreshPostExcerpt);
}

export default rootSaga;
