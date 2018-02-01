import { call, put, takeEvery, takeLatest } from 'redux-saga/effects';
import {
  loadKeywordsRequest,
  loadKeywordsSuccess,
  loadKeywordsFailure,
  createKeywordRequest,
  deleteKeywordRequest
} from './index';

function* loadKeywords() {
  try {
    const rows = yield call(request, 'wl_get_keyword_rows');
    yield put(loadKeywordsSuccess(rows.data));
  } catch (error) {
    console.error(`An error occurred: ${error}`, error);
    yield put(loadKeywordsFailure(error));
  }
}

function* createKeyword({ payload: keyword }) {
  console.log(`Creating Keyword "${keyword}"...`);

  try {
    const result = yield call(request, 'wl_add_keyword', { keyword });
    console.info(`Keyword "${result}" created.`);
  } catch (error) {
    console.error(`An error occurred: ${error}`, error);
  }
}

function* deleteKeyword({ payload: keyword }) {
  console.log(`Deleting Keyword "${keyword}"...`);

  try {
    const result = yield call(request, 'wl_delete_keyword', { keyword });
    console.info(`Keyword "${result}" deleted.`);
  } catch (error) {
    console.error(`An error occurred: ${error}`, error);
  }
}

function* saga() {
  yield takeLatest(loadKeywordsRequest, loadKeywords);
  yield takeEvery(createKeywordRequest, createKeyword);
  yield takeEvery(deleteKeywordRequest, deleteKeyword);
}

// see https://github.com/redux-saga/redux-saga/pull/975#issuecomment-300272786
function request(action, data = {}) {
  return window.wp.ajax.post(action, data).then(
    data => data,
    (jqXHR, textStatus, errorThrown) => {
      // use those ^ to construct the Error
      return new Error();
    }
  );
}

export default saga;
