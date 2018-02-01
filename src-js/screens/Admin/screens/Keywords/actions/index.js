import { createAction, handleAction, handleActions } from 'redux-actions';

export const KEYWORDS_LOAD_REQUEST = 'KEYWORDS_LOAD_REQUEST';
export const loadKeywordsRequest = createAction(KEYWORDS_LOAD_REQUEST);

export const KEYWORDS_LOAD_SUCCESS = 'KEYWORDS_LOAD_SUCCESS';
export const loadKeywordsSuccess = createAction(KEYWORDS_LOAD_SUCCESS);

export const KEYWORDS_LOAD_FAILURE = 'KEYWORDS_LOAD_FAILURE';
export const loadKeywordsFailure = createAction(KEYWORDS_LOAD_FAILURE);

export const POPUP_TOGGLE = 'POPUP_TOGGLE';
export const togglePopup = createAction(POPUP_TOGGLE);

export const KEYWORD_CHANGE = 'KEYWORD_CHANGE';
export const changeKeyword = createAction(KEYWORD_CHANGE);

export const KEYWORD_CREATE_REQUEST = 'KEYWORD_CREATE_REQUEST';
export const createKeywordRequest = createAction('KEYWORD_CREATE_REQUEST');

export const KEYWORD_DELETE_REQUEST = 'KEYWORD_DELETE_REQUEST';
export const deleteKeywordRequest = createAction(KEYWORD_DELETE_REQUEST);

export const dataReducer = handleAction(
  loadKeywordsSuccess,
  (state, { payload: data }) => data,
  []
);

export const popupReducer = handleActions(
  {
    [togglePopup](state) {
      return !state.showPopup;
    },
    // Hide the popup when creating a keyword.
    [createKeywordRequest]() {
      return false;
    }
  },
  false
);

export const keywordReducer = handleActions(
  {
    [changeKeyword](state, { payload: keyword }) {
      return keyword;
    },
    [createKeywordRequest]() {
      return '';
    },
    [deleteKeywordRequest]() {
      return '';
    }
  },
  ''
);
